@extends('emails.layouts.master')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div style="margin-bottom: 20px;">
                <span class="badge badge-rose">Enquiry Logged</span>
            </div>

            <h1 style="font-size: 22px; font-weight: 800; color: #ffffff; margin: 0 0 12px 0;">
                Hello, {{ $enquiry->customer_name }}
            </h1>

            <p style="font-size: 14px; color: #94a3b8; line-height: 1.6; margin: 0 0 20px 0;">
                Thank you for contacting <strong style="color: #ffffff;">{{ $showroom->name }}</strong>. We have received your {{ $enquiry->isTestRide() ? 'test ride booking request' : 'motorcycle purchase enquiry' }}. Our dedicated showroom representative will reach out to you shortly.
            </p>

            <table class="details-table" role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="label">Reference ID</td>
                    <td class="value mono" style="color: #fb7185;">#{{ $enquiry->enquiry_number }}</td>
                </tr>
                <tr>
                    <td class="label">Type</td>
                    <td class="value">{{ $enquiry->type->label() }}</td>
                </tr>
                @if ($enquiry->bike)
                    <tr>
                        <td class="label">Motorcycle Model</td>
                        <td class="value">{{ $enquiry->bike->name }} ({{ $enquiry->bike->brand }})</td>
                    </tr>
                @endif
                @if ($enquiry->isTestRide() && $enquiry->preferred_date)
                    <tr>
                        <td class="label">Preferred Date & Time</td>
                        <td class="value">{{ $enquiry->preferred_date->format('M d, Y') }} @ {{ $enquiry->preferred_time_slot ?? 'Flexible' }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Your Contact Phone</td>
                    <td class="value">{{ $enquiry->customer_phone }}</td>
                </tr>
                @if ($enquiry->customer_notes)
                    <tr>
                        <td class="label">Your Message</td>
                        <td class="value" style="font-size: 12px; color: #cbd5e1; font-weight: 400;">"{{ $enquiry->customer_notes }}"</td>
                    </tr>
                @endif
            </table>

            <div style="margin-top: 24px; text-align: center;">
                <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="btn-primary">
                    Browse More Motorcycles
                </a>
            </div>

            @if ($showroom->phone)
                <div style="margin-top: 24px; padding: 14px; background-color: #090e1a; border-radius: 10px; text-align: center; font-size: 12px; color: #94a3b8;">
                    Need immediate assistance? Call our dealership desk directly at <strong style="color: #ffffff;">{{ $showroom->phone }}</strong>.
                </div>
            @endif
        </td>
    </tr>
</table>
@endsection
