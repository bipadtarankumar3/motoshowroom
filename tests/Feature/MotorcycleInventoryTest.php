<?php

namespace Tests\Feature;

use App\Enums\BikeCategory;
use App\Enums\BikeStatus;
use App\Models\Bike;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MotorcycleInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $ownerA;

    protected User $ownerB;

    protected Showroom $showroomA;

    protected Showroom $showroomB;

    protected SubscriptionPlan $starterPlan;

    protected SubscriptionPlan $proPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@platform.example',
            'password' => 'Password123!',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->ownerA = User::create([
            'name' => 'Rajesh Sharma',
            'email' => 'rajesh@apex.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->ownerB = User::create([
            'name' => 'Vikram Malhotra',
            'email' => 'vikram@velocity.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->showroomA = Showroom::create([
            'name' => 'Apex Moto Hub',
            'slug' => 'apex-moto',
            'email' => 'contact@apex.example',
            'phone' => '+91 8023456789',
            'city' => 'Bangalore',
            'state' => 'Karnataka',
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        $this->showroomB = Showroom::create([
            'name' => 'Velocity Superbikes',
            'slug' => 'velocity-superbikes',
            'email' => 'contact@velocity.example',
            'phone' => '+91 1123456789',
            'city' => 'New Delhi',
            'state' => 'Delhi',
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroomB->id,
            'user_id' => $this->ownerB->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        $this->starterPlan = SubscriptionPlan::create([
            'name' => 'Starter Dealership',
            'slug' => 'starter',
            'description' => 'Entry plan for boutique showrooms.',
            'monthly_price' => 999.00,
            'annual_price' => 9990.00,
            'currency' => 'INR',
            'trial_days' => 7,
            'max_staff_accounts' => 3,
            'max_bike_listings' => 2, // Low quota for testing limit enforcement
            'max_monthly_bookings' => 50,
            'media_storage_mb' => 1024,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Professional Showroom',
            'slug' => 'professional',
            'description' => 'Growth tier for high volume dealers.',
            'monthly_price' => 2999.00,
            'annual_price' => 29990.00,
            'currency' => 'INR',
            'trial_days' => 14,
            'max_staff_accounts' => 10,
            'max_bike_listings' => 50,
            'max_monthly_bookings' => 250,
            'media_storage_mb' => 5120,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->proPlan, 'monthly', $this->admin->id);
        $subscriptionService->subscribe($this->showroomB, $this->starterPlan, 'monthly', $this->admin->id);
    }

    public function test_showroom_owner_can_view_motorcycle_inventory_index(): void
    {
        $this->actingAs($this->ownerA);

        $bike = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Ducati Panigale V4 S',
            'slug' => 'ducati-panigale-v4-s',
            'sku' => 'APEX-PAN-V4S',
            'brand' => 'Ducati',
            'model_year' => 2025,
            'category' => BikeCategory::Sports->value,
            'price' => 3300000.00,
            'engine_cc' => 1103,
            'power_bhp' => 215.50,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
            'is_featured' => true,
            'is_published' => true,
        ]);

        $response = $this->get("/showrooms/{$this->showroomA->slug}/manage/bikes");

        $response->assertOk();
        $response->assertSee('Motorcycle Inventory');
        $response->assertSee('Ducati Panigale V4 S');
        $response->assertSee('APEX-PAN-V4S');
        $response->assertSee('₹3,300,000.00');
    }

    public function test_showroom_owner_can_create_a_motorcycle_with_specs_images_and_variants(): void
    {
        $this->actingAs($this->ownerA);

        $createResponse = $this->get("/showrooms/{$this->showroomA->slug}/manage/bikes/create");
        $createResponse->assertOk();
        $createResponse->assertSee('Add New Motorcycle');

        $payload = [
            'name' => 'Yamaha YZF-R1M Carbon',
            'brand' => 'Yamaha',
            'model_year' => 2025,
            'category' => BikeCategory::Sports->value,
            'sku' => 'APEX-YAM-R1M',
            'price' => 2850000.00,
            'engine_cc' => 998,
            'power_bhp' => 197.30,
            'torque_nm' => 113.30,
            'fuel_type' => 'petrol',
            'transmission' => '6-Speed Quickshifter',
            'mileage_kmpl' => 14.50,
            'top_speed_kmh' => 299,
            'curb_weight_kg' => 202,
            'status' => BikeStatus::InStock->value,
            'is_featured' => 1,
            'is_published' => 1,
            'description' => 'The ultimate Yamaha track weapon with electronic Öhlins suspension.',
            'spec_keys' => ['Suspension', 'Brakes'],
            'spec_values' => ['Öhlins Electronic NPX', 'Brembo Stylema Calipers'],
            'images' => [
                [
                    'image_url' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc',
                    'alt_text' => 'Front track view',
                    'is_primary' => 1,
                ],
            ],
            'variants' => [
                [
                    'name' => 'Carbon Fiber Racing Trim',
                    'color_name' => 'Silver Blu Carbon',
                    'color_hex' => '#002B7F',
                    'price_difference' => 0.00,
                    'stock_quantity' => 2,
                ],
            ],
        ];

        $response = $this->post("/showrooms/{$this->showroomA->slug}/manage/bikes", $payload);

        $response->assertRedirect("/showrooms/{$this->showroomA->slug}/manage/bikes");
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bikes', [
            'tenant_id' => $this->showroomA->id,
            'name' => 'Yamaha YZF-R1M Carbon',
            'slug' => 'yamaha-yzf-r1m-carbon',
            'sku' => 'APEX-YAM-R1M',
            'price' => 2850000.00,
            'is_featured' => true,
            'is_published' => true,
        ]);

        $bike = Bike::where('sku', 'APEX-YAM-R1M')->firstOrFail();

        $this->assertDatabaseHas('bike_images', [
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $bike->id,
            'image_url' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc',
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('bike_variants', [
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $bike->id,
            'name' => 'Carbon Fiber Racing Trim',
            'color_hex' => '#002B7F',
            'stock_quantity' => 2,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'bike.created',
            'tenant_id' => $this->showroomA->id,
        ]);
    }

    public function test_system_enforces_plan_max_bike_listings_quota(): void
    {
        $this->actingAs($this->ownerB);

        // Showroom B is on Starter plan which has max_bike_listings = 2
        Bike::create([
            'tenant_id' => $this->showroomB->id,
            'name' => 'Bike One',
            'slug' => 'bike-one',
            'sku' => 'VEL-001',
            'brand' => 'Kawasaki',
            'model_year' => 2024,
            'category' => BikeCategory::Sports->value,
            'price' => 500000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
        ]);

        Bike::create([
            'tenant_id' => $this->showroomB->id,
            'name' => 'Bike Two',
            'slug' => 'bike-two',
            'sku' => 'VEL-002',
            'brand' => 'Honda',
            'model_year' => 2024,
            'category' => BikeCategory::Commuter->value,
            'price' => 200000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
        ]);

        // Attempt to access create page when limit reached
        $createResponse = $this->get("/showrooms/{$this->showroomB->slug}/manage/bikes/create");
        $createResponse->assertRedirect("/showrooms/{$this->showroomB->slug}/manage/bikes");
        $createResponse->assertSessionHas('error');

        // Attempt to store a third bike
        $postResponse = $this->post("/showrooms/{$this->showroomB->slug}/manage/bikes", [
            'name' => 'Bike Three',
            'brand' => 'Suzuki',
            'model_year' => 2025,
            'category' => BikeCategory::Sports->value,
            'sku' => 'VEL-003',
            'price' => 700000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
        ]);

        $postResponse->assertRedirect("/showrooms/{$this->showroomB->slug}/manage/bikes");
        $postResponse->assertSessionHas('error');

        $this->assertDatabaseMissing('bikes', [
            'sku' => 'VEL-003',
        ]);
    }

    public function test_showroom_owner_can_update_motorcycle_and_specifications(): void
    {
        $this->actingAs($this->ownerA);

        $bike = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Triumph Tiger 900',
            'slug' => 'triumph-tiger-900',
            'sku' => 'APEX-TRI-T900',
            'brand' => 'Triumph',
            'model_year' => 2024,
            'category' => BikeCategory::Adventure->value,
            'price' => 1450000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
            'is_published' => true,
        ]);

        $editResponse = $this->get("/showrooms/{$this->showroomA->slug}/manage/bikes/{$bike->slug}/edit");
        $editResponse->assertOk();
        $editResponse->assertSee('Edit Motorcycle');
        $editResponse->assertSee('Triumph Tiger 900');

        $updateResponse = $this->put("/showrooms/{$this->showroomA->slug}/manage/bikes/{$bike->slug}", [
            'name' => 'Triumph Tiger 900 Rally Pro',
            'brand' => 'Triumph',
            'model_year' => 2025,
            'category' => BikeCategory::Adventure->value,
            'sku' => 'APEX-TRI-T900',
            'price' => 1595000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
            'is_featured' => 1,
            'is_published' => 1,
            'description' => 'Updated 2025 rally pro edition.',
            'spec_keys' => ['Front Wheel'],
            'spec_values' => ['21-inch Spoke'],
        ]);

        $updateResponse->assertRedirect("/showrooms/{$this->showroomA->slug}/manage/bikes");
        $updateResponse->assertSessionHas('success');

        $bike->refresh();
        $this->assertEquals('Triumph Tiger 900 Rally Pro', $bike->name);
        $this->assertEquals(1595000.00, (float) $bike->price);
        $this->assertEquals(2025, $bike->model_year);
        $this->assertTrue($bike->is_featured);
        $this->assertEquals(['Front Wheel' => '21-inch Spoke'], $bike->specifications);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'bike.updated',
            'tenant_id' => $this->showroomA->id,
        ]);
    }

    public function test_showroom_owner_can_soft_delete_motorcycle(): void
    {
        $this->actingAs($this->ownerA);

        $bike = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Old Commuter 125',
            'slug' => 'old-commuter-125',
            'sku' => 'APEX-OLD-125',
            'brand' => 'Hero',
            'model_year' => 2020,
            'category' => BikeCategory::Commuter->value,
            'price' => 75000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::Discontinued->value,
        ]);

        $response = $this->delete("/showrooms/{$this->showroomA->slug}/manage/bikes/{$bike->slug}");

        $response->assertRedirect("/showrooms/{$this->showroomA->slug}/manage/bikes");
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('bikes', [
            'id' => $bike->id,
            'sku' => 'APEX-OLD-125',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'bike.deleted',
            'tenant_id' => $this->showroomA->id,
        ]);
    }

    public function test_showroom_owner_can_toggle_featured_and_published_flags(): void
    {
        $this->actingAs($this->ownerA);

        $bike = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'BMW S1000RR',
            'slug' => 'bmw-s1000rr',
            'sku' => 'APEX-BMW-S1000',
            'brand' => 'BMW',
            'model_year' => 2025,
            'category' => BikeCategory::Sports->value,
            'price' => 2450000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
            'is_featured' => false,
            'is_published' => true,
        ]);

        $featuredResponse = $this->post("/showrooms/{$this->showroomA->slug}/manage/bikes/{$bike->slug}/toggle-featured");
        $featuredResponse->assertRedirect();
        $this->assertTrue($bike->fresh()->is_featured);

        $publishedResponse = $this->post("/showrooms/{$this->showroomA->slug}/manage/bikes/{$bike->slug}/toggle-published");
        $publishedResponse->assertRedirect();
        $this->assertFalse($bike->fresh()->is_published);
    }

    public function test_multi_tenant_isolation_prevents_access_to_another_showrooms_motorcycles(): void
    {
        $bikeA = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Showroom A Private Superbike',
            'slug' => 'showroom-a-private-superbike',
            'sku' => 'APEX-PVT-01',
            'brand' => 'Ducati',
            'model_year' => 2025,
            'category' => BikeCategory::Sports->value,
            'price' => 4500000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
        ]);

        // Owner B logs in and attempts to access Showroom A's manage bikes route
        $this->actingAs($this->ownerB);

        $unauthorizedIndex = $this->get("/showrooms/{$this->showroomA->slug}/manage/bikes");
        $unauthorizedIndex->assertForbidden();

        // Owner B attempts to edit Showroom A's bike under Showroom B's tenant route
        $crossTenantEdit = $this->get("/showrooms/{$this->showroomB->slug}/manage/bikes/{$bikeA->slug}/edit");
        $crossTenantEdit->assertNotFound();

        // Owner B attempts to delete Showroom A's bike
        $crossTenantDelete = $this->delete("/showrooms/{$this->showroomB->slug}/manage/bikes/{$bikeA->slug}");
        $crossTenantDelete->assertNotFound();

        $this->assertDatabaseHas('bikes', [
            'id' => $bikeA->id,
            'deleted_at' => null,
        ]);
    }

    public function test_customer_can_browse_public_catalog_of_published_bikes_only(): void
    {
        $publishedBike = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Kawasaki Ninja ZX-10R',
            'slug' => 'kawasaki-ninja-zx-10r',
            'sku' => 'APEX-KAW-ZX10R',
            'brand' => 'Kawasaki',
            'model_year' => 2025,
            'category' => BikeCategory::Sports->value,
            'price' => 1750000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
            'is_published' => true,
        ]);

        $draftBike = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Secret Prototype EV',
            'slug' => 'secret-prototype-ev',
            'sku' => 'APEX-SECRET-EV',
            'brand' => 'Yamaha',
            'model_year' => 2026,
            'category' => BikeCategory::Electric->value,
            'price' => 999999.00,
            'fuel_type' => 'electric',
            'status' => BikeStatus::Incoming->value,
            'is_published' => false, // Hidden draft
        ]);

        $response = $this->get("/showrooms/{$this->showroomA->slug}/bikes");

        $response->assertOk();
        $response->assertSee('Motorcycle Collection');
        $response->assertSee('Kawasaki Ninja ZX-10R');
        $response->assertSee('₹1,750,000.00');
        $response->assertDontSee('Secret Prototype EV');
    }

    public function test_customer_can_view_public_bike_detail_page(): void
    {
        $bike = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'KTM 1290 Super Duke R',
            'slug' => 'ktm-1290-super-duke-r',
            'sku' => 'APEX-KTM-1290',
            'brand' => 'KTM',
            'model_year' => 2024,
            'category' => BikeCategory::Sports->value,
            'price' => 1950000.00,
            'engine_cc' => 1301,
            'power_bhp' => 180.00,
            'torque_nm' => 140.00,
            'fuel_type' => 'petrol',
            'transmission' => '6-speed with Pankl transmission',
            'mileage_kmpl' => 16.00,
            'status' => BikeStatus::InStock->value,
            'is_published' => true,
            'description' => 'The Beast - stripped down naked hypersport machine.',
            'specifications' => [
                'Chassis' => 'Chrome-moly tubular space frame',
            ],
        ]);

        $response = $this->get("/showrooms/{$this->showroomA->slug}/bikes/{$bike->slug}");

        $response->assertOk();
        $response->assertSee('KTM 1290 Super Duke R');
        $response->assertSee('APEX-KTM-1290');
        $response->assertSee('1301 cc');
        $response->assertSee('180.00 bhp');
        $response->assertSee('Chrome-moly tubular space frame');
        $response->assertSee('₹1,950,000.00');
    }

    public function test_showroom_storefront_homepage_showcases_featured_motorcycles(): void
    {
        $featuredBike = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Royal Enfield Continental GT 650',
            'slug' => 'royal-enfield-continental-gt-650',
            'sku' => 'APEX-RE-GT650',
            'brand' => 'Royal Enfield',
            'model_year' => 2024,
            'category' => BikeCategory::Cruiser->value,
            'price' => 385000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
            'is_featured' => true,
            'is_published' => true,
        ]);

        $response = $this->get("/showrooms/{$this->showroomA->slug}");

        $response->assertOk();
        $response->assertSee('Featured Motorcycles');
        $response->assertSee('Royal Enfield Continental GT 650');
        $response->assertSee('₹385,000.00');
        $response->assertSee("/showrooms/{$this->showroomA->slug}/bikes/{$featuredBike->slug}");
    }
}
