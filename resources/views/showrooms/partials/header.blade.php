{{-- ============================================================
     SHOWROOM PUBLIC HEADER — Modern Responsive Motorbike Dealership Navbar
     ============================================================ --}}
<header
    class="border-b border-slate-200/80 bg-white/95 backdrop-blur-xl sticky top-0 z-40 transition-all shadow-xs"
    x-data="{ mobileOpen: false }"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 py-3">

            {{-- ═══ BRAND IDENTITY & LOGO ═══ --}}
            <a href="{{ route('showrooms.show', $showroom) }}" class="flex items-center gap-3.5 group flex-shrink-0">
                @if($showroom->logo_url)
                    <img src="{{ $showroom->logo_url }}" alt="{{ $showroom->name }}"
                         class="h-11 w-auto max-w-[140px] rounded-xl object-contain group-hover:scale-105 transition-transform drop-shadow-xs">
                @else
                    {{-- Motorcycle Dealership Crest / Logo Emblem --}}
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-white text-lg font-display shadow-md shadow-slate-900/10 group-hover:scale-105 transition-transform flex-shrink-0 relative overflow-hidden"
                         style="background: linear-gradient(135deg, {{ $showroom->brand_color_primary ?? '#dc2626' }}, {{ $showroom->brand_color_accent ?? '#991b1b' }})">
                        <div class="absolute -right-2 -bottom-2 w-8 h-8 rounded-full bg-white/10 blur-2xs"></div>
                        <svg class="w-6 h-6 text-white drop-shadow-xs" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.5 8c-1.38 0-2.5 1.12-2.5 2.5 0 .28.05.55.13.8L14.7 13H10l-1.3-2.6A3.49 3.49 0 005.5 9C3.57 9 2 10.57 2 12.5S3.57 16 5.5 16c1.61 0 2.96-1.09 3.37-2.57L11.5 15h4.15l-1.15 2.3a2.5 2.5 0 102.24 1.12L18.4 15.1c.35.13.72.2 1.1.2 1.93 0 3.5-1.57 3.5-3.5S21.43 8 19.5 8zm-14 6c-.83 0-1.5-.67-1.5-1.5S4.67 11 5.5 11s1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm14-1.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                        </svg>
                    </div>
                @endif
                <div class="leading-tight">
                    <p class="text-base sm:text-lg font-black font-display text-slate-900 tracking-tight leading-none group-hover:text-slate-800 transition">
                        {{ $showroom->name }}
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[11px] text-slate-500 font-semibold">{{ $showroom->city ?? 'Motorcycle' }} Dealership</span>
                        <span class="text-slate-300">•</span>
                        @if($showroom->isOpenNow())
                            <span class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded-full border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Open
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] text-amber-700 font-bold bg-amber-50 px-1.5 py-0.5 rounded-full border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Closed
                            </span>
                        @endif
                    </div>
                </div>
            </a>

            {{-- ═══ DESKTOP NAVIGATION ═══ --}}
            <nav class="hidden lg:flex items-center gap-1 xl:gap-1.5">
                @php
                    $isHome = request()->routeIs('showrooms.show');
                    $isInventory = request()->routeIs('showrooms.bikes*');
                    $isTestRide = request()->routeIs('showrooms.enquiries*');
                    $isService = request()->routeIs('showrooms.services*');
                    $isCareers = request()->routeIs('showrooms.careers*');
                    $isPromotions = request()->routeIs('showrooms.promotions*');
                    $primaryColor = $showroom->brand_color_primary ?? '#dc2626';
                @endphp

                <a href="{{ route('showrooms.show', $showroom) }}"
                   class="px-3 py-2 rounded-xl text-[13px] font-semibold transition-colors
                          {{ $isHome ? 'text-slate-900 font-bold bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    Home
                </a>

                <a href="{{ route('showrooms.bikes.index', $showroom) }}"
                   class="px-3 py-2 rounded-xl text-[13px] font-semibold transition-colors
                          {{ $isInventory ? 'text-slate-900 font-bold bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    Motorcycles
                </a>

                <a href="{{ route('showrooms.enquiries.create', $showroom) }}"
                   class="px-3 py-2 rounded-xl text-[13px] font-semibold transition-colors
                          {{ $isTestRide ? 'text-slate-900 font-bold bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    Test Ride
                </a>

                <a href="{{ route('showrooms.services.create', $showroom) }}"
                   class="px-3 py-2 rounded-xl text-[13px] font-semibold transition-colors
                          {{ $isService ? 'text-slate-900 font-bold bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    Service & Repair
                </a>

                <a href="{{ route('showrooms.careers.index', $showroom) }}"
                   class="px-3 py-2 rounded-xl text-[13px] font-semibold transition-colors
                          {{ $isCareers ? 'text-slate-900 font-bold bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    Careers
                </a>

                <a href="{{ route('showrooms.promotions.index', $showroom) }}"
                   class="px-3 py-2 rounded-xl text-[13px] font-semibold transition-colors
                          {{ $isPromotions ? 'text-slate-900 font-bold bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    Promotions
                </a>

                <a href="{{ route('showrooms.show', $showroom) }}#about"
                   class="px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                    About
                </a>

                <a href="{{ route('showrooms.show', $showroom) }}#hours"
                   class="px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                    Hours
                </a>

                <a href="{{ route('showrooms.show', $showroom) }}#contact"
                   class="px-3 py-2 rounded-xl text-[13px] font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                    Contact
                </a>
            </nav>

            {{-- ═══ RIGHT ACTIONS (Back-Office + CTA) ═══ --}}
            <div class="flex items-center gap-2.5">
                @auth
                    @if (auth()->user()->canAccessTenant($showroom))
                        <a href="{{ route('showrooms.manage', $showroom) }}"
                           class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-white text-xs font-bold shadow-md shadow-slate-900/10 transition hover:brightness-110 active:scale-95"
                           style="background-color: {{ $primaryColor }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Back-Office</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Staff Login</span>
                    </a>
                @endauth

                {{-- Book Test Ride CTA --}}
                <a href="{{ route('showrooms.enquiries.create', $showroom) }}"
                   class="hidden md:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-xs font-bold shadow-md shadow-slate-900/10 transition hover:brightness-110 active:scale-95"
                   style="background-color: {{ $primaryColor }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    <span>Book Test Ride</span>
                </a>

                {{-- Mobile Hamburger --}}
                <button
                    @click="mobileOpen = !mobileOpen"
                    class="lg:hidden p-2.5 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition"
                    id="btn-showroom-mobile-toggle"
                    aria-label="Toggle Navigation Menu"
                >
                    <svg class="w-6 h-6" x-show="!mobileOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg class="w-6 h-6" x-show="mobileOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ═══ MOBILE DROPDOWN NAVIGATION ═══ --}}
        <div
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="lg:hidden border-t border-slate-100 py-4 grid grid-cols-2 gap-1.5"
            x-cloak
        >
            <a href="{{ route('showrooms.show', $showroom) }}"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Home</span>
            </a>
            <a href="{{ route('showrooms.bikes.index', $showroom) }}"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Motorcycles</span>
            </a>
            <a href="{{ route('showrooms.enquiries.create', $showroom) }}"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Book Test Ride</span>
            </a>
            <a href="{{ route('showrooms.services.create', $showroom) }}"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Service Center</span>
            </a>
            <a href="{{ route('showrooms.careers.index', $showroom) }}"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Careers</span>
            </a>
            <a href="{{ route('showrooms.promotions.index', $showroom) }}"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                <span>Promotions</span>
            </a>
            <a href="{{ route('showrooms.show', $showroom) }}#contact"
               class="col-span-2 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-slate-100 text-xs font-bold text-slate-700 hover:bg-slate-200 transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <span>Call & Visit Showroom</span>
            </a>
        </div>
    </div>
</header>
