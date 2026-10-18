<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'MotoShowroom SaaS') }}</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased min-h-full flex flex-col bg-slate-50 text-slate-900 selection:bg-rose-600 selection:text-white">
    <!-- Background Glow Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-rose-500/5 blur-[120px] rounded-full"></div>
        <div class="absolute top-1/2 -right-40 w-[600px] h-[400px] bg-indigo-500/5 blur-[140px] rounded-full"></div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
