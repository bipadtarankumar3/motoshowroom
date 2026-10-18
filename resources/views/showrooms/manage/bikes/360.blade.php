@extends('layouts.app')

@section('title', '360° Studio - ' . $bike->name . ' - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-cyan-500 selection:text-white">

    <!-- Showroom Branded Topbar -->
    <header class="border-b border-slate-800 bg-slate-950/90 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <div class="flex items-center gap-3">
                    <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider">Interactive 360° Studio</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        </div>
                        <h1 class="text-lg sm:text-xl font-black font-display text-white tracking-tight leading-none mt-0.5">
                            {{ $bike->name }} <span class="text-slate-400 font-normal">({{ $bike->model_year }})</span>
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if($bike->is_published && $bike->has360View() && $hasFeature)
                        <a href="{{ route('showrooms.bikes.360', [$showroom, $bike]) }}" target="_blank"
                           class="px-4 py-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-semibold flex items-center gap-2 transition shadow-lg shadow-cyan-500/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Live Storefront 360</span>
                        </a>
                    @endif
                    <a href="{{ route('showrooms.manage.bikes.edit', [$showroom, $bike]) }}"
                       class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition">
                        Edit Specs
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Plan Entitlement Warning (if plan lacks 360 viewer) -->
        @if(! $hasFeature)
            <div class="mb-8 rounded-3xl bg-amber-500/10 border border-amber-500/30 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xl">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">360° Studio Gated Feature</h3>
                        <p class="text-xs text-amber-300/90 mt-0.5">
                            Your current subscription plan does not include the Interactive 360° Motorcycle Viewer. Upgrade to Pro or Enterprise to enable 360 turntable sequences, hotspots, and 3D showcases.
                        </p>
                    </div>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}"
                   class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shrink-0 transition shadow-lg shadow-amber-500/20">
                    Upgrade Subscription
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Column: Interactive 360 Previewer & Hotspots Overlay (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 360 Studio Turntable Canvas Box -->
                <div class="glass-panel-dark rounded-3xl border border-slate-800 p-4 sm:p-6 shadow-2xl relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-cyan-400 animate-ping"></span>
                            <h2 class="text-sm font-bold uppercase tracking-wider text-white">Studio Turntable Preview</h2>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span id="preview-angle-badge" class="px-2.5 py-1 rounded-lg bg-cyan-500/15 text-cyan-300 font-mono font-bold border border-cyan-500/30">
                                0°
                            </span>
                            <span class="text-slate-400">
                                <span id="preview-frame-index" class="text-white font-bold">{{ $frames->isNotEmpty() ? '1' : '0' }}</span> / {{ $frames->count() }} Frames
                            </span>
                        </div>
                    </div>

                    <!-- Turntable Stage -->
                    <div id="turntable-container"
                         class="relative aspect-[16/10] rounded-2xl bg-gradient-to-b from-slate-900 to-slate-950 border border-slate-800 overflow-hidden cursor-grab active:cursor-grabbing select-none flex items-center justify-center">

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
                                        <div class="w-6 h-6 rounded-full bg-cyan-500/30 border-2 border-cyan-400 flex items-center justify-center shadow-lg shadow-cyan-500/50 animate-pulse">
                                            <div class="w-2 h-2 rounded-full bg-cyan-300"></div>
                                        </div>
                                        <!-- Hotspot Tooltip -->
                                        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 w-48 p-2.5 rounded-xl bg-slate-900/95 border border-cyan-500/40 text-white text-xs shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-30 backdrop-blur">
                                            <p class="font-bold text-cyan-300">{{ $spot['title'] }}</p>
                                            @if(!empty($spot['description']))
                                                <p class="text-[11px] text-slate-300 mt-1 leading-snug">{{ $spot['description'] }}</p>
                                            @endif
                                            <span class="inline-block mt-1 text-[9px] text-slate-400 uppercase font-mono">Visible at {{ $spot['frame_angle'] }}°</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Drag Instruction Hint (Fades out upon interaction) -->
                            <div id="drag-hint" class="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full bg-slate-950/80 border border-slate-700/80 text-[11px] font-semibold text-slate-300 backdrop-blur flex items-center gap-2 pointer-events-none transition-opacity">
                                <svg class="w-4 h-4 text-cyan-400 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span>Click & Drag to rotate 360°</span>
                            </div>
                        @else
                            <div class="p-8 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <p class="text-sm font-semibold text-slate-400">No 360° frames uploaded yet</p>
                                <p class="text-xs text-slate-500 mt-1">Upload a sequence of turntable photos below to activate the 360 view.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Turntable Playback Controls -->
                    <div class="mt-4 pt-4 border-t border-slate-800 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <button type="button" id="btn-spin-left" class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 transition text-xs font-semibold flex items-center gap-1" title="Rotate Left 10°">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button type="button" id="btn-toggle-play" class="px-3 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 hover:text-white border border-slate-800 transition text-xs font-semibold flex items-center gap-1.5">
                                <svg id="play-icon" class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                <span id="play-text">Auto Spin</span>
                            </button>
                            <button type="button" id="btn-spin-right" class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 transition text-xs font-semibold flex items-center gap-1" title="Rotate Right 10°">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <button type="button" id="btn-reset-view" class="px-3 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 transition text-xs font-semibold">
                                Reset 0°
                            </button>
                        </div>

                        <!-- Rotation Scrubber Slider -->
                        <div class="flex-1 min-w-[160px] flex items-center gap-2">
                            <input type="range" id="angle-scrubber" min="0" max="359" value="0"
                                   class="w-full h-1.5 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-cyan-400">
                        </div>
                    </div>
                </div>

                <!-- Interactive Hotspots Management Card -->
                <div class="glass-panel-dark rounded-3xl border border-slate-800 p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Interactive Feature Hotspots</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Pins appear when the motorcycle reaches the designated angle, highlighting technical features.</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-900 text-cyan-400 border border-slate-800">
                            {{ count($hotspots) }} Active Hotspots
                        </span>
                    </div>

                    <form method="POST" action="{{ route('showrooms.manage.bikes.360.hotspots', [$showroom, $bike]) }}">
                        @csrf

                        <div id="hotspots-list-container" class="space-y-3 mb-4">
                            @forelse($hotspots as $index => $spot)
                                <div class="hotspot-item p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-12 gap-2 w-full">
                                        <div class="sm:col-span-4">
                                            <label class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Feature Title</label>
                                            <input type="text" name="hotspots[{{ $index }}][title]" value="{{ $spot['title'] }}" required
                                                   class="w-full px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-cyan-500">
                                        </div>
                                        <div class="sm:col-span-4">
                                            <label class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Description</label>
                                            <input type="text" name="hotspots[{{ $index }}][description]" value="{{ $spot['description'] ?? '' }}"
                                                   class="w-full px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-cyan-500">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Angle (°)</label>
                                            <input type="number" name="hotspots[{{ $index }}][frame_angle]" value="{{ $spot['frame_angle'] }}" min="0" max="359" required
                                                   class="w-full px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-cyan-500 font-mono">
                                        </div>
                                        <div class="sm:col-span-2 flex gap-1">
                                            <div class="w-1/2">
                                                <label class="text-[10px] text-slate-400 font-bold uppercase block mb-1">X %</label>
                                                <input type="number" name="hotspots[{{ $index }}][x_pct]" value="{{ $spot['x_pct'] }}" min="0" max="100" step="1" required
                                                       class="w-full px-2 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-cyan-500 font-mono">
                                            </div>
                                            <div class="w-1/2">
                                                <label class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Y %</label>
                                                <input type="number" name="hotspots[{{ $index }}][y_pct]" value="{{ $spot['y_pct'] }}" min="0" max="100" step="1" required
                                                       class="w-full px-2 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-cyan-500 font-mono">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" onclick="this.closest('.hotspot-item').remove()"
                                            class="p-2 rounded-xl text-rose-400 hover:bg-rose-500/10 transition shrink-0" title="Remove hotspot">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            @empty
                                <p id="no-hotspots-text" class="text-xs text-slate-500 py-3 text-center">No interactive hotspots configured. Click "Add Hotspot" below to create feature callouts.</p>
                            @endforelse
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <button type="button" id="btn-add-hotspot"
                                    class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-cyan-300 transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Add Hotspot</span>
                            </button>

                            <button type="submit" @disabled(! $hasFeature)
                                    class="px-5 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition shadow-lg shadow-cyan-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                Save Hotspots
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Settings & Turntable Frame Sequence Upload (5 cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- 360 Studio General Configuration Card -->
                <div class="glass-panel-dark rounded-3xl border border-slate-800 p-6 shadow-xl">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Viewer Configuration</span>
                    </h3>

                    <form method="POST" action="{{ route('showrooms.manage.bikes.360.settings', [$showroom, $bike]) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Enable 360 View Switch -->
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                            <div>
                                <span class="text-xs font-bold text-white block">Enable 360° Studio</span>
                                <span class="text-[11px] text-slate-400">Display interactive 360 view on the public motorcycle profile.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="has_360_view" value="1" {{ old('has_360_view', $bike->has_360_view) ? 'checked' : '' }}
                                       @disabled(! $hasFeature)
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500"></div>
                            </label>
                        </div>

                        <!-- Viewer Engine Type -->
                        <div>
                            <label for="viewer_type" class="block text-xs font-bold uppercase text-slate-400 mb-1">Viewer Engine</label>
                            <select name="viewer_type" id="viewer_type" @disabled(! $hasFeature)
                                    class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500">
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
                            <label for="model_3d_path" class="block text-xs font-bold uppercase text-slate-400 mb-1">3D GLTF / GLB Model URL</label>
                            <input type="text" name="model_3d_path" id="model_3d_path" value="{{ old('model_3d_path', $bike->model_3d_path) }}"
                                   placeholder="https://cdn.example.com/models/motorcycle.glb"
                                   @disabled(! $hasFeature)
                                   class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500">
                            <span class="text-[10px] text-slate-500 mt-1 block">Full URL or storage path to an optimized 3D GLTF/GLB motorcycle file.</span>
                        </div>

                        <!-- Auto-Rotate & Speed Settings -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="rotation_speed" class="block text-xs font-bold uppercase text-slate-400 mb-1">Rotation Speed (1-10)</label>
                                <input type="number" name="rotation_speed" id="rotation_speed" min="1" max="10"
                                       value="{{ old('rotation_speed', $settings['rotation_speed'] ?? 3) }}"
                                       @disabled(! $hasFeature)
                                       class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500 font-mono">
                            </div>
                            <div>
                                <label for="default_angle" class="block text-xs font-bold uppercase text-slate-400 mb-1">Default Angle (0-359°)</label>
                                <input type="number" name="default_angle" id="default_angle" min="0" max="359"
                                       value="{{ old('default_angle', $settings['default_angle'] ?? 0) }}"
                                       @disabled(! $hasFeature)
                                       class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500 font-mono">
                            </div>
                        </div>

                        <!-- Behavioral Checkboxes -->
                        <div class="space-y-2 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="auto_rotate" value="1" {{ old('auto_rotate', $settings['auto_rotate'] ?? true) ? 'checked' : '' }}
                                       @disabled(! $hasFeature)
                                       class="rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-cyan-500">
                                <span class="text-xs text-slate-300">Auto-rotate turntable on page load</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="reverse_direction" value="1" {{ old('reverse_direction', $settings['reverse_direction'] ?? false) ? 'checked' : '' }}
                                       @disabled(! $hasFeature)
                                       class="rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-cyan-500">
                                <span class="text-xs text-slate-300">Reverse spin direction</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="enable_zoom" value="1" {{ old('enable_zoom', $settings['enable_zoom'] ?? true) ? 'checked' : '' }}
                                       @disabled(! $hasFeature)
                                       class="rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-cyan-500">
                                <span class="text-xs text-slate-300">Enable click-to-zoom in full screen</span>
                            </label>
                        </div>

                        <button type="submit" @disabled(! $hasFeature)
                                class="w-full py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition shadow-lg shadow-cyan-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                            Update Viewer Settings
                        </button>
                    </form>
                </div>

                <!-- Turntable Frames Importer Card -->
                <div class="glass-panel-dark rounded-3xl border border-slate-800 p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-white">Add 360° Frames</h3>
                        @if($frames->isNotEmpty())
                            <form method="POST" action="{{ route('showrooms.manage.bikes.360.frames.clear', [$showroom, $bike]) }}"
                                  onsubmit="return confirm('Are you sure you want to remove all {{ $frames->count() }} 360° frames?');">
                                @csrf
                                <button type="submit" @disabled(! $hasFeature)
                                        class="text-xs text-rose-400 hover:text-rose-300 transition font-semibold">
                                    Clear All Frames
                                </button>
                            </form>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('showrooms.manage.bikes.360.frames.store', [$showroom, $bike]) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <!-- Bulk URLs Input -->
                        <div>
                            <label for="bulk_urls" class="block text-xs font-bold uppercase text-slate-400 mb-1">
                                Bulk Frame URLs (One per line)
                            </label>
                            <textarea name="bulk_urls" id="bulk_urls" rows="4" @disabled(! $hasFeature)
                                      placeholder="https://cdn.example.com/bike-frame-00.jpg&#10;https://cdn.example.com/bike-frame-01.jpg&#10;https://cdn.example.com/bike-frame-02.jpg"
                                      class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-cyan-500 font-mono"></textarea>
                            <span class="text-[10px] text-slate-500 mt-1 block">Angles will be distributed evenly across 360° based on total lines entered.</span>
                        </div>

                        <!-- Or Upload Local Files -->
                        <div class="pt-2 border-t border-slate-800">
                            <label for="frame_files" class="block text-xs font-bold uppercase text-slate-400 mb-1">
                                Or Upload Image Files
                            </label>
                            <input type="file" name="frame_files[]" id="frame_files" multiple accept="image/*" @disabled(! $hasFeature)
                                   class="w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700 cursor-pointer">
                        </div>

                        <button type="submit" @disabled(! $hasFeature)
                                class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-bold text-xs transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Import Frames
                        </button>
                    </form>

                    <!-- Existing Frames Thumbnail Grid -->
                    @if($frames->isNotEmpty())
                        <div class="mt-6 pt-6 border-t border-slate-800">
                            <span class="text-xs font-bold uppercase text-slate-400 block mb-3">Frame Sequence ({{ $frames->count() }} Frames)</span>
                            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 max-h-56 overflow-y-auto pr-1">
                                @foreach($frames as $idx => $f)
                                    <div class="relative group rounded-xl bg-slate-900 border border-slate-800 overflow-hidden aspect-[4/3]">
                                        <img src="{{ $f->image_url }}" alt="{{ $f->alt_text }}" class="w-full h-full object-cover">
                                        <span class="absolute bottom-1 left-1 px-1 py-0.5 rounded text-[8px] font-mono font-bold bg-slate-950/80 text-cyan-300">
                                            {{ $f->frame_angle }}°
                                        </span>
                                        <form method="POST" action="{{ route('showrooms.manage.bikes.360.frames.destroy', [$showroom, $bike, $f]) }}"
                                              class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded-md bg-rose-950/90 text-rose-300 hover:text-white" title="Delete frame">
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
    // Viewer Engine Type toggle
    const viewerTypeSelect = document.getElementById('viewer_type');
    const gltfGroup = document.getElementById('gltf-model-input-group');
    if (viewerTypeSelect && gltfGroup) {
        viewerTypeSelect.addEventListener('change', function () {
            if (this.value === 'gltf_model') {
                gltfGroup.classList.remove('hidden');
            } else {
                gltfGroup.classList.add('hidden');
            }
        });
    }

    // Interactive Turntable Studio Engine
    const container = document.getElementById('turntable-container');
    const frames = document.querySelectorAll('.turntable-frame');
    const angleScrubber = document.getElementById('angle-scrubber');
    const angleBadge = document.getElementById('preview-angle-badge');
    const frameIndexSpan = document.getElementById('preview-frame-index');
    const btnPlay = document.getElementById('btn-toggle-play');
    const playText = document.getElementById('play-text');
    const btnSpinLeft = document.getElementById('btn-spin-left');
    const btnSpinRight = document.getElementById('btn-spin-right');
    const btnReset = document.getElementById('btn-reset-view');
    const dragHint = document.getElementById('drag-hint');
    const hotspotPins = document.querySelectorAll('.hotspot-pin');

    if (!frames || frames.length === 0) return;

    let totalFrames = frames.length;
    let currentIndex = 0;
    let isDragging = false;
    let startX = 0;
    let isPlaying = false;
    let playInterval = null;
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

        if (angleScrubber) angleScrubber.value = currentAngle;
        if (angleBadge) angleBadge.textContent = currentAngle + '°';
        if (frameIndexSpan) frameIndexSpan.textContent = (currentIndex + 1);

        // Update Hotspot pins visibility within ±25 degrees of current angle
        hotspotPins.forEach(pin => {
            const pinAngle = parseInt(pin.getAttribute('data-angle'), 10) || 0;
            const diff = Math.abs(currentAngle - pinAngle);
            const wrappedDiff = Math.min(diff, 360 - diff);
            if (wrappedDiff <= 25) {
                pin.style.display = 'block';
                pin.style.opacity = Math.max(0.4, 1 - (wrappedDiff / 25));
            } else {
                pin.style.display = 'none';
            }
        });
    }

    // Pointer Dragging
    container.addEventListener('pointerdown', function (e) {
        isDragging = true;
        startX = e.clientX;
        if (dragHint) dragHint.style.opacity = '0';
        if (isPlaying) stopAutoSpin();
        container.setPointerCapture(e.pointerId);
    });

    container.addEventListener('pointermove', function (e) {
        if (!isDragging) return;
        const deltaX = e.clientX - startX;
        const sensitivity = 8; // Pixels per frame shift
        if (Math.abs(deltaX) >= sensitivity) {
            const framesToAdvance = Math.floor(deltaX / sensitivity);
            showFrame(currentIndex - framesToAdvance);
            startX = e.clientX;
        }
    });

    container.addEventListener('pointerup', function (e) {
        isDragging = false;
        container.releasePointerCapture(e.pointerId);
    });

    container.addEventListener('pointercancel', function () {
        isDragging = false;
    });

    // Scrubber
    if (angleScrubber) {
        angleScrubber.addEventListener('input', function () {
            const targetAngle = parseInt(this.value, 10);
            const targetIndex = Math.round((targetAngle / 360) * totalFrames);
            showFrame(targetIndex);
            if (isPlaying) stopAutoSpin();
        });
    }

    // Buttons
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

    function startAutoSpin() {
        isPlaying = true;
        if (playText) playText.textContent = 'Pause';
        const intervalMs = Math.max(30, 200 - (speed * 18));
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
            row.className = 'hotspot-item p-4 rounded-2xl bg-slate-900/80 border border-cyan-500/30 flex flex-col sm:flex-row items-start sm:items-center gap-3 animate-fade-in';
            row.innerHTML = `
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-12 gap-2 w-full">
                    <div class="sm:col-span-4">
                        <label class="text-[10px] text-cyan-400 font-bold uppercase block mb-1">Feature Title</label>
                        <input type="text" name="hotspots[${index}][title]" placeholder="e.g. Brembo Monobloc Calipers" required
                               class="w-full px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-cyan-500">
                    </div>
                    <div class="sm:col-span-4">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Description</label>
                        <input type="text" name="hotspots[${index}][description]" placeholder="Key specs or details"
                               class="w-full px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-cyan-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Angle (°)</label>
                        <input type="number" name="hotspots[${index}][frame_angle]" value="${currentAngle}" min="0" max="359" required
                               class="w-full px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-cyan-500 font-mono">
                    </div>
                    <div class="sm:col-span-2 flex gap-1">
                        <div class="w-1/2">
                            <label class="text-[10px] text-slate-400 font-bold uppercase block mb-1">X %</label>
                            <input type="number" name="hotspots[${index}][x_pct]" value="50" min="0" max="100" step="1" required
                                   class="w-full px-2 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-cyan-500 font-mono">
                        </div>
                        <div class="w-1/2">
                            <label class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Y %</label>
                            <input type="number" name="hotspots[${index}][y_pct]" value="50" min="0" max="100" step="1" required
                                   class="w-full px-2 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-cyan-500 font-mono">
                        </div>
                    </div>
                </div>
                <button type="button" onclick="this.closest('.hotspot-item').remove()"
                        class="p-2 rounded-xl text-rose-400 hover:bg-rose-500/10 transition shrink-0" title="Remove hotspot">
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
