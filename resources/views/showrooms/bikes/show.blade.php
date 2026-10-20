@extends('layouts.app')

@section('title', $bike->name . ' — Purchase & 360° Studio — ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50/70 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white"
     x-data="{
         buyModalOpen: false,
         bookingTermsModalOpen: false,
         termsAccepted: false,
         bookingPaymentMethod: 'razorpay',
         selectedVariant: '{{ $bike->variants->first()?->name ?? 'Standard' }}',
         selectedColorHex: '{{ $bike->variants->first()?->color_hex ?? '#CC0000' }}',
         downPaymentPct: 20,
         loanTenureMonths: 36,
         basePrice: {{ (float) $bike->price }},
         rtoEstimate: {{ round($bike->price * 0.13) }},
         insuranceEstimate: {{ round($bike->price * 0.035) }},
         get onRoadPrice() {
             return this.basePrice + this.rtoEstimate + this.insuranceEstimate;
         },
         get downPaymentAmount() {
             return Math.round(this.onRoadPrice * (this.downPaymentPct / 100));
         },
         get loanAmount() {
             return this.onRoadPrice - this.downPaymentAmount;
         },
         get monthlyEmi() {
             const annualRate = 0.085; // 8.5% p.a.
             const monthlyRate = annualRate / 12;
             const n = this.loanTenureMonths;
             const emi = (this.loanAmount * monthlyRate * Math.pow(1 + monthlyRate, n)) / (Math.pow(1 + monthlyRate, n) - 1);
             return Math.round(emi);
         }
     }">

    {{-- Branded Dealership Header --}}
    @include('showrooms.partials.header')

    <main class="flex-1 w-full max-w-[1720px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 py-8 sm:py-10 space-y-10">

        {{-- ═══ TOP BREADCRUMB & AVAILABILITY STATUS ═══ --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
            <div class="flex items-center gap-2 text-slate-500 flex-wrap">
                <a href="{{ route('showrooms.show', $showroom) }}" class="inline-flex items-center gap-1.5 hover:text-slate-900 font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>{{ $showroom->name }}</span>
                </a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="hover:text-slate-900 transition">Motorcycles</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-500">{{ $bike->brand }}</span>
                <span class="text-slate-300">/</span>
                <span class="font-bold text-slate-900">{{ $bike->name }}</span>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold bg-white border border-slate-200/90 text-emerald-700 shadow-2xs">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Ready for Delivery • Chassis VIN Allocation Active</span>
                </span>

                <a href="{{ route('showrooms.bikes.index', $showroom) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 font-bold transition shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back to Fleet</span>
                </a>
            </div>
        </div>

        {{-- ═══ SESSION ALERTS ═══ --}}
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center gap-3 shadow-2xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- ═══ MAIN 2-COLUMN PRODUCT COCKPIT ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">

            {{-- ═══════════════════════════════════════════════════════════════
                 LEFT COLUMN: INTERACTIVE 360° STUDIO, GALLERY & TELEMETRY (8 COLS)
                 ═══════════════════════════════════════════════════════════════ --}}
            <div class="lg:col-span-8 space-y-8" id="studio360">

                @php
                    $allFrames = $bike->frames360;
                    if ($allFrames->isEmpty()) {
                        // Fallback frames with 8 standard turntable perspectives
                        $fallbackUrls = [
                            0 => $bike->primary_image_url,
                            45 => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=1400&q=85',
                            90 => $bike->primary_image_url,
                            135 => 'https://images.unsplash.com/photo-1558981420-87aa9dad1c89?auto=format&fit=crop&w=1400&q=85',
                            180 => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1400&q=85',
                            225 => 'https://images.unsplash.com/photo-1558980664-769d59546b3d?auto=format&fit=crop&w=1400&q=85',
                            270 => 'https://images.unsplash.com/photo-1558980664-3a031cf67ea8?auto=format&fit=crop&w=1400&q=85',
                            315 => 'https://images.unsplash.com/photo-1558981285-6f0c94958bb6?auto=format&fit=crop&w=1400&q=85',
                        ];
                    }
                    $glbModelUrl = !empty($bike->model_3d_path)
                        ? $bike->model_3d_path
                        : asset('models/motorcycle.glb');
                @endphp

                {{-- ═══ CARD 1: 360° VIRTUAL STUDIO TURNTABLE & 3D GLB ORBIT ═══ --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-6 overflow-hidden shadow-xs space-y-5"
                     x-data="{
                         mode: '{{ $has360Viewer ? '360' : 'gallery' }}',
                         studioView: '{{ $bike->viewer_type === 'gltf_model' || !empty($bike->model_3d_path) ? 'glb' : 'glb' }}',
                         currentAngle: 0,
                         isDragging: false,
                         startX: 0,
                         startAngle: 0,
                         autoSpinInterval: null,
                         isAutoSpinning: false,
                         glbAutoRotate: true,
                         selectedGalleryPhoto: '{{ $bike->primary_image_url }}',
                         angles: [0, 45, 90, 135, 180, 225, 270, 315],
                         get angleText() {
                             const a = this.currentAngle;
                             if (a >= 338 || a <= 22) return '0° Front Facia Stance';
                             if (a > 22 && a < 68) return '45° Front-Right Dynamic Angle';
                             if (a >= 68 && a <= 112) return '90° Right Side Flank Profile';
                             if (a > 112 && a < 158) return '135° Rear-Right Quarter Exhaust';
                             if (a >= 158 && a <= 202) return '180° Rear Tail & Aero Winglet';
                             if (a > 202 && a < 248) return '225° Rear-Left Quarter Monoshock';
                             if (a >= 248 && a <= 292) return '270° Left Side Flank Transmission';
                             return '315° Front-Left Dynamic Angle';
                         },
                         get activeFrameIndex() {
                             const step = 360 / 8;
                             const idx = Math.round(this.currentAngle / step) % 8;
                             return idx;
                         },
                         setAngle(deg) {
                             this.currentAngle = (deg % 360 + 360) % 360;
                         },
                         startDrag(e) {
                             this.isDragging = true;
                             this.startX = e.clientX || (e.touches && e.touches[0].clientX) || 0;
                             this.startAngle = this.currentAngle;
                             if (this.isAutoSpinning) this.stopAutoSpin();
                         },
                         onDrag(e) {
                             if (!this.isDragging) return;
                             const clientX = e.clientX || (e.touches && e.touches[0].clientX) || 0;
                             const deltaX = clientX - this.startX;
                             const deltaAngle = Math.round(deltaX * 0.85);
                             this.currentAngle = ((this.startAngle - deltaAngle) % 360 + 360) % 360;
                         },
                         stopDrag() {
                             this.isDragging = false;
                         },
                         toggleAutoSpin() {
                             if (this.isAutoSpinning) {
                                 this.stopAutoSpin();
                             } else {
                                 this.startAutoSpin();
                             }
                         },
                         startAutoSpin() {
                             this.isAutoSpinning = true;
                             this.autoSpinInterval = setInterval(() => {
                                 this.currentAngle = (this.currentAngle + 3) % 360;
                             }, 45);
                         },
                         stopAutoSpin() {
                             this.isAutoSpinning = false;
                             clearInterval(this.autoSpinInterval);
                         },
                         toggleGlbAutoRotate() {
                             const mv = document.getElementById('bike-model-viewer');
                             if (!mv) return;
                             this.glbAutoRotate = !this.glbAutoRotate;
                             mv.autoRotate = this.glbAutoRotate;
                         },
                         resetGlbCamera() {
                             const mv = document.getElementById('bike-model-viewer');
                             if (mv) {
                                 mv.cameraOrbit = '0deg 75deg 105%';
                                 mv.jumpCameraToGoal();
                             }
                         },
                         setGlbOrbit(orbit) {
                             const mv = document.getElementById('bike-model-viewer');
                             if (mv) {
                                 mv.cameraOrbit = orbit;
                             }
                         },
                         zoomGlb(delta) {
                             const mv = document.getElementById('bike-model-viewer');
                             if (mv) {
                                 mv.zoom(delta);
                             }
                         }
                     }">

                    {{-- Media View Switcher Bar --}}
                    @if($has360Viewer)
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-3">
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        @click="mode = '360'"
                                        :class="mode === '360' ? 'bg-gradient-to-r from-slate-900 via-rose-600 to-[#FF6B00] text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                                        class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition flex items-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" :class="isAutoSpinning || (studioView === 'glb' && glbAutoRotate) ? 'animate-spin-slow' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span>Interactive 360° Studio</span>
                                </button>

                                <button type="button"
                                        @click="mode = 'gallery'; stopAutoSpin();"
                                        :class="mode === 'gallery' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>High-Res Gallery</span>
                                </button>
                            </div>

                            <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                                {{-- 3D GLB vs Turntable Sub-Switcher --}}
                                <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 border border-slate-200" x-show="mode === '360'">
                                    <button type="button" @click="studioView = 'glb'"
                                            :class="studioView === 'glb' ? 'bg-white text-slate-900 font-bold shadow-2xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900'"
                                            class="px-2.5 py-1 rounded-lg text-xs transition flex items-center gap-1.5 cursor-pointer"
                                            title="Interactive 3D WebGL Model Orbit">
                                        <svg class="w-3.5 h-3.5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                        <span>3D GLB Orbit</span>
                                    </button>
                                    <button type="button" @click="studioView = 'turntable'"
                                            :class="studioView === 'turntable' ? 'bg-white text-slate-900 font-bold shadow-2xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900'"
                                            class="px-2.5 py-1 rounded-lg text-xs transition flex items-center gap-1.5 cursor-pointer"
                                            title="Sequential Turntable Photos">
                                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span>Turntable</span>
                                    </button>
                                </div>

                                <a href="{{ route('showrooms.bikes.360', [$showroom, $bike]) }}"
                                   class="text-xs font-bold text-cyan-700 hover:text-cyan-900 transition flex items-center gap-1">
                                    <span>Fullscreen 360</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>

                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200" x-show="studioView === 'turntable'">
                                    <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
                                    <span x-text="angleText"></span>
                                </span>
                            </div>
                        </div>

                        {{-- ═══ MODE 1: INTERACTIVE 360° STUDIO ═══ --}}
                        <div x-show="mode === '360'" class="space-y-4">

                            {{-- ── SUB-MODE A: 3D GLB WEBGL ORBIT VIEWER (GLOBE / 3D MODEL) ── --}}
                            <div x-show="studioView === 'glb'" class="space-y-4">
                                <div class="relative aspect-[16/10] rounded-2xl bg-gradient-to-b from-slate-100 via-slate-50 to-slate-200/90 border border-slate-200 overflow-hidden shadow-inner flex items-center justify-center">

                                    {{-- Ambient Studio Stage Lighting Elements --}}
                                    <div class="absolute inset-0 bg-radial from-white via-transparent to-slate-900/10 pointer-events-none"></div>
                                    <div class="absolute bottom-4 inset-x-12 h-20 bg-slate-900/20 blur-2xl rounded-full pointer-events-none"></div>

                                    {{-- Concentric Turntable Circular Stage Floor Graphic --}}
                                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-[70%] h-24 rounded-[100%] border-2 border-cyan-400/40 bg-gradient-to-t from-cyan-100/30 to-transparent pointer-events-none transform -rotate-x-60"></div>
                                    <div class="absolute bottom-9 left-1/2 -translate-x-1/2 w-[52%] h-16 rounded-[100%] border border-cyan-500/30 pointer-events-none transform -rotate-x-60"></div>

                                    {{-- Google Model-Viewer WebGL 3D Component --}}
                                    <model-viewer
                                        id="bike-model-viewer"
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
                                        loading="lazy"
                                        ar
                                        ar-modes="webxr scene-viewer quick-look"
                                        class="w-full h-full cursor-grab active:cursor-grabbing z-10"
                                        style="width: 100%; height: 100%; outline: none;"
                                    >
                                        {{-- 3D Hotspot Annotations on Model --}}
                                        @foreach($bike->hotspots_list as $sIdx => $spot)
                                            <button class="view-3d-hotspot" slot="hotspot-{{ $sIdx }}"
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

                                        {{-- Loading Poster --}}
                                        <div slot="poster" class="flex flex-col items-center justify-center w-full h-full bg-slate-50 text-slate-600 font-bold text-xs gap-2">
                                            <svg class="w-7 h-7 animate-spin text-rose-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                            <span>Loading 3D WebGL Model &amp; Studio Lighting...</span>
                                        </div>
                                    </model-viewer>

                                    {{-- Top HUD Overlays --}}
                                    <div class="absolute top-4 left-4 flex items-center gap-2 pointer-events-none z-20">
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-slate-900 text-white shadow-xs">
                                            {{ $bike->category->label() }}
                                        </span>
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-white/90 text-cyan-800 border border-cyan-200/80 shadow-xs flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-cyan-500 animate-ping"></span>
                                            <span>3D GLB Orbit Active</span>
                                        </span>
                                    </div>

                                    <div class="absolute top-4 right-4 pointer-events-none z-20">
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-white/90 text-slate-700 border border-slate-200 shadow-xs">
                                            Drag to orbit 360° &bull; Pinch to zoom
                                        </span>
                                    </div>
                                </div>

                                {{-- 3D GLB Orbit Controls Bar --}}
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                                    <div class="flex items-center justify-between gap-3 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <button type="button"
                                                    @click="toggleGlbAutoRotate()"
                                                    :class="glbAutoRotate ? 'bg-cyan-700 text-white' : 'bg-white text-slate-800 border border-slate-200 hover:bg-slate-100'"
                                                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                                <span x-text="glbAutoRotate ? '⏸ Pause Auto-Orbit' : '▶ Play Auto-Orbit'"></span>
                                            </button>

                                            <button type="button"
                                                    @click="resetGlbCamera()"
                                                    class="px-3 py-2 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold transition flex items-center gap-1 shadow-2xs cursor-pointer"
                                                    title="Reset camera to default front view">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Reset Angle</span>
                                            </button>
                                        </div>

                                        {{-- Zoom In / Out Controls --}}
                                        <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl p-1 shadow-2xs">
                                            <span class="text-[10px] text-slate-400 font-bold px-1.5 uppercase">Zoom</span>
                                            <button type="button" @click="zoomGlb(15)" class="w-7 h-7 rounded-lg hover:bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs" title="Zoom in">+</button>
                                            <button type="button" @click="zoomGlb(-15)" class="w-7 h-7 rounded-lg hover:bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs" title="Zoom out">&minus;</button>
                                        </div>
                                    </div>

                                    {{-- Fast 3D Preset Angle Buttons --}}
                                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-1.5 pt-1 border-t border-slate-200/60">
                                        <button type="button" @click="setGlbOrbit('0deg 75deg 105%')" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer bg-white hover:bg-slate-100 text-slate-700 border border-slate-200">0° Front</button>
                                        <button type="button" @click="setGlbOrbit('45deg 75deg 105%')" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer bg-white hover:bg-slate-100 text-slate-700 border border-slate-200">45° Dynamic</button>
                                        <button type="button" @click="setGlbOrbit('90deg 75deg 105%')" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer bg-white hover:bg-slate-100 text-slate-700 border border-slate-200">90° Flank</button>
                                        <button type="button" @click="setGlbOrbit('180deg 75deg 105%')" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer bg-white hover:bg-slate-100 text-slate-700 border border-slate-200">180° Exhaust</button>
                                        <button type="button" @click="setGlbOrbit('270deg 75deg 105%')" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer bg-white hover:bg-slate-100 text-slate-700 border border-slate-200">270° Left</button>
                                        <button type="button" @click="setGlbOrbit('0deg 25deg 120%')" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer bg-cyan-50 hover:bg-cyan-100 text-cyan-800 border border-cyan-200">Top Cockpit</button>
                                    </div>
                                </div>
                            </div>

                            {{-- ── SUB-MODE B: SEQUENTIAL TURNTABLE FRAMES ── --}}
                            <div x-show="studioView === 'turntable'" class="space-y-4">
                                <div class="relative aspect-[16/10] rounded-2xl bg-gradient-to-b from-slate-100 via-slate-50 to-slate-200/90 border border-slate-200 overflow-hidden cursor-grab active:cursor-grabbing select-none flex items-center justify-center shadow-inner"
                                     @mousedown="startDrag($event)"
                                     @mousemove="onDrag($event)"
                                     @mouseup="stopDrag()"
                                     @mouseleave="stopDrag()"
                                     @touchstart="startDrag($event)"
                                     @touchmove="onDrag($event)"
                                     @touchend="stopDrag()">

                                    {{-- Ambient Studio Stage Lighting Elements --}}
                                    <div class="absolute inset-0 bg-radial from-white via-transparent to-slate-900/10 pointer-events-none"></div>
                                    <div class="absolute bottom-6 inset-x-12 h-16 bg-slate-900/20 blur-xl rounded-full pointer-events-none"></div>
                                    <div class="absolute bottom-8 inset-x-20 h-10 bg-slate-900/30 blur-md rounded-full pointer-events-none"></div>

                                    {{-- Concentric Turntable Circular Stage Floor Graphic --}}
                                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-[70%] h-24 rounded-[100%] border-2 border-rose-400/30 bg-gradient-to-t from-rose-100/20 to-transparent pointer-events-none transform -rotate-x-60"></div>

                                    {{-- 360 Turntable Frames Preloaded in DOM for Zero Lag --}}
                                    @if($allFrames->isNotEmpty())
                                        @foreach($allFrames as $idx => $frame)
                                            <img src="{{ $frame->image_url }}"
                                                 alt="{{ $frame->alt_text }}"
                                                 x-show="activeFrameIndex === {{ $idx }}"
                                                 class="absolute inset-0 w-full h-full object-contain pointer-events-none transition-opacity duration-75">
                                        @endforeach
                                    @else
                                        @foreach($fallbackUrls as $deg => $fUrl)
                                            @php $fIdx = array_search($deg, [0, 45, 90, 135, 180, 225, 270, 315]); @endphp
                                            <img src="{{ $fUrl }}"
                                                 alt="{{ $bike->name }} {{ $deg }} degree"
                                                 x-show="activeFrameIndex === {{ $fIdx }}"
                                                 class="absolute inset-0 w-full h-full object-contain pointer-events-none transition-opacity duration-75">
                                        @endforeach
                                    @endif

                                    {{-- Top HUD Overlays --}}
                                    <div class="absolute top-4 left-4 flex items-center gap-2 pointer-events-none">
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-slate-900 text-white shadow-xs">
                                            {{ $bike->category->label() }}
                                        </span>
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-white/90 text-slate-800 border border-slate-200/80 shadow-xs">
                                            Turntable Studio Active
                                        </span>
                                    </div>

                                    <div class="absolute top-4 right-4 pointer-events-none">
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-white/90 text-slate-700 border border-slate-200 shadow-xs">
                                            Drag horizontally to spin 360°
                                        </span>
                                    </div>

                                    {{-- Interactive Hotspot Inspection Pins --}}
                                    <div class="absolute inset-0 pointer-events-none z-20">
                                        @foreach($bike->hotspots_list as $spot)
                                            <div class="absolute -translate-x-1/2 -translate-y-1/2 pointer-events-auto group cursor-pointer"
                                                 style="left: {{ $spot['x_pct'] }}%; top: {{ $spot['y_pct'] }}%;">
                                                <div class="w-7 h-7 rounded-full bg-cyan-500/25 border-2 border-cyan-500 flex items-center justify-center shadow-lg animate-pulse">
                                                    <div class="w-2.5 h-2.5 rounded-full bg-cyan-500"></div>
                                                </div>
                                                {{-- Tooltip --}}
                                                <div class="absolute bottom-9 left-1/2 -translate-x-1/2 w-52 p-3 rounded-2xl bg-white border border-slate-200 text-slate-900 text-xs shadow-xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-30 space-y-1">
                                                    <p class="font-black text-cyan-800 leading-tight">{{ $spot['title'] }}</p>
                                                    <p class="text-[11px] text-slate-600 leading-normal">{{ $spot['description'] }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Turntable Controls Bar --}}
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                                    <div class="flex items-center justify-between gap-4">
                                        <button type="button"
                                                @click="toggleAutoSpin()"
                                                :class="isAutoSpinning ? 'bg-rose-600 text-white' : 'bg-white text-slate-800 border border-slate-200 hover:bg-slate-100'"
                                                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 shadow-2xs cursor-pointer">
                                            <span x-text="isAutoSpinning ? '⏸ Pause Auto-Spin' : '▶ Play Auto-Spin'"></span>
                                        </button>

                                        {{-- Continuous Degree Slider --}}
                                        <div class="flex-1 flex items-center gap-3">
                                            <span class="text-[11px] font-mono text-slate-400 font-bold">0°</span>
                                            <input type="range"
                                                   min="0"
                                                   max="360"
                                                   step="5"
                                                   x-model="currentAngle"
                                                   class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#FF6B00]">
                                            <span class="text-[11px] font-mono text-slate-400 font-bold">360°</span>
                                        </div>

                                        <span class="font-mono font-bold text-xs text-slate-800 px-2.5 py-1 bg-white rounded-lg border border-slate-200 shrink-0">
                                            <span x-text="currentAngle"></span>°
                                        </span>
                                    </div>

                                    {{-- Fast Angle Preset Buttons --}}
                                    <div class="grid grid-cols-4 sm:grid-cols-8 gap-1.5 pt-1 border-t border-slate-200/60">
                                        <button type="button" @click="setAngle(0)" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer" :class="activeFrameIndex === 0 ? 'bg-slate-900 text-white' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200'">0° Front</button>
                                        <button type="button" @click="setAngle(45)" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer" :class="activeFrameIndex === 1 ? 'bg-slate-900 text-white' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200'">45° Front-R</button>
                                        <button type="button" @click="setAngle(90)" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer" :class="activeFrameIndex === 2 ? 'bg-slate-900 text-white' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200'">90° Flank</button>
                                        <button type="button" @click="setAngle(135)" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer" :class="activeFrameIndex === 3 ? 'bg-slate-900 text-white' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200'">135° Rear-R</button>
                                        <button type="button" @click="setAngle(180)" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer" :class="activeFrameIndex === 4 ? 'bg-slate-900 text-white' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200'">180° Tail</button>
                                        <button type="button" @click="setAngle(225)" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer" :class="activeFrameIndex === 5 ? 'bg-slate-900 text-white' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200'">225° Rear-L</button>
                                        <button type="button" @click="setAngle(270)" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer" :class="activeFrameIndex === 6 ? 'bg-slate-900 text-white' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200'">270° Left</button>
                                        <button type="button" @click="setAngle(315)" class="py-1.5 rounded-lg text-[10px] font-bold transition text-center cursor-pointer" :class="activeFrameIndex === 7 ? 'bg-slate-900 text-white' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200'">315° Front-L</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                    {{-- ═══ MODE 2: HIGH-RES GALLERY PHOTO FRAME ═══ --}}
                    <div x-show="mode === 'gallery'" class="space-y-4">
                        <div class="aspect-[16/10] rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 relative">
                            <img :src="selectedGalleryPhoto"
                                 alt="{{ $bike->name }}"
                                 class="w-full h-full object-cover transition-all duration-300">
                        </div>

                        {{-- Gallery Thumbnails Strip --}}
                        <div class="flex items-center gap-3 overflow-x-auto pb-1">
                            @foreach($bike->images as $img)
                                <button type="button"
                                        @click="selectedGalleryPhoto = '{{ $img->image_url }}'"
                                        class="w-24 h-16 rounded-xl overflow-hidden bg-slate-100 border-2 transition-all shrink-0 cursor-pointer"
                                        :class="selectedGalleryPhoto === '{{ $img->image_url }}' ? 'border-[#FF6B00] ring-2 ring-orange-200' : 'border-slate-200 hover:border-slate-300'">
                                    <img src="{{ $img->image_url }}" alt="{{ $img->alt_text }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- ═══ CARD 2: PERFORMANCE & TELEMETRY MATRIX ═══ --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#FF6B00]"></span>
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-900">
                                Certified Engineering & Performance Telemetry
                            </h3>
                        </div>
                        <span class="text-xs font-mono text-slate-400">Dyno Certified</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Displacement</span>
                            <p class="text-lg font-black font-mono text-slate-900">{{ $bike->engine_cc ? $bike->engine_cc . ' cc' : 'EV' }}</p>
                            <span class="text-[10px] text-slate-500">Liquid-Cooled</span>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Max Power</span>
                            <p class="text-lg font-black font-mono text-rose-600">{{ $bike->power_bhp ? $bike->power_bhp . ' bhp' : 'N/A' }}</p>
                            <span class="text-[10px] text-slate-500">Peak Output</span>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Max Torque</span>
                            <p class="text-lg font-black font-mono text-slate-900">{{ $bike->torque_nm ? $bike->torque_nm . ' Nm' : 'N/A' }}</p>
                            <span class="text-[10px] text-slate-500">High Traction</span>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Top Speed</span>
                            <p class="text-lg font-black font-mono text-slate-900">{{ $bike->top_speed_kmh ? $bike->top_speed_kmh . ' km/h' : 'N/A' }}</p>
                            <span class="text-[10px] text-slate-500">Track Verified</span>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Kerb Weight</span>
                            <p class="text-lg font-black font-mono text-slate-900">{{ $bike->curb_weight_kg ? $bike->curb_weight_kg . ' kg' : 'N/A' }}</p>
                            <span class="text-[10px] text-slate-500">Optimal Ratio</span>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Fuel Economy</span>
                            <p class="text-lg font-black font-mono text-emerald-600">{{ $bike->mileage_kmpl ? $bike->mileage_kmpl . ' km/l' : 'N/A' }}</p>
                            <span class="text-[10px] text-slate-500">Certified ARAI</span>
                        </div>
                    </div>
                </div>

                {{-- ═══ CARD 3: TECHNICAL SPECIFICATIONS & EQUIPMENT ═══ --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span>Comprehensive Factory Specifications</span>
                        </h3>
                        <span class="text-xs font-mono text-slate-400">Technical Datasheet</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <span class="font-bold text-slate-900 uppercase tracking-wider block text-[11px] text-[#FF6B00]">
                                Powertrain & Transmission
                            </span>
                            <div class="space-y-1.5 text-slate-600">
                                <div class="flex justify-between py-1 border-b border-slate-200/60">
                                    <span class="text-slate-500">Engine Type</span>
                                    <span class="font-bold text-slate-800">{{ $bike->brand }} Performance Powerplant</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-200/60">
                                    <span class="text-slate-500">Transmission</span>
                                    <span class="font-bold text-slate-800">{{ $bike->transmission ?? '6-Speed Constant Mesh' }}</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-200/60">
                                    <span class="text-slate-500">Fuel System</span>
                                    <span class="font-bold text-slate-800">Electronic Fuel Injection (EFI)</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-slate-500">Emission Standard</span>
                                    <span class="font-bold text-slate-800">BS-VI Phase 2 Compliant</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <span class="font-bold text-slate-900 uppercase tracking-wider block text-[11px] text-blue-600">
                                Chassis, Brakes & Safety
                            </span>
                            <div class="space-y-1.5 text-slate-600">
                                <div class="flex justify-between py-1 border-b border-slate-200/60">
                                    <span class="text-slate-500">Braking System</span>
                                    <span class="font-bold text-slate-800">Dual Channel Cornering ABS</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-200/60">
                                    <span class="text-slate-500">Traction Control</span>
                                    <span class="font-bold text-slate-800">Multi-Level DTC & Wheelie Control</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-slate-200/60">
                                    <span class="text-slate-500">Chassis Frame</span>
                                    <span class="font-bold text-slate-800">Lightweight Aluminum Monocoque</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-slate-500">Riding Modes</span>
                                    <span class="font-bold text-slate-800">Race, Sport, Street, Wet</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!empty($bike->specifications))
                        <div class="pt-2">
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Custom Factory Options & Equipment</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                @foreach($bike->specifications as $k => $v)
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">{{ $k }}</span>
                                        <span class="font-bold text-slate-900">{{ $v }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ═══ CARD 4: OVERVIEW & INCLUSIONS ═══ --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-4">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-900">
                        Motorcycle Overview & Story
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ $bike->description ?? 'Engineered with supreme aerodynamic efficiency and racetrack pedigree. Built for discerning riders who demand relentless power delivery, supreme handling dynamics, and unmistakable road presence.' }}
                    </p>

                    <div class="pt-4 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-slate-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>2 Master Smart Keys</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>OEM Tool Kit</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>PDI Inspection Sheet</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>3-Yr Factory Warranty</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ═══════════════════════════════════════════════════════════════
                 RIGHT COLUMN: BUY FOR PURCHASE & FINANCE COCKPIT (4 COLS)
                 ═══════════════════════════════════════════════════════════════ --}}
            <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24" id="reserve">

                {{-- ═══ 1. PRIMARY BUY / PURCHASE RESERVATION CARD ═══ --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-md shadow-slate-900/5 space-y-6">

                    {{-- Brand & Title --}}
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-rose-600 uppercase tracking-widest">{{ $bike->brand }}</span>
                            <span class="font-mono text-xs font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg">{{ $bike->sku }}</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 mt-1 leading-tight">
                            {{ $bike->name }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Model Year {{ $bike->model_year }} • Ready for Immediate Delivery
                        </p>
                    </div>

                    {{-- Ex-Showroom & Price Breakdown --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-3">
                        <div class="flex items-baseline justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Ex-Showroom Price</span>
                                <span class="text-2xl sm:text-3xl font-mono font-black text-slate-900 leading-none">
                                    {{ $bike->formatted_price }}
                                </span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-lg">
                                In Stock
                            </span>
                        </div>

                        {{-- On-Road Estimate Breakdown --}}
                        <div class="pt-3 border-t border-slate-200 text-xs space-y-1.5 text-slate-600">
                            <div class="flex justify-between">
                                <span class="text-slate-500">RTO &amp; Road Tax (Est.):</span>
                                <span class="font-mono font-bold text-slate-800">₹{{ number_format(round($bike->price * 0.13)) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Comprehensive 5-Yr Insurance:</span>
                                <span class="font-mono font-bold text-slate-800">₹{{ number_format(round($bike->price * 0.035)) }}</span>
                            </div>
                            <div class="flex justify-between pt-1 border-t border-slate-200/60 font-bold text-slate-900">
                                <span>Estimated On-Road Total:</span>
                                <span class="text-sm font-mono text-[#FF6B00]">₹<span x-text="onRoadPrice.toLocaleString('en-IN')"></span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Available Variants & Color Swatches --}}
                    @if($bike->variants->isNotEmpty())
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-900 uppercase tracking-wider block">
                                Choose Color / Trim Variant
                            </span>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($bike->variants as $variant)
                                    <label class="flex items-center justify-between p-3 rounded-xl border transition cursor-pointer"
                                           :class="selectedVariant === '{{ $variant->name }}' ? 'bg-orange-50/80 border-[#FF6B00] ring-1 ring-orange-200' : 'bg-white border-slate-200 hover:border-slate-300'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio"
                                                   name="variant_choice"
                                                   value="{{ $variant->name }}"
                                                   x-model="selectedVariant"
                                                   @change="selectedColorHex = '{{ $variant->color_hex ?? '#CC0000' }}'"
                                                   class="w-4 h-4 text-[#FF6B00] border-slate-300 focus:ring-[#FF6B00]">
                                            <div class="w-4 h-4 rounded-full border border-slate-300 shrink-0" style="background-color: {{ $variant->color_hex ?? '#CC0000' }};"></div>
                                            <div class="text-xs">
                                                <p class="font-bold text-slate-900">{{ $variant->name }}</p>
                                                <p class="text-[10px] text-slate-500">{{ $variant->color_name ?? 'Factory Finish' }}</p>
                                            </div>
                                        </div>
                                        <span class="text-[11px] font-bold text-emerald-600 font-mono">
                                            {{ $variant->stock_quantity > 0 ? $variant->stock_quantity . ' in stock' : 'Pre-order' }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ═══ THE PRIMARY BUY / RESERVE ACTION BUTTON ═══ --}}
                    <div class="space-y-3 pt-2">
                        <a :href="'{{ route('showrooms.bikes.checkout', [$showroom, $bike]) }}?variant=' + encodeURIComponent(selectedVariant)"
                           id="btn-book-buy-bike"
                           class="w-full py-4 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-emerald-600/25 transition flex items-center justify-center gap-2.5 text-center cursor-pointer">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>Book / Reserve to Buy</span>
                        </a>
                        <p class="text-[11px] text-center text-slate-500 leading-tight">
                            Reserve with ₹10,000 refundable advance token • 100% Price Lock Guaranteed
                        </p>

                        {{-- Secondary Actions: WhatsApp & Call --}}
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            @if($showroom->getSetting('whatsapp_number'))
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $showroom->getSetting('whatsapp_number')) }}?text={{ urlencode('Hi! I want to purchase the ' . $bike->name . ' (SKU: ' . $bike->sku . ') from ' . $showroom->name . '.') }}"
                                   target="_blank"
                                   class="py-2.5 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition flex items-center justify-center gap-1.5 shadow-2xs">
                                    <svg class="w-4 h-4 fill-emerald-600" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    <span>WhatsApp</span>
                                </a>
                            @endif

                            @if($showroom->phone)
                                <a href="tel:{{ $showroom->phone }}"
                                   class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs border border-slate-200 transition flex items-center justify-center gap-1.5 shadow-2xs">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span>Call Sales</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Dealership Physical Pit Info --}}
                    <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-2">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <div>
                                <span class="font-bold text-slate-900 block">{{ $showroom->name }}</span>
                                <span>{{ $showroom->address ?? 'Bangalore Pitstop' }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ═══ 2. INTERACTIVE EMI & FINANCE CALCULATOR ═══ --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Finance & EMI Calculator</span>
                        </h3>
                        <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-bold font-mono">8.5% APR</span>
                    </div>

                    {{-- Dynamic EMI Result Display --}}
                    <div class="p-4 rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between">
                        <div>
                            <span class="text-[11px] text-slate-400 font-medium block">Estimated Monthly EMI</span>
                            <span class="text-xs text-slate-300">Based on Selected Down Payment</span>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-black font-mono text-emerald-400">
                                ₹<span x-text="monthlyEmi.toLocaleString('en-IN')"></span>
                            </p>
                            <span class="text-[10px] text-slate-400">/ month</span>
                        </div>
                    </div>

                    {{-- Down Payment Slider --}}
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between font-semibold">
                            <span class="text-slate-500">Down Payment:</span>
                            <span class="font-mono text-slate-900 font-bold">
                                ₹<span x-text="downPaymentAmount.toLocaleString('en-IN')"></span> (<span x-text="downPaymentPct"></span>%)
                            </span>
                        </div>
                        <input type="range"
                               min="10"
                               max="50"
                               step="5"
                               x-model="downPaymentPct"
                               class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#FF6B00]">
                    </div>

                    {{-- Tenure Buttons --}}
                    <div class="space-y-1.5 text-xs">
                        <span class="text-slate-500 font-semibold block">Loan Tenure (Months):</span>
                        <div class="grid grid-cols-4 gap-1.5">
                            <button type="button" @click="loanTenureMonths = 12" :class="loanTenureMonths === 12 ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-700 font-medium'" class="py-1.5 rounded-lg text-xs transition cursor-pointer">12m</button>
                            <button type="button" @click="loanTenureMonths = 24" :class="loanTenureMonths === 24 ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-700 font-medium'" class="py-1.5 rounded-lg text-xs transition cursor-pointer">24m</button>
                            <button type="button" @click="loanTenureMonths = 36" :class="loanTenureMonths === 36 ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-700 font-medium'" class="py-1.5 rounded-lg text-xs transition cursor-pointer">36m</button>
                            <button type="button" @click="loanTenureMonths = 48" :class="loanTenureMonths === 48 ? 'bg-slate-900 text-white font-bold' : 'bg-slate-100 text-slate-700 font-medium'" class="py-1.5 rounded-lg text-xs transition cursor-pointer">48m</button>
                        </div>
                    </div>
                </div>

                {{-- ═══ 3. BUYER PROTECTION ASSURANCES ═══ --}}
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-3 text-xs">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Official Dealership Buyer Guarantee</span>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-slate-700">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>100% Price Lock at Booking Time</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-700">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Direct Factory Allocation &amp; VIN Registry</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-700">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Fully Refundable ₹10,000 Advance Token</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- ═══ SIMILAR FLEET SECTION ═══ --}}
        @if($relatedBikes->isNotEmpty())
            <div class="pt-10 border-t border-slate-200 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs uppercase font-bold text-rose-600 tracking-wider">Explore Alternative Machines</span>
                        <h3 class="text-2xl font-black font-display text-slate-900 mt-0.5">Similar Motorcycles in Showroom</h3>
                    </div>
                    <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition flex items-center gap-1">
                        <span>View All Motorcycles</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedBikes as $relBike)
                        <div class="group bg-white rounded-2xl border border-slate-200 hover:border-slate-300 overflow-hidden flex flex-col transition duration-300 hover:shadow-lg">
                            <div class="aspect-[16/10] overflow-hidden bg-slate-100 relative">
                                <img src="{{ $relBike->primary_image_url }}" alt="{{ $relBike->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-2.5 left-2.5">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white/95 text-slate-800 shadow-xs">
                                        {{ $relBike->brand }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                                <div>
                                    <h4 class="font-display font-bold text-sm text-slate-900 group-hover:text-rose-600 transition leading-snug">
                                        <a href="{{ route('showrooms.bikes.show', [$showroom, $relBike]) }}">
                                            {{ $relBike->name }}
                                        </a>
                                    </h4>
                                    <span class="text-xs font-mono font-black text-slate-900 mt-1 block">{{ $relBike->formatted_price }}</span>
                                </div>
                                <a href="{{ route('showrooms.bikes.show', [$showroom, $relBike]) }}"
                                   class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-900 hover:text-white font-bold text-xs text-slate-800 transition text-center">
                                    <span>View &amp; Buy Online</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </main>

    {{-- ═══ PURCHASE RESERVATION MODAL (BUY BIKE ONLINE) ═══ --}}
    <div x-cloak x-show="buyModalOpen"
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="buyModalOpen = false"></div>
        <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 text-left shadow-2xl border border-slate-200 my-8 overflow-hidden"
                 @click.away="if (!bookingTermsModalOpen) buyModalOpen = false">

                <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                            Instant Vehicle Purchase Reservation
                        </span>
                        <h3 class="text-2xl font-black font-display text-slate-900 mt-1">Book &amp; Reserve Motorcycle</h3>
                        <p class="text-xs text-slate-500">{{ $bike->name }} (SKU: {{ $bike->sku }}) • {{ $showroom->name }}</p>
                    </div>
                    <button type="button" @click="buyModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Purchase Reservation Form --}}
                <form method="POST" action="{{ route('showrooms.enquiries.store', $showroom) }}" class="space-y-4 pt-4 text-xs">
                    @csrf
                    <input type="hidden" name="bike_id" value="{{ $bike->id }}">
                    <input type="hidden" name="type" value="purchase">
                    <input type="hidden" name="booking_amount" value="10000">
                    <input type="hidden" name="payment_method" :value="bookingPaymentMethod">

                    {{-- Selected Machine Badge --}}
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400">Selected Machine</span>
                            <p class="font-bold text-slate-900 text-sm">{{ $bike->name }}</p>
                            <p class="text-[11px] text-slate-500 font-mono">Trim: <span x-text="selectedVariant"></span></p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Booking Token</span>
                            <p class="text-lg font-black font-mono text-emerald-600">₹10,000</p>
                            <span class="text-[10px] text-slate-500" x-text="bookingPaymentMethod === 'razorpay' ? 'Online Token (Refundable)' : 'Pay at Showroom (Refundable)'"></span>
                        </div>
                    </div>

                    {{-- Booking & Payment Method Choice --}}
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Select Booking &amp; Payment Method <span class="text-rose-600">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            {{-- Razorpay Online Booking --}}
                            <div @click="bookingPaymentMethod = 'razorpay'"
                                 :class="bookingPaymentMethod === 'razorpay' ? 'border-emerald-600 bg-emerald-50/50 ring-1 ring-emerald-600 shadow-xs' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-3 rounded-2xl border cursor-pointer transition flex flex-col justify-between">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 text-xs">Razorpay Online Booking</p>
                                            <p class="text-[10px] font-semibold text-emerald-700">Instant Digital Confirmation</p>
                                        </div>
                                    </div>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 mt-0.5"
                                          :class="bookingPaymentMethod === 'razorpay' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white'">
                                        <svg x-show="bookingPaymentMethod === 'razorpay'" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-600 mt-2 leading-relaxed">
                                    Pay ₹10,000 refundable advance online via UPI, Credit/Debit card, or NetBanking to lock chassis instantly.
                                </p>
                            </div>

                            {{-- Normal Booking (Pay at Showroom) --}}
                            <div @click="bookingPaymentMethod = 'offline'"
                                 :class="bookingPaymentMethod === 'offline' ? 'border-emerald-600 bg-emerald-50/50 ring-1 ring-emerald-600 shadow-xs' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-3 rounded-2xl border cursor-pointer transition flex flex-col justify-between">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 text-xs">Normal Booking</p>
                                            <p class="text-[10px] font-semibold text-slate-500">Pay Token at Dealership</p>
                                        </div>
                                    </div>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 mt-0.5"
                                          :class="bookingPaymentMethod === 'offline' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white'">
                                        <svg x-show="bookingPaymentMethod === 'offline'" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-600 mt-2 leading-relaxed">
                                    Reserve priority chassis offline. Visit dealership within 48 hours to complete document KYC &amp; deposit token.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Your Full Name <span class="text-rose-600">*</span></label>
                            <input type="text" name="customer_name" required placeholder="e.g. Rahul Verma"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 text-xs font-medium">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Phone Number <span class="text-rose-600">*</span></label>
                            <input type="tel" name="customer_phone" required placeholder="+91 98765 43210"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 text-xs font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address <span class="text-rose-600">*</span></label>
                        <input type="email" name="customer_email" required placeholder="rahul@example.com"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 text-xs font-medium">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Preferred Delivery Method</label>
                            <select name="delivery_method"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs font-medium focus:bg-white cursor-pointer">
                                <option value="showroom_handover">Showroom Pit Handover (Bangalore)</option>
                                <option value="home_delivery">Enclosed Flatbed Home Delivery</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Target Delivery Date</label>
                            <input type="date" name="preferred_date" min="{{ now()->format('Y-m-d') }}" value="{{ now()->addDays(3)->format('Y-m-d') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs font-medium focus:bg-white">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Customer Remarks / Customization Requests</label>
                        <textarea name="message" rows="2" placeholder="e.g. Please include titanium slip-on exhaust or tank pad kit."
                                  class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs font-medium focus:bg-white"></textarea>
                    </div>

                    {{-- Terms & Conditions Checkbox with Modal Trigger --}}
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-start gap-2.5">
                            <input type="checkbox"
                                   name="terms_accepted"
                                   id="booking_terms_accepted"
                                   value="1"
                                   x-model="termsAccepted"
                                   required
                                   class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <div class="text-xs text-slate-700 leading-snug">
                                <label for="booking_terms_accepted" class="cursor-pointer select-none">
                                    I agree to the
                                </label>
                                <button type="button"
                                        @click.prevent.stop="bookingTermsModalOpen = true"
                                        class="font-bold text-emerald-600 hover:text-emerald-800 underline underline-offset-2 cursor-pointer transition inline-block">
                                    Terms &amp; Conditions and Reservation Policy
                                </button>
                                <span class="text-rose-600">*</span>
                                <span class="block text-[11px] text-slate-500 mt-0.5">
                                    View policy on 100% refundable token, priority chassis lock, and cancellation guidelines.
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1">
                        <p class="font-bold flex items-center gap-1.5 text-xs">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>100% Refundable Booking Guarantee</span>
                        </p>
                        <p class="text-[11px] text-emerald-800 leading-relaxed">
                            Submitting this reservation locks your vehicle chassis priority. You may inspect the bike at {{ $showroom->name }} or cancel anytime prior to invoicing for a 100% full refund.
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <button type="submit"
                                :disabled="!termsAccepted"
                                :class="termsAccepted ? 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white shadow-md shadow-emerald-600/20 cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                class="w-full py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition flex items-center justify-center gap-2">
                            <span x-text="bookingPaymentMethod === 'razorpay' ? 'Proceed to Pay ₹10,000 via Razorpay' : 'Confirm Normal Booking (Pay at Showroom)'"></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        <p x-show="!termsAccepted" class="text-center text-[10px] text-amber-600 font-medium mt-1.5">
                            * Please review and check the Terms &amp; Conditions to proceed.
                        </p>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- ═══ TERMS & CONDITIONS POPUP MODAL ═══ --}}
    <div x-cloak x-show="bookingTermsModalOpen"
         class="fixed inset-0 z-60 overflow-y-auto"
         role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="bookingTermsModalOpen = false"></div>
        <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 text-left shadow-2xl border border-slate-200 my-8 overflow-hidden"
                 @click.away="bookingTermsModalOpen = false">

                {{-- Header --}}
                <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                            Legal &amp; Dealership Guidelines
                        </span>
                        <h3 class="text-xl font-black font-display text-slate-900 mt-1">Booking Terms &amp; Reservation Policy</h3>
                        <p class="text-xs text-slate-500">{{ $showroom->name }} • Authorized Customer Agreement</p>
                    </div>
                    <button type="button" @click="bookingTermsModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Terms Body --}}
                <div class="py-4 space-y-4 max-h-[60vh] overflow-y-auto pr-2 text-xs text-slate-700 leading-relaxed">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-mono text-[11px] font-bold inline-flex items-center justify-center">1</span>
                            Token Advance &amp; Chassis Allocation
                        </h4>
                        <p class="text-[11px] text-slate-600 pl-6.5">
                            The booking token amount of <strong>₹10,000</strong> secures your vehicle queue priority for <strong>{{ $bike->name }}</strong>. Submitting a reservation blocks the vehicle VIN/chassis against your contact details.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-200 space-y-1">
                        <h4 class="font-bold text-emerald-950 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-mono text-[11px] font-bold inline-flex items-center justify-center">2</span>
                            100% Full Refund Guarantee
                        </h4>
                        <p class="text-[11px] text-emerald-900 pl-6.5">
                            Your reservation advance is <strong>100% refundable</strong> with zero cancellation charges prior to final commercial invoicing and RTO tax registration. If you change your mind for any reason, the advance will be credited back via original payment channel within 3–5 working days.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-800 font-mono text-[11px] font-bold inline-flex items-center justify-center">3</span>
                            Normal Booking (Pay at Showroom)
                        </h4>
                        <p class="text-[11px] text-slate-600 pl-6.5">
                            If choosing Normal Booking, your provisional allocation is held for <strong>48 hours</strong>. You must visit {{ $showroom->name }} with valid ID/Address proofs to inspect the motorcycle and deposit the booking advance in person; unconfirmed bookings may be released thereafter.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-800 font-mono text-[11px] font-bold inline-flex items-center justify-center">4</span>
                            Razorpay Online Booking
                        </h4>
                        <p class="text-[11px] text-slate-600 pl-6.5">
                            Online transactions are encrypted with 256-bit SSL and processed via Razorpay. Supported modes include UPI (GPay, PhonePe, Paytm), Credit &amp; Debit Cards, NetBanking, and authorized wallets. A digital receipt and tracking ID will be generated immediately upon authorization.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-800 font-mono text-[11px] font-bold inline-flex items-center justify-center">5</span>
                            On-Road Pricing &amp; Delivery Settlement
                        </h4>
                        <p class="text-[11px] text-slate-600 pl-6.5">
                            The ₹10,000 token is adjusted against your final invoice. The final on-road amount is subject to state RTO road tax rates, mandatory insurance coverages, and any manufacturer ex-showroom price revisions applicable on the invoice generation date.
                        </p>
                    </div>
                </div>

                {{-- Modal Action Footer --}}
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <button type="button"
                            @click="bookingTermsModalOpen = false"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                        Close
                    </button>
                    <button type="button"
                            @click="termsAccepted = true; bookingTermsModalOpen = false"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm cursor-pointer flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>I Read &amp; Accept Booking Terms</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Motorcycle Showroom Footer --}}
    @include('showrooms.partials.footer')
</div>

{{-- Google WebGL 3D Model-Viewer Library --}}
<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.4.0/model-viewer.min.js"></script>

<style>
model-viewer#bike-model-viewer {
    width: 100%;
    height: 100%;
    min-height: 440px;
    --poster-color: transparent;
    outline: none;
}
model-viewer#bike-model-viewer::part(default-ar-button) {
    background-color: #0F172A;
    border-radius: 12px;
    color: white;
}
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
@endsection
