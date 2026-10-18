<?php

namespace App\Enums;

/**
 * Types of dealership customer enquiries.
 */
enum EnquiryType: string
{
    case Purchase = 'purchase';
    case TestRide = 'test_ride';
    case General = 'general';

    /**
     * Get a human-readable display label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Purchase => 'Purchase Enquiry',
            self::TestRide => 'Test Ride Request',
            self::General => 'General Consultation',
        };
    }

    /**
     * Get Tailwind badge classes for display.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Purchase => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            self::TestRide => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::General => 'bg-sky-500/10 text-sky-400 border border-sky-500/20',
        };
    }
}
