@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-white transition">Dashboard</a>
            <span>/</span>
            <span class="text-slate-200">Financial Ledger & Payments</span>
        </div>

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">Payments & Financial Ledger</h1>
                <p class="text-sm text-slate-400 mt-1">Audit log of Razorpay transactions, customer invoice settlements, and subscription purchases.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.billing.index', $showroom) }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Subscription Hub</span>
                </a>
            </div>
        </div>

        <!-- KPI Metric Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Volume Processed</span>
                <p class="text-2xl font-black font-display text-emerald-400 mt-1">₹{{ number_format($totalVolume) }}</p>
                <span class="text-[10px] text-slate-500">Captured revenue</span>
            </div>

            <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800">
                <span class="text-[10px] uppercase font-bold text-sky-400 tracking-wider">Total Transactions</span>
                <p class="text-2xl font-black font-display text-white mt-1">{{ $totalTransactions }}</p>
                <span class="text-[10px] text-slate-500">All gateway attempts</span>
            </div>

            <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800">
                <span class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider">Successful Payments</span>
                <p class="text-2xl font-black font-display text-emerald-400 mt-1">{{ $successfulCount }}</p>
                <span class="text-[10px] text-slate-500">Verified & captured</span>
            </div>

            <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800">
                <span class="text-[10px] uppercase font-bold text-rose-400 tracking-wider">Failed / Declined</span>
                <p class="text-2xl font-black font-display text-rose-400 mt-1">{{ $failedCount }}</p>
                <span class="text-[10px] text-slate-500">Declined transactions</span>
            </div>
        </div>

        <!-- Search & Filter Toolbar -->
        <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800">
            <form method="GET" action="{{ route('showrooms.manage.payments.index', $showroom) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Search -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Search Transactions</label>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Order ID, Payment ID, Method..."
                           class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white placeholder-slate-500 focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Transaction Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                        <option value="all">All Statuses</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" {{ $status === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition">
                        Filter
                    </button>
                    <a href="{{ route('showrooms.manage.payments.index', $showroom) }}"
                       class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Payments Ledger Table -->
        <div class="glass-panel-dark rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
            @if ($payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 bg-slate-900/60 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                <th class="py-3 px-4">Transaction / Gateway Order</th>
                                <th class="py-3 px-4">Payer / Customer</th>
                                <th class="py-3 px-4">Purpose</th>
                                <th class="py-3 px-4">Amount</th>
                                <th class="py-3 px-4">Status & Signature</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4 text-right">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach ($payments as $pm)
                                <tr class="hover:bg-slate-900/40 transition">
                                    <!-- Transaction & Order ID -->
                                    <td class="py-3.5 px-4 font-mono">
                                        <a href="{{ route('showrooms.manage.payments.show', [$showroom, $pm]) }}" class="font-bold text-rose-400 hover:underline block">
                                            #PAY-{{ str_pad($pm->id, 6, '0', STR_PAD_LEFT) }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 block mt-0.5">
                                            {{ $pm->razorpay_order_id ?? 'Direct Offline' }}
                                        </span>
                                        @if ($pm->razorpay_payment_id)
                                            <span class="text-[10px] text-slate-500 block font-mono">{{ $pm->razorpay_payment_id }}</span>
                                        @endif
                                    </td>

                                    <!-- Payer -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-white text-xs">
                                            {{ $pm->user?->name ?? 'Guest Customer' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            {{ $pm->user?->email ?? ($pm->notes['customer_email'] ?? 'N/A') }}
                                        </div>
                                    </td>

                                    <!-- Purpose -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-900 border border-slate-700 text-slate-300">
                                            {{ str_replace('_', ' ', $pm->purpose) }}
                                        </span>
                                        @if ($pm->method)
                                            <span class="text-[10px] text-slate-500 block mt-0.5 uppercase font-mono">via {{ $pm->method }}</span>
                                        @endif
                                    </td>

                                    <!-- Amount -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-mono text-sm font-bold text-emerald-400">
                                            ₹{{ number_format($pm->amount) }}
                                        </div>
                                        <span class="text-[10px] text-slate-500 uppercase">{{ $pm->currency }}</span>
                                    </td>

                                    <!-- Status & Verification -->
                                    <td class="py-3.5 px-4">
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold {{ $pm->status->badgeClasses() }}">
                                                {{ $pm->status->label() }}
                                            </span>
                                            @if ($pm->signature_verified)
                                                <span class="inline-flex items-center gap-1 text-[10px] text-emerald-400 block font-mono">
                                                    <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    HMAC Verified
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Date -->
                                    <td class="py-3.5 px-4 text-slate-400">
                                        <div>{{ $pm->created_at->format('M d, Y') }}</div>
                                        <span class="text-[10px] text-slate-500 font-mono">{{ $pm->created_at->format('h:i A') }}</span>
                                    </td>

                                    <!-- Action -->
                                    <td class="py-3.5 px-4 text-right">
                                        <a href="{{ route('showrooms.manage.payments.show', [$showroom, $pm]) }}"
                                           class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-white rounded-lg font-semibold text-[11px] transition">
                                            Receipt
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($payments->hasPages())
                    <div class="p-4 border-t border-slate-800">
                        {{ $payments->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-16 px-4 space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-900 text-slate-500 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-white">No Payment Records Found</h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto">
                        No financial transactions match the current search or status filter.
                    </p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
