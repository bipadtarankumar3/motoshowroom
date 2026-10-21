@php
    $aiAvailable = $showroom->canAccessFeature('has_ai_assistant');
    $aiSetting = $showroom->getOrCreateAiSetting();
    $primaryColor = $showroom->brand_color_primary ?? '#dc2626';
    $accentColor = $showroom->brand_color_accent ?? '#0f172a';
    $avatarUrl = $aiSetting->avatar_url;
    $avatarType = $aiSetting->avatar_type ?? 'male_tvs';
    $voiceGreeting = $aiSetting->voice_welcome_text_or_default;
    $voiceAudioUrl = $aiSetting->voice_audio_url;
    $voiceEnabled = (bool) $aiSetting->voice_enabled;
    $voiceLang = $aiSetting->voice_language ?? 'hi-IN';
    $voiceGender = $aiSetting->effective_voice_gender;
    $voicePitch = $aiSetting->effective_voice_pitch;
    $voiceName = $aiSetting->voice_name;
    $welcomeVideoUrl = $aiSetting->welcome_video_url;
    $hasWelcomeVideo = ($aiSetting->effective_presenter_mode === 'video') && ! empty($welcomeVideoUrl);

    $advisorModelUrl = ($avatarType === 'male_tvs' || $voiceGender === 'male')
        ? asset('models/avatars/advisor-male.glb')
        : asset('models/avatars/advisor-female.glb');
@endphp

@if($aiAvailable && $aiSetting->is_enabled)
<!-- AI Character & Real-Time Audio Lip-Sync Styles -->
<style>
    @keyframes rgbSpin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes rgbGlowRotate {
        0% { filter: hue-rotate(0deg); }
        100% { filter: hue-rotate(360deg); }
    }

    @keyframes rgbPulse {
        0%, 100% {
            transform: scale(0.96);
            opacity: 0.65;
        }
        50% {
            transform: scale(1.08);
            opacity: 0.95;
        }
    }

    @keyframes subtleBounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }

    .avatar-rgb-halo {
        background: conic-gradient(from 0deg at 50% 50%, #ff0055, #ff5500, #ffbe00, #00ff88, #00d4ff, #7a00ff, #ff00c8, #ff0055);
        animation: rgbSpin 5s linear infinite, rgbGlowRotate 7s linear infinite;
    }

    .avatar-rgb-ambient {
        background: conic-gradient(from 0deg at 50% 50%, #ff0055, #ff5500, #ffbe00, #00ff88, #00d4ff, #7a00ff, #ff00c8, #ff0055);
        animation: rgbSpin 7s linear infinite, rgbPulse 3.5s ease-in-out infinite;
    }

    .animate-bounce-subtle {
        animation: subtleBounce 2.5s ease-in-out infinite;
    }

    .sound-bar {
        width: 3px;
        background-color: #f59e0b;
        border-radius: 999px;
        transition: height 0.06s ease;
    }
</style>

<div id="ai-speech-root" class="font-sans" style="font-family: inherit;">

    <!-- ===================================================================
         1. CENTER WELCOME STAGE: Fullscreen Blur with Frameless Avatar Hero
         =================================================================== -->
    <div id="ai-center-modal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/65 backdrop-blur-2xl transition-all duration-500 opacity-100 overflow-y-auto">

        <!-- Stage Container: Frameless & Centered (No Box) -->
        <div id="ai-center-stage" class="relative flex flex-col items-center justify-center w-full max-w-2xl my-auto transition-all duration-500">

            <!-- Top Close/Skip button (Subtle Glass Pill) -->
            <button type="button" id="ai-center-skip-btn"
                    class="absolute -top-2 right-2 sm:right-6 p-2 rounded-full bg-white/20 hover:bg-white/40 text-white backdrop-blur-md border border-white/30 transition shadow-lg z-30 cursor-pointer"
                    title="Skip to Showroom">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- =========================================================
                 REALISTIC AI PRESENTER (LARGE & FRAMELESS WITH RGB AURA)
                 ========================================================= -->
            @if($hasWelcomeVideo)
                <!-- Photorealistic AI Welcome Video (Google AI Studio / Veo / ElevenLabs) -->
                <div class="relative w-full max-w-[520px] aspect-video my-3 rounded-3xl overflow-hidden bg-slate-900 border-2 border-white/40 shadow-2xl flex items-center justify-center z-10">
                    <video id="ai-welcome-video-player"
                           src="{{ $welcomeVideoUrl }}"
                           class="w-full h-full object-cover"
                           playsinline
                           preload="auto"
                           controls></video>
                </div>
            @else
                <!-- Interactive 3D GLB Presenter: Large Frameless Avatar with RGB Glowing Halo -->
                <div id="ai-living-character" class="relative w-[340px] sm:w-[440px] md:w-[480px] h-[460px] sm:h-[540px] md:h-[600px] flex items-center justify-center select-none z-10 pointer-events-none">
                    <!-- Dynamic RGB Ambient Aura Glow behind avatar -->
                    <div class="absolute inset-0 m-auto w-[280px] h-[280px] sm:w-[380px] sm:h-[380px] md:w-[430px] md:h-[430px] rounded-full avatar-rgb-ambient blur-3xl opacity-80 pointer-events-none"></div>

                    <!-- Glowing RGB Base Ring / Podium -->
                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-52 sm:w-72 h-10 rounded-full avatar-rgb-halo blur-lg opacity-70 pointer-events-none"></div>

                    <!-- 3D WebGL Avatar Canvas Mount (Transparent, High-Res, Frameless) -->
                    <div id="ai-3d-avatar-canvas" class="relative w-full h-full flex items-center justify-center z-20 filter drop-shadow-[0_16px_32px_rgba(0,0,0,0.45)]"></div>
                </div>
            @endif

            <!-- Explore the Website Button: Revealed smoothly only when avatar completes speaking -->
            <div id="ai-explore-action-container"
                 class="hidden opacity-0 translate-y-6 pointer-events-none mt-4 flex items-center justify-center z-30 transition-all duration-500">
                <button type="button" id="ai-explore-btn"
                        class="group relative py-3.5 px-8 rounded-full text-white font-extrabold text-sm sm:text-base shadow-2xl hover:shadow-rose-500/40 transition-all duration-300 active:scale-95 flex items-center justify-center gap-2.5 cursor-pointer overflow-hidden animate-bounce-subtle"
                        style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $accentColor }});">
                    <span class="absolute inset-0 rounded-full avatar-rgb-halo opacity-50 blur-sm group-hover:opacity-90 transition"></span>
                    <span class="relative z-10 flex items-center gap-2 tracking-wide">
                        <span>Explore the Website</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </span>
                </button>
            </div>

        </div>
    </div>


    <!-- ===================================================================
         2. MINIMIZED FLOATING AVATAR: Bottom-Right Corner with RGB Glow Ring
         =================================================================== -->
    <div id="ai-floating-pill"
         class="hidden fixed bottom-6 right-4 sm:right-6 z-40 transition-all duration-400 opacity-0 scale-75">
        <button type="button" id="ai-pill-trigger-btn"
                class="group relative flex items-center gap-3 p-1.5 pr-4 rounded-full bg-white/95 backdrop-blur-md border border-slate-200/90 shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 focus:outline-none cursor-pointer">
            <!-- Avatar with RGB Halo Border -->
            <div class="relative p-0.5 rounded-full avatar-rgb-halo shrink-0 shadow-lg">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full overflow-hidden bg-slate-900 border-2 border-white flex items-center justify-center">
                    <img src="{{ $avatarUrl }}" alt="{{ $showroom->name }} AI Advisor" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                </div>
                <!-- Pulsing online status indicator -->
                <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
            </div>
            <div class="text-left hidden sm:block">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-black text-slate-900 leading-tight">AI Advisor</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black bg-rose-100 text-rose-700">Namaste</span>
                </div>
                <span class="text-[10px] text-slate-500 font-semibold block">Tap to chat & ask prices</span>
            </div>
            <span class="w-8 h-8 rounded-full bg-amber-50 border border-amber-200/80 flex items-center justify-center text-amber-700 shrink-0 group-hover:bg-amber-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </span>
        </button>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const centerModal = document.getElementById('ai-center-modal');
    const centerStage = document.getElementById('ai-center-stage');
    const floatingPill = document.getElementById('ai-floating-pill');
    const pillTriggerBtn = document.getElementById('ai-pill-trigger-btn');
    const exploreBtn = document.getElementById('ai-explore-btn');
    const exploreActionContainer = document.getElementById('ai-explore-action-container');
    const skipBtn = document.getElementById('ai-center-skip-btn');
    const replayBtn = document.getElementById('ai-center-replay-btn');
    const tapListenBtn = document.getElementById('ai-tap-listen-btn');
    const tapActionBox = document.getElementById('ai-one-tap-action-box');
    const visualizerBars = document.getElementById('ai-bars-visualizer');
    const statusLabel = document.getElementById('ai-status-label');
    const welcomeVideo = document.getElementById('ai-welcome-video-player');
    const avatarCanvasContainer = document.getElementById('ai-3d-avatar-canvas');
    const model3DUrl = "{{ $advisorModelUrl }}";

    let currentActiveSpokenText = "{{ addslashes($voiceGreeting) }}";

    // ── Mount 3D GLB Avatar with Namaste Pose ────────────────────────────
    function initShowroom3DAvatar() {
        if (!avatarCanvasContainer || window.showroom3DAvatar) return;

        if (window.create3DAvatar) {
            window.showroom3DAvatar = window.create3DAvatar(avatarCanvasContainer, {
                modelUrl: model3DUrl,
                interactive: true,
                cameraFov: 28,
                onLoad: function(avatar) {
                    console.log('[Showroom 3D] Namaste GLB Advisor mounted successfully');
                    if (isSpeaking && currentActiveSpokenText && avatar && typeof avatar.startSpeech === 'function') {
                        avatar.startSpeech(currentActiveSpokenText, 0.94);
                    }
                }
            });
        } else {
            setTimeout(initShowroom3DAvatar, 80);
        }
    }
    initShowroom3DAvatar();

    if (!centerModal) return;

    const voiceEnabled = {{ $voiceEnabled ? 'true' : 'false' }};
    const voiceLang = "{{ $voiceLang }}";
    const voiceGender = "{{ $voiceGender }}";
    const voicePitch = {{ $voicePitch }};
    const voiceName = "{{ addslashes($voiceName ?? '') }}";
    const voiceText = "{{ addslashes($voiceGreeting) }}";
    const voiceAudioUrl = "{{ $voiceAudioUrl }}";

    let audioElement = null;
    let isSpeaking = false;
    let hasPlayedOnce = false;

    // ── Real-time Audio-Driven Lip-Sync Engine ─────────────────────────────
    let audioCtx = null;
    let analyser = null;
    let freqData = null;
    let lipSyncRafId = null;
    let speechSynthAmplitude = 0; // synthetic amplitude fallback for SpeechSynthesisAPI
    let speechSynthRafId = null;

    function ensureAudioContext() {
        if (!audioCtx || audioCtx.state === 'closed') {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume().catch(() => {});
        }
    }

    function connectAnalyserToAudio(sourceNode) {
        if (!audioCtx) return;
        if (analyser) {
            try { analyser.disconnect(); } catch(e) {}
        }
        analyser = audioCtx.createAnalyser();
        analyser.fftSize = 256;
        analyser.smoothingTimeConstant = 0.6;
        freqData = new Uint8Array(analyser.frequencyBinCount);
        sourceNode.connect(analyser);
        analyser.connect(audioCtx.destination);
    }

    function startLipSyncLoop() {
        if (lipSyncRafId) cancelAnimationFrame(lipSyncRafId);
        const loop = () => {
            if (!isSpeaking) return;
            let amplitude = 0;
            if (analyser && freqData) {
                analyser.getByteFrequencyData(freqData);
                // Focus on vocal frequency range (roughly 300–3000 Hz within our FFT bins)
                const binStart = Math.floor(freqData.length * 0.05);
                const binEnd   = Math.floor(freqData.length * 0.45);
                let sum = 0;
                for (let i = binStart; i < binEnd; i++) sum += freqData[i];
                amplitude = sum / ((binEnd - binStart) * 255); // 0..1
            } else {
                // Fallback: synthetic sine-wave amplitude for Web Speech API
                amplitude = speechSynthAmplitude;
            }

            // Send real-time amplitude to 3D Avatar Viseme Engine
            if (window.showroom3DAvatar) {
                window.showroom3DAvatar.setSpeaking(true, amplitude, analyser ? 'audio' : 'speech');
            }

            lipSyncRafId = requestAnimationFrame(loop);
        };
        lipSyncRafId = requestAnimationFrame(loop);
    }

    function stopLipSyncLoop() {
        if (lipSyncRafId) { cancelAnimationFrame(lipSyncRafId); lipSyncRafId = null; }
        if (speechSynthRafId) { cancelAnimationFrame(speechSynthRafId); speechSynthRafId = null; }
        if (window.showroom3DAvatar) {
            window.showroom3DAvatar.setSpeaking(false, 0);
        }
    }

    // Synthetic oscillating amplitude for Web Speech API (no AudioContext access)
    function startSyntheticAmplitudeLoop() {
        let t = 0;
        const tick = () => {
            if (!isSpeaking) return;
            t += 0.18;
            // Multi-frequency blend mimics natural speech cadence
            speechSynthAmplitude = Math.max(0,
                0.35 * Math.abs(Math.sin(t * 2.1)) +
                0.25 * Math.abs(Math.sin(t * 3.7)) +
                0.15 * Math.abs(Math.sin(t * 5.3))
            );
            speechSynthRafId = requestAnimationFrame(tick);
        };
        speechSynthRafId = requestAnimationFrame(tick);
    }
    // ── End Lip-Sync Engine ────────────────────────────────────────────────

    if (welcomeVideo) {
        welcomeVideo.addEventListener('play', () => setLipSyncState(true, 'video'));
        welcomeVideo.addEventListener('pause', () => setLipSyncState(false));
        welcomeVideo.addEventListener('ended', () => setLipSyncState(false));
    }

    function isFemaleVoice(name) {
        const n = name.toLowerCase();
        return n.includes('female') || n.includes('woman') || n.includes('girl') ||
               n.includes('lekha') || n.includes('swara') || n.includes('veena') ||
               n.includes('samantha') || n.includes('zira') || n.includes('kavya') ||
               n.includes('priya') || n.includes('aditi') || n.includes('heera') ||
               n.includes('kalpana') || n.includes('geeta') || n.includes('neerja');
    }

    function isMaleVoice(name) {
        const n = name.toLowerCase();
        return n.includes('male') || n.includes('man') || n.includes('boy') ||
               n.includes('rishi') || n.includes('daniel') || n.includes('george') ||
               n.includes('madhav') || n.includes('hemant') || n.includes('david') ||
               n.includes('ravi') || n.includes('prabhat');
    }

    function isNaturalVoice(name) {
        const n = name.toLowerCase();
        return n.includes('natural') || n.includes('online') || n.includes('google') ||
               n.includes('neural') || n.includes('premium') || n.includes('enhanced') ||
               n.includes('siri');
    }

    function transliterateHinglishFallback(text) {
        if (!text || !/[a-zA-Z]/.test(text)) return text;
        const DICT = {
            'namaste': 'नमस्ते', 'namaskar': 'नमस्कार', 'pranam': 'प्रणाम',
            'royal': 'रॉयल', 'bike': 'बाइक', 'bikes': 'बाइक्स', 'motor': 'मोटर', 'motors': 'मोटर्स',
            'tvs': 'टीवीएस', 'honda': 'होंडा', 'hero': 'हीरो', 'yamaha': 'यामाहा', 'bajaj': 'बजाज',
            'suzuki': 'सुजुकी', 'bullet': 'बुलेट', 'rider': 'राइडर', 'raider': 'रेडर', 'apache': 'अपाचे',
            'jupiter': 'जुपिटर', 'scooter': 'स्कूटर', 'scooty': 'स्कूटी', 'gaadi': 'गाड़ी', 'gadi': 'गाड़ी',
            'apka': 'आपका', 'aapka': 'आपका', 'aapke': 'आपके', 'apke': 'आपके', 'aapki': 'आपकी', 'apki': 'आपकी',
            'hamare': 'हमारे', 'hamaare': 'हमारे', 'humare': 'हमारे', 'hamara': 'हमारा', 'hamaari': 'हमारी',
            'swagat': 'स्वागत', 'hai': 'है', 'hain': 'हैं', 'ish': 'इस', 'is': 'इस', 'ye': 'ये', 'yeh': 'यह',
            'showroom': 'शोरूम', 'sorum': 'शोरूम', 'me': 'में', 'mein': 'में', 'parr': 'पर', 'par': 'पर', 'pe': 'पे',
            'sabse': 'सबसे', 'badiya': 'बढ़िया', 'badhiya': 'बढ़िया', 'achha': 'अच्छा', 'accha': 'अच्छा', 'achhi': 'अच्छी',
            'bataiye': 'बताइए', 'batao': 'बताओ', 'aaj': 'आज', 'hum': 'हम', 'kya': 'क्या', 'kaise': 'कैसे',
            'sahayata': 'सहायता', 'madad': 'मदद', 'kar': 'कर', 'sakta': 'सकता', 'sakti': 'सकती', 'sakte': 'सकते',
            'hu': 'हूँ', 'hoon': 'हूँ', 'ho': 'हो', 'dhanyawad': 'धन्यवाद', 'dhanyavad': 'धन्यवाद', 'shukriya': 'शुक्रिया',
            'offer': 'ऑफर', 'offers': 'ऑफ़र्स', 'discount': 'डिस्काउंट', 'deal': 'डील', 'deals': 'डील्स',
            'finance': 'फाइनेंस', 'emi': 'ईएमआई', 'price': 'प्राइस', 'rate': 'रेट', 'booking': 'बुकिंग',
            'test': 'टेस्ट', 'ride': 'राइड', 'karo': 'करो', 'kariye': 'करिए', 'dekhiye': 'देखिए',
            'chahiye': 'चाहिए', 'lena': 'लेना', 'kharidna': 'खरीदना', 'service': 'सर्विस'
        };
        return text.replace(/\b[a-zA-Z]+\b/g, (match) => {
            const lower = match.toLowerCase();
            return DICT[lower] || match;
        });
    }

    function resolveVoice(voices) {
        if (!voices || voices.length === 0) return null;
        if (voiceName) {
            const exact = voices.find(v => v.name === voiceName);
            if (exact) return exact;
        }

        const hindiVoices = voices.filter(v => v.lang === 'hi-IN' || v.lang.replace('_', '-').startsWith('hi'));
        const indianVoices = voices.filter(v => v.lang === 'en-IN' || v.lang.replace('_', '-').startsWith('en-IN') || v.lang === 'en_IN');

        if (voiceGender === 'female') {
            const naturalHindi = hindiVoices.find(v => isFemaleVoice(v.name) && isNaturalVoice(v.name));
            if (naturalHindi) return naturalHindi;

            const anyHindiFemale = hindiVoices.find(v => isFemaleVoice(v.name));
            if (anyHindiFemale) return anyHindiFemale;

            const naturalIndian = indianVoices.find(v => isFemaleVoice(v.name) && isNaturalVoice(v.name));
            if (naturalIndian) return naturalIndian;

            const anyIndianFemale = indianVoices.find(v => isFemaleVoice(v.name));
            if (anyIndianFemale) return anyIndianFemale;

            return hindiVoices[0] || indianVoices[0] || voices[0];
        } else {
            const naturalHindiMale = hindiVoices.find(v => isMaleVoice(v.name) && isNaturalVoice(v.name));
            if (naturalHindiMale) return naturalHindiMale;

            const anyHindiMale = hindiVoices.find(v => isMaleVoice(v.name));
            if (anyHindiMale) return anyHindiMale;

            const naturalIndianMale = indianVoices.find(v => isMaleVoice(v.name) && isNaturalVoice(v.name));
            if (naturalIndianMale) return naturalIndianMale;

            const anyIndianMale = indianVoices.find(v => isMaleVoice(v.name));
            if (anyIndianMale) return anyIndianMale;

            const naturalHindi = hindiVoices.find(v => isNaturalVoice(v.name));
            if (naturalHindi) return naturalHindi;

            return hindiVoices[0] || indianVoices[0] || voices[0];
        }
    }

    function onSpeakingCompleted() {
        if (statusLabel) {
            statusLabel.textContent = "Welcome to {{ addslashes($showroom->name) }}";
            statusLabel.classList.remove('text-amber-800');
            statusLabel.classList.add('text-emerald-700');
        }
        if (exploreActionContainer) {
            exploreActionContainer.classList.remove('hidden', 'opacity-0', 'translate-y-6', 'pointer-events-none');
            exploreActionContainer.classList.add('flex', 'opacity-100', 'translate-y-0', 'pointer-events-auto');
        }
        if (tapActionBox) {
            tapActionBox.classList.add('hidden');
        }
    }

    // Synchronize Mouth Lip-Sync with Speech State
    function setLipSyncState(speaking, source) {
        isSpeaking = speaking;
        if (speaking) {
            if (visualizerBars) visualizerBars.classList.remove('hidden');
            if (statusLabel) {
                statusLabel.textContent = (source === 'video') ? "Playing AI Welcome Video..." : "Speaking Welcome Greeting...";
                statusLabel.classList.remove('text-slate-500', 'text-emerald-700');
                statusLabel.classList.add('text-amber-800');
            }
            if (exploreActionContainer) {
                exploreActionContainer.classList.add('hidden', 'opacity-0', 'translate-y-6', 'pointer-events-none');
                exploreActionContainer.classList.remove('flex', 'opacity-100', 'translate-y-0', 'pointer-events-auto');
            }
            // Start the real-time render loop
            if (source !== 'video') startSyntheticAmplitudeLoop();
            startLipSyncLoop();
        } else {
            if (visualizerBars) visualizerBars.classList.add('hidden');
            stopLipSyncLoop();
            onSpeakingCompleted();
        }
    }

    // Play Voice Speech (Web Speech API, studio audio file, or welcome video)
    function startVoiceSpeech(isUserGesture = false) {
        // Photorealistic AI Video Player
        if (welcomeVideo) {
            welcomeVideo.currentTime = 0;
            if (isUserGesture) {
                welcomeVideo.muted = false;
                const p = welcomeVideo.play();
                if (p !== undefined) {
                    p.then(() => {
                        hasPlayedOnce = true;
                        setLipSyncState(true);
                        if (tapActionBox) tapActionBox.classList.add('hidden');
                    }).catch(err => console.log('Video play error:', err));
                }
            } else {
                const p = welcomeVideo.play();
                if (p !== undefined) {
                    p.then(() => {
                        hasPlayedOnce = true;
                        setLipSyncState(true);
                        if (tapActionBox) tapActionBox.classList.add('hidden');
                    }).catch(() => {
                        welcomeVideo.muted = true;
                        welcomeVideo.play().catch(e => console.log('Muted video autoplay error:', e));
                        if (tapActionBox) tapActionBox.classList.remove('hidden');
                    });
                }
            }
            return;
        }

        if (!voiceEnabled) return;

        // Custom Studio Audio File — connected to Web Audio API for real-time lip-sync
        if (voiceAudioUrl) {
            ensureAudioContext();
            if (!audioElement) {
                audioElement = new Audio(voiceAudioUrl);
                audioElement.crossOrigin = 'anonymous';
                audioElement.addEventListener('play', () => {
                    setLipSyncState(true, 'audio');
                    if (window.showroom3DAvatar && typeof window.showroom3DAvatar.startSpeech === 'function') {
                        window.showroom3DAvatar.startSpeech(voiceText, 0.94);
                    }
                    // Connect analyser node for real amplitude data
                    if (audioCtx) {
                        try {
                            const src = audioCtx.createMediaElementSource(audioElement);
                            connectAnalyserToAudio(src);
                        } catch(e) {
                            // Already connected or CORS issue — fallback synthetic loop already started
                        }
                    }
                });
                audioElement.addEventListener('ended', () => {
                    if (window.showroom3DAvatar && typeof window.showroom3DAvatar.stopSpeech === 'function') {
                        window.showroom3DAvatar.stopSpeech();
                    }
                    setLipSyncState(false);
                });
                audioElement.addEventListener('pause', () => setLipSyncState(false));
                audioElement.addEventListener('error', () => setLipSyncState(false));
            }
            audioElement.currentTime = 0;
            const promise = audioElement.play();
            if (promise !== undefined) {
                promise.then(() => {
                    hasPlayedOnce = true;
                    if (tapActionBox) tapActionBox.classList.add('hidden');
                }).catch(err => {
                    console.log('Autoplay restricted by browser:', err);
                    if (tapActionBox) tapActionBox.classList.remove('hidden');
                });
            }
            return;
        }

        // Web Speech API Synthesis
        if ('speechSynthesis' in window) {
            try {
                if (window.speechSynthesis.speaking) {
                    window.speechSynthesis.cancel();
                }
                const voices = window.speechSynthesis.getVoices();
                const matchedVoice = resolveVoice(voices);

                let spokenText = voiceText;
                if (voiceLang.startsWith('hi') && /[a-zA-Z]/.test(spokenText) && (!matchedVoice || matchedVoice.lang.startsWith('hi'))) {
                    spokenText = transliterateHinglishFallback(spokenText);
                }
                currentActiveSpokenText = spokenText;

                const utterance = new SpeechSynthesisUtterance(spokenText);
                utterance.lang = matchedVoice ? matchedVoice.lang : voiceLang;
                utterance.pitch = (voicePitch && voicePitch >= 0.9 && voicePitch <= 1.1) ? voicePitch : 1.00;
                utterance.rate = 0.94;
                if (matchedVoice) utterance.voice = matchedVoice;

                utterance.onstart = () => {
                    hasPlayedOnce = true;
                    if (window.showroom3DAvatar && typeof window.showroom3DAvatar.startSpeech === 'function') {
                        window.showroom3DAvatar.startSpeech(spokenText, utterance.rate || 0.94);
                    }
                    setLipSyncState(true, 'speech');
                    if (tapActionBox) tapActionBox.classList.add('hidden');
                };
                utterance.onboundary = (event) => {
                    if (event.name === 'word') {
                        const text = utterance.text;
                        const word = text.slice(event.charIndex).split(/\s+/)[0] || '';
                        if (window.showroom3DAvatar && typeof window.showroom3DAvatar.pulseWord === 'function') {
                            window.showroom3DAvatar.pulseWord(word, event.charIndex);
                        }
                    }
                };
                utterance.onend = () => {
                    if (window.showroom3DAvatar && typeof window.showroom3DAvatar.stopSpeech === 'function') {
                        window.showroom3DAvatar.stopSpeech();
                    }
                    setLipSyncState(false);
                };
                utterance.onerror = (event) => {
                    console.warn('[AI Assistant] Utterance event error:', event ? event.error : 'unknown');
                    if (event && (event.error === 'not-allowed' || event.error === 'interrupted')) {
                        // Browser autoplay policy blocked programmatic speech before user interaction
                        // Do not cancel or stop speech state; wait for user gesture activation
                        return;
                    }
                    if (window.showroom3DAvatar && typeof window.showroom3DAvatar.stopSpeech === 'function') {
                        window.showroom3DAvatar.stopSpeech();
                    }
                    setLipSyncState(false);
                };

                window.speechSynthesis.speak(utterance);
            } catch (err) {
                console.warn('Speech error:', err);
            }
        }
    }

    // Open Center Welcome Screen
    function openCenterModal(triggerAudio = true) {
        if (floatingPill) {
            floatingPill.classList.remove('opacity-100', 'scale-100');
            floatingPill.classList.add('opacity-0', 'scale-75');
            setTimeout(() => floatingPill.classList.add('hidden'), 200);
        }
        if (centerStage) {
            centerStage.style.transform = '';
            centerStage.style.opacity = '';
        }
        centerModal.classList.remove('hidden');
        setTimeout(() => {
            centerModal.classList.remove('opacity-0');
            centerModal.classList.add('opacity-100');
        }, 10);

        if (exploreActionContainer) {
            exploreActionContainer.classList.add('hidden', 'opacity-0', 'translate-y-6', 'pointer-events-none');
            exploreActionContainer.classList.remove('flex', 'opacity-100', 'translate-y-0', 'pointer-events-auto');
        }

        if (triggerAudio && voiceEnabled) {
            startVoiceSpeech(false);
            // Safety timeout: if speech was blocked and user didn't tap, reveal button after 9s so user can proceed
            setTimeout(() => {
                if (!isSpeaking && !hasPlayedOnce) onSpeakingCompleted();
            }, 9000);
        } else {
            onSpeakingCompleted();
        }
    }

    // Minimize to Bottom-Right Floating Avatar
    function minimizeCenterModal() {
        try {
            if ('speechSynthesis' in window) window.speechSynthesis.cancel();
            if (audioElement) audioElement.pause();
            if (welcomeVideo) welcomeVideo.pause();
            if (window.showroom3DAvatar && typeof window.showroom3DAvatar.stopSpeech === 'function') {
                window.showroom3DAvatar.stopSpeech();
            }
            stopLipSyncLoop();
        } catch (err) {
            console.warn('[AI Assistant] Cleanup error:', err);
        }

        // Animate the center avatar smoothly shrinking down towards the bottom-right corner
        if (centerStage) {
            centerStage.style.transition = 'all 0.5s cubic-bezier(0.16, 1, 0.3, 1)';
            centerStage.style.transform = 'translate(36vw, 36vh) scale(0.12)';
            centerStage.style.opacity = '0';
        }
        centerModal.classList.remove('opacity-100');
        centerModal.classList.add('opacity-0');

        setTimeout(() => {
            centerModal.classList.add('hidden');
            if (centerStage) {
                centerStage.style.transition = '';
                centerStage.style.transform = '';
                centerStage.style.opacity = '';
            }
            if (floatingPill) {
                floatingPill.classList.remove('hidden');
                requestAnimationFrame(() => {
                    floatingPill.classList.remove('opacity-0', 'scale-75');
                    floatingPill.classList.add('opacity-100', 'scale-100');
                });
            }
        }, 460);
    }

    // Event Handlers
    if (exploreBtn) {
        exploreBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            minimizeCenterModal();
        });
    }
    if (skipBtn) {
        skipBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            minimizeCenterModal();
        });
    }

    if (pillTriggerBtn) {
        pillTriggerBtn.addEventListener('click', function () {
            openCenterModal(true);
            startVoiceSpeech(true);
        });
    }

    // Global user gesture activation to unlock speech across all modern browsers
    function globalUserGestureUnlock(e) {
        if (!centerModal || centerModal.classList.contains('hidden')) return;
        if (e.target && (e.target.closest('#ai-center-skip-btn') || e.target.closest('#ai-explore-btn'))) return;
        if ('speechSynthesis' in window && window.speechSynthesis.paused) {
            window.speechSynthesis.resume();
        }
        if (!isSpeaking && voiceEnabled) {
            startVoiceSpeech(true);
        }
    }
    window.addEventListener('click', globalUserGestureUnlock, { capture: true });
    window.addEventListener('pointerdown', globalUserGestureUnlock, { capture: true });
    window.addEventListener('touchstart', globalUserGestureUnlock, { capture: true, passive: true });
    window.addEventListener('keydown', globalUserGestureUnlock, { capture: true });

    // Handle asynchronous voice loading in Chrome / Safari
    if ('speechSynthesis' in window) {
        window.speechSynthesis.onvoiceschanged = function() {
            if (!hasPlayedOnce && voiceEnabled && !isSpeaking) {
                startVoiceSpeech(false);
            }
        };
    }

    // Initial Appearance in the Middle for Visitor
    setTimeout(() => {
        openCenterModal(true);
    }, 400);
});
</script>
@endpush
@endif
