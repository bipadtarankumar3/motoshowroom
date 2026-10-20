@extends('layouts.app')

@section('title', 'AI Virtual Concierge - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8">

        <!-- Top Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-rose-600 font-semibold">AI Concierge</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">AI Virtual Showroom Advisor</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                        24/7 Concierge
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($showroom->isWebsiteAccessible())
                    <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                       class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 hover:text-slate-900 shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>View Storefront Widget</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Plan Gated Banner (if plan lacks AI feature) -->
        @if(! $hasFeature)
            <div class="mb-8 rounded-3xl bg-amber-50 border border-amber-200 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">AI Assistant Gated Feature</h3>
                        <p class="text-xs text-amber-800 mt-0.5">
                            Your current subscription plan does not include the AI Virtual Showroom Concierge. Upgrade to Pro or Enterprise to enable 24/7 intelligent sales inquiries, automatic lead generation, and custom FAQs.
                        </p>
                    </div>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}"
                   class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shrink-0 transition shadow-xs">
                    Upgrade Subscription
                </a>
            </div>
        @endif

        <!-- Metric KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="bg-white rounded-3xl p-5 border border-slate-200 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Total Conversations</span>
                    <span class="text-2xl font-black text-slate-900 mt-1 block">{{ number_format($totalConversations) }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-slate-200 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Leads Converted</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ number_format($totalConvertedLeads) }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-5 border border-slate-200 flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Today's Inquiries</span>
                    <span class="text-2xl font-black text-cyan-700 mt-1 block">{{ number_format($todayInquiries) }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 border border-cyan-200 flex items-center justify-center text-cyan-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Column: Bot Settings & Custom FAQs (5 cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Bot Configuration Form -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Concierge Settings</span>
                    </h3>

                    <form method="POST" action="{{ route('showrooms.manage.ai.settings', $showroom) }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <!-- Enable/Disable Switch -->
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Enable Public AI Assistant</span>
                                <span class="text-[11px] text-slate-500">Display interactive character widget on showroom website.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_enabled" value="1" {{ old('is_enabled', $setting->is_enabled) ? 'checked' : '' }}
                                       @disabled(! $hasFeature) class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                            </label>
                        </div>

                        <!-- Character Avatar Selection -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-bold uppercase text-slate-900 block">AI Character Avatar</span>
                                    <span class="text-[11px] text-slate-500">Select the welcome advisor character with Namaste gesture.</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Namaste 3D / 2D</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <!-- TVS Male Advisor -->
                                <label id="card-avatar-male" onclick="selectAvatar('male_tvs')"
                                       class="avatar-card relative flex flex-col items-center p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 text-center bg-white {{ old('avatar_type', $setting->avatar_type) === 'male_tvs' ? 'border-rose-600 ring-2 ring-rose-500/20 shadow-xs' : 'border-slate-200 hover:border-slate-300' }}">
                                    <span id="badge-male" class="{{ old('avatar_type', $setting->avatar_type) === 'male_tvs' ? '' : 'hidden' }} absolute top-2 right-2 px-1.5 py-0.5 rounded bg-rose-600 text-[9px] font-black text-white shadow-xs">✓ Active</span>
                                    <input type="radio" name="avatar_type" id="radio-avatar-male" value="male_tvs" {{ old('avatar_type', $setting->avatar_type) === 'male_tvs' ? 'checked' : '' }} class="sr-only">
                                    <div class="w-16 h-16 rounded-full overflow-hidden bg-slate-100 border border-slate-200 mb-2 shadow-xs">
                                        <img src="{{ asset('images/ai-avatars/avatar-male-tvs.jpg') }}" alt="TVS Male Advisor" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-xs font-bold text-slate-900 leading-tight">TVS Male Advisor</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5">Sawaranti TVS Uniform</span>
                                </label>

                                <!-- Female Advisor -->
                                <label id="card-avatar-female" onclick="selectAvatar('female_saree')"
                                       class="avatar-card relative flex flex-col items-center p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 text-center bg-white {{ old('avatar_type', $setting->avatar_type) === 'female_saree' ? 'border-rose-600 ring-2 ring-rose-500/20 shadow-xs' : 'border-slate-200 hover:border-slate-300' }}">
                                    <span id="badge-female" class="{{ old('avatar_type', $setting->avatar_type) === 'female_saree' ? '' : 'hidden' }} absolute top-2 right-2 px-1.5 py-0.5 rounded bg-rose-600 text-[9px] font-black text-white shadow-xs">✓ Active</span>
                                    <input type="radio" name="avatar_type" id="radio-avatar-female" value="female_saree" {{ old('avatar_type', $setting->avatar_type) === 'female_saree' ? 'checked' : '' }} class="sr-only">
                                    <div class="w-16 h-16 rounded-full overflow-hidden bg-slate-100 border border-slate-200 mb-2 shadow-xs">
                                        <img src="{{ asset('images/ai-avatars/avatar-female-saree.jpg') }}" alt="Female Advisor" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-xs font-bold text-slate-900 leading-tight">Female Advisor</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5">Traditional Saree Namaste</span>
                                </label>
                            </div>

                            <!-- Custom Upload Option -->
                            <div class="pt-2 border-t border-slate-200">
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-800">
                                    <input type="radio" name="avatar_type" id="radio-avatar-custom" value="custom" {{ old('avatar_type', $setting->avatar_type) === 'custom' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500" onclick="selectAvatar('custom')">
                                    <span>Or upload custom showroom character</span>
                                </label>

                                <div id="custom-avatar-upload-box" class="mt-2 {{ old('avatar_type', $setting->avatar_type) === 'custom' ? '' : 'hidden' }} space-y-2">
                                    @if($setting->avatar_type === 'custom' && $setting->avatar_custom_url)
                                        <div class="flex items-center gap-3 p-2 bg-white rounded-lg border border-slate-200">
                                            <img src="{{ $setting->avatar_url }}" alt="Current Custom Avatar" class="w-10 h-10 rounded-full object-cover border border-slate-200">
                                            <div class="flex-1 min-w-0">
                                                <span class="text-xs font-bold text-slate-800 block truncate">Current Custom Avatar</span>
                                                <label class="text-[11px] text-rose-600 hover:text-rose-700 flex items-center gap-1 cursor-pointer">
                                                    <input type="checkbox" name="remove_custom_avatar" value="1" class="rounded text-rose-600">
                                                    <span>Remove and reset</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" name="avatar_custom" accept="image/png,image/jpeg,image/webp"
                                           class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                                </div>
                            </div>
                        </div>

                        <!-- Spoken Voice & Namaste Greeting Experience (Recommendation #1) -->
                        <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200 space-y-3.5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-bold uppercase text-slate-900 block flex items-center gap-1.5">
                                        <span>Interactive Voice & Lip-Sync</span>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200">1-Tap</span>
                                    </span>
                                    <span class="text-[11px] text-slate-600">Audio welcome with Namaste animation and voice synthesis.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="voice_enabled" value="1" {{ old('voice_enabled', $setting->voice_enabled) ? 'checked' : '' }}
                                           @disabled(! $hasFeature) class="sr-only peer">
                                    <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-600"></div>
                                </label>
                            </div>

                            <!-- Voice Persona & Gender Selector -->
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Spoken Voice Persona & Gender</label>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <!-- Female Voice Option -->
                                    <label id="card-voice-female" onclick="selectVoiceGender('female')"
                                           class="voice-card flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition bg-white {{ old('voice_gender', $setting->effective_voice_gender) === 'female' ? 'border-amber-500 ring-2 ring-amber-400/20 bg-amber-50/40' : 'border-slate-200 hover:border-slate-300' }}">
                                        <input type="radio" name="voice_gender" id="radio-voice-female" value="female" {{ old('voice_gender', $setting->effective_voice_gender) === 'female' ? 'checked' : '' }} class="sr-only">
                                        <span class="text-xl">👩</span>
                                        <div class="text-left flex-1 min-w-0">
                                            <span class="text-xs font-bold text-slate-900 block leading-tight">Female Voice</span>
                                            <span class="text-[10px] text-slate-500 block">Sweet & Welcoming (महिला)</span>
                                        </div>
                                        <span id="check-voice-female" class="text-amber-600 text-xs font-black {{ old('voice_gender', $setting->effective_voice_gender) === 'female' ? '' : 'hidden' }}">✓</span>
                                    </label>

                                    <!-- Male Voice Option -->
                                    <label id="card-voice-male" onclick="selectVoiceGender('male')"
                                           class="voice-card flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition bg-white {{ old('voice_gender', $setting->effective_voice_gender) === 'male' ? 'border-amber-500 ring-2 ring-amber-400/20 bg-amber-50/40' : 'border-slate-200 hover:border-slate-300' }}">
                                        <input type="radio" name="voice_gender" id="radio-voice-male" value="male" {{ old('voice_gender', $setting->effective_voice_gender) === 'male' ? 'checked' : '' }} class="sr-only">
                                        <span class="text-xl">👨</span>
                                        <div class="text-left flex-1 min-w-0">
                                            <span class="text-xs font-bold text-slate-900 block leading-tight">Male Voice</span>
                                            <span class="text-[10px] text-slate-500 block">TVS Advisor (पुरुष)</span>
                                        </div>
                                        <span id="check-voice-male" class="text-amber-600 text-xs font-black {{ old('voice_gender', $setting->effective_voice_gender) === 'male' ? '' : 'hidden' }}">✓</span>
                                    </label>
                                </div>
                                <input type="hidden" name="voice_pitch" id="input_voice_pitch" value="{{ old('voice_pitch', $setting->effective_voice_pitch) }}">
                            </div>

                            <!-- Voice Language & Specific Voice Dropdown -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label for="voice_language" class="block text-xs font-bold uppercase text-slate-700 mb-1">Voice Language</label>
                                    <select name="voice_language" id="voice_language" onchange="updateAvailableVoices()" @disabled(! $hasFeature)
                                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                        <option value="hi-IN" {{ old('voice_language', $setting->voice_language) === 'hi-IN' ? 'selected' : '' }}>Hindi (हिंदी - Namaste)</option>
                                        <option value="en-IN" {{ old('voice_language', $setting->voice_language) === 'en-IN' ? 'selected' : '' }}>Indian English (English - India)</option>
                                        <option value="en-US" {{ old('voice_language', $setting->voice_language) === 'en-US' ? 'selected' : '' }}>English (US)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="voice_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Browser Voice Model</label>
                                    <select name="voice_name" id="voice_name" @disabled(! $hasFeature)
                                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                        <option value="">Auto-matched to Gender</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Spoken Welcome Text -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="voice_welcome_text" class="block text-xs font-bold uppercase text-slate-700">Spoken Voice Greeting</label>
                                    <button type="button" id="btn-test-voice" class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] shadow-xs flex items-center gap-1.5 transition active:scale-95 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                        <span>▶️ Test Selected Voice</span>
                                    </button>
                                </div>
                                <textarea name="voice_welcome_text" id="voice_welcome_text" rows="2" @disabled(! $hasFeature)
                                          placeholder="e.g. नमस्ते! सवरंती टीवीएस में आपका स्वागत है। बताइए आज मैं आपकी क्या सहायता कर सकता हूँ?"
                                          class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 leading-relaxed">{{ old('voice_welcome_text', $setting->voice_welcome_text) }}</textarea>
                                <span class="text-[10px] text-slate-500 mt-0.5 block">Leave blank to use the smart default greeting for this showroom.</span>
                            </div>

                            <!-- Optional Custom Audio Recording -->
                            <div class="pt-2 border-t border-amber-200/60">
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Optional Studio Voiceover File (.mp3, .wav)</label>
                                @if($setting->voice_audio_path)
                                    <div class="flex items-center gap-3 p-2 bg-white rounded-lg border border-slate-200 mb-2">
                                        <audio controls class="h-8 flex-1">
                                            <source src="{{ $setting->voice_audio_url }}">
                                        </audio>
                                        <label class="text-[11px] text-rose-600 hover:text-rose-700 flex items-center gap-1 cursor-pointer shrink-0">
                                            <input type="checkbox" name="remove_custom_audio" value="1" class="rounded text-rose-600">
                                            <span>Delete audio</span>
                                        </label>
                                    </div>
                                @endif
                                <input type="file" name="voice_audio" accept="audio/mpeg,audio/wav,audio/ogg,audio/mp4"
                                       class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
                            </div>
                        </div>

                        <button type="submit" @disabled(! $hasFeature)
                                class="w-full py-3 rounded-xl btn-brand-primary font-bold text-xs transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                style="box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;">
                            Save AI Welcome Speech Settings
                        </button>
                    </form>
                </div>

            </div>

            <!-- Right Column: Avatar Preview, QR Code & Conversations Ledger (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Interactive Character Live Preview & QR Scan Card -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Live Character Preview -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block">Live Character Preview</span>
                                <h4 class="text-sm font-bold text-slate-900 mt-0.5">{{ $setting->bot_name }}</h4>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Namaste Active</span>
                            </span>
                        </div>

                        <div class="flex items-center gap-4 py-2">
                            <div class="relative w-20 h-20 rounded-2xl overflow-hidden bg-slate-100 border-2 border-rose-500/30 shadow-md shrink-0">
                                <img id="preview-avatar-img" src="{{ $setting->avatar_url }}" alt="Character Preview" class="w-full h-full object-cover">
                                <div id="preview-speaking-indicator" class="hidden absolute bottom-1 right-1 px-1 py-0.5 rounded bg-emerald-500 text-[8px] font-black text-white">
                                    LIVE
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-slate-600 italic line-clamp-2 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                    "{{ $setting->voice_welcome_text_or_default }}"
                                </p>
                                <div class="mt-2 flex items-center gap-2">
                                    <button type="button" id="btn-preview-listen" class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] transition shadow-xs flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                        <span>Listen Spoken Greeting</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Voice: <strong id="preview-voice-gender-label" class="text-slate-800">{{ $setting->effective_voice_gender === 'female' ? 'Female (Sweet)' : 'Male (TVS Advisor)' }}</strong> (<span id="preview-voice-lang-label">{{ $setting->voice_language === 'hi-IN' ? 'Hindi' : 'Indian English' }}</span>)</span>
                            <span>Mode: <strong class="text-emerald-700">1-Tap Experience</strong></span>
                        </div>
                    </div>

                    <!-- In-Showroom QR Welcome Standee Card -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div>
                                <span class="text-[10px] font-bold text-cyan-700 uppercase tracking-wider block">QR Scan Welcome</span>
                                <h4 class="text-sm font-bold text-slate-900 mt-0.5">Customer QR Standee</h4>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                Auto-Greet
                            </span>
                        </div>

                        @php
                            $welcomeUrl = route('showrooms.show', ['showroom' => $showroom, 'welcome' => 1]);
                            $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($welcomeUrl);
                        @endphp

                        <div class="flex items-center gap-3 py-1">
                            <div class="p-2 rounded-2xl bg-white border border-slate-200 shadow-xs shrink-0">
                                <img src="{{ $qrImageUrl }}" alt="Showroom AI Concierge QR Code" class="w-16 h-16 object-contain">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] text-slate-600 leading-snug">
                                    Customers scanning this in the showroom land straight on the 1-Tap Namaste AI welcome experience.
                                </p>
                                <div class="mt-2 flex items-center gap-1.5 flex-wrap">
                                    <a href="{{ $welcomeUrl }}" target="_blank" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-[10px] transition">
                                        Test Link &rarr;
                                    </a>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $welcomeUrl }}'); alert('Direct QR link copied!');" class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-[10px] transition">
                                        Copy Link
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 pt-2 border-t border-slate-100 text-[10px] text-slate-500 truncate font-mono">
                            {{ $welcomeUrl }}
                        </div>
                    </div>
                </div>

                <!-- Conversations Ledger Card -->
                <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
                    <div class="p-6 border-b border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Customer Conversation History</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Live transcripts of visitor inquiries, recommended bikes, and captured sales leads.</p>
                            </div>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="{{ route('showrooms.manage.ai.index', $showroom) }}" class="mt-4 grid grid-cols-1 sm:grid-cols-12 gap-2">
                            <div class="sm:col-span-6">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Search customer, phone, or session..."
                                       class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                            </div>
                            <div class="sm:col-span-3">
                                <select name="intent" class="w-full px-2.5 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                    <option value="all">All Intents</option>
                                    <option value="bike_recommendation" {{ $intent === 'bike_recommendation' ? 'selected' : '' }}>Motorcycle Inquiry</option>
                                    <option value="test_ride" {{ $intent === 'test_ride' ? 'selected' : '' }}>Test Ride Request</option>
                                    <option value="service_inquiry" {{ $intent === 'service_inquiry' ? 'selected' : '' }}>Workshop Service</option>
                                    <option value="promotions" {{ $intent === 'promotions' ? 'selected' : '' }}>Promotions</option>
                                    <option value="general" {{ $intent === 'general' ? 'selected' : '' }}>General Info</option>
                                </select>
                            </div>
                            <div class="sm:col-span-3 flex gap-1">
                                <select name="converted" class="w-full px-2 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                    <option value="">All Leads</option>
                                    <option value="1" {{ $converted === '1' ? 'selected' : '' }}>Converted Only</option>
                                    <option value="0" {{ $converted === '0' ? 'selected' : '' }}>Not Converted</option>
                                </select>
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-xs font-semibold text-white transition shadow-xs">Go</button>
                            </div>
                        </form>
                    </div>

                    <!-- Conversation Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-4">Visitor / Contact</th>
                                    <th class="py-3 px-4">Intent</th>
                                    <th class="py-3 px-4">Lead Status</th>
                                    <th class="py-3 px-4">Messages</th>
                                    <th class="py-3 px-4">Date</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($conversations as $conv)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-4">
                                            @if($conv->customer_name)
                                                <span class="font-bold text-slate-900 block">{{ $conv->customer_name }}</span>
                                            @endif
                                            @if($conv->customer_phone)
                                                <span class="font-mono text-cyan-700 block text-[11px]">{{ $conv->customer_phone }}</span>
                                            @else
                                                <span class="font-mono text-slate-400 text-[10px]">{{ Str::limit($conv->session_id, 12) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ ucfirst(str_replace('_', ' ', $conv->detected_intent ?? 'General')) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($conv->lead_converted)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span>Converted Lead</span>
                                                </span>
                                            @else
                                                <span class="text-slate-500 text-[11px]">Chatting</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-mono font-bold text-slate-700">{{ $conv->messages->count() }} msgs</span>
                                        </td>
                                        <td class="py-3 px-4 text-[11px] text-slate-500">
                                            {{ $conv->created_at->diffForHumans() }}
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('showrooms.manage.ai.conversations.show', [$showroom, $conv]) }}"
                                                   class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition" title="View Full Transcript">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <form method="POST" action="{{ route('showrooms.manage.ai.conversations.destroy', [$showroom, $conv]) }}"
                                                      onsubmit="return confirm('Delete this conversation history?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete conversation">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-500">
                                            No customer chat conversations recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($conversations->hasPages())
                        <div class="p-4 border-t border-slate-200 bg-white">
                            {{ $conversations->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </main>
    @include('showrooms.partials.manage-nav-close')
</div>

@push('scripts')
<script>
let currentVoiceGender = "{{ old('voice_gender', $setting->effective_voice_gender) }}";
let savedVoiceName = "{{ old('voice_name', $setting->voice_name ?? '') }}";

function selectAvatar(choice) {
    const cardMale = document.getElementById('card-avatar-male');
    const cardFemale = document.getElementById('card-avatar-female');
    const badgeMale = document.getElementById('badge-male');
    const badgeFemale = document.getElementById('badge-female');
    const radioMale = document.getElementById('radio-avatar-male');
    const radioFemale = document.getElementById('radio-avatar-female');
    const radioCustom = document.getElementById('radio-avatar-custom');
    const uploadBox = document.getElementById('custom-avatar-upload-box');
    const previewImg = document.getElementById('preview-avatar-img');

    const activeClasses = ['border-rose-600', 'ring-2', 'ring-rose-500/20', 'shadow-xs'];
    const inactiveClasses = ['border-slate-200', 'hover:border-slate-300'];

    if (choice === 'male_tvs') {
        if (radioMale) radioMale.checked = true;
        if (cardMale) {
            cardMale.classList.add(...activeClasses);
            cardMale.classList.remove(...inactiveClasses);
        }
        if (cardFemale) {
            cardFemale.classList.remove(...activeClasses);
            cardFemale.classList.add(...inactiveClasses);
        }
        if (badgeMale) badgeMale.classList.remove('hidden');
        if (badgeFemale) badgeFemale.classList.add('hidden');
        if (uploadBox) uploadBox.classList.add('hidden');
        if (previewImg) previewImg.src = "{{ asset('images/ai-avatars/avatar-male-tvs-cutout.png') }}";

        // Auto-switch voice persona to male TVS advisor
        selectVoiceGender('male');
    } else if (choice === 'female_saree') {
        if (radioFemale) radioFemale.checked = true;
        if (cardFemale) {
            cardFemale.classList.add(...activeClasses);
            cardFemale.classList.remove(...inactiveClasses);
        }
        if (cardMale) {
            cardMale.classList.remove(...activeClasses);
            cardMale.classList.add(...inactiveClasses);
        }
        if (badgeFemale) badgeFemale.classList.remove('hidden');
        if (badgeMale) badgeMale.classList.add('hidden');
        if (uploadBox) uploadBox.classList.add('hidden');
        if (previewImg) previewImg.src = "{{ asset('images/ai-avatars/avatar-female-saree-cutout.png') }}";

        // Auto-switch voice persona to female advisor
        selectVoiceGender('female');
    } else if (choice === 'custom') {
        if (radioCustom) radioCustom.checked = true;
        if (cardMale) {
            cardMale.classList.remove(...activeClasses);
            cardMale.classList.add(...inactiveClasses);
        }
        if (cardFemale) {
            cardFemale.classList.remove(...activeClasses);
            cardFemale.classList.add(...inactiveClasses);
        }
        if (badgeMale) badgeMale.classList.add('hidden');
        if (badgeFemale) badgeFemale.classList.add('hidden');
        if (uploadBox) uploadBox.classList.remove('hidden');
    }
}

function selectVoiceGender(gender) {
    currentVoiceGender = gender;
    const cardFemale = document.getElementById('card-voice-female');
    const cardMale = document.getElementById('card-voice-male');
    const radioFemale = document.getElementById('radio-voice-female');
    const radioMale = document.getElementById('radio-voice-male');
    const checkFemale = document.getElementById('check-voice-female');
    const checkMale = document.getElementById('check-voice-male');
    const pitchInput = document.getElementById('input_voice_pitch');
    const genderLabel = document.getElementById('preview-voice-gender-label');

    const activeClasses = ['border-amber-500', 'ring-2', 'ring-amber-400/20', 'bg-amber-50/40'];
    const inactiveClasses = ['border-slate-200', 'hover:border-slate-300'];

    if (gender === 'female') {
        if (radioFemale) radioFemale.checked = true;
        if (cardFemale) {
            cardFemale.classList.add(...activeClasses);
            cardFemale.classList.remove(...inactiveClasses);
        }
        if (cardMale) {
            cardMale.classList.remove(...activeClasses);
            cardMale.classList.add(...inactiveClasses);
        }
        if (checkFemale) checkFemale.classList.remove('hidden');
        if (checkMale) checkMale.classList.add('hidden');
        if (pitchInput) pitchInput.value = '1.22';
        if (genderLabel) genderLabel.textContent = 'Female (Sweet)';
    } else {
        if (radioMale) radioMale.checked = true;
        if (cardMale) {
            cardMale.classList.add(...activeClasses);
            cardMale.classList.remove(...inactiveClasses);
        }
        if (cardFemale) {
            cardFemale.classList.remove(...activeClasses);
            cardFemale.classList.add(...inactiveClasses);
        }
        if (checkMale) checkMale.classList.remove('hidden');
        if (checkFemale) checkFemale.classList.add('hidden');
        if (pitchInput) pitchInput.value = '0.88';
        if (genderLabel) genderLabel.textContent = 'Male (TVS Advisor)';
    }

    updateAvailableVoices();
}

function isFemaleVoiceName(name) {
    const n = name.toLowerCase();
    return n.includes('female') || n.includes('woman') || n.includes('girl') ||
           n.includes('lekha') || n.includes('swara') || n.includes('veena') ||
           n.includes('samantha') || n.includes('zira') || n.includes('kavya') ||
           n.includes('priya') || n.includes('aditi') || n.includes('heera') ||
           n.includes('kalpana') || n.includes('geeta') || n.includes('victoria') ||
           n.includes('karen') || n.includes('susan');
}

function isMaleVoiceName(name) {
    const n = name.toLowerCase();
    return n.includes('male') || n.includes('man') || n.includes('boy') ||
           n.includes('rishi') || n.includes('daniel') || n.includes('george') ||
           n.includes('madhav') || n.includes('hemant') || n.includes('david') ||
           n.includes('ravi') || n.includes('alex') || n.includes('fred');
}

function updateAvailableVoices() {
    if (!('speechSynthesis' in window)) return;

    const langSelect = document.getElementById('voice_language');
    const voiceSelect = document.getElementById('voice_name');
    const langLabel = document.getElementById('preview-voice-lang-label');
    if (!voiceSelect) return;

    const targetLang = langSelect ? langSelect.value : 'hi-IN';
    if (langLabel) {
        langLabel.textContent = targetLang === 'hi-IN' ? 'Hindi' : (targetLang === 'en-IN' ? 'Indian English' : 'English');
    }

    const voices = window.speechSynthesis.getVoices();
    const langPrefix = targetLang.substring(0, 2);

    const relevantVoices = voices.filter(v => v.lang === targetLang || v.lang.replace('_', '-').startsWith(langPrefix));
    const previousSelection = voiceSelect.value || savedVoiceName;
    voiceSelect.innerHTML = '';

    const autoOpt = document.createElement('option');
    autoOpt.value = '';
    autoOpt.textContent = `Auto-matched (${currentVoiceGender === 'female' ? 'Female Sweet Pitch 1.22' : 'Male Advisor Pitch 0.88'})`;
    voiceSelect.appendChild(autoOpt);

    relevantVoices.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v.name;
        const isF = isFemaleVoiceName(v.name);
        const isM = isMaleVoiceName(v.name);
        const genderTag = isF ? ' (Female)' : (isM ? ' (Male)' : '');
        opt.textContent = `${v.name}${genderTag} [${v.lang}]`;
        if (v.name === previousSelection) {
            opt.selected = true;
        }
        voiceSelect.appendChild(opt);
    });
}

function findBestVoice(lang, gender, preferredName) {
    if (!('speechSynthesis' in window)) return null;
    const voices = window.speechSynthesis.getVoices();
    if (!voices || voices.length === 0) return null;

    if (preferredName) {
        const named = voices.find(v => v.name === preferredName);
        if (named) return named;
    }

    const langPrefix = lang.substring(0, 2);
    const matchingLangVoices = voices.filter(v => v.lang === lang || v.lang.replace('_', '-').startsWith(langPrefix));

    if (gender === 'female') {
        const femaleVoice = matchingLangVoices.find(v => isFemaleVoiceName(v.name));
        if (femaleVoice) return femaleVoice;
    } else {
        const maleVoice = matchingLangVoices.find(v => isMaleVoiceName(v.name));
        if (maleVoice) return maleVoice;
    }

    return matchingLangVoices[0] || voices[0];
}

function playVoiceTest() {
    const textInput = document.getElementById('voice_welcome_text');
    const langSelect = document.getElementById('voice_language');
    const voiceSelect = document.getElementById('voice_name');
    const audioUrl = "{{ $setting->voice_audio_url }}";

    if (audioUrl) {
        const audio = new Audio(audioUrl);
        audio.play().catch(e => console.log('Audio playback error', e));
        return;
    }

    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const text = (textInput && textInput.value.trim()) ? textInput.value.trim() : "{{ addslashes($setting->voice_welcome_text_or_default) }}";
        const lang = langSelect ? langSelect.value : 'hi-IN';
        const chosenVoiceName = voiceSelect ? voiceSelect.value : '';

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = lang;

        const isFemale = currentVoiceGender === 'female';
        utterance.pitch = isFemale ? 1.22 : 0.88;
        utterance.rate = isFemale ? 0.98 : 0.94;

        const bestVoice = findBestVoice(lang, currentVoiceGender, chosenVoiceName);
        if (bestVoice) {
            utterance.voice = bestVoice;
        }

        const indicator = document.getElementById('preview-speaking-indicator');
        utterance.onstart = () => { if (indicator) indicator.classList.remove('hidden'); };
        utterance.onend = () => { if (indicator) indicator.classList.add('hidden'); };
        utterance.onerror = () => { if (indicator) indicator.classList.add('hidden'); };

        window.speechSynthesis.speak(utterance);
    } else {
        alert('Speech synthesis is not supported on this browser.');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.onvoiceschanged = updateAvailableVoices;
        updateAvailableVoices();
    }

    const btnTestVoice = document.getElementById('btn-test-voice');
    const btnPreviewListen = document.getElementById('btn-preview-listen');
    if (btnTestVoice) btnTestVoice.addEventListener('click', playVoiceTest);
    if (btnPreviewListen) btnPreviewListen.addEventListener('click', playVoiceTest);
});
</script>
@endpush
@endsection
