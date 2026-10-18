@extends('layouts.app')

@section('title', 'Promotions & Scratch Cards - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    @include('showrooms.partials.header')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        <!-- Promotions Hero Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-50 via-white to-slate-50 border border-purple-200 p-8 sm:p-12 mb-10 shadow-sm">
            <div class="absolute -right-16 -bottom-16 w-80 h-80 rounded-full bg-purple-500/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-3">
                    <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-slate-900 transition">Home</a>
                    <span>/</span>
                    <span class="text-purple-600 font-bold">Promotions</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-black font-display text-slate-900 tracking-tight leading-tight">
                    Exclusive Rewards & <span class="text-purple-600">Scratch-and-Win</span>
                </h1>
                <p class="text-base sm:text-lg text-slate-600 mt-4 leading-relaxed">
                    Claim your digital scratch cards, unlock instant workshop discounts, riding gear vouchers, and exclusive savings at <strong class="text-slate-900">{{ $showroom->name }}</strong>.
                </p>
            </div>
        </div>

        <!-- Active Campaigns Grid -->
        @if($campaigns->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($campaigns as $camp)
                    <div class="rounded-3xl bg-white border border-slate-200 p-6 flex flex-col justify-between hover:border-purple-400 hover:shadow-xl transition group shadow-xs">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs px-2.5 py-1 rounded-lg font-bold bg-purple-50 text-purple-700 border border-purple-200 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                                    Active Promotion
                                </span>
                                <span class="text-xs text-slate-500 font-medium">
                                    Until {{ $camp->end_date->format('M d, Y') }}
                                </span>
                            </div>

                            <h3 class="text-xl font-bold font-display text-slate-900 group-hover:text-purple-600 transition mb-2">
                                {{ $camp->name }}
                            </h3>

                            <p class="text-xs text-slate-600 mb-4 line-clamp-2 leading-relaxed">
                                {{ $camp->description ?: 'Participate in this official showroom promotion to reveal instant prizes and discount coupons.' }}
                            </p>

                            <!-- Prize Pool Teaser -->
                            @if($camp->prizes->count() > 0)
                                <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200 mb-6">
                                    <span class="text-[10px] uppercase font-bold text-slate-500 block mb-2">Featured Prize Rewards</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($camp->prizes->take(3) as $pz)
                                            <span class="text-[11px] px-2 py-0.5 rounded font-semibold {{ $pz->prize_type->badgeClasses() }}">
                                                {{ $pz->title }}
                                            </span>
                                        @endforeach
                                        @if($camp->prizes->count() > 3)
                                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold">
                                                +{{ $camp->prizes->count() - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-500">
                                Max <strong>{{ $camp->max_participations_per_customer }}</strong> entry per customer
                            </span>

                            <a href="{{ route('showrooms.promotions.show', [$showroom, $camp]) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md shadow-purple-600/20 transition">
                                <span>Claim Scratch Card</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="rounded-3xl bg-white border border-slate-200 p-12 text-center max-w-xl mx-auto shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto mb-4 border border-purple-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                </div>
                <h3 class="text-xl font-bold font-display text-slate-900 mb-2">No Active Promotions Right Now</h3>
                <p class="text-sm text-slate-600 mb-6">
                    Our team is currently preparing the next festive promotional campaign. Follow our updates or book a test-ride today.
                </p>
                <a href="{{ route('showrooms.show', $showroom) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-bold text-slate-700 transition">
                    Explore Showroom Storefront
                </a>
            </div>
        @endif

    </main>

    @include('showrooms.partials.footer')
</div>
@endsection
