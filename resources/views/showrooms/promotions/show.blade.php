@extends('layouts.app')

@section('title', $campaign->name . ' - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans selection:bg-purple-500 selection:text-white">

    <!-- Showroom Branded Topbar / Header -->
    <header class="border-b border-slate-200 bg-white/90 backdrop-blur sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Showroom Logo & Identity -->
                <a href="{{ route('showrooms.show', $showroom) }}" class="flex items-center gap-3 group">
                    @if($showroom->logo_url)
                        <img src="{{ $showroom->logo_url }}" alt="{{ $showroom->name }}" class="h-10 w-auto rounded-xl object-contain bg-white border border-slate-200 p-1">
                    @else
                        <span class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-white text-base shadow-md group-hover:scale-105 transition-transform"
                              style="background: linear-gradient(135deg, {{ $showroom->brand_color_primary ?? '#dc2626' }}, {{ $showroom->brand_color_accent ?? '#f43f5e' }})">
                            {{ strtoupper(substr($showroom->name, 0, 1)) }}
                        </span>
                    @endif
                    <div>
                        <span class="font-display font-black text-lg sm:text-xl tracking-tight text-slate-900 block leading-none">{{ $showroom->name }}</span>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest block mt-0.5">{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-6 text-sm font-semibold">
                    <a href="{{ route('showrooms.show', $showroom) }}" class="text-slate-600 hover:text-slate-900 transition">Home</a>
                    <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="text-slate-600 hover:text-slate-900 transition">Motorcycles</a>
                    <a href="{{ route('showrooms.services.create', $showroom) }}" class="text-slate-600 hover:text-slate-900 transition">Service & Repair</a>
                    <a href="{{ route('showrooms.promotions.index', $showroom) }}" class="text-purple-600 font-bold transition">Promotions</a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('showrooms.promotions.index', $showroom) }}"
                       class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-semibold text-slate-700 transition flex items-center gap-1.5 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        All Campaigns
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-6">
            <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-slate-900 transition">Home</a>
            <span>/</span>
            <a href="{{ route('showrooms.promotions.index', $showroom) }}" class="hover:text-slate-900 transition">Promotions</a>
            <span>/</span>
            <span class="text-purple-600 font-bold truncate max-w-xs sm:max-w-md">{{ $campaign->name }}</span>
        </div>

        <!-- Flash Validation Errors -->
        @if ($errors->has('participation'))
            <div class="mb-8 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800 shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="text-sm font-semibold">{{ $errors->first('participation') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left: Campaign Information & Prize Pool -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Campaign Header Card -->
                <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs px-3 py-1 rounded-lg font-bold bg-purple-50 text-purple-700 border border-purple-200">
                            Digital Scratch-and-Win
                        </span>
                        <span class="text-xs text-slate-500 font-medium">
                            Valid until {{ $campaign->end_date->format('M d, Y') }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black font-display text-slate-900 tracking-tight leading-tight mb-4">
                        {{ $campaign->name }}
                    </h1>

                    @if($campaign->description)
                        <p class="text-sm text-slate-600 leading-relaxed mb-6">
                            {{ $campaign->description }}
                        </p>
                    @endif

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-500 block">Showroom Location</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">{{ $showroom->name }}, {{ $showroom->city }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Campaign Dates</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">{{ $campaign->start_date->format('M d') }} - {{ $campaign->end_date->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Per-Customer Limit</span>
                            <span class="font-bold text-purple-600 mt-0.5 block">{{ $campaign->max_participations_per_customer }} Scratch Card(s)</span>
                        </div>
                    </div>
                </div>

                <!-- Prize Pool Showcase -->
                <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-sm">
                    <h2 class="text-lg font-bold font-display text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                        Available Campaign Rewards
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($campaign->prizes as $prize)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">{{ $prize->title }}</h4>
                                    <span class="text-[10px] px-2 py-0.5 rounded font-semibold mt-1 inline-block {{ $prize->prize_type->badgeClasses() }}">
                                        {{ $prize->prize_type->label() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Terms and Conditions -->
                @if($campaign->terms_and_conditions)
                    <div class="rounded-3xl bg-white border border-slate-200 p-6 text-xs text-slate-600 leading-relaxed shadow-xs">
                        <h4 class="font-bold text-slate-800 uppercase tracking-wider mb-2">Terms & Conditions</h4>
                        <div class="whitespace-pre-line">{{ $campaign->terms_and_conditions }}</div>
                    </div>
                @endif

            </div>

            <!-- Right: Claim Form -->
            <div class="lg:col-span-5">
                <div class="sticky top-24 rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-md">
                    <div class="mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-600 text-white flex items-center justify-center mb-3 shadow-md shadow-purple-600/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold font-display text-slate-900">Claim Your Scratch Card</h2>
                        <p class="text-xs text-slate-500 mt-1">Enter your details to generate your digital scratch card and reveal your prize.</p>
                    </div>

                    <form method="POST" action="{{ route('showrooms.promotions.claim', [$showroom, $campaign]) }}" class="space-y-4">
                        @csrf

                        <!-- Customer Name -->
                        <div>
                            <label for="customer_name" class="block text-xs font-semibold text-slate-700 mb-1">
                                Full Name <span class="text-purple-600">*</span>
                            </label>
                            <input type="text"
                                   id="customer_name"
                                   name="customer_name"
                                   value="{{ old('customer_name', auth()->user()?->name) }}"
                                   required
                                   placeholder="e.g. Sameer Kapoor"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-purple-500 focus:bg-white focus:ring-1 focus:ring-purple-500">
                            @error('customer_name')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Customer Email -->
                        <div>
                            <label for="customer_email" class="block text-xs font-semibold text-slate-700 mb-1">
                                Email Address <span class="text-purple-600">*</span>
                            </label>
                            <input type="email"
                                   id="customer_email"
                                   name="customer_email"
                                   value="{{ old('customer_email', auth()->user()?->email) }}"
                                   required
                                   placeholder="you@domain.com"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-purple-500 focus:bg-white focus:ring-1 focus:ring-purple-500">
                            @error('customer_email')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Customer Phone -->
                        <div>
                            <label for="customer_phone" class="block text-xs font-semibold text-slate-700 mb-1">
                                Mobile Number <span class="text-purple-600">*</span>
                            </label>
                            <input type="text"
                                   id="customer_phone"
                                   name="customer_phone"
                                   value="{{ old('customer_phone', auth()->user()?->phone) }}"
                                   required
                                   placeholder="+91 98765 43210"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-purple-500 focus:bg-white focus:ring-1 focus:ring-purple-500 font-mono">
                            @error('customer_phone')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Claim Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    id="btn-claim-card"
                                    class="w-full py-3.5 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-purple-600/20 transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>Get My Scratch Card &rarr;</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-8 mt-12 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} {{ $showroom->name }}. Powered by MotoShowroom SaaS.</p>
    </footer>

</div>
@endsection
