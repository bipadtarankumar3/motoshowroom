<x-app-layout title="Super Admin Dashboard | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 xl:px-10">
        <div class="w-full space-y-8">

            <!-- Catalyst-style Header (Welcome back + Action Controls) -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/80 pb-6">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center font-black text-white text-lg shadow-md shadow-orange-500/20 ring-4 ring-orange-50 flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-black font-display text-slate-900 tracking-tight">Super Admin Dashboard</h1>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200">
                                Multi-Tenant Master Control
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                            <span>Welcome back, {{ auth()->user()->name }}</span>
                            <span>👋</span>
                            <span class="text-slate-300">•</span>
                            <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-[11px]">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Platform Healthy
                            </span>
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs text-slate-600 shadow-2xs font-medium">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Last 30 Days</span>
                    </div>

                    {{-- Off BI Analytics
                    <a href="{{ route('admin.analytics.index') }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold shadow-2xs transition flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>BI Analytics</span>
                    </a>
                    --}}

                    <a href="{{ route('admin.settings.index') }}" class="p-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-800 transition shadow-2xs" title="Platform Settings">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>

                    <a href="{{ route('admin.showrooms.create') }}" class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs shadow-md shadow-orange-600/20 transition flex items-center gap-2 cursor-pointer" id="btn-provision-showroom">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ New Showroom</span>
                    </a>
                </div>
            </div>

            <!-- Key Metric Cards Grid (8 Cards) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                <!-- 1. Showrooms Total -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-slate-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Showrooms</span>
                        <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black font-display text-slate-900 mt-2">{{ $stats['total_showrooms'] }}</p>
                    <div class="flex items-center gap-1.5 mt-3 text-xs flex-wrap">
                        <span class="text-emerald-700 font-bold">{{ $stats['active_showrooms'] }} Active</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-amber-700 font-bold">{{ $stats['trial_showrooms'] }} Trial</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-rose-700 font-bold">{{ $stats['suspended_showrooms'] }} Suspended</span>
                    </div>
                </div>

                <!-- 2. MRR (Monthly Recurring Revenue) -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-slate-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Monthly Recurring</span>
                        <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black font-display text-slate-900 mt-2">{{ $stats['formatted_mrr'] }}</p>
                    <div class="mt-3 text-xs text-slate-500 flex items-center justify-between">
                        <span>ARR: <strong class="text-slate-800">{{ $stats['formatted_arr'] }}</strong></span>
                        <span class="text-emerald-700 font-bold">{{ $stats['active_subscriptions'] }} Subscribed</span>
                    </div>
                </div>

                <!-- 3. Platform Users -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-slate-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Platform Users</span>
                        <span class="p-2 rounded-xl bg-sky-50 text-sky-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black font-display text-slate-900 mt-2">{{ $stats['total_users'] }}</p>
                    <div class="flex items-center gap-2 mt-3 text-xs">
                        <span class="text-rose-700 font-bold">{{ $stats['admin_users'] }} Admins</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-sky-700 font-bold">{{ $stats['regular_users'] }} Staff/Users</span>
                    </div>
                </div>

                <!-- 4. Motorcycle Fleet -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-slate-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Motorcycle Fleet</span>
                        <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black font-display text-slate-900 mt-2">{{ $stats['total_bikes'] }}</p>
                    <div class="mt-3 text-xs text-slate-500 flex items-center justify-between">
                        <span>{{ $stats['in_stock_bikes'] }} in-stock models</span>
                        <span class="text-indigo-700 font-bold">Catalog</span>
                    </div>
                </div>

                <!-- 5. Inbound Customer Leads -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-slate-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Customer Leads</span>
                        <span class="p-2 rounded-xl bg-cyan-50 text-cyan-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black font-display text-slate-900 mt-2">{{ $stats['total_enquiries'] }}</p>
                    <div class="mt-3 text-xs text-slate-500 flex items-center justify-between">
                        <span>Test rides & enquiries</span>
                        <span class="text-cyan-700 font-bold">Pipeline</span>
                    </div>
                </div>

                <!-- 6. Workshop Appointments -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-slate-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Service Bookings</span>
                        <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black font-display text-slate-900 mt-2">{{ $stats['total_bookings'] }}</p>
                    <div class="mt-3 text-xs text-slate-500 flex items-center justify-between">
                        <span>Workshop bay jobs</span>
                        <span class="text-amber-700 font-bold">Scheduled</span>
                    </div>
                </div>

                <!-- 7. Platform GMV Volume -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-slate-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Gross Platform GMV</span>
                        <span class="p-2 rounded-xl bg-purple-50 text-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black font-display text-slate-900 mt-2">{{ $stats['formatted_gmv'] }}</p>
                    <div class="mt-3 text-xs text-slate-500 flex items-center justify-between">
                        <span>{{ $stats['total_transactions'] }} captured settlements</span>
                        <span class="text-purple-700 font-bold">Processed</span>
                    </div>
                </div>

                <!-- 8. Isolation Engine -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:border-slate-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Isolation Engine</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                    <p class="text-2xl font-black font-display text-emerald-700 mt-2">Shared DB Isolation</p>
                    <div class="mt-3 text-xs text-slate-500 flex items-center justify-between">
                        <span>Multi-Tenant MySQL 8.0</span>
                        <span class="text-emerald-700 font-bold">Active</span>
                    </div>
                </div>
            </div>

            <!-- Interactive Visual Charts Row (2 Charts) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Chart 1: Revenue & Tenant Growth Trend (8 Cols) -->
                <div class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-bold font-display text-slate-900">Platform Revenue & Tenant Growth</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    6-Month Trajectory
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Monthly platform gross transaction volume (INR ₹) vs. expanding showroom tenancies.</p>
                        </div>

                        <div class="flex items-center gap-4 text-xs font-semibold">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-orange-500 inline-block"></span>
                                <span class="text-slate-700">Gross GMV (₹)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-sky-500 inline-block"></span>
                                <span class="text-slate-700">Active Showrooms</span>
                            </div>
                        </div>
                    </div>

                    <!-- Canvas -->
                    <div class="relative h-72 w-full pt-4">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>

                <!-- Chart 2: Plan Breakdown (4 Cols) -->
                <div class="lg:col-span-4 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold font-display text-slate-900">Tier Distribution</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                SaaS Plans
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Showroom subscribers distributed by billing tier.</p>
                    </div>

                    <!-- Doughnut Canvas -->
                    <div class="relative h-56 w-full flex items-center justify-center py-2">
                        <canvas id="planDistributionChart"></canvas>
                    </div>

                    <!-- Legend List -->
                    <div class="space-y-2 pt-2 border-t border-slate-100 text-xs">
                        @foreach($plansBreakdown as $plan)
                            <div class="flex items-center justify-between py-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                                    <span class="font-bold text-slate-800">{{ $plan['name'] }}</span>
                                    <span class="text-[11px] text-slate-400 font-mono">₹{{ number_format($plan['monthly_price'], 0) }}/mo</span>
                                </div>
                                <span class="font-extrabold text-slate-900">{{ $plan['active_subscribers'] }} active</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Table 1: Comprehensive Showrooms Tenant Directory (Full Width Table) -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden space-y-4 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-bold font-display text-slate-900">Showroom Tenants Directory</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $showrooms->count() }} Registered
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Multi-tenant dealership instances with ownership credentials, live inventory counts, and plan tiers.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.showrooms.index') }}" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-xs font-bold text-slate-700 hover:text-slate-900 transition shadow-2xs flex items-center gap-1.5">
                            <span>Open Full Showroom Manager</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-6 sm:-mx-8">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200/80 bg-slate-50/75 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-6 sm:px-8">Showroom Instance</th>
                                <th class="py-3 px-4">Dealership Owner</th>
                                <th class="py-3 px-4">Active Plan</th>
                                <th class="py-3 px-4 text-center">Bikes Fleet</th>
                                <th class="py-3 px-4 text-center">Inbound Leads</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-6 sm:px-8 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($showrooms as $showroom)
                                <tr class="hover:bg-slate-50/80 transition group">
                                    <td class="py-4 px-6 sm:px-8">
                                        <div class="flex items-center gap-3.5 min-w-[200px]">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white shrink-0 shadow-xs"
                                                 style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                                                {{ strtoupper(substr($showroom->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.showrooms.show', $showroom) }}" class="font-bold text-slate-900 hover:text-rose-600 transition block truncate text-sm">
                                                    {{ $showroom->name }}
                                                </a>
                                                <span class="text-[11px] text-slate-500 block truncate">{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }} · <span class="font-mono text-slate-400">/{{ $showroom->slug }}</span></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="font-bold text-slate-900 block">{{ $showroom->owner?->name ?? 'No Owner Assigned' }}</span>
                                        <span class="text-[11px] text-slate-500 block">{{ $showroom->owner?->email ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if($showroom->subscription?->plan)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                {{ $showroom->subscription->plan->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                Free Trial
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <span class="font-mono font-bold text-slate-900 text-sm">{{ $showroom->bikes_count }}</span>
                                        <span class="text-[10px] text-slate-400 block">inventory</span>
                                    </td>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <span class="font-mono font-bold text-cyan-700 text-sm">{{ $showroom->enquiries_count }}</span>
                                        <span class="text-[10px] text-slate-400 block">leads</span>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if($showroom->status === 'active')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        @elseif($showroom->status === 'trial')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Trial
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Suspended
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 sm:px-8 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($showroom->owner)
                                                <form method="POST" action="{{ route('admin.users.impersonate', $showroom->owner) }}" class="m-0">
                                                    @csrf
                                                    <button type="submit"
                                                            class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold shadow-2xs transition flex items-center gap-1 cursor-pointer"
                                                            title="Visit Showroom as Dealership Owner">
                                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                                        <span>Visit Dashboard</span>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                                               class="p-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 hover:text-slate-900 hover:bg-slate-50 shadow-2xs transition"
                                               title="View Public Storefront">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>

                                            <a href="{{ route('admin.showrooms.show', $showroom) }}"
                                               class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-2xs transition">
                                                Manage
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        No showroom dealerships provisioned yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Two-Column Deep-Dive Grids: Transactions & Live Feeds -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                <!-- 1. Recent Transactions Table -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold font-display text-slate-900">Recent Platform Transactions</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Captured payments, subscription renewals, and service charges.</p>
                        </div>
                        <a href="{{ route('admin.subscriptions.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                            <span>View All</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto -mx-6 sm:-mx-8">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/50 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                                    <th class="py-2.5 px-6 sm:px-8">Reference</th>
                                    <th class="py-2.5 px-3">Showroom</th>
                                    <th class="py-2.5 px-3">Amount</th>
                                    <th class="py-2.5 px-3">Status</th>
                                    <th class="py-2.5 px-6 sm:px-8 text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($recentPayments as $payment)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-6 sm:px-8">
                                            <span class="font-mono font-bold text-slate-900">#PAY-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span>
                                            <span class="text-[10px] text-slate-400 block truncate max-w-[120px]">{{ $payment->razorpay_payment_id ?? 'Razorpay' }}</span>
                                        </td>
                                        <td class="py-3 px-3">
                                            <span class="font-semibold text-slate-800 block truncate max-w-[140px]">{{ $payment->showroom?->name ?? 'Platform' }}</span>
                                        </td>
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            <span class="font-mono font-black text-slate-900">₹{{ number_format($payment->amount, 2) }}</span>
                                        </td>
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            @if($payment->status->value === 'captured')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Paid</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">{{ ucfirst($payment->status->value) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 sm:px-8 text-right text-[11px] text-slate-500 whitespace-nowrap">
                                            {{ $payment->created_at->format('d M, H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400">
                                            No transaction records logged yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Live Security & Audit Trail -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold font-display text-slate-900">Platform Security & Audit Feed</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Immutable audit trail of authentication, membership changes, and tenant provisioning.</p>
                        </div>
                        <a href="{{ route('admin.audit-logs.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                            <span>View All</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto -mx-6 sm:-mx-8">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/50 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                                    <th class="py-2.5 px-6 sm:px-8">Action Event</th>
                                    <th class="py-2.5 px-3">Initiated By</th>
                                    <th class="py-2.5 px-3">Target Showroom</th>
                                    <th class="py-2.5 px-6 sm:px-8 text-right">Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($recentAuditLogs as $log)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-6 sm:px-8">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                                <span class="font-bold text-slate-900">{{ $log->action }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            <span class="text-slate-800 font-semibold">{{ $log->user?->name ?? 'System' }}</span>
                                        </td>
                                        <td class="py-3 px-3">
                                            <span class="text-slate-600 truncate block max-w-[140px]">{{ $log->showroom?->name ?? 'Platform Wide' }}</span>
                                        </td>
                                        <td class="py-3 px-6 sm:px-8 text-right text-[11px] text-slate-400 whitespace-nowrap">
                                            {{ $log->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400">
                                            No audit events recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('admin.partials.nav-close')

    @push('scripts')
    <!-- Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Revenue & Tenant Growth Chart
            const revCtx = document.getElementById('revenueTrendChart')?.getContext('2d');
            if (revCtx) {
                const months = @json($chartData['months'] ?? []);
                const revenue = @json($chartData['revenue'] ?? []);
                const tenants = @json($chartData['tenants'] ?? []);

                new Chart(revCtx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: 'Monthly GMV (₹)',
                                data: revenue,
                                borderColor: '#f97316',
                                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                                fill: true,
                                tension: 0.4,
                                yAxisID: 'y',
                                borderWidth: 3,
                                pointBackgroundColor: '#ea580c',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            },
                            {
                                label: 'Showrooms',
                                data: tenants,
                                borderColor: '#0284c7',
                                backgroundColor: 'rgba(2, 132, 199, 0.05)',
                                fill: false,
                                borderDash: [5, 5],
                                tension: 0.3,
                                yAxisID: 'y1',
                                borderWidth: 2,
                                pointBackgroundColor: '#0284c7',
                                pointRadius: 3,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#cbd5e1',
                                padding: 12,
                                cornerRadius: 12,
                                callbacks: {
                                    label: function(context) {
                                        if (context.dataset.yAxisID === 'y') {
                                            return ' Revenue: ₹' + Number(context.raw).toLocaleString('en-IN', { maximumFractionDigits: 2 });
                                        }
                                        return ' Showrooms: ' + context.raw;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 }, color: '#64748b' }
                            },
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    font: { size: 10 },
                                    color: '#64748b',
                                    callback: function(val) { return '₹' + val.toLocaleString('en-IN'); }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: { drawOnChartArea: false },
                                ticks: {
                                    font: { size: 10 },
                                    color: '#64748b',
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            // 2. Plan Distribution Doughnut Chart
            const planCtx = document.getElementById('planDistributionChart')?.getContext('2d');
            if (planCtx) {
                const planLabels = @json($chartData['plan_labels'] ?? []);
                const planCounts = @json($chartData['plan_counts'] ?? []);

                // If counts are all 0, provide visible sample allocation
                const totalSubs = planCounts.reduce((a, b) => a + b, 0);
                const displayCounts = totalSubs > 0 ? planCounts : [1, 1, 0];

                new Chart(planCtx, {
                    type: 'doughnut',
                    data: {
                        labels: planLabels.length > 0 ? planLabels : ['Starter', 'Professional', 'Enterprise'],
                        datasets: [{
                            data: displayCounts,
                            backgroundColor: ['#3b82f6', '#f97316', '#a855f7'],
                            borderColor: '#ffffff',
                            borderWidth: 3,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#cbd5e1',
                                padding: 10,
                                cornerRadius: 10
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
