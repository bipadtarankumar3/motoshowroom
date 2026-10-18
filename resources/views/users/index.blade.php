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

            <!-- Top Header & Actions -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Platform Users</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                            Super Admin
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mt-1">Manage system administrators, showroom owners, and registered platform users.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.users.export', request()->query()) }}"
                       class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold transition flex items-center gap-2 shadow-xs cursor-pointer"
                       title="Export all filtered users to CSV">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export All (CSV)</span>
                    </a>

                    <a href="{{ route('profile') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold transition flex items-center gap-2 shadow-xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>My Details Page</span>
                    </a>

                    <a href="{{ route('admin.users.create') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs sm:text-sm shadow-xs transition flex items-center gap-2" id="btn-create-user-top">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Create User</span>
                    </a>
                </div>
            </div>

            <!-- Metric Stat Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Total Users</span>
                    <p class="text-3xl font-extrabold font-display text-slate-900 mt-1">{{ $stats['total'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-rose-600 tracking-wider">Administrators</span>
                    <p class="text-3xl font-extrabold font-display text-rose-600 mt-1">{{ $stats['admins'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-sky-600 tracking-wider">Regular Users</span>
                    <p class="text-3xl font-extrabold font-display text-sky-600 mt-1">{{ $stats['regular_users'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-emerald-600 tracking-wider">Active Status</span>
                    <p class="text-3xl font-extrabold font-display text-emerald-600 mt-1">{{ $stats['active'] }}</p>
                </div>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row items-center gap-4">
                    <div class="flex-grow w-full">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by name, email, phone, or city..."
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        <select name="role" class="px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                        </select>

                        <select name="status" class="px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>

                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition shadow-xs cursor-pointer">
                            Filter
                        </button>

                        @if (request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('admin.users.index') }}" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Floating / Inline Bulk Action Bar -->
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

            <!-- Users Data Table -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                            <tr>
                                <th scope="col" class="py-4 px-4 sm:px-6 w-12 text-center">
                                    <input type="checkbox"
                                           x-model="selectAll"
                                           @change="toggleAll({{ json_encode($users->pluck('id')->all()) }})"
                                           class="w-4 h-4 rounded text-rose-600 border-slate-300 focus:ring-rose-500 cursor-pointer"
                                           title="Select all on this page">
                                </th>
                                <th scope="col" class="py-4 px-4">User</th>
                                <th scope="col" class="py-4 px-4">Role</th>
                                <th scope="col" class="py-4 px-4">Status</th>
                                <th scope="col" class="py-4 px-4">Contact & City</th>
                                <th scope="col" class="py-4 px-4">Registered</th>
                                <th scope="col" class="py-4 px-4 sm:px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse ($users as $u)
                                <tr class="hover:bg-slate-50/80 transition" :class="selected.includes({{ $u->id }}) ? 'bg-rose-50/40' : ''">
                                    <td class="py-4 px-4 sm:px-6 text-center">
                                        <input type="checkbox"
                                               value="{{ $u->id }}"
                                               x-model="selected"
                                               class="w-4 h-4 rounded text-rose-600 border-slate-300 focus:ring-rose-500 cursor-pointer">
                                    </td>

                                    <td class="py-4 px-4 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center font-bold text-rose-600 text-sm font-display flex-shrink-0">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('users.show', $u) }}" class="font-bold text-slate-900 hover:text-rose-600 transition block truncate">
                                                {{ $u->name }}
                                            </a>
                                            <span class="text-xs text-slate-500 block truncate">{{ $u->email }}</span>
                                        </div>
                                    </td>

                                    <td class="py-4 px-4">
                                        @if ($u->isAdmin())
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                                Admin
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                                User
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-4">
                                        @if ($u->status === 'active')
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5 w-max">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1.5 w-max">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> {{ ucfirst($u->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-4 text-xs text-slate-600">
                                        <div>{{ $u->phone ?: 'No phone' }}</div>
                                        <div class="text-slate-400">{{ $u->city ?: 'No city' }}</div>
                                    </td>

                                    <td class="py-4 px-4 text-xs text-slate-500 whitespace-nowrap">
                                        {{ $u->created_at->format('M d, Y') }}
                                    </td>

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

                                            {{-- More Details Modal Trigger --}}
                                            <button type="button"
                                                    @click="openDetails({{ json_encode([
                                                        'id' => $u->id,
                                                        'name' => $u->name,
                                                        'email' => $u->email,
                                                        'role' => $u->role,
                                                        'status' => $u->status,
                                                        'phone' => $u->phone,
                                                        'city' => $u->city,
                                                        'address' => $u->address,
                                                        'bio' => $u->bio,
                                                        'created_at' => $u->created_at->format('M d, Y H:i'),
                                                        'showrooms' => $u->showrooms->pluck('name')->all(),
                                                        'profile_url' => route('users.show', $u),
                                                        'edit_url' => route('users.edit', $u),
                                                    ]) }})"
                                                    class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition border border-slate-200 shadow-2xs cursor-pointer"
                                                    title="More details">
                                                <span>More Details</span>
                                            </button>

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
                                    <td colspan="7" class="py-12 text-center text-slate-500 text-sm">
                                        No users found matching the given filters.
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

        {{-- ═══ USER MORE DETAILS MODAL ═══ --}}
        <div x-show="showDetailModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4"
             x-cloak>
            <div @click.away="showDetailModal = false"
                 class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 relative">

                {{-- Close Button --}}
                <button @click="showDetailModal = false" class="absolute top-5 right-5 p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                {{-- Modal Header --}}
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-rose-500 to-rose-600 flex items-center justify-center font-black text-white text-xl font-display shadow-md">
                        <span x-text="detailUser ? detailUser.name.substring(0,2).toUpperCase() : ''"></span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 font-display" x-text="detailUser ? detailUser.name : ''"></h3>
                        <p class="text-xs text-slate-500" x-text="detailUser ? detailUser.email : ''"></p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                  :class="detailUser && detailUser.role === 'admin' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-sky-50 text-sky-700 border border-sky-200'"
                                  x-text="detailUser ? detailUser.role : ''"></span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                  :class="detailUser && detailUser.status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                                  x-text="detailUser ? detailUser.status : ''"></span>
                        </div>
                    </div>
                </div>

                {{-- Detailed Fields Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-slate-50 rounded-2xl p-4 border border-slate-100">
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Phone</span>
                        <span class="font-semibold text-slate-800" x-text="detailUser && detailUser.phone ? detailUser.phone : 'Not provided'"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">City</span>
                        <span class="font-semibold text-slate-800" x-text="detailUser && detailUser.city ? detailUser.city : 'Not provided'"></span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Address</span>
                        <span class="font-semibold text-slate-800" x-text="detailUser && detailUser.address ? detailUser.address : 'Not provided'"></span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Bio / Notes</span>
                        <span class="font-semibold text-slate-800 leading-relaxed" x-text="detailUser && detailUser.bio ? detailUser.bio : 'No personal bio added.'"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Registered On</span>
                        <span class="font-semibold text-slate-800" x-text="detailUser ? detailUser.created_at : ''"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Assigned Showrooms</span>
                        <span class="font-semibold text-slate-800" x-text="detailUser && detailUser.showrooms.length ? detailUser.showrooms.join(', ') : 'None'"></span>
                    </div>
                </div>

                {{-- Modal Actions --}}
                <div class="flex items-center justify-between pt-2">
                    <a :href="detailUser ? detailUser.profile_url : '#'" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Open Full Profile Page &rarr;
                    </a>
                    <div class="flex items-center gap-2">
                        <a :href="detailUser ? detailUser.edit_url : '#'" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs">
                            Edit User
                        </a>
                        <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 text-xs font-bold transition cursor-pointer">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @include('admin.partials.nav-close')
</x-app-layout>
