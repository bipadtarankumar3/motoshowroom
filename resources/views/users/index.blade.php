<x-app-layout title="User Management - Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')
    <div class="min-h-screen bg-slate-950 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Top Header & Actions -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-black font-display text-white tracking-tight">Platform Users</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                            Super Admin
                        </span>
                    </div>
                    <p class="text-sm text-slate-400 mt-1">Manage system administrators, showroom owners, and registered users.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('profile') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-sm font-medium transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>My Details Page</span>
                    </a>

                    <a href="{{ route('admin.users.create') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xl shadow-rose-600/30 transition flex items-center gap-2" id="btn-create-user-top">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Create User</span>
                    </a>
                </div>
            </div>

            <!-- Metric Stat Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800/80 shadow-lg">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Total Users</span>
                    <p class="text-3xl font-extrabold font-display text-white mt-1">{{ $stats['total'] }}</p>
                </div>

                <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800/80 shadow-lg">
                    <span class="text-xs uppercase font-bold text-rose-400 tracking-wider">Administrators</span>
                    <p class="text-3xl font-extrabold font-display text-rose-400 mt-1">{{ $stats['admins'] }}</p>
                </div>

                <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800/80 shadow-lg">
                    <span class="text-xs uppercase font-bold text-sky-400 tracking-wider">Regular Users</span>
                    <p class="text-3xl font-extrabold font-display text-sky-400 mt-1">{{ $stats['regular_users'] }}</p>
                </div>

                <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800/80 shadow-lg">
                    <span class="text-xs uppercase font-bold text-emerald-400 tracking-wider">Active Status</span>
                    <p class="text-3xl font-extrabold font-display text-emerald-400 mt-1">{{ $stats['active'] }}</p>
                </div>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800/80 shadow-lg">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row items-center gap-4">
                    <div class="flex-grow w-full">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by name, email, phone, or city..."
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <select name="role" class="px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                        </select>

                        <select name="status" class="px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>

                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition border border-slate-700">
                            Filter
                        </button>

                        @if (request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('admin.users.index') }}" class="px-3 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 text-xs transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Users Data Table -->
            <div class="glass-panel-dark rounded-3xl border border-slate-800 shadow-xl overflow-hidden backdrop-blur-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-900/80 border-b border-slate-800 text-xs uppercase tracking-wider text-slate-400 font-bold">
                            <tr>
                                <th scope="col" class="py-4 px-6">User</th>
                                <th scope="col" class="py-4 px-6">Role</th>
                                <th scope="col" class="py-4 px-6">Status</th>
                                <th scope="col" class="py-4 px-6">Contact & City</th>
                                <th scope="col" class="py-4 px-6">Registered</th>
                                <th scope="col" class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-medium">
                            @forelse ($users as $u)
                                <tr class="hover:bg-slate-900/40 transition">
                                    <td class="py-4 px-6 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-rose-400 text-sm font-display flex-shrink-0">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('users.show', $u) }}" class="font-bold text-white hover:text-rose-400 transition block">
                                                {{ $u->name }}
                                            </a>
                                            <span class="text-xs text-slate-400">{{ $u->email }}</span>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6">
                                        @if ($u->isAdmin())
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                                Admin
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                                User
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6">
                                        @if ($u->status === 'active')
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1.5 w-max">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Active
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center gap-1.5 w-max">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> {{ ucfirst($u->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 text-xs text-slate-400">
                                        <div>{{ $u->phone ?: 'No phone' }}</div>
                                        <div class="text-slate-500">{{ $u->city ?: 'No city' }}</div>
                                    </td>

                                    <td class="py-4 px-6 text-xs text-slate-400">
                                        {{ $u->created_at->format('M d, Y') }}
                                    </td>

                                    <td class="py-4 px-6 text-right space-x-2">
                                        <a href="{{ route('users.show', $u) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition border border-slate-700" title="Open user details page">
                                            View Details
                                        </a>
                                        <a href="{{ route('users.edit', $u) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-rose-950/40 hover:text-rose-400 text-slate-400 text-xs font-semibold transition border border-slate-800" title="Edit user">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-500 text-sm">
                                        No users found matching the given filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($users->hasPages())
                    <div class="p-4 border-t border-slate-800 bg-slate-900/40">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
