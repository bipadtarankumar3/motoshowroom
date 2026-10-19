<x-app-layout :title="$showroom->name . ' - Showroom Back-Office | MotoShowroom SaaS'">
    @include('showrooms.partials.manage-nav')

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 xl:px-10 font-sans selection:bg-rose-500 selection:text-white">
        <div class="w-full space-y-8">

            <!-- 1. Hero Dealership Console Header Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm relative overflow-hidden">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-2xl text-white shadow-md shadow-red-500/20 shrink-0"
                             style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                            {{ strtoupper(substr($showroom->name, 0, 1)) }}
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">{{ $showroom->name }}</h1>
                                @if($showroom->subscription)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $showroom->subscription->status->badgeClasses() }}">
                                        {{ $showroom->subscription->plan->name }} ({{ $showroom->subscription->status->label() }})
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ ucfirst($showroom->status) }}
                                    </span>
                                @endif
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Live Back-Office</span>
                                </span>
                            </div>

                            <p class="text-xs text-slate-500 mt-1.5 flex flex-wrap items-center gap-3">
                                <span>City: <strong class="text-slate-800">{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</strong></span>
                                <span class="text-slate-300">•</span>
                                <span>Slug: <strong class="text-slate-800 font-mono">{{ $showroom->slug }}</strong></span>
                                @if($showroom->subscription)
                                    <span class="text-slate-300">•</span>
                                    <span>Plan Status: <strong class="text-emerald-700 font-bold">{{ $showroom->subscription->days_remaining }} days remaining</strong></span>
                                    <span class="text-slate-300">•</span>
                                    <span>Expiry: <strong class="text-slate-800 font-mono">{{ $showroom->subscription->current_period_end ? $showroom->subscription->current_period_end->format('d M Y') : 'N/A' }}</strong></span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <button type="button"
                                @click="$dispatch('open-onboarding-modal')"
                                class="px-3.5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95" id="btn-edit-profile-showroom">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>Edit Profile & Showroom</span>
                        </button>

                        <a href="{{ route('showrooms.manage.bikes.create', $showroom) }}"
                           class="px-3.5 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs shadow-md shadow-red-600/20 transition flex items-center gap-1.5" id="btn-add-bike">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Add Motorcycle</span>
                        </a>

                        <a href="{{ route('showrooms.manage.website', $showroom) }}"
                           class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 hover:text-slate-900 font-bold text-xs shadow-2xs transition flex items-center gap-1.5" id="btn-customizer">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Website Builder</span>
                        </a>

                        <a href="{{ route('showrooms.billing.index', $showroom) }}"
                           class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 hover:text-slate-900 font-bold text-xs shadow-2xs transition flex items-center gap-1.5" id="btn-billing">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Subscription</span>
                        </a>

                        <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                           class="p-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-900 shadow-2xs transition" title="Preview Public Storefront">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. High-Impact Operational KPI Metrics Grid (6 Curated Cards) -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- 1. Inventory -->
                <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}" class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs hover:border-red-400 hover:shadow-xs transition group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Inventory</span>
                        <div class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-slate-900 mt-2 group-hover:text-red-600 transition">{{ $metrics['bikes_count'] }}</p>
                    <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                        <span>{{ $metrics['published_bikes'] }} Published</span>
                        <span class="text-emerald-600 font-bold">Catalog</span>
                    </div>
                </a>

                <!-- 2. Enquiries & Leads -->
                <a href="{{ route('showrooms.manage.enquiries.index', $showroom) }}" class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs hover:border-sky-400 hover:shadow-xs transition group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-sky-600 tracking-wider">Customer Leads</span>
                        <div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-sky-600 mt-2">{{ $metrics['active_enquiries'] }}</p>
                    <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                        <span>{{ $metrics['total_enquiries'] }} Total Received</span>
                        <span class="text-sky-700 font-bold">Pipeline</span>
                    </div>
                </a>

                <!-- 3. Workshop Services -->
                <a href="{{ route('showrooms.manage.services.index', $showroom) }}" class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs hover:border-emerald-400 hover:shadow-xs transition group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-emerald-600 tracking-wider">Service Bookings</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-emerald-600 mt-2">{{ $metrics['today_services'] }}</p>
                    <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                        <span>{{ $metrics['total_services'] }} All-Time</span>
                        <span class="text-emerald-700 font-bold">Today</span>
                    </div>
                </a>

                <!-- 4. Gross Revenue -->
                <a href="{{ route('showrooms.manage.payments.index', $showroom) }}" class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs hover:border-amber-400 hover:shadow-xs transition group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-amber-600 tracking-wider">Settled GMV</span>
                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-xl sm:text-2xl font-black font-display text-slate-900 mt-2 truncate">{{ $metrics['formatted_revenue'] }}</p>
                    <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                        <span>Razorpay Ledger</span>
                        <span class="text-amber-700 font-bold">Captured</span>
                    </div>
                </a>

                <!-- 5. Scratch Cards / Campaigns -->
                <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs hover:border-purple-400 hover:shadow-xs transition group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-purple-600 tracking-wider">Scratch Cards</span>
                        <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-purple-600 mt-2">{{ $metrics['active_campaigns'] }}</p>
                    <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                        <span>Active Promos</span>
                        <span class="text-purple-700 font-bold">Marketing</span>
                    </div>
                </a>

                <!-- 6. Team & Dealership Staff -->
                <a href="{{ route('showrooms.manage.settings', $showroom) }}" class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs hover:border-rose-400 hover:shadow-xs transition group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-rose-600 tracking-wider">Authorized Staff</span>
                        <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-rose-600 mt-2">{{ $metrics['staff_count'] }}</p>
                    <div class="flex items-center justify-between mt-1 text-[11px] text-slate-500">
                        <span>{{ $metrics['open_jobs'] }} Open Careers</span>
                        <span class="text-rose-700 font-bold">Team</span>
                    </div>
                </a>
            </div>

            <!-- 3. Modern Interactive Charts Section (Chart.js Trends & Funnels) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Chart 1: 6-Month Operations Trend (8 Columns) -->
                <div class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-bold font-display text-slate-900">Showroom Operations Trend</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                    Last 6 Months
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Inbound purchase enquiries & leads vs. workshop service appointments.</p>
                        </div>

                        <div class="flex items-center gap-4 text-xs font-semibold">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-600 inline-block"></span>
                                <span class="text-slate-700">Enquiries / Leads</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                                <span class="text-slate-700">Workshop Services</span>
                            </div>
                        </div>
                    </div>

                    <!-- Canvas -->
                    <div class="relative h-72 w-full pt-2">
                        <canvas id="showroomOperationsTrendChart"></canvas>
                    </div>
                </div>

                <!-- Chart 2: Customer Enquiry Funnel & Service Health (4 Columns) -->
                <div class="lg:col-span-4 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold font-display text-slate-900">Conversion Funnel</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                Sales Pipeline
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Customer stage progression from inquiry to booking.</p>
                    </div>

                    <!-- Doughnut Canvas -->
                    <div class="relative h-56 w-full flex items-center justify-center py-2">
                        <canvas id="enquiryFunnelChart"></canvas>
                    </div>

                    <!-- Stage Indicators -->
                    <div class="space-y-2 pt-2 border-t border-slate-100 text-xs">
                        <div class="flex items-center justify-between py-0.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                <span class="text-slate-700 font-semibold">New / Uncontacted</span>
                            </div>
                            <span class="font-extrabold text-slate-900 font-mono">{{ $chartData['enquiry_funnel'][0] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between py-0.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span class="text-slate-700 font-semibold">Contacted / In Progress</span>
                            </div>
                            <span class="font-extrabold text-slate-900 font-mono">{{ $chartData['enquiry_funnel'][1] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between py-0.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                                <span class="text-slate-700 font-semibold">Test Ride Scheduled</span>
                            </div>
                            <span class="font-extrabold text-slate-900 font-mono">{{ $chartData['enquiry_funnel'][2] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between py-0.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span class="text-slate-700 font-semibold">Deposit / Booking Pending</span>
                            </div>
                            <span class="font-extrabold text-slate-900 font-mono">{{ $chartData['enquiry_funnel'][4] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Interactive Live Operational Tables (3 Columns Grid: Recent Leads & Recent Bookings) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

                <!-- Table 1: Inbound Customer Enquiries & Test Rides -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-bold font-display text-slate-900">Recent Customer Leads</h3>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                    {{ $recentEnquiries->count() }} Latest
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Direct purchase inquiries and rider test slot appointments.</p>
                        </div>
                        <a href="{{ route('showrooms.manage.enquiries.index', $showroom) }}" class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1">
                            <span>View All</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 text-[10px] uppercase font-bold text-slate-500 tracking-wider border-b border-slate-100">
                                    <th class="py-3 px-5">Customer</th>
                                    <th class="py-3 px-4">Bike Interest</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @forelse($recentEnquiries as $enquiry)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-5">
                                            <div class="font-bold text-slate-900">{{ $enquiry->customer_name }}</div>
                                            <div class="text-[11px] text-slate-500 font-mono">{{ $enquiry->customer_phone ?? $enquiry->customer_email }}</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($enquiry->bike)
                                                <span class="font-semibold text-slate-800">{{ $enquiry->bike->name }}</span>
                                            @else
                                                <span class="text-slate-400 italic">General Enquiry</span>
                                            @endif
                                            <span class="block text-[10px] uppercase tracking-wider font-bold text-slate-400 mt-0.5">{{ $enquiry->type->value }}</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $enquiry->status->badgeClasses() }}">
                                                {{ $enquiry->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <a href="{{ route('showrooms.manage.enquiries.show', [$showroom, $enquiry]) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition inline-block">
                                                Manage
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400">
                                            <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            No customer enquiries received yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Table 2: Workshop Service Bay Appointments -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-bold font-display text-slate-900">Upcoming Workshop Bookings</h3>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $recentBookings->count() }} Scheduled
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Live service bay queue, maintenance logs, and vehicle delivery schedule.</p>
                        </div>
                        <a href="{{ route('showrooms.manage.services.index', $showroom) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                            <span>View All</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 text-[10px] uppercase font-bold text-slate-500 tracking-wider border-b border-slate-100">
                                    <th class="py-3 px-5">Rider & Vehicle</th>
                                    <th class="py-3 px-4">Service Package</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @forelse($recentBookings as $booking)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-5">
                                            <div class="font-bold text-slate-900">{{ $booking->customer_name }}</div>
                                            <div class="text-[11px] text-slate-500 font-mono">{{ $booking->bike_make_model ?? 'Motorcycle' }} ({{ $booking->registration_number ?? 'Reg N/A' }})</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-semibold text-slate-800">{{ $booking->getServiceName() }}</span>
                                            <div class="text-[10px] text-slate-400 mt-0.5 font-mono">{{ $booking->booking_date ? $booking->booking_date->format('d M') : 'Today' }} · {{ $booking->time_slot }}</div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $booking->status->badgeClasses() }}">
                                                {{ $booking->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <a href="{{ route('showrooms.manage.services.show', [$showroom, $booking]) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition inline-block">
                                                Bay Job
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400">
                                            <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            No workshop service appointments scheduled yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- 5. Showroom Motorcycle Showcase & Departmental Hub Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

                <!-- Left 2 Cols: Latest Motorcycles in Dealership -->
                <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-bold font-display text-slate-900">Motorcycle Fleet & Inventory</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                    {{ $metrics['bikes_count'] }} Listed
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Latest models available for buyer inspection, online 360° spin, and token booking.</p>
                        </div>
                        <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}" class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1">
                            <span>Manage Fleet</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($latestBikes as $bike)
                            <div class="p-4 rounded-2xl border border-slate-200 hover:border-slate-300 transition bg-slate-50/50 flex items-center gap-4 group">
                                <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 p-1 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                                    @if($bike->primary_image_url)
                                        <img src="{{ $bike->primary_image_url }}" alt="{{ $bike->name }}" class="w-full h-full object-cover rounded-lg group-hover:scale-105 transition">
                                    @else
                                        <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="font-bold text-slate-900 text-xs sm:text-sm truncate">{{ $bike->name }}</h4>
                                        <span class="text-xs font-mono font-extrabold text-red-600">₹{{ number_format($bike->price, 0) }}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $bike->brand }} · {{ $bike->engine_cc ?? 0 }} cc</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $bike->is_published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $bike->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                        @if($bike->has_360_view)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                360° Spin
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="sm:col-span-2 text-center py-8 rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-slate-400">
                                <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                                <p class="text-xs font-semibold text-slate-600">No motorcycles listed in showroom inventory yet.</p>
                                <a href="{{ route('showrooms.manage.bikes.create', $showroom) }}" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs shadow-xs transition">
                                    + Add Your First Motorcycle
                                </a>
                            </div>
                        @endforelse
                    </div>

                    <!-- Department Operations Direct Links -->
                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-3">Quick Navigation Hub</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <a href="{{ route('showrooms.manage.website', $showroom) }}" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 transition text-center group">
                                <span class="text-xs font-bold text-slate-800 group-hover:text-red-600 transition block">Storefront Customizer</span>
                                <span class="text-[10px] text-slate-400">Branding & Theme</span>
                            </a>
                            <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 transition text-center group">
                                <span class="text-xs font-bold text-slate-800 group-hover:text-red-600 transition block">Motorcycle Inventory</span>
                                <span class="text-[10px] text-slate-400">All Fleet Models</span>
                            </a>
                            <a href="{{ route('showrooms.manage.categories.index', $showroom) }}" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 transition text-center group">
                                <span class="text-xs font-bold text-slate-800 group-hover:text-red-600 transition block">Categories</span>
                                <span class="text-[10px] text-slate-400">Master Catalog</span>
                            </a>
                            <a href="{{ route('showrooms.manage.careers.index', $showroom) }}" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 transition text-center group">
                                <span class="text-xs font-bold text-slate-800 group-hover:text-red-600 transition block">Careers & ATS</span>
                                <span class="text-[10px] text-slate-400">Staff Hiring</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Col: Storefront Status & Security Audit Trail -->
                <div class="space-y-6">
                    <!-- Storefront Status Card -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Public Storefront</h3>
                            @if($showroom->public_website_enabled)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Online</span>
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Offline</span>
                            @endif
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $showroom->settings['tagline'] ?? 'Premium Motorcycle Dealership & Certified Maintenance Bay' }}
                        </p>

                        <div class="pt-2">
                            <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                               class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs cursor-pointer active:scale-98">
                                <span>Preview Live Storefront</span>
                                <svg class="w-3.5 h-3.5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Recent Activity Trail -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Showroom Activity Trail</h3>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Live Logs</span>
                        </div>
                        <div class="space-y-3">
                            @forelse($recentActivities as $act)
                                <div class="text-xs border-b border-slate-100 pb-2.5 last:border-0 last:pb-0">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-800">{{ $act->action }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $act->created_at->diffForHumans() }}</span>
                                    </div>
                                    <span class="text-[11px] text-slate-500 mt-0.5 block">by {{ $act->user?->name ?? 'System' }}</span>
                                </div>
                            @empty
                                <div class="text-center py-4 text-xs text-slate-400">
                                    No activity logged yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    @include('showrooms.partials.onboarding-modal')
    @include('showrooms.partials.manage-nav-close')

    @push('scripts')
    <!-- Chart.js via CDN for Modern Visual Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Showroom 6-Month Operations Trend Line Chart
            const trendCtx = document.getElementById('showroomOperationsTrendChart')?.getContext('2d');
            if (trendCtx) {
                const months = @json($chartData['months'] ?? []);
                const enquiries = @json($chartData['enquiries'] ?? []);
                const services = @json($chartData['services'] ?? []);

                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: 'Enquiries & Leads',
                                data: enquiries,
                                borderColor: '#dc2626',
                                backgroundColor: 'rgba(220, 38, 38, 0.08)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointBackgroundColor: '#dc2626',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            },
                            {
                                label: 'Workshop Services',
                                data: services,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#10b981',
                                pointRadius: 3.5,
                                pointHoverRadius: 5,
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
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
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

            // 2. Enquiry Conversion Funnel Doughnut Chart
            const funnelCtx = document.getElementById('enquiryFunnelChart')?.getContext('2d');
            if (funnelCtx) {
                const rawFunnel = @json($chartData['enquiry_funnel'] ?? []);
                const sum = rawFunnel.reduce((a, b) => a + b, 0);
                const displayData = sum > 0 ? rawFunnel : [2, 1, 1, 1, 1];

                new Chart(funnelCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['New', 'Contacted', 'Test Ride', 'Negotiation', 'Booking Pending'],
                        datasets: [{
                            data: displayData,
                            backgroundColor: ['#3b82f6', '#f59e0b', '#a855f7', '#6366f1', '#10b981'],
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
