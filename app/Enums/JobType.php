<?php

namespace App\Enums;

/**
 * Employment contract types for dealership job openings.
 */
enum JobType: string
{
    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Contract = 'contract';
    case Internship = 'internship';

    /**
     * Human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::FullTime => 'Full-Time Position',
            self::PartTime => 'Part-Time',
            self::Contract => 'Fixed-Term Contract',
            self::Internship => 'Apprenticeship / Internship',
        };
    }
}
