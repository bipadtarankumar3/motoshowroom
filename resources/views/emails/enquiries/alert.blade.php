@extends('emails.layouts.master')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div style="margin-bottom: 20px;">
                <span class="badge badge-rose">New Lead Alert</span>
            </div>

            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0;">
                New Enquiry Received!
            </h1>

            <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 20px 0;">
                A new customer has submitted an enquiry through your public dealership showcase. Please review customer details and follow up promptly to maximize conversion.
            </p>

            <table class="details-table" role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="label">Reference ID</td>
                    <td class="value mono" style="color: #e11d48; font-weight: bold;">#{{ $enquiry->enquiry_number }}</td>
                </tr>
                <tr>
                    <td class="label">Customer Name</td>
                    <td class="value">{{ $enquiry->customer_name }}</td>
                </tr>
                <tr>
                    <td class="label">Phone</td>
                    <td class="value mono">{{ $enquiry->customer_phone }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value mono">{{ $enquiry->customer_email }}</td>
                </tr>
                <tr>
                    <td class="label">Type</td>
                    <td class="value">{{ $enquiry->type->label() }}</td>
                </tr>
                @if ($enquiry->bike)
                    <tr>
                        <td class="label">Target Motorcycle</td>
                        <td class="value">{{ $enquiry->bike->name }} ({{ $enquiry->bike->brand }})</td>
                    </tr>
                @endif
                @if ($enquiry->preferred_date)
                    <tr>
                        <td class="label">Scheduled Time</td>
                        <td class="value">{{ $enquiry->preferred_date->format('M d, Y') }} @ {{ $enquiry->preferred_time_slot ?? 'Anytime' }}</td>
                    </tr>
                @endif
                @if ($enquiry->customer_notes)
                    <tr>
                        <td class="label">Customer Notes</td>
                        <td class="value" style="font-size: 12px; color: #334155; font-weight: 400;">"{{ $enquiry->customer_notes }}"</td>
                    </tr>
                @endif
            </table>

            <div style="margin-top: 24px; text-align: center;">
                <a href="{{ $enquiry->id ? route('showrooms.manage.enquiries.show', ['showroom' => $showroom, 'enquiry' => $enquiry]) : route('showrooms.manage.enquiries.index', $showroom) }}" class="btn-primary">
                    Open Lead in Back-Office
                </a>
            </div>
        </td>
    </tr>
</table>
@endsection
