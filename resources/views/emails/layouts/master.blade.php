<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notification from ' . ($showroom->name ?? 'MotoShowroom SaaS') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #030712;
            color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        table {
            border-collapse: collapse;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }
        .card {
            background-color: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 16px;
            padding: 32px;
            margin: 20px 0;
        }
        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
            color: #ffffff !important;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 10px;
            text-align: center;
        }
        .btn-secondary {
            display: inline-block;
            background-color: #1e293b;
            border: 1px solid #334155;
            color: #cbd5e1 !important;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            text-align: center;
        }
        .badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-rose {
            background-color: rgba(225, 29, 72, 0.15);
            color: #fb7185;
            border: 1px solid rgba(225, 29, 72, 0.3);
        }
        .badge-emerald {
            background-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .badge-sky {
            background-color: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(14, 165, 233, 0.3);
        }
        .details-table {
            width: 100%;
            margin: 20px 0;
            background-color: #090e1a;
            border: 1px solid #1e293b;
            border-radius: 12px;
            overflow: hidden;
        }
        .details-table td {
            padding: 12px 16px;
            font-size: 13px;
            border-bottom: 1px solid #1e293b;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .label {
            color: #94a3b8;
            width: 38%;
            font-weight: 500;
        }
        .value {
            color: #f8fafc;
            font-weight: 600;
        }
        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #64748b;
            padding: 24px 16px;
            line-height: 1.6;
        }
    </style>
</head>
<body style="background-color: #030712; padding: 20px 10px;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table class="container" role="presentation" cellspacing="0" cellpadding="0">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 20px 0 10px 0;">
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-block; background: linear-gradient(135deg, #e11d48, #be123c); width: 36px; height: 36px; border-radius: 10px; line-height: 36px; text-align: center; vertical-align: middle; margin-right: 10px;">
                                            <span style="font-size: 18px; color: #ffffff;">🏍</span>
                                        </div>
                                        <span style="font-size: 20px; font-weight: 900; color: #ffffff; letter-spacing: -0.5px; vertical-align: middle;">
                                            {{ $showroom->name ?? 'MotoShowroom SaaS' }}
                                        </span>
                                    </td>
                                </tr>
                                @if (isset($showroom->city))
                                    <tr>
                                        <td align="center" style="padding-top: 4px;">
                                            <span style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">
                                                {{ $showroom->city }} • Authorized Dealership & Service
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content Card -->
                    <tr>
                        <td>
                            <div class="card">
                                @yield('content')
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p style="margin: 0 0 6px 0; color: #94a3b8; font-weight: 600;">
                                {{ $showroom->name ?? 'MotoShowroom SaaS' }}
                            </p>
                            @if (isset($showroom->address) || isset($showroom->phone))
                                <p style="margin: 0 0 8px 0; color: #64748b;">
                                    {{ $showroom->address ?? '' }} 
                                    @if (isset($showroom->phone))
                                        • Hotline: {{ $showroom->phone }}
                                    @endif
                                </p>
                            @endif
                            <p style="margin: 0; color: #475569; font-size: 11px;">
                                This is an automated transactional message sent regarding your activity on {{ $showroom->name ?? 'MotoShowroom' }}. Please do not reply directly to this email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
