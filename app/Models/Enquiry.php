<?php

namespace App\Models;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Enquiry extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'user_id',
        'bike_id',
        'enquiry_number',
        'type',
        'customer_name',
        'customer_email',
        'customer_phone',
        'preferred_date',
        'preferred_time_slot',
        'driving_license_number',
        'status',
        'customer_notes',
        'staff_notes',
        'assigned_to_user_id',
        'scheduled_at',
        'contacted_at',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => EnquiryType::class,
            'status' => EnquiryStatus::class,
            'preferred_date' => 'date',
            'scheduled_at' => 'datetime',
            'contacted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Boot model to auto-generate human-friendly reference number.
     */
    protected static function booted(): void
    {
        static::creating(function (Enquiry $enquiry): void {
            if (empty($enquiry->enquiry_number)) {
                $prefix = 'ENQ-'.date('Ymd');
                $randomSuffix = strtoupper(Str::random(4));
                $enquiry->enquiry_number = "{$prefix}-{$randomSuffix}";
            }
        });
    }

    /**
     * The customer user account if submitted by an authenticated user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The specific motorcycle this enquiry or test ride concerns.
     */
    public function bike(): BelongsTo
    {
        return $this->belongsTo(Bike::class)->withTrashed();
    }

    /**
     * Dealership staff member assigned to follow up on this lead.
     */
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Scratch card participations unlocked by this bike enquiry/booking.
     */
    public function scratchCardParticipations(): HasMany
    {
        return $this->hasMany(ScratchCardParticipation::class, 'enquiry_id');
    }

    /**
     * Scope query to active, unresolved leads requiring dealership action.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            EnquiryStatus::New->value,
            EnquiryStatus::Contacted->value,
            EnquiryStatus::TestRideScheduled->value,
            EnquiryStatus::Negotiation->value,
            EnquiryStatus::BookingPending->value,
        ]);
    }

    /**
     * Filter by enquiry type (purchase, test_ride, general).
     */
    public function scopeFilterType(Builder $query, ?string $type): Builder
    {
        if (blank($type) || $type === 'all') {
            return $query;
        }

        return $query->where('type', $type);
    }

    /**
     * Filter by lead lifecycle status.
     */
    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status) || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Search by customer name, phone, email, enquiry number or notes.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('customer_name', 'like', "%{$term}%")
                ->orWhere('customer_email', 'like', "%{$term}%")
                ->orWhere('customer_phone', 'like', "%{$term}%")
                ->orWhere('enquiry_number', 'like', "%{$term}%")
                ->orWhere('customer_notes', 'like', "%{$term}%");
        });
    }

    /**
     * Determine if this enquiry is a test ride booking request.
     */
    public function isTestRide(): bool
    {
        return $this->type === EnquiryType::TestRide;
    }

    /**
     * Determine if this enquiry is a purchase enquiry.
     */
    public function isPurchase(): bool
    {
        return $this->type === EnquiryType::Purchase;
    }
}
