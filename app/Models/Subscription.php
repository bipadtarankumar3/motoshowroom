<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'showroom_id',
        'plan_id',
        'status',
        'billing_interval',
        'current_period_start',
        'current_period_end',
        'trial_ends_at',
        'cancelled_at',
        'cancellation_reason',
        'suspension_reason',
        'auto_renew',
        'last_payment_reference',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
            'trial_ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'auto_renew' => 'boolean',
        ];
    }

    /**
     * The showroom tenant holding this subscription.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class);
    }

    /**
     * The plan governing this subscription's entitlements.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Audit history entries of renewals, upgrades, and extensions.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(SubscriptionHistory::class)->latest('created_at');
    }

    /**
     * Determine if subscription is in an active, usable state.
     */
    public function isActive(): bool
    {
        if (! $this->status->grantsAccess()) {
            return false;
        }

        return $this->current_period_end->isFuture();
    }

    /**
     * Determine if currently within evaluation trial.
     */
    public function isTrial(): bool
    {
        return $this->status === SubscriptionStatus::Trial;
    }

    /**
     * Determine if subscription has elapsed its billing period.
     */
    public function isExpired(): bool
    {
        return $this->status === SubscriptionStatus::Expired ||
            ($this->current_period_end->isPast() && ! $this->status->grantsAccess());
    }

    /**
     * Get number of days remaining in the current billing or trial period.
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->current_period_end->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->current_period_end);
    }

    /**
     * Evaluate if a specific feature flag is granted under this active subscription.
     * During the trial period, full VIP platform capabilities are unlocked.
     */
    public function canAccessFeature(string $featureKey): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        // Trial users get full platform access to test all premium features
        if ($this->isTrial()) {
            return true;
        }

        return (bool) ($this->plan->$featureKey ?? false);
    }

    /**
     * Evaluate if a quantitative limit has been reached or exceeded.
     * During trial, generous testing headroom is provided.
     */
    public function hasReachedLimit(string $limitKey, int $currentCount): bool
    {
        if (! $this->isActive()) {
            return true;
        }

        // Unlimited testing in trial period
        if ($this->isTrial()) {
            return false;
        }

        $limit = $this->plan->$limitKey ?? null;

        if ($limit === null) {
            return false;
        }

        return $currentCount >= (int) $limit;
    }
}
