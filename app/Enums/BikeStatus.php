<?php

namespace App\Enums;

/**
 * Inventory stock availability statuses for motorcycles.
 */
enum BikeStatus: string
{
    case InStock = 'in_stock';
    case OutOfStock = 'out_of_stock';
    case Incoming = 'incoming';
    case Discontinued = 'discontinued';

    /**
     * Determine if the motorcycle is currently available for purchase or booking.
     */
    public function isAvailable(): bool
    {
        return in_array($this, [self::InStock, self::Incoming], true);
    }

    /**
     * Get a human-readable display label.
     */
    public function label(): string
    {
        return match ($this) {
            self::InStock => 'In Stock',
            self::OutOfStock => 'Out of Stock',
            self::Incoming => 'Incoming / Pre-Order',
            self::Discontinued => 'Discontinued',
        };
    }

    /**
     * Get Tailwind badge classes for display.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::InStock => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            self::OutOfStock => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
            self::Incoming => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::Discontinued => 'bg-zinc-500/10 text-zinc-400 border border-zinc-500/20',
        };
    }
}
