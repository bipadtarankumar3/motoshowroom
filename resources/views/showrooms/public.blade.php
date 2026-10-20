<x-showroom-layout :showroom="$showroom" :title="$showroom->name . ' - ' . $showroom->tagline">

    @include('showrooms.partials.header')

    {{-- ══════════════════════════════════════════════════════════════════
         GLOBAL PAGE ANIMATION STYLES
         ══════════════════════════════════════════════════════════════════ --}}
    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes floatBob {
            0%, 100% { transform: translateY(0px);   }
            50%       { transform: translateY(-12px); }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,0); }
            50%       { box-shadow: 0 0 40px 8px rgba(220,38,38,.18); }
        }
        @keyframes scanLine {
            0%   { background-position: 0% 0%;   }
            100% { background-position: 0% 100%; }
        }
        @keyframes revealBar {
            from { transform: scaleX(0); }
            to   { transform: scaleX(1); }
        }
        @keyframes bikeSlide {
            from { opacity: 0; transform: translateX(60px) scale(.96); }
            to   { opacity: 1; transform: translateX(0)    scale(1);   }
        }
        @keyframes countUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .anim-fade-up   { animation: fadeUp      .65s cubic-bezier(.22,.68,0,1.2) both; }
        .anim-fade-up-1 { animation-delay: .08s; }
        .anim-fade-up-2 { animation-delay: .18s; }
        .anim-fade-up-3 { animation-delay: .30s; }
        .anim-fade-up-4 { animation-delay: .42s; }
        .anim-fade-up-5 { animation-delay: .56s; }
        .anim-bike      { animation: bikeSlide    .85s cubic-bezier(.22,.68,0,1.2) .15s both; }
        .anim-float     { animation: floatBob      4s  ease-in-out infinite; }
        .anim-pulse-glow{ animation: pulseGlow     2.5s ease-in-out infinite; }
        .anim-count     { animation: countUp       .55s cubic-bezier(.22,.68,0,1.2) both; }

        /* Hero backdrop scan-line shimmer */
        .hero-scan {
            background: linear-gradient(180deg, transparent 0%, rgba(255,255,255,.06) 50%, transparent 100%);
            background-size: 100% 60px;
            animation: scanLine 3s linear infinite;
        }
        /* Magnetic card hover tilt */
        .card-tilt { transition: transform .3s cubic-bezier(.22,.68,0,1.2), box-shadow .3s ease; }
        .card-tilt:hover { transform: translateY(-6px) scale(1.015); box-shadow: 0 24px 48px -8px rgba(0,0,0,.13); }

        /* Shimmer loader on images */
        .img-shimmer { position:relative; overflow:hidden; }
        .img-shimmer::after {
            content:''; position:absolute; inset:0;
            background:linear-gradient(90deg,transparent 25%,rgba(255,255,255,.25) 50%,transparent 75%);
            background-size:200% 100%;
            animation: scanLine .9s linear infinite;
        }

        /* Glowing CTA button */
        .btn-primary {
            background: linear-gradient(135deg,#dc2626,#FF6B00);
            transition: filter .2s,transform .2s,box-shadow .2s;
        }
        .btn-primary:hover {
            filter: brightness(1.12);
            transform: translateY(-2px);
            box-shadow: 0 16px 40px -8px rgba(220,38,38,.45);
        }
        .btn-primary:active { transform: translateY(0); }

        /* Stats counter roll */
        .stat-card { transition: transform .3s, box-shadow .3s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow:0 16px 32px -8px rgba(0,0,0,.1); }

        /* Feature card */
        .feature-card { transition: transform .3s cubic-bezier(.22,.68,0,1.2), box-shadow .3s; }
        .feature-card:hover { transform: translateY(-8px); box-shadow: 0 24px 48px -8px rgba(0,0,0,.12); }

        /* Reveal bar */
        .reveal-bar {
            height: 3px;
            background: linear-gradient(90deg,#dc2626,#FF6B00);
            transform-origin: left;
            transform: scaleX(0);
            transition: transform .4s cubic-bezier(.22,.68,0,1.2);
        }
        .group:hover .reveal-bar { transform: scaleX(1); }

        /* Category card hover */
        .cat-card { transition: transform .3s cubic-bezier(.22,.68,0,1.2), box-shadow .3s, border-color .3s; }
        .cat-card:hover { transform: translateY(-8px); box-shadow: 0 24px 48px -12px rgba(220,38,38,.18); border-color: rgba(220,38,38,.5); }
        .cat-card:hover .cat-arrow { background:#dc2626; color:#fff; }

        /* Bike card */
        .bike-card { transition: transform .3s cubic-bezier(.22,.68,0,1.2), box-shadow .3s; }
        .bike-card:hover { transform: translateY(-8px); box-shadow:0 24px 48px -8px rgba(0,0,0,.14); }
        .bike-card:hover .bike-img img { transform: scale(1.08); }

        /* Testimonial */
        .testi-card { transition: transform .3s, box-shadow .3s; }
        .testi-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px -8px rgba(0,0,0,.1); }

        /* Intersection observer reveal */
        .will-reveal { opacity:0; transform:translateY(28px); transition: opacity .6s ease, transform .6s cubic-bezier(.22,.68,0,1.2); }
        .will-reveal.revealed { opacity:1; transform:translateY(0); }
        .delay-1 { transition-delay:.1s; }
        .delay-2 { transition-delay:.2s; }
        .delay-3 { transition-delay:.3s; }
        .delay-4 { transition-delay:.4s; }

        @keyframes turntableOrbit {
            0%   { transform: perspective(800px) rotateY(0deg) scale(1); }
            35%  { transform: perspective(800px) rotateY(-16deg) scale(0.96) translateY(4px); }
            70%  { transform: perspective(800px) rotateY(16deg) scale(1.02) translateY(-3px); }
            100% { transform: perspective(800px) rotateY(0deg) scale(1); }
        }
        .anim-turntable-spin {
            animation: turntableOrbit 0.85s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }
        .bike-rotating-left {
            transform: perspective(800px) rotateY(-24deg) translateX(-70px) scale(0.86) !important;
            opacity: 0.2 !important;
            filter: blur(2px) !important;
        }
        .bike-rotating-right {
            transform: perspective(800px) rotateY(24deg) translateX(70px) scale(0.86) !important;
            opacity: 0.2 !important;
            filter: blur(2px) !important;
        }
    </style>

    <main class="w-full bg-white text-slate-800">

        @if(! $showroom->public_website_enabled)
            <div class="bg-amber-500 text-slate-950 px-4 py-2.5 text-center text-xs font-bold uppercase tracking-wider sticky top-20 z-30 shadow-md flex items-center justify-center gap-2">
                <span class="w-2 h-2 rounded-full bg-slate-950 animate-pulse"></span>
                <span>Storefront Offline Mode: Customers cannot see this website right now. You are previewing it as a dealership manager.</span>
                <a href="{{ route('showrooms.manage.website', $showroom) }}" class="underline ml-2 hover:text-white">Enable in Builder</a>
            </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════════════
             § 1. HERO — Cinematic Taller Multi-Banner Carousel (Alpine.js)
             ══════════════════════════════════════════════════════════════════ --}}
        @php
            $primaryColor = $showroom->brand_color_primary ?? '#dc2626';
            $accentColor = $showroom->brand_color_accent ?? '#f97316';
            $rawSections = $showroom->settings['homepage_sections'] ?? [];
            $defaultHomepageSections = [
                'show_hero' => true,
                'show_categories' => true,
                'show_featured_bikes' => true,
                'show_trust_pillars' => true,
                'show_about' => true,
                'show_news' => true,
                'featured_title' => 'Featured Motorcycles',
                'featured_bikes_title' => 'Featured Motorcycles',
                'featured_bikes_count' => 8,
                'categories_title' => 'Browse By Category',
            ];
            $homepageSections = array_merge($defaultHomepageSections, is_array($rawSections) ? $rawSections : []);

            // 1. Resolve Banners from Database (Active Banners first)
            $dbBanners = $showroom->activeBanners;
            if ($dbBanners->isEmpty()) {
                $dbBanners = $showroom->banners;
            }

            $heroSlides = [];
            if ($dbBanners->isNotEmpty()) {
                foreach ($dbBanners as $b) {
                    $heroSlides[] = [
                        'id' => $b->id,
                        'badge' => $b->badge ?? 'NEW COLLECTION 2026',
                        'title_prefix' => $b->title_prefix ?? 'Fuel Your',
                        'title_highlight' => $b->title_highlight ?? 'Next',
                        'title_suffix' => $b->title_suffix ?? 'Adventure',
                        'subtitle' => $b->subtitle ?? 'Explore the latest bikes with unmatched performance, style and technology.',
                        'btn_primary_text' => $b->btn_primary_text ?: 'Explore Bikes',
                        'btn_primary_url' => $b->btn_primary_url ?: route('showrooms.bikes.index', $showroom),
                        'btn_secondary_text' => $b->btn_secondary_text ?: 'Book a Test Ride',
                        'btn_secondary_url' => $b->btn_secondary_url ?: route('showrooms.enquiries.create', $showroom),
                        'feature_1_title' => $b->feature_1_title ?: 'Trusted Showroom',
                        'feature_1_sub' => $b->feature_1_sub ?: '100% Genuine Bikes',
                        'feature_2_title' => $b->feature_2_title ?: 'After Sales Support',
                        'feature_2_sub' => $b->feature_2_sub ?: 'Always Here for You',
                        'feature_3_title' => $b->feature_3_title ?: 'Best Price Guarantee',
                        'feature_3_sub' => $b->feature_3_sub ?: 'Unbeatable Offers',
                        'image_url' => $b->display_image_url,
                    ];
                }
            } else {
                // Fallback default slide
                $heroSlides = [
                    [
                        'id' => 1,
                        'badge' => 'NEW COLLECTION 2026',
                        'title_prefix' => !empty($showroom->headline) ? $showroom->headline : 'Fuel Your',
                        'title_highlight' => !empty($showroom->headline) ? '' : 'Next',
                        'title_suffix' => !empty($showroom->headline) ? '' : 'Adventure',
                        'subtitle' => !empty($showroom->tagline) ? $showroom->tagline : 'Explore the latest bikes with unmatched performance, style and technology.',
                        'btn_primary_text' => 'Explore Bikes',
                        'btn_primary_url' => route('showrooms.bikes.index', $showroom),
                        'btn_secondary_text' => 'Book a Test Ride',
                        'btn_secondary_url' => route('showrooms.enquiries.create', $showroom),
                        'feature_1_title' => 'Trusted Showroom',
                        'feature_1_sub' => '100% Genuine Bikes',
                        'feature_2_title' => 'After Sales Support',
                        'feature_2_sub' => 'Always Here for You',
                        'feature_3_title' => 'Best Price Guarantee',
                        'feature_3_sub' => 'Unbeatable Offers',
                        'image_url' => asset('images/hero/hero_adventure_red.jpg'),
                    ]
                ];
            }

            // 2. Resolve Categories from Database
            $dbCategories = $showroom->activeCategories;
            if ($dbCategories->isEmpty()) {
                $dbCategories = $showroom->categories;
            }
        @endphp

        @if($homepageSections['show_hero'] ?? true)
            <section class="relative w-full overflow-hidden bg-slate-950 min-h-[780px] sm:min-h-[860px] lg:min-h-[920px] xl:min-h-[980px] 2xl:h-[92vh] flex items-center select-none"
                     id="hero-showcase-section"
                     x-data="{
                         activeSlide: 0,
                         totalSlides: {{ count($heroSlides) }},
                         timer: null,
                         startAutoplay() {
                             if (this.totalSlides > 1) {
                                 this.timer = setInterval(() => { this.next(); }, 6500);
                             }
                         },
                         stopAutoplay() {
                             if (this.timer) clearInterval(this.timer);
                         },
                         next() {
                             this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
                         },
                         prev() {
                             this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
                         }
                     }"
                     x-init="startAutoplay()"
                     @mouseenter="stopAutoplay()"
                     @mouseleave="startAutoplay()">

                {{-- ── Background Image & Cinematic Lighting for Each Slide ── --}}
                @foreach($heroSlides as $sIdx => $slide)
                    <div x-show="activeSlide === {{ $sIdx }}"
                         x-transition:enter="transition ease-out duration-1000"
                         x-transition:enter-start="opacity-0 scale-105"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-800"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute inset-0 z-0">
                        <img src="{{ $slide['image_url'] }}"
                             alt="{{ $showroom->name }} Hero Slide {{ $sIdx + 1 }}"
                             class="w-full h-full object-cover object-[center_right] sm:object-center transform transition-transform duration-1000 ease-out"
                             loading="{{ $sIdx === 0 ? 'eager' : 'lazy' }}">
                        
                        {{-- Dark Vignette Gradients for High Text Legibility --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-black/95 via-black/80 to-transparent"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-black/40"></div>
                        <div class="hero-scan absolute inset-0 pointer-events-none"></div>
                    </div>
                @endforeach

                {{-- ── Quick Manage Button for Showroom Owner / Staff ── --}}
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->canAccessTenant($showroom))
                        <div class="absolute top-6 right-6 z-30 flex items-center gap-2">
                            <a href="{{ route('showrooms.manage.banners.index', $showroom) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white text-xs font-bold backdrop-blur-md border border-white/20 transition-all duration-200 shadow-xl group">
                                <svg class="w-3.5 h-3.5 text-amber-400 group-hover:rotate-45 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Manage Hero Banners</span>
                            </a>
                        </div>
                    @endif
                @endauth

                {{-- ── Main Container & Foreground Typography ── --}}
                <div class="relative z-10 w-full max-w-[1880px] mx-auto px-6 sm:px-12 lg:px-20 py-20 sm:py-28 lg:py-36">
                    @foreach($heroSlides as $sIdx => $slide)
                        <div x-show="activeSlide === {{ $sIdx }}"
                             x-transition:enter="transition ease-out duration-700 delay-150"
                             x-transition:enter-start="opacity-0 translate-y-8"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-4"
                             class="max-w-2xl lg:max-w-3xl">

                            {{-- Badge & Dealership Headline Anchor --}}
                            <div class="mb-5 flex flex-wrap items-center gap-3">
                                <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-[0.22em] text-white backdrop-blur-md border border-white/20 font-mono shadow-sm"
                                      style="background: linear-gradient(135deg, {{ $primaryColor }}cc, {{ $accentColor }}cc);">
                                    {{ $slide['badge'] }}
                                </span>
                                @if(!empty($showroom->headline))
                                    <span class="text-xs text-slate-300 font-bold uppercase tracking-wider backdrop-blur-xs px-2.5 py-1 rounded-md bg-white/10 border border-white/10">
                                        &bull; {{ $showroom->headline }}
                                    </span>
                                @endif
                            </div>

                            {{-- Main Headline --}}
                            <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white tracking-tight leading-[1.02] drop-shadow-lg">
                                {{ $slide['title_prefix'] }}<br>
                                @if(!empty($slide['title_highlight']))
                                    <span class="font-black" style="color: {{ $primaryColor }};">{{ $slide['title_highlight'] }}</span>
                                @endif
                                @if(!empty($slide['title_suffix']))
                                    {{ $slide['title_suffix'] }}
                                @endif
                            </h1>

                            {{-- Subtitle & Dealership Tagline Anchor --}}
                            <p class="text-sm sm:text-base lg:text-lg text-slate-200 max-w-xl mt-6 leading-relaxed font-normal drop-shadow-sm">
                                {{ $slide['subtitle'] }}
                                @if(!empty($showroom->tagline))
                                    <span class="block text-xs text-slate-400 mt-2 font-medium tracking-wide">
                                        {{ $showroom->tagline }}
                                    </span>
                                @endif
                            </p>

                            {{-- CTA Buttons --}}
                            <div class="flex flex-wrap items-center gap-4 mt-8 sm:mt-12">
                                {{-- Primary CTA with dealership brand color --}}
                                <a href="{{ $slide['btn_primary_url'] }}"
                                   class="inline-flex items-center gap-2.5 px-8 py-4 rounded-full text-white font-bold text-sm tracking-wide shadow-2xl transition-all duration-200 hover:scale-105 active:scale-95 group"
                                   style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $accentColor }}); box-shadow: 0 16px 36px -8px {{ $primaryColor }}70;">
                                    <span>{{ $slide['btn_primary_text'] }}</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>

                                {{-- Secondary CTA --}}
                                <a href="{{ $slide['btn_secondary_url'] }}"
                                   class="inline-flex items-center justify-center px-8 py-4 rounded-full bg-slate-900/60 hover:bg-white/15 text-white font-bold text-sm tracking-wide border border-white/20 hover:border-white/40 backdrop-blur-md transition-all duration-200 active:scale-95">
                                    {{ $slide['btn_secondary_text'] }}
                                </a>
                            </div>

                            {{-- Bottom 3 Trust Feature Highlights --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8 pt-10 sm:pt-14 mt-10 border-t border-white/15 max-w-2xl">
                                {{-- 1. Trusted Showroom --}}
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-full border border-white/20 bg-white/10 flex items-center justify-center flex-shrink-0 text-white backdrop-blur-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-white uppercase tracking-wider">{{ $slide['feature_1_title'] }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $slide['feature_1_sub'] }}</p>
                                    </div>
                                </div>

                                {{-- 2. After Sales Support --}}
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-full border border-white/20 bg-white/10 flex items-center justify-center flex-shrink-0 text-white backdrop-blur-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.166a1 1 0 11-1.414-1.414"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-white uppercase tracking-wider">{{ $slide['feature_2_title'] }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $slide['feature_2_sub'] }}</p>
                                    </div>
                                </div>

                                {{-- 3. Best Price Guarantee --}}
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-full border border-white/20 bg-white/10 flex items-center justify-center flex-shrink-0 text-white backdrop-blur-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-white uppercase tracking-wider">{{ $slide['feature_3_title'] }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $slide['feature_3_sub'] }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- ── Vertical Right Dots Carousel Navigator (Clickable!) ── --}}
                @if(count($heroSlides) > 1)
                    <div class="hidden lg:flex flex-col items-center gap-3 absolute right-8 sm:right-12 top-1/2 -translate-y-1/2 z-20">
                        @foreach($heroSlides as $dotIdx => $dSlide)
                            <button type="button"
                                    @click="activeSlide = {{ $dotIdx }}"
                                    :class="activeSlide === {{ $dotIdx }} ? 'h-8 w-2.5 rounded-full shadow-lg' : 'h-2.5 w-2.5 rounded-full bg-white/40 hover:bg-white/80'"
                                    :style="activeSlide === {{ $dotIdx }} ? 'background-color: {{ $primaryColor }}' : ''"
                                    class="transition-all duration-300 cursor-pointer focus:outline-none"
                                    title="Go to slide {{ $dotIdx + 1 }}">
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- ── Bottom Right Prev/Next Floating Navigation Buttons ── --}}
                @if(count($heroSlides) > 1)
                    <div class="absolute bottom-8 right-6 sm:right-12 z-20 flex items-center gap-2.5">
                        <button type="button"
                                @click="prev()"
                                class="w-11 h-11 rounded-full bg-slate-900/60 hover:bg-white/20 text-white backdrop-blur-md border border-white/20 flex items-center justify-center transition-all duration-200 active:scale-95 cursor-pointer shadow-lg"
                                title="Previous Banner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button"
                                @click="next()"
                                class="w-11 h-11 rounded-full bg-slate-900/60 hover:bg-white/20 text-white backdrop-blur-md border border-white/20 flex items-center justify-center transition-all duration-200 active:scale-95 cursor-pointer shadow-lg"
                                title="Next Banner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                @endif

            </section>
        @endif

        {{-- ══════════════════════════════════════════════════════════════════
             § 2. CATEGORIES — Dealership Category Master Strip
             ══════════════════════════════════════════════════════════════════ --}}
        @if($homepageSections['show_categories'] ?? true)
            <section id="categories" class="py-20 bg-white border-b border-slate-100">
                <div class="w-full max-w-[1800px] mx-auto px-4 sm:px-8 lg:px-14 xl:px-20">

                    <div class="flex items-end justify-between mb-10">
                        <div class="will-reveal">
                            <span class="text-[11px] font-black uppercase tracking-[.18em]" style="color: {{ $primaryColor }};">
                                Category Master
                            </span>
                            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-1">
                                {{ $homepageSections['categories_title'] ?? 'Browse By Category' }}
                            </h2>
                        </div>
                        <div class="flex items-center gap-4">
                            @auth
                                @if(auth()->user()->isAdmin() || auth()->user()->canAccessTenant($showroom))
                                    <a href="{{ route('showrooms.manage.categories.index', $showroom) }}"
                                       class="text-xs font-bold text-slate-500 hover:text-slate-800 transition hidden sm:inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>Manage Categories</span>
                                    </a>
                                @endif
                            @endauth
                            <a href="{{ route('showrooms.bikes.index', $showroom) }}"
                               class="will-reveal delay-2 inline-flex items-center gap-1.5 text-sm font-bold transition group"
                               style="color: {{ $primaryColor }};">
                                <span>View All</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
                        @foreach($dbCategories as $i => $cat)
                            <a href="{{ route('showrooms.bikes.index', [$showroom, 'category' => $cat->slug]) }}"
                               class="cat-card group relative rounded-2xl bg-white border border-slate-200 overflow-hidden will-reveal delay-{{ ($i % 4) + 1 }}">

                                {{-- Image Stage (Supports Uploaded Category Files & URLs) --}}
                                <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                                    <img src="{{ $cat->display_image_url }}"
                                         alt="{{ $cat->name }}"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 ease-out"
                                         loading="lazy">
                                </div>

                                {{-- Reveal bar with Dealership Brand Color --}}
                                <div class="reveal-bar" style="background: linear-gradient(90deg, {{ $primaryColor }}, {{ $accentColor }});"></div>

                                {{-- Content --}}
                                <div class="p-4 flex items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-sm text-slate-900 truncate group-hover:text-red-600 transition-colors"
                                            style="--tw-group-hover-color: {{ $primaryColor }};">
                                            {{ $cat->name }}
                                        </h3>
                                        <p class="text-[11px] text-slate-500 truncate">{{ $cat->subtitle ?? 'Bikes & Machinery' }}</p>
                                    </div>
                                    <div class="cat-arrow w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center transition-all duration-300 shrink-0 group-hover:text-white"
                                         style="--tw-hover-bg: {{ $primaryColor }};">
                                        <svg class="w-3.5 h-3.5 text-slate-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif


        {{-- ══════════════════════════════════════════════════════════════════
             § 3. FEATURED MOTORCYCLES — Filterable Grid
             ══════════════════════════════════════════════════════════════════ --}}
        @if($homepageSections['show_featured_bikes'] ?? true)
            @php
                $featuredLimit = (int) ($homepageSections['featured_bikes_count'] ?? ($homepageSections['featured_limit'] ?? 8));
                $featuredHeading = $homepageSections['featured_bikes_title'] ?? ($homepageSections['featured_title'] ?? 'Featured Motorcycles');
                $displayBikes = ($showroom->publishedBikes->isNotEmpty()
                    ? $showroom->publishedBikes
                    : $showroom->bikes)->take($featuredLimit);
            @endphp

            <section class="py-20 bg-slate-50 border-b border-slate-100" x-data="{ activeFilter: 'all' }">
                <div class="w-full max-w-[1800px] mx-auto px-4 sm:px-8 lg:px-14 xl:px-20">

                    {{-- Header + Filter Tabs --}}
                    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                        <div class="will-reveal">
                            <span class="text-[11px] font-black uppercase tracking-[.18em]" style="color: {{ $primaryColor }};">
                                Premier Showcase
                            </span>
                            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-1">
                                {{ $featuredHeading }}
                            </h2>
                            <p class="text-sm text-slate-500 mt-1">Hand-picked performance machines with guaranteed pricing &amp; instant purchase reservation.</p>
                        </div>
                        <div class="will-reveal delay-2 flex items-center gap-1.5 p-1.5 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-x-auto">
                            @foreach([['all','All Bikes'],['sports','Sports'],['adventure','Adventure'],['cruiser','Cruiser'],['electric','Electric']] as [$val, $label])
                                <button type="button"
                                        @click="activeFilter = '{{ $val }}'"
                                        :class="activeFilter === '{{ $val }}' ? 'text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                                        :style="activeFilter === '{{ $val }}' ? 'background: linear-gradient(135deg, {{ $primaryColor }}, {{ $accentColor }});' : ''"
                                        class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wide transition-all duration-200 whitespace-nowrap cursor-pointer">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Bikes Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @forelse($displayBikes as $idx => $fBike)
                            @php
                                $bikeCatVal = is_object($fBike->category) ? $fBike->category->value : $fBike->category;
                                $bikeCatSlug = $fBike->categoryMaster?->slug ?? '';
                            @endphp
                            <div x-show="activeFilter === 'all' || activeFilter === '{{ $bikeCatVal }}' || activeFilter === '{{ $bikeCatSlug }}'"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="bike-card group will-reveal delay-{{ ($idx % 4) + 1 }} bg-white rounded-3xl border border-slate-200 overflow-hidden flex flex-col">

                                {{-- Image Stage --}}
                                <div class="bike-img relative aspect-[16/11] bg-slate-100 overflow-hidden">
                                    <img src="{{ $fBike->primary_image_url }}"
                                         alt="{{ $fBike->name }}"
                                         class="w-full h-full object-cover transition-transform duration-500 ease-out"
                                         loading="lazy">

                                    {{-- Dark gradient for text legibility --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                    {{-- Badges --}}
                                    <div class="absolute top-3 left-3 flex gap-1.5 z-10">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider text-white shadow-sm"
                                              style="background-color: {{ $primaryColor }};">
                                            {{ $fBike->is_featured ? 'Best Seller' : 'New Arrival' }}
                                        </span>
                                    </div>

                                    {{-- 360 Badge (Only if checked in admin panel) --}}
                                    @if($showroom->canAccessFeature('has_360_viewer') && $fBike->has_360_view)
                                        <div class="absolute top-3 right-3 z-10">
                                            <a href="{{ route('showrooms.bikes.360', [$showroom, $fBike]) }}"
                                               class="px-2 py-1 rounded-lg text-[10px] font-mono font-black bg-slate-900/85 text-cyan-400 border border-cyan-500/30 hover:scale-105 transition-transform backdrop-blur-sm shadow-xs">
                                                360°
                                            </a>
                                        </div>
                                    @endif

                                    {{-- Hover: quick buy overlay --}}
                                    <div class="absolute bottom-0 inset-x-0 p-3 z-10 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                        <a href="{{ route('showrooms.bikes.show', [$showroom, $fBike]) }}"
                                           class="block w-full py-2.5 px-4 rounded-xl text-white text-xs font-black uppercase tracking-wider text-center hover:brightness-110 transition shadow-lg"
                                           style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $accentColor }});">
                                            View Details &amp; Buy
                                        </a>
                                    </div>
                                </div>

                                {{-- Card Body --}}
                                <div class="p-5 flex-1 flex flex-col gap-4">
                                    {{-- Brand --}}
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $fBike->brand }} &bull; {{ $fBike->model_year }}</span>
                                    </div>

                                    {{-- Bike Name --}}
                                    <a href="{{ route('showrooms.bikes.show', [$showroom, $fBike]) }}"
                                       class="font-black text-lg text-slate-900 transition-colors leading-snug line-clamp-2">
                                        {{ $fBike->name }}
                                    </a>

                                    {{-- Specs Chips --}}
                                    <div class="grid grid-cols-3 gap-1.5 py-3 px-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                        <div>
                                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Engine</span>
                                            <span class="font-black text-slate-800 text-xs">{{ $fBike->engine_capacity_cc ?? 998 }} cc</span>
                                        </div>
                                        <div class="border-x border-slate-200">
                                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Power</span>
                                            <span class="font-black text-slate-800 text-xs">{{ $fBike->power_bhp ?? 180 }} bhp</span>
                                        </div>
                                        <div>
                                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Mileage</span>
                                            <span class="font-black text-slate-800 text-xs">{{ $fBike->mileage_kmpl ?? 16 }} km/l</span>
                                        </div>
                                    </div>

                                    {{-- Price Row --}}
                                    <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4">
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wide block">Ex-Showroom</span>
                                            <span class="font-mono font-black text-lg text-slate-900">{{ $fBike->formatted_price }}</span>
                                        </div>
                                        @php $emi = round($fBike->price * 0.022); @endphp
                                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            EMI ~₹{{ number_format($emi) }}/mo
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-20 text-center text-sm text-slate-400">No bikes published yet.</div>
                        @endforelse
                    </div>

                    {{-- View all CTA --}}
                    <div class="mt-12 text-center will-reveal">
                        <a href="{{ route('showrooms.bikes.index', $showroom) }}"
                           class="inline-flex items-center gap-2.5 px-10 py-4 rounded-2xl border-2 border-slate-900 text-slate-900 font-black text-sm uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all duration-300 group">
                            <span>Browse Full Inventory</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </section>
        @endif



        {{-- ══════════════════════════════════════════════════════════════════
             § 7. WHY CHOOSE US
             ══════════════════════════════════════════════════════════════════ --}}
        @if($homepageSections['show_trust_pillars'] ?? true)
            <section class="py-20 bg-slate-50 border-t border-slate-100">
                <div class="w-full max-w-[1800px] mx-auto px-4 sm:px-8 lg:px-14 xl:px-20">
                    <div class="text-center mb-12 will-reveal">
                        <span class="text-[11px] font-black uppercase tracking-[.18em]" style="color: {{ $primaryColor }};">The Dealership Advantage</span>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-1">Why Choose {{ $showroom->name }}?</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach([
                            ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','color'=>'rose','title'=>'100% Genuine Bikes','desc'=>'Every motorcycle sourced directly from the manufacturer with factory warranty and 100-point certified PDI.'],
                            ['icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z','color'=>'orange','title'=>'Expert Team','desc'=>'Personalized guidance from certified technical consultants and experienced riders for your needs.'],
                            ['icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z','color'=>'cyan','title'=>'Service & Maintenance','desc'=>'60-minute express service bays, genuine OEM spares, and live digital booking tracking to your phone.'],
                            ['icon'=>'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z','color'=>'emerald','title'=>'Exclusive Offers','desc'=>'Seasonal vouchers, instant scratch card gifts, and transparent on-road pricing with zero surprises.'],
                        ] as $idx => $pillar)
                            <div class="feature-card will-reveal delay-{{ $idx + 1 }} p-6 rounded-2xl bg-white border border-slate-200 space-y-4">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center"
                                     style="color: {{ $primaryColor }};">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $pillar['icon'] }}"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-black text-base text-slate-900">{{ $pillar['title'] }}</h4>
                                    <p class="text-xs text-slate-600 leading-relaxed mt-1.5">{{ $pillar['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif



        {{-- ══════════════════════════════════════════════════════════════════
             § 9. LATEST NEWS & GUIDES
             ══════════════════════════════════════════════════════════════════ --}}
        @if($homepageSections['show_news'] ?? true)
            <section class="py-20 bg-slate-50 border-t border-slate-100">
                <div class="w-full max-w-[1800px] mx-auto px-4 sm:px-8 lg:px-14 xl:px-20">
                    <div class="flex items-end justify-between mb-12">
                        <div class="will-reveal">
                            <span class="text-[11px] font-black uppercase tracking-[.18em]" style="color: {{ $primaryColor }};">Moto Journalism</span>
                            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-1">Latest News & Updates</h2>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach([
                            ['img'=>'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80','tag'=>"Buyer's Guide","date"=>'Oct 2026','title'=>'Top 5 Superbikes to Buy in 2026 for Track and Street Riding','desc'=>'Power delivery, lean-angle electronics, and maintenance costs compared across flagships.'],
                            ['img'=>'https://images.unsplash.com/photo-1558981359-219d6364c9c8?auto=format&fit=crop&w=600&q=80','tag'=>'Safety Standards','date'=>'Oct 2026','title'=>'Choosing the Right ECE 22.06 Helmet and Carbon Fiber Gear','desc'=>'Visor clarity, EPS foam density, ventilation channels, and emergency cheek pad removal.'],
                            ['img'=>'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=600&q=80','tag'=>'Touring Handbook','date'=>'Oct 2026','title'=>'Pre-Touring Checklist: Chain Slack, Coolant, and Tire Care','desc'=>'Inspect tread depth, brake pads, and suspension preload before cross-country road trips.'],
                        ] as $idx => $news)
                            <div class="will-reveal delay-{{ $idx + 1 }} bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm group hover:shadow-xl transition-shadow duration-300">
                                <div class="aspect-[16/10] overflow-hidden bg-slate-100">
                                    <img src="{{ $news['img'] }}" alt="{{ $news['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" loading="lazy">
                                </div>
                                <div class="p-6 space-y-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black uppercase tracking-wider" style="color: {{ $primaryColor }};">{{ $news['tag'] }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span class="text-[10px] text-slate-400 font-bold">{{ $news['date'] }}</span>
                                    </div>
                                    <h4 class="font-black text-base text-slate-900 transition-colors leading-snug">{{ $news['title'] }}</h4>
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ $news['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ══════════════════════════════════════════════════════════════════
             § 10. ABOUT + OPERATING HOURS
             ══════════════════════════════════════════════════════════════════ --}}
        @if($homepageSections['show_about'] ?? true)
            <section id="about" class="py-20 bg-white border-t border-slate-100">
                <div class="w-full max-w-[1800px] mx-auto px-4 sm:px-8 lg:px-14 xl:px-20">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-start">

                        {{-- About Column --}}
                        <div class="will-reveal space-y-6">
                            <span class="text-[11px] font-black uppercase tracking-[.18em]" style="color: {{ $primaryColor }};">About Our Dealership</span>
                            <h3 class="text-3xl font-black text-slate-900">Welcome to<br>{{ $showroom->name }}</h3>
                            <p class="text-slate-600 leading-relaxed text-sm">{{ $showroom->about_text }}</p>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Location</span>
                                    <p class="text-slate-900 font-bold text-sm mt-0.5">{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</p>
                                </div>
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Dealership Status</span>
                                    <p class="text-emerald-600 font-bold text-sm mt-0.5 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Verified Official Partner
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3 pt-2">
                                @if($showroom->phone)
                                    <a href="tel:{{ $showroom->phone }}" class="px-5 py-3 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center gap-2 hover:bg-slate-800 transition-all">
                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        Call {{ $showroom->phone }}
                                    </a>
                                @endif
                                @if($showroom->getSetting('whatsapp_number'))
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $showroom->getSetting('whatsapp_number')) }}" target="_blank"
                                       class="px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-2 transition-all">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.586 1.761.882 2.796.882h.005c3.18 0 5.766-2.586 5.767-5.766.001-3.181-2.585-5.769-5.772-5.769zm0 10.375c-.911 0-1.636-.264-2.399-.714l-.171-.102-1.782.467.476-1.737-.113-.179c-.482-.767-.736-1.53-.735-2.344.001-2.408 1.959-4.366 4.368-4.366 2.409 0 4.368 1.959 4.369 4.367 0 2.409-1.959 4.368-4.372 4.368z"/></svg>
                                        WhatsApp
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Hours Card --}}
                        <div id="hours" class="will-reveal delay-2 bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between mb-5">
                                <h4 class="text-base font-black text-slate-900">Showroom Pit Hours</h4>
                                @if($showroom->isOpenNow())
                                    <span class="px-3 py-1.5 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Open Now
                                    </span>
                                @else
                                    <span class="px-3 py-1.5 rounded-full text-xs font-black bg-amber-50 text-amber-700 border border-amber-200">
                                        Closed Now
                                    </span>
                                @endif
                            </div>
                            @php $todayName = strtolower(now()->format('l')); @endphp
                            <div class="divide-y divide-slate-100 text-xs">
                                @foreach($showroom->getOperatingHours() as $day => $data)
                                    <div class="py-3 flex items-center justify-between {{ $todayName === $day ? 'font-black bg-slate-50 px-2.5 rounded-xl' : 'text-slate-600' }}"
                                         @if($todayName === $day) style="color: {{ $primaryColor }};" @endif>
                                        <span class="capitalize">{{ $day }} @if($todayName === $day) (Today) @endif</span>
                                        <span class="font-bold">
                                            @if(!empty($data['is_closed'])) <span class="text-rose-500">Closed</span>
                                            @else {{ $data['open'] ?? '09:30' }} – {{ $data['close'] ?? '20:00' }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        @endif

    </main>

    @include('showrooms.partials.footer')

    {{-- ══════════════════════════════════════════════════════════════════
         INTERSECTION OBSERVER — Scroll-Reveal Animations
         ══════════════════════════════════════════════════════════════════ --}}
    <script>
        (function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

            document.querySelectorAll('.will-reveal').forEach(el => observer.observe(el));
        })();
    </script>

</x-showroom-layout>
