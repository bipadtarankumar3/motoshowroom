<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'annual_price',
        'currency',
        'trial_days',
        'max_staff_accounts',
        'max_bike_listings',
        'max_monthly_bookings',
        'media_storage_mb',
        'has_careers_module',
        'has_scratch_cards',
        'has_360_viewer',
        'has_ai_assistant',
        'has_custom_domain',
        'has_advanced_analytics',
        'features_json',
        'is_active',
        'sort_order',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monthly_price' => 'decimal:2',
            'annual_price' => 'decimal:2',
            'trial_days' => 'integer',
            'max_staff_accounts' => 'integer',
            'max_bike_listings' => 'integer',
            'max_monthly_bookings' => 'integer',
            'media_storage_mb' => 'integer',
            'has_careers_module' => 'boolean',
            'has_scratch_cards' => 'boolean',
            'has_360_viewer' => 'boolean',
            'has_ai_assistant' => 'boolean',
            'has_custom_domain' => 'boolean',
            'has_advanced_analytics' => 'boolean',
            'features_json' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Subscriptions currently attached to this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    /**
     * Scope to filter only active plans available for subscription.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order')->orderBy('monthly_price');
    }

    /**
     * Determine if this plan is completely free (e.g. community/trial tier).
     */
    public function isFree(): bool
    {
        return (float) $this->monthly_price === 0.0 && (float) $this->annual_price === 0.0;
    }

    /**
     * Format monthly price with currency symbol.
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        $symbol = $this->currency === 'INR' ? '₹' : '$';

        return $symbol.number_format((float) $this->monthly_price, 2);
    }

    /**
     * Format annual price with currency symbol.
     */
    public function getFormattedAnnualPriceAttribute(): string
    {
        $symbol = $this->currency === 'INR' ? '₹' : '$';

        return $symbol.number_format((float) $this->annual_price, 2);
    }
}
