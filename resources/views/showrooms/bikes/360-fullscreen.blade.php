@extends('layouts.app')

@section('title', '360° Studio - ' . $bike->name . ' - ' . $showroom->name)

@section('content')
<div id="studio-360-viewport" class="fixed inset-0 bg-slate-100 text-slate-900 flex flex-col font-sans z-50 selection:bg-cyan-500 selection:text-white select-none overflow-hidden">

    <!-- Ambient Studio Spotlight Background Effects -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-cyan-500/10 blur-[140px]"></div>
        <div class="absolute bottom-1/4 left-1/2 -translate-x-1/2 w-[700px] h-[250px] rounded-full bg-slate-300/40 blur-[90px]"></div>
        <!-- Studio Floor Turntable Circular Grid -->
        <div class="absolute bottom-16 sm:bottom-24 left-1/2 -translate-x-1/2 w-[80vw] max-w-4xl h-48 rounded-[100%] border border-cyan-500/25 bg-gradient-to-t from-cyan-100/40 to-transparent pointer-events-none transform -rotate-x-60"></div>
    </div>

    <!-- Top Navigation Dock -->
    <header class="relative z-30 border-b border-slate-200 bg-white/90 backdrop-blur-md px-4 sm:px-8 py-3 sm:py-4 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-3 sm:gap-4">
            <a href="{{ route('showrooms.bikes.show', [$showroom, $bike]) }}"
               class="p-2 sm:px-3.5 sm:py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 hover:text-slate-900 text-xs font-semibold flex items-center gap-2 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span class="hidden sm:inline">Exit 360 Studio</span>
            </a>

            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-cyan-50 text-cyan-700 border border-cyan-200">
                        Interactive 360° Studio
                    </span>
                    <span class="text-xs text-slate-500 font-mono hidden md:inline">{{ $showroom->name }}</span>
                </div>
                <h1 class="text-base sm:text-xl font-black font-display text-slate-900 tracking-tight leading-none mt-0.5">
                    {{ $bike->name }} <span class="text-slate-500 font-normal">({{ $bike->model_year }})</span>
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <div class="text-right hidden sm:block">
                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider block">Price</span>
                <span class="font-mono font-black text-slate-900 text-base text-cyan-700">{{ $bike->formatted_price }}</span>
            </div>

            <a href="{{ route('showrooms.enquiries.create', ['showroom' => $showroom, 'bike' => $bike->id]) }}"
               class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-md shadow-rose-600/20">
                Book Test Ride
            </a>

            <button type="button" id="btn-fullscreen-toggle"
                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 hover:text-slate-900 transition" title="Toggle Fullscreen">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            </button>
        </div>
    </header>

    <!-- Center Interactive Turntable Stage -->
    <main id="turntable-stage" class="relative flex-1 flex items-center justify-center cursor-grab active:cursor-grabbing overflow-hidden">

        @if($bike->viewer_type === 'gltf_model')
            @php
                $glbModelUrl = !empty($bike->model_3d_path)
                    ? $bike->model_3d_path
                    : asset('models/motorcycle.glb');
            @endphp
            <div class="relative w-full h-full flex items-center justify-center">
                <model-viewer
                    src="{{ $glbModelUrl }}"
                    alt="{{ $bike->name }} 3D WebGL Model"
                    camera-controls
                    touch-action="pan-y"
                    auto-rotate
                    rotation-per-second="25deg"
                    shadow-intensity="1.6"
                    shadow-softness="0.8"
                    exposure="1.05"
                    camera-orbit="0deg 75deg 105%"
                    min-camera-orbit="auto auto 40%"
                    max-camera-orbit="auto auto 220%"
                    class="w-full h-full cursor-grab active:cursor-grabbing z-10"
                    style="width: 100%; height: 100%; outline: none;"
                >
                    @foreach($hotspots as $spot)
                        <button class="view-3d-hotspot" slot="hotspot-{{ $loop->index }}"
                                data-position="{{ (($spot['x_pct'] ?? 50) - 50) / 40 }} 0.75 {{ ((50 - ($spot['y_pct'] ?? 50))) / 40 }}"
                                data-normal="0 1 0">
                            <div class="hotspot-3d-pin">
                                <div class="hotspot-3d-dot"></div>
                                <div class="hotspot-3d-card">
                                    <span class="font-bold text-cyan-800 text-xs block">{{ $spot['title'] }}</span>
                                    @if(!empty($spot['description']))
                                        <span class="text-[10px] text-slate-500 block">{{ $spot['description'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </button>
                    @endforeach
                </model-viewer>

                <div id="fullscreen-drag-hint"
                     class="absolute top-6 left-1/2 -translate-x-1/2 px-5 py-2 rounded-full bg-white/90 border border-slate-300 text-xs font-semibold text-slate-700 shadow-sm flex items-center gap-2 pointer-events-none z-20">
                    <svg class="w-4 h-4 text-cyan-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    <span>Drag horizontally or vertically to orbit 360° &bull; Pinch to zoom</span>
                </div>
            </div>
        @elseif($bike->viewer_type === 'video_loop' && !empty($bike->model_3d_path))
            <!-- Fullscreen Video Loop Player -->
            <div class="relative w-full h-full max-w-6xl max-h-[85vh] flex items-center justify-center p-4">
                <video src="{{ $bike->model_3d_path }}"
                       class="w-full h-full object-contain rounded-2xl shadow-lg"
                       autoplay
                       loop
                       muted
                       playsinline
                       controls>
                </video>
            </div>
        @elseif($frames->isNotEmpty())
            <!-- 360 Turntable Frame Sequence -->
            <div id="turntable-viewport" class="relative w-full h-full max-w-6xl max-h-[75vh] flex items-center justify-center p-4 transition-transform duration-200">
                @foreach($frames as $index => $frame)
                    <img src="{{ $frame->image_url }}"
                         alt="{{ $frame->alt_text }}"
                         data-frame-index="{{ $index }}"
                         data-frame-angle="{{ $frame->frame_angle }}"
                         class="turntable-fullscreen-frame absolute inset-0 w-full h-full object-contain pointer-events-none transition-opacity duration-75 {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
                @endforeach

                <!-- Interactive Hotspots Overlay -->
                <div id="hotspots-overlay" class="absolute inset-0 pointer-events-none z-20">
                    @foreach($hotspots as $spot)
                        <div class="fullscreen-hotspot-pin absolute -translate-x-1/2 -translate-y-1/2 pointer-events-auto group cursor-pointer"
                             data-angle="{{ $spot['frame_angle'] }}"
                             style="left: {{ $spot['x_pct'] }}%; top: {{ $spot['y_pct'] }}%;">
                            <!-- Pulsing Target Ring -->
                            <div class="relative w-8 h-8 rounded-full bg-cyan-500/25 border-2 border-cyan-500 flex items-center justify-center shadow-lg shadow-cyan-400/40 group-hover:scale-110 transition-transform">
                                <div class="w-2.5 h-2.5 rounded-full bg-cyan-600 animate-ping absolute"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-cyan-600"></div>
                            </div>

                            <!-- Popover Info Card -->
                            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 w-64 p-3.5 rounded-2xl bg-white border border-slate-200 text-slate-900 shadow-2xl opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-30">
                                <div class="flex items-center justify-between text-[10px] text-cyan-700 font-mono font-bold uppercase mb-1">
                                    <span>Feature Highlight</span>
                                    <span>{{ $spot['frame_angle'] }}°</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-900">{{ $spot['title'] }}</h4>
                                @if(!empty($spot['description']))
                                    <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">{{ $spot['description'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Drag Instruction Overlay Hint -->
            <div id="fullscreen-drag-hint"
                 class="absolute top-6 left-1/2 -translate-x-1/2 px-5 py-2 rounded-full bg-white/90 border border-slate-300 text-xs font-semibold text-slate-700 shadow-sm flex items-center gap-2 pointer-events-none z-20 transition-opacity duration-300">
                <svg class="w-4 h-4 text-cyan-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>Drag horizontally to spin 360° &bull; Pinch or Zoom</span>
            </div>
        @else
            <div class="p-8 text-center text-slate-500">
                <p>No 360° frames currently loaded for this motorcycle.</p>
            </div>
        @endif

    </main>

    <!-- Floating Bottom Control Dock -->
    <footer class="relative z-30 border-t border-slate-200 bg-white/95 backdrop-blur-md px-4 sm:px-8 py-3 sm:py-4 shadow-sm">
        <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-6">

            <!-- Angle & Rotation Scrubber -->
            <div class="w-full sm:flex-1 flex items-center gap-3">
                <span id="fullscreen-angle-badge" class="px-2.5 py-1 rounded-xl bg-cyan-50 text-cyan-700 font-mono font-bold text-xs border border-cyan-200 min-w-[50px] text-center">
                    0°
                </span>
                <input type="range" id="fullscreen-angle-scrubber" min="0" max="359" value="0"
                       class="flex-1 h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-cyan-600">
                <span class="text-xs text-slate-500 font-mono hidden md:inline">
                    <span id="fullscreen-frame-num" class="text-slate-900 font-bold">1</span>/{{ $frames->count() }}
                </span>
            </div>

            <!-- Action Controls Group -->
            <div class="flex items-center gap-2">
                <!-- Rotate Left -->
                <button type="button" id="btn-fs-spin-left" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 border border-slate-300 transition shadow-xs" title="Rotate Left (Left Arrow)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <!-- Play/Pause Auto-Spin -->
                <button type="button" id="btn-fs-toggle-play" class="px-4 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs transition shadow-md shadow-cyan-600/20 flex items-center gap-2">
                    <svg id="fs-play-icon" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                    <span id="fs-play-text">Auto Spin</span>
                </button>

                <!-- Rotate Right -->
                <button type="button" id="btn-fs-spin-right" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 border border-slate-300 transition shadow-xs" title="Rotate Right (Right Arrow)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>

                <!-- Reset to 0° -->
                <button type="button" id="btn-fs-reset" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 border border-slate-300 transition text-xs font-semibold shadow-xs" title="Reset View">
                    0° Reset
                </button>

                <!-- Zoom Toggle -->
                <button type="button" id="btn-fs-zoom" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 border border-slate-300 transition shadow-xs" title="Toggle Zoom">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                </button>
            </div>

        </div>
    </footer>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stage = document.getElementById('turntable-stage');
    const viewport = document.getElementById('turntable-viewport');
    const frames = document.querySelectorAll('.turntable-fullscreen-frame');
    const scrubber = document.getElementById('fullscreen-angle-scrubber');
    const angleBadge = document.getElementById('fullscreen-angle-badge');
    const frameNum = document.getElementById('fullscreen-frame-num');
    const btnPlay = document.getElementById('btn-fs-toggle-play');
    const playText = document.getElementById('fs-play-text');
    const btnSpinLeft = document.getElementById('btn-fs-spin-left');
    const btnSpinRight = document.getElementById('btn-fs-spin-right');
    const btnReset = document.getElementById('btn-fs-reset');
    const btnZoom = document.getElementById('btn-fs-zoom');
    const dragHint = document.getElementById('fullscreen-drag-hint');
    const btnFullscreen = document.getElementById('btn-fullscreen-toggle');
    const hotspots = document.querySelectorAll('.fullscreen-hotspot-pin');

    if (!frames || frames.length === 0) return;

    let totalFrames = frames.length;
    let currentIndex = 0;
    let isDragging = false;
    let startX = 0;
    let isPlaying = false;
    let playInterval = null;
    let isZoomed = false;
    let speed = {{ $settings['rotation_speed'] ?? 3 }};
    let stepDegrees = 360 / totalFrames;

    function showFrame(index) {
        if (index < 0) index = (totalFrames + (index % totalFrames)) % totalFrames;
        if (index >= totalFrames) index = index % totalFrames;

        currentIndex = index;
        const currentAngle = Math.round(currentIndex * stepDegrees);

        frames.forEach((f, idx) => {
            if (idx === currentIndex) {
                f.classList.remove('opacity-0', 'z-0');
                f.classList.add('opacity-100', 'z-10');
            } else {
                f.classList.remove('opacity-100', 'z-10');
                f.classList.add('opacity-0', 'z-0');
            }
        });

        if (scrubber) scrubber.value = currentAngle;
        if (angleBadge) angleBadge.textContent = currentAngle + '°';
        if (frameNum) frameNum.textContent = (currentIndex + 1);

        // Update Hotspot pins within ±22 degrees of angle
        hotspots.forEach(pin => {
            const pinAngle = parseInt(pin.getAttribute('data-angle'), 10) || 0;
            const diff = Math.abs(currentAngle - pinAngle);
            const wrappedDiff = Math.min(diff, 360 - diff);
            if (wrappedDiff <= 22) {
                pin.style.display = 'block';
                pin.style.opacity = Math.max(0.3, 1 - (wrappedDiff / 22));
            } else {
                pin.style.display = 'none';
            }
        });
    }

    // Drag Interaction
    stage.addEventListener('pointerdown', function (e) {
        isDragging = true;
        startX = e.clientX;
        if (dragHint) dragHint.style.opacity = '0';
        if (isPlaying) stopAutoSpin();
        stage.setPointerCapture(e.pointerId);
    });

    stage.addEventListener('pointermove', function (e) {
        if (!isDragging) return;
        const deltaX = e.clientX - startX;
        const sensitivity = 8;
        if (Math.abs(deltaX) >= sensitivity) {
            const advance = Math.floor(deltaX / sensitivity);
            showFrame(currentIndex - advance);
            startX = e.clientX;
        }
    });

    stage.addEventListener('pointerup', function (e) {
        isDragging = false;
        stage.releasePointerCapture(e.pointerId);
    });

    stage.addEventListener('pointercancel', function () {
        isDragging = false;
    });

    // Scrubber
    if (scrubber) {
        scrubber.addEventListener('input', function () {
            const targetAngle = parseInt(this.value, 10);
            const targetIndex = Math.round((targetAngle / 360) * totalFrames);
            showFrame(targetIndex);
            if (isPlaying) stopAutoSpin();
        });
    }

    // Step Buttons
    if (btnSpinLeft) {
        btnSpinLeft.addEventListener('click', () => {
            showFrame(currentIndex - 1);
            if (isPlaying) stopAutoSpin();
        });
    }

    if (btnSpinRight) {
        btnSpinRight.addEventListener('click', () => {
            showFrame(currentIndex + 1);
            if (isPlaying) stopAutoSpin();
        });
    }

    if (btnReset) {
        btnReset.addEventListener('click', () => {
            showFrame(0);
            if (isPlaying) stopAutoSpin();
        });
    }

    // Auto Spin
    function startAutoSpin() {
        isPlaying = true;
        if (playText) playText.textContent = 'Pause';
        const intervalMs = Math.max(35, 180 - (speed * 15));
        playInterval = setInterval(() => {
            showFrame(currentIndex + 1);
        }, intervalMs);
    }

    function stopAutoSpin() {
        isPlaying = false;
        if (playText) playText.textContent = 'Auto Spin';
        if (playInterval) clearInterval(playInterval);
    }

    if (btnPlay) {
        btnPlay.addEventListener('click', function () {
            if (isPlaying) {
                stopAutoSpin();
            } else {
                startAutoSpin();
            }
        });
    }

    // Zoom Toggle
    if (btnZoom && viewport) {
        btnZoom.addEventListener('click', function () {
            isZoomed = !isZoomed;
            viewport.style.transform = isZoomed ? 'scale(1.4)' : 'scale(1)';
            this.classList.toggle('text-cyan-600', isZoomed);
            this.classList.toggle('bg-cyan-100', isZoomed);
        });
    }

    // Fullscreen Toggle
    if (btnFullscreen) {
        btnFullscreen.addEventListener('click', function () {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen().catch(() => {});
            }
        });
    }

    // Keyboard Shortcuts (Arrow keys & Space)
    window.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') {
            showFrame(currentIndex - 1);
            if (isPlaying) stopAutoSpin();
        } else if (e.key === 'ArrowRight') {
            showFrame(currentIndex + 1);
            if (isPlaying) stopAutoSpin();
        } else if (e.key === ' ') {
            e.preventDefault();
            if (isPlaying) stopAutoSpin();
            else startAutoSpin();
        }
    });

    // Auto-rotate on load if configured
    @if(!empty($settings['auto_rotate']))
        setTimeout(startAutoSpin, 600);
    @endif

    // Initial frame
    showFrame({{ (int) ($settings['default_angle'] ?? 0) }});
});
</script>
<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.4.0/model-viewer.min.js"></script>
<style>
.view-3d-hotspot {
    background: transparent;
    border: none;
    box-sizing: border-box;
    cursor: pointer;
    position: relative;
    padding: 0;
}
.hotspot-3d-pin {
    width: 26px;
    height: 26px;
    border-radius: 9999px;
    background: rgba(6, 182, 212, 0.25);
    border: 2px solid #06b6d4;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 14px rgba(6, 182, 212, 0.5);
    transition: transform 0.2s ease;
}
.view-3d-hotspot:hover .hotspot-3d-pin,
.view-3d-hotspot:focus .hotspot-3d-pin {
    transform: scale(1.2);
    border-color: #0891b2;
}
.hotspot-3d-dot {
    width: 8px;
    height: 8px;
    border-radius: 9999px;
    background: #0891b2;
}
.hotspot-3d-card {
    position: absolute;
    bottom: 34px;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    padding: 8px 12px;
    border-radius: 14px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    pointer-events: none;
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.2s ease, transform 0.2s ease;
    z-index: 30;
}
.view-3d-hotspot:hover .hotspot-3d-card,
.view-3d-hotspot:focus .hotspot-3d-card {
    opacity: 1;
    transform: translateX(-50%) translateY(-2px);
}
</style>
@endpush
@endsection
