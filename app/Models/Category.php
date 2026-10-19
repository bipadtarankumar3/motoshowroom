<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Category extends Model
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
        'subtitle',
        'description',
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
     * Showroom that owns this category.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }

    /**
     * Motorcycles associated with this custom category.
     */
    public function bikes(): HasMany
    {
        return $this->hasMany(Bike::class, 'category_id');
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

        return 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=500&q=80';
    }

    /**
     * Auto-provision standard default categories for a showroom if none exist.
     */
    public static function seedDefaultsForShowroom(Showroom $showroom): void
    {
        $defaults = [
            ['name' => 'Sports Bikes', 'slug' => 'sports', 'subtitle' => 'Track & Superbikes', 'image_url' => 'https://images.unsplash.com/photo-1609630875171-b1321377ee65?auto=format&fit=crop&w=500&q=80'],
            ['name' => 'Street Bikes', 'slug' => 'commuter', 'subtitle' => 'Naked & Roadsters', 'image_url' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=500&q=80'],
            ['name' => 'Adventure Bikes', 'slug' => 'adventure', 'subtitle' => 'Dual Sport & Off-Road', 'image_url' => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=500&q=80'],
            ['name' => 'Scooters', 'slug' => 'scooter', 'subtitle' => 'Urban & Maxi Commuters', 'image_url' => 'https://images.unsplash.com/photo-1591637333184-19aa84b3e01f?auto=format&fit=crop&w=500&q=80'],
            ['name' => 'Electric Bikes', 'slug' => 'electric', 'subtitle' => 'Clean Tech EVs', 'image_url' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=500&q=80'],
            ['name' => 'Cruiser Bikes', 'slug' => 'cruiser', 'subtitle' => 'Highway Heritage', 'image_url' => 'https://images.unsplash.com/photo-1558980664-769d59546b3d?auto=format&fit=crop&w=500&q=80'],
        ];

        foreach ($defaults as $order => $catData) {
            static::updateOrCreate(
                ['tenant_id' => $showroom->id, 'slug' => $catData['slug']],
                [
                    'name' => $catData['name'],
                    'subtitle' => $catData['subtitle'],
                    'image_url' => $catData['image_url'],
                    'is_active' => true,
                    'sort_order' => $order,
                ]
            );
        }
    }
}
