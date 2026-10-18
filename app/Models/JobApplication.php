<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JobApplication extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'job_opening_id',
        'user_id',
        'applicant_number',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'current_company',
        'experience_years',
        'resume_path',
        'portfolio_url',
        'cover_letter',
        'status',
        'internal_notes',
        'rejection_reason',
        'interview_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'experience_years' => 'decimal:1',
            'interview_at' => 'datetime',
        ];
    }

    /**
     * Boot model to automatically generate human-friendly application tracking number.
     */
    protected static function booted(): void
    {
        static::creating(function (JobApplication $application): void {
            if (empty($application->applicant_number)) {
                $prefix = 'APP-'.date('Ymd');
                $randomSuffix = strtoupper(Str::random(4));
                $application->applicant_number = "{$prefix}-{$randomSuffix}";
            }
        });
    }

    /**
     * The showroom dealership conducting this recruitment.
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'tenant_id');
    }

    /**
     * The specific job opening applied for.
     */
    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class)->withTrashed();
    }

    /**
     * Candidate's registered user account if authenticated during submission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Filter by recruitment pipeline status.
     */
    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status) || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Search candidate applications by name, email, phone, applicant number, or current company.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('applicant_name', 'like', "%{$term}%")
                ->orWhere('applicant_email', 'like', "%{$term}%")
                ->orWhere('applicant_phone', 'like', "%{$term}%")
                ->orWhere('applicant_number', 'like', "%{$term}%")
                ->orWhere('current_company', 'like', "%{$term}%");
        });
    }
}
