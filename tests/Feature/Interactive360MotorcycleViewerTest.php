<?php

namespace Tests\Feature;

use App\Enums\BikeCategory;
use App\Enums\BikeStatus;
use App\Models\Bike;
use App\Models\BikeImage;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Interactive360MotorcycleViewerTest extends TestCase
{
    use RefreshDatabase;

    protected User $ownerA;

    protected User $ownerB;

    protected Showroom $showroomA;

    protected Showroom $showroomB;

    protected SubscriptionPlan $starterPlan;

    protected SubscriptionPlan $proPlan;

    protected Bike $bikeA;

    protected Bike $bikeB;

    protected function setUp(): void
    {
        parent::setUp();

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

        // Starter plan does not include 360 viewer
        $this->starterPlan = SubscriptionPlan::create([
            'name' => 'Starter Dealership',
            'slug' => 'starter',
            'description' => 'Entry plan for boutique showrooms.',
            'monthly_price' => 999.00,
            'annual_price' => 9990.00,
            'currency' => 'INR',
            'trial_days' => 7,
            'max_staff_accounts' => 3,
            'max_bike_listings' => 10,
            'max_monthly_bookings' => 50,
            'media_storage_mb' => 1024,
            'has_360_viewer' => false,
            'is_active' => true,
        ]);

        // Pro plan includes 360 viewer
        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Pro Superbike Dealer',
            'slug' => 'pro-superbike',
            'description' => 'Full-featured package with interactive 360 turntable.',
            'monthly_price' => 2999.00,
            'annual_price' => 29990.00,
            'currency' => 'INR',
            'trial_days' => 14,
            'max_staff_accounts' => 10,
            'max_bike_listings' => 50,
            'max_monthly_bookings' => 200,
            'media_storage_mb' => 5120,
            'has_360_viewer' => true,
            'is_active' => true,
        ]);

        // Give Showroom A Pro plan (entitled to 360 viewer)
        $subService = app(SubscriptionService::class);
        $subService->subscribe($this->showroomA, $this->proPlan, 'monthly');

        // Give Showroom B Starter plan (no 360 viewer)
        $subService->subscribe($this->showroomB, $this->starterPlan, 'monthly');

        // Motorcycle for Showroom A
        $this->bikeA = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Ducati Panigale V4 S',
            'slug' => 'ducati-panigale-v4-s',
            'sku' => 'MOTO-DUC-0001',
            'brand' => 'Ducati',
            'model_year' => 2026,
            'category' => BikeCategory::Sports->value,
            'price' => 2850000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
            'is_featured' => true,
            'is_published' => true,
            'has_360_view' => true,
            'viewer_type' => 'image_sequence',
        ]);

        // Motorcycle for Showroom B
        $this->bikeB = Bike::create([
            'tenant_id' => $this->showroomB->id,
            'name' => 'BMW S1000RR M Sport',
            'slug' => 'bmw-s1000rr-m-sport',
            'sku' => 'MOTO-BMW-0001',
            'brand' => 'BMW',
            'model_year' => 2026,
            'category' => BikeCategory::Sports->value,
            'price' => 2600000.00,
            'fuel_type' => 'petrol',
            'status' => BikeStatus::InStock->value,
            'is_featured' => true,
            'is_published' => true,
            'has_360_view' => false,
            'viewer_type' => 'image_sequence',
        ]);
    }

    public function test_back_office_staff_can_view_360_studio_for_their_bike(): void
    {
        $response = $this->actingAs($this->ownerA)
            ->get(route('showrooms.manage.bikes.360', [$this->showroomA, $this->bikeA]));

        $response->assertStatus(200);
        $response->assertSee('Ducati Panigale V4 S');
        $response->assertSee('Studio Turntable Preview');
        $response->assertSee('Add 360° Frames');
        $response->assertSee('Viewer Configuration');
        $response->assertDontSee('360° Studio Gated Feature');
    }

    public function test_360_studio_displays_upgrade_prompt_when_plan_lacks_has_360_viewer(): void
    {
        $response = $this->actingAs($this->ownerB)
            ->get(route('showrooms.manage.bikes.360', [$this->showroomB, $this->bikeB]));

        $response->assertStatus(200);
        $response->assertSee('360° Studio Gated Feature');
        $response->assertSee('Upgrade Subscription');
    }

    public function test_staff_can_update_360_viewer_settings(): void
    {
        $response = $this->actingAs($this->ownerA)
            ->put(route('showrooms.manage.bikes.360.settings', [$this->showroomA, $this->bikeA]), [
                'has_360_view' => 1,
                'viewer_type' => 'image_sequence',
                'rotation_speed' => 6,
                'default_angle' => 180,
                'auto_rotate' => 1,
                'reverse_direction' => 1,
                'enable_zoom' => 1,
            ]);

        $response->assertRedirect(route('showrooms.manage.bikes.360', [$this->showroomA, $this->bikeA]));
        $response->assertSessionHas('success', '360° Studio settings updated successfully.');

        $this->bikeA->refresh();
        $this->assertTrue($this->bikeA->has_360_view);
        $this->assertSame('image_sequence', $this->bikeA->viewer_type);
        $this->assertSame(6, $this->bikeA->viewer_settings['rotation_speed']);
        $this->assertSame(180, $this->bikeA->viewer_settings['default_angle']);
        $this->assertTrue($this->bikeA->viewer_settings['reverse_direction']);
    }

    public function test_staff_cannot_update_360_settings_if_plan_lacks_feature(): void
    {
        $response = $this->actingAs($this->ownerB)
            ->put(route('showrooms.manage.bikes.360.settings', [$this->showroomB, $this->bikeB]), [
                'has_360_view' => 1,
                'viewer_type' => 'image_sequence',
                'rotation_speed' => 5,
            ]);

        $response->assertStatus(403);
    }

    public function test_staff_can_import_bulk_360_frame_urls_with_auto_calculated_angles(): void
    {
        $urls = [
            'https://cdn.example.com/bikes/ducati/360-00.jpg',
            'https://cdn.example.com/bikes/ducati/360-01.jpg',
            'https://cdn.example.com/bikes/ducati/360-02.jpg',
            'https://cdn.example.com/bikes/ducati/360-03.jpg',
        ];

        $response = $this->actingAs($this->ownerA)
            ->post(route('showrooms.manage.bikes.360.frames.store', [$this->showroomA, $this->bikeA]), [
                'bulk_urls' => implode("\n", $urls),
            ]);

        $response->assertRedirect(route('showrooms.manage.bikes.360', [$this->showroomA, $this->bikeA]));
        $response->assertSessionHas('success', '4 360° frames added successfully.');

        $this->assertSame(4, $this->bikeA->frames360()->count());

        $firstFrame = $this->bikeA->frames360()->where('sort_order', 0)->first();
        $this->assertNotNull($firstFrame);
        $this->assertSame(0, $firstFrame->frame_angle);
        $this->assertTrue($firstFrame->is_360_frame);

        $secondFrame = $this->bikeA->frames360()->where('sort_order', 1)->first();
        $this->assertNotNull($secondFrame);
        $this->assertSame(90, $secondFrame->frame_angle);

        $lastFrame = $this->bikeA->frames360()->where('sort_order', 3)->first();
        $this->assertNotNull($lastFrame);
        $this->assertSame(270, $lastFrame->frame_angle);
    }

    public function test_staff_can_import_single_frame_with_custom_angle(): void
    {
        $response = $this->actingAs($this->ownerA)
            ->post(route('showrooms.manage.bikes.360.frames.store', [$this->showroomA, $this->bikeA]), [
                'single_url' => 'https://cdn.example.com/bikes/ducati/frame-120.jpg',
                'single_angle' => 120,
            ]);

        $response->assertRedirect(route('showrooms.manage.bikes.360', [$this->showroomA, $this->bikeA]));

        $frame = $this->bikeA->frames360()->where('frame_angle', 120)->first();
        $this->assertNotNull($frame);
        $this->assertSame('https://cdn.example.com/bikes/ducati/frame-120.jpg', $frame->image_url);
        $this->assertTrue($frame->is_360_frame);
    }

    public function test_staff_can_delete_individual_360_frame(): void
    {
        $frame = BikeImage::create([
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $this->bikeA->id,
            'image_url' => 'https://cdn.example.com/frame-delete.jpg',
            'is_360_frame' => true,
            'frame_angle' => 45,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->ownerA)
            ->delete(route('showrooms.manage.bikes.360.frames.destroy', [$this->showroomA, $this->bikeA, $frame]));

        $response->assertRedirect(route('showrooms.manage.bikes.360', [$this->showroomA, $this->bikeA]));
        $this->assertDatabaseMissing('bike_images', ['id' => $frame->id]);
    }

    public function test_staff_can_clear_all_360_frames(): void
    {
        for ($i = 0; $i < 3; $i++) {
            BikeImage::create([
                'tenant_id' => $this->showroomA->id,
                'bike_id' => $this->bikeA->id,
                'image_url' => "https://cdn.example.com/frame-{$i}.jpg",
                'is_360_frame' => true,
                'frame_angle' => $i * 120,
                'sort_order' => $i,
            ]);
        }

        $this->assertSame(3, $this->bikeA->frames360()->count());

        $response = $this->actingAs($this->ownerA)
            ->post(route('showrooms.manage.bikes.360.frames.clear', [$this->showroomA, $this->bikeA]));

        $response->assertRedirect(route('showrooms.manage.bikes.360', [$this->showroomA, $this->bikeA]));
        $this->assertSame(0, $this->bikeA->frames360()->count());
    }

    public function test_staff_can_configure_interactive_hotspots(): void
    {
        $hotspots = [
            [
                'title' => 'Brembo Stylema Calipers',
                'description' => 'Dual 330mm semi-floating discs with radially mounted 4-piston calipers',
                'frame_angle' => 90,
                'x_pct' => 40.5,
                'y_pct' => 70.0,
            ],
            [
                'title' => 'Öhlins Smart EC 2.0',
                'description' => 'Electronic semi-active suspension system',
                'frame_angle' => 270,
                'x_pct' => 60.0,
                'y_pct' => 50.0,
            ],
        ];

        $response = $this->actingAs($this->ownerA)
            ->post(route('showrooms.manage.bikes.360.hotspots', [$this->showroomA, $this->bikeA]), [
                'hotspots' => $hotspots,
            ]);

        $response->assertRedirect(route('showrooms.manage.bikes.360', [$this->showroomA, $this->bikeA]));
        $response->assertSessionHas('success', 'Interactive hotspots saved successfully.');

        $this->bikeA->refresh();
        $this->assertCount(2, $this->bikeA->hotspots_list);
        $this->assertSame('Brembo Stylema Calipers', $this->bikeA->hotspots_list[0]['title']);
        $this->assertSame(90, $this->bikeA->hotspots_list[0]['frame_angle']);
        $this->assertSame('Öhlins Smart EC 2.0', $this->bikeA->hotspots_list[1]['title']);
    }

    public function test_multi_tenant_isolation_staff_cannot_manage_another_showroom_bike_360(): void
    {
        // Owner B tries to access Showroom A's bike 360 studio
        $response = $this->actingAs($this->ownerB)
            ->get(route('showrooms.manage.bikes.360', [$this->showroomA, $this->bikeA]));

        // Blocked by tenant membership middleware
        $response->assertStatus(403);

        // Owner B tries to mutate Showroom A's settings under Showroom B route
        $response2 = $this->actingAs($this->ownerB)
            ->put(route('showrooms.manage.bikes.360.settings', [$this->showroomB, $this->bikeA]), [
                'has_360_view' => 1,
                'viewer_type' => 'image_sequence',
            ]);

        $response2->assertStatus(404);
    }

    public function test_public_visitor_can_view_bike_detail_with_360_viewer_tab(): void
    {
        // Add 2 frames to Bike A
        BikeImage::create([
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $this->bikeA->id,
            'image_url' => 'https://cdn.example.com/panigale-0.jpg',
            'is_360_frame' => true,
            'frame_angle' => 0,
            'sort_order' => 0,
        ]);
        BikeImage::create([
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $this->bikeA->id,
            'image_url' => 'https://cdn.example.com/panigale-180.jpg',
            'is_360_frame' => true,
            'frame_angle' => 180,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('showrooms.bikes.show', [$this->showroomA, $this->bikeA]));

        $response->assertStatus(200);
        $response->assertSee('Interactive 360° Studio');
        $response->assertSee('Fullscreen 360');
        $response->assertSee('Drag to spin 360°');
    }

    public function test_public_visitor_sees_standard_gallery_when_360_disabled_or_unentitled(): void
    {
        // Bike B has has_360_view = false and Showroom B lacks entitlement
        $response = $this->get(route('showrooms.bikes.show', [$this->showroomB, $this->bikeB]));

        $response->assertStatus(200);
        $response->assertDontSee('Interactive 360° Studio');
        $response->assertDontSee('Fullscreen 360');
    }

    public function test_public_visitor_can_view_dedicated_fullscreen_360_page(): void
    {
        // Add frame to Bike A
        BikeImage::create([
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $this->bikeA->id,
            'image_url' => 'https://cdn.example.com/panigale-0.jpg',
            'is_360_frame' => true,
            'frame_angle' => 0,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('showrooms.bikes.360', [$this->showroomA, $this->bikeA]));

        $response->assertStatus(200);
        $response->assertSee('Exit 360 Studio');
        $response->assertSee('Drag horizontally to spin 360°');
        $response->assertSee('Book Test Ride');
        $response->assertSee('Auto Spin');
    }

    public function test_fullscreen_360_page_redirects_if_unentitled_or_no_360_view(): void
    {
        // Bike B has no 360 view and Showroom B lacks entitlement
        $response = $this->get(route('showrooms.bikes.360', [$this->showroomB, $this->bikeB]));

        $response->assertRedirect(route('showrooms.bikes.show', [$this->showroomB, $this->bikeB]));
        $response->assertSessionHas('error');
    }

    public function test_360_json_data_endpoint_returns_frames_and_hotspots(): void
    {
        BikeImage::create([
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $this->bikeA->id,
            'image_url' => 'https://cdn.example.com/panigale-0.jpg',
            'is_360_frame' => true,
            'frame_angle' => 0,
            'sort_order' => 0,
        ]);

        $this->bikeA->update([
            'hotspots' => [
                [
                    'title' => 'TFT Display',
                    'frame_angle' => 0,
                    'x_pct' => 50,
                    'y_pct' => 30,
                ],
            ],
        ]);

        $response = $this->getJson(route('showrooms.bikes.360.data', [$this->showroomA, $this->bikeA]));

        $response->assertStatus(200);
        $response->assertJson([
            'available' => true,
            'bike' => [
                'name' => 'Ducati Panigale V4 S',
            ],
            'viewer_type' => 'image_sequence',
        ]);
        $response->assertJsonCount(1, 'frames');
        $response->assertJsonCount(1, 'hotspots');
    }

    public function test_360_frames_are_excluded_from_standard_gallery_images_relation(): void
    {
        // Add 2 regular gallery images
        BikeImage::create([
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $this->bikeA->id,
            'image_url' => 'https://cdn.example.com/gallery-1.jpg',
            'is_primary' => true,
            'is_360_frame' => false,
            'sort_order' => 0,
        ]);
        BikeImage::create([
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $this->bikeA->id,
            'image_url' => 'https://cdn.example.com/gallery-2.jpg',
            'is_primary' => false,
            'is_360_frame' => false,
            'sort_order' => 1,
        ]);

        // Add 3 360 frames
        for ($i = 0; $i < 3; $i++) {
            BikeImage::create([
                'tenant_id' => $this->showroomA->id,
                'bike_id' => $this->bikeA->id,
                'image_url' => "https://cdn.example.com/360-{$i}.jpg",
                'is_360_frame' => true,
                'frame_angle' => $i * 120,
                'sort_order' => $i,
            ]);
        }

        $this->bikeA->refresh();

        // Standard images() should only return the 2 gallery photos
        $this->assertSame(2, $this->bikeA->images()->count());
        $this->assertSame(2, $this->bikeA->images->count());

        // frames360() should return the 3 turntable frames
        $this->assertSame(3, $this->bikeA->frames360()->count());
        $this->assertSame(3, $this->bikeA->frames360->count());

        // allImages() should return all 5
        $this->assertSame(5, $this->bikeA->allImages()->count());
    }
}
