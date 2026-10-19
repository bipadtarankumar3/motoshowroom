<?php

namespace Database\Seeders;

use App\Enums\BikeCategory;
use App\Enums\BikeStatus;
use App\Models\Banner;
use App\Models\Bike;
use App\Models\Category;
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

        // 9. Seed Inventory for Showroom A (Apex Moto Hub)
        $r1m = Bike::updateOrCreate(
            ['tenant_id' => $showroomA->id, 'slug' => 'yamaha-yzf-r1m-carbon'],
            [
                'name' => 'Yamaha YZF-R1M Carbon Edition',
                'sku' => 'APEX-YAM-R1M',
                'brand' => 'Yamaha',
                'model_year' => 2025,
                'category' => BikeCategory::Sports->value,
                'engine_cc' => 998,
                'power_bhp' => 197.30,
                'torque_nm' => 113.30,
                'fuel_type' => 'petrol',
                'transmission' => '6-speed with Quick Shift System (QSS)',
                'mileage_kmpl' => 14.50,
                'top_speed_kmh' => 299,
                'curb_weight_kg' => 202,
                'price' => 2850000.00,
                'is_featured' => true,
                'is_published' => true,
                'status' => BikeStatus::InStock->value,
                'description' => 'The YZF-R1M is Yamaha’s ultimate production superbike. Featuring Öhlins Electronic Racing Suspension (ERS), lightweight carbon fiber bodywork, and MotoGP-derived electronics package.',
                'specifications' => [
                    'Suspension' => 'Öhlins Electronic Racing Suspension (ERS) NPX gas-charged front fork',
                    'Brakes' => 'Dual 320mm discs with 4-piston radial monobloc calipers',
                    'ABS' => 'Lean-sensitive Brake Control (BC) system',
                    'Fuel Tank' => '17 Litres polished aluminium',
                ],
            ]
        );

        $r1m->images()->delete();
        $r1m->images()->create([
            'tenant_id' => $showroomA->id,
            'image_url' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=1200&q=80',
            'alt_text' => 'Yamaha YZF-R1M Track Stance',
            'is_primary' => true,
            'sort_order' => 0,
        ]);
        $r1m->images()->create([
            'tenant_id' => $showroomA->id,
            'image_url' => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=1200&q=80',
            'alt_text' => 'Yamaha YZF-R1M Cockpit & Tank',
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        $r1m->variants()->delete();
        $r1m->variants()->create([
            'tenant_id' => $showroomA->id,
            'name' => 'Silver Blu Carbon',
            'color_name' => 'Carbon Fiber / Yamaha Blue',
            'color_hex' => '#002B7F',
            'price_difference' => 0.00,
            'stock_quantity' => 2,
        ]);

        $panigale = Bike::updateOrCreate(
            ['tenant_id' => $showroomA->id, 'slug' => 'ducati-panigale-v4-s'],
            [
                'name' => 'Ducati Panigale V4 S',
                'sku' => 'APEX-DUC-V4S',
                'brand' => 'Ducati',
                'model_year' => 2025,
                'category' => BikeCategory::Sports->value,
                'engine_cc' => 1103,
                'power_bhp' => 215.50,
                'torque_nm' => 123.60,
                'fuel_type' => 'petrol',
                'transmission' => '6-speed with Ducati Quick Shift (DQS) EVO 2',
                'mileage_kmpl' => 13.00,
                'top_speed_kmh' => 299,
                'curb_weight_kg' => 195,
                'price' => 3350000.00,
                'is_featured' => true,
                'is_published' => true,
                'status' => BikeStatus::InStock->value,
                'description' => 'The Panigale V4 represents the last step in the characteristic path of Borgo Panigale sports bikes. Desmosedici Stradale 90-degree V4 engine with counter-rotating crankshaft.',
                'specifications' => [
                    'Suspension' => 'Öhlins Smart EC 2.0 electronic suspension',
                    'Wheels' => 'Forged aluminum 3-spoke Marchesini wheels',
                    'Brakes' => 'Brembo Monobloc Stylema calipers',
                ],
            ]
        );

        $panigale->images()->delete();
        $panigale->images()->create([
            'tenant_id' => $showroomA->id,
            'image_url' => 'https://images.unsplash.com/photo-1609630875171-b1321377ee65?auto=format&fit=crop&w=1200&q=80',
            'alt_text' => 'Ducati Panigale V4 S Ducati Red',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $panigale->variants()->delete();
        $panigale->variants()->create([
            'tenant_id' => $showroomA->id,
            'name' => 'Ducati Red',
            'color_name' => 'Rosso Corsa Red',
            'color_hex' => '#CC0000',
            'price_difference' => 0.00,
            'stock_quantity' => 1,
        ]);

        $tiger = Bike::updateOrCreate(
            ['tenant_id' => $showroomA->id, 'slug' => 'triumph-tiger-900-rally-pro'],
            [
                'name' => 'Triumph Tiger 900 Rally Pro',
                'sku' => 'APEX-TRI-T900',
                'brand' => 'Triumph',
                'model_year' => 2024,
                'category' => BikeCategory::Adventure->value,
                'engine_cc' => 888,
                'power_bhp' => 106.50,
                'torque_nm' => 90.00,
                'fuel_type' => 'petrol',
                'transmission' => '6-speed manual with slip and assist clutch',
                'mileage_kmpl' => 21.00,
                'top_speed_kmh' => 200,
                'curb_weight_kg' => 228,
                'price' => 1595000.00,
                'is_featured' => false,
                'is_published' => true,
                'status' => BikeStatus::InStock->value,
                'description' => 'The ultimate all-road, all-terrain adventure motorcycle. Triple-cylinder power with T-plane crank for supreme low-down grunt.',
                'specifications' => [
                    'Suspension' => 'Showa 45mm manual USD forks with 240mm travel',
                    'Front Wheel' => '21-inch tubeless spoked wheel',
                ],
            ]
        );

        $tiger->images()->delete();
        $tiger->images()->create([
            'tenant_id' => $showroomA->id,
            'image_url' => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=1200&q=80',
            'alt_text' => 'Triumph Tiger 900 Rally Pro Khaki Green',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        // Seed 360-degree turntable frames for all showroom motorcycles
        $turntableFrames = [
            0 => ['url' => 'https://images.unsplash.com/photo-1609630875171-b1321377ee65?auto=format&fit=crop&w=1400&q=85', 'label' => '0° Front Facia Stance'],
            45 => ['url' => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=1400&q=85', 'label' => '45° Front-Right Dynamic Angle'],
            90 => ['url' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=1400&q=85', 'label' => '90° Right Side Flank Profile'],
            135 => ['url' => 'https://images.unsplash.com/photo-1558981420-87aa9dad1c89?auto=format&fit=crop&w=1400&q=85', 'label' => '135° Rear-Right Quarter Exhaust'],
            180 => ['url' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1400&q=85', 'label' => '180° Rear Tail Aero Section'],
            225 => ['url' => 'https://images.unsplash.com/photo-1558980664-769d59546b3d?auto=format&fit=crop&w=1400&q=85', 'label' => '225° Rear-Left Quarter Monoshock'],
            270 => ['url' => 'https://images.unsplash.com/photo-1558980664-3a031cf67ea8?auto=format&fit=crop&w=1400&q=85', 'label' => '270° Left Flank Transmission Profile'],
            315 => ['url' => 'https://images.unsplash.com/photo-1558981285-6f0c94958bb6?auto=format&fit=crop&w=1400&q=85', 'label' => '315° Front-Left Dynamic Angle'],
        ];

        foreach ([$r1m, $panigale, $tiger] as $seededBike) {
            $seededBike->update(['has_360_view' => true]);
            $seededBike->frames360()->delete();
            $order = 0;
            foreach ($turntableFrames as $deg => $fData) {
                $seededBike->frames360()->create([
                    'tenant_id' => $showroomA->id,
                    'image_url' => $fData['url'],
                    'alt_text' => $seededBike->name.' '.$fData['label'],
                    'is_primary' => false,
                    'is_360_frame' => true,
                    'frame_angle' => $deg,
                    'sort_order' => $order++,
                ]);
            }
        }

        // 10. Seed Categories and Banners for Showrooms
        foreach ([$showroomA, $showroomB] as $showroom) {
            $catList = [
                ['name' => 'Sports Bikes', 'slug' => 'sports', 'subtitle' => 'Track & Superbikes', 'image_url' => 'https://images.unsplash.com/photo-1609630875171-b1321377ee65?auto=format&fit=crop&w=500&q=80'],
                ['name' => 'Street Bikes', 'slug' => 'commuter', 'subtitle' => 'Naked & Roadsters', 'image_url' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=500&q=80'],
                ['name' => 'Adventure Bikes', 'slug' => 'adventure', 'subtitle' => 'Dual Sport & Off-Road', 'image_url' => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=500&q=80'],
                ['name' => 'Scooters', 'slug' => 'scooter', 'subtitle' => 'Urban & Maxi Commuters', 'image_url' => 'https://images.unsplash.com/photo-1591637333184-19aa84b3e01f?auto=format&fit=crop&w=500&q=80'],
                ['name' => 'Electric Bikes', 'slug' => 'electric', 'subtitle' => 'Clean Tech EVs', 'image_url' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=500&q=80'],
                ['name' => 'Cruiser Bikes', 'slug' => 'cruiser', 'subtitle' => 'Highway Heritage', 'image_url' => 'https://images.unsplash.com/photo-1558980664-769d59546b3d?auto=format&fit=crop&w=500&q=80'],
            ];

            foreach ($catList as $order => $catData) {
                Category::updateOrCreate(
                    ['tenant_id' => $showroom->id, 'slug' => $catData['slug']],
                    [
                        'name' => $catData['name'],
                        'subtitle' => $catData['subtitle'],
                        'image_url' => $catData['image_url'],
                        'is_active' => true,
                        'sort_order' => $order,
                    ]
                );
            }

            $bannerList = [
                [
                    'badge' => 'NEW COLLECTION 2026',
                    'title_prefix' => 'Fuel Your',
                    'title_highlight' => 'Next',
                    'title_suffix' => 'Adventure',
                    'subtitle' => 'Explore the latest bikes with unmatched performance, style and technology.',
                    'btn_primary_text' => 'Explore Bikes',
                    'btn_primary_url' => '',
                    'btn_secondary_text' => 'Book a Test Ride',
                    'btn_secondary_url' => '',
                    'feature_1_title' => 'Trusted Showroom',
                    'feature_1_sub' => '100% Genuine Bikes',
                    'feature_2_title' => 'After Sales Support',
                    'feature_2_sub' => 'Always Here for You',
                    'feature_3_title' => 'Best Price Guarantee',
                    'feature_3_sub' => 'Unbeatable Offers',
                    'image_url' => '/images/hero/hero_adventure_red.jpg',
                    'is_active' => true,
                    'sort_order' => 0,
                ],
                [
                    'badge' => 'SPEED & PRECISION',
                    'title_prefix' => 'Pure Track',
                    'title_highlight' => 'Domination',
                    'title_suffix' => 'Awaits',
                    'subtitle' => 'Aerodynamic winglets, race-tuned suspension, and championship electronics.',
                    'btn_primary_text' => 'View Superbikes',
                    'btn_primary_url' => '',
                    'btn_secondary_text' => 'Book Test Ride',
                    'btn_secondary_url' => '',
                    'feature_1_title' => 'Track Ready',
                    'feature_1_sub' => 'Factory Tuned',
                    'feature_2_title' => 'Warranty',
                    'feature_2_sub' => '5 Years Coverage',
                    'feature_3_title' => 'Fast Delivery',
                    'feature_3_sub' => '48hr Handover',
                    'image_url' => '/images/hero/yamaha_blue.jpg',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'badge' => 'POWER CRUISER',
                    'title_prefix' => 'Command The',
                    'title_highlight' => 'Open Highway',
                    'title_suffix' => 'In Comfort',
                    'subtitle' => 'Torque-heavy twin engines, laid-back ergonomics, and custom styling.',
                    'btn_primary_text' => 'Explore Cruisers',
                    'btn_primary_url' => '',
                    'btn_secondary_text' => 'Calculate EMI',
                    'btn_secondary_url' => '',
                    'feature_1_title' => 'Premium Finish',
                    'feature_1_sub' => 'Chrome & Matte',
                    'feature_2_title' => 'Low Interest',
                    'feature_2_sub' => 'From 6.99%',
                    'feature_3_title' => 'Exchange Bonus',
                    'feature_3_sub' => 'Top Valuation',
                    'image_url' => '/images/hero/ducati_diavel_red.jpg',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
            ];

            foreach ($bannerList as $order => $bannerData) {
                Banner::updateOrCreate(
                    ['tenant_id' => $showroom->id, 'sort_order' => $order],
                    $bannerData
                );
            }
        }

        // Link existing bikes to categories
        foreach (Bike::all() as $bike) {
            $cat = Category::where('tenant_id', $bike->tenant_id)
                ->where('slug', is_object($bike->category) ? $bike->category->value : $bike->category)
                ->first();
            if ($cat) {
                $bike->update(['category_id' => $cat->id]);
            }
        }
    }
}
