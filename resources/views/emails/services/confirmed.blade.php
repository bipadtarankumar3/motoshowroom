@extends('emails.layouts.master')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div style="margin-bottom: 20px;">
                <span class="badge badge-emerald">Appointment Confirmed</span>
            </div>

            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0;">
                Service Appointment Confirmed
            </h1>

            <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 20px 0;">
                Dear <strong style="color: #0f172a;">{{ $booking->customer_name }}</strong>, your service appointment at <strong style="color: #0f172a;">{{ $showroom->name }}</strong> has been confirmed. Our certified workshop technicians are ready for your motorcycle.
            </p>

            <table class="details-table" role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="label">Booking Number</td>
                    <td class="value mono" style="color: #059669;">#{{ $booking->booking_number }}</td>
                </tr>
                <tr>
                    <td class="label">Date & Time Slot</td>
                    <td class="value">{{ $booking->booking_date->format('M d, Y') }} @ {{ date('h:i A', strtotime($booking->time_slot)) }}</td>
                </tr>
                <tr>
                    <td class="label">Vehicle Model</td>
                    <td class="value">{{ $booking->bike_make_model }}</td>
                </tr>
                <tr>
                    <td class="label">Registration Plate</td>
                    <td class="value mono">{{ $booking->registration_number }}</td>
                </tr>
                <tr>
                    <td class="label">Package Type</td>
                    <td class="value">{{ $booking->service_type->label() }}</td>
                </tr>
                @if ($booking->bay_number)
                    <tr>
                        <td class="label">Reserved Service Bay</td>
                        <td class="value mono">{{ $booking->bay_number }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Estimated Cost</td>
                    <td class="value mono">₹{{ number_format($booking->estimated_cost) }}</td>
                </tr>
            </table>

            <div style="margin-top: 24px; text-align: center;">
                <a href="{{ route('showrooms.services.track', ['showroom' => $showroom, 'booking' => $booking->booking_number]) }}" class="btn-primary">
                    Live Workshop Tracker
                </a>
            </div>

            <div style="margin-top: 20px; text-align: center;">
                <p style="font-size: 12px; color: #64748b; margin: 0;">
                    Please arrive 10 minutes prior to your scheduled time slot with your vehicle registration card and keys.
                </p>
            </div>
        </td>
    </tr>
</table>
@endsection
