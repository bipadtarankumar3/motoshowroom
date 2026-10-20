<?php

namespace App\Models;

use App\Enums\BikeCategory;
use App\Enums\BikeStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bike extends Model
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
        'sku',
        'brand',
        'model_year',
        'category',
        'category_id',
        'engine_cc',
        'power_bhp',
        'torque_nm',
        'fuel_type',
        'transmission',
        'mileage_kmpl',
        'top_speed_kmh',
        'curb_weight_kg',
        'price',
        'is_featured',
        'is_published',
        'status',
        'description',
        'specifications',
        'has_360_view',
        'viewer_type',
        'model_3d_path',
        'viewer_settings',
        'hotspots',
    ];

    /**
     * Get the route key for implicit model binding.
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
            'price' => 'decimal:2',
            'power_bhp' => 'decimal:2',
            'torque_nm' => 'decimal:2',
            'mileage_kmpl' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'has_360_view' => 'boolean',
            'viewer_settings' => 'array',
            'hotspots' => 'array',
            'specifications' => 'array',
            'category' => BikeCategory::class,
            'status' => BikeStatus::class,
            'model_year' => 'integer',
            'engine_cc' => 'integer',
            'top_speed_kmh' => 'integer',
            'curb_weight_kg' => 'integer',
        ];
    }

    /**
     * Custom showroom category master record (if linked).
     */
    public function categoryMaster(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get human-readable category name.
     */
    public function getCategoryDisplayAttribute(): string
    {
        return $this->categoryMaster?->name ?? ($this->category instanceof BikeCategory ? $this->category->label() : ucfirst((string) $this->category));
    }

    /**
     * Standard media gallery images associated with this motorcycle (excluding 360 turntable frames).
     */
    public function images(): HasMany
    {
        return $this->hasMany(BikeImage::class)
            ->where('is_360_frame', false)
            ->orderByDesc('is_primary')
            ->orderBy('sort_order');
    }

    /**
     * All media records including both gallery photos and 360-degree frames.
     */
    public function allImages(): HasMany
    {
        return $this->hasMany(BikeImage::class)->orderBy('sort_order');
    }

    /**
     * Interactive 360-degree turntable frames.
     */
    public function frames360(): HasMany
    {
        return $this->hasMany(BikeImage::class)
            ->where('is_360_frame', true)
            ->orderBy('frame_angle')
            ->orderBy('sort_order');
    }

    /**
     * Check if this motorcycle has active 360 assets ready for presentation.
     */
    public function has360View(): bool
    {
        if (! $this->has_360_view) {
            return false;
        }

        if (in_array($this->viewer_type, ['gltf_model', 'external_embed', 'video_loop'], true) && ! empty($this->model_3d_path)) {
            return true;
        }

        return $this->frames360()->exists();
    }

    /**
     * Check if this motorcycle's 360 viewer is entitled and active for display.
     */
    public function canShow360View(): bool
    {
        $hasEntitlement = $this->showroom && $this->showroom->canAccessFeature('has_360_viewer');

        return $hasEntitlement && $this->has360View();
    }

    /**
     * Get normalized hotspots array.
     *
     * @return list<array<string, mixed>>
     */
    public function getHotspotsListAttribute(): array
    {
        return is_array($this->hotspots) ? array_values($this->hotspots) : [];
    }

    /**
     * Get merged viewer settings with default fallbacks.
     *
     * @return array<string, mixed>
     */
    public function getViewerSettingsOrDefaultAttribute(): array
    {
        return array_merge([
            'auto_rotate' => true,
            'rotation_speed' => 3,
            'reverse_direction' => false,
            'enable_zoom' => true,
            'default_angle' => 0,
        ], is_array($this->viewer_settings) ? $this->viewer_settings : []);
    }

    /**
     * The designated primary gallery photo.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(BikeImage::class)
            ->where('is_360_frame', false)
            ->where('is_primary', true)
            ->latestOfMany();
    }

    /**
     * Available color and trim variants for this motorcycle.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(BikeVariant::class);
    }

    /**
     * Return primary image URL or fallback to first image or default motorcycle silhouette placeholder.
     */
    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->images->firstWhere('is_primary', true);
        if ($primary && ! empty($primary->image_url)) {
            return $primary->image_url;
        }

        $first = $this->images->first();
        if ($first && ! empty($first->image_url)) {
            return $first->image_url;
        }

        return 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=800&q=80';
    }

    /**
     * Human-friendly formatted Indian rupee price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₹'.number_format((float) $this->price, 2);
    }

    /**
     * Calculate aggregated on-hand stock quantity across all variants.
     */
    public function getTotalStockAttribute(): int
    {
        if ($this->variants->isNotEmpty()) {
            return (int) $this->variants->sum('stock_quantity');
        }

        return $this->status === BikeStatus::InStock ? 1 : 0;
    }

    /**
     * Scope query to only published inventory for storefront presentation.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope query to featured showroom highlight motorcycles.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope query to units in stock or available for order.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereIn('status', [BikeStatus::InStock->value, BikeStatus::Incoming->value]);
    }

    /**
     * Search filter by name, brand, SKU or description.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Filter by category enum or string value.
     */
    public function scopeFilterCategory(Builder $query, ?string $category): Builder
    {
        if (blank($category) || $category === 'all') {
            return $query;
        }

        return $query->where('category', $category);
    }

    /**
     * Filter by stock status enum or string value.
     */
    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status) || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }
}
