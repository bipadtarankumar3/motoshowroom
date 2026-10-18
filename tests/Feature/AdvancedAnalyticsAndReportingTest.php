<?php

namespace Tests\Feature;

use App\Enums\BikeCategory;
use App\Enums\BookingStatus;
use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Enums\PaymentStatus;
use App\Enums\ServiceType;
use App\Models\AiConversation;
use App\Models\Bike;
use App\Models\Enquiry;
use App\Models\Payment;
use App\Models\PromotionalCampaign;
use App\Models\ScratchCardParticipation;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedAnalyticsAndReportingTest extends TestCase
{
    use RefreshDatabase;

    protected User $ownerA;

    protected User $ownerB;

    protected User $superAdmin;

    protected Showroom $showroomA;

    protected Showroom $showroomB;

    protected SubscriptionPlan $starterPlan;

    protected SubscriptionPlan $proPlan;

    protected Bike $bikeA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ownerA = User::create([
            'name' => 'Aditya Roy',
            'email' => 'aditya@royalmoto.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->ownerB = User::create([
            'name' => 'Kabir Sen',
            'email' => 'kabir@speedways.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->superAdmin = User::create([
            'name' => 'Platform Super Admin',
            'email' => 'superadmin@motoshowroom.example',
            'password' => 'Password123!',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->showroomA = Showroom::create([
            'name' => 'Royal Moto Bangalore',
            'slug' => 'royal-moto-blr',
            'email' => 'info@royalmoto.example',
            'phone' => '+919876543210',
            'address' => 'Indiranagar 100ft Road',
            'city' => 'Bengaluru',
            'state' => 'Karnataka',
            'country' => 'India',
            'postal_code' => '560038',
            'currency' => 'INR',
            'status' => 'active',
        ]);

        $this->showroomB = Showroom::create([
            'name' => 'Speedways Pune',
            'slug' => 'speedways-pune',
            'email' => 'info@speedways.example',
            'phone' => '+919876543211',
            'address' => 'FC Road',
            'city' => 'Pune',
            'state' => 'Maharashtra',
            'country' => 'India',
            'postal_code' => '411004',
            'currency' => 'INR',
            'status' => 'active',
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'role' => 'owner',
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroomB->id,
            'user_id' => $this->ownerB->id,
            'role' => 'owner',
        ]);

        $this->starterPlan = SubscriptionPlan::create([
            'name' => 'Starter Plan',
            'slug' => 'starter',
            'monthly_price' => 1999.00,
            'annual_price' => 19990.00,
            'max_bikes' => 5,
            'max_staff' => 2,
            'has_advanced_analytics' => false,
            'has_scratch_cards' => false,
            'has_ai_assistant' => false,
            'is_active' => true,
        ]);

        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Pro Intelligence Plan',
            'slug' => 'pro-intel',
            'monthly_price' => 4999.00,
            'annual_price' => 49990.00,
            'max_bikes' => 50,
            'max_staff' => 15,
            'has_advanced_analytics' => true,
            'has_scratch_cards' => true,
            'has_ai_assistant' => true,
            'is_active' => true,
        ]);

        $this->bikeA = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Thunderbird 350X',
            'slug' => 'thunderbird-350x',
            'sku' => 'MOTO-RE-0001',
            'brand' => 'Royal Enfield',
            'model_year' => 2026,
            'category' => BikeCategory::Cruiser->value,
            'price' => 215000.00,
            'is_published' => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_dealership_analytics(): void
    {
        $response = $this->get(route('showrooms.manage.analytics.index', $this->showroomA));
        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_export_analytics_csv(): void
    {
        $response = $this->get(route('showrooms.manage.analytics.export', [$this->showroomA, 'type' => 'revenue']));
        $response->assertRedirect(route('login'));
    }

    public function test_non_member_cannot_access_other_showroom_analytics(): void
    {
        $response = $this->actingAs($this->ownerB)->get(route('showrooms.manage.analytics.index', $this->showroomA));
        $response->assertForbidden();
    }

    public function test_non_member_cannot_export_other_showroom_analytics(): void
    {
        $response = $this->actingAs($this->ownerB)->get(route('showrooms.manage.analytics.export', [$this->showroomA, 'type' => 'revenue']));
        $response->assertForbidden();
    }

    public function test_showroom_without_advanced_analytics_feature_sees_gated_banner(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->starterPlan, 'monthly');

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.analytics.index', $this->showroomA));

        $response->assertOk();
        $response->assertSee('Advanced Analytics & BI Gated', false);
        $response->assertSee('Upgrade Subscription');
        $response->assertDontSee('Sales Pipeline Conversion Funnel');
    }

    public function test_showroom_without_advanced_analytics_cannot_export_csv(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->starterPlan, 'monthly');

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.analytics.export', [$this->showroomA, 'type' => 'revenue']));

        $response->assertForbidden();
    }

    public function test_entitled_showroom_analytics_dashboard_renders_metrics(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->proPlan, 'monthly');

        Payment::create([
            'tenant_id' => $this->showroomA->id,
            'payable_type' => Bike::class,
            'payable_id' => $this->bikeA->id,
            'purpose' => 'bike_booking_deposit',
            'razorpay_order_id' => 'order_analytics_1',
            'razorpay_payment_id' => 'pay_analytics_1',
            'amount' => 12500.00,
            'currency' => 'INR',
            'status' => PaymentStatus::Captured,
            'method' => 'card',
        ]);

        Enquiry::create([
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $this->bikeA->id,
            'enquiry_number' => 'ENQ-ANA-001',
            'type' => EnquiryType::Purchase,
            'status' => EnquiryStatus::Completed,
            'customer_name' => 'Arun Kumar',
            'customer_phone' => '+919998887771',
            'customer_email' => 'arun@example.com',
        ]);

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.analytics.index', $this->showroomA));

        $response->assertOk();
        $response->assertSee('Business Intelligence', false);
        $response->assertSee('Gross Captured');
        $response->assertSee('₹12,500.00');
        $response->assertSee('Sales Pipeline Conversion Funnel');
        $response->assertSee('Thunderbird 350X');
    }

    public function test_period_filtering_updates_analytics_time_horizon(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->proPlan, 'monthly');

        // Create a payment 15 days ago
        $oldPayment = Payment::create([
            'tenant_id' => $this->showroomA->id,
            'payable_type' => Bike::class,
            'payable_id' => $this->bikeA->id,
            'purpose' => 'bike_booking_deposit',
            'razorpay_order_id' => 'order_old_15d',
            'razorpay_payment_id' => 'pay_old_15d',
            'amount' => 8000.00,
            'currency' => 'INR',
            'status' => PaymentStatus::Captured,
        ]);
        $oldPayment->created_at = Carbon::now()->subDays(15);
        $oldPayment->save();

        // Under 7d filter: old payment not included
        $response7d = $this->actingAs($this->ownerA)->get(route('showrooms.manage.analytics.index', [$this->showroomA, 'period' => '7d']));
        $response7d->assertOk();
        $response7d->assertSee('₹0.00');

        // Under 30d filter: old payment is included
        $response30d = $this->actingAs($this->ownerA)->get(route('showrooms.manage.analytics.index', [$this->showroomA, 'period' => '30d']));
        $response30d->assertOk();
        $response30d->assertSee('₹8,000.00');
    }

    public function test_revenue_metrics_and_service_split_calculated_accurately(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->proPlan, 'monthly');

        $booking = ServiceBooking::create([
            'tenant_id' => $this->showroomA->id,
            'booking_number' => 'SRV-TEST-001',
            'customer_name' => 'Deepak Verma',
            'customer_email' => 'deepak@example.com',
            'customer_phone' => '+919876500001',
            'bike_make_model' => 'Royal Enfield Classic 350',
            'registration_number' => 'KA04MN1111',
            'service_type' => ServiceType::GeneralService,
            'booking_date' => now()->toDateString(),
            'time_slot' => '09:00 - 11:00',
            'status' => BookingStatus::Completed,
            'estimated_cost' => 3500.00,
        ]);

        // Workshop Service Payment
        Payment::create([
            'tenant_id' => $this->showroomA->id,
            'payable_type' => ServiceBooking::class,
            'payable_id' => $booking->id,
            'purpose' => 'service_invoice',
            'amount' => 3500.00,
            'currency' => 'INR',
            'status' => PaymentStatus::Captured,
        ]);

        // Vehicle Direct Payment
        Payment::create([
            'tenant_id' => $this->showroomA->id,
            'payable_type' => Bike::class,
            'payable_id' => $this->bikeA->id,
            'purpose' => 'bike_booking_deposit',
            'amount' => 6500.00,
            'currency' => 'INR',
            'status' => PaymentStatus::Captured,
        ]);

        $analytics = app(AnalyticsService::class)->getDealershipAnalytics($this->showroomA, '30d');

        $this->assertEquals(10000.00, $analytics['revenue']['total']);
        $this->assertEquals(3500.00, $analytics['revenue']['service_revenue']);
        $this->assertEquals(6500.00, $analytics['revenue']['other_revenue']);
        $this->assertEquals(2, $analytics['revenue']['total_transactions']);
    }

    public function test_sales_funnel_and_enquiry_type_analytics(): void
    {
        Enquiry::create([
            'tenant_id' => $this->showroomA->id,
            'enquiry_number' => 'ENQ-F-1',
            'type' => EnquiryType::Purchase,
            'status' => EnquiryStatus::New,
            'customer_name' => 'Lead One',
            'customer_email' => 'lead1@example.com',
            'customer_phone' => '+919999900001',
        ]);

        Enquiry::create([
            'tenant_id' => $this->showroomA->id,
            'enquiry_number' => 'ENQ-F-2',
            'type' => EnquiryType::TestRide,
            'status' => EnquiryStatus::TestRideScheduled,
            'customer_name' => 'Lead Two',
            'customer_email' => 'lead2@example.com',
            'customer_phone' => '+919999900002',
        ]);

        Enquiry::create([
            'tenant_id' => $this->showroomA->id,
            'enquiry_number' => 'ENQ-F-3',
            'type' => EnquiryType::General,
            'status' => EnquiryStatus::Completed,
            'customer_name' => 'Lead Three',
            'customer_email' => 'lead3@example.com',
            'customer_phone' => '+919999900003',
        ]);

        $analytics = app(AnalyticsService::class)->getDealershipAnalytics($this->showroomA, '30d');

        $this->assertEquals(3, $analytics['enquiries']['total']);
        $this->assertEquals(1, $analytics['enquiries']['by_type']['purchase']);
        $this->assertEquals(1, $analytics['enquiries']['by_type']['test_ride']);
        $this->assertEquals(1, $analytics['enquiries']['by_type']['general']);
        $this->assertEquals(1, $analytics['enquiries']['funnel']['new']);
        $this->assertEquals(1, $analytics['enquiries']['funnel']['test_ride_scheduled']);
        $this->assertEquals(1, $analytics['enquiries']['funnel']['completed']);
        $this->assertEquals(33.3, $analytics['enquiries']['conversion_rate']);
    }

    public function test_workshop_completion_rate_and_status_breakdown(): void
    {
        ServiceBooking::create([
            'tenant_id' => $this->showroomA->id,
            'booking_number' => 'SRV-W-1',
            'customer_name' => 'Client A',
            'customer_email' => 'clienta@example.com',
            'customer_phone' => '+919999911111',
            'bike_make_model' => 'Royal Enfield Classic 350',
            'registration_number' => 'KA04MN2222',
            'service_type' => ServiceType::GeneralService,
            'booking_date' => now()->toDateString(),
            'time_slot' => '09:00 - 11:00',
            'status' => BookingStatus::Completed,
        ]);

        ServiceBooking::create([
            'tenant_id' => $this->showroomA->id,
            'booking_number' => 'SRV-W-2',
            'customer_name' => 'Client B',
            'customer_email' => 'clientb@example.com',
            'customer_phone' => '+919999911112',
            'bike_make_model' => 'Honda CB350',
            'registration_number' => 'KA04MN3333',
            'service_type' => ServiceType::GeneralService,
            'booking_date' => now()->toDateString(),
            'time_slot' => '11:00 - 13:00',
            'status' => BookingStatus::Pending,
        ]);

        $analytics = app(AnalyticsService::class)->getDealershipAnalytics($this->showroomA, '30d');

        $this->assertEquals(2, $analytics['workshop']['total']);
        $this->assertEquals(1, $analytics['workshop']['completed']);
        $this->assertEquals(1, $analytics['workshop']['by_status']['completed']);
        $this->assertEquals(1, $analytics['workshop']['by_status']['pending']);
        $this->assertEquals(50.0, $analytics['workshop']['completion_rate']);
    }

    public function test_scratch_cards_and_ai_lead_conversion_telemetry(): void
    {
        $campaign = PromotionalCampaign::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Diwali Bonanza',
            'slug' => 'diwali-bonanza',
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(5),
            'status' => 'active',
        ]);

        ScratchCardParticipation::create([
            'tenant_id' => $this->showroomA->id,
            'campaign_id' => $campaign->id,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+919876543201',
            'participation_token' => 'TOKEN_1',
            'coupon_code' => 'HELM100',
            'is_scratched' => true,
            'is_redeemed' => true,
        ]);

        AiConversation::create([
            'tenant_id' => $this->showroomA->id,
            'session_id' => 'ai_sess_ana_1',
            'customer_name' => 'AI Prospect',
            'customer_phone' => '+919876543202',
            'lead_converted' => true,
        ]);

        $analytics = app(AnalyticsService::class)->getDealershipAnalytics($this->showroomA, '30d');

        $this->assertEquals(1, $analytics['marketing']['scratch_cards_total']);
        $this->assertEquals(1, $analytics['marketing']['scratched_count']);
        $this->assertEquals(1, $analytics['marketing']['redeemed_count']);
        $this->assertEquals(100.0, $analytics['marketing']['redemption_rate']);

        $this->assertEquals(1, $analytics['ai_assistant']['total_chats']);
        $this->assertEquals(1, $analytics['ai_assistant']['converted_leads']);
        $this->assertEquals(100.0, $analytics['ai_assistant']['conversion_rate']);
    }

    public function test_csv_export_validation_requires_valid_report_type(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->proPlan, 'monthly');

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.analytics.export', [
            $this->showroomA,
            'type' => 'unsupported_type',
        ]));

        $response->assertSessionHasErrors(['type']);
    }

    public function test_csv_export_revenue_streams_valid_content(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->proPlan, 'monthly');

        Payment::create([
            'tenant_id' => $this->showroomA->id,
            'payable_type' => Bike::class,
            'payable_id' => $this->bikeA->id,
            'purpose' => 'bike_booking_deposit',
            'razorpay_order_id' => 'order_csv_rev',
            'razorpay_payment_id' => 'pay_csv_12345',
            'amount' => 4500.00,
            'currency' => 'INR',
            'status' => PaymentStatus::Captured,
            'method' => 'upi',
        ]);

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.analytics.export', [
            $this->showroomA,
            'type' => 'revenue',
            'period' => '30d',
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('pay_csv_12345', $response->getContent());
        $this->assertStringContainsString('4500.00', $response->getContent());
        $this->assertStringContainsString('Reference,Type', $response->getContent());
    }

    public function test_csv_export_enquiries_streams_valid_content(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->proPlan, 'monthly');

        Enquiry::create([
            'tenant_id' => $this->showroomA->id,
            'bike_id' => $this->bikeA->id,
            'enquiry_number' => 'ENQ-CSV-999',
            'type' => EnquiryType::Purchase,
            'status' => EnquiryStatus::Contacted,
            'customer_name' => 'Nikhil Gupta',
            'customer_phone' => '+919876543299',
            'customer_email' => 'nikhil@example.com',
        ]);

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.analytics.export', [
            $this->showroomA,
            'type' => 'enquiries',
            'period' => '30d',
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('ENQ-CSV-999', $response->getContent());
        $this->assertStringContainsString('Nikhil Gupta', $response->getContent());
        $this->assertStringContainsString('Thunderbird 350X', $response->getContent());
    }

    public function test_csv_export_workshop_streams_valid_content(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->proPlan, 'monthly');

        ServiceBooking::create([
            'tenant_id' => $this->showroomA->id,
            'booking_number' => 'SRV-CSV-777',
            'customer_name' => 'Meera Nair',
            'customer_email' => 'meera@example.com',
            'customer_phone' => '+919876543777',
            'registration_number' => 'KA04MN1234',
            'bike_make_model' => 'Yamaha R15 V4',
            'service_type' => ServiceType::GeneralService,
            'booking_date' => now()->toDateString(),
            'time_slot' => '14:00 - 16:00',
            'status' => BookingStatus::InService,
            'estimated_cost' => 2200.00,
        ]);

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.analytics.export', [
            $this->showroomA,
            'type' => 'workshop',
            'period' => '30d',
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('SRV-CSV-777', $response->getContent());
        $this->assertStringContainsString('Meera Nair', $response->getContent());
        $this->assertStringContainsString('KA04MN1234', $response->getContent());
    }

    public function test_strict_multi_tenant_isolation_in_analytics_and_csv(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->proPlan, 'monthly');
        $subscriptionService->subscribe($this->showroomB, $this->proPlan, 'monthly');

        // Showroom B payment & enquiry
        Payment::create([
            'tenant_id' => $this->showroomB->id,
            'payable_type' => Showroom::class,
            'payable_id' => $this->showroomB->id,
            'purpose' => 'general',
            'razorpay_order_id' => 'order_sec_b',
            'razorpay_payment_id' => 'pay_private_b_999',
            'amount' => 99999.00,
            'currency' => 'INR',
            'status' => PaymentStatus::Captured,
        ]);

        Enquiry::create([
            'tenant_id' => $this->showroomB->id,
            'enquiry_number' => 'ENQ-SECRET-B',
            'type' => EnquiryType::Purchase,
            'status' => EnquiryStatus::New,
            'customer_name' => 'Secret Customer B',
            'customer_email' => 'secretb@example.com',
            'customer_phone' => '+919999999999',
        ]);

        // Showroom A should never see Showroom B data
        $analyticsA = app(AnalyticsService::class)->getDealershipAnalytics($this->showroomA, '30d');
        $this->assertEquals(0, $analyticsA['revenue']['total']);
        $this->assertEquals(0, $analyticsA['enquiries']['total']);

        // Showroom A CSV should not have Showroom B's payment
        $csvContent = app(AnalyticsService::class)->exportCsv($this->showroomA, 'revenue', '30d');
        $this->assertStringNotContainsString('pay_private_b_999', $csvContent);

        // Showroom A CSV should not have Showroom B's enquiry
        $enquiryCsv = app(AnalyticsService::class)->exportCsv($this->showroomA, 'enquiries', '30d');
        $this->assertStringNotContainsString('ENQ-SECRET-B', $enquiryCsv);
    }

    public function test_super_admin_can_access_platform_analytics(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->showroomA, $this->proPlan, 'monthly');

        Payment::create([
            'tenant_id' => $this->showroomA->id,
            'payable_type' => Showroom::class,
            'payable_id' => $this->showroomA->id,
            'purpose' => 'subscription_purchase',
            'razorpay_order_id' => 'order_super_gmv',
            'razorpay_payment_id' => 'pay_super_gmv_1',
            'amount' => 50000.00,
            'currency' => 'INR',
            'status' => PaymentStatus::Captured,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.analytics.index'));

        $response->assertOk();
        $response->assertSee('Platform Financial Intelligence');
        $response->assertSee('Platform Gross GMV');
        $response->assertSee('Monthly Recurring (MRR)');
        $response->assertSee('Annual Recurring (ARR)');
        $response->assertSee('Active Showrooms');
        $response->assertSee('₹50,000.00');
    }

    public function test_non_admin_cannot_access_platform_analytics(): void
    {
        $response = $this->actingAs($this->ownerA)->get(route('admin.analytics.index'));
        $response->assertForbidden();
    }
}
