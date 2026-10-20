@extends('layouts.app')

@section('title', 'Motorcycle Collection & Inventory — ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50/70 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    {{-- Branded Dealership Header --}}
    @include('showrooms.partials.header')

    <main class="flex-1 w-full max-w-[1720px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 py-8 sm:py-12 space-y-8">

        {{-- ═══ TOP BREADCRUMB & GARAGE STATUS ═══ --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
            <div class="flex items-center gap-2 text-slate-500 flex-wrap">
                <a href="{{ route('showrooms.show', $showroom) }}" class="inline-flex items-center gap-1.5 hover:text-slate-900 font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>{{ $showroom->name }}</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="font-bold text-slate-900">Motorcycle Showroom Inventory</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold bg-white border border-slate-200/90 text-slate-700 shadow-2xs">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>{{ $bikes->total() }} Certified Machines In Showroom</span>
                </span>
            </div>
        </div>

        {{-- ═══ HERO BANNER ═══ --}}
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-gradient-to-br from-orange-400/10 via-rose-500/10 to-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute left-1/4 -bottom-20 w-64 h-64 bg-cyan-500/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="max-w-3xl space-y-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                            <span>🏍️</span>
                            <span>Authorized Dealership Fleet</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span>Instant Online Purchase Reservation</span>
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black font-display text-slate-900 tracking-tight leading-tight">
                        Motorcycle Collection
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-2xl">
                        Explore our premier lineup of superbikes, adventure tourers, and sport machines. Inspect each motorcycle in full <strong class="text-slate-900 font-bold">interactive 360° Studio view</strong> and reserve directly for immediate factory allocation.
                    </p>
                </div>

                {{-- Key Buyer Assurance Badges --}}
                <div class="grid grid-cols-2 gap-3 sm:gap-4 shrink-0">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-0.5">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Online Booking</span>
                        <p class="text-xs font-bold text-slate-900">₹10,000 Token</p>
                        <span class="text-[10px] text-emerald-600 font-semibold">100% Refundable</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-0.5">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Finance & EMI</span>
                        <p class="text-xs font-bold text-slate-900">From 7.99% APR</p>
                        <span class="text-[10px] text-slate-500">Instant Pre-Approval</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-0.5">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Warranty</span>
                        <p class="text-xs font-bold text-slate-900">3-Year Factory</p>
                        <span class="text-[10px] text-slate-500">OEM Comprehensive</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-0.5">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Virtual Studio</span>
                        <p class="text-xs font-bold text-cyan-700">360° Turntable</p>
                        <span class="text-[10px] text-slate-500">Every Machine</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ FILTER & SEARCH CAPSULE ═══ --}}
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-xs space-y-4">
            <form method="GET" action="{{ route('showrooms.bikes.index', $showroom) }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    {{-- Search Input --}}
                    <div class="sm:col-span-6 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search by model, brand, engine displacement..."
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#FF6B00] focus:ring-2 focus:ring-[#FF6B00]/20 transition shadow-2xs font-medium">
                    </div>

                    {{-- Brand Selector --}}
                    <div class="sm:col-span-3">
                        <select name="brand"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-[#FF6B00] focus:ring-2 focus:ring-[#FF6B00]/20 transition shadow-2xs font-semibold cursor-pointer">
                            <option value="">All Brands ({{ $brands->count() }})</option>
                            @foreach($brands as $b)
                                <option value="{{ $b }}" {{ $brand === $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort Order --}}
                    <div class="sm:col-span-2">
                        <select name="sort"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm text-slate-900 focus:bg-white focus:border-[#FF6B00] focus:ring-2 focus:ring-[#FF6B00]/20 transition shadow-2xs font-semibold cursor-pointer">
                            <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Latest Arrivals</option>
                            <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="year_desc" {{ $sort === 'year_desc' ? 'selected' : '' }}>Model Year (Newest)</option>
                        </select>
                    </div>

                    {{-- Filter Button --}}
                    <div class="sm:col-span-1">
                        <button type="submit"
                                class="w-full h-full py-3 px-4 rounded-2xl bg-gradient-to-r from-slate-900 via-rose-600 to-[#FF6B00] hover:opacity-95 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm flex items-center justify-center cursor-pointer">
                            Filter
                        </button>
                    </div>
                </div>

                {{-- Category Filter Pills --}}
                <div class="pt-3 border-t border-slate-100 flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                    <span class="font-black text-slate-400 uppercase tracking-widest text-[10px] shrink-0 mr-1">Category:</span>
                    <a href="{{ route('showrooms.bikes.index', array_merge(['showroom' => $showroom], request()->except(['category', 'page']))) }}"
                       class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 {{ empty($category) ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        All Inventory
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('showrooms.bikes.index', array_merge(['showroom' => $showroom], request()->except('page'), ['category' => $cat->value])) }}"
                           class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 {{ $category === $cat->value ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            {{ $cat->label() }}
                        </a>
                    @endforeach
                </div>
            </form>
        </div>

        {{-- ═══ MOTORCYCLES GRID ═══ --}}
        @if($bikes->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 my-8 shadow-xs max-w-xl mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="text-xl font-black font-display text-slate-900">No Motorcycles Found</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    We currently do not have any motorcycles matching your criteria. Try resetting filters to explore our full fleet.
                </p>
                <div class="pt-2">
                    <a href="{{ route('showrooms.bikes.index', $showroom) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
                        <span>Clear All Filters</span>
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @foreach($bikes as $bike)
                    <div class="group bg-white rounded-3xl border border-slate-200/90 hover:border-slate-300 overflow-hidden flex flex-col transition-all duration-300 hover:shadow-xl relative shadow-xs">

                        {{-- Photo Container --}}
                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                            <img src="{{ $bike->primary_image_url }}" alt="{{ $bike->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">

                            {{-- Top Overlay Badges --}}
                            <div class="absolute top-3.5 inset-x-3.5 flex items-center justify-between gap-2 pointer-events-none">
                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-white/95 backdrop-blur text-slate-900 border border-slate-200/80 shadow-xs">
                                    {{ $bike->category->label() }}
                                </span>

                                <div class="flex items-center gap-1.5">
                                    {{-- 360 Available Pill --}}
                                    @if($showroom->canAccessFeature('has_360_viewer') && $bike->has360View())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-cyan-600 text-white shadow-xs">
                                            <svg class="w-3.5 h-3.5 animate-spin-slow text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            <span>360° Studio</span>
                                        </span>
                                    @endif

                                    @if($bike->is_featured)
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-amber-400 text-amber-950 shadow-xs">
                                            Featured
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Bottom Brand & Year Strip --}}
                            <div class="absolute bottom-3 left-3">
                                <span class="px-3 py-1 rounded-xl text-[10px] font-black bg-slate-900/90 text-white backdrop-blur shadow-xs">
                                    {{ $bike->brand }} • {{ $bike->model_year }}
                                </span>
                            </div>

                            {{-- In Stock Pill --}}
                            <div class="absolute bottom-3 right-3">
                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-emerald-500 text-white shadow-xs flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    <span>Ready for Delivery</span>
                                </span>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-6 flex-1 flex flex-col justify-between space-y-5">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <span class="text-[10px] font-black text-rose-600 uppercase tracking-widest block">{{ $bike->brand }}</span>
                                        <h3 class="font-display font-black text-xl text-slate-900 group-hover:text-rose-600 transition leading-snug mt-0.5">
                                            <a href="{{ route('showrooms.bikes.show', [$showroom, $bike]) }}">
                                                {{ $bike->name }}
                                            </a>
                                        </h3>
                                    </div>
                                    <span class="text-xs font-mono font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg shrink-0">
                                        {{ $bike->sku }}
                                    </span>
                                </div>

                                {{-- Telemetry Spec Matrix --}}
                                <div class="grid grid-cols-4 gap-2 my-4 pt-4 border-t border-slate-100 text-center">
                                    <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-200/80">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Engine</span>
                                        <span class="text-xs font-black text-slate-900 mt-0.5 block font-mono">
                                            {{ $bike->engine_cc ? $bike->engine_cc . ' cc' : 'EV' }}
                                        </span>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-200/80">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Power</span>
                                        <span class="text-xs font-black text-slate-900 mt-0.5 block font-mono">
                                            {{ $bike->power_bhp ? $bike->power_bhp . ' hp' : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-200/80">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Top Spd</span>
                                        <span class="text-xs font-black text-slate-900 mt-0.5 block font-mono">
                                            {{ $bike->top_speed_kmh ? $bike->top_speed_kmh . ' kmh' : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-200/80">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">Mileage</span>
                                        <span class="text-xs font-black text-slate-900 mt-0.5 block font-mono">
                                            {{ $bike->mileage_kmpl ? $bike->mileage_kmpl . ' km/l' : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Price & Buy Action Section --}}
                            <div class="pt-4 border-t border-slate-100 space-y-3">
                                <div class="flex items-baseline justify-between">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Ex-Showroom Price</span>
                                        <span class="font-mono font-black text-2xl text-slate-900 leading-tight">{{ $bike->formatted_price }}</span>
                                    </div>
                                    <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200">
                                        EMI from ₹{{ number_format(($bike->price * 0.022), 0) }}/mo
                                    </span>
                                </div>

                                {{-- Dual Actions: View 360 & Book / Buy --}}
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    @if($showroom->canAccessFeature('has_360_viewer') && $bike->has360View())
                                        <a href="{{ route('showrooms.bikes.show', [$showroom, $bike]) }}#studio360"
                                           class="py-2.5 px-3 rounded-xl bg-cyan-50 hover:bg-cyan-100 text-cyan-800 font-bold text-xs transition border border-cyan-200 flex items-center justify-center gap-1.5 shadow-2xs">
                                            <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            <span>360° Studio</span>
                                        </a>
                                    @else
                                        <a href="{{ route('showrooms.bikes.show', [$showroom, $bike]) }}"
                                           class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200 flex items-center justify-center gap-1.5 shadow-2xs">
                                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>View Details</span>
                                        </a>
                                    @endif

                                    <a href="{{ route('showrooms.bikes.show', [$showroom, $bike]) }}#reserve"
                                       class="py-2.5 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs uppercase tracking-wider transition shadow-md shadow-emerald-600/20 flex items-center justify-center gap-1.5">
                                        <span>Buy / Reserve</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($bikes->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $bikes->links() }}
                </div>
            @endif
        @endif

    </main>

    {{-- Motorcycle Showroom Footer --}}
    @include('showrooms.partials.footer')
</div>
@endsection
