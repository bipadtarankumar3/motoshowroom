<x-app-layout title="Platform Settings - Super Admin | MotoShowroom SaaS">
    @include('admin.partials.nav')

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 xl:px-10">
        <div class="w-full space-y-8">

            <!-- Breadcrumb & Header -->
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition">Admin</a>
                <span>/</span>
                <span class="text-rose-600 font-semibold">Platform Settings</span>
            </div>

            <div class="border-b border-slate-200 pb-5">
                <h1 class="text-3xl font-black font-display text-slate-900 tracking-tight">Platform Configuration</h1>
                <p class="text-sm text-slate-500 mt-1">Configure global platform metadata, trial tiers, security policies, and feature availability.</p>
            </div>

            <!-- Settings Form -->
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
                @csrf

                <!-- Section 1: General Brand & Currency -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        Brand & Regional Currency
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label for="platform_name" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Platform Name *</label>
                            <input type="text" name="platform_name" id="platform_name" value="{{ old('platform_name', $settings['platform_name']) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 @error('platform_name') border-rose-500 @enderror">
                            @error('platform_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="support_email" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Central Support Email *</label>
                            <input type="email" name="support_email" id="support_email" value="{{ old('support_email', $settings['support_email']) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 @error('support_email') border-rose-500 @enderror">
                            @error('support_email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="platform_currency" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Currency Code (ISO) *</label>
                            <input type="text" name="platform_currency" id="platform_currency" value="{{ old('platform_currency', $settings['platform_currency']) }}" required
                                   placeholder="INR"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label for="currency_symbol" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Currency Symbol *</label>
                            <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol']) }}" required
                                   placeholder="₹"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Subscription & Trial Defaults -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Subscription & Trial Defaults
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="default_trial_days" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Default Trial Duration (Days) *</label>
                            <input type="number" name="default_trial_days" id="default_trial_days" min="0" max="365"
                                   value="{{ old('default_trial_days', $settings['default_trial_days']) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">Require Card for Trial</span>
                                <span class="text-xs text-slate-500">Demand payment authorization upfront before trial starts.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="trial_requires_card" value="1" {{ old('trial_requires_card', $settings['trial_requires_card']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Razorpay Payment Gateway & SaaS Subscriptions Billing -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6"
                     x-data="{
                         showSecret: false,
                         showWebhook: false,
                         copiedWebhook: false,
                         copyWebhookUrl() {
                             navigator.clipboard.writeText('{{ url('/api/v1/webhooks/razorpay') }}');
                             this.copiedWebhook = true;
                             setTimeout(() => this.copiedWebhook = false, 2000);
                         }
                     }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Super Admin Razorpay Gateway (Platform Subscriptions)
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Configure the master Razorpay Merchant credentials for charging SaaS subscription plans and recurring renewals to dealerships.
                            </p>
                        </div>

                        <div>
                            @if(!empty($settings['razorpay_key_id']) && !str_contains($settings['razorpay_key_id'], 'placeholder'))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Master Gateway Configured</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span>Using Sandbox Defaults</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Enable Toggle --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div>
                            <span class="font-bold text-slate-900 text-sm block">Enable Razorpay for SaaS Subscriptions</span>
                            <span class="text-xs text-slate-500">Allow showrooms to pay for plan upgrades (Starter, Pro, Elite) online via Razorpay.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="razorpay_subscriptions_enabled" value="1" {{ old('razorpay_subscriptions_enabled', $settings['razorpay_subscriptions_enabled']) == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    {{-- Master Credentials Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="admin_razorpay_key_id" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">
                                Master Razorpay Key ID *
                            </label>
                            <input type="text"
                                   name="razorpay_key_id"
                                   id="admin_razorpay_key_id"
                                   value="{{ old('razorpay_key_id', $settings['razorpay_key_id']) }}"
                                   placeholder="rzp_live_... or rzp_test_..."
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            <p class="text-[11px] text-slate-400 mt-1">Super Admin account Key ID from dashboard.razorpay.com.</p>
                        </div>

                        <div>
                            <label for="admin_razorpay_key_secret" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">
                                Master Razorpay Key Secret *
                            </label>
                            <div class="relative">
                                <input :type="showSecret ? 'text' : 'password'"
                                       name="razorpay_key_secret"
                                       id="admin_razorpay_key_secret"
                                       placeholder="{{ !empty($settings['razorpay_key_secret']) ? '•••••••••••••••• (Leave blank to keep current)' : 'Enter Master Key Secret' }}"
                                       class="w-full px-4 py-2.5 pr-10 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <button type="button"
                                        @click="showSecret = !showSecret"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <svg x-show="!showSecret" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showSecret" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Master secret used to verify SaaS billing subscriptions.</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="admin_razorpay_webhook_secret" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">
                                Razorpay Webhook Secret <span class="text-slate-400 font-normal lowercase">(for subscription recurring renewals)</span>
                            </label>
                            <div class="relative">
                                <input :type="showWebhook ? 'text' : 'password'"
                                       name="razorpay_webhook_secret"
                                       id="admin_razorpay_webhook_secret"
                                       placeholder="{{ !empty($settings['razorpay_webhook_secret']) ? '•••••••••••••••• (Leave blank to keep current)' : 'Optional webhook signing secret' }}"
                                       class="w-full px-4 py-2.5 pr-10 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <button type="button"
                                        @click="showWebhook = !showWebhook"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <svg x-show="!showWebhook" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showWebhook" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Webhook URL Info Box --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="space-y-0.5">
                            <span class="font-bold text-slate-800 block">SaaS Subscription Webhook Endpoint:</span>
                            <code class="font-mono text-emerald-700 bg-white px-2 py-0.5 rounded border border-slate-200 inline-block text-[11px]">
                                {{ url('/api/v1/webhooks/razorpay') }}
                            </code>
                            <p class="text-[11px] text-slate-400">Add this endpoint in your Razorpay Dashboard with events: <code>order.paid</code>, <code>payment.captured</code>, <code>subscription.charged</code>.</p>
                        </div>
                        <button type="button"
                                @click="copyWebhookUrl()"
                                class="px-3.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-slate-300 text-slate-700 font-bold transition shadow-2xs shrink-0 cursor-pointer flex items-center gap-1.5 self-start sm:self-auto">
                            <span x-text="copiedWebhook ? '✓ Copied' : 'Copy URL'"></span>
                        </button>
                    </div>
                </div>

                <!-- Section 4: Feature Toggles & System Policy -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-sky-600"></span>
                        Feature Flags & Security Policies
                    </h2>

                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">Allow Custom Domains</span>
                                <span class="text-xs text-slate-500">Permit showrooms to attach custom DNS hostnames (e.g. apexsuperbikes.in).</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_custom_domains" value="1" {{ old('allow_custom_domains', $settings['allow_custom_domains']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">Interactive 360-Degree Viewer</span>
                                <span class="text-xs text-slate-500">Enable Three.js WebGL 360 turntable canvas on motorcycle detail pages.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_360_viewer" value="1" {{ old('allow_360_viewer', $settings['allow_360_viewer']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">AI Virtual Showroom Assistant</span>
                                <span class="text-xs text-slate-500">Activate LLM conversational bike advisor on showroom storefronts.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_ai_assistant" value="1" {{ old('allow_ai_assistant', $settings['allow_ai_assistant']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">Allow Public Self-Registration</span>
                                <span class="text-xs text-slate-500">Allow customers to register accounts freely on the platform.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_registration" value="1" {{ old('allow_registration', $settings['allow_registration']) == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <button type="submit" class="px-7 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xs transition flex items-center gap-2" id="btn-save-settings">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Save Platform Configuration</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
    @include('admin.partials.nav-close')
</x-app-layout>
