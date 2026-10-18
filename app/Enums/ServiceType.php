<?php

namespace App\Enums;

/**
 * Service package types offered by motorcycle dealership service centers.
 */
enum ServiceType: string
{
    case GeneralService = 'general_service';
    case OilLube = 'oil_lube';
    case BrakeOverhaul = 'brake_overhaul';
    case EngineTuning = 'engine_tuning';
    case ElectricalBattery = 'electrical_battery';
    case AccidentRepair = 'accident_repair';
    case Custom = 'custom';

    /**
     * Get a human-readable display label.
     */
    public function label(): string
    {
        return match ($this) {
            self::GeneralService => 'Periodic General Maintenance',
            self::OilLube => 'Engine Oil & Filter Service',
            self::BrakeOverhaul => 'Brake System Overhaul',
            self::EngineTuning => 'Performance & Engine Tuning',
            self::ElectricalBattery => 'Electrical & Battery Diagnostic',
            self::AccidentRepair => 'Accident & Body Restoration',
            self::Custom => 'Custom Upgrades & Accessories',
        };
    }

    /**
     * Detailed package inclusions description.
     */
    public function description(): string
    {
        return match ($this) {
            self::GeneralService => 'Comprehensive 32-point inspection, spark plug service, chain cleaning & slack adjustment, filter clean, fluid top-up, and full pressure wash.',
            self::OilLube => 'Premium synthetic engine oil replacement, OEM oil filter replacement, chain degrease & high-temp wax lubrication.',
            self::BrakeOverhaul => 'Front and rear brake pad inspection/replacement, DOT4 brake fluid flush & bleed, caliper pin greasing, and rotor true check.',
            self::EngineTuning => 'Electronic throttle body cleaning, EFI sensor diagnostics, valve tappet clearance check, and spark ignition timing sync.',
            self::ElectricalBattery => 'Digital battery load testing, charging stator check, wiring harness resistance scan, and high-output LED alignment.',
            self::AccidentRepair => 'Structural chassis alignment, fairing/body panel repair, fork straightness verification, and OEM paint refinishing.',
            self::Custom => 'Exhaust system installation, crash protection bars, handlebar risers, auxiliary lights, and performance sprocket conversion.',
        };
    }

    /**
     * Standard baseline estimated cost in Indian Rupees (INR).
     */
    public function estimatedPrice(): float
    {
        return match ($this) {
            self::GeneralService => 1499.00,
            self::OilLube => 799.00,
            self::BrakeOverhaul => 1199.00,
            self::EngineTuning => 2499.00,
            self::ElectricalBattery => 699.00,
            self::AccidentRepair => 4999.00,
            self::Custom => 1999.00,
        };
    }

    /**
     * Estimated bay occupation duration in minutes.
     */
    public function estimatedDurationMinutes(): int
    {
        return match ($this) {
            self::GeneralService => 90,
            self::OilLube => 45,
            self::BrakeOverhaul => 60,
            self::EngineTuning => 120,
            self::ElectricalBattery => 45,
            self::AccidentRepair => 240,
            self::Custom => 120,
        };
    }

    /**
     * Tailwind styled badge classes.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::GeneralService => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            self::OilLube => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            self::BrakeOverhaul => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
            self::EngineTuning => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
            self::ElectricalBattery => 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20',
            self::AccidentRepair => 'bg-orange-500/10 text-orange-400 border border-orange-500/20',
            self::Custom => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
        };
    }
}
