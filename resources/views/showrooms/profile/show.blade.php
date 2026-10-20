@extends('layouts.app')

@section('title', $user->name . ' — Showroom Owner Profile')

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans"
     x-data="{ activeTab: 'showrooms' }">

    @if($showroom)
        @include('showrooms.partials.manage-nav')
    @endif

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-6 sm:py-8 space-y-6">

        <!-- Flash Feedback Notifications -->
        @if (session('success'))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
            @if($showroom)
                <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Showroom Back-Office</a>
                <span>/</span>
                <span class="text-slate-600">Owner Profile</span>
            @else
                <a href="{{ route('dashboard') }}" class="hover:text-slate-900 transition">Dashboard</a>
                <span>/</span>
                <span class="text-slate-600">My Profile</span>
            @endif
            <span>/</span>
            <span class="text-brand-primary font-bold">{{ $user->name }}</span>
        </div>

        <!-- Showroom Owner Header Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-start sm:items-center gap-5">
                @php
                    $primaryBg = $showroom ? $showroom->primary_color : '#0f172a';
                    $primaryText = $showroom ? $showroom->primary_text_color : '#ffffff';
                @endphp
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center font-black text-2xl font-display uppercase tracking-wider shadow-md shrink-0"
                     style="background-color: {{ $primaryBg }}; color: {{ $primaryText }};">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>

                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                        <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">{{ $user->name }}</h1>
                        <span class="font-mono text-xs text-slate-400 font-bold">#{{ $user->id }}</span>

                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-800 border border-slate-200">
                            {{ $user->role === 'admin' ? 'Admin' : 'User' }}
                        </span>

                        @if($user->showrooms->isNotEmpty())
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                                Showroom Owner
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
                                Verified Email
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                Unverified Email
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <strong class="text-slate-700">{{ $user->email }}</strong>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <strong class="text-slate-700">{{ $user->phone ?: 'No phone added' }}</strong>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <strong class="text-slate-700">{{ $user->city ?: 'No city' }}</strong>
                        </span>
                        <span>•</span>
                        <span>
                            Member since <strong class="text-slate-700">{{ $user->created_at->format('M d, Y') }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Controls -->
            <div class="flex items-center gap-3">
                @php
                    $editUrl = $showroom
                        ? route('showrooms.manage.profile.edit', $showroom)
                        : route('users.edit', $user);
                @endphp
                <a href="{{ $editUrl }}"
                   class="btn-brand-primary px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-md transition flex items-center gap-2 cursor-pointer"
                   id="btn-edit-owner-details">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Details</span>
                </a>
            </div>
        </div>

        <!-- 6 Telemetry Stat Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
            <!-- Dealership Showrooms -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                <span class="text-[11px] uppercase font-bold tracking-wider text-slate-500">Showrooms</span>
                <p class="text-2xl sm:text-3xl font-black font-display text-slate-900 mt-2">{{ $telemetry['showrooms_count'] }}</p>
                <span class="text-[11px] text-slate-500 font-medium mt-1 block">Tenants associated</span>
            </div>

            <!-- Total Paid Spend -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                <span class="text-[11px] uppercase font-bold tracking-wider text-emerald-700">Total Spend</span>
                <p class="text-2xl sm:text-3xl font-black font-display text-emerald-600 mt-2">₹{{ number_format($telemetry['total_spend']) }}</p>
                <span class="text-[11px] text-emerald-700/80 font-medium mt-1 block">{{ $telemetry['captured_payments_count'] }} paid transactions</span>
            </div>

            <!-- Current Plan -->
            @php
                $primaryPlan = $user->showrooms->first()?->subscription?->plan;
            @endphp
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                <span class="text-[11px] uppercase font-bold tracking-wider text-amber-700">Current Plan</span>
                <p class="text-base sm:text-lg font-black font-display text-amber-800 mt-2 truncate">{{ $primaryPlan?->name ?: 'Standard Plan' }}</p>
                <span class="text-[11px] text-amber-700/80 font-medium mt-1 block">
                    {{ ($primaryPlan && $primaryPlan->price !== null) ? '₹' . number_format((float) $primaryPlan->price) . ' / ' . ($primaryPlan->billing_interval ?: 'mo') : 'Free Plan' }}
                </span>
            </div>

            <!-- Subscription Health -->
            @php
                $activeSub = $user->showrooms->first()?->subscription;
                $isSubActive = $activeSub && ! $activeSub->isExpired();
            @endphp
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                <span class="text-[11px] uppercase font-bold tracking-wider {{ $isSubActive ? 'text-emerald-700' : 'text-rose-700' }}">Sub Health</span>
                <p class="text-base sm:text-lg font-black font-display {{ $isSubActive ? 'text-emerald-600' : 'text-rose-600' }} mt-2">
                    {{ $activeSub ? ($isSubActive ? 'Active' : 'Expired') : 'No Plan' }}
                </p>
                <span class="text-[11px] text-slate-500 font-medium mt-1 block">
                    {{ $activeSub?->current_period_end ? $activeSub->current_period_end->diffForHumans() : 'N/A' }}
                </span>
            </div>

            <!-- Enquiries & Leads -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                <span class="text-[11px] uppercase font-bold tracking-wider text-sky-700">Enquiries</span>
                <p class="text-2xl sm:text-3xl font-black font-display text-sky-600 mt-2">{{ $telemetry['enquiries_count'] }}</p>
                <span class="text-[11px] text-sky-700/80 font-medium mt-1 block">Test rides & leads</span>
            </div>

            <!-- Workshop Bookings -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                <span class="text-[11px] uppercase font-bold tracking-wider text-purple-700">Workshops</span>
                <p class="text-2xl sm:text-3xl font-black font-display text-purple-600 mt-2">{{ $telemetry['bookings_count'] }}</p>
                <span class="text-[11px] text-purple-700/80 font-medium mt-1 block">Service bookings</span>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="border-b border-slate-200 flex items-center gap-2 sm:gap-6 overflow-x-auto text-xs sm:text-sm font-bold">
            <button type="button" @click="activeTab = 'showrooms'"
                    :class="activeTab === 'showrooms' ? 'text-brand-primary border-b-2 border-brand-primary pb-3 font-black' : 'text-slate-500 hover:text-slate-900 pb-3 font-medium transition cursor-pointer'"
                    class="flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Showroom Dealerships</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 font-mono">{{ $user->showrooms->count() }}</span>
            </button>

            <button type="button" @click="activeTab = 'payments'"
                    :class="activeTab === 'payments' ? 'text-brand-primary border-b-2 border-brand-primary pb-3 font-black' : 'text-slate-500 hover:text-slate-900 pb-3 font-medium transition cursor-pointer'"
                    class="flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Billing & Payments</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 font-mono">{{ $payments->total() }}</span>
            </button>

            <button type="button" @click="activeTab = 'enquiries'"
                    :class="activeTab === 'enquiries' ? 'text-brand-primary border-b-2 border-brand-primary pb-3 font-black' : 'text-slate-500 hover:text-slate-900 pb-3 font-medium transition cursor-pointer'"
                    class="flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <span>Enquiries</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 font-mono">{{ $enquiries->total() }}</span>
            </button>

            <button type="button" @click="activeTab = 'bookings'"
                    :class="activeTab === 'bookings' ? 'text-brand-primary border-b-2 border-brand-primary pb-3 font-black' : 'text-slate-500 hover:text-slate-900 pb-3 font-medium transition cursor-pointer'"
                    class="flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Workshop Bookings</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 font-mono">{{ $serviceBookings->total() }}</span>
            </button>

            <button type="button" @click="activeTab = 'audit'"
                    :class="activeTab === 'audit' ? 'text-brand-primary border-b-2 border-brand-primary pb-3 font-black' : 'text-slate-500 hover:text-slate-900 pb-3 font-medium transition cursor-pointer'"
                    class="flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Audit Trail</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 font-mono">{{ $auditLogs->count() }}</span>
            </button>

            <button type="button" @click="activeTab = 'info'"
                    :class="activeTab === 'info' ? 'text-brand-primary border-b-2 border-brand-primary pb-3 font-black' : 'text-slate-500 hover:text-slate-900 pb-3 font-medium transition cursor-pointer'"
                    class="flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Personal Info & Notes</span>
            </button>
        </div>

        <!-- ============================================== -->
        <!-- TAB 1: SHOWROOM DEALERSHIPS                   -->
        <!-- ============================================== -->
        <div x-show="activeTab === 'showrooms'" class="space-y-4">
            @if ($user->showrooms->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($user->showrooms as $sh)
                        @php
                            $sub = $sh->subscription;
                            $plan = $sub?->plan;
                            $isExpired = $sub ? $sub->isExpired() : true;
                            $shPrimaryColor = $sh->primary_color;
                            $shPrimaryTextColor = $sh->primary_text_color;
                        @endphp
                        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                            <div class="space-y-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-sm uppercase tracking-wider shrink-0"
                                             style="background-color: {{ $shPrimaryColor }}; color: {{ $shPrimaryTextColor }};">
                                            {{ strtoupper(substr($sh->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-slate-900 text-base leading-tight group-hover:text-slate-950 transition">{{ $sh->name }}</h3>
                                            <span class="font-mono text-[11px] text-slate-400">/showrooms/{{ $sh->slug }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $sh->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $sh->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <div class="space-y-2 py-3 border-y border-slate-100 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Subscription Tier:</span>
                                        <span class="font-bold text-slate-900">{{ $plan?->name ?: 'No Plan' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Billing Cycle:</span>
                                        <span class="font-medium text-slate-700 capitalize">{{ $plan?->billing_interval ?: 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Current Period:</span>
                                        <span class="font-mono text-slate-700 text-[11px]">{{ $sub?->current_period_start?->format('M d, Y') }} - {{ $sub?->current_period_end?->format('M d, Y') ?: 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Auto Renewal:</span>
                                        <span class="font-semibold {{ $sub?->auto_renew ? 'text-emerald-700' : 'text-slate-500' }}">
                                            {{ $sub?->auto_renew ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between pt-1">
                                        <span class="text-slate-500">Fleet Inventory:</span>
                                        <span class="font-bold text-slate-900">{{ $sh->bikes->count() }} Motorcycles</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 flex items-center justify-between gap-3 text-xs">
                                <a href="{{ route('showrooms.show', $sh) }}" target="_blank"
                                   class="text-slate-500 hover:text-slate-900 font-semibold flex items-center gap-1 transition">
                                    <span>View Storefront</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>

                                <a href="{{ route('showrooms.manage', $sh) }}"
                                   class="btn-brand-primary px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs">
                                    Manage Showroom &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/90 shadow-xs space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">No Dealership Showrooms Found</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">You do not currently have any active showroom tenants registered under your profile.</p>
                </div>
            @endif
        </div>

        <!-- ============================================== -->
        <!-- TAB 2: BILLING & PAYMENTS                     -->
        <!-- ============================================== -->
        <div x-show="activeTab === 'payments'" class="space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Payment Ledger & Receipts</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Records of all platform plan subscriptions and add-on transactions.</p>
                    </div>
                </div>

                @if ($payments->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    <th class="py-3 px-4">Transaction / ID</th>
                                    <th class="py-3 px-4">Showroom</th>
                                    <th class="py-3 px-4">Amount</th>
                                    <th class="py-3 px-4">Gateway</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach ($payments as $payment)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                            {{ $payment->payment_reference ?: '#' . $payment->id }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-700">
                                            {{ $payment->showroom?->name ?: 'N/A' }}
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-700">
                                            ₹{{ number_format($payment->amount) }}
                                        </td>
                                        <td class="py-3.5 px-4 uppercase text-[10px] font-bold text-slate-600">
                                            {{ $payment->gateway }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                {{ $payment->status->value === 'captured' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700' }}">
                                                {{ $payment->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-500">
                                            {{ $payment->created_at->format('M d, Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-100">
                        {{ $payments->links() }}
                    </div>
                @else
                    <div class="p-12 text-center text-slate-400 text-xs">
                        No payment transactions recorded yet.
                    </div>
                @endif
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 3: ENQUIRIES & LEADS                      -->
        <!-- ============================================== -->
        <div x-show="activeTab === 'enquiries'" class="space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Purchase Leads & Test Rides</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Direct inquiries submitted by prospective customers for motorcycles.</p>
                </div>

                @if ($enquiries->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    <th class="py-3 px-4">Customer Name</th>
                                    <th class="py-3 px-4">Showroom</th>
                                    <th class="py-3 px-4">Motorcycle Model</th>
                                    <th class="py-3 px-4">Type</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach ($enquiries as $enquiry)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3.5 px-4">
                                            <span class="font-bold text-slate-900 block">{{ $enquiry->customer_name }}</span>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $enquiry->customer_phone }}</span>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-700">
                                            {{ $enquiry->showroom?->name ?: 'N/A' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-800 font-medium">
                                            {{ $enquiry->bike?->title ?: 'General Inquiry' }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                                {{ $enquiry->enquiry_type ?: 'Purchase' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                                {{ ucfirst($enquiry->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-500">
                                            {{ $enquiry->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-100">
                        {{ $enquiries->links() }}
                    </div>
                @else
                    <div class="p-12 text-center text-slate-400 text-xs">
                        No customer enquiries found.
                    </div>
                @endif
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 4: WORKSHOP BOOKINGS                      -->
        <!-- ============================================== -->
        <div x-show="activeTab === 'bookings'" class="space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Workshop & Service Appointments</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Scheduled maintenance, repairs, and service packages across your bays.</p>
                </div>

                @if ($serviceBookings->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    <th class="py-3 px-4">Job Code</th>
                                    <th class="py-3 px-4">Customer</th>
                                    <th class="py-3 px-4">Showroom</th>
                                    <th class="py-3 px-4">Vehicle / Reg</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Slot Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach ($serviceBookings as $booking)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                            {{ $booking->job_card_number ?: '#' . $booking->id }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-900 font-bold">
                                            {{ $booking->customer_name }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-700">
                                            {{ $booking->showroom?->name ?: 'N/A' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-800">
                                            {{ $booking->bike_model }} <span class="text-[10px] font-mono text-slate-400">({{ $booking->registration_number }})</span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                                                {{ $booking->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-500">
                                            {{ $booking->scheduled_date?->format('M d, Y') }} {{ $booking->slot_time }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-100">
                        {{ $serviceBookings->links() }}
                    </div>
                @else
                    <div class="p-12 text-center text-slate-400 text-xs">
                        No workshop bookings registered yet.
                    </div>
                @endif
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 5: AUDIT TRAIL                            -->
        <!-- ============================================== -->
        <div x-show="activeTab === 'audit'" class="space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 space-y-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Security & Activity Audit Log</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Chronological record of authentication attempts and administrative updates.</p>
                </div>

                @if($auditLogs->isNotEmpty())
                    <div class="divide-y divide-slate-100 text-xs">
                        @foreach($auditLogs as $log)
                            <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-800">{{ $log->action }}</span>
                                        <span class="text-slate-500 text-[11px] block">{{ $log->description ?: 'System action executed' }}</span>
                                    </div>
                                </div>
                                <div class="text-right text-[11px] text-slate-400 font-mono">
                                    {{ $log->created_at->format('M d, Y H:i:s') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400 text-xs">
                        No audit log entries recorded for this account.
                    </div>
                @endif
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 6: PERSONAL INFO & NOTES                  -->
        <!-- ============================================== -->
        <div x-show="activeTab === 'info'" class="space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Owner Contact & Location Profile</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Personal details used for administrative correspondence and dealership registry.</p>
                    </div>
                    <a href="{{ $editUrl }}" class="btn-brand-primary px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs">
                        Edit Information
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    <div>
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Full Name</span>
                        <p class="text-sm font-bold text-slate-900">{{ $user->name }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Email Address</span>
                        <p class="text-sm font-bold text-slate-900 font-mono">{{ $user->email }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Phone Number</span>
                        <p class="text-sm font-bold text-slate-900">{{ $user->phone ?: 'Not provided' }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">City</span>
                        <p class="text-sm font-bold text-slate-900">{{ $user->city ?: 'Not provided' }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Physical Address / Headquarters</span>
                        <p class="text-sm text-slate-800">{{ $user->address ?: 'No address specified' }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Bio / Showroom Background Notes</span>
                        <p class="text-sm text-slate-800 leading-relaxed">{{ $user->bio ?: 'No bio provided.' }}</p>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>
@endsection
