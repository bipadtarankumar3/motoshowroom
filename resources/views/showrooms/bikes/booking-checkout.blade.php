<x-app-layout :title="'Motorcycle Reservation Payment - ' . $enquiry->enquiry_number . ' - ' . $showroom->name">
    {{-- Branded Dealership Header --}}
    @include('showrooms.partials.header')

    <div class="min-h-screen bg-slate-50/70 text-slate-900 py-8 px-4 sm:px-6 lg:px-8 xl:px-12"
         x-data="{
             termsAccepted: true,
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
        <div class="w-full max-w-[1400px] mx-auto space-y-8">

            {{-- ═══ TOP BREADCRUMBS & CHECKOUT PROGRESS FUNNEL ═══ --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 text-xs">
                <div class="flex items-center gap-2 text-slate-500 flex-wrap">
                    <a href="{{ route('showrooms.show', $showroom) }}" class="inline-flex items-center gap-1.5 hover:text-slate-900 font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>{{ $showroom->name }}</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    @if($enquiry->bike)
                        <a href="{{ route('showrooms.bikes.show', [$showroom, $enquiry->bike]) }}" class="hover:text-slate-900 transition">
                            {{ $enquiry->bike->name }}
                        </a>
                        <span class="text-slate-300">/</span>
                    @endif
                    <span class="font-bold text-slate-800">Booking Token Checkout</span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-200 font-bold text-xs shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Razorpay 256-Bit SSL Encrypted</span>
                    </span>

                    @if($enquiry->bike)
                        <a href="{{ route('showrooms.bikes.show', [$showroom, $enquiry->bike]) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 font-bold transition shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Return to Motorcycle</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- ═══ HERO CHECKOUT HEADER ═══ --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-slate-200 shadow-xs relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-gradient-to-br from-emerald-500/10 via-teal-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-3 max-w-2xl">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                🏍️ Priority Vehicle Chassis Reservation
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                Reservation #{{ $enquiry->enquiry_number }}
                            </span>
                        </div>

                        <h1 class="text-2xl sm:text-4xl font-black font-display text-slate-900 tracking-tight leading-tight">
                            Pay Vehicle Booking Advance
                        </h1>

                        <p class="text-sm text-slate-600 leading-relaxed">
                            Complete your 100% refundable booking token of ₹{{ number_format($payment->amount) }} via Razorpay to lock your priority chassis allocation for {{ $enquiry->bike?->name ?? 'your chosen motorcycle' }}.
                        </p>
                    </div>

                    {{-- Quick Total Amount Pill --}}
                    <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 shrink-0 text-left sm:text-right shadow-2xs space-y-1">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Refundable Advance Token</span>
                        <p class="text-3xl sm:text-4xl font-black font-display text-emerald-600 leading-none">
                            ₹{{ number_format($payment->amount) }}
                        </p>
                        <span class="text-[11px] text-slate-400 font-mono uppercase block pt-1">
                            {{ $payment->currency }} • 100% Refundable Guarantee
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

            {{-- ═══ MAIN 2-COLUMN CHECKOUT GRID ═══ --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- LEFT COLUMN: ORDER DETAILS & MOTORCYCLE SUMMARY --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Machine Card --}}
                    @if($enquiry->bike)
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-5">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span>Selected Motorcycle Allocation</span>
                                </h3>
                                <span class="text-xs font-mono text-slate-400">SKU: {{ $enquiry->bike->sku }}</span>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-5">
                                <div class="w-full sm:w-44 aspect-[16/10] bg-slate-100 rounded-2xl overflow-hidden shrink-0 border border-slate-200">
                                    <img src="{{ $enquiry->bike->primary_image_url }}" alt="{{ $enquiry->bike->name }}"
                                         class="w-full h-full object-cover">
                                </div>
                                <div class="space-y-1.5 text-center sm:text-left flex-1">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ $enquiry->bike->brand }}
                                    </span>
                                    <h4 class="text-lg font-black font-display text-slate-900">{{ $enquiry->bike->name }}</h4>
                                    <p class="text-xs font-mono font-bold text-slate-700">Ex-Showroom: {{ $enquiry->bike->formatted_price }}</p>
                                    <p class="text-[11px] text-slate-500 font-medium">Dealership: {{ $showroom->name }} ({{ $showroom->city }})</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-slate-100 text-xs">
                                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Engine</span>
                                    <span class="font-bold text-slate-800">{{ $enquiry->bike->displacement_cc ?? 'Performance' }} cc</span>
                                </div>
                                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Power</span>
                                    <span class="font-bold text-slate-800">{{ $enquiry->bike->power_bhp ? $enquiry->bike->power_bhp . ' bhp' : 'N/A' }}</span>
                                </div>
                                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Delivery Target</span>
                                    <span class="font-bold text-slate-800">{{ $enquiry->preferred_date ? $enquiry->preferred_date->format('d M, Y') : 'Within 7 Days' }}</span>
                                </div>
                                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Status</span>
                                    <span class="font-bold text-emerald-600">Priority Reserved</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Customer & Reservation Details --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-xs font-black uppercase tracking-widest text-slate-900">
                                Customer Contact & Preferences
                            </h3>
                            <button type="button" @click="copyToClipboard('{{ $enquiry->enquiry_number }}')"
                                    class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition">
                                <span x-text="copiedRef ? 'Copied!' : 'Copy Reference'"></span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">Reserved In Name Of</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $enquiry->customer_name }}</span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">Phone Number</span>
                                <span class="font-bold text-slate-900 font-mono text-sm mt-0.5 block">{{ $enquiry->customer_phone }}</span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">Email Address</span>
                                <span class="font-bold text-slate-900 mt-0.5 block truncate">{{ $enquiry->customer_email }}</span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">Notes / Customization</span>
                                <span class="font-medium text-slate-700 mt-0.5 block line-clamp-2">{{ $enquiry->customer_notes ?: 'No special customization requested.' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 100% Refundable Guarantee Card --}}
                    <div class="p-5 rounded-2xl bg-emerald-50/80 border border-emerald-200 text-emerald-900 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black text-xs shrink-0">✓</span>
                            <span class="font-bold text-xs">100% Refundable Advance Guarantee</span>
                        </div>
                        <p class="text-xs text-emerald-800 leading-relaxed pl-8">
                            This advance payment of ₹{{ number_format($payment->amount) }} reserves your motorcycle chassis immediately. You may inspect the bike at the dealership upon arrival, test ride, and should you choose not to proceed before final invoicing, you will receive an unconditional 100% refund.
                        </p>
                    </div>

                </div>

                {{-- RIGHT COLUMN: RAZORPAY CHECKOUT ACTION CARD --}}
                <div class="lg:col-span-5 space-y-6">

                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <span class="text-[10px] uppercase font-black text-slate-400 tracking-wider">Gateway Authorization</span>
                                <h3 class="text-base font-black font-display text-slate-900">Razorpay Checkout</h3>
                            </div>
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                        </div>

                        {{-- Order Summary --}}
                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between py-1 text-slate-600">
                                <span>Vehicle Booking Advance</span>
                                <span class="font-mono font-bold text-slate-900">₹{{ number_format($payment->amount) }}</span>
                            </div>
                            <div class="flex justify-between py-1 text-slate-600">
                                <span>Payment Processing Fee</span>
                                <span class="font-mono font-bold text-emerald-600">₹0 (Waived)</span>
                            </div>
                            <div class="flex justify-between py-1 text-slate-600">
                                <span>Applicable Taxes (GST)</span>
                                <span class="font-mono font-bold text-emerald-600">Included</span>
                            </div>
                            <div class="pt-3 border-t border-slate-100 flex justify-between items-baseline">
                                <span class="font-bold text-slate-900 text-sm">Total Payable</span>
                                <span class="text-2xl font-black font-display text-emerald-600">₹{{ number_format($payment->amount) }}</span>
                            </div>
                        </div>

                        {{-- Terms Checkbox --}}
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                <input type="checkbox"
                                       x-model="termsAccepted"
                                       class="mt-0.5 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                <span class="text-xs text-slate-600 leading-snug">
                                    I accept the
                                    <button type="button" @click="termsModalOpen = true" class="text-emerald-700 underline font-bold hover:text-emerald-800">
                                        Vehicle Reservation &amp; Booking Terms
                                    </button>
                                </span>
                            </label>
                            <div x-show="shakeWarning" x-cloak class="text-[11px] font-bold text-rose-600">
                                Please accept the reservation terms to proceed with payment.
                            </div>
                        </div>

                        {{-- Live Razorpay Pay Button --}}
                        <div class="space-y-3">
                            <button type="button"
                                    @click="if (requireTerms('pay')) launchRazorpay()"
                                    class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs uppercase tracking-wider transition shadow-lg shadow-emerald-600/25 flex items-center justify-center gap-2 cursor-pointer hover:scale-[1.01] active:scale-[0.99]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Pay ₹{{ number_format($payment->amount) }} via Razorpay</span>
                            </button>
                            <p class="text-[11px] text-slate-400 text-center flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                <span>Supports UPI, Cards, Netbanking &amp; Wallets</span>
                            </p>
                        </div>

                        {{-- Test Sandbox Simulator Pill (for local demo & developer verification) --}}
                        <div class="pt-4 border-t border-slate-100 space-y-2.5">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Sandbox Instant Simulation</span>
                            <div class="grid grid-cols-2 gap-2">
                                <form method="POST" action="{{ route('showrooms.payments.verify', $showroom) }}">
                                    @csrf
                                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                    <input type="hidden" name="razorpay_order_id" value="{{ $payment->razorpay_order_id }}">
                                    <input type="hidden" name="razorpay_payment_id" value="{{ $simulatedPaymentId }}">
                                    <input type="hidden" name="razorpay_signature" value="{{ $simulatedSignature }}">
                                    <input type="hidden" name="method" value="upi">
                                    <button type="submit"
                                            @click="return requireTerms('upi')"
                                            class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-bold transition flex items-center justify-center gap-1.5">
                                        <span>⚡ Simulate UPI</span>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('showrooms.payments.verify', $showroom) }}">
                                    @csrf
                                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                    <input type="hidden" name="razorpay_order_id" value="{{ $payment->razorpay_order_id }}">
                                    <input type="hidden" name="razorpay_payment_id" value="{{ $simulatedPaymentId }}">
                                    <input type="hidden" name="razorpay_signature" value="{{ $simulatedSignature }}">
                                    <input type="hidden" name="method" value="card">
                                    <button type="submit"
                                            @click="return requireTerms('card')"
                                            class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                                        <span>💳 Simulate Card</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Fallback: Switch to Normal Booking (Pay at Showroom) --}}
                        <div class="pt-3 border-t border-slate-100 text-center">
                            <form method="POST" action="{{ route('showrooms.booking.switch-offline', [$showroom, $enquiry]) }}">
                                @csrf
                                <button type="submit"
                                        class="text-xs font-semibold text-slate-500 hover:text-slate-900 underline underline-offset-2 transition cursor-pointer">
                                    Prefer to pay at dealership? Switch to Normal Booking &rarr;
                                </button>
                            </form>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Hidden Form for Live Razorpay Client Callback Verification --}}
        <form id="booking-payment-verify-form" method="POST" action="{{ route('showrooms.payments.verify', $showroom) }}" class="hidden">
            @csrf
            <input type="hidden" name="payment_id" value="{{ $payment->id }}">
            <input type="hidden" name="razorpay_order_id" value="{{ $payment->razorpay_order_id }}">
            <input type="hidden" name="razorpay_payment_id" id="booking_rzp_payment_id">
            <input type="hidden" name="razorpay_signature" id="booking_rzp_signature">
            <input type="hidden" name="method" value="razorpay_checkout">
        </form>

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
                                Booking &amp; Reservation Policies
                            </span>
                            <h3 class="text-xl font-black font-display text-slate-900 mt-1">Vehicle Booking Terms &amp; Conditions</h3>
                            <p class="text-xs text-slate-500">{{ $showroom->name }} Authorized Dealership</p>
                        </div>
                        <button type="button" @click="termsModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="py-5 space-y-4 max-h-[60vh] overflow-y-auto text-xs text-slate-600 leading-relaxed pr-2">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <h4 class="font-bold text-slate-900 text-sm">1. 100% Refundable Booking Advance</h4>
                            <p>
                                The booking advance token of ₹10,000 is 100% refundable at any time prior to tax invoicing and RTO vehicle registration. No cancellation charges or deduction fees will apply.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <h4 class="font-bold text-slate-900 text-sm">2. Priority Chassis &amp; Trim Allocation</h4>
                            <p>
                                Submitting your booking holds your preferred vehicle variant and color code in priority inventory allocation for 7 business days pending final paperwork.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <h4 class="font-bold text-slate-900 text-sm">3. Normal Booking vs Razorpay Booking</h4>
                            <p>
                                <strong>Normal Booking:</strong> Allows reservation confirmation without online payment. The advance token must be deposited at the showroom within 48 hours.<br>
                                <strong>Razorpay Booking:</strong> Instantly completes encrypted advance token payment with automatic invoice confirmation and priority chassis queue jump.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                            <h4 class="font-bold text-slate-900 text-sm">4. Documentation &amp; Showroom Handover</h4>
                            <p>
                                Standard KYC documents (Aadhaar, PAN, and valid Driving License) will be verified at the dealership prior to final delivery and permanent RTO registration.
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <button type="button" @click="termsAccepted = true; termsModalOpen = false"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs">
                            I Understand &amp; Accept Terms
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
            description: "Reservation Advance #{{ $enquiry->enquiry_number }} - {{ addslashes($enquiry->bike?->name ?? 'Motorcycle') }}",
            order_id: "{{ $payment->razorpay_order_id }}",
            prefill: {
                name: "{{ addslashes($enquiry->customer_name) }}",
                email: "{{ addslashes($enquiry->customer_email) }}",
                contact: "{{ addslashes($enquiry->customer_phone) }}"
            },
            theme: {
                color: "#059669"
            },
            handler: function (response) {
                document.getElementById('booking_rzp_payment_id').value = response.razorpay_payment_id;
                document.getElementById('booking_rzp_signature').value = response.razorpay_signature;
                document.getElementById('booking-payment-verify-form').submit();
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();
    }
    </script>

    {{-- Motorcycle Showroom Footer --}}
    @include('showrooms.partials.footer')
</x-app-layout>
