<x-app-layout title="Staff & Personnel Roster | {{ $showroom->name }}">
    @include('showrooms.partials.manage-nav')

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 xl:px-10" x-data="{
        addModalOpen: false,
        editModalOpen: false,
        statusModalOpen: false,
        currentStaff: {
            id: null,
            name: '',
            email: '',
            phone: '',
            role: 'sales_executive',
            department: 'Sales & Customer Relations',
            designation: '',
            shift: 'General (9:30 AM - 6:30 PM)',
            availability_status: 'available',
            notes: '',
            status: 'active',
            is_owner: false
        },
        openEdit(member) {
            this.currentStaff = { ...member };
            this.editModalOpen = true;
        },
        openStatus(member) {
            this.currentStaff = { ...member };
            this.statusModalOpen = true;
        }
    }">
        <div class="w-full space-y-6">

            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-xs animate-fade-in">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs animate-fade-in">
                    <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
                    <p class="font-bold flex items-center gap-2 mb-1.5">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Please correct the following errors:
                    </p>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/80 pb-5">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black font-display text-slate-900 tracking-tight">Staff & Personnel Roster</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $totalStaff }} Members
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Manage authorized dealership personnel, monitor live attendance & busy status, and control staff plan quotas.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('showrooms.billing.index', $showroom) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold shadow-2xs transition">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Plan Quotas</span>
                    </a>

                    @if($canAddStaff)
                        <button
                            @click="addModalOpen = true"
                            id="btn-add-staff"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs shadow-md shadow-orange-600/20 transition cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            <span>+ Add Staff Member</span>
                        </button>
                    @else
                        <a
                            href="{{ route('showrooms.billing.index', $showroom) }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition"
                            title="Upgrade your subscription plan to create additional staff accounts"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Quota Reached (Upgrade Plan)</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Key Telemetry & Plan Quota Cards (5 Cards) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
                <!-- 1. Plan Quota Capacity -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-slate-500">Plan Staff Limit</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black {{ $canAddStaff ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ $canAddStaff ? 'Cap OK' : 'Full' }}
                        </span>
                    </div>
                    <div class="flex items-baseline gap-1 mt-2">
                        <p class="text-2xl sm:text-3xl font-black font-display text-slate-900">{{ $totalStaff }}</p>
                        <span class="text-xs font-bold text-slate-400">/ {{ $maxStaffLimit }} allowed</span>
                    </div>
                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2.5 overflow-hidden">
                        <div class="h-1.5 rounded-full transition-all duration-500 {{ $quotaPercentage >= 100 ? 'bg-rose-500' : ($quotaPercentage > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                             style="width: {{ $quotaPercentage }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-500 mt-1.5 font-medium">
                        <span>{{ $slotsRemaining }} slots left</span>
                        <span class="font-bold {{ $canAddStaff ? 'text-slate-700' : 'text-rose-600' }}">{{ $quotaPercentage }}% Used</span>
                    </div>
                </div>

                <!-- 2. Available / On Duty -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs relative overflow-hidden group hover:border-emerald-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-emerald-700">Available (On Duty)</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-emerald-600 mt-2">{{ $availableCount }}</p>
                    <p class="text-[11px] text-emerald-700/80 font-medium mt-1">Ready for leads & visitors</p>
                </div>

                <!-- 3. Busy (With Customer / In Workshop) -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs relative overflow-hidden group hover:border-amber-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-amber-700">Busy (Attending)</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-amber-600 mt-2">{{ $busyCount }}</p>
                    <p class="text-[11px] text-amber-700/80 font-medium mt-1">In test rides or workshop</p>
                </div>

                <!-- 4. Not Attending (On Leave / Absent) -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs relative overflow-hidden group hover:border-rose-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-rose-700">Not Attending</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-rose-600 mt-2">{{ $notAttendingCount }}</p>
                    <p class="text-[11px] text-rose-700/80 font-medium mt-1">On leave or absent today</p>
                </div>

                <!-- 5. Off Duty / Shift Ended -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs relative overflow-hidden group hover:border-slate-300 transition col-span-2 sm:col-span-1">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-slate-500">Off Duty</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black font-display text-slate-700 mt-2">{{ $offDutyCount }}</p>
                    <p class="text-[11px] text-slate-500 font-medium mt-1">Shift completed / off hours</p>
                </div>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                <form method="GET" action="{{ route('showrooms.manage.staff.index', $showroom) }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div class="flex flex-1 flex-wrap items-center gap-2.5">
                        <!-- Search input -->
                        <div class="relative min-w-[220px] flex-1">
                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Search by staff name, email, phone, or title..."
                                class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 bg-white"
                            >
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Status Filter -->
                        <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 cursor-pointer">
                            <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Duty Statuses</option>
                            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>🟢 Available (On Duty)</option>
                            <option value="busy" {{ request('status') === 'busy' ? 'selected' : '' }}>🟡 Busy (With Customer)</option>
                            <option value="not_attending" {{ request('status') === 'not_attending' ? 'selected' : '' }}>🔴 Not Attending (On Leave)</option>
                            <option value="off_duty" {{ request('status') === 'off_duty' ? 'selected' : '' }}>⚪ Off Duty</option>
                        </select>

                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition">
                            Apply Filter
                        </button>

                        @if(request()->anyFilled(['q', 'status']))
                            <a href="{{ route('showrooms.manage.staff.index', $showroom) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Staff Directory Cards & Table -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold font-display text-slate-900">Personnel Roster & Duty Desk</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Real-time attendance, active customer workload, and quick duty toggle.</p>
                    </div>
                    <span class="text-xs font-bold text-slate-500">
                        Showing {{ $members->count() }} of {{ $totalStaff }} members
                    </span>
                </div>

                @if($members->isEmpty())
                    <div class="text-center py-12 px-4">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">No staff members found</h3>
                        <p class="text-xs text-slate-500 mt-1">Try resetting your filters or add your first staff member.</p>
                        @if($canAddStaff)
                            <button @click="addModalOpen = true" class="mt-4 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs shadow-xs transition">
                                + Add First Staff Member
                            </button>
                        @endif
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($members as $member)
                            @php
                                $user = $member->user;
                                $status = $member->availability_status ?? 'available';
                                $isOwner = $member->isOwner();

                                $statusConfig = [
                                    'available' => [
                                        'label' => 'Available (On Duty)',
                                        'badge' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'dot' => 'bg-emerald-500 animate-pulse',
                                        'desc' => 'Ready to attend customer enquiries & test rides',
                                    ],
                                    'busy' => [
                                        'label' => 'Busy with Customer',
                                        'badge' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'dot' => 'bg-amber-500',
                                        'desc' => 'Currently engaged with customer or service bay',
                                    ],
                                    'not_attending' => [
                                        'label' => 'Not Attending (On Leave)',
                                        'badge' => 'bg-rose-50 text-rose-800 border-rose-200',
                                        'dot' => 'bg-rose-500',
                                        'desc' => 'Unavailable / Absent / Not attending today',
                                    ],
                                    'off_duty' => [
                                        'label' => 'Off Duty',
                                        'badge' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'dot' => 'bg-slate-400',
                                        'desc' => 'Shift concluded or scheduled off-hours',
                                    ],
                                ];

                                $currStatus = $statusConfig[$status] ?? $statusConfig['available'];
                            @endphp

                            <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <!-- Left: Avatar + Identity + Role & Dept -->
                                <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                                    <div class="relative flex-shrink-0">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 text-white font-black text-base flex items-center justify-center shadow-xs">
                                            {{ strtoupper(substr($user?->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <!-- Realtime status badge dot overlay -->
                                        <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white {{ $currStatus['dot'] }}" title="{{ $currStatus['label'] }}"></span>
                                    </div>

                                    <div class="min-w-0 space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-sm font-bold text-slate-900 truncate">{{ $user?->name }}</h3>
                                            @if($isOwner)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
                                                    Showroom Owner
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $roles[$member->role] ?? str_replace('_', ' ', $member->role) }}
                                                </span>
                                            @endif

                                            @if($member->designation)
                                                <span class="text-xs font-semibold text-slate-600 hidden sm:inline">• {{ $member->designation }}</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-3 text-xs text-slate-500 flex-wrap">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                <span>{{ $user?->email }}</span>
                                            </span>

                                            @if($user?->phone)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <span>{{ $user->phone }}</span>
                                                </span>
                                            @endif

                                            @if($member->shift)
                                                <span class="flex items-center gap-1 text-slate-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>{{ $member->shift }}</span>
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Notes / Current Status Description -->
                                        @if($member->notes)
                                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50/80 border border-amber-200/80 text-[11px] text-amber-800 mt-1">
                                                <span class="font-bold">Note:</span>
                                                <span>{{ $member->notes }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Middle: Workload Metrics (Assigned Enquiries & Services) -->
                                <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
                                    <div class="p-2 sm:px-3 sm:py-2 rounded-xl bg-slate-50 border border-slate-200/80 text-left min-w-[100px]">
                                        <span class="text-[10px] font-bold uppercase text-slate-400 block tracking-wider">Active Leads</span>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <span class="font-black text-slate-900 text-sm">{{ $member->active_enquiries_count }}</span>
                                            <span class="text-[10px] text-slate-500">assigned</span>
                                        </div>
                                    </div>

                                    <div class="p-2 sm:px-3 sm:py-2 rounded-xl bg-slate-50 border border-slate-200/80 text-left min-w-[100px]">
                                        <span class="text-[10px] font-bold uppercase text-slate-400 block tracking-wider">Workshop Jobs</span>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <span class="font-black text-slate-900 text-sm">{{ $member->active_services_count }}</span>
                                            <span class="text-[10px] text-slate-500">bookings</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Live Attendance Badge & Actions -->
                                <div class="flex items-center justify-between lg:justify-end gap-3 flex-shrink-0 pt-2 lg:pt-0 border-t border-slate-100 lg:border-t-0">
                                    <!-- Current Duty Badge with Quick Change Trigger -->
                                    <button
                                        type="button"
                                        @click="openStatus({
                                            id: {{ $member->id }},
                                            name: '{{ addslashes($user?->name) }}',
                                            availability_status: '{{ $status }}',
                                            notes: '{{ addslashes($member->notes ?? '') }}'
                                        })"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold border transition shadow-2xs hover:scale-102 cursor-pointer {{ $currStatus['badge'] }}"
                                        title="Click to toggle status: Available, Busy, Not Attending, Off Duty"
                                    >
                                        <span class="w-2 h-2 rounded-full {{ $currStatus['dot'] }}"></span>
                                        <span>{{ $currStatus['label'] }}</span>
                                        <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-1.5">
                                        <button
                                            type="button"
                                            @click="openEdit({
                                                id: {{ $member->id }},
                                                name: '{{ addslashes($user?->name) }}',
                                                email: '{{ addslashes($user?->email) }}',
                                                phone: '{{ addslashes($user?->phone ?? '') }}',
                                                role: '{{ $member->role }}',
                                                department: '{{ addslashes($member->department ?? '') }}',
                                                designation: '{{ addslashes($member->designation ?? '') }}',
                                                shift: '{{ addslashes($member->shift ?? '') }}',
                                                availability_status: '{{ $status }}',
                                                notes: '{{ addslashes($member->notes ?? '') }}',
                                                status: '{{ $member->status }}',
                                                is_owner: {{ $isOwner ? 'true' : 'false' }}
                                            })"
                                            class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition border border-transparent hover:border-slate-200"
                                            title="Edit Staff Details"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>

                                        @if(!$isOwner)
                                            <form
                                                action="{{ route('showrooms.manage.staff.destroy', [$showroom, $member]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to remove {{ addslashes($user?->name) }} from this showroom? Any assigned leads or bookings will be unassigned.');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="p-2 rounded-xl text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition border border-transparent hover:border-rose-200"
                                                    title="Remove Staff Member"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════
             MODAL 1: ADD NEW STAFF MEMBER (With Plan Quota Check)
             ══════════════════════════════════════════════════════ --}}
        <div
            x-show="addModalOpen"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
        >
            <div
                class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 space-y-5"
                @click.away="addModalOpen = false"
            >
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-bold font-display text-slate-900">Add Dealership Staff Member</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Assign authorized role and configure daily duty attendance.</p>
                    </div>
                    <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 transition p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Plan quota warning inside modal -->
                <div class="p-3 rounded-xl bg-orange-50 border border-orange-200/80 flex items-center justify-between text-xs text-orange-900">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Subscription Quota: <strong>{{ $totalStaff }} of {{ $maxStaffLimit }}</strong> used ({{ $slotsRemaining }} remaining).</span>
                    </div>
                </div>

                <form action="{{ route('showrooms.manage.staff.store', $showroom) }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required placeholder="e.g. Vikram Verma" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" required placeholder="vikram@dealership.com" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Contact Phone</label>
                            <input type="tel" name="phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Temporary Password</label>
                            <input type="password" name="password" placeholder="Leave blank to auto-generate" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        </div>

                        <!-- Hidden defaults: Role = staff, Department = Workshop -->
                        <input type="hidden" name="role" value="staff">
                        <input type="hidden" name="department" value="Workshop">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Designation Title</label>
                            <input type="text" name="designation" placeholder="e.g. Senior Technician / Service Advisor" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Shift Hours</label>
                            <input type="text" name="shift" value="General (9:30 AM - 6:30 PM)" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Initial Duty & Attendance Status <span class="text-rose-500">*</span></label>
                        <select name="availability_status" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 cursor-pointer">
                            <option value="available" selected>🟢 Available (On Duty - Ready for customers)</option>
                            <option value="busy">🟡 Busy (Attending Customer / In Workshop)</option>
                            <option value="not_attending">🔴 Not Attending (On Leave / Absent)</option>
                            <option value="off_duty">⚪ Off Duty (Not on Shift)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Note / Reason</label>
                        <input type="text" name="notes" placeholder="Optional notes (e.g. Assigned to morning test-ride bay)" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs shadow-md shadow-orange-600/20 transition">
                            Save Staff Member
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             MODAL 2: EDIT STAFF MEMBER DETAILS
             ══════════════════════════════════════════════════════ --}}
        <div
            x-show="editModalOpen"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
        >
            <div
                class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 space-y-5"
                @click.away="editModalOpen = false"
            >
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-bold font-display text-slate-900">Edit Staff Details</h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="currentStaff.name"></p>
                    </div>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 transition p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="`{{ url('/showrooms/' . $showroom->slug . '/manage/staff') }}/${currentStaff.id}`" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" x-model="currentStaff.name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Contact Phone</label>
                            <input type="tel" name="phone" x-model="currentStaff.phone" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        </div>

                        <!-- Hidden defaults: Role = staff/owner, Department = Workshop -->
                        <input type="hidden" name="role" :value="currentStaff.is_owner ? 'owner' : 'staff'">
                        <input type="hidden" name="department" value="Workshop">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Designation Title</label>
                            <input type="text" name="designation" x-model="currentStaff.designation" placeholder="e.g. Senior Technician / Service Advisor" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Shift Hours</label>
                            <input type="text" name="shift" x-model="currentStaff.shift" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Duty & Attendance Status <span class="text-rose-500">*</span></label>
                            <select name="availability_status" x-model="currentStaff.availability_status" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 cursor-pointer">
                                <option value="available">🟢 Available (On Duty)</option>
                                <option value="busy">🟡 Busy with Customer</option>
                                <option value="not_attending">🔴 Not Attending (On Leave / Absent)</option>
                                <option value="off_duty">⚪ Off Duty</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Account Access Status <span class="text-rose-500">*</span></label>
                            <select name="status" x-model="currentStaff.status" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 cursor-pointer">
                                <option value="active">Active Access</option>
                                <option value="suspended">Suspended Access</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Note / Current Assignment</label>
                        <input type="text" name="notes" x-model="currentStaff.notes" placeholder="e.g. Attending VIP customer delivery" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             MODAL 3: QUICK DUTY & ATTENDANCE STATUS TOGGLE
             ══════════════════════════════════════════════════════ --}}
        <div
            x-show="statusModalOpen"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
        >
            <div
                class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4"
                @click.away="statusModalOpen = false"
            >
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold font-display text-slate-900">Update Duty Attendance</h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="currentStaff.name"></p>
                    </div>
                    <button @click="statusModalOpen = false" class="text-slate-400 hover:text-slate-600 transition p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="`{{ url('/showrooms/' . $showroom->slug . '/manage/staff') }}/${currentStaff.id}/status`" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-2">
                        <!-- Option 1: Available -->
                        <label class="flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer hover:bg-slate-50"
                               :class="currentStaff.availability_status === 'available' ? 'border-emerald-500 bg-emerald-50/50 ring-1 ring-emerald-500' : 'border-slate-200'">
                            <input type="radio" name="availability_status" value="available" x-model="currentStaff.availability_status" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">🟢 Available (On Duty)</span>
                                <span class="text-[11px] text-slate-500 block">Ready to attend store walk-ins, test rides & leads.</span>
                            </div>
                        </label>

                        <!-- Option 2: Busy -->
                        <label class="flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer hover:bg-slate-50"
                               :class="currentStaff.availability_status === 'busy' ? 'border-amber-500 bg-amber-50/50 ring-1 ring-amber-500' : 'border-slate-200'">
                            <input type="radio" name="availability_status" value="busy" x-model="currentStaff.availability_status" class="mt-1 text-amber-600 focus:ring-amber-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">🟡 Busy with Customer / In Workshop</span>
                                <span class="text-[11px] text-slate-500 block">Currently engaged in test ride, vehicle handover, or bay repair.</span>
                            </div>
                        </label>

                        <!-- Option 3: Not Attending / Absent -->
                        <label class="flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer hover:bg-slate-50"
                               :class="currentStaff.availability_status === 'not_attending' ? 'border-rose-500 bg-rose-50/50 ring-1 ring-rose-500' : 'border-slate-200'">
                            <input type="radio" name="availability_status" value="not_attending" x-model="currentStaff.availability_status" class="mt-1 text-rose-600 focus:ring-rose-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">🔴 Not Attending (On Leave / Absent)</span>
                                <span class="text-[11px] text-slate-500 block">Sick leave, planned holiday, or not attending today.</span>
                            </div>
                        </label>

                        <!-- Option 4: Off Duty -->
                        <label class="flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer hover:bg-slate-50"
                               :class="currentStaff.availability_status === 'off_duty' ? 'border-slate-500 bg-slate-100 ring-1 ring-slate-500' : 'border-slate-200'">
                            <input type="radio" name="availability_status" value="off_duty" x-model="currentStaff.availability_status" class="mt-1 text-slate-600 focus:ring-slate-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">⚪ Off Duty</span>
                                <span class="text-[11px] text-slate-500 block">Completed shift or weekend off.</span>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Reason / Note (Optional)</label>
                        <input type="text" name="notes" x-model="currentStaff.notes" placeholder="e.g. Test ride with client until 4 PM" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="statusModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs shadow-md shadow-orange-600/20 transition">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    @include('showrooms.partials.manage-nav-close')
</x-app-layout>
