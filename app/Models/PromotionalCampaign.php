<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PromotionalCampaign extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'start_date',
        'end_date',
        'status',
        'min_booking_amount',
        'max_participations_per_customer',
        'banner_image',
        'terms_and_conditions',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CampaignStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'min_booking_amount' => 'decimal:2',
            'max_participations_per_customer' => 'integer',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Boot model to automatically generate slug.
     */
    protected static function booted(): void
    {
        static::creating(function (PromotionalCampaign $campaign): void {
            if (empty($campaign->slug)) {
                $baseSlug = Str::slug($campaign->name);
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('tenant_id', $campaign->tenant_id)->where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $campaign->slug = $slug;
            }
        });
    }

    /**
     * The showroom hosting this campaign.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }

    /**
     * Prizes configured for this promotional campaign.
     */
    public function prizes(): HasMany
    {
        return $this->hasMany(CampaignPrize::class, 'campaign_id');
    }

    /**
     * All scratch card participations issued under this campaign.
     */
    public function participations(): HasMany
    {
        return $this->hasMany(ScratchCardParticipation::class, 'campaign_id');
    }

    /**
     * Scope to currently active, live, and non-expired campaigns.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CampaignStatus::Active->value)
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today());
    }

    /**
     * Search campaigns by name or description.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Determine if this campaign is currently active, started, and not expired.
     */
    public function isCurrentlyActive(): bool
    {
        return $this->status === CampaignStatus::Active
            && $this->start_date->lte(today())
            && $this->end_date->gte(today());
    }
}
