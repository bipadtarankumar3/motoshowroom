<?php

namespace App\Enums;

/**
 * Job applicant recruitment workflow statuses.
 */
enum ApplicationStatus: string
{
    case New = 'new';
    case Reviewing = 'reviewing';
    case Shortlisted = 'shortlisted';
    case InterviewScheduled = 'interview_scheduled';
    case Offered = 'offered';
    case Rejected = 'rejected';
    case Hired = 'hired';

    /**
     * Human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::New => 'New Application',
            self::Reviewing => 'Under Review',
            self::Shortlisted => 'Shortlisted',
            self::InterviewScheduled => 'Interview Scheduled',
            self::Offered => 'Offer Extended',
            self::Rejected => 'Application Declined',
            self::Hired => 'Candidate Hired 🎉',
        };
    }

    /**
     * Tailwind badge styles for display.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::New => 'bg-slate-500/10 text-slate-400 border border-slate-500/20',
            self::Reviewing => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::Shortlisted => 'bg-sky-500/10 text-sky-400 border border-sky-500/20',
            self::InterviewScheduled => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
            self::Offered => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
            self::Rejected => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
            self::Hired => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
        };
    }
}
