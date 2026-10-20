@extends('layouts.app')

@section('title', 'AI Virtual Concierge - ' . $showroom->name)

@section('content')
<style>
    @keyframes avatarLifeBreathe {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-3px) scale(1.012); }
    }
    @keyframes naturalEyelidBlink {
        0%, 94%, 100% { opacity: 0; }
        96%, 98% { opacity: 0.95; }
    }
    @keyframes mouthPhonemeSync {
        0% { transform: translate(-50%, -50%) scaleY(0.15) scaleX(0.85); }
        25% { transform: translate(-50%, -50%) scaleY(0.85) scaleX(1.1); }
        50% { transform: translate(-50%, -50%) scaleY(0.35) scaleX(0.95); }
        75% { transform: translate(-50%, -50%) scaleY(0.95) scaleX(1.15); }
        100% { transform: translate(-50%, -50%) scaleY(0.2) scaleX(0.9); }
    }
    .animate-avatar-life {
        animation: avatarLifeBreathe 4s ease-in-out infinite;
    }
    .animate-avatar-blink {
        animation: naturalEyelidBlink 4.8s infinite;
    }
    .animate-avatar-speaking {
        animation: mouthPhonemeSync 0.24s ease-in-out infinite alternate;
    }
</style>
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

                        <!-- 🎯 Presenter Mode Selector: Choose EITHER Avatar OR Video (Enable Any 1) -->
                        <div class="p-4 rounded-2xl bg-slate-50 border-2 border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-black uppercase text-slate-900 block flex items-center gap-1.5">
                                        <span>Showroom Presenter Mode</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-200">Enable Any 1</span>
                                    </span>
                                    <span class="text-[11px] text-slate-500">Choose which presenter experience is enabled on your showroom website.</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Mode 1: AI Character Avatar -->
                                <label id="card-mode-avatar" onclick="selectPresenterMode('avatar')"
                                       class="presenter-mode-card relative flex flex-col p-3.5 rounded-xl border-2 cursor-pointer transition-all duration-200 bg-white {{ old('presenter_mode', $setting->presenter_mode ?? 'avatar') === 'avatar' ? 'border-rose-600 ring-2 ring-rose-500/20 shadow-xs' : 'border-slate-200 hover:border-slate-300 opacity-60' }}">
                                    <span id="badge-mode-avatar" class="{{ old('presenter_mode', $setting->presenter_mode ?? 'avatar') === 'avatar' ? '' : 'hidden' }} absolute top-2.5 right-2.5 px-2 py-0.5 rounded-full bg-rose-600 text-[9px] font-black text-white">✓ Active</span>
                                    <input type="radio" name="presenter_mode" id="radio-mode-avatar" value="avatar" {{ old('presenter_mode', $setting->presenter_mode ?? 'avatar') === 'avatar' ? 'checked' : '' }} class="sr-only">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xl">👤</span>
                                        <span class="text-xs font-bold text-slate-900">AI Character Avatar</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 leading-normal">
                                        Authentic 2D human cutout with natural breathing and synthetic voice synthesis.
                                    </p>
                                </label>

                                <!-- Mode 2: Photorealistic Welcome Video -->
                                <label id="card-mode-video" onclick="selectPresenterMode('video')"
                                       class="presenter-mode-card relative flex flex-col p-3.5 rounded-xl border-2 cursor-pointer transition-all duration-200 bg-white {{ old('presenter_mode', $setting->presenter_mode ?? 'avatar') === 'video' ? 'border-indigo-600 ring-2 ring-indigo-500/20 shadow-xs' : 'border-slate-200 hover:border-slate-300 opacity-60' }}">
                                    <span id="badge-mode-video" class="{{ old('presenter_mode', $setting->presenter_mode ?? 'avatar') === 'video' ? '' : 'hidden' }} absolute top-2.5 right-2.5 px-2 py-0.5 rounded-full bg-indigo-600 text-[9px] font-black text-white">✓ Active</span>
                                    <input type="radio" name="presenter_mode" id="radio-mode-video" value="video" {{ old('presenter_mode', $setting->presenter_mode ?? 'avatar') === 'video' ? 'checked' : '' }} class="sr-only">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xl">🎬</span>
                                        <span class="text-xs font-bold text-slate-900">Photorealistic Video</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 leading-normal">
                                        Full AI video (Veo / Gemini / MP4) with photorealistic lip-sync and human speech.
                                    </p>
                                </label>
                            </div>
                        </div>

                        @php
                            $isPresenterAvatar = old('presenter_mode', $setting->presenter_mode ?? 'avatar') === 'avatar';
                            $isPresenterVideo = old('presenter_mode', $setting->presenter_mode ?? 'avatar') === 'video';
                        @endphp

                        <!-- 1. Character Avatar Section -->
                        <div id="section-avatar-settings" class="p-4 sm:p-5 rounded-2xl border transition-all duration-200 space-y-4 bg-white border-rose-300 shadow-xs {{ $isPresenterAvatar ? '' : 'hidden' }}">
                            <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-900 block truncate">Option 1: AI Character Avatar</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            ● Active
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-500 block mt-0.5">2D authentic human cutout with natural breathing and interactive voice greeting.</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <!-- TVS Male Advisor -->
                                <label id="card-avatar-male" onclick="selectAvatar('male_tvs')"
                                       class="avatar-card relative flex flex-col items-center p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 text-center bg-white {{ old('avatar_type', $setting->avatar_type) === 'male_tvs' ? 'border-rose-600 ring-2 ring-rose-500/20 shadow-xs' : 'border-slate-200 hover:border-slate-300' }}">
                                    <span id="badge-male" class="{{ old('avatar_type', $setting->avatar_type) === 'male_tvs' ? '' : 'hidden' }} absolute top-2 right-2 px-1.5 py-0.5 rounded bg-rose-600 text-[9px] font-black text-white shadow-xs">✓ Active</span>
                                    <input type="radio" name="avatar_type" id="radio-avatar-male" value="male_tvs" {{ old('avatar_type', $setting->avatar_type) === 'male_tvs' ? 'checked' : '' }} class="sr-only">
                                    <div class="rounded-full overflow-hidden bg-slate-100 border border-slate-200 mb-2 shadow-xs shrink-0" style="width: 64px; height: 64px; min-width: 64px; min-height: 64px;">
                                        <img src="{{ asset('images/ai-avatars/avatar-male-tvs.jpg') }}" alt="Rohit - Real Human TVS Advisor" class="w-full h-full object-cover object-top" style="width: 100%; height: 100%; object-fit: cover; object-position: top center;">
                                    </div>
                                    <span class="text-xs font-bold text-slate-900 leading-tight">Rohit (TVS Advisor)</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5">Real Human Male</span>
                                </label>

                                <!-- Female Advisor -->
                                <label id="card-avatar-female" onclick="selectAvatar('female_saree')"
                                       class="avatar-card relative flex flex-col items-center p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 text-center bg-white {{ old('avatar_type', $setting->avatar_type) === 'female_saree' ? 'border-rose-600 ring-2 ring-rose-500/20 shadow-xs' : 'border-slate-200 hover:border-slate-300' }}">
                                    <span id="badge-female" class="{{ old('avatar_type', $setting->avatar_type) === 'female_saree' ? '' : 'hidden' }} absolute top-2 right-2 px-1.5 py-0.5 rounded bg-rose-600 text-[9px] font-black text-white shadow-xs">✓ Active</span>
                                    <input type="radio" name="avatar_type" id="radio-avatar-female" value="female_saree" {{ old('avatar_type', $setting->avatar_type) === 'female_saree' ? 'checked' : '' }} class="sr-only">
                                    <div class="rounded-full overflow-hidden bg-slate-100 border border-slate-200 mb-2 shadow-xs shrink-0" style="width: 64px; height: 64px; min-width: 64px; min-height: 64px;">
                                        <img src="{{ asset('images/ai-avatars/avatar-female-saree.jpg') }}" alt="Priya - Real Human Female Advisor" class="w-full h-full object-cover object-top" style="width: 100%; height: 100%; object-fit: cover; object-position: top center;">
                                    </div>
                                    <span class="text-xs font-bold text-slate-900 leading-tight">Priya (Female Advisor)</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5">Real Human Namaste</span>
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
                        </div>

                        <!-- 2. 🎬 AI Welcome Video (Gemini Veo / Upload / Direct Link) -->
                        <div id="section-video-settings" class="p-4 sm:p-5 rounded-2xl border transition-all duration-200 space-y-4 bg-white border-indigo-300 shadow-xs {{ $isPresenterVideo ? '' : 'hidden' }}">
                            <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-900 block truncate">Option 2: Photorealistic Welcome Video</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            ● Active
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-500 block mt-0.5">High-definition AI video generated with Gemini, uploaded, or linked.</span>
                                </div>
                            </div>

                            @if($setting->has_welcome_video)
                                <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                            🎬
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-emerald-900 block truncate">Active Video Configured</span>
                                            <span class="text-[10px] text-emerald-700 block truncate">
                                                Source: {{ $setting->welcome_video_type === 'url' ? 'External Link' : ($setting->welcome_video_type === 'gemini' ? 'Gemini AI Generated' : 'Self-hosted Upload') }}
                                            </span>
                                        </div>
                                    </div>
                                    <label class="text-xs text-rose-600 hover:text-rose-700 font-bold flex items-center gap-1 cursor-pointer shrink-0">
                                        <input type="checkbox" name="remove_welcome_video" value="1" class="rounded text-rose-600">
                                        <span>Remove Video</span>
                                    </label>
                                </div>
                            @endif

                            <!-- Mode Selector Tabs (Choose Any One) -->
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-700 mb-2">Choose How to Provide Video</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <button type="button" onclick="setVideoMode('gemini')" id="btn-mode-gemini"
                                            class="video-mode-tab flex items-center justify-center gap-1.5 p-2.5 rounded-xl border-2 text-xs font-bold transition cursor-pointer bg-white border-indigo-600 text-indigo-700 ring-2 ring-indigo-500/20">
                                        <span>✨ Generate with Gemini</span>
                                    </button>
                                    <button type="button" onclick="setVideoMode('upload')" id="btn-mode-upload"
                                            class="video-mode-tab flex items-center justify-center gap-1.5 p-2.5 rounded-xl border-2 text-xs font-bold transition cursor-pointer bg-white border-slate-200 text-slate-700 hover:border-slate-300">
                                        <span>📁 Upload File (.mp4)</span>
                                    </button>
                                    <button type="button" onclick="setVideoMode('url')" id="btn-mode-url"
                                            class="video-mode-tab flex items-center justify-center gap-1.5 p-2.5 rounded-xl border-2 text-xs font-bold transition cursor-pointer bg-white border-slate-200 text-slate-700 hover:border-slate-300">
                                        <span>🔗 Direct Video URL</span>
                                    </button>
                                </div>
                            </div>

                            <!-- MODE 1: Direct in-app Gemini (Veo) Generation -->
                            <div id="video-pane-gemini" class="p-4 bg-white rounded-2xl border border-indigo-100 shadow-xs space-y-3.5">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                    <span class="text-xs font-bold text-indigo-900 flex items-center gap-1.5">
                                        <span>🤖 Google Gemini / Veo Generator</span>
                                        <span class="text-[10px] font-normal text-slate-500">(Generates directly in your dashboard)</span>
                                    </span>
                                    <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer"
                                       class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                        <span>Get API Key</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>

                                <!-- Gemini API Key Input -->
                                <div>
                                    <label for="gemini_api_key_input" class="block text-xs font-bold uppercase text-slate-700 mb-1">Gemini API Key</label>
                                    <div class="relative">
                                        <input type="password" id="gemini_api_key_input" name="gemini_api_key"
                                               value="{{ old('gemini_api_key', $setting->gemini_api_key) }}"
                                               placeholder="AIzaSy..."
                                               class="w-full px-3 py-2 pr-10 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                        <button type="button" onclick="toggleApiKeyVisibility()"
                                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 cursor-pointer text-xs">
                                            👁
                                        </button>
                                    </div>
                                    <span class="text-[10px] text-slate-500 mt-1 block">Your API Key is saved securely for this showroom so you only have to enter it once.</span>
                                </div>

                                <!-- Prompt Selection & Input -->
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Prompt & Dialogue</label>
                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        <button type="button" onclick="applyPresetPrompt(1)"
                                                class="px-2 py-1 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition cursor-pointer">
                                            + Indian Namaste Welcome
                                        </button>
                                        <button type="button" onclick="applyPresetPrompt(2)"
                                                class="px-2 py-1 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition cursor-pointer">
                                            + Cinematic 4K Showroom
                                        </button>
                                        <button type="button" onclick="applyPresetPrompt(3)"
                                                class="px-2 py-1 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition cursor-pointer">
                                            + Friendly Advisor Welcome
                                        </button>
                                    </div>
                                    <textarea id="gemini_video_prompt" rows="3"
                                              class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 leading-relaxed"
                                              placeholder="Describe the showroom character and greeting...">lets make a video where an Indian woman saying namaste {{ $showroom->name }} showroom. for welcoming with bikes in background</textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="gemini_duration" class="block text-xs font-bold uppercase text-slate-700 mb-1">Duration</label>
                                        <select id="gemini_duration" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-500">
                                            <option value="8" selected>8 Seconds (Recommended)</option>
                                            <option value="5">5 Seconds</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="gemini_resolution" class="block text-xs font-bold uppercase text-slate-700 mb-1">Resolution</label>
                                        <select id="gemini_resolution" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-500">
                                            <option value="720p" selected>720p HD</option>
                                            <option value="1080p">1080p Full HD</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Action Trigger -->
                                <div>
                                    <button type="button" id="btn-trigger-gemini-generate" onclick="startGeminiGeneration()"
                                            class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-rose-600 hover:from-indigo-700 hover:to-rose-700 text-white font-bold text-xs shadow-md transition-all active:scale-98 cursor-pointer flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span>✨ Generate Video Directly with Gemini</span>
                                    </button>
                                </div>

                                <!-- Live Generation Status Box -->
                                <div id="gemini-gen-status-box" class="hidden p-3.5 bg-indigo-50 rounded-xl border border-indigo-200 text-xs text-indigo-900 space-y-2">
                                    <div class="flex items-center gap-2.5 font-bold">
                                        <svg class="w-4 h-4 animate-spin text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span id="gemini-status-title">Initiating Generation with Gemini...</span>
                                    </div>
                                    <p id="gemini-status-desc" class="text-[11px] text-slate-600 leading-normal pl-6">
                                        Communicating with Gemini AI. Video generation typically takes ~30–60 seconds.
                                    </p>
                                </div>
                            </div>

                            <!-- MODE 2: File Upload (.mp4) -->
                            <div id="video-pane-upload" class="hidden p-4 bg-white rounded-2xl border border-slate-200 shadow-xs space-y-3">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Upload Video File (.mp4, .webm)</label>
                                    <input type="file" name="welcome_video" accept="video/mp4,video/webm,video/quicktime,video/ogg"
                                           class="w-full text-xs text-slate-600 file:mr-2 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <span class="text-[10px] text-slate-500 mt-1 block">Supported formats: MP4, WebM (up to 50MB).</span>
                                </div>
                            </div>

                            <!-- MODE 3: Direct Video URL -->
                            <div id="video-pane-url" class="hidden p-4 bg-white rounded-2xl border border-slate-200 shadow-xs space-y-3">
                                <div>
                                    <label for="welcome_video_url" class="block text-xs font-bold uppercase text-slate-700 mb-1">Direct Video URL</label>
                                    <input type="url" name="welcome_video_url" id="welcome_video_url"
                                           value="{{ old('welcome_video_url', $setting->welcome_video_type === 'url' ? $setting->welcome_video_path : '') }}"
                                           placeholder="https://example.com/videos/namaste-welcome.mp4"
                                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                    <span class="text-[10px] text-slate-500 mt-1 block">Paste direct video stream link if hosting on Google Drive, Cloudinary, AWS S3, or CDN.</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" @disabled(! $hasFeature)
                                class="w-full py-3 rounded-xl btn-brand-primary font-bold text-xs transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                style="box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;">
                            Save AI Concierge & Presenter Settings
                        </button>
                    </form>
                </div>

            </div>

            <!-- Right Column: Avatar Preview, QR Code & Conversations Ledger (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Live Human Concierge Recitation Stage (Strictly Dimensioned, Real Human Presenter) -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                    Real Human AI Presenter
                                </span>
                                <span id="preview-live-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span id="preview-status-text">Ready to Speak</span>
                                </span>
                            </div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1 flex items-center gap-2">
                                <span>{{ $setting->bot_name }}</span>
                                <span class="text-xs font-normal text-slate-500">• 1-Tap Voice Greeting</span>
                            </h3>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span>Voice: <strong id="preview-voice-gender-label" class="text-slate-800">{{ $setting->effective_voice_gender === 'female' ? 'Female (Sweet)' : 'Male (TVS Advisor)' }}</strong></span>
                            <span>•</span>
                            <span id="preview-voice-lang-label" class="text-slate-700 font-semibold">{{ $setting->voice_language === 'hi-IN' ? 'Hindi (Namaste)' : ($setting->voice_language === 'en-IN' ? 'Indian English' : 'English (US)') }}</span>
                        </div>
                    </div>

                    <!-- Human Presenter Stage + Spoken Speech Display -->
                    <div class="mt-6 flex flex-col md:flex-row items-center md:items-stretch gap-6">

                        <!-- Human Presenter Stage (Strict fixed width/height container) -->
                        <div class="shrink-0 flex flex-col items-center" style="width: 200px; min-width: 200px; max-width: 200px;">
                            <div id="preview-avatar-stage"
                                 class="relative rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 shadow-md select-none"
                                 style="width: 200px; height: 250px; min-width: 200px; max-width: 200px; min-height: 250px; max-height: 250px;">

                                @php
                                    $initialMode = old('presenter_mode', $setting->presenter_mode ?? 'avatar');
                                @endphp

                                <!-- 1. Living Breathing Real Human Container -->
                                <div id="preview-character-container" class="{{ $initialMode === 'avatar' ? '' : 'hidden' }} relative w-full h-full overflow-hidden flex items-end justify-center animate-avatar-life">
                                    <!-- Real Human Photo -->
                                    <img id="preview-avatar-img"
                                         src="{{ $setting->avatar_type === 'female_saree' ? asset('images/ai-avatars/avatar-female-saree.jpg') : asset('images/ai-avatars/avatar-male-tvs.jpg') }}"
                                         alt="Real Human Advisor"
                                         class="w-full h-full object-cover object-top transition-transform duration-300"
                                         style="width: 100%; height: 100%; object-fit: cover; object-position: top center;">

                                    <!-- Subtle Natural Eyelid Blink Overlay -->
                                    <div id="preview-blink-overlay"
                                         class="absolute pointer-events-none opacity-0 animate-avatar-blink"
                                         style="left: 50%; top: 24.5%; width: 44px; height: 12px; transform: translate(-50%, -50%);">
                                        <div class="w-full h-[2px] bg-slate-900/40 rounded-full mx-auto"></div>
                                    </div>

                                    <!-- Organic Talking Mouth Overlay (Articulates during speech) -->
                                    <div id="preview-mouth-overlay"
                                         class="absolute pointer-events-none transition-opacity duration-150 opacity-0"
                                         style="left: 50%; top: 33.5%; width: 32px; height: 16px; transform: translate(-50%, -50%);">
                                        <svg viewBox="0 0 50 30" class="w-full h-full overflow-visible" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M 4 12 Q 25 7 46 12" stroke="#a34e44" stroke-width="2.5" stroke-linecap="round"/>
                                            <path d="M 5 12 Q 25 30 45 12 Z" fill="#3b0f11"/>
                                            <path d="M 9 12 Q 25 18 41 12" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
                                            <ellipse cx="25" cy="18" rx="9" ry="4" fill="#cf4e58"/>
                                            <path d="M 6 13 Q 25 29 44 13" stroke="#b85b52" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </div>

                                    <!-- Presenter HUD Badge Top-Left -->
                                    <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-slate-900/70 backdrop-blur-xs text-[9px] font-bold text-white shadow-xs">
                                        <span id="preview-hud-dot" class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        <span id="preview-hud-status">AI PRESENTER</span>
                                    </div>

                                    <!-- Bottom Gradient Overlay with Persona Name -->
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/80 via-slate-950/40 to-transparent p-2.5 pt-6 text-left">
                                        <span id="preview-character-persona-title" class="text-xs font-bold text-white block truncate leading-tight">
                                            {{ $setting->avatar_type === 'female_saree' ? 'Priya (Female Advisor)' : 'Rohit (TVS Advisor)' }}
                                        </span>
                                        <span class="text-[10px] text-slate-300 block leading-none mt-0.5">Real Human Showroom Host</span>
                                    </div>
                                </div>

                                <!-- Speaking Pulse Ring Halo -->
                                <div id="preview-speaking-halo" class="absolute inset-0 ring-4 ring-amber-400/60 rounded-2xl opacity-0 transition-opacity duration-300 pointer-events-none"></div>

                                <!-- 2. Living Breathing AI Video Player Container -->
                                <div id="preview-video-container" class="{{ $initialMode === 'video' ? '' : 'hidden' }} relative w-full h-full bg-slate-900">
                                    @if($setting->has_welcome_video)
                                        <video id="preview-welcome-video"
                                               src="{{ $setting->welcome_video_url }}"
                                               class="w-full h-full object-cover"
                                               playsinline
                                               controls
                                               preload="metadata"></video>
                                        <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-indigo-950/80 backdrop-blur-xs text-[9px] font-bold text-white shadow-xs pointer-events-none">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span>AI VIDEO ACTIVE</span>
                                        </div>
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center p-4 text-center bg-slate-900 text-white">
                                            <span class="text-3xl mb-2">🎬</span>
                                            <span class="text-xs font-bold text-indigo-300">No Video Configured</span>
                                            <span class="text-[10px] text-slate-400 mt-1">Generate with Gemini or upload an MP4 on the left</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Badges below avatar -->
                            <div class="mt-2 text-center">
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600">
                                    <span>✨ Photorealistic</span>
                                    <span>•</span>
                                    <span>HD 4K</span>
                                </span>
                            </div>
                        </div>

                        <!-- Speech Balloon & Interactive Controls -->
                        <div class="flex-1 min-w-0 w-full flex flex-col justify-between">
                            <!-- Speech Bubble pointing to the human avatar -->
                            <div class="relative bg-slate-50 border border-slate-200 rounded-2xl p-5 shadow-xs flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-200/60">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                            <span>Spoken Showroom Welcome Dialogue</span>
                                        </span>
                                        <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            Recited to Visitors
                                        </span>
                                    </div>

                                    <p id="preview-welcome-bubble-text" class="text-sm sm:text-base font-semibold text-slate-900 leading-relaxed italic select-all py-1">
                                        "{{ $setting->voice_welcome_text_or_default }}"
                                    </p>
                                </div>

                                <!-- Equalizer Sound Wave (active when speaking) -->
                                <div id="preview-equalizer-row" class="hidden mt-4 pt-3 border-t border-slate-200/80 flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 h-5">
                                        <span class="w-1 bg-amber-500 rounded-full animate-pulse h-2"></span>
                                        <span class="w-1 bg-amber-500 rounded-full animate-pulse h-5" style="animation-delay: 150ms;"></span>
                                        <span class="w-1 bg-amber-500 rounded-full animate-pulse h-3" style="animation-delay: 300ms;"></span>
                                        <span class="w-1 bg-amber-500 rounded-full animate-pulse h-5" style="animation-delay: 450ms;"></span>
                                        <span class="w-1 bg-amber-500 rounded-full animate-pulse h-2" style="animation-delay: 200ms;"></span>
                                        <span class="w-1 bg-amber-500 rounded-full animate-pulse h-4" style="animation-delay: 350ms;"></span>
                                    </div>
                                    <span class="text-xs font-bold text-amber-700 animate-pulse flex items-center gap-1">
                                        <span>🎙️ Human Voice Synthesis Playing...</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Speech Action Controls -->
                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                <button type="button" id="btn-preview-listen"
                                        class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-98 text-white font-bold text-xs shadow-md hover:shadow-lg transition flex items-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                    <span id="btn-preview-listen-text">Listen Spoken Greeting</span>
                                </button>

                                <button type="button" id="btn-preview-stop" class="hidden px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                                    ⏹ Stop
                                </button>

                                @if($showroom->isWebsiteAccessible())
                                    <a href="{{ route('showrooms.show', ['showroom' => $showroom, 'welcome' => 1]) }}" target="_blank"
                                       class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-xs transition flex items-center gap-1.5 sm:ml-auto">
                                        <span>Test on Website</span>
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
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
let activeAudio = null;

function selectPresenterMode(mode) {
    const radioAvatar = document.getElementById('radio-mode-avatar');
    const radioVideo = document.getElementById('radio-mode-video');
    const cardAvatar = document.getElementById('card-mode-avatar');
    const cardVideo = document.getElementById('card-mode-video');
    const badgeAvatar = document.getElementById('badge-mode-avatar');
    const badgeVideo = document.getElementById('badge-mode-video');
    const sectionAvatar = document.getElementById('section-avatar-settings');
    const sectionVideo = document.getElementById('section-video-settings');
    const previewCharContainer = document.getElementById('preview-character-container');
    const previewVideoContainer = document.getElementById('preview-video-container');
    const previewStatusText = document.getElementById('preview-status-text');

    if (mode === 'avatar') {
        if (radioAvatar) radioAvatar.checked = true;
        if (radioVideo) radioVideo.checked = false;

        // Top selector cards
        if (cardAvatar) {
            cardAvatar.classList.remove('opacity-60', 'border-slate-200');
            cardAvatar.classList.add('border-rose-600', 'ring-2', 'ring-rose-500/20', 'shadow-xs');
        }
        if (cardVideo) {
            cardVideo.classList.remove('border-indigo-600', 'ring-2', 'ring-indigo-500/20', 'shadow-xs');
            cardVideo.classList.add('opacity-60', 'border-slate-200');
        }
        if (badgeAvatar) badgeAvatar.classList.remove('hidden');
        if (badgeVideo) badgeVideo.classList.add('hidden');

        // Show Option 1 box, Hide Option 2 box
        if (sectionAvatar) sectionAvatar.classList.remove('hidden');
        if (sectionVideo) sectionVideo.classList.add('hidden');

        // Stage preview
        if (previewCharContainer) previewCharContainer.classList.remove('hidden');
        if (previewVideoContainer) {
            previewVideoContainer.classList.add('hidden');
            const videoElem = previewVideoContainer.querySelector('video');
            if (videoElem) videoElem.pause();
        }
        if (previewStatusText) previewStatusText.textContent = 'Ready to Speak';
    } else {
        if (radioVideo) radioVideo.checked = true;
        if (radioAvatar) radioAvatar.checked = false;

        // Top selector cards
        if (cardVideo) {
            cardVideo.classList.remove('opacity-60', 'border-slate-200');
            cardVideo.classList.add('border-indigo-600', 'ring-2', 'ring-indigo-500/20', 'shadow-xs');
        }
        if (cardAvatar) {
            cardAvatar.classList.remove('border-rose-600', 'ring-2', 'ring-rose-500/20', 'shadow-xs');
            cardAvatar.classList.add('opacity-60', 'border-slate-200');
        }
        if (badgeVideo) badgeVideo.classList.remove('hidden');
        if (badgeAvatar) badgeAvatar.classList.add('hidden');

        // Hide Option 1 box, Show Option 2 box
        if (sectionAvatar) sectionAvatar.classList.add('hidden');
        if (sectionVideo) sectionVideo.classList.remove('hidden');

        // Stage preview
        if (previewCharContainer) previewCharContainer.classList.add('hidden');
        if (previewVideoContainer) {
            previewVideoContainer.classList.remove('hidden');
            const videoElem = previewVideoContainer.querySelector('video');
            if (videoElem) videoElem.play().catch(e => console.log('Video autoplay error', e));
        }
        if (previewStatusText) previewStatusText.textContent = 'AI Video Presenter';
    }
}

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
    const personaTitle = document.getElementById('preview-character-persona-title');

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
        if (previewImg) previewImg.src = "{{ asset('images/ai-avatars/avatar-male-tvs.jpg') }}";
        if (personaTitle) personaTitle.textContent = "Rohit (TVS Advisor)";
        const mouthOverlay = document.getElementById('preview-mouth-overlay');
        const blinkOverlay = document.getElementById('preview-blink-overlay');
        if (mouthOverlay) {
            mouthOverlay.style.top = '29.5%';
            mouthOverlay.style.left = '49.5%';
        }
        if (blinkOverlay) {
            blinkOverlay.style.top = '21.5%';
            blinkOverlay.style.left = '49.5%';
        }

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
        if (previewImg) previewImg.src = "{{ asset('images/ai-avatars/avatar-female-saree.jpg') }}";
        if (personaTitle) personaTitle.textContent = "Priya (Female Advisor)";
        const mouthOverlay = document.getElementById('preview-mouth-overlay');
        const blinkOverlay = document.getElementById('preview-blink-overlay');
        if (mouthOverlay) {
            mouthOverlay.style.top = '33.5%';
            mouthOverlay.style.left = '50%';
        }
        if (blinkOverlay) {
            blinkOverlay.style.top = '24.5%';
            blinkOverlay.style.left = '50%';
        }

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
        langLabel.textContent = targetLang === 'hi-IN' ? 'Hindi (Namaste)' : (targetLang === 'en-IN' ? 'Indian English' : 'English (US)');
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

function setSpeakingUIState(isSpeaking) {
    const halo = document.getElementById('preview-speaking-halo');
    const equalizer = document.getElementById('preview-equalizer-row');
    const statusText = document.getElementById('preview-status-text');
    const liveBadge = document.getElementById('preview-live-badge');
    const btnText = document.getElementById('btn-preview-listen-text');
    const stopBtn = document.getElementById('btn-preview-stop');
    const mouthOverlay = document.getElementById('preview-mouth-overlay');
    const hudDot = document.getElementById('preview-hud-dot');
    const hudStatus = document.getElementById('preview-hud-status');

    if (isSpeaking) {
        if (halo) {
            halo.classList.remove('opacity-0');
            halo.classList.add('opacity-100');
        }
        if (mouthOverlay) {
            mouthOverlay.classList.remove('opacity-0');
            mouthOverlay.classList.add('opacity-100', 'animate-avatar-speaking');
        }
        if (hudDot) hudDot.className = "w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping";
        if (hudStatus) hudStatus.textContent = "SPEAKING LIVE";
        if (equalizer) equalizer.classList.remove('hidden');
        if (statusText) statusText.textContent = "Speaking Greeting...";
        if (liveBadge) {
            liveBadge.className = "px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1.5";
        }
        if (btnText) btnText.textContent = "Speaking Greeting...";
        if (stopBtn) stopBtn.classList.remove('hidden');
    } else {
        if (halo) {
            halo.classList.add('opacity-0');
            halo.classList.remove('opacity-100');
        }
        if (mouthOverlay) {
            mouthOverlay.classList.add('opacity-0');
            mouthOverlay.classList.remove('opacity-100', 'animate-avatar-speaking');
        }
        if (hudDot) hudDot.className = "w-1.5 h-1.5 rounded-full bg-emerald-400";
        if (hudStatus) hudStatus.textContent = "AI PRESENTER";
        if (equalizer) equalizer.classList.add('hidden');
        if (statusText) statusText.textContent = "Ready to Speak";
        if (liveBadge) {
            liveBadge.className = "px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5";
        }
        if (btnText) btnText.textContent = "Listen Spoken Greeting";
        if (stopBtn) stopBtn.classList.add('hidden');
    }
}

function stopVoicePlayback() {
    if (activeAudio) {
        activeAudio.pause();
        activeAudio.currentTime = 0;
        activeAudio = null;
    }
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
    }
    setSpeakingUIState(false);
}

function playVoiceTest() {
    stopVoicePlayback();

    const textInput = document.getElementById('voice_welcome_text');
    const langSelect = document.getElementById('voice_language');
    const voiceSelect = document.getElementById('voice_name');
    const audioUrl = "{{ $setting->voice_audio_url }}";

    if (audioUrl) {
        setSpeakingUIState(true);
        activeAudio = new Audio(audioUrl);
        activeAudio.onended = () => setSpeakingUIState(false);
        activeAudio.onerror = () => setSpeakingUIState(false);
        activeAudio.play().catch(e => {
            console.log('Audio playback error', e);
            setSpeakingUIState(false);
        });
        return;
    }

    if ('speechSynthesis' in window) {
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

        utterance.onstart = () => setSpeakingUIState(true);
        utterance.onend = () => setSpeakingUIState(false);
        utterance.onerror = () => setSpeakingUIState(false);

        window.speechSynthesis.speak(utterance);
    } else {
        alert('Speech synthesis is not supported on this browser.');
    }
}

function copyPrompt(elementId, btnElement) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const text = el.innerText || el.textContent;
    navigator.clipboard.writeText(text).then(() => {
        const originalHtml = btnElement.innerHTML;
        btnElement.innerHTML = '<span>✓ Copied!</span>';
        btnElement.classList.add('bg-emerald-600', 'text-white');
        btnElement.classList.remove('bg-indigo-50', 'text-indigo-700');
        setTimeout(() => {
            btnElement.innerHTML = originalHtml;
            btnElement.classList.remove('bg-emerald-600', 'text-white');
            btnElement.classList.add('bg-indigo-50', 'text-indigo-700');
        }, 2200);
    }).catch(err => {
        console.error('Copy failed:', err);
    });
}

function setVideoMode(mode) {
    const paneGemini = document.getElementById('video-pane-gemini');
    const paneUpload = document.getElementById('video-pane-upload');
    const paneUrl = document.getElementById('video-pane-url');

    const btnGemini = document.getElementById('btn-mode-gemini');
    const btnUpload = document.getElementById('btn-mode-upload');
    const btnUrl = document.getElementById('btn-mode-url');

    const allPanes = [paneGemini, paneUpload, paneUrl];
    const allBtns = [btnGemini, btnUpload, btnUrl];

    allPanes.forEach(p => p && p.classList.add('hidden'));
    allBtns.forEach(b => {
        if (b) {
            b.classList.remove('border-indigo-600', 'text-indigo-700', 'ring-2', 'ring-indigo-500/20');
            b.classList.add('border-slate-200', 'text-slate-700');
        }
    });

    if (mode === 'gemini') {
        paneGemini && paneGemini.classList.remove('hidden');
        btnGemini && btnGemini.classList.add('border-indigo-600', 'text-indigo-700', 'ring-2', 'ring-indigo-500/20');
    } else if (mode === 'upload') {
        paneUpload && paneUpload.classList.remove('hidden');
        btnUpload && btnUpload.classList.add('border-indigo-600', 'text-indigo-700', 'ring-2', 'ring-indigo-500/20');
    } else if (mode === 'url') {
        paneUrl && paneUrl.classList.remove('hidden');
        btnUrl && btnUrl.classList.add('border-indigo-600', 'text-indigo-700', 'ring-2', 'ring-indigo-500/20');
    }
}

function toggleApiKeyVisibility() {
    const input = document.getElementById('gemini_api_key_input');
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
}

function applyPresetPrompt(num) {
    const promptInput = document.getElementById('gemini_video_prompt');
    if (!promptInput) return;
    const showroomName = "{{ addslashes($showroom->name) }}";
    if (num === 1) {
        promptInput.value = `lets make a video where an Indian woman saying namaste ${showroomName} showroom. for welcoming with bikes in background`;
    } else if (num === 2) {
        promptInput.value = `A photorealistic video of a polite, smiling Indian female advisor in showroom attire with folded hands saying Namaste, welcome to ${showroomName} inside a modern motorcycle showroom with polished bikes in background, cinematic lighting`;
    } else if (num === 3) {
        promptInput.value = `A professional young Indian male motorcycle specialist greeting customers with folded hands in Namaste saying Namaste! Welcome to ${showroomName} with bikes in background`;
    }
}

let geminiPollInterval = null;

function startGeminiGeneration() {
    const apiKeyInput = document.getElementById('gemini_api_key_input');
    const promptInput = document.getElementById('gemini_video_prompt');
    const durationSelect = document.getElementById('gemini_duration');
    const resolutionSelect = document.getElementById('gemini_resolution');
    const statusBox = document.getElementById('gemini-gen-status-box');
    const statusTitle = document.getElementById('gemini-status-title');
    const statusDesc = document.getElementById('gemini-status-desc');
    const btnGenerate = document.getElementById('btn-trigger-gemini-generate');

    const apiKey = apiKeyInput ? apiKeyInput.value.trim() : '';
    const prompt = promptInput ? promptInput.value.trim() : '';

    if (!apiKey) {
        alert('Please enter your Gemini API Key first.');
        apiKeyInput && apiKeyInput.focus();
        return;
    }

    if (!prompt) {
        alert('Please provide a prompt for video generation.');
        promptInput && promptInput.focus();
        return;
    }

    btnGenerate.disabled = true;
    btnGenerate.classList.add('opacity-60', 'cursor-not-allowed');
    statusBox.classList.remove('hidden');
    statusTitle.textContent = "Step 1/3: Calling Gemini Veo API...";
    statusDesc.textContent = "Sending prompt and configuring video generation parameters...";

    fetch("{{ route('showrooms.manage.ai.generate-video', $showroom) }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json",
        },
        body: JSON.stringify({
            gemini_api_key: apiKey,
            prompt: prompt,
            duration: durationSelect ? durationSelect.value : 8,
            resolution: resolutionSelect ? resolutionSelect.value : "720p",
        })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Failed to initiate video generation.');
        }

        statusTitle.textContent = "Step 2/3: Gemini is synthesizing video frames...";
        statusDesc.textContent = "This takes about 30–60 seconds. Please keep this page open...";
        pollGeminiStatus(data.operation_name, apiKey);
    })
    .catch(err => {
        btnGenerate.disabled = false;
        btnGenerate.classList.remove('opacity-60', 'cursor-not-allowed');
        statusTitle.textContent = "❌ Generation Failed";
        statusDesc.textContent = err.message || "An unexpected error occurred.";
    });
}

function pollGeminiStatus(operationName, apiKey) {
    if (geminiPollInterval) clearInterval(geminiPollInterval);

    let attempts = 0;
    const maxAttempts = 36;

    geminiPollInterval = setInterval(() => {
        attempts++;

        fetch("{{ route('showrooms.manage.ai.check-video', $showroom) }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
            },
            body: JSON.stringify({
                operation_name: operationName,
                gemini_api_key: apiKey,
            })
        })
        .then(res => res.json())
        .then(data => {
            const statusTitle = document.getElementById('gemini-status-title');
            const statusDesc = document.getElementById('gemini-status-desc');
            const btnGenerate = document.getElementById('btn-trigger-gemini-generate');

            if (!data.done) {
                statusTitle.textContent = `Step 2/3: Generating video frames (${attempts * 5}s elapsed)...`;
                statusDesc.textContent = data.message || "Gemini neural model is rendering each frame...";
                return;
            }

            clearInterval(geminiPollInterval);
            btnGenerate.disabled = false;
            btnGenerate.classList.remove('opacity-60', 'cursor-not-allowed');

            if (!data.success) {
                statusTitle.textContent = "❌ Generation Error";
                statusDesc.textContent = data.message || "Failed to render video.";
                return;
            }

            statusTitle.textContent = "🎉 Video Ready & Activated!";
            statusDesc.textContent = data.message || "The video was downloaded and activated on your showroom.";

            setTimeout(() => {
                window.location.reload();
            }, 1500);
        })
        .catch(err => {
            if (attempts >= maxAttempts) {
                clearInterval(geminiPollInterval);
                const statusTitle = document.getElementById('gemini-status-title');
                const statusDesc = document.getElementById('gemini-status-desc');
                const btnGenerate = document.getElementById('btn-trigger-gemini-generate');
                btnGenerate.disabled = false;
                btnGenerate.classList.remove('opacity-60', 'cursor-not-allowed');
                statusTitle.textContent = "⚠️ Polling Timed Out";
                statusDesc.textContent = "Generation took longer than expected. Please check again in a moment.";
            }
        });
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function () {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.onvoiceschanged = updateAvailableVoices;
        updateAvailableVoices();
    }

    const btnTestVoice = document.getElementById('btn-test-voice');
    const btnPreviewListen = document.getElementById('btn-preview-listen');
    const btnPreviewStop = document.getElementById('btn-preview-stop');
    const voiceWelcomeInput = document.getElementById('voice_welcome_text');
    const previewBubbleText = document.getElementById('preview-welcome-bubble-text');

    if (btnTestVoice) btnTestVoice.addEventListener('click', playVoiceTest);
    if (btnPreviewListen) btnPreviewListen.addEventListener('click', playVoiceTest);
    if (btnPreviewStop) btnPreviewStop.addEventListener('click', stopVoicePlayback);

    if (voiceWelcomeInput && previewBubbleText) {
        voiceWelcomeInput.addEventListener('input', function () {
            const val = this.value.trim();
            if (val.length > 0) {
                previewBubbleText.textContent = `"${val}"`;
            } else {
                previewBubbleText.textContent = `"{{ addslashes($setting->voice_welcome_text_or_default) }}"`;
            }
        });
    }
});
</script>
@endpush
@endsection
