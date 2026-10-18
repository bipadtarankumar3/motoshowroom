@extends('layouts.app')

@section('title', 'Participations - ' . $campaign->name)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-2">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-white transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="hover:text-white transition">Campaigns</a>
            <span>/</span>
            <span class="text-slate-200">Participations: {{ $campaign->name }}</span>
        </div>

        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">Campaign Scratch Card Ledger</h1>
                <p class="text-sm text-slate-400 mt-1">Audit customer participations, prize outcomes, and in-store coupon redemptions.</p>
            </div>
            <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-bold text-slate-300 hover:text-white transition">
                &larr; Back to Campaigns
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 mb-6 backdrop-blur">
            <form method="GET" action="{{ route('showrooms.manage.campaigns.participations', [$showroom, $campaign]) }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <div class="sm:col-span-8 relative">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search customer name, email, phone, or coupon code..."
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 pl-9 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-500">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <div class="sm:col-span-3">
                    <select name="redeemed" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-purple-500">
                        <option value="all">All Redemption States</option>
                        <option value="yes" @selected($redeemed === 'yes')>Redeemed Only</option>
                        <option value="no" @selected($redeemed === 'no')>Unredeemed Only</option>
                    </select>
                </div>

                <div class="sm:col-span-1">
                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs py-2.5 px-3 rounded-xl transition">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Participations Table -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 text-slate-400 font-semibold border-b border-slate-800 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4">Customer & Contact</th>
                            <th class="px-6 py-4">Card Token</th>
                            <th class="px-6 py-4">Scratch State</th>
                            <th class="px-6 py-4">Prize Allocated</th>
                            <th class="px-6 py-4">Coupon Code</th>
                            <th class="px-6 py-4">Redemption Status</th>
                            <th class="px-6 py-4 text-right">Claim Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-medium">
                        @forelse($participations as $part)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-white text-sm">{{ $part->customer_name }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $part->customer_email }} &bull; {{ $part->customer_phone }}</div>
                                </td>
                                <td class="px-6 py-4 font-mono text-[11px] text-slate-400">
                                    {{ substr($part->participation_token, 0, 10) }}...
                                </td>
                                <td class="px-6 py-4">
                                    @if($part->is_scratched)
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Revealed {{ $part->scratched_at?->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700">
                                            Unscratched
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($part->prize)
                                        <span class="text-white font-bold block">{{ $part->prize->title }}</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded font-semibold inline-block mt-0.5 {{ $part->prize->prize_type->badgeClasses() }}">
                                            {{ $part->prize->prize_type->label() }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 italic">None</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-purple-400">
                                    {{ $part->coupon_code ?: '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($part->is_redeemed)
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            Redeemed on {{ $part->redeemed_at?->format('M d, Y') }}
                                        </span>
                                        @if($part->redeemedBy)
                                            <span class="text-[10px] text-slate-500 block mt-0.5">by {{ $part->redeemedBy->name }}</span>
                                        @endif
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700">
                                            Unredeemed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-slate-400">
                                    {{ $part->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    <p class="font-bold text-white text-sm">No Participations Yet</p>
                                    <p class="text-xs text-slate-400 mt-1">Cards claimed by customers on your showroom storefront will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($participations->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $participations->links() }}
                </div>
            @endif
        </div>

    </main>
</div>
@endsection
