<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@hasSection('title')@yield('title') - {{ config('app.name', 'MotoShowroom SaaS') }}@else{{ $title ?? config('app.name', 'MotoShowroom SaaS') }}@endif</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @if(isset($showroom) && !empty($showroom->brand_color_primary))
        <style>
            :root {
                --brand-primary: {{ $showroom->brand_color_primary }};
                --brand-accent: {{ $showroom->brand_color_accent ?? '#0f172a' }};
            }
            .bg-brand-primary { background-color: var(--brand-primary) !important; }
            .text-brand-primary { color: var(--brand-primary) !important; }
            .border-brand-primary { border-color: var(--brand-primary) !important; }
            .ring-brand-primary { --tw-ring-color: var(--brand-primary) !important; }
            .hover\:bg-brand-primary:hover { background-color: var(--brand-primary) !important; }
            .hover\:text-brand-primary:hover { color: var(--brand-primary) !important; }
        </style>
    @endif
</head>
<body class="font-sans antialiased min-h-full flex flex-col bg-slate-50 text-slate-900">
    {{-- Super Admin Impersonation Top Banner --}}
    @if (session()->has('admin_impersonator_id'))
        <div class="sticky top-0 z-50 h-11 bg-amber-400 border-b border-amber-500 text-slate-950 px-4 sm:px-6 flex items-center justify-between gap-3 shadow-md text-xs font-semibold">
            <div class="flex items-center gap-2.5 truncate">
                <span class="relative flex h-2.5 w-2.5 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-600 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600"></span>
                </span>
                <span class="truncate">
                    <strong class="font-bold">Super Admin Impersonation:</strong>
                    Currently viewing as <span class="font-extrabold underline">{{ auth()->user()->name }}</span> ({{ auth()->user()->email }})
                </span>
            </div>
            <form method="POST" action="{{ route('impersonate.leave') }}" class="m-0 shrink-0">
                @csrf
                <button type="submit"
                        id="btn-return-admin-dashboard"
                        class="px-3 py-1 rounded-lg bg-slate-950 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Return to Admin Dashboard</span>
                </button>
            </form>
        </div>
    @endif

    <!-- Flash Messages (Auto-hiding with dismiss cross icon) -->
    @if (session()->has('success'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 4000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             class="fixed top-4 right-4 z-50 bg-white text-slate-800 border border-emerald-200 px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3 text-sm ring-1 ring-emerald-500/20 max-w-md"
             role="alert">
            <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <span class="font-medium text-xs sm:text-sm text-slate-800 flex-1">{{ session('success') }}</span>
            <button type="button" @click="show = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer" title="Dismiss notification">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 5000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             class="fixed top-4 right-4 z-50 bg-white text-slate-800 border border-rose-200 px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3 text-sm ring-1 ring-rose-500/20 max-w-md"
             role="alert">
            <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <span class="font-medium text-xs sm:text-sm text-slate-800 flex-1">{{ session('error') }}</span>
            <button type="button" @click="show = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer" title="Dismiss notification">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    <!-- Content Slot -->
    <main class="flex-grow">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
