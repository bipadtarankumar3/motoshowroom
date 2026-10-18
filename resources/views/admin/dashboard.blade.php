<x-app-layout title="Super Admin Dashboard | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 xl:px-10">
        <div class="w-full space-y-8">

            <!-- Catalyst-style Header (Welcome back + Action Controls) -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/80 pb-6">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center font-black text-white text-lg shadow-md shadow-orange-500/20 ring-4 ring-orange-50 flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-black font-display text-slate-900 tracking-tight">Super Admin Dashboard</h1>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200">
                                Multi-Tenant Master Control
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                            <span>Welcome back, {{ auth()->user()->name }}</span>
                            <span>👋</span>
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs text-slate-600 shadow-2xs font-medium">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Last 30 Days</span>
                    </div>

                    <a href="{{ route('admin.settings.index') }}" class="p-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-800 transition shadow-2xs" title="Platform Settings">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>

                    <a href="{{ route('admin.showrooms.create') }}" class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs shadow-md shadow-orange-600/20 transition flex items-center gap-2" id="btn-provision-showroom">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ New Showroom</span>
                    </a>
                </div>
            </div>

            <!-- Key Metric Cards Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Showrooms Total -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Total Showrooms</span>
                        <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black font-display text-slate-900 mt-2">{{ $stats['total_showrooms'] }}</p>
                    <div class="flex items-center gap-2 mt-3 text-xs">
                        <span class="text-emerald-700 font-bold">{{ $stats['active_showrooms'] }} Active</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-amber-700 font-bold">{{ $stats['trial_showrooms'] }} Trial</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-rose-700 font-bold">{{ $stats['suspended_showrooms'] }} Suspended</span>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Platform Users</span>
                        <span class="p-2 rounded-xl bg-sky-50 text-sky-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black font-display text-slate-900 mt-2">{{ $stats['total_users'] }}</p>
                    <div class="flex items-center gap-2 mt-3 text-xs">
                        <span class="text-rose-700 font-bold">{{ $stats['admin_users'] }} Admins</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-sky-700 font-bold">{{ $stats['regular_users'] }} Customers/Staff</span>
                    </div>
                </div>

                <!-- Audit Events -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Audit Trail Events</span>
                        <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                    </div>
                    <p class="text-3xl font-black font-display text-slate-900 mt-2">{{ $stats['total_audit_events'] }}</p>
                    <div class="mt-3 text-xs text-slate-500">
                        Append-only immutable record
                    </div>
                </div>

                <!-- Multi-Tenant Architecture Status -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Isolation Engine</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                    <p class="text-2xl font-black font-display text-emerald-700 mt-2">Shared DB Isolation</p>
                    <div class="mt-3 text-xs text-slate-500 flex items-center justify-between">
                        <span>Database: MySQL 8.0</span>
                        <span class="text-emerald-700 font-semibold">Active</span>
                    </div>
                </div>
            </div>

            <!-- Main Content Two-Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left 2 Cols: Recent Showrooms -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold font-display text-slate-900">Showroom Tenants Directory</h2>
                        <a href="{{ route('admin.showrooms.index') }}" class="text-xs text-rose-600 hover:text-rose-700 font-semibold flex items-center gap-1">
                            <span>View All Showrooms</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                        <div class="divide-y divide-slate-100 font-medium">
                            @forelse($recentShowrooms as $showroom)
                                <div class="p-5 hover:bg-slate-50/80 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-start gap-3.5">
                                        <div class="w-11 h-11 rounded-xl flex items-center justify-center font-black text-white shrink-0 shadow-xs"
                                             style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                                            {{ strtoupper(substr($showroom->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.showrooms.show', $showroom) }}" class="font-bold text-slate-900 hover:text-rose-600 transition text-base">
                                                    {{ $showroom->name }}
                                                </a>
                                                @if($showroom->status === 'active')
                                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                                                @elseif($showroom->status === 'trial')
                                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Trial</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Suspended</span>
                                                @endif
                                            </div>
                                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1">
                                                <span>{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</span>
                                                <span>•</span>
                                                <span>Owner: <strong class="text-slate-700">{{ $showroom->owner?->name ?? 'None' }}</strong></span>
                                                <span>•</span>
                                                <span class="font-mono text-slate-400">/showrooms/{{ $showroom->slug }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ route('showrooms.show', $showroom) }}" target="_blank" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs text-slate-700 hover:text-slate-900 shadow-xs transition flex items-center gap-1" title="Public storefront">
                                            <span>Storefront</span>
                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>

                                        <a href="{{ route('admin.showrooms.show', $showroom) }}" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold shadow-xs transition">
                                            Manage
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400">
                                    No showrooms provisioned yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Col: Recent Audit Trail Feed -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold font-display text-slate-900">Live Audit Feed</h2>
                        <a href="{{ route('admin.audit-logs.index') }}" class="text-xs text-rose-600 hover:text-rose-700 font-semibold flex items-center gap-1">
                            <span>View All</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4">
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @forelse($recentAuditLogs as $log)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-xs text-slate-700">
                                                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-xs text-slate-700">
                                                        <span class="font-bold text-slate-900">{{ $log->action }}</span>
                                                        @if($log->showroom)
                                                            <span class="text-slate-500">on <strong class="text-rose-700">{{ $log->showroom->name }}</strong></span>
                                                        @endif
                                                    </div>
                                                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2">
                                                        <span>{{ $log->user?->name ?? 'System' }}</span>
                                                        <span>•</span>
                                                        <span>{{ $log->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-center py-6 text-xs text-slate-400">
                                        No audit activity logged yet.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    @include('admin.partials.nav-close')
</x-app-layout>
