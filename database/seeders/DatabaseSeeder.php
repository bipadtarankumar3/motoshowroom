<?php

namespace Database\Seeders;

use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\User;
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
    }
}
