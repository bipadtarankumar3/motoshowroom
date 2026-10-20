@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col"
     x-data="{
        tab: '{{ $activeTab }}',
        selectedIds: [],
        allIds: {{ json_encode($bookings->pluck('id')) }},
        toggleAll() {
            if (this.selectedIds.length === this.allIds.length) {
                this.selectedIds = [];
            } else {
                this.selectedIds = [...this.allIds];
            }
        },
        activeBooking: null,
        detailsModalOpen: false,
        openDetails(bk) {
            this.activeBooking = bk;
            this.detailsModalOpen = true;
        },
        createModalOpen: false,
        editModalOpen: false,
        editingService: {
            id: null,
            name: '',
            price: '',
            estimated_duration_minutes: 60,
            badge: '',
            is_payment_enabled: true,
            is_active: true,
            description: '',
            features: ''
        },
        openEdit(svc) {
            this.editingService = {
                id: svc.id,
                name: svc.name,
                price: svc.price,
                estimated_duration_minutes: svc.estimated_duration_minutes,
                badge: svc.badge || '',
                is_payment_enabled: !!svc.is_payment_enabled,
                is_active: !!svc.is_active,
                description: svc.description || '',
                features: (svc.features || []).join('\n')
            };
            this.editModalOpen = true;
        }
     }">
    @include('showrooms.partials.manage-nav')
    <div class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 space-y-6">

        <!-- Top Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>/</span>
            <span class="text-slate-700 font-semibold">Workshop & Service Center</span>
        </div>

        <!-- Notification Banner -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1 shadow-xs">
                <div class="font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Action could not be completed:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-2">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Workshop & Service Center</h1>
                <p class="text-sm text-slate-500 mt-1">Manage customer appointments, live bay occupancy, mechanic job cards, and custom service packages with online payment settings.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" @click="createModalOpen = true"
                        class="btn-brand-primary px-4 py-2.5 rounded-xl font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer" id="btn-add-service-package-top">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Service Package</span>
                </button>

                <a href="{{ route('showrooms.manage.services.export', $showroom) }}"
                   class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-xs font-bold text-slate-700 hover:text-slate-900 transition flex items-center gap-1.5 shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Export CSV</span>
                </a>

                <a href="{{ route('showrooms.services.create', $showroom) }}" target="_blank"
                   class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-xs font-semibold text-slate-700 hover:text-slate-900 transition flex items-center gap-1.5 shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Public Booking Page</span>
                </a>

                <a href="{{ route('showrooms.services.track', $showroom) }}" target="_blank"
                   class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-xs font-semibold text-slate-700 hover:text-slate-900 transition flex items-center gap-1.5 shadow-xs">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Status Tracker</span>
                </a>
            </div>
        </div>

        <!-- Segmented Tab Navigation -->
        <div class="flex items-center gap-2 p-1 rounded-2xl bg-slate-200/80 border border-slate-300 w-fit text-xs font-bold">
            <button type="button" @click="tab = 'bookings'"
                    :class="tab === 'bookings' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 rounded-xl transition flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Appointments & Bay Schedule</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 font-mono">{{ $bookings->total() }}</span>
            </button>

            <button type="button" @click="tab = 'catalog'"
                    :class="tab === 'catalog' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 rounded-xl transition flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Service Catalog & Custom Pricing</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-50 text-emerald-700 font-mono border border-emerald-200">{{ $totalServicesCount }}</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: APPOINTMENTS & BAY SCHEDULE         -->
        <!-- ========================================== -->
        <div x-show="tab === 'bookings'" class="space-y-6">
            <!-- Quick Timeframe & Status Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ empty($timeframe) && empty($status) && empty($date) ? 'bg-slate-900 text-white shadow-xs' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
                    <span>All Appointments</span>
                    <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ empty($timeframe) && empty($status) && empty($date) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $bookings->total() }}</span>
                </a>

                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'timeframe' => 'today']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $timeframe === 'today' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white hover:bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Today's Service</span>
                    <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $timeframe === 'today' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">{{ $todayTotal }}</span>
                </a>

                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'timeframe' => 'tomorrow']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $timeframe === 'tomorrow' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white hover:bg-amber-50 text-amber-800 border border-amber-200' }}">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span>Tomorrow's Service</span>
                    <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $timeframe === 'tomorrow' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">{{ $tomorrowTotal }}</span>
                </a>

                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'timeframe' => 'this_week']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $timeframe === 'this_week' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white hover:bg-indigo-50 text-indigo-800 border border-indigo-200' }}">
                    <span>This Week</span>
                    <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $timeframe === 'this_week' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800' }}">{{ $thisWeekTotal }}</span>
                </a>

                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'timeframe' => 'in_service']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $timeframe === 'in_service' || $status === 'in_service' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white hover:bg-amber-50 text-amber-800 border border-amber-200' }}">
                    <span>In Service Bays</span>
                    <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $timeframe === 'in_service' || $status === 'in_service' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">{{ $inServiceCount }}</span>
                </a>

                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'timeframe' => 'ready']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $timeframe === 'ready' || $status === 'ready_for_collection' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-white hover:bg-emerald-50 text-emerald-900 border border-emerald-300' }}">
                    <span>Ready for Collection</span>
                    <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $timeframe === 'ready' || $status === 'ready_for_collection' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-900' }}">{{ $readyCount }}</span>
                </a>

                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'timeframe' => 'pending']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $timeframe === 'pending' || $status === 'pending' ? 'bg-sky-600 text-white shadow-xs' : 'bg-white hover:bg-sky-50 text-sky-800 border border-sky-200' }}">
                    <span>Pending Confirmation</span>
                    <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $timeframe === 'pending' || $status === 'pending' ? 'bg-white/20 text-white' : 'bg-sky-100 text-sky-800' }}">{{ $pendingCount }}</span>
                </a>
            </div>

            <!-- Metric KPI Cards (6 Cards) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'timeframe' => 'today']) }}"
                   class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-emerald-400 hover:shadow-sm transition block group">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-emerald-700 tracking-wider">Today's Appointments</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                    <p class="text-2xl font-black font-display text-emerald-700 mt-1.5 group-hover:scale-105 transition-transform">{{ $todayTotal }}</p>
                    <span class="text-[10px] text-slate-500 block mt-0.5">Scheduled for today</span>
                </a>

                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'timeframe' => 'tomorrow']) }}"
                   class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-amber-400 hover:shadow-sm transition block group">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-amber-700 tracking-wider">Tomorrow's Service</span>
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    </div>
                    <p class="text-2xl font-black font-display text-amber-700 mt-1.5 group-hover:scale-105 transition-transform">{{ $tomorrowTotal }}</p>
                    <span class="text-[10px] text-slate-500 block mt-0.5">Scheduled tomorrow</span>
                </a>

                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'timeframe' => 'in_service']) }}"
                   class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-amber-400 hover:shadow-sm transition block group">
                    <span class="text-[10px] uppercase font-bold text-amber-600 tracking-wider block">In Service Bays</span>
                    <p class="text-2xl font-black font-display text-amber-600 mt-1.5 group-hover:scale-105 transition-transform">{{ $inServiceCount }}</p>
                    <span class="text-[10px] text-slate-500 block mt-0.5">Actively on the lift</span>
                </a>

                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'timeframe' => 'ready']) }}"
                   class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-emerald-400 hover:shadow-sm transition block group">
                    <span class="text-[10px] uppercase font-bold text-emerald-600 tracking-wider block">Ready for Collection</span>
                    <p class="text-2xl font-black font-display text-emerald-600 mt-1.5 group-hover:scale-105 transition-transform">{{ $readyCount }}</p>
                    <span class="text-[10px] text-slate-500 block mt-0.5">Service completed</span>
                </a>

                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'timeframe' => 'pending']) }}"
                   class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-sky-400 hover:shadow-sm transition block group">
                    <span class="text-[10px] uppercase font-bold text-sky-600 tracking-wider block">Pending Confirmation</span>
                    <p class="text-2xl font-black font-display text-sky-600 mt-1.5 group-hover:scale-105 transition-transform">{{ $pendingCount }}</p>
                    <span class="text-[10px] text-slate-500 block mt-0.5">Awaiting bay approval</span>
                </a>

                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings', 'status' => 'completed']) }}"
                   class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-slate-400 hover:shadow-sm transition block group">
                    <span class="text-[10px] uppercase font-bold text-slate-700 tracking-wider block">Completed Work</span>
                    <p class="text-2xl font-black font-display text-slate-900 mt-1.5 group-hover:scale-105 transition-transform">{{ $completedCount }}</p>
                    <span class="text-[10px] text-slate-500 block mt-0.5">Finished work orders</span>
                </a>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('showrooms.manage.services.index', $showroom) }}" class="space-y-3">
                    <input type="hidden" name="tab" value="bookings">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Search Appointments</label>
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Reg #, Customer, Bike, Ref #..."
                                   class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 placeholder-slate-400 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                        </div>

                        <!-- Timeframe Filter -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Timeframe</label>
                            <select name="timeframe" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                <option value="">All Timeframes</option>
                                <option value="today" {{ $timeframe === 'today' ? 'selected' : '' }}>Today's Service</option>
                                <option value="tomorrow" {{ $timeframe === 'tomorrow' ? 'selected' : '' }}>Tomorrow's Service</option>
                                <option value="this_week" {{ $timeframe === 'this_week' ? 'selected' : '' }}>This Week</option>
                                <option value="upcoming" {{ $timeframe === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="past" {{ $timeframe === 'past' ? 'selected' : '' }}>Past / Overdue</option>
                                <option value="in_service" {{ $timeframe === 'in_service' ? 'selected' : '' }}>In Service Bays</option>
                                <option value="ready" {{ $timeframe === 'ready' ? 'selected' : '' }}>Ready for Collection</option>
                                <option value="pending" {{ $timeframe === 'pending' ? 'selected' : '' }}>Pending Confirmation</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Lifecycle Status</label>
                            <select name="status" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                <option value="all">All Statuses</option>
                                @foreach ($statuses as $st)
                                    <option value="{{ $st->value }}" {{ $status === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Package Type -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Service Package</label>
                            <select name="service_type" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                <option value="all">All Packages</option>
                                @foreach ($types as $tp)
                                    <option value="{{ $tp->value }}" {{ $serviceType === $tp->value ? 'selected' : '' }}>{{ $tp->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assigned Technician -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Technician / Mechanic</label>
                            <select name="assigned_to" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                <option value="">All Technicians</option>
                                @foreach ($technicians as $tech)
                                    <option value="{{ $tech->id }}" {{ $assignedTo == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100">
                        <div class="flex flex-wrap items-center gap-4">
                            <!-- Specific Date -->
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-semibold text-slate-600">Specific Date:</label>
                                <input type="date" name="date" value="{{ $date }}"
                                       class="px-2.5 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                            </div>

                            <!-- Payment Status Filter -->
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-semibold text-slate-600">Payment:</label>
                                <select name="payment_status" class="px-2.5 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                    <option value="">All Payments</option>
                                    <option value="captured" {{ $paymentStatus === 'captured' ? 'selected' : '' }}>Paid / Advance Captured</option>
                                    <option value="pending" {{ $paymentStatus === 'pending' ? 'selected' : '' }}>Pay at Bay / Pending</option>
                                </select>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <button type="submit" class="py-2 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs cursor-pointer">
                                Apply Filters
                            </button>
                            @if($search || $date || $status || $serviceType || $timeframe || $assignedTo || $paymentStatus)
                                <a href="{{ route('showrooms.manage.services.index', [$showroom, 'tab' => 'bookings']) }}"
                                   class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 hover:text-slate-900 text-xs font-semibold transition" title="Clear Filters">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Appointments Table -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                @if ($bookings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase font-bold text-slate-500 tracking-wider">
                                    <th class="py-3 px-4 w-10 text-center">
                                        <input type="checkbox"
                                               @change="toggleAll()"
                                               :checked="selectedIds.length === allIds.length && allIds.length > 0"
                                               class="w-4 h-4 text-rose-600 border-slate-300 rounded focus:ring-rose-500">
                                    </th>
                                    <th class="py-3 px-4">Booking Ref</th>
                                    <th class="py-3 px-4">Customer</th>
                                    <th class="py-3 px-4">Motorcycle & Reg #</th>
                                    <th class="py-3 px-4">Service Package</th>
                                    <th class="py-3 px-4">Appointment Slot</th>
                                    <th class="py-3 px-4">Bay & Tech</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($bookings as $bk)
                                    @php
                                        $isToday = $bk->booking_date->toDateString() === today()->toDateString();
                                        $isTomorrow = $bk->booking_date->toDateString() === today()->addDay()->toDateString();
                                        $isPast = $bk->booking_date->toDateString() < today()->toDateString();
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $bk->customer_phone);

                                        $bkJson = [
                                            'id' => $bk->id,
                                            'booking_number' => $bk->booking_number,
                                            'customer_name' => $bk->customer_name,
                                            'customer_email' => $bk->customer_email,
                                            'customer_phone' => $bk->customer_phone,
                                            'bike' => $bk->bike_make_model,
                                            'reg' => $bk->registration_number,
                                            'odometer' => $bk->odometer_reading ? number_format($bk->odometer_reading) . ' km' : 'Not recorded',
                                            'package' => $bk->getServiceName(),
                                            'date' => $bk->booking_date->format('M d, Y'),
                                            'time_slot' => $bk->time_slot,
                                            'bay' => $bk->bay_number ?? 'Unassigned',
                                            'status' => $bk->status->label(),
                                            'cost' => '₹' . number_format($bk->final_cost ?? $bk->estimated_cost),
                                            'technician' => $bk->assignedTechnician?->name ?? 'Unassigned',
                                            'notes' => $bk->customer_notes ?? 'No notes provided.',
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition group {{ $isToday ? 'bg-emerald-50/20' : '' }}">
                                        <td class="py-3.5 px-4 text-center">
                                            <input type="checkbox" :value="{{ $bk->id }}" x-model="selectedIds"
                                                   class="w-4 h-4 text-rose-600 border-slate-300 rounded focus:ring-rose-500">
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-mono font-bold text-slate-900 group-hover:text-rose-600 transition flex items-center gap-1.5">
                                                <span>#{{ $bk->booking_number }}</span>
                                            </div>
                                            <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                                Created {{ $bk->created_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900">{{ $bk->customer_name }}</div>
                                            <div class="text-[11px] text-slate-500 font-mono flex items-center gap-1.5 mt-0.5">
                                                <a href="tel:{{ $bk->customer_phone }}" class="hover:text-slate-900 transition">{{ $bk->customer_phone }}</a>
                                                @if($cleanPhone)
                                                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-bold" title="Open WhatsApp chat">
                                                        💬 WA
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900">{{ $bk->bike_make_model }}</div>
                                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $bk->registration_number }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-semibold text-slate-800">{{ $bk->getServiceName() }}</div>
                                            <div class="text-[11px] font-mono font-bold text-emerald-700">
                                                ₹{{ number_format($bk->final_cost ?? $bk->estimated_cost) }}
                                                @if($bk->payment_required || $bk->payment_status === \App\Enums\PaymentStatus::Captured)
                                                    <span class="text-[9px] text-emerald-600 font-sans font-normal">(Paid)</span>
                                                @else
                                                    <span class="text-[9px] text-amber-600 font-sans font-normal">(Pay at Bay)</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            @if($isToday)
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                                    <span>TODAY</span>
                                                    <span class="font-normal text-emerald-700">• {{ $bk->time_slot }}</span>
                                                </div>
                                            @elseif($isTomorrow)
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-100 text-amber-800 border border-amber-300 shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                    <span>TOMORROW</span>
                                                    <span class="font-normal text-amber-700">• {{ $bk->time_slot }}</span>
                                                </div>
                                            @elseif($isPast)
                                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span>OVERDUE • {{ $bk->booking_date->format('M d, Y') }}</span>
                                                </div>
                                                <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $bk->time_slot }}</div>
                                            @else
                                                <div class="font-bold text-slate-900">{{ $bk->booking_date->format('M d, Y') }}</div>
                                                <div class="text-[10px] font-mono text-slate-500">{{ $bk->time_slot }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4">
                                            @if($bk->bay_number)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    Bay #{{ $bk->bay_number }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-xs italic">No Bay Assigned</span>
                                            @endif
                                            <div class="text-[11px] text-slate-600 mt-0.5 font-medium">
                                                {{ $bk->assignedTechnician?->name ?? 'Unassigned' }}
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold {{ $bk->status->badgeClasses() }}">
                                                {{ $bk->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <button type="button"
                                                        @click="openDetails({{ json_encode($bkJson) }})"
                                                        class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold text-[11px] transition shadow-xs flex items-center gap-1 cursor-pointer"
                                                        title="Quick Details">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Details</span>
                                                </button>

                                                <a href="{{ route('showrooms.manage.services.show', [$showroom, $bk]) }}"
                                                   class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-semibold text-[11px] transition shadow-xs">
                                                    Job Card
                                                </a>

                                                <form method="POST" action="{{ route('showrooms.manage.services.destroy', [$showroom, $bk]) }}"
                                                      onsubmit="return confirm('Cancel/Archive this appointment?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Archive appointment">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($bookings->hasPages())
                        <div class="p-4 border-t border-slate-200 bg-white">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-16 px-4 space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">No Service Bookings Found</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            No service appointments match the current filter criteria or calendar date.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: SERVICE CATALOG & CUSTOM PRICING    -->
        <!-- ========================================== -->
        <div x-show="tab === 'catalog'" class="space-y-6">
            <!-- Catalog Telemetry Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Total Offerings</span>
                    <p class="text-2xl font-black font-display text-slate-900 mt-1">{{ $totalServicesCount }}</p>
                    <span class="text-[10px] text-slate-500">Configured service packages</span>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                    <span class="text-[10px] uppercase font-bold text-emerald-600 tracking-wider">Online Advance Required</span>
                    <p class="text-2xl font-black font-display text-emerald-600 mt-1">{{ $paymentEnabledCount }}</p>
                    <span class="text-[10px] text-slate-500">Payment gate enabled</span>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                    <span class="text-[10px] uppercase font-bold text-amber-600 tracking-wider">Pay at Workshop / Bay</span>
                    <p class="text-2xl font-black font-display text-amber-600 mt-1">{{ $payAtBayCount }}</p>
                    <span class="text-[10px] text-slate-500">No upfront payment</span>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                    <span class="text-[10px] uppercase font-bold text-sky-600 tracking-wider">Active on Public Portal</span>
                    <p class="text-2xl font-black font-display text-sky-600 mt-1">{{ $activeCatalogCount }}</p>
                    <span class="text-[10px] text-slate-500">Visible for customer booking</span>
                </div>
            </div>

            <!-- Catalog Explanation Banner -->
            <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-50 via-white to-sky-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                            Custom Service Engine
                        </span>
                        <span class="text-xs text-slate-500 font-medium">Auto-synced with public booking portal</span>
                    </div>
                    <p class="text-xs text-slate-700 leading-relaxed max-w-2xl">
                        Add your own workshop services with custom pricing and specify whether online advance payment is enabled or disabled. Customers booking payment-enabled packages are seamlessly routed to Razorpay with workshop terms & conditions acceptance.
                    </p>
                </div>

                <button type="button" @click="createModalOpen = true"
                        class="btn-brand-primary px-5 py-2.5 rounded-xl font-bold text-xs shadow-md transition flex items-center justify-center gap-2 shrink-0 cursor-pointer" id="btn-add-service-package">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Add New Package</span>
                </button>
            </div>

            <!-- Custom Service Packages Grid -->
            @if ($customServices->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($customServices as $svc)
                        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs hover:shadow-md transition flex flex-col justify-between relative overflow-hidden group">
                            <!-- Status Accents -->
                            @if (! $svc->is_active)
                                <div class="absolute top-0 right-0 bg-slate-200 text-slate-600 font-bold text-[10px] uppercase px-3 py-1 rounded-bl-xl tracking-wider">
                                    Hidden / Draft
                                </div>
                            @endif

                            <div class="space-y-4">
                                <!-- Top Row: Badge & Duration -->
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        @if ($svc->badge)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $svc->badgeClasses() }}">
                                                {{ $svc->badge }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>{{ $svc->formattedDuration() }}</span>
                                        </span>
                                    </div>

                                    @if ($svc->is_active)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Active
                                        </span>
                                    @endif
                                </div>

                                <!-- Title & Price -->
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 group-hover:text-emerald-700 transition">
                                        {{ $svc->name }}
                                    </h3>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <span class="text-2xl font-black font-display text-emerald-600">
                                            {{ $svc->formattedPrice() }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 uppercase font-mono">INR</span>
                                    </div>
                                </div>

                                <!-- Payment Policy Mode -->
                                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block">Booking Payment</span>
                                        @if ($svc->is_payment_enabled)
                                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 mt-0.5">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                                <span>Online Advance Required</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 mt-0.5">
                                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                <span>Pay at Workshop / Bay</span>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Quick Payment Toggle Form -->
                                    <form method="POST" action="{{ route('showrooms.manage.services.packages.toggle-payment', [$showroom, $svc]) }}">
                                        @csrf
                                        <button type="submit"
                                                class="px-2.5 py-1.5 rounded-xl border text-[11px] font-bold transition shadow-2xs {{ $svc->is_payment_enabled ? 'bg-white hover:bg-slate-100 border-slate-300 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-700 border-emerald-600 text-white' }}"
                                                title="{{ $svc->is_payment_enabled ? 'Click to change to Pay at Bay' : 'Click to enable Online Payment' }}">
                                            {{ $svc->is_payment_enabled ? 'Switch to Bay' : 'Enable Pay' }}
                                        </button>
                                    </form>
                                </div>

                                <!-- Description -->
                                @if ($svc->description)
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        {{ $svc->description }}
                                    </p>
                                @endif

                                <!-- Inclusions / Feature list -->
                                @if (! empty($svc->features))
                                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Inclusions</span>
                                        <ul class="text-xs text-slate-600 space-y-1">
                                            @foreach ((array) $svc->features as $item)
                                                <li class="flex items-start gap-2">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span class="leading-tight">{{ $item }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            <!-- Card Bottom Actions -->
                            <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5">
                                    <!-- Edit Trigger -->
                                    <button type="button" @click="openEdit({{ json_encode($svc) }})"
                                            class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        <span>Edit</span>
                                    </button>

                                    <!-- Toggle Active Form -->
                                    <form method="POST" action="{{ route('showrooms.manage.services.packages.toggle-active', [$showroom, $svc]) }}">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-xl text-xs font-semibold transition border {{ $svc->is_active ? 'bg-white hover:bg-slate-50 border-slate-300 text-slate-600' : 'bg-emerald-50 hover:bg-emerald-100 border-emerald-300 text-emerald-700' }}">
                                            {{ $svc->is_active ? 'Hide' : 'Publish' }}
                                        </button>
                                    </form>
                                </div>

                                <!-- Delete Form -->
                                <form method="POST" action="{{ route('showrooms.manage.services.packages.destroy', [$showroom, $svc]) }}"
                                      onsubmit="return confirm('Remove package \'{{ $svc->name }}\' from workshop offerings?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Delete Package">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 px-4 bg-white rounded-3xl border border-slate-200 space-y-4 shadow-xs">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto border border-emerald-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">No Workshop Packages Configured</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Add your first motorcycle maintenance or repair service package with custom pricing and payment requirements.
                    </p>
                    <button type="button" @click="createModalOpen = true"
                            class="btn-brand-primary px-6 py-3 rounded-xl font-bold text-xs shadow-md transition cursor-pointer">
                        + Create First Package
                    </button>
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- MODAL 1: ADD SERVICE PACKAGE (LIGHT THEME) -->
        <!-- ========================================== -->
        <div x-cloak x-show="createModalOpen"
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="createModalOpen = false"></div>
            <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 text-left shadow-2xl border border-slate-200 my-8 overflow-hidden"
                     @click.away="createModalOpen = false">

                    <form method="POST" action="{{ route('showrooms.manage.services.packages.store', $showroom) }}" class="space-y-4">
                        @csrf

                        <!-- Modal Header -->
                        <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Add Workshop Service Package</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Create a customizable service offering for your customer booking portal.</p>
                            </div>
                            <button type="button" @click="createModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Form Fields -->
                        <div class="space-y-4 py-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Service Name <span class="text-rose-600">*</span>
                                </label>
                                <input type="text" name="name" required placeholder="e.g. Monsoon Care & Brake Flush"
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Amount / Price (₹) <span class="text-rose-600">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-500">₹</span>
                                        <input type="number" step="0.01" min="0" name="price" required placeholder="1499.00"
                                               class="w-full pl-8 pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 font-mono transition">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Est. Bay Duration (Mins) <span class="text-rose-600">*</span>
                                    </label>
                                    <input type="number" min="5" max="1440" name="estimated_duration_minutes" value="60" required
                                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 font-mono focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Badge / Tag (Optional)
                                    </label>
                                    <input type="text" name="badge" placeholder="e.g. Popular, Best Value, Express"
                                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Catalog Status
                                    </label>
                                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-300 cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" checked
                                               class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                                        <span class="text-xs font-semibold text-slate-800">Publish Immediately</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Online Payment Switch -->
                            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-1">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_payment_enabled" value="1" checked
                                           class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 mt-0.5">
                                    <div>
                                        <span class="text-xs font-bold text-emerald-900 block">Require Online Advance Payment</span>
                                        <span class="text-[11px] text-emerald-700 leading-tight block mt-0.5">
                                            When enabled, customers booking this package must settle payment upfront via Razorpay with Terms & Conditions acceptance. Uncheck to allow Pay at Bay.
                                        </span>
                                    </div>
                                </label>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Package Inclusions / Description
                                </label>
                                <textarea name="description" rows="2" placeholder="Brief summary of service scope and procedures..."
                                          class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Feature Bullet Points (One per line)
                                </label>
                                <textarea name="features" rows="3" placeholder="32-Point Comprehensive Inspection&#10;Synthetic Engine Oil Flush&#10;Hydraulic Brake Bleed & True"
                                          class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 font-mono transition"></textarea>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="createModalOpen = false"
                                    class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="btn-brand-primary px-6 py-2.5 rounded-xl text-xs font-bold shadow-md transition cursor-pointer" id="btn-save-service-package">
                                Save Service Package
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL 2: EDIT SERVICE PACKAGE (LIGHT THEME) -->
        <!-- ========================================== -->
        <div x-cloak x-show="editModalOpen"
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="editModalOpen = false"></div>
            <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 text-left shadow-2xl border border-slate-200 my-8 overflow-hidden"
                     @click.away="editModalOpen = false">

                    <form method="POST" :action="'{{ url('/showrooms/' . $showroom->slug . '/manage/services/packages') }}/' + editingService.id" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Modal Header -->
                        <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Edit Service Package</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Modify package name, custom amount, duration, and online payment requirement.</p>
                            </div>
                            <button type="button" @click="editModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Form Fields -->
                        <div class="space-y-4 py-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Service Name <span class="text-rose-600">*</span>
                                </label>
                                <input type="text" name="name" x-model="editingService.name" required
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Amount / Price (₹) <span class="text-rose-600">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-500">₹</span>
                                        <input type="number" step="0.01" min="0" name="price" x-model="editingService.price" required
                                               class="w-full pl-8 pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 font-mono focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Est. Bay Duration (Mins) <span class="text-rose-600">*</span>
                                    </label>
                                    <input type="number" min="5" max="1440" name="estimated_duration_minutes" x-model="editingService.estimated_duration_minutes" required
                                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 font-mono focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Badge / Tag
                                    </label>
                                    <input type="text" name="badge" x-model="editingService.badge" placeholder="e.g. Popular, Recommended"
                                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Catalog Status
                                    </label>
                                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-300 cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" x-model="editingService.is_active"
                                               class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                                        <span class="text-xs font-semibold text-slate-800">Active on Booking Portal</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Online Payment Switch -->
                            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-1">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_payment_enabled" value="1" x-model="editingService.is_payment_enabled"
                                           class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 mt-0.5">
                                    <div>
                                        <span class="text-xs font-bold text-emerald-900 block">Require Online Advance Payment</span>
                                        <span class="text-[11px] text-emerald-700 leading-tight block mt-0.5">
                                            When enabled, customers must pay via Razorpay advance checkout. Uncheck to allow Pay at Bay.
                                        </span>
                                    </div>
                                </label>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Package Inclusions / Description
                                </label>
                                <textarea name="description" rows="2" x-model="editingService.description"
                                          class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Feature Bullet Points (One per line)
                                </label>
                                <textarea name="features" rows="3" x-model="editingService.features"
                                          class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-900 font-mono focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"></textarea>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="editModalOpen = false"
                                    class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="btn-brand-primary px-6 py-2.5 rounded-xl text-xs font-bold shadow-md transition cursor-pointer" id="btn-update-service-package">
                                Update Service Package
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Floating Bulk Action Bar (Appointments) -->
        <div x-cloak x-show="selectedIds.length > 0"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-6"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-6"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-white/95 backdrop-blur border border-slate-300 rounded-2xl shadow-2xl px-5 py-3 flex items-center gap-4 text-xs">
            <span class="font-bold text-slate-800" x-text="selectedIds.length + ' appointments selected'"></span>

            <div class="h-4 w-px bg-slate-200"></div>

            <!-- Export Selected -->
            <form method="GET" action="{{ route('showrooms.manage.services.export', $showroom) }}" class="inline">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 font-semibold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Export Selected</span>
                </button>
            </form>

            <!-- Bulk Archive/Delete -->
            <form method="POST" action="{{ route('showrooms.manage.services.bulk-destroy', $showroom) }}"
                  onsubmit="return confirm('Are you sure you want to cancel/archive the selected appointments?');" class="inline">
                @csrf
                @method('DELETE')
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 font-semibold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Archive Selected</span>
                </button>
            </form>

            <button type="button" @click="selectedIds = []" class="text-slate-500 hover:text-slate-800 underline">
                Clear
            </button>
        </div>

        <!-- Booking Details Modal (Light Theme) -->
        <div x-cloak x-show="detailsModalOpen"
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="detailsModalOpen = false"></div>
            <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 text-left shadow-2xl border border-slate-200 my-8 overflow-hidden"
                     @click.away="detailsModalOpen = false">
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                        <div>
                            <span class="font-mono text-xs font-bold text-rose-600" x-text="activeBooking?.booking_number"></span>
                            <h3 class="text-lg font-bold text-slate-900" x-text="activeBooking?.customer_name"></h3>
                            <p class="text-xs text-slate-500" x-text="activeBooking?.date + ' • ' + activeBooking?.time_slot"></p>
                        </div>
                        <button type="button" @click="detailsModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Modal Body Grid -->
                    <div class="py-5 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Phone</span>
                                <a :href="'tel:' + activeBooking?.customer_phone" class="font-mono font-bold text-xs text-slate-900 hover:text-rose-600" x-text="activeBooking?.customer_phone"></a>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Email</span>
                                <a :href="'mailto:' + activeBooking?.customer_email" class="font-mono text-xs text-slate-900 hover:text-rose-600 truncate block" x-text="activeBooking?.customer_email"></a>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Motorcycle Model</span>
                                <span class="font-semibold text-xs text-slate-800" x-text="activeBooking?.bike"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Registration Plate</span>
                                <span class="font-mono font-bold text-xs text-rose-700" x-text="activeBooking?.reg"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Service Package</span>
                                <span class="font-semibold text-xs text-slate-800" x-text="activeBooking?.package"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Estimated Cost</span>
                                <span class="font-mono font-bold text-xs text-emerald-700" x-text="activeBooking?.cost"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Bay Number</span>
                                <span class="font-semibold text-xs text-slate-800" x-text="activeBooking?.bay"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Technician</span>
                                <span class="font-semibold text-xs text-slate-800" x-text="activeBooking?.technician"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 col-span-2">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Odometer Reading</span>
                                <span class="font-semibold text-xs text-slate-800" x-text="activeBooking?.odometer"></span>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Customer / Technician Notes</span>
                            <p class="text-xs text-slate-700 whitespace-pre-line leading-relaxed" x-text="activeBooking?.notes"></p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                        <button type="button" @click="detailsModalOpen = false" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @include('showrooms.partials.manage-nav-close')
</div>
@endsection
