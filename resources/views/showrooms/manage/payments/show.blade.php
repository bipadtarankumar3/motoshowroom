@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="flex items-center justify-between text-xs text-slate-500 print:hidden">
            <div class="flex items-center gap-2">
                <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
                <span>/</span>
                <a href="{{ route('showrooms.manage.payments.index', $showroom) }}" class="hover:text-slate-900 transition">Payments</a>
                <span>/</span>
                <span class="text-slate-900 font-semibold">#PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>

            <button type="button" onclick="window.print()"
                    class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-700 hover:text-slate-900 shadow-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Receipt</span>
            </button>
        </div>

        <!-- Official Payment Receipt Card -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xs space-y-8">

            <!-- Receipt Header -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-slate-200">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-rose-600 block mb-1">Payment Receipt</span>
                    <h1 class="text-2xl font-black font-display text-slate-900 tracking-tight">
                        #PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                    </h1>
                    <p class="text-xs text-slate-500 font-mono mt-1">
                        Issued: {{ $payment->created_at->format('F d, Y \a\t h:i A') }}
                    </p>
                </div>

                <div class="text-left sm:text-right">
                    <span class="font-display font-black text-lg text-slate-900 block">{{ $showroom->name }}</span>
                    <span class="text-xs text-slate-500 block">{{ $showroom->city }}, {{ $showroom->state }}</span>
                    @if ($showroom->email)
                        <span class="text-xs text-slate-500 font-mono block">{{ $showroom->email }}</span>
                    @endif
                </div>
            </div>

            <!-- Verification & Status Banner -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold {{ $payment->status->badgeClasses() }}">
                        {{ $payment->status->label() }}
                    </span>
                    @if ($payment->signature_verified)
                        <span class="inline-flex items-center gap-1.5 text-xs text-emerald-700 font-bold font-mono">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            HMAC-SHA256 Cryptographically Verified
                        </span>
                    @endif
                </div>

                <div class="text-xs text-slate-500 font-mono">
                    Gateway: <span class="text-slate-900 font-bold">Razorpay</span>
                </div>
            </div>

            <!-- Payer & Gateway Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                <div class="space-y-2 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h3 class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Payer Information</h3>
                    <p class="text-sm font-bold text-slate-900">{{ $payment->user?->name ?? 'Guest Customer' }}</p>
                    <p class="text-slate-500 font-mono">{{ $payment->user?->email ?? ($payment->notes['customer_email'] ?? 'N/A') }}</p>
                    @if ($payment->user?->phone)
                        <p class="text-slate-500 font-mono">{{ $payment->user->phone }}</p>
                    @endif
                </div>

                <div class="space-y-2 p-4 rounded-2xl bg-slate-50 border border-slate-200 font-mono">
                    <h3 class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Gateway Telemetry</h3>
                    <div>
                        <span class="text-slate-500 text-[10px] block">Razorpay Order ID:</span>
                        <span class="text-slate-800">{{ $payment->razorpay_order_id ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 text-[10px] block">Razorpay Payment ID:</span>
                        <span class="text-emerald-700 font-bold">{{ $payment->razorpay_payment_id ?? 'Pending Capture' }}</span>
                    </div>
                    @if ($payment->method)
                        <div>
                            <span class="text-slate-500 text-[10px] block">Payment Method:</span>
                            <span class="text-slate-800 uppercase">{{ $payment->method }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Invoiced Item Line Breakdown -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-500 tracking-wider">
                            <th class="py-3 px-4">Item & Description</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4 text-right">Amount ({{ $payment->currency }})</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        <tr>
                            <td class="py-3.5 px-4 font-sans">
                                <span class="font-bold text-slate-900 text-sm block">
                                    {{ ucwords(str_replace('_', ' ', $payment->purpose)) }}
                                </span>
                                @if ($payment->notes)
                                    <span class="text-[11px] text-slate-500 block mt-0.5">
                                        @foreach ($payment->notes as $k => $v)
                                            <span class="text-slate-500">{{ $k }}:</span> {{ is_array($v) ? json_encode($v) : $v }}@if (!$loop->last) • @endif
                                        @endforeach
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 uppercase text-[11px] text-slate-500">
                                {{ class_basename($payment->payable_type) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-emerald-700 text-sm">
                                ₹{{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t border-slate-200 font-mono font-bold">
                            <td colspan="2" class="py-3.5 px-4 text-right text-slate-600 uppercase text-xs">Total Paid:</td>
                            <td class="py-3.5 px-4 text-right text-emerald-700 text-base">₹{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer Compliance Notice -->
            <div class="text-[10px] text-slate-500 text-center leading-relaxed border-t border-slate-200 pt-6">
                This is a digitally generated tax receipt for transaction #PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}.
                All payments are processed securely through Razorpay in accordance with RBI payment aggregator guidelines.
            </div>

        </div>

    </div>
</div>
@endsection
