@extends('layouts.app')

@section('title', 'Enquiries & Test Rides - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col"
     x-data="{
        selectedIds: [],
        allIds: {{ json_encode($enquiries->pluck('id')) }},
        toggleAll() {
            if (this.selectedIds.length === this.allIds.length) {
                this.selectedIds = [];
            } else {
                this.selectedIds = [...this.allIds];
            }
        },
        activeEnquiry: null,
        detailsModalOpen: false,
        openDetails(enquiry) {
            this.activeEnquiry = enquiry;
            this.detailsModalOpen = true;
        }
     }">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8">

        <!-- Flash Feedback Messages -->
        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-700 font-semibold">Bike Bookings</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Bike Bookings & Customer Enquiries</h1>
                <p class="text-sm text-slate-500 mt-1">Manage motorcycle purchase bookings, test rides, lead conversions, and staff assignments.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.manage.enquiries.export', $showroom) }}"
                   class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-xs font-bold text-slate-700 hover:text-slate-900 transition flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Export Leads (CSV)</span>
                </a>

                <a href="{{ route('showrooms.enquiries.create', $showroom) }}" target="_blank"
                   class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-xs font-semibold text-slate-700 hover:text-slate-900 transition flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Public Form Preview</span>
                </a>
            </div>
        </div>

        <!-- Quick Timeframe Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-4 scrollbar-none">
            <a href="{{ route('showrooms.manage.enquiries.index', $showroom) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ empty($timeframe) && empty($status) ? 'bg-slate-900 text-white shadow-xs' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
                <span>All Leads</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ empty($timeframe) && empty($status) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $totalCount }}</span>
            </a>

            <a href="{{ route('showrooms.manage.enquiries.index', [$showroom, 'timeframe' => 'today']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $timeframe === 'today' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white hover:bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Today's Bookings</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $timeframe === 'today' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">{{ $todayCount }}</span>
            </a>

            <a href="{{ route('showrooms.manage.enquiries.index', [$showroom, 'timeframe' => 'tomorrow']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $timeframe === 'tomorrow' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white hover:bg-amber-50 text-amber-800 border border-amber-200' }}">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                <span>Tomorrow's Bookings</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $timeframe === 'tomorrow' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">{{ $tomorrowCount }}</span>
            </a>

            <a href="{{ route('showrooms.manage.enquiries.index', [$showroom, 'timeframe' => 'this_week']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $timeframe === 'this_week' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white hover:bg-indigo-50 text-indigo-800 border border-indigo-200' }}">
                <span>This Week</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $timeframe === 'this_week' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-800' }}">{{ $thisWeekCount }}</span>
            </a>

            <a href="{{ route('showrooms.manage.enquiries.index', [$showroom, 'status' => 'new']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $status === 'new' ? 'bg-sky-600 text-white shadow-xs' : 'bg-white hover:bg-sky-50 text-sky-800 border border-sky-200' }}">
                <span>New Leads</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $status === 'new' ? 'bg-white/20 text-white' : 'bg-sky-100 text-sky-800' }}">{{ $newCount }}</span>
            </a>

            <a href="{{ route('showrooms.manage.enquiries.index', [$showroom, 'status' => 'test_ride_scheduled']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $status === 'test_ride_scheduled' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white hover:bg-purple-50 text-purple-800 border border-purple-200' }}">
                <span>Test Rides Scheduled</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $status === 'test_ride_scheduled' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-800' }}">{{ $scheduledCount }}</span>
            </a>

            <a href="{{ route('showrooms.manage.enquiries.index', [$showroom, 'status' => 'booked']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ $status === 'booked' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-white hover:bg-emerald-50 text-emerald-900 border border-emerald-300' }}">
                <span>Booked & Delivered</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $status === 'booked' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-900' }}">{{ $completedCount }}</span>
            </a>
        </div>

        <!-- Metric KPI Cards (6 Cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <a href="{{ route('showrooms.manage.enquiries.index', $showroom) }}"
               class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-slate-400 hover:shadow-sm transition block group">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block">Total Inquiries</span>
                <p class="text-2xl font-black font-display text-slate-900 mt-1.5 group-hover:scale-105 transition-transform">{{ $totalCount }}</p>
                <span class="text-[10px] text-slate-500 block mt-0.5">Lifetime leads</span>
            </a>

            <a href="{{ route('showrooms.manage.enquiries.index', [$showroom, 'timeframe' => 'today']) }}"
               class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-emerald-400 hover:shadow-sm transition block group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-emerald-700 tracking-wider">Today's Bookings</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
                <p class="text-2xl font-black font-display text-emerald-700 mt-1.5 group-hover:scale-105 transition-transform">{{ $todayCount }}</p>
                <span class="text-[10px] text-slate-500 block mt-0.5">Test rides & visits</span>
            </a>

            <a href="{{ route('showrooms.manage.enquiries.index', [$showroom, 'timeframe' => 'tomorrow']) }}"
               class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-amber-400 hover:shadow-sm transition block group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-amber-700 tracking-wider">Tomorrow's Bookings</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                </div>
                <p class="text-2xl font-black font-display text-amber-700 mt-1.5 group-hover:scale-105 transition-transform">{{ $tomorrowCount }}</p>
                <span class="text-[10px] text-slate-500 block mt-0.5">Scheduled tomorrow</span>
            </a>

            <a href="{{ route('showrooms.manage.enquiries.index', [$showroom, 'status' => 'new']) }}"
               class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-sky-400 hover:shadow-sm transition block group">
                <span class="text-[10px] uppercase font-bold text-sky-600 tracking-wider block">New Leads</span>
                <p class="text-2xl font-black font-display text-sky-600 mt-1.5 group-hover:scale-105 transition-transform">{{ $newCount }}</p>
                <span class="text-[10px] text-slate-500 block mt-0.5">Awaiting contact</span>
            </a>

            <a href="{{ route('showrooms.manage.enquiries.index', [$showroom, 'status' => 'test_ride_scheduled']) }}"
               class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-purple-400 hover:shadow-sm transition block group">
                <span class="text-[10px] uppercase font-bold text-purple-600 tracking-wider block">Test Rides Scheduled</span>
                <p class="text-2xl font-black font-display text-purple-600 mt-1.5 group-hover:scale-105 transition-transform">{{ $scheduledCount }}</p>
                <span class="text-[10px] text-slate-500 block mt-0.5">Confirmed rides</span>
            </a>

            <a href="{{ route('showrooms.manage.enquiries.index', [$showroom, 'status' => 'booked']) }}"
               class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-emerald-400 hover:shadow-sm transition block group">
                <span class="text-[10px] uppercase font-bold text-emerald-700 tracking-wider block">Converted / Booked</span>
                <p class="text-2xl font-black font-display text-emerald-700 mt-1.5 group-hover:scale-105 transition-transform">{{ $completedCount }}</p>
                <span class="text-[10px] text-slate-500 block mt-0.5">Completed orders</span>
            </a>
        </div>

        <!-- Filters & Search Toolbar -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs mb-6">
            <form method="GET" action="{{ route('showrooms.manage.enquiries.index', $showroom) }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <!-- Search Query -->
                    <div class="lg:col-span-2">
                        <label for="search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $search }}"
                                   placeholder="Search customer, phone, ref #, bike..."
                                   class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                    </div>

                    <!-- Timeframe Filter -->
                    <div>
                        <label for="timeframe" class="sr-only">Timeframe</label>
                        <select name="timeframe" id="timeframe"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Timeframes</option>
                            <option value="today" {{ $timeframe === 'today' ? 'selected' : '' }}>Today's Bookings</option>
                            <option value="tomorrow" {{ $timeframe === 'tomorrow' ? 'selected' : '' }}>Tomorrow's Bookings</option>
                            <option value="this_week" {{ $timeframe === 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="upcoming" {{ $timeframe === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="past" {{ $timeframe === 'past' ? 'selected' : '' }}>Past / Overdue</option>
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="sr-only">Type</label>
                        <select name="type" id="type"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Enquiry Types</option>
                            @foreach($types as $t)
                                <option value="{{ $t->value }}" {{ $type === $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="sr-only">Status</label>
                        <select name="status" id="status"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Lead Statuses</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st->value }}" {{ $status === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assigned Staff Filter -->
                    <div>
                        <label for="assigned_to" class="sr-only">Assigned Staff</label>
                        <select name="assigned_to" id="assigned_to"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Staff</option>
                            @foreach($staffMembers as $member)
                                <option value="{{ $member->id }}" {{ $assignedTo == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100">
                    <div class="flex items-center gap-2">
                        <label for="date" class="text-xs font-semibold text-slate-600">Specific Date:</label>
                        <input type="date" name="date" id="date" value="{{ $date }}"
                               class="px-2.5 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="py-2 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-xs font-semibold text-white transition text-center shadow-xs cursor-pointer">
                            Apply Filters
                        </button>
                        @if($search || $type || $status || $timeframe || $date || $assignedTo)
                            <a href="{{ route('showrooms.manage.enquiries.index', $showroom) }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Enquiries Data Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            @if($enquiries->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto text-slate-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">No bookings or leads found</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        @if($search || $type || $status || $timeframe || $date || $assignedTo)
                            No customer records match your filter criteria. Try clearing filters.
                        @else
                            Customer motorcycle purchase bookings and test ride appointments submitted through your storefront will show up here.
                        @endif
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                            <tr>
                                <th class="py-3 px-4 w-10 text-center">
                                    <input type="checkbox"
                                           @change="toggleAll()"
                                           :checked="selectedIds.length === allIds.length && allIds.length > 0"
                                           class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
                                </th>
                                <th class="py-3 px-4">Ref #</th>
                                <th class="py-3 px-4">Customer Details</th>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4">Motorcycle Model</th>
                                <th class="py-3 px-4">Booking / Slot Date</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Assigned Staff</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($enquiries as $enquiry)
                                @php
                                    $isToday = false;
                                    $isTomorrow = false;
                                    $isPast = false;
                                    $targetDate = $enquiry->scheduled_at ? $enquiry->scheduled_at->toDateString() : ($enquiry->preferred_date ? $enquiry->preferred_date->toDateString() : null);

                                    if ($targetDate) {
                                        $isToday = $targetDate === today()->toDateString();
                                        $isTomorrow = $targetDate === today()->addDay()->toDateString();
                                        $isPast = $targetDate < today()->toDateString();
                                    }

                                    $cleanPhone = preg_replace('/[^0-9]/', '', $enquiry->customer_phone);

                                    $enquiryJson = [
                                        'id' => $enquiry->id,
                                        'enquiry_number' => $enquiry->enquiry_number,
                                        'customer_name' => $enquiry->customer_name,
                                        'customer_phone' => $enquiry->customer_phone,
                                        'customer_email' => $enquiry->customer_email,
                                        'type' => $enquiry->type->label(),
                                        'bike_name' => $enquiry->bike?->name ?? 'General Dealership Inquiry',
                                        'status' => $enquiry->status->label(),
                                        'slot' => $enquiry->scheduled_at ? $enquiry->scheduled_at->format('M d, Y h:i A') : ($enquiry->preferred_date ? $enquiry->preferred_date->format('M d, Y') . ' (' . ($enquiry->preferred_time_slot ?? 'N/A') . ')' : 'Immediate Contact'),
                                        'message' => $enquiry->message ?? $enquiry->notes ?? 'No message provided',
                                        'assigned' => $enquiry->assignedStaff?->name ?? 'Unassigned',
                                        'created_at' => $enquiry->created_at->format('M d, Y h:i A'),
                                    ];
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition {{ $isToday ? 'bg-emerald-50/20' : '' }}" :class="selectedIds.includes({{ $enquiry->id }}) ? 'bg-rose-50/40' : ''">
                                    <!-- Row Checkbox -->
                                    <td class="py-3.5 px-4 text-center">
                                        <input type="checkbox"
                                               :value="{{ $enquiry->id }}"
                                               x-model.number="selectedIds"
                                               class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
                                    </td>

                                    <!-- Ref # & Date -->
                                    <td class="py-3.5 px-4">
                                        <a href="{{ route('showrooms.manage.enquiries.show', [$showroom, $enquiry]) }}" class="font-mono font-bold text-rose-600 hover:underline block">
                                            {{ $enquiry->enquiry_number }}
                                        </a>
                                        <span class="text-[10px] text-slate-500 mt-0.5 block">
                                            {{ $enquiry->created_at->format('M d, Y h:i A') }}
                                        </span>
                                    </td>

                                    <!-- Customer Details -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 text-sm">
                                            {{ $enquiry->customer_name }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5 flex flex-wrap items-center gap-1.5">
                                            <a href="tel:{{ $enquiry->customer_phone }}" class="hover:text-slate-900 transition font-mono">{{ $enquiry->customer_phone }}</a>
                                            @if($cleanPhone)
                                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-bold" title="Open WhatsApp chat">
                                                    💬 WA
                                                </a>
                                            @endif
                                            <span class="text-slate-400">• {{ $enquiry->customer_email }}</span>
                                        </div>
                                    </td>

                                    <!-- Type Badge -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold {{ $enquiry->type->badgeClasses() }}">
                                            {{ $enquiry->type->label() }}
                                        </span>
                                    </td>

                                    <!-- Motorcycle Info -->
                                    <td class="py-3.5 px-4">
                                        @if($enquiry->bike)
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0">
                                                    <img src="{{ $enquiry->bike->primary_image_url }}" alt="{{ $enquiry->bike->name }}" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-slate-900 block">{{ $enquiry->bike->name }}</span>
                                                    <span class="font-mono text-[10px] text-slate-500">{{ $enquiry->bike->formatted_price }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">General Dealership Inquiry</span>
                                        @endif
                                    </td>

                                    <!-- Preferred Slot / Schedule Date with Smart Badges -->
                                    <td class="py-3.5 px-4">
                                        @if($isToday)
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                                <span>TODAY</span>
                                                <span class="font-normal text-emerald-700">• {{ $enquiry->preferred_time_slot ?? $enquiry->scheduled_at?->format('h:i A') ?? 'All Day' }}</span>
                                            </div>
                                        @elseif($isTomorrow)
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-100 text-amber-800 border border-amber-300 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                <span>TOMORROW</span>
                                                <span class="font-normal text-amber-700">• {{ $enquiry->preferred_time_slot ?? $enquiry->scheduled_at?->format('h:i A') ?? 'All Day' }}</span>
                                            </div>
                                        @elseif($enquiry->scheduled_at)
                                            <div class="text-[11px] font-semibold {{ $isPast ? 'text-slate-500' : 'text-sky-800' }}">
                                                {{ $enquiry->scheduled_at->format('M d, Y') }}
                                                <span class="text-[10px] text-slate-500 block">{{ $enquiry->scheduled_at->format('h:i A') }}</span>
                                            </div>
                                        @elseif($enquiry->preferred_date)
                                            <div class="text-[11px] font-semibold {{ $isPast ? 'text-slate-500' : 'text-slate-800' }}">
                                                {{ $enquiry->preferred_date->format('M d, Y') }}
                                                @if($enquiry->preferred_time_slot)
                                                    <span class="text-slate-500 block text-[10px]">{{ $enquiry->preferred_time_slot }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-[10px]">Immediate Contact</span>
                                        @endif
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold {{ $enquiry->status->badgeClasses() }}">
                                            {{ $enquiry->status->label() }}
                                        </span>
                                    </td>

                                    <!-- Assigned Staff -->
                                    <td class="py-3.5 px-4">
                                        @if($enquiry->assignedStaff)
                                            <span class="font-medium text-slate-800 text-xs">
                                                {{ $enquiry->assignedStaff->name }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Unassigned</span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button"
                                                    @click="openDetails({{ json_encode($enquiryJson) }})"
                                                    class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold text-[11px] transition shadow-xs flex items-center gap-1 cursor-pointer"
                                                    title="Quick Details">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Details</span>
                                            </button>

                                            <a href="{{ route('showrooms.manage.enquiries.show', [$showroom, $enquiry]) }}"
                                               class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-semibold text-[11px] transition shadow-xs">
                                                Manage
                                            </a>

                                            <form method="POST" action="{{ route('showrooms.manage.enquiries.destroy', [$showroom, $enquiry]) }}" onsubmit="return confirm('Archive this enquiry?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Archive lead">
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

                @if($enquiries->hasPages())
                    <div class="p-4 border-t border-slate-200 bg-white">
                        {{ $enquiries->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Floating Bulk Action Bar -->
        <div x-cloak x-show="selectedIds.length > 0"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-6"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-6"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-white/95 backdrop-blur border border-slate-300 rounded-2xl shadow-2xl px-5 py-3 flex items-center gap-4 text-xs">
            <span class="font-bold text-slate-800" x-text="selectedIds.length + ' enquiries selected'"></span>

            <div class="h-4 w-px bg-slate-200"></div>

            <!-- Export Selected -->
            <form method="GET" action="{{ route('showrooms.manage.enquiries.export', $showroom) }}" class="inline">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 font-semibold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Export Selected</span>
                </button>
            </form>

            <!-- Bulk Archive/Delete -->
            <form method="POST" action="{{ route('showrooms.manage.enquiries.bulk-destroy', $showroom) }}"
                  onsubmit="return confirm('Are you sure you want to archive the selected enquiries?');" class="inline">
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

        <!-- Enquiry Details Modal (Light Theme) -->
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
                            <span class="font-mono text-xs font-bold text-rose-600" x-text="activeEnquiry?.enquiry_number"></span>
                            <h3 class="text-lg font-bold text-slate-900" x-text="activeEnquiry?.customer_name"></h3>
                            <p class="text-xs text-slate-500" x-text="'Submitted on ' + activeEnquiry?.created_at"></p>
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
                                <a :href="'tel:' + activeEnquiry?.customer_phone" class="font-mono font-bold text-xs text-slate-900 hover:text-rose-600" x-text="activeEnquiry?.customer_phone"></a>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Email</span>
                                <a :href="'mailto:' + activeEnquiry?.customer_email" class="font-mono text-xs text-slate-900 hover:text-rose-600 truncate block" x-text="activeEnquiry?.customer_email"></a>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Enquiry Type</span>
                                <span class="font-semibold text-xs text-slate-800" x-text="activeEnquiry?.type"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Current Status</span>
                                <span class="font-semibold text-xs text-emerald-700" x-text="activeEnquiry?.status"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Motorcycle Model</span>
                                <span class="font-semibold text-xs text-slate-800" x-text="activeEnquiry?.bike_name"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Preferred Slot</span>
                                <span class="font-semibold text-xs text-slate-800" x-text="activeEnquiry?.slot"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 col-span-2">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Assigned Staff</span>
                                <span class="font-semibold text-xs text-slate-800" x-text="activeEnquiry?.assigned"></span>
                            </div>
                        </div>

                        <!-- Customer Message / Notes -->
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Customer Message / Special Requirements</span>
                            <p class="text-xs text-slate-700 whitespace-pre-line leading-relaxed" x-text="activeEnquiry?.message"></p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="detailsModalOpen = false" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </main>
    @include('showrooms.partials.manage-nav-close')
</div>
@endsection
