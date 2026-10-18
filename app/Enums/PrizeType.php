<?php

namespace App\Enums;

/**
 * Promotional scratch card prize types.
 */
enum PrizeType: string
{
    case FreeAccessory = 'free_accessory';
    case ServiceDiscount = 'service_discount';
    case PercentageDiscount = 'percentage_discount';
    case FixedCoupon = 'fixed_coupon';
    case BetterLuck = 'better_luck';

    /**
     * Human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::FreeAccessory => 'Free Accessory / Riding Gear',
            self::ServiceDiscount => 'Workshop Service Discount (Flat)',
            self::PercentageDiscount => 'Percentage Discount (%)',
            self::FixedCoupon => 'Direct Cash Coupon (₹)',
            self::BetterLuck => 'Better Luck Next Time',
        };
    }

    /**
     * Tailwind CSS badge styling.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::FreeAccessory => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            self::ServiceDiscount => 'bg-sky-500/10 text-sky-400 border border-sky-500/20',
            self::PercentageDiscount => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
            self::FixedCoupon => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::BetterLuck => 'bg-slate-500/10 text-slate-400 border border-slate-500/20',
        };
    }

    /**
     * Determine if this is a tangible winning reward vs better luck next time.
     */
    public function isWinningPrize(): bool
    {
        return $this !== self::BetterLuck;
    }
}
