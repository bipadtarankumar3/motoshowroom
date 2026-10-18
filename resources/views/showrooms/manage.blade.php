<x-app-layout :title="$showroom->name . ' - Showroom Back-Office | MotoShowroom SaaS'">
    <div class="min-h-screen bg-slate-950 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Top Breadcrumb & Navigation -->
            <div class="flex items-center justify-between border-b border-slate-800 pb-5">
                <div class="flex items-center gap-3">
                    <a href="{{ route('profile') }}" class="text-xs text-slate-400 hover:text-white transition">My Profile</a>
                    <span class="text-slate-600">/</span>
                    <span class="text-sm font-bold text-white">{{ $showroom->name }}</span>
                    <span class="px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        Tenant Active
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('showrooms.show', $showroom) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700 text-slate-300 text-xs font-semibold transition flex items-center gap-1.5">
                        <span>View Public Storefront</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </div>

            <!-- Showroom Management Header Card -->
            <div class="glass-panel-dark rounded-3xl p-8 border border-slate-800 shadow-2xl relative overflow-hidden backdrop-blur-xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-rose-600 to-amber-500 p-0.5 shadow-xl shadow-rose-600/30 flex-shrink-0">
                            <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center font-bold text-2xl text-rose-400 font-display">
                                {{ strtoupper(substr($showroom->name, 0, 1)) }}
                            </div>
                        </div>

                        <div>
                            <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">{{ $showroom->name }}</h1>
                            <p class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                                <span>Slug: <strong class="text-slate-300 font-mono">{{ $showroom->slug }}</strong></span>
                                <span class="text-slate-600">•</span>
                                <span>Status: <strong class="text-emerald-400 font-semibold uppercase">{{ $showroom->status }}</strong></span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block sm:text-right">Authorized User:</span>
                        <span class="text-sm font-semibold text-rose-400 block sm:text-right">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                    </div>
                </div>
            </div>

            <!-- Departmental Modules Overview Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Inventory -->
                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800/80">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Inventory</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300">Phase 6</span>
                    </div>
                    <p class="text-2xl font-black text-white font-display">0 Bikes</p>
                    <p class="text-xs text-slate-400 mt-1">Motorcycle catalog & 360° assets</p>
                </div>

                <!-- Service Bay -->
                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800/80">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Service Bays</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300">Phase 8</span>
                    </div>
                    <p class="text-2xl font-black text-white font-display">0 Bookings</p>
                    <p class="text-xs text-slate-400 mt-1">Online service scheduler</p>
                </div>

                <!-- Leads & Enquiries -->
                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800/80">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Leads</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300">Phase 7</span>
                    </div>
                    <p class="text-2xl font-black text-white font-display">0 Enquiries</p>
                    <p class="text-xs text-slate-400 mt-1">Test rides & bike reservations</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
