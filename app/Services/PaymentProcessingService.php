<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Enquiry;
use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\WebhookEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentProcessingService
{
    public function __construct(
        protected RazorpayService $razorpay,
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Initiate a Razorpay Order and create a pending Payment record for SaaS subscription.
     */
    public function initiateSubscriptionPayment(
        Showroom $showroom,
        SubscriptionPlan $plan,
        User $user,
        string $billingInterval = 'monthly'
    ): Payment {
        $amount = $billingInterval === 'annual'
            ? (float) ($plan->annual_price ?: ($plan->monthly_price * 10))
            : (float) $plan->monthly_price;

        $receipt = 'SUB-'.$showroom->id.'-'.time();

        $notes = [
            'tenant_id' => (string) $showroom->id,
            'plan_id' => (string) $plan->id,
            'plan_slug' => $plan->slug,
            'billing_interval' => $billingInterval,
            'user_id' => (string) $user->id,
            'showroom_name' => $showroom->name,
        ];

        $order = $this->razorpay->createOrder($amount, $receipt, $notes);

        return Payment::create([
            'tenant_id' => $showroom->id,
            'user_id' => $user->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => $plan->id,
            'purpose' => 'subscription_purchase',
            'amount' => $amount,
            'currency' => 'INR',
            'razorpay_order_id' => $order['id'],
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
            'notes' => $notes,
        ]);
    }

    /**
     * Initiate a Razorpay Order and create a pending Payment record for customer workshop service.
     */
    public function initiateServicePayment(
        Showroom $showroom,
        ServiceBooking $booking,
        ?float $customAmount = null,
        ?User $user = null
    ): Payment {
        $amount = $customAmount ?? (float) ($booking->final_cost ?? $booking->estimated_cost);
        $receipt = 'SRV-'.$booking->id.'-'.time();

        $notes = [
            'tenant_id' => (string) $showroom->id,
            'booking_id' => (string) $booking->id,
            'booking_number' => $booking->booking_number,
            'registration_number' => $booking->registration_number,
            'customer_name' => $booking->customer_name,
        ];

        $order = $this->razorpay->createOrder($amount, $receipt, $notes);

        return Payment::create([
            'tenant_id' => $showroom->id,
            'user_id' => $user?->id ?? $booking->user_id,
            'payable_type' => ServiceBooking::class,
            'payable_id' => $booking->id,
            'purpose' => 'service_invoice',
            'amount' => $amount,
            'currency' => 'INR',
            'razorpay_order_id' => $order['id'],
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
            'notes' => $notes,
        ]);
    }

    /**
     * Initiate a Razorpay Order and create a pending Payment record for customer motorcycle purchase reservation.
     */
    public function initiateBookingPayment(
        Showroom $showroom,
        Enquiry $enquiry,
        float $amount = 10000.0,
        ?User $user = null
    ): Payment {
        $receipt = 'BKG-'.$enquiry->id.'-'.time();
        $bikeName = $enquiry->bike?->name ?? 'Motorcycle Reservation';

        $notes = [
            'tenant_id' => (string) $showroom->id,
            'enquiry_id' => (string) $enquiry->id,
            'enquiry_number' => $enquiry->enquiry_number,
            'bike_name' => $bikeName,
            'customer_name' => $enquiry->customer_name,
            'purpose' => 'vehicle_reservation_advance',
        ];

        $order = $this->razorpay->createOrder($amount, $receipt, $notes);

        return Payment::create([
            'tenant_id' => $showroom->id,
            'user_id' => $user?->id ?? $enquiry->user_id,
            'payable_type' => Enquiry::class,
            'payable_id' => $enquiry->id,
            'purpose' => 'vehicle_reservation',
            'amount' => $amount,
            'currency' => 'INR',
            'razorpay_order_id' => $order['id'],
            'status' => PaymentStatus::Pending->value,
            'signature_verified' => false,
            'notes' => $notes,
        ]);
    }

    /**
     * Confirm a payment via client response using HMAC-SHA256 signature verification.
     */
    public function confirmPayment(
        Payment $payment,
        string $razorpayPaymentId,
        string $razorpaySignature,
        ?string $method = 'upi'
    ): bool {
        // 1. Verify HMAC Signature
        $isValid = $this->razorpay->verifySignature(
            $payment->razorpay_order_id,
            $razorpayPaymentId,
            $razorpaySignature
        );

        if (! $isValid) {
            $payment->update([
                'status' => PaymentStatus::Failed->value,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
                'signature_verified' => false,
                'error_code' => 'BAD_SIGNATURE',
                'error_description' => 'HMAC-SHA256 signature mismatch detected.',
            ]);

            AuditLogger::log(
                'payment.failed',
                $payment,
                null,
                ['reason' => 'signature_mismatch', 'order_id' => $payment->razorpay_order_id],
                $payment->tenant_id
            );

            return false;
        }

        // 2. Fulfill Payment State Transition inside Transaction
        $result = DB::transaction(function () use ($payment, $razorpayPaymentId, $razorpaySignature, $method): bool {
            $payment->update([
                'status' => PaymentStatus::Captured->value,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
                'signature_verified' => true,
                'method' => $method,
            ]);

            // Fulfill Subscription Plan Purchase
            if ($payment->payable_type === SubscriptionPlan::class || str_starts_with($payment->purpose, 'subscription')) {
                $showroom = $payment->showroom;
                $plan = SubscriptionPlan::find($payment->payable_id);
                $interval = $payment->notes['billing_interval'] ?? 'monthly';

                if ($showroom && $plan) {
                    $this->subscriptionService->subscribe(
                        $showroom,
                        $plan,
                        $interval,
                        $payment->user_id,
                        $razorpayPaymentId
                    );
                }
            }

            // Fulfill Service Booking Invoice
            if ($payment->payable_type === ServiceBooking::class) {
                /** @var ServiceBooking|null $booking */
                $booking = ServiceBooking::find($payment->payable_id);
                if ($booking) {
                    $booking->update([
                        'payment_status' => PaymentStatus::Captured->value,
                        'final_cost' => $payment->amount,
                    ]);
                }
            }

            AuditLogger::log(
                'payment.captured',
                $payment,
                ['status' => 'pending'],
                [
                    'status' => 'captured',
                    'amount' => $payment->amount,
                    'razorpay_payment_id' => $razorpayPaymentId,
                ],
                $payment->tenant_id
            );

            return true;
        });

        if ($result) {
            app(ShowroomNotificationService::class)->notifyPaymentCaptured($payment->fresh());
        }

        return $result;
    }

    /**
     * Process an incoming Razorpay webhook event with idempotency verification.
     *
     * @param  array<string, mixed>  $payload
     * @return array{status: string, event_id: string, message?: string}
     */
    public function processWebhook(string $eventId, string $eventType, array $payload): array
    {
        // 1. Idempotency Check: Return immediately if already processed
        if (WebhookEvent::where('event_id', $eventId)->exists()) {
            return [
                'status' => 'already_processed',
                'event_id' => $eventId,
                'message' => 'Event already ingested and processed.',
            ];
        }

        // 2. Register in Idempotency Ledger
        $webhookEvent = WebhookEvent::create([
            'event_id' => $eventId,
            'event_type' => $eventType,
            'payload' => $payload,
            'processed' => false,
        ]);

        try {
            DB::transaction(function () use ($eventType, $payload): void {
                if ($eventType === 'payment.captured' || $eventType === 'order.paid') {
                    $paymentEntity = $payload['payload']['payment']['entity'] ?? null;
                    $orderId = $paymentEntity['order_id'] ?? ($payload['payload']['order']['entity']['id'] ?? null);
                    $paymentId = $paymentEntity['id'] ?? null;
                    $method = $paymentEntity['method'] ?? 'webhook';

                    if ($orderId) {
                        $payment = Payment::where('razorpay_order_id', $orderId)->first();

                        if ($payment && ! $payment->isCaptured()) {
                            $payment->update([
                                'status' => PaymentStatus::Captured->value,
                                'razorpay_payment_id' => $paymentId ?? $payment->razorpay_payment_id,
                                'signature_verified' => true,
                                'method' => $method,
                            ]);

                            // Fulfill Subscription if pending
                            if ($payment->payable_type === SubscriptionPlan::class && $payment->showroom) {
                                $plan = SubscriptionPlan::find($payment->payable_id);
                                if ($plan) {
                                    $interval = $payment->notes['billing_interval'] ?? 'monthly';
                                    $this->subscriptionService->subscribe($payment->showroom, $plan, $interval, $payment->user_id, $paymentId);
                                }
                            }

                            // Fulfill Service Booking if pending
                            if ($payment->payable_type === ServiceBooking::class) {
                                $booking = ServiceBooking::find($payment->payable_id);
                                if ($booking) {
                                    $booking->update([
                                        'payment_status' => PaymentStatus::Captured->value,
                                        'final_cost' => $payment->amount,
                                    ]);
                                }
                            }

                            app(ShowroomNotificationService::class)->notifyPaymentCaptured($payment->fresh());
                        }
                    }
                } elseif ($eventType === 'payment.failed') {
                    $paymentEntity = $payload['payload']['payment']['entity'] ?? null;
                    $orderId = $paymentEntity['order_id'] ?? null;

                    if ($orderId) {
                        $payment = Payment::where('razorpay_order_id', $orderId)->first();
                        if ($payment && ! $payment->isCaptured()) {
                            $payment->update([
                                'status' => PaymentStatus::Failed->value,
                                'error_code' => $paymentEntity['error_code'] ?? 'PAYMENT_FAILED',
                                'error_description' => $paymentEntity['error_description'] ?? 'Payment failed via webhook.',
                            ]);
                        }
                    }
                }
            });

            $webhookEvent->update([
                'processed' => true,
                'processed_at' => now(),
            ]);

            return [
                'status' => 'processed',
                'event_id' => $eventId,
            ];
        } catch (\Throwable $e) {
            Log::error('Webhook processing error: '.$e->getMessage(), [
                'event_id' => $eventId,
                'event_type' => $eventType,
            ]);

            $webhookEvent->update([
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
