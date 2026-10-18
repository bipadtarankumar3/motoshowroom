<x-app-layout :title="'Settings - ' . $showroom->name">
    @include('showrooms.partials.manage-nav')

    <div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-8">

            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-200">Showroom Back-Office</a>
                <span>/</span>
                <span class="text-rose-400 font-semibold">Settings & Staff</span>
            </div>

            <div class="border-b border-slate-800 pb-5">
                <h1 class="text-3xl font-black font-display text-white tracking-tight">Showroom Business Settings</h1>
                <p class="text-sm text-slate-400 mt-1">Manage physical address, commercial contact channels, and authorized dealership personnel.</p>
            </div>

            <!-- Contact & Address Form -->
            <form method="POST" action="{{ route('showrooms.manage.settings.update', $showroom) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 shadow-xl space-y-6">
                    <h2 class="text-lg font-bold font-display text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Commercial Contact & Address
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="email" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Business Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $showroom->email) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Main Contact Phone *</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $showroom->phone) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="address_line1" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Street Address</label>
                            <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1', $showroom->address_line1) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500">
                        </div>

                        <div>
                            <label for="city" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">City *</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $showroom->city) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500">
                        </div>

                        <div>
                            <label for="state" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">State / Province</label>
                            <input type="text" name="state" id="state" value="{{ old('state', $showroom->state) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500">
                        </div>

                        <div>
                            <label for="postal_code" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Postal Code</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $showroom->postal_code) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-800">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg transition">
                            Save Contact Details
                        </button>
                    </div>
                </div>
            </form>

            <!-- Showroom Team Members Roster -->
            <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold font-display text-white">Dealership Personnel</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Staff members authorized to manage this showroom.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-300">
                        {{ $showroom->memberships->count() }} Authorized Staff
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-800">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900 uppercase font-bold text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Member Name</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Access Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            @foreach($showroom->memberships as $membership)
                                <tr>
                                    <td class="px-4 py-3 font-bold text-white">{{ $membership->user?->name }}</td>
                                    <td class="px-4 py-3 text-slate-400">{{ $membership->user?->email }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-800 text-rose-300 border border-slate-700">
                                            {{ str_replace('_', ' ', $membership->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-emerald-400 font-semibold">{{ $membership->status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
