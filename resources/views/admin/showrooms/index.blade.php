<x-app-layout title="Showrooms Management - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Header & Actions -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
                <div>
                    <h1 class="text-3xl font-black font-display text-white tracking-tight">Showroom Tenants</h1>
                    <p class="text-sm text-slate-400 mt-1">Manage all independent dealership accounts, domains, statuses, and staff allocations.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.showrooms.create') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xl shadow-rose-600/30 transition flex items-center gap-2" id="btn-create-showroom-top">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Provision New Showroom</span>
                    </a>
                </div>
            </div>

            <!-- Metric Stat Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800 shadow-lg">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Total Showrooms</span>
                    <p class="text-3xl font-extrabold font-display text-white mt-1">{{ $stats['total'] }}</p>
                </div>

                <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800 shadow-lg">
                    <span class="text-xs uppercase font-bold text-emerald-400 tracking-wider">Active</span>
                    <p class="text-3xl font-extrabold font-display text-emerald-400 mt-1">{{ $stats['active'] }}</p>
                </div>

                <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800 shadow-lg">
                    <span class="text-xs uppercase font-bold text-amber-400 tracking-wider">Trial Tier</span>
                    <p class="text-3xl font-extrabold font-display text-amber-400 mt-1">{{ $stats['trial'] }}</p>
                </div>

                <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800 shadow-lg">
                    <span class="text-xs uppercase font-bold text-rose-400 tracking-wider">Suspended</span>
                    <p class="text-3xl font-extrabold font-display text-rose-400 mt-1">{{ $stats['suspended'] }}</p>
                </div>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800 shadow-lg">
                <form method="GET" action="{{ route('admin.showrooms.index') }}" class="flex flex-col md:flex-row items-center gap-4">
                    <div class="flex-grow w-full">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by showroom name, slug, email, or city..."
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>

                    <div class="w-full md:w-56 shrink-0">
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <button type="submit" class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-sm transition">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.showrooms.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white text-sm transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Showrooms Data Table -->
            <div class="glass-panel-dark rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-900/90 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-6 py-4">Showroom</th>
                                <th class="px-6 py-4">Owner / Contact</th>
                                <th class="px-6 py-4">Location</th>
                                <th class="px-6 py-4">Staff Count</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @forelse($showrooms as $showroom)
                                <tr class="hover:bg-slate-900/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white shrink-0 shadow"
                                                 style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                                                {{ strtoupper(substr($showroom->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.showrooms.show', $showroom) }}" class="font-bold text-white hover:text-rose-400 transition block">
                                                    {{ $showroom->name }}
                                                </a>
                                                <span class="text-xs font-mono text-slate-500">slug: {{ $showroom->slug }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-white font-medium">{{ $showroom->owner?->name ?? 'No Owner Assigned' }}</div>
                                        <div class="text-xs text-slate-400">{{ $showroom->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-slate-200">{{ $showroom->city }}</div>
                                        <div class="text-xs text-slate-500">{{ $showroom->state ?? 'IN' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                            {{ $showroom->memberships_count }} Members
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($showroom->status === 'active')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                                Active
                                            </span>
                                        @elseif($showroom->status === 'trial')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                Trial
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                                Suspended
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('showrooms.show', $showroom) }}" target="_blank" class="p-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white transition" title="View Public Storefront">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                            <a href="{{ route('admin.showrooms.show', $showroom) }}" class="px-3 py-1.5 rounded-lg bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 text-xs font-semibold transition">
                                                Manage
                                            </a>
                                            <a href="{{ route('admin.showrooms.edit', $showroom) }}" class="p-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white transition" title="Edit Showroom Settings">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                        No showrooms match your query.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($showrooms->hasPages())
                    <div class="px-6 py-4 border-t border-slate-800">
                        {{ $showrooms->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
