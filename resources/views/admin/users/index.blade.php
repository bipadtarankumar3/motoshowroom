<x-app-layout title="User Management - Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')
    <div class="bg-slate-50 min-h-screen py-6 sm:py-8 px-4 sm:px-6 lg:px-8 xl:px-10 w-full"
         x-data="{
             selected: [],
             selectAll: false,
             detailUser: null,
             showDetailModal: false,
             toggleAll(allIds) {
                 if (this.selectAll) {
                     this.selected = [...allIds];
                 } else {
                     this.selected = [];
                 }
             },
             openDetails(user) {
                 this.detailUser = user;
                 this.showDetailModal = true;
             }
         }">
        <div class="space-y-6 sm:space-y-8 w-full">

            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Admin</a>
                <span>/</span>
                <span class="text-rose-600 font-semibold">Users</span>
            </div>

            <!-- Top Header & Primary Actions -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Platform Users</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                            Super Admin Console
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mt-1">Manage platform members, dealership owners, staff personnel, and subscription lifecycle.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.users.export', request()->query()) }}"
                       class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold transition flex items-center gap-2 shadow-xs cursor-pointer"
                       title="Export all matching users to CSV">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export CSV</span>
                    </a>

                    <a href="{{ route('profile') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold transition flex items-center gap-2 shadow-xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>My Profile</span>
                    </a>

                    <a href="{{ route('admin.users.create') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs sm:text-sm shadow-xs transition flex items-center gap-2" id="btn-create-user-top">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Create User</span>
                    </a>
                </div>
            </div>

            <!-- Dynamic Metric Stat Cards (Interactive Preset Filters) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                <!-- All Users -->
                <a href="{{ route('admin.users.index') }}"
                   class="p-4 rounded-2xl border transition shadow-xs block group {{ !request()->filled('preset') && !request()->filled('role') && !request()->filled('status') ? 'bg-rose-50/50 border-rose-300 ring-2 ring-rose-500/20' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-slate-500">Total Users</span>
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-slate-900 mt-2">{{ number_format($stats['total']) }}</p>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Full platform registry</span>
                </a>

                <!-- Registered Today -->
                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['preset' => 'today'])) }}"
                   class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'today' ? 'bg-amber-50/60 border-amber-300 ring-2 ring-amber-500/20' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-amber-700">Today</span>
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-amber-600 mt-2">{{ number_format($stats['registered_today']) }}</p>
                    <span class="text-[11px] text-amber-700/80 font-medium mt-1 block">New signups today</span>
                </a>

                <!-- Registered This Week -->
                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['preset' => 'week'])) }}"
                   class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'week' ? 'bg-sky-50/60 border-sky-300 ring-2 ring-sky-500/20' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-sky-700">In 7 Days</span>
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-sky-600 mt-2">{{ number_format($stats['registered_this_week']) }}</p>
                    <span class="text-[11px] text-sky-700/80 font-medium mt-1 block">Past 7 days volume</span>
                </a>

                <!-- Registered In 1 Month -->
                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['preset' => 'month'])) }}"
                   class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'month' ? 'bg-indigo-50/60 border-indigo-300 ring-2 ring-indigo-500/20' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-indigo-700">In 1 Month</span>
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-indigo-600 mt-2">{{ number_format($stats['registered_this_month']) }}</p>
                    <span class="text-[11px] text-indigo-700/80 font-medium mt-1 block">Past 30 days growth</span>
                </a>

                <!-- Expired / Suspended Users -->
                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['preset' => 'expired'])) }}"
                   class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'expired' ? 'bg-rose-50/60 border-rose-300 ring-2 ring-rose-500/20' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-rose-700">Expired / At Risk</span>
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-rose-600 mt-2">{{ number_format($stats['expired_or_suspended']) }}</p>
                    <span class="text-[11px] text-rose-700/80 font-medium mt-1 block">Expired or suspended</span>
                </a>

                <!-- Recently Paid / Renewed -->
                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['preset' => 'recently_paid'])) }}"
                   class="p-4 rounded-2xl border transition shadow-xs block group {{ request('preset') === 'recently_paid' ? 'bg-emerald-50/60 border-emerald-300 ring-2 ring-emerald-500/20' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-emerald-700">Recently Paid</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-emerald-600 mt-2">{{ number_format($stats['recently_paid']) }}</p>
                    <span class="text-[11px] text-emerald-700/80 font-medium mt-1 block">Renewed in 30 days</span>
                </a>
            </div>

            <!-- Quick Filter Navigation Bar (Pills) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <a href="{{ route('admin.users.index') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border {{ !request()->filled('preset') && !request()->filled('role') && !request()->filled('status') ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                    All Users ({{ $stats['total'] }})
                </a>

                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['preset' => 'today'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'today' ? 'bg-amber-600 text-white border-amber-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-amber-50 hover:text-amber-800' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'today' ? 'bg-white' : 'bg-amber-500' }}"></span>
                    Registered Today ({{ $stats['registered_today'] }})
                </a>

                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['preset' => 'week'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'week' ? 'bg-sky-600 text-white border-sky-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-sky-50 hover:text-sky-800' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'week' ? 'bg-white' : 'bg-sky-500' }}"></span>
                    Registered in 1 Week ({{ $stats['registered_this_week'] }})
                </a>

                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['preset' => 'month'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'month' ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-indigo-50 hover:text-indigo-800' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'month' ? 'bg-white' : 'bg-indigo-500' }}"></span>
                    Registered in 1 Month ({{ $stats['registered_this_month'] }})
                </a>

                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['preset' => 'expired'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'expired' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-rose-50 hover:text-rose-800' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'expired' ? 'bg-white' : 'bg-rose-500' }}"></span>
                    Expired / At Risk ({{ $stats['expired_or_suspended'] }})
                </a>

                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['preset' => 'recently_paid'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'recently_paid' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-emerald-50 hover:text-emerald-800' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ request('preset') === 'recently_paid' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                    Recently Paid Users ({{ $stats['recently_paid'] }})
                </a>

                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['preset' => 'owners'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('preset') === 'owners' ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    Showroom Owners ({{ $stats['owners'] }})
                </a>

                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'user', 'preset' => null])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('role') === 'user' ? 'bg-sky-600 text-white border-sky-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-sky-50 hover:text-sky-800' }}">
                    Regular Users ({{ $stats['regular_users'] }})
                </a>

                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => 'admin', 'preset' => null])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition border flex items-center gap-1.5 {{ request('role') === 'admin' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-rose-50 hover:text-rose-800' }}">
                    Admins ({{ $stats['admins'] }})
                </a>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                    {{-- Search Input (5 cols) --}}
                    <div class="md:col-span-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search name, email, phone, city, or showroom..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    {{-- Role Selector (2 cols) --}}
                    <div class="md:col-span-2">
                        <select name="role" class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>

                    {{-- Status Selector (2 cols) --}}
                    <div class="md:col-span-2">
                        <select name="status" class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    {{-- Showroom Association (2 cols) --}}
                    <div class="md:col-span-2">
                        <select name="showroom_id" class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Showrooms</option>
                            @foreach($allShowrooms as $sr)
                                <option value="{{ $sr->id }}" {{ request('showroom_id') == $sr->id ? 'selected' : '' }}>
                                    {{ $sr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Hidden Preset Preservation --}}
                    @if(request()->filled('preset'))
                        <input type="hidden" name="preset" value="{{ request('preset') }}">
                    @endif

                    {{-- Action Buttons (2 cols) --}}
                    <div class="md:col-span-2 flex items-center gap-2">
                        <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold transition shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <span>Filter</span>
                        </button>

                        @if (request()->hasAny(['search', 'role', 'status', 'showroom_id', 'preset']))
                            <a href="{{ route('admin.users.index') }}" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition text-center whitespace-nowrap">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Floating Bulk Actions Bar -->
            <div x-show="selected.length > 0"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="bg-slate-900 text-white p-3.5 sm:px-6 rounded-2xl flex flex-wrap items-center justify-between gap-3 shadow-lg"
                 x-cloak>
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-rose-600 text-white text-xs font-black flex items-center justify-center" x-text="selected.length"></span>
                    <span class="text-xs sm:text-sm font-semibold">User(s) selected</span>
                </div>

                <div class="flex items-center gap-2.5">
                    {{-- Export Selected --}}
                    <form method="GET" action="{{ route('admin.users.export') }}" target="_blank">
                        <input type="hidden" name="ids" :value="selected.join(',')">
                        <button type="submit"
                                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Export Selected</span>
                        </button>
                    </form>

                    {{-- Bulk Delete Selected --}}
                    <form method="POST" action="{{ route('admin.users.bulk-destroy') }}" onsubmit="return confirm('Are you sure you want to delete all selected users? This action cannot be undone.');">
                        @csrf
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit"
                                class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Delete Selected</span>
                        </button>
                    </form>

                    {{-- Clear Selection --}}
                    <button type="button" @click="selected = []; selectAll = false"
                            class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition cursor-pointer">
                        Clear
                    </button>
                </div>
            </div>

            <!-- Users Data Table with Rich Details -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50/90 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                            <tr>
                                <th scope="col" class="py-4 px-4 sm:px-6 w-12 text-center">
                                    <input type="checkbox"
                                           x-model="selectAll"
                                           @change="toggleAll({{ json_encode($users->pluck('id')->all()) }})"
                                           class="w-4 h-4 rounded text-rose-600 border-slate-300 focus:ring-rose-500 cursor-pointer"
                                           title="Select all on this page">
                                </th>
                                <th scope="col" class="py-4 px-4 min-w-[220px]">User Profile</th>
                                <th scope="col" class="py-4 px-4 min-w-[150px]">Showroom Dealership</th>
                                <th scope="col" class="py-4 px-4 min-w-[130px]">Subscription Plan</th>
                                <th scope="col" class="py-4 px-4 min-w-[120px]">Role & Access</th>
                                <th scope="col" class="py-4 px-4 min-w-[110px]">Account Status</th>
                                <th scope="col" class="py-4 px-4 min-w-[140px]">Registered On</th>
                                <th scope="col" class="py-4 px-4 sm:px-6 text-right min-w-[200px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse ($users as $u)
                                @php
                                    $primaryShowroom = $u->showrooms->first();
                                    $subscription = $primaryShowroom?->subscription;
                                    $lastPayment = $u->payments->first();
                                    $isRegisteredToday = $u->created_at->isToday();
                                    $isRegisteredThisWeek = $u->created_at->isAfter(now()->subDays(7));
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition" :class="selected.includes({{ $u->id }}) ? 'bg-rose-50/40' : ''">
                                    <td class="py-4 px-4 sm:px-6 text-center">
                                        <input type="checkbox"
                                               value="{{ $u->id }}"
                                               x-model="selected"
                                               class="w-4 h-4 rounded text-rose-600 border-slate-300 focus:ring-rose-500 cursor-pointer">
                                    </td>

                                    <!-- User Profile Info -->
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-50 to-rose-100 border border-rose-200 flex items-center justify-center font-bold text-rose-600 text-sm font-display flex-shrink-0 shadow-2xs">
                                                {{ strtoupper(substr($u->name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5">
                                                    <a href="{{ route('admin.users.show', $u) }}" class="font-bold text-slate-900 hover:text-rose-600 transition truncate text-sm">
                                                        {{ $u->name }}
                                                    </a>
                                                    @if($u->email_verified_at)
                                                        <span title="Email Verified" class="text-emerald-500 inline-block">
                                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-slate-500 block truncate">{{ $u->email }}</span>
                                                <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-2">
                                                    <span>{{ $u->phone ?: 'No phone' }}</span>
                                                    @if($u->city)
                                                        <span>•</span>
                                                        <span>{{ $u->city }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Showroom Dealership Association -->
                                    <td class="py-4 px-4">
                                        @if($primaryShowroom)
                                            <div class="space-y-1">
                                                <a href="{{ route('admin.showrooms.show', $primaryShowroom) }}" class="font-bold text-slate-800 hover:text-rose-600 transition text-xs block truncate">
                                                    {{ $primaryShowroom->name }}
                                                </a>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                    {{ $u->pivot?->role ? ucfirst($u->pivot->role) : 'Member' }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 italic">No Showroom</span>
                                        @endif
                                    </td>

                                    <!-- Subscription Plan & Health -->
                                    <td class="py-4 px-4">
                                        @if($subscription && $subscription->plan)
                                            <div>
                                                <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 block w-max">
                                                    {{ $subscription->plan->name }}
                                                </span>
                                                <div class="text-[11px] mt-1">
                                                    @if($subscription->isActive())
                                                        <span class="text-emerald-700 font-semibold">Active</span>
                                                        <span class="text-slate-400">• {{ $subscription->current_period_end->diffForHumans(['parts' => 1]) }}</span>
                                                    @elseif($subscription->isExpired())
                                                        <span class="text-rose-600 font-semibold">Expired</span>
                                                    @else
                                                        <span class="text-slate-500">{{ ucfirst($subscription->status->value) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">Standard User</span>
                                        @endif
                                    </td>

                                    <!-- Role Pill -->
                                    <td class="py-4 px-4">
                                        @if ($u->isAdmin())
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center gap-1">
                                                <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                Admin
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                                User
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Account Status -->
                                    <td class="py-4 px-4">
                                        @if ($u->status === 'active')
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5 w-max">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                            </span>
                                        @elseif($u->status === 'suspended')
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1.5 w-max">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspended
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-300 flex items-center gap-1.5 w-max">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Registration Date & Relative Badge -->
                                    <td class="py-4 px-4 text-xs whitespace-nowrap">
                                        <div class="font-semibold text-slate-800">{{ $u->created_at->format('M d, Y') }}</div>
                                        <div class="mt-0.5">
                                            @if($isRegisteredToday)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                                    Today
                                                </span>
                                            @elseif($isRegisteredThisWeek)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">
                                                    This Week
                                                </span>
                                            @else
                                                <span class="text-[11px] text-slate-400">{{ $u->created_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Row Actions -->
                                    <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            {{-- Visit Dashboard (Impersonate) --}}
                                            @if ($u->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.impersonate', $u) }}" class="inline-block m-0">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold transition shadow-2xs cursor-pointer"
                                                            title="Visit this user's dashboard (Impersonate)">
                                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Visit Dashboard</span>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- More Details Page Link --}}
                                            <a href="{{ route('admin.users.show', $u) }}"
                                               class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition border border-slate-200 shadow-2xs"
                                               title="View full user details page">
                                                <span>More Details</span>
                                            </a>

                                            {{-- Edit --}}
                                            <a href="{{ route('users.edit', $u) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition border border-slate-300 shadow-2xs" title="Edit user">
                                                Edit
                                            </a>

                                            {{-- Delete Single --}}
                                            @if ($u->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                                      onsubmit="return confirm('Are you sure you want to delete user \'{{ $u->name }}\'?');"
                                                      class="inline-block m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                            title="Delete User">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-16 text-center text-slate-500">
                                        <div class="max-w-md mx-auto space-y-3">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto text-slate-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                            </div>
                                            <p class="text-base font-bold text-slate-800">No users found</p>
                                            <p class="text-xs text-slate-500">No user records matched your search query or active filter preset.</p>
                                            <div class="pt-2">
                                                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold inline-block">
                                                    Reset Filters
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($users->hasPages())
                    <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>
    @include('admin.partials.nav-close')
</x-app-layout>
