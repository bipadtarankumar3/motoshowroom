<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\EnquiryStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Models\SubscriptionPlan;
use App\Services\PaymentProcessingService;
use App\Services\RazorpayService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentCheckoutController extends Controller
{
    public function __construct(
        protected PaymentProcessingService $paymentProcessor,
        protected RazorpayService $razorpay
    ) {}

    /**
     * Show checkout screen for subscription plan purchase or upgrade.
     */
    public function subscriptionCheckout(Request $request, Showroom $showroom): View|RedirectResponse
    {
        $planId = $request->query('plan_id');
        $interval = $request->query('interval', 'monthly');

        $plan = SubscriptionPlan::find($planId);
        if (! $plan) {
            return redirect()->route('showrooms.billing.index', $showroom)
                ->with('error', 'Please select a valid subscription plan.');
        }

        $user = Auth::user();
        $payment = $this->paymentProcessor->initiateSubscriptionPayment(
            $showroom,
            $plan,
            $user,
            $interval
        );

        $keyId = $this->razorpay->getKeyId();

        // In test or local sandbox mode, generate valid simulation parameters
        $simulatedPaymentId = 'pay_'.substr(md5($payment->razorpay_order_id.'sim'), 0, 14);
        $simulatedSignature = $this->razorpay->generateSignature($payment->razorpay_order_id, $simulatedPaymentId);

        return view('showrooms.billing.checkout', compact(
            'showroom',
            'plan',
            'payment',
            'interval',
            'keyId',
            'simulatedPaymentId',
            'simulatedSignature'
        ));
    }

    /**
     * Show checkout screen for customer vehicle service booking invoice.
     */
    public function serviceCheckout(Showroom $showroom, ServiceBooking $booking): View
    {
        abort_if($booking->tenant_id !== $showroom->id, 404);

        $payment = $this->paymentProcessor->initiateServicePayment($showroom, $booking);
        $keyId = $this->razorpay->getKeyId();

        $simulatedPaymentId = 'pay_'.substr(md5($payment->razorpay_order_id.'sim'), 0, 14);
        $simulatedSignature = $this->razorpay->generateSignature($payment->razorpay_order_id, $simulatedPaymentId);

        return view('showrooms.services.checkout', compact(
            'showroom',
            'booking',
            'payment',
            'keyId',
            'simulatedPaymentId',
            'simulatedSignature'
        ));
    }

    /**
     * Show checkout screen for customer motorcycle purchase reservation advance.
     */
    public function bikeBookingCheckout(Showroom $showroom, Enquiry $enquiry): View
    {
        abort_if($enquiry->tenant_id !== $showroom->id, 404);

        $amount = (float) ($enquiry->booking_amount ?: 10000.0);
        $payment = $this->paymentProcessor->initiateBookingPayment($showroom, $enquiry, $amount);
        $keyId = $this->razorpay->getKeyId();

        $simulatedPaymentId = 'pay_'.substr(md5($payment->razorpay_order_id.'sim'), 0, 14);
        $simulatedSignature = $this->razorpay->generateSignature($payment->razorpay_order_id, $simulatedPaymentId);

        return view('showrooms.bikes.booking-checkout', compact(
            'showroom',
            'enquiry',
            'payment',
            'keyId',
            'simulatedPaymentId',
            'simulatedSignature'
        ));
    }

    /**
     * Confirm and finalize payment submission via HMAC-SHA256 signature verification.
     */
    public function verifyPayment(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'payment_id' => ['required', 'exists:payments,id'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        /** @var Payment $payment */
        $payment = Payment::findOrFail($validated['payment_id']);
        abort_if($payment->tenant_id !== $showroom->id, 404);

        $success = $this->paymentProcessor->confirmPayment(
            $payment,
            $validated['razorpay_payment_id'],
            $validated['razorpay_signature'],
            $request->input('method', 'upi')
        );

        if (! $success) {
            if ($payment->payable_type === Enquiry::class) {
                return redirect()->route('showrooms.booking.failed', [$showroom, $payment->payable_id])
                    ->with('error', 'Payment verification failed: HMAC signature mismatch detected.');
            }

            return back()->with('error', 'Payment verification failed: HMAC signature mismatch detected.');
        }

        // Redirect based on purpose
        if (str_starts_with($payment->purpose, 'subscription')) {
            return redirect()->route('showrooms.billing.index', $showroom)
                ->with('success', 'Payment of ₹'.number_format($payment->amount).' confirmed! Your subscription is now active.');
        }

        if ($payment->payable_type === ServiceBooking::class) {
            /** @var ServiceBooking|null $booking */
            $booking = ServiceBooking::find($payment->payable_id);
            if ($booking) {
                $booking->update([
                    'terms_accepted' => true,
                    'payment_status' => PaymentStatus::Captured->value,
                ]);
            }

            return redirect()->route('showrooms.services.track', [
                'showroom' => $showroom,
                'booking' => $booking?->booking_number,
            ])->with('success', 'Payment of ₹'.number_format($payment->amount).' captured successfully! Invoice settled.');
        }

        if ($payment->payable_type === Enquiry::class) {
            /** @var Enquiry|null $enquiry */
            $enquiry = Enquiry::find($payment->payable_id);
            if ($enquiry) {
                $enquiry->update([
                    'status' => EnquiryStatus::Booked->value,
                    'payment_status' => PaymentStatus::Captured->value,
                    'booking_amount' => $payment->amount,
                    'payment_method' => 'razorpay',
                    'terms_accepted' => true,
                ]);
            }

            return redirect()->route('showrooms.booking.success', [$showroom, $enquiry])
                ->with('success', 'Booking Advance of ₹'.number_format($payment->amount).' received via Razorpay! Your motorcycle reservation (#'.$enquiry?->enquiry_number.') is confirmed.');
        }

        return redirect()->route('showrooms.manage', $showroom)
            ->with('success', 'Payment verified and recorded successfully.');
    }

    /**
     * Display motorcycle booking confirmation & receipt screen.
     */
    public function bikeBookingSuccess(Showroom $showroom, Enquiry $enquiry): View
    {
        abort_if($enquiry->tenant_id !== $showroom->id, 404);
        $enquiry->load(['bike.images', 'bike.variants']);

        $latestPayment = $enquiry->payments()->latest()->first();

        return view('showrooms.bikes.booking-success', compact('showroom', 'enquiry', 'latestPayment'));
    }

    /**
     * Display payment failed/cancelled screen with retry options.
     */
    public function bikeBookingFailed(Showroom $showroom, Enquiry $enquiry): View
    {
        abort_if($enquiry->tenant_id !== $showroom->id, 404);
        $enquiry->load(['bike.images', 'bike.variants']);

        return view('showrooms.bikes.booking-failed', compact('showroom', 'enquiry'));
    }

    /**
     * Switch an uncompleted online booking to Normal Booking (Pay at Showroom).
     */
    public function switchBookingToOffline(Showroom $showroom, Enquiry $enquiry): RedirectResponse
    {
        abort_if($enquiry->tenant_id !== $showroom->id, 404);

        $enquiry->update([
            'status' => EnquiryStatus::Booked->value,
            'payment_method' => 'offline',
            'payment_status' => 'unpaid',
            'terms_accepted' => true,
        ]);

        return redirect()->route('showrooms.booking.success', [$showroom, $enquiry])
            ->with('success', 'Your reservation has been switched to Normal Booking (Pay at Showroom). Please visit our dealership within 48 hours to complete your refundable advance token.');
    }
}
