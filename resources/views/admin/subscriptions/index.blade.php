<x-app-layout title="Subscriptions Ledger - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="min-h-screen bg-slate-50 text-slate-900 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-black font-display text-slate-900 tracking-tight">Subscriptions Oversight</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                            Billing Engine
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mt-1">Tenant subscription lifecycles, manual expiry extensions, plan upgrades, and grace period controls.</p>
                </div>
            </div>

            <!-- Stats Metric Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Total Subscriptions</span>
                    <p class="text-2xl font-black font-display text-slate-900 mt-1">{{ $stats['total'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                    <span class="text-[10px] uppercase font-bold text-emerald-600 tracking-wider">Active</span>
                    <p class="text-2xl font-black font-display text-emerald-600 mt-1">{{ $stats['active'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                    <span class="text-[10px] uppercase font-bold text-amber-600 tracking-wider">In Trial</span>
                    <p class="text-2xl font-black font-display text-amber-600 mt-1">{{ $stats['trial'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                    <span class="text-[10px] uppercase font-bold text-orange-600 tracking-wider">Past Due (Grace)</span>
                    <p class="text-2xl font-black font-display text-orange-600 mt-1">{{ $stats['past_due'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                    <span class="text-[10px] uppercase font-bold text-rose-600 tracking-wider">Expired / Suspended</span>
                    <p class="text-2xl font-black font-display text-rose-600 mt-1">{{ $stats['expired'] }}</p>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search showroom name or email..."
                               class="w-full px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <div>
                        <select name="status" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="past_due" {{ request('status') === 'past_due' ? 'selected' : '' }}>Past Due</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <select name="plan_id" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Plans</option>
                            @foreach($plans as $p)
                                <option value="{{ $p->id }}" {{ request('plan_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="w-full py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'plan_id']))
                            <a href="{{ route('admin.subscriptions.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Subscriptions Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 uppercase font-bold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Showroom</th>
                                <th class="px-6 py-4">Current Plan</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Period End / Days Left</th>
                                <th class="px-6 py-4 text-right">Administrative Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($subscriptions as $subscription)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-white shrink-0 shadow-xs"
                                                 style="background-color: {{ $subscription->showroom->brand_color_primary ?? '#dc2626' }}">
                                                {{ strtoupper(substr($subscription->showroom->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.showrooms.show', $subscription->showroom) }}" class="font-bold text-slate-900 hover:text-rose-600 transition block">
                                                    {{ $subscription->showroom->name }}
                                                </a>
                                                <span class="text-[11px] text-slate-500">{{ $subscription->showroom->email }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">{{ $subscription->plan->name }}</div>
                                        <div class="text-[11px] text-slate-500 uppercase font-semibold">{{ $subscription->billing_interval }}</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded text-xs font-bold border {{ $subscription->status->badgeClasses() }}">
                                            {{ $subscription->status->label() }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-mono text-slate-900 font-semibold">{{ $subscription->current_period_end->format('M d, Y') }}</div>
                                        <div class="text-[11px] {{ $subscription->days_remaining > 5 ? 'text-emerald-700' : 'text-amber-700' }} font-bold">
                                            {{ $subscription->days_remaining }} days left
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Extend Form Modal/Details -->
                                            <details class="relative">
                                                <summary class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold cursor-pointer list-none flex items-center gap-1 shadow-xs">
                                                    <span>Extend</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </summary>
                                                <div class="absolute right-0 mt-2 w-72 p-4 rounded-xl bg-white border border-slate-200 shadow-xl z-30 text-left space-y-3">
                                                    <h4 class="text-xs font-bold text-slate-900">Extend Expiry Date</h4>
                                                    <form method="POST" action="{{ route('admin.subscriptions.extend', $subscription) }}" class="space-y-3">
                                                        @csrf
                                                        <div>
                                                            <label class="block text-[10px] uppercase font-bold text-slate-600 mb-1">Add Days</label>
                                                            <input type="number" name="days" value="14" min="1" max="365" required class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] uppercase font-bold text-slate-600 mb-1">Audit Reason</label>
                                                            <input type="text" name="reason" placeholder="e.g. Trial extension granted" required class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                                        </div>
                                                        <button type="submit" class="w-full py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                                                            Confirm Extension
                                                        </button>
                                                    </form>
                                                </div>
                                            </details>

                                            <!-- Change Plan Details -->
                                            <details class="relative">
                                                <summary class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold cursor-pointer list-none flex items-center gap-1 shadow-xs">
                                                    <span>Switch Plan</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </summary>
                                                <div class="absolute right-0 mt-2 w-64 p-4 rounded-xl bg-white border border-slate-200 shadow-xl z-30 text-left space-y-3">
                                                    <h4 class="text-xs font-bold text-slate-900">Switch Plan Tier</h4>
                                                    <form method="POST" action="{{ route('admin.subscriptions.change-plan', $subscription) }}" class="space-y-3">
                                                        @csrf
                                                        <div>
                                                            <label class="block text-[10px] uppercase font-bold text-slate-600 mb-1">Target Plan</label>
                                                            <select name="plan_id" required class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                                                @foreach($plans as $p)
                                                                    <option value="{{ $p->id }}" {{ $subscription->plan_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="w-full py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition">
                                                            Update Plan
                                                        </button>
                                                    </form>
                                                </div>
                                            </details>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        No showroom subscriptions found matching filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($subscriptions->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-white">
                        {{ $subscriptions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
