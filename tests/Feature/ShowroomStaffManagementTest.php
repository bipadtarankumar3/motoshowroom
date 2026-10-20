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

class ShowroomStaffManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected Showroom $showroom;

    protected SubscriptionPlan $plan;

    protected Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'name' => 'Rajesh Sharma',
            'email' => 'rajesh@apex.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->plan = SubscriptionPlan::create([
            'name' => 'Starter Dealership',
            'slug' => 'starter-dealership',
            'description' => 'Entry plan for small dealerships',
            'monthly_price' => 999.00,
            'annual_price' => 9990.00,
            'currency' => 'INR',
            'trial_days' => 14,
            'max_staff_accounts' => 2, // Limit of 2 staff accounts (owner + 1 staff)
            'max_bike_listings' => 15,
            'max_monthly_bookings' => 50,
            'media_storage_mb' => 1024,
            'is_active' => true,
        ]);

        $this->showroom = Showroom::create([
            'name' => 'Apex Moto Hub',
            'slug' => 'apex-moto-hub',
            'owner_id' => $this->owner->id,
            'email' => 'contact@apex.example',
            'phone' => '+91 98765 43210',
            'city' => 'Bangalore',
            'status' => 'active',
            'max_staff_accounts' => 2,
        ]);

        $this->subscription = Subscription::create([
            'showroom_id' => $this->showroom->id,
            'plan_id' => $this->plan->id,
            'status' => SubscriptionStatus::Active,
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
            'billing_interval' => 'monthly',
        ]);

        // Owner membership (1 account used)
        ShowroomMembership::create([
            'showroom_id' => $this->showroom->id,
            'user_id' => $this->owner->id,
            'role' => 'owner',
            'status' => 'active',
            'availability_status' => 'available',
            'permissions' => ['all'],
        ]);
    }

    public function test_showroom_owner_can_view_staff_roster_page(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('showrooms.manage.staff.index', $this->showroom));

        $response->assertStatus(200);
        $response->assertSee('Staff & Personnel Roster');
        $response->assertSee('Rajesh Sharma');
        $response->assertSee('Available (On Duty)');
        $response->assertSee('1 of 2'); // 1 of 2 accounts used
    }

    public function test_showroom_owner_can_add_staff_member_within_plan_limit(): void
    {
        $response = $this->actingAs($this->owner)
            ->post(route('showrooms.manage.staff.store', $this->showroom), [
                'name' => 'Vikram Verma',
                'email' => 'vikram@apex.example',
                'phone' => '+91 98765 11111',
                'role' => 'sales_executive',
                'department' => 'Sales & Customer Relations',
                'designation' => 'Senior Test Ride Specialist',
                'shift' => 'General (9:30 AM - 6:30 PM)',
                'availability_status' => 'available',
                'notes' => 'Handling morning customer test rides',
            ]);

        $response->assertRedirect(route('showrooms.manage.staff.index', $this->showroom));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'vikram@apex.example',
            'name' => 'Vikram Verma',
        ]);

        $this->assertDatabaseHas('showroom_memberships', [
            'showroom_id' => $this->showroom->id,
            'role' => 'sales_executive',
            'department' => 'Sales & Customer Relations',
            'designation' => 'Senior Test Ride Specialist',
            'availability_status' => 'available',
        ]);
    }

    public function test_staff_creation_is_blocked_when_plan_quota_is_reached(): void
    {
        // Add 1 staff to hit limit (owner 1 + staff 1 = 2)
        $staffUser = User::create([
            'name' => 'Staff Member One',
            'email' => 'staff1@apex.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroom->id,
            'user_id' => $staffUser->id,
            'role' => 'sales_executive',
            'status' => 'active',
            'availability_status' => 'available',
        ]);

        // Showroom now has 2 members, hitting the limit of 2
        $response = $this->actingAs($this->owner)
            ->post(route('showrooms.manage.staff.store', $this->showroom), [
                'name' => 'Exceeding Staff',
                'email' => 'exceeding@apex.example',
                'role' => 'service_advisor',
                'availability_status' => 'available',
            ]);

        $response->assertRedirect(route('showrooms.manage.staff.index', $this->showroom));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Plan Quota Reached', session('error'));

        $this->assertDatabaseMissing('users', [
            'email' => 'exceeding@apex.example',
        ]);
    }

    public function test_staff_attendance_and_busy_status_can_be_updated(): void
    {
        $staffUser = User::create([
            'name' => 'Amit Kumar',
            'email' => 'amit@apex.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $membership = ShowroomMembership::create([
            'showroom_id' => $this->showroom->id,
            'user_id' => $staffUser->id,
            'role' => 'sales_executive',
            'status' => 'active',
            'availability_status' => 'available',
        ]);

        // Update status to 'busy' with customer
        $response = $this->actingAs($this->owner)
            ->patch(route('showrooms.manage.staff.status', [$this->showroom, $membership]), [
                'availability_status' => 'busy',
                'notes' => 'Attending customer delivery in bay 1',
            ]);

        $response->assertRedirect(route('showrooms.manage.staff.index', $this->showroom));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('showroom_memberships', [
            'id' => $membership->id,
            'availability_status' => 'busy',
            'notes' => 'Attending customer delivery in bay 1',
        ]);

        // Update status to 'not_attending' (on leave)
        $response2 = $this->actingAs($this->owner)
            ->patch(route('showrooms.manage.staff.status', [$this->showroom, $membership]), [
                'availability_status' => 'not_attending',
                'notes' => 'Medical leave today',
            ]);

        $response2->assertRedirect(route('showrooms.manage.staff.index', $this->showroom));

        $this->assertDatabaseHas('showroom_memberships', [
            'id' => $membership->id,
            'availability_status' => 'not_attending',
            'notes' => 'Medical leave today',
        ]);
    }

    public function test_showroom_owner_cannot_be_deleted(): void
    {
        $ownerMembership = $this->showroom->memberships()->where('role', 'owner')->firstOrFail();

        $response = $this->actingAs($this->owner)
            ->delete(route('showrooms.manage.staff.destroy', [$this->showroom, $ownerMembership]));

        $response->assertRedirect(route('showrooms.manage.staff.index', $this->showroom));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('showroom_memberships', [
            'id' => $ownerMembership->id,
        ]);
    }

    public function test_regular_staff_member_can_be_removed(): void
    {
        $staffUser = User::create([
            'name' => 'Temporary Staff',
            'email' => 'temp@apex.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $membership = ShowroomMembership::create([
            'showroom_id' => $this->showroom->id,
            'user_id' => $staffUser->id,
            'role' => 'staff',
            'status' => 'active',
            'availability_status' => 'available',
        ]);

        $response = $this->actingAs($this->owner)
            ->delete(route('showrooms.manage.staff.destroy', [$this->showroom, $membership]));

        $response->assertRedirect(route('showrooms.manage.staff.index', $this->showroom));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('showroom_memberships', [
            'id' => $membership->id,
        ]);
    }
}
