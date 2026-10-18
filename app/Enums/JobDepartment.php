<?php

namespace App\Enums;

/**
 * Dealership career departments.
 */
enum JobDepartment: string
{
    case Sales = 'sales';
    case Workshop = 'workshop';
    case Parts = 'parts';
    case Management = 'management';
    case Marketing = 'marketing';
    case CustomerRelations = 'customer_relations';

    /**
     * Human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Sales => 'Sales & Business Development',
            self::Workshop => 'Workshop & Technical Service',
            self::Parts => 'Parts & Performance Accessories',
            self::Management => 'General Dealership Operations',
            self::Marketing => 'Digital Marketing & Community',
            self::CustomerRelations => 'Customer Experience & CRM',
        };
    }

    /**
     * Badge styles for display.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Sales => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
            self::Workshop => 'bg-sky-500/10 text-sky-400 border border-sky-500/20',
            self::Parts => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::Management => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
            self::Marketing => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            self::CustomerRelations => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
        };
    }
}
