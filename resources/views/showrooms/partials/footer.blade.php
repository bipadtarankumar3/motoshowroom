{{-- ============================================================
     SHOWROOM PUBLIC FOOTER — Latest Design Bike-Themed Dealership Footer
     ============================================================ --}}
<footer class="bg-white border-t border-slate-200 text-slate-600 relative overflow-hidden">

    {{-- ═══ MOTORCYCLE RACING / SPEEDLINE TOP ACCENT ═══ --}}
    <div class="h-1.5 w-full bg-gradient-to-r from-slate-900 via-rose-600 to-[#FF6B00] opacity-90"></div>

    {{-- ═══ RIDER ASSURANCE STRIP (4 MOTORBIKE PILLARS) ═══ --}}
    <div class="border-b border-slate-100 bg-slate-50/70 py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

                {{-- Pillar 1 --}}
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-center text-rose-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-900 uppercase tracking-tight">100% Genuine OEM Spares</p>
                        <p class="text-[11px] text-slate-500">Factory-certified parts</p>
                    </div>
                </div>

                {{-- Pillar 2 --}}
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 via-[#FF6B00] to-orange-600 shadow-md shadow-orange-500/25 flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-900 uppercase tracking-tight">Express Service Bay</p>
                        <p class="text-[11px] text-slate-500">60-minute quick maintenance</p>
                    </div>
                </div>

                {{-- Pillar 3 --}}
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-center text-blue-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-900 uppercase tracking-tight">Multi-Point Bike Check</p>
                        <p class="text-[11px] text-slate-500">Dyno tested & road ready</p>
                    </div>
                </div>

                {{-- Pillar 4 --}}
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-900 uppercase tracking-tight">Instant EMI & Finance</p>
                        <p class="text-[11px] text-slate-500">Low interest rider plans</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══ MAIN 4-COLUMN MOTORCYCLE FOOTER ═══ --}}
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-12 pt-14 pb-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-slate-100">

            {{-- ═══ COL 1: DEALERSHIP HERITAGE & SOCIAL ═══ --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3.5">
                    @if($showroom->logo_url)
                        <img src="{{ $showroom->logo_url }}" alt="{{ $showroom->name }}"
                             class="h-12 w-auto max-w-[150px] rounded-xl object-contain drop-shadow-xs">
                    @else
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-white text-lg font-display shadow-md shadow-slate-900/10 flex-shrink-0"
                             style="background: linear-gradient(135deg, {{ $showroom->brand_color_primary ?? '#dc2626' }}, {{ $showroom->brand_color_accent ?? '#991b1b' }})">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19.5 8c-1.38 0-2.5 1.12-2.5 2.5 0 .28.05.55.13.8L14.7 13H10l-1.3-2.6A3.49 3.49 0 005.5 9C3.57 9 2 10.57 2 12.5S3.57 16 5.5 16c1.61 0 2.96-1.09 3.37-2.57L11.5 15h4.15l-1.15 2.3a2.5 2.5 0 102.24 1.12L18.4 15.1c.35.13.72.2 1.1.2 1.93 0 3.5-1.57 3.5-3.5S21.43 8 19.5 8zm-14 6c-.83 0-1.5-.67-1.5-1.5S4.67 11 5.5 11s1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm14-1.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="leading-tight">
                        <p class="font-black text-slate-900 text-base font-display">{{ $showroom->name }}</p>
                        <p class="text-xs text-slate-500 font-medium">{{ $showroom->city ?? 'Motorcycle' }}, {{ $showroom->state ?? 'IN' }}</p>
                    </div>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed">
                    {{ $showroom->tagline ?? 'Your premier authorized dealership for performance superbikes, adventure tourers, and certified motorcycle workshop care.' }}
                </p>

                {{-- Social Icons --}}
                <div class="flex items-center gap-2 pt-1">
                    @foreach($showroom->getSocialLinks() as $network => $url)
                        @if(!empty($url))
                            <a href="{{ $url }}" target="_blank" rel="noopener"
                               class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 flex items-center justify-center transition capitalize text-xs font-bold shadow-2xs"
                               title="{{ ucfirst($network) }}">
                                {{ strtoupper(substr($network, 0, 2)) }}
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Helmet Safety Badge --}}
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200/80 text-[11px] font-bold text-amber-800">
                    <span>🏍️</span>
                    <span>Ride Responsibly. Always Wear a Helmet.</span>
                </div>
            </div>

            {{-- ═══ COL 2: MOTORCYCLE LINEUP ═══ --}}
            <div>
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Motorcycle Lineup</span>
                </h3>
                <ul class="space-y-2.5 text-sm">
                    <li>
                        <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="text-slate-600 hover:text-slate-950 font-medium transition flex items-center justify-between group">
                            <span>Browse All Motorcycles</span>
                            <span class="text-xs text-slate-400 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('showrooms.bikes.index', ['showroom' => $showroom, 'category' => 'Sport']) }}" class="text-slate-600 hover:text-slate-950 font-medium transition flex items-center justify-between group">
                            <span>Supersport & Track Bikes</span>
                            <span class="text-xs text-slate-400 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('showrooms.bikes.index', ['showroom' => $showroom, 'category' => 'Cruiser']) }}" class="text-slate-600 hover:text-slate-950 font-medium transition flex items-center justify-between group">
                            <span>Cruisers & Tourers</span>
                            <span class="text-xs text-slate-400 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('showrooms.bikes.index', ['showroom' => $showroom, 'category' => 'Adventure']) }}" class="text-slate-600 hover:text-slate-950 font-medium transition flex items-center justify-between group">
                            <span>Adventure & Off-Road</span>
                            <span class="text-xs text-slate-400 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('showrooms.enquiries.create', $showroom) }}" class="text-rose-600 hover:text-rose-700 font-bold transition flex items-center gap-1">
                            <span>⚡ Book a Test Ride</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- ═══ COL 3: PITSTOP & RIDER CARE ═══ --}}
            <div>
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#FF6B00]"></span>
                    <span>Pitstop & Care</span>
                </h3>
                <ul class="space-y-2.5 text-sm">
                    <li>
                        <a href="{{ route('showrooms.services.create', $showroom) }}" class="text-slate-600 hover:text-slate-950 font-medium transition flex items-center justify-between group">
                            <span>Book Workshop Service</span>
                            <span class="text-xs text-slate-400 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('showrooms.services.track', $showroom) }}" class="text-slate-600 hover:text-slate-950 font-medium transition flex items-center justify-between group">
                            <span>Track My Bike Service</span>
                            <span class="text-xs text-slate-400 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('showrooms.promotions.index', $showroom) }}" class="text-slate-600 hover:text-slate-950 font-medium transition flex items-center justify-between group">
                            <span>Seasonal Offers & Scratch Cards</span>
                            <span class="text-xs text-slate-400 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('showrooms.careers.index', $showroom) }}" class="text-slate-600 hover:text-slate-950 font-medium transition flex items-center justify-between group">
                            <span>Dealership Careers</span>
                            <span class="text-xs text-slate-400 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('showrooms.show', $showroom) }}#about" class="text-slate-600 hover:text-slate-950 font-medium transition">
                            <span>About Showroom Pit</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- ═══ COL 4: DEALERSHIP LOCATION & TIMINGS ═══ --}}
            <div>
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Showroom Pit Hours</span>
                </h3>

                <ul class="space-y-3">
                    @if($showroom->phone)
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <div>
                                <a href="tel:{{ $showroom->phone }}" class="text-sm text-slate-800 hover:text-rose-600 font-bold transition">{{ $showroom->phone }}</a>
                                <p class="text-[10px] text-slate-400">Direct Sales & Service Desk</p>
                            </div>
                        </li>
                    @endif

                    @if($showroom->email)
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:{{ $showroom->email }}" class="text-xs text-slate-700 hover:text-slate-950 font-medium transition break-all">{{ $showroom->email }}</a>
                        </li>
                    @endif

                    @if($showroom->address)
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-xs text-slate-600 font-medium leading-relaxed">{{ $showroom->address }}</span>
                        </li>
                    @endif

                    <li class="pt-1">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] space-y-1">
                            <div class="flex justify-between font-semibold text-slate-700">
                                <span>Mon – Sat:</span>
                                <span>9:30 AM – 8:00 PM</span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>Sunday:</span>
                                <span>10:00 AM – 4:00 PM</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

        </div>

        {{-- ═══ BOTTOM BAR ═══ --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-8 text-xs text-slate-500">
            <p>
                &copy; {{ date('Y') }} <span class="text-slate-900 font-bold">{{ $showroom->name }}</span>. All rights reserved. Authorized Two-Wheeler Dealership.
            </p>
            <div class="flex items-center gap-4 text-slate-400">
                <a href="{{ route('showrooms.show', $showroom) }}#about" class="hover:text-slate-700 transition">About</a>
                <span>•</span>
                <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="hover:text-slate-700 transition">Bikes</a>
                <span>•</span>
                <a href="{{ route('showrooms.services.create', $showroom) }}" class="hover:text-slate-700 transition">Service</a>
                <span>•</span>
                <span class="text-slate-600 font-bold">MotoShowroom Cloud</span>
            </div>
        </div>
    </div>
</footer>
