<?php

namespace App\Enums;

/**
 * Motorcycle classification categories.
 */
enum BikeCategory: string
{
    case Sports = 'sports';
    case Cruiser = 'cruiser';
    case Adventure = 'adventure';
    case Commuter = 'commuter';
    case Touring = 'touring';
    case Scooter = 'scooter';
    case Electric = 'electric';

    /**
     * Get a human-readable display label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Sports => 'Sports / Superbike',
            self::Cruiser => 'Cruiser',
            self::Adventure => 'Adventure / Dual Sport',
            self::Commuter => 'Commuter',
            self::Touring => 'Tourer',
            self::Scooter => 'Scooter / Maxi',
            self::Electric => 'Electric EV',
        };
    }

    /**
     * Get Tailwind badge classes for display.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Sports => 'bg-red-500/10 text-red-400 border border-red-500/20',
            self::Cruiser => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::Adventure => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            self::Commuter => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
            self::Touring => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
            self::Scooter => 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20',
            self::Electric => 'bg-teal-500/10 text-teal-300 border border-teal-500/20',
        };
    }
}
