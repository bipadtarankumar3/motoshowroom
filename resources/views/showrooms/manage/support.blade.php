<x-app-layout :title="'Help & Support Hub - ' . $showroom->name">
    @include('showrooms.partials.manage-nav')

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 xl:px-10">
        <div class="w-full space-y-8">

            {{-- ═══ TOP BREADCRUMB & HEADER ═══ --}}
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Showroom Back-Office</a>
                <span>/</span>
                <span class="text-rose-600 font-semibold">Support &amp; Help Desk</span>
            </div>

            <div class="border-b border-slate-200 pb-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black font-display text-slate-900 tracking-tight flex items-center gap-3">
                        <span>Dealership Support &amp; Help Center</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            24/7 Priority Desk
                        </span>
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">Get immediate technical assistance, platform onboarding, payment gateway resolution, and API guidance.</p>
                </div>

                {{-- Direct Contact Hotline Quick Badge --}}
                <div class="flex items-center gap-3">
                    <a href="tel:+918005550199" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:border-slate-300 text-slate-800 text-xs font-bold transition flex items-center gap-2 shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>Hotline: +91 (800) 555-0199</span>
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-800 text-sm shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- ═══ 1. HIGH-PRIORITY SUPPORT CHANNELS (4 Curated Cards) ═══ --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- 1. Dedicated Technical Concierge --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between space-y-4 hover:border-slate-300 transition">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Direct Live Chat</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Connect with our dedicated automotive software engineers for rapid troubleshooting.</p>
                    </div>
                    <div class="pt-2">
                        <a href="https://wa.me/918005550199?text=Hello%20MotoShowroom%20Support%2C%20I%20need%20help%20with%20dealership%20{{ urlencode($showroom->name) }}"
                           target="_blank"
                           class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-2xs">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.586 1.761.882 2.796.882h.005c3.18 0 5.766-2.586 5.767-5.766.001-3.181-2.585-5.769-5.772-5.769zm0 10.375c-.911 0-1.636-.264-2.399-.714l-.171-.102-1.782.467.476-1.737-.113-.179c-.482-.767-.736-1.53-.735-2.344.001-2.408 1.959-4.366 4.368-4.366 2.409 0 4.368 1.959 4.369 4.367 0 2.409-1.959 4.368-4.372 4.368z"/></svg>
                            <span>WhatsApp Concierge</span>
                        </a>
                    </div>
                </div>

                {{-- 2. Official Email Desk --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between space-y-4 hover:border-slate-300 transition">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Priority Email Desk</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Submit detailed error traces, contract inquiries, or multi-tenant domain verification requests.</p>
                    </div>
                    <div class="pt-2">
                        <a href="mailto:support@motoshowroom.com?subject=Support%20Request%20for%20{{ urlencode($showroom->name) }}"
                           class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                            <span>support@motoshowroom.com</span>
                        </a>
                    </div>
                </div>

                {{-- 3. Payment Gateway & Razorpay Support --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between space-y-4 hover:border-slate-300 transition">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Billing &amp; Payments</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Assistance with Razorpay webhooks, automated payout reconciliations, and subscription renewals.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('showrooms.billing.index', $showroom) }}"
                           class="w-full py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-bold text-xs transition flex items-center justify-center gap-2 shadow-2xs">
                            <span>Open Billing Hub</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                {{-- 4. Storefront & Website Builder Guide --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between space-y-4 hover:border-slate-300 transition">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Storefront Builder Guide</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Customize your public landing page, toggle hero banners, categories, 360 viewer, and testimonials.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('showrooms.manage.website', $showroom) }}"
                           class="w-full py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-bold text-xs transition flex items-center justify-center gap-2 shadow-2xs">
                            <span>Launch Website Builder</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ═══ 2. DIRECT SUPPORT CONTACT DETAILS & DEALERSHIP METADATA ═══ --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- Left: Comprehensive Direct Support Channels --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <h2 class="text-xl font-bold font-display text-slate-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                <span>Official Support Channels &amp; Contact Desk</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Reach out directly via phone, WhatsApp, or email for immediate assistance with your showroom operations.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Phone Support --}}
                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Toll-Free Hotline</span>
                                <a href="tel:+918005550199" class="text-base font-black text-slate-900 hover:text-rose-600 transition block">
                                    +91 (800) 555-0199
                                </a>
                                <p class="text-[11px] text-slate-500">Available Mon – Sat, 9:00 AM – 9:00 PM IST for priority technical support.</p>
                            </div>

                            {{-- WhatsApp Support --}}
                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.586 1.761.882 2.796.882h.005c3.18 0 5.766-2.586 5.767-5.766.001-3.181-2.585-5.769-5.772-5.769zm0 10.375c-.911 0-1.636-.264-2.399-.714l-.171-.102-1.782.467.476-1.737-.113-.179c-.482-.767-.736-1.53-.735-2.344.001-2.408 1.959-4.366 4.368-4.366 2.409 0 4.368 1.959 4.369 4.367 0 2.409-1.959 4.368-4.372 4.368z"/></svg>
                                </div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Instant WhatsApp Chat</span>
                                <a href="https://wa.me/918005550199?text=Hello%20MotoShowroom%20Support%2C%20I%20need%20assistance%20with%20dealership%20{{ urlencode($showroom->name) }}"
                                   target="_blank" class="text-base font-black text-slate-900 hover:text-emerald-600 transition block">
                                    Chat on WhatsApp →
                                </a>
                                <p class="text-[11px] text-slate-500">Live chat with support executives. Average response time: under 5 minutes.</p>
                            </div>

                            {{-- Technical Email --}}
                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Technical Desk Email</span>
                                <a href="mailto:support@motoshowroom.com?subject=Technical%20Assistance%20for%20{{ urlencode($showroom->name) }}" class="text-sm font-black text-slate-900 hover:text-indigo-600 transition block truncate">
                                    support@motoshowroom.com
                                </a>
                                <p class="text-[11px] text-slate-500">For bugs, custom code integration, and API webhook configurations.</p>
                            </div>

                            {{-- Billing & Accounts Email --}}
                            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Billing &amp; GST Invoicing</span>
                                <a href="mailto:billing@motoshowroom.com?subject=Billing%20Inquiry%20for%20{{ urlencode($showroom->name) }}" class="text-sm font-black text-slate-900 hover:text-amber-600 transition block truncate">
                                    billing@motoshowroom.com
                                </a>
                                <p class="text-[11px] text-slate-500">For invoices, GST receipts, plan upgrades, and Razorpay gateway queries.</p>
                            </div>
                        </div>

                        {{-- Service Level Agreement (SLA) Matrix --}}
                        <div class="p-5 rounded-2xl bg-slate-900 text-white space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs uppercase font-bold text-emerald-400 tracking-wider">Guaranteed Response Times</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-white">2026 Enterprise SLA</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-1">
                                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Critical Outages</span>
                                    <span class="text-base font-black text-rose-400">&lt; 30 Minutes</span>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Storefront or payment failure</p>
                                </div>
                                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Billing &amp; Config</span>
                                    <span class="text-base font-black text-amber-300">&lt; 2 Hours</span>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Custom domain &amp; plans</p>
                                </div>
                                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">General Inquiries</span>
                                    <span class="text-base font-black text-emerald-400">&lt; 4 Hours</span>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Feature &amp; usage guidance</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Dealership Operational Context Card --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Showroom Profile</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 font-mono">
                                ID: #{{ $showroom->id }}
                            </span>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">Dealership Name</span>
                                <span class="font-bold text-slate-900">{{ $showroom->name }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">Storefront Slug</span>
                                <span class="font-mono text-slate-700">/showrooms/{{ $showroom->slug }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">Storefront Status</span>
                                @if($showroom->public_website_enabled)
                                    <span class="text-emerald-700 font-bold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                                    </span>
                                @else
                                    <span class="text-rose-700 font-bold">Offline</span>
                                @endif
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500">Active Plan</span>
                                <span class="font-bold text-indigo-700">
                                    {{ $showroom->subscription?->plan?->name ?? 'Standard Plan' }}
                                </span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-500">Official Contact</span>
                                <span class="font-semibold text-slate-800">{{ $showroom->phone ?? 'Not specified' }}</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                            <span class="font-bold text-slate-800 block">SLA Commitment</span>
                            <p class="leading-relaxed">Critical outages (payments or storefront inaccessible) are prioritized with under 1-hour response times.</p>
                        </div>
                    </div>

                    {{-- Useful Quick Links --}}
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-3">
                        <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider mb-2">Helpful Quick Links</h3>
                        <div class="grid grid-cols-1 gap-2 text-xs">
                            <a href="{{ route('showrooms.manage.website', $showroom) }}" class="p-2.5 rounded-xl hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center justify-between border border-slate-100 transition">
                                <span class="font-medium">Homepage Section Toggles &amp; Layout</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            <a href="{{ route('showrooms.manage.bikes.create', $showroom) }}" class="p-2.5 rounded-xl hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center justify-between border border-slate-100 transition">
                                <span class="font-medium">Add New Motorcycle &amp; 360 Sequences</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            <a href="{{ route('showrooms.manage.services.index', $showroom) }}" class="p-2.5 rounded-xl hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center justify-between border border-slate-100 transition">
                                <span class="font-medium">Service Packages &amp; Workshop Bay Setup</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            <a href="{{ route('showrooms.manage.settings', $showroom) }}" class="p-2.5 rounded-xl hover:bg-slate-50 text-slate-700 hover:text-slate-900 flex items-center justify-between border border-slate-100 transition">
                                <span class="font-medium">Configure Razorpay &amp; Staff Access</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ 3. FREQUENTLY ASKED QUESTIONS (Accordion with Alpine.js) ═══ --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6" x-data="{ openFaq: 0 }">
                <div class="border-b border-slate-100 pb-4">
                    <span class="text-[11px] font-black uppercase tracking-wider text-rose-600 block">Knowledge Base</span>
                    <h2 class="text-xl font-bold font-display text-slate-900 mt-1">Frequently Asked Questions</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Quick guides to managing your motorcycle showroom and online sales platform.</p>
                </div>

                <div class="space-y-3">
                    @foreach($faqs as $idx => $faq)
                        <div class="rounded-2xl border border-slate-200 overflow-hidden transition"
                             :class="openFaq === {{ $idx }} ? 'bg-slate-50/70 border-slate-300' : 'bg-white hover:border-slate-300'">
                            <button
                                type="button"
                                @click="openFaq = (openFaq === {{ $idx }} ? null : {{ $idx }})"
                                class="w-full px-5 py-4 flex items-center justify-between text-left cursor-pointer"
                            >
                                <div class="space-y-1 pr-4">
                                    <span class="text-[10px] uppercase font-bold text-rose-600 tracking-wider block">
                                        {{ $faq['category'] }}
                                    </span>
                                    <h4 class="font-bold text-sm text-slate-900">{{ $faq['question'] }}</h4>
                                </div>
                                <svg class="w-5 h-5 text-slate-400 transition-transform duration-200 shrink-0"
                                     :class="openFaq === {{ $idx }} ? 'rotate-180 text-rose-600' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openFaq === {{ $idx }}" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100/80">
                                {{ $faq['answer'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
    @include('showrooms.partials.manage-nav-close')
</x-app-layout>
