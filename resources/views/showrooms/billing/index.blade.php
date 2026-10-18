<x-showroom-layout :showroom="$showroom" :title="'Subscription & Billing - ' . $showroom->name">
    <div class="max-w-7xl mx-auto space-y-8 py-8 px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-800">Showroom Back-Office</a>
                    <span>/</span>
                    <span class="text-rose-600 font-semibold">Subscription & Billing</span>
                </div>
                <h1 class="text-3xl font-black font-display text-slate-900 tracking-tight">Showroom Subscription & Quotas</h1>
                <p class="text-sm text-slate-600 mt-1">Review active plan entitlements, monitor resource limits, and upgrade showroom capabilities.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.manage', $showroom) }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 hover:text-slate-900 text-xs font-semibold transition flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back-Office Dashboard</span>
                </a>
            </div>
        </div>

        <!-- Expiry Warning Alert if Expired or Past Due -->
        @if($subscription && ($subscription->isExpired() || $subscription->status->value === 'past_due'))
            <div class="rounded-2xl p-5 border border-rose-300 bg-rose-50 text-rose-900 shadow-sm flex items-start gap-4">
                <div class="p-2 rounded-xl bg-rose-100 text-rose-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-rose-800">
                        {{ $subscription->status->value === 'past_due' ? 'Subscription In Grace Period' : 'Subscription Has Expired' }}
                    </h3>
                    <p class="text-xs text-rose-700 leading-relaxed">
                        Your back-office management access is temporarily restricted until your subscription is renewed. Select a plan below to immediately restore full access for your showroom team.
                    </p>
                </div>
            </div>
        @endif

        <!-- Current Plan Status & Resource Meters -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Active Plan Summary -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Active Subscription</span>
                    @if($subscription)
                        <span class="px-2.5 py-1 rounded text-xs font-bold border {{ $subscription->status->badgeClasses() }}">
                            {{ $subscription->status->label() }}
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">None</span>
                    @endif
                </div>

                @if($subscription && $currentPlan)
                    <div>
                        <h2 class="text-2xl font-black font-display text-slate-900">{{ $currentPlan->name }}</h2>
                        <div class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                            <span>{{ $currentPlan->formatted_monthly_price }}/mo</span>
                            <span>•</span>
                            <span class="capitalize">{{ $subscription->billing_interval }} Billing</span>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs border-t border-slate-100 pt-4">
                        <div class="flex justify-between py-1 text-slate-500">
                            <span>Period End Date:</span>
                            <span class="font-mono text-slate-900 font-bold">{{ $subscription->current_period_end->format('M d, Y') }}</span>
                        </div>

                        <div class="flex justify-between py-1 text-slate-500">
                            <span>Days Remaining:</span>
                            <span class="font-bold {{ $subscription->days_remaining > 5 ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $subscription->days_remaining }} Days
                            </span>
                        </div>

                        <div class="flex justify-between py-1 text-slate-500">
                            <span>Auto-Renew:</span>
                            <span class="font-bold {{ $subscription->auto_renew ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ $subscription->auto_renew ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="py-6 text-center text-xs text-slate-500">
                        No active subscription found. Select a plan below to activate your showroom.
                    </div>
                @endif
            </div>

            <!-- Resource Usage Meters (2 Cols) -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h2 class="text-lg font-bold font-display text-slate-900">Plan Resource Entitlements</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Real-time consumption meters evaluated against your plan caps.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Staff Accounts -->
                    <div class="space-y-2 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-700">Staff Accounts</span>
                            <span class="font-mono text-rose-600 font-bold">{{ $usage['staff']['current'] }} / {{ $usage['staff']['limit'] }}</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
                            @php $pctStaff = min(100, ($usage['staff']['current'] / max(1, $usage['staff']['limit'])) * 100); @endphp
                            <div class="h-full bg-rose-600 rounded-full" style="width: {{ $pctStaff }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-500 block">Showroom team members with portal login</span>
                    </div>

                    <!-- Active Bike Listings -->
                    <div class="space-y-2 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-700">Motorcycle Inventory</span>
                            <span class="font-mono text-emerald-600 font-bold">{{ $usage['bikes']['current'] }} / {{ $usage['bikes']['limit'] }}</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
                            @php $pctBikes = min(100, ($usage['bikes']['current'] / max(1, $usage['bikes']['limit'])) * 100); @endphp
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $pctBikes }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-500 block">Active showcase models in public storefront</span>
                    </div>

                    <!-- Monthly Bookings -->
                    <div class="space-y-2 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-700">Monthly Bookings Cap</span>
                            <span class="font-mono text-sky-600 font-bold">{{ $usage['bookings']['current'] }} / {{ $usage['bookings']['limit'] }}</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
                            @php $pctBookings = min(100, ($usage['bookings']['current'] / max(1, $usage['bookings']['limit'])) * 100); @endphp
                            <div class="h-full bg-sky-500 rounded-full" style="width: {{ $pctBookings }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-500 block">Service appointments and customer test rides</span>
                    </div>

                    <!-- Cloud Media Storage -->
                    <div class="space-y-2 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-700">Media Cloud Storage</span>
                            <span class="font-mono text-amber-600 font-bold">{{ $usage['storage']['current_mb'] }} MB / {{ $usage['storage']['limit_mb'] }} MB</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
                            @php $pctStorage = min(100, ($usage['storage']['current_mb'] / max(1, $usage['storage']['limit_mb'])) * 100); @endphp
                            <div class="h-full bg-amber-500 rounded-full" style="width: {{ $pctStorage }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-500 block">High-resolution bike galleries and 360 viewer frames</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Available Plans Matrix -->
        <div class="space-y-6 pt-4">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <h2 class="text-2xl font-black font-display text-slate-900">Upgrade or Switch Showroom Tier</h2>
                <p class="text-xs text-slate-600">Scale up your bike listings, team members, and unlock interactive 360-degree viewing and AI customer advisors.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($availablePlans as $plan)
                    @php $isCurrent = $subscription && $subscription->plan_id === $plan->id; @endphp
                    <div class="bg-white rounded-2xl p-6 border {{ $isCurrent ? 'border-rose-500 ring-2 ring-rose-200' : 'border-slate-200' }} shadow-sm flex flex-col justify-between relative">
                        @if($isCurrent)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-rose-600 text-white text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-xs">
                                Current Plan
                            </div>
                        @endif

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-xl font-black font-display text-slate-900">{{ $plan->name }}</h3>
                                <p class="text-xs text-slate-500 mt-1 min-h-[32px]">{{ $plan->description }}</p>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-3xl font-black font-display text-slate-900">{{ $plan->formatted_monthly_price }}</span>
                                <span class="text-xs text-slate-500">/month</span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">or {{ $plan->formatted_annual_price }} billed annually</span>
                            </div>

                            <ul class="space-y-2 text-xs text-slate-600">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span><strong>{{ $plan->max_staff_accounts }}</strong> Staff Team Members</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span><strong>{{ $plan->max_bike_listings }}</strong> Active Bike Listings</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span><strong>{{ $plan->max_monthly_bookings }}</strong> Monthly Bookings</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span><strong>{{ $plan->media_storage_mb >= 1024 ? ($plan->media_storage_mb / 1024).' GB' : $plan->media_storage_mb.' MB' }}</strong> Cloud Storage</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 {{ $plan->has_360_viewer ? 'text-emerald-600' : 'text-slate-300' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="{{ $plan->has_360_viewer ? 'text-slate-700' : 'text-slate-400 line-through' }}">Interactive 360° Motorcycle Viewer</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 {{ $plan->has_ai_assistant ? 'text-emerald-600' : 'text-slate-300' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="{{ $plan->has_ai_assistant ? 'text-slate-700' : 'text-slate-400 line-through' }}">AI Virtual Showroom Advisor</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 {{ $plan->has_custom_domain ? 'text-emerald-600' : 'text-slate-300' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="{{ $plan->has_custom_domain ? 'text-slate-700' : 'text-slate-400 line-through' }}">Custom Domain Brand Mapping</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Plan Selection Form -->
                        <div class="pt-6 border-t border-slate-100 mt-6">
                            @if($isCurrent && $subscription->isActive())
                                <button disabled class="w-full py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold cursor-not-allowed">
                                    Current Active Tier
                                </button>
                            @else
                                <form method="POST" action="{{ route('showrooms.billing.select-plan', $showroom) }}" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <div class="flex items-center justify-center gap-4 text-[11px] text-slate-600">
                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="billing_interval" value="monthly" checked class="text-rose-600">
                                            <span>Monthly</span>
                                        </label>
                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="billing_interval" value="annual" class="text-rose-600">
                                            <span>Annual</span>
                                        </label>
                                    </div>
                                    <button type="submit" class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition">
                                        {{ $isCurrent ? 'Renew Current Plan' : 'Choose '.$plan->name }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-showroom-layout>
