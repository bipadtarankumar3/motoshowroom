<?php

namespace App\Enums;

/**
 * Roles available across MotoShowroom SaaS platform and tenant showrooms.
 */
enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case ShowroomOwner = 'showroom_owner';
    case SalesManager = 'sales_manager';
    case ServiceManager = 'service_manager';
    case ServiceAdvisor = 'service_advisor';
    case HrManager = 'hr_manager';
    case MarketingManager = 'marketing_manager';
    case Receptionist = 'receptionist';
    case Customer = 'customer';

    /**
     * Get a human-readable display label.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrator',
            self::ShowroomOwner => 'Showroom Owner',
            self::SalesManager => 'Sales Manager',
            self::ServiceManager => 'Service Manager',
            self::ServiceAdvisor => 'Service Advisor',
            self::HrManager => 'HR Manager',
            self::MarketingManager => 'Marketing Manager',
            self::Receptionist => 'Receptionist',
            self::Customer => 'Customer',
        };
    }

    /**
     * Determine if this role represents showroom staff or owner.
     */
    public function isShowroomStaff(): bool
    {
        return in_array($this, [
            self::ShowroomOwner,
            self::SalesManager,
            self::ServiceManager,
            self::ServiceAdvisor,
            self::HrManager,
            self::MarketingManager,
            self::Receptionist,
        ], true);
    }
}
