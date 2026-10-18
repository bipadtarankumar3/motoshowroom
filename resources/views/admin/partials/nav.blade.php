<nav class="border-b border-slate-200 bg-white/95 backdrop-blur sticky top-0 z-40 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Brand & Badging -->
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 group">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-rose-600 to-rose-500 flex items-center justify-center text-white shadow-md shadow-rose-600/25 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                    <div>
                        <span class="font-display font-black text-lg tracking-tight text-slate-900 block leading-none">MOTO<span class="text-rose-600">SHOWROOM</span></span>
                        <span class="text-[10px] font-bold text-rose-600 uppercase tracking-widest block">Super Admin</span>
                    </div>
                </a>

                <!-- Admin Navigation Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('admin.dashboard') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.dashboard*') ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.showrooms.index') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.showrooms*') ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Showrooms
                    </a>
                    <a href="{{ route('admin.plans.index') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.plans*') ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Plans
                    </a>
                    <a href="{{ route('admin.subscriptions.index') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.subscriptions*') ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Subscriptions
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.users*') ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Users
                    </a>
                    <a href="{{ route('admin.analytics.index') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.analytics*') ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Analytics
                    </a>
                    <a href="{{ route('admin.audit-logs.index') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.audit-logs*') ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Audit Logs
                    </a>
                    <a href="{{ route('admin.settings.index') }}"
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.settings*') ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Settings
                    </a>
                </div>
            </div>

            <!-- Right Profile & Utilities -->
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="hidden sm:flex items-center gap-1.5 text-xs text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>View Portal</span>
                </a>

                <a href="{{ route('profile') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-xs font-medium text-slate-700 transition shadow-2xs">
                    <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-slate-100 transition" title="Sign Out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Nav Menu Bar -->
        <div class="md:hidden flex items-center space-x-1 overflow-x-auto py-2 border-t border-slate-200 text-xs">
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.dashboard*') ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-600' }}">Dashboard</a>
            <a href="{{ route('admin.showrooms.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.showrooms*') ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-600' }}">Showrooms</a>
            <a href="{{ route('admin.plans.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.plans*') ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-600' }}">Plans</a>
            <a href="{{ route('admin.subscriptions.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.subscriptions*') ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-600' }}">Subscriptions</a>
            <a href="{{ route('admin.users.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.users*') ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-600' }}">Users</a>
            <a href="{{ route('admin.audit-logs.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.audit-logs*') ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-600' }}">Audit Logs</a>
            <a href="{{ route('admin.settings.index') }}" class="px-3 py-1.5 rounded-lg {{ request()->routeIs('admin.settings*') ? 'bg-rose-50 text-rose-600 font-bold' : 'text-slate-600' }}">Settings</a>
        </div>
    </div>
</nav>
