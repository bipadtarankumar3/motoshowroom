<x-guest-layout title="Terms & Conditions — MotoShowroom SaaS">
    <div class="min-h-screen bg-slate-50 text-slate-800 font-sans selection:bg-red-600 selection:text-white">

        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-2xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-red-600 to-amber-500 p-0.5 shadow-md shadow-red-600/20 group-hover:scale-105 transition-transform">
                        <div class="w-full h-full bg-white rounded-[10px] flex items-center justify-center text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-base sm:text-lg font-black font-display tracking-tight text-slate-900 group-hover:text-red-600 transition">MOTOSHOWROOM</span>
                        <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400">LEGAL & TERMS OF SERVICE</span>
                    </div>
                </a>

                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-700 hover:text-red-600 hover:bg-slate-100 transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white shadow-md shadow-red-600/20 transition">
                        Create Account
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Hero Header -->
        <div class="bg-gradient-to-b from-white to-slate-50 border-b border-slate-200/80 py-12 sm:py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 border border-red-200 text-red-700 text-xs font-bold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                    <span>Legal Agreement · Version 2.4</span>
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black font-display text-slate-900 tracking-tight">
                    Terms & Conditions
                </h1>
                <p class="text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                    Please read these terms carefully before accessing or using the MotoShowroom SaaS platform, multi-tenant dealership portal, 360° interactive viewer, and online booking tools.
                </p>
                <div class="pt-2 text-xs text-slate-400 font-medium">
                    Effective Date: October 20, 2026 · Last Updated: October 2026
                </div>
            </div>
        </div>

        <!-- Terms Content Body -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-8">

            <!-- Highlights Card -->
            <div class="bg-amber-50/70 rounded-2xl p-5 sm:p-6 border border-amber-200/90 shadow-2xs space-y-3">
                <div class="flex items-center gap-2.5 text-amber-800 font-bold text-sm">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span>Summary at a Glance</span>
                </div>
                <ul class="text-xs sm:text-sm text-amber-900/90 space-y-1.5 list-disc list-inside">
                    <li>MotoShowroom is an enterprise multi-tenant software platform for motorcycle dealerships and retail buyers.</li>
                    <li>Token deposits and booking advances are processed securely via PCI-DSS compliant Razorpay rails.</li>
                    <li>Dealership inventory, test rides, 360° turntable images, and workshop services are subject to dealership availability.</li>
                    <li>All multi-tenant data is encrypted at rest and strictly isolated per tenant boundary.</li>
                </ul>
            </div>

            <!-- Section 1 -->
            <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm">1</span>
                    <h2 class="text-lg sm:text-xl font-bold font-display text-slate-900">Acceptance of Terms</h2>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    By registering for an account, accessing any dealership subdomain, submitting booking tokens, scheduling workshop services, or exploring interactive 360° virtual visualizers, you agree to be bound by these Terms and Conditions and our Privacy Policy. If you do not agree to these terms, you must not access or use the platform.
                </p>
            </section>

            <!-- Section 2 -->
            <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm">2</span>
                    <h2 class="text-lg sm:text-xl font-bold font-display text-slate-900">User Accounts & Security</h2>
                </div>
                <div class="text-sm text-slate-600 space-y-2 leading-relaxed">
                    <p>
                        When creating an account on MotoShowroom SaaS, you agree to:
                    </p>
                    <ul class="list-disc list-inside space-y-1 pl-2">
                        <li>Provide accurate, current, and complete personal and contact details.</li>
                        <li>Maintain the security of your password and accept all risks of unauthorized access.</li>
                        <li>Complete two-factor OTP verification challenges where required for administrative or financial actions.</li>
                        <li>Notify platform support immediately if you suspect any unauthorized account activity.</li>
                    </ul>
                </div>
            </section>

            <!-- Section 3 -->
            <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm">3</span>
                    <h2 class="text-lg sm:text-xl font-bold font-display text-slate-900">Online Bookings & Razorpay Payments</h2>
                </div>
                <div class="text-sm text-slate-600 space-y-2 leading-relaxed">
                    <p>
                        MotoShowroom enables retail customers to place reservation tokens for motorcycles and book workshop service slots:
                    </p>
                    <ul class="list-disc list-inside space-y-1 pl-2">
                        <li><strong>Token Deposit:</strong> Booking tokens reserve vehicle allocation with the selected dealer for a designated holding period.</li>
                        <li><strong>Payment Processing:</strong> Transactions are processed through Razorpay's encrypted, 256-bit SSL gateway. MotoShowroom does not store raw credit card credentials on its servers.</li>
                        <li><strong>Cancellations & Refunds:</strong> Booking token refund requests are governed by each individual dealership's policy, subject to statutory consumer protection laws.</li>
                    </ul>
                </div>
            </section>

            <!-- Section 4 -->
            <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm">4</span>
                    <h2 class="text-lg sm:text-xl font-bold font-display text-slate-900">Interactive 360° Visualizer & Media Usage</h2>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    High-resolution vehicle turntable frames, 360° panoramic media, and technical specifications are provided for demonstration and retail showcase purposes. While dealerships endeavor to maintain real-time accuracy regarding colors, variants, and on-road availability, final physical vehicle specifications should be verified at the physical showroom before delivery.
                </p>
            </section>

            <!-- Section 5 -->
            <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm">5</span>
                    <h2 class="text-lg sm:text-xl font-bold font-display text-slate-900">Workshop & Service Appointments</h2>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Service packages, parts pricing, and inspection schedules booked through the portal are estimates. Authorized technicians provide final physical diagnosis upon vehicle intake. Customers will be alerted and must authorize any additional repairs before execution.
                </p>
            </section>

            <!-- Section 6 -->
            <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm">6</span>
                    <h2 class="text-lg sm:text-xl font-bold font-display text-slate-900">Limitation of Liability & Governing Law</h2>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    MotoShowroom SaaS provides software infrastructure connecting dealerships and customers. Under no circumstances shall MotoShowroom or its affiliates be held liable for indirect, incidental, or consequential damages resulting from dealership inventory discrepancies or mechanical disputes. These terms are governed by the laws of India.
                </p>
            </section>

            <!-- Bottom Back & Actions Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-slate-200">
                <a href="{{ route('home') }}" class="text-xs font-bold text-slate-600 hover:text-red-600 flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back to Home</span>
                </a>
                <div class="flex items-center gap-4 text-xs font-semibold text-slate-500">
                    <a href="{{ route('privacy') }}" class="hover:text-red-600 transition">Privacy Policy</a>
                    <span>·</span>
                    <a href="{{ route('register') }}" class="text-red-600 font-bold hover:underline">Create Account</a>
                </div>
            </div>
        </main>
    </div>
</x-guest-layout>
