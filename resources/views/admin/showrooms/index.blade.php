<x-app-layout title="Showrooms Management - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="bg-slate-50 min-h-screen py-6 sm:py-8 px-4 sm:px-6 lg:px-8 xl:px-10 w-full"
         x-data="{
             selected: [],
             selectAll: false,
             detailShowroom: null,
             showDetailModal: false,
             toggleAll(allIds) {
                 if (this.selectAll) {
                     this.selected = [...allIds];
                 } else {
                     this.selected = [];
                 }
             },
             openDetails(showroom) {
                 this.detailShowroom = showroom;
                 this.showDetailModal = true;
             }
         }">
        <div class="space-y-6 sm:space-y-8 w-full">

            <!-- Header & Actions -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Showroom Tenants</h1>
                    <p class="text-sm text-slate-500 mt-1">Manage all independent dealership accounts, domains, statuses, and staff allocations.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.showrooms.export', request()->query()) }}"
                       class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold transition flex items-center gap-2 shadow-xs cursor-pointer"
                       title="Export all filtered showrooms to CSV">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export All (CSV)</span>
                    </a>

                    <a href="{{ route('admin.showrooms.create') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs sm:text-sm shadow-xs transition flex items-center gap-2 cursor-pointer" id="btn-create-showroom-top">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Provision New Showroom</span>
                    </a>
                </div>
            </div>

            <!-- Metric Stat Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Total Showrooms</span>
                    <p class="text-3xl font-extrabold font-display text-slate-900 mt-1">{{ $stats['total'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-emerald-600 tracking-wider">Active</span>
                    <p class="text-3xl font-extrabold font-display text-emerald-600 mt-1">{{ $stats['active'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-amber-600 tracking-wider">Trial Tier</span>
                    <p class="text-3xl font-extrabold font-display text-amber-600 mt-1">{{ $stats['trial'] }}</p>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                    <span class="text-xs uppercase font-bold text-rose-600 tracking-wider">Suspended</span>
                    <p class="text-3xl font-extrabold font-display text-rose-600 mt-1">{{ $stats['suspended'] }}</p>
                </div>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('admin.showrooms.index') }}" class="flex flex-col md:flex-row items-center gap-4">
                    <div class="flex-grow w-full">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by showroom name, slug, email, or city..."
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <div class="w-full md:w-56 shrink-0">
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <button type="submit" class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm shadow-xs transition cursor-pointer">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.showrooms.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-sm transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Floating Bulk Action Bar -->
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
                    <span class="text-xs sm:text-sm font-semibold">Showroom(s) selected</span>
                </div>

                <div class="flex items-center gap-2.5">
                    <form method="GET" action="{{ route('admin.showrooms.export') }}" target="_blank">
                        <input type="hidden" name="ids" :value="selected.join(',')">
                        <button type="submit"
                                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Export Selected</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.showrooms.bulk-destroy') }}" onsubmit="return confirm('Are you sure you want to delete all selected showrooms? All associated tenant records will be affected.');">
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

                    <button type="button" @click="selected = []; selectAll = false"
                            class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition cursor-pointer">
                        Clear
                    </button>
                </div>
            </div>

            <!-- Showrooms Data Table -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th scope="col" class="py-4 px-4 sm:px-6 w-12 text-center">
                                    <input type="checkbox"
                                           x-model="selectAll"
                                           @change="toggleAll({{ json_encode($showrooms->pluck('id')->all()) }})"
                                           class="w-4 h-4 rounded text-rose-600 border-slate-300 focus:ring-rose-500 cursor-pointer"
                                           title="Select all on this page">
                                </th>
                                <th class="px-4 py-4">Showroom</th>
                                <th class="px-4 py-4">Owner / Contact</th>
                                <th class="px-4 py-4">Location</th>
                                <th class="px-4 py-4">Staff Count</th>
                                <th class="px-4 py-4">Status</th>
                                <th class="px-4 sm:px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($showrooms as $showroom)
                                <tr class="hover:bg-slate-50/80 transition" :class="selected.includes({{ $showroom->id }}) ? 'bg-rose-50/40' : ''">
                                    <td class="py-4 px-4 sm:px-6 text-center">
                                        <input type="checkbox"
                                               value="{{ $showroom->id }}"
                                               x-model="selected"
                                               class="w-4 h-4 rounded text-rose-600 border-slate-300 focus:ring-rose-500 cursor-pointer">
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white shrink-0 shadow-xs font-display"
                                                 style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                                                {{ strtoupper(substr($showroom->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.showrooms.show', $showroom) }}" class="font-bold text-slate-900 hover:text-rose-600 transition block truncate">
                                                    {{ $showroom->name }}
                                                </a>
                                                <span class="text-xs font-mono text-slate-400 block truncate">slug: {{ $showroom->slug }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-xs">
                                        <div class="text-slate-900 font-bold">{{ $showroom->owner?->name ?? 'No Owner Assigned' }}</div>
                                        <div class="text-slate-500">{{ $showroom->email }}</div>
                                    </td>

                                    <td class="px-4 py-4 text-xs">
                                        <div class="text-slate-700 font-semibold">{{ $showroom->city }}</div>
                                        <div class="text-slate-400">{{ $showroom->state ?? 'IN' }}</div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $showroom->memberships_count }} Members
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        @if($showroom->status === 'active')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Active
                                            </span>
                                        @elseif($showroom->status === 'trial')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Trial
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                Suspended
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 sm:px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            {{-- Direct Link to Showroom Manage Back-Office --}}
                                            <a href="{{ route('showrooms.manage', $showroom) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-800 border border-orange-200 text-xs font-bold transition shadow-2xs"
                                               title="Visit Showroom Back-Office">
                                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>Back-Office</span>
                                            </a>

                                            {{-- More Details Modal Trigger --}}
                                            <button type="button"
                                                    @click="openDetails({{ json_encode([
                                                        'id' => $showroom->id,
                                                        'name' => $showroom->name,
                                                        'slug' => $showroom->slug,
                                                        'email' => $showroom->email,
                                                        'phone' => $showroom->phone,
                                                        'city' => $showroom->city,
                                                        'state' => $showroom->state,
                                                        'address' => $showroom->address,
                                                        'status' => $showroom->status,
                                                        'members_count' => $showroom->memberships_count,
                                                        'owner' => $showroom->owner?->name ?? 'None',
                                                        'primary_color' => $showroom->brand_color_primary ?? '#dc2626',
                                                        'created_at' => $showroom->created_at->format('M d, Y H:i'),
                                                        'storefront_url' => route('showrooms.show', $showroom),
                                                        'manage_url' => route('showrooms.manage', $showroom),
                                                        'admin_show_url' => route('admin.showrooms.show', $showroom),
                                                        'edit_url' => route('admin.showrooms.edit', $showroom),
                                                    ]) }})"
                                                    class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition border border-slate-200 shadow-2xs cursor-pointer"
                                                    title="More details">
                                                <span>More Details</span>
                                            </button>

                                            {{-- Storefront --}}
                                            <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                                               class="p-1.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-600 hover:text-slate-900 transition shadow-2xs"
                                               title="View Public Storefront">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>

                                            {{-- Edit --}}
                                            <a href="{{ route('admin.showrooms.edit', $showroom) }}"
                                               class="p-1.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-600 hover:text-slate-900 transition shadow-2xs"
                                               title="Edit Showroom Settings">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>

                                            {{-- Delete Single --}}
                                            <form method="POST" action="{{ route('admin.showrooms.destroy', $showroom) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete showroom \'{{ $showroom->name }}\'?');"
                                                  class="inline-block m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                        title="Delete Showroom">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                        No showrooms match your query.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($showrooms->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-white">
                        {{ $showrooms->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- ═══ SHOWROOM MORE DETAILS MODAL ═══ --}}
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

                <button @click="showDetailModal = false" class="absolute top-5 right-5 p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-black text-white text-xl font-display shadow-md shrink-0"
                         :style="'background-color: ' + (detailShowroom ? detailShowroom.primary_color : '#dc2626')">
                        <span x-text="detailShowroom ? detailShowroom.name.substring(0,1).toUpperCase() : ''"></span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 font-display" x-text="detailShowroom ? detailShowroom.name : ''"></h3>
                        <p class="text-xs text-slate-500 font-mono" x-text="detailShowroom ? 'slug: ' + detailShowroom.slug : ''"></p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                  :class="detailShowroom && detailShowroom.status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                                  x-text="detailShowroom ? detailShowroom.status : ''"></span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200"
                                  x-text="detailShowroom ? detailShowroom.members_count + ' Members' : ''"></span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-slate-50 rounded-2xl p-4 border border-slate-100">
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Owner</span>
                        <span class="font-semibold text-slate-800" x-text="detailShowroom ? detailShowroom.owner : 'None'"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Email</span>
                        <span class="font-semibold text-slate-800" x-text="detailShowroom ? detailShowroom.email : ''"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Phone</span>
                        <span class="font-semibold text-slate-800" x-text="detailShowroom && detailShowroom.phone ? detailShowroom.phone : 'Not provided'"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Location</span>
                        <span class="font-semibold text-slate-800" x-text="detailShowroom ? detailShowroom.city + ', ' + detailShowroom.state : ''"></span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Address</span>
                        <span class="font-semibold text-slate-800" x-text="detailShowroom && detailShowroom.address ? detailShowroom.address : 'Not provided'"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Provisioned Date</span>
                        <span class="font-semibold text-slate-800" x-text="detailShowroom ? detailShowroom.created_at : ''"></span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center gap-2">
                        <a :href="detailShowroom ? detailShowroom.manage_url : '#'" class="px-3.5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition shadow-xs">
                            Access Back-Office &rarr;
                        </a>
                        <a :href="detailShowroom ? detailShowroom.admin_show_url : '#'" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            Admin Overview
                        </a>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 text-xs font-bold transition cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>

    </div>
    @include('admin.partials.nav-close')
</x-app-layout>
