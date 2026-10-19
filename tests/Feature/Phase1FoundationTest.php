<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\User;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Phase1FoundationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the SaaS landing page renders with branding and status 200.
     */
    public function test_welcome_page_renders_with_motoshowroom_branding(): void
    {
        $response = $this->get('/landing');

        $response->assertStatus(200);
        $response->assertSeeText('MOTOSHOWROOM');
        $response->assertSee('Multi-Tenant Architecture');
        $response->assertSee('Razorpay');
    }

    /**
     * Test that guests visiting the root URL see the sign-in page with the showroom carousel.
     */
    public function test_root_url_shows_login_page_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('form-login', false);
        $response->assertSee('Welcome back');
        $response->assertSee('Live Showroom Tour');
    }

    /**
     * Test that authenticated users visiting the root URL are redirected to their console.
     */
    public function test_root_url_redirects_authenticated_users_to_their_console(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $customer = User::factory()->create(['role' => 'user', 'status' => 'active']);

        $this->actingAs($admin)->get('/')->assertRedirect(route('admin.dashboard'));
        $this->actingAs($customer)->get('/')->assertRedirect(route('users.show', $customer));
    }

    /**
     * Test that the database connection is live and migrations are executed.
     */
    public function test_database_connection_and_migrations_table(): void
    {
        $migrationsCount = DB::table('migrations')->count();

        $this->assertGreaterThanOrEqual(3, $migrationsCount);
    }

    /**
     * Test that TenantContext is registered as a singleton and can manage state.
     */
    public function test_tenant_context_service_singleton(): void
    {
        $context1 = app(TenantContext::class);
        $context2 = app(TenantContext::class);

        $this->assertSame($context1, $context2);
        $this->assertFalse($context1->hasTenant());
        $this->assertNull($context1->getTenantId());

        $context1->setBypassScope(true);
        $this->assertTrue($context1->isBypassingScope());
        $context1->setBypassScope(false);
        $this->assertFalse($context1->isBypassingScope());
    }

    /**
     * Test that domain enums have proper keys and labels.
     */
    public function test_domain_enums_configuration(): void
    {
        $this->assertSame('super_admin', UserRole::SuperAdmin->value);
        $this->assertSame('showroom_owner', UserRole::ShowroomOwner->value);
        $this->assertTrue(UserRole::ShowroomOwner->isShowroomStaff());
        $this->assertFalse(UserRole::Customer->isShowroomStaff());

        $this->assertTrue(SubscriptionStatus::Active->grantsAccess());
        $this->assertTrue(SubscriptionStatus::Trial->grantsAccess());
        $this->assertFalse(SubscriptionStatus::Expired->grantsAccess());

        $this->assertTrue(PaymentStatus::Captured->isSuccess());
        $this->assertFalse(PaymentStatus::Pending->isSuccess());
    }

    /**
     * Test that custom config showroom.php is loaded properly.
     */
    public function test_showroom_config_loaded(): void
    {
        $this->assertSame('INR', config('showroom.currency.default'));
        $this->assertTrue(config('showroom.tenancy.subdomain_enabled'));
        $this->assertSame('showrooms', config('showroom.tenancy.path_prefix'));
    }
}
