<?php

namespace Tests\Feature;

use App\Enums\BikeCategory;
use App\Enums\BikeStatus;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\ServiceType;
use App\Enums\SubscriptionStatus;
use App\Models\Bike;
use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Services\RazorpayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RazorpayPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $ownerA;

    protected User $ownerB;

    protected User $customer;

    protected Showroom $showroomA;

    protected Showroom $showroomB;

    protected SubscriptionPlan $starterPlan;

    protected SubscriptionPlan $proPlan;

    protected RazorpayService $razorpayService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->razorpayService = app(RazorpayService::class);

        $this->ownerA = User::create([
            'name' => 'Rajesh Sharma',
            'email' => 'rajesh@apex.example',
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
            'name' => 'Amit Verma',
            'email' => 'amit@customer.example',
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
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        $this->showroomB = Showroom::create([
            'name' => 'Velocity Superbikes',
            'slug' => 'velocity-bikes',
            'email' => 'contact@velocity.example',
            'phone' => '+91 2298765432',
            'city' => 'Mumbai',
            'status' => 'active',
            'public_website_enabled' => true,
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
            'name' => 'Starter Throttle',
            'slug' => 'starter-throttle',
            'monthly_price' => 1999.00,
            'annual_price' => 19990.00,
            'currency' => 'INR',
            'max_bike_listings' => 10,
            'max_staff_accounts' => 2,
            'features_json' => ['inventory', 'enquiries'],
            'is_active' => true,
        ]);

        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Apex Pro',
            'slug' => 'apex-pro',
            'monthly_price' => 4999.00,
            'annual_price' => 49990.00,
            'currency' => 'INR',
            'max_bike_listings' => 50,
            'max_staff_accounts' => 10,
            'features_json' => ['inventory', 'enquiries', 'services', 'payments'],
            'is_active' => true,
        ]);
    }

    private function activateSubscriptionFor(Showroom $showroom): void
    {
        Subscription::create([
            'showroom_id' => $showroom->id,
            'plan_id' => $this->proPlan->id,
            'status' => SubscriptionStatus::Active->value,
            'billing_interval' => 'monthly',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);
    }

    public function test_showroom_owner_can_view_subscription_checkout_page(): void
    {
        $response = $this->actingAs($this->ownerA)->get(route('showrooms.billing.checkout', [
            'showroom' => $this->showroomA,
            'plan_id' => $this->proPlan->id,
            'interval' => 'monthly',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('showrooms.billing.checkout');
        $response->assertSeeText($this->proPlan->name);
        $response->assertSee('4,999');

        $this->assertDatabaseHas('payments', [
            'tenant_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->proPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'status' => PaymentStatus::Pending->value,
        ]);
    }

    public function test_unauthenticated_user_cannot_view_subscription_checkout(): void
    {
        $response = $this->get(route('showrooms.billing.checkout', [
            'showroom' => $this->showroomA,
            'plan_id' => $this->proPlan->id,
        ]));

        $response->assertRedirect(route('login'));
    }

    public function test_customer_can_view_service_checkout_page(): void
    {
        $bike = Bike::create([
            'tenant_id' => $this->showroomA->id,
            'name' => 'Ninja ZX-10R',
            'slug' => 'ninja-zx10r',
            'sku' => 'ZX10R-TEST-001',
            'brand' => 'Kawasaki',
            'category' => BikeCategory::Sports->value,
            'price' => 1600000.00,
            'engine_cc' => 998,
            'model_year' => 2026,
            'status' => BikeStatus::InStock->value,
            'is_featured' => true,
        ]);

        $booking = ServiceBooking::create([
            'tenant_id' => $this->showroomA->id,
            'user_id' => $this->customer->id,
            'bike_id' => $bike->id,
            'bike_make_model' => 'Kawasaki Ninja ZX-10R',
            'booking_number' => 'SRV-TEST-001',
            'customer_name' => 'Amit Verma',
            'customer_phone' => '+91 9876543210',
            'customer_email' => 'amit@customer.example',
            'registration_number' => 'KA-01-EQ-9999',
            'service_type' => ServiceType::GeneralService->value,
            'status' => BookingStatus::Completed->value,
            'payment_status' => PaymentStatus::Pending->value,
            'booking_date' => now()->toDateString(),
            'time_slot' => '10:00:00',
            'estimated_cost' => 3500.00,
            'final_cost' => 3500.00,
        ]);

        $response = $this->get(route('showrooms.services.checkout', [
            'showroom' => $this->showroomA,
            'booking' => $booking,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('showrooms.services.checkout');
        $response->assertSeeText('SRV-TEST-001');
        $response->assertSee('3,500');

        $this->assertDatabaseHas('payments', [
            'tenant_id' => $this->showroomA->id,
            'payable_type' => ServiceBooking::class,
            'payable_id' => $booking->id,
            'purpose' => 'service_invoice',
            'amount' => 3500.00,
            'status' => PaymentStatus::Pending->value,
        ]);
    }

    public function test_service_checkout_aborts_if_booking_belongs_to_different_showroom(): void
    {
        $bookingB = ServiceBooking::create([
            'tenant_id' => $this->showroomB->id,
            'bike_make_model' => 'Yamaha R15 V4',
            'booking_number' => 'SRV-TEST-B01',
            'customer_name' => 'Vikram Client',
            'customer_phone' => '+91 9999988888',
            'customer_email' => 'client@velocity.example',
            'registration_number' => 'MH-02-ZZ-1234',
            'service_type' => ServiceType::GeneralService->value,
            'status' => BookingStatus::Confirmed->value,
            'payment_status' => PaymentStatus::Pending->value,
            'booking_date' => now()->toDateString(),
            'time_slot' => '11:00:00',
            'estimated_cost' => 2000.00,
        ]);

        $response = $this->get(route('showrooms.services.checkout', [
            'showroom' => $this->showroomA,
            'booking' => $bookingB,
        ]));

        $response->assertStatus(404);
    }

    public function test_valid_hmac_signature_verifies_payment_and_activates_subscription(): void
    {
        // 1. Create a pending payment
        $payment = Payment::create([
            'tenant_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->proPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_valid_sub_123',
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
            'notes' => [
                'billing_interval' => 'monthly',
                'plan_id' => (string) $this->proPlan->id,
            ],
        ]);

        $paymentId = 'pay_test_succ_12345';
        $validSignature = $this->razorpayService->generateSignature($payment->razorpay_order_id, $paymentId);

        $response = $this->actingAs($this->ownerA)->post(route('showrooms.payments.verify', $this->showroomA), [
            'payment_id' => $payment->id,
            'razorpay_order_id' => $payment->razorpay_order_id,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $validSignature,
            'method' => 'upi',
        ]);

        $response->assertRedirect(route('showrooms.billing.index', $this->showroomA));
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals(PaymentStatus::Captured->value, $payment->status->value);
        $this->assertTrue($payment->signature_verified);
        $this->assertEquals($paymentId, $payment->razorpay_payment_id);
        $this->assertEquals('upi', $payment->method);

        // Assert subscription is activated
        $this->assertDatabaseHas('subscriptions', [
            'showroom_id' => $this->showroomA->id,
            'plan_id' => $this->proPlan->id,
            'status' => SubscriptionStatus::Active->value,
        ]);

        // Assert audit log was recorded
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'payment.captured',
            'tenant_id' => $this->showroomA->id,
        ]);
    }

    public function test_valid_hmac_signature_verifies_service_booking_payment(): void
    {
        $booking = ServiceBooking::create([
            'tenant_id' => $this->showroomA->id,
            'customer_name' => 'Amit Verma',
            'customer_phone' => '+91 9876543210',
            'customer_email' => 'amit@customer.example',
            'bike_make_model' => 'Honda CB350 Hness',
            'registration_number' => 'KA-01-EQ-9999',
            'service_type' => ServiceType::GeneralService->value,
            'status' => BookingStatus::Completed->value,
            'payment_status' => PaymentStatus::Pending->value,
            'booking_date' => now()->toDateString(),
            'time_slot' => '10:00:00',
            'estimated_cost' => 2500.00,
        ]);

        $payment = Payment::create([
            'tenant_id' => $this->showroomA->id,
            'payable_type' => ServiceBooking::class,
            'payable_id' => $booking->id,
            'purpose' => 'service_invoice',
            'amount' => 2500.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_srv_pay_456',
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
            'notes' => ['booking_id' => (string) $booking->id],
        ]);

        $paymentId = 'pay_srv_test_9988';
        $validSignature = $this->razorpayService->generateSignature($payment->razorpay_order_id, $paymentId);

        $response = $this->post(route('showrooms.payments.verify', $this->showroomA), [
            'payment_id' => $payment->id,
            'razorpay_order_id' => $payment->razorpay_order_id,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $validSignature,
            'method' => 'card',
        ]);

        $response->assertRedirect(route('showrooms.services.track', [
            'showroom' => $this->showroomA,
            'booking' => $booking->booking_number,
        ]));
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals(PaymentStatus::Captured->value, $payment->status->value);
        $this->assertTrue($payment->signature_verified);

        $booking->refresh();
        $this->assertEquals(PaymentStatus::Captured->value, $booking->payment_status->value);
        $this->assertEquals(2500.00, (float) $booking->final_cost);
    }

    public function test_invalid_hmac_signature_is_rejected_and_marks_payment_as_failed(): void
    {
        $payment = Payment::create([
            'tenant_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->proPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_fake_999',
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
        ]);

        $response = $this->actingAs($this->ownerA)->from('/some-page')->post(route('showrooms.payments.verify', $this->showroomA), [
            'payment_id' => $payment->id,
            'razorpay_order_id' => $payment->razorpay_order_id,
            'razorpay_payment_id' => 'pay_tampered_123',
            'razorpay_signature' => 'invalid_sha256_forged_signature_xyz',
            'method' => 'netbanking',
        ]);

        $response->assertRedirect('/some-page');
        $response->assertSessionHas('error');

        $payment->refresh();
        $this->assertEquals(PaymentStatus::Failed->value, $payment->status->value);
        $this->assertFalse($payment->signature_verified);
        $this->assertEquals('BAD_SIGNATURE', $payment->error_code);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'payment.failed',
            'tenant_id' => $this->showroomA->id,
        ]);
    }

    public function test_payment_verification_aborts_for_cross_tenant_tampering(): void
    {
        // Payment belongs to Showroom B
        $paymentB = Payment::create([
            'tenant_id' => $this->showroomB->id,
            'user_id' => $this->ownerB->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->proPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_b_456',
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
        ]);

        $signature = $this->razorpayService->generateSignature($paymentB->razorpay_order_id, 'pay_xyz_123');

        // Attempt to verify Showroom B's payment via Showroom A route
        $response = $this->actingAs($this->ownerA)->post(route('showrooms.payments.verify', $this->showroomA), [
            'payment_id' => $paymentB->id,
            'razorpay_order_id' => $paymentB->razorpay_order_id,
            'razorpay_payment_id' => 'pay_xyz_123',
            'razorpay_signature' => $signature,
        ]);

        $response->assertStatus(404);
        $paymentB->refresh();
        $this->assertEquals(PaymentStatus::Pending->value, $paymentB->status->value);
    }

    public function test_valid_webhook_processes_payment_captured_event(): void
    {
        $payment = Payment::create([
            'tenant_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->proPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_hook_captured_777',
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
            'notes' => ['billing_interval' => 'monthly'],
        ]);

        $payload = [
            'entity' => 'event',
            'id' => 'evt_captured_abc123',
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_hook_987654',
                        'order_id' => 'order_hook_captured_777',
                        'amount' => 499900,
                        'currency' => 'INR',
                        'status' => 'captured',
                        'method' => 'card',
                    ],
                ],
            ],
            'created_at' => time(),
        ];

        $rawBody = json_encode($payload);
        $signature = $this->razorpayService->generateWebhookSignature($rawBody);

        $response = $this->call(
            'POST',
            route('api.webhooks.razorpay'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
            ],
            $rawBody
        );

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'processed',
            'event_id' => 'evt_captured_abc123',
        ]);

        $payment->refresh();
        $this->assertEquals(PaymentStatus::Captured->value, $payment->status->value);
        $this->assertEquals('pay_hook_987654', $payment->razorpay_payment_id);
        $this->assertTrue($payment->signature_verified);

        // Webhook ledger registered and processed
        $this->assertDatabaseHas('webhook_events', [
            'event_id' => 'evt_captured_abc123',
            'event_type' => 'payment.captured',
            'processed' => true,
        ]);
    }

    public function test_duplicate_webhook_is_idempotent_and_returns_already_processed(): void
    {
        WebhookEvent::create([
            'event_id' => 'evt_already_received_999',
            'event_type' => 'payment.captured',
            'payload' => ['some' => 'data'],
            'processed' => true,
            'processed_at' => now(),
        ]);

        $payload = [
            'id' => 'evt_already_received_999',
            'event' => 'payment.captured',
            'payload' => [],
        ];

        $rawBody = json_encode($payload);
        $signature = $this->razorpayService->generateWebhookSignature($rawBody);

        $response = $this->call(
            'POST',
            route('api.webhooks.razorpay'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
            ],
            $rawBody
        );

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'already_processed',
            'event_id' => 'evt_already_received_999',
        ]);
    }

    public function test_webhook_with_invalid_signature_is_rejected_with_400(): void
    {
        $payload = ['id' => 'evt_forged_000', 'event' => 'payment.captured'];
        $rawBody = json_encode($payload);

        $response = $this->call(
            'POST',
            route('api.webhooks.razorpay'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => 'totally_fake_webhook_signature',
            ],
            $rawBody
        );

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid webhook signature']);
    }

    public function test_webhook_handles_payment_failed_event(): void
    {
        $payment = Payment::create([
            'tenant_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->proPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_hook_failed_111',
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
        ]);

        $payload = [
            'id' => 'evt_fail_999',
            'event' => 'payment.failed',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_failed_xyz',
                        'order_id' => 'order_hook_failed_111',
                        'error_code' => 'BAD_REQUEST_ERROR',
                        'error_description' => 'Card expired',
                    ],
                ],
            ],
        ];

        $rawBody = json_encode($payload);
        $signature = $this->razorpayService->generateWebhookSignature($rawBody);

        $response = $this->call(
            'POST',
            route('api.webhooks.razorpay'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
            ],
            $rawBody
        );

        $response->assertStatus(200);

        $payment->refresh();
        $this->assertEquals(PaymentStatus::Failed->value, $payment->status->value);
        $this->assertEquals('BAD_REQUEST_ERROR', $payment->error_code);
        $this->assertEquals('Card expired', $payment->error_description);
    }

    public function test_showroom_owner_can_view_payments_financial_ledger_with_kpis(): void
    {
        $this->activateSubscriptionFor($this->showroomA);

        Payment::create([
            'tenant_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->proPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_kpi_1',
            'razorpay_payment_id' => 'pay_kpi_1',
            'status' => PaymentStatus::Captured->value,
            'signature_verified' => true,
        ]);

        Payment::create([
            'tenant_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->starterPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 1999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_kpi_2',
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
        ]);

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.payments.index', $this->showroomA));

        $response->assertStatus(200);
        $response->assertViewIs('showrooms.manage.payments.index');
        $response->assertSeeText('Payments & Financial Ledger');
        $response->assertSee('4,999');
        $response->assertSeeText('order_kpi_1');
        $response->assertSeeText('order_kpi_2');
    }

    public function test_showroom_owner_can_view_payment_receipt(): void
    {
        $this->activateSubscriptionFor($this->showroomA);

        $payment = Payment::create([
            'tenant_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->proPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_rcpt_100',
            'razorpay_payment_id' => 'pay_rcpt_100',
            'status' => PaymentStatus::Captured->value,
            'signature_verified' => true,
            'method' => 'card',
        ]);

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.payments.show', [
            'showroom' => $this->showroomA,
            'payment' => $payment,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('showrooms.manage.payments.show');
        $response->assertSeeText('Payment Receipt');
        $response->assertSeeText('HMAC-SHA256 Cryptographically Verified');
        $response->assertSeeText('order_rcpt_100');
        $response->assertSeeText('pay_rcpt_100');
    }

    public function test_showroom_owner_cannot_view_payment_receipt_of_another_showroom(): void
    {
        $this->activateSubscriptionFor($this->showroomA);

        $paymentB = Payment::create([
            'tenant_id' => $this->showroomB->id,
            'user_id' => $this->ownerB->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->proPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_b_secure',
            'status' => PaymentStatus::Captured->value,
        ]);

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.payments.show', [
            'showroom' => $this->showroomA,
            'payment' => $paymentB,
        ]));

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_view_payments_ledger(): void
    {
        $response = $this->get(route('showrooms.manage.payments.index', $this->showroomA));

        $response->assertRedirect(route('login'));
    }
}
