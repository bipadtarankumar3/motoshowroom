<x-guest-layout title="Create Your Account — MotoShowroom SaaS">
    <div class="min-h-screen lg:h-screen w-full flex flex-col lg:flex-row bg-slate-50 text-slate-900 selection:bg-red-600 selection:text-white lg:overflow-hidden"
         x-data="{
             tab: 'register',
             termsModalOpen: false,
             showPassword: false,
             showPasswordConfirm: false,
             activeSlide: 0,
             paused: false,
             slides: [
                 {
                     image: '{{ asset('images/hero/ducati_diavel_red.jpg') }}',
                     tag: 'Flagship Muscle Cruiser',
                     title: 'Ducati Diavel V4 Redline',
                     location: 'Apex Moto Hub · Bangalore',
                     specs: '1158 cc · 168 HP · 360° Live Tour',
                     token: '₹5,000 Reservation Token'
                 },
                 {
                     image: '{{ asset('images/hero/ducati_white.jpg') }}',
                     tag: 'Virtual 360° Pedestal',
                     title: 'Ducati Panigale V4 White Speciale',
                     location: 'Velocity Superbikes · Mumbai',
                     specs: '1103 cc · 214 HP · 4K Telemetry',
                     token: '₹10,000 Booking Deposit'
                 },
                 {
                     image: '{{ asset('images/hero/hero_adventure_red.jpg') }}',
                     tag: 'Adventure Touring',
                     title: 'Multistrada V4 Rally Studio',
                     location: 'Apex Moto Hub · Bangalore',
                     specs: '1158 cc · Semi-Active Suspension',
                     token: 'Instant Test Ride Available'
                 },
                 {
                     image: '{{ asset('images/hero/yamaha_blue.jpg') }}',
                     tag: 'Track Racing Series',
                     title: 'Yamaha YZF-R1M Factory Bay',
                     location: 'Torque Motors · Delhi NCR',
                     specs: '998 cc · Öhlins ERS · Quickshifter',
                     token: 'Verified Dealership Stock'
                 },
                 {
                     image: '{{ asset('images/hero/yellow_bike_front.jpg') }}',
                     tag: 'Streetfighter Edition',
                     title: 'Streetfighter V4 SP Corsa',
                     location: 'Speedway Hub · Hyderabad',
                     specs: '1103 cc · 208 HP · Biplane Wings',
                     token: '₹7,500 Booking Token'
                 }
             ],
             next() {
                 this.activeSlide = (this.activeSlide + 1) % this.slides.length;
             },
             prev() {
                 this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
             }
         }"
         x-init="setInterval(() => { if (! paused) { next(); } }, 5500)">

        <!-- MOBILE TOP STICKY PILL TABS -->
        <div class="lg:hidden sticky top-0 z-40 w-full py-3 bg-slate-50/95 backdrop-blur-md border-b border-slate-200/80 flex items-center justify-center shadow-xs">
            <div class="flex items-center justify-center gap-2.5" role="tablist">
                <!-- Tab: Create Account -->
                <button type="button"
                        role="tab"
                        id="tab-register"
                        @click="tab = 'register'"
                        :class="tab === 'register'
                            ? 'bg-slate-900 text-white shadow-md shadow-slate-900/20 border-slate-900 font-bold'
                            : 'bg-white/90 hover:bg-white text-slate-700 border-slate-200 font-semibold shadow-2xs hover:border-slate-300'"
                        class="px-5 sm:px-6 py-2 rounded-full text-xs sm:text-sm transition-all duration-200 flex items-center gap-2 border">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Create Account</span>
                </button>

                <!-- Tab: Review Tour & Demo -->
                <button type="button"
                        role="tab"
                        id="tab-demo"
                        @click="tab = 'demo'"
                        :class="tab === 'demo'
                            ? 'bg-slate-900 text-white shadow-md shadow-slate-900/20 border-slate-900 font-bold'
                            : 'bg-white/90 hover:bg-white text-slate-700 border-slate-200 font-semibold shadow-2xs hover:border-slate-300'"
                        class="px-5 sm:px-6 py-2 rounded-full text-xs sm:text-sm transition-all duration-200 flex items-center gap-2 border">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Review Tour & Demo</span>
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                </button>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- LEFT SECTION (70% on desktop): Showroom Carousel & Story -->
        <!-- Shown on desktop; on mobile shown when tab is 'demo'     -->
        <!-- ======================================================== -->
        <section id="showroom-section"
                 class="w-full lg:w-[68%] xl:w-[70%] min-h-screen lg:h-full flex flex-col justify-between p-5 sm:p-8 xl:p-10 2xl:p-12 bg-slate-50/70 border-r border-slate-200/80 relative overflow-y-auto"
                 :class="tab === 'demo' ? 'flex' : 'hidden lg:flex'">

            <!-- Ambient Glow Accents -->
            <div class="pointer-events-none absolute -top-28 -left-28 w-96 h-96 rounded-full bg-red-400/10 blur-3xl"></div>
            <div class="pointer-events-none absolute top-1/2 -right-28 w-96 h-96 rounded-full bg-amber-400/10 blur-3xl"></div>
            <div class="pointer-events-none absolute inset-0 opacity-[0.03] bg-[radial-gradient(#0f172a_1px,transparent_1px)] [background-size:20px_20px]"></div>

            <!-- Top Header & Live Badges -->
            <header class="relative z-10 flex items-center justify-between gap-4 shrink-0 pb-2">
                <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-red-600 to-amber-500 p-0.5 shadow-md shadow-red-600/20 group-hover:scale-105 transition-transform">
                        <div class="w-full h-full bg-white rounded-[10px] flex items-center justify-center text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-lg font-black font-display tracking-tight text-slate-900 group-hover:text-red-600 transition">MOTOSHOWROOM</span>
                        <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400">MOTORCYCLE DEALERSHIP SAAS</span>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-slate-200 shadow-2xs text-[11px] font-semibold text-slate-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Multi-Tenant Architecture</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 shadow-2xs text-[11px] font-bold text-red-700">
                        <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Razorpay Verified</span>
                    </span>
                </div>
            </header>

            <!-- Showroom 360 Carousel Visual -->
            <div class="relative z-10 flex-1 my-3 flex flex-col justify-center min-h-0">
                <div class="relative w-full h-full min-h-[440px] 2xl:min-h-[500px] rounded-2xl overflow-hidden shadow-xl border border-slate-200/90 bg-white group flex flex-col"
                     @mouseenter="paused = true"
                     @mouseleave="paused = false">

                    <!-- Carousel Image Container -->
                    <div class="relative flex-1 w-full min-h-[360px] overflow-hidden bg-gradient-to-b from-slate-100/90 via-slate-50 to-slate-100/70">
                        <template x-for="(slide, index) in slides" :key="index">
                            <div class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                                 x-show="activeSlide === index"
                                 x-transition:enter="transition-opacity duration-700 ease-out"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition-opacity duration-700 ease-in"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0">
                                <img :src="slide.image"
                                     :alt="slide.title"
                                     class="w-full h-full object-cover object-center transform group-hover:scale-[1.01] transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-slate-900/5 pointer-events-none"></div>
                            </div>
                        </template>

                        <!-- Top Floating Badge: Live 360 Tour & Slide Counter -->
                        <div class="absolute top-4 left-4 flex items-center gap-2">
                            <span class="px-3.5 py-1.5 rounded-full bg-white/95 backdrop-blur-md border border-white/80 shadow-md text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                                <span>Live Showroom Tour</span>
                                <span class="text-slate-300">·</span>
                                <span x-text="slides[activeSlide].tag"></span>
                            </span>
                            <span class="px-3 py-1.5 rounded-full bg-slate-900/80 backdrop-blur-md text-white text-xs font-mono font-semibold">
                                <span x-text="'0' + (activeSlide + 1)"></span>/<span x-text="'0' + slides.length"></span>
                            </span>
                        </div>

                        <!-- Top Right Badge: 360 Virtual View -->
                        <div class="absolute top-4 right-4">
                            <span class="px-3.5 py-1.5 rounded-full bg-white/95 backdrop-blur-md border border-white/80 shadow-md text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-red-600 animate-spin" style="animation-duration: 8s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>360° Interactive Showroom</span>
                            </span>
                        </div>

                        <!-- Navigation Arrows (Left / Right) -->
                        <button type="button"
                                @click="prev()"
                                aria-label="Previous bike slide"
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/90 hover:bg-white shadow-lg text-slate-800 flex items-center justify-center transition hover:scale-110 active:scale-95 border border-slate-200/60 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button type="button"
                                @click="next()"
                                aria-label="Next bike slide"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/90 hover:bg-white shadow-lg text-slate-800 flex items-center justify-center transition hover:scale-110 active:scale-95 border border-slate-200/60 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Bottom Floating Spec Overlay Card -->
                        <div class="absolute bottom-4 left-4 right-4 bg-white/95 backdrop-blur-md rounded-xl p-3.5 border border-white/80 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-black text-slate-900" x-text="slides[activeSlide].title"></h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                                          x-text="slides[activeSlide].token">
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium" x-text="slides[activeSlide].location"></p>
                            </div>
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span x-text="slides[activeSlide].specs"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Dots & Auto-cycle indicator bar -->
                    <div class="px-4 py-2.5 bg-slate-50/90 border-t border-slate-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <template x-for="(slide, i) in slides" :key="'dot-' + i">
                                <button type="button"
                                        @click="activeSlide = i"
                                        :class="activeSlide === i ? 'w-8 bg-red-600' : 'w-2 bg-slate-300 hover:bg-slate-400'"
                                        class="h-2 rounded-full transition-all duration-300"
                                        :title="'Slide ' + (i + 1)"></button>
                            </template>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">Click arrows or dots to cycle showcase</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Story, Stats & 3 Feature Cards -->
            <div class="relative z-10 shrink-0 space-y-3 pt-2">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-0.5 bg-red-600 rounded-full"></span>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">ALL-IN-ONE DEALERSHIP MANAGEMENT PLATFORM</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl xl:text-3xl font-black font-display text-slate-900 tracking-tight leading-snug">
                            Join 250+ Showrooms. <span class="bg-gradient-to-r from-red-600 to-amber-500 bg-clip-text text-transparent">Power Retail.</span>
                        </h1>
                    </div>
                    <div class="grid grid-cols-4 gap-2.5 shrink-0">
                        <div class="bg-white px-3 py-1.5 rounded-xl border border-slate-200 text-center shadow-2xs">
                            <div class="text-sm font-black text-slate-900">250+</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase">Dealers</div>
                        </div>
                        <div class="bg-white px-3 py-1.5 rounded-xl border border-slate-200 text-center shadow-2xs">
                            <div class="text-sm font-black text-red-600">12K+</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase">Bikes</div>
                        </div>
                        <div class="bg-white px-3 py-1.5 rounded-xl border border-slate-200 text-center shadow-2xs">
                            <div class="text-sm font-black text-emerald-600">₹4.2Cr</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase">Tokens</div>
                        </div>
                        <div class="bg-white px-3 py-1.5 rounded-xl border border-slate-200 text-center shadow-2xs">
                            <div class="text-sm font-black text-amber-600">99.9%</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase">Uptime</div>
                        </div>
                    </div>
                </div>

                <!-- 3 Feature Cards in Horizontal Row -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-white rounded-xl p-3 border border-slate-200/90 shadow-2xs flex items-center gap-3 hover:border-red-200 transition">
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate">Instant Onboarding</h4>
                            <p class="text-[10px] text-slate-500 truncate">Free buyer & dealer account</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-3 border border-slate-200/90 shadow-2xs flex items-center gap-3 hover:border-red-200 transition">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate">Interactive 360° Spins</h4>
                            <p class="text-[10px] text-slate-500 truncate">Full 4K visualizer access</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-3 border border-slate-200/90 shadow-2xs flex items-center gap-3 hover:border-red-200 transition">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate">Protected Bookings</h4>
                            <p class="text-[10px] text-slate-500 truncate">Razorpay secured holding</p>
                        </div>
                    </div>
                </div>

                <!-- Footer line -->
                <div class="pt-1 flex items-center justify-between text-[11px] text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Enterprise Ready Cloud</span>
                        <span class="text-slate-300">|</span>
                        <span>Multi-Tenant Architecture</span>
                    </div>
                    <span>Built for Next-Gen Motorcycle Retail</span>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- RIGHT SECTION (STRICTLY 30%): Registration Panel         -->
        <!-- Shown on desktop; on mobile shown when tab is 'register'  -->
        <!-- ======================================================== -->
        <section class="w-full lg:w-[32%] xl:w-[30%] min-w-[340px] min-h-screen lg:h-full flex flex-col justify-center items-center p-4 sm:p-6 xl:p-8 bg-slate-50/70 border-l border-slate-200/90 relative z-20 overflow-y-auto bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:18px_18px]"
                 :class="tab === 'register' ? 'flex' : 'hidden lg:flex'">
            <!-- Top Accent Gradient Line -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-red-600 via-rose-500 to-amber-500"></div>

            <div class="w-full max-w-[420px] mx-auto my-auto space-y-3.5 py-4">

                <!-- MAIN ELEVATED WHITE CARD -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xl shadow-slate-200/60 p-6 sm:p-7 space-y-4 relative">

                    <!-- Brand Logo -->
                    <div class="flex items-center justify-center gap-2.5 pt-1">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-red-600 to-amber-500 p-0.5 shadow-md shadow-red-600/20">
                            <div class="w-full h-full bg-white rounded-[8px] flex items-center justify-center text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-left">
                            <div class="text-base font-black font-display tracking-tight text-slate-900 leading-tight">MOTOSHOWROOM</div>
                            <div class="text-[9px] font-bold uppercase tracking-widest text-slate-400 leading-tight">MULTI-TENANT DEALERSHIP PLATFORM</div>
                        </div>
                    </div>

                    <!-- Badge -->
                    <div class="text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold tracking-wide">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Fast & Free Registration</span>
                        </span>
                    </div>

                    <!-- Heading & Subtitle -->
                    <div class="text-center space-y-1">
                        <h2 class="text-2xl font-black font-display text-slate-900 tracking-tight">
                            Create your account
                        </h2>
                        <p class="text-xs text-slate-500">
                            Join MotoShowroom SaaS to manage your bike showroom, bookings, or test rides.
                        </p>
                    </div>

                    <!-- REGISTER FORM VIEW -->
                    <div class="space-y-4">

                        <!-- Google Sign-Up Button -->
                        <div>
                            <a href="{{ route('auth.google') }}"
                               id="btn-google-register"
                               class="w-full py-3 px-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm border border-slate-200/90 flex items-center justify-center gap-3 transition shadow-2xs hover:shadow-xs">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                                    <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.4l3.7 2.9C6.5 7.4 9 5 12 5z"/>
                                    <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.7-.2-2.3H12v4.6h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.9z"/>
                                    <path fill="#FBBC05" d="M5.6 14.7c-.2-.7-.4-1.5-.4-2.7s.2-2 .4-2.7L1.9 6.4C.7 8.8 0 10.8 0 12s.7 3.2 1.9 5.6l3.7-2.9z"/>
                                    <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2-6.4-4.8L1.9 16.4C3.7 20.2 7.5 23 12 23z"/>
                                </svg>
                                <span>Sign up with Google</span>
                            </a>
                        </div>

                        <!-- Divider: OR REGISTER WITH EMAIL -->
                        <div class="relative flex items-center justify-center my-2">
                            <div class="border-t border-slate-200 w-full"></div>
                            <span class="bg-white px-3 text-[10px] font-bold tracking-wider text-slate-400 uppercase absolute">OR REGISTER WITH EMAIL</span>
                        </div>

                        <!-- Main Form -->
                        <form method="POST" action="{{ route('register') }}" class="space-y-3" id="form-register">
                            @csrf

                            <!-- Full Name Input -->
                            <div class="text-left">
                                <label for="name" class="block text-xs font-bold text-slate-700 mb-1">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           value="{{ old('name') }}"
                                           required
                                           autofocus
                                           placeholder="e.g. John Doe"
                                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50/50 hover:bg-white focus:bg-white border @error('name') border-red-500 @else border-slate-200/90 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm font-medium transition shadow-2xs">
                                </div>
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Address Input -->
                            <div class="text-left">
                                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           value="{{ old('email') }}"
                                           required
                                           placeholder="name@company.com"
                                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50/50 hover:bg-white focus:bg-white border @error('email') border-red-500 @else border-slate-200/90 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm font-medium transition shadow-2xs">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone & City 2-Column Row -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-left">
                                <div>
                                    <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">
                                        Phone Number
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                        <input type="text"
                                               name="phone"
                                               id="phone"
                                               value="{{ old('phone') }}"
                                               placeholder="+91 98765..."
                                               class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-slate-50/50 hover:bg-white focus:bg-white border @error('phone') border-red-500 @else border-slate-200/90 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm font-medium transition shadow-2xs">
                                    </div>
                                    @error('phone')
                                        <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="city" class="block text-xs font-bold text-slate-700 mb-1">
                                        City
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <input type="text"
                                               name="city"
                                               id="city"
                                               value="{{ old('city') }}"
                                               placeholder="e.g. Mumbai"
                                               class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-slate-50/50 hover:bg-white focus:bg-white border @error('city') border-red-500 @else border-slate-200/90 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm font-medium transition shadow-2xs">
                                    </div>
                                    @error('city')
                                        <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password & Confirm Password Row -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-left">
                                <div>
                                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1">
                                        Password <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                        <input :type="showPassword ? 'text' : 'password'"
                                               name="password"
                                               id="password"
                                               required
                                               placeholder="Min. 8 chars"
                                               class="w-full pl-9 pr-8 py-2.5 rounded-xl bg-slate-50/50 hover:bg-white focus:bg-white border @error('password') border-red-500 @else border-slate-200/90 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm font-medium transition shadow-2xs">
                                        <button type="button"
                                                @click="showPassword = !showPassword"
                                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                                            <svg x-show="!showPassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showPassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">
                                        Confirm <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                        <input :type="showPasswordConfirm ? 'text' : 'password'"
                                               name="password_confirmation"
                                               id="password_confirmation"
                                               required
                                               placeholder="Re-enter pass"
                                               class="w-full pl-9 pr-8 py-2.5 rounded-xl bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200/90 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm font-medium transition shadow-2xs">
                                        <button type="button"
                                                @click="showPasswordConfirm = !showPasswordConfirm"
                                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                                            <svg x-show="!showPasswordConfirm" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showPasswordConfirm" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms & Conditions Acceptance Checkbox -->
                            <div class="pt-1.5 text-left">
                                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                    <input type="checkbox"
                                           name="terms"
                                           id="terms-accept-checkbox"
                                           value="1"
                                           required
                                           checked
                                           class="w-4 h-4 mt-0.5 rounded border-slate-300 text-red-600 focus:ring-red-500/20 shrink-0">
                                    <span class="text-xs text-slate-600 leading-snug">
                                        I agree to the
                                        <button type="button"
                                                @click="termsModalOpen = true"
                                                class="font-bold text-red-600 hover:text-red-700 hover:underline">
                                            Terms & Conditions
                                        </button>
                                        and
                                        <a href="{{ route('privacy') }}"
                                           target="_blank"
                                           class="font-bold text-red-600 hover:text-red-700 hover:underline">
                                            Privacy Policy
                                        </a>.
                                    </span>
                                </label>
                                @error('terms')
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Primary Action Button: Create Account → -->
                            <button type="submit"
                                    id="btn-submit-register"
                                    class="w-full py-3.5 px-5 mt-2 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-bold text-sm sm:text-base shadow-lg shadow-red-600/25 hover:shadow-xl hover:shadow-red-600/35 hover:scale-[1.005] active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2">
                                <span>Create Account</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Footer: Already registered? Sign in -->
                    <div class="text-center text-xs text-slate-500 pt-1">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-bold text-red-600 hover:text-red-700 hover:underline ml-1">
                            Sign In
                        </a>
                    </div>

                    <!-- Security & Terms Footer -->
                    <div class="pt-2 border-t border-slate-100 space-y-1 text-center">
                        <div class="flex items-center justify-center gap-1.5 text-[11px] text-slate-600 font-semibold">
                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>256–Bit SSL Encrypted • Multi-Tenant Isolated</span>
                        </div>
                        <p class="text-[10px] text-slate-400">
                            By registering, your dealership and personal data are strictly isolated and protected.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- TERMS & CONDITIONS ON-PAGE MODAL (Light Theme)           -->
        <!-- ======================================================== -->
        <div x-cloak
             x-show="termsModalOpen"
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
             role="dialog"
             aria-modal="true">

            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"
                 x-show="termsModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="termsModalOpen = false"></div>

            <!-- Modal Window -->
            <div class="relative bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col z-10 overflow-hidden"
                 x-show="termsModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                 @click.away="termsModalOpen = false">

                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/70 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center border border-red-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-black font-display text-slate-900">MotoShowroom Terms & Conditions</h3>
                            <p class="text-xs text-slate-500">Platform SaaS, Dealership & Booking Guidelines</p>
                        </div>
                    </div>
                    <button type="button"
                            @click="termsModalOpen = false"
                            class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 overflow-y-auto space-y-4 text-xs sm:text-sm text-slate-600 leading-relaxed">
                    <div class="bg-amber-50 rounded-xl p-3.5 border border-amber-200 text-amber-900 space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-xs text-amber-800">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span>Platform Agreement Notice</span>
                        </div>
                        <p class="text-xs">
                            By creating an account, you agree to comply with MotoShowroom SaaS platform rules, payment guidelines, and vehicle reservation protocols.
                        </p>
                    </div>

                    <div class="space-y-1.5">
                        <h4 class="font-bold text-slate-900 text-sm">1. Account Registration & Multi-Tenant Boundaries</h4>
                        <p>
                            You must provide accurate personal and contact details. Each dealership workspace operates under strict multi-tenant data segregation. Account sharing across different legal dealership entities is strictly prohibited.
                        </p>
                    </div>

                    <div class="space-y-1.5">
                        <h4 class="font-bold text-slate-900 text-sm">2. Booking Tokens & Razorpay Security</h4>
                        <p>
                            Token payments to reserve motorcycle allocations or book service slots are processed via 256-bit SSL encrypted Razorpay payment gateways. MotoShowroom does not store raw credit card numbers. Refund policies are subject to individual dealer agreements and statutory consumer regulations.
                        </p>
                    </div>

                    <div class="space-y-1.5">
                        <h4 class="font-bold text-slate-900 text-sm">3. Interactive 360° Visualizer & Specifications</h4>
                        <p>
                            Motorcycle images, 360-degree turntable spins, and technical specifications are provided for illustrative demonstration. Final pricing, road taxes, and delivery timelines are formalized via dealership invoices upon vehicle handover.
                        </p>
                    </div>

                    <div class="space-y-1.5">
                        <h4 class="font-bold text-slate-900 text-sm">4. Privacy & Data Handling</h4>
                        <p>
                            We value your confidentiality. Your personal information is processed strictly to maintain your account, process reservations, and manage customer-dealer communications.
                        </p>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('terms') }}" target="_blank" class="text-xs font-bold text-red-600 hover:underline flex items-center gap-1">
                            <span>Read Full Comprehensive Terms on Dedicated Page</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                    <span class="text-xs text-slate-500 font-medium">
                        By continuing, you agree to these guidelines.
                    </span>
                    <div class="flex items-center gap-2.5 w-full sm:w-auto">
                        <button type="button"
                                @click="termsModalOpen = false"
                                class="w-full sm:w-auto px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                            Close
                        </button>
                        <button type="button"
                                @click="document.getElementById('terms-accept-checkbox').checked = true; termsModalOpen = false"
                                class="w-full sm:w-auto px-5 py-2 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white text-xs font-bold shadow-md shadow-red-600/20 transition">
                            I Agree &amp; Accept
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-guest-layout>
