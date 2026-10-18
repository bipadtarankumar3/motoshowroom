<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Showroom;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Showroom::factory(),
            'user_id' => User::factory(),
            'payable_type' => Subscription::class,
            'payable_id' => 1,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_'.Str::random(14),
            'razorpay_payment_id' => 'pay_'.Str::random(14),
            'razorpay_signature' => hash_hmac('sha256', 'order_dummy|pay_dummy', 'secret'),
            'status' => PaymentStatus::Pending->value,
            'method' => 'upi',
            'signature_verified' => false,
            'error_code' => null,
            'error_description' => null,
            'notes' => ['plan' => 'pro'],
        ];
    }

    /**
     * State for successfully captured payments.
     */
    public function captured(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Captured->value,
            'signature_verified' => true,
        ]);
    }

    /**
     * State for failed payments.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Failed->value,
            'error_code' => 'BAD_REQUEST_ERROR',
            'error_description' => 'Payment was declined by customer bank.',
        ]);
    }
}
