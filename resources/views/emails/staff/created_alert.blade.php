@extends('emails.layouts.master')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div style="margin-bottom: 20px;">
                <span class="badge badge-sky">Staff Created</span>
            </div>

            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0;">
                New Staff Member Added
            </h1>

            <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 20px 0;">
                Hello {{ $creator->name }}, you have successfully added <strong>{{ $staffUser->name }}</strong> to <strong>{{ $showroom->name }}</strong>. Welcome credentials have also been dispatched to the staff member's email address.
            </p>

            <table class="details-table" role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="label">Staff Member</td>
                    <td class="value" style="font-weight: bold; color: #0f172a;">{{ $staffUser->name }}</td>
                </tr>
                <tr>
                    <td class="label">Staff Email</td>
                    <td class="value mono" style="color: #0284c7;">{{ $staffUser->email }}</td>
                </tr>
                @if($staffUser->phone)
                    <tr>
                        <td class="label">Contact Phone</td>
                        <td class="value mono">{{ $staffUser->phone }}</td>
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
                        <td class="label">Shift Timing</td>
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

            <div style="margin-top: 24px; text-align: center;">
                <a href="{{ route('showrooms.manage.staff.index', $showroom) }}" class="btn-primary" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    View Staff Roster & Duty Desk &rarr;
                </a>
            </div>
        </td>
    </tr>
</table>
@endsection
