<x-app-layout title="Showrooms Management - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Header & Actions -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <h1 class="text-3xl font-black font-display text-slate-900 tracking-tight">Showroom Tenants</h1>
                    <p class="text-sm text-slate-500 mt-1">Manage all independent dealership accounts, domains, statuses, and staff allocations.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.showrooms.create') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xs transition flex items-center gap-2" id="btn-create-showroom-top">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Provision New Showroom</span>
                    </a>
                </div>
            </div>

            <!-- Metric Stat Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Total Showrooms</span>
                    <p class="text-3xl font-extrabold font-display text-slate-900 mt-1">{{ $stats['total'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-emerald-600 tracking-wider">Active</span>
                    <p class="text-3xl font-extrabold font-display text-emerald-600 mt-1">{{ $stats['active'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-amber-600 tracking-wider">Trial Tier</span>
                    <p class="text-3xl font-extrabold font-display text-amber-600 mt-1">{{ $stats['trial'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-rose-600 tracking-wider">Suspended</span>
                    <p class="text-3xl font-extrabold font-display text-rose-600 mt-1">{{ $stats['suspended'] }}</p>
                </div>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('admin.showrooms.index') }}" class="flex flex-col md:flex-row items-center gap-4">
                    <div class="flex-grow w-full">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by showroom name, slug, email, or city..."
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <div class="w-full md:w-56 shrink-0">
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <button type="submit" class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm shadow-xs transition">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.showrooms.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-sm transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Showrooms Data Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Showroom</th>
                                <th class="px-6 py-4">Owner / Contact</th>
                                <th class="px-6 py-4">Location</th>
                                <th class="px-6 py-4">Staff Count</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($showrooms as $showroom)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white shrink-0 shadow-xs"
                                                 style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                                                {{ strtoupper(substr($showroom->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.showrooms.show', $showroom) }}" class="font-bold text-slate-900 hover:text-rose-600 transition block">
                                                    {{ $showroom->name }}
                                                </a>
                                                <span class="text-xs font-mono text-slate-400">slug: {{ $showroom->slug }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-slate-900 font-medium">{{ $showroom->owner?->name ?? 'No Owner Assigned' }}</div>
                                        <div class="text-xs text-slate-500">{{ $showroom->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-slate-700">{{ $showroom->city }}</div>
                                        <div class="text-xs text-slate-400">{{ $showroom->state ?? 'IN' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $showroom->memberships_count }} Members
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($showroom->status === 'active')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Active
                                            </span>
                                        @elseif($showroom->status === 'trial')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Trial
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                Suspended
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('showrooms.show', $showroom) }}" target="_blank" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-600 hover:text-slate-900 transition" title="View Public Storefront">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                            <a href="{{ route('admin.showrooms.show', $showroom) }}" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold shadow-xs transition">
                                                Manage
                                            </a>
                                            <a href="{{ route('admin.showrooms.edit', $showroom) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-600 hover:text-slate-900 transition" title="Edit Showroom Settings">
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
                    <div class="px-6 py-4 border-t border-slate-200 bg-white">
                        {{ $showrooms->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
    @include('admin.partials.nav-close')
</x-app-layout>
