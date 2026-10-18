@extends('emails.layouts.master')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div style="margin-bottom: 20px;">
                <span class="badge badge-rose">Application Received</span>
            </div>

            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0;">
                Hello, {{ $application->applicant_name }}
            </h1>

            <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 20px 0;">
                Thank you for applying for the position of <strong style="color: #0f172a;">{{ $jobOpening->title }}</strong> at <strong style="color: #0f172a;">{{ $showroom->name }}</strong>. We have successfully logged your profile and resume in our recruitment system. Our hiring team will review your qualifications and contact you if your experience aligns with our needs.
            </p>

            <table class="details-table" role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="label">Reference ID</td>
                    <td class="value mono" style="color: #e11d48; font-weight: bold;">#{{ $application->applicant_number }}</td>
                </tr>
                <tr>
                    <td class="label">Position</td>
                    <td class="value">{{ $jobOpening->title }}</td>
                </tr>
                <tr>
                    <td class="label">Department</td>
                    <td class="value">{{ $jobOpening->department instanceof \BackedEnum ? $jobOpening->department->label() : ucfirst($jobOpening->department) }}</td>
                </tr>
                <tr>
                    <td class="label">Employment Type</td>
                    <td class="value">{{ $jobOpening->job_type instanceof \BackedEnum ? $jobOpening->job_type->label() : ucfirst(str_replace('_', ' ', $jobOpening->job_type)) }}</td>
                </tr>
                <tr>
                    <td class="label">Contact Phone</td>
                    <td class="value mono">{{ $application->applicant_phone }}</td>
                </tr>
                <tr>
                    <td class="label">Contact Email</td>
                    <td class="value mono">{{ $application->applicant_email }}</td>
                </tr>
                <tr>
                    <td class="label">Total Experience</td>
                    <td class="value">{{ $application->experience_years }} {{ Str::plural('year', (float)$application->experience_years) }}</td>
                </tr>
            </table>

            <div style="margin-top: 24px; text-align: center;">
                <a href="{{ route('showrooms.careers.index', $showroom) }}" class="btn-primary">
                    View More Careers
                </a>
            </div>

            @if ($showroom->email)
                <div style="margin-top: 24px; padding: 14px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; text-align: center; font-size: 12px; color: #64748b;">
                    Have questions regarding your application? You can reach the dealership recruitment desk at <strong style="color: #0f172a;">{{ $showroom->email }}</strong>.
                </div>
            @endif
        </td>
    </tr>
</table>
@endsection
