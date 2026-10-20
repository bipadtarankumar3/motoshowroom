<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShowroomMembership extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'showroom_id',
        'user_id',
        'role',
        'permissions',
        'status',
        'availability_status',
        'department',
        'designation',
        'shift',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    /**
     * Relationship to the showroom.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class);
    }

    /**
     * Relationship to the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if this member is the showroom owner.
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Check if membership matches specific role(s).
     *
     * @param  string|list<string>  $roles
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }

        return in_array($this->role, $roles, true);
    }

    /**
     * Check if membership has a specific granted permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        $permissions = $this->permissions ?? [];

        return in_array($permission, $permissions, true);
    }
}
