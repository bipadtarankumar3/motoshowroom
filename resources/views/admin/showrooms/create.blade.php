<x-app-layout title="Provision Showroom - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-8">

            <!-- Breadcrumb & Header -->
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-200">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.showrooms.index') }}" class="hover:text-slate-200">Showrooms</a>
                <span>/</span>
                <span class="text-rose-400 font-semibold">Provision Tenant</span>
            </div>

            <div class="border-b border-slate-800 pb-5">
                <h1 class="text-3xl font-black font-display text-white tracking-tight">Provision New Showroom</h1>
                <p class="text-sm text-slate-400 mt-1">Initialize a multi-tenant dealership account with dedicated branding, owner credentials, and public storefront.</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.showrooms.store') }}" class="space-y-8">
                @csrf

                <!-- Section 1: Showroom Profile -->
                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 shadow-xl space-y-6">
                    <h2 class="text-lg font-bold font-display text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Showroom Organization Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Showroom Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   placeholder="e.g. Apex Moto Hub"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 @error('name') border-rose-500 @enderror">
                            @error('name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">URL Slug (leave blank to auto-generate)</label>
                            <div class="flex items-center rounded-xl bg-slate-900 border border-slate-700 overflow-hidden focus-within:ring-2 focus-within:ring-rose-500">
                                <span class="px-3 text-xs text-slate-500 bg-slate-800/60 border-r border-slate-700 py-2.5">/showrooms/</span>
                                <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                       placeholder="apex-moto"
                                       class="w-full px-3 py-2.5 bg-transparent text-white placeholder-slate-500 text-sm focus:outline-none @error('slug') border-rose-500 @enderror">
                            </div>
                            @error('slug') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Business Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   placeholder="contact@apexmotohub.example"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 @error('email') border-rose-500 @enderror">
                            @error('email') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Contact Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   placeholder="+91 98765 43210"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                        </div>

                        <div>
                            <label for="city" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">City *</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}" required
                                   placeholder="e.g. Bangalore"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 @error('city') border-rose-500 @enderror">
                            @error('city') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="state" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">State / Region</label>
                            <input type="text" name="state" id="state" value="{{ old('state') }}"
                                   placeholder="e.g. Karnataka"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                        </div>

                        <div>
                            <label for="status" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Initial Status *</label>
                            <select name="status" id="status" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Full Access)</option>
                                <option value="trial" {{ old('status') === 'trial' ? 'selected' : '' }}>Trial (14-Day Evaluation)</option>
                                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="address_line1" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Address</label>
                            <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1') }}"
                                   placeholder="124 Ring Road, Indiranagar"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Visual Branding -->
                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 shadow-xl space-y-6">
                    <h2 class="text-lg font-bold font-display text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Showroom Brand Colors
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="brand_color_primary" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Primary Brand Color (Hex)</label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="primary_picker" value="{{ old('brand_color_primary', '#dc2626') }}"
                                       oninput="document.getElementById('brand_color_primary').value = this.value"
                                       class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border-0 p-0">
                                <input type="text" name="brand_color_primary" id="brand_color_primary" value="{{ old('brand_color_primary', '#dc2626') }}"
                                       placeholder="#dc2626"
                                       class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-mono text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                            </div>
                        </div>

                        <div>
                            <label for="brand_color_accent" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Accent / Dark Brand Color (Hex)</label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="accent_picker" value="{{ old('brand_color_accent', '#0f172a') }}"
                                       oninput="document.getElementById('brand_color_accent').value = this.value"
                                       class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border-0 p-0">
                                <input type="text" name="brand_color_accent" id="brand_color_accent" value="{{ old('brand_color_accent', '#0f172a') }}"
                                       placeholder="#0f172a"
                                       class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-mono text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Showroom Owner Assignment -->
                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 shadow-xl space-y-6">
                    <h2 class="text-lg font-bold font-display text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Showroom Owner Assignment
                    </h2>

                    <!-- Mode Toggle Radio -->
                    <div class="flex items-center gap-6 border-b border-slate-800 pb-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="owner_mode" value="existing" {{ old('owner_mode', 'existing') === 'existing' ? 'checked' : '' }}
                                   onchange="toggleOwnerMode('existing')"
                                   class="text-rose-600 focus:ring-rose-500 bg-slate-900 border-slate-700">
                            <span class="text-sm font-semibold text-slate-200">Assign Existing User</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="owner_mode" value="new" {{ old('owner_mode') === 'new' ? 'checked' : '' }}
                                   onchange="toggleOwnerMode('new')"
                                   class="text-rose-600 focus:ring-rose-500 bg-slate-900 border-slate-700">
                            <span class="text-sm font-semibold text-slate-200">Create New Owner User</span>
                        </label>
                    </div>

                    <!-- Existing User Picker -->
                    <div id="existing-owner-section" class="{{ old('owner_mode', 'existing') === 'new' ? 'hidden' : '' }} space-y-4">
                        <label for="owner_user_id" class="block text-xs uppercase font-bold text-slate-300 tracking-wider">Select Registered User</label>
                        <select name="owner_user_id" id="owner_user_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                            <option value="">Choose an existing user...</option>
                            @foreach($existingUsers as $user)
                                <option value="{{ $user->id }}" {{ old('owner_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }}) [{{ $user->role }}]
                                </option>
                            @endforeach
                        </select>
                        @error('owner_user_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- New User Creation Fields -->
                    <div id="new-owner-section" class="{{ old('owner_mode', 'existing') === 'new' ? '' : 'hidden' }} space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="new_owner_name" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Owner Full Name</label>
                                <input type="text" name="new_owner_name" id="new_owner_name" value="{{ old('new_owner_name') }}"
                                       placeholder="Rajesh Kumar"
                                       class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                                @error('new_owner_name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="new_owner_email" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Owner Email</label>
                                <input type="email" name="new_owner_email" id="new_owner_email" value="{{ old('new_owner_email') }}"
                                       placeholder="rajesh@example.com"
                                       class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                                @error('new_owner_email') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="new_owner_password" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Temporary Password (min 8 characters)</label>
                                <input type="password" name="new_owner_password" id="new_owner_password"
                                       placeholder="••••••••"
                                       class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                                @error('new_owner_password') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.showrooms.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-800 bg-slate-900 hover:bg-slate-800 text-slate-300 text-sm font-semibold transition">
                        Cancel
                    </a>

                    <button type="submit" class="px-7 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xl shadow-rose-600/30 transition flex items-center gap-2" id="btn-submit-showroom">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Provision Showroom</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function toggleOwnerMode(mode) {
            const existingSection = document.getElementById('existing-owner-section');
            const newSection = document.getElementById('new-owner-section');

            if (mode === 'new') {
                existingSection.classList.add('hidden');
                newSection.classList.remove('hidden');
            } else {
                existingSection.classList.remove('hidden');
                newSection.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
