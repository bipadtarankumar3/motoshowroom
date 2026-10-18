<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BikeVariant extends Model
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
        'name',
        'color_name',
        'color_hex',
        'price_difference',
        'stock_quantity',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_difference' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    /**
     * The motorcycle this variant belongs to.
     */
    public function bike(): BelongsTo
    {
        return $this->belongsTo(Bike::class);
    }

    /**
     * Get the final effective price for this variant including delta.
     */
    public function getEffectivePriceAttribute(): float
    {
        $base = (float) ($this->bike?->price ?? 0);

        return $base + (float) $this->price_difference;
    }
}
