<?php

namespace Tests\Feature;

use App\Models\PlatformSetting;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $regularUser;

    protected Showroom $showroom;

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
            'name' => 'John Customer',
            'email' => 'john@customer.example',
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
            'user_id' => $this->regularUser->id,
            'role' => 'owner',
            'status' => 'active',
            'permissions' => ['all'],
        ]);
    }

    public function test_non_admin_cannot_access_super_admin_dashboard_or_routes(): void
    {
        $this->actingAs($this->regularUser);

        $this->get('/admin')->assertForbidden();
        $this->get('/admin/dashboard')->assertForbidden();
        $this->get('/admin/showrooms')->assertForbidden();
        $this->get('/admin/showrooms/create')->assertForbidden();
        $this->get('/admin/audit-logs')->assertForbidden();
        $this->get('/admin/settings')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_admin_routes(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/showrooms')->assertRedirect('/login');
    }

    public function test_admin_can_view_dashboard_with_platform_metrics(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Super Admin Dashboard');
        $response->assertSee('Multi-Tenant Master Control');
        $response->assertSee('Apex Moto Hub');
    }

    public function test_admin_can_view_showrooms_list_with_search_and_filters(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/showrooms');

        $response->assertOk();
        $response->assertSee('Showroom Tenants');
        $response->assertSee('Apex Moto Hub');
        $response->assertSee('apex-moto');

        // Test search filter
        $searchResponse = $this->get('/admin/showrooms?search=Apex');
        $searchResponse->assertOk();
        $searchResponse->assertSee('Apex Moto Hub');

        $emptyResponse = $this->get('/admin/showrooms?search=NonExistentShowroom');
        $emptyResponse->assertOk();
        $emptyResponse->assertSee('No showrooms match your query');
    }

    public function test_admin_can_provision_new_showroom_with_existing_owner(): void
    {
        $this->actingAs($this->admin);

        $existingUser = User::create([
            'name' => 'Suresh Dealership Owner',
            'email' => 'suresh@dealership.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $payload = [
            'name' => 'Velocity Superbikes',
            'slug' => 'velocity-superbikes',
            'email' => 'contact@velocity.example',
            'phone' => '+91 9988776655',
            'city' => 'Pune',
            'state' => 'Maharashtra',
            'address_line1' => 'MG Road, Camp',
            'brand_color_primary' => '#2563eb',
            'brand_color_accent' => '#0f172a',
            'status' => 'active',
            'owner_mode' => 'existing',
            'owner_user_id' => $existingUser->id,
        ];

        $response = $this->post('/admin/showrooms', $payload);

        $response->assertRedirect();

        $showroom = Showroom::where('slug', 'velocity-superbikes')->first();
        $this->assertNotNull($showroom);
        $this->assertEquals('Velocity Superbikes', $showroom->name);
        $this->assertEquals('#2563eb', $showroom->brand_color_primary);

        // Verify membership created
        $this->assertDatabaseHas('showroom_memberships', [
            'showroom_id' => $showroom->id,
            'user_id' => $existingUser->id,
            'role' => 'owner',
        ]);

        // Verify audit log created
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'showroom.created',
            'tenant_id' => $showroom->id,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_provision_new_showroom_with_new_owner_inline(): void
    {
        $this->actingAs($this->admin);

        $payload = [
            'name' => 'Torque Motors',
            'slug' => 'torque-motors',
            'email' => 'info@torquemotors.example',
            'city' => 'Mumbai',
            'status' => 'trial',
            'owner_mode' => 'new',
            'new_owner_name' => 'Amit Sharma',
            'new_owner_email' => 'amit@torquemotors.example',
            'new_owner_password' => 'SecretPassword123!',
        ];

        $response = $this->post('/admin/showrooms', $payload);

        $response->assertRedirect();

        // Verify new user created
        $owner = User::where('email', 'amit@torquemotors.example')->first();
        $this->assertNotNull($owner);
        $this->assertEquals('Amit Sharma', $owner->name);

        $showroom = Showroom::where('slug', 'torque-motors')->first();
        $this->assertNotNull($showroom);
        $this->assertEquals('trial', $showroom->status);

        // Verify membership
        $this->assertDatabaseHas('showroom_memberships', [
            'showroom_id' => $showroom->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);
    }

    public function test_admin_can_view_and_update_showroom_details(): void
    {
        $this->actingAs($this->admin);

        // View showroom
        $showResponse = $this->get("/admin/showrooms/{$this->showroom->slug}");
        $showResponse->assertOk();
        $showResponse->assertSee('Apex Moto Hub');

        // View edit form
        $editResponse = $this->get("/admin/showrooms/{$this->showroom->slug}/edit");
        $editResponse->assertOk();

        // Update showroom
        $updateResponse = $this->put("/admin/showrooms/{$this->showroom->slug}", [
            'name' => 'Apex Moto Hub Premier',
            'email' => 'updated@apex.example',
            'phone' => '+91 88888 99999',
            'city' => 'Bangalore East',
            'brand_color_primary' => '#b91c1c',
            'brand_color_accent' => '#1e293b',
            'public_website_enabled' => 1,
            'read_only_mode' => 0,
        ]);

        $updateResponse->assertRedirect("/admin/showrooms/{$this->showroom->slug}");

        $this->showroom->refresh();
        $this->assertEquals('Apex Moto Hub Premier', $this->showroom->name);
        $this->assertEquals('updated@apex.example', $this->showroom->email);
        $this->assertEquals('#b91c1c', $this->showroom->brand_color_primary);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'showroom.updated',
            'tenant_id' => $this->showroom->id,
        ]);
    }

    public function test_admin_can_toggle_showroom_status_and_audit_log_is_recorded(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post("/admin/showrooms/{$this->showroom->slug}/status", [
            'status' => 'suspended',
            'reason' => 'Terms of service violation',
        ]);

        $response->assertRedirect("/admin/showrooms/{$this->showroom->slug}");

        $this->showroom->refresh();
        $this->assertEquals('suspended', $this->showroom->status);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'showroom.status_changed',
            'tenant_id' => $this->showroom->id,
            'user_id' => $this->admin->id,
        ]);

        // Re-activate showroom
        $activateResponse = $this->post("/admin/showrooms/{$this->showroom->slug}/status", [
            'status' => 'active',
            'reason' => 'Issue resolved',
        ]);

        $activateResponse->assertRedirect();
        $this->showroom->refresh();
        $this->assertEquals('active', $this->showroom->status);
    }

    public function test_admin_can_add_and_remove_showroom_members(): void
    {
        $this->actingAs($this->admin);

        $staffUser = User::create([
            'name' => 'Ravi Sales Manager',
            'email' => 'ravi@sales.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        // Add member
        $addResponse = $this->post("/admin/showrooms/{$this->showroom->slug}/members", [
            'user_id' => $staffUser->id,
            'role' => 'sales_manager',
        ]);

        $addResponse->assertRedirect("/admin/showrooms/{$this->showroom->slug}");

        $membership = ShowroomMembership::where('showroom_id', $this->showroom->id)
            ->where('user_id', $staffUser->id)
            ->first();

        $this->assertNotNull($membership);
        $this->assertEquals('sales_manager', $membership->role);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'showroom.member_added',
            'tenant_id' => $this->showroom->id,
        ]);

        // Remove member
        $removeResponse = $this->delete("/admin/showrooms/{$this->showroom->slug}/members/{$membership->id}");
        $removeResponse->assertRedirect();

        $this->assertDatabaseMissing('showroom_memberships', [
            'id' => $membership->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'showroom.member_removed',
            'tenant_id' => $this->showroom->id,
        ]);
    }

    public function test_admin_can_view_and_filter_audit_logs(): void
    {
        $this->actingAs($this->admin);

        // Generate an audit log
        AuditLogger::log(
            action: 'test.action',
            auditable: $this->showroom,
            oldValues: null,
            newValues: ['test' => 'data'],
            tenantId: $this->showroom->id,
            user: $this->admin
        );

        $response = $this->get('/admin/audit-logs');
        $response->assertOk();
        $response->assertSee('System Audit Trail');
        $response->assertSee('test.action');

        // Filter by action
        $filterResponse = $this->get('/admin/audit-logs?action=test.action');
        $filterResponse->assertOk();
        $filterResponse->assertSee('test.action');
    }

    public function test_admin_can_view_and_update_platform_settings(): void
    {
        $this->actingAs($this->admin);

        // View settings
        $response = $this->get('/admin/settings');
        $response->assertOk();
        $response->assertSee('Platform Configuration');

        // Update settings
        $updateResponse = $this->post('/admin/settings', [
            'platform_name' => 'MotoShowroom Cloud Pro',
            'support_email' => 'billing@motoshowroom.example',
            'platform_currency' => 'INR',
            'currency_symbol' => '₹',
            'default_trial_days' => 30,
            'allow_custom_domains' => 1,
            'allow_360_viewer' => 1,
            'allow_ai_assistant' => 1,
            'allow_registration' => 1,
        ]);

        $updateResponse->assertRedirect('/admin/settings');

        // Verify values via PlatformSetting helper
        $this->assertEquals('MotoShowroom Cloud Pro', PlatformSetting::get('platform_name'));
        $this->assertEquals('billing@motoshowroom.example', PlatformSetting::get('support_email'));
        $this->assertEquals('30', PlatformSetting::get('default_trial_days'));
        $this->assertEquals('1', PlatformSetting::get('allow_360_viewer'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'platform.settings_updated',
        ]);
    }
}
