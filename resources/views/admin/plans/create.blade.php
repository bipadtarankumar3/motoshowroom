<x-app-layout title="Create Subscription Plan - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="min-h-screen bg-slate-50 text-slate-900 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-8">

            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.plans.index') }}" class="hover:text-slate-900 transition">Plans</a>
                <span>/</span>
                <span class="text-rose-600 font-semibold">Create</span>
            </div>

            <div class="border-b border-slate-200 pb-5">
                <h1 class="text-3xl font-black font-display text-slate-900 tracking-tight">Create Subscription Plan</h1>
                <p class="text-sm text-slate-500 mt-1">Configure pricing, resource allocations, and feature flags for a new SaaS showroom tier.</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-8">
                @csrf

                <!-- Basic Info & Pricing -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        Plan Identity & Commercial Pricing
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Plan Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   placeholder="e.g. Professional Dealership"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 @error('name') border-rose-500 @enderror">
                            @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Slug (optional)</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                   placeholder="professional-dealership"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Plan Description</label>
                            <textarea name="description" id="description" rows="2"
                                      placeholder="Full-featured solution for established bike franchises and high-volume dealerships."
                                      class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label for="monthly_price" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Monthly Price *</label>
                            <input type="number" step="0.01" name="monthly_price" id="monthly_price" value="{{ old('monthly_price', '2999.00') }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label for="annual_price" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Annual Price *</label>
                            <input type="number" step="0.01" name="annual_price" id="annual_price" value="{{ old('annual_price', '29990.00') }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label for="currency" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Currency (ISO) *</label>
                            <input type="text" name="currency" id="currency" value="{{ old('currency', 'INR') }}" required maxlength="3"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 font-mono text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label for="trial_days" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Trial Days Duration *</label>
                            <input type="number" name="trial_days" id="trial_days" value="{{ old('trial_days', '14') }}" required min="0" max="365"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                    </div>
                </div>

                <!-- Resource Quotas -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Resource Allocations & Quotas
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="max_staff_accounts" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Max Staff Accounts *</label>
                            <input type="number" name="max_staff_accounts" id="max_staff_accounts" value="{{ old('max_staff_accounts', '10') }}" required min="1"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label for="max_bike_listings" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Max Active Bike Listings *</label>
                            <input type="number" name="max_bike_listings" id="max_bike_listings" value="{{ old('max_bike_listings', '50') }}" required min="1"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label for="max_monthly_bookings" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Max Monthly Service/Test Ride Bookings *</label>
                            <input type="number" name="max_monthly_bookings" id="max_monthly_bookings" value="{{ old('max_monthly_bookings', '250') }}" required min="1"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label for="media_storage_mb" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Cloud Storage Quota (MB) *</label>
                            <input type="number" name="media_storage_mb" id="media_storage_mb" value="{{ old('media_storage_mb', '5120') }}" required min="100"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <span class="text-[11px] text-slate-500 mt-1 block">5120 MB = 5 GB</span>
                        </div>
                    </div>
                </div>

                <!-- Feature Flags -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-sky-600"></span>
                        Entitled Feature Flags
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="has_careers_module" value="1" {{ old('has_careers_module', '1') ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500 bg-white border-slate-300 w-4 h-4">
                            <div>
                                <span class="font-bold text-slate-900 text-xs block">Careers & Job Portal</span>
                                <span class="text-[11px] text-slate-500">Post dealership vacancies & review job applications.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="has_scratch_cards" value="1" {{ old('has_scratch_cards', '1') ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500 bg-white border-slate-300 w-4 h-4">
                            <div>
                                <span class="font-bold text-slate-900 text-xs block">Promotional Scratch Cards</span>
                                <span class="text-[11px] text-slate-500">Launch gamified promotional discounts & customer rewards.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="has_360_viewer" value="1" {{ old('has_360_viewer', '1') ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500 bg-white border-slate-300 w-4 h-4">
                            <div>
                                <span class="font-bold text-slate-900 text-xs block">Interactive 360° Viewer</span>
                                <span class="text-[11px] text-slate-500">Three.js WebGL 36-frame interactive turntable canvas.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="has_ai_assistant" value="1" {{ old('has_ai_assistant') ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500 bg-white border-slate-300 w-4 h-4">
                            <div>
                                <span class="font-bold text-slate-900 text-xs block">AI Virtual Assistant</span>
                                <span class="text-[11px] text-slate-500">Conversational bike advisor grounded on showroom inventory.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="has_custom_domain" value="1" {{ old('has_custom_domain') ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500 bg-white border-slate-300 w-4 h-4">
                            <div>
                                <span class="font-bold text-slate-900 text-xs block">Custom Domain Mapping</span>
                                <span class="text-[11px] text-slate-500">Allow showroom to bind apexdomain or custom subdomains.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="has_advanced_analytics" value="1" {{ old('has_advanced_analytics') ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500 bg-white border-slate-300 w-4 h-4">
                            <div>
                                <span class="font-bold text-slate-900 text-xs block">Advanced Analytics</span>
                                <span class="text-[11px] text-slate-500">Sales velocity, campaign ROI, and customer conversion reports.</span>
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center gap-6 pt-4 border-t border-slate-100">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-500 bg-white border-slate-300 w-4 h-4">
                            <span class="text-xs font-bold text-slate-800">Active for new subscriptions</span>
                        </label>

                        <div class="flex items-center gap-2">
                            <label for="sort_order" class="text-xs font-bold text-slate-700">Display Order:</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', '0') }}"
                                   class="w-20 px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-900 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                    </div>
                </div>

                <!-- Submit & Cancel -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.plans.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">
                        Cancel
                    </a>

                    <button type="submit" class="px-7 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xs transition flex items-center gap-2" id="btn-save-plan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Create Plan</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
