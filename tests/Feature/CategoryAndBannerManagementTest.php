<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryAndBannerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $unauthorizedUser;

    protected Showroom $showroom;

    protected SubscriptionPlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->owner = User::create([
            'name' => 'Apex Dealer Owner',
            'email' => 'owner@apex.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->unauthorizedUser = User::create([
            'name' => 'Unauthorized User',
            'email' => 'unauth@other.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->showroom = Showroom::create([
            'name' => 'Apex Moto Hub',
            'slug' => 'apex-moto',
            'email' => 'contact@apex.example',
            'phone' => '+91 8023456789',
            'city' => 'Bangalore',
            'state' => 'Karnataka',
            'pincode' => '560001',
            'address' => '100 Feet Road, Indiranagar',
            'brand_color_primary' => '#ff3366',
            'brand_color_accent' => '#38bdf8',
            'is_active' => true,
            'is_verified' => true,
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroom->id,
            'user_id' => $this->owner->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        $this->plan = SubscriptionPlan::create([
            'name' => 'Enterprise Dealer',
            'slug' => 'enterprise-dealer',
            'price' => 9999,
            'billing_cycle' => 'monthly',
            'max_bikes' => 100,
            'max_staff' => 20,
            'features' => ['all'],
            'is_active' => true,
        ]);

        app(SubscriptionService::class)->startTrial($this->showroom, $this->plan, 30);
    }

    public function test_owner_can_view_category_master_page(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('showrooms.manage.categories.index', $this->showroom));

        $response->assertOk();
        $response->assertSee('Category Master');
        $response->assertSee('Add New Category');
    }

    public function test_owner_can_create_category_with_uploaded_image(): void
    {
        $imageFile = UploadedFile::fake()->image('adventure_tourer.jpg', 600, 450);

        $response = $this->actingAs($this->owner)
            ->post(route('showrooms.manage.categories.store', $this->showroom), [
                'name' => 'Adventure Tourer',
                'subtitle' => 'Long Distance & Off-Road',
                'description' => 'Built for trans-continental rallies and rugged terrain.',
                'image_file' => $imageFile,
                'sort_order' => 1,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('showrooms.manage.categories.index', $this->showroom));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'tenant_id' => $this->showroom->id,
            'name' => 'Adventure Tourer',
            'slug' => 'adventure-tourer',
            'subtitle' => 'Long Distance & Off-Road',
            'is_active' => true,
        ]);

        $category = Category::where('slug', 'adventure-tourer')->first();
        $this->assertNotNull($category->image_path);
        Storage::disk('public')->assertExists($category->image_path);
    }

    public function test_owner_can_update_category_and_toggle_status(): void
    {
        $category = Category::create([
            'tenant_id' => $this->showroom->id,
            'name' => 'Cruisers',
            'slug' => 'cruisers',
            'subtitle' => 'V-Twin Highways',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $updateResponse = $this->actingAs($this->owner)
            ->put(route('showrooms.manage.categories.update', [$this->showroom, $category]), [
                'name' => 'Classic Cruisers',
                'subtitle' => 'Low Slung Comfort',
                'sort_order' => 5,
                'is_active' => '1',
            ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Classic Cruisers',
            'subtitle' => 'Low Slung Comfort',
            'sort_order' => 5,
        ]);

        // Toggle Active
        $toggleResponse = $this->actingAs($this->owner)
            ->post(route('showrooms.manage.categories.toggle-active', [$this->showroom, $category]));

        $toggleResponse->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => false,
        ]);
    }

    public function test_owner_can_delete_category(): void
    {
        $category = Category::create([
            'tenant_id' => $this->showroom->id,
            'name' => 'Vintage',
            'slug' => 'vintage',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->owner)
            ->delete(route('showrooms.manage.categories.destroy', [$this->showroom, $category]));

        $response->assertRedirect();
        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_creating_motorcycle_with_category_master(): void
    {
        $customCat = Category::create([
            'tenant_id' => $this->showroom->id,
            'name' => 'Super Naked',
            'slug' => 'super-naked',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->owner)
            ->post(route('showrooms.manage.bikes.store', $this->showroom), [
                'name' => 'Ducati Streetfighter V4',
                'brand' => 'Ducati',
                'model_year' => 2026,
                'category' => 'super-naked',
                'category_id' => $customCat->id,
                'price' => 2450000,
                'engine_cc' => 1103,
                'power_bhp' => 208,
                'fuel_type' => 'petrol',
                'status' => 'in_stock',
                'is_published' => '1',
            ]);

        $response->assertRedirect(route('showrooms.manage.bikes.index', $this->showroom));

        $this->assertDatabaseHas('bikes', [
            'tenant_id' => $this->showroom->id,
            'name' => 'Ducati Streetfighter V4',
            'category_id' => $customCat->id,
        ]);
    }

    public function test_owner_can_manage_hero_banners(): void
    {
        // 1. View Banner index
        $indexResponse = $this->actingAs($this->owner)
            ->get(route('showrooms.manage.banners.index', $this->showroom));

        $indexResponse->assertOk();
        $indexResponse->assertSee('Homepage Hero Banners');

        // 2. Upload and Create Banner
        $bannerImage = UploadedFile::fake()->image('banner_hero.jpg', 1920, 1080);

        $storeResponse = $this->actingAs($this->owner)
            ->post(route('showrooms.manage.banners.store', $this->showroom), [
                'badge' => 'SPRING FEST 2026',
                'title_prefix' => 'Unleash Your',
                'title_highlight' => 'Pure Passion',
                'title_suffix' => 'On The Track',
                'subtitle' => 'Experience unmatched velocity with our new track edition beasts.',
                'btn_primary_text' => 'Book Test Ride',
                'btn_primary_url' => '/test-ride',
                'feature_1_title' => 'Track Ready',
                'feature_1_sub' => 'Dyno Tested',
                'image_file' => $bannerImage,
                'sort_order' => 1,
                'is_active' => '1',
            ]);

        $storeResponse->assertRedirect(route('showrooms.manage.banners.index', $this->showroom));
        $this->assertDatabaseHas('banners', [
            'tenant_id' => $this->showroom->id,
            'badge' => 'SPRING FEST 2026',
            'title_highlight' => 'Pure Passion',
            'is_active' => true,
        ]);

        $banner = Banner::where('badge', 'SPRING FEST 2026')->first();
        $this->assertNotNull($banner->image_path);
        Storage::disk('public')->assertExists($banner->image_path);
    }

    public function test_active_banners_and_categories_render_on_public_storefront(): void
    {
        // Create active custom category
        Category::create([
            'tenant_id' => $this->showroom->id,
            'name' => 'Track Supersport',
            'slug' => 'track-supersport',
            'subtitle' => 'Aerodynamic Dominance',
            'image_url' => 'https://images.unsplash.com/photo-example',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Create active hero banner
        Banner::create([
            'tenant_id' => $this->showroom->id,
            'badge' => 'FLAGSHIP 2026',
            'title_prefix' => 'Conquer Every',
            'title_highlight' => 'Circuit',
            'title_suffix' => 'Worldwide',
            'subtitle' => 'Official dealership with track warranty.',
            'btn_primary_text' => 'Explore Inventory',
            'image_url' => 'https://images.unsplash.com/banner-example.jpg',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('showrooms.show', $this->showroom));

        $response->assertOk();
        $response->assertSee('FLAGSHIP 2026');
        $response->assertSee('Conquer Every');
        $response->assertSee('Circuit');
        $response->assertSee('Track Supersport');
        $response->assertSee('Aerodynamic Dominance');
    }

    public function test_unauthorized_user_cannot_manage_categories_or_banners(): void
    {
        $catResponse = $this->actingAs($this->unauthorizedUser)
            ->get(route('showrooms.manage.categories.index', $this->showroom));

        $catResponse->assertForbidden();

        $bannerResponse = $this->actingAs($this->unauthorizedUser)
            ->get(route('showrooms.manage.banners.index', $this->showroom));

        $bannerResponse->assertForbidden();
    }

    public function test_showroom_staff_can_update_razorpay_credentials_in_settings(): void
    {
        $response = $this->actingAs($this->owner)
            ->put(route('showrooms.manage.settings.update', $this->showroom), [
                'email' => 'apex.updated@example.com',
                'phone' => '+91 9999988888',
                'city' => 'Bengaluru',
                'razorpay_enabled' => '1',
                'razorpay_key_id' => 'rzp_live_custom12345678',
                'razorpay_key_secret' => 'super_secret_rzp_key_999',
                'razorpay_webhook_secret' => 'whsec_custom_123',
                'booking_token_amount' => 15000,
            ]);

        $response->assertRedirect(route('showrooms.manage.settings', $this->showroom));
        $response->assertSessionHas('success');

        $this->showroom->refresh();
        $this->assertEquals('apex.updated@example.com', $this->showroom->email);
        $this->assertEquals('rzp_live_custom12345678', $this->showroom->getRazorpayKeyId());
        $this->assertEquals('super_secret_rzp_key_999', $this->showroom->getRazorpayKeySecret());
        $this->assertEquals('whsec_custom_123', $this->showroom->getRazorpayWebhookSecret());
        $this->assertTrue($this->showroom->hasCustomRazorpayCredentials());
        $this->assertTrue($this->showroom->isRazorpayOnlinePaymentEnabled());
        $this->assertEquals(15000.0, $this->showroom->getBookingTokenAmount());

        // Check settings page renders these credentials properly
        $getSettingsResponse = $this->actingAs($this->owner)
            ->get(route('showrooms.manage.settings', $this->showroom));
        $getSettingsResponse->assertOk();
        $getSettingsResponse->assertSee('Razorpay Payment Gateway');
        $getSettingsResponse->assertSee('Custom Account Connected');
        $getSettingsResponse->assertSee('rzp_live_custom12345678');
    }
}
