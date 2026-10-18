<x-app-layout :title="$showroom->name . ' - Showroom Back-Office | MotoShowroom SaaS'">
    @include('showrooms.partials.manage-nav')

    <div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Hero Header Card -->
            <div class="glass-panel-dark rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl relative overflow-hidden backdrop-blur-xl">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-2xl text-white shadow-xl shrink-0"
                             style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                            {{ strtoupper(substr($showroom->name, 0, 1)) }}
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">{{ $showroom->name }}</h1>
                                @if($showroom->subscription)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $showroom->subscription->status->badgeClasses() }}">
                                        {{ $showroom->subscription->plan->name }} ({{ $showroom->subscription->status->label() }})
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-800 text-slate-300">
                                        {{ ucfirst($showroom->status) }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-400 mt-1 flex flex-wrap items-center gap-3">
                                <span>City: <strong class="text-slate-300">{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</strong></span>
                                <span>•</span>
                                <span>Slug: <strong class="text-slate-300 font-mono">{{ $showroom->slug }}</strong></span>
                                @if($showroom->subscription)
                                    <span>•</span>
                                    <span>Billing: <strong class="text-emerald-400">{{ $showroom->subscription->days_remaining }} days left</strong></span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}"
                           class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-xs font-semibold transition flex items-center gap-2" id="btn-bikes">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span>Motorcycles</span>
                        </a>

                        <a href="{{ route('showrooms.manage.website', $showroom) }}"
                           class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs shadow-lg shadow-rose-600/30 transition flex items-center gap-2" id="btn-customizer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Website Builder</span>
                        </a>

                        <a href="{{ route('showrooms.billing.index', $showroom) }}"
                           class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-xs font-semibold transition flex items-center gap-2" id="btn-billing">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Subscription</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Operational Metrics Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}" class="glass-panel-dark rounded-2xl p-4 border border-slate-800 shadow hover:border-rose-500/50 transition group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Inventory</span>
                        <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-rose-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <p class="text-2xl font-black font-display text-white mt-1 group-hover:text-rose-400 transition">{{ $metrics['bikes_count'] ?? 0 }}</p>
                    <span class="text-[10px] text-slate-500">Motorcycles listed</span>
                </a>

                <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800 shadow">
                    <span class="text-[10px] uppercase font-bold text-sky-400 tracking-wider">Enquiries</span>
                    <p class="text-2xl font-black font-display text-sky-400 mt-1">{{ $metrics['active_enquiries'] ?? 0 }}</p>
                    <span class="text-[10px] text-slate-500">Pending follow-ups</span>
                </div>

                <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800 shadow">
                    <span class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider">Service Bookings</span>
                    <p class="text-2xl font-black font-display text-emerald-400 mt-1">{{ $metrics['today_services'] ?? 0 }}</p>
                    <span class="text-[10px] text-slate-500">Scheduled appointments</span>
                </div>

                <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800 shadow">
                    <span class="text-[10px] uppercase font-bold text-amber-400 tracking-wider">Careers</span>
                    <p class="text-2xl font-black font-display text-amber-400 mt-1">{{ $metrics['open_jobs'] ?? 0 }}</p>
                    <span class="text-[10px] text-slate-500">Active job vacancies</span>
                </div>

                <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800 shadow">
                    <span class="text-[10px] uppercase font-bold text-purple-400 tracking-wider">Scratch Cards</span>
                    <p class="text-2xl font-black font-display text-purple-400 mt-1">{{ $metrics['active_campaigns'] ?? 0 }}</p>
                    <span class="text-[10px] text-slate-500">Promotional campaigns</span>
                </div>

                <div class="glass-panel-dark rounded-2xl p-4 border border-slate-800 shadow">
                    <span class="text-[10px] uppercase font-bold text-rose-400 tracking-wider">Team Members</span>
                    <p class="text-2xl font-black font-display text-rose-400 mt-1">{{ $metrics['staff_count'] ?? 1 }}</p>
                    <span class="text-[10px] text-slate-500">Authorized staff</span>
                </div>
            </div>

            <!-- Two-Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left 2 Cols: Departmental Modules Directory -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold font-display text-white">Departmental Operations</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Website Builder Card -->
                        <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300">Phase 5</span>
                                </div>
                                <h3 class="text-base font-bold text-white">Storefront Customizer</h3>
                                <p class="text-xs text-slate-400">Hero banners, headlines, operating hours, social handles, and theme branding.</p>
                            </div>
                            <div class="pt-4 border-t border-slate-800/80 mt-4">
                                <a href="{{ route('showrooms.manage.website', $showroom) }}" class="text-xs text-rose-400 hover:text-rose-300 font-bold flex items-center gap-1">
                                    <span>Customize Website</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Inventory Card -->
                        <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400">Phase 6</span>
                                </div>
                                <h3 class="text-base font-bold text-white">Motorcycle Inventory</h3>
                                <p class="text-xs text-slate-400">Manage models, specifications, prices, multi-angle galleries, and 360 viewer frames.</p>
                            </div>
                            <div class="pt-4 border-t border-slate-800/80 mt-4">
                                <span class="text-xs text-slate-500 font-medium">Coming in Phase 6</span>
                            </div>
                        </div>

                        <!-- Enquiries & Test Rides -->
                        <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400">Phase 7</span>
                                </div>
                                <h3 class="text-base font-bold text-white">Purchase & Test Rides</h3>
                                <p class="text-xs text-slate-400">Customer purchase enquiries, test ride slots, and sales staff lead management.</p>
                            </div>
                            <div class="pt-4 border-t border-slate-800/80 mt-4">
                                <span class="text-xs text-slate-500 font-medium">Coming in Phase 7</span>
                            </div>
                        </div>

                        <!-- Service Appointments -->
                        <div class="glass-panel-dark rounded-2xl p-5 border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400">Phase 8</span>
                                </div>
                                <h3 class="text-base font-bold text-white">Service Booking Concurrency</h3>
                                <p class="text-xs text-slate-400">Bay scheduling, service packages, customer maintenance logs, and pickup tracking.</p>
                            </div>
                            <div class="pt-4 border-t border-slate-800/80 mt-4">
                                <span class="text-xs text-slate-500 font-medium">Coming in Phase 8</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Col: Storefront Status & Recent Activity -->
                <div class="space-y-6">
                    <!-- Storefront Status Card -->
                    <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 shadow-xl space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Public Storefront</h3>
                            @if($showroom->public_website_enabled)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300">Live Online</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300">Offline</span>
                            @endif
                        </div>

                        <p class="text-xs text-slate-400">
                            {{ $showroom->tagline }}
                        </p>

                        <div class="pt-2">
                            <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                               class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700 text-white text-xs font-bold transition flex items-center justify-center gap-2">
                                <span>Preview Live Storefront</span>
                                <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Recent Activity Stream -->
                    <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 shadow-xl space-y-4">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Showroom Activity Trail</h3>
                        <div class="space-y-3">
                            @forelse($recentActivities as $act)
                                <div class="text-xs border-b border-slate-800/80 pb-2.5 last:border-0 last:pb-0">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-white">{{ $act->action }}</span>
                                        <span class="text-[10px] text-slate-500">{{ $act->created_at->diffForHumans() }}</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 mt-0.5 block">by {{ $act->user?->name ?? 'System' }}</span>
                                </div>
                            @empty
                                <div class="text-center py-4 text-xs text-slate-500">
                                    No activity logged yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
