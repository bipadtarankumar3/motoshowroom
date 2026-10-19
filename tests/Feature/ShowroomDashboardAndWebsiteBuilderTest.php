<?php

namespace Tests\Feature;

use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowroomDashboardAndWebsiteBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $owner;

    protected User $otherUser;

    protected Showroom $showroom;

    protected SubscriptionPlan $plan;

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

        $this->owner = User::create([
            'name' => 'Rajesh Sharma',
            'email' => 'rajesh@apex.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->otherUser = User::create([
            'name' => 'Other Customer',
            'email' => 'other@customer.example',
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
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroom->id,
            'user_id' => $this->owner->id,
            'role' => 'owner',
            'status' => 'active',
            'permissions' => ['all'],
        ]);

        $this->plan = SubscriptionPlan::create([
            'name' => 'Professional Showroom',
            'slug' => 'professional',
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
            'is_active' => true,
        ]);

        app(SubscriptionService::class)->subscribe($this->showroom, $this->plan, 'monthly');
    }

    public function test_authorized_staff_can_view_showroom_manage_dashboard(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get("/showrooms/{$this->showroom->slug}/manage");

        $response->assertOk();
        $response->assertSee('Apex Moto Hub');
        $response->assertSee('Storefront Customizer');
        $response->assertSee('Motorcycle Inventory');
        $response->assertSee('Professional Showroom');
    }

    public function test_unauthorized_user_cannot_access_showroom_back_office_dashboard(): void
    {
        $this->actingAs($this->otherUser);

        $response = $this->get("/showrooms/{$this->showroom->slug}/manage");

        $response->assertForbidden();
    }

    public function test_owner_can_access_website_builder_page(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get("/showrooms/{$this->showroom->slug}/manage/website");

        $response->assertOk();
        $response->assertSee('Storefront Customizer');
        $response->assertSee('Hero Banner & Visual Identity', false);
        $response->assertSee('Weekly Operating Hours Matrix');
    }

    public function test_owner_can_update_website_builder_content_and_branding(): void
    {
        $this->actingAs($this->owner);

        $payload = [
            'headline' => 'Pure Two-Wheeled Adrenaline',
            'tagline' => 'Premier Superbikes and Certified Dyno Tuning',
            'about_text' => 'Founded in 2020, Apex Moto Hub is the premier motorcycle destination.',
            'banner_url' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc',
            'brand_color_primary' => '#059669',
            'brand_color_accent' => '#064e3b',
            'whatsapp_number' => '+919988776655',
            'public_website_enabled' => 1,
            'operating_hours' => [
                'monday' => ['open' => '10:00', 'close' => '19:00', 'is_closed' => false],
                'sunday' => ['open' => '11:00', 'close' => '15:00', 'is_closed' => true],
            ],
            'social_links' => [
                'instagram' => 'https://instagram.com/apexmotohub',
                'youtube' => 'https://youtube.com/@apexmotohub',
            ],
        ];

        $response = $this->put("/showrooms/{$this->showroom->slug}/manage/website", $payload);

        $response->assertRedirect("/showrooms/{$this->showroom->slug}/manage/website");
        $response->assertSessionHas('success');

        $this->showroom->refresh();

        $this->assertEquals('Pure Two-Wheeled Adrenaline', $this->showroom->headline);
        $this->assertEquals('Premier Superbikes and Certified Dyno Tuning', $this->showroom->tagline);
        $this->assertEquals('#059669', $this->showroom->brand_color_primary);
        $this->assertEquals('+919988776655', $this->showroom->getSetting('whatsapp_number'));
        $this->assertEquals('https://instagram.com/apexmotohub', $this->showroom->getSocialLinks()['instagram']);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'showroom.website_updated',
            'tenant_id' => $this->showroom->id,
            'user_id' => $this->owner->id,
        ]);
    }

    public function test_public_storefront_reflects_website_builder_updates(): void
    {
        $this->showroom->update([
            'brand_color_primary' => '#059669',
            'settings' => [
                'headline' => 'Fastest Superbikes in Town',
                'tagline' => 'Track Ready Machines with Warranty',
                'about_text' => 'We curate only championship-grade motorcycles.',
                'whatsapp_number' => '+919876543210',
                'social_links' => [
                    'instagram' => 'https://instagram.com/apex',
                ],
            ],
        ]);

        $response = $this->get("/showrooms/{$this->showroom->slug}");

        $response->assertOk();
        $response->assertSee('Fastest Superbikes in Town');
        $response->assertSee('Track Ready Machines with Warranty');
        $response->assertSee('We curate only championship-grade motorcycles.');
        $response->assertSee('wa.me/919876543210', false);
        $response->assertSee('instagram.com/apex', false);
    }

    public function test_owner_can_view_and_update_business_settings(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get("/showrooms/{$this->showroom->slug}/manage/settings");
        $response->assertOk();
        $response->assertSee('Showroom Business Settings');

        $updateResponse = $this->put("/showrooms/{$this->showroom->slug}/manage/settings", [
            'email' => 'newcontact@apex.example',
            'phone' => '+91 8099887766',
            'address_line1' => '99 MG Road, Metro Pillar 42',
            'city' => 'Bangalore Central',
            'state' => 'Karnataka',
            'postal_code' => '560001',
        ]);

        $updateResponse->assertRedirect("/showrooms/{$this->showroom->slug}/manage/settings");

        $this->showroom->refresh();
        $this->assertEquals('newcontact@apex.example', $this->showroom->email);
        $this->assertEquals('+91 8099887766', $this->showroom->phone);
        $this->assertEquals('Bangalore Central', $this->showroom->city);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'showroom.settings_updated',
            'tenant_id' => $this->showroom->id,
        ]);
    }

    public function test_super_admin_can_access_any_showroom_dashboard_and_website_builder(): void
    {
        $this->actingAs($this->admin);

        $dashboardResponse = $this->get("/showrooms/{$this->showroom->slug}/manage");
        $dashboardResponse->assertOk();

        $builderResponse = $this->get("/showrooms/{$this->showroom->slug}/manage/website");
        $builderResponse->assertOk();
    }

    public function test_public_storefront_shows_offline_page_when_disabled(): void
    {
        $this->showroom->update(['public_website_enabled' => false]);

        // Unauthenticated customer visiting offline showroom gets 503 with offline page
        $response = $this->get("/showrooms/{$this->showroom->slug}");
        $response->assertStatus(503);
        $response->assertSee('Storefront Currently Offline');
        $response->assertSee($this->showroom->name);

        // Showroom manager visiting offline showroom sees preview banner
        $this->actingAs($this->owner);
        $managerResponse = $this->get("/showrooms/{$this->showroom->slug}");
        $managerResponse->assertOk();
        $managerResponse->assertSee('Storefront Offline Mode');
    }

    public function test_manager_can_access_support_hub(): void
    {
        $this->actingAs($this->owner);

        $response = $this->get("/showrooms/{$this->showroom->slug}/manage/support");
        $response->assertOk();
        $response->assertSee('Dealership Support & Help Center');
        $response->assertSee('Official Support Channels & Contact Desk');
        $response->assertSee('+91 (800) 555-0199');
        $response->assertSee('support@motoshowroom.com');
        $response->assertSee('Frequently Asked Questions');
    }
}
