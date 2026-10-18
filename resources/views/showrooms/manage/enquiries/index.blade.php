@extends('layouts.app')

@section('title', 'Enquiries & Test Rides - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

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
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-700 font-semibold">Enquiries</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Customer Enquiries & Test Rides</h1>
                <p class="text-sm text-slate-500 mt-1">Manage purchase leads, test-ride bookings, staff assignments, and conversion status.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.enquiries.create', $showroom) }}" target="_blank"
                   class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-xs font-semibold text-slate-700 hover:text-slate-900 transition flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Public Form Preview</span>
                </a>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Total Inquiries</span>
                <p class="text-2xl font-black font-display text-slate-900 mt-1">{{ $totalCount }}</p>
                <span class="text-[10px] text-slate-500">Lifetime leads received</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-sky-600 tracking-wider">New Leads</span>
                <p class="text-2xl font-black font-display text-sky-600 mt-1">{{ $newCount }}</p>
                <span class="text-[10px] text-slate-500">Awaiting initial contact</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-amber-600 tracking-wider">Test Rides Scheduled</span>
                <p class="text-2xl font-black font-display text-amber-600 mt-1">{{ $scheduledCount }}</p>
                <span class="text-[10px] text-slate-500">Confirmed customer rides</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-emerald-600 tracking-wider">Converted / Booked</span>
                <p class="text-2xl font-black font-display text-emerald-600 mt-1">{{ $completedCount }}</p>
                <span class="text-[10px] text-slate-500">Successful vehicle deliveries</span>
            </div>
        </div>

        <!-- Filters & Search Toolbar -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs mb-6">
            <form method="GET" action="{{ route('showrooms.manage.enquiries.index', $showroom) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Query -->
                <div>
                    <label for="search" class="sr-only">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ $search }}"
                               placeholder="Search customer, phone, ref #..."
                               class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>
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

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-xs font-semibold text-white transition text-center shadow-xs">
                        Filter
                    </button>
                    @if($search || $type || $status)
                        <a href="{{ route('showrooms.manage.enquiries.index', $showroom) }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                            Reset
                        </a>
                    @endif
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
                    <h3 class="text-base font-bold text-slate-900 mb-1">No enquiries found</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        @if($search || $type || $status)
                            No customer records match your filter criteria. Try clearing filters.
                        @else
                            Customer purchase enquiries and test ride bookings submitted through your storefront will show up here.
                        @endif
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                            <tr>
                                <th class="py-3 px-4">Ref #</th>
                                <th class="py-3 px-4">Customer Details</th>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4">Motorcycle Model</th>
                                <th class="py-3 px-4">Preferred Slot</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Assigned Staff</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($enquiries as $enquiry)
                                <tr class="hover:bg-slate-50/80 transition">
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
                                        <div class="text-[11px] text-slate-500 mt-0.5 space-y-0.5">
                                            <a href="tel:{{ $enquiry->customer_phone }}" class="hover:text-slate-900 transition font-mono">{{ $enquiry->customer_phone }}</a>
                                            <span class="text-slate-400 block sm:inline sm:ml-1">• {{ $enquiry->customer_email }}</span>
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

                                    <!-- Preferred Slot / Schedule -->
                                    <td class="py-3.5 px-4">
                                        @if($enquiry->scheduled_at)
                                            <div class="text-amber-700 font-semibold text-[11px]">
                                                Scheduled: {{ $enquiry->scheduled_at->format('M d, Y h:i A') }}
                                            </div>
                                        @elseif($enquiry->preferred_date)
                                            <div class="text-slate-700 text-[11px]">
                                                {{ $enquiry->preferred_date->format('M d, Y') }}
                                                @if($enquiry->preferred_time_slot)
                                                    <span class="text-slate-500 block text-[10px]">({{ $enquiry->preferred_time_slot }})</span>
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
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('showrooms.manage.enquiries.show', [$showroom, $enquiry]) }}"
                                               class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-semibold text-[11px] transition shadow-xs">
                                                Manage
                                            </a>

                                            <form method="POST" action="{{ route('showrooms.manage.enquiries.destroy', [$showroom, $enquiry]) }}" onsubmit="return confirm('Archive this enquiry?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Archive lead">
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
    </main>
</div>
@endsection
