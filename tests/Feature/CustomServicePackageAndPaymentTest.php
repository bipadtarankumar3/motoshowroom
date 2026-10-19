<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\ShowroomService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomServicePackageAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $customer;

    protected Showroom $showroom;

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

        $this->customer = User::create([
            'name' => 'Karan Johar',
            'email' => 'karan@customer.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->showroom = Showroom::create([
            'name' => 'Apex Moto Hub',
            'slug' => 'apex-moto',
            'email' => 'contact@apex.example',
            'phone' => '+91 8023456789',
            'city' => 'Bangalore',
            'state' => 'Karnataka',
            'status' => 'active',
            'public_website_enabled' => true,
            'settings' => [
                'service_bays_count' => 4,
            ],
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroom->id,
            'user_id' => $this->owner->id,
            'role' => 'owner',
            'status' => 'active',
        ]);
    }

    public function test_showroom_owner_can_create_custom_service_package(): void
    {
        $this->actingAs($this->owner);

        $response = $this->post(route('showrooms.manage.services.packages.store', $this->showroom), [
            'name' => 'Monsoon Ceramic Guard & Brake Service',
            'price' => 3499.00,
            'estimated_duration_minutes' => 120,
            'badge' => 'Monsoon Special',
            'is_payment_enabled' => '1',
            'is_active' => '1',
            'description' => 'Complete hydrophobic ceramic coat plus hydraulic brake flush.',
            'features' => "Ceramic Paint Shield\nDOT4 Brake Fluid Flush\nWater-Repellent Chain Lube",
        ]);

        $response->assertRedirect(route('showrooms.manage.services.index', [$this->showroom, 'tab' => 'catalog']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('showroom_services', [
            'tenant_id' => $this->showroom->id,
            'name' => 'Monsoon Ceramic Guard & Brake Service',
            'price' => 3499.00,
            'estimated_duration_minutes' => 120,
            'badge' => 'Monsoon Special',
            'is_payment_enabled' => true,
            'is_active' => true,
        ]);

        $service = ShowroomService::where('name', 'Monsoon Ceramic Guard & Brake Service')->first();
        $this->assertNotNull($service);
        $this->assertCount(3, $service->features);
        $this->assertEquals('Ceramic Paint Shield', $service->features[0]);
    }

    public function test_showroom_owner_can_toggle_payment_requirement_for_service(): void
    {
        $this->actingAs($this->owner);

        $service = $this->showroom->services()->create([
            'name' => 'Express Chain Lube',
            'slug' => 'express-chain-lube',
            'price' => 299.00,
            'estimated_duration_minutes' => 20,
            'is_payment_enabled' => true,
            'is_active' => true,
        ]);

        $response = $this->post(route('showrooms.manage.services.packages.toggle-payment', [$this->showroom, $service]));

        $response->assertRedirect(route('showrooms.manage.services.index', [$this->showroom, 'tab' => 'catalog']));
        $this->assertFalse($service->fresh()->is_payment_enabled);

        // Toggle back to enabled
        $this->post(route('showrooms.manage.services.packages.toggle-payment', [$this->showroom, $service]));
        $this->assertTrue($service->fresh()->is_payment_enabled);
    }

    public function test_showroom_owner_can_update_service_details_and_price(): void
    {
        $this->actingAs($this->owner);

        $service = $this->showroom->services()->create([
            'name' => 'Standard Oil Change',
            'slug' => 'standard-oil-change',
            'price' => 799.00,
            'estimated_duration_minutes' => 45,
            'is_payment_enabled' => false,
            'is_active' => true,
        ]);

        $response = $this->put(route('showrooms.manage.services.packages.update', [$this->showroom, $service]), [
            'name' => 'Premium Synthetic Oil Change',
            'price' => 1299.00,
            'estimated_duration_minutes' => 60,
            'badge' => 'High Performance',
            'is_payment_enabled' => '1',
            'is_active' => '1',
            'description' => 'Upgraded 100% synthetic Ester lubricant.',
            'features' => "Motul 300V 15W50\nOEM Magnet Drain Plug Check",
        ]);

        $response->assertRedirect(route('showrooms.manage.services.index', [$this->showroom, 'tab' => 'catalog']));
        $fresh = $service->fresh();
        $this->assertEquals('Premium Synthetic Oil Change', $fresh->name);
        $this->assertEquals(1299.00, (float) $fresh->price);
        $this->assertTrue($fresh->is_payment_enabled);
    }

    public function test_showroom_owner_can_delete_service_package(): void
    {
        $this->actingAs($this->owner);

        $service = $this->showroom->services()->create([
            'name' => 'Obsolete Inspection',
            'slug' => 'obsolete-inspection',
            'price' => 499.00,
            'estimated_duration_minutes' => 30,
            'is_payment_enabled' => false,
            'is_active' => true,
        ]);

        $response = $this->delete(route('showrooms.manage.services.packages.destroy', [$this->showroom, $service]));

        $response->assertRedirect(route('showrooms.manage.services.index', [$this->showroom, 'tab' => 'catalog']));
        $this->assertSoftDeleted('showroom_services', ['id' => $service->id]);
    }

    public function test_public_booking_page_lists_custom_services(): void
    {
        $service = $this->showroom->services()->create([
            'name' => 'Apex Track Day Inspection',
            'slug' => 'apex-track-day-inspection',
            'price' => 2499.00,
            'estimated_duration_minutes' => 90,
            'badge' => 'Track Ready',
            'is_payment_enabled' => true,
            'is_active' => true,
            'features' => ['Slick Tire Pressure Setup', 'Race Spec Brake Fluid Flush'],
        ]);

        $response = $this->get(route('showrooms.services.create', $this->showroom));

        $response->assertStatus(200);
        $response->assertSee('Apex Track Day Inspection');
        $response->assertSee('Track Ready');
        $response->assertSee('₹2,499');
        $response->assertSee('Online Advance');
        $response->assertSee('Slick Tire Pressure Setup');
        $response->assertSee('Read Full Service Terms');
    }

    public function test_customer_booking_payment_enabled_service_redirects_to_checkout(): void
    {
        $service = $this->showroom->services()->create([
            'name' => 'Desmo Valve Clearance Tuning',
            'slug' => 'desmo-valve-clearance-tuning',
            'price' => 4500.00,
            'estimated_duration_minutes' => 180,
            'is_payment_enabled' => true,
            'is_active' => true,
        ]);

        $bookingDate = now()->addDays(2)->format('Y-m-d');

        $response = $this->post(route('showrooms.services.store', $this->showroom), [
            'customer_name' => 'Siddharth Roy',
            'customer_email' => 'siddharth@example.com',
            'customer_phone' => '+91 9988776655',
            'bike_make_model' => 'Ducati Panigale V2',
            'registration_number' => 'KA-04-DU-9999',
            'odometer_reading' => 8400,
            'showroom_service_id' => $service->id,
            'booking_date' => $bookingDate,
            'time_slot' => '09:00 - 11:00',
            'terms_accepted' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_bookings', [
            'tenant_id' => $this->showroom->id,
            'showroom_service_id' => $service->id,
            'customer_name' => 'Siddharth Roy',
            'registration_number' => 'KA-04-DU-9999',
            'estimated_cost' => 4500.00,
            'payment_required' => true,
            'terms_accepted' => true,
            'payment_status' => PaymentStatus::Pending->value,
        ]);

        $booking = $this->showroom->serviceBookings()->where('registration_number', 'KA-04-DU-9999')->first();
        $response->assertRedirect(route('showrooms.services.checkout', [$this->showroom, $booking]));
    }

    public function test_customer_booking_pay_at_bay_service_redirects_to_tracking(): void
    {
        $service = $this->showroom->services()->create([
            'name' => 'Basic Chain Maintenance',
            'slug' => 'basic-chain-maintenance',
            'price' => 350.00,
            'estimated_duration_minutes' => 25,
            'is_payment_enabled' => false,
            'is_active' => true,
        ]);

        $bookingDate = now()->addDays(2)->format('Y-m-d');

        $response = $this->post(route('showrooms.services.store', $this->showroom), [
            'customer_name' => 'Aditya Kashyap',
            'customer_email' => 'aditya@example.com',
            'customer_phone' => '+91 9123456789',
            'bike_make_model' => 'KTM RC 200',
            'registration_number' => 'MH-12-RC-1212',
            'showroom_service_id' => $service->id,
            'booking_date' => $bookingDate,
            'time_slot' => '11:00 - 13:00',
            'terms_accepted' => '1',
        ]);

        $response->assertRedirect();
        $booking = $this->showroom->serviceBookings()->where('registration_number', 'MH-12-RC-1212')->first();
        $this->assertNotNull($booking);
        $this->assertFalse($booking->payment_required);
        $response->assertRedirect(route('showrooms.services.track', [
            'showroom' => $this->showroom,
            'booking' => $booking->booking_number,
        ]));
    }
}
