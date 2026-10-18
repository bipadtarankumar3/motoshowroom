<x-app-layout title="Subscription Plans - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-black font-display text-white tracking-tight">Subscription Plans</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                            Commercial Tiers
                        </span>
                    </div>
                    <p class="text-sm text-slate-400 mt-1">Configure database-driven pricing, quotas, and feature flags for multi-tenant bike showrooms.</p>
                </div>

                <a href="{{ route('admin.plans.create') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xl shadow-rose-600/30 transition flex items-center gap-2" id="btn-create-plan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Create New Plan</span>
                </a>
            </div>

            <!-- Plans Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($plans as $plan)
                    <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 shadow-xl flex flex-col justify-between relative overflow-hidden group hover:border-slate-700 transition">
                        @if($plan->is_active)
                            <div class="absolute top-0 right-0 bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-bl-xl shadow">
                                Active Tier
                            </div>
                        @else
                            <div class="absolute top-0 right-0 bg-slate-800 text-slate-400 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-bl-xl shadow">
                                Inactive
                            </div>
                        @endif

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-xl font-black font-display text-white">{{ $plan->name }}</h3>
                                <p class="text-xs text-slate-400 mt-1 min-h-[32px]">{{ $plan->description ?? 'All-in-one motorcycle showroom management tier.' }}</p>
                            </div>

                            <!-- Pricing -->
                            <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 flex items-baseline justify-between">
                                <div>
                                    <span class="text-2xl font-black font-display text-white">{{ $plan->formatted_monthly_price }}</span>
                                    <span class="text-xs text-slate-400">/mo</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-slate-300">{{ $plan->formatted_annual_price }}</span>
                                    <span class="text-[11px] text-slate-500 block">/yr billed annually</span>
                                </div>
                            </div>

                            <!-- Limits Breakdown -->
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between py-1 border-b border-slate-800/80 text-slate-400">
                                    <span>Trial Period:</span>
                                    <span class="font-bold text-slate-200">{{ $plan->trial_days }} Days</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-800/80 text-slate-400">
                                    <span>Staff Team Accounts:</span>
                                    <span class="font-bold text-slate-200">{{ $plan->max_staff_accounts }} Staff</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-800/80 text-slate-400">
                                    <span>Active Bike Listings:</span>
                                    <span class="font-bold text-slate-200">{{ $plan->max_bike_listings }} Bikes</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-800/80 text-slate-400">
                                    <span>Monthly Bookings:</span>
                                    <span class="font-bold text-slate-200">{{ $plan->max_monthly_bookings }} Appointments</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-800/80 text-slate-400">
                                    <span>Cloud Media Storage:</span>
                                    <span class="font-bold text-slate-200">{{ $plan->media_storage_mb >= 1024 ? ($plan->media_storage_mb / 1024).' GB' : $plan->media_storage_mb.' MB' }}</span>
                                </div>
                            </div>

                            <!-- Feature Badges -->
                            <div class="space-y-1.5 pt-2">
                                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block">Entitled Features:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_careers_module ? 'bg-emerald-500/15 text-emerald-300' : 'bg-slate-900 text-slate-600 line-through' }}">
                                        Careers Portal
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_scratch_cards ? 'bg-emerald-500/15 text-emerald-300' : 'bg-slate-900 text-slate-600 line-through' }}">
                                        Scratch Cards
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_360_viewer ? 'bg-emerald-500/15 text-emerald-300' : 'bg-slate-900 text-slate-600 line-through' }}">
                                        360° Viewer
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_ai_assistant ? 'bg-emerald-500/15 text-emerald-300' : 'bg-slate-900 text-slate-600 line-through' }}">
                                        AI Assistant
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_custom_domain ? 'bg-emerald-500/15 text-emerald-300' : 'bg-slate-900 text-slate-600 line-through' }}">
                                        Custom Domain
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $plan->has_advanced_analytics ? 'bg-emerald-500/15 text-emerald-300' : 'bg-slate-900 text-slate-600 line-through' }}">
                                        Advanced Analytics
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="pt-6 border-t border-slate-800/80 mt-6 flex items-center justify-between gap-2">
                            <span class="text-xs text-slate-400">
                                <strong>{{ $plan->subscriptions_count }}</strong> Showrooms
                            </span>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.plans.toggle', $plan) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-700 hover:bg-slate-800 text-xs font-semibold text-slate-300 transition">
                                        {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <a href="{{ route('admin.plans.edit', $plan) }}" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 p-12 text-center text-slate-400 glass-panel-dark rounded-2xl border border-slate-800">
                        No subscription plans configured. Click "Create New Plan" to set up pricing tiers.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
