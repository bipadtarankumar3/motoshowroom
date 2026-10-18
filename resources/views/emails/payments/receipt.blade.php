@extends('emails.layouts.master')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div style="margin-bottom: 20px;">
                <span class="badge badge-emerald">Payment Captured</span>
            </div>

            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0;">
                Official Payment Receipt
            </h1>

            <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 20px 0;">
                Your payment of <strong style="color: #059669; font-size: 16px;">₹{{ number_format($payment->amount) }}</strong> has been verified and captured successfully via Razorpay.
            </p>

            <table class="details-table" role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="label">Receipt Reference</td>
                    <td class="value mono" style="color: #e11d48; font-weight: bold;">#PAY-{{ str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td class="label">Purpose / Description</td>
                    <td class="value">{{ ucwords(str_replace('_', ' ', $payment->purpose)) }}</td>
                </tr>
                <tr>
                    <td class="label">Total Amount Paid</td>
                    <td class="value mono" style="color: #059669; font-size: 15px; font-weight: bold;">₹{{ number_format($payment->amount) }} {{ $payment->currency }}</td>
                </tr>
                <tr>
                    <td class="label">Razorpay Payment ID</td>
                    <td class="value mono">{{ $payment->razorpay_payment_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Razorpay Order ID</td>
                    <td class="value mono">{{ $payment->razorpay_order_id }}</td>
                </tr>
                @if ($payment->method)
                    <tr>
                        <td class="label">Payment Mode</td>
                        <td class="value" style="text-transform: uppercase;">{{ $payment->method }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Date & Time</td>
                    <td class="value">{{ $payment->updated_at?->format('M d, Y @ h:i A') ?? now()->format('M d, Y @ h:i A') }}</td>
                </tr>
                <tr>
                    <td class="label">HMAC Verification</td>
                    <td class="value" style="color: #059669; font-weight: bold;">✔ Verified Cryptographically</td>
                </tr>
            </table>

            <div style="margin-top: 24px; text-align: center;">
                @if (str_starts_with($payment->purpose, 'subscription'))
                    <a href="{{ $payment->id ? route('showrooms.manage.payments.show', ['showroom' => $showroom, 'payment' => $payment]) : route('showrooms.manage.payments.index', $showroom) }}" class="btn-primary">
                        View Printable Tax Receipt
                    </a>
                @elseif ($payment->payable_type === \App\Models\ServiceBooking::class && $payment->payable)
                    <a href="{{ route('showrooms.services.track', ['showroom' => $showroom, 'booking' => $payment->payable->booking_number]) }}" class="btn-primary">
                        View Service Booking
                    </a>
                @endif
            </div>
        </td>
    </tr>
</table>
@endsection
