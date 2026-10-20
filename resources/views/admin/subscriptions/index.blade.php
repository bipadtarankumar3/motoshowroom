<x-app-layout title="Subscriptions Oversight & Billing Engine | Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="bg-slate-50 min-h-screen py-6 sm:py-8 px-4 sm:px-6 lg:px-8 xl:px-10 w-full"
         x-data="{
             selectedPlan: null,
             extendModal: false,
             switchPlanModal: false,
             activeSubscription: null,
             selectedIds: [],
             allPageIds: {{ json_encode($subscriptions->pluck('id')->values()) }},
             showDateFilter: {{ request()->filled('from_date') || request()->filled('to_date') || request()->filled('date_type') ? 'true' : 'false' }},
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
             setDatePreset(days, type = 'expiry') {
                 const today = new Date();
                 const target = new Date();
                 target.setDate(today.getDate() + days);

                 const formatDate = (d) => d.toISOString().split('T')[0];

                 const fromInput = document.getElementById('date_from_input');
                 const toInput = document.getElementById('date_to_input');
                 const typeInput = document.getElementById('date_type_select');

                 if (typeInput) typeInput.value = type;
                 if (fromInput) fromInput.value = formatDate(today);
                 if (toInput) toInput.value = formatDate(target);
             },
             setPastExpiredPreset() {
                 const yesterday = new Date();
                 yesterday.setDate(yesterday.getDate() - 1);
                 const formatDate = (d) => d.toISOString().split('T')[0];

                 const fromInput = document.getElementById('date_from_input');
                 const toInput = document.getElementById('date_to_input');
                 const typeInput = document.getElementById('date_type_select');

                 if (typeInput) typeInput.value = 'expiry';
                 if (fromInput) fromInput.value = '';
                 if (toInput) toInput.value = formatDate(yesterday);
             },
             openExtend(sub) {
                 this.activeSubscription = sub;
                 this.extendModal = true;
             },
             openSwitch(sub) {
                 this.activeSubscription = sub;
                 this.switchPlanModal = true;
             }
         }">
        <div class="space-y-6 sm:space-y-8 w-full">

            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Admin</a>
                <span>/</span>
                <span class="text-rose-600 font-semibold">Subscriptions Oversight</span>
            </div>

            <!-- Top Header & Actions -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Subscriptions Oversight</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                            Billing Engine
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Tenant recurring revenue calculations, renewal forecasts, manual expiry extensions, and plan controls.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Export CSV Dropdown Menu -->
                    <div class="relative" x-data="{ exportMenuOpen: false }">
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
                            <a href="{{ route('admin.subscriptions.export', request()->all()) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-slate-900">Export Filtered</div>
                                    <div class="text-[10px] text-slate-400">Current search & date matches ({{ $subscriptions->total() }})</div>
                                </div>
                            </a>

                            <a href="{{ route('admin.subscriptions.export') }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 transition border-t border-slate-100">
                                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-slate-900">Export All Subscriptions</div>
                                    <div class="text-[10px] text-slate-400">All {{ $stats['total'] }} database records</div>
                                </div>
                            </a>

                            <div x-show="selectedIds.length > 0" class="border-t border-slate-100">
                                <form method="POST" action="{{ route('admin.subscriptions.export') }}">
                                    @csrf
                                    <template x-for="id in selectedIds" :key="id">
                                        <input type="hidden" name="selected_ids[]" :value="id">
                                    </template>
                                    <button type="submit"
                                            class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-xs text-rose-700 bg-rose-50/50 hover:bg-rose-50 transition cursor-pointer">
                                        <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-rose-800">Export Selected (<span x-text="selectedIds.length"></span>)</div>
                                            <div class="text-[10px] text-rose-600">Only manually checked rows</div>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('admin.plans.index') }}"
                       class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold transition flex items-center gap-2 shadow-xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Plans & Pricing</span>
                    </a>

                    <a href="{{ route('admin.showrooms.create') }}"
                       class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold transition flex items-center gap-2 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>New Showroom</span>
                    </a>
                </div>
            </div>

            <!-- Financial Calculation Telemetry Cards (Interactive Presets) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                <!-- Total Platform Revenue Earned -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-emerald-700">Total Earned</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-emerald-600 mt-2">₹{{ number_format($financials['total_earned']) }}</p>
                    <div class="text-[11px] text-slate-500 font-medium mt-1">
                        ARR: <strong class="text-slate-800 font-mono">₹{{ number_format($financials['arr']) }}</strong>/yr
                    </div>
                </div>

                <!-- Monthly Recurring Revenue (MRR) -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-slate-500">Platform MRR</span>
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-slate-900 mt-2">₹{{ number_format($financials['mrr']) }}<span class="text-xs font-normal text-slate-400">/mo</span></p>
                    <div class="text-[11px] text-slate-500 font-medium mt-1">
                        ARPU: <strong class="text-slate-800 font-mono">₹{{ number_format($financials['arpu']) }}</strong>/tenant
                    </div>
                </div>

                <!-- Expiring in 7 Days (🚨 High Priority) -->
                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'expiring_7'])) }}"
                   class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'expiring_7' ? 'bg-rose-50/70 border-rose-300 ring-2 ring-rose-500/20' : 'bg-white border-slate-200/90 hover:border-rose-200 hover:bg-rose-50/30' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-rose-700">Expiring in 7D</span>
                        <span class="w-2 h-2 rounded-full bg-rose-500 {{ $stats['expiring_7_days'] > 0 ? 'animate-ping' : '' }}"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-rose-600 mt-2">{{ number_format($stats['expiring_7_days']) }}</p>
                    <div class="text-[11px] text-rose-700/80 font-medium mt-1">
                        At risk: <strong class="font-mono">₹{{ number_format($financials['at_risk_7_days']) }}</strong>
                    </div>
                </a>

                <!-- Expiring in 30 Days (⚠️ Renewals Forecast) -->
                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'expiring_30'])) }}"
                   class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'expiring_30' ? 'bg-amber-50/70 border-amber-300 ring-2 ring-amber-500/20' : 'bg-white border-slate-200/90 hover:border-amber-200 hover:bg-amber-50/30' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-amber-700">Expiring in 30D</span>
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-amber-600 mt-2">{{ number_format($stats['expiring_30_days']) }}</p>
                    <div class="text-[11px] text-amber-700/80 font-medium mt-1">
                        Renewals: <strong class="font-mono">₹{{ number_format($financials['at_risk_30_days']) }}</strong>
                    </div>
                </a>

                <!-- Active Subscribers -->
                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'active'])) }}"
                   class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'active' ? 'bg-sky-50/70 border-sky-300 ring-2 ring-sky-500/20' : 'bg-white border-slate-200/90 hover:border-sky-200 hover:bg-sky-50/30' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-sky-700">Active Tenants</span>
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-sky-600 mt-2">{{ number_format($stats['active']) }}</p>
                    <div class="text-[11px] text-sky-700/80 font-medium mt-1">
                        Auto-renew: <strong class="font-mono">{{ $stats['auto_renew'] }}</strong>
                    </div>
                </a>

                <!-- Expired / Past Due -->
                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'expired'])) }}"
                   class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'expired' ? 'bg-slate-100 border-slate-300 ring-2 ring-slate-400/20' : 'bg-white border-slate-200/90 hover:border-slate-300 hover:bg-slate-50' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-slate-600">Expired / Lapsed</span>
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-slate-700 mt-2">{{ number_format($stats['expired']) }}</p>
                    <div class="text-[11px] text-slate-500 font-medium mt-1">
                        Past due: <strong class="font-mono">{{ $stats['past_due'] }}</strong>
                    </div>
                </a>
            </div>

            <!-- Quick Filter Presets (Pills) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <a href="{{ route('admin.subscriptions.index') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border {{ !request()->filled('preset') && !request()->filled('status') && !request()->filled('plan_id') && !request()->filled('billing_interval') ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                    All Subscriptions ({{ $stats['total'] }})
                </a>

                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'expiring_7'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'expiring_7' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-rose-700 border-rose-200 hover:bg-rose-50' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'expiring_7' ? 'bg-white' : 'bg-rose-500' }}"></span>
                    🚨 Expiring in 7 Days ({{ $stats['expiring_7_days'] }})
                </a>

                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'expiring_30'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'expiring_30' ? 'bg-amber-600 text-white border-amber-600 shadow-xs' : 'bg-white text-amber-700 border-amber-200 hover:bg-amber-50' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'expiring_30' ? 'bg-white' : 'bg-amber-500' }}"></span>
                    ⚠️ Expiring in 30 Days ({{ $stats['expiring_30_days'] }})
                </a>

                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'expired'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'expired' ? 'bg-slate-800 text-white border-slate-800 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'expired' ? 'bg-white' : 'bg-slate-400' }}"></span>
                    🔴 Expired / Lapsed ({{ $stats['expired'] }})
                </a>

                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'active'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'active' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-emerald-700 border-emerald-200 hover:bg-emerald-50' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'active' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                    🟢 Active ({{ $stats['active'] }})
                </a>

                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'trial'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'trial' ? 'bg-amber-500 text-white border-amber-500 shadow-xs' : 'bg-white text-amber-800 border-amber-200 hover:bg-amber-50' }}">
                    🟡 In Trial ({{ $stats['trial'] }})
                </a>

                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'past_due'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'past_due' ? 'bg-orange-600 text-white border-orange-600 shadow-xs' : 'bg-white text-orange-700 border-orange-200 hover:bg-orange-50' }}">
                    🟠 Past Due / Grace ({{ $stats['past_due'] }})
                </a>

                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'auto_renew'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'auto_renew' ? 'bg-sky-600 text-white border-sky-600 shadow-xs' : 'bg-white text-sky-700 border-sky-200 hover:bg-sky-50' }}">
                    ⚡ Auto-Renew ON ({{ $stats['auto_renew'] }})
                </a>

                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'annual'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'annual' ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-indigo-700 border-indigo-200 hover:bg-indigo-50' }}">
                    💎 Annual ({{ $stats['annual'] }})
                </a>

                <a href="{{ route('admin.subscriptions.index', array_merge(request()->except('page'), ['preset' => 'monthly'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'monthly' ? 'bg-slate-700 text-white border-slate-700 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                    🗓️ Monthly ({{ $stats['monthly'] }})
                </a>
            </div>

            <!-- Multi-Filter Toolbar with Date-Wise Search -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs space-y-4">
                <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="space-y-3.5">
                    {{-- Row 1: Search & Primary Dimensions --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-center">
                        {{-- Search Input (4 cols) --}}
                        <div class="md:col-span-3 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search showroom, owner, email..."
                                   class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        {{-- Status Select (2 cols) --}}
                        <div class="md:col-span-2">
                            <select name="status" class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                                <option value="past_due" {{ request('status') === 'past_due' ? 'selected' : '' }}>Past Due</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        {{-- Plan Select (2 cols) --}}
                        <div class="md:col-span-2">
                            <select name="plan_id" class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <option value="">All Plans</option>
                                @foreach($plans as $p)
                                    <option value="{{ $p->id }}" {{ request('plan_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Billing Interval (2 cols) --}}
                        <div class="md:col-span-2">
                            <select name="billing_interval" class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <option value="">All Cycles</option>
                                <option value="monthly" {{ request('billing_interval') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="annual" {{ request('billing_interval') === 'annual' ? 'selected' : '' }}>Annual</option>
                            </select>
                        </div>

                        {{-- Sort Order (2 cols) --}}
                        <div class="md:col-span-2">
                            <select name="sort" class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <option value="expiry_asc" {{ request('sort') === 'expiry_asc' ? 'selected' : '' }}>Expiry (Soonest)</option>
                                <option value="expiry_desc" {{ request('sort') === 'expiry_desc' ? 'selected' : '' }}>Expiry (Furthest)</option>
                                <option value="created_desc" {{ request('sort') === 'created_desc' ? 'selected' : '' }}>Newest Subscribed</option>
                            </select>
                        </div>

                        {{-- Filter Action Buttons (1 col) --}}
                        <div class="md:col-span-1 flex items-center gap-1.5">
                            <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs cursor-pointer flex items-center justify-center">
                                Filter
                            </button>
                            @if(request()->hasAny(['search', 'status', 'plan_id', 'billing_interval', 'expiry_range', 'preset', 'sort', 'from_date', 'to_date', 'date_type']))
                                <a href="{{ route('admin.subscriptions.index') }}" class="px-2 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition" title="Reset All Filters">
                                    ✕
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Row 2: Date-Wise Search Section with Presets --}}
                    <div class="pt-3 border-t border-slate-100 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3 text-xs">
                        <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                            <span class="font-bold text-slate-700 flex items-center gap-1.5 shrink-0">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Date Wise Search:</span>
                            </span>

                            {{-- Date Column Selector --}}
                            <select name="date_type" id="date_type_select" class="px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-700 text-xs font-medium focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <option value="expiry" {{ request('date_type', 'expiry') === 'expiry' ? 'selected' : '' }}>Expiry / Period End</option>
                                <option value="start" {{ request('date_type') === 'start' ? 'selected' : '' }}>Period Start</option>
                                <option value="created" {{ request('date_type') === 'created' ? 'selected' : '' }}>Subscribed Date</option>
                                <option value="payment" {{ request('date_type') === 'payment' ? 'selected' : '' }}>Last Payment Date</option>
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

                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold transition text-xs shadow-xs cursor-pointer">
                                Apply Dates
                            </button>
                        </div>

                        {{-- Quick Date Preset Shortcut Pills --}}
                        <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                            <span class="text-slate-400 font-medium">Quick Dates:</span>
                            <button type="button" @click="setDatePreset(7, 'expiry')" class="px-2 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition cursor-pointer">
                                Next 7D
                            </button>
                            <button type="button" @click="setDatePreset(30, 'expiry')" class="px-2 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition cursor-pointer">
                                Next 30D
                            </button>
                            <button type="button" @click="setPastExpiredPreset()" class="px-2 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition cursor-pointer">
                                Past Expired
                            </button>
                            @if(request()->filled('from_date') || request()->filled('to_date'))
                                <a href="{{ route('admin.subscriptions.index', request()->except(['from_date', 'to_date', 'date_type'])) }}"
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

            <!-- Subscriptions Ledger Table with Calculation UI & Checkbox Selection -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 text-[11px] tracking-wider">
                            <tr>
                                <th class="px-4 py-4 w-12 text-center">
                                    <input type="checkbox"
                                           id="selectAllSubscriptions"
                                           @change="toggleSelectAll($event)"
                                           :checked="isAllSelected()"
                                           title="Select all rows on this page"
                                           class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500 cursor-pointer">
                                </th>
                                <th class="px-5 py-4 min-w-[220px]">Showroom Dealership & Owner</th>
                                <th class="px-5 py-4 min-w-[170px]">Plan & Price Calculation</th>
                                <th class="px-5 py-4 min-w-[140px]">Revenue Earned</th>
                                <th class="px-5 py-4 min-w-[220px]">Billing Cycle & Expiry Forecast</th>
                                <th class="px-5 py-4 min-w-[120px]">Auto-Renew</th>
                                <th class="px-5 py-4 min-w-[100px]">Status</th>
                                <th class="px-5 py-4 text-right min-w-[160px]">Administrative Controls</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($subscriptions as $subscription)
                                @php
                                    $plan = $subscription->plan;
                                    $showroom = $subscription->showroom;
                                    $owner = $showroom?->ownerMembership?->user;

                                    // Cycle calculations
                                    $startDate = $subscription->current_period_start ?? $subscription->created_at;
                                    $endDate = $subscription->current_period_end;
                                    $totalDays = max(1, $startDate->diffInDays($endDate));
                                    $daysElapsed = min($totalDays, max(0, $startDate->diffInDays(now())));
                                    $cycleProgress = min(100, max(0, round(($daysElapsed / $totalDays) * 100)));

                                    $isExpired = $subscription->isExpired() || ($endDate && $endDate->isPast());
                                    $daysRemaining = $subscription->days_remaining;

                                    // Pricing & Revenue calculations
                                    $cyclePrice = $subscription->billing_interval === 'annual'
                                        ? (float) ($plan?->annual_price ?? 0)
                                        : (float) ($plan?->monthly_price ?? 0);

                                    $normalizedMrr = $subscription->billing_interval === 'annual'
                                        ? ($cyclePrice / 12)
                                        : $cyclePrice;

                                    $annualArr = $normalizedMrr * 12;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition" :class="selectedIds.includes({{ $subscription->id }}) ? 'bg-rose-50/40' : ''">
                                    <!-- Row Select Checkbox -->
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox"
                                               name="selected_ids[]"
                                               :value="{{ $subscription->id }}"
                                               x-model="selectedIds"
                                               class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500 cursor-pointer">
                                    </td>

                                    <!-- Showroom Tenant & Owner Details -->
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white text-sm shrink-0 shadow-2xs font-display"
                                                 style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                                                {{ strtoupper(substr($showroom->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.showrooms.show', $showroom) }}" class="font-bold text-slate-900 hover:text-rose-600 transition block truncate text-sm">
                                                    {{ $showroom->name }}
                                                </a>
                                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                                                    <span>{{ $showroom->city ?: 'Showroom' }}</span>
                                                    <span>•</span>
                                                    <span class="font-mono text-slate-400">/{{ $showroom->slug }}</span>
                                                </div>
                                                @if($owner)
                                                    <div class="text-[11px] text-slate-600 mt-0.5">
                                                        Owner: <a href="{{ route('admin.users.show', $owner) }}" class="font-semibold text-slate-700 hover:text-rose-600 transition">{{ $owner->name }}</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Plan & Price Calculation -->
                                    <td class="px-5 py-4">
                                        <div class="space-y-1">
                                            <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 inline-block">
                                                {{ $plan?->name ?: 'Custom Plan' }}
                                            </span>
                                            <div class="font-bold text-slate-900 text-sm font-mono">
                                                ₹{{ number_format($cyclePrice) }}
                                                <span class="text-[11px] text-slate-400 font-normal uppercase">/ {{ $subscription->billing_interval }}</span>
                                            </div>
                                            <div class="text-[10px] text-slate-500 font-medium">
                                                Normalized MRR: <strong class="text-slate-700 font-mono">₹{{ number_format($normalizedMrr) }}/mo</strong>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Revenue Earned / Lifetime Collected -->
                                    <td class="px-5 py-4">
                                        <div class="space-y-1">
                                            <div class="font-bold text-emerald-600 text-sm font-mono">
                                                ₹{{ number_format($cyclePrice) }}
                                            </div>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Paid Current Period
                                            </span>
                                            <div class="text-[10px] text-slate-400">
                                                Est. LTV: <strong class="text-slate-600 font-mono">₹{{ number_format($annualArr) }}</strong>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Billing Cycle & Expiry Countdown Calculation Bar -->
                                    <td class="px-5 py-4">
                                        <div class="space-y-1.5">
                                            <div class="flex items-center justify-between text-[11px]">
                                                <span class="font-bold text-slate-800 font-mono">{{ $endDate ? $endDate->format('M d, Y') : 'N/A' }}</span>

                                                @if($isExpired)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                        🔴 Expired
                                                    </span>
                                                @elseif($daysRemaining <= 7)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300 animate-pulse">
                                                        🚨 {{ $daysRemaining }} days left
                                                    </span>
                                                @elseif($daysRemaining <= 30)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                        ⚠️ {{ $daysRemaining }} days left
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        🟢 {{ $daysRemaining }} days left
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Visual Progress Bar -->
                                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-1.5 rounded-full transition-all duration-300 {{ $isExpired ? 'bg-rose-500' : ($daysRemaining <= 7 ? 'bg-rose-500' : ($daysRemaining <= 30 ? 'bg-amber-500' : 'bg-emerald-500')) }}"
                                                     style="width: {{ $cycleProgress }}%"></div>
                                            </div>

                                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono">
                                                <span>{{ $startDate ? $startDate->format('M d, Y') : '' }}</span>
                                                <span>{{ $cycleProgress }}% elapsed</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Auto-Renew Status -->
                                    <td class="px-5 py-4">
                                        @if($subscription->auto_renew)
                                            <div class="space-y-0.5">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Auto-Renew ON
                                                </span>
                                                <div class="text-[10px] text-slate-400">Next: ₹{{ number_format($cyclePrice) }}</div>
                                            </div>
                                        @else
                                            <div class="space-y-0.5">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 inline-block">
                                                    Manual Renewal
                                                </span>
                                                <div class="text-[10px] text-amber-600 font-medium">Invoicing required</div>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Lifecycle Status -->
                                    <td class="px-5 py-4">
                                        <span class="px-2.5 py-1 rounded text-xs font-bold border {{ $subscription->status->badgeClasses() }}">
                                            {{ $subscription->status->label() }}
                                        </span>
                                    </td>

                                    <!-- Administrative Actions -->
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Extend Dropdown -->
                                            <details class="relative">
                                                <summary class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 text-xs font-bold cursor-pointer list-none flex items-center gap-1 shadow-2xs">
                                                    <span>+ Extend</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </summary>
                                                <div class="absolute right-0 mt-2 w-72 p-4 rounded-2xl bg-white border border-slate-200 shadow-xl z-30 text-left space-y-3">
                                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                                        <h4 class="text-xs font-bold text-slate-900">Extend Expiry Date</h4>
                                                        <span class="text-[10px] font-bold text-emerald-600">{{ $showroom->name }}</span>
                                                    </div>
                                                    <form method="POST" action="{{ route('admin.subscriptions.extend', $subscription) }}" class="space-y-3">
                                                        @csrf
                                                        <div>
                                                            <label class="block text-[10px] uppercase font-bold text-slate-600 mb-1">Select Horizon / Days</label>
                                                            <select name="days" class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                                                <option value="7">+7 Days (Trial / Grace Extension)</option>
                                                                <option value="14">+14 Days (Two Weeks Extension)</option>
                                                                <option value="30" selected>+30 Days (One Month Extension)</option>
                                                                <option value="90">+90 Days (Quarterly Extension)</option>
                                                                <option value="365">+365 Days (Full Annual Extension)</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] uppercase font-bold text-slate-600 mb-1">Extension Reason</label>
                                                            <input type="text" name="reason" value="Super admin extension granted" required class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                                        </div>
                                                        <button type="submit" class="w-full py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                                                            Confirm Extension
                                                        </button>
                                                    </form>
                                                </div>
                                            </details>

                                            <!-- Switch Plan Dropdown -->
                                            <details class="relative">
                                                <summary class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold cursor-pointer list-none flex items-center gap-1 shadow-2xs">
                                                    <span>Switch Plan</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </summary>
                                                <div class="absolute right-0 mt-2 w-64 p-4 rounded-2xl bg-white border border-slate-200 shadow-xl z-30 text-left space-y-3">
                                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                                        <h4 class="text-xs font-bold text-slate-900">Switch Plan Tier</h4>
                                                    </div>
                                                    <form method="POST" action="{{ route('admin.subscriptions.change-plan', $subscription) }}" class="space-y-3">
                                                        @csrf
                                                        <div>
                                                            <label class="block text-[10px] uppercase font-bold text-slate-600 mb-1">Target Plan</label>
                                                            <select name="plan_id" required class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                                                @foreach($plans as $p)
                                                                    <option value="{{ $p->id }}" {{ $subscription->plan_id == $p->id ? 'selected' : '' }}>
                                                                        {{ $p->name }} (₹{{ number_format($p->monthly_price) }}/mo)
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="w-full py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                                                            Update Plan Tier
                                                        </button>
                                                    </form>
                                                </div>
                                            </details>

                                            <!-- Special Feature Access & Overrides Dropdown -->
                                            @php
                                                $subOverrides = $subscription->feature_overrides ?? $showroom?->settings['feature_overrides'] ?? [];
                                            @endphp
                                            <details class="relative">
                                                <summary class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-900 text-xs font-bold cursor-pointer list-none flex items-center gap-1 shadow-2xs"
                                                         title="Grant Extra Feature Access">
                                                    <span>⭐ Extra Access</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </summary>
                                                <div class="absolute right-0 mt-2 w-84 p-5 rounded-2xl bg-white border border-slate-200 shadow-2xl z-30 text-left space-y-4">
                                                    <div class="border-b border-slate-100 pb-3">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-800 bg-amber-100 px-2 py-0.5 rounded border border-amber-300">
                                                                VIP Feature Grants
                                                            </span>
                                                            <span class="text-[11px] font-bold text-slate-800">{{ $plan?->name }}</span>
                                                        </div>
                                                        <h4 class="text-sm font-black text-slate-900 mt-1">{{ $showroom->name }}</h4>
                                                        <p class="text-[11px] text-slate-500">Grant features to this user even if not in their plan.</p>
                                                    </div>

                                                    <form method="POST" action="{{ route('admin.subscriptions.feature-overrides', $subscription) }}" class="space-y-3 text-xs">
                                                        @csrf

                                                        <!-- Features Stack -->
                                                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                                            @php
                                                                $featureList = [
                                                                    'has_ai_assistant' => ['AI Assistant', '🤖', (bool) ($plan?->has_ai_assistant ?? false)],
                                                                    'has_360_viewer' => ['360° Viewer', '🔄', (bool) ($plan?->has_360_viewer ?? false)],
                                                                    'has_custom_domain' => ['Custom Domain', '🌐', (bool) ($plan?->has_custom_domain ?? false)],
                                                                    'has_scratch_cards' => ['Scratch Cards', '🎁', (bool) ($plan?->has_scratch_cards ?? false)],
                                                                    'has_careers_module' => ['Careers Portal', '💼', (bool) ($plan?->has_careers_module ?? false)],
                                                                    'has_advanced_analytics' => ['Advanced Analytics', '📊', (bool) ($plan?->has_advanced_analytics ?? false)],
                                                                ];
                                                            @endphp

                                                            @foreach($featureList as $key => [$label, $icon, $inPlan])
                                                                @php
                                                                    $currentVal = isset($subOverrides[$key])
                                                                        ? ($subOverrides[$key] ? 'grant' : 'deny')
                                                                        : 'inherit';
                                                                @endphp
                                                                <div class="p-2 rounded-xl border border-slate-200 bg-slate-50/60 flex items-center justify-between gap-2">
                                                                    <div class="min-w-0">
                                                                        <div class="font-bold text-slate-900 text-[11px] truncate flex items-center gap-1">
                                                                            <span>{{ $icon }}</span> {{ $label }}
                                                                        </div>
                                                                        <div class="text-[10px] {{ $inPlan ? 'text-emerald-700' : 'text-slate-400' }}">
                                                                            {{ $inPlan ? 'Plan: Included' : 'Plan: Not in plan' }}
                                                                        </div>
                                                                    </div>
                                                                    <select name="{{ $key }}" class="px-2 py-1 rounded-lg bg-white border border-slate-300 text-[11px] font-semibold text-slate-800 focus:ring-1 focus:ring-rose-500">
                                                                        <option value="inherit" {{ $currentVal === 'inherit' ? 'selected' : '' }}>Follow Plan</option>
                                                                        <option value="grant" {{ $currentVal === 'grant' ? 'selected' : '' }}>⭐ Grant Access</option>
                                                                        <option value="deny" {{ $currentVal === 'deny' ? 'selected' : '' }}>🚫 Disable</option>
                                                                    </select>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <!-- Custom Max Bike Listings -->
                                                        <div class="pt-2 border-t border-slate-100">
                                                            <label class="block text-[10px] uppercase font-bold text-slate-600 mb-1">Custom Max Active Bikes (Optional)</label>
                                                            <input type="number" name="max_bike_listings" value="{{ $subOverrides['max_bike_listings'] ?? '' }}" min="1" placeholder="Plan: {{ $plan?->max_bike_listings ?? 25 }}"
                                                                   class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-1 focus:ring-rose-500">
                                                        </div>

                                                        <button type="submit" class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition cursor-pointer">
                                                            Save Special Feature Grants
                                                        </button>
                                                    </form>
                                                </div>
                                            </details>

                                            <!-- Showroom Manage Link -->
                                            <a href="{{ route('admin.showrooms.show', $showroom) }}"
                                               class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 transition shadow-2xs"
                                               title="View Showroom Details">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center text-slate-500">
                                        <div class="max-w-sm mx-auto space-y-3">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            </div>
                                            <p class="text-base font-bold text-slate-800">No Subscriptions Found</p>
                                            <p class="text-xs text-slate-500">No tenant subscription records matched your selected filters or search parameters.</p>
                                            <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold inline-block">
                                                Reset Filters
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($subscriptions->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                        {{ $subscriptions->links() }}
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
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs sm:text-sm font-bold text-white">
                        <span x-text="selectedIds.length" class="font-mono text-emerald-400 text-base"></span> subscriptions selected
                    </span>
                </div>

                <div class="flex items-center gap-2.5">
                    <form method="POST" action="{{ route('admin.subscriptions.export') }}" class="inline">
                        @csrf
                        <template x-for="id in selectedIds" :key="id">
                            <input type="hidden" name="selected_ids[]" :value="id">
                        </template>
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-md">
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

        </div>
    </div>
    @include('admin.partials.nav-close')
</x-app-layout>
