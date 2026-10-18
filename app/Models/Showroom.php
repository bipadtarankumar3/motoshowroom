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
}
