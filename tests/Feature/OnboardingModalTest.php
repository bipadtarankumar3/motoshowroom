<?php

namespace Tests\Feature;

use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingModalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::firstOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter Dealership',
                'description' => 'Starter dealership subscription plan',
                'monthly_price' => 999.00,
                'annual_price' => 9990.00,
                'currency' => 'INR',
                'trial_days' => 14,
                'is_active' => true,
            ]
        );
    }

    /**
     * Test new user without onboarding_completed_at sees the onboarding modal on dashboard.
     */
    public function test_new_user_sees_onboarding_modal_on_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'onboarding_completed_at' => null,
        ]);

        $showroom = Showroom::factory()->create(['status' => 'trial']);
        ShowroomMembership::create([
            'showroom_id' => $showroom->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        $plan = SubscriptionPlan::first();
        app(SubscriptionService::class)->startTrial($showroom, $plan);

        $response = $this->actingAs($user)->get(route('showrooms.manage', $showroom));

        $response->assertOk();
        $response->assertSeeText('Set Up Your Profile & Showroom');
        $response->assertSee('form-onboarding-modal');
        $response->assertSeeText('Trial Expiry');
        $response->assertSee(route('showrooms.billing.index', $showroom));
    }

    /**
     * Test submitting mandatory fields completes onboarding and updates showroom & user.
     */
    public function test_submitting_onboarding_form_updates_details_and_clears_flag(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'onboarding_completed_at' => null,
        ]);

        $showroom = Showroom::factory()->create(['status' => 'trial']);
        ShowroomMembership::create([
            'showroom_id' => $showroom->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        $plan = SubscriptionPlan::first();
        app(SubscriptionService::class)->startTrial($showroom, $plan);

        $postData = [
            'user_name' => 'Aditya Sharma',
            'user_phone' => '+91 98888 77777',
            'user_city' => 'Pune',
            'user_address' => '102 MG Road',
            'user_bio' => 'Passionate motorcycle dealer',
            'showroom_name' => 'Speedway Wheels Pune',
            'showroom_email' => 'contact@speedwaywheels.example',
            'showroom_phone' => '+91 20 6666 5555',
            'showroom_city' => 'Pune',
            'showroom_state' => 'Maharashtra',
            'showroom_address_line1' => 'Plot 45, FC Road',
            'showroom_postal_code' => '411005',
            'brand_color_primary' => '#FF0000',
            'tagline' => 'Best Superbikes in Pune',
            'about_text' => 'Official superbike dealership in Maharashtra',
        ];

        $response = $this->actingAs($user)->post(route('showrooms.onboarding.save', $showroom), $postData);

        $response->assertRedirect(route('showrooms.manage', $showroom));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->onboarding_completed_at);
        $this->assertSame('Aditya Sharma', $user->name);
        $this->assertSame('+91 98888 77777', $user->phone);
        $this->assertSame('Pune', $user->city);

        $showroom->refresh();
        $this->assertSame('Speedway Wheels Pune', $showroom->name);
        $this->assertSame('contact@speedwaywheels.example', $showroom->email);
        $this->assertSame('Plot 45, FC Road', $showroom->address_line1);
        $this->assertSame('#FF0000', $showroom->brand_color_primary);
    }

    /**
     * Test mandatory fields validation failure.
     */
    public function test_mandatory_fields_are_enforced_in_onboarding(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'onboarding_completed_at' => null,
        ]);

        $showroom = Showroom::factory()->create(['status' => 'trial']);
        ShowroomMembership::create([
            'showroom_id' => $showroom->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        $plan = SubscriptionPlan::first();
        app(SubscriptionService::class)->startTrial($showroom, $plan);

        // Missing required fields
        $response = $this->actingAs($user)->post(route('showrooms.onboarding.save', $showroom), [
            'user_name' => '',
            'showroom_name' => '',
        ]);

        $response->assertSessionHasErrors(['user_name', 'user_phone', 'user_city', 'showroom_name', 'showroom_email', 'showroom_phone', 'showroom_city', 'showroom_state', 'showroom_address_line1', 'showroom_postal_code']);
    }

    /**
     * Test trial users have unrestricted access to all features during evaluation trial.
     */
    public function test_trial_users_can_access_all_features_during_trial(): void
    {
        $showroom = Showroom::factory()->create(['status' => 'trial']);
        $plan = SubscriptionPlan::first();
        $subscription = app(SubscriptionService::class)->startTrial($showroom, $plan);

        $this->assertTrue($subscription->isTrial());
        $this->assertTrue($subscription->canAccessFeature('has_360_viewer'));
        $this->assertTrue($subscription->canAccessFeature('has_ai_assistant'));
        $this->assertTrue($subscription->canAccessFeature('has_custom_domain'));
        $this->assertFalse($subscription->hasReachedLimit('max_bike_listings', 999999));
    }
}
