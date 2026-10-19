<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $owner;

    protected Showroom $showroom;

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

        $this->owner = User::create([
            'name' => 'Rajesh Sharma',
            'email' => 'rajesh@apex.example',
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

        $this->starterPlan = SubscriptionPlan::create([
            'name' => 'Starter Dealership',
            'slug' => 'starter',
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
        ]);

        $this->proPlan = SubscriptionPlan::create([
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
            'has_advanced_analytics' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }

    public function test_admin_can_view_subscription_plans_and_pricing(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/plans');

        $response->assertOk();
        $response->assertSee('Subscription Plans');
        $response->assertSee('Starter Dealership');
        $response->assertSee('Professional Showroom');
        $response->assertSee('₹999.00');
    }

    public function test_admin_can_create_new_subscription_plan(): void
    {
        $this->actingAs($this->admin);

        $payload = [
            'name' => 'Enterprise Chain',
            'slug' => 'enterprise-chain',
            'description' => 'Unlimited scaling for motorcycle dealership chains.',
            'monthly_price' => 7999.00,
            'annual_price' => 79990.00,
            'currency' => 'INR',
            'trial_days' => 30,
            'max_staff_accounts' => 50,
            'max_bike_listings' => 200,
            'max_monthly_bookings' => 1000,
            'media_storage_mb' => 20480,
            'has_careers_module' => 1,
            'has_scratch_cards' => 1,
            'has_360_viewer' => 1,
            'has_ai_assistant' => 1,
            'has_custom_domain' => 1,
            'has_advanced_analytics' => 1,
            'is_active' => 1,
            'sort_order' => 3,
        ];

        $response = $this->post('/admin/plans', $payload);

        $response->assertRedirect('/admin/plans');

        $this->assertDatabaseHas('subscription_plans', [
            'slug' => 'enterprise-chain',
            'monthly_price' => 7999.00,
            'max_bike_listings' => 200,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'subscription_plan.created',
        ]);
    }

    public function test_admin_can_update_and_toggle_plan_status(): void
    {
        $this->actingAs($this->admin);

        // Edit
        $updateResponse = $this->put("/admin/plans/{$this->starterPlan->slug}", [
            'name' => 'Starter Dealership v2',
            'monthly_price' => 1199.00,
            'annual_price' => 11990.00,
            'currency' => 'INR',
            'trial_days' => 7,
            'max_staff_accounts' => 4,
            'max_bike_listings' => 20,
            'max_monthly_bookings' => 60,
            'media_storage_mb' => 2048,
            'is_active' => 1,
        ]);

        $updateResponse->assertRedirect('/admin/plans');

        $this->starterPlan->refresh();
        $this->assertEquals('Starter Dealership v2', $this->starterPlan->name);
        $this->assertEquals('1199.00', $this->starterPlan->monthly_price);

        // Toggle active status
        $toggleResponse = $this->post("/admin/plans/{$this->starterPlan->slug}/toggle");
        $toggleResponse->assertRedirect();

        $this->starterPlan->refresh();
        $this->assertFalse($this->starterPlan->is_active);
    }

    public function test_admin_can_view_all_showroom_subscriptions(): void
    {
        $this->actingAs($this->admin);

        $service = app(SubscriptionService::class);
        $service->subscribe($this->showroom, $this->proPlan, 'monthly', $this->admin->id);

        $response = $this->get('/admin/subscriptions');

        $response->assertOk();
        $response->assertSee('Subscriptions Oversight');
        $response->assertSee('Apex Moto Hub');
        $response->assertSee('Professional Showroom');
    }

    public function test_admin_can_extend_subscription_expiry(): void
    {
        $this->actingAs($this->admin);

        $service = app(SubscriptionService::class);
        $subscription = $service->subscribe($this->showroom, $this->proPlan, 'monthly', $this->admin->id);

        $originalEnd = $subscription->current_period_end->copy();

        $response = $this->post("/admin/subscriptions/{$subscription->id}/extend", [
            'days' => 30,
            'reason' => 'VIP dealership extension',
        ]);

        $response->assertRedirect();

        $subscription->refresh();
        $this->assertTrue($subscription->current_period_end->gt($originalEnd));

        $this->assertDatabaseHas('subscription_histories', [
            'subscription_id' => $subscription->id,
            'action' => 'expiry_extended',
        ]);
    }

    public function test_admin_can_switch_showroom_subscription_plan(): void
    {
        $this->actingAs($this->admin);

        $service = app(SubscriptionService::class);
        $subscription = $service->subscribe($this->showroom, $this->starterPlan, 'monthly', $this->admin->id);

        $this->assertEquals($this->starterPlan->id, $subscription->plan_id);

        $response = $this->post("/admin/subscriptions/{$subscription->id}/change-plan", [
            'plan_id' => $this->proPlan->id,
        ]);

        $response->assertRedirect();

        $subscription->refresh();
        $this->assertEquals($this->proPlan->id, $subscription->plan_id);

        $this->assertDatabaseHas('subscription_histories', [
            'subscription_id' => $subscription->id,
            'action' => 'plan_changed',
            'previous_plan_id' => $this->starterPlan->id,
            'new_plan_id' => $this->proPlan->id,
        ]);
    }

    public function test_showroom_entitlement_and_feature_flags_evaluated_dynamically(): void
    {
        $service = app(SubscriptionService::class);

        // On Starter Plan: No 360 viewer, max 3 staff
        $service->subscribe($this->showroom, $this->starterPlan, 'monthly');

        $this->showroom->refresh();
        $this->assertFalse($this->showroom->canAccessFeature('has_360_viewer'));
        $this->assertFalse($this->showroom->hasReachedLimit('max_staff_accounts', 2));
        $this->assertTrue($this->showroom->hasReachedLimit('max_staff_accounts', 3));
        $this->assertTrue($this->showroom->hasReachedLimit('max_staff_accounts', 5));

        // Switch to Pro Plan: Has 360 viewer, max 10 staff
        $service->subscribe($this->showroom, $this->proPlan, 'monthly');

        $this->showroom->refresh();
        $this->assertTrue($this->showroom->canAccessFeature('has_360_viewer'));
        $this->assertTrue($this->showroom->canAccessFeature('has_ai_assistant'));
        $this->assertFalse($this->showroom->hasReachedLimit('max_staff_accounts', 5));
        $this->assertTrue($this->showroom->hasReachedLimit('max_staff_accounts', 10));
    }

    public function test_subscription_expiry_enforcement_and_middleware_redirection(): void
    {
        $this->actingAs($this->owner);

        // Expired subscription
        $subscription = Subscription::create([
            'showroom_id' => $this->showroom->id,
            'plan_id' => $this->starterPlan->id,
            'status' => SubscriptionStatus::Expired,
            'billing_interval' => 'monthly',
            'current_period_start' => now()->subMonths(2),
            'current_period_end' => now()->subMonth(),
            'auto_renew' => false,
        ]);

        // Accessing back-office with expired subscription redirects to billing page
        $response = $this->get("/showrooms/{$this->showroom->slug}/manage");
        $response->assertRedirect(route('showrooms.billing.index', $this->showroom));
        $response->assertSessionHas('error');

        // Billing page itself remains accessible so the showroom can renew
        $billingResponse = $this->get("/showrooms/{$this->showroom->slug}/billing");
        $billingResponse->assertOk();
        $billingResponse->assertSee('Subscription Has Expired');
    }

    public function test_showroom_owner_can_view_billing_page_and_select_plan(): void
    {
        $this->actingAs($this->owner);

        // View billing page
        $response = $this->get("/showrooms/{$this->showroom->slug}/billing");
        $response->assertOk();
        $response->assertSee('Showroom Subscription & Quotas', false);
        $response->assertSee('Starter Dealership');
        // Select Pro plan with direct activation
        $selectResponse = $this->post("/showrooms/{$this->showroom->slug}/billing/select-plan", [
            'plan_id' => $this->proPlan->id,
            'billing_interval' => 'monthly',
            'direct_activate' => true,
        ]);

        $selectResponse->assertRedirect(route('showrooms.billing.index', $this->showroom));
        $selectResponse->assertSessionHas('success');

        $this->showroom->refresh();
        $this->assertNotNull($this->showroom->subscription);
        $this->assertEquals($this->proPlan->id, $this->showroom->subscription->plan_id);
        $this->assertTrue($this->showroom->subscription->isActive());

        // Now back-office management access is granted
        $manageResponse = $this->get("/showrooms/{$this->showroom->slug}/manage");
        $manageResponse->assertOk();
    }

    public function test_showroom_owner_selecting_plan_redirects_to_razorpay_checkout(): void
    {
        $this->actingAs($this->owner);

        $response = $this->post("/showrooms/{$this->showroom->slug}/billing/select-plan", [
            'plan_id' => $this->proPlan->id,
            'billing_interval' => 'monthly',
        ]);

        $response->assertRedirect(route('showrooms.billing.checkout', [
            'showroom' => $this->showroom,
            'plan_id' => $this->proPlan->id,
            'interval' => 'monthly',
        ]));
    }

    public function test_super_admin_bypasses_subscription_gate_even_if_showroom_expired(): void
    {
        $this->actingAs($this->admin);

        // Showroom subscription is expired
        Subscription::create([
            'showroom_id' => $this->showroom->id,
            'plan_id' => $this->starterPlan->id,
            'status' => SubscriptionStatus::Expired,
            'billing_interval' => 'monthly',
            'current_period_start' => now()->subMonths(2),
            'current_period_end' => now()->subMonth(),
            'auto_renew' => false,
        ]);

        // Super Admin can access back-office with zero blockage
        $response = $this->get("/showrooms/{$this->showroom->slug}/manage");
        $response->assertOk();
    }

    public function test_admin_can_filter_subscriptions_by_date_range(): void
    {
        $this->actingAs($this->admin);

        $sub = Subscription::create([
            'showroom_id' => $this->showroom->id,
            'plan_id' => $this->proPlan->id,
            'status' => SubscriptionStatus::Active,
            'billing_interval' => 'monthly',
            'current_period_start' => now()->startOfMonth(),
            'current_period_end' => now()->addDays(15),
            'auto_renew' => true,
        ]);

        // Filter within matching range
        $response = $this->get(route('admin.subscriptions.index', [
            'date_type' => 'expiry',
            'from_date' => now()->format('Y-m-d'),
            'to_date' => now()->addDays(20)->format('Y-m-d'),
        ]));

        $response->assertOk();
        $response->assertSee($this->showroom->name);

        // Filter outside matching range
        $mismatchResponse = $this->get(route('admin.subscriptions.index', [
            'date_type' => 'expiry',
            'from_date' => now()->addDays(50)->format('Y-m-d'),
            'to_date' => now()->addDays(60)->format('Y-m-d'),
        ]));

        $mismatchResponse->assertOk();
        $mismatchResponse->assertDontSee($this->showroom->name);
    }

    public function test_admin_can_export_all_filtered_subscriptions_to_csv(): void
    {
        $this->actingAs($this->admin);

        Subscription::create([
            'showroom_id' => $this->showroom->id,
            'plan_id' => $this->proPlan->id,
            'status' => SubscriptionStatus::Active,
            'billing_interval' => 'monthly',
            'current_period_start' => now()->startOfMonth(),
            'current_period_end' => now()->addDays(25),
            'auto_renew' => true,
        ]);

        $response = $this->get(route('admin.subscriptions.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('Subscription ID', $content);
        $this->assertStringContainsString('Showroom Name', $content);
        $this->assertStringContainsString('Apex Moto Hub', $content);
        $this->assertStringContainsString('Professional Showroom', $content);
    }

    public function test_admin_can_export_selected_subscriptions_to_csv(): void
    {
        $this->actingAs($this->admin);

        $showroom2 = Showroom::create([
            'name' => 'Zenith Speedways',
            'slug' => 'zenith-speed',
            'email' => 'info@zenith.example',
            'city' => 'Pune',
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        $sub1 = Subscription::create([
            'showroom_id' => $this->showroom->id,
            'plan_id' => $this->proPlan->id,
            'status' => SubscriptionStatus::Active,
            'billing_interval' => 'monthly',
            'current_period_start' => now()->startOfMonth(),
            'current_period_end' => now()->addDays(25),
            'auto_renew' => true,
        ]);

        $sub2 = Subscription::create([
            'showroom_id' => $showroom2->id,
            'plan_id' => $this->starterPlan->id,
            'status' => SubscriptionStatus::Active,
            'billing_interval' => 'annual',
            'current_period_start' => now()->startOfMonth(),
            'current_period_end' => now()->addYear(),
            'auto_renew' => true,
        ]);

        // Export only sub1
        $response = $this->post(route('admin.subscriptions.export'), [
            'selected_ids' => [$sub1->id],
        ]);

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('Apex Moto Hub', $content);
        $this->assertStringNotContainsString('Zenith Speedways', $content);
    }
}
