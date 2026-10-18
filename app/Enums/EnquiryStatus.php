<?php

namespace App\Enums;

/**
 * Statuses for purchase enquiries and test ride requests.
 */
enum EnquiryStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case TestRideScheduled = 'test_ride_scheduled';
    case Negotiation = 'negotiation';
    case BookingPending = 'booking_pending';
    case Booked = 'booked';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Lost = 'lost';

    /**
     * Get a human-readable display label.
     */
    public function label(): string
    {
        return match ($this) {
            self::New => 'New Enquiry',
            self::Contacted => 'Customer Contacted',
            self::TestRideScheduled => 'Test Ride Scheduled',
            self::Negotiation => 'In Negotiation',
            self::BookingPending => 'Booking Pending',
            self::Booked => 'Vehicle Booked',
            self::Completed => 'Completed / Delivered',
            self::Cancelled => 'Cancelled',
            self::Lost => 'Lost Lead',
        };
    }

    /**
     * Get Tailwind badge classes for display.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::New => 'bg-sky-100 text-sky-800 border-sky-300 dark:bg-sky-900/40 dark:text-sky-300',
            self::Contacted => 'bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-900/40 dark:text-indigo-300',
            self::TestRideScheduled => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
            self::Negotiation => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/40 dark:text-purple-300',
            self::BookingPending => 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/40 dark:text-yellow-300',
            self::Booked => 'bg-teal-100 text-teal-800 border-teal-300 dark:bg-teal-900/40 dark:text-teal-300',
            self::Completed => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
            self::Cancelled => 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300',
            self::Lost => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300',
        };
    }
}
