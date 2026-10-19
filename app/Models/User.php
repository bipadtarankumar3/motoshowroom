<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'status',
        'onboarding_completed_at',
        'bio',
        'address',
        'city',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * All showroom memberships for this user.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(ShowroomMembership::class);
    }

    /**
     * All showrooms this user belongs to.
     */
    public function showrooms(): BelongsToMany
    {
        return $this->belongsToMany(Showroom::class, 'showroom_memberships')
            ->withPivot('role', 'permissions', 'status')
            ->withTimestamps();
    }

    /**
     * All linked third-party OAuth identities (e.g. Google).
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * All payment transactions processed by this user.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * All test ride and purchase enquiries submitted by this user.
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'user_id');
    }

    /**
     * All workshop service appointments booked by this user.
     */
    public function serviceBookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class, 'user_id');
    }

    /**
     * System activity and security audit trail for this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    /**
     * Check if the user has an admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user has a regular user role.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Determine if this user is authorized to access a given showroom tenant.
     */
    public function canAccessTenant(int|Showroom $tenant): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $tenantId = is_object($tenant) ? $tenant->id : (int) $tenant;

        return $this->memberships()
            ->where('showroom_id', $tenantId)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get the user's membership for a specific showroom.
     */
    public function membershipFor(int|Showroom $tenant): ?ShowroomMembership
    {
        $tenantId = is_object($tenant) ? $tenant->id : (int) $tenant;

        return $this->memberships()
            ->where('showroom_id', $tenantId)
            ->first();
    }

    /**
     * Determine if this user is the registered owner of the given showroom.
     */
    public function isOwnerOf(int|Showroom $tenant): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $tenantId = is_object($tenant) ? $tenant->id : (int) $tenant;

        return $this->memberships()
            ->where('showroom_id', $tenantId)
            ->where('role', 'owner')
            ->where('status', 'active')
            ->exists();
    }
}
