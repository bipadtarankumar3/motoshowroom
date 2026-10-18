<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentProcessingService;
use App\Services\RazorpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function __construct(
        protected RazorpayService $razorpay,
        protected PaymentProcessingService $paymentProcessor
    ) {}

    /**
     * Handle incoming asynchronous webhook callbacks from Razorpay.
     */
    public function handle(Request $request): JsonResponse
    {
        $signature = $request->header('X-Razorpay-Signature');
        $rawPayload = $request->getContent();

        // 1. Verify HMAC-SHA256 signature
        if (empty($signature) || ! $this->razorpay->verifyWebhookSignature($rawPayload, $signature)) {
            Log::warning('Razorpay Webhook rejected: Invalid HMAC signature.', [
                'ip' => $request->ip(),
                'signature' => $signature,
            ]);

            return response()->json([
                'error' => 'Invalid webhook signature',
            ], 400);
        }

        $payload = json_decode($rawPayload, true) ?? [];
        $eventId = $payload['event_id'] ?? ($payload['id'] ?? null);
        $eventType = $payload['event'] ?? 'unknown';

        if (empty($eventId)) {
            return response()->json([
                'error' => 'Missing event identifier',
            ], 400);
        }

        // 2. Process with Idempotency Guard
        try {
            $result = $this->paymentProcessor->processWebhook($eventId, $eventType, $payload);

            return response()->json($result, 200);
        } catch (\Throwable $e) {
            Log::error('Razorpay Webhook Processing Failed: '.$e->getMessage(), [
                'event_id' => $eventId,
                'event_type' => $eventType,
            ]);

            return response()->json([
                'error' => 'Webhook processing failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
