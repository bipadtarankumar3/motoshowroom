<x-app-layout :title="$user->name . ' - User Details | Super Admin | MotoShowroom SaaS'">
    @if(auth()->check() && auth()->user()->isAdmin())
        @include('admin.partials.nav')
    @endif

    <div class="bg-slate-50 min-h-screen py-6 sm:py-8 px-4 sm:px-6 lg:px-8 xl:px-10 w-full"
         x-data="{ activeTab: 'showrooms' }">
        <div class="w-full space-y-6 sm:space-y-8">

            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-slate-500">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Admin</a>
                    <span>/</span>
                    <a href="{{ route('admin.users.index') }}" class="hover:text-slate-900 transition">Users</a>
                @else
                    <a href="{{ route('dashboard') }}" class="hover:text-slate-900 transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-600">My Profile</span>
                @endif
                <span>/</span>
                <span class="text-rose-600 font-semibold">{{ $user->name }}</span>
            </div>

            <!-- Admin Header Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-start sm:items-center gap-5">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-rose-500 to-rose-600 text-white flex items-center justify-center font-black text-2xl font-display uppercase tracking-wider shadow-md shrink-0">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>

                    <div>
                        <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                            <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">{{ $user->name }}</h1>
                            <span class="font-mono text-xs text-slate-400 font-bold">#{{ $user->id }}</span>

                            @if ($user->isAdmin())
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                    Admin
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                    User
                                </span>
                            @endif

                            @if ($user->status === 'active')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                </span>
                            @elseif($user->status === 'suspended')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspended
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                </span>
                            @endif

                            @if($user->email_verified_at)
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Verified
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Unverified Email
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs text-slate-500 mt-2">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <strong class="text-slate-700">{{ $user->email }}</strong>
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <strong class="text-slate-700">{{ $user->phone ?: 'No phone' }}</strong>
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <strong class="text-slate-700">{{ $user->city ?: 'No city' }}</strong>
                            </span>
                            <span>•</span>
                            <span>
                                Member since <strong class="text-slate-700">{{ $user->created_at->format('M d, Y') }}</strong> ({{ $user->created_at->diffForHumans() }})
                                @if($user->created_at->isToday())
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Registered Today</span>
                                @elseif($user->created_at->isAfter(now()->subDays(7)))
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">In 7 Days</span>
                                @elseif($user->created_at->isAfter(now()->subDays(30)))
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800">In 1 Month</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="flex flex-wrap items-center gap-2.5">
                    {{-- Impersonate / Visit Dashboard Button (Admin viewing another user) --}}
                    @if(auth()->user()->isAdmin() && auth()->id() !== $user->id)
                        <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="m-0">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs sm:text-sm font-bold transition shadow-xs cursor-pointer flex items-center gap-1.5"
                                    title="Visit this user's dashboard (Impersonate)">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Visit Dashboard</span>
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('users.edit', $user) }}"
                       class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold transition flex items-center gap-2 shadow-xs"
                       id="btn-edit-user">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Edit Details</span>
                    </a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}"
                           class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold transition shadow-xs">
                            All Users
                        </a>
                    @endif
                </div>
            </div>

            <!-- 6 Comprehensive Telemetry Stat Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                <!-- Dealership Showrooms -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-slate-500">Showrooms</span>
                    <p class="text-2xl sm:text-3xl font-black font-display text-slate-900 mt-2">{{ $telemetry['showrooms_count'] }}</p>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Tenants associated</span>
                </div>

                <!-- Total Paid Revenue -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-emerald-700">Total Spend</span>
                    <p class="text-2xl sm:text-3xl font-black font-display text-emerald-600 mt-2">₹{{ number_format($telemetry['total_spend']) }}</p>
                    <span class="text-[11px] text-emerald-700/80 font-medium mt-1 block">{{ $telemetry['captured_payments_count'] }} paid transactions</span>
                </div>

                <!-- Primary Plan -->
                @php
                    $primaryPlan = $user->showrooms->first()?->subscription?->plan;
                @endphp
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-amber-700">Current Plan</span>
                    <p class="text-base sm:text-lg font-black font-display text-amber-800 mt-2 truncate">{{ $primaryPlan?->name ?: 'Standard User' }}</p>
                    <span class="text-[11px] text-amber-700/80 font-medium mt-1 block">
                        {{ $primaryPlan ? '₹' . number_format($primaryPlan->price) . ' / ' . $primaryPlan->billing_interval : 'Free Access' }}
                    </span>
                </div>

                <!-- Subscription Health -->
                @php
                    $primarySub = $user->showrooms->first()?->subscription;
                @endphp
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-slate-500">Sub Health</span>
                    <p class="text-base sm:text-lg font-black font-display mt-2 truncate {{ $primarySub?->isActive() ? 'text-emerald-600' : ($primarySub?->isExpired() ? 'text-rose-600' : 'text-slate-700') }}">
                        @if($primarySub)
                            {{ ucfirst($primarySub->status->value) }}
                        @else
                            No Active Sub
                        @endif
                    </p>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">
                        @if($primarySub)
                            {{ $primarySub->current_period_end->diffForHumans(['parts' => 1]) }}
                        @else
                            Standard Tier
                        @endif
                    </span>
                </div>

                <!-- Customer Enquiries -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-sky-700">Enquiries</span>
                    <p class="text-2xl sm:text-3xl font-black font-display text-sky-600 mt-2">{{ $telemetry['enquiries_count'] }}</p>
                    <span class="text-[11px] text-sky-700/80 font-medium mt-1 block">Test rides & leads</span>
                </div>

                <!-- Workshop Appointments -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                    <span class="text-[11px] uppercase font-bold tracking-wider text-indigo-700">Workshops</span>
                    <p class="text-2xl sm:text-3xl font-black font-display text-indigo-600 mt-2">{{ $telemetry['bookings_count'] }}</p>
                    <span class="text-[11px] text-indigo-700/80 font-medium mt-1 block">Service bookings</span>
                </div>
            </div>

            <!-- Tabbed Navigation Bar -->
            <div class="border-b border-slate-200 flex items-center gap-2 overflow-x-auto scrollbar-none">
                <button @click="activeTab = 'showrooms'"
                        :class="activeTab === 'showrooms' ? 'border-rose-600 text-rose-600 font-bold bg-white' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-4 py-3 border-b-2 text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Showroom Dealerships ({{ $user->showrooms->count() }})</span>
                </button>

                <button @click="activeTab = 'payments'"
                        :class="activeTab === 'payments' ? 'border-rose-600 text-rose-600 font-bold bg-white' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-4 py-3 border-b-2 text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Billing & Payments ({{ $payments->total() }})</span>
                </button>

                <button @click="activeTab = 'enquiries'"
                        :class="activeTab === 'enquiries' ? 'border-rose-600 text-rose-600 font-bold bg-white' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-4 py-3 border-b-2 text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>Enquiries ({{ $enquiries->total() }})</span>
                </button>

                <button @click="activeTab = 'bookings'"
                        :class="activeTab === 'bookings' ? 'border-rose-600 text-rose-600 font-bold bg-white' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-4 py-3 border-b-2 text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Workshop Bookings ({{ $serviceBookings->total() }})</span>
                </button>

                <button @click="activeTab = 'audit'"
                        :class="activeTab === 'audit' ? 'border-rose-600 text-rose-600 font-bold bg-white' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-4 py-3 border-b-2 text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Audit Trail ({{ $auditLogs->count() }})</span>
                </button>

                <button @click="activeTab = 'profile'"
                        :class="activeTab === 'profile' ? 'border-rose-600 text-rose-600 font-bold bg-white' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-4 py-3 border-b-2 text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Personal Info & Notes</span>
                </button>
            </div>

            <!-- TAB 1: Associated Showrooms & Subscriptions -->
            <div x-show="activeTab === 'showrooms'" class="space-y-6">
                @if($user->showrooms->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($user->showrooms as $sr)
                            @php
                                $sub = $sr->subscription;
                                $plan = $sub?->plan;
                            @endphp
                            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-5 flex flex-col justify-between">
                                <div class="space-y-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-lg font-display">
                                                {{ strtoupper(substr($sr->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <h3 class="font-bold text-slate-900 text-base">{{ $sr->name }}</h3>
                                                <p class="text-xs text-slate-500 font-mono">/showrooms/{{ $sr->slug }}</p>
                                            </div>
                                        </div>

                                        @if($sr->status === 'active')
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                {{ ucfirst($sr->status) }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Subscription Card Detail -->
                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-xs space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-500 font-medium">Subscription Tier:</span>
                                            <span class="font-bold text-slate-900">{{ $plan?->name ?: 'No Active Plan' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-500 font-medium">Billing Cycle:</span>
                                            <span class="font-semibold text-slate-700">{{ $sub?->billing_interval ? ucfirst($sub->billing_interval) : 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-500 font-medium">Current Period:</span>
                                            <span class="font-semibold text-slate-700">
                                                @if($sub)
                                                    {{ $sub->current_period_start->format('M d, Y') }} - {{ $sub->current_period_end->format('M d, Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-500 font-medium">Auto Renewal:</span>
                                            <span class="font-bold {{ $sub?->auto_renew ? 'text-emerald-700' : 'text-slate-500' }}">
                                                {{ $sub?->auto_renew ? 'Enabled' : 'Disabled' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between pt-2 border-t border-slate-200/80">
                                            <span class="text-slate-500 font-medium">Fleet Inventory:</span>
                                            <span class="font-bold text-slate-900">{{ $sr->bikes->count() }} Motorcycles</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                    <a href="{{ route('showrooms.show', $sr) }}" target="_blank"
                                       class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                                        <span>View Storefront</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>

                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.showrooms.show', $sr) }}"
                                           class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                                            Manage Showroom
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-xs max-w-lg mx-auto space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">No Showrooms Associated</h3>
                        <p class="text-xs text-slate-500">This account is currently registered as a standard customer or platform administrator without a linked dealership.</p>
                    </div>
                @endif
            </div>

            <!-- TAB 2: Payment & Billing Ledger -->
            <div x-show="activeTab === 'payments'" class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base">Payment & Billing History</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Recorded subscription and transaction invoices</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Total Paid: ₹{{ number_format($telemetry['total_spend']) }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                                <tr>
                                    <th class="py-3 px-5">Ref / ID</th>
                                    <th class="py-3 px-5">Dealership Showroom</th>
                                    <th class="py-3 px-5">Purpose</th>
                                    <th class="py-3 px-5">Amount</th>
                                    <th class="py-3 px-5">Method</th>
                                    <th class="py-3 px-5">Status</th>
                                    <th class="py-3 px-5 text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-xs">
                                @forelse($payments as $p)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-5 font-mono text-slate-600 font-bold">
                                            {{ $p->razorpay_payment_id ?: ('#' . $p->id) }}
                                        </td>
                                        <td class="py-3 px-5">
                                            <span class="font-bold text-slate-900">{{ $p->showroom?->name ?: 'Platform' }}</span>
                                        </td>
                                        <td class="py-3 px-5 text-slate-600">
                                            {{ $p->purpose ?: 'Subscription Renewal' }}
                                        </td>
                                        <td class="py-3 px-5 font-bold text-slate-900">
                                            ₹{{ number_format($p->amount, 2) }}
                                        </td>
                                        <td class="py-3 px-5 uppercase text-slate-500 text-[11px]">
                                            {{ $p->method ?: 'Razorpay' }}
                                        </td>
                                        <td class="py-3 px-5">
                                            @if($p->status->value === 'captured')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Captured
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    {{ ucfirst($p->status->value) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-5 text-right text-slate-500 whitespace-nowrap">
                                            {{ $p->created_at->format('M d, Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-10 text-center text-slate-500">
                                            No payment transactions recorded for this account.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($payments->hasPages())
                        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                            {{ $payments->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 3: Enquiries & Test Rides -->
            <div x-show="activeTab === 'enquiries'" class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-base">Customer Enquiries & Test Ride Requests</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Leads submitted or assigned to this user</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                                <tr>
                                    <th class="py-3 px-5">Enquiry #</th>
                                    <th class="py-3 px-5">Dealership</th>
                                    <th class="py-3 px-5">Bike Interested</th>
                                    <th class="py-3 px-5">Scheduled Window</th>
                                    <th class="py-3 px-5">Status</th>
                                    <th class="py-3 px-5 text-right">Created</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-xs">
                                @forelse($enquiries as $eq)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-5 font-mono text-slate-700 font-bold">
                                            {{ $eq->enquiry_number ?: ('#' . $eq->id) }}
                                        </td>
                                        <td class="py-3 px-5 font-bold text-slate-900">
                                            {{ $eq->showroom?->name ?: 'General' }}
                                        </td>
                                        <td class="py-3 px-5 text-slate-700">
                                            {{ $eq->bike?->model_name ?: 'General Purchase Inquiry' }}
                                        </td>
                                        <td class="py-3 px-5 text-slate-600">
                                            {{ $eq->preferred_date ? $eq->preferred_date->format('M d, Y') : 'Immediate' }}
                                            @if($eq->preferred_time_slot)
                                                ({{ $eq->preferred_time_slot }})
                                            @endif
                                        </td>
                                        <td class="py-3 px-5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                                {{ ucfirst(str_replace('_', ' ', $eq->status)) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-5 text-right text-slate-500 whitespace-nowrap">
                                            {{ $eq->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-10 text-center text-slate-500">
                                            No inquiries recorded.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($enquiries->hasPages())
                        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                            {{ $enquiries->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 4: Workshop Service Bookings -->
            <div x-show="activeTab === 'bookings'" class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-base">Workshop Service Appointments</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Service maintenance, inspections, and repair appointments</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                                <tr>
                                    <th class="py-3 px-5">Booking #</th>
                                    <th class="py-3 px-5">Dealership Workshop</th>
                                    <th class="py-3 px-5">Bike Model</th>
                                    <th class="py-3 px-5">Appointment Slot</th>
                                    <th class="py-3 px-5">Status</th>
                                    <th class="py-3 px-5 text-right">Booked Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-xs">
                                @forelse($serviceBookings as $b)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-5 font-mono text-slate-700 font-bold">
                                            {{ $b->booking_number ?: ('#' . $b->id) }}
                                        </td>
                                        <td class="py-3 px-5 font-bold text-slate-900">
                                            {{ $b->showroom?->name ?: 'Workshop' }}
                                        </td>
                                        <td class="py-3 px-5 text-slate-700">
                                            {{ $b->bike_make_model ?: ($b->bike?->model_name ?: 'Motorcycle') }}
                                        </td>
                                        <td class="py-3 px-5 text-slate-600">
                                            {{ $b->preferred_date ? $b->preferred_date->format('M d, Y') : 'TBD' }} ({{ $b->time_slot ?: 'Morning' }})
                                        </td>
                                        <td class="py-3 px-5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                {{ ucfirst(str_replace('_', ' ', $b->status)) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-5 text-right text-slate-500 whitespace-nowrap">
                                            {{ $b->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-10 text-center text-slate-500">
                                            No workshop service appointments recorded.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($serviceBookings->hasPages())
                        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                            {{ $serviceBookings->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 5: Audit Trail -->
            <div x-show="activeTab === 'audit'" class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-base">User Activity & Security Audit Trail</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Immutable record of changes, logins, and operations</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                                <tr>
                                    <th class="py-3 px-5">Action Event</th>
                                    <th class="py-3 px-5">Target Entity</th>
                                    <th class="py-3 px-5">IP Address</th>
                                    <th class="py-3 px-5 text-right">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-xs">
                                @forelse($auditLogs as $log)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-5 font-mono text-slate-900 font-bold">
                                            {{ $log->action }}
                                        </td>
                                        <td class="py-3 px-5 text-slate-600">
                                            {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                        </td>
                                        <td class="py-3 px-5 text-slate-500 font-mono text-[11px]">
                                            {{ $log->ip_address ?: '127.0.0.1' }}
                                        </td>
                                        <td class="py-3 px-5 text-right text-slate-500 whitespace-nowrap">
                                            {{ $log->created_at->format('M d, Y H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-10 text-center text-slate-500">
                                            No audit log events found for this user account.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 6: Personal Profile & Notes -->
            <div x-show="activeTab === 'profile'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-200 pb-3">Personal & Contact Record</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Full Legal Name</span>
                                <span class="text-sm font-bold text-slate-900 mt-1 block">{{ $user->name }}</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Primary Email Address</span>
                                <span class="text-sm font-bold text-slate-900 mt-1 block">{{ $user->email }}</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Contact Phone Number</span>
                                <span class="text-sm font-bold text-slate-900 mt-1 block">{{ $user->phone ?: 'Not provided' }}</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">City Location</span>
                                <span class="text-sm font-bold text-slate-900 mt-1 block">{{ $user->city ?: 'Not provided' }}</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 sm:col-span-2">
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Street Address</span>
                                <span class="text-sm font-medium text-slate-800 mt-1 block">{{ $user->address ?: 'Not provided' }}</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block mb-2">Showroom Notes & Biography</span>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                                {{ $user->bio ?: 'No personal notes or showroom biography recorded.' }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-200 pb-3">System Specifications</h3>

                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">System Role</span>
                                <span class="font-bold text-slate-900 uppercase">{{ $user->role }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">Account Status</span>
                                <span class="font-bold text-emerald-600 uppercase">{{ $user->status }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">Email Verified</span>
                                <span class="font-bold text-slate-800">{{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'No' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-500">OAuth Providers</span>
                                <span class="font-bold text-slate-800">{{ $user->socialAccounts->count() > 0 ? $user->socialAccounts->pluck('provider')->implode(', ') : 'Direct Password' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-slate-500">Profile Identifier</span>
                                <span class="font-mono text-slate-600 font-bold">#{{ $user->id }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <a href="{{ route('users.edit', $user) }}"
                               class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit User Profile</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @if(auth()->check() && auth()->user()->isAdmin())
        @include('admin.partials.nav-close')
    @endif
</x-app-layout>
