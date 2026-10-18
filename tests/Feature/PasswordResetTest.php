<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test requesting a password reset link generates a token in password_reset_tokens table.
     */
    public function test_user_can_request_password_reset_token(): void
    {
        $user = User::create([
            'name' => 'Reset Tester',
            'email' => 'reset@example.com',
            'password' => 'OldPassword123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'reset@example.com',
        ]);

        $response->assertSessionHas('status');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'reset@example.com',
        ]);
    }

    /**
     * Test user can reset password using a valid token.
     */
    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::create([
            'name' => 'Reset Tester',
            'email' => 'reset@example.com',
            'password' => 'OldPassword123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $plainToken = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email' => 'reset@example.com',
            'token' => Hash::make($plainToken),
            'created_at' => now(),
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $plainToken,
            'email' => 'reset@example.com',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Token should be consumed
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'reset@example.com',
        ]);

        // Can login with new password
        $this->assertTrue(Auth::attempt([
            'email' => 'reset@example.com',
            'password' => 'NewSecurePassword123!',
        ]));
    }

    /**
     * Test invalid token is rejected.
     */
    public function test_invalid_reset_token_is_rejected(): void
    {
        User::create([
            'name' => 'Reset Tester',
            'email' => 'reset@example.com',
            'password' => 'OldPassword123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token-12345',
            'email' => 'reset@example.com',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
