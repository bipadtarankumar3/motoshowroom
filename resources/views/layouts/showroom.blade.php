<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? ($showroom->name ?? 'Motorcycle Showroom') }}</title>
    <meta name="description" content="{{ $metaDescription ?? ($showroom->name . ' - Authorized motorcycle showroom, bike sales, servicing, and bookings.') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Dynamic Showroom Branding Styles -->
    @php
        $brandPrimary = $showroom->primary_color ?? '#DC2626';
        $brandAccent = $showroom->accent_color ?? '#0F172A';
        $brandPrimaryText = $showroom->primary_text_color ?? '#FFFFFF';
        $brandAccentText = $showroom->accent_text_color ?? '#FFFFFF';
    @endphp
    <style>
        :root {
            --brand-primary: {{ $brandPrimary }};
            --brand-primary-text: {{ $brandPrimaryText }};
            --brand-accent: {{ $brandAccent }};
            --brand-accent-text: {{ $brandAccentText }};
        }
        .bg-brand-primary { background-color: var(--brand-primary) !important; color: var(--brand-primary-text) !important; }
        .text-brand-primary { color: var(--brand-primary) !important; }
        .border-brand-primary { border-color: var(--brand-primary) !important; }
        .ring-brand-primary { --tw-ring-color: var(--brand-primary) !important; }
        .hover\:bg-brand-primary:hover { background-color: var(--brand-primary) !important; color: var(--brand-primary-text) !important; }
        .hover\:text-brand-primary:hover { color: var(--brand-primary) !important; }
        .hover\:border-brand-primary:hover { border-color: var(--brand-primary) !important; }
        .focus\:border-brand-primary:focus { border-color: var(--brand-primary) !important; }
        .focus\:ring-brand-primary:focus { --tw-ring-color: var(--brand-primary) !important; }
        .btn-brand-primary {
            background-color: var(--brand-primary) !important;
            color: var(--brand-primary-text) !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-brand-primary:hover {
            filter: brightness(0.94);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15);
        }
        .btn-brand-accent {
            background-color: var(--brand-accent) !important;
            color: var(--brand-accent-text) !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-brand-accent:hover {
            filter: brightness(1.1);
        }
        .badge-brand-primary {
            background-color: {{ $brandPrimary }}18 !important;
            color: {{ $brandPrimaryText === '#0f172a' ? '#0f172a' : $brandPrimary }} !important;
            border: 1px solid {{ $brandPrimary }}35 !important;
        }
    </style>
</head>
<body class="font-sans antialiased min-h-full flex flex-col bg-slate-50 text-slate-900 selection:bg-brand-primary selection:text-white">

    <!-- Read-Only / Subscription Notice Banner if applicable -->
    @if (isset($showroom) && $showroom->read_only_mode)
        <div class="bg-amber-500 text-slate-950 px-4 py-2 text-center text-xs font-semibold uppercase tracking-wider sticky top-0 z-50 shadow-md">
            Notice: This showroom is currently operating in catalog-only mode. Online reservations and enquiries are temporarily offline.
        </div>
    @endif

    <!-- Content Slot -->
    <div class="flex-grow flex flex-col">
        {{ $slot }}
    </div>

    @if(isset($showroom))
        @include('components.ai-assistant-widget', ['showroom' => $showroom])
    @endif

    @livewireScripts
    @stack('scripts')
</body>
</html>
