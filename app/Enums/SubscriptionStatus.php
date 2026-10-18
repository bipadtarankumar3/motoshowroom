<?php

namespace App\Enums;

/**
 * Lifecycle statuses for showroom subscriptions.
 */
enum SubscriptionStatus: string
{
    case Trial = 'trial';
    case PendingPayment = 'pending_payment';
    case Active = 'active';
    case PastDue = 'past_due';
    case Expired = 'expired';
    case Suspended = 'suspended';
    case Cancelled = 'cancelled';

    /**
     * Determine if this subscription status grants access to showroom management.
     */
    public function grantsAccess(): bool
    {
        return in_array($this, [self::Trial, self::Active, self::PastDue], true);
    }

    /**
     * Get a human-readable display label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Trial => 'Trial Period',
            self::PendingPayment => 'Pending Payment',
            self::Active => 'Active',
            self::PastDue => 'Past Due (Grace Period)',
            self::Expired => 'Expired',
            self::Suspended => 'Suspended',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Get Tailwind badge classes for display.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Trial => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
            self::PendingPayment => 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/40 dark:text-yellow-300',
            self::Active => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
            self::PastDue => 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900/40 dark:text-orange-300',
            self::Expired => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300',
            self::Suspended => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/40 dark:text-purple-300',
            self::Cancelled => 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300',
        };
    }
}
