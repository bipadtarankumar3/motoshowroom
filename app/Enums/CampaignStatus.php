<?php

namespace App\Enums;

/**
 * Promotional marketing campaign lifecycle status.
 */
enum CampaignStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Paused = 'paused';
    case Expired = 'expired';
    case Archived = 'archived';

    /**
     * Human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Active => 'Active & Live',
            self::Paused => 'Paused',
            self::Expired => 'Campaign Expired',
            self::Archived => 'Archived',
        };
    }

    /**
     * Tailwind CSS badge styling.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-500/10 text-slate-400 border border-slate-500/20',
            self::Active => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            self::Paused => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::Expired => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
            self::Archived => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
        };
    }
}
