<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class RazorpayService
{
    protected string $keyId;

    protected string $keySecret;

    protected string $webhookSecret;

    protected ?Api $api = null;

    public function __construct()
    {
        $this->keyId = config('services.razorpay.key_id', 'rzp_test_placeholder');
        $this->keySecret = config('services.razorpay.key_secret', 'rzp_secret_placeholder');
        $this->webhookSecret = config('services.razorpay.webhook_secret', 'rzp_webhook_secret_placeholder');

        if ($this->hasLiveCredentials()) {
            $this->api = new Api($this->keyId, $this->keySecret);
        }
    }

    /**
     * Check if live or real Razorpay API keys are configured.
     */
    public function hasLiveCredentials(): bool
    {
        return ! empty($this->keyId)
            && ! empty($this->keySecret)
            && ! str_contains($this->keyId, 'placeholder')
            && ! app()->environment('testing');
    }

    /**
     * Create a Razorpay Order.
     *
     * @param  array<string, mixed>  $notes
     * @return array<string, mixed>
     */
    public function createOrder(float $amount, string $receipt, array $notes = []): array
    {
        $amountPaise = (int) round($amount * 100);

        if ($this->hasLiveCredentials() && $this->api) {
            try {
                $order = $this->api->order->create([
                    'receipt' => $receipt,
                    'amount' => $amountPaise,
                    'currency' => 'INR',
                    'notes' => $notes,
                ]);

                return $order->toArray();
            } catch (Exception $e) {
                Log::error('Razorpay Order Creation Failed: '.$e->getMessage(), [
                    'receipt' => $receipt,
                    'amount' => $amount,
                ]);
                throw $e;
            }
        }

        // Mock Order Generation for Sandbox / Testing / Local Development
        return [
            'id' => 'order_'.Str::random(14),
            'entity' => 'order',
            'amount' => $amountPaise,
            'amount_paid' => 0,
            'amount_due' => $amountPaise,
            'currency' => 'INR',
            'receipt' => $receipt,
            'status' => 'created',
            'notes' => $notes,
            'created_at' => time(),
        ];
    }

    /**
     * Verify server-side HMAC-SHA256 signature for client payment response.
     */
    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        if (empty($orderId) || empty($paymentId) || empty($signature)) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', "{$orderId}|{$paymentId}", $this->keySecret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate valid signature for an order and payment pair (used in tests and simulation).
     */
    public function generateSignature(string $orderId, string $paymentId): string
    {
        return hash_hmac('sha256', "{$orderId}|{$paymentId}", $this->keySecret);
    }

    /**
     * Verify webhook payload against the X-Razorpay-Signature header.
     */
    public function verifyWebhookSignature(string $rawPayload, string $signature): bool
    {
        if (empty($rawPayload) || empty($signature)) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $rawPayload, $this->webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate valid webhook signature for a payload (used in tests and simulation).
     */
    public function generateWebhookSignature(string $rawPayload): string
    {
        return hash_hmac('sha256', $rawPayload, $this->webhookSecret);
    }

    /**
     * Get the configured public Razorpay Key ID for frontend checkout.
     */
    public function getKeyId(): string
    {
        return $this->keyId;
    }
}
