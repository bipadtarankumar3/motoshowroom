<x-app-layout :title="'Settings - ' . $showroom->name">
    @include('showrooms.partials.manage-nav')

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 xl:px-10">
        <div class="w-full space-y-8">

            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Showroom Back-Office</a>
                <span>/</span>
                <span class="text-rose-600 font-semibold">Settings & Staff</span>
            </div>

            <div class="border-b border-slate-200 pb-5">
                <h1 class="text-3xl font-black font-display text-slate-900 tracking-tight">Showroom Business Settings</h1>
                <p class="text-sm text-slate-500 mt-1">Manage physical address, commercial contact channels, and authorized dealership personnel.</p>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 text-sm shadow-xs">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>Please fix the following issues:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs pl-6">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Contact & Address Form -->
            <form method="POST" action="{{ route('showrooms.manage.settings.update', $showroom) }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                {{-- Section: Dealership Brand & Logo Upload --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6" x-data="{
                    previewUrl: '{{ $showroom->logo_url ? asset($showroom->logo_url) : '' }}',
                    handleFileSelect(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.previewUrl = URL.createObjectURL(file);
                        }
                    }
                }">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                Dealership Logo &amp; Identity
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Upload your official brand logo for your public storefront header, invoice headers, and customer communications.</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            PNG, JPG, WEBP, SVG (Max 5MB)
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        {{-- Live Logo Preview Box --}}
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col items-center justify-center text-center space-y-3">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Live Logo Preview</span>
                            <div class="w-28 h-28 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-center p-3 overflow-hidden relative group">
                                <template x-if="previewUrl">
                                    <img :src="previewUrl" alt="{{ $showroom->name }}" class="max-h-full max-w-full object-contain">
                                </template>
                                <template x-if="!previewUrl">
                                    <div class="w-full h-full rounded-xl flex flex-col items-center justify-center font-display font-black text-2xl shadow-xs"
                                         style="background: linear-gradient(135deg, {{ $showroom->primary_color }}, {{ $showroom->accent_color }}); color: {{ $showroom->primary_text_color }};">
                                        <span>{{ strtoupper(substr($showroom->name, 0, 1)) }}</span>
                                        <span class="text-[9px] uppercase tracking-widest font-sans font-bold opacity-80 mt-1">Default</span>
                                    </div>
                                </template>
                            </div>
                            <span class="text-xs font-semibold text-slate-700 truncate max-w-full px-2">{{ $showroom->name }}</span>
                        </div>

                        {{-- Upload controls --}}
                        <div class="md:col-span-2 space-y-4">
                            <div>
                                <label class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">
                                    Upload Logo File
                                </label>
                                <input type="file" name="logo_file" id="logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                       @change="handleFileSelect($event)"
                                       class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer border border-slate-200 rounded-xl bg-slate-50/50 p-1.5 transition">
                                <p class="text-[11px] text-slate-400 mt-1.5">For optimal visual quality, use a transparent PNG or SVG logo at least 400x120px.</p>
                            </div>

                            <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3">
                                <div class="flex-1">
                                    <label for="logo_url" class="block text-[11px] font-bold text-slate-600 mb-1">
                                        Or External Logo URL <span class="text-slate-400 font-normal">(CDN or hosted asset)</span>
                                    </label>
                                    <input type="text" name="logo_url" id="logo_url" value="{{ old('logo_url', $showroom->logo_url) }}"
                                           placeholder="https://example.com/logo.png"
                                           @input="if (!$event.target.value.startsWith('blob:')) previewUrl = $event.target.value"
                                           class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-900 text-xs font-mono focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                </div>

                                @if($showroom->logo_url)
                                    <label class="inline-flex items-center gap-2 text-xs font-bold text-rose-600 hover:text-rose-700 cursor-pointer pt-4 sm:pt-6">
                                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                        <span>Remove Logo</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 1: Commercial Contact & Physical Address --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        Commercial Contact & Address
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="email" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Business Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $showroom->email) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-medium">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Main Contact Phone *</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $showroom->phone) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-medium">
                        </div>

                        <div class="md:col-span-2">
                            <label for="address_line1" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Street Address</label>
                            <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1', $showroom->address_line1) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-medium">
                        </div>

                        <div>
                            <label for="city" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">City *</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $showroom->city) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-medium">
                        </div>

                        <div>
                            <label for="state" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">State / Province</label>
                            <input type="text" name="state" id="state" value="{{ old('state', $showroom->state) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-medium">
                        </div>

                        <div>
                            <label for="postal_code" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Postal Code</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $showroom->postal_code) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-medium">
                        </div>
                    </div>
                </div>

                {{-- Section 2: Razorpay Payment Gateway & Online Booking Credentials --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6"
                     x-data="{
                         showSecret: false,
                         showWebhook: false,
                         razorpayEnabled: {{ $showroom->isRazorpayOnlinePaymentEnabled() ? 'true' : 'false' }}
                     }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Razorpay Payment Gateway &amp; Online Booking
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Connect your dealership's Razorpay Merchant account to receive token booking advances and online payments.
                            </p>
                        </div>

                        <div>
                            @if($showroom->hasCustomRazorpayCredentials())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Custom Account Connected</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span>Platform Sandbox Fallback</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Enable Toggle --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                        <input type="checkbox"
                               name="razorpay_enabled"
                               id="razorpay_enabled"
                               value="1"
                               x-model="razorpayEnabled"
                               class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <div>
                            <label for="razorpay_enabled" class="text-sm font-bold text-slate-900 cursor-pointer select-none">
                                Enable Online Razorpay Payments &amp; Token Reservations
                            </label>
                            <p class="text-xs text-slate-500 mt-0.5">
                                When enabled, customers can reserve motorcycles by paying the advance token directly via Razorpay (UPI, Credit/Debit Cards, NetBanking).
                            </p>
                        </div>
                    </div>

                    {{-- Gateway Credentials Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5" x-show="razorpayEnabled">
                        <div>
                            <label for="razorpay_key_id" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">
                                Razorpay Key ID
                            </label>
                            <input type="text"
                                   name="razorpay_key_id"
                                   id="razorpay_key_id"
                                   value="{{ old('razorpay_key_id', $showroom->getSetting('razorpay_key_id')) }}"
                                   placeholder="rzp_live_... or rzp_test_..."
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            <p class="text-[11px] text-slate-400 mt-1">Found in your Razorpay Dashboard &rarr; Account &amp; Settings &rarr; API Keys.</p>
                        </div>

                        <div>
                            <label for="razorpay_key_secret" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">
                                Razorpay Key Secret
                            </label>
                            <div class="relative">
                                <input :type="showSecret ? 'text' : 'password'"
                                       name="razorpay_key_secret"
                                       id="razorpay_key_secret"
                                       placeholder="{{ $showroom->getSetting('razorpay_key_secret') ? '•••••••••••••••• (Leave blank to keep current)' : 'Enter Razorpay Key Secret' }}"
                                       class="w-full px-4 py-2.5 pr-10 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <button type="button"
                                        @click="showSecret = !showSecret"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <svg x-show="!showSecret" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showSecret" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Generated together with your Key ID. Kept encrypted on server.</p>
                        </div>

                        <div>
                            <label for="razorpay_webhook_secret" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">
                                Webhook Secret <span class="text-slate-400 font-normal lowercase">(optional)</span>
                            </label>
                            <div class="relative">
                                <input :type="showWebhook ? 'text' : 'password'"
                                       name="razorpay_webhook_secret"
                                       id="razorpay_webhook_secret"
                                       placeholder="{{ $showroom->getSetting('razorpay_webhook_secret') ? '•••••••••••••••• (Leave blank to keep current)' : 'Optional webhook signing secret' }}"
                                       class="w-full px-4 py-2.5 pr-10 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <button type="button"
                                        @click="showWebhook = !showWebhook"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <svg x-show="!showWebhook" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showWebhook" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Used to verify asynchronous webhook status updates from Razorpay.</p>
                        </div>

                        <div>
                            <label for="booking_token_amount" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">
                                Default Refundable Booking Token (₹)
                            </label>
                            <input type="number"
                                   name="booking_token_amount"
                                   id="booking_token_amount"
                                   min="100"
                                   step="100"
                                   value="{{ old('booking_token_amount', $showroom->getBookingTokenAmount()) }}"
                                   required
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            <p class="text-[11px] text-slate-400 mt-1">Amount charged to customers when reserving a vehicle (e.g. ₹10,000).</p>
                        </div>
                    </div>

                    {{-- Help & Guidance Note --}}
                    <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/80 text-xs text-emerald-900 flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="space-y-1 leading-relaxed">
                            <p class="font-bold text-emerald-950">How Razorpay Integration Works for {{ $showroom->name }}:</p>
                            <p>
                                1. Log in to your <a href="https://dashboard.razorpay.com/" target="_blank" class="font-bold underline text-emerald-800 hover:text-emerald-950">Razorpay Dashboard</a>.
                            </p>
                            <p>
                                2. Navigate to <strong>Account &amp; Settings &rarr; API Keys &rarr; Generate Key</strong>. Copy your <em>Key ID</em> and <em>Key Secret</em>.
                            </p>
                            <p>
                                3. Paste them into the fields above and click Save. All customer booking advances will be credited directly to your bank account!
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Unified Save Button --}}
                <div class="flex items-center justify-between pt-2">
                    <p class="text-xs text-slate-500">Ensure all details are accurate before saving.</p>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider shadow-md transition cursor-pointer">
                        Save Showroom &amp; Payment Settings
                    </button>
                </div>
            </form>

            <!-- Showroom Team Members Roster -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold font-display text-slate-900">Dealership Personnel</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Staff members authorized to manage this showroom.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $showroom->memberships->count() }} Authorized Staff
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 uppercase font-bold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Member Name</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Access Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($showroom->memberships as $membership)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-3 font-bold text-slate-900">{{ $membership->user?->name }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $membership->user?->email }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-rose-700 border border-slate-200">
                                            {{ str_replace('_', ' ', $membership->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-emerald-700 font-semibold">{{ $membership->status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

    </div>
    @include('showrooms.partials.manage-nav-close')
</x-app-layout>
