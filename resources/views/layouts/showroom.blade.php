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
    <style>
        :root {
            --brand-primary: {{ $showroom->brand_color_primary ?? '#DC2626' }};
            --brand-accent: {{ $showroom->brand_color_accent ?? '#0F172A' }};
        }
    </style>
</head>
<body class="font-sans antialiased min-h-full flex flex-col bg-slate-900 text-slate-100 selection:bg-brand-primary selection:text-white">

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
