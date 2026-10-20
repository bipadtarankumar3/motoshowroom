<?php

namespace Tests\Feature;

use App\Enums\BikeCategory;
use App\Enums\BikeStatus;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\ServiceType;
use App\Models\Bike;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceBookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $ownerA;

    protected User $technicianA;

    protected User $ownerB;

    protected User $customer;

    protected Showroom $showroomA;

    protected Showroom $showroomB;

    protected Bike $bikeA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ownerA = User::create([
            'name' => 'Rajesh Sharma',
            'email' => 'rajesh@apex.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->technicianA = User::create([
            'name' => 'Suresh Mechanic',
            'email' => 'suresh@apex.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->ownerB = User::create([
            'name' => 'Vikram Malhotra',
            'email' => 'vikram@velocity.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->customer = User::create([
            'name' => 'Amitabh Varma',
            'email' => 'amitabh@customer.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->showroomA = Showroom::create([
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

        $this->showroomB = Showroom::create([
            'name' => 'Velocity Superbikes',
            'slug' => 'velocity-superbikes',
            'email' => 'contact@velocity.example',
            'phone' => '+91 1123456789',
            'city' => 'New Delhi',
            'state' => 'Delhi',
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroomA->id,
            'user_id' => $this->technicianA->id,
            'role' => 'staff',
            'status' => 'active',
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroomB->id,
            'user_id' => $this->ownerB->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        $this->bikeA = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Bajaj Pulsar NS400Z',
            'slug' => 'pulsar-ns400z',
            'sku' => 'APEX-PUL-400',
            'brand' => 'Bajaj',
            'model_year' => 2026,
            'category' => BikeCategory::Sports->value,
            'status' => BikeStatus::InStock->value,
            'price' => 185000.00,
            'is_featured' => true,
            'is_published' => true,
        ]);
    }

    public function test_public_visitor_can_view_service_booking_form_with_packages(): void
    {
        $response = $this->get(route('showrooms.services.create', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('Schedule Motorcycle Service');
        $response->assertSee('Periodic General Maintenance');
        $response->assertSee('Engine Oil &amp; Filter Service', false);
        $response->assertSee('Active Service Bays');
    }

    public function test_public_visitor_can_retrieve_bay_slot_availability_via_api(): void
    {
        $targetDate = now()->addDays(2)->format('Y-m-d');

        $response = $this->get(route('showrooms.services.slots', [
            'showroom' => $this->showroomA,
            'date' => $targetDate,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'date',
            'capacity',
            'slots' => [
                '09:00 - 11:00' => ['slot', 'occupied', 'capacity', 'available', 'remaining'],
            ],
        ]);
    }

    public function test_customer_can_book_service_appointment_successfully(): void
    {
        $bookingDate = now()->addDays(2)->format('Y-m-d');

        $response = $this->post(route('showrooms.services.store', $this->showroomA), [
            'customer_name' => 'Ravi Shankar',
            'customer_email' => 'ravi@example.com',
            'customer_phone' => '+91 9876500000',
            'bike_make_model' => 'KTM Duke 390',
            'registration_number' => 'KA-01-MJ-5500',
            'odometer_reading' => 12500,
            'service_type' => ServiceType::GeneralService->value,
            'booking_date' => $bookingDate,
            'time_slot' => '09:00 - 11:00',
            'customer_notes' => 'Please adjust chain slack and inspect front brake pads.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('service_bookings', [
            'tenant_id' => $this->showroomA->id,
            'customer_name' => 'Ravi Shankar',
            'customer_email' => 'ravi@example.com',
            'registration_number' => 'KA-01-MJ-5500',
            'bike_make_model' => 'KTM Duke 390',
            'service_type' => ServiceType::GeneralService->value,
            'time_slot' => '09:00 - 11:00',
            'status' => BookingStatus::Pending->value,
        ]);

        $booking = ServiceBooking::where('registration_number', 'KA-01-MJ-5500')->first();
        $this->assertNotNull($booking);
        $this->assertStringStartsWith('SRV-', $booking->booking_number);
    }

    public function test_authenticated_customer_has_user_id_attached_to_booking(): void
    {
        $this->actingAs($this->customer);
        $bookingDate = now()->addDays(3)->format('Y-m-d');

        $this->post(route('showrooms.services.store', $this->showroomA), [
            'customer_name' => 'Amitabh Varma',
            'customer_email' => 'amitabh@customer.example',
            'customer_phone' => '+91 9811122233',
            'bike_make_model' => 'Royal Enfield Interceptor 650',
            'registration_number' => 'DL-04-AB-1234',
            'service_type' => ServiceType::OilLube->value,
            'booking_date' => $bookingDate,
            'time_slot' => '11:00 - 13:00',
        ]);

        $this->assertDatabaseHas('service_bookings', [
            'tenant_id' => $this->showroomA->id,
            'user_id' => $this->customer->id,
            'registration_number' => 'DL-04-AB-1234',
        ]);
    }

    public function test_booking_validates_required_fields(): void
    {
        $response = $this->post(route('showrooms.services.store', $this->showroomA), [
            'customer_name' => '',
            'customer_email' => 'invalid-email',
            'customer_phone' => '',
            'bike_make_model' => '',
            'registration_number' => '',
            'service_type' => '',
            'booking_date' => '',
            'time_slot' => '',
        ]);

        $response->assertSessionHasErrors([
            'customer_name',
            'customer_email',
            'customer_phone',
            'bike_make_model',
            'registration_number',
            'service_type',
            'booking_date',
            'time_slot',
        ]);
    }

    public function test_booking_validates_package_type_and_time_slot(): void
    {
        $response = $this->post(route('showrooms.services.store', $this->showroomA), [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+91 9999988888',
            'bike_make_model' => 'Honda CB300R',
            'registration_number' => 'MH-02-CD-5678',
            'service_type' => 'unsupported_package',
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'time_slot' => '03:00 - 05:00',
        ]);

        $response->assertSessionHasErrors(['service_type', 'time_slot']);
    }

    public function test_booking_rejects_past_date(): void
    {
        $response = $this->post(route('showrooms.services.store', $this->showroomA), [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+91 9999988888',
            'bike_make_model' => 'Honda CB300R',
            'registration_number' => 'MH-02-CD-5678',
            'service_type' => ServiceType::GeneralService->value,
            'booking_date' => now()->subDay()->format('Y-m-d'),
            'time_slot' => '09:00 - 11:00',
        ]);

        $response->assertSessionHasErrors(['booking_date']);
    }

    public function test_bay_slot_concurrency_rejects_booking_when_all_bays_are_occupied(): void
    {
        $targetDate = now()->addDays(4)->format('Y-m-d');
        $slot = '09:00 - 11:00';

        // Showroom A has capacity of 4 service bays. Create 4 active bookings.
        for ($i = 1; $i <= 4; $i++) {
            ServiceBooking::factory()->create([
                'tenant_id' => $this->showroomA->id,
                'booking_date' => $targetDate,
                'time_slot' => $slot,
                'status' => BookingStatus::Confirmed->value,
                'registration_number' => "KA-01-XX-000{$i}",
            ]);
        }

        // 5th customer attempts to book the same slot
        $response = $this->post(route('showrooms.services.store', $this->showroomA), [
            'customer_name' => 'Overbooked Customer',
            'customer_email' => 'overbooked@example.com',
            'customer_phone' => '+91 9123456789',
            'bike_make_model' => 'Yamaha MT-15',
            'registration_number' => 'KA-01-XX-9999',
            'service_type' => ServiceType::GeneralService->value,
            'booking_date' => $targetDate,
            'time_slot' => $slot,
        ]);

        $response->assertSessionHasErrors(['time_slot']);
        $this->assertDatabaseMissing('service_bookings', [
            'registration_number' => 'KA-01-XX-9999',
        ]);
    }

    public function test_bay_slot_concurrency_ignores_cancelled_or_no_show_bookings(): void
    {
        $targetDate = now()->addDays(5)->format('Y-m-d');
        $slot = '14:00 - 16:00';

        // 3 confirmed bookings
        for ($i = 1; $i <= 3; $i++) {
            ServiceBooking::factory()->create([
                'tenant_id' => $this->showroomA->id,
                'booking_date' => $targetDate,
                'time_slot' => $slot,
                'status' => BookingStatus::Confirmed->value,
            ]);
        }

        // 1 cancelled booking (bay freed up)
        ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'booking_date' => $targetDate,
            'time_slot' => $slot,
            'status' => BookingStatus::Cancelled->value,
        ]);

        // 5th customer should be accepted because 1 of 4 was cancelled
        $response = $this->post(route('showrooms.services.store', $this->showroomA), [
            'customer_name' => 'Accepted Customer',
            'customer_email' => 'accepted@example.com',
            'customer_phone' => '+91 9777788888',
            'bike_make_model' => 'Royal Enfield Meteor 350',
            'registration_number' => 'KA-01-AC-1000',
            'service_type' => ServiceType::OilLube->value,
            'booking_date' => $targetDate,
            'time_slot' => $slot,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_bookings', [
            'registration_number' => 'KA-01-AC-1000',
        ]);
    }

    public function test_customer_can_track_service_booking_via_booking_reference(): void
    {
        $booking = ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'booking_number' => 'SRV-20261018-TEST',
            'customer_name' => 'Trackable Customer',
            'bike_make_model' => 'Kawasaki Z900',
            'registration_number' => 'KA-05-ZZ-9999',
            'status' => BookingStatus::InService->value,
        ]);

        $response = $this->get(route('showrooms.services.track', [
            'showroom' => $this->showroomA,
            'booking' => 'SRV-20261018-TEST',
        ]));

        $response->assertStatus(200);
        $response->assertSee('SRV-20261018-TEST');
        $response->assertSee('Kawasaki Z900');
        $response->assertSee('KA-05-ZZ-9999');
        $response->assertSee('In Service / Repair');
    }

    public function test_customer_can_track_service_booking_via_registration_number(): void
    {
        ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'customer_name' => 'Plate Search Customer',
            'bike_make_model' => 'Ducati Monster 821',
            'registration_number' => 'MH-12-AB-7777',
            'status' => BookingStatus::ReadyForCollection->value,
        ]);

        $response = $this->get(route('showrooms.services.track', [
            'showroom' => $this->showroomA,
            'booking' => 'MH-12-AB-7777',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Ducati Monster 821');
        $response->assertSee('Ready for Pickup');
    }

    public function test_customer_service_tracker_shows_not_found_on_invalid_ref(): void
    {
        $response = $this->get(route('showrooms.services.track', [
            'showroom' => $this->showroomA,
            'booking' => 'NON-EXISTENT-REF',
        ]));

        $response->assertStatus(200);
        $response->assertSee('No Service Booking Found');
    }

    public function test_showroom_staff_can_view_workshop_management_index_with_kpi_counts(): void
    {
        // 1 today appointment
        ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'booking_date' => today(),
            'status' => BookingStatus::InService->value,
        ]);

        $this->actingAs($this->ownerA);

        $response = $this->get(route('showrooms.manage.services.index', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('Workshop');
        $response->assertSee('Appointments');
        $response->assertSee('In Service Bays');
        $response->assertSee('Ready for Collection');
        $response->assertSee('Pending Confirmation');
    }

    public function test_showroom_staff_can_filter_bookings_by_status_and_date(): void
    {
        $today = today()->format('Y-m-d');
        $tomorrow = today()->addDay()->format('Y-m-d');

        ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'customer_name' => 'Today Target',
            'booking_date' => $today,
            'status' => BookingStatus::Confirmed->value,
        ]);

        ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'customer_name' => 'Tomorrow Target',
            'booking_date' => $tomorrow,
            'status' => BookingStatus::Pending->value,
        ]);

        $this->actingAs($this->ownerA);

        $response = $this->get(route('showrooms.manage.services.index', [
            'showroom' => $this->showroomA,
            'date' => $today,
            'status' => 'confirmed',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Today Target');
        $response->assertDontSee('Tomorrow Target');
    }

    public function test_showroom_staff_can_search_bookings_by_registration_plate_or_customer(): void
    {
        ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'customer_name' => 'Girish UniqueSpecialCustomer',
            'registration_number' => 'KA-09-QQ-1122',
        ]);

        ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'customer_name' => 'Other Regular',
            'registration_number' => 'KA-01-XX-4455',
        ]);

        $this->actingAs($this->ownerA);

        $response = $this->get(route('showrooms.manage.services.index', [
            'showroom' => $this->showroomA,
            'search' => 'UniqueSpecialCustomer',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Girish UniqueSpecialCustomer');
        $response->assertDontSee('Other Regular');
    }

    public function test_showroom_staff_can_view_individual_job_card(): void
    {
        $booking = ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'customer_name' => 'Job Card Customer',
            'bike_make_model' => 'Yamaha YZF-R3',
            'registration_number' => 'KA-05-R3-3000',
        ]);

        $this->actingAs($this->ownerA);

        $response = $this->get(route('showrooms.manage.services.show', [
            'showroom' => $this->showroomA,
            'booking' => $booking,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Job Card Customer');
        $response->assertSee('Yamaha YZF-R3');
        $response->assertSee('KA-05-R3-3000');
        $response->assertSee('Update Job Card');
    }

    public function test_showroom_staff_can_update_booking_status_bay_and_assign_technician(): void
    {
        $booking = ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'status' => BookingStatus::Pending->value,
            'bay_number' => null,
            'assigned_to_user_id' => null,
        ]);

        $this->actingAs($this->ownerA);

        $response = $this->put(route('showrooms.manage.services.update', [
            'showroom' => $this->showroomA,
            'booking' => $booking,
        ]), [
            'status' => BookingStatus::InService->value,
            'bay_number' => 'Bay 3 - Hydraulic Lift',
            'assigned_to_user_id' => $this->technicianA->id,
            'final_cost' => 2499.00,
            'payment_status' => PaymentStatus::Captured->value,
            'technician_notes' => 'Synthetic 10W-50 oil filled, new spark plugs installed.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('service_bookings', [
            'id' => $booking->id,
            'tenant_id' => $this->showroomA->id,
            'status' => BookingStatus::InService->value,
            'bay_number' => 'Bay 3 - Hydraulic Lift',
            'assigned_to_user_id' => $this->technicianA->id,
            'final_cost' => 2499.00,
            'payment_status' => PaymentStatus::Captured->value,
        ]);
    }

    public function test_lifecycle_milestone_progression_sets_respective_timestamps(): void
    {
        $booking = ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'status' => BookingStatus::Pending->value,
            'confirmed_at' => null,
            'checked_in_at' => null,
            'in_service_at' => null,
            'ready_at' => null,
            'completed_at' => null,
        ]);

        $this->actingAs($this->ownerA);

        // 1. Confirm
        $this->put(route('showrooms.manage.services.update', [$this->showroomA, $booking]), [
            'status' => BookingStatus::Confirmed->value,
        ]);
        $booking->refresh();
        $this->assertNotNull($booking->confirmed_at);

        // 2. Check In
        $this->put(route('showrooms.manage.services.update', [$this->showroomA, $booking]), [
            'status' => BookingStatus::CheckedIn->value,
        ]);
        $booking->refresh();
        $this->assertNotNull($booking->checked_in_at);

        // 3. In Service
        $this->put(route('showrooms.manage.services.update', [$this->showroomA, $booking]), [
            'status' => BookingStatus::InService->value,
        ]);
        $booking->refresh();
        $this->assertNotNull($booking->in_service_at);

        // 4. Ready
        $this->put(route('showrooms.manage.services.update', [$this->showroomA, $booking]), [
            'status' => BookingStatus::ReadyForCollection->value,
        ]);
        $booking->refresh();
        $this->assertNotNull($booking->ready_at);

        // 5. Complete
        $this->put(route('showrooms.manage.services.update', [$this->showroomA, $booking]), [
            'status' => BookingStatus::Completed->value,
        ]);
        $booking->refresh();
        $this->assertNotNull($booking->completed_at);
    }

    public function test_showroom_staff_can_soft_delete_service_booking(): void
    {
        $booking = ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
        ]);

        $this->actingAs($this->ownerA);

        $response = $this->delete(route('showrooms.manage.services.destroy', [
            'showroom' => $this->showroomA,
            'booking' => $booking,
        ]));

        $response->assertRedirect(route('showrooms.manage.services.index', $this->showroomA));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('service_bookings', [
            'id' => $booking->id,
        ]);
    }

    public function test_multi_tenant_isolation_prevents_viewing_or_modifying_other_showroom_service_bookings(): void
    {
        $bookingB = ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomB->id,
            'customer_name' => 'Showroom B Private Customer',
        ]);

        $this->actingAs($this->ownerA);

        // Attempt view Showroom B booking under Showroom A
        $viewResponse = $this->get(route('showrooms.manage.services.show', [
            'showroom' => $this->showroomA,
            'booking' => $bookingB,
        ]));
        $viewResponse->assertStatus(404);

        // Attempt update Showroom B booking under Showroom A
        $updateResponse = $this->put(route('showrooms.manage.services.update', [
            'showroom' => $this->showroomA,
            'booking' => $bookingB,
        ]), [
            'status' => BookingStatus::Cancelled->value,
        ]);
        $updateResponse->assertStatus(404);

        // Attempt delete Showroom B booking under Showroom A
        $deleteResponse = $this->delete(route('showrooms.manage.services.destroy', [
            'showroom' => $this->showroomA,
            'booking' => $bookingB,
        ]));
        $deleteResponse->assertStatus(404);

        $this->assertDatabaseHas('service_bookings', [
            'id' => $bookingB->id,
            'deleted_at' => null,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_workshop_management(): void
    {
        $response = $this->get(route('showrooms.manage.services.index', $this->showroomA));

        $response->assertRedirect(route('login'));
    }

    public function test_showroom_dashboard_accurately_counts_today_active_services(): void
    {
        // 2 active bookings for today
        ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'booking_date' => today(),
            'status' => BookingStatus::Confirmed->value,
        ]);
        ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'booking_date' => today(),
            'status' => BookingStatus::InService->value,
        ]);

        // 1 cancelled booking for today (should not count as active)
        ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'booking_date' => today(),
            'status' => BookingStatus::Cancelled->value,
        ]);

        // 1 future booking (should not count in today's count)
        ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'booking_date' => today()->addDays(2),
            'status' => BookingStatus::Confirmed->value,
        ]);

        $this->actingAs($this->ownerA);

        $response = $this->get(route('showrooms.manage', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('Service Bookings');
        $this->assertEquals(2, $response->viewData('metrics')['today_services']);
    }

    public function test_showroom_staff_can_filter_service_bookings_by_today_and_tomorrow_timeframe(): void
    {
        $todayBooking = ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'booking_date' => today(),
            'customer_name' => 'Kunal Today Customer',
            'status' => BookingStatus::Confirmed->value,
        ]);

        $tomorrowBooking = ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'booking_date' => today()->addDay(),
            'customer_name' => 'Aarav Tomorrow Customer',
            'status' => BookingStatus::Pending->value,
        ]);

        $futureBooking = ServiceBooking::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'booking_date' => today()->addDays(5),
            'customer_name' => 'Rohan Future Customer',
            'status' => BookingStatus::Confirmed->value,
        ]);

        $this->actingAs($this->ownerA);

        // Filter by today
        $responseToday = $this->get(route('showrooms.manage.services.index', [
            'showroom' => $this->showroomA,
            'timeframe' => 'today',
        ]));
        $responseToday->assertStatus(200);
        $responseToday->assertSee('Kunal Today Customer');
        $responseToday->assertDontSee('Aarav Tomorrow Customer');
        $responseToday->assertDontSee('Rohan Future Customer');

        // Filter by tomorrow
        $responseTomorrow = $this->get(route('showrooms.manage.services.index', [
            'showroom' => $this->showroomA,
            'timeframe' => 'tomorrow',
        ]));
        $responseTomorrow->assertStatus(200);
        $responseTomorrow->assertSee('Aarav Tomorrow Customer');
        $responseTomorrow->assertDontSee('Kunal Today Customer');
        $responseTomorrow->assertDontSee('Rohan Future Customer');
    }
}
