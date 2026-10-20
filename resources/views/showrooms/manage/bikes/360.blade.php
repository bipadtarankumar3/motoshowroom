@extends('layouts.app')

@section('title', '360° Studio - ' . $bike->name . ' - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans selection:bg-cyan-600 selection:text-white">

    <!-- Showroom Branded Topbar -->
    <header class="border-b border-slate-200 bg-white/90 backdrop-blur sticky top-0 z-40">
        <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <div class="flex items-center gap-3">
                    <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}" class="p-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-cyan-700 uppercase tracking-wider">Interactive 360° Studio</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-600 animate-pulse"></span>
                        </div>
                        <h1 class="text-lg sm:text-xl font-black font-display text-slate-900 tracking-tight leading-none mt-0.5">
                            {{ $bike->name }} <span class="text-slate-500 font-normal">({{ $bike->model_year }})</span>
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if($bike->is_published && $bike->has360View() && $hasFeature)
                        <a href="{{ route('showrooms.bikes.360', [$showroom, $bike]) }}" target="_blank"
                           class="px-4 py-2 rounded-xl bg-cyan-50 hover:bg-cyan-100 text-cyan-800 border border-cyan-200 text-xs font-semibold flex items-center gap-2 transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Live Storefront 360</span>
                        </a>
                    @endif
                    <a href="{{ route('showrooms.manage.bikes.edit', [$showroom, $bike]) }}"
                       class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-semibold text-slate-700 hover:text-slate-900 transition">
                        Edit Specs
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 space-y-6">

        <!-- Plan Entitlement Warning (if plan lacks 360 viewer) -->
        @if(! $hasFeature)
            <div class="mb-8 rounded-3xl bg-amber-50 border border-amber-200 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">360° Studio Gated Feature</h3>
                        <p class="text-xs text-amber-800 mt-0.5">
                            Your current subscription plan does not include the Interactive 360° Motorcycle Viewer. Upgrade to Pro or Enterprise to enable 360 turntable sequences, hotspots, and 3D showcases.
                        </p>
                    </div>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}"
                   class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shrink-0 transition shadow-xs">
                    Upgrade Subscription
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Column: Interactive 360 Previewer & Hotspots Overlay (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 360 Studio Turntable Canvas Box -->
                <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-6 shadow-xs relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">Studio Turntable Preview</h2>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span id="preview-angle-badge" class="px-2.5 py-1 rounded-lg bg-cyan-50 text-cyan-800 font-mono font-bold border border-cyan-200">
                                0°
                            </span>
                            <span class="text-slate-500">
                                <span id="preview-frame-index" class="text-slate-900 font-bold">{{ $frames->isNotEmpty() ? '1' : '0' }}</span> / {{ $frames->count() }} Frames
                            </span>
                        </div>
                    </div>

                    <!-- Turntable Stage -->
                    <div id="turntable-container"
                         class="relative aspect-[16/10] rounded-2xl bg-gradient-to-b from-slate-100 via-slate-50 to-slate-200/80 border border-slate-200 overflow-hidden cursor-grab active:cursor-grabbing select-none flex items-center justify-center">

                        @if($frames->isNotEmpty())
                            <!-- Turntable Image Elements -->
                            @foreach($frames as $index => $frame)
                                <img src="{{ $frame->image_url }}"
                                     alt="{{ $frame->alt_text }}"
                                     data-frame-index="{{ $index }}"
                                     data-frame-angle="{{ $frame->frame_angle }}"
                                     class="turntable-frame absolute inset-0 w-full h-full object-contain pointer-events-none transition-opacity duration-75 {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
                            @endforeach

                            <!-- Hotspots Overlay Layer -->
                            <div id="preview-hotspots-layer" class="absolute inset-0 pointer-events-none z-20">
                                @foreach($hotspots as $spot)
                                    <div class="hotspot-pin absolute -translate-x-1/2 -translate-y-1/2 pointer-events-auto group cursor-pointer"
                                         data-angle="{{ $spot['frame_angle'] }}"
                                         style="left: {{ $spot['x_pct'] }}%; top: {{ $spot['y_pct'] }}%;">
                                        <div class="w-6 h-6 rounded-full bg-cyan-500/20 border-2 border-cyan-600 flex items-center justify-center shadow-md animate-pulse">
                                            <div class="w-2 h-2 rounded-full bg-cyan-600"></div>
                                        </div>
                                        <!-- Hotspot Tooltip -->
                                        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 w-48 p-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs shadow-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-30">
                                            <p class="font-bold text-cyan-700">{{ $spot['title'] }}</p>
                                            @if(!empty($spot['description']))
                                                <p class="text-[11px] text-slate-600 mt-1 leading-snug">{{ $spot['description'] }}</p>
                                            @endif
                                            <span class="inline-block mt-1 text-[9px] text-slate-400 uppercase font-mono">Visible at {{ $spot['frame_angle'] }}°</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Drag Instruction Hint (Fades out upon interaction) -->
                            <div id="drag-hint" class="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full bg-white/95 border border-slate-300 text-[11px] font-semibold text-slate-700 shadow-sm flex items-center gap-2 pointer-events-none transition-opacity">
                                <svg class="w-4 h-4 text-cyan-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span>Click & Drag to rotate 360°</span>
                            </div>
                        @else
                            <div class="p-8 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <p class="text-sm font-semibold text-slate-700">No 360° frames uploaded yet</p>
                                <p class="text-xs text-slate-500 mt-1">Upload a sequence of turntable photos below to activate the 360 view.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Turntable Playback Controls -->
                    <div class="mt-4 pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <button type="button" id="btn-spin-left" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 border border-slate-200 transition text-xs font-semibold flex items-center gap-1 shadow-xs" title="Rotate Left 10°">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button type="button" id="btn-toggle-play" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 border border-slate-200 transition text-xs font-semibold flex items-center gap-1.5 shadow-xs">
                                <svg id="play-icon" class="w-4 h-4 text-cyan-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                <span id="play-text">Auto Spin</span>
                            </button>
                            <button type="button" id="btn-spin-right" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 border border-slate-200 transition text-xs font-semibold flex items-center gap-1 shadow-xs" title="Rotate Right 10°">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <button type="button" id="btn-reset-view" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 border border-slate-200 transition text-xs font-semibold shadow-xs">
                                Reset 0°
                            </button>
                        </div>

                        <!-- Rotation Scrubber Slider -->
                        <div class="flex-1 min-w-[160px] flex items-center gap-2">
                            <input type="range" id="angle-scrubber" min="0" max="359" value="0"
                                   class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-cyan-600">
                        </div>
                    </div>
                </div>

                <!-- Interactive Hotspots Management Card -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Interactive Feature Hotspots</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Pins appear when the motorcycle reaches the designated angle, highlighting technical features.</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                            {{ count($hotspots) }} Active Hotspots
                        </span>
                    </div>

                    <form method="POST" action="{{ route('showrooms.manage.bikes.360.hotspots', [$showroom, $bike]) }}">
                        @csrf

                        <div id="hotspots-list-container" class="space-y-3 mb-4">
                            @forelse($hotspots as $index => $spot)
                                <div class="hotspot-item p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-12 gap-2 w-full">
                                        <div class="sm:col-span-4">
                                            <label class="text-[10px] text-slate-500 font-bold uppercase block mb-1">Feature Title</label>
                                            <input type="text" name="hotspots[{{ $index }}][title]" value="{{ $spot['title'] }}" required
                                                   class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                                        </div>
                                        <div class="sm:col-span-4">
                                            <label class="text-[10px] text-slate-500 font-bold uppercase block mb-1">Description</label>
                                            <input type="text" name="hotspots[{{ $index }}][description]" value="{{ $spot['description'] ?? '' }}"
                                                   class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="text-[10px] text-slate-500 font-bold uppercase block mb-1">Angle (°)</label>
                                            <input type="number" name="hotspots[{{ $index }}][frame_angle]" value="{{ $spot['frame_angle'] }}" min="0" max="359" required
                                                   class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                                        </div>
                                        <div class="sm:col-span-2 flex gap-1">
                                            <div class="w-1/2">
                                                <label class="text-[10px] text-slate-500 font-bold uppercase block mb-1">X %</label>
                                                <input type="number" name="hotspots[{{ $index }}][x_pct]" value="{{ $spot['x_pct'] }}" min="0" max="100" step="1" required
                                                       class="w-full px-2 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                                            </div>
                                            <div class="w-1/2">
                                                <label class="text-[10px] text-slate-500 font-bold uppercase block mb-1">Y %</label>
                                                <input type="number" name="hotspots[{{ $index }}][y_pct]" value="{{ $spot['y_pct'] }}" min="0" max="100" step="1" required
                                                       class="w-full px-2 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" onclick="this.closest('.hotspot-item').remove()"
                                            class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 transition shrink-0" title="Remove hotspot">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            @empty
                                <p id="no-hotspots-text" class="text-xs text-slate-500 py-3 text-center">No interactive hotspots configured. Click "Add Hotspot" below to create feature callouts.</p>
                            @endforelse
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <button type="button" id="btn-add-hotspot"
                                    class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-semibold text-slate-700 transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Add Hotspot</span>
                            </button>

                            <button type="submit" @disabled(! $hasFeature)
                                    class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed">
                                Save Hotspots
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Settings & Turntable Frame Sequence Upload (5 cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- 360 Studio General Configuration Card -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Viewer Configuration</span>
                    </h3>

                    <form method="POST" action="{{ route('showrooms.manage.bikes.360.settings', [$showroom, $bike]) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Enable 360 View Switch -->
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Enable 360° Studio</span>
                                <span class="text-[11px] text-slate-500">Display interactive 360 view on the public motorcycle profile.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="has_360_view" value="0">
                                <input type="checkbox" name="has_360_view" value="1" {{ old('has_360_view', $bike->has_360_view) ? 'checked' : '' }}
                                       @disabled(! $hasFeature)
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-600"></div>
                            </label>
                        </div>

                        <!-- Viewer Engine Type -->
                        <div>
                            <label for="viewer_type" class="block text-xs font-bold uppercase text-slate-600 mb-1">Viewer Engine</label>
                            <select name="viewer_type" id="viewer_type" @disabled(! $hasFeature)
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                                <option value="image_sequence" {{ old('viewer_type', $bike->viewer_type) === 'image_sequence' ? 'selected' : '' }}>
                                    Turntable Image Sequence (Multi-Frame)
                                </option>
                                <option value="gltf_model" {{ old('viewer_type', $bike->viewer_type) === 'gltf_model' ? 'selected' : '' }}>
                                    3D WebGL Model (Three.js GLTF/GLB)
                                </option>
                            </select>
                        </div>

                        <!-- 3D Model Path (GLTF/GLB) -->
                        <div id="gltf-model-input-group" class="{{ old('viewer_type', $bike->viewer_type) === 'gltf_model' ? '' : 'hidden' }}">
                            <label for="model_3d_path" class="block text-xs font-bold uppercase text-slate-600 mb-1">3D GLTF / GLB Model URL</label>
                            <input type="text" name="model_3d_path" id="model_3d_path" value="{{ old('model_3d_path', $bike->model_3d_path) }}"
                                   placeholder="https://cdn.example.com/models/motorcycle.glb"
                                   @disabled(! $hasFeature)
                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                            <span class="text-[10px] text-slate-500 mt-1 block">Full URL or storage path to an optimized 3D GLTF/GLB motorcycle file.</span>
                        </div>

                        <!-- Auto-Rotate & Speed Settings -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="rotation_speed" class="block text-xs font-bold uppercase text-slate-600 mb-1">Rotation Speed (1-10)</label>
                                <input type="number" name="rotation_speed" id="rotation_speed" min="1" max="10"
                                       value="{{ old('rotation_speed', $settings['rotation_speed'] ?? 3) }}"
                                       @disabled(! $hasFeature)
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                            </div>
                            <div>
                                <label for="default_angle" class="block text-xs font-bold uppercase text-slate-600 mb-1">Default Angle (0-359°)</label>
                                <input type="number" name="default_angle" id="default_angle" min="0" max="359"
                                       value="{{ old('default_angle', $settings['default_angle'] ?? 0) }}"
                                       @disabled(! $hasFeature)
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                            </div>
                        </div>

                        <!-- Behavioral Checkboxes -->
                        <div class="space-y-2 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="auto_rotate" value="1" {{ old('auto_rotate', $settings['auto_rotate'] ?? true) ? 'checked' : '' }}
                                       @disabled(! $hasFeature)
                                       class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                <span class="text-xs text-slate-700">Auto-rotate turntable on page load</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="reverse_direction" value="1" {{ old('reverse_direction', $settings['reverse_direction'] ?? false) ? 'checked' : '' }}
                                       @disabled(! $hasFeature)
                                       class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                <span class="text-xs text-slate-700">Reverse spin direction</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="enable_zoom" value="1" {{ old('enable_zoom', $settings['enable_zoom'] ?? true) ? 'checked' : '' }}
                                       @disabled(! $hasFeature)
                                       class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                <span class="text-xs text-slate-700">Enable click-to-zoom in full screen</span>
                            </label>
                        </div>

                        <button type="submit" @disabled(! $hasFeature)
                                class="w-full py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed">
                            Update Viewer Settings
                        </button>
                    </form>
                </div>

                <!-- Turntable Frames Importer Card -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Add 360° Frames</h3>
                        @if($frames->isNotEmpty())
                            <form method="POST" action="{{ route('showrooms.manage.bikes.360.frames.clear', [$showroom, $bike]) }}"
                                  onsubmit="return confirm('Are you sure you want to remove all {{ $frames->count() }} 360° frames?');">
                                @csrf
                                <button type="submit" @disabled(! $hasFeature)
                                        class="text-xs text-rose-600 hover:text-rose-700 transition font-semibold">
                                    Clear All Frames
                                </button>
                            </form>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('showrooms.manage.bikes.360.frames.store', [$showroom, $bike]) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <!-- Bulk URLs Input -->
                        <div>
                            <label for="bulk_urls" class="block text-xs font-bold uppercase text-slate-600 mb-1">
                                Bulk Frame URLs (One per line)
                            </label>
                            <textarea name="bulk_urls" id="bulk_urls" rows="4" @disabled(! $hasFeature)
                                      placeholder="https://cdn.example.com/bike-frame-00.jpg&#10;https://cdn.example.com/bike-frame-01.jpg&#10;https://cdn.example.com/bike-frame-02.jpg"
                                      class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono"></textarea>
                            <span class="text-[10px] text-slate-500 mt-1 block">Angles will be distributed evenly across 360° based on total lines entered.</span>
                        </div>

                        <!-- Or Upload Local Files -->
                        <div class="pt-2 border-t border-slate-200">
                            <label for="frame_files" class="block text-xs font-bold uppercase text-slate-600 mb-1">
                                Or Upload Image Files
                            </label>
                            <input type="file" name="frame_files[]" id="frame_files" multiple accept="image/*" @disabled(! $hasFeature)
                                   class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-cyan-700 hover:file:bg-slate-200 cursor-pointer">
                        </div>

                        <button type="submit" @disabled(! $hasFeature)
                                class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition disabled:opacity-50 disabled:cursor-not-allowed shadow-xs">
                            Import Frames
                        </button>
                    </form>

                    <!-- Existing Frames Thumbnail Grid -->
                    @if($frames->isNotEmpty())
                        <div class="mt-6 pt-6 border-t border-slate-200">
                            <span class="text-xs font-bold uppercase text-slate-600 block mb-3">Frame Sequence ({{ $frames->count() }} Frames)</span>
                            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 max-h-56 overflow-y-auto pr-1">
                                @foreach($frames as $idx => $f)
                                    <div class="relative group rounded-xl bg-slate-100 border border-slate-200 overflow-hidden aspect-[4/3]">
                                        <img src="{{ $f->image_url }}" alt="{{ $f->alt_text }}" class="w-full h-full object-cover">
                                        <span class="absolute bottom-1 left-1 px-1 py-0.5 rounded text-[8px] font-mono font-bold bg-white/90 text-cyan-800 border border-slate-200 shadow-xs">
                                            {{ $f->frame_angle }}°
                                        </span>
                                        <form method="POST" action="{{ route('showrooms.manage.bikes.360.frames.destroy', [$showroom, $bike, $f]) }}"
                                              class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded-md bg-rose-600 text-white hover:bg-rose-700" title="Delete frame">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </main>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('turntable-container');
    const frames = Array.from(document.querySelectorAll('.turntable-frame'));
    const hotspots = Array.from(document.querySelectorAll('.hotspot-pin'));
    const angleBadge = document.getElementById('preview-angle-badge');
    const frameIndexDisplay = document.getElementById('preview-frame-index');
    const scrubber = document.getElementById('angle-scrubber');
    const dragHint = document.getElementById('drag-hint');

    const totalFrames = frames.length;
    if (totalFrames === 0) return;

    let currentIndex = 0;
    let isDragging = false;
    let startX = 0;
    let autoSpinInterval = null;
    const stepDegrees = 360 / totalFrames;

    function showFrame(index) {
        if (index < 0) index = totalFrames - 1;
        if (index >= totalFrames) index = 0;

        frames.forEach((f, idx) => {
            if (idx === index) {
                f.classList.remove('opacity-0', 'z-0');
                f.classList.add('opacity-100', 'z-10');
            } else {
                f.classList.remove('opacity-100', 'z-10');
                f.classList.add('opacity-0', 'z-0');
            }
        });

        currentIndex = index;
        const currentAngle = Math.round(index * stepDegrees);

        if (angleBadge) angleBadge.textContent = currentAngle + '°';
        if (frameIndexDisplay) frameIndexDisplay.textContent = (index + 1);
        if (scrubber) scrubber.value = currentAngle;

        // Filter and display hotspots matching current viewing angle (+/- half step)
        const halfStep = stepDegrees / 1.5;
        hotspots.forEach(pin => {
            const pinAngle = parseFloat(pin.dataset.angle);
            const diff = Math.abs(currentAngle - pinAngle);
            const circularDiff = Math.min(diff, 360 - diff);
            if (circularDiff <= halfStep) {
                pin.classList.remove('hidden');
                pin.classList.add('animate-fade-in');
            } else {
                pin.classList.add('hidden');
            }
        });
    }

    // Drag-to-rotate interaction
    container.addEventListener('mousedown', function (e) {
        isDragging = true;
        startX = e.clientX;
        stopAutoSpin();
        if (dragHint) dragHint.classList.add('opacity-0');
    });

    window.addEventListener('mouseup', function () {
        isDragging = false;
    });

    window.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        const deltaX = e.clientX - startX;
        const sensitivity = 8; // Pixels per frame switch
        if (Math.abs(deltaX) > sensitivity) {
            if (deltaX > 0) {
                showFrame(currentIndex - 1);
            } else {
                showFrame(currentIndex + 1);
            }
            startX = e.clientX;
        }
    });

    // Touch event support for tablets & mobile
    container.addEventListener('touchstart', function (e) {
        if (e.touches.length === 1) {
            isDragging = true;
            startX = e.touches[0].clientX;
            stopAutoSpin();
            if (dragHint) dragHint.classList.add('opacity-0');
        }
    }, { passive: true });

    window.addEventListener('touchend', function () {
        isDragging = false;
    });

    window.addEventListener('touchmove', function (e) {
        if (!isDragging || e.touches.length !== 1) return;
        const deltaX = e.touches[0].clientX - startX;
        const sensitivity = 10;
        if (Math.abs(deltaX) > sensitivity) {
            if (deltaX > 0) {
                showFrame(currentIndex - 1);
            } else {
                showFrame(currentIndex + 1);
            }
            startX = e.touches[0].clientX;
        }
    }, { passive: true });

    // Buttons control
    const btnLeft = document.getElementById('btn-spin-left');
    const btnRight = document.getElementById('btn-spin-right');
    const btnReset = document.getElementById('btn-reset-view');
    const btnPlay = document.getElementById('btn-toggle-play');

    if (btnLeft) {
        btnLeft.addEventListener('click', function () {
            stopAutoSpin();
            showFrame(currentIndex - 1);
        });
    }

    if (btnRight) {
        btnRight.addEventListener('click', function () {
            stopAutoSpin();
            showFrame(currentIndex + 1);
        });
    }

    if (btnReset) {
        btnReset.addEventListener('click', function () {
            stopAutoSpin();
            showFrame(0);
        });
    }

    if (scrubber) {
        scrubber.addEventListener('input', function () {
            stopAutoSpin();
            const angle = parseInt(this.value, 10);
            const targetIndex = Math.round(angle / stepDegrees) % totalFrames;
            showFrame(targetIndex);
        });
    }

    function startAutoSpin() {
        if (autoSpinInterval) return;
        autoSpinInterval = setInterval(function () {
            showFrame(currentIndex + 1);
        }, 80);
        if (btnPlay) {
            document.getElementById('play-text').textContent = 'Pause';
        }
    }

    function stopAutoSpin() {
        if (autoSpinInterval) {
            clearInterval(autoSpinInterval);
            autoSpinInterval = null;
            if (btnPlay) {
                document.getElementById('play-text').textContent = 'Auto Spin';
            }
        }
    }

    if (btnPlay) {
        btnPlay.addEventListener('click', function () {
            if (autoSpinInterval) {
                stopAutoSpin();
            } else {
                startAutoSpin();
            }
        });
    }

    // Dynamic Hotspot Addition
    const btnAddHotspot = document.getElementById('btn-add-hotspot');
    const hotspotsContainer = document.getElementById('hotspots-list-container');
    const noHotspotsText = document.getElementById('no-hotspots-text');

    if (btnAddHotspot && hotspotsContainer) {
        btnAddHotspot.addEventListener('click', function () {
            if (noHotspotsText) noHotspotsText.style.display = 'none';
            const index = hotspotsContainer.querySelectorAll('.hotspot-item').length;
            const currentAngle = Math.round(currentIndex * stepDegrees);

            const row = document.createElement('div');
            row.className = 'hotspot-item p-4 rounded-2xl bg-slate-50 border border-cyan-200 flex flex-col sm:flex-row items-start sm:items-center gap-3 animate-fade-in';
            row.innerHTML = `
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-12 gap-2 w-full">
                    <div class="sm:col-span-4">
                        <label class="text-[10px] text-cyan-800 font-bold uppercase block mb-1">Feature Title</label>
                        <input type="text" name="hotspots[${index}][title]" placeholder="e.g. Brembo Monobloc Calipers" required
                               class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                    </div>
                    <div class="sm:col-span-4">
                        <label class="text-[10px] text-slate-500 font-bold uppercase block mb-1">Description</label>
                        <input type="text" name="hotspots[${index}][description]" placeholder="Key specs or details"
                               class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-[10px] text-slate-500 font-bold uppercase block mb-1">Angle (°)</label>
                        <input type="number" name="hotspots[${index}][frame_angle]" value="${currentAngle}" min="0" max="359" required
                               class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                    </div>
                    <div class="sm:col-span-2 flex gap-1">
                        <div class="w-1/2">
                            <label class="text-[10px] text-slate-500 font-bold uppercase block mb-1">X %</label>
                            <input type="number" name="hotspots[${index}][x_pct]" value="50" min="0" max="100" step="1" required
                                   class="w-full px-2 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                        </div>
                        <div class="w-1/2">
                            <label class="text-[10px] text-slate-500 font-bold uppercase block mb-1">Y %</label>
                            <input type="number" name="hotspots[${index}][y_pct]" value="50" min="0" max="100" step="1" required
                                   class="w-full px-2 py-1.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                        </div>
                    </div>
                </div>
                <button type="button" onclick="this.closest('.hotspot-item').remove()"
                        class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 transition shrink-0" title="Remove hotspot">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            `;
            hotspotsContainer.appendChild(row);
        });
    }

    // Initialize initial frame
    showFrame(0);
});
</script>
@endpush
@endsection
