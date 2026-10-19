{{-- ============================================================
     SHOWROOM MANAGE SIDEBAR — Catalyst-style fixed left sidebar
     ============================================================ --}}
<div class="manage-shell" x-data="{ open: false, sidebarCollapsed: false, topUserMenuOpen: false }">

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
        id="manage-sidebar"
        class="fixed {{ session()->has('admin_impersonator_id') ? 'top-11' : 'top-0' }} bottom-0 left-0 z-40 flex w-64 flex-col bg-white border-r border-slate-200/80 shadow-xs
               transition-all duration-300 ease-in-out
               -translate-x-full lg:translate-x-0"
        :class="open ? '!translate-x-0' : (sidebarCollapsed ? 'lg:!-translate-x-full' : 'lg:translate-x-0')"
        x-cloak
    >
        {{-- Brand / Showroom Header (Catalyst-style) --}}
        <div class="flex items-center justify-between h-18 px-4 border-b border-slate-100">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="flex items-center gap-3 min-w-0 group">
                @if($showroom->logo_url)
                    <img src="{{ $showroom->logo_url }}" alt="{{ $showroom->name }}"
                         class="w-10 h-10 rounded-2xl object-contain bg-white border border-slate-200 shadow-xs group-hover:scale-105 transition-transform flex-shrink-0 p-1">
                @else
                    <span class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-white text-base shadow-md group-hover:scale-105 transition-transform flex-shrink-0"
                          style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                        {{ strtoupper(substr($showroom->name, 0, 1)) }}
                    </span>
                @endif
                <div class="min-w-0 leading-tight">
                    <span class="font-display font-black text-sm tracking-tight text-slate-900 block truncate">{{ $showroom->name }}</span>
                    <span class="text-[11px] text-slate-400 font-medium block truncate">{{ $showroom->city ?? 'Back-Office' }} Dealership</span>
                </div>
            </a>
            <a href="{{ route('showrooms.show', $showroom) }}" target="_blank" title="View Storefront" class="text-slate-400 hover:text-slate-600 transition p-1 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-0.5">

            {{-- MAIN section --}}
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 pt-2 pb-1.5">Main</p>

            @php
                $primaryColor = $showroom->brand_color_primary ?? '#dc2626';
                $manageMainLinks = [
                    ['route' => 'showrooms.manage', 'label' => 'Overview', 'params' => [$showroom], 'pattern' => 'showrooms.manage',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'],
                    ['route' => 'showrooms.manage.bikes.index', 'label' => 'Motorcycles', 'params' => [$showroom], 'pattern' => 'showrooms.manage.bikes*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>'],
                    ['route' => 'showrooms.manage.categories.index', 'label' => 'Category Master', 'params' => [$showroom], 'pattern' => 'showrooms.manage.categories*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>'],
                    ['route' => 'showrooms.manage.enquiries.index', 'label' => 'Enquiries & Leads', 'params' => [$showroom], 'pattern' => 'showrooms.manage.enquiries*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>'],
                    ['route' => 'showrooms.manage.services.index', 'label' => 'Workshop & Service', 'params' => [$showroom], 'pattern' => 'showrooms.manage.services*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                    ['route' => 'showrooms.manage.careers.index', 'label' => 'Careers & ATS', 'params' => [$showroom], 'pattern' => ['showrooms.manage.careers*', 'showrooms.manage.applicants*'],
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>'],
                    ['route' => 'showrooms.manage.campaigns.index', 'label' => 'Scratch Cards', 'params' => [$showroom], 'pattern' => 'showrooms.manage.campaigns*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>'],
                    ['route' => 'showrooms.manage.ai.index', 'label' => 'AI Concierge', 'params' => [$showroom], 'pattern' => 'showrooms.manage.ai*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>'],
                    ['route' => 'showrooms.manage.analytics.index', 'label' => 'Analytics', 'params' => [$showroom], 'pattern' => 'showrooms.manage.analytics*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
                ];

                $manageOperationsLinks = [
                    ['route' => 'showrooms.manage.payments.index', 'label' => 'Payments', 'params' => [$showroom], 'pattern' => 'showrooms.manage.payments*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
                    ['route' => 'showrooms.billing.index', 'label' => 'Subscription', 'params' => [$showroom], 'pattern' => 'showrooms.billing*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>'],
                    ['route' => 'showrooms.manage.website', 'label' => 'Website Builder', 'params' => [$showroom], 'pattern' => 'showrooms.manage.website*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'],
                    ['route' => 'showrooms.manage.banners.index', 'label' => 'Hero Banners', 'params' => [$showroom], 'pattern' => 'showrooms.manage.banners*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
                    // ['route' => 'showrooms.manage.email-previews.index', 'label' => 'Email Templates', 'params' => [$showroom], 'pattern' => 'showrooms.manage.email-previews*',
                    //  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
                ];
            @endphp

            @foreach($manageMainLinks as $link)
                @php
                    $patterns = is_array($link['pattern']) ? $link['pattern'] : [$link['pattern']];
                    $active = request()->routeIs(...$patterns);
                @endphp
                <a href="{{ route($link['route'], $link['params']) }}"
                   class="group relative flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-medium transition-all
                          {{ $active ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    @if($active)
                        <span class="absolute -left-3 inset-y-1.5 w-1 rounded-r-full" style="background-color: {{ $primaryColor }}"></span>
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

            {{-- OPERATIONS section (Catalyst-style with + button) --}}
            <div class="pt-4 pb-1.5 px-3 flex items-center justify-between">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Operations</p>
                <a href="{{ route('showrooms.manage.bikes.create', $showroom) }}" title="Add New Bike" class="text-slate-400 hover:text-slate-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </a>
            </div>

            @foreach($manageOperationsLinks as $link)
                @php $active = request()->routeIs($link['pattern']); @endphp
                <a href="{{ route($link['route'], $link['params']) }}"
                   class="group relative flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] font-medium transition-all
                          {{ $active ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    @if($active)
                        <span class="absolute -left-3 inset-y-1.5 w-1 rounded-r-full" style="background-color: {{ $primaryColor }}"></span>
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

            {{-- Storefront Link --}}
            <div class="mt-2 pt-2 border-t border-slate-100">
                <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition group">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>View Public Storefront</span>
                </a>
            </div>
        </nav>

        {{-- Bottom Utility Links (Settings & Support like Catalyst) --}}
        <div class="px-3 pt-2 pb-1 border-t border-slate-100 space-y-0.5">
            @php $settingsActive = request()->routeIs('showrooms.manage.settings*'); @endphp
            <a href="{{ route('showrooms.manage.settings', $showroom) }}"
               class="group relative flex items-center justify-between px-3 py-2 rounded-xl text-[13px] font-medium transition
                      {{ $settingsActive ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                @if($settingsActive)
                    <span class="absolute -left-3 inset-y-1 w-1 rounded-r-full" style="background-color: {{ $primaryColor }}"></span>
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
                    id="btn-manage-user-menu"
                >
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-white text-xs flex-shrink-0 shadow-xs ring-2 ring-white"
                             style="background-color: {{ $primaryColor }}">
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
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-orange-600 hover:bg-orange-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Super Admin Console</span>
                        </a>
                    @endif
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
                    id="btn-manage-toggle-sidebar"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    <span class="hidden md:inline-block text-xs font-semibold text-slate-500 group-hover:text-slate-700" x-text="sidebarCollapsed ? 'Show Panel' : 'Hide Panel'"></span>
                </button>

                <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

                {{-- Showroom Identity Breadcrumb --}}
                <a href="{{ route('showrooms.manage', $showroom) }}" class="flex items-center gap-2.5 group">
                    @if($showroom->logo_url)
                        <img src="{{ $showroom->logo_url }}" alt="{{ $showroom->name }}"
                             class="w-7 h-7 rounded-lg object-contain bg-white border border-slate-200 shadow-2xs group-hover:scale-105 transition-transform flex-shrink-0 p-0.5">
                    @else
                        <span class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-white text-xs flex-shrink-0 shadow-2xs group-hover:scale-105 transition-transform"
                              style="background-color: {{ $primaryColor }}">
                            {{ strtoupper(substr($showroom->name, 0, 1)) }}
                        </span>
                    @endif
                    <div class="leading-tight">
                        <span class="font-display font-black text-sm tracking-tight text-slate-900 block truncate group-hover:text-slate-700 transition">{{ $showroom->name }}</span>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block">Back-Office</span>
                    </div>
                </a>
            </div>

            {{-- Right-side Actions & User Dropdown --}}
            <div class="flex items-center gap-2.5 sm:gap-3" x-data="{ userMenuOpen: false }">
                {{-- Subscription Expiry Date Badge & Link to Subscription / Billing --}}
                @php
                    $sub = $showroom->subscription;
                    $isExpired = $sub ? $sub->isExpired() : true;
                    $isTrial = $sub ? $sub->isTrial() : false;
                    $expiryDate = $sub?->current_period_end ? $sub->current_period_end->format('d M Y') : 'No Plan';
                    $daysRemaining = $sub ? $sub->days_remaining : 0;
                @endphp
                <a href="{{ route('showrooms.billing.index', $showroom) }}"
                   id="header-subscription-expiry-link"
                   title="Subscription Status: Click to view details and upgrade plans"
                   class="group relative inline-flex items-center gap-2 px-3 py-1.5 rounded-2xl border transition shadow-2xs text-xs font-medium cursor-pointer
                          {{ $isExpired 
                              ? 'bg-rose-50/90 hover:bg-rose-100/90 border-rose-200 text-rose-800' 
                              : ($isTrial 
                                  ? 'bg-amber-50/90 hover:bg-amber-100/90 border-amber-200/90 text-amber-900' 
                                  : 'bg-emerald-50/90 hover:bg-emerald-100/90 border-emerald-200/90 text-emerald-900') }}">
                    
                    {{-- Status Icon / Clock --}}
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0
                                {{ $isExpired 
                                    ? 'bg-rose-100 text-rose-600' 
                                    : ($isTrial ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                        @if($isExpired)
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($isTrial)
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @endif
                    </div>

                    <div class="flex flex-col text-left leading-tight pr-1">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider opacity-75">
                                {{ $isTrial ? 'Trial Expiry' : ($isExpired ? 'Expired' : 'Valid Until') }}
                            </span>
                            @if(! $isExpired)
                                <span class="w-1.5 h-1.5 rounded-full {{ $isTrial ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="font-extrabold text-xs tracking-tight text-slate-900 group-hover:text-slate-950 font-mono">{{ $expiryDate }}</span>
                            @if(! $isExpired && $daysRemaining <= 14)
                                <span class="text-[10px] font-bold text-amber-700">({{ $daysRemaining }}d left)</span>
                            @endif
                        </div>
                    </div>

                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 transition shrink-0 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                   class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition border border-transparent hover:border-slate-200">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Storefront</span>
                </a>

                {{-- User Icon & Dropdown --}}
                <div class="relative">
                    <button
                        @click="userMenuOpen = !userMenuOpen"
                        id="btn-manage-header-user-menu"
                        class="flex items-center gap-2.5 p-1.5 sm:px-3 sm:py-1.5 rounded-2xl border border-slate-200/80 bg-white hover:bg-slate-50 hover:border-slate-300 transition shadow-2xs group cursor-pointer"
                    >
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-white text-xs shadow-xs flex-shrink-0 group-hover:scale-105 transition-transform"
                             style="background-color: {{ $primaryColor }}">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left leading-tight">
                            <span class="text-xs font-bold text-slate-900 block truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block">Showroom Staff</span>
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
                            <span class="inline-flex mt-1.5 items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                {{ $showroom->name }}
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

                            <a href="{{ route('showrooms.show', $showroom) }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition font-medium">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span>Public Storefront</span>
                            </a>

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-orange-600 hover:bg-orange-50 transition font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>Super Admin Console</span>
                                </a>
                            @endif

                            @if(session()->has('admin_impersonator_id'))
                                <form method="POST" action="{{ route('impersonate.leave') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-amber-700 hover:bg-amber-50 transition font-semibold text-left cursor-pointer">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                        <span>Return to Admin Dashboard</span>
                                    </button>
                                </form>
                            @endif
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
