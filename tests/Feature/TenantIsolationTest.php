<?php

namespace Tests\Feature;

use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $ownerA;

    protected User $ownerB;

    protected Showroom $showroomA;

    protected Showroom $showroomB;

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

        $this->ownerA = User::create([
            'name' => 'Rajesh Owner A',
            'email' => 'rajesh@owner-a.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->ownerB = User::create([
            'name' => 'Vikram Owner B',
            'email' => 'vikram@owner-b.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->showroomA = Showroom::create([
            'name' => 'Apex Moto Hub',
            'slug' => 'apex-moto',
            'email' => 'contact@apex.example',
            'phone' => '+91 8023456789',
            'brand_color_primary' => '#DC2626',
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        $this->showroomB = Showroom::create([
            'name' => 'Velocity Superbikes',
            'slug' => 'velocity-superbikes',
            'email' => 'sales@velocity.example',
            'phone' => '+91 1123456789',
            'brand_color_primary' => '#2563EB',
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
    }

    /**
     * Test public showroom website resolution by slug.
     */
    public function test_public_can_view_showroom_website_by_slug(): void
    {
        $responseA = $this->get('/showrooms/apex-moto');
        $responseA->assertStatus(200);
        $responseA->assertSee('Apex Moto Hub');

        $responseB = $this->get('/showrooms/velocity-superbikes');
        $responseB->assertStatus(200);
        $responseB->assertSee('Velocity Superbikes');
    }

    /**
     * Test showroom owner can access their own showroom back-office.
     */
    public function test_owner_a_can_access_showroom_a_backoffice(): void
    {
        $this->actingAs($this->ownerA);

        $response = $this->get('/showrooms/apex-moto/manage');
        $response->assertStatus(200);
        $response->assertSee('Apex Moto Hub');
        $response->assertSee('Rajesh Owner A');
    }

    /**
     * CRITICAL SECURITY TEST: Owner A is denied access to Showroom B (Cross-tenant access).
     */
    public function test_owner_a_cannot_access_showroom_b_backoffice(): void
    {
        $this->actingAs($this->ownerA);

        $response = $this->get('/showrooms/velocity-superbikes/manage');
        $response->assertStatus(403);
    }

    /**
     * CRITICAL SECURITY TEST: Owner B is denied access to Showroom A (Cross-tenant access).
     */
    public function test_owner_b_cannot_access_showroom_a_backoffice(): void
    {
        $this->actingAs($this->ownerB);

        $response = $this->get('/showrooms/apex-moto/manage');
        $response->assertStatus(403);
    }

    /**
     * Test Super Admin has universal platform oversight across both showrooms.
     */
    public function test_super_admin_can_access_both_showrooms(): void
    {
        $this->actingAs($this->admin);

        $this->get('/showrooms/apex-moto/manage')->assertStatus(200);
        $this->get('/showrooms/velocity-superbikes/manage')->assertStatus(200);
    }

    /**
     * Test unauthenticated guest is redirected to login when accessing back-office.
     */
    public function test_guest_redirected_to_login_when_accessing_backoffice(): void
    {
        $response = $this->get('/showrooms/apex-moto/manage');
        $response->assertRedirect(route('login'));
    }

    /**
     * Test invalid showroom slug returns 404.
     */
    public function test_invalid_showroom_slug_returns_404(): void
    {
        $response = $this->get('/showrooms/non-existent-showroom-12345');
        $response->assertStatus(404);
    }
}
