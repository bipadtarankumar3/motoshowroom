<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ShowroomService extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'showroom_services';

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
        'price',
        'estimated_duration_minutes',
        'is_payment_enabled',
        'is_active',
        'badge',
        'features',
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
            'price' => 'decimal:2',
            'estimated_duration_minutes' => 'integer',
            'is_payment_enabled' => 'boolean',
            'is_active' => 'boolean',
            'features' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Auto-generate slug upon creation if not explicitly provided.
     */
    protected static function booted(): void
    {
        static::creating(function (ShowroomService $service): void {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    /**
     * The showroom that owns this service package.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }

    /**
     * Customer bookings created for this service package.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class, 'showroom_service_id');
    }

    /**
     * Human-readable formatted currency price.
     */
    public function formattedPrice(): string
    {
        return '₹'.number_format((float) $this->price);
    }

    /**
     * Human-friendly formatted duration.
     */
    public function formattedDuration(): string
    {
        $mins = $this->estimated_duration_minutes;
        if ($mins >= 60) {
            $hours = floor($mins / 60);
            $remainingMins = $mins % 60;

            return $remainingMins > 0 ? "{$hours}h {$remainingMins}m" : "{$hours} hr".($hours > 1 ? 's' : '');
        }

        return "{$mins} mins";
    }

    /**
     * Badge classes for aesthetic presentation.
     */
    public function badgeClasses(): string
    {
        return match (strtolower((string) $this->badge)) {
            'popular', 'most popular' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'recommended' => 'bg-purple-50 text-purple-700 border-purple-200',
            'best value' => 'bg-sky-50 text-sky-700 border-sky-200',
            'express', 'quick' => 'bg-amber-50 text-amber-700 border-amber-200',
            'premium' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
