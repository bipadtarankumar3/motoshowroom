<?php

namespace App\Models;

use App\Enums\JobDepartment;
use App\Enums\JobType;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JobOpening extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'department',
        'job_type',
        'experience_level',
        'location',
        'salary_range',
        'description',
        'requirements',
        'benefits',
        'is_active',
        'deadline',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'department' => JobDepartment::class,
            'job_type' => JobType::class,
            'is_active' => 'boolean',
            'deadline' => 'date',
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
        static::creating(function (JobOpening $job): void {
            if (empty($job->slug)) {
                $baseSlug = Str::slug($job->title);
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('tenant_id', $job->tenant_id)->where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $job->slug = $slug;
            }
        });
    }

    /**
     * The showroom dealership offering this vacancy.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }

    /**
     * Candidate applications submitted for this role.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class)->latest('created_at');
    }

    /**
     * Scope to currently active and open positions.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q): void {
                $q->whereNull('deadline')
                    ->orWhereDate('deadline', '>=', today());
            });
    }

    /**
     * Scope filter by functional department.
     */
    public function scopeFilterDepartment(Builder $query, ?string $department): Builder
    {
        if (blank($department) || $department === 'all') {
            return $query;
        }

        return $query->where('department', $department);
    }

    /**
     * Scope filter by employment contract type.
     */
    public function scopeFilterType(Builder $query, ?string $jobType): Builder
    {
        if (blank($jobType) || $jobType === 'all') {
            return $query;
        }

        return $query->where('job_type', $jobType);
    }

    /**
     * Search job openings by title, location, or description.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('requirements', 'like', "%{$term}%");
        });
    }
}
