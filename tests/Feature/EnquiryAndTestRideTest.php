<?php

namespace Tests\Feature;

use App\Enums\BikeCategory;
use App\Enums\BikeStatus;
use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Models\Bike;
use App\Models\Enquiry;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnquiryAndTestRideTest extends TestCase
{
    use RefreshDatabase;

    protected User $ownerA;

    protected User $staffA;

    protected User $ownerB;

    protected User $customer;

    protected Showroom $showroomA;

    protected Showroom $showroomB;

    protected Bike $bikeA;

    protected Bike $bikeB;

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

        $this->staffA = User::create([
            'name' => 'Pooja Verma',
            'email' => 'pooja@apex.example',
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
            'name' => 'Arun Kumar',
            'email' => 'arun@customer.example',
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
            'user_id' => $this->staffA->id,
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

        $this->bikeB = Bike::create([
            'tenant_id' => $this->showroomB->id,
            'name' => 'Kawasaki Ninja ZX-6R',
            'slug' => 'kawasaki-ninja-zx6r',
            'sku' => 'VEL-NIN-6R',
            'brand' => 'Kawasaki',
            'model_year' => 2026,
            'category' => BikeCategory::Sports->value,
            'status' => BikeStatus::InStock->value,
            'price' => 1120000.00,
            'is_featured' => true,
            'is_published' => true,
        ]);
    }

    public function test_public_visitor_can_view_enquiry_and_test_ride_page(): void
    {
        $response = $this->get(route('showrooms.enquiries.create', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('Book a Test Ride', false);
        $response->assertSee('Bajaj Pulsar NS400Z');
        $response->assertSee('Apex Moto Hub');
    }

    public function test_public_visitor_can_pre_select_bike_and_type_via_query_parameters(): void
    {
        $response = $this->get(route('showrooms.enquiries.create', [
            'showroom' => $this->showroomA,
            'bike' => 'pulsar-ns400z',
            'type' => 'test_ride',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Bajaj Pulsar NS400Z');
        $response->assertSee('Book a Test Ride');
    }

    public function test_customer_can_submit_test_ride_request_successfully(): void
    {
        $preferredDate = now()->addDays(3)->format('Y-m-d');

        $response = $this->post(route('showrooms.enquiries.store', $this->showroomA), [
            'customer_name' => 'Rohan Sen',
            'customer_email' => 'rohan@example.com',
            'customer_phone' => '+91 9876543210',
            'type' => EnquiryType::TestRide->value,
            'bike_id' => $this->bikeA->id,
            'preferred_date' => $preferredDate,
            'preferred_time_slot' => '10:00 - 12:00',
            'driving_license_number' => 'DL-KA-05-2022-0012345',
            'customer_notes' => 'Please arrange the test ride in the morning hours.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('enquiries', [
            'tenant_id' => $this->showroomA->id,
            'customer_name' => 'Rohan Sen',
            'customer_email' => 'rohan@example.com',
            'customer_phone' => '+91 9876543210',
            'type' => EnquiryType::TestRide->value,
            'bike_id' => $this->bikeA->id,
            'preferred_date' => $preferredDate.' 00:00:00',
            'preferred_time_slot' => '10:00 - 12:00',
            'driving_license_number' => 'DL-KA-05-2022-0012345',
            'status' => EnquiryStatus::New->value,
        ]);

        $enquiry = Enquiry::where('customer_email', 'rohan@example.com')->first();
        $this->assertNotNull($enquiry);
        $this->assertStringStartsWith('ENQ-', $enquiry->enquiry_number);
    }

    public function test_customer_can_submit_purchase_quotation_enquiry_with_bike_selected(): void
    {
        $this->actingAs($this->customer);

        $response = $this->post(route('showrooms.enquiries.store', $this->showroomA), [
            'customer_name' => 'Arun Kumar',
            'customer_email' => 'arun@customer.example',
            'customer_phone' => '+91 9811122233',
            'type' => EnquiryType::Purchase->value,
            'bike_id' => $this->bikeA->id,
            'customer_notes' => 'Looking for on-road price in Bangalore with accessories.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('enquiries', [
            'tenant_id' => $this->showroomA->id,
            'user_id' => $this->customer->id,
            'customer_name' => 'Arun Kumar',
            'customer_email' => 'arun@customer.example',
            'type' => EnquiryType::Purchase->value,
            'bike_id' => $this->bikeA->id,
            'status' => EnquiryStatus::New->value,
        ]);
    }

    public function test_customer_enquiry_submission_validates_required_fields(): void
    {
        $response = $this->post(route('showrooms.enquiries.store', $this->showroomA), [
            'customer_name' => '',
            'customer_email' => 'not-an-email',
            'customer_phone' => '',
            'type' => '',
        ]);

        $response->assertSessionHasErrors(['customer_name', 'customer_email', 'customer_phone', 'type']);
    }

    public function test_customer_enquiry_submission_validates_enquiry_type(): void
    {
        $response = $this->post(route('showrooms.enquiries.store', $this->showroomA), [
            'customer_name' => 'Test Customer',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '+91 9999999999',
            'type' => 'invalid_type',
        ]);

        $response->assertSessionHasErrors(['type']);
    }

    public function test_customer_enquiry_submission_rejects_bike_belonging_to_another_showroom(): void
    {
        // $this->bikeB belongs to showroomB
        $response = $this->post(route('showrooms.enquiries.store', $this->showroomA), [
            'customer_name' => 'Sneha Roy',
            'customer_email' => 'sneha@example.com',
            'customer_phone' => '+91 9888877777',
            'type' => EnquiryType::Purchase->value,
            'bike_id' => $this->bikeB->id,
        ]);

        $response->assertSessionHasErrors(['bike_id']);
        $this->assertDatabaseMissing('enquiries', [
            'customer_email' => 'sneha@example.com',
        ]);
    }

    public function test_customer_enquiry_submission_rejects_past_preferred_date(): void
    {
        $response = $this->post(route('showrooms.enquiries.store', $this->showroomA), [
            'customer_name' => 'Sneha Roy',
            'customer_email' => 'sneha@example.com',
            'customer_phone' => '+91 9888877777',
            'type' => EnquiryType::TestRide->value,
            'preferred_date' => now()->subDay()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors(['preferred_date']);
    }

    public function test_customer_enquiry_creates_audit_log(): void
    {
        $this->post(route('showrooms.enquiries.store', $this->showroomA), [
            'customer_name' => 'Kiran Varma',
            'customer_email' => 'kiran@example.com',
            'customer_phone' => '+91 9777766666',
            'type' => EnquiryType::General->value,
            'customer_notes' => 'Do you provide exchange bonus on older bikes?',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'enquiry.created',
        ]);
    }

    public function test_showroom_owner_can_view_enquiries_management_index_with_metrics(): void
    {
        Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'type' => EnquiryType::TestRide->value,
            'status' => EnquiryStatus::New->value,
        ]);

        Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'type' => EnquiryType::Purchase->value,
            'status' => EnquiryStatus::TestRideScheduled->value,
        ]);

        Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'type' => EnquiryType::General->value,
            'status' => EnquiryStatus::Completed->value,
        ]);

        $this->actingAs($this->ownerA);

        $response = $this->get(route('showrooms.manage.enquiries.index', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('Customer Enquiries');
        $response->assertSee('Total Inquiries');
        $response->assertSee('New Leads');
        $response->assertSee('Test Rides Scheduled');
        $response->assertSee('Converted / Booked');
    }

    public function test_showroom_staff_can_filter_enquiries_by_type_and_status(): void
    {
        $enquiryTestRide = Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'type' => EnquiryType::TestRide->value,
            'status' => EnquiryStatus::New->value,
            'customer_name' => 'Filter Target Test Ride',
        ]);

        $enquiryPurchase = Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'type' => EnquiryType::Purchase->value,
            'status' => EnquiryStatus::Contacted->value,
            'customer_name' => 'Filter Target Purchase',
        ]);

        $this->actingAs($this->staffA);

        // Filter by type=test_ride
        $responseType = $this->get(route('showrooms.manage.enquiries.index', [
            'showroom' => $this->showroomA,
            'type' => 'test_ride',
        ]));

        $responseType->assertStatus(200);
        $responseType->assertSee('Filter Target Test Ride');
        $responseType->assertDontSee('Filter Target Purchase');

        // Filter by status=contacted
        $responseStatus = $this->get(route('showrooms.manage.enquiries.index', [
            'showroom' => $this->showroomA,
            'status' => 'contacted',
        ]));

        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('Filter Target Purchase');
        $responseStatus->assertDontSee('Filter Target Test Ride');
    }

    public function test_showroom_staff_can_search_enquiries_by_customer_details(): void
    {
        Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'customer_name' => 'Alok Nath UniqueName',
            'customer_phone' => '+91 9123456789',
        ]);

        Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'customer_name' => 'Sunil Gupta OtherName',
            'customer_phone' => '+91 9988776655',
        ]);

        $this->actingAs($this->staffA);

        $response = $this->get(route('showrooms.manage.enquiries.index', [
            'showroom' => $this->showroomA,
            'search' => 'UniqueName',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Alok Nath UniqueName');
        $response->assertDontSee('Sunil Gupta OtherName');
    }

    public function test_showroom_owner_can_view_individual_enquiry_details(): void
    {
        $enquiry = Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $this->bikeA->id,
            'customer_name' => 'Deepak Chopra',
            'customer_email' => 'deepak@example.com',
            'customer_notes' => 'Needs quotation for dual-channel ABS variant.',
        ]);

        $this->actingAs($this->ownerA);

        $response = $this->get(route('showrooms.manage.enquiries.show', [
            'showroom' => $this->showroomA,
            'enquiry' => $enquiry,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Deepak Chopra');
        $response->assertSee('deepak@example.com');
        $response->assertSee('Bajaj Pulsar NS400Z');
        $response->assertSee('Needs quotation for dual-channel ABS variant.');
    }

    public function test_showroom_owner_can_update_enquiry_status_assign_staff_and_add_notes(): void
    {
        $enquiry = Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $this->bikeA->id,
            'status' => EnquiryStatus::New->value,
            'assigned_to_user_id' => null,
            'staff_notes' => null,
        ]);

        $this->actingAs($this->ownerA);

        $scheduleTime = now()->addDays(2)->format('Y-m-d H:i:s');

        $response = $this->put(route('showrooms.manage.enquiries.update', [
            'showroom' => $this->showroomA,
            'enquiry' => $enquiry,
        ]), [
            'status' => EnquiryStatus::TestRideScheduled->value,
            'assigned_to_user_id' => $this->staffA->id,
            'scheduled_at' => $scheduleTime,
            'staff_notes' => 'Scheduled customer for Saturday 11 AM. Helmets ready.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('enquiries', [
            'id' => $enquiry->id,
            'tenant_id' => $this->showroomA->id,
            'status' => EnquiryStatus::TestRideScheduled->value,
            'assigned_to_user_id' => $this->staffA->id,
            'staff_notes' => 'Scheduled customer for Saturday 11 AM. Helmets ready.',
        ]);
    }

    public function test_status_transition_to_contacted_sets_contacted_at_timestamp(): void
    {
        $enquiry = Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'status' => EnquiryStatus::New->value,
            'contacted_at' => null,
        ]);

        $this->actingAs($this->ownerA);

        $this->put(route('showrooms.manage.enquiries.update', [
            'showroom' => $this->showroomA,
            'enquiry' => $enquiry,
        ]), [
            'status' => EnquiryStatus::Contacted->value,
            'staff_notes' => 'Spoke to customer on phone.',
        ]);

        $enquiry->refresh();
        $this->assertEquals(EnquiryStatus::Contacted, $enquiry->status);
        $this->assertNotNull($enquiry->contacted_at);
    }

    public function test_status_transition_to_completed_sets_completed_at_timestamp(): void
    {
        $enquiry = Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'status' => EnquiryStatus::TestRideScheduled->value,
            'completed_at' => null,
        ]);

        $this->actingAs($this->ownerA);

        $this->put(route('showrooms.manage.enquiries.update', [
            'showroom' => $this->showroomA,
            'enquiry' => $enquiry,
        ]), [
            'status' => EnquiryStatus::Completed->value,
            'staff_notes' => 'Customer completed test ride and gave great feedback.',
        ]);

        $enquiry->refresh();
        $this->assertEquals(EnquiryStatus::Completed, $enquiry->status);
        $this->assertNotNull($enquiry->completed_at);
    }

    public function test_showroom_owner_can_soft_delete_enquiry(): void
    {
        $enquiry = Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
        ]);

        $this->actingAs($this->ownerA);

        $response = $this->delete(route('showrooms.manage.enquiries.destroy', [
            'showroom' => $this->showroomA,
            'enquiry' => $enquiry,
        ]));

        $response->assertRedirect(route('showrooms.manage.enquiries.index', $this->showroomA));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('enquiries', [
            'id' => $enquiry->id,
        ]);
    }

    public function test_multi_tenant_isolation_prevents_viewing_enquiries_from_another_showroom(): void
    {
        $enquiryB = Enquiry::factory()->create([
            'tenant_id' => $this->showroomB->id,
            'customer_name' => 'Confidential Lead Showroom B',
        ]);

        // Attempt by showroom A owner to view showroom B's enquiry under showroom A's URL
        $this->actingAs($this->ownerA);

        $response = $this->get(route('showrooms.manage.enquiries.show', [
            'showroom' => $this->showroomA,
            'enquiry' => $enquiryB,
        ]));

        $response->assertStatus(404);
    }

    public function test_multi_tenant_isolation_prevents_updating_or_deleting_enquiries_from_another_showroom(): void
    {
        $enquiryB = Enquiry::factory()->create([
            'tenant_id' => $this->showroomB->id,
            'customer_name' => 'Target Showroom B Lead',
        ]);

        $this->actingAs($this->ownerA);

        // Attempt update
        $updateResponse = $this->put(route('showrooms.manage.enquiries.update', [
            'showroom' => $this->showroomA,
            'enquiry' => $enquiryB,
        ]), [
            'status' => EnquiryStatus::Lost->value,
        ]);

        $updateResponse->assertStatus(404);

        // Attempt delete
        $deleteResponse = $this->delete(route('showrooms.manage.enquiries.destroy', [
            'showroom' => $this->showroomA,
            'enquiry' => $enquiryB,
        ]));

        $deleteResponse->assertStatus(404);

        $this->assertDatabaseHas('enquiries', [
            'id' => $enquiryB->id,
            'deleted_at' => null,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_enquiry_management(): void
    {
        $response = $this->get(route('showrooms.manage.enquiries.index', $this->showroomA));

        $response->assertRedirect(route('login'));
    }

    public function test_showroom_dashboard_counts_active_enquiries_accurately(): void
    {
        // 2 active leads
        Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'status' => EnquiryStatus::New->value,
        ]);
        Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'status' => EnquiryStatus::TestRideScheduled->value,
        ]);

        // 1 inactive/completed lead
        Enquiry::factory()->create([
            'tenant_id' => $this->showroomA->id,
            'status' => EnquiryStatus::Completed->value,
        ]);

        $this->actingAs($this->ownerA);

        $response = $this->get(route('showrooms.manage', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('Enquiries');
        $response->assertSee('Active leads');
        $this->assertEquals(2, $response->viewData('metrics')['active_enquiries']);
    }
}
