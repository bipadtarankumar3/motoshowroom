<x-app-layout :title="'Live Service Tracker - ' . $showroom->name">
    {{-- Branded Dealership Header --}}
    @include('showrooms.partials.header')

<div class="min-h-screen bg-slate-50 text-slate-900 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">

        <!-- Back Navigation -->
        <div class="flex items-center justify-between text-xs text-slate-500">
            <a href="{{ route('showrooms.show', $showroom) }}" class="inline-flex items-center gap-1.5 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to {{ $showroom->name }}</span>
            </a>
            <a href="{{ route('showrooms.services.create', $showroom) }}" class="inline-flex items-center gap-1.5 text-emerald-600 hover:text-emerald-700 font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Book New Service Appointment</span>
            </a>
        </div>

        <!-- Header -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                        Live Digital Job Card
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Real-Time Bay Telemetry
                    </span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black font-display text-slate-900 tracking-tight">
                    Track Your Motorcycle Service
                </h1>
                <p class="text-sm text-slate-600 max-w-xl">
                    Enter your service reference number or vehicle registration plate to monitor diagnostics, assigned bay status, and collection readiness.
                </p>

                <!-- Search Input Form -->
                <form method="GET" action="{{ route('showrooms.services.track', $showroom) }}" class="flex flex-col sm:flex-row gap-3 pt-2 max-w-xl">
                    <div class="relative flex-1">
                        <input type="text" name="booking" value="{{ $searchRef }}" required
                               placeholder="e.g. SRV-20261018-XXXX or KA-05-EM-9921"
                               class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm font-mono uppercase tracking-wider focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="submit"
                            class="px-6 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition">
                        Track Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Notification if newly booked -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($bookingRecord)
            @php
                $statusVal = $bookingRecord->status->value;
                $steps = [
                    'pending' => ['label' => 'Appointment Booked', 'desc' => 'Request received by service reception'],
                    'confirmed' => ['label' => 'Bay Scheduled', 'desc' => 'Slot confirmed & technician assigned'],
                    'checked_in' => ['label' => 'Bike Checked In', 'desc' => 'Vehicle handed over at workshop bay'],
                    'in_service' => ['label' => 'In Service / Repair', 'desc' => 'Technician actively performing service'],
                    'ready_for_collection' => ['label' => 'Ready for Pickup', 'desc' => 'Quality inspection passed, ready for delivery'],
                    'completed' => ['label' => 'Completed & Delivered', 'desc' => 'Invoiced & handed over to customer'],
                ];
                $stepKeys = array_keys($steps);
                $currentIdx = array_search($statusVal, $stepKeys, true);
                if ($currentIdx === false) {
                    $currentIdx = 0;
                }
            @endphp

            <!-- Live Progress Timeline Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-8">
                <!-- Vehicle & Ref Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-black font-display text-slate-900">{{ $bookingRecord->bike_make_model }}</h2>
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-slate-100 border border-slate-200 text-slate-700">
                                {{ $bookingRecord->registration_number }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 font-mono">
                            Ref: <span class="text-rose-600 font-bold">#{{ $bookingRecord->booking_number }}</span> • Booked by {{ $bookingRecord->customer_name }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold {{ $bookingRecord->status->badgeClasses() }}">
                            {{ $bookingRecord->status->label() }}
                        </span>
                    </div>
                </div>

                <!-- Visual Step-by-Step Timeline -->
                <div class="relative">
                    <div class="space-y-6">
                        @foreach ($steps as $key => $meta)
                            @php
                                $idx = array_search($key, $stepKeys, true);
                                $isPast = $idx < $currentIdx;
                                $isCurrent = $idx === $currentIdx;
                                $isFuture = $idx > $currentIdx;
                            @endphp
                            <div class="flex items-start gap-4 relative">
                                <!-- Step Circle -->
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold z-10 transition
                                    {{ $isPast ? 'bg-emerald-600 text-white shadow-xs' : ($isCurrent ? 'bg-rose-600 text-white ring-4 ring-rose-100 shadow-sm animate-pulse' : 'bg-slate-100 border border-slate-200 text-slate-400') }}">
                                    @if ($isPast)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        {{ $idx + 1 }}
                                    @endif
                                </div>

                                <!-- Step Text -->
                                <div class="flex-1 pt-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-bold {{ $isCurrent ? 'text-slate-900' : ($isPast ? 'text-slate-700' : 'text-slate-400') }}">
                                            {{ $meta['label'] }}
                                        </h3>
                                        @if ($isCurrent)
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                                Active Stage
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $meta['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Job Card Key Metrics -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 border-t border-slate-100">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Service Package</span>
                        <p class="text-sm font-bold text-slate-900 mt-1">{{ $bookingRecord->service_type->label() }}</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Scheduled Slot</span>
                        <p class="text-sm font-bold text-slate-900 mt-1">{{ $bookingRecord->booking_date->format('M d, Y') }}</p>
                        <span class="text-[10px] text-slate-500 font-mono">{{ $bookingRecord->time_slot }}</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Workshop Bay</span>
                        <p class="text-sm font-bold text-emerald-600 mt-1">{{ $bookingRecord->bay_number ?? 'Assigned on arrival' }}</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Estimated Cost</span>
                        <p class="text-sm font-bold text-emerald-600 mt-1">₹{{ number_format($bookingRecord->final_cost ?? $bookingRecord->estimated_cost) }}</p>
                        <span class="text-[10px] text-slate-500">{{ $bookingRecord->final_cost ? 'Final Invoiced' : 'Initial Estimate' }}</span>
                    </div>
                </div>

                @if ($bookingRecord->technician_notes)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Technician Diagnostic Notes</p>
                        <p class="text-xs text-slate-600 leading-relaxed font-mono">{{ $bookingRecord->technician_notes }}</p>
                    </div>
                @endif

                <!-- Dealership Contact Help -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-slate-900">Need to reschedule or speak with service advisor?</p>
                        <p class="text-xs text-slate-500">{{ $showroom->name }} • {{ $showroom->city }}, {{ $showroom->state }}</p>
                    </div>
                    @if ($showroom->phone)
                        <a href="tel:{{ $showroom->phone }}"
                           class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 text-xs font-bold transition flex items-center justify-center gap-2 shrink-0 shadow-xs">
                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>{{ $showroom->phone }}</span>
                        </a>
                    @endif
                </div>
            </div>

        @elseif ($searched)
            <!-- Not Found State -->
            <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center space-y-4 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">No Service Booking Found</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    We could not find an appointment matching "<span class="font-mono text-slate-800">{{ $searchRef }}</span>" for this showroom. Please verify your reference number or vehicle registration.
                </p>
                <div class="pt-2">
                    <a href="{{ route('showrooms.services.create', $showroom) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-xs">
                        <span>Book an Appointment</span>
                    </a>
                </div>
            </div>
        @else
            <!-- Initial Prompt State -->
            <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center space-y-4 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto border border-slate-200">
                    <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Track Your Repair Progress</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    Enter the Reference Number received during booking (e.g. <span class="font-mono text-slate-700">SRV-20261018-EA64</span>) or your Motorcycle Registration Number above.
                </p>
            </div>
        @endif

    </div>
</div>

    {{-- Motorcycle Showroom Footer --}}
    @include('showrooms.partials.footer')
</x-app-layout>
