<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MotoShowroom SaaS - Multi-Tenant Motorcycle Showroom Platform</title>
    <meta name="description" content="Commercial Multi-Tenant Bike Showroom Management SaaS. Branded Dealership Websites, 360° Motorcycle Viewer, Online Service Booking, Razorpay Payments, and AI Virtual Assistant.">

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 selection:bg-rose-600 selection:text-white">

    <!-- Background Atmospheric Glows -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[1000px] h-[550px] bg-rose-500/5 blur-[140px] rounded-full"></div>
        <div class="absolute top-1/3 -left-40 w-[600px] h-[400px] bg-indigo-500/5 blur-[130px] rounded-full"></div>
        <div class="absolute bottom-10 right-0 w-[500px] h-[450px] bg-amber-500/5 blur-[150px] rounded-full"></div>
    </div>

    <!-- Header / Navigation Bar -->
    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-3 group" id="nav-brand-logo">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 p-0.5 shadow-md shadow-rose-600/20 group-hover:scale-105 transition duration-300">
                    <div class="w-full h-full bg-white rounded-[10px] flex items-center justify-center">
                        <svg class="w-6 h-6 text-rose-600 group-hover:text-rose-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight font-display text-slate-900">
                        MOTO<span class="text-rose-600">SHOWROOM</span>
                    </span>
                    <span class="block text-[10px] uppercase font-bold tracking-widest text-slate-500">Enterprise SaaS</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="#features" class="hover:text-slate-900 transition">Features</a>
                <a href="#viewer-360" class="hover:text-slate-900 transition">360° Tech</a>
                <a href="#scratch-cards" class="hover:text-slate-900 transition">Scratch Cards</a>
                <a href="#pricing" class="hover:text-slate-900 transition">Pricing Plans</a>
                <a href="#ai-assistant" class="hover:text-slate-900 transition">AI Assistant</a>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition shadow-sm" id="btn-dashboard">
                            Open Dashboard
                        </a>
                    @else
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition px-3 py-2" id="btn-login">
                            Sign In
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-semibold text-sm transition shadow-md shadow-rose-600/25" id="btn-register">
                            Start Free Trial
                        </a>
                    @endauth
                @else
                    <a href="{{ url('/login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition px-3 py-2" id="btn-login">
                        Sign In
                    </a>
                    <a href="{{ url('/register') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-semibold text-sm transition shadow-md shadow-rose-600/25" id="btn-register">
                        Start Free Trial
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative pt-20 pb-28 lg:pt-32 lg:pb-36 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Super Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 text-xs font-medium text-slate-700 mb-8 shadow-xs">
                <span class="flex h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
                <span>Multi-Tenant Architecture • Laravel 13 & MySQL 8.0</span>
                <span class="text-slate-300">|</span>
                <span class="text-rose-600 font-semibold">Razorpay Verified</span>
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black font-display tracking-tight text-slate-900 max-w-5xl mx-auto leading-[1.1]">
                Scale Your Motorcycle Dealership With A <span class="bg-gradient-to-r from-rose-600 via-amber-600 to-rose-600 bg-clip-text text-transparent">Complete SaaS Operating System</span>
            </h1>

            <!-- Subtitle -->
            <p class="mt-8 text-lg sm:text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                Empower your motorcycle showrooms with independent branded storefronts, interactive 360° bike viewing, automated service booking, promotional scratch cards, and an AI showroom assistant.
            </p>

            <!-- CTA Row -->
            <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-base transition shadow-lg shadow-rose-600/30 hover:scale-[1.02] flex items-center justify-center gap-3" id="hero-cta-trial">
                    <span>Launch Your Showroom</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="#features" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold text-base transition border border-slate-200 shadow-xs flex items-center justify-center gap-2" id="hero-cta-features">
                    <span>Explore Architecture</span>
                </a>
            </div>

            <!-- Stat Counters -->
            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto border-t border-slate-200 pt-10 text-left">
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <p class="text-3xl font-extrabold text-slate-900 font-display">100%</p>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mt-1">Tenant Isolation</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <p class="text-3xl font-extrabold text-rose-600 font-display">360°</p>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mt-1">Interactive Viewer</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <p class="text-3xl font-extrabold text-amber-600 font-display">Razorpay</p>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mt-1">HMAC-Verified</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <p class="text-3xl font-extrabold text-indigo-600 font-display">AI</p>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mt-1">Virtual Assistant</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Features Grid -->
    <section id="features" class="py-24 border-t border-slate-200 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-rose-600">Comprehensive Modules</span>
                <h2 class="text-3xl sm:text-5xl font-extrabold font-display text-slate-900 mt-3">Built for High-Growth Showrooms</h2>
                <p class="mt-4 text-slate-600">Everything needed to power front-office bike sales, back-office service bays, promotional campaigns, and customer retention.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-slate-50/80 border border-slate-200 rounded-2xl p-8 hover:border-slate-300 hover:bg-white hover:shadow-xl transition group">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">Branded Showroom Websites</h3>
                    <p class="mt-3 text-slate-600 text-sm leading-relaxed">
                        Every dealership gets an independent public storefront with tailored brand colors, logo, hero banners, curated inventory, and customer contact forms.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-slate-50/80 border border-slate-200 rounded-2xl p-8 hover:border-slate-300 hover:bg-white hover:shadow-xl transition group">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">Interactive 360° Viewer</h3>
                    <p class="mt-3 text-slate-600 text-sm leading-relaxed">
                        Three.js powered 3D model inspector and 36-frame turntable photo sequence viewer allowing buyers to rotate and zoom into every motorcycle detail.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-slate-50/80 border border-slate-200 rounded-2xl p-8 hover:border-slate-300 hover:bg-white hover:shadow-xl transition group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">Service Appointments</h3>
                    <p class="mt-3 text-slate-600 text-sm leading-relaxed">
                        Online service booking engine with daily capacity controls, time slot protection, optional booking deposits, and automated confirmations.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="bg-slate-50/80 border border-slate-200 rounded-2xl p-8 hover:border-slate-300 hover:bg-white hover:shadow-xl transition group">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-200 text-purple-600 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">Razorpay Payments</h3>
                    <p class="mt-3 text-slate-600 text-sm leading-relaxed">
                        Server-side signature verified payments for SaaS subscriptions and customer service deposits with idempotent webhook ledger and refund handling.
                    </p>
                </div>

                <!-- Card 5 -->
                <div class="bg-slate-50/80 border border-slate-200 rounded-2xl p-8 hover:border-slate-300 hover:bg-white hover:shadow-xl transition group">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">Scratch Card Campaigns</h3>
                    <p class="mt-3 text-slate-600 text-sm leading-relaxed">
                        Interactive customer rewards system with server-side prize determination, quantity caps, anti-tampering verification, and unique redemption coupons.
                    </p>
                </div>

                <!-- Card 6 -->
                <div class="bg-slate-50/80 border border-slate-200 rounded-2xl p-8 hover:border-slate-300 hover:bg-white hover:shadow-xl transition group">
                    <div class="w-12 h-12 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-600 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">AI Virtual Assistant</h3>
                    <p class="mt-3 text-slate-600 text-sm leading-relaxed">
                        Intelligent virtual dealership assistant answering questions strictly grounded in the showroom’s authorized stock, service menu, and store timings.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 border-t border-slate-200 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-rose-600">Subscription Plans</span>
                <h2 class="text-3xl sm:text-5xl font-extrabold font-display text-slate-900 mt-3">Transparent Dealership Pricing</h2>
                <p class="mt-4 text-slate-600">Scale seamlessly from single independent showrooms to large motorcycle franchise chains.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Starter Tier -->
                <div class="bg-white border border-slate-200 rounded-3xl p-8 flex flex-col justify-between shadow-xs hover:shadow-md transition">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 font-display">Starter</h3>
                        <p class="text-xs text-slate-500 mt-1">For single location bike outlets</p>
                        <div class="mt-6 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-slate-900 font-display">₹2,499</span>
                            <span class="text-sm text-slate-500">/ month</span>
                        </div>
                        <ul class="mt-8 space-y-4 text-sm text-slate-700">
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Up to 15 Bike Listings</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 3 Staff Accounts</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Branded Public Website</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Service Booking Scheduler</li>
                            <li class="flex items-center gap-3 text-slate-400"><svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> 360° Interactive Viewer</li>
                            <li class="flex items-center gap-3 text-slate-400"><svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> AI Virtual Assistant</li>
                        </ul>
                    </div>
                    <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="mt-8 block text-center py-3 px-6 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 font-semibold text-sm transition border border-slate-300">
                        Select Starter
                    </a>
                </div>

                <!-- Pro Tier (Popular) -->
                <div class="bg-white border-2 border-rose-500 rounded-3xl p-8 flex flex-col justify-between shadow-xl shadow-rose-500/10 relative">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-gradient-to-r from-rose-600 to-amber-500 text-white text-[11px] font-bold uppercase tracking-wider shadow-md">
                        Most Popular
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 font-display">Professional</h3>
                        <p class="text-xs text-slate-500 mt-1">For growing multi-brand dealerships</p>
                        <div class="mt-6 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-slate-900 font-display">₹5,999</span>
                            <span class="text-sm text-slate-500">/ month</span>
                        </div>
                        <ul class="mt-8 space-y-4 text-sm text-slate-700">
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Up to 50 Bike Listings</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 10 Staff Accounts (Role-based)</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 360° Interactive Motorcycle Viewer</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Promotional Scratch Cards</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Dealership Careers Portal</li>
                            <li class="flex items-center gap-3 text-slate-400"><svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> AI Virtual Assistant</li>
                        </ul>
                    </div>
                    <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="mt-8 block text-center py-3.5 px-6 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm transition shadow-md shadow-rose-600/25">
                        Start 14-Day Pro Trial
                    </a>
                </div>

                <!-- Enterprise Tier -->
                <div class="bg-white border border-slate-200 rounded-3xl p-8 flex flex-col justify-between shadow-xs hover:shadow-md transition">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 font-display">Enterprise</h3>
                        <p class="text-xs text-slate-500 mt-1">For multi-city franchise networks</p>
                        <div class="mt-6 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-slate-900 font-display">₹12,999</span>
                            <span class="text-sm text-slate-500">/ month</span>
                        </div>
                        <ul class="mt-8 space-y-4 text-sm text-slate-700">
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Unlimited Bike Listings</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Unlimited Staff Accounts</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 360° Viewer + Custom 3D Models</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> AI Virtual Showroom Assistant</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Custom Domain Mapping</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Priority 24/7 SLA Support</li>
                        </ul>
                    </div>
                    <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="mt-8 block text-center py-3 px-6 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 font-semibold text-sm transition border border-slate-300">
                        Get Enterprise
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-12 text-slate-600 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <span class="text-lg font-bold font-display text-slate-900">MOTO<span class="text-rose-600">SHOWROOM</span></span>
                <span class="text-xs text-slate-500">© {{ date('Y') }} All Rights Reserved.</span>
            </div>
            <div class="flex items-center gap-6 text-xs text-slate-600">
                <a href="/docs/ARCHITECTURE.md" class="hover:text-slate-900 transition">Architecture</a>
                <a href="/docs/SECURITY.md" class="hover:text-slate-900 transition">Security Policy</a>
                <a href="/docs/SUBSCRIPTIONS.md" class="hover:text-slate-900 transition">Subscription Terms</a>
                <a href="/docs/PAYMENTS.md" class="hover:text-slate-900 transition">Payment Guide</a>
            </div>
        </div>
    </footer>

</body>
</html>
