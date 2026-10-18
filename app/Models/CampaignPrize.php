<?php

namespace App\Models;

use App\Enums\PrizeType;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignPrize extends Model
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
        'title',
        'prize_type',
        'discount_amount',
        'discount_percentage',
        'total_quantity',
        'remaining_quantity',
        'probability_weight',
        'icon_or_image',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prize_type' => PrizeType::class,
            'discount_amount' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'total_quantity' => 'integer',
            'remaining_quantity' => 'integer',
            'probability_weight' => 'integer',
        ];
    }

    /**
     * The showroom owning this prize tier.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }

    /**
     * The promotional campaign this prize belongs to.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(PromotionalCampaign::class, 'campaign_id');
    }

    /**
     * Participations that won this prize.
     */
    public function participations(): HasMany
    {
        return $this->hasMany(ScratchCardParticipation::class, 'prize_id');
    }

    /**
     * Determine if this prize is still available to be won.
     */
    public function hasInventoryAvailable(): bool
    {
        if ($this->prize_type === PrizeType::BetterLuck) {
            return true;
        }

        return $this->remaining_quantity > 0;
    }
}
