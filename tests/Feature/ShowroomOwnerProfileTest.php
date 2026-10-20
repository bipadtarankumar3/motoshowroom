<?php

namespace Tests\Feature;

use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowroomOwnerProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a showroom owner can access their dedicated profile page via showroom route and user route.
     */
    public function test_showroom_owner_can_view_profile_page(): void
    {
        $owner = User::factory()->create([
            'name' => 'Rajesh Sharma',
            'email' => 'rajesh@example.com',
            'phone' => '+91 9876543210',
            'city' => 'Bangalore',
            'role' => 'user',
            'status' => 'active',
        ]);

        $showroom = Showroom::factory()->create([
            'name' => 'Apex Moto Hub',
            'slug' => 'apex-moto-hub',
        ]);

        ShowroomMembership::create([
            'showroom_id' => $showroom->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        $this->actingAs($owner);

        // Access via showroom manage profile route
        $response = $this->get(route('showrooms.manage.profile', $showroom));
        $response->assertStatus(200);
        $response->assertSee('Rajesh Sharma');
        $response->assertSee('rajesh@example.com');
        $response->assertSee('Showroom Owner');
        $response->assertSee('Apex Moto Hub');

        // Access via /users/{user} route renders the dedicated showroom profile
        $userRouteResponse = $this->get(route('users.show', $owner));
        $userRouteResponse->assertStatus(200);
        $userRouteResponse->assertSee('Rajesh Sharma');
        $userRouteResponse->assertSee('Apex Moto Hub');
    }

    /**
     * Test that a showroom owner can view the edit form and update their profile details.
     */
    public function test_showroom_owner_can_edit_and_update_profile_details(): void
    {
        $owner = User::factory()->create([
            'name' => 'Original Owner',
            'email' => 'original.owner@example.com',
            'role' => 'user',
            'status' => 'active',
        ]);

        $showroom = Showroom::factory()->create([
            'name' => 'Speedway Bikes',
            'slug' => 'speedway-bikes',
        ]);

        ShowroomMembership::create([
            'showroom_id' => $showroom->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        $this->actingAs($owner);

        // View edit form
        $editResponse = $this->get(route('showrooms.manage.profile.edit', $showroom));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Showroom Owner Profile');

        // Submit updates
        $updateResponse = $this->put(route('showrooms.manage.profile.update', $showroom), [
            'name' => 'Updated Owner Name',
            'email' => 'updated.owner@example.com',
            'phone' => '+91 9988776655',
            'city' => 'Pune',
            'address' => '456 Ring Road, Hinjewadi',
            'bio' => 'Founder and managing director of Speedway Bikes.',
        ]);

        $updateResponse->assertRedirect(route('showrooms.manage.profile', $showroom));

        $owner->refresh();
        $this->assertSame('Updated Owner Name', $owner->name);
        $this->assertSame('updated.owner@example.com', $owner->email);
        $this->assertSame('+91 9988776655', $owner->phone);
        $this->assertSame('Pune', $owner->city);
        $this->assertSame('456 Ring Road, Hinjewadi', $owner->address);
        $this->assertSame('Founder and managing director of Speedway Bikes.', $owner->bio);
    }
}
