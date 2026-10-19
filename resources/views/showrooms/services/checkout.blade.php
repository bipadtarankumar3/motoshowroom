<x-app-layout :title="'Service Invoice Payment - ' . $booking->booking_number . ' - ' . $showroom->name">
    {{-- Branded Dealership Header --}}
    @include('showrooms.partials.header')

    <div class="min-h-screen bg-slate-50/70 text-slate-900 py-8 px-4 sm:px-6 lg:px-8 xl:px-12"
         x-data="{
             termsAccepted: false,
             termsModalOpen: false,
             shakeWarning: false,
             copiedRef: false,
             requireTerms(action) {
                 if (!this.termsAccepted) {
                     this.shakeWarning = true;
                     setTimeout(() => this.shakeWarning = false, 1500);
                     return false;
                 }
                 return true;
             },
             copyToClipboard(text) {
                 navigator.clipboard.writeText(text);
                 this.copiedRef = true;
                 setTimeout(() => this.copiedRef = false, 2000);
             }
         }">
        <div class="w-full max-w-[1600px] mx-auto space-y-8">

            {{-- ═══ TOP BREADCRUMBS & CHECKOUT PROGRESS FUNNEL ═══ --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 text-xs">
                <div class="flex items-center gap-2 text-slate-500 flex-wrap">
                    <a href="{{ route('showrooms.show', $showroom) }}" class="inline-flex items-center gap-1.5 hover:text-slate-900 font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>{{ $showroom->name }}</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('showrooms.services.track', [$showroom, 'booking' => $booking->booking_number]) }}" class="hover:text-slate-900 transition">
                        Job Card #{{ $booking->booking_number }}
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="font-bold text-slate-800">Secure Payment Checkout</span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-200 font-bold text-xs shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Razorpay PCI-DSS Level 1 Encrypted</span>
                    </span>

                    <a href="{{ route('showrooms.services.track', [$showroom, 'booking' => $booking->booking_number]) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 font-bold transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Return to Tracker</span>
                    </a>
                </div>
            </div>

            {{-- ═══ HERO CHECKOUT HEADER ═══ --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-slate-200 shadow-xs relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-gradient-to-br from-emerald-500/10 via-teal-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute left-1/3 -bottom-20 w-64 h-64 bg-rose-500/5 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-3 max-w-2xl">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                💳 Workshop Service Advance Invoice
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Ref #{{ $booking->booking_number }}
                            </span>
                        </div>

                        <h1 class="text-2xl sm:text-4xl font-black font-display text-slate-900 tracking-tight leading-tight">
                            Complete Service Payment
                        </h1>

                        <p class="text-sm text-slate-600 leading-relaxed">
                            Confirm your appointment slot and authorize payment through our certified payment gateway. Your official invoice and bay reservation will be locked immediately.
                        </p>
                    </div>

                    {{-- Quick Total Amount Pill --}}
                    <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200/90 shrink-0 text-left sm:text-right shadow-2xs space-y-1">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Total Amount Due</span>
                        <p class="text-3xl sm:text-4xl font-black font-display text-emerald-600 leading-none">
                            ₹{{ number_format($payment->amount) }}
                        </p>
                        <span class="text-[11px] text-slate-400 font-mono uppercase block pt-1">
                            {{ $payment->currency }} • GST & Workshop Fees Included
                        </span>
                    </div>
                </div>
            </div>

            {{-- ═══ SESSION ALERTS ═══ --}}
            @if (session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm flex items-center gap-3 shadow-2xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center gap-3 shadow-2xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- ═══ 2-COLUMN COCKPIT LAYOUT ═══ --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- ═══════════════════════════════════════════════════════════════
                     LEFT COLUMN: ORDER DETAILS, VEHICLE & ASSURANCES (7 COLS)
                     ═══════════════════════════════════════════════════════════════ --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- ═══ CARD 1: VEHICLE & APPOINTMENT OVERVIEW ═══ --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#FF6B00]"></span>
                                <span>Vehicle & Booking Specifications</span>
                            </h3>
                            <span class="text-xs text-slate-400 font-mono">Job Card Summary</span>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center shrink-0 shadow-xs">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19.5 8c-1.38 0-2.5 1.12-2.5 2.5 0 .28.05.55.13.8L14.7 13H10l-1.3-2.6A3.49 3.49 0 005.5 9C3.57 9 2 10.57 2 12.5S3.57 16 5.5 16c1.61 0 2.96-1.09 3.37-2.57L11.5 15h4.15l-1.15 2.3a2.5 2.5 0 102.24 1.12L18.4 15.1c.35.13.72.2 1.1.2 1.93 0 3.5-1.57 3.5-3.5S21.43 8 19.5 8zm-14 6c-.83 0-1.5-.67-1.5-1.5S4.67 11 5.5 11s1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm14-1.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-black font-display text-slate-900">
                                        {{ $booking->bike_make_model }}
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Rider: <strong class="text-slate-800">{{ $booking->customer_name }}</strong>
                                    </p>
                                </div>
                            </div>

                            {{-- License Plate --}}
                            <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-amber-100 border-2 border-amber-300 text-amber-950 font-mono font-black text-xs uppercase tracking-widest shadow-2xs self-start sm:self-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-800"></span>
                                <span>{{ $booking->registration_number }}</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-800"></span>
                            </div>
                        </div>

                        {{-- Metadata Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-slate-400 text-[11px] font-medium block">Service Package</span>
                                <span class="font-bold text-slate-900 text-sm block">{{ $booking->getServiceName() }}</span>
                                <span class="text-[10px] text-emerald-600 font-semibold block">Certified OEM Protocol</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-slate-400 text-[11px] font-medium block">Scheduled Pit Slot</span>
                                <span class="font-bold text-slate-900 text-sm block">{{ $booking->booking_date->format('M d, Y') }}</span>
                                <span class="text-[10px] text-slate-600 font-mono block">{{ $booking->time_slot }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-slate-400 text-[11px] font-medium block">Rider Contact Phone</span>
                                <span class="font-bold text-slate-900 text-sm font-mono block">{{ $booking->customer_phone }}</span>
                                <span class="text-[10px] text-slate-500 block">Instant SMS notifications</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-slate-400 text-[11px] font-medium block">Dealership Center</span>
                                <span class="font-bold text-slate-900 text-sm block">{{ $showroom->name }}</span>
                                <span class="text-[10px] text-slate-500 block">{{ $showroom->city }}, {{ $showroom->state }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- ═══ CARD 2: ITEMIZED INVOICE BREAKDOWN ═══ --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-5">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span>Itemized Workshop Invoice</span>
                            </h3>
                            <span class="text-xs text-slate-400 font-mono">Tax Invoice #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <div>
                                    <p class="font-bold text-slate-900">{{ $booking->getServiceName() }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Comprehensive workshop maintenance & factory fluids inspection</p>
                                </div>
                                <span class="font-bold text-slate-900 text-sm">₹{{ number_format($payment->amount) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-100 text-slate-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Dedicated Workshop Bay & Tooling Allocation</span>
                                </div>
                                <span class="font-mono text-emerald-700 font-bold uppercase text-[11px]">Included</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-100 text-slate-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Electronic ECU Diagnostic Scan & Dyno Check</span>
                                </div>
                                <span class="font-mono text-emerald-700 font-bold uppercase text-[11px]">Included</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-100 text-slate-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>GST (18%) & Environmental Waste Disposal</span>
                                </div>
                                <span class="font-mono text-slate-600 text-[11px]">All Taxes Included</span>
                            </div>

                            {{-- Total Due Banner --}}
                            <div class="flex justify-between items-center pt-3 text-slate-900">
                                <div>
                                    <p class="text-sm font-black uppercase tracking-wider">Total Advance Payable</p>
                                    <p class="text-[11px] text-slate-500">Official receipt generated immediately upon payment</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl sm:text-3xl font-black font-display text-emerald-600">
                                        ₹{{ number_format($payment->amount) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ═══ CARD 3: DEALERSHIP ASSURANCES ═══ --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-2">
                            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900">100% Genuine OEM Spares</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Factory-certified parts and fluids sourced through authorized brand distribution.
                            </p>
                        </div>

                        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-2">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900">2-Hour Free Cancellation</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Modify or cancel up to 2 hours before your scheduled bay time with 100% instant refund.
                            </p>
                        </div>

                        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-2">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900">6-Month Service Warranty</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Workshop repair craftsmanship and component replacements are guaranteed for 6 months.
                            </p>
                        </div>

                        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-2">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-[#FF6B00] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900">Live Pit Telemetry</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Real-time digital milestone tracking from check-in to quality assurance inspection.
                            </p>
                        </div>
                    </div>

                </div>

                {{-- ═══════════════════════════════════════════════════════════════
                     RIGHT COLUMN: STICKY PAYMENT AUTHORIZATION COCKPIT (5 COLS)
                     ═══════════════════════════════════════════════════════════════ --}}
                <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">

                    {{-- ═══ MAIN PAYMENT AUTHORIZATION CARD ═══ --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-md shadow-slate-900/5 space-y-6">

                        {{-- Card Header --}}
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-900">
                                    Payment Authorization
                                </h3>
                            </div>
                            <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 font-bold uppercase tracking-wider">
                                SSL 256-Bit
                            </span>
                        </div>

                        {{-- Total Summary Box --}}
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between shadow-xs">
                            <div>
                                <span class="text-[11px] text-slate-400 font-medium block">Total Payable Now</span>
                                <span class="text-xs text-slate-300">Advance Bay Booking</span>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black font-display text-white">
                                    ₹{{ number_format($payment->amount) }}
                                </p>
                                <span class="text-[10px] text-emerald-400 font-mono">Taxes Included</span>
                            </div>
                        </div>

                        {{-- ═══ TERMS & CONDITIONS MANDATORY AGREEMENT ═══ --}}
                        <div class="p-4 rounded-2xl border transition-all duration-300"
                             :class="shakeWarning ? 'bg-rose-50 border-rose-300 ring-2 ring-rose-300 animate-bounce' : (termsAccepted ? 'bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-200' : 'bg-slate-50 border-slate-200')">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox"
                                       x-model="termsAccepted"
                                       id="terms-accept-checkbox"
                                       class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 mt-1 cursor-pointer">
                                <div class="text-xs text-slate-700 leading-relaxed">
                                    <span class="font-bold text-slate-900 block text-xs">
                                        I Accept Workshop Terms & Conditions
                                    </span>
                                    <span class="text-[11px] text-slate-500 block mt-0.5">
                                        I confirm this appointment and agree to the workshop repair guidelines, parts warranty, and 2-hour cancellation policy.
                                    </span>
                                    <button type="button"
                                            @click="termsModalOpen = true"
                                            class="text-emerald-700 hover:text-emerald-800 font-bold underline text-[11px] mt-1.5 inline-flex items-center gap-1">
                                        <span>Read Full Policies & Refund Rules</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </button>
                                </div>
                            </label>
                        </div>

                        {{-- Terms Warning Notice if Unchecked --}}
                        <div x-cloak x-show="!termsAccepted"
                             class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-[11px] flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Please accept terms & conditions above to enable payment checkout.</span>
                        </div>

                        {{-- ═══ RAZORPAY LIVE CHECKOUT BUTTON ═══ --}}
                        <button type="button"
                                id="btn-rzp-pay-service"
                                @click="if(!requireTerms('razorpay')) return; launchRazorpay();"
                                :class="termsAccepted ? 'bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 text-white cursor-pointer shadow-lg shadow-emerald-600/25 scale-[1.01]' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                class="w-full py-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Pay ₹{{ number_format($payment->amount) }} via Razorpay</span>
                        </button>

                        {{-- Supported Payment Methods Strip --}}
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block text-center">
                                Accepted Payment Methods
                            </span>
                            <div class="flex items-center justify-center gap-2 text-[11px] text-slate-500 flex-wrap">
                                <span class="px-2 py-1 rounded bg-slate-100 border border-slate-200/80 font-semibold">UPI (GPay / PhonePe)</span>
                                <span class="px-2 py-1 rounded bg-slate-100 border border-slate-200/80 font-semibold">Cards (Visa / MC / RuPay)</span>
                                <span class="px-2 py-1 rounded bg-slate-100 border border-slate-200/80 font-semibold">NetBanking</span>
                            </div>
                        </div>

                        {{-- ═══ SANDBOX TESTING SIMULATOR ═══ --}}
                        <div class="pt-2">
                            <div class="relative flex items-center justify-center py-2">
                                <div class="border-t border-slate-200 w-full"></div>
                                <span class="bg-white px-3 text-[10px] text-slate-400 uppercase tracking-widest absolute">or Sandbox Simulation</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 mt-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-800 flex items-center gap-1.5">
                                        <span>⚙️</span>
                                        <span>Development Sandbox</span>
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800">
                                        HMAC-SHA256
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    Simulate immediate payment capture and invoice verification using authorized testing keys.
                                </p>

                                <form method="POST" action="{{ route('showrooms.payments.verify', $showroom) }}" id="service-payment-verify-form"
                                      @submit.prevent="if(!requireTerms('simulate')) return; $el.submit();">
                                    @csrf
                                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                    <input type="hidden" name="razorpay_order_id" value="{{ $payment->razorpay_order_id }}">
                                    <input type="hidden" name="razorpay_payment_id" value="{{ $simulatedPaymentId }}" id="service_rzp_payment_id">
                                    <input type="hidden" name="razorpay_signature" value="{{ $simulatedSignature }}" id="service_rzp_signature">
                                    <input type="hidden" name="method" value="upi">
                                    <input type="hidden" name="terms_accepted" value="1">

                                    <button type="submit"
                                            id="btn-simulate-service-payment"
                                            :class="termsAccepted ? 'bg-slate-900 hover:bg-slate-800 text-white cursor-pointer shadow-xs' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                            class="w-full py-2.5 rounded-xl font-bold text-xs transition flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Simulate Successful Payment</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Order Identifiers Footer --}}
                        <div class="pt-2 text-[10px] text-slate-400 flex items-center justify-between border-t border-slate-100">
                            <span>Gateway: <strong class="text-emerald-700 font-mono">Razorpay Active</strong></span>
                            <span>Order: <span class="font-mono text-slate-600">{{ $payment->razorpay_order_id }}</span></span>
                        </div>

                    </div>

                    {{-- ═══ DIRECT ASSISTANCE WIDGET ═══ --}}
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-2 text-xs">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#FF6B00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span class="font-bold text-slate-900">Need Help Completing Checkout?</span>
                        </div>
                        <p class="text-slate-500 leading-relaxed text-[11px]">
                            Our dealership service advisor is available to answer any questions regarding warranty coverage or slot rescheduling.
                        </p>
                        @if ($showroom->phone)
                            <a href="tel:{{ $showroom->phone }}" class="text-rose-600 hover:text-rose-700 font-bold inline-flex items-center gap-1 text-xs pt-1">
                                <span>Call Workshop: {{ $showroom->phone }}</span>
                                <span>→</span>
                            </a>
                        @endif
                    </div>

                </div>

            </div>

        </div>

        {{-- ═══ TERMS & CONDITIONS MODAL (LIGHT THEME) ═══ --}}
        <div x-cloak x-show="termsModalOpen"
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="termsModalOpen = false"></div>
            <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 text-left shadow-2xl border border-slate-200 my-8 overflow-hidden"
                     @click.away="termsModalOpen = false">

                    <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                Payment & Service Policies
                            </span>
                            <h3 class="text-xl font-black font-display text-slate-900 mt-1">Workshop Terms & Conditions</h3>
                            <p class="text-xs text-slate-500">{{ $showroom->name }} Workshop & Service Center</p>
                        </div>
                        <button type="button" @click="termsModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="py-5 space-y-4 max-h-[60vh] overflow-y-auto text-xs text-slate-600 leading-relaxed pr-2">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <h4 class="font-bold text-slate-900 text-sm">1. Advance Payment & Invoice Confirmation</h4>
                            <p>
                                Online payments are safely processed through Razorpay PCI-DSS certified gateway. Once captured, your appointment bay slot is permanently held and a digital invoice receipt is generated.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <h4 class="font-bold text-slate-900 text-sm">2. Free Cancellation & Refund Window</h4>
                            <p>
                                You may cancel or reschedule your service booking at any point up to 2 hours prior to your scheduled bay appointment with a 100% full instant refund to your original payment method.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <h4 class="font-bold text-slate-900 text-sm">3. Genuine OEM Parts & Workmanship Warranty</h4>
                            <p>
                                All maintenance is executed using certified diagnostic equipment and genuine OEM replacement parts. Servicing includes a complimentary 6-month / 5,000 km repair warranty.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <h4 class="font-bold text-slate-900 text-sm">4. Road Testing & Vehicle Safety</h4>
                            <p>
                                You authorize certified dealership technicians to conduct quality control road tests under dealership garage-keeper insurance coverage to ensure vehicle reliability.
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <button type="button" @click="termsAccepted = true; termsModalOpen = false"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs">
                            I Understand & Accept Terms
                        </button>
                        <button type="button" @click="termsModalOpen = false"
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Razorpay Checkout Client Script --}}
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
    function launchRazorpay() {
        const options = {
            key: "{{ $keyId }}",
            amount: {{ $payment->amount * 100 }},
            currency: "{{ $payment->currency }}",
            name: "{{ addslashes($showroom->name) }}",
            description: "Service Invoice #{{ $booking->booking_number }} - {{ addslashes($booking->bike_make_model) }}",
            order_id: "{{ $payment->razorpay_order_id }}",
            prefill: {
                name: "{{ addslashes($booking->customer_name) }}",
                email: "{{ addslashes($booking->customer_email) }}",
                contact: "{{ addslashes($booking->customer_phone) }}"
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

    {{-- Motorcycle Showroom Footer --}}
    @include('showrooms.partials.footer')
</x-app-layout>
