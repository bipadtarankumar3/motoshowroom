<x-app-layout title="Platform Settings - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-8">

            <!-- Breadcrumb & Header -->
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-200">Admin</a>
                <span>/</span>
                <span class="text-rose-400 font-semibold">Platform Settings</span>
            </div>

            <div class="border-b border-slate-800 pb-5">
                <h1 class="text-3xl font-black font-display text-white tracking-tight">Platform Configuration</h1>
                <p class="text-sm text-slate-400 mt-1">Configure global platform metadata, trial tiers, security policies, and feature availability.</p>
            </div>

            <!-- Settings Form -->
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
                @csrf

                <!-- Section 1: General Brand & Currency -->
                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 shadow-xl space-y-6">
                    <h2 class="text-lg font-bold font-display text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Brand & Regional Currency
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label for="platform_name" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Platform Name *</label>
                            <input type="text" name="platform_name" id="platform_name" value="{{ old('platform_name', $settings['platform_name']) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500 @error('platform_name') border-rose-500 @enderror">
                            @error('platform_name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="support_email" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Central Support Email *</label>
                            <input type="email" name="support_email" id="support_email" value="{{ old('support_email', $settings['support_email']) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500 @error('support_email') border-rose-500 @enderror">
                            @error('support_email') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="platform_currency" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Currency Code (ISO) *</label>
                            <input type="text" name="platform_currency" id="platform_currency" value="{{ old('platform_currency', $settings['platform_currency']) }}" required
                                   placeholder="INR"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500">
                        </div>

                        <div>
                            <label for="currency_symbol" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Currency Symbol *</label>
                            <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol']) }}" required
                                   placeholder="₹"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Subscription & Trial Defaults -->
                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 shadow-xl space-y-6">
                    <h2 class="text-lg font-bold font-display text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Subscription & Trial Defaults
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="default_trial_days" class="block text-xs uppercase font-bold text-slate-300 tracking-wider mb-2">Default Trial Duration (Days) *</label>
                            <input type="number" name="default_trial_days" id="default_trial_days" min="0" max="365"
                                   value="{{ old('default_trial_days', $settings['default_trial_days']) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-rose-500">
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                            <div>
                                <span class="font-bold text-white text-sm block">Require Card for Trial</span>
                                <span class="text-xs text-slate-400">Demand payment authorization upfront before trial starts.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="trial_requires_card" value="1" {{ old('trial_requires_card', $settings['trial_requires_card']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Feature Toggles & System Policy -->
                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 shadow-xl space-y-6">
                    <h2 class="text-lg font-bold font-display text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        Feature Flags & Security Policies
                    </h2>

                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                            <div>
                                <span class="font-bold text-white text-sm block">Allow Custom Domains</span>
                                <span class="text-xs text-slate-400">Permit showrooms to attach custom DNS hostnames (e.g. apexsuperbikes.in).</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_custom_domains" value="1" {{ old('allow_custom_domains', $settings['allow_custom_domains']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                            <div>
                                <span class="font-bold text-white text-sm block">Interactive 360-Degree Viewer</span>
                                <span class="text-xs text-slate-400">Enable Three.js WebGL 360 turntable canvas on motorcycle detail pages.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_360_viewer" value="1" {{ old('allow_360_viewer', $settings['allow_360_viewer']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                            <div>
                                <span class="font-bold text-white text-sm block">AI Virtual Showroom Assistant</span>
                                <span class="text-xs text-slate-400">Activate LLM conversational bike advisor on showroom storefronts.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_ai_assistant" value="1" {{ old('allow_ai_assistant', $settings['allow_ai_assistant']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                            <div>
                                <span class="font-bold text-white text-sm block">Allow Public Self-Registration</span>
                                <span class="text-xs text-slate-400">Allow customers to register accounts freely on the platform.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_registration" value="1" {{ old('allow_registration', $settings['allow_registration']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <button type="submit" class="px-7 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xl shadow-rose-600/30 transition flex items-center gap-2" id="btn-save-settings">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Save Platform Configuration</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
