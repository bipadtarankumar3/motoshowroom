<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\Showroom;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Subscribe a showroom to a specific plan with an active billing period.
     */
    public function subscribe(
        Showroom $showroom,
        SubscriptionPlan $plan,
        string $billingInterval = 'monthly',
        ?int $adminUserId = null,
        ?string $paymentRef = null
    ): Subscription {
        return DB::transaction(function () use ($showroom, $plan, $billingInterval, $adminUserId, $paymentRef) {
            $periodStart = now();
            $periodEnd = $billingInterval === 'annual'
                ? now()->addYear()
                : now()->addMonth();

            $existing = $showroom->subscription;
            $prevPlanId = $existing?->plan_id;
            $prevStatus = $existing?->status->value;

            $subscription = Subscription::updateOrCreate(
                ['showroom_id' => $showroom->id],
                [
                    'plan_id' => $plan->id,
                    'status' => SubscriptionStatus::Active,
                    'billing_interval' => $billingInterval,
                    'current_period_start' => $periodStart,
                    'current_period_end' => $periodEnd,
                    'auto_renew' => true,
                    'last_payment_reference' => $paymentRef,
                    'cancelled_at' => null,
                    'cancellation_reason' => null,
                ]
            );

            $showroom->update(['status' => 'active']);

            SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'showroom_id' => $showroom->id,
                'admin_user_id' => $adminUserId,
                'action' => $existing ? 'plan_changed' : 'subscribed',
                'previous_status' => $prevStatus,
                'new_status' => SubscriptionStatus::Active->value,
                'previous_plan_id' => $prevPlanId,
                'new_plan_id' => $plan->id,
                'notes' => "Subscribed to {$plan->name} ({$billingInterval})",
                'created_at' => now(),
            ]);

            AuditLogger::log(
                action: 'subscription.activated',
                auditable: $subscription,
                oldValues: ['status' => $prevStatus, 'plan_id' => $prevPlanId],
                newValues: ['status' => SubscriptionStatus::Active->value, 'plan_id' => $plan->id],
                tenantId: $showroom->id
            );

            return $subscription;
        });
    }

    /**
     * Start a free trial period for a newly registered or provisioned showroom.
     */
    public function startTrial(
        Showroom $showroom,
        SubscriptionPlan $plan,
        ?int $days = null,
        ?int $adminUserId = null
    ): Subscription {
        return DB::transaction(function () use ($showroom, $plan, $days, $adminUserId) {
            $trialDays = $days ?? $plan->trial_days ?? 14;
            $periodStart = now();
            $periodEnd = now()->addDays($trialDays);

            $subscription = Subscription::updateOrCreate(
                ['showroom_id' => $showroom->id],
                [
                    'plan_id' => $plan->id,
                    'status' => SubscriptionStatus::Trial,
                    'billing_interval' => 'monthly',
                    'current_period_start' => $periodStart,
                    'current_period_end' => $periodEnd,
                    'trial_ends_at' => $periodEnd,
                    'auto_renew' => true,
                ]
            );

            $showroom->update(['status' => 'trial']);

            SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'showroom_id' => $showroom->id,
                'admin_user_id' => $adminUserId,
                'action' => 'trial_started',
                'previous_status' => null,
                'new_status' => SubscriptionStatus::Trial->value,
                'previous_plan_id' => null,
                'new_plan_id' => $plan->id,
                'notes' => "Started {$trialDays}-day trial on {$plan->name}",
                'created_at' => now(),
            ]);

            AuditLogger::log(
                action: 'subscription.trial_started',
                auditable: $subscription,
                oldValues: null,
                newValues: ['plan_id' => $plan->id, 'trial_days' => $trialDays],
                tenantId: $showroom->id
            );

            return $subscription;
        });
    }

    /**
     * Renew an existing subscription for another billing cycle.
     */
    public function renew(
        Subscription $subscription,
        ?string $paymentRef = null,
        ?int $adminUserId = null
    ): Subscription {
        return DB::transaction(function () use ($subscription, $paymentRef, $adminUserId) {
            $baseDate = $subscription->current_period_end->isFuture()
                ? $subscription->current_period_end
                : now();

            $newEnd = $subscription->billing_interval === 'annual'
                ? $baseDate->copy()->addYear()
                : $baseDate->copy()->addMonth();

            $prevStatus = $subscription->status->value;

            $subscription->update([
                'status' => SubscriptionStatus::Active,
                'current_period_start' => now(),
                'current_period_end' => $newEnd,
                'last_payment_reference' => $paymentRef ?? $subscription->last_payment_reference,
            ]);

            $subscription->showroom->update(['status' => 'active']);

            SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'showroom_id' => $subscription->showroom_id,
                'admin_user_id' => $adminUserId,
                'action' => 'renewed',
                'previous_status' => $prevStatus,
                'new_status' => SubscriptionStatus::Active->value,
                'previous_plan_id' => $subscription->plan_id,
                'new_plan_id' => $subscription->plan_id,
                'notes' => "Renewed until {$newEnd->toDateString()}",
                'created_at' => now(),
            ]);

            AuditLogger::log(
                action: 'subscription.renewed',
                auditable: $subscription,
                oldValues: ['current_period_end' => $baseDate->toIso8601String()],
                newValues: ['current_period_end' => $newEnd->toIso8601String()],
                tenantId: $subscription->showroom_id
            );

            return $subscription;
        });
    }

    /**
     * Switch an active subscription to a different plan tier.
     */
    public function changePlan(
        Subscription $subscription,
        SubscriptionPlan $newPlan,
        ?int $adminUserId = null
    ): Subscription {
        return DB::transaction(function () use ($subscription, $newPlan, $adminUserId) {
            $oldPlanId = $subscription->plan_id;

            $subscription->update([
                'plan_id' => $newPlan->id,
            ]);

            SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'showroom_id' => $subscription->showroom_id,
                'admin_user_id' => $adminUserId,
                'action' => 'plan_changed',
                'previous_status' => $subscription->status->value,
                'new_status' => $subscription->status->value,
                'previous_plan_id' => $oldPlanId,
                'new_plan_id' => $newPlan->id,
                'notes' => "Plan changed to {$newPlan->name}",
                'created_at' => now(),
            ]);

            AuditLogger::log(
                action: 'subscription.plan_changed',
                auditable: $subscription,
                oldValues: ['plan_id' => $oldPlanId],
                newValues: ['plan_id' => $newPlan->id],
                tenantId: $subscription->showroom_id
            );

            return $subscription;
        });
    }

    /**
     * Manually extend expiry date of a showroom subscription.
     */
    public function extendExpiry(
        Subscription $subscription,
        int $days,
        string $reason,
        ?int $adminUserId = null
    ): Subscription {
        return DB::transaction(function () use ($subscription, $days, $reason, $adminUserId) {
            $baseDate = $subscription->current_period_end->isFuture()
                ? $subscription->current_period_end
                : now();

            $newEnd = $baseDate->copy()->addDays($days);
            $prevStatus = $subscription->status->value;

            $subscription->update([
                'current_period_end' => $newEnd,
                'status' => SubscriptionStatus::Active,
            ]);

            $subscription->showroom->update(['status' => 'active']);

            SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'showroom_id' => $subscription->showroom_id,
                'admin_user_id' => $adminUserId,
                'action' => 'expiry_extended',
                'previous_status' => $prevStatus,
                'new_status' => SubscriptionStatus::Active->value,
                'previous_plan_id' => $subscription->plan_id,
                'new_plan_id' => $subscription->plan_id,
                'notes' => "Extended by {$days} days. Reason: {$reason}",
                'created_at' => now(),
            ]);

            AuditLogger::log(
                action: 'subscription.expiry_extended',
                auditable: $subscription,
                oldValues: ['current_period_end' => $baseDate->toIso8601String(), 'status' => $prevStatus],
                newValues: ['current_period_end' => $newEnd->toIso8601String(), 'status' => 'active', 'reason' => $reason],
                tenantId: $subscription->showroom_id
            );

            return $subscription;
        });
    }

    /**
     * Cancel a showroom subscription.
     */
    public function cancel(
        Subscription $subscription,
        string $reason,
        bool $immediately = false,
        ?int $adminUserId = null
    ): Subscription {
        return DB::transaction(function () use ($subscription, $reason, $immediately, $adminUserId) {
            $prevStatus = $subscription->status->value;
            $newStatus = $immediately ? SubscriptionStatus::Cancelled : $subscription->status;

            $payload = [
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
                'auto_renew' => false,
            ];

            if ($immediately) {
                $payload['status'] = SubscriptionStatus::Cancelled;
                $payload['current_period_end'] = now();
                $subscription->showroom->update(['status' => 'cancelled']);
            }

            $subscription->update($payload);

            SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'showroom_id' => $subscription->showroom_id,
                'admin_user_id' => $adminUserId,
                'action' => 'cancelled',
                'previous_status' => $prevStatus,
                'new_status' => $newStatus->value,
                'previous_plan_id' => $subscription->plan_id,
                'new_plan_id' => $subscription->plan_id,
                'notes' => $immediately ? "Cancelled immediately: {$reason}" : "Cancelled at period end: {$reason}",
                'created_at' => now(),
            ]);

            AuditLogger::log(
                action: 'subscription.cancelled',
                auditable: $subscription,
                oldValues: ['status' => $prevStatus, 'auto_renew' => true],
                newValues: ['status' => $newStatus->value, 'auto_renew' => false, 'reason' => $reason],
                tenantId: $subscription->showroom_id
            );

            return $subscription;
        });
    }

    /**
     * Synchronize and evaluate subscription expiry status.
     */
    public function checkExpiryStatus(Subscription $subscription): SubscriptionStatus
    {
        if ($subscription->status === SubscriptionStatus::Suspended ||
            $subscription->status === SubscriptionStatus::Cancelled) {
            return $subscription->status;
        }

        if ($subscription->current_period_end->isFuture()) {
            return $subscription->status;
        }

        $daysPast = now()->diffInDays($subscription->current_period_end);

        // 3 days grace period
        $newStatus = $daysPast <= 3
            ? SubscriptionStatus::PastDue
            : SubscriptionStatus::Expired;

        if ($subscription->status !== $newStatus) {
            $subscription->update(['status' => $newStatus]);
            $subscription->showroom->update(['status' => $newStatus === SubscriptionStatus::Expired ? 'suspended' : 'trial']);
        }

        return $newStatus;
    }
}
