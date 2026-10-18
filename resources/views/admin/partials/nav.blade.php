{{-- ============================================================
     ADMIN SIDEBAR — Catalyst-style fixed left sidebar
     ============================================================ --}}
<div class="admin-shell" x-data="{ open: false, sidebarCollapsed: false, topUserMenuOpen: false }">

    {{-- Backdrop (mobile) --}}
    <div
        class="fixed inset-0 z-30 bg-slate-900/40 backdrop-blur-sm lg:hidden"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        x-cloak
    ></div>

    {{-- ═══ SIDEBAR ═══ --}}
    <aside
        id="admin-sidebar"
        class="fixed {{ session()->has('admin_impersonator_id') ? 'top-11' : 'top-0' }} bottom-0 left-0 z-40 flex w-64 flex-col bg-white border-r border-slate-200/80 shadow-xs
               transition-all duration-300 ease-in-out
               -translate-x-full lg:translate-x-0"
        :class="open ? '!translate-x-0' : (sidebarCollapsed ? 'lg:!-translate-x-full' : 'lg:translate-x-0')"
        x-cloak
    >
        {{-- Brand / Workspace Header (Catalyst-style) --}}
        <div class="flex items-center justify-between h-18 px-4 border-b border-slate-100">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 min-w-0 group">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center text-white shadow-md shadow-orange-500/25 group-hover:scale-105 transition-transform flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                <div class="min-w-0 leading-tight">
                    <span class="font-display font-black text-sm tracking-tight text-slate-900 block truncate">MotoShowroom</span>
                    <span class="text-[11px] text-slate-400 font-medium block truncate">Super Admin Console</span>
                </div>
            </a>
            <div class="text-slate-400 hover:text-slate-600 transition p-1 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-0.5">

            {{-- MAIN section --}}
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 pt-2 pb-1.5">Main</p>

            @php
                $adminMainLinks = [
                    ['route' => 'admin.dashboard', 'label' => 'Overview', 'pattern' => 'admin.dashboard*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'],
                    ['route' => 'admin.analytics.index', 'label' => 'Analytics', 'pattern' => 'admin.analytics*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
                    ['route' => 'admin.showrooms.index', 'label' => 'Showrooms', 'pattern' => 'admin.showrooms*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
                    ['route' => 'admin.users.index', 'label' => 'Users', 'pattern' => 'admin.users*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>'],
                    ['route' => 'admin.plans.index', 'label' => 'Plans & Pricing', 'pattern' => 'admin.plans*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
                    ['route' => 'admin.subscriptions.index', 'label' => 'Subscriptions', 'pattern' => 'admin.subscriptions*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
                    ['route' => 'admin.audit-logs.index', 'label' => 'Audit Logs', 'pattern' => 'admin.audit-logs*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
                ];
            @endphp

            @foreach($adminMainLinks as $link)
                @php $active = request()->routeIs($link['pattern']); @endphp
                <a href="{{ route($link['route']) }}"
                   class="group relative flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-medium transition-all
                          {{ $active ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    @if($active)
                        <span class="absolute -left-3 inset-y-1.5 w-1 rounded-r-full bg-orange-500"></span>
                    @endif
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 {{ $active ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $link['icon'] !!}
                        </svg>
                        <span class="truncate">{{ $link['label'] }}</span>
                    </div>
                    @if($active)
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    @endif
                </a>
            @endforeach

            {{-- QUICK ACTIONS section --}}
            <div class="pt-4 pb-1.5 px-3 flex items-center justify-between">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Quick Actions</p>
                <a href="{{ route('admin.showrooms.create') }}" title="Provision Showroom" class="text-slate-400 hover:text-orange-600 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </a>
            </div>

            <a href="{{ route('admin.showrooms.create') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition group">
                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>New Showroom</span>
            </a>

            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition group">
                <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Live Portal</span>
            </a>
        </nav>

        {{-- Bottom Utility Links (Settings & Support like Catalyst) --}}
        <div class="px-3 pt-2 pb-1 border-t border-slate-100 space-y-0.5">
            @php $settingsActive = request()->routeIs('admin.settings*'); @endphp
            <a href="{{ route('admin.settings.index') }}"
               class="group relative flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition
                      {{ $settingsActive ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                @if($settingsActive)
                    <span class="absolute -left-3 inset-y-1 w-1 rounded-r-full bg-orange-500"></span>
                @endif
                <div class="flex items-center gap-3">
                    <svg class="w-[18px] h-[18px] {{ $settingsActive ? 'text-slate-900' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Settings</span>
                </div>
                @if($settingsActive)
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @endif
            </a>

            <a href="mailto:support@motoshowroom.com"
               class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition group">
                <svg class="w-[18px] h-[18px] text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824-2.126a5 5 0 017.072 0"/></svg>
                <span>Support</span>
            </a>
        </div>

        {{-- User Profile Card (Catalyst style with avatar, verified badge, email, dropdown) --}}
        <div class="border-t border-slate-100 p-3" x-data="{ userMenuOpen: false }">
            <div class="relative">
                <button
                    @click="userMenuOpen = !userMenuOpen"
                    class="w-full flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 transition group text-left"
                    id="btn-admin-user-menu"
                >
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center font-bold text-white text-xs flex-shrink-0 shadow-xs ring-2 ring-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1">
                                <p class="text-[13px] font-semibold text-slate-900 leading-tight truncate">{{ auth()->user()->name }}</p>
                                {{-- Verified Blue Checkmark --}}
                                <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 flex-shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>

                {{-- Popup Menu --}}
                <div
                    x-show="userMenuOpen"
                    @click.away="userMenuOpen = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                    class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 text-xs"
                    x-cloak
                >
                    <a href="{{ route('profile') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Account Profile</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-rose-600 hover:bg-rose-50 transition text-left">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- ═══ MAIN CONTENT CONTAINER (offsets sidebar on desktop when not collapsed) ═══ --}}
    <div class="flex flex-col min-h-screen lg:pl-64 transition-all duration-300 ease-in-out"
         :class="sidebarCollapsed ? 'lg:!pl-0' : 'lg:pl-64'">

        {{-- Modern Sticky Top Navbar / Header (Desktop & Mobile) --}}
        <header class="sticky {{ session()->has('admin_impersonator_id') ? 'top-11' : 'top-0' }} z-30 flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-2xs">
            <div class="flex items-center gap-3">
                {{-- Toggle Side Panel button --}}
                <button
                    @click="if (window.innerWidth >= 1024) { sidebarCollapsed = !sidebarCollapsed; } else { open = !open; }"
                    class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition flex items-center gap-2 group cursor-pointer"
                    title="Toggle Side Panel (Show/Hide)"
                    id="btn-admin-toggle-sidebar"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    <span class="hidden md:inline-block text-xs font-semibold text-slate-500 group-hover:text-slate-700" x-text="sidebarCollapsed ? 'Show Panel' : 'Hide Panel'"></span>
                </button>

                <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

                {{-- Admin Context Badge --}}
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                    <span class="text-xs font-black uppercase tracking-wider text-slate-700">Super Admin Console</span>
                </div>
            </div>

            {{-- Right-side User Icon with Dropdown Menu --}}
            <div class="flex items-center gap-3" x-data="{ userMenuOpen: false }">
                <a href="{{ route('home') }}" target="_blank"
                   class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition border border-transparent hover:border-slate-200">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Live Portal</span>
                </a>

                {{-- User Icon & Dropdown --}}
                <div class="relative">
                    <button
                        @click="userMenuOpen = !userMenuOpen"
                        id="btn-admin-header-user-menu"
                        class="flex items-center gap-2.5 p-1.5 sm:px-3 sm:py-1.5 rounded-2xl border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-slate-300 transition shadow-2xs group cursor-pointer"
                    >
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center font-bold text-white text-xs shadow-xs flex-shrink-0 group-hover:scale-105 transition-transform">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left leading-tight">
                            <span class="text-xs font-bold text-slate-900 block truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] font-semibold text-orange-600 uppercase tracking-wider block">Admin</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown Card --}}
                    <div
                        x-show="userMenuOpen"
                        @click.away="userMenuOpen = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                        class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50 text-xs"
                        x-cloak
                    >
                        {{-- User Header Details --}}
                        <div class="px-4 py-2.5 border-b border-slate-100">
                            <p class="font-bold text-slate-900 text-sm truncate">{{ auth()->user()->name }}</p>
                            <p class="text-slate-500 text-[11px] truncate">{{ auth()->user()->email }}</p>
                            <span class="inline-flex mt-1.5 items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200">
                                Super Administrator
                            </span>
                        </div>

                        {{-- Links --}}
                        <div class="py-1">
                            <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition font-medium">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>View Profile & Details</span>
                            </a>

                            {{-- Side Panel Toggle (vide panel) --}}
                            <button
                                type="button"
                                @click="sidebarCollapsed = !sidebarCollapsed; userMenuOpen = false"
                                class="w-full flex items-center justify-between px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition font-medium text-left cursor-pointer"
                            >
                                <div class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                    <span x-text="sidebarCollapsed ? 'Show Side Panel' : 'Hide Side Panel'"></span>
                                </div>
                                <span class="text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">Toggle</span>
                            </button>

                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition font-medium">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                <span>Admin Dashboard</span>
                            </a>
                        </div>

                        {{-- Logout --}}
                        <div class="border-t border-slate-100 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-rose-600 hover:bg-rose-50 transition font-semibold text-left cursor-pointer">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page view content continues below --}}
