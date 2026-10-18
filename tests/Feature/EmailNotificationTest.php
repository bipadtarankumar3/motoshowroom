<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\EnquiryType;
use App\Enums\PaymentStatus;
use App\Enums\ServiceType;
use App\Enums\SubscriptionStatus;
use App\Mail\EnquiryReceivedMail;
use App\Mail\NewEnquiryAlertMail;
use App\Mail\PaymentReceiptMail;
use App\Mail\ServiceBookingConfirmedMail;
use App\Mail\ServiceStatusUpdatedMail;
use App\Mail\SubscriptionExpiringMail;
use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\RazorpayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected Showroom $showroom;

    protected SubscriptionPlan $proPlan;

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

        $this->showroom = Showroom::create([
            'name' => 'Apex Moto Hub',
            'slug' => 'apex-moto',
            'email' => 'contact@apex.example',
            'phone' => '+91 8023456789',
            'city' => 'Bangalore',
            'status' => 'active',
            'public_website_enabled' => true,
            'service_bays_count' => 3,
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroom->id,
            'user_id' => $this->owner->id,
            'role' => 'owner',
        ]);

        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Apex Pro',
            'slug' => 'apex-pro',
            'monthly_price' => 4999.00,
            'annual_price' => 49990.00,
            'currency' => 'INR',
            'max_bike_listings' => 50,
            'max_staff_accounts' => 10,
            'is_active' => true,
        ]);

        Subscription::create([
            'showroom_id' => $this->showroom->id,
            'plan_id' => $this->proPlan->id,
            'status' => SubscriptionStatus::Active->value,
            'billing_interval' => 'monthly',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);
    }

    public function test_submitting_customer_enquiry_dispatches_customer_acknowledgement_and_staff_alert(): void
    {
        Mail::fake();

        $response = $this->post(route('showrooms.enquiries.store', $this->showroom), [
            'customer_name' => 'Vikram Seth',
            'customer_email' => 'vikram@customer.example',
            'customer_phone' => '+91 9876543210',
            'type' => EnquiryType::TestRide->value,
            'preferred_date' => now()->addDays(2)->toDateString(),
            'preferred_time_slot' => '10:00 AM - 11:00 AM',
            'customer_notes' => 'Please confirm helmet requirement.',
        ]);

        $response->assertSessionHas('success');

        // Verify customer acknowledgement email
        Mail::assertQueued(EnquiryReceivedMail::class, function (EnquiryReceivedMail $mail) {
            return $mail->hasTo('vikram@customer.example') &&
                $mail->enquiry->customer_name === 'Vikram Seth' &&
                $mail->showroom->id === $this->showroom->id;
        });

        // Verify dealership staff alert email
        Mail::assertQueued(NewEnquiryAlertMail::class, function (NewEnquiryAlertMail $mail) {
            return ($mail->hasTo('contact@apex.example') || $mail->hasTo('rajesh@apex.example')) &&
                $mail->enquiry->customer_name === 'Vikram Seth';
        });
    }

    public function test_scheduling_service_booking_dispatches_booking_confirmed_email(): void
    {
        Mail::fake();

        $response = $this->post(route('showrooms.services.store', $this->showroom), [
            'customer_name' => 'Rohit Verma',
            'customer_email' => 'rohit@customer.example',
            'customer_phone' => '+91 9988776655',
            'bike_make_model' => 'Yamaha MT-15 V2',
            'registration_number' => 'KA-01-AB-1234',
            'service_type' => ServiceType::GeneralService->value,
            'booking_date' => now()->addDays(2)->toDateString(),
            'time_slot' => '09:00 - 11:00',
            'customer_notes' => 'Chain lube and oil change required.',
        ]);

        $response->assertSessionHas('success');

        Mail::assertQueued(ServiceBookingConfirmedMail::class, function (ServiceBookingConfirmedMail $mail) {
            return $mail->hasTo('rohit@customer.example') &&
                $mail->booking->customer_name === 'Rohit Verma' &&
                $mail->booking->registration_number === 'KA-01-AB-1234';
        });
    }

    public function test_updating_service_booking_status_dispatches_status_updated_email(): void
    {
        Mail::fake();

        $booking = ServiceBooking::create([
            'tenant_id' => $this->showroom->id,
            'customer_name' => 'Karan Malhotra',
            'customer_email' => 'karan@customer.example',
            'customer_phone' => '+91 9876543210',
            'bike_make_model' => 'KTM Duke 390',
            'registration_number' => 'KA-03-JJ-9999',
            'service_type' => ServiceType::GeneralService->value,
            'booking_date' => now()->toDateString(),
            'time_slot' => '09:00 - 11:00',
            'status' => BookingStatus::InService->value,
            'estimated_cost' => 3500.00,
        ]);

        $response = $this->actingAs($this->owner)->put(
            route('showrooms.manage.services.update', ['showroom' => $this->showroom, 'booking' => $booking]),
            [
                'status' => BookingStatus::ReadyForCollection->value,
                'final_cost' => 3800.00,
                'technician_notes' => 'Brake bleeding complete, ready for collection.',
            ]
        );

        $response->assertSessionHas('success');

        Mail::assertQueued(ServiceStatusUpdatedMail::class, function (ServiceStatusUpdatedMail $mail) use ($booking) {
            return $mail->hasTo('karan@customer.example') &&
                $mail->booking->id === $booking->id &&
                $mail->booking->status === BookingStatus::ReadyForCollection;
        });
    }

    public function test_payment_capture_dispatches_payment_receipt_email(): void
    {
        Mail::fake();

        $payment = Payment::create([
            'tenant_id' => $this->showroom->id,
            'user_id' => $this->owner->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->proPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_mail_test_100',
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
            'notes' => ['plan_id' => (string) $this->proPlan->id],
        ]);

        $razorpay = app(RazorpayService::class);
        $paymentId = 'pay_mail_test_100';
        $signature = $razorpay->generateSignature($payment->razorpay_order_id, $paymentId);

        $response = $this->actingAs($this->owner)->post(route('showrooms.payments.verify', $this->showroom), [
            'payment_id' => $payment->id,
            'razorpay_order_id' => $payment->razorpay_order_id,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $signature,
            'method' => 'upi',
        ]);

        $response->assertSessionHas('success');

        Mail::assertQueued(PaymentReceiptMail::class, function (PaymentReceiptMail $mail) use ($payment) {
            return $mail->hasTo('rajesh@apex.example') &&
                $mail->payment->id === $payment->id &&
                $mail->payment->status === PaymentStatus::Captured;
        });
    }

    public function test_webhook_payment_capture_dispatches_payment_receipt_email(): void
    {
        Mail::fake();

        $payment = Payment::create([
            'tenant_id' => $this->showroom->id,
            'user_id' => $this->owner->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $this->proPlan->id,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_hook_mail_200',
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
        ]);

        $payload = [
            'entity' => 'event',
            'id' => 'evt_mail_webhook_200',
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_hook_mail_200',
                        'order_id' => 'order_hook_mail_200',
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
        $signature = app(RazorpayService::class)->generateWebhookSignature($rawBody);

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

        Mail::assertQueued(PaymentReceiptMail::class, function (PaymentReceiptMail $mail) use ($payment) {
            return $mail->hasTo('rajesh@apex.example') &&
                $mail->payment->id === $payment->id;
        });
    }

    public function test_artisan_command_sends_subscription_expiry_reminders(): void
    {
        Mail::fake();

        // Expiring in 4 days
        $expiringSubscription = Subscription::create([
            'showroom_id' => $this->showroom->id,
            'plan_id' => $this->proPlan->id,
            'status' => SubscriptionStatus::Active->value,
            'billing_interval' => 'monthly',
            'current_period_start' => now()->subDays(26),
            'current_period_end' => now()->addDays(4),
        ]);

        $exitCode = Artisan::call('subscriptions:send-expiry-reminders', ['--days' => 7]);
        $this->assertEquals(0, $exitCode);

        Mail::assertQueued(SubscriptionExpiringMail::class, function (SubscriptionExpiringMail $mail) {
            return ($mail->hasTo('rajesh@apex.example') || $mail->hasTo('contact@apex.example')) &&
                $mail->showroom->id === $this->showroom->id;
        });
    }

    public function test_showroom_owner_can_view_email_previews_index_and_rendered_html(): void
    {
        $response = $this->actingAs($this->owner)->get(route('showrooms.manage.email-previews.index', $this->showroom));
        $response->assertStatus(200);
        $response->assertViewIs('showrooms.manage.email-previews.index');
        $response->assertSeeText('Transactional Email System');
        $response->assertSeeText('Customer Enquiry Acknowledgement');
        $response->assertSeeText('Service Appointment Confirmation');

        // Test each HTML preview rendered
        $previewTypes = [
            'enquiry_received',
            'enquiry_alert',
            'service_confirmed',
            'service_status_updated',
            'payment_receipt',
            'subscription_expiring',
        ];

        foreach ($previewTypes as $type) {
            $previewResponse = $this->actingAs($this->owner)->get(
                route('showrooms.manage.email-previews.show', ['showroom' => $this->showroom, 'type' => $type])
            );
            $previewResponse->assertStatus(200);
            $previewResponse->assertSeeText($this->showroom->name);
        }
    }

    public function test_unauthenticated_user_cannot_access_email_previews(): void
    {
        $response = $this->get(route('showrooms.manage.email-previews.index', $this->showroom));
        $response->assertRedirect(route('login'));
    }
}
