<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleOAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test redirect to Google returns redirect response.
     */
    public function test_google_redirect(): void
    {
        $response = $this->get(route('auth.google'));
        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    /**
     * Test Google OAuth callback creates a new user with role 'user' and links social account.
     */
    public function test_google_callback_registers_new_user_with_user_role(): void
    {
        $abstractUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $abstractUser->shouldReceive('getId')->andReturn('google_unique_id_999');
        $abstractUser->shouldReceive('getName')->andReturn('Aarav Google');
        $abstractUser->shouldReceive('getEmail')->andReturn('aarav.google@example.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/photo.jpg');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        SubscriptionPlan::firstOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter Dealership',
                'description' => 'Starter plan',
                'monthly_price' => 999.00,
                'annual_price' => 9990.00,
                'currency' => 'INR',
                'trial_days' => 14,
                'is_active' => true,
            ]
        );

        $response = $this->get(route('auth.google.callback'));

        $user = User::where('email', 'aarav.google@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Aarav Google', $user->name);
        $this->assertSame('user', $user->role); // Ensure security rule: never admin
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google_unique_id_999',
        ]);

        $showroom = $user->showrooms()->first();
        $this->assertNotNull($showroom);
        $this->assertSame('Aarav Google Motors', $showroom->name);
        $this->assertNotNull($showroom->subscription);
        $this->assertTrue($showroom->subscription->isTrial());

        $response->assertRedirect(route('showrooms.manage', $showroom));
    }
}
