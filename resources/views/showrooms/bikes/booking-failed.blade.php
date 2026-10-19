@extends('layouts.app')

@section('title', 'Payment Incomplete — Reservation #' . $enquiry->enquiry_number . ' — ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50/70 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    @include('showrooms.partials.header')

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        {{-- Top Error Card --}}
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm text-center relative overflow-hidden mb-8">
            <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-gradient-to-b from-rose-500/10 via-amber-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-xl mx-auto space-y-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-rose-100 text-rose-600 mx-auto flex items-center justify-center shadow-md shadow-rose-500/20">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>

                <div class="flex items-center justify-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                        Payment Authorization Incomplete
                    </span>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold font-mono bg-slate-100 text-slate-800 border border-slate-200">
                        #{{ $enquiry->enquiry_number }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">
                    Online Payment Could Not Be Verified
                </h1>

                @if(session('error'))
                    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                        {{ session('error') }}
                    </div>
                @else
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        The Razorpay payment gateway could not confirm the ₹10,000 reservation advance. This could be due to a bank server timeout, window dismissal, or declined card authorization.
                    </p>
                @endif

                <p class="text-xs text-slate-500">
                    Don't worry — your customer reservation details for <strong>{{ $enquiry->bike?->name ?? 'your motorcycle' }}</strong> are safely saved. You can retry payment online or switch to Normal Booking.
                </p>
            </div>
        </div>

        {{-- Resolution Actions Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">

            {{-- Option 1: Retry Razorpay --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Retry Online Payment via Razorpay</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Attempt the ₹10,000 refundable advance payment again using UPI (GPay/PhonePe), different Debit/Credit card, or NetBanking.
                    </p>
                </div>

                <a href="{{ route('showrooms.bikes.booking.checkout', [$showroom, $enquiry]) }}"
                   class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition text-center shadow-md shadow-emerald-600/20 block">
                    Retry Payment via Razorpay &rarr;
                </a>
            </div>

            {{-- Option 2: Switch to Normal Booking --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Switch to Normal Booking (Pay at Showroom)</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Prefer not to pay online right now? Keep your vehicle priority on hold for 48 hours and pay the token directly at {{ $showroom->name }}.
                    </p>
                </div>

                <form method="POST" action="{{ route('showrooms.booking.switch-offline', [$showroom, $enquiry]) }}">
                    @csrf
                    <button type="submit"
                            class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider transition text-center cursor-pointer shadow-sm">
                        Confirm Normal Booking &rarr;
                    </button>
                </form>
            </div>

        </div>

        {{-- Direct Dealership Support Box --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <h4 class="font-bold text-slate-900">Need Assistance with your Booking?</h4>
                <p class="text-slate-500">Contact our sales desk at {{ $showroom->name }} to finalize your vehicle order over phone.</p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                @if($showroom->phone)
                    <a href="tel:{{ $showroom->phone }}"
                       class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold transition">
                        Call {{ $showroom->phone }}
                    </a>
                @endif
                <a href="{{ route('showrooms.show', $showroom) }}"
                   class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold transition">
                    Return to Showroom
                </a>
            </div>
        </div>

    </main>

    @include('showrooms.partials.footer')
</div>
@endsection
