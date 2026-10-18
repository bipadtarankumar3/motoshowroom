<x-app-layout :title="$user->name . ' - User Details | MotoShowroom SaaS'">
    <div class="min-h-screen bg-slate-50 text-slate-900 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-8">

            <!-- Top Navigation Bar -->
            <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 p-0.5 shadow-xs group-hover:scale-105 transition">
                            <div class="w-full h-full bg-white rounded-[10px] flex items-center justify-center">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                        </div>
                        <span class="font-extrabold font-display tracking-tight text-lg text-slate-900">
                            MOTO<span class="text-rose-600">SHOWROOM</span>
                        </span>
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-3 text-sm font-medium">
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-rose-600 font-semibold transition flex items-center gap-2 shadow-xs" id="nav-admin-dashboard">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Super Admin</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-semibold transition flex items-center gap-2 shadow-xs" id="nav-admin-users">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <span>Manage Users</span>
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold transition shadow-xs flex items-center gap-1.5" id="nav-admin-create-user">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Create User</span>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 border border-slate-300 text-slate-600 transition text-sm font-medium shadow-xs" id="btn-logout">
                            Sign Out
                        </button>
                    </form>
                </div>
            </header>

            <!-- User Profile Header Card -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xs relative overflow-hidden">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative">
                    <!-- Left: Avatar & Primary Info -->
                    <div class="flex items-center gap-6">
                        <div class="w-24 h-24 rounded-3xl bg-rose-50 border-2 border-rose-200 flex items-center justify-center text-3xl font-black text-rose-600 font-display uppercase tracking-wider shadow-xs flex-shrink-0">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">{{ $user->name }}</h1>

                                <!-- Role Badge -->
                                @if ($user->isAdmin())
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                        Admin
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                        User
                                    </span>
                                @endif

                                <!-- Status Badge -->
                                @if ($user->status === 'active')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> {{ ucfirst($user->status) }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm text-slate-600 mt-1 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ $user->email }}</span>
                            </p>

                            <p class="text-xs text-slate-500 mt-2">
                                Member since <span class="text-slate-700 font-semibold">{{ $user->created_at->format('M d, Y') }}</span> ({{ $user->created_at->diffForHumans() }})
                            </p>
                        </div>
                    </div>

                    <!-- Right: Action Buttons -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('users.edit', $user) }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-semibold text-sm shadow-xs transition flex items-center gap-2" id="btn-edit-user">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit Details</span>
                        </a>

                        @if (auth()->user()->isAdmin() && !$isOwnProfile)
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-sm font-semibold transition shadow-xs">
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
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-5">
                        <h2 class="text-lg font-bold font-display text-slate-900 border-b border-slate-200 pb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Personal & Contact Information</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                <span class="text-xs text-slate-500 uppercase font-bold tracking-wider block">Full Name</span>
                                <span class="text-base text-slate-900 font-semibold mt-1 block">{{ $user->name }}</span>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                <span class="text-xs text-slate-500 uppercase font-bold tracking-wider block">Email Address</span>
                                <span class="text-base text-slate-900 font-semibold mt-1 block">{{ $user->email }}</span>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                <span class="text-xs text-slate-500 uppercase font-bold tracking-wider block">Phone Number</span>
                                <span class="text-base text-slate-900 font-semibold mt-1 block">{{ $user->phone ?: 'Not provided' }}</span>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                <span class="text-xs text-slate-500 uppercase font-bold tracking-wider block">City</span>
                                <span class="text-base text-slate-900 font-semibold mt-1 block">{{ $user->city ?: 'Not provided' }}</span>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 sm:col-span-2">
                                <span class="text-xs text-slate-500 uppercase font-bold tracking-wider block">Address / Location</span>
                                <span class="text-base text-slate-900 font-semibold mt-1 block">{{ $user->address ?: 'Not provided' }}</span>
                            </div>
                        </div>

                        <!-- Biography / Notes -->
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <span class="text-xs text-slate-500 uppercase font-bold tracking-wider block mb-2">Showroom Notes & Biography</span>
                            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                                {{ $user->bio ?: 'No biography or showroom notes added yet.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Account Specifications & System Role -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-5">
                        <h2 class="text-lg font-bold font-display text-slate-900 border-b border-slate-200 pb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Account Details</span>
                        </h2>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">User ID</span>
                                <span class="font-mono text-slate-700 font-bold">#{{ $user->id }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">Assigned Role</span>
                                <span class="font-bold {{ $user->isAdmin() ? 'text-rose-600' : 'text-sky-600' }} uppercase text-xs">
                                    {{ $user->role }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">Account Status</span>
                                <span class="font-bold text-emerald-600 uppercase text-xs">
                                    {{ $user->status }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">Profile URL</span>
                                <span class="font-mono text-[11px] text-slate-500">/users/{{ $user->id }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2">
                                <span class="text-slate-500">Last Updated</span>
                                <span class="text-slate-700 text-xs font-medium">{{ $user->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <p class="text-xs text-slate-500 leading-relaxed">
                                @if ($user->isAdmin())
                                    <strong class="text-rose-600">Administrator Privileges:</strong> Full access to manage platform users, create new users, and configure SaaS showroom settings.
                                @else
                                    <strong class="text-sky-600">Standard User:</strong> Access to personal showroom profile, enquiries, and service reservations.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
