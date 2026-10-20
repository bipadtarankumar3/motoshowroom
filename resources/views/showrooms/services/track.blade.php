<x-app-layout :title="'Live Service Tracker - ' . $showroom->name">
    {{-- Branded Dealership Header --}}
    @include('showrooms.partials.header')

    <div class="min-h-screen bg-slate-50/70 text-slate-900 py-8 px-4 sm:px-6 lg:px-8 xl:px-12"
         x-data="{
             copiedRef: false,
             copyToClipboard(text) {
                 navigator.clipboard.writeText(text);
                 this.copiedRef = true;
                 setTimeout(() => this.copiedRef = false, 2000);
             }
         }">
        <div class="w-full max-w-[1680px] mx-auto space-y-8">

            {{-- ═══ TOP TELEMETRY BREADCRUMB & QUICK ACTIONS ═══ --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                <div class="flex items-center gap-2 text-slate-500 flex-wrap">
                    <a href="{{ route('showrooms.show', $showroom) }}" class="inline-flex items-center gap-1.5 hover:text-slate-900 font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>{{ $showroom->name }}</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('showrooms.services.create', $showroom) }}" class="hover:text-slate-900 transition">Workshop Pitstop</a>
                    <span class="text-slate-300">/</span>
                    <span class="font-bold text-slate-800">Live Service Telemetry</span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold bg-white border border-slate-200/90 text-slate-700 shadow-2xs">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Live Pit Bay Telemetry Active</span>
                    </span>

                    <a href="{{ route('showrooms.services.create', $showroom) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 hover:text-slate-950 font-bold transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-[#FF6B00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Book New Service</span>
                    </a>
                </div>
            </div>

            {{-- ═══ HERO TELEMETRY SEARCH COCKPIT ═══ --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-slate-200 shadow-xs relative overflow-hidden">
                {{-- Decorative motorsport accent gradients --}}
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-gradient-to-br from-orange-400/10 via-rose-500/10 to-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute left-1/3 -bottom-24 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div class="max-w-2xl space-y-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200/80">
                                <span>🏍️</span>
                                <span>Digital Job Card Monitor</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                <span>Certified Workshop Diagnostics</span>
                            </span>
                        </div>

                        <h1 class="text-2xl sm:text-4xl font-black font-display text-slate-900 tracking-tight leading-tight">
                            Track Your Motorcycle Service
                        </h1>

                        <p class="text-sm text-slate-600 leading-relaxed">
                            Monitor your motorcycle's live diagnostic status, assigned workshop bay, technician milestones, and invoice settlement in real-time.
                        </p>
                    </div>

                    {{-- Search Input Form Capsule --}}
                    <div class="w-full lg:max-w-lg bg-slate-50/90 p-2 sm:p-2.5 rounded-2xl border border-slate-200/90 shadow-2xs">
                        <form method="GET" action="{{ route('showrooms.services.track', $showroom) }}" class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text"
                                       name="booking"
                                       value="{{ $searchRef }}"
                                       required
                                       placeholder="SRV-20261019-XXXX or Plate No."
                                       class="w-full pl-10 pr-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 placeholder-slate-400 text-sm font-mono uppercase tracking-wider font-semibold focus:border-[#FF6B00] focus:ring-2 focus:ring-[#FF6B00]/20 transition shadow-2xs">
                            </div>

                            <button type="submit"
                                    class="px-6 py-3 rounded-xl btn-brand-primary font-bold text-xs uppercase tracking-wider shadow-sm transition flex items-center justify-center gap-2 shrink-0 cursor-pointer"
                                    style="box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;">
                                <span>Track Job Card</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </form>

                        <div class="flex items-center justify-between px-2 pt-2 text-[11px] text-slate-500 font-medium">
                            <span>Supported: Service Ref ID or Vehicle Registration</span>
                            @if($bookingRecord)
                                <a href="{{ route('showrooms.services.track', $showroom) }}" class="font-semibold hover:underline" style="color: {{ $showroom->primary_color }};">Clear Search</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ NOTIFICATION ALERTS ═══ --}}
            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-start sm:items-center gap-3 shadow-2xs">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="font-medium leading-relaxed">{{ session('success') }}</span>
                </div>
            @endif

            {{-- ═══ ACTIVE BOOKING TELEMETRY DASHBOARD ═══ --}}
            @if ($bookingRecord)
                @php
                    $statusVal = $bookingRecord->status->value;
                    $steps = [
                        'pending' => [
                            'label' => 'Appointment Booked',
                            'short' => 'Booked',
                            'desc' => 'Request received by service reception',
                            'time' => $bookingRecord->created_at,
                        ],
                        'confirmed' => [
                            'label' => 'Bay Scheduled',
                            'short' => 'Scheduled',
                            'desc' => 'Slot confirmed & technician assigned',
                            'time' => $bookingRecord->confirmed_at,
                        ],
                        'checked_in' => [
                            'label' => 'Bike Checked In',
                            'short' => 'Checked In',
                            'desc' => 'Vehicle handed over at workshop bay',
                            'time' => $bookingRecord->checked_in_at,
                        ],
                        'in_service' => [
                            'label' => 'In Service / Repair',
                            'short' => 'In Service',
                            'desc' => 'Technician actively performing service',
                            'time' => $bookingRecord->in_service_at,
                        ],
                        'ready_for_collection' => [
                            'label' => 'Ready for Pickup',
                            'short' => 'Ready',
                            'desc' => 'Quality inspection passed, ready for delivery',
                            'time' => $bookingRecord->ready_at,
                        ],
                        'completed' => [
                            'label' => 'Completed & Delivered',
                            'short' => 'Delivered',
                            'desc' => 'Invoiced & handed over to customer',
                            'time' => $bookingRecord->completed_at,
                        ],
                    ];
                    $stepKeys = array_keys($steps);
                    $currentIdx = array_search($statusVal, $stepKeys, true);
                    if ($currentIdx === false) {
                        $currentIdx = 0;
                    }
                    $progressPercent = min(100, max(8, ($currentIdx / (count($steps) - 1)) * 100));
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    {{-- ═══════════════════════════════════════════════════════════════
                         LEFT MAIN COLUMN: COCKPIT, MILESTONES & DIAGNOSTICS (8 COLS)
                         ═══════════════════════════════════════════════════════════════ --}}
                    <div class="lg:col-span-8 space-y-8">

                        {{-- ═══ 1. MOTORCYCLE JOB CARD MASTER BANNER ═══ --}}
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs relative overflow-hidden">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                                <div class="flex items-start gap-4">
                                    {{-- Motorcycle Icon Emblem --}}
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white flex items-center justify-center shrink-0 shadow-md shadow-slate-900/10">
                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19.5 8c-1.38 0-2.5 1.12-2.5 2.5 0 .28.05.55.13.8L14.7 13H10l-1.3-2.6A3.49 3.49 0 005.5 9C3.57 9 2 10.57 2 12.5S3.57 16 5.5 16c1.61 0 2.96-1.09 3.37-2.57L11.5 15h4.15l-1.15 2.3a2.5 2.5 0 102.24 1.12L18.4 15.1c.35.13.72.2 1.1.2 1.93 0 3.5-1.57 3.5-3.5S21.43 8 19.5 8zm-14 6c-.83 0-1.5-.67-1.5-1.5S4.67 11 5.5 11s1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm14-1.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                                        </svg>
                                    </div>

                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <h2 class="text-2xl font-black font-display text-slate-900 tracking-tight">
                                                {{ $bookingRecord->bike_make_model }}
                                            </h2>

                                            {{-- Automotive Stamped License Plate Badge --}}
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-100/90 border-2 border-amber-300 text-amber-950 font-mono font-black text-xs uppercase tracking-widest shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-800"></span>
                                                <span>{{ $bookingRecord->registration_number }}</span>
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-800"></span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3 text-xs text-slate-500 flex-wrap">
                                            <div class="inline-flex items-center gap-1.5 font-mono">
                                                <span>Ref:</span>
                                                <span class="font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-200/60">
                                                    #{{ $bookingRecord->booking_number }}
                                                </span>
                                                <button type="button"
                                                        @click="copyToClipboard('{{ $bookingRecord->booking_number }}')"
                                                        class="text-slate-400 hover:text-slate-700 transition"
                                                        title="Copy reference number">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                </button>
                                                <span x-show="copiedRef" x-cloak class="text-[10px] text-emerald-600 font-bold">Copied!</span>
                                            </div>

                                            <span>•</span>
                                            <span>Rider: <strong class="text-slate-700">{{ $bookingRecord->customer_name }}</strong></span>

                                            @if($bookingRecord->odometer_reading)
                                                <span>•</span>
                                                <span>Odo: <strong class="text-slate-700 font-mono">{{ number_format($bookingRecord->odometer_reading) }} KM</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Status Pill --}}
                                <div class="flex items-center gap-2 self-start sm:self-center">
                                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider border {{ $bookingRecord->status->badgeClasses() }} shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-current animate-pulse"></span>
                                        <span>{{ $bookingRecord->status->label() }}</span>
                                    </span>
                                </div>
                            </div>

                            {{-- ═══ 2. CONNECTED MILESTONE TELEMETRY PROGRESS ═══ --}}
                            <div class="pt-8">
                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full bg-[#FF6B00]"></div>
                                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-900">
                                            Telemetry Milestone Timeline
                                        </h3>
                                    </div>
                                    <span class="text-xs text-slate-500 font-medium">
                                        Stage {{ $currentIdx + 1 }} of {{ count($steps) }} Completed
                                    </span>
                                </div>

                                {{-- Desktop Horizontal Connected Stepper --}}
                                <div class="hidden md:block relative pt-2 pb-6">
                                    {{-- Connecting line background --}}
                                    <div class="absolute top-6 left-8 right-8 h-1 bg-slate-100 rounded-full"></div>
                                    {{-- Filled progress line --}}
                                    <div class="absolute top-6 left-8 h-1 bg-gradient-to-r from-emerald-500 via-amber-500 to-[#FF6B00] rounded-full transition-all duration-700"
                                         style="width: calc({{ $progressPercent }}% - 2rem)"></div>

                                    <div class="grid grid-cols-6 gap-2 relative z-10">
                                        @foreach ($steps as $key => $meta)
                                            @php
                                                $idx = array_search($key, $stepKeys, true);
                                                $isPast = $idx < $currentIdx;
                                                $isCurrent = $idx === $currentIdx;
                                                $isFuture = $idx > $currentIdx;
                                            @endphp
                                            <div class="flex flex-col items-center text-center group">
                                                {{-- Node circle --}}
                                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-xs transition-all shadow-xs
                                                    {{ $isPast ? 'bg-emerald-600 text-white' : ($isCurrent ? 'bg-gradient-to-br from-amber-400 via-[#FF6B00] to-orange-600 text-white ring-4 ring-orange-100 scale-110 shadow-md shadow-orange-500/20' : 'bg-white border-2 border-slate-200 text-slate-400') }}">
                                                    @if ($isPast)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    @else
                                                        {{ $idx + 1 }}
                                                    @endif
                                                </div>

                                                {{-- Label & Meta --}}
                                                <div class="mt-3 space-y-0.5">
                                                    <p class="text-xs font-bold {{ $isCurrent ? 'text-slate-900 font-black' : ($isPast ? 'text-slate-700' : 'text-slate-400') }}">
                                                        {{ $meta['short'] }}
                                                    </p>
                                                    @if ($isCurrent)
                                                        <span class="inline-block text-[9px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">
                                                            Active
                                                        </span>
                                                    @elseif ($meta['time'])
                                                        <span class="block text-[10px] text-slate-400 font-mono">
                                                            {{ $meta['time']->format('H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Mobile Vertical Stepper --}}
                                <div class="md:hidden space-y-4 pt-2">
                                    @foreach ($steps as $key => $meta)
                                        @php
                                            $idx = array_search($key, $stepKeys, true);
                                            $isPast = $idx < $currentIdx;
                                            $isCurrent = $idx === $currentIdx;
                                            $isFuture = $idx > $currentIdx;
                                        @endphp
                                        <div class="flex items-start gap-3.5">
                                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-xs font-bold transition
                                                {{ $isPast ? 'bg-emerald-600 text-white' : ($isCurrent ? 'bg-[#FF6B00] text-white ring-4 ring-orange-100 shadow-xs' : 'bg-slate-100 border border-slate-200 text-slate-400') }}">
                                                @if ($isPast)
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                @else
                                                    {{ $idx + 1 }}
                                                @endif
                                            </div>
                                            <div class="flex-1 pt-1">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-xs font-bold {{ $isCurrent ? 'text-slate-900' : ($isPast ? 'text-slate-700' : 'text-slate-400') }}">
                                                        {{ $meta['label'] }}
                                                    </p>
                                                    @if ($isCurrent)
                                                        <span class="text-[9px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">
                                                            Active Stage
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $meta['desc'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Active Telemetry Stage Callout Box --}}
                                <div class="mt-6 p-4 rounded-2xl bg-gradient-to-r from-orange-50/80 via-amber-50/50 to-white border border-orange-200/70 flex items-start sm:items-center gap-3.5 shadow-2xs">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-[#FF6B00] text-white flex items-center justify-center shrink-0 shadow-xs">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <div class="flex-1 text-xs">
                                        <p class="font-black text-slate-900 uppercase tracking-wide">
                                            Current Workshop Status: {{ $steps[$statusVal]['label'] ?? 'In Progress' }}
                                        </p>
                                        <p class="text-slate-600 mt-0.5">
                                            {{ $steps[$statusVal]['desc'] ?? 'The certified dealership technician is currently handling your motorcycle.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ═══ 3. 4-TILE LIVE WORKSHOP TELEMETRY STATS ═══ --}}
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
                                <div class="flex items-center justify-between text-slate-400 mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Service Package</span>
                                    <svg class="w-4 h-4 text-[#FF6B00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-900 line-clamp-1" title="{{ $bookingRecord->getServiceName() }}">
                                    {{ $bookingRecord->getServiceName() }}
                                </p>
                                <span class="text-[11px] text-slate-500 block">OEM Spec Service</span>
                            </div>

                            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
                                <div class="flex items-center justify-between text-slate-400 mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Scheduled Slot</span>
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-900">
                                    {{ $bookingRecord->booking_date->format('M d, Y') }}
                                </p>
                                <span class="text-[11px] text-slate-500 font-mono">{{ $bookingRecord->time_slot }}</span>
                            </div>

                            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
                                <div class="flex items-center justify-between text-slate-400 mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Workshop Bay</span>
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <p class="text-sm font-bold text-emerald-600">
                                    {{ $bookingRecord->bay_number ? 'Bay #' . $bookingRecord->bay_number : 'Assigned on arrival' }}
                                </p>
                                <span class="text-[11px] text-slate-500">Express Pit Area</span>
                            </div>

                            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-1">
                                <div class="flex items-center justify-between text-slate-400 mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Estimated Cost</span>
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-base font-black text-slate-900 font-display">
                                    ₹{{ number_format($bookingRecord->final_cost ?? $bookingRecord->estimated_cost) }}
                                </p>
                                <span class="text-[11px] text-slate-500">{{ $bookingRecord->final_cost ? 'Final Invoiced' : 'Initial Estimate' }}</span>
                            </div>
                        </div>

                        {{-- ═══ 4. DIAGNOSTIC SCOPE & TECHNICIAN NOTES ═══ --}}
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-900">
                                        Workshop Diagnostic & Inspection Scope
                                    </h3>
                                </div>
                                <span class="text-xs text-slate-400 font-mono">Job Card Telemetry</span>
                            </div>

                            @if ($bookingRecord->technician_notes)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                            <span>🔧</span>
                                            <span>Lead Technician Inspection Report</span>
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-mono">Verified Diagnostics</span>
                                    </div>
                                    <p class="text-xs text-slate-700 leading-relaxed font-mono bg-white p-3 rounded-xl border border-slate-200/80">
                                        {{ $bookingRecord->technician_notes }}
                                    </p>
                                </div>
                            @endif

                            @if ($bookingRecord->customer_notes)
                                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/70 space-y-1">
                                    <span class="text-xs font-bold text-amber-900 uppercase tracking-wider block">
                                        Customer Concern / Instructions Provided at Booking:
                                    </span>
                                    <p class="text-xs text-amber-800 leading-relaxed italic">
                                        "{{ $bookingRecord->customer_notes }}"
                                    </p>
                                </div>
                            @endif

                            {{-- 4-Point Standard Dealership Quality Checklist --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="font-semibold">100% Genuine Factory OEM Parts</span>
                                </div>

                                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="font-semibold">Electronic Diagnostics & ECU Scan</span>
                                </div>

                                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="font-semibold">Multi-Point Brake & Chain Lube</span>
                                </div>

                                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="font-semibold">Road Test & Final Quality Signoff</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- ═══════════════════════════════════════════════════════════════
                         RIGHT SIDEBAR: GAMIFIED REWARD, INVOICE & ADVISOR (4 COLS)
                         ═══════════════════════════════════════════════════════════════ --}}
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

                        {{-- ═══ 1. SCRATCH & WIN REWARD CARD ═══ --}}
                        @if (isset($scratchParticipation) && $scratchParticipation)
                            <div class="rounded-3xl bg-gradient-to-br from-purple-50 via-white to-pink-50 border-2 border-purple-200/80 p-6 shadow-sm relative overflow-hidden">
                                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>

                                <div class="space-y-4 relative z-10">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-100 text-purple-700 border border-purple-200">
                                            🎁 Appointment Reward
                                        </span>
                                        @if($scratchParticipation->is_scratched)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Revealed</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse">Unscratched</span>
                                        @endif
                                    </div>

                                    <div class="flex items-start gap-3.5">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-600 via-indigo-600 to-rose-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-purple-500/25">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                        </div>

                                        <div>
                                            <h4 class="text-base font-black font-display text-slate-900 leading-tight">
                                                @if($scratchParticipation->is_scratched)
                                                    {{ $scratchParticipation->prize?->title ?? 'Showroom Voucher' }}
                                                @else
                                                    Scratch & Win Reward
                                                @endif
                                            </h4>
                                            <p class="text-xs text-slate-600 mt-1">
                                                @if($scratchParticipation->is_scratched)
                                                    @if($scratchParticipation->coupon_code)
                                                        Coupon code: <strong class="font-mono text-purple-700">{{ $scratchParticipation->coupon_code }}</strong>
                                                    @else
                                                        Present this booking at the service counter to redeem.
                                                    @endif
                                                @else
                                                    You unlocked a dealership scratch card for this booking! Reveal discounts or free perks.
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <a href="{{ route('showrooms.scratch.play', [$showroom, $scratchParticipation]) }}"
                                       class="w-full text-center px-5 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-purple-600/20 transition flex items-center justify-center gap-2">
                                        @if($scratchParticipation->is_scratched)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>View Scratch Voucher</span>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Scratch Card Now</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- ═══ 2. BILLING & INVOICE SETTLEMENT CARD ═══ --}}
                        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-5">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Billing & Payment Status</span>
                                </h3>
                                <span class="text-[10px] text-slate-400 font-mono">Invoice Summary</span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-500">Service Fee / Package</span>
                                    <span class="font-bold text-slate-800">₹{{ number_format($bookingRecord->estimated_cost) }}</span>
                                </div>

                                @if($bookingRecord->final_cost && $bookingRecord->final_cost != $bookingRecord->estimated_cost)
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-500">Workshop Adjustments</span>
                                        <span class="font-bold text-slate-800">₹{{ number_format($bookingRecord->final_cost - $bookingRecord->estimated_cost) }}</span>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                                    <div>
                                        <span class="text-xs font-black text-slate-900 uppercase">Total Amount</span>
                                        <span class="block text-[10px] text-slate-400">All Workshop Taxes Included</span>
                                    </div>
                                    <span class="text-2xl font-black font-display text-emerald-600">
                                        ₹{{ number_format($bookingRecord->final_cost ?? $bookingRecord->estimated_cost) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Payment Action Box --}}
                            @if ($bookingRecord->payment_status->value !== 'captured')
                                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 space-y-3">
                                    <div class="flex items-start gap-2.5">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse mt-1 shrink-0"></span>
                                        <div class="text-xs">
                                            <p class="font-bold text-amber-900">Payment Pending</p>
                                            <p class="text-amber-700 mt-0.5 text-[11px] leading-relaxed">
                                                Pay securely online via Razorpay or settle at the workshop reception upon delivery.
                                            </p>
                                        </div>
                                    </div>

                                    @if(isset($payment) && $payment)
                                        <div class="space-y-2 pt-1">
                                            <button type="button"
                                                    id="btn-rzp-pay-service"
                                                    onclick="launchTrackerRazorpay()"
                                                    class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                                <span>Pay ₹{{ number_format($payment->amount) }} via Razorpay</span>
                                            </button>

                                            {{-- Sandbox Instant Simulation Form --}}
                                            <form method="POST" action="{{ route('showrooms.payments.verify', $showroom) }}" id="service-payment-verify-form">
                                                @csrf
                                                <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                                <input type="hidden" name="razorpay_order_id" value="{{ $payment->razorpay_order_id }}">
                                                <input type="hidden" name="razorpay_payment_id" value="{{ $simulatedPaymentId }}" id="service_rzp_payment_id">
                                                <input type="hidden" name="razorpay_signature" value="{{ $simulatedSignature }}" id="service_rzp_signature">
                                                <input type="hidden" name="method" value="upi">
                                                <input type="hidden" name="terms_accepted" value="1">

                                                <button type="submit"
                                                        class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition flex items-center justify-center gap-1.5 cursor-pointer">
                                                    <span>⚡ Simulate Instant Payment (Sandbox)</span>
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    <p class="text-[11px] text-slate-500 text-center pt-1 border-t border-amber-200/60">
                                        You can also pay cash/card at the workshop bay upon delivery.
                                    </p>
                                </div>
                            @else
                                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-1.5 text-xs text-emerald-800">
                                    <div class="flex items-center gap-2 font-bold text-emerald-900">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Payment Settled & Verified</span>
                                    </div>
                                    <p class="text-[11px] text-emerald-700 leading-relaxed">
                                        Transaction captured via Razorpay secured gateway. Terms & Conditions accepted.
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- ═══ 3. WORKSHOP ADVISOR & DEALERSHIP SUPPORT ═══ --}}
                        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#FF6B00]"></span>
                                    <span>Pitstop Support</span>
                                </h3>
                                <span class="text-[10px] text-slate-400">Direct Desk</span>
                            </div>

                            <p class="text-xs text-slate-600 leading-relaxed">
                                Need to modify your appointment, add additional repair requests, or inquire about collection timing?
                            </p>

                            <div class="space-y-2.5">
                                @if ($showroom->phone)
                                    <a href="tel:{{ $showroom->phone }}"
                                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold transition flex items-center justify-center gap-2 shadow-2xs">
                                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span>Call Workshop: {{ $showroom->phone }}</span>
                                    </a>
                                @endif

                                @if ($showroom->email)
                                    <a href="mailto:{{ $showroom->email }}"
                                       class="w-full px-4 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 text-xs font-medium transition flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span>{{ $showroom->email }}</span>
                                    </a>
                                @endif
                            </div>

                            <div class="pt-3 border-t border-slate-100 text-[11px] text-slate-500 space-y-1">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-slate-700">Workshop Timings:</span>
                                    <span>Mon – Sat: 9:30 AM – 8:00 PM</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-slate-700">Sunday Service:</span>
                                    <span>10:00 AM – 4:00 PM</span>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            {{-- ═══ NOT FOUND STATE ═══ --}}
            @elseif ($searched)
                <div class="bg-white rounded-3xl p-10 sm:p-14 border border-slate-200 text-center space-y-5 shadow-xs max-w-2xl mx-auto">
                    <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-100 shadow-xs">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-xl font-black font-display text-slate-900">
                            No Service Booking Found
                        </h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                            We could not find an appointment matching "<strong class="font-mono text-slate-900">{{ $searchRef }}</strong>" under {{ $showroom->name }}. Please verify your reference number or vehicle registration.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                        <a href="{{ route('showrooms.services.track', $showroom) }}"
                           class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                            <span>Try Another Reference</span>
                        </a>

                        <a href="{{ route('showrooms.services.create', $showroom) }}"
                           class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-xs">
                            <span>Book an Appointment</span>
                        </a>
                    </div>
                </div>

            {{-- ═══ INITIAL LANDING STATE ═══ --}}
            @else
                <div class="bg-white rounded-3xl p-10 sm:p-16 border border-slate-200 text-center space-y-6 shadow-xs max-w-3xl mx-auto">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 via-[#FF6B00] to-orange-600 text-white flex items-center justify-center mx-auto shadow-md shadow-orange-500/20">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-xl sm:text-2xl font-black font-display text-slate-900">
                            Track Your Motorcycle Service & Repairs
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto leading-relaxed">
                            Enter the Service Booking Reference received via SMS or email (e.g. <span class="font-mono text-slate-800 font-bold">SRV-20261019-XXXX</span>) or your Motorcycle License Plate Number to access real-time diagnostic progress.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 text-left">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                            <span class="text-xs font-bold text-slate-900 block flex items-center gap-1.5">
                                <span>⚡</span>
                                <span>Live Bay Milestones</span>
                            </span>
                            <span class="text-[11px] text-slate-500 block leading-relaxed">
                                Monitor step-by-step progress from bike check-in through dyno testing.
                            </span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                            <span class="text-xs font-bold text-slate-900 block flex items-center gap-1.5">
                                <span>🔧</span>
                                <span>Technician Notes</span>
                            </span>
                            <span class="text-[11px] text-slate-500 block leading-relaxed">
                                View certified mechanic observations, torque tests, and parts used.
                            </span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                            <span class="text-xs font-bold text-slate-900 block flex items-center gap-1.5">
                                <span>💳</span>
                                <span>Instant Online Pay</span>
                            </span>
                            <span class="text-[11px] text-slate-500 block leading-relaxed">
                                Settle invoices seamlessly with Razorpay or pay at the pit counter.
                            </span>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    @if(isset($payment) && $payment)
        {{-- Direct Modal Razorpay Script --}}
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
        function launchTrackerRazorpay() {
            const options = {
                key: "{{ $keyId }}",
                amount: {{ $payment->amount * 100 }},
                currency: "{{ $payment->currency }}",
                name: "{{ addslashes($showroom->name) }}",
                description: "Service Invoice #{{ $bookingRecord->booking_number }} - {{ addslashes($bookingRecord->bike_make_model) }}",
                order_id: "{{ $payment->razorpay_order_id }}",
                prefill: {
                    name: "{{ addslashes($bookingRecord->customer_name) }}",
                    email: "{{ addslashes($bookingRecord->customer_email) }}",
                    contact: "{{ addslashes($bookingRecord->customer_phone) }}"
                },
                theme: {
                    color: "#059669"
                },
                handler: function (response) {
                    document.getElementById('service_rzp_payment_id').value = response.razorpay_payment_id;
                    document.getElementById('service_rzp_signature').value = response.razorpay_signature;
                    document.getElementById('service-payment-verify-form').submit();
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();
        }
        </script>
    @endif

    {{-- Motorcycle Showroom Footer --}}
    @include('showrooms.partials.footer')
</x-app-layout>
