@extends('layouts.app')

@section('title', 'Participations - ' . $campaign->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col"
     x-data="{
         selectedIds: [],
         allPageIds: {{ json_encode($participations->pluck('id')->values()) }},
         exportMenuOpen: false,
         redeemModalOpen: false,
         quickCouponCode: '',
         toggleSelectAll(e) {
             if (e.target.checked) {
                 this.selectedIds = [...new Set([...this.selectedIds, ...this.allPageIds])];
             } else {
                 this.selectedIds = this.selectedIds.filter(id => !this.allPageIds.includes(id));
             }
         },
         isAllSelected() {
             return this.allPageIds.length > 0 && this.allPageIds.every(id => this.selectedIds.includes(id));
         },
         clearSelection() {
             this.selectedIds = [];
         },
         setTodayPreset(type = 'created') {
             const today = new Date().toISOString().split('T')[0];
             const fromInput = document.getElementById('date_from_input');
             const toInput = document.getElementById('date_to_input');
             const typeInput = document.getElementById('date_type_select');

             if (typeInput) typeInput.value = type;
             if (fromInput) fromInput.value = today;
             if (toInput) toInput.value = today;
         },
         setDatePreset(days, type = 'created') {
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
         openRedeem(code) {
             this.quickCouponCode = code;
             this.redeemModalOpen = true;
         }
     }">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-6 sm:py-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="hover:text-slate-900 transition">Campaigns</a>
            <span>/</span>
            <span class="text-purple-700 font-semibold truncate max-w-xs sm:max-w-md">Participations: {{ $campaign->name }}</span>
        </div>

        <!-- Header & Action Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Campaign Scratch Card Ledger</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                        {{ $campaign->name }}
                    </span>
                    @if($campaign->isCurrentlyActive())
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $campaign->status->label() }}
                        </span>
                    @endif
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Audit customer participations, track scratch reveals, check won prizes, and redeem in-store coupons.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Export CSV Menu -->
                <div class="relative">
                    <button @click="exportMenuOpen = !exportMenuOpen"
                            type="button"
                            class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold transition flex items-center gap-2 shadow-xs cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export CSV</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="exportMenuOpen"
                         @click.away="exportMenuOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-30">
                        <a href="{{ route('showrooms.manage.campaigns.participations.export', array_merge([$showroom, $campaign], request()->all())) }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            </div>
                            <div class="text-left">
                                <div class="font-bold text-slate-900">Export Filtered</div>
                                <div class="text-[10px] text-slate-400">Current search & date matches ({{ $participations->total() }})</div>
                            </div>
                        </a>

                        <a href="{{ route('showrooms.manage.campaigns.participations.export', [$showroom, $campaign]) }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition border-t border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </div>
                            <div class="text-left">
                                <div class="font-bold text-slate-900">Export All Cards</div>
                                <div class="text-[10px] text-slate-400">All {{ $stats['total'] }} campaign participations</div>
                            </div>
                        </a>

                        <div x-show="selectedIds.length > 0" class="border-t border-slate-100">
                            <form method="POST" action="{{ route('showrooms.manage.campaigns.participations.export', [$showroom, $campaign]) }}">
                                @csrf
                                <template x-for="id in selectedIds" :key="id">
                                    <input type="hidden" name="selected_ids[]" :value="id">
                                </template>
                                <button type="submit"
                                        class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-xs text-purple-800 bg-purple-50/50 hover:bg-purple-50 transition cursor-pointer">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-purple-900">Export Selected (<span x-text="selectedIds.length"></span>)</div>
                                        <div class="text-[10px] text-purple-600">Only manually selected rows</div>
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Quick Redeem Button -->
                <button type="button"
                        @click="openRedeem('')"
                        class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs sm:text-sm font-bold transition flex items-center gap-2 shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    <span>Redeem In-Store Coupon</span>
                </button>

                <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}"
                   class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 shadow-xs">
                    &larr; Back to Campaigns
                </a>
            </div>
        </div>

        <!-- 6 Telemetry Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
            <!-- Total Participations -->
            <a href="{{ route('showrooms.manage.campaigns.participations', [$showroom, $campaign]) }}"
               class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs block group hover:border-slate-300 transition">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-slate-500">Total Cards</span>
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-slate-900 mt-2">{{ number_format($stats['total']) }}</p>
                <div class="text-[11px] text-slate-500 font-medium mt-1">
                    Today: <strong class="text-purple-700 font-mono">+{{ $stats['today'] }}</strong>
                </div>
            </a>

            <!-- Revealed / Scratched -->
            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except('page'), ['preset' => 'scratched'])) }}"
               class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'scratched' ? 'bg-purple-50/70 border-purple-300 ring-2 ring-purple-500/20' : 'bg-white border-slate-200/90 hover:border-purple-200 hover:bg-purple-50/30' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-purple-700">Scratched</span>
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-purple-700 mt-2">{{ number_format($stats['scratched']) }}</p>
                <div class="text-[11px] text-purple-700/80 font-medium mt-1">
                    Today: <strong class="font-mono">+{{ $stats['scratched_today'] }}</strong>
                </div>
            </a>

            <!-- Unscratched Pending -->
            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except('page'), ['preset' => 'unscratched'])) }}"
               class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'unscratched' ? 'bg-indigo-50/70 border-indigo-300 ring-2 ring-indigo-500/20' : 'bg-white border-slate-200/90 hover:border-indigo-200 hover:bg-indigo-50/30' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-indigo-700">Unscratched</span>
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-indigo-600 mt-2">{{ number_format($stats['unscratched']) }}</p>
                <div class="text-[11px] text-indigo-700/80 font-medium mt-1">
                    Awaiting customer reveal
                </div>
            </a>

            <!-- Redeemed In-Store -->
            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except('page'), ['preset' => 'redeemed'])) }}"
               class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'redeemed' ? 'bg-emerald-50/70 border-emerald-300 ring-2 ring-emerald-500/20' : 'bg-white border-slate-200/90 hover:border-emerald-200 hover:bg-emerald-50/30' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-emerald-700">Redeemed</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-emerald-600 mt-2">{{ number_format($stats['redeemed']) }}</p>
                <div class="text-[11px] text-emerald-700/80 font-medium mt-1">
                    Today: <strong class="font-mono">+{{ $stats['redeemed_today'] }}</strong>
                </div>
            </a>

            <!-- Pending In-Store Redemption -->
            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except('page'), ['preset' => 'pending'])) }}"
               class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'pending' ? 'bg-amber-50/70 border-amber-300 ring-2 ring-amber-500/20' : 'bg-white border-slate-200/90 hover:border-amber-200 hover:bg-amber-50/30' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-amber-700">Awaiting Visit</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-amber-600 mt-2">{{ number_format($stats['pending_redemption']) }}</p>
                <div class="text-[11px] text-amber-700/80 font-medium mt-1">
                    Active unredeemed vouchers
                </div>
            </a>

            <!-- Won Prize Value -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-rose-700">Won Value</span>
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                </div>
                <p class="text-2xl sm:text-3xl font-black font-display text-rose-600 mt-2">₹{{ number_format($stats['total_prize_value']) }}</p>
                <div class="text-[11px] text-slate-500 font-medium mt-1">
                    Prize winners: <strong class="text-slate-800 font-mono">{{ $stats['won_prizes'] }}</strong>
                </div>
            </div>
        </div>

        <!-- Interactive Quick Preset Pills Bar -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <a href="{{ route('showrooms.manage.campaigns.participations', [$showroom, $campaign]) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border {{ !request()->filled('preset') && !request()->filled('scratched') && !request()->filled('redeemed') && !request()->filled('prize_id') && !request()->filled('from_date') ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                All Cards ({{ $stats['total'] }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except('page'), ['preset' => 'today'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'today' ? 'bg-purple-600 text-white border-purple-600 shadow-xs' : 'bg-white text-purple-700 border-purple-200 hover:bg-purple-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'today' ? 'bg-white' : 'bg-purple-500' }}"></span>
                ⚡ Claimed Today ({{ $stats['today'] }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except('page'), ['preset' => 'scratched_today'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'scratched_today' ? 'bg-purple-600 text-white border-purple-600 shadow-xs' : 'bg-white text-purple-700 border-purple-200 hover:bg-purple-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'scratched_today' ? 'bg-white' : 'bg-purple-500' }}"></span>
                ✨ Scratched Today ({{ $stats['scratched_today'] }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except('page'), ['preset' => 'scratched'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'scratched' ? 'bg-purple-700 text-white border-purple-700 shadow-xs' : 'bg-white text-purple-800 border-purple-200 hover:bg-purple-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'scratched' ? 'bg-white' : 'bg-purple-500' }}"></span>
                🎉 All Scratched ({{ $stats['scratched'] }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except('page'), ['preset' => 'unscratched'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'unscratched' ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-indigo-700 border-indigo-200 hover:bg-indigo-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'unscratched' ? 'bg-white' : 'bg-indigo-400' }}"></span>
                🎟️ Unscratched ({{ $stats['unscratched'] }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except('page'), ['preset' => 'redeemed'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'redeemed' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-emerald-700 border-emerald-200 hover:bg-emerald-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'redeemed' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                ✅ Redeemed ({{ $stats['redeemed'] }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except('page'), ['preset' => 'pending'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'pending' ? 'bg-amber-600 text-white border-amber-600 shadow-xs' : 'bg-white text-amber-700 border-amber-200 hover:bg-amber-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'pending' ? 'bg-white' : 'bg-amber-500' }}"></span>
                ⏳ Pending In-Store ({{ $stats['pending_redemption'] }})
            </a>

            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except('page'), ['preset' => 'won'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'won' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-rose-700 border-rose-200 hover:bg-rose-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'won' ? 'bg-white' : 'bg-rose-500' }}"></span>
                🎁 Won Prizes ({{ $stats['won_prizes'] }})
            </a>
        </div>

        <!-- Multi-Filter Toolbar with Date-Wise Search -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs space-y-4">
            <form method="GET" action="{{ route('showrooms.manage.campaigns.participations', [$showroom, $campaign]) }}" class="space-y-3.5">
                {{-- Row 1: Search & Dimension Filters --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-center">
                    {{-- Search Input (4 cols) --}}
                    <div class="md:col-span-3 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search customer, phone, code..."
                               class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 text-xs focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    </div>

                    {{-- Scratch State Select (2 cols) --}}
                    <div class="md:col-span-2">
                        <select name="scratched" class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                            <option value="">All Scratch States</option>
                            <option value="yes" {{ request('scratched') === 'yes' ? 'selected' : '' }}>Scratched Only</option>
                            <option value="no" {{ request('scratched') === 'no' ? 'selected' : '' }}>Unscratched Only</option>
                        </select>
                    </div>

                    {{-- Redemption State Select (2 cols) --}}
                    <div class="md:col-span-2">
                        <select name="redeemed" class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                            <option value="">All Redemption</option>
                            <option value="yes" {{ request('redeemed') === 'yes' ? 'selected' : '' }}>Redeemed In-Store</option>
                            <option value="no" {{ request('redeemed') === 'no' ? 'selected' : '' }}>Unredeemed</option>
                        </select>
                    </div>

                    {{-- Prize Tier Select (2 cols) --}}
                    <div class="md:col-span-2">
                        <select name="prize_id" class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                            <option value="">All Prize Tiers</option>
                            <option value="any" {{ request('prize_id') === 'any' ? 'selected' : '' }}>Any Prize Won</option>
                            <option value="none" {{ request('prize_id') === 'none' ? 'selected' : '' }}>No Prize (Blank)</option>
                            @foreach($prizes as $prize)
                                <option value="{{ $prize->id }}" {{ request('prize_id') == $prize->id ? 'selected' : '' }}>{{ $prize->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort Order (2 cols) --}}
                    <div class="md:col-span-2">
                        <select name="sort" class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest Claimed</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest Claimed</option>
                            <option value="scratched_desc" {{ request('sort') === 'scratched_desc' ? 'selected' : '' }}>Recently Scratched</option>
                            <option value="redeemed_desc" {{ request('sort') === 'redeemed_desc' ? 'selected' : '' }}>Recently Redeemed</option>
                            <option value="customer_asc" {{ request('sort') === 'customer_asc' ? 'selected' : '' }}>Customer Name (A-Z)</option>
                        </select>
                    </div>

                    {{-- Filter Action Buttons (1 col) --}}
                    <div class="md:col-span-1 flex items-center gap-1.5">
                        <button type="submit" class="w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition shadow-xs cursor-pointer flex items-center justify-center">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'scratched', 'redeemed', 'prize_id', 'preset', 'sort', 'from_date', 'to_date', 'date_type']))
                            <a href="{{ route('showrooms.manage.campaigns.participations', [$showroom, $campaign]) }}"
                               class="px-2 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition" title="Reset All Filters">
                                ✕
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Row 2: Date-Wise Search Controls & Presets --}}
                <div class="pt-3 border-t border-slate-100 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3 text-xs">
                    <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                        <span class="font-bold text-slate-700 flex items-center gap-1.5 shrink-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Date Wise Search:</span>
                        </span>

                        {{-- Date Column Selector --}}
                        <select name="date_type" id="date_type_select" class="px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-700 text-xs font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                            <option value="created" {{ request('date_type', 'created') === 'created' ? 'selected' : '' }}>Claim / Issue Date</option>
                            <option value="scratched" {{ request('date_type') === 'scratched' ? 'selected' : '' }}>Scratch Reveal Date</option>
                            <option value="redeemed" {{ request('date_type') === 'redeemed' ? 'selected' : '' }}>Redemption Date</option>
                        </select>

                        {{-- From Date Input --}}
                        <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1">
                            <span class="text-[11px] text-slate-400 font-medium">From:</span>
                            <input type="date" name="from_date" id="date_from_input" value="{{ request('from_date') }}"
                                   class="bg-transparent text-slate-800 text-xs font-mono border-0 p-0 focus:ring-0">
                        </div>

                        {{-- To Date Input --}}
                        <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1">
                            <span class="text-[11px] text-slate-400 font-medium">To:</span>
                            <input type="date" name="to_date" id="date_to_input" value="{{ request('to_date') }}"
                                   class="bg-transparent text-slate-800 text-xs font-mono border-0 p-0 focus:ring-0">
                        </div>

                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold transition text-xs shadow-xs cursor-pointer">
                            Apply Dates
                        </button>
                    </div>

                    {{-- Quick Date Preset Shortcut Pills --}}
                    <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                        <span class="text-slate-400 font-medium">Quick Presets:</span>
                        <button type="button" @click="setTodayPreset()" class="px-2 py-1 rounded-md bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold transition cursor-pointer">
                            Today
                        </button>
                        <button type="button" @click="setDatePreset(7, 'created')" class="px-2 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition cursor-pointer">
                            Last 7 Days
                        </button>
                        <button type="button" @click="setDatePreset(30, 'created')" class="px-2 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition cursor-pointer">
                            Last 30 Days
                        </button>
                        @if(request()->filled('from_date') || request()->filled('to_date'))
                            <a href="{{ route('showrooms.manage.campaigns.participations', array_merge([$showroom, $campaign], request()->except(['from_date', 'to_date', 'date_type']))) }}"
                               class="px-2 py-1 rounded-md bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold transition">
                                Clear Dates ✕
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Preset Preservation --}}
                @if(request()->filled('preset'))
                    <input type="hidden" name="preset" value="{{ request('preset') }}">
                @endif
            </form>
        </div>

        <!-- Participations Table with Checkbox Row Selection -->
        <div class="rounded-3xl bg-white border border-slate-200/90 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-4 w-12 text-center">
                                <input type="checkbox"
                                       id="selectAllParticipations"
                                       @change="toggleSelectAll($event)"
                                       :checked="isAllSelected()"
                                       title="Select all rows on this page"
                                       class="w-4 h-4 text-purple-600 rounded border-slate-300 focus:ring-purple-500 cursor-pointer">
                            </th>
                            <th class="px-5 py-4 min-w-[200px]">Customer & Contact</th>
                            <th class="px-5 py-4 min-w-[130px]">Scratch State</th>
                            <th class="px-5 py-4 min-w-[180px]">Prize Allocated</th>
                            <th class="px-5 py-4 min-w-[140px]">Coupon Code</th>
                            <th class="px-5 py-4 min-w-[180px]">Redemption Status</th>
                            <th class="px-5 py-4 min-w-[110px]">Claim Date</th>
                            <th class="px-5 py-4 text-right min-w-[120px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($participations as $part)
                            @php
                                $prize = $part->prize;
                                $redeemedBy = $part->redeemedBy;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition" :class="selectedIds.includes({{ $part->id }}) ? 'bg-purple-50/40' : ''">
                                <!-- Row Checkbox -->
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox"
                                           name="selected_ids[]"
                                           :value="{{ $part->id }}"
                                           x-model="selectedIds"
                                           class="w-4 h-4 text-purple-600 rounded border-slate-300 focus:ring-purple-500 cursor-pointer">
                                </td>

                                <!-- Customer Details -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-800 font-black flex items-center justify-center shrink-0 text-sm shadow-2xs font-display">
                                            {{ strtoupper(substr($part->customer_name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 text-sm truncate">{{ $part->customer_name }}</div>
                                            <div class="text-[11px] text-slate-500 mt-0.5 flex flex-wrap items-center gap-1.5">
                                                <span>{{ $part->customer_email }}</span>
                                                @if($part->customer_phone)
                                                    <span>•</span>
                                                    <span>{{ $part->customer_phone }}</span>
                                                @endif
                                            </div>
                                            @if($part->booking_type === 'service' || $part->service_booking_id)
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 rounded-md">
                                                        🔧 Service Appointment
                                                    </span>
                                                </div>
                                            @elseif($part->booking_type === 'bike' || $part->enquiry_id)
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-sky-700 bg-sky-50 border border-sky-200 px-1.5 py-0.5 rounded-md">
                                                        🏍️ Bike Purchase / Test Ride
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Scratch State -->
                                <td class="px-5 py-4">
                                    @if($part->is_scratched)
                                        <div class="space-y-0.5">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Revealed
                                            </span>
                                            <div class="text-[10px] text-slate-400">
                                                {{ $part->scratched_at?->format('M d, Y H:i') ?? 'Revealed' }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="space-y-0.5">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 inline-block">
                                                Unscratched
                                            </span>
                                            <div class="text-[10px] text-slate-400">Token: <span class="font-mono">{{ substr($part->participation_token, 0, 8) }}...</span></div>
                                        </div>
                                    @endif
                                </td>

                                <!-- Prize Allocated -->
                                <td class="px-5 py-4">
                                    @if($prize)
                                        <div class="space-y-1">
                                            <div class="font-bold text-slate-900 text-xs">{{ $prize->title }}</div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[10px] px-2 py-0.5 rounded font-semibold inline-block {{ $prize->prize_type->badgeClasses() }}">
                                                    {{ $prize->prize_type->label() }}
                                                </span>
                                                @if($prize->discount_amount > 0)
                                                    <span class="text-[11px] font-mono font-bold text-emerald-600">₹{{ number_format($prize->discount_amount) }}</span>
                                                @elseif($prize->discount_percentage > 0)
                                                    <span class="text-[11px] font-mono font-bold text-emerald-600">{{ $prize->discount_percentage }}% Off</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic text-xs">No prize / blank card</span>
                                    @endif
                                </td>

                                <!-- Coupon Code -->
                                <td class="px-5 py-4">
                                    @if($part->coupon_code)
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-900 font-mono font-bold text-xs border border-purple-200 tracking-wider">
                                                {{ $part->coupon_code }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>

                                <!-- Redemption Status -->
                                <td class="px-5 py-4">
                                    @if($part->is_redeemed)
                                        <div class="space-y-0.5">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Redeemed In-Store
                                            </span>
                                            <div class="text-[10px] text-slate-500">
                                                {{ $part->redeemed_at?->format('M d, Y') }}
                                                @if($redeemedBy)
                                                    <span>by {{ $redeemedBy->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($part->is_scratched && $part->coupon_code)
                                        <div class="space-y-0.5">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Pending In-Store Visit
                                            </span>
                                            <div class="text-[10px] text-amber-700/80">Valid for redemption</div>
                                        </div>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 inline-block">
                                            Unrevealed
                                        </span>
                                    @endif
                                </td>

                                <!-- Claim Date -->
                                <td class="px-5 py-4 text-slate-600 text-xs font-mono">
                                    {{ $part->created_at->format('M d, Y') }}
                                    <div class="text-[10px] text-slate-400 font-sans">{{ $part->created_at->format('h:i A') }}</div>
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-4 text-right">
                                    @if(!$part->is_redeemed && $part->coupon_code)
                                        <button type="button"
                                                @click="openRedeem('{{ $part->coupon_code }}')"
                                                class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-bold transition shadow-2xs cursor-pointer inline-flex items-center gap-1">
                                            <span>Redeem</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </button>
                                    @elseif($part->is_redeemed)
                                        <span class="text-emerald-600 text-xs font-bold inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Done
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-slate-500">
                                    <div class="max-w-sm mx-auto space-y-3">
                                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                        </div>
                                        <p class="text-base font-bold text-slate-800">No Participations Found</p>
                                        <p class="text-xs text-slate-500">No customer scratch cards matched your query or selected date parameters.</p>
                                        <a href="{{ route('showrooms.manage.campaigns.participations', [$showroom, $campaign]) }}"
                                           class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold inline-block transition shadow-xs">
                                            Reset Filters
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($participations->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                    {{ $participations->links() }}
                </div>
            @endif
        </div>

        <!-- Floating Batch Selection & Export Action Bar -->
        <div x-show="selectedIds.length > 0"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-6"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-6"
             class="fixed bottom-6 inset-x-0 mx-auto max-w-xl z-40 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center justify-between gap-4 border border-slate-800">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-pulse"></span>
                <span class="text-xs sm:text-sm font-bold text-white">
                    <span x-text="selectedIds.length" class="font-mono text-purple-400 text-base"></span> cards selected
                </span>
            </div>

            <div class="flex items-center gap-2.5">
                <form method="POST" action="{{ route('showrooms.manage.campaigns.participations.export', [$showroom, $campaign]) }}" class="inline">
                    @csrf
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="selected_ids[]" :value="id">
                    </template>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Export Selected (<span x-text="selectedIds.length"></span>)</span>
                    </button>
                </form>

                <button type="button"
                        @click="clearSelection()"
                        class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-medium transition cursor-pointer">
                    Deselect
                </button>
            </div>
        </div>

        <!-- In-Store Coupon Redemption Modal -->
        <div x-show="redeemModalOpen"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            <div @click.away="redeemModalOpen = false"
                 class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900">In-Store Coupon Redemption</h3>
                            <p class="text-xs text-slate-500">Validate and record coupon redemption</p>
                        </div>
                    </div>
                    <button type="button" @click="redeemModalOpen = false" class="text-slate-400 hover:text-slate-600 transition">
                        ✕
                    </button>
                </div>

                <form method="POST" action="{{ route('showrooms.manage.coupons.redeem', $showroom) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-600 mb-1.5">Coupon Code</label>
                        <input type="text"
                               name="coupon_code"
                               x-model="quickCouponCode"
                               required
                               placeholder="e.g. MOTO-WIN-9823"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 font-mono font-bold text-sm uppercase focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                        <p class="text-[11px] text-slate-400 mt-1">Enter the unique code shown on the customer's revealed scratch card.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button"
                                @click="redeemModalOpen = false"
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition shadow-xs cursor-pointer">
                            Confirm Redemption
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</div>
@endsection
