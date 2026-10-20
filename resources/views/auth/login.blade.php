<x-guest-layout title="Sign In — MotoShowroom SaaS">
    <div class="min-h-screen lg:h-screen w-full flex flex-col lg:flex-row bg-slate-50 text-slate-900 selection:bg-red-600 selection:text-white lg:overflow-hidden"
         x-data="{
             tab: 'login',
             email: @js(old('email', '')),
             password: '',
             showPassword: false,
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
             },
             signInAs(role, e, p) {
                 this.email = e;
                 this.password = p;
                 this.tab = 'login';
                 this.$nextTick(() => {
                     document.getElementById('form-login').submit();
                 });
             },
             fillDemo(e, p) {
                 this.email = e;
                 this.password = p;
                 this.tab = 'login';
             }
         }"
         x-init="setInterval(() => { if (! paused) { next(); } }, 5500)">

        <!-- ======================================================== -->
        <!-- LEFT SECTION (70% on desktop): Showroom Carousel & Story -->
        <!-- Shown on desktop; on mobile shown when tab is 'demo'     -->
        <!-- ======================================================== -->
        <section id="showroom-section"
                 class="w-full lg:w-[68%] xl:w-[70%] h-full flex flex-col justify-between p-6 sm:p-8 xl:p-10 2xl:p-12 bg-slate-50/70 border-r border-slate-200/80 relative overflow-hidden"
                 :class="tab === 'demo' ? 'flex order-2 lg:order-1' : 'hidden lg:flex lg:order-1'">

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
                <div class="relative w-full h-full min-h-[460px] 2xl:min-h-[520px] rounded-2xl overflow-hidden shadow-xl border border-slate-200/90 bg-white group flex flex-col"
                     @mouseenter="paused = true"
                     @mouseleave="paused = false">

                    <!-- Carousel Image Container (Dynamically fills vertical space) -->
                    <div class="relative flex-1 w-full min-h-[380px] overflow-hidden bg-gradient-to-b from-slate-100/90 via-slate-50 to-slate-100/70">
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

                        <!-- Bottom Slide Info Pill -->
                        <div class="absolute bottom-4 left-4 right-4 sm:right-auto max-w-lg">
                            <div class="rounded-xl bg-white/95 backdrop-blur-md p-3.5 shadow-lg border border-white/80 space-y-1">
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="text-sm sm:text-base font-black text-slate-900 truncate" x-text="slides[activeSlide].title"></h3>
                                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full whitespace-nowrap" x-text="slides[activeSlide].token"></span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-slate-500 gap-2">
                                    <span x-text="slides[activeSlide].location"></span>
                                    <span class="font-semibold text-slate-700" x-text="slides[activeSlide].specs"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Carousel Prev / Next Controls -->
                        <div class="absolute bottom-4 right-4 hidden sm:flex items-center gap-2">
                            <button type="button"
                                    @click="prev()"
                                    class="w-9 h-9 rounded-full bg-white/95 hover:bg-white text-slate-700 hover:text-red-600 shadow-md flex items-center justify-center transition hover:scale-105 active:scale-95"
                                    title="Previous Slide">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button type="button"
                                    @click="next()"
                                    class="w-9 h-9 rounded-full bg-gradient-to-r from-red-600 to-amber-500 hover:from-red-500 hover:to-amber-500 text-white shadow-md flex items-center justify-center transition hover:scale-105 active:scale-95"
                                    title="Next Slide">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Slide Progress Thumbnails Strip -->
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
                <!-- Heading & Stats Row -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-0.5 bg-red-600 rounded-full"></span>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">ALL-IN-ONE DEALERSHIP MANAGEMENT PLATFORM</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl xl:text-3xl font-black font-display text-slate-900 tracking-tight leading-snug">
                            Run Your Showroom. <span class="bg-gradient-to-r from-red-600 to-amber-500 bg-clip-text text-transparent">Smarter.</span>
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
                            <h4 class="text-xs font-bold text-slate-900 truncate">Multi-Tenant Workspace</h4>
                            <p class="text-[10px] text-slate-500 truncate">Isolated DB, roles & subdomains</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-3 border border-slate-200/90 shadow-2xs flex items-center gap-3 hover:border-red-200 transition">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate">360° Virtual Showroom</h4>
                            <p class="text-[10px] text-slate-500 truncate">Interactive 4K spins & specs</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-3 border border-slate-200/90 shadow-2xs flex items-center gap-3 hover:border-red-200 transition">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 truncate">Secure Razorpay Billing</h4>
                            <p class="text-[10px] text-slate-500 truncate">Instant booking tokens & plans</p>
                        </div>
                    </div>
                </div>

                <!-- Footer line -->
                <div class="pt-1 flex items-center justify-between text-[11px] text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>All Systems Operational</span>
                        <span class="text-slate-300">|</span>
                        <span>Trusted by 1,000+ Dealerships</span>
                    </div>
                    <span>Built for Next-Gen Motorcycle Retail</span>
                </div>
            </div>
        </section>

        <!-- ======================================================== -->
        <!-- RIGHT SECTION (STRICTLY 30%): Modern Login & Demo Panel  -->
        <!-- ======================================================== -->
        <section class="w-full lg:w-[32%] xl:w-[30%] min-w-[360px] h-full flex flex-col justify-center items-center p-4 sm:p-6 xl:p-8 bg-slate-50/70 border-l border-slate-200/90 relative z-20 overflow-y-auto order-1 lg:order-2 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:18px_18px]">
            <!-- Top Accent Gradient Line -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-red-600 via-rose-500 to-amber-500"></div>

            <div class="w-full max-w-[420px] mx-auto my-auto space-y-3.5 py-4">

                <!-- TOP FLOATING PILL TABS (AskReview Style - Mobile Only) -->
                <div class="flex lg:hidden items-center justify-center gap-2.5" role="tablist">
                    <!-- Tab: Sign In -->
                    <button type="button"
                            role="tab"
                            id="tab-login"
                            @click="tab = 'login'"
                            :class="tab === 'login'
                                ? 'bg-slate-900 text-white shadow-md shadow-slate-900/20 border-slate-900 font-bold'
                                : 'bg-white/90 hover:bg-white text-slate-700 border-slate-200 font-semibold shadow-2xs hover:border-slate-300'"
                            class="px-5 sm:px-6 py-2.5 rounded-full text-xs sm:text-sm transition-all duration-200 flex items-center gap-2 border">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Sign In</span>
                    </button>

                    <!-- Tab: Review Tour & Demo -->
                    <button type="button"
                            role="tab"
                            id="tab-demo"
                            @click="tab = 'demo'; $nextTick(() => { document.getElementById('showroom-section')?.scrollIntoView({ behavior: 'smooth' }); })"
                            :class="tab === 'demo'
                                ? 'bg-slate-900 text-white shadow-md shadow-slate-900/20 border-slate-900 font-bold'
                                : 'bg-white/90 hover:bg-white text-slate-700 border-slate-200 font-semibold shadow-2xs hover:border-slate-300'"
                            class="px-5 sm:px-6 py-2.5 rounded-full text-xs sm:text-sm transition-all duration-200 flex items-center gap-2 border">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Review Tour & Demo</span>
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    </button>
                </div>

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
                            <span class="text-lg font-black font-display tracking-tight text-slate-900 block leading-none">MOTOSHOWROOM</span>
                            <span class="text-[8px] font-bold uppercase tracking-widest text-slate-400">Multi-Tenant Dealership Platform</span>
                        </div>
                    </div>

                    <!-- Business Dashboard / Dealer Console Pill Badge -->
                    <div class="text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold tracking-wide">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944z" clip-rule="evenodd"/>
                            </svg>
                            <span>Dealer Console</span>
                        </span>
                    </div>

                    <!-- Welcome Back Heading & Subtitle -->
                    <div class="text-center space-y-1">
                        <h2 class="text-2xl font-black font-display text-slate-900 tracking-tight">
                            Welcome back
                        </h2>
                        <p class="text-xs text-slate-500">
                            Sign in to manage your showroom, bikes, bookings, and customer test rides.
                        </p>
                    </div>

                    <!-- ============================= -->
                    <!-- TAB 1: LOGIN FORM VIEW        -->
                    <!-- ============================= -->
                    <div x-show="tab === 'login'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-4">

                        <!-- Google Sign-In Button (Directly below subtitle, matching AskReview) -->
                        <div>
                            <a href="{{ route('auth.google') }}"
                               id="btn-google-login"
                               class="w-full py-3 px-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm border border-slate-200/90 flex items-center justify-center gap-3 transition shadow-2xs hover:shadow-xs">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                                    <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.4l3.7 2.9C6.5 7.4 9 5 12 5z"/>
                                    <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.7-.2-2.3H12v4.6h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.9z"/>
                                    <path fill="#FBBC05" d="M5.6 14.7c-.2-.7-.4-1.5-.4-2.7s.2-2 .4-2.7L1.9 6.4C.7 8.8 0 10.8 0 12s.7 3.2 1.9 5.6l3.7-2.9z"/>
                                    <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2-6.4-4.8L1.9 16.4C3.7 20.2 7.5 23 12 23z"/>
                                </svg>
                                <span>Sign In with Google</span>
                            </a>
                        </div>

                        <!-- Divider: OR SIGN IN WITH EMAIL -->
                        <div class="relative flex items-center justify-center my-2">
                            <div class="border-t border-slate-200 w-full"></div>
                            <span class="bg-white px-3 text-[10px] font-bold tracking-wider text-slate-400 uppercase absolute">OR SIGN IN WITH EMAIL</span>
                        </div>

                        <!-- Main Form -->
                        <form method="POST" action="{{ route('login') }}" class="space-y-3.5" id="form-login">
                            @csrf

                            <!-- Email Address Input -->
                            <div class="text-left">
                                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Email Address
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
                                           x-model="email"
                                           required
                                           autofocus
                                           placeholder="name@company.com"
                                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50/50 hover:bg-white focus:bg-white border @error('email') border-red-500 @else border-slate-200/90 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm font-medium transition shadow-2xs">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Input -->
                            <div class="text-left">
                                <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Password
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <input :type="showPassword ? 'text' : 'password'"
                                           name="password"
                                           id="password"
                                           x-model="password"
                                           required
                                           placeholder="••••••••••••"
                                           class="w-full pl-10 pr-10 py-3 rounded-xl bg-slate-50/50 hover:bg-white focus:bg-white border @error('password') border-red-500 @else border-slate-200/90 @enderror text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm font-medium transition shadow-2xs">
                                    <button type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Remember Me & Forgot Password Row -->
                            <div class="flex items-center justify-between pt-0.5">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox"
                                           name="remember"
                                           value="1"
                                           checked
                                           class="w-4 h-4 rounded border-slate-300 text-red-600 focus:ring-red-500/20">
                                    <span class="text-xs text-slate-700 font-semibold">Remember me</span>
                                </label>
                                <a href="{{ route('password.request') }}" class="text-xs text-red-600 hover:text-red-700 font-bold hover:underline">
                                    Forgot password?
                                </a>
                            </div>

                            <!-- Primary Action Button: Sign In → -->
                            <button type="submit"
                                    id="btn-submit-login"
                                    class="w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-bold text-sm sm:text-base shadow-lg shadow-red-600/25 hover:shadow-xl hover:shadow-red-600/35 hover:scale-[1.005] active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2">
                                <span>Sign In</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- ============================= -->
                    <!-- TAB 2: DEMO ROLES & UI VIEW   -->
                    <!-- ============================= -->
                    <div x-show="tab === 'demo'"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-3">

                        <div class="rounded-xl bg-amber-50/90 border border-amber-200/90 p-3 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-xs text-amber-900 leading-snug text-left">
                                Click any role to <strong>instantly sign in</strong> and explore the live multi-tenant motorcycle showroom environment.
                            </p>
                        </div>

                        <!-- Demo Role 1: Super Admin -->
                        <button type="button"
                                @click="signInAs('admin', 'admin@motoshowroom.example', 'Password123!')"
                                class="w-full text-left p-3.5 rounded-xl bg-white hover:bg-red-50/60 border border-slate-200 hover:border-red-300 shadow-2xs transition group flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg group-hover:scale-105 transition shrink-0 border border-red-100">
                                    👑
                                </div>
                                <div>
                                    <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-red-600 transition">Super Administrator</div>
                                    <div class="text-[11px] text-slate-400 font-mono">admin@motoshowroom.example</div>
                                    <div class="text-[10px] text-slate-500">Platform billing & showroom tenants</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-red-600 opacity-0 group-hover:opacity-100 transition whitespace-nowrap pl-2">
                                Launch →
                            </span>
                        </button>

                        <!-- Demo Role 2: Showroom Dealer Owner -->
                        <button type="button"
                                @click="signInAs('dealer', 'rajesh@apex.example', 'Password123!')"
                                class="w-full text-left p-3.5 rounded-xl bg-white hover:bg-amber-50/60 border border-slate-200 hover:border-amber-300 shadow-2xs transition group flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg group-hover:scale-105 transition shrink-0 border border-amber-100">
                                    🏍️
                                </div>
                                <div>
                                    <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-amber-700 transition">Showroom Owner (Apex Moto)</div>
                                    <div class="text-[11px] text-slate-400 font-mono">rajesh@apex.example</div>
                                    <div class="text-[10px] text-slate-500">360° bikes, service bays & tokens</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-amber-600 opacity-0 group-hover:opacity-100 transition whitespace-nowrap pl-2">
                                Launch →
                            </span>
                        </button>

                        <!-- Demo Role 3: Customer / Rider -->
                        <button type="button"
                                @click="signInAs('customer', 'user@motoshowroom.example', 'Password123!')"
                                class="w-full text-left p-3.5 rounded-xl bg-white hover:bg-blue-50/60 border border-slate-200 hover:border-blue-300 shadow-2xs transition group flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg group-hover:scale-105 transition shrink-0 border border-sky-100">
                                    👤
                                </div>
                                <div>
                                    <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-blue-700 transition">Customer / Buyer</div>
                                    <div class="text-[11px] text-slate-400 font-mono">user@motoshowroom.example</div>
                                    <div class="text-[10px] text-slate-500">Test rides, bookings & scratch cards</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-blue-600 opacity-0 group-hover:opacity-100 transition whitespace-nowrap pl-2">
                                Launch →
                            </span>
                        </button>

                        <!-- Mobile callout to view left side UI -->
                        <div class="lg:hidden pt-2">
                            <button type="button"
                                    @click="document.getElementById('showroom-section')?.scrollIntoView({ behavior: 'smooth' })"
                                    class="w-full py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center gap-1.5 transition">
                                <span>👀 View Showroom 360° Carousel Below</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Footer: Don't have an account? Create Account -->
                    <div class="text-center text-xs text-slate-500 pt-1">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="font-bold text-red-600 hover:text-red-700 hover:underline ml-1">
                            Create Account
                        </a>
                    </div>

                    <!-- Security & Terms Footer (AskReview Style) -->
                    <div class="pt-2 border-t border-slate-100 space-y-1 text-center">
                        <div class="flex items-center justify-center gap-1.5 text-[11px] text-slate-600 font-semibold">
                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>256–Bit SSL Encrypted • Multi-Tenant Isolated</span>
                        </div>
                        <p class="text-[10px] text-slate-400">
                            By signing in, you agree to our
                            <a href="#" class="text-red-600 hover:underline font-medium">Terms & Conditions</a>
                            and
                            <a href="#" class="text-red-600 hover:underline font-medium">Privacy Policy</a>.
                        </p>
                    </div>

                </div>
            </div>
        </section>
    </div>
</x-guest-layout>
