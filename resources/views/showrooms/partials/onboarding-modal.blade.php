@php
    $currentUser = auth()->user();
    $mustOnboard = ! $currentUser->onboarding_completed_at;
@endphp

<div x-data="{
        isOpen: {{ $mustOnboard ? 'true' : 'false' }},
        step: 1,
        activeTab: 'profile',
        mustComplete: {{ $mustOnboard ? 'true' : 'false' }},
        init() {
            window.addEventListener('open-onboarding-modal', () => {
                this.isOpen = true;
            });
        }
     }"
     x-show="isOpen"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    {{-- Backdrop --}}
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
         @click="if(!mustComplete) isOpen = false">
    </div>

    {{-- Dialog Box --}}
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6 lg:p-8">
        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-3xl border border-slate-200">

            {{-- Top Accent Bar --}}
            <div class="h-2 w-full bg-gradient-to-r from-red-600 via-rose-500 to-amber-500"></div>

            {{-- Modal Header --}}
            <div class="p-6 sm:p-7 border-b border-slate-100 bg-slate-50/60 flex items-start justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-red-600 to-amber-500 flex items-center justify-center text-white shadow-md shadow-red-500/20 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl sm:text-2xl font-black font-display text-slate-900 tracking-tight" id="modal-title">
                                Set Up Your Profile & Showroom
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                Required Setup
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Complete your owner identity and showroom dealership details to activate your fully operational back-office.
                        </p>
                    </div>
                </div>

                {{-- Close Button (only allowed if already completed before) --}}
                <template x-if="!mustComplete">
                    <button type="button"
                            @click="isOpen = false"
                            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </template>
            </div>

            {{-- Mandatory Alert Banner --}}
            <template x-if="mustComplete">
                <div class="px-6 sm:px-7 py-3 bg-amber-50 border-b border-amber-200/80 flex items-center gap-2.5 text-xs text-amber-900 font-medium">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>Please fill in the required fields marked with <strong class="text-rose-600 font-bold">*</strong> and submit to start managing your bikes, enquiries, and showroom.</span>
                </div>
            </template>

            {{-- Tab Selectors --}}
            <div class="px-6 sm:px-7 pt-4 border-b border-slate-100 flex items-center gap-2" role="tablist">
                <button type="button"
                        @click="activeTab = 'profile'"
                        :class="activeTab === 'profile'
                            ? 'border-red-600 text-red-600 font-bold bg-red-50/50'
                            : 'border-transparent text-slate-500 hover:text-slate-700 font-semibold'"
                        class="px-4 py-2.5 border-b-2 text-xs sm:text-sm flex items-center gap-2 transition rounded-t-xl cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>1. Owner Profile Details</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                </button>

                <button type="button"
                        @click="activeTab = 'showroom'"
                        :class="activeTab === 'showroom'
                            ? 'border-red-600 text-red-600 font-bold bg-red-50/50'
                            : 'border-transparent text-slate-500 hover:text-slate-700 font-semibold'"
                        class="px-4 py-2.5 border-b-2 text-xs sm:text-sm flex items-center gap-2 transition rounded-t-xl cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>2. Showroom Dealership</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                </button>
            </div>

            {{-- Form Container --}}
            <form method="POST" action="{{ route('showrooms.onboarding.save', $showroom) }}" id="form-onboarding-modal" class="p-6 sm:p-7 space-y-6">
                @csrf

                {{-- TAB 1: Owner Profile Details --}}
                <div x-show="activeTab === 'profile'" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Name (Mandatory) --}}
                        <div>
                            <label for="modal_user_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Your Full Name <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="user_name"
                                   id="modal_user_name"
                                   required
                                   value="{{ old('user_name', $currentUser->name) }}"
                                   placeholder="e.g. Rajesh Kumar"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                        </div>

                        {{-- Email (Read-Only) --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Google Email Address
                            </label>
                            <input type="email"
                                   disabled
                                   value="{{ $currentUser->email }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 text-sm font-medium cursor-not-allowed">
                        </div>

                        {{-- Phone (Mandatory) --}}
                        <div>
                            <label for="modal_user_phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Contact Phone Number <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="user_phone"
                                   id="modal_user_phone"
                                   required
                                   value="{{ old('user_phone', $currentUser->phone) }}"
                                   placeholder="+91 98765 43210"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                        </div>

                        {{-- City (Mandatory) --}}
                        <div>
                            <label for="modal_user_city" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                City / Location <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="user_city"
                                   id="modal_user_city"
                                   required
                                   value="{{ old('user_city', $currentUser->city) }}"
                                   placeholder="e.g. Bangalore"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label for="modal_user_address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Personal Residential / Office Address
                        </label>
                        <input type="text"
                               name="user_address"
                               id="modal_user_address"
                               value="{{ old('user_address', $currentUser->address) }}"
                               placeholder="e.g. #14 Silicon Valley Avenue, Koramangala"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                    </div>

                    {{-- Bio --}}
                    <div>
                        <label for="modal_user_bio" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Bio / About Yourself
                        </label>
                        <textarea name="user_bio"
                                  id="modal_user_bio"
                                  rows="2"
                                  placeholder="e.g. Motorcycle enthusiast and dealership founder with 10+ years in superbike distribution."
                                  class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">{{ old('user_bio', $currentUser->bio) }}</textarea>
                    </div>

                    <div class="pt-3 flex justify-end">
                        <button type="button"
                                @click="activeTab = 'showroom'"
                                class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <span>Proceed to Showroom Details →</span>
                        </button>
                    </div>
                </div>

                {{-- TAB 2: Showroom Dealership Details --}}
                <div x-show="activeTab === 'showroom'" x-cloak class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Showroom Name (Mandatory) --}}
                        <div>
                            <label for="modal_showroom_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Dealership / Showroom Name <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="showroom_name"
                                   id="modal_showroom_name"
                                   required
                                   value="{{ old('showroom_name', $showroom->name) }}"
                                   placeholder="e.g. Apex Superbikes Hub"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                        </div>

                        {{-- Showroom Email (Mandatory) --}}
                        <div>
                            <label for="modal_showroom_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Official Showroom Email <span class="text-rose-600">*</span>
                            </label>
                            <input type="email"
                                   name="showroom_email"
                                   id="modal_showroom_email"
                                   required
                                   value="{{ old('showroom_email', $showroom->email) }}"
                                   placeholder="contact@apexsuperbikes.com"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                        </div>

                        {{-- Showroom Phone (Mandatory) --}}
                        <div>
                            <label for="modal_showroom_phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Showroom Phone / Hotline <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="showroom_phone"
                                   id="modal_showroom_phone"
                                   required
                                   value="{{ old('showroom_phone', $showroom->phone) }}"
                                   placeholder="+91 80 2345 6789"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                        </div>

                        {{-- Showroom City (Mandatory) --}}
                        <div>
                            <label for="modal_showroom_city" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Showroom City <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="showroom_city"
                                   id="modal_showroom_city"
                                   required
                                   value="{{ old('showroom_city', $showroom->city) }}"
                                   placeholder="e.g. Bangalore"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                        </div>

                        {{-- State (Mandatory) --}}
                        <div>
                            <label for="modal_showroom_state" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                State / Province <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="showroom_state"
                                   id="modal_showroom_state"
                                   required
                                   value="{{ old('showroom_state', $showroom->state ?? 'Karnataka') }}"
                                   placeholder="e.g. Karnataka"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                        </div>

                        {{-- Postal Code (Mandatory) --}}
                        <div>
                            <label for="modal_showroom_postal_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Postal Code / PIN <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="showroom_postal_code"
                                   id="modal_showroom_postal_code"
                                   required
                                   value="{{ old('showroom_postal_code', $showroom->postal_code ?? '560001') }}"
                                   placeholder="e.g. 560001"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                        </div>
                    </div>

                    {{-- Address Line 1 (Mandatory) --}}
                    <div>
                        <label for="modal_showroom_address_line1" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Showroom Physical Street Address <span class="text-rose-600">*</span>
                        </label>
                        <input type="text"
                               name="showroom_address_line1"
                               id="modal_showroom_address_line1"
                               required
                               value="{{ old('showroom_address_line1', $showroom->address_line1) }}"
                               placeholder="e.g. Plot #42, Brigade Road, Commercial Hub"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Brand Primary Color --}}
                        <div>
                            <label for="modal_brand_color_primary" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Brand Primary Color
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="color"
                                       id="modal_brand_color_picker"
                                       value="{{ $showroom->brand_color_primary ?? '#DC2626' }}"
                                       onchange="document.getElementById('modal_brand_color_primary').value = this.value"
                                       class="w-10 h-10 rounded-xl cursor-pointer border border-slate-200 p-0.5 bg-white">
                                <input type="text"
                                       name="brand_color_primary"
                                       id="modal_brand_color_primary"
                                       value="{{ old('brand_color_primary', $showroom->brand_color_primary ?? '#DC2626') }}"
                                       class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-mono focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                            </div>
                        </div>

                        {{-- Tagline --}}
                        <div>
                            <label for="modal_tagline" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Showroom Tagline
                            </label>
                            <input type="text"
                                   name="tagline"
                                   id="modal_tagline"
                                   value="{{ old('tagline', $showroom->settings['tagline'] ?? 'Authorized Premium Motorcycle Dealership') }}"
                                   placeholder="e.g. Pure Performance Superbikes"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">
                        </div>
                    </div>

                    {{-- About Text --}}
                    <div>
                        <label for="modal_about_text" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Showroom Description & Story
                        </label>
                        <textarea name="about_text"
                                  id="modal_about_text"
                                  rows="2"
                                  placeholder="Brief description about your dealership, brands represented, and service warranty."
                                  class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-2xs">{{ old('about_text', $showroom->settings['about_text'] ?? '') }}</textarea>
                    </div>

                    <div class="pt-2 flex items-center justify-between">
                        <button type="button"
                                @click="activeTab = 'profile'"
                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            ← Back to Profile
                        </button>

                        <button type="submit"
                                id="btn-submit-onboarding"
                                class="px-6 py-3 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-bold text-sm shadow-lg shadow-red-600/25 transition flex items-center gap-2 cursor-pointer active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Save Details & Access Dashboard →</span>
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>
