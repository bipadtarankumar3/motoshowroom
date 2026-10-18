<x-app-layout :title="$user->name . ' - User Details | MotoShowroom SaaS'">
    <div class="min-h-screen bg-slate-950 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-8">

            <!-- Top Navigation Bar -->
            <header class="flex items-center justify-between border-b border-slate-800/80 pb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 p-0.5 shadow-lg shadow-rose-600/20 group-hover:scale-105 transition">
                            <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                        </div>
                        <span class="font-extrabold font-display tracking-tight text-lg text-white">
                            MOTO<span class="text-rose-500">SHOWROOM</span>
                        </span>
                    </a>
                </div>

                <div class="flex items-center gap-4 text-sm font-medium">
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700/80 text-slate-200 transition flex items-center gap-2" id="nav-admin-users">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <span>Manage Users</span>
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold transition shadow-md shadow-rose-600/20 flex items-center gap-1.5" id="nav-admin-create-user">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Create User</span>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900/80 hover:bg-rose-950/40 hover:text-rose-400 border border-slate-800 text-slate-400 transition text-sm" id="btn-logout">
                            Sign Out
                        </button>
                    </form>
                </div>
            </header>

            <!-- User Profile Header Card -->
            <div class="glass-panel-dark rounded-3xl p-8 border border-slate-800/80 shadow-2xl relative overflow-hidden backdrop-blur-xl">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-rose-600/10 blur-[90px] rounded-full pointer-events-none"></div>

                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative">
                    <!-- Left: Avatar & Primary Info -->
                    <div class="flex items-center gap-6">
                        <div class="w-24 h-24 rounded-3xl bg-gradient-to-tr from-rose-600 via-amber-500 to-rose-400 p-1 shadow-2xl shadow-rose-600/30 flex-shrink-0">
                            <div class="w-full h-full bg-slate-950 rounded-[22px] flex items-center justify-center text-3xl font-black text-rose-400 font-display uppercase tracking-wider">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">{{ $user->name }}</h1>

                                <!-- Role Badge -->
                                @if ($user->isAdmin())
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        Admin
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                        User
                                    </span>
                                @endif

                                <!-- Status Badge -->
                                @if ($user->status === 'active')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Active
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> {{ ucfirst($user->status) }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm text-slate-400 mt-1 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ $user->email }}</span>
                            </p>

                            <p class="text-xs text-slate-500 mt-2">
                                Member since <span class="text-slate-300 font-medium">{{ $user->created_at->format('M d, Y') }}</span> ({{ $user->created_at->diffForHumans() }})
                            </p>
                        </div>
                    </div>

                    <!-- Right: Action Buttons -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('users.edit', $user) }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-semibold text-sm shadow-lg shadow-rose-600/30 transition flex items-center gap-2" id="btn-edit-user">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit Details</span>
                        </a>

                        @if (auth()->user()->isAdmin() && !$isOwnProfile)
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700 text-slate-300 text-sm font-medium transition">
                                All Users
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detailed Grid: Information Sections -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Left Column: Contact & Location (2 cols) -->
                <div class="md:col-span-2 space-y-6">
                    <div class="glass-panel-dark rounded-3xl p-6 border border-slate-800/80 shadow-xl space-y-5">
                        <h2 class="text-lg font-bold font-display text-white border-b border-slate-800 pb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Personal & Contact Information</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
                                <span class="text-xs text-slate-400 uppercase font-semibold tracking-wider block">Full Name</span>
                                <span class="text-base text-white font-medium mt-1 block">{{ $user->name }}</span>
                            </div>

                            <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
                                <span class="text-xs text-slate-400 uppercase font-semibold tracking-wider block">Email Address</span>
                                <span class="text-base text-white font-medium mt-1 block">{{ $user->email }}</span>
                            </div>

                            <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
                                <span class="text-xs text-slate-400 uppercase font-semibold tracking-wider block">Phone Number</span>
                                <span class="text-base text-white font-medium mt-1 block">{{ $user->phone ?: 'Not provided' }}</span>
                            </div>

                            <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
                                <span class="text-xs text-slate-400 uppercase font-semibold tracking-wider block">City</span>
                                <span class="text-base text-white font-medium mt-1 block">{{ $user->city ?: 'Not provided' }}</span>
                            </div>

                            <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800 sm:col-span-2">
                                <span class="text-xs text-slate-400 uppercase font-semibold tracking-wider block">Address / Location</span>
                                <span class="text-base text-white font-medium mt-1 block">{{ $user->address ?: 'Not provided' }}</span>
                            </div>
                        </div>

                        <!-- Biography / Notes -->
                        <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
                            <span class="text-xs text-slate-400 uppercase font-semibold tracking-wider block mb-2">Showroom Notes & Biography</span>
                            <p class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                                {{ $user->bio ?: 'No biography or showroom notes added yet.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Account Specifications & System Role -->
                <div class="space-y-6">
                    <div class="glass-panel-dark rounded-3xl p-6 border border-slate-800/80 shadow-xl space-y-5">
                        <h2 class="text-lg font-bold font-display text-white border-b border-slate-800 pb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Account Details</span>
                        </h2>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center py-2 border-b border-slate-800/60">
                                <span class="text-slate-400">User ID</span>
                                <span class="font-mono text-slate-200">#{{ $user->id }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-800/60">
                                <span class="text-slate-400">Assigned Role</span>
                                <span class="font-semibold {{ $user->isAdmin() ? 'text-rose-400' : 'text-sky-400' }} uppercase text-xs">
                                    {{ $user->role }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-800/60">
                                <span class="text-slate-400">Account Status</span>
                                <span class="font-semibold text-emerald-400 uppercase text-xs">
                                    {{ $user->status }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-800/60">
                                <span class="text-slate-400">Profile URL</span>
                                <span class="font-mono text-[11px] text-slate-400">/users/{{ $user->id }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2">
                                <span class="text-slate-400">Last Updated</span>
                                <span class="text-slate-300 text-xs">{{ $user->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-800">
                            <p class="text-xs text-slate-400">
                                @if ($user->isAdmin())
                                    <strong class="text-rose-400">Administrator Privileges:</strong> Full access to manage platform users, create new users, and configure SaaS showroom settings.
                                @else
                                    <strong class="text-sky-400">Standard User:</strong> Access to personal showroom profile, enquiries, and service reservations.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
