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

    // Exact mouth anchor coordinates for cutouts
    $mouthLeft = $avatarType === 'female_saree' ? '49.0%' : '50.6%';
    $mouthTop = $avatarType === 'female_saree' ? '31.3%' : '24.0%';
    $mouthWidth = $avatarType === 'female_saree' ? '42px' : '46px';
    $mouthHeight = $avatarType === 'female_saree' ? '24px' : '26px';
@endphp

@if($aiAvailable && $aiSetting->is_enabled)
<!-- AI Character & Natural Lip-Sync Styles -->
<style>
    @keyframes avatarLifeBreathe {
        0%, 100% {
            transform: translateY(0) scale(1);
        }
        50% {
            transform: translateY(-8px) scale(1.015);
        }
    }

    @keyframes auraRadialPulse {
        0%, 100% {
            transform: scale(0.96);
            opacity: 0.45;
        }
        50% {
            transform: scale(1.08);
            opacity: 0.85;
        }
    }

    @keyframes mouthPhonemeSync {
        0% { transform: translate(-50%, -50%) scaleY(0.12) scaleX(0.85); }
        20% { transform: translate(-50%, -50%) scaleY(0.85) scaleX(1.1); }
        40% { transform: translate(-50%, -50%) scaleY(0.35) scaleX(0.95); }
        60% { transform: translate(-50%, -50%) scaleY(0.95) scaleX(1.15); }
        80% { transform: translate(-50%, -50%) scaleY(0.4) scaleX(0.9); }
        100% { transform: translate(-50%, -50%) scaleY(0.18) scaleX(0.88); }
    }

    @keyframes jawSpeechSync {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(2.5px); }
    }

    @keyframes naturalEyelidBlink {
        0%, 94%, 100% { opacity: 0; }
        96%, 98% { opacity: 0.95; }
    }

    @keyframes soundBarDance {
        0%, 100% { height: 4px; }
        50% { height: 16px; }
    }

    .animate-avatar-life {
        animation: avatarLifeBreathe 4.5s ease-in-out infinite;
    }

    .animate-aura-glow {
        animation: auraRadialPulse 3.5s ease-in-out infinite;
    }

    .avatar-mouth-active {
        animation: mouthPhonemeSync 0.26s ease-in-out infinite alternate;
    }

    .avatar-jaw-active {
        animation: jawSpeechSync 0.26s ease-in-out infinite alternate;
    }

    .sound-bar {
        width: 3px;
        background-color: #f59e0b;
        border-radius: 999px;
        animation: soundBarDance 0.8s ease-in-out infinite;
    }
    .sound-bar:nth-child(2) { animation-delay: 0.15s; }
    .sound-bar:nth-child(3) { animation-delay: 0.3s; }
    .sound-bar:nth-child(4) { animation-delay: 0.45s; }
</style>

<div id="ai-speech-root" class="font-sans" style="font-family: inherit;">

    <!-- ===================================================================
         1. CENTER WELCOME STAGE: Prominently shown in the middle on first visit
         =================================================================== -->
    <div id="ai-center-modal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 backdrop-blur-md transition-all duration-500 opacity-100">

        <!-- Outer Card Box: Pure Premium Light Theme -->
        <div class="relative w-full max-w-xl bg-white rounded-3xl border border-slate-200/80 shadow-2xl p-6 sm:p-8 flex flex-col items-center text-center overflow-hidden transform transition-all duration-500 scale-100">

            <!-- Top Close/Skip button -->
            <button type="button" id="ai-center-skip-btn"
                    class="absolute top-4 right-4 p-2 rounded-full text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition z-20 cursor-pointer"
                    title="Skip to Showroom">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Showroom Welcome Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold uppercase tracking-wider mb-2">
                <span>🙏 Namaste</span>
                <span class="w-1 h-1 rounded-full bg-rose-400"></span>
                <span>Welcome to {{ $showroom->name }}</span>
            </div>

            <!-- =========================================================
                 REALISTIC CUTOUT CHARACTER WITH NATURAL LIP-SYNCING
                 (NOT a static picture box - Cutout + Aura + Animated Mouth)
                 ========================================================= -->
            <div class="relative w-full max-w-[340px] h-[340px] sm:h-[380px] my-2 flex items-center justify-center select-none">

                <!-- Golden Radial Halo Aura (Blends character seamlessly into the canvas) -->
                <div class="absolute inset-0 m-auto w-64 h-64 sm:w-72 sm:h-72 rounded-full bg-gradient-to-tr from-amber-300/35 via-rose-300/25 to-cyan-300/25 blur-2xl animate-aura-glow pointer-events-none"></div>

                <!-- Living Breathing Character Container -->
                <div id="ai-living-character" class="relative h-full w-auto max-w-full flex items-end justify-center animate-avatar-life">

                    <!-- Transparent Cutout Avatar Image with Drop Shadow Depth -->
                    <img id="ai-character-cutout"
                         src="{{ $avatarUrl }}"
                         alt="{{ $showroom->name }} AI Advisor"
                         class="h-full w-auto object-contain max-h-[380px] pointer-events-none"
                         style="filter: drop-shadow(0 20px 25px rgba(15, 23, 42, 0.18));">

                    <!-- Organic Lip-Sync Mouth Mesh Overlay -->
                    <div id="ai-lip-sync-overlay"
                         class="absolute pointer-events-none transition-opacity duration-150 opacity-0"
                         style="left: {{ $mouthLeft }}; top: {{ $mouthTop }}; width: {{ $mouthWidth }}; height: {{ $mouthHeight }}; transform: translate(-50%, -50%);">

                        <!-- Morphing SVG Mouth Cavity (Phoneme articulation: Teeth, Tongue, Cavity) -->
                        <svg viewBox="0 0 50 30" class="w-full h-full overflow-visible" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Upper Lip Line -->
                            <path d="M 4 12 Q 25 7 46 12" stroke="#a34e44" stroke-width="2.5" stroke-linecap="round"/>
                            <!-- Open Mouth Cavity -->
                            <path d="M 5 12 Q 25 30 45 12 Z" fill="#3b0f11"/>
                            <!-- Upper Teeth Row -->
                            <path d="M 9 12 Q 25 18 41 12" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
                            <!-- Tongue -->
                            <ellipse cx="25" cy="18" rx="10" ry="5" fill="#cf4e58"/>
                            <!-- Lower Lip Highlight -->
                            <path d="M 6 13 Q 25 29 44 13" stroke="#b85b52" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <!-- Subtle Natural Eyelid Blink Overlay -->
                    <div id="ai-blink-overlay"
                         class="absolute pointer-events-none opacity-0"
                         style="left: {{ $mouthLeft }}; top: calc({{ $mouthTop }} - 5.5%); width: 70px; height: 18px; transform: translate(-50%, -50%); animation: naturalEyelidBlink 4.8s infinite;">
                        <div class="w-full h-[2px] bg-slate-900/40 rounded-full mx-auto"></div>
                    </div>
                </div>
            </div>

            <!-- Speech Audio Waves Status Indicator -->
            <div id="ai-speech-status-bar" class="flex items-center gap-2 mb-2">
                <div id="ai-bars-visualizer" class="hidden flex items-center gap-1 h-4">
                    <span class="sound-bar"></span>
                    <span class="sound-bar"></span>
                    <span class="sound-bar"></span>
                    <span class="sound-bar"></span>
                </div>
                <span id="ai-status-label" class="text-xs font-bold text-amber-700 uppercase tracking-wide">
                    Speaking Welcome Greeting
                </span>
            </div>

            <!-- Spoken Subtitle Text Bubble -->
            <p id="ai-speech-caption"
               class="text-xs sm:text-sm font-semibold text-slate-800 bg-slate-50 border border-slate-200/90 rounded-2xl p-3.5 max-w-lg shadow-inner leading-relaxed mb-4">
                "{{ $voiceGreeting }}"
            </p>

            <!-- 1-Tap Play / Autoplay Blocked Fallback Action -->
            <div id="ai-one-tap-action-box" class="w-full mb-3 {{ $voiceEnabled ? '' : 'hidden' }}">
                <button type="button" id="ai-tap-listen-btn"
                        class="w-full py-3 px-5 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 active:scale-98 cursor-pointer">
                    <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                    <span>🔊 सुनने के लिए टैप करें / Tap to Listen</span>
                </button>
            </div>

            <!-- Navigation Actions -->
            <div class="flex items-center gap-2.5 w-full">
                <!-- Replay Speech -->
                <button type="button" id="ai-center-replay-btn"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Replay</span>
                </button>

                <!-- Explore Showroom / Minimize to corner -->
                <button type="button" id="ai-explore-btn"
                        class="flex-1 py-2.5 px-4 rounded-xl text-white font-bold text-xs transition shadow-md hover:brightness-110 active:scale-98 flex items-center justify-center gap-2 cursor-pointer"
                        style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $accentColor }});">
                    <span>Explore Showroom / शोरूम देखें</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>

        </div>
    </div>


    <!-- ===================================================================
         2. MINIMIZED FLOATING AVATAR: Bottom-right corner trigger pill
         =================================================================== -->
    <div id="ai-floating-pill"
         class="hidden fixed bottom-6 right-4 sm:right-6 z-40">
        <button type="button" id="ai-pill-trigger-btn"
                class="group relative flex items-center gap-3 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 shadow-xl hover:scale-105 active:scale-95 transition-all duration-300 focus:outline-none cursor-pointer">
            <!-- Cutout Character Thumbnail with Living Aura -->
            <div class="relative w-10 h-10 rounded-full overflow-hidden bg-gradient-to-tr from-amber-100 to-rose-50 border border-amber-300 shadow-xs flex items-center justify-center shrink-0">
                <img src="{{ $avatarUrl }}" alt="{{ $showroom->name }} AI Advisor" class="h-full w-auto object-cover">
                <span class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 ring-2 ring-white animate-pulse"></span>
            </div>
            <div class="text-left hidden sm:block">
                <span class="text-xs font-black text-slate-900 block leading-tight">🙏 AI Advisor</span>
                <span class="text-[10px] text-amber-700 font-semibold block">Tap to hear welcome voice</span>
            </div>
            <span class="w-7 h-7 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700 shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
            </span>
        </button>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const centerModal = document.getElementById('ai-center-modal');
    const floatingPill = document.getElementById('ai-floating-pill');
    const pillTriggerBtn = document.getElementById('ai-pill-trigger-btn');
    const exploreBtn = document.getElementById('ai-explore-btn');
    const skipBtn = document.getElementById('ai-center-skip-btn');
    const replayBtn = document.getElementById('ai-center-replay-btn');
    const tapListenBtn = document.getElementById('ai-tap-listen-btn');
    const tapActionBox = document.getElementById('ai-one-tap-action-box');
    const lipSyncOverlay = document.getElementById('ai-lip-sync-overlay');
    const characterContainer = document.getElementById('ai-living-character');
    const visualizerBars = document.getElementById('ai-bars-visualizer');
    const statusLabel = document.getElementById('ai-status-label');

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

    function isFemaleVoice(name) {
        const n = name.toLowerCase();
        return n.includes('female') || n.includes('woman') || n.includes('girl') ||
               n.includes('lekha') || n.includes('swara') || n.includes('veena') ||
               n.includes('samantha') || n.includes('zira') || n.includes('kavya') ||
               n.includes('priya') || n.includes('aditi') || n.includes('heera') ||
               n.includes('kalpana') || n.includes('geeta');
    }

    function isMaleVoice(name) {
        const n = name.toLowerCase();
        return n.includes('male') || n.includes('man') || n.includes('boy') ||
               n.includes('rishi') || n.includes('daniel') || n.includes('george') ||
               n.includes('madhav') || n.includes('hemant') || n.includes('david') ||
               n.includes('ravi');
    }

    function resolveVoice(voices) {
        if (!voices || voices.length === 0) return null;
        if (voiceName) {
            const exact = voices.find(v => v.name === voiceName);
            if (exact) return exact;
        }

        const langPrefix = voiceLang.substring(0, 2);
        const matchingLangVoices = voices.filter(v => v.lang === voiceLang || v.lang.replace('_', '-').startsWith(langPrefix));

        if (voiceGender === 'female') {
            const fVoice = matchingLangVoices.find(v => isFemaleVoice(v.name));
            if (fVoice) return fVoice;
        } else {
            const mVoice = matchingLangVoices.find(v => isMaleVoice(v.name));
            if (mVoice) return mVoice;
        }

        return matchingLangVoices[0] || voices[0];
    }

    // Synchronize Mouth Lip-Sync with Speech State
    function setLipSyncState(speaking) {
        isSpeaking = speaking;
        if (speaking) {
            if (lipSyncOverlay) {
                lipSyncOverlay.classList.remove('opacity-0');
                lipSyncOverlay.classList.add('opacity-100', 'avatar-mouth-active');
            }
            if (characterContainer) {
                characterContainer.classList.add('avatar-jaw-active');
            }
            if (visualizerBars) visualizerBars.classList.remove('hidden');
            if (statusLabel) {
                statusLabel.textContent = "Speaking Welcome Greeting...";
                statusLabel.classList.remove('text-slate-500');
                statusLabel.classList.add('text-amber-700');
            }
        } else {
            if (lipSyncOverlay) {
                lipSyncOverlay.classList.remove('avatar-mouth-active', 'opacity-100');
                lipSyncOverlay.classList.add('opacity-0');
            }
            if (characterContainer) {
                characterContainer.classList.remove('avatar-jaw-active');
            }
            if (visualizerBars) visualizerBars.classList.add('hidden');
            if (statusLabel) {
                statusLabel.textContent = "AI Showroom Welcome";
                statusLabel.classList.remove('text-amber-700');
                statusLabel.classList.add('text-slate-500');
            }
        }
    }

    // Play Voice Speech (Web Speech API or studio audio file)
    function startVoiceSpeech(isUserGesture = false) {
        if (!voiceEnabled) return;

        // Custom Studio Audio File
        if (voiceAudioUrl) {
            if (!audioElement) {
                audioElement = new Audio(voiceAudioUrl);
                audioElement.addEventListener('play', () => setLipSyncState(true));
                audioElement.addEventListener('ended', () => setLipSyncState(false));
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
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(voiceText);
                utterance.lang = voiceLang;

                const isFemale = voiceGender === 'female';
                utterance.pitch = voicePitch || (isFemale ? 1.22 : 0.88);
                utterance.rate = isFemale ? 0.98 : 0.94;

                const voices = window.speechSynthesis.getVoices();
                const matchedVoice = resolveVoice(voices);
                if (matchedVoice) utterance.voice = matchedVoice;

                utterance.onstart = () => {
                    hasPlayedOnce = true;
                    setLipSyncState(true);
                    if (tapActionBox) tapActionBox.classList.add('hidden');
                };
                utterance.onend = () => setLipSyncState(false);
                utterance.onerror = () => setLipSyncState(false);

                window.speechSynthesis.speak(utterance);

                // Check if browser blocked speech before user click
                if (!isUserGesture && window.speechSynthesis.paused) {
                    if (tapActionBox) tapActionBox.classList.remove('hidden');
                }
            } catch (err) {
                console.log('Speech error:', err);
                if (tapActionBox) tapActionBox.classList.remove('hidden');
            }
        }
    }

    // Open Center Welcome Screen
    function openCenterModal(triggerAudio = true) {
        if (floatingPill) floatingPill.classList.add('hidden');
        centerModal.classList.remove('hidden');
        setTimeout(() => {
            centerModal.classList.remove('opacity-0');
            centerModal.classList.add('opacity-100');
        }, 10);

        if (triggerAudio) {
            startVoiceSpeech(false);
        }
    }

    // Minimize to Bottom-Right Floating Avatar
    function minimizeCenterModal() {
        if ('speechSynthesis' in window) window.speechSynthesis.cancel();
        if (audioElement) audioElement.pause();
        setLipSyncState(false);

        centerModal.classList.remove('opacity-100');
        centerModal.classList.add('opacity-0');

        setTimeout(() => {
            centerModal.classList.add('hidden');
            if (floatingPill) floatingPill.classList.remove('hidden');
        }, 300);
    }

    // Event Handlers
    if (exploreBtn) exploreBtn.addEventListener('click', minimizeCenterModal);
    if (skipBtn) skipBtn.addEventListener('click', minimizeCenterModal);

    if (replayBtn) {
        replayBtn.addEventListener('click', function () {
            startVoiceSpeech(true);
        });
    }

    if (tapListenBtn) {
        tapListenBtn.addEventListener('click', function () {
            startVoiceSpeech(true);
        });
    }

    if (pillTriggerBtn) {
        pillTriggerBtn.addEventListener('click', function () {
            openCenterModal(true);
            startVoiceSpeech(true);
        });
    }

    // Single Tap Anywhere on Center Modal starts speech if browser had blocked autoplay
    centerModal.addEventListener('click', function (e) {
        if (e.target.closest('#ai-center-skip-btn') || e.target.closest('#ai-explore-btn')) return;
        if (!hasPlayedOnce && voiceEnabled) {
            startVoiceSpeech(true);
        }
    }, { once: true });

    // Initial Appearance in the Middle for First-Time Visitor (or QR scan ?welcome=1)
    setTimeout(() => {
        openCenterModal(true);
    }, 400);
});
</script>
@endpush
@endif
