<x-app-layout title="Platform Analytics & Intelligence | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Hero Header & Period Filter -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-black font-display text-slate-900 tracking-tight">Platform Financial Intelligence</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Executive Telemetry
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mt-1">Cross-tenant GMV, recurring subscription metrics (MRR/ARR), and dealership growth analytics.</p>
                </div>

                <!-- Time Horizon Filter -->
                <div class="inline-flex items-center p-1 rounded-2xl bg-white border border-slate-200 shadow-xs text-xs">
                    @foreach(['7d' => '7D', '30d' => '30D', '90d' => '90D', '365d' => '1Y', 'all' => 'ALL'] as $key => $label)
                        <a href="{{ route('admin.analytics.index', ['period' => $key]) }}"
                           class="px-3.5 py-1.5 rounded-xl font-bold transition {{ $period === $key ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Key Financial Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- GMV Card -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Platform Gross GMV</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $analytics['formatted_gmv'] }}</div>
                    <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                        <span>{{ $analytics['total_transactions'] }} captured charges</span>
                        <span class="text-emerald-700 font-semibold">Processed</span>
                    </div>
                </div>

                <!-- MRR Card -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Monthly Recurring (MRR)</span>
                        <div class="w-8 h-8 rounded-xl bg-cyan-50 border border-cyan-200 flex items-center justify-center text-cyan-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $analytics['formatted_mrr'] }}</div>
                    <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                        <span>Active recurring run-rate</span>
                        <span class="text-cyan-700 font-semibold">Normalized</span>
                    </div>
                </div>

                <!-- ARR Card -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Annual Recurring (ARR)</span>
                        <div class="w-8 h-8 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $analytics['formatted_arr'] }}</div>
                    <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                        <span>MRR &times; 12 Projected</span>
                        <span class="text-purple-700 font-semibold">Forward ARR</span>
                    </div>
                </div>

                <!-- Active Showrooms Card -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Active Showrooms</span>
                        <div class="w-8 h-8 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-slate-900 tracking-tight">{{ $analytics['showrooms']['active'] }}</div>
                    <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                        <span>of {{ $analytics['showrooms']['total'] }} registered</span>
                        <span class="text-rose-700 font-semibold">{{ $analytics['showrooms']['trial'] }} in trial</span>
                    </div>
                </div>
            </div>

            <!-- Two-Column Portfolio & Plans Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- 1. Showroom Tenant Breakdown -->
                <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-xs">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Tenant Showrooms Portfolio
                    </h3>
                    <p class="text-xs text-slate-500 mb-6">Distribution of dealership tenants across account statuses.</p>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Active</span>
                            <span class="text-2xl font-black text-emerald-700 font-mono mt-1 block">{{ $analytics['showrooms']['active'] }}</span>
                            <span class="text-[10px] text-slate-400 block mt-1">Operational</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Trialing</span>
                            <span class="text-2xl font-black text-amber-700 font-mono mt-1 block">{{ $analytics['showrooms']['trial'] }}</span>
                            <span class="text-[10px] text-slate-400 block mt-1">Free Sandbox</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Suspended</span>
                            <span class="text-2xl font-black text-rose-700 font-mono mt-1 block">{{ $analytics['showrooms']['suspended'] }}</span>
                            <span class="text-[10px] text-slate-400 block mt-1">Locked / Past Due</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Subscription Plans Distribution -->
                <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-xs">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        Active Subscribers by Plan
                    </h3>
                    <p class="text-xs text-slate-500 mb-6">Tier distribution and monetization density across tiers.</p>

                    <div class="space-y-3">
                        @forelse($analytics['plans_breakdown'] as $plan)
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $plan['name'] }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono">₹{{ number_format($plan['monthly_price'], 2) }}/month</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-cyan-700 text-sm">{{ $plan['active_subscribers'] }}</span>
                                    <span class="text-[10px] text-slate-400">subscribers</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">No plans configured yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
    @include('admin.partials.nav-close')
</x-app-layout>
