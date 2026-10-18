@extends('emails.layouts.master')

@section('content')
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <div style="margin-bottom: 20px;">
                <span class="badge badge-rose">Subscription Renewal</span>
            </div>

            <h1 style="font-size: 22px; font-weight: 800; color: #ffffff; margin: 0 0 12px 0;">
                Subscription Expiring Soon
            </h1>

            <p style="font-size: 14px; color: #94a3b8; line-height: 1.6; margin: 0 0 20px 0;">
                Dear Owner of <strong style="color: #ffffff;">{{ $showroom->name }}</strong>, your SaaS plan subscription for <strong style="color: #fb7185;">{{ $subscription->plan->name }}</strong> is scheduled to expire on <strong style="color: #ffffff;">{{ $subscription->current_period_end?->format('M d, Y') }}</strong> ({{ $daysRemaining }} day{{ $daysRemaining === 1 ? '' : 's' }} remaining).
            </p>

            <table class="details-table" role="presentation" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="label">Showroom Dealership</td>
                    <td class="value">{{ $showroom->name }}</td>
                </tr>
                <tr>
                    <td class="label">Current Plan</td>
                    <td class="value" style="color: #fb7185;">{{ $subscription->plan->name }}</td>
                </tr>
                <tr>
                    <td class="label">Billing Interval</td>
                    <td class="value" style="text-transform: capitalize;">{{ $subscription->billing_interval ?? 'Monthly' }}</td>
                </tr>
                <tr>
                    <td class="label">Current Period End</td>
                    <td class="value mono">{{ $subscription->current_period_end?->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Days Remaining</td>
                    <td class="value mono" style="color: #f59e0b; font-weight: bold;">{{ $daysRemaining }} day(s)</td>
                </tr>
            </table>

            <p style="font-size: 13px; color: #94a3b8; line-height: 1.5; margin: 20px 0;">
                To ensure uninterrupted access to your dealership back-office, motorcycle inventory publishing, online service appointment booking, and customer enquiries, please renew your subscription or upgrade your plan.
            </p>

            <div style="margin-top: 24px; text-align: center;">
                <a href="{{ route('showrooms.billing.index', $showroom) }}" class="btn-primary">
                    Renew / Upgrade Plan Now
                </a>
            </div>
        </td>
    </tr>
</table>
@endsection
