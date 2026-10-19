<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ScratchCardParticipation extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'campaign_id',
        'prize_id',
        'user_id',
        'service_booking_id',
        'enquiry_id',
        'booking_type',
        'customer_name',
        'customer_email',
        'customer_phone',
        'participation_token',
        'is_scratched',
        'scratched_at',
        'coupon_code',
        'is_redeemed',
        'redeemed_at',
        'redeemed_by_user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_scratched' => 'boolean',
            'scratched_at' => 'datetime',
            'is_redeemed' => 'boolean',
            'redeemed_at' => 'datetime',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'participation_token';
    }

    /**
     * Boot model to automatically generate secure participation token.
     */
    protected static function booted(): void
    {
        static::creating(function (ScratchCardParticipation $participation): void {
            if (empty($participation->participation_token)) {
                $participation->participation_token = Str::random(40);
            }
        });
    }

    /**
     * The showroom dealership running this promotion.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }

    /**
     * The campaign this participation was issued under.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(PromotionalCampaign::class, 'campaign_id');
    }

    /**
     * The prize won (if revealed/assigned).
     */
    public function prize(): BelongsTo
    {
        return $this->belongsTo(CampaignPrize::class, 'prize_id')->withTrashed();
    }

    /**
     * Registered customer user account (if logged in).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Associated workshop booking if issued on service completion.
     */
    public function serviceBooking(): BelongsTo
    {
        return $this->belongsTo(ServiceBooking::class);
    }

    /**
     * Associated bike enquiry/test-ride booking if issued on bike booking.
     */
    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    /**
     * Staff member who processed the in-store coupon redemption.
     */
    public function redeemedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by_user_id');
    }

    /**
     * Scope to scratched cards.
     */
    public function scopeScratched(Builder $query): Builder
    {
        return $query->where('is_scratched', true);
    }

    /**
     * Scope to redeemed coupons.
     */
    public function scopeRedeemed(Builder $query): Builder
    {
        return $query->where('is_redeemed', true);
    }

    /**
     * Search by customer name, email, phone, or coupon code.
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
                ->orWhere('coupon_code', 'like', "%{$term}%");
        });
    }
}
