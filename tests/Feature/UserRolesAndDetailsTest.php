<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolesAndDetailsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a public user can self-register and lands on their personal details page.
     */
    public function test_user_can_register_and_is_redirected_to_their_details_page(): void
    {
        $response = $this->post('/register', [
            'name' => 'Karan Patel',
            'email' => 'karan@example.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'phone' => '+91 9123456789',
            'city' => 'Pune',
        ]);

        $user = User::where('email', 'karan@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('user', $user->role);
        $this->assertSame('active', $user->status);
        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('users.show', $user));

        // Follow redirect to their details page
        $detailsResponse = $this->get(route('users.show', $user));
        $detailsResponse->assertStatus(200);
        $detailsResponse->assertSee('Karan Patel');
        $detailsResponse->assertSee('karan@example.com');
        $detailsResponse->assertSee('Pune');
        $detailsResponse->assertSeeText('User');
    }

    /**
     * Test that a user can edit their own details page and cannot escalate privileges to admin.
     */
    public function test_user_can_edit_own_details_and_cannot_escalate_to_admin(): void
    {
        $user = User::create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->actingAs($user);

        // Attempt to update details AND smuggle role=admin
        $response = $this->put(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '+91 9998887776',
            'city' => 'Hyderabad',
            'bio' => 'Passionate about custom motorcycle builds.',
            'role' => 'admin', // Malicious attempt to self-promote
        ]);

        $response->assertRedirect(route('users.show', $user));

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertSame('Hyderabad', $user->city);
        $this->assertSame('Passionate about custom motorcycle builds.', $user->bio);
        // Crucial security verification: role must remain 'user'
        $this->assertSame('user', $user->role);
    }

    /**
     * Test that a regular user cannot access another user's private details page.
     */
    public function test_user_cannot_view_another_users_details_page(): void
    {
        $userA = User::create([
            'name' => 'User A',
            'email' => 'usera@example.com',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $userB = User::create([
            'name' => 'User B',
            'email' => 'userb@example.com',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->actingAs($userA);

        $response = $this->get(route('users.show', $userB));
        $response->assertStatus(403);
    }

    /**
     * Test that an admin can view any user's details page.
     */
    public function test_admin_can_view_any_users_details_page(): void
    {
        $admin = User::create([
            'name' => 'Platform Admin',
            'email' => 'admin@example.com',
            'password' => 'Password123!',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $regularUser = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('users.show', $regularUser));
        $response->assertStatus(200);
        $response->assertSee('Customer User');
        $response->assertSee('customer@example.com');
    }

    /**
     * Test that an admin can create a new user with chosen role and view their details page.
     */
    public function test_admin_can_create_new_user(): void
    {
        $admin = User::create([
            'name' => 'Platform Admin',
            'email' => 'admin@example.com',
            'password' => 'Password123!',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => 'Password123!',
            'role' => 'admin',
            'status' => 'active',
            'phone' => '+91 9911223344',
            'city' => 'Delhi',
            'bio' => 'New regional branch manager.',
        ]);

        $createdUser = User::where('email', 'staff@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertSame('admin', $createdUser->role);
        $this->assertSame('Delhi', $createdUser->city);

        $response->assertRedirect(route('users.show', $createdUser));
    }

    /**
     * Test that non-admin users and guests are denied from admin routes.
     */
    public function test_non_admin_cannot_access_admin_user_routes(): void
    {
        $regularUser = User::create([
            'name' => 'Normal User',
            'email' => 'normal@example.com',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        // Guest access
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));

        // Regular authenticated user
        $this->actingAs($regularUser);
        $this->get(route('admin.users.index'))->assertStatus(403);
        $this->get(route('admin.users.create'))->assertStatus(403);
    }

    /**
     * Test user login and logout lifecycle.
     */
    public function test_user_login_and_logout(): void
    {
        $user = User::create([
            'name' => 'Login Tester',
            'email' => 'login@example.com',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'login@example.com',
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('users.show', $user));

        $logoutResponse = $this->post(route('logout'));
        $this->assertGuest();
        $logoutResponse->assertRedirect('/');
    }
}
