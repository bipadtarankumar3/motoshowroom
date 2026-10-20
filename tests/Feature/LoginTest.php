<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_form_remember_checkbox_submits_a_boolean_value(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('name="remember"', false)
            ->assertSee('value="1"', false);
    }

    public function test_admin_can_login_with_remember_me_checked(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'Password123!',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'Password123!',
            'remember' => '1',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }
}
