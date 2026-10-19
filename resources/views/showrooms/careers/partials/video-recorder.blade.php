{{-- Modern 1-Minute Video Pitch Recorder Partial --}}
<div x-data="careerVideoRecorder()" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div>
            <label class="block text-xs font-bold text-slate-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </span>
                <span>1-Minute Video Pitch</span>
                <span class="px-2 py-0.5 rounded-md bg-rose-100/70 text-rose-700 text-[10px] font-bold uppercase tracking-wider">Recommended</span>
            </label>
            <p class="text-[11px] text-slate-500 mt-1">
                Record a quick 60-second video introduction, preview it with sound, and submit with your application.
            </p>
        </div>
        
        <!-- Mode Switcher Tabs -->
        <div class="inline-flex items-center bg-slate-100 p-1 rounded-xl text-xs font-semibold text-slate-600 shrink-0">
            <button type="button"
                    @click="setMode('record')"
                    :class="mode === 'record' ? 'bg-white text-rose-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 font-bold">
                <span class="w-2 h-2 rounded-full" :class="mode === 'record' ? 'bg-rose-500 animate-pulse' : 'bg-slate-400'"></span>
                Record Live
            </button>
            <button type="button"
                    @click="setMode('upload')"
                    :class="mode === 'upload' ? 'bg-white text-rose-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 font-bold">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload File
            </button>
        </div>
    </div>

    <!-- Hidden Native Inputs for Form Submission -->
    <input type="file"
           id="video_file"
           name="video_file"
           accept="video/*"
           class="sr-only"
           x-ref="fileInput"
           @change="handleManualFile($event)">
    <input type="hidden" name="video_base64" id="video_base64" :value="videoBase64">
    <input type="hidden" name="video_duration_seconds" :value="durationRecorded">

    <!-- RECORD LIVE MODE -->
    <div x-show="mode === 'record'" class="space-y-3">

        <!-- 1. INITIAL IDLE STATE (Camera not activated yet) -->
        <div x-show="state === 'idle'" class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/70 p-6 sm:p-8 text-center hover:border-rose-300 transition">
            <div class="w-14 h-14 rounded-2xl bg-white shadow-xs text-rose-600 flex items-center justify-center mx-auto mb-3 border border-rose-100">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <h4 class="text-sm font-bold text-slate-800 mb-1">Record Your 60-Second Video Intro</h4>
            <p class="text-xs text-slate-500 max-w-md mx-auto mb-5 leading-relaxed">
                Introduce yourself, your passion for motorcycles, and why you would love to join {{ $showroom->name }}. You can preview and re-record before submitting!
            </p>
            <button type="button"
                    @click="enableCamera()"
                    :disabled="isInitializing"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 disabled:opacity-50 text-white text-xs font-bold shadow-md shadow-rose-600/20 transition active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="isInitializing ? 'Connecting Camera...' : 'Turn On Camera & Mic'"></span>
            </button>
        </div>

        <!-- 2. CAMERA READY / LIVE RECORDING STATE -->
        <div x-show="state === 'ready' || state === 'recording'" class="relative rounded-2xl overflow-hidden bg-slate-950 border border-slate-300 shadow-inner">
            <!-- Mirrored live video feed for natural user view -->
            <video x-ref="liveVideo"
                   autoplay
                   playsinline
                   muted
                   class="w-full aspect-video object-cover bg-black"
                   style="transform: scaleX(-1);"></video>

            <!-- Top Overlays -->
            <div class="absolute top-3 left-3 right-3 flex items-center justify-between pointer-events-none z-10">
                <template x-if="state === 'recording'">
                    <div class="flex items-center gap-2 bg-rose-600 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-lg animate-pulse">
                        <span class="w-2.5 h-2.5 rounded-full bg-white"></span>
                        <span>RECORDING &bull; <span x-text="formatTime(timeRemaining)">01:00</span></span>
                    </div>
                </template>
                <template x-if="state === 'ready'">
                    <div class="flex items-center gap-2 bg-slate-900/90 text-white text-[11px] font-semibold px-3 py-1 rounded-full backdrop-blur-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Camera Active (60s Max)</span>
                    </div>
                </template>

                <div class="text-[11px] font-mono font-bold text-white bg-black/70 px-2.5 py-1 rounded-lg backdrop-blur-xs">
                    <span x-text="formatTime(timeElapsed)">00:00</span> / 01:00
                </div>
            </div>

            <!-- Animated Progress Bar during recording -->
            <div x-show="state === 'recording'" class="absolute bottom-0 left-0 right-0 h-2 bg-white/20">
                <div class="h-full bg-rose-500 transition-all duration-200 ease-linear" :style="`width: ${(timeElapsed / 60) * 100}%`"></div>
            </div>

            <!-- Bottom Floating Action Controls -->
            <div class="absolute bottom-4 left-0 right-0 flex items-center justify-center gap-3 z-10 px-4">
                <template x-if="state === 'ready'">
                    <button type="button"
                            @click="startRecording()"
                            class="px-6 py-3 rounded-full bg-rose-600 hover:bg-rose-700 text-white font-black text-xs uppercase tracking-wider shadow-xl shadow-rose-900/40 flex items-center gap-2 transition hover:scale-105 active:scale-95">
                        <span class="w-3.5 h-3.5 rounded-full bg-white"></span>
                        <span>Start 1-Min Recording</span>
                    </button>
                </template>

                <template x-if="state === 'recording'">
                    <button type="button"
                            @click="stopRecording()"
                            class="px-6 py-3 rounded-full bg-white hover:bg-slate-100 text-rose-600 font-black text-xs uppercase tracking-wider shadow-xl flex items-center gap-2 transition hover:scale-105 active:scale-95">
                        <span class="w-3.5 h-3.5 rounded-xs bg-rose-600"></span>
                        <span>Stop & Preview Video</span>
                    </button>
                </template>

                <button type="button"
                        x-show="state === 'ready'"
                        @click="cancelCamera()"
                        class="px-4 py-2 rounded-full bg-slate-900/80 hover:bg-slate-900 text-slate-200 text-xs font-semibold backdrop-blur-xs transition">
                    Cancel
                </button>
            </div>
        </div>

        <!-- 3. PREVIEW & PLAYBACK REVIEW STATE -->
        <div x-show="state === 'preview'" class="space-y-3">
            <div class="rounded-2xl overflow-hidden bg-slate-950 border border-slate-300 shadow-md relative group aspect-video flex items-center justify-center cursor-pointer select-none"
                 @click="togglePlay()">
                <!-- Video Element with poster thumbnail -->
                <video x-ref="previewVideo"
                       :src="previewUrl"
                       :poster="previewPoster"
                       controls
                       playsinline
                       preload="auto"
                       @play="isPlaying = true"
                       @pause="isPlaying = false"
                       @ended="isPlaying = false"
                       class="w-full h-full object-contain bg-black"></video>

                <!-- Center Big Play Button Overlay when paused -->
                <div x-show="!isPlaying"
                     class="absolute inset-0 m-auto w-16 h-16 rounded-full bg-rose-600/90 text-white flex items-center justify-center shadow-2xl backdrop-blur-xs transition hover:scale-110 active:scale-95 z-20 pointer-events-none"
                     title="Click to play video pitch with audio">
                    <svg class="w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>

                <!-- Top Status Badge -->
                <div class="absolute top-3 left-3 bg-emerald-600/90 backdrop-blur-xs text-white text-[11px] font-bold px-3 py-1 rounded-full flex items-center gap-1.5 shadow-md pointer-events-none z-10">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Pitch Recorded (<span x-text="durationRecorded"></span>s)</span>
                </div>
            </div>

            <!-- Preview Actions Toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-xs text-slate-700 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <span class="font-bold text-slate-900 block">Video Attached & Ready</span>
                        <span class="text-[11px] text-slate-500">Click play above to preview your pitch with audio before submitting.</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button"
                            @click.stop="togglePlay()"
                            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                        <template x-if="!isPlaying">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                        <template x-if="isPlaying">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                        <span x-text="isPlaying ? 'Pause' : 'Play with Sound'"></span>
                    </button>
                    <button type="button"
                            @click="rerecord()"
                            class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Re-record
                    </button>
                    <button type="button"
                            @click="discardVideo()"
                            class="px-2.5 py-2 rounded-xl text-rose-600 hover:bg-rose-50 text-xs font-bold transition"
                            title="Remove video">
                        Remove
                    </button>
                </div>
            </div>
        </div>

        <!-- CAMERA PERMISSION ERROR -->
        <div x-show="errorMessage" class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 space-y-2">
            <div class="flex items-start gap-2.5">
                <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="font-bold" x-text="errorMessage"></p>
                    <p class="text-[11px] text-rose-600 mt-0.5">You can allow camera permissions or switch to the "Upload File" tab to upload a recorded video.</p>
                </div>
            </div>
            <button type="button"
                    @click="setMode('upload')"
                    class="px-3 py-1.5 rounded-lg bg-white border border-rose-200 text-rose-700 font-bold text-xs hover:bg-rose-100/50 transition">
                Switch to Upload File &rarr;
            </button>
        </div>

    </div>

    <!-- UPLOAD FILE MODE -->
    <div x-show="mode === 'upload'" class="space-y-3">
        <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50/70 p-6 text-center hover:border-rose-400 hover:bg-white transition cursor-pointer"
             @click="$refs.fileInput.click()">
            <div class="w-12 h-12 rounded-xl bg-white text-slate-600 shadow-xs flex items-center justify-center mx-auto mb-2 border border-slate-200">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-800">
                <span x-text="uploadedFileName ? uploadedFileName : 'Choose a video file from your device'"></span>
            </p>
            <p class="text-[11px] text-slate-400 mt-1">MP4, WebM, QuickTime up to 50MB (~60s recommended)</p>
            <button type="button" class="mt-3 px-4 py-1.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold shadow-xs">
                Browse Files
            </button>
        </div>

        <template x-if="uploadedFileUrl">
            <div class="rounded-2xl overflow-hidden bg-black border border-slate-300 shadow-sm relative">
                <video :src="uploadedFileUrl" controls playsinline preload="auto" class="w-full aspect-video bg-black object-contain"></video>
            </div>
        </template>
    </div>

    <!-- Video Attached Status Confirmation Pill -->
    <template x-if="hasAttachedVideo">
        <div class="mt-3 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="font-bold">Video Pitch Attached:</span>
                <span class="font-mono text-[11px]" x-text="attachedVideoInfo"></span>
            </div>
            <span class="text-[10px] uppercase font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">Ready to Submit</span>
        </div>
    </template>

    @error('video_file')
        <p class="text-[11px] text-rose-600 mt-2 font-semibold">{{ $message }}</p>
    @enderror
</div>

<script>
function careerVideoRecorder() {
    return {
        mode: 'record', // 'record' | 'upload'
        state: 'idle', // 'idle' | 'ready' | 'recording' | 'preview'
        isInitializing: false,
        isPlaying: false,
        mediaStream: null,
        mediaRecorder: null,
        recordedChunks: [],
        recordedBlob: null,
        videoBase64: '',
        previewUrl: null,
        previewPoster: null,
        durationRecorded: 0,
        timeElapsed: 0,
        timeRemaining: 60,
        timerInterval: null,
        errorMessage: null,
        uploadedFileName: '',
        uploadedFileUrl: null,

        init() {
            this.$nextTick(() => {
                const form = this.$el.closest('form');
                if (form) {
                    form.addEventListener('submit', () => {
                        const el = document.getElementById('video_base64');
                        if (this.videoBase64 && el && !el.value) {
                            el.value = this.videoBase64;
                        }
                    });
                }
            });
        },

        get hasAttachedVideo() {
            return (this.state === 'preview' && (this.recordedBlob !== null || this.videoBase64 !== '')) || (this.mode === 'upload' && this.uploadedFileName !== '');
        },

        get attachedVideoInfo() {
            if (this.state === 'preview' && this.recordedBlob) {
                const sizeMb = (this.recordedBlob.size / (1024 * 1024)).toFixed(1);
                return `${this.durationRecorded}s pitch (${sizeMb} MB)`;
            }
            if (this.mode === 'upload' && this.uploadedFileName) {
                return this.uploadedFileName;
            }
            return '';
        },

        setMode(newMode) {
            if (this.mode === newMode) return;
            if (this.state === 'recording') {
                this.stopRecording();
            }
            this.mode = newMode;
        },

        async enableCamera() {
            this.errorMessage = null;
            this.isInitializing = true;

            try {
                // 1. Transition state to ready first so container is visible in DOM
                this.state = 'ready';
                await this.$nextTick();

                // 2. Request user media with graceful audio fallback
                let stream = null;
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        audio: true,
                        video: {
                            facingMode: 'user',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        }
                    });
                } catch (audioErr) {
                    console.warn('Audio+Video failed, requesting Video only:', audioErr);
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        }
                    });
                }

                this.mediaStream = stream;

                // 3. Attach stream to live video element and explicitly play
                const liveEl = this.$refs.liveVideo;
                if (liveEl) {
                    liveEl.srcObject = this.mediaStream;
                    liveEl.muted = true; // Avoid audio feedback
                    try {
                        await liveEl.play();
                    } catch (playErr) {
                        console.warn('liveEl.play error:', playErr);
                    }
                }

                this.timeElapsed = 0;
                this.timeRemaining = 60;
            } catch (err) {
                console.error('Camera access error:', err);
                this.errorMessage = 'Camera / Microphone access failed: ' + (err.message || 'Permission denied or device not found.');
                this.state = 'idle';
                this.stopTracks();
            } finally {
                this.isInitializing = false;
            }
        },

        cancelCamera() {
            this.stopTracks();
            this.state = 'idle';
        },

        stopTracks() {
            if (this.mediaStream) {
                this.mediaStream.getTracks().forEach(track => {
                    try { track.stop(); } catch(e) {}
                });
                this.mediaStream = null;
            }
            if (this.$refs.liveVideo) {
                this.$refs.liveVideo.srcObject = null;
            }
        },

        startRecording() {
            if (!this.mediaStream) return;
            this.recordedChunks = [];
            this.timeElapsed = 0;
            this.timeRemaining = 60;

            // Pick supported container type, prioritizing WebM for reliable Chromium & Firefox recording
            const mimeCandidates = [
                'video/webm;codecs=vp8,opus',
                'video/webm;codecs=vp9,opus',
                'video/webm',
                'video/mp4;codecs=avc1,mp4a.40.2',
                'video/mp4'
            ];
            let selectedMime = mimeCandidates.find(m => window.MediaRecorder && MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported(m)) || '';

            const options = selectedMime
                ? { mimeType: selectedMime, videoBitsPerSecond: 1200000 }
                : { videoBitsPerSecond: 1200000 };

            try {
                this.mediaRecorder = new MediaRecorder(this.mediaStream, options);
            } catch (e) {
                this.mediaRecorder = new MediaRecorder(this.mediaStream);
            }

            this.mediaRecorder.ondataavailable = (event) => {
                if (event.data && event.data.size > 0) {
                    this.recordedChunks.push(event.data);
                }
            };

            this.mediaRecorder.onstop = () => {
                const mimeType = this.mediaRecorder.mimeType || selectedMime || 'video/webm';
                this.recordedBlob = new Blob(this.recordedChunks, { type: mimeType });
                this.durationRecorded = Math.max(1, this.timeElapsed);

                // Stop live camera tracks
                this.stopTracks();

                if (this.previewUrl) {
                    URL.revokeObjectURL(this.previewUrl);
                }
                this.previewUrl = URL.createObjectURL(this.recordedBlob);

                // Convert Blob to Base64 for guaranteed form submission across all browsers
                const reader = new FileReader();
                reader.onloadend = () => {
                    this.videoBase64 = reader.result;
                    const el = document.getElementById('video_base64');
                    if (el) el.value = reader.result;
                };
                reader.readAsDataURL(this.recordedBlob);

                // Also populate native file input via DataTransfer as secondary mechanism
                this.populateFileInput(this.recordedBlob);

                // Switch UI to preview mode
                this.state = 'preview';
                this.isPlaying = false;

                // Assign to preview player element and load properly
                this.$nextTick(() => {
                    const previewEl = this.$refs.previewVideo;
                    if (previewEl) {
                        previewEl.src = this.previewUrl;
                        previewEl.controls = true;

                        // Fix Chrome's MediaRecorder WebM duration Infinity bug
                        previewEl.onloadedmetadata = () => {
                            if (previewEl.duration === Infinity) {
                                previewEl.currentTime = 1e101;
                                previewEl.ontimeupdate = () => {
                                    previewEl.ontimeupdate = null;
                                    previewEl.currentTime = 0;
                                };
                            }
                        };
                        previewEl.load();
                    }
                });
            };

            this.mediaRecorder.start(250); // Record in 250ms chunks
            this.state = 'recording';

            // 60-second countdown timer
            const startTimestamp = Date.now();
            this.timerInterval = setInterval(() => {
                const elapsed = Math.floor((Date.now() - startTimestamp) / 1000);
                this.timeElapsed = Math.min(60, elapsed);
                this.timeRemaining = Math.max(0, 60 - this.timeElapsed);

                if (this.timeElapsed >= 60) {
                    this.stopRecording();
                }
            }, 500);
        },

        capturePosterSnapshot() {
            try {
                const live = this.$refs.liveVideo;
                if (live && live.videoWidth > 0 && live.videoHeight > 0) {
                    const canvas = document.createElement('canvas');
                    canvas.width = live.videoWidth;
                    canvas.height = live.videoHeight;
                    const ctx = canvas.getContext('2d');
                    // Unmirror the image for a natural recorded look
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(live, 0, 0, canvas.width, canvas.height);
                    this.previewPoster = canvas.toDataURL('image/jpeg', 0.92);
                }
            } catch (err) {
                console.warn('Poster snapshot error:', err);
            }
        },

        stopRecording() {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
                this.timerInterval = null;
            }

            // Capture poster while live webcam feed is still active!
            this.capturePosterSnapshot();

            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                this.mediaRecorder.stop();
            }
        },

        togglePlay() {
            const previewEl = this.$refs.previewVideo;
            if (!previewEl) return;
            if (previewEl.paused || previewEl.ended) {
                this.playPreviewWithSound();
            } else {
                previewEl.pause();
                this.isPlaying = false;
            }
        },

        playPreviewWithSound() {
            const previewEl = this.$refs.previewVideo;
            if (previewEl) {
                previewEl.muted = false;
                if (previewEl.ended) {
                    previewEl.currentTime = 0;
                }
                const playPromise = previewEl.play();
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        this.isPlaying = true;
                    }).catch(e => {
                        console.warn('Play preview with sound error, retrying muted:', e);
                        previewEl.muted = true;
                        previewEl.play().then(() => {
                            this.isPlaying = true;
                            setTimeout(() => { previewEl.muted = false; }, 150);
                        }).catch(mutedErr => {
                            console.error('Playback failed completely:', mutedErr);
                        });
                    });
                }
            }
        },

        populateFileInput(blob) {
            try {
                const ext = blob.type.includes('mp4') ? 'mp4' : 'webm';
                const file = new File([blob], `video_pitch_${Date.now()}.${ext}`, { type: blob.type });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                this.$refs.fileInput.files = dataTransfer.files;
            } catch (err) {
                console.warn('DataTransfer File population error:', err);
            }
        },

        rerecord() {
            this.discardVideo();
            this.enableCamera();
        },

        discardVideo() {
            this.stopRecording();
            this.stopTracks();
            if (this.previewUrl) {
                URL.revokeObjectURL(this.previewUrl);
                this.previewUrl = null;
            }
            this.previewPoster = null;
            this.recordedChunks = [];
            this.recordedBlob = null;
            this.videoBase64 = '';
            const el = document.getElementById('video_base64');
            if (el) el.value = '';
            this.durationRecorded = 0;
            this.timeElapsed = 0;
            this.timeRemaining = 60;
            this.state = 'idle';
            this.isPlaying = false;
            this.$refs.fileInput.value = '';
            if (this.$refs.previewVideo) {
                this.$refs.previewVideo.src = '';
                this.$refs.previewVideo.poster = '';
            }
            this.uploadedFileName = '';
            this.uploadedFileUrl = null;
        },

        handleManualFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.uploadedFileName = file.name;
            if (this.uploadedFileUrl) {
                URL.revokeObjectURL(this.uploadedFileUrl);
            }
            this.uploadedFileUrl = URL.createObjectURL(file);
            this.durationRecorded = 60;

            const reader = new FileReader();
            reader.onloadend = () => {
                this.videoBase64 = reader.result;
                const hiddenEl = document.getElementById('video_base64');
                if (hiddenEl) hiddenEl.value = reader.result;
            };
            reader.readAsDataURL(file);
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }
    };
}
</script>
