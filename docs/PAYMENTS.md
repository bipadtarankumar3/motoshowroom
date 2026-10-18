# MotoShowroom SaaS - Payment Integration Architecture (Razorpay)

## 1. Overview
The platform integrates **Razorpay** for all financial transactions:
* **SaaS Subscriptions**: Plan purchase and recurring renewals.
* **Customer Service Booking Deposits**: Optional upfront service reservation fees.
* **Motorcycle Booking Reservation Fees**: Configurable customer vehicle deposits.

---

## 2. Server-Side Payment Workflow

```
1. Customer / Owner initiates Checkout
         │
         ▼
2. Backend creates Razorpay Order (API: /orders)
   - Order ID generated & stored in `payments` with status 'pending'
   - Currency: INR, Amount in paise (integer arithmetic)
         │
         ▼
3. Frontend launches Razorpay Checkout Modal
   - User completes payment via UPI / Cards / NetBanking
         │
         ▼
4. Frontend submits payment response:
   - `razorpay_order_id`
   - `razorpay_payment_id`
   - `razorpay_signature`
         │
         ▼
5. Backend verifies HMAC-SHA256 Signature
   - If invalid -> mark payment 'failed', reject request
   - If valid -> mark payment 'captured', trigger state change inside DB Transaction
```

---

## 3. Webhook Architecture & Idempotency

### 3.1 Webhook Endpoint
* **Route**: `POST /api/webhooks/razorpay`
* **Signature Header**: `X-Razorpay-Signature`
* **Verification Algorithm**:
  ```php
  $expectedSignature = hash_hmac('sha256', $rawPayload, config('services.razorpay.webhook_secret'));
  if (!hash_equals($expectedSignature, $request->header('X-Razorpay-Signature'))) {
      abort(400, 'Invalid webhook signature');
  }
  ```

### 3.2 Idempotency Ledger (`webhook_events`)
* Every incoming webhook logs its `event_id`.
* If `WebhookEvent::where('event_id', $eventId)->exists()`, the controller immediately returns `200 OK` without re-processing.
* This guarantees safety against duplicated or out-of-order webhook deliveries.

---

## 4. Refund Handling
* Super Admins and Showroom Managers can trigger refunds via `PaymentService::refundPayment()`.
* Creates a record in `refunds` table with `razorpay_refund_id`.
* Adjusts transaction status to `refunded` or `partially_refunded`.
* Updates service booking or subscription records accordingly.

---

## 5. Sandbox & Production Configuration
In `.env`:
```env
RAZORPAY_KEY_ID=rzp_test_...
RAZORPAY_KEY_SECRET=...
RAZORPAY_WEBHOOK_SECRET=...
```
For local testing, simulated mock payment flows are available in development mode to allow end-to-end testing without external network dependencies.
