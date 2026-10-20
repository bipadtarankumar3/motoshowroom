<x-guest-layout title="Privacy Policy — MotoShowroom SaaS">
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
                        <span class="block text-[9px] font-bold uppercase tracking-widest text-slate-400">DATA PRIVACY & COMPLIANCE</span>
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
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>256-Bit SSL Encrypted · Multi-Tenant Protection</span>
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black font-display text-slate-900 tracking-tight">
                    Privacy Policy
                </h1>
                <p class="text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                    How MotoShowroom collects, isolates, and protects customer, dealership, and transaction data across our multi-tenant cloud platform.
                </p>
                <div class="pt-2 text-xs text-slate-400 font-medium">
                    Effective Date: October 20, 2026 · Last Updated: October 2026
                </div>
            </div>
        </div>

        <!-- Privacy Content Body -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-8">

            <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">1</span>
                    <h2 class="text-lg sm:text-xl font-bold font-display text-slate-900">Information We Collect</h2>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    We collect personal information necessary to deliver motorcycle retail and dealership software services, including name, email, phone number, city, and vehicle booking choices. We also log standard telemetry (browser type, session timestamps) for security auditing.
                </p>
            </section>

            <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">2</span>
                    <h2 class="text-lg sm:text-xl font-bold font-display text-slate-900">Multi-Tenant Database Isolation</h2>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Each dealership tenant operates within an architecturally isolated scope. Customer bookings, inventory catalogs, service tickets, and communications are restricted to authorized personnel of the respective dealership workspace.
                </p>
            </section>

            <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">3</span>
                    <h2 class="text-lg sm:text-xl font-bold font-display text-slate-900">Payment Security (Razorpay PCI-DSS)</h2>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Online payments and booking deposits are handled directly by Razorpay's Level 1 PCI-DSS compliant infrastructure. MotoShowroom does not capture, store, or view your card numbers or CVV.
                </p>
            </section>

            <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">4</span>
                    <h2 class="text-lg sm:text-xl font-bold font-display text-slate-900">Your Data Rights</h2>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    You have the right to inspect, update, or request the deletion of your account information at any time through your User Profile or by contacting support@motoshowroom.example.
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
                    <a href="{{ route('terms') }}" class="hover:text-red-600 transition">Terms & Conditions</a>
                    <span>·</span>
                    <a href="{{ route('register') }}" class="text-red-600 font-bold hover:underline">Create Account</a>
                </div>
            </div>
        </main>
    </div>
</x-guest-layout>
