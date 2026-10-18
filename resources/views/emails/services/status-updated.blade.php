@extends('emails.layouts.master')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div style="margin-bottom: 20px;">
                @if ($booking->isReady() || $booking->isCompleted())
                    <span class="badge badge-emerald">Ready for Delivery</span>
                @else
                    <span class="badge badge-sky">Status Updated</span>
                @endif
            </div>

            <h1 style="font-size: 22px; font-weight: 800; color: #ffffff; margin: 0 0 12px 0;">
                @if ($booking->isReady() || $booking->isCompleted())
                    Your Motorcycle is Ready for Pickup! 🏁
                @else
                    Service Status Update: {{ $booking->status->label() }}
                @endif
            </h1>

            <p style="font-size: 14px; color: #94a3b8; line-height: 1.6; margin: 0 0 20px 0;">
                Dear <strong style="color: #ffffff;">{{ $booking->customer_name }}</strong>, your motorcycle service status for <strong style="color: #ffffff;">{{ $booking->bike_make_model }} ({{ $booking->registration_number }})</strong> has been updated to <strong style="color: #38bdf8;">{{ $booking->status->label() }}</strong>.
            </p>

            <table class="details-table" role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="label">Booking Number</td>
                    <td class="value mono">#{{ $booking->booking_number }}</td>
                </tr>
                <tr>
                    <td class="label">Current Status</td>
                    <td class="value" style="color: #38bdf8;">{{ $booking->status->label() }}</td>
                </tr>
                <tr>
                    <td class="label">Service Type</td>
                    <td class="value">{{ $booking->service_type->label() }}</td>
                </tr>
                @if ($booking->bay_number)
                    <tr>
                        <td class="label">Workshop Bay</td>
                        <td class="value mono">{{ $booking->bay_number }}</td>
                    </tr>
                @endif
                @if ($booking->final_cost)
                    <tr>
                        <td class="label">Final Invoice Amount</td>
                        <td class="value mono" style="color: #34d399; font-size: 15px;">₹{{ number_format($booking->final_cost) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Payment Status</td>
                        <td class="value">
                            @if ($booking->isPaid())
                                <span style="color: #34d399; font-weight: bold;">PAID / CAPTURED</span>
                            @else
                                <span style="color: #fbbf24; font-weight: bold;">PAYMENT DUE</span>
                            @endif
                        </td>
                    </tr>
                @endif
                @if ($booking->technician_notes)
                    <tr>
                        <td class="label">Technician Notes</td>
                        <td class="value" style="font-size: 12px; color: #cbd5e1; font-weight: 400;">
                            "{{ $booking->technician_notes }}"
                        </td>
                    </tr>
                @endif
            </table>

            <div style="margin-top: 24px; text-align: center;">
                @if (($booking->isReady() || $booking->isCompleted()) && ! $booking->isPaid())
                    <a href="{{ route('showrooms.services.checkout', ['showroom' => $showroom, 'booking' => $booking]) }}" class="btn-primary" style="margin-right: 8px;">
                        Pay Invoice Online (Razorpay)
                    </a>
                @endif
                <a href="{{ route('showrooms.services.track', ['showroom' => $showroom, 'booking' => $booking->booking_number]) }}" class="btn-secondary">
                    View Live Tracker
                </a>
            </div>
        </td>
    </tr>
</table>
@endsection
