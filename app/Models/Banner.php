<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'badge',
        'title_prefix',
        'title_highlight',
        'title_suffix',
        'subtitle',
        'btn_primary_text',
        'btn_primary_url',
        'btn_secondary_text',
        'btn_secondary_url',
        'feature_1_title',
        'feature_1_sub',
        'feature_2_title',
        'feature_2_sub',
        'feature_3_title',
        'feature_3_sub',
        'image_path',
        'image_url',
        'is_active',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Showroom that owns this banner.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }

    /**
     * Get the display image URL, checking uploaded file storage first, then URL, then fallback.
     */
    public function getDisplayImageUrlAttribute(): string
    {
        if (! empty($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        if (! empty($this->image_url)) {
            return $this->image_url;
        }

        return asset('images/hero/hero_adventure_red.jpg');
    }
}
