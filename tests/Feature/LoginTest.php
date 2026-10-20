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

    public function test_valid_credentials_redirect_to_otp_verification(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'Password123!',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'Password123!',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('login.otp'));
        $this->assertGuest();
        $this->assertTrue(session()->has('auth.otp'));
    }

    public function test_user_can_complete_login_with_default_otp(): void
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
        ])->assertRedirect(route('login.otp'));

        $this->get('/login/otp')
            ->assertOk()
            ->assertSee('123456');

        $verifyResponse = $this->post('/login/otp', [
            'otp' => '123456',
        ]);

        $verifyResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertFalse(session()->has('auth.otp'));
    }

    public function test_invalid_otp_is_rejected(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'Password123!',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'Password123!',
        ]);

        $this->post('/login/otp', [
            'otp' => '999999',
        ])->assertSessionHasErrors('otp');

        $this->assertGuest();
    }
}
