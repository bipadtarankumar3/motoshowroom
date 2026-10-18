<x-app-layout title="Edit Showroom - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 xl:px-10">
        <div class="w-full space-y-8">

            <!-- Breadcrumb & Header -->
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.showrooms.index') }}" class="hover:text-slate-900 transition">Showrooms</a>
                <span>/</span>
                <a href="{{ route('admin.showrooms.show', $showroom) }}" class="hover:text-slate-900 transition">{{ $showroom->name }}</a>
                <span>/</span>
                <span class="text-rose-600 font-semibold">Edit</span>
            </div>

            <div class="border-b border-slate-200 pb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black font-display text-slate-900 tracking-tight">Edit Showroom: {{ $showroom->name }}</h1>
                    <p class="text-sm text-slate-500 mt-1">Update profile information, contact channels, branding, and platform domain rules.</p>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.showrooms.update', $showroom) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Section 1: Basic Information -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        Showroom Organization Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Showroom Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $showroom->name) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 @error('name') border-rose-500 @enderror">
                            @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Business Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $showroom->email) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 @error('email') border-rose-500 @enderror">
                            @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Contact Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $showroom->phone) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label for="city" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">City *</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $showroom->city) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 @error('city') border-rose-500 @enderror">
                            @error('city') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="state" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">State / Region</label>
                            <input type="text" name="state" id="state" value="{{ old('state', $showroom->state) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="address_line1" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Address</label>
                            <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1', $showroom->address_line1) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Visual Branding -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        Showroom Brand Colors
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="brand_color_primary" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Primary Brand Color (Hex)</label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="primary_picker" value="{{ old('brand_color_primary', $showroom->brand_color_primary ?? '#dc2626') }}"
                                       oninput="document.getElementById('brand_color_primary').value = this.value"
                                       class="w-10 h-10 rounded-lg cursor-pointer bg-white border border-slate-300 p-0">
                                <input type="text" name="brand_color_primary" id="brand_color_primary" value="{{ old('brand_color_primary', $showroom->brand_color_primary ?? '#dc2626') }}"
                                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                        </div>

                        <div>
                            <label for="brand_color_accent" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Accent Brand Color (Hex)</label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="accent_picker" value="{{ old('brand_color_accent', $showroom->brand_color_accent ?? '#0f172a') }}"
                                       oninput="document.getElementById('brand_color_accent').value = this.value"
                                       class="w-10 h-10 rounded-lg cursor-pointer bg-white border border-slate-300 p-0">
                                <input type="text" name="brand_color_accent" id="brand_color_accent" value="{{ old('brand_color_accent', $showroom->brand_color_accent ?? '#0f172a') }}"
                                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Domain & Access Control -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        Domain & Feature Flags
                    </h2>

                    <div class="space-y-5">
                        <div>
                            <label for="custom_domain" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Custom Domain (e.g. apexmotohub.com)</label>
                            <input type="text" name="custom_domain" id="custom_domain" value="{{ old('custom_domain', $showroom->custom_domain) }}"
                                   placeholder="bikes.example.com"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 @error('custom_domain') border-rose-500 @enderror">
                            @error('custom_domain') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">Public Storefront Active</span>
                                <span class="text-xs text-slate-500">Allow customers to access the public showroom website via web.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="public_website_enabled" value="1" {{ old('public_website_enabled', $showroom->public_website_enabled) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">Read-Only Mode</span>
                                <span class="text-xs text-slate-500">Temporarily lock staff from creating new inventory or editing records.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="read_only_mode" value="1" {{ old('read_only_mode', $showroom->read_only_mode) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit & Cancel -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.showrooms.show', $showroom) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">
                        Cancel
                    </a>

                    <button type="submit" class="px-7 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xs transition flex items-center gap-2" id="btn-update-showroom">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
    @include('admin.partials.nav-close')
</x-app-layout>
