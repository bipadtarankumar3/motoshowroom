@extends('layouts.app')

@section('title', 'Promotional Scratch Cards - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col"
     x-data="{
        copiedSlug: null,
        copyLink(url, slug) {
            navigator.clipboard.writeText(url);
            this.copiedSlug = slug;
            setTimeout(() => this.copiedSlug = null, 2500);
        },
        setDatePreset(days, type = 'schedule') {
            const today = new Date();
            const target = new Date();
            target.setDate(today.getDate() - days);

            const formatDate = (d) => d.toISOString().split('T')[0];

            const fromInput = document.getElementById('date_from_input');
            const toInput = document.getElementById('date_to_input');
            const typeInput = document.getElementById('date_type_select');

            if (typeInput) typeInput.value = type;
            if (fromInput) fromInput.value = formatDate(target);
            if (toInput) toInput.value = formatDate(today);
        },
        setTodayPreset(type = 'schedule') {
            const today = new Date().toISOString().split('T')[0];
            const fromInput = document.getElementById('date_from_input');
            const toInput = document.getElementById('date_to_input');
            const typeInput = document.getElementById('date_type_select');

            if (typeInput) typeInput.value = type;
            if (fromInput) fromInput.value = today;
            if (toInput) toInput.value = today;
        }
     }">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-6 sm:py-8 space-y-6">

        <!-- Flash Feedback Notifications -->
        @if (session('success'))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}" class="text-xs font-bold uppercase tracking-wider text-rose-600 underline hover:text-rose-700">Upgrade Plan</a>
            </div>
        @endif

        @if (! $hasModuleAccess)
            <div class="rounded-3xl bg-amber-50 border border-amber-200 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Promotional Scratch Cards Feature is Locked</h3>
                        <p class="text-xs text-slate-600 mt-0.5">Your current subscription tier does not include interactive promotional marketing campaigns. Upgrade to Pro/Enterprise to create campaigns and issue scratch cards.</p>
                    </div>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}"
                   class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-amber-600/20 transition whitespace-nowrap">
                    Upgrade Subscription
                </a>
            </div>
        @endif

        <!-- Quick In-Store Coupon Redemption Desk Bar -->
        <div class="rounded-2xl bg-gradient-to-r from-purple-50 via-white to-purple-50/80 border border-purple-200/90 p-4 sm:p-5 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">In-Store Coupon Redemption Desk</h4>
                        <p class="text-[11px] text-slate-500">Validate and redeem scratch card prize vouchers presented by customers at the billing or service desk.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('showrooms.manage.coupons.redeem', $showroom) }}" class="flex items-center gap-2 w-full md:w-auto">
                    @csrf
                    <input type="text"
                           name="coupon_code"
                           placeholder="ENTER COUPON (E.G. MOTO-ABCD-1234)"
                           required
                           class="w-full md:w-72 bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 uppercase font-mono tracking-wider focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 shadow-2xs">
                    <button type="submit"
                            id="btn-redeem-coupon"
                            class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs uppercase tracking-wider transition shrink-0 shadow-sm shadow-purple-600/20 cursor-pointer">
                        Redeem
                    </button>
                </form>
            </div>
        </div>

        <!-- Breadcrumbs & Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-purple-700 font-semibold">Promotions</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Promotional Scratch Cards</h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-600 animate-pulse"></span>
                        {{ $activeCampaigns }} Active {{ Str::plural('Campaign', $activeCampaigns) }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Design promotional campaigns, configure prize probabilities, and reward customers booking bikes or services.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('showrooms.promotions.index', $showroom) }}"
                   target="_blank"
                   class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>View Public Promotions</span>
                </a>

                @if ($hasModuleAccess)
                    <a href="{{ route('showrooms.manage.campaigns.create', $showroom) }}"
                       id="btn-add-campaign"
                       class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm shadow-purple-600/20 transition flex items-center gap-2 group cursor-pointer">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>New Campaign</span>
                    </a>
                @else
                    <button disabled class="opacity-50 cursor-not-allowed px-5 py-2.5 rounded-xl bg-slate-200 border border-slate-300 text-slate-500 font-bold text-xs flex items-center gap-2" title="Plan upgrade required">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>New Campaign</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- 6 Modern Telemetry Metric KPI Cards (matching Ledger design) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
            <!-- Total Campaigns -->
            <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}"
               class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs block group hover:border-slate-300 transition">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-slate-500">Total Campaigns</span>
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-slate-900 mt-2">{{ number_format($totalCampaigns) }}</p>
                <div class="text-[11px] text-slate-500 font-medium mt-1">
                    Active: <strong class="text-emerald-600 font-mono">{{ $activeCampaigns }}</strong>
                </div>
            </a>

            <!-- Active & Live -->
            <a href="{{ route('showrooms.manage.campaigns.index', array_merge([$showroom], request()->except('page'), ['preset' => 'active'])) }}"
               class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'active' ? 'bg-emerald-50/70 border-emerald-300 ring-2 ring-emerald-500/20' : 'bg-white border-slate-200/90 hover:border-emerald-200 hover:bg-emerald-50/30' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-emerald-700">Active & Live</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-emerald-600 mt-2">{{ number_format($activeCampaigns) }}</p>
                <div class="text-[11px] text-emerald-700/80 font-medium mt-1">
                    Upcoming: <strong class="font-mono">{{ $upcomingCampaigns }}</strong>
                </div>
            </a>

            <!-- Cards Issued -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs block group hover:border-purple-200 hover:bg-purple-50/20 transition">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-purple-700">Cards Issued</span>
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-purple-700 mt-2">{{ number_format($totalParticipations) }}</p>
                <div class="text-[11px] text-purple-700/80 font-medium mt-1">
                    Today: <strong class="font-mono text-purple-800">+{{ $participationsToday }}</strong>
                </div>
            </div>

            <!-- Cards Scratched -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs block group hover:border-sky-200 hover:bg-sky-50/20 transition">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-sky-700">Scratched</span>
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-sky-600 mt-2">{{ number_format($totalScratched) }}</p>
                <div class="text-[11px] text-sky-700/80 font-medium mt-1">
                    Today: <strong class="font-mono text-sky-800">+{{ $scratchedToday }}</strong>
                </div>
            </div>

            <!-- Coupons Redeemed -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs block group hover:border-amber-200 hover:bg-amber-50/20 transition">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-amber-700">Redeemed</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-amber-600 mt-2">{{ number_format($totalRedeemed) }}</p>
                <div class="text-[11px] text-amber-700/80 font-medium mt-1">
                    Pending visit: <strong class="font-mono text-amber-800">{{ $pendingRedemption }}</strong>
                </div>
            </div>

            <!-- Total Won Value -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-rose-700">Won Value</span>
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-rose-600 mt-2">₹{{ number_format($totalPrizeValue) }}</p>
                <div class="text-[11px] text-slate-500 font-medium mt-1">
                    Prize winners: <strong class="text-slate-800 font-mono">{{ $wonPrizesCount }}</strong>
                </div>
            </div>
        </div>

        <!-- Interactive Quick Preset Pills Bar -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border {{ !request()->filled('preset') && !request()->filled('status') && !request()->filled('applicable_to') && !request()->filled('from_date') ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                All Campaigns ({{ $totalCampaigns }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.index', array_merge([$showroom], request()->except('page'), ['preset' => 'active'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'active' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-emerald-700 border-emerald-200 hover:bg-emerald-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'active' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                ⚡ Active & Live ({{ $activeCampaigns }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.index', array_merge([$showroom], request()->except('page'), ['preset' => 'service'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'service' ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-indigo-700 border-indigo-200 hover:bg-indigo-50' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                🔧 Service Bookings ({{ $serviceCampaigns }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.index', array_merge([$showroom], request()->except('page'), ['preset' => 'bike'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'bike' ? 'bg-sky-600 text-white border-sky-600 shadow-xs' : 'bg-white text-sky-700 border-sky-200 hover:bg-sky-50' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                🏍️ Bike Bookings ({{ $bikeCampaigns }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.index', array_merge([$showroom], request()->except('page'), ['preset' => 'upcoming'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'upcoming' ? 'bg-purple-600 text-white border-purple-600 shadow-xs' : 'bg-white text-purple-700 border-purple-200 hover:bg-purple-50' }}">
                🗓️ Upcoming ({{ $upcomingCampaigns }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.index', array_merge([$showroom], request()->except('page'), ['preset' => 'drafts'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'drafts' ? 'bg-slate-700 text-white border-slate-700 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                📝 Drafts ({{ $draftCampaigns }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.index', array_merge([$showroom], request()->except('page'), ['preset' => 'expired'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'expired' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-rose-700 border-rose-200 hover:bg-rose-50' }}">
                🏁 Concluded ({{ $expiredCampaigns }})
            </a>
        </div>

        <!-- Comprehensive Modern Search, Filter & Date Presets Bar -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs space-y-4">
            <form method="GET" action="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="space-y-4">
                @if(request()->filled('preset'))
                    <input type="hidden" name="preset" value="{{ request('preset') }}">
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                    {{-- Search Input --}}
                    <div class="sm:col-span-2 lg:col-span-5 relative">
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search campaigns by name, slug, or description..."
                               class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 pl-9 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 shadow-2xs">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    {{-- Target Audience Filter --}}
                    <div class="sm:col-span-1 lg:col-span-3">
                        <select name="applicable_to" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 shadow-2xs">
                            <option value="all">All Target Audiences</option>
                            <option value="service" @selected($applicableTo === 'service')>🔧 Service & Repair Bookings</option>
                            <option value="bike" @selected($applicableTo === 'bike')>🏍️ Bike Purchase & Test Rides</option>
                        </select>
                    </div>

                    {{-- Status Dropdown --}}
                    <div class="sm:col-span-1 lg:col-span-3">
                        <select name="status" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 shadow-2xs">
                            <option value="all">All Campaign Statuses</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st->value }}" @selected($status === $st->value)>{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Primary Filter Button --}}
                    <div class="sm:col-span-2 lg:col-span-1">
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs py-2.5 px-3 rounded-xl transition shadow-xs cursor-pointer">
                            Filter
                        </button>
                    </div>
                </div>

                {{-- Date-Wise Search Bar --}}
                <div class="pt-3 border-t border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-3 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 font-bold text-slate-700">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Date Wise Search:
                        </span>

                        <select id="date_type_select" name="date_type" class="bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            <option value="schedule" @selected($dateType === 'schedule')>Campaign Schedule (Start/End)</option>
                            <option value="created" @selected($dateType === 'created')>Created Date</option>
                        </select>

                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-400 text-[11px]">From:</span>
                            <input type="date"
                                   id="date_from_input"
                                   name="from_date"
                                   value="{{ $fromDate }}"
                                   class="bg-white border border-slate-300 rounded-xl px-2.5 py-1.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        </div>

                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-400 text-[11px]">To:</span>
                            <input type="date"
                                   id="date_to_input"
                                   name="to_date"
                                   value="{{ $toDate }}"
                                   class="bg-white border border-slate-300 rounded-xl px-2.5 py-1.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        </div>

                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition cursor-pointer">
                            Apply Dates
                        </button>
                    </div>

                    {{-- Quick Date Presets --}}
                    <div class="flex items-center gap-1.5">
                        <span class="text-slate-400 text-[11px]">Quick Presets:</span>
                        <button type="button" @click="setTodayPreset('schedule'); $el.closest('form').submit()" class="px-2.5 py-1 rounded-lg text-xs font-bold text-purple-700 hover:bg-purple-50 transition cursor-pointer">Today</button>
                        <button type="button" @click="setDatePreset(7, 'schedule'); $el.closest('form').submit()" class="px-2.5 py-1 rounded-lg text-xs font-bold text-purple-700 hover:bg-purple-50 transition cursor-pointer">Last 7 Days</button>
                        <button type="button" @click="setDatePreset(30, 'schedule'); $el.closest('form').submit()" class="px-2.5 py-1 rounded-lg text-xs font-bold text-purple-700 hover:bg-purple-50 transition cursor-pointer">Last 30 Days</button>
                        @if(request()->hasAny(['search', 'status', 'applicable_to', 'from_date', 'to_date', 'preset']))
                            <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="px-2.5 py-1 rounded-lg text-xs font-bold text-rose-600 hover:bg-rose-50 transition">Reset</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Modern Campaigns Table -->
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4">Campaign Name & Target</th>
                            <th class="px-6 py-4">Active Schedule</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Prize Pool</th>
                            <th class="px-6 py-4">Cards Issued</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($campaigns as $camp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="font-bold text-slate-900 text-sm hover:text-purple-700 transition">
                                            <a href="{{ route('showrooms.manage.campaigns.participations', [$showroom, $camp]) }}">{{ $camp->name }}</a>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $camp->applicableBadgeClasses() }}">
                                            @if($camp->applicable_to === 'service')
                                                🔧 Service Bookings
                                            @elseif($camp->applicable_to === 'bike')
                                                🏍️ Bike Purchase
                                            @else
                                                All Visitors
                                            @endif
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[11px] text-slate-400 font-mono">/promotions/{{ $camp->slug }}</span>
                                        <button type="button"
                                                @click="copyLink('{{ route('showrooms.promotions.show', [$showroom, $camp]) }}', '{{ $camp->slug }}')"
                                                class="text-[11px] font-semibold text-purple-600 hover:text-purple-700 transition flex items-center gap-0.5 cursor-pointer">
                                            <span x-show="copiedSlug !== '{{ $camp->slug }}'">Copy Link</span>
                                            <span x-show="copiedSlug === '{{ $camp->slug }}'" class="text-emerald-600 font-bold" x-cloak>Copied! ✓</span>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-slate-800 font-semibold block">{{ $camp->start_date->format('M d, Y') }}</span>
                                    <span class="text-[11px] text-slate-500 block">to {{ $camp->end_date->format('M d, Y') }}</span>
                                    @if($camp->isCurrentlyActive())
                                        <span class="text-[10px] font-bold text-emerald-600">● Live Now</span>
                                    @elseif($camp->start_date->isFuture())
                                        <span class="text-[10px] font-bold text-purple-600">Starts in {{ $camp->start_date->diffForHumans(null, true) }}</span>
                                    @else
                                        <span class="text-[10px] text-slate-400">Concluded</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('showrooms.manage.campaigns.toggle-status', [$showroom, $camp]) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ $camp->status->badgeClasses() }} hover:opacity-90 cursor-pointer" title="Click to toggle status">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $camp->status === \App\Enums\CampaignStatus::Active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                            <span>{{ $camp->status->label() }}</span>
                                        </button>
                                    </form>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $camp->prizes_count }} Prize {{ Str::plural('Tier', $camp->prizes_count) }}</div>
                                    <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                        @php
                                            $topPrize = $camp->prizes->sortByDesc('discount_amount')->first();
                                        @endphp
                                        @if($topPrize)
                                            <span class="truncate max-w-[150px]" title="{{ $topPrize->title }}">{{ $topPrize->title }}</span>
                                        @else
                                            <span>Configured</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <a href="{{ route('showrooms.manage.campaigns.participations', [$showroom, $camp]) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-800 transition font-bold border border-purple-200">
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                        <span>{{ number_format($camp->participations_count) }}</span>
                                        <span class="text-[10px] text-purple-600 font-normal">Cards</span>
                                    </a>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Public Scratch Page --}}
                                        <a href="{{ route('showrooms.promotions.show', [$showroom, $camp]) }}"
                                           target="_blank"
                                           class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition"
                                           title="Public Scratch Page">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>

                                        {{-- Ledger --}}
                                        <a href="{{ route('showrooms.manage.campaigns.participations', [$showroom, $camp]) }}"
                                           class="p-2 rounded-xl bg-slate-100 hover:bg-purple-50 text-slate-600 hover:text-purple-700 transition"
                                           title="View Scratched Cards Ledger">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('showrooms.manage.campaigns.edit', [$showroom, $camp]) }}"
                                           class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition"
                                           title="Edit Campaign">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('showrooms.manage.campaigns.destroy', [$showroom, $camp]) }}" onsubmit="return confirm('Delete promotional campaign &quot;{{ $camp->name }}&quot;?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition cursor-pointer" title="Delete Campaign">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                    <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto mb-3 border border-purple-100">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                                    </div>
                                    <p class="font-bold text-slate-800 text-sm">No Promotional Campaigns Found</p>
                                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">No campaigns matched your search or selected filter parameters.</p>
                                    <div class="mt-4 flex items-center justify-center gap-2">
                                        <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                            Reset Filters
                                        </a>
                                        @if($hasModuleAccess)
                                            <a href="{{ route('showrooms.manage.campaigns.create', $showroom) }}" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition">
                                                Create Campaign
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($campaigns->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>

    </main>
</div>
@endsection
