<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
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
        'payable_type',
        'payable_id',
        'purpose',
        'amount',
        'currency',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'status',
        'method',
        'signature_verified',
        'error_code',
        'error_description',
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
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'signature_verified' => 'boolean',
            'notes' => 'array',
        ];
    }

    /**
     * The showroom dealership for which this transaction was processed.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }

    /**
     * The customer or showroom staff account who initiated payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The entity paid for (e.g. Subscription, ServiceBooking).
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Any refunds associated with this payment transaction.
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    /**
     * Scope query to successfully captured payments.
     */
    public function scopeCaptured(Builder $query): Builder
    {
        return $query->where('status', PaymentStatus::Captured->value);
    }

    /**
     * Scope query to pending payments.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PaymentStatus::Pending->value);
    }

    /**
     * Filter by payment lifecycle status.
     */
    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status) || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Search by transaction IDs, purpose, or method.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('razorpay_order_id', 'like', "%{$term}%")
                ->orWhere('razorpay_payment_id', 'like', "%{$term}%")
                ->orWhere('purpose', 'like', "%{$term}%")
                ->orWhere('method', 'like', "%{$term}%");
        });
    }

    /**
     * Check if payment is verified and captured.
     */
    public function isCaptured(): bool
    {
        return $this->status === PaymentStatus::Captured && $this->signature_verified;
    }
}
