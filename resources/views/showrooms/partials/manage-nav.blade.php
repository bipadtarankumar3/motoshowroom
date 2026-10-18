{{-- ============================================================
     SHOWROOM MANAGE SIDEBAR — Catalyst-style fixed left sidebar
     ============================================================ --}}
<div class="manage-shell" x-data="{ open: false }">

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
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-white border-r border-slate-200 shadow-sm
               transition-transform duration-300 ease-in-out
               lg:translate-x-0"
        :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        x-cloak
    >
        {{-- Brand/Showroom Logo --}}
        <div class="flex items-center gap-3 h-16 px-4 border-b border-slate-100">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="flex items-center gap-2.5 min-w-0 group">
                <span class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-white text-lg shadow-md group-hover:scale-105 transition-transform flex-shrink-0"
                      style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                    {{ strtoupper(substr($showroom->name, 0, 1)) }}
                </span>
                <div class="min-w-0 leading-tight">
                    <span class="font-display font-black text-[13px] tracking-tight text-slate-900 block truncate">{{ $showroom->name }}</span>
                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Back-Office</span>
                </div>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-0.5">

            {{-- MAIN section --}}
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest px-3 pt-1 pb-2">Main</p>

            @php
                $manageLinks = [
                    ['route' => 'showrooms.manage', 'label' => 'Overview', 'params' => [$showroom], 'pattern' => 'showrooms.manage',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                    ['route' => 'showrooms.manage.bikes.index', 'label' => 'Motorcycles', 'params' => [$showroom], 'pattern' => 'showrooms.manage.bikes*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>'],
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
                $operationsLinks = [
                    ['route' => 'showrooms.manage.payments.index', 'label' => 'Payments', 'params' => [$showroom], 'pattern' => 'showrooms.manage.payments*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
                    ['route' => 'showrooms.billing.index', 'label' => 'Subscription', 'params' => [$showroom], 'pattern' => 'showrooms.billing*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>'],
                    ['route' => 'showrooms.manage.website', 'label' => 'Website Builder', 'params' => [$showroom], 'pattern' => 'showrooms.manage.website*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'],
                    ['route' => 'showrooms.manage.email-previews.index', 'label' => 'Email Templates', 'params' => [$showroom], 'pattern' => 'showrooms.manage.email-previews*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
                    ['route' => 'showrooms.manage.settings', 'label' => 'Settings', 'params' => [$showroom], 'pattern' => 'showrooms.manage.settings*',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                ];
            @endphp

            @foreach($manageLinks as $link)
                @php
                    $patterns = is_array($link['pattern']) ? $link['pattern'] : [$link['pattern']];
                    $active = request()->routeIs(...$patterns);
                @endphp
                <a href="{{ route($link['route'], $link['params']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group relative
                          {{ $active ? 'bg-rose-50 text-rose-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    @if($active)
                        <span class="absolute left-0 inset-y-2 w-1 rounded-full" style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}"></span>
                    @endif
                    <svg class="w-[18px] h-[18px] flex-shrink-0 {{ $active ? 'text-rose-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $link['icon'] !!}
                    </svg>
                    <span class="truncate">{{ $link['label'] }}</span>
                </a>
            @endforeach

            {{-- OPERATIONS section --}}
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest px-3 pt-4 pb-2">Operations</p>

            @foreach($operationsLinks as $link)
                @php $active = request()->routeIs($link['pattern']); @endphp
                <a href="{{ route($link['route'], $link['params']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all group relative
                          {{ $active ? 'bg-rose-50 text-rose-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    @if($active)
                        <span class="absolute left-0 inset-y-2 w-1 rounded-full" style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}"></span>
                    @endif
                    <svg class="w-[18px] h-[18px] flex-shrink-0 {{ $active ? 'text-rose-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $link['icon'] !!}
                    </svg>
                    <span class="truncate">{{ $link['label'] }}</span>
                </a>
            @endforeach

            {{-- Storefront link --}}
            <div class="mt-2 pt-3 border-t border-slate-100">
                <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all group">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>View Storefront</span>
                </a>
            </div>
        </nav>

        {{-- User Profile Footer --}}
        <div class="border-t border-slate-100 px-3 py-3">
            <div class="flex items-center justify-between px-2 py-2 rounded-xl hover:bg-slate-50 transition">
                <a href="{{ route('profile') }}" class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-white text-sm flex-shrink-0 shadow-sm"
                         style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900 leading-tight truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" title="Sign Out" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ═══ TOPBAR (mobile hamburger) ═══ --}}
    <div class="lg:pl-64 flex flex-col min-h-screen">
        <header class="sticky top-0 z-20 lg:hidden flex items-center gap-3 h-14 px-4 bg-white border-b border-slate-200 shadow-xs">
            <button @click="open = true" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition" id="btn-manage-open-sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
            </button>
            <a href="{{ route('showrooms.manage', $showroom) }}" class="flex items-center gap-2 min-w-0">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-white text-sm flex-shrink-0"
                      style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                    {{ strtoupper(substr($showroom->name, 0, 1)) }}
                </span>
                <span class="font-display font-black text-sm tracking-tight text-slate-900 truncate">{{ $showroom->name }}</span>
            </a>
        </header>

        {{-- Page content injected here by each view --}}
