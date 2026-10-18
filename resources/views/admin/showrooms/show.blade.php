<x-app-layout title="{{ $showroom->name }} - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="min-h-screen bg-slate-50 text-slate-900 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.showrooms.index') }}" class="hover:text-slate-900 transition">Showrooms</a>
                <span>/</span>
                <span class="text-rose-600 font-semibold">{{ $showroom->name }}</span>
            </div>

            <!-- Top Header & Status Controls -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-2xl text-white shadow-xs shrink-0"
                         style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                        {{ strtoupper(substr($showroom->name, 0, 1)) }}
                    </div>

                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl md:text-3xl font-black font-display text-slate-900 tracking-tight">{{ $showroom->name }}</h1>
                            @if($showroom->status === 'active')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                            @elseif($showroom->status === 'trial')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">Trial Tier</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Suspended</span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 mt-2">
                            <span>Slug: <strong class="font-mono text-slate-700">{{ $showroom->slug }}</strong></span>
                            <span>•</span>
                            <span>City: <strong class="text-slate-700">{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</strong></span>
                            <span>•</span>
                            <span>Created: <strong class="text-slate-700">{{ $showroom->created_at->format('M d, Y') }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Action Links -->
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                       class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 hover:text-slate-900 text-xs font-semibold shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Storefront</span>
                    </a>

                    <a href="{{ route('showrooms.manage', $showroom) }}" target="_blank"
                       class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 hover:text-slate-900 text-xs font-semibold shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Showroom Back-Office</span>
                    </a>

                    <a href="{{ route('admin.showrooms.edit', $showroom) }}"
                       class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Edit Showroom</span>
                    </a>
                </div>
            </div>

            <!-- Two-Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left Column: Details & Status Controls -->
                <div class="space-y-6">

                    <!-- Status Change Widget -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                        <h2 class="text-base font-bold font-display text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                            Tenant Status Control
                        </h2>

                        <form method="POST" action="{{ route('admin.showrooms.status', $showroom) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="status-select" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Change Status</label>
                                <select name="status" id="status-select" class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                    <option value="active" {{ $showroom->status === 'active' ? 'selected' : '' }}>Active (Full Service)</option>
                                    <option value="trial" {{ $showroom->status === 'trial' ? 'selected' : '' }}>Trial (Evaluation)</option>
                                    <option value="suspended" {{ $showroom->status === 'suspended' ? 'selected' : '' }}>Suspended (Access Blocked)</option>
                                    <option value="cancelled" {{ $showroom->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label for="status-reason" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Audit Reason (optional)</label>
                                <input type="text" name="reason" id="status-reason" placeholder="e.g. Subscription payment overdue"
                                       class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>

                            <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs" id="btn-update-status">
                                Update Account Status
                            </button>
                        </form>
                    </div>

                    <!-- Contact & Identity Details -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                        <h2 class="text-base font-bold font-display text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-sky-600"></span>
                            Profile & Identity
                        </h2>

                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between py-1.5 border-b border-slate-100">
                                <span class="text-slate-500">Business Email:</span>
                                <span class="font-medium text-slate-900">{{ $showroom->email }}</span>
                            </div>

                            <div class="flex justify-between py-1.5 border-b border-slate-100">
                                <span class="text-slate-500">Phone:</span>
                                <span class="font-medium text-slate-900">{{ $showroom->phone ?? 'Not provided' }}</span>
                            </div>

                            <div class="flex justify-between py-1.5 border-b border-slate-100">
                                <span class="text-slate-500">Address:</span>
                                <span class="font-medium text-slate-900 text-right">{{ $showroom->address_line1 ?? 'Not specified' }}</span>
                            </div>

                            <div class="flex justify-between py-1.5 border-b border-slate-100">
                                <span class="text-slate-500">Custom Domain:</span>
                                <span class="font-mono text-slate-700">{{ $showroom->custom_domain ?? 'None (Subpath only)' }}</span>
                            </div>

                            <div class="flex justify-between py-1.5 border-b border-slate-100">
                                <span class="text-slate-500">Storefront Enabled:</span>
                                <span class="font-bold {{ $showroom->public_website_enabled ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $showroom->public_website_enabled ? 'Yes' : 'Disabled' }}
                                </span>
                            </div>

                            <div class="flex justify-between py-1.5">
                                <span class="text-slate-500">Read-Only Mode:</span>
                                <span class="font-bold {{ $showroom->read_only_mode ? 'text-amber-700' : 'text-slate-500' }}">
                                    {{ $showroom->read_only_mode ? 'Locked' : 'Off' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Brand Styling Preview -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                        <h2 class="text-base font-bold font-display text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                            Visual Branding Swatches
                        </h2>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                                <div class="h-10 rounded-lg shadow-inner flex items-center justify-center font-mono font-bold text-white text-[11px]"
                                     style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                                    {{ $showroom->brand_color_primary ?? '#dc2626' }}
                                </div>
                                <span class="block text-slate-600 text-[10px] uppercase font-bold text-center">Primary Brand Color</span>
                            </div>

                            <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                                <div class="h-10 rounded-lg shadow-inner flex items-center justify-center font-mono font-bold text-white text-[11px]"
                                     style="background-color: {{ $showroom->brand_color_accent ?? '#0f172a' }}">
                                    {{ $showroom->brand_color_accent ?? '#0f172a' }}
                                </div>
                                <span class="block text-slate-600 text-[10px] uppercase font-bold text-center">Accent Color</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right 2 Columns: Staff Allocation & Audit Trail -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Staff & Memberships Section -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold font-display text-slate-900">Staff & Membership Roster</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Users permitted to manage bikes, appointments, and enquiries in this tenant.</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $showroom->memberships->count() }} Authorized
                            </span>
                        </div>

                        <!-- Memberships Table -->
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-left text-xs text-slate-700">
                                <thead class="bg-slate-50 uppercase font-bold text-slate-500 border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3">User</th>
                                        <th class="px-4 py-3">Showroom Role</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @forelse($showroom->memberships as $membership)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-slate-900">{{ $membership->user?->name ?? 'Unknown User' }}</div>
                                                <div class="text-[11px] text-slate-500">{{ $membership->user?->email }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase
                                                    {{ $membership->role === 'owner' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                                    {{ str_replace('_', ' ', $membership->role) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-emerald-700 font-semibold">{{ $membership->status }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <form method="POST" action="{{ route('admin.showrooms.members.destroy', [$showroom, $membership]) }}"
                                                      onsubmit="return confirm('Remove user from showroom staff?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold">
                                                        Remove
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                                                No staff memberships assigned.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Add Member Form -->
                        @if($availableUsers->isNotEmpty())
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 space-y-3">
                                <h3 class="text-xs uppercase font-bold text-slate-700 tracking-wider">Assign Additional Staff Member</h3>
                                <form method="POST" action="{{ route('admin.showrooms.members.store', $showroom) }}" class="flex flex-col sm:flex-row items-center gap-3">
                                    @csrf
                                    <div class="flex-grow w-full">
                                        <select name="user_id" required class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                            <option value="">Select registered user...</option>
                                            @foreach($availableUsers as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-full sm:w-44">
                                        <select name="role" required class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                            <option value="owner">Owner</option>
                                            <option value="sales_manager">Sales Manager</option>
                                            <option value="service_manager">Service Manager</option>
                                            <option value="staff" selected>Showroom Staff</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition shrink-0" id="btn-add-staff">
                                        Assign Role
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Showroom Audit Trail -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold font-display text-slate-900">Showroom Audit Trail</h2>
                            <span class="text-xs text-slate-500">Latest 15 events</span>
                        </div>

                        <div class="space-y-3">
                            @forelse($showroom->auditLogs as $log)
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                    <div>
                                        <span class="font-bold text-slate-900">{{ $log->action }}</span>
                                        <span class="text-slate-500">by {{ $log->user?->name ?? 'System' }}</span>
                                        @if($log->new_values)
                                            <span class="text-slate-500 font-mono text-[11px] block mt-0.5">
                                                {{ json_encode($log->new_values) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-400 shrink-0">
                                        {{ $log->created_at->format('M d, Y H:i') }}
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-xs text-slate-400">
                                    No audit entries recorded for this showroom yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
