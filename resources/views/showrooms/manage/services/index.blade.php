@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900">
    @include('showrooms.partials.manage-nav')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

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

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Workshop & Service Center</h1>
                <p class="text-sm text-slate-500 mt-1">Manage service appointments, live bay occupancy, mechanic assignments, and repair job cards.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.services.create', $showroom) }}" target="_blank"
                   class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-xs font-semibold text-slate-700 hover:text-slate-900 transition flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Public Booking Page</span>
                </a>

                <a href="{{ route('showrooms.services.track', $showroom) }}" target="_blank"
                   class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-xs font-semibold text-slate-700 hover:text-slate-900 transition flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Status Tracker</span>
                </a>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Today's Appointments</span>
                <p class="text-2xl font-black font-display text-slate-900 mt-1">{{ $todayTotal }}</p>
                <span class="text-[10px] text-slate-500">Scheduled for today</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-amber-600 tracking-wider">In Service Bays</span>
                <p class="text-2xl font-black font-display text-amber-600 mt-1">{{ $inServiceCount }}</p>
                <span class="text-[10px] text-slate-500">Actively on the lift</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-emerald-600 tracking-wider">Ready for Collection</span>
                <p class="text-2xl font-black font-display text-emerald-600 mt-1">{{ $readyCount }}</p>
                <span class="text-[10px] text-slate-500">Service completed</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-sky-600 tracking-wider">Pending Confirmation</span>
                <p class="text-2xl font-black font-display text-sky-600 mt-1">{{ $pendingCount }}</p>
                <span class="text-[10px] text-slate-500">Awaiting bay approval</span>
            </div>
        </div>

        <!-- Filters & Search Toolbar -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('showrooms.manage.services.index', $showroom) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Search Appointments</label>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Reg #, Customer, Ref #..."
                           class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 placeholder-slate-400 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Appointment Date</label>
                    <input type="date" name="date" value="{{ $date }}"
                           class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
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

                <!-- Actions -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs">
                        Filter
                    </button>
                    <a href="{{ route('showrooms.manage.services.index', $showroom) }}"
                       class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 hover:text-slate-900 text-xs font-semibold transition" title="Clear Filters">
                        Clear
                    </a>
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
                                <th class="py-3 px-4">Ref & Scheduled Slot</th>
                                <th class="py-3 px-4">Customer Details</th>
                                <th class="py-3 px-4">Motorcycle & Plate</th>
                                <th class="py-3 px-4">Package</th>
                                <th class="py-3 px-4">Bay & Mechanic</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($bookings as $bk)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <!-- Ref & Scheduled Slot -->
                                    <td class="py-3.5 px-4">
                                        <a href="{{ route('showrooms.manage.services.show', [$showroom, $bk]) }}" class="font-mono font-bold text-rose-600 hover:underline block">
                                            {{ $bk->booking_number }}
                                        </a>
                                        <div class="text-[11px] text-slate-700 mt-0.5 font-medium">
                                            {{ $bk->booking_date->format('M d, Y') }}
                                        </div>
                                        <span class="text-[10px] text-slate-500 font-mono">{{ $bk->time_slot }}</span>
                                    </td>

                                    <!-- Customer Details -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 text-sm">
                                            {{ $bk->customer_name }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5 space-y-0.5">
                                            <a href="tel:{{ $bk->customer_phone }}" class="hover:text-slate-900 transition font-mono">{{ $bk->customer_phone }}</a>
                                            <span class="text-slate-400 block sm:inline sm:ml-1">• {{ $bk->customer_email }}</span>
                                        </div>
                                    </td>

                                    <!-- Motorcycle & Plate -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800 text-xs">
                                            {{ $bk->bike_make_model }}
                                        </div>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-slate-100 border border-slate-200 text-rose-700">
                                            {{ $bk->registration_number }}
                                        </span>
                                        @if ($bk->odometer_reading)
                                            <span class="text-[10px] text-slate-500 block mt-0.5">{{ number_format($bk->odometer_reading) }} km</span>
                                        @endif
                                    </td>

                                    <!-- Service Package -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold {{ $bk->service_type->badgeClasses() }}">
                                            {{ $bk->service_type->label() }}
                                        </span>
                                        <div class="text-[11px] font-mono text-emerald-700 font-bold mt-1">
                                            ₹{{ number_format($bk->final_cost ?? $bk->estimated_cost) }}
                                        </div>
                                    </td>

                                    <!-- Bay & Mechanic -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-xs text-slate-900">
                                            {{ $bk->bay_number ?? 'No Bay Assigned' }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            {{ $bk->assignedTechnician?->name ?? 'Unassigned' }}
                                        </div>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold {{ $bk->status->badgeClasses() }}">
                                            {{ $bk->status->label() }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('showrooms.manage.services.show', [$showroom, $bk]) }}"
                                               class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg font-semibold text-[11px] transition shadow-xs">
                                                Job Card
                                            </a>

                                            <form method="POST" action="{{ route('showrooms.manage.services.destroy', [$showroom, $bk]) }}"
                                                  onsubmit="return confirm('Cancel/Archive this appointment?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Archive appointment">
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
    @include('showrooms.partials.manage-nav-close')
</div>
@endsection
