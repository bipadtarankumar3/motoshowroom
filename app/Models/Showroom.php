<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Showroom extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'custom_domain',
        'domain_verified_at',
        'email',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'logo_url',
        'banner_url',
        'brand_color_primary',
        'brand_color_accent',
        'status',
        'public_website_enabled',
        'read_only_mode',
        'settings',
    ];

    /**
     * Get the route key for the model to resolve by slug by default.
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
            'domain_verified_at' => 'datetime',
            'public_website_enabled' => 'boolean',
            'read_only_mode' => 'boolean',
            'settings' => 'array',
        ];
    }

    /**
     * All memberships of staff and owners for this showroom.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(ShowroomMembership::class);
    }

    /**
     * All users associated with this showroom.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'showroom_memberships')
            ->withPivot('role', 'permissions', 'status')
            ->withTimestamps();
    }

    /**
     * The primary owner membership for this showroom.
     */
    public function ownerMembership(): HasOne
    {
        return $this->hasOne(ShowroomMembership::class)->where('role', 'owner');
    }

    /**
     * Check if the showroom is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial'], true);
    }

    /**
     * Determine if the public website is accessible.
     */
    public function isWebsiteAccessible(): bool
    {
        return $this->public_website_enabled && $this->status !== 'suspended';
    }

    /**
     * Audit logs related to this showroom.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'tenant_id')->latest('created_at');
    }

    /**
     * Get the owner user of the showroom.
     */
    public function getOwnerAttribute(): ?User
    {
        return $this->ownerMembership?->user;
    }

    /**
     * All subscriptions for this showroom.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->latest('created_at');
    }

    /**
     * The active or current subscription for this showroom.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    /**
     * Determine if showroom has access to a specific plan feature flag.
     */
    public function canAccessFeature(string $featureKey): bool
    {
        return $this->subscription?->canAccessFeature($featureKey) ?? false;
    }

    /**
     * Determine if showroom has reached an entitlement limit.
     */
    public function hasReachedLimit(string $limitKey, int $currentCount): bool
    {
        if (! $this->subscription) {
            return true;
        }

        return $this->subscription->hasReachedLimit($limitKey, $currentCount);
    }

    /**
     * Get a setting from the json settings attribute with optional fallback default.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Get the marketing headline or default to showroom name.
     */
    public function getHeadlineAttribute(): string
    {
        return $this->getSetting('headline', 'Welcome to '.$this->name);
    }

    /**
     * Get the marketing tagline.
     */
    public function getTaglineAttribute(): string
    {
        return $this->getSetting('tagline', 'Authorized Premium Motorcycle Dealership & Certified Service Center');
    }

    /**
     * Get the about us description story.
     */
    public function getAboutTextAttribute(): string
    {
        return $this->getSetting('about_text', "Welcome to {$this->name}. We are dedicated to providing motorcycle enthusiasts with world-class bikes, genuine parts, factory-certified service technicians, and an exceptional customer experience.");
    }

    /**
     * Get social media handles.
     *
     * @return array<string, string>
     */
    public function getSocialLinks(): array
    {
        return $this->getSetting('social_links', [
            'instagram' => '',
            'youtube' => '',
            'facebook' => '',
            'twitter' => '',
        ]);
    }

    /**
     * Get weekly operating schedule.
     *
     * @return array<string, array{open: string, close: string, is_closed: bool}>
     */
    public function getOperatingHours(): array
    {
        $default = [
            'monday' => ['open' => '09:30', 'close' => '20:00', 'is_closed' => false],
            'tuesday' => ['open' => '09:30', 'close' => '20:00', 'is_closed' => false],
            'wednesday' => ['open' => '09:30', 'close' => '20:00', 'is_closed' => false],
            'thursday' => ['open' => '09:30', 'close' => '20:00', 'is_closed' => false],
            'friday' => ['open' => '09:30', 'close' => '20:00', 'is_closed' => false],
            'saturday' => ['open' => '09:30', 'close' => '20:30', 'is_closed' => false],
            'sunday' => ['open' => '10:00', 'close' => '14:00', 'is_closed' => false],
        ];

        return $this->getSetting('operating_hours', $default);
    }

    /**
     * Determine if the showroom is currently open for business.
     */
    public function isOpenNow(): bool
    {
        $today = strtolower(now()->format('l'));
        $hours = $this->getOperatingHours();

        if (! isset($hours[$today]) || ($hours[$today]['is_closed'] ?? false)) {
            return false;
        }

        $now = now()->format('H:i');
        $open = $hours[$today]['open'] ?? '09:30';
        $close = $hours[$today]['close'] ?? '20:00';

        return $now >= $open && $now <= $close;
    }
}
