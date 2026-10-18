<x-showroom-layout :showroom="$showroom" :title="$showroom->name . ' - ' . $showroom->tagline">

    {{-- ══════════════════════════════════════════════════════════
         SHOWROOM PUBLIC HEADER — Modern responsive sticky navbar
         ══════════════════════════════════════════════════════════ --}}
    <header
        class="border-b border-slate-200 bg-white/95 backdrop-blur-xl sticky top-0 z-40"
        x-data="{ mobileOpen: false }"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 py-3">

                {{-- Brand / Logo --}}
                <a href="{{ route('showrooms.show', $showroom) }}" class="flex items-center gap-3 group flex-shrink-0">
                    @if($showroom->logo_url)
                        <img src="{{ $showroom->logo_url }}" alt="{{ $showroom->name }}"
                             class="h-10 w-auto rounded-xl object-contain group-hover:opacity-90 transition">
                    @else
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white text-xl font-display shadow-md group-hover:scale-105 transition-transform"
                             style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                            {{ strtoupper(substr($showroom->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="leading-tight">
                        <p class="text-base font-black font-display text-slate-900 tracking-tight leading-none">
                            {{ $showroom->name }}
                        </p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[11px] text-slate-400 font-medium">{{ $showroom->city ?? 'Official Dealership' }}</span>
                            @if($showroom->isOpenNow())
                                <span class="flex items-center gap-1 text-[11px] text-emerald-600 font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Open
                                </span>
                            @else
                                <span class="flex items-center gap-1 text-[11px] text-amber-600 font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Closed
                                </span>
                            @endif
                        </div>
                    </div>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden lg:flex items-center gap-1">
                    @php
                        $navLinks = [
                            ['href' => route('showrooms.bikes.index', $showroom), 'label' => 'Inventory'],
                            ['href' => route('showrooms.enquiries.create', $showroom), 'label' => 'Test Ride'],
                            ['href' => route('showrooms.services.create', $showroom), 'label' => 'Service'],
                            ['href' => route('showrooms.careers.index', $showroom), 'label' => 'Careers'],
                            ['href' => route('showrooms.promotions.index', $showroom), 'label' => 'Promotions'],
                            ['href' => '#about', 'label' => 'About'],
                            ['href' => '#hours', 'label' => 'Hours'],
                            ['href' => '#contact', 'label' => 'Contact'],
                        ];
                    @endphp
                    @foreach($navLinks as $nl)
                        <a href="{{ $nl['href'] }}"
                           class="px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                            {{ $nl['label'] }}
                        </a>
                    @endforeach
                </nav>

                {{-- Right Actions --}}
                <div class="flex items-center gap-2">
                    @auth
                        @if (auth()->user()->canAccessTenant($showroom))
                            <a href="{{ route('showrooms.manage', $showroom) }}"
                               class="hidden sm:flex items-center gap-1.5 px-4 py-2 rounded-xl text-white text-sm font-bold shadow-md transition hover:brightness-110"
                               style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Back-Office
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="hidden sm:flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Staff Sign In
                        </a>
                    @endauth

                    {{-- CTA Book Test Ride --}}
                    <a href="{{ route('showrooms.enquiries.create', $showroom) }}"
                       class="hidden md:flex items-center gap-1.5 px-4 py-2 rounded-xl text-white text-sm font-bold shadow-md transition hover:brightness-110"
                       style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                        Book Test Ride
                    </a>

                    {{-- Mobile hamburger --}}
                    <button @click="mobileOpen = !mobileOpen"
                            class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition"
                            id="btn-public-mobile-menu">
                        <svg class="w-5 h-5" x-show="!mobileOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg class="w-5 h-5" x-show="mobileOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile dropdown menu --}}
            <div
                x-show="mobileOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="lg:hidden border-t border-slate-100 py-3 grid grid-cols-2 gap-1"
                x-cloak
            >
                <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Inventory
                </a>
                <a href="{{ route('showrooms.enquiries.create', $showroom) }}" class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Test Ride
                </a>
                <a href="{{ route('showrooms.services.create', $showroom) }}" class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Service
                </a>
                <a href="{{ route('showrooms.careers.index', $showroom) }}" class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Careers
                </a>
                <a href="{{ route('showrooms.promotions.index', $showroom) }}" class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    Promotions
                </a>
                <a href="#contact" class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    Contact
                </a>
            </div>
        </div>
    </header>

    <!-- Showroom Hero -->
    <section class="py-20 lg:py-28 relative overflow-hidden bg-gradient-to-b from-white via-slate-50 to-slate-100/60 border-b border-slate-200">
        @if($showroom->banner_url)
            <div class="absolute inset-0 z-0 opacity-15">
                <img src="{{ $showroom->banner_url }}" alt="Showroom Banner" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-50 via-slate-50/70 to-transparent"></div>
            </div>
        @else
            <div class="absolute inset-0 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:16px_16px] opacity-60 z-0"></div>
        @endif

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
            <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest bg-rose-50 text-rose-600 border border-rose-200 inline-block shadow-xs">
                Official Dealership Storefront
            </span>

            <h2 class="text-4xl sm:text-6xl font-black font-display text-slate-900 tracking-tight max-w-4xl mx-auto leading-tight">
                {{ $showroom->headline }}
            </h2>

            <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                {{ $showroom->tagline }}
            </p>

            <div class="pt-4 flex flex-wrap justify-center gap-4">
                <a href="{{ route('showrooms.bikes.index', $showroom) }}"
                   class="px-6 py-3.5 rounded-xl bg-brand-primary text-white font-bold text-sm shadow-lg shadow-brand-primary/20 transition hover:brightness-110 flex items-center gap-2" id="btn-browse-inventory">
                    <span>Explore Motorcycles</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>

                <a href="{{ route('showrooms.enquiries.create', $showroom) }}"
                   class="px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-bold text-sm transition shadow-xs flex items-center gap-2" id="btn-book-test-ride">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Book Test Ride</span>
                </a>

                <a href="{{ route('showrooms.services.create', $showroom) }}"
                   class="px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-bold text-sm transition shadow-xs flex items-center gap-2" id="btn-book-service">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Book Service</span>
                </a>

                @if($showroom->getSetting('whatsapp_number'))
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $showroom->getSetting('whatsapp_number')) }}" target="_blank"
                       class="px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.586 1.761.882 2.796.882h.005c3.18 0 5.766-2.586 5.767-5.766.001-3.181-2.585-5.769-5.772-5.769zm0 10.375c-.911 0-1.636-.264-2.399-.714l-.171-.102-1.782.467.476-1.737-.113-.179c-.482-.767-.736-1.53-.735-2.344.001-2.408 1.959-4.366 4.368-4.366 2.409 0 4.368 1.959 4.369 4.367 0 2.409-1.959 4.368-4.372 4.368zm4.811-2.738c-.088-.147-.324-.236-.677-.412s-2.091-1.032-2.415-1.15c-.324-.118-.559-.176-.795.176s-.913 1.15-1.119 1.385c-.206.236-.412.265-.765.088s-1.493-.55-2.844-1.756c-1.052-.938-1.762-2.098-1.968-2.451s-.022-.544.154-.72c.159-.158.353-.412.53-.618s.235-.353.353-.589c.118-.235.059-.441-.029-.618s-.795-1.914-1.089-2.62c-.287-.688-.578-.595-.795-.606-.206-.01-.441-.012-.677-.012s-.618.088-.942.441c-.324.353-1.236 1.207-1.236 2.944s1.266 3.415 1.442 3.65c.177.235 2.49 3.803 6.033 5.333.843.364 1.501.582 2.014.745.847.269 1.618.231 2.227.14.679-.101 2.091-.854 2.385-1.678.295-.824.295-1.531.206-1.678z"/></svg>
                        <span>WhatsApp Showroom</span>
                    </a>
                @endif

                <a href="#contact" class="px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-bold text-sm transition shadow-xs">
                    Contact & Location
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Motorcycles Section -->
    @php
        $displayBikes = $showroom->featuredBikes->isNotEmpty()
            ? $showroom->featuredBikes->take(6)
            : $showroom->publishedBikes->take(6);
    @endphp
    @if($displayBikes->isNotEmpty())
        <section class="py-20 bg-slate-50 border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-brand-primary">Premier Lineup</span>
                        <h2 class="text-3xl sm:text-4xl font-black font-display text-slate-900 mt-1">Featured Motorcycles</h2>
                        <p class="text-sm text-slate-600 mt-1">Hand-picked performance and touring machines available at our showroom.</p>
                    </div>

                    <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-xs font-bold text-slate-800 transition shadow-xs group self-start md:self-auto">
                        <span>View All Inventory</span>
                        <svg class="w-4 h-4 text-brand-primary group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($displayBikes as $fBike)
                        <div class="bg-white rounded-3xl border border-slate-200 hover:border-brand-primary/50 overflow-hidden flex flex-col transition-all duration-300 group hover:shadow-xl">
                            <div class="aspect-[16/10] overflow-hidden bg-slate-100 relative">
                                <img src="{{ $fBike->primary_image_url }}" alt="{{ $fBike->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-3 inset-x-3 flex items-center justify-between">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $fBike->category->badgeClasses() }} shadow-xs">
                                        {{ $fBike->category->label() }}
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold {{ $fBike->status->badgeClasses() }} shadow-xs">
                                        {{ $fBike->status->label() }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">{{ $fBike->brand }} • {{ $fBike->model_year }}</span>
                                    <h3 class="font-display font-bold text-lg text-slate-900 group-hover:text-brand-primary transition mt-1 leading-snug">
                                        <a href="{{ route('showrooms.bikes.show', [$showroom, $fBike]) }}">
                                            {{ $fBike->name }}
                                        </a>
                                    </h3>
                                </div>

                                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block">Price</span>
                                        <span class="font-mono font-black text-lg text-slate-900">{{ $fBike->formatted_price }}</span>
                                    </div>

                                    <a href="{{ route('showrooms.bikes.show', [$showroom, $fBike]) }}"
                                       class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-brand-primary hover:text-white text-slate-800 font-bold text-xs transition border border-slate-200 flex items-center gap-1.5 shadow-xs">
                                        <span>Details</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- About & Operating Schedule Section -->
    <section id="about" class="py-20 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

                <!-- About Us Story -->
                <div class="space-y-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-primary">Our Dealership Story</span>
                    <h3 class="text-3xl font-black font-display text-slate-900">About {{ $showroom->name }}</h3>
                    <p class="text-slate-600 leading-relaxed text-sm">
                        {{ $showroom->about_text }}
                    </p>

                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-xs uppercase font-bold text-slate-500">Location</span>
                            <p class="text-slate-900 font-bold text-sm mt-1">{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-xs uppercase font-bold text-slate-500">Dealership Status</span>
                            <p class="text-emerald-600 font-bold text-sm mt-1">Verified Partner</p>
                        </div>
                    </div>
                </div>

                <!-- Operating Hours Matrix -->
                <div id="hours" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-bold font-display text-slate-900">Showroom Working Hours</h4>
                        @if($showroom->isOpenNow())
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Open Today
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Closed Now
                            </span>
                        @endif
                    </div>

                    <div class="divide-y divide-slate-100 text-xs">
                        @php $todayName = strtolower(now()->format('l')); @endphp
                        @foreach($showroom->getOperatingHours() as $day => $data)
                            <div class="py-2.5 flex items-center justify-between {{ $todayName === $day ? 'text-brand-primary font-bold bg-rose-50/50 px-2 rounded-lg' : 'text-slate-600' }}">
                                <span class="capitalize">{{ $day }} @if($todayName === $day) (Today) @endif</span>
                                <span>
                                    @if(!empty($data['is_closed']))
                                        <span class="text-rose-600 font-semibold">Closed</span>
                                    @else
                                        {{ $data['open'] ?? '09:30' }} - {{ $data['close'] ?? '20:00' }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Location & Contact Section -->
    <section id="contact" class="py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 space-y-2">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-primary">Visit Our Dealership</span>
                <h3 class="text-3xl font-black font-display text-slate-900">Contact & Directions</h3>
                <p class="text-xs text-slate-500">Drop by our showroom for a test ride, service inquiry, or to meet our two-wheeler specialists.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-200 text-center space-y-2 shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-brand-primary/10 text-brand-primary mx-auto flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <span class="text-xs uppercase font-bold text-slate-500">Showroom Address</span>
                    <p class="text-slate-900 text-sm font-semibold">{{ $showroom->address_line1 ?? $showroom->city }}</p>
                    <span class="text-xs text-slate-500">{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</span>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200 text-center space-y-2 shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-brand-primary/10 text-brand-primary mx-auto flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <span class="text-xs uppercase font-bold text-slate-500">Phone Support</span>
                    <p class="text-slate-900 text-sm font-semibold">{{ $showroom->phone }}</p>
                    <span class="text-xs text-slate-500">Sales & Service Helpline</span>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200 text-center space-y-2 shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-brand-primary/10 text-brand-primary mx-auto flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-xs uppercase font-bold text-slate-500">Direct Email</span>
                    <p class="text-slate-900 text-sm font-semibold">{{ $showroom->email }}</p>
                    <span class="text-xs text-slate-500">Official Correspondence</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════
         MODERN SHOWROOM FOOTER (Clean Light Theme)
         ══════════════════════════════════════════════════════════ --}}
    <footer class="bg-white border-t border-slate-200 text-slate-600 pt-16 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Top Row: Brand + Nav Columns --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-slate-100">

                {{-- Brand column --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white text-lg shadow-md flex-shrink-0"
                             style="background-color: {{ $showroom->brand_color_primary ?? '#f97316' }}">
                            {{ strtoupper(substr($showroom->name, 0, 1)) }}
                        </div>
                        <div class="leading-tight">
                            <p class="font-black text-slate-900 text-base font-display">{{ $showroom->name }}</p>
                            <p class="text-xs text-slate-500 font-medium">{{ $showroom->city }}, {{ $showroom->state ?? 'India' }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        {{ $showroom->tagline ?? 'Your trusted premium motorcycle dealership and certified service center.' }}
                    </p>
                    {{-- Social Icons --}}
                    <div class="flex items-center gap-2 pt-1">
                        @foreach($showroom->getSocialLinks() as $network => $url)
                            @if(!empty($url))
                                <a href="{{ $url }}" target="_blank" rel="noopener"
                                   class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-700 hover:text-slate-900 transition capitalize text-xs font-bold"
                                   title="{{ ucfirst($network) }}">
                                    {{ strtoupper(substr($network, 0, 2)) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Explore --}}
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Explore</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('showrooms.bikes.index', $showroom) }}" class="text-sm text-slate-600 hover:text-slate-950 font-medium transition">Motorcycle Inventory</a></li>
                        <li><a href="{{ route('showrooms.enquiries.create', $showroom) }}" class="text-sm text-slate-600 hover:text-slate-950 font-medium transition">Book a Test Ride</a></li>
                        <li><a href="{{ route('showrooms.promotions.index', $showroom) }}" class="text-sm text-slate-600 hover:text-slate-950 font-medium transition">Promotions & Offers</a></li>
                        <li><a href="#about" class="text-sm text-slate-600 hover:text-slate-950 font-medium transition">About Dealership</a></li>
                        <li><a href="#hours" class="text-sm text-slate-600 hover:text-slate-950 font-medium transition">Business Hours</a></li>
                    </ul>
                </div>

                {{-- Services --}}
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Customer Care</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('showrooms.services.create', $showroom) }}" class="text-sm text-slate-600 hover:text-slate-950 font-medium transition">Book Workshop Service</a></li>
                        <li><a href="{{ route('showrooms.services.track', $showroom) }}" class="text-sm text-slate-600 hover:text-slate-950 font-medium transition">Track Service Status</a></li>
                        <li><a href="{{ route('showrooms.careers.index', $showroom) }}" class="text-sm text-slate-600 hover:text-slate-950 font-medium transition">Career Opportunities</a></li>
                        <li><a href="#contact" class="text-sm text-slate-600 hover:text-slate-950 font-medium transition">Contact Support</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Visit Us</h3>
                    <ul class="space-y-3">
                        @if($showroom->phone)
                            <li class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <a href="tel:{{ $showroom->phone }}" class="text-sm text-slate-700 hover:text-slate-950 font-medium transition">{{ $showroom->phone }}</a>
                            </li>
                        @endif
                        @if($showroom->email)
                            <li class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <a href="mailto:{{ $showroom->email }}" class="text-sm text-slate-700 hover:text-slate-950 font-medium transition break-all">{{ $showroom->email }}</a>
                            </li>
                        @endif
                        @if($showroom->address)
                            <li class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-sm text-slate-600 font-medium">{{ $showroom->address }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-8">
                <p class="text-xs text-slate-500 font-medium">
                    &copy; {{ date('Y') }} <span class="text-slate-800 font-bold">{{ $showroom->name }}</span>. All rights reserved.
                </p>
                <p class="text-xs text-slate-400">
                    Powered by <span class="text-slate-600 font-bold">MotoShowroom Cloud</span>
                </p>
            </div>
        </div>
    </footer>
</x-showroom-layout>
