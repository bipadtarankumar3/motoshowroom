@extends('layouts.app')

@section('title', 'Promotional Scratch Cards - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8">

        <!-- Flash Feedback Notifications -->
        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}" class="text-xs font-bold uppercase tracking-wider text-rose-600 underline hover:text-rose-700">Upgrade Plan</a>
            </div>
        @endif

        @if (! $hasModuleAccess)
            <div class="mb-8 rounded-3xl bg-amber-50 border border-amber-200 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Promotional Scratch Cards Feature is Locked</h3>
                        <p class="text-xs text-slate-600 mt-0.5">Your current subscription tier does not include interactive promotional marketing campaigns. Upgrade to Pro/Enterprise to create campaigns and issue scratch cards.</p>
                    </div>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}"
                   class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-amber-600/20 transition whitespace-nowrap">
                    Upgrade Subscription
                </a>
            </div>
        @endif

        <!-- Quick In-Store Coupon Redemption Bar -->
        <div class="mb-8 rounded-2xl bg-gradient-to-r from-purple-50 via-white to-purple-50 border border-purple-200 p-5 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">In-Store Coupon Redemption Desk</h4>
                        <p class="text-[11px] text-slate-500">Validate and redeem scratch card prize vouchers presented by customers at the billing or service desk.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('showrooms.manage.coupons.redeem', $showroom) }}" class="flex items-center gap-2 w-full md:w-auto">
                    @csrf
                    <input type="text"
                           name="coupon_code"
                           placeholder="Enter Coupon (e.g. MOTO-ABCD-1234)"
                           required
                           class="w-full md:w-64 bg-white border border-slate-300 rounded-xl px-4 py-2 text-xs text-slate-900 placeholder-slate-400 uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    <button type="submit"
                            id="btn-redeem-coupon"
                            class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs uppercase tracking-wider transition shrink-0 shadow-md shadow-purple-600/20">
                        Redeem
                    </button>
                </form>
            </div>
        </div>

        <!-- Header & Top Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-700 font-semibold">Promotions</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Promotional Scratch Cards</h1>
                <p class="text-sm text-slate-500 mt-1">Design promotional campaigns, configure prize probabilities, and track digital redemptions.</p>
            </div>

            <div class="flex items-center gap-3">
                @if ($hasModuleAccess)
                    <a href="{{ route('showrooms.manage.campaigns.create', $showroom) }}"
                       id="btn-add-campaign"
                       class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-purple-600/20 transition flex items-center gap-2 group">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>New Campaign</span>
                    </a>
                @else
                    <button disabled class="opacity-50 cursor-not-allowed px-5 py-2.5 rounded-xl bg-slate-200 border border-slate-300 text-slate-500 font-bold text-xs flex items-center gap-2" title="Plan upgrade required">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>New Campaign</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-xs">
                <span class="text-[11px] text-slate-500 font-medium block">Total Campaigns</span>
                <span class="text-2xl font-black font-display text-slate-900 mt-1 block">{{ $totalCampaigns }}</span>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-xs">
                <span class="text-[11px] text-emerald-600 font-medium block">Active Campaigns</span>
                <span class="text-2xl font-black font-display text-emerald-600 mt-1 block">{{ $activeCampaigns }}</span>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-xs">
                <span class="text-[11px] text-purple-600 font-medium block">Cards Issued</span>
                <span class="text-2xl font-black font-display text-purple-600 mt-1 block">{{ $totalParticipations }}</span>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-xs">
                <span class="text-[11px] text-sky-600 font-medium block">Cards Scratched</span>
                <span class="text-2xl font-black font-display text-sky-600 mt-1 block">{{ $totalScratched }}</span>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-xs">
                <span class="text-[11px] text-amber-600 font-medium block">Coupons Redeemed</span>
                <span class="text-2xl font-black font-display text-amber-600 mt-1 block">{{ $totalRedeemed }}</span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 mb-6 shadow-xs">
            <form method="GET" action="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <div class="sm:col-span-8 relative">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search campaigns by name or description..."
                           class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 pl-9 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <div class="sm:col-span-3">
                    <select name="status" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        <option value="all">All Campaign Statuses</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" @selected($status === $st->value)>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-1">
                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-2.5 px-3 rounded-xl transition shadow-xs">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Campaigns Table -->
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4">Campaign Name</th>
                            <th class="px-6 py-4">Active Schedule</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Prize Pool</th>
                            <th class="px-6 py-4">Cards Issued</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($campaigns as $camp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $camp->name }}</div>
                                    <span class="text-[11px] text-slate-400 font-mono">/promotions/{{ $camp->slug }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-800 block">{{ $camp->start_date->format('M d, Y') }}</span>
                                    <span class="text-[11px] text-slate-500 block">to {{ $camp->end_date->format('M d, Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('showrooms.manage.campaigns.toggle-status', [$showroom, $camp]) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ $camp->status->badgeClasses() }}" title="Click to toggle status">
                                            <span>{{ $camp->status->label() }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-800 block font-bold">{{ $camp->prizes_count }} Tiers</span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('showrooms.manage.campaigns.participations', [$showroom, $camp]) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 transition font-bold border border-slate-200">
                                        <span>{{ $camp->participations_count }}</span>
                                        <span class="text-[10px] text-slate-500 font-normal">Cards</span>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('showrooms.promotions.show', [$showroom, $camp]) }}"
                                           target="_blank"
                                           class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition"
                                           title="Public View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>

                                        <a href="{{ route('showrooms.manage.campaigns.participations', [$showroom, $camp]) }}"
                                           class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition"
                                           title="View Scratched Cards Ledger">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </a>

                                        <a href="{{ route('showrooms.manage.campaigns.edit', [$showroom, $camp]) }}"
                                           class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition"
                                           title="Edit Campaign">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <form method="POST" action="{{ route('showrooms.manage.campaigns.destroy', [$showroom, $camp]) }}" onsubmit="return confirm('Delete this promotional campaign?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition" title="Delete Campaign">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                                    </div>
                                    <p class="font-bold text-slate-800 text-sm">No Promotional Campaigns Yet</p>
                                    <p class="text-xs text-slate-500 mt-1">Create your first scratch card marketing campaign to reward visiting riders.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($campaigns->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>

    </main>
    @include('showrooms.partials.manage-nav-close')
</div>
@endsection
