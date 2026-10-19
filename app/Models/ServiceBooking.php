<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\ServiceType;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ServiceBooking extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    /**
     * Standard daily service appointment windows.
     *
     * @var list<string>
     */
    public const TIME_SLOTS = [
        '09:00 - 11:00',
        '11:00 - 13:00',
        '14:00 - 16:00',
        '16:00 - 18:00',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'user_id',
        'bike_id',
        'showroom_service_id',
        'booking_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'bike_make_model',
        'registration_number',
        'odometer_reading',
        'service_type',
        'booking_date',
        'time_slot',
        'bay_number',
        'status',
        'estimated_cost',
        'final_cost',
        'payment_status',
        'customer_notes',
        'terms_accepted',
        'payment_required',
        'technician_notes',
        'assigned_to_user_id',
        'confirmed_at',
        'checked_in_at',
        'in_service_at',
        'ready_at',
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
            'service_type' => ServiceType::class,
            'status' => BookingStatus::class,
            'payment_status' => PaymentStatus::class,
            'booking_date' => 'date',
            'estimated_cost' => 'decimal:2',
            'final_cost' => 'decimal:2',
            'terms_accepted' => 'boolean',
            'payment_required' => 'boolean',
            'confirmed_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'in_service_at' => 'datetime',
            'ready_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Auto-generate human-friendly service tracking reference number.
     */
    protected static function booted(): void
    {
        static::creating(function (ServiceBooking $booking): void {
            if (empty($booking->booking_number)) {
                $prefix = 'SRV-'.date('Ymd');
                $randomSuffix = strtoupper(Str::random(4));
                $booking->booking_number = "{$prefix}-{$randomSuffix}";
            }
        });
    }

    /**
     * Showroom service center where this appointment was booked.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }

    /**
     * Registered customer account if authenticated during booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Inventory bike model if customer selected an authorized model.
     */
    public function bike(): BelongsTo
    {
        return $this->belongsTo(Bike::class)->withTrashed();
    }

    /**
     * Certified service technician assigned to service this motorcycle.
     */
    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Scratch card participations unlocked by this service appointment.
     */
    public function scratchCardParticipations(): HasMany
    {
        return $this->hasMany(ScratchCardParticipation::class, 'service_booking_id');
    }

    /**
     * Custom showroom workshop service package if assigned.
     */
    public function showroomService(): BelongsTo
    {
        return $this->belongsTo(ShowroomService::class, 'showroom_service_id')->withTrashed();
    }

    /**
     * Human-readable service package title.
     */
    public function getServiceName(): string
    {
        if ($this->showroomService) {
            return $this->showroomService->name;
        }

        return $this->service_type instanceof ServiceType ? $this->service_type->label() : (string) $this->service_type;
    }

    /**
     * Scope to active appointments occupying service bay slots.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            BookingStatus::Cancelled->value,
            BookingStatus::NoShow->value,
        ]);
    }

    /**
     * Scope to today's scheduled service appointments.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('booking_date', today());
    }

    /**
     * Scope to appointments matching specific lifecycle status.
     */
    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status) || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Scope to appointments on a specific calendar date.
     */
    public function scopeFilterDate(Builder $query, ?string $date): Builder
    {
        if (blank($date)) {
            return $query;
        }

        return $query->whereDate('booking_date', $date);
    }

    /**
     * Search by customer name, registration number, phone, email, or booking number.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('customer_name', 'like', "%{$term}%")
                ->orWhere('registration_number', 'like', "%{$term}%")
                ->orWhere('booking_number', 'like', "%{$term}%")
                ->orWhere('customer_phone', 'like', "%{$term}%")
                ->orWhere('customer_email', 'like', "%{$term}%")
                ->orWhere('bike_make_model', 'like', "%{$term}%");
        });
    }

    /**
     * Count existing active bookings for a given date and time slot in a showroom.
     */
    public static function getSlotOccupancy(int $tenantId, string $date, string $timeSlot): int
    {
        return static::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->whereDate('booking_date', $date)
            ->where('time_slot', $timeSlot)
            ->whereNotIn('status', [
                BookingStatus::Cancelled->value,
                BookingStatus::NoShow->value,
            ])
            ->count();
    }

    /**
     * Check if a showroom has bay capacity available for a specific date and time slot.
     */
    public static function isSlotAvailable(Showroom $showroom, string $date, string $timeSlot): bool
    {
        $occupancy = static::getSlotOccupancy($showroom->id, $date, $timeSlot);
        $capacity = $showroom->service_bay_capacity;

        return $occupancy < $capacity;
    }

    /**
     * Determine if service is ready for customer collection.
     */
    public function isReady(): bool
    {
        return $this->status === BookingStatus::ReadyForCollection;
    }

    /**
     * Determine if service is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === BookingStatus::Completed;
    }

    /**
     * Determine if invoice has been paid/captured.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::Captured;
    }
}
