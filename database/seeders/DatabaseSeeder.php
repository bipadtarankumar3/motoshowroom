<?php

namespace Database\Seeders;

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
        // Default Super Administrator
        User::updateOrCreate(
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

        // Default Regular User
        User::updateOrCreate(
            ['email' => 'user@motoshowroom.example'],
            [
                'name' => 'Rajesh Sharma',
                'password' => 'Password123!',
                'role' => 'user',
                'status' => 'active',
                'phone' => '+91 9876543210',
                'city' => 'Bangalore',
                'address' => '42 Brigade Road, Indiranagar',
                'bio' => 'Motorcycle enthusiast and prospective bike dealership owner.',
            ]
        );
    }
}
