<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Showroom;
use App\Services\AuditLogger;
use App\Services\PaymentProcessingService;
use App\Services\RazorpayService;
use App\Services\ScratchCardService;
use App\Services\ShowroomNotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PublicEnquiryController extends Controller
{
    /**
     * Display public enquiry and test-ride booking form for this showroom.
     */
    public function create(Request $request, Showroom $showroom): View
    {
        $bikeSlug = $request->query('bike');
        $selectedBike = null;
        if (! empty($bikeSlug)) {
            $selectedBike = $showroom->publishedBikes()->where('slug', $bikeSlug)->first();
        }

        $type = $request->query('type', EnquiryType::Purchase->value);
        if (! in_array($type, array_column(EnquiryType::cases(), 'value'), true)) {
            $type = EnquiryType::Purchase->value;
        }

        $bikes = $showroom->publishedBikes()->orderBy('name')->get();

        $scratchParticipation = null;
        if (session('scratch_token')) {
            $scratchParticipation = $showroom->scratchCardParticipations()
                ->with(['prize', 'campaign'])
                ->where('participation_token', session('scratch_token'))
                ->first();
        }

        return view('showrooms.enquiries.create', compact('showroom', 'selectedBike', 'bikes', 'type', 'scratchParticipation'));
    }

    /**
     * Store a newly submitted customer enquiry or test-ride request.
     */
    public function store(Request $request, Showroom $showroom): RedirectResponse|JsonResponse
    {
        $isPurchase = $request->input('type') === EnquiryType::Purchase->value;
        $isBookingReservation = $isPurchase && ($request->filled('payment_method') || $request->filled('booking_amount') || $request->has('terms_accepted'));

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'type' => ['required', 'string', Rule::in(array_column(EnquiryType::cases(), 'value'))],
            'bike_id' => [
                'nullable',
                Rule::exists('bikes', 'id')->where('tenant_id', $showroom->id),
            ],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'preferred_time_slot' => ['nullable', 'string', 'max:50'],
            'driving_license_number' => ['nullable', 'string', 'max:50'],
            'customer_notes' => ['nullable', 'string', 'max:1500'],
            'message' => ['nullable', 'string', 'max:1500'],
            'delivery_method' => ['nullable', 'string', 'max:100'],
            'payment_method' => [$isBookingReservation ? 'required' : 'nullable', 'string', 'in:offline,razorpay'],
            'booking_amount' => ['nullable', 'numeric', 'min:0'],
            'terms_accepted' => [$isBookingReservation ? 'accepted' : 'nullable'],
        ]);

        $notes = $validated['customer_notes'] ?? $validated['message'] ?? null;
        if (! empty($validated['delivery_method'])) {
            $deliveryLabel = str_replace('_', ' ', ucwords($validated['delivery_method'], '_'));
            $notes = "Delivery Method: {$deliveryLabel}".($notes ? "\n".$notes : '');
        }

        $paymentMethod = $isBookingReservation ? ($validated['payment_method'] ?? 'offline') : null;
        $bookingAmount = $isBookingReservation ? (isset($validated['booking_amount']) ? (float) $validated['booking_amount'] : 10000.0) : null;
        $termsAccepted = $request->boolean('terms_accepted');

        $status = EnquiryStatus::New->value;
        $paymentStatus = 'unpaid';

        if ($isBookingReservation) {
            if ($paymentMethod === 'razorpay') {
                $status = EnquiryStatus::BookingPending->value;
                $paymentStatus = 'pending';
            } else {
                $status = EnquiryStatus::Booked->value;
                $paymentStatus = 'unpaid';
            }
        }

        $enquiry = $showroom->enquiries()->create([
            'user_id' => Auth::id(),
            'bike_id' => $validated['bike_id'] ?? null,
            'type' => $validated['type'],
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'preferred_date' => $validated['preferred_date'] ?? null,
            'preferred_time_slot' => $validated['preferred_time_slot'] ?? null,
            'driving_license_number' => $validated['driving_license_number'] ?? null,
            'customer_notes' => $notes,
            'status' => $status,
            'booking_amount' => $bookingAmount,
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'terms_accepted' => $termsAccepted,
        ]);

        AuditLogger::log(
            'enquiry.created',
            $enquiry,
            null,
            [
                'enquiry_number' => $enquiry->enquiry_number,
                'customer_name' => $enquiry->customer_name,
                'type' => $enquiry->type->value,
                'bike_id' => $enquiry->bike_id,
                'payment_method' => $paymentMethod,
                'booking_amount' => $bookingAmount,
            ],
            $showroom->id,
            Auth::user()
        );

        app(ShowroomNotificationService::class)->notifyEnquiryCreated($enquiry);

        $typeLabel = $enquiry->type->label();

        $scratchParticipation = null;
        if ($showroom->canAccessFeature('has_scratch_cards')) {
            $scratchParticipation = app(ScratchCardService::class)->autoIssueForBikeEnquiry($enquiry);
        }

        // If Razorpay online payment was selected for purchase reservation
        if ($isBookingReservation && $paymentMethod === 'razorpay') {
            $paymentProcessor = app(PaymentProcessingService::class);
            $payment = $paymentProcessor->initiateBookingPayment($showroom, $enquiry, (float) ($bookingAmount ?? 10000.0), Auth::user());
            $keyId = $showroom->getRazorpayKeyId();

            $razorpay = app(RazorpayService::class);
            $simulatedPaymentId = 'pay_'.substr(md5($payment->razorpay_order_id.'sim'), 0, 14);
            $simulatedSignature = $razorpay->generateSignature($payment->razorpay_order_id, $simulatedPaymentId, $showroom->getRazorpayKeySecret());

            if ($request->expectsJson() || $request->ajax() || $request->boolean('ajax')) {
                return response()->json([
                    'success' => true,
                    'key_id' => $keyId,
                    'amount' => $payment->amount,
                    'amount_paisa' => (int) round($payment->amount * 100),
                    'currency' => $payment->currency,
                    'payment_id' => $payment->id,
                    'razorpay_order_id' => $payment->razorpay_order_id,
                    'enquiry_id' => $enquiry->id,
                    'enquiry_number' => $enquiry->enquiry_number,
                    'customer_name' => $enquiry->customer_name,
                    'customer_email' => $enquiry->customer_email,
                    'customer_phone' => $enquiry->customer_phone,
                    'bike_name' => $enquiry->bike?->name ?? 'Motorcycle',
                    'verify_url' => route('showrooms.payments.verify', $showroom),
                    'simulated_payment_id' => $simulatedPaymentId,
                    'simulated_signature' => $simulatedSignature,
                ]);
            }

            return redirect()->route('showrooms.bikes.booking.checkout', [$showroom, $enquiry]);
        }

        // If Normal Booking (Pay at Showroom) was selected, redirect to dedicated confirmation screen
        if ($isBookingReservation) {
            $successMsg = "Thank you, {$enquiry->customer_name}! Your vehicle reservation (#{$enquiry->enquiry_number}) is registered. Please visit our dealership within 48 hours to inspect your machine and complete the refundable advance.";
            $redirect = redirect()->route('showrooms.booking.success', [$showroom, $enquiry])->with('success', $successMsg);

            if ($scratchParticipation) {
                $redirect->with('scratch_token', $scratchParticipation->participation_token);
            }

            return $redirect;
        }

        $successMsg = "Thank you, {$enquiry->customer_name}! Your {$typeLabel} (#{$enquiry->enquiry_number}) has been registered. Our dealership team will get in touch with you shortly.";
        $redirect = redirect()->back()->with('success', $successMsg);

        if ($scratchParticipation) {
            $redirect->with('scratch_token', $scratchParticipation->participation_token);
        }

        return $redirect;
    }
}
