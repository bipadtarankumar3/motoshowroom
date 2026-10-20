<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSpecialFeatureAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $regularUser;

    protected Showroom $showroom;

    protected SubscriptionPlan $starterPlan;

    protected Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Platform Super Admin',
            'email' => 'admin@platform.example',
            'password' => 'Password123!',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->regularUser = User::create([
            'name' => 'Regular Showroom Owner',
            'email' => 'owner@dealer.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->starterPlan = SubscriptionPlan::create([
            'name' => 'Starter Dealership',
            'slug' => 'starter-dealership',
            'description' => 'Entry plan for small dealerships',
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
            'has_360_viewer' => true,
            'has_ai_assistant' => false,
            'has_custom_domain' => false,
            'has_advanced_analytics' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->showroom = Showroom::create([
            'name' => 'Velocity Speed Hub',
            'slug' => 'velocity-speed',
            'email' => 'hello@velocity.example',
            'phone' => '+91 9876543210',
            'city' => 'Pune',
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroom->id,
            'user_id' => $this->regularUser->id,
            'role' => 'owner',
            'status' => 'active',
            'permissions' => ['all'],
        ]);

        $this->subscription = Subscription::create([
            'showroom_id' => $this->showroom->id,
            'plan_id' => $this->starterPlan->id,
            'status' => SubscriptionStatus::Active,
            'billing_interval' => 'monthly',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
            'auto_renew' => true,
        ]);
    }

    public function test_showroom_on_starter_plan_initially_cannot_access_ai_assistant_or_custom_domain(): void
    {
        $this->assertFalse($this->showroom->canAccessFeature('has_ai_assistant'));
        $this->assertFalse($this->showroom->canAccessFeature('has_custom_domain'));
        $this->assertFalse($this->showroom->canAccessFeature('has_advanced_analytics'));

        // But starter plan features work
        $this->assertTrue($this->showroom->canAccessFeature('has_360_viewer'));
        $this->assertTrue($this->showroom->canAccessFeature('has_scratch_cards'));
    }

    public function test_admin_can_view_plans_index_with_feature_overrides_controls(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.plans.index'));

        $response->assertOk();
        $response->assertSee('Grant Extra Feature Access');
        $response->assertSee('Showroom Special Feature Access & VIP Grants', false);
        $response->assertSee('Velocity Speed Hub');
    }

    public function test_admin_can_grant_extra_feature_access_via_subscription_endpoint(): void
    {
        // Starter plan does not include AI Assistant or Custom Domain
        $this->assertFalse($this->showroom->fresh()->canAccessFeature('has_ai_assistant'));

        $response = $this->actingAs($this->admin)
            ->post(route('admin.subscriptions.feature-overrides', $this->subscription), [
                'has_ai_assistant' => 'grant',
                'has_custom_domain' => 'grant',
                'grant_notes' => 'Complimentary VIP access granted by admin',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->showroom->refresh();
        $this->subscription->refresh();

        // Extra access is now active for this user regardless of plan!
        $this->assertTrue($this->showroom->canAccessFeature('has_ai_assistant'));
        $this->assertTrue($this->showroom->canAccessFeature('has_custom_domain'));
        $this->assertFalse($this->showroom->canAccessFeature('has_advanced_analytics'));

        // Audit log created
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'subscription.feature_overrides_updated',
            'tenant_id' => $this->showroom->id,
        ]);
    }

    public function test_admin_can_grant_extra_feature_access_and_custom_quotas_via_showroom_endpoint(): void
    {
        // Standard starter plan limit is 15 bikes
        $this->assertTrue($this->showroom->hasReachedLimit('max_bike_listings', 15));
        $this->assertFalse($this->showroom->hasReachedLimit('max_bike_listings', 14));

        $response = $this->actingAs($this->admin)
            ->post(route('admin.showrooms.feature-overrides', $this->showroom), [
                'has_advanced_analytics' => 'grant',
                'max_bike_listings' => 50,
                'grant_notes' => 'Custom 50 bike limit upgrade',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->showroom->refresh();

        // Feature granted
        $this->assertTrue($this->showroom->canAccessFeature('has_advanced_analytics'));

        // Custom quota override of 50 active
        $this->assertFalse($this->showroom->hasReachedLimit('max_bike_listings', 40));
        $this->assertTrue($this->showroom->hasReachedLimit('max_bike_listings', 50));
    }

    public function test_admin_can_force_disable_a_feature_via_override(): void
    {
        // By default starter plan has 360 viewer
        $this->assertTrue($this->showroom->canAccessFeature('has_360_viewer'));

        // Admin forces disable
        $this->actingAs($this->admin)
            ->post(route('admin.showrooms.feature-overrides', $this->showroom), [
                'has_360_viewer' => 'deny',
            ]);

        $this->showroom->refresh();
        $this->assertFalse($this->showroom->canAccessFeature('has_360_viewer'));
    }

    public function test_non_admin_cannot_update_feature_overrides(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('admin.showrooms.feature-overrides', $this->showroom), [
                'has_ai_assistant' => 'grant',
            ]);

        $response->assertForbidden();
    }
}
