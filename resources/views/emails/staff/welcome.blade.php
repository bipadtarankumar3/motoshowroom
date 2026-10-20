@extends('emails.layouts.master')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div style="margin-bottom: 20px;">
                <span class="badge badge-emerald">Team Welcome</span>
            </div>

            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0;">
                Welcome to {{ $showroom->name }} Staff!
            </h1>

            <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 20px 0;">
                Hello {{ $staffUser->name }}, you have been added as an authorized staff member for <strong>{{ $showroom->name }}</strong>. You can now log in to the dealership portal to manage workshop duties, service bookings, and customer interactions.
            </p>

            <table class="details-table" role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="label">Dealership</td>
                    <td class="value" style="font-weight: bold; color: #0f172a;">{{ $showroom->name }}</td>
                </tr>
                <tr>
                    <td class="label">Login Email</td>
                    <td class="value mono" style="color: #0284c7; font-weight: bold;">{{ $staffUser->email }}</td>
                </tr>
                @if($plainPassword)
                    <tr>
                        <td class="label">Temporary Password</td>
                        <td class="value mono" style="color: #ea580c; font-weight: bold;">{{ $plainPassword }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Role</td>
                    <td class="value">Staff</td>
                </tr>
                <tr>
                    <td class="label">Department</td>
                    <td class="value">{{ $membership->department ?? 'Workshop' }}</td>
                </tr>
                @if($membership->designation)
                    <tr>
                        <td class="label">Designation</td>
                        <td class="value">{{ $membership->designation }}</td>
                    </tr>
                @endif
                @if($membership->shift)
                    <tr>
                        <td class="label">Assigned Shift</td>
                        <td class="value">{{ $membership->shift }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Initial Duty Status</td>
                    <td class="value">
                        @if($membership->availability_status === 'available')
                            <span style="color: #059669; font-weight: bold;">🟢 Available (On Duty)</span>
                        @elseif($membership->availability_status === 'busy')
                            <span style="color: #d97706; font-weight: bold;">🟡 Busy with Customer / Workshop</span>
                        @elseif($membership->availability_status === 'not_attending')
                            <span style="color: #e11d48; font-weight: bold;">🔴 Not Attending (On Leave)</span>
                        @else
                            <span style="color: #64748b; font-weight: bold;">⚪ Off Duty</span>
                        @endif
                    </td>
                </tr>
            </table>

            @if($plainPassword)
                <p style="font-size: 12px; color: #64748b; margin: 16px 0 0 0; line-height: 1.5;">
                    * For your security, please update your temporary password after logging in for the first time.
                </p>
            @endif

            <div style="margin-top: 24px; text-align: center;">
                <a href="{{ route('login') }}" class="btn-primary" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);">
                    Log In to Dealership Desk &rarr;
                </a>
            </div>
        </td>
    </tr>
</table>
@endsection
