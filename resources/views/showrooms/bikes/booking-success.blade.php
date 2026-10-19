@extends('layouts.app')

@section('title', 'Booking Confirmed — #' . $enquiry->enquiry_number . ' — ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50/70 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    @include('showrooms.partials.header')

    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        {{-- Top Success Status Banner --}}
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm text-center relative overflow-hidden mb-8">
            <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-gradient-to-b from-emerald-500/10 via-teal-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl mx-auto space-y-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center shadow-md shadow-emerald-500/20">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>

                <div class="flex items-center justify-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                        🎉 Booking Confirmed &amp; Chassis Priority Locked
                    </span>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold font-mono bg-slate-100 text-slate-800 border border-slate-200">
                        #{{ $enquiry->enquiry_number }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black font-display text-slate-900 tracking-tight">
                    Thank You, {{ $enquiry->customer_name }}!
                </h1>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    @if($enquiry->payment_method === 'razorpay')
                        Your online advance token of <strong>₹{{ number_format($enquiry->booking_amount ?? 10000) }}</strong> has been received securely via Razorpay. Your vehicle allocation is prioritized in our dispatch schedule.
                    @else
                        Your reservation has been placed via <strong>Normal Booking (Pay at Showroom)</strong>. Your priority chassis is tentatively locked for 48 hours pending in-person advance token deposit at the dealership.
                    @endif
                </p>

                {{-- Payment Method & Status Pill --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl {{ $enquiry->payment_method === 'razorpay' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }} text-xs font-bold">
                    @if($enquiry->payment_method === 'razorpay')
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>Razorpay Online Payment Verified (Captured)</span>
                    @else
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Normal Booking (Pay Token at Dealership in 48h)</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Scratch Card Reward Banner (if unlocked) --}}
        @if(session('scratch_token'))
            <div class="mb-8 rounded-3xl bg-gradient-to-r from-purple-50 via-white to-purple-50 border-2 border-purple-200 p-6 sm:p-7 shadow-sm relative overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-purple-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 uppercase tracking-wider">
                                Booking Bonus Reward
                            </span>
                            <h3 class="text-base sm:text-lg font-black font-display text-slate-900 mt-1">
                                🎉 You Unlocked a Rider Scratch &amp; Win Card!
                            </h3>
                            <p class="text-xs text-slate-600">
                                Scratch now to claim riding gear vouchers, helmet discounts, or free service rewards.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('showrooms.promotions.index', $showroom) }}"
                       class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-purple-600/20 transition text-center shrink-0">
                        Scratch Card Now &rarr;
                    </a>
                </div>
            </div>
        @endif

        {{-- ═══ 2-COLUMN BOOKING SUMMARY ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- LEFT: VEHICLE & ORDER BREAKDOWN --}}
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">

                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Vehicle &amp; Allocation Details</h2>
                    <span class="text-xs text-slate-400 font-mono">{{ now()->format('d M Y, h:i A') }}</span>
                </div>

                {{-- Bike Detail Snippet --}}
                @if($enquiry->bike)
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="w-20 h-20 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                            @if($enquiry->bike->primaryImage)
                                <img src="{{ Storage::url($enquiry->bike->primaryImage->image_path) }}"
                                     alt="{{ $enquiry->bike->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <span class="text-[10px] font-mono text-slate-400">Bike</span>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $enquiry->bike->brand }}</span>
                            <h3 class="font-bold text-slate-900 text-base leading-snug">{{ $enquiry->bike->name }}</h3>
                            <p class="text-xs text-slate-500 font-mono">SKU: {{ $enquiry->bike->sku }}</p>
                        </div>
                    </div>
                @endif

                {{-- Particulars List --}}
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Booking Reference:</span>
                        <span class="font-bold font-mono text-slate-900">#{{ $enquiry->enquiry_number }}</span>
                    </div>

                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Booking Type:</span>
                        <span class="font-bold text-slate-900">Vehicle Purchase Reservation</span>
                    </div>

                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Payment Option:</span>
                        <span class="font-bold {{ $enquiry->payment_method === 'razorpay' ? 'text-emerald-700' : 'text-slate-900' }}">
                            {{ $enquiry->payment_method === 'razorpay' ? 'Razorpay Online Token' : 'Normal Booking (Pay at Showroom)' }}
                        </span>
                    </div>

                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Booking Advance Token:</span>
                        <span class="font-black font-mono text-emerald-600 text-sm">₹{{ number_format($enquiry->booking_amount ?? 10000) }}</span>
                    </div>

                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Payment Status:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $enquiry->payment_status === 'captured' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                            {{ $enquiry->payment_status }}
                        </span>
                    </div>

                    @if($latestPayment && $latestPayment->razorpay_payment_id)
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Razorpay Payment ID:</span>
                            <span class="font-mono text-slate-700">{{ $latestPayment->razorpay_payment_id }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Target Delivery Date:</span>
                        <span class="font-bold text-slate-900">
                            {{ $enquiry->preferred_date ? \Carbon\Carbon::parse($enquiry->preferred_date)->format('d F Y') : 'To Be Coordinated' }}
                        </span>
                    </div>

                    @if($enquiry->customer_notes)
                        <div class="py-2">
                            <span class="text-slate-500 block mb-1">Customer Remarks:</span>
                            <p class="text-slate-700 italic bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $enquiry->customer_notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Guarantee Note --}}
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span><strong>100% Refundable Guarantee:</strong> Full money-back on request prior to vehicle invoice generation.</span>
                </div>

            </div>

            {{-- RIGHT: NEXT STEPS & DEALERSHIP PITSTOP --}}
            <div class="lg:col-span-5 space-y-6">

                {{-- Next Steps Timeline --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">What Happens Next?</h3>

                    <div class="space-y-4 text-xs text-slate-600">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold font-mono text-[11px] flex items-center justify-center shrink-0 mt-0.5">1</div>
                            <div>
                                <p class="font-bold text-slate-900">Showroom Advisor Follow-Up</p>
                                <p class="text-slate-500 mt-0.5">A dedicated customer relationship manager will contact you within 2 business hours.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold font-mono text-[11px] flex items-center justify-center shrink-0 mt-0.5">2</div>
                            <div>
                                <p class="font-bold text-slate-900">Machine Inspection</p>
                                <p class="text-slate-500 mt-0.5">Visit {{ $showroom->name }} to inspect your allocated VIN/chassis and accessories.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold font-mono text-[11px] flex items-center justify-center shrink-0 mt-0.5">3</div>
                            <div>
                                <p class="font-bold text-slate-900">Registration &amp; Handover</p>
                                <p class="text-slate-500 mt-0.5">Complete RTO road tax and insurance documentation for ceremonial pit handover.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dealership Contact Box --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm text-xs space-y-3">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">{{ $showroom->name }} Pit Location</h3>
                    <p class="text-slate-500 leading-relaxed">{{ $showroom->address }}</p>

                    <div class="pt-2 flex flex-col gap-2">
                        @if($showroom->phone)
                            <a href="tel:{{ $showroom->phone }}"
                               class="w-full py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-center transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>Call Showroom Desk ({{ $showroom->phone }})</span>
                            </a>
                        @endif

                        @if($showroom->getSetting('whatsapp_number'))
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $showroom->getSetting('whatsapp_number')) }}?text={{ urlencode('Hi! I have confirmed vehicle reservation #' . $enquiry->enquiry_number . ' for ' . ($enquiry->bike?->name ?? 'Motorcycle') . ' at ' . $showroom->name . '.') }}"
                               target="_blank"
                               class="w-full py-2.5 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold border border-emerald-200 text-center transition flex items-center justify-center gap-1.5">
                                <span>Chat via WhatsApp</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons (Print & Return) --}}
                <div class="space-y-2">
                    <button type="button"
                            onclick="window.print()"
                            class="w-full py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print / Save Booking Slip</span>
                    </button>

                    <a href="{{ route('showrooms.show', $showroom) }}"
                       class="w-full py-3 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 transition text-center block">
                        Return to Showroom Home
                    </a>
                </div>

            </div>

        </div>

    </main>

    @include('showrooms.partials.footer')
</div>
@endsection
