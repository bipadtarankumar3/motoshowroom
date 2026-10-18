<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Services\ShowroomNotificationService;
use Illuminate\Console\Command;

class SendSubscriptionExpiryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:send-expiry-reminders {--days=7 : Number of days before expiry to alert}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to showroom owners whose subscriptions are expiring soon';

    /**
     * Execute the console command.
     */
    public function handle(ShowroomNotificationService $notificationService): int
    {
        $days = (int) $this->option('days');
        $threshold = now()->addDays($days);

        $subscriptions = Subscription::with(['showroom.ownerMembership.user', 'plan'])
            ->where('status', SubscriptionStatus::Active->value)
            ->whereNotNull('current_period_end')
            ->where('current_period_end', '>=', now())
            ->where('current_period_end', '<=', $threshold)
            ->get();

        $this->info("Found {$subscriptions->count()} active subscription(s) expiring within {$days} days.");

        $sentCount = 0;
        foreach ($subscriptions as $subscription) {
            $showroom = $subscription->showroom;
            if (! $showroom) {
                continue;
            }

            $notificationService->notifySubscriptionExpiring($showroom, $subscription);
            $this->line("• Sent expiry alert for Showroom: {$showroom->name} (Plan: {$subscription->plan->name})");
            $sentCount++;
        }

        $this->info("Successfully dispatched {$sentCount} subscription expiration alert(s).");

        return self::SUCCESS;
    }
}
