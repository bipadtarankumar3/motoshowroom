@extends('layouts.app')

@section('title', 'Advanced Analytics - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-rose-500 selection:text-white">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Top Header & Time Filter -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-white transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-rose-400">Analytics & BI</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">Business Intelligence & Analytics</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/15 text-rose-400 border border-rose-500/30">
                        Pro Metrics
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Multi-dimensional operational telemetry, conversion funnel diagnostics, and revenue metrics.</p>
            </div>

            <!-- Period Switcher & CSV Export Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Time Horizon Filter -->
                <div class="inline-flex items-center p-1 rounded-2xl bg-slate-900 border border-slate-800 text-xs">
                    @foreach(['7d' => '7D', '30d' => '30D', '90d' => '90D', '365d' => '1Y', 'all' => 'ALL'] as $key => $label)
                        <a href="{{ route('showrooms.manage.analytics.index', [$showroom, 'period' => $key]) }}"
                           class="px-3 py-1.5 rounded-xl font-bold transition {{ $period === $key ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-400 hover:text-white' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                @if($hasFeature)
                    <!-- Quick Export Dropdown / Buttons -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('showrooms.manage.analytics.export', [$showroom, 'type' => 'revenue', 'period' => $period]) }}"
                           class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition flex items-center gap-1.5"
                           title="Export captured payments CSV">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Revenue CSV</span>
                        </a>

                        <a href="{{ route('showrooms.manage.analytics.export', [$showroom, 'type' => 'enquiries', 'period' => $period]) }}"
                           class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition flex items-center gap-1.5"
                           title="Export enquiries & leads CSV">
                            <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Leads CSV</span>
                        </a>

                        <a href="{{ route('showrooms.manage.analytics.export', [$showroom, 'type' => 'workshop', 'period' => $period]) }}"
                           class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition flex items-center gap-1.5"
                           title="Export workshop bookings CSV">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Workshop CSV</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Plan Gated Banner (if plan lacks advanced analytics feature) -->
        @if(! $hasFeature)
            <div class="mb-8 rounded-3xl bg-amber-500/10 border border-amber-500/30 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xl">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">Advanced Analytics & BI Gated</h3>
                        <p class="text-xs text-amber-300/90 mt-0.5">
                            Your current subscription plan does not include Advanced Business Intelligence and CSV Reporting. Upgrade to the Pro or Enterprise plan to unlock multi-period financial metrics, conversion funnels, and unthrottled data exports.
                        </p>
                    </div>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}"
                   class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shrink-0 transition shadow-lg shadow-amber-500/20">
                    Upgrade Subscription
                </a>
            </div>
        @else

            <!-- Metric KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Revenue Card -->
                <div class="p-5 rounded-3xl bg-slate-900/60 border border-slate-800 shadow-xl relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Gross Captured</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-white tracking-tight">{{ $analytics['revenue']['formatted_total'] }}</div>
                    <div class="mt-2 text-xs text-slate-400 flex items-center justify-between">
                        <span>{{ $analytics['revenue']['total_transactions'] }} transactions</span>
                        <span class="text-emerald-400 font-semibold">Avg {{ $analytics['revenue']['avg_transaction_value'] }}</span>
                    </div>
                </div>

                <!-- Sales Funnel Conversion Rate -->
                <div class="p-5 rounded-3xl bg-slate-900/60 border border-slate-800 shadow-xl relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lead Conversion</span>
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-white tracking-tight">{{ $analytics['enquiries']['conversion_rate'] }}%</div>
                    <div class="mt-2 text-xs text-slate-400 flex items-center justify-between">
                        <span>{{ $analytics['enquiries']['total'] }} total enquiries</span>
                        <span class="text-cyan-400 font-semibold">{{ $analytics['enquiries']['closed_won'] }} closed deals</span>
                    </div>
                </div>

                <!-- Workshop Utilization -->
                <div class="p-5 rounded-3xl bg-slate-900/60 border border-slate-800 shadow-xl relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Service Completion</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-white tracking-tight">{{ $analytics['workshop']['completion_rate'] }}%</div>
                    <div class="mt-2 text-xs text-slate-400 flex items-center justify-between">
                        <span>{{ $analytics['workshop']['total'] }} booked jobs</span>
                        <span class="text-amber-400 font-semibold">{{ $analytics['workshop']['completed'] }} completed</span>
                    </div>
                </div>

                <!-- Marketing ROI & AI Lead Rate -->
                <div class="p-5 rounded-3xl bg-slate-900/60 border border-slate-800 shadow-xl relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">AI Leads Rate</span>
                        <div class="w-8 h-8 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-white tracking-tight">{{ $analytics['ai_assistant']['conversion_rate'] }}%</div>
                    <div class="mt-2 text-xs text-slate-400 flex items-center justify-between">
                        <span>{{ $analytics['ai_assistant']['total_chats'] }} conversations</span>
                        <span class="text-rose-400 font-semibold">{{ $analytics['ai_assistant']['converted_leads'] }} leads captured</span>
                    </div>
                </div>
            </div>

            <!-- Two-Column Operational Deep Dives -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

                <!-- 1. Sales Conversion Funnel & Enquiry Classification -->
                <div class="p-6 rounded-3xl bg-slate-900/60 border border-slate-800 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                    Sales Pipeline Conversion Funnel
                                </h3>
                                <p class="text-xs text-slate-400 mt-1">Lifecycle transition progression from inbound lead to closed sale.</p>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 font-bold">
                                {{ $analytics['enquiries']['total'] }} Leads
                            </span>
                        </div>

                        <!-- Funnel Bars -->
                        <div class="space-y-4">
                            @php
                                $totalE = max(1, $analytics['enquiries']['total']);
                                $stages = [
                                    ['label' => '1. New Enquiries', 'count' => $analytics['enquiries']['funnel']['new'], 'color' => 'bg-slate-400'],
                                    ['label' => '2. Staff Contacted', 'count' => $analytics['enquiries']['funnel']['contacted'], 'color' => 'bg-blue-400'],
                                    ['label' => '3. Test Ride Booked', 'count' => $analytics['enquiries']['funnel']['test_ride_scheduled'], 'color' => 'bg-indigo-400'],
                                    ['label' => '4. Negotiation / Quotation', 'count' => $analytics['enquiries']['funnel']['negotiation'], 'color' => 'bg-purple-400'],
                                    ['label' => '5. Closed Won (Booking / Sale)', 'count' => $analytics['enquiries']['funnel']['completed'], 'color' => 'bg-emerald-400'],
                                ];
                            @endphp

                            @foreach($stages as $stage)
                                @php
                                    $pct = round(($stage['count'] / $totalE) * 100);
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1.5 font-semibold">
                                        <span class="text-slate-300">{{ $stage['label'] }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-white font-mono">{{ $stage['count'] }}</span>
                                            <span class="text-slate-500 text-[10px]">({{ $pct }}%)</span>
                                        </div>
                                    </div>
                                    <div class="h-2.5 w-full bg-slate-950 rounded-full overflow-hidden border border-slate-800">
                                        <div class="h-full {{ $stage['color'] }} rounded-full transition-all duration-500" style="width: {{ max(4, $pct) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Enquiry Types Split -->
                    <div class="pt-6 mt-6 border-t border-slate-800/80 flex items-center justify-around text-center">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Purchase</span>
                            <span class="text-base font-bold text-white">{{ $analytics['enquiries']['by_type']['purchase'] }}</span>
                        </div>
                        <div class="h-8 w-px bg-slate-800"></div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Test Rides</span>
                            <span class="text-base font-bold text-white">{{ $analytics['enquiries']['by_type']['test_ride'] }}</span>
                        </div>
                        <div class="h-8 w-px bg-slate-800"></div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">General</span>
                            <span class="text-base font-bold text-white">{{ $analytics['enquiries']['by_type']['general'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Revenue Segmentation & Breakdown -->
                <div class="p-6 rounded-3xl bg-slate-900/60 border border-slate-800 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Revenue Stream Segmentation
                                </h3>
                                <p class="text-xs text-slate-400 mt-1">Classification of captured gross transaction volume.</p>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold">
                                {{ $analytics['revenue']['formatted_total'] }}
                            </span>
                        </div>

                        <!-- Revenue Stream Breakdown -->
                        <div class="space-y-4">
                            @php
                                $totalRev = max(1, $analytics['revenue']['total']);
                                $servicePct = round(($analytics['revenue']['service_revenue'] / $totalRev) * 100);
                                $otherPct = 100 - $servicePct;
                            @endphp

                            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800/80">
                                <div class="flex items-center justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-300">Workshop & Service Payments</span>
                                    <span class="font-mono text-emerald-400 font-bold">₹{{ number_format($analytics['revenue']['service_revenue'], 2) }}</span>
                                </div>
                                <div class="h-2 w-full bg-slate-900 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $servicePct }}%"></div>
                                </div>
                                <div class="mt-1 text-[10px] text-slate-500 text-right">{{ $servicePct }}% of total captured</div>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800/80">
                                <div class="flex items-center justify-between text-xs mb-2">
                                    <span class="font-bold text-slate-300">Vehicle Bookings & Direct Payments</span>
                                    <span class="font-mono text-cyan-400 font-bold">₹{{ number_format($analytics['revenue']['other_revenue'], 2) }}</span>
                                </div>
                                <div class="h-2 w-full bg-slate-900 rounded-full overflow-hidden">
                                    <div class="h-full bg-cyan-500 rounded-full" style="width: {{ $otherPct }}%"></div>
                                </div>
                                <div class="mt-1 text-[10px] text-slate-500 text-right">{{ $otherPct }}% of total captured</div>
                            </div>
                        </div>
                    </div>

                    <!-- Top 5 Most In-Demand Motorcycles -->
                    <div class="pt-6 mt-6 border-t border-slate-800/80">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-3">Top Enquired Models</span>
                        @if(count($analytics['enquiries']['top_bikes']) > 0)
                            <div class="space-y-2">
                                @foreach($analytics['enquiries']['top_bikes'] as $bike)
                                    <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-950/70 border border-slate-800/50">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span class="font-bold text-white">{{ $bike['brand'] }} {{ $bike['name'] }}</span>
                                        </div>
                                        <span class="text-slate-400 font-mono font-semibold">{{ $bike['count'] }} leads</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-500 italic">No bike-specific lead records recorded yet in this time frame.</p>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Two-Column Operational Details: Workshop & Marketing ROI -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

                <!-- 3. Workshop Operations Metrics -->
                <div class="p-6 rounded-3xl bg-slate-900/60 border border-slate-800 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Workshop Bay Operations
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">Service bay throughput and turnaround status.</p>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 font-bold">
                            {{ $analytics['workshop']['total'] }} Bookings
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach([
                            'pending' => ['Pending', 'text-amber-400', 'bg-amber-500/10'],
                            'confirmed' => ['Confirmed', 'text-blue-400', 'bg-blue-500/10'],
                            'in_service' => ['In Bay', 'text-indigo-400', 'bg-indigo-500/10'],
                            'ready' => ['Ready for Pickup', 'text-cyan-400', 'bg-cyan-500/10'],
                            'completed' => ['Completed', 'text-emerald-400', 'bg-emerald-500/10'],
                            'cancelled' => ['Cancelled', 'text-rose-400', 'bg-rose-500/10'],
                        ] as $statusKey => [$statusName, $textColor, $bgColor])
                            <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/80">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">{{ $statusName }}</span>
                                <div class="mt-1 flex items-center justify-between">
                                    <span class="text-xl font-black text-white font-mono">{{ $analytics['workshop']['by_status'][$statusKey] }}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded {{ $bgColor }} {{ $textColor }} font-bold">
                                        {{ $analytics['workshop']['total'] > 0 ? round(($analytics['workshop']['by_status'][$statusKey] / $analytics['workshop']['total']) * 100) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Marketing & Gamification ROI -->
                <div class="p-6 rounded-3xl bg-slate-900/60 border border-slate-800 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                                Marketing Gamification & Scratch Cards
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">Footfall driving campaigns and voucher conversion.</p>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 font-bold">
                            {{ $analytics['marketing']['scratch_cards_total'] }} Cards
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800/80 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-300 block">Scratched vs Issued</span>
                                <span class="text-[10px] text-slate-500">Customer engagement rate</span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-black text-white font-mono">{{ $analytics['marketing']['scratched_count'] }}</span>
                                <span class="text-xs text-slate-500 block">of {{ $analytics['marketing']['scratch_cards_total'] }} issued</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800/80 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-300 block">In-Store Coupon Redemptions</span>
                                <span class="text-[10px] text-slate-500">Physical dealership visit conversion</span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-black text-emerald-400 font-mono">{{ $analytics['marketing']['redeemed_count'] }}</span>
                                <span class="text-xs text-emerald-500/90 font-bold block">{{ $analytics['marketing']['redemption_rate'] }}% redemption</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        @endif

    </main>
</div>
@endsection
