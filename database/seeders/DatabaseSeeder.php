<?php

namespace Database\Seeders;

use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Default Super Administrator
        $admin = User::updateOrCreate(
            ['email' => 'admin@motoshowroom.example'],
            [
                'name' => 'Super Administrator',
                'password' => 'Password123!',
                'role' => 'admin',
                'status' => 'active',
                'phone' => '+91 9900011223',
                'city' => 'Mumbai',
                'address' => 'HQ Suite 100, MotoShowroom Tower',
                'bio' => 'Lead SaaS Platform Administrator controlling dealership tenancies, billing, subscriptions, and platform settings.',
            ]
        );

        // 2. Showroom A Owner
        $ownerA = User::updateOrCreate(
            ['email' => 'rajesh@apex.example'],
            [
                'name' => 'Rajesh Sharma',
                'password' => 'Password123!',
                'role' => 'user',
                'status' => 'active',
                'phone' => '+91 9876543210',
                'city' => 'Bangalore',
                'address' => '42 Brigade Road, Indiranagar',
                'bio' => 'Founder & Owner of Apex Moto Hub.',
            ]
        );

        // 3. Showroom B Owner
        $ownerB = User::updateOrCreate(
            ['email' => 'vikram@velocity.example'],
            [
                'name' => 'Vikram Malhotra',
                'password' => 'Password123!',
                'role' => 'user',
                'status' => 'active',
                'phone' => '+91 9812345678',
                'city' => 'New Delhi',
                'address' => '15 Connaught Place',
                'bio' => 'Owner of Velocity Superbikes.',
            ]
        );

        // 4. Create Showroom A: Apex Moto Hub
        $showroomA = Showroom::updateOrCreate(
            ['slug' => 'apex-moto'],
            [
                'name' => 'Apex Moto Hub',
                'email' => 'contact@apexmotohub.example',
                'phone' => '+91 8023456789',
                'address_line1' => '42 Brigade Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'postal_code' => '560025',
                'country' => 'India',
                'brand_color_primary' => '#DC2626',
                'brand_color_accent' => '#0F172A',
                'status' => 'active',
                'public_website_enabled' => true,
                'read_only_mode' => false,
            ]
        );

        // 5. Create Showroom B: Velocity Superbikes
        $showroomB = Showroom::updateOrCreate(
            ['slug' => 'velocity-superbikes'],
            [
                'name' => 'Velocity Superbikes',
                'email' => 'sales@velocitysuperbikes.example',
                'phone' => '+91 1123456789',
                'address_line1' => '15 Connaught Place',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'postal_code' => '110001',
                'country' => 'India',
                'brand_color_primary' => '#2563EB',
                'brand_color_accent' => '#1E293B',
                'status' => 'active',
                'public_website_enabled' => true,
                'read_only_mode' => false,
            ]
        );

        // 6. Establish Memberships
        ShowroomMembership::updateOrCreate(
            ['showroom_id' => $showroomA->id, 'user_id' => $ownerA->id],
            ['role' => 'owner', 'status' => 'active']
        );

        ShowroomMembership::updateOrCreate(
            ['showroom_id' => $showroomB->id, 'user_id' => $ownerB->id],
            ['role' => 'owner', 'status' => 'active']
        );

        // 7. Seed Default Subscription Plans
        $starterPlan = SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter Dealership',
                'description' => 'Perfect for independent single-location bike showrooms getting started online.',
                'monthly_price' => 999.00,
                'annual_price' => 9990.00,
                'currency' => 'INR',
                'trial_days' => 14,
                'max_staff_accounts' => 3,
                'max_bike_listings' => 15,
                'max_monthly_bookings' => 50,
                'media_storage_mb' => 1024,
                'has_careers_module' => true,
                'has_scratch_cards' => true,
                'has_360_viewer' => false,
                'has_ai_assistant' => false,
                'has_custom_domain' => false,
                'has_advanced_analytics' => false,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $proPlan = SubscriptionPlan::updateOrCreate(
            ['slug' => 'professional'],
            [
                'name' => 'Professional Showroom',
                'description' => 'Comprehensive multi-department suite with interactive 360 viewer and AI advisor.',
                'monthly_price' => 2999.00,
                'annual_price' => 29990.00,
                'currency' => 'INR',
                'trial_days' => 14,
                'max_staff_accounts' => 10,
                'max_bike_listings' => 50,
                'max_monthly_bookings' => 250,
                'media_storage_mb' => 5120,
                'has_careers_module' => true,
                'has_scratch_cards' => true,
                'has_360_viewer' => true,
                'has_ai_assistant' => true,
                'has_custom_domain' => true,
                'has_advanced_analytics' => false,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $enterprisePlan = SubscriptionPlan::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise Franchise',
                'description' => 'Unlimited scaling for dealership chains, franchise networks, and high-volume dealers.',
                'monthly_price' => 7999.00,
                'annual_price' => 79990.00,
                'currency' => 'INR',
                'trial_days' => 30,
                'max_staff_accounts' => 50,
                'max_bike_listings' => 200,
                'max_monthly_bookings' => 1000,
                'media_storage_mb' => 20480,
                'has_careers_module' => true,
                'has_scratch_cards' => true,
                'has_360_viewer' => true,
                'has_ai_assistant' => true,
                'has_custom_domain' => true,
                'has_advanced_analytics' => true,
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // 8. Assign Subscriptions to Seeded Showrooms
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($showroomA, $proPlan, 'monthly', $admin->id);
        $subscriptionService->subscribe($showroomB, $starterPlan, 'annual', $admin->id);
    }
}
