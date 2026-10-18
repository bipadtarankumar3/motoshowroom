<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BikeImage extends Model
{
    use BelongsToTenant, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'bike_id',
        'image_url',
        'alt_text',
        'is_primary',
        'is_360_frame',
        'frame_angle',
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
            'is_primary' => 'boolean',
            'is_360_frame' => 'boolean',
            'frame_angle' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope query to standard media gallery photos (excluding 360 frames).
     */
    public function scopeGallery($query)
    {
        return $query->where('is_360_frame', false)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    /**
     * Scope query to 360-degree turntable frames.
     */
    public function scopeFrames360($query)
    {
        return $query->where('is_360_frame', true)->orderBy('frame_angle')->orderBy('sort_order');
    }

    /**
     * The motorcycle this photo belongs to.
     */
    public function bike(): BelongsTo
    {
        return $this->belongsTo(Bike::class);
    }
}
