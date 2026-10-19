<x-app-layout :title="'Subscription & Billing - ' . $showroom->name">
    @include('showrooms.partials.manage-nav')
    <div class="w-full space-y-8 py-8 px-4 sm:px-6 lg:px-8 xl:px-10">

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
                            <span class="font-bold text-slate-700">{{ $currentPlan->formatted_monthly_price }}</span>
                            <span>•</span>
                            <span>Valid until <strong class="text-slate-900 font-mono">{{ $subscription->current_period_end->format('M d, Y') }}</strong></span>
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
                            </div>                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-3xl font-black font-display text-slate-900">{{ $plan->formatted_monthly_price }}</span>
                                <span class="text-xs text-slate-500 font-medium block mt-1">Tier Subscription with Validity Period</span>
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
                                <button disabled class="w-full py-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold cursor-not-allowed">
                                    Current Active Tier
                                </button>
                            @else
                                <form method="POST" action="{{ route('showrooms.billing.select-plan', $showroom) }}" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <input type="hidden" name="billing_interval" value="monthly">
                                    <button type="submit" class="w-full py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                                        {{ $isCurrent ? 'Renew Current Plan' : 'Choose '.$plan->name }}
                                    </button>
                                </form>
                            @endif
                        </div>iv>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment & Billing Transaction Ledger -->
        <div class="space-y-4 pt-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-black font-display text-slate-900 tracking-tight">Payment &amp; Billing History</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Detailed records of all subscription purchases, renewals, and Razorpay transactions for this showroom.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Admin Gateway: Razorpay</span>
                    </span>
                    <a href="{{ route('showrooms.manage.payments.index', $showroom) }}" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 transition shadow-xs">
                        View Full Ledger
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                @if(isset($payments) && $payments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50 text-[10px] uppercase font-bold text-slate-500 tracking-wider">
                                    <th class="py-3 px-4">Transaction / Order</th>
                                    <th class="py-3 px-4">Purpose / Plan</th>
                                    <th class="py-3 px-4">Paid By</th>
                                    <th class="py-3 px-4">Amount</th>
                                    <th class="py-3 px-4">Gateway &amp; Status</th>
                                    <th class="py-3 px-4">Date</th>
                                    <th class="py-3 px-4 text-right">Receipt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($payments as $pm)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <!-- Transaction & Order ID -->
                                        <td class="py-3.5 px-4 font-mono">
                                            <a href="{{ route('showrooms.manage.payments.show', [$showroom, $pm]) }}" class="font-bold text-rose-600 hover:underline block">
                                                #PAY-{{ str_pad($pm->id, 6, '0', STR_PAD_LEFT) }}
                                            </a>
                                            <span class="text-[10px] text-slate-500 block mt-0.5 truncate max-w-[160px]" title="{{ $pm->razorpay_order_id }}">
                                                {{ $pm->razorpay_order_id ?? 'Direct Offline' }}
                                            </span>
                                            @if($pm->razorpay_payment_id)
                                                <span class="text-[10px] text-slate-400 block font-mono truncate max-w-[160px]" title="{{ $pm->razorpay_payment_id }}">
                                                    {{ $pm->razorpay_payment_id }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Purpose / Plan -->
                                        <td class="py-3.5 px-4">
                                            <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 border border-slate-200 text-slate-700">
                                                {{ str_replace('_', ' ', $pm->purpose) }}
                                            </span>
                                            @if(isset($pm->notes['plan_slug']))
                                                <span class="text-[11px] text-slate-700 font-bold block mt-1 capitalize">
                                                    {{ str_replace('-', ' ', $pm->notes['plan_slug']) }} Plan
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Paid By -->
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900 text-xs">
                                                {{ $pm->user?->name ?? 'Dealership Admin' }}
                                            </div>
                                            <div class="text-[10px] text-slate-500 font-mono">
                                                {{ $pm->user?->email ?? 'N/A' }}
                                            </div>
                                        </td>

                                        <!-- Amount -->
                                        <td class="py-3.5 px-4">
                                            <div class="font-mono text-sm font-black text-emerald-700">
                                                ₹{{ number_format($pm->amount) }}
                                            </div>
                                            <span class="text-[10px] text-slate-400 uppercase font-mono">{{ $pm->currency }}</span>
                                        </td>

                                        <!-- Gateway & Status -->
                                        <td class="py-3.5 px-4">
                                            <div class="space-y-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold {{ $pm->status->badgeClasses() }}">
                                                    {{ $pm->status->label() }}
                                                </span>
                                                @if($pm->signature_verified)
                                                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 block font-mono">
                                                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        Razorpay Verified
                                                    </span>
                                                @endif
                                                @if($pm->method)
                                                    <span class="text-[10px] text-slate-400 uppercase font-mono block">via {{ $pm->method }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Date -->
                                        <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                                            <div class="text-xs font-bold text-slate-800">{{ $pm->created_at->format('M d, Y') }}</div>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $pm->created_at->format('h:i A') }}</span>
                                        </td>

                                        <!-- Receipt Action -->
                                        <td class="py-3.5 px-4 text-right">
                                            <a href="{{ route('showrooms.manage.payments.show', [$showroom, $pm]) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 rounded-xl font-bold text-[11px] transition shadow-xs">
                                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>Receipt</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($payments->hasPages())
                        <div class="p-4 border-t border-slate-200 bg-white">
                            {{ $payments->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 px-4 space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">No Payment History Yet</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            When you purchase or upgrade a subscription tier, your Razorpay transaction records, invoices, and tax receipts will appear here.
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>
    @include('showrooms.partials.manage-nav-close')
</x-app-layout>
