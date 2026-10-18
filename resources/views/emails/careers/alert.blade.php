@extends('emails.layouts.master')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div style="margin-bottom: 20px;">
                <span class="badge badge-rose">New Candidate Application</span>
            </div>

            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0;">
                New Applicant for {{ $jobOpening->title }}!
            </h1>

            <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 20px 0;">
                A new job application has been submitted through your public careers portal. Review candidate credentials below or open your back-office ATS to inspect the resume and schedule an interview.
            </p>

            <table class="details-table" role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="label">Reference ID</td>
                    <td class="value mono" style="color: #e11d48; font-weight: bold;">#{{ $application->applicant_number }}</td>
                </tr>
                <tr>
                    <td class="label">Target Role</td>
                    <td class="value">{{ $jobOpening->title }} ({{ $jobOpening->department instanceof \BackedEnum ? $jobOpening->department->label() : ucfirst($jobOpening->department) }})</td>
                </tr>
                <tr>
                    <td class="label">Candidate Name</td>
                    <td class="value">{{ $application->applicant_name }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value mono">{{ $application->applicant_email }}</td>
                </tr>
                <tr>
                    <td class="label">Phone</td>
                    <td class="value mono">{{ $application->applicant_phone }}</td>
                </tr>
                <tr>
                    <td class="label">Experience</td>
                    <td class="value">{{ $application->experience_years }} {{ Str::plural('year', (float)$application->experience_years) }}</td>
                </tr>
                @if ($application->current_company)
                    <tr>
                        <td class="label">Current Employer</td>
                        <td class="value">{{ $application->current_company }}</td>
                    </tr>
                @endif
                @if ($application->portfolio_url)
                    <tr>
                        <td class="label">Portfolio / Link</td>
                        <td class="value"><a href="{{ $application->portfolio_url }}" target="_blank" style="color: #0284c7; text-decoration: underline;">{{ $application->portfolio_url }}</a></td>
                    </tr>
                @endif
                @if ($application->cover_letter)
                    <tr>
                        <td class="label">Cover Note</td>
                        <td class="value" style="font-size: 12px; color: #334155; font-weight: 400;">"{{ $application->cover_letter }}"</td>
                    </tr>
                @endif
            </table>

            <div style="margin-top: 24px; text-align: center;">
                <a href="{{ $application->id ? route('showrooms.manage.applicants.show', ['showroom' => $showroom, 'applicant' => $application]) : route('showrooms.manage.applicants.index', $showroom) }}" class="btn-primary">
                    Open Candidate Profile in ATS
                </a>
            </div>
        </td>
    </tr>
</table>
@endsection
