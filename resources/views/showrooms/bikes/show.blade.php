@extends('layouts.app')

@section('title', $bike->name . ' - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    <!-- Showroom Branded Topbar / Header -->
    <header class="border-b border-slate-800/80 bg-slate-950/90 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Showroom Identity -->
                <a href="{{ route('showrooms.show', $showroom) }}" class="flex items-center gap-3 group">
                    @if($showroom->logo_url)
                        <img src="{{ $showroom->logo_url }}" alt="{{ $showroom->name }}" class="h-10 w-auto rounded-xl object-contain bg-slate-900 border border-slate-800 p-1">
                    @else
                        <span class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-white text-base shadow-lg group-hover:scale-105 transition-transform"
                              style="background: linear-gradient(135deg, {{ $showroom->brand_color_primary ?? '#dc2626' }}, {{ $showroom->brand_color_accent ?? '#f43f5e' }})">
                            {{ strtoupper(substr($showroom->name, 0, 1)) }}
                        </span>
                    @endif
                    <div>
                        <span class="font-display font-black text-lg sm:text-xl tracking-tight text-white block leading-none">{{ $showroom->name }}</span>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest block mt-0.5">{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-6 text-sm font-semibold">
                    <a href="{{ route('showrooms.show', $showroom) }}" class="text-slate-300 hover:text-white transition">Home</a>
                    <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="text-rose-400 font-bold transition">Motorcycles</a>
                    <a href="{{ route('showrooms.show', $showroom) }}#about" class="text-slate-300 hover:text-white transition">About Us</a>
                    <a href="{{ route('showrooms.show', $showroom) }}#contact" class="text-slate-300 hover:text-white transition">Contact</a>
                </div>

                <!-- WhatsApp Quick Action -->
                @if($showroom->getSetting('whatsapp_number'))
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $showroom->getSetting('whatsapp_number')) }}?text={{ urlencode('Hello! I am interested in the ' . $bike->name . ' (SKU: ' . $bike->sku . ').') }}"
                       target="_blank"
                       class="flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/40 text-emerald-300 text-xs font-bold transition shadow-lg">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span class="hidden sm:inline">WhatsApp Dealer</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-6">
            <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-white transition">Home</a>
            <span>/</span>
            <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="hover:text-white transition">Inventory</a>
            <span>/</span>
            <span class="text-rose-400">{{ $bike->name }}</span>
        </div>

        <!-- Main Product Layout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

            <!-- Left Column: Gallery & In-Depth Specs (8 cols) -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-8">

                <!-- Interactive High-Res Media Stage (Gallery & 360 Studio) -->
                <div class="glass-panel-dark rounded-3xl border border-slate-800 p-3 sm:p-4 overflow-hidden">
                    @if($has360Viewer)
                        <!-- Media View Mode Tabs -->
                        <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-800/80">
                            <div class="flex items-center gap-2">
                                <button type="button" id="tab-btn-gallery"
                                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-slate-800 text-white border border-slate-700 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Photos ({{ $bike->images->count() }})</span>
                                </button>
                                <button type="button" id="tab-btn-360"
                                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-slate-900/60 hover:bg-cyan-500/10 text-cyan-400 hover:text-cyan-300 border border-cyan-500/30 flex items-center gap-1.5 shadow-sm">
                                    <svg class="w-4 h-4 text-cyan-400 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span>Interactive 360° Studio</span>
                                </button>
                            </div>

                            <a href="{{ route('showrooms.bikes.360', [$showroom, $bike]) }}"
                               class="hidden sm:inline-flex items-center gap-1 text-[11px] font-bold text-cyan-400 hover:text-cyan-300 transition">
                                <span>Fullscreen 360</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    @endif

                    <!-- Mode 1: Standard Photo Gallery View -->
                    <div id="media-view-gallery" class="space-y-3">
                        <!-- Main Hero Image Frame -->
                        <div class="aspect-[16/10] rounded-2xl overflow-hidden bg-slate-900 border border-slate-800 relative group">
                            <img id="main-gallery-image"
                                 src="{{ $bike->primary_image_url }}"
                                 alt="{{ $bike->name }}"
                                 class="w-full h-full object-cover transition-all duration-300">

                            <div class="absolute top-4 left-4 flex items-center gap-2">
                                <span class="px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-wider {{ $bike->category->badgeClasses() }} backdrop-blur-md">
                                    {{ $bike->category->label() }}
                                </span>
                                @if($bike->is_featured)
                                    <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider bg-amber-500 text-slate-950 shadow-md">
                                        Featured Machine
                                    </span>
                                @endif
                                @if($has360Viewer)
                                    <button type="button" onclick="document.getElementById('tab-btn-360').click()"
                                            class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-cyan-500 text-slate-950 shadow-md flex items-center gap-1 hover:scale-105 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span>360° Studio</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Gallery Thumbnails (if multiple images) -->
                        @if($bike->images->count() > 1)
                            <div class="flex items-center gap-3 overflow-x-auto pb-1">
                                @foreach($bike->images as $img)
                                    <button type="button"
                                            onclick="document.getElementById('main-gallery-image').src = '{{ $img->image_url }}'"
                                            class="w-20 h-14 rounded-xl overflow-hidden bg-slate-900 border-2 border-slate-800 hover:border-rose-500 focus:border-rose-500 transition-all flex-shrink-0">
                                        <img src="{{ $img->image_url }}" alt="{{ $img->alt_text }}" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if($has360Viewer)
                        <!-- Mode 2: Interactive 360 Studio View -->
                        <div id="media-view-360" class="hidden space-y-3">
                            <div id="inline-turntable-container"
                                 class="relative aspect-[16/10] rounded-2xl bg-gradient-to-b from-slate-900 to-slate-950 border border-slate-800 overflow-hidden cursor-grab active:cursor-grabbing select-none flex items-center justify-center">

                                <!-- Frames -->
                                @foreach($bike->frames360 as $index => $frame)
                                    <img src="{{ $frame->image_url }}"
                                         alt="{{ $frame->alt_text }}"
                                         data-frame-index="{{ $index }}"
                                         class="inline-turntable-frame absolute inset-0 w-full h-full object-contain pointer-events-none transition-opacity duration-75 {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
                                @endforeach

                                <!-- Hotspots Layer -->
                                <div class="absolute inset-0 pointer-events-none z-20">
                                    @foreach($bike->hotspots_list as $spot)
                                        <div class="inline-hotspot-pin absolute -translate-x-1/2 -translate-y-1/2 pointer-events-auto group cursor-pointer"
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
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Drag Hint -->
                                <div id="inline-drag-hint" class="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full bg-slate-950/80 border border-slate-700/80 text-[11px] font-semibold text-slate-300 backdrop-blur flex items-center gap-2 pointer-events-none">
                                    <svg class="w-4 h-4 text-cyan-400 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    <span>Drag to spin 360°</span>
                                </div>
                            </div>

                            <!-- Turntable Playback Controls -->
                            <div class="pt-2 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2">
                                    <button type="button" id="btn-inline-spin-left" class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <button type="button" id="btn-inline-toggle-play" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 hover:text-white border border-slate-800 transition font-semibold flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                        <span id="inline-play-text">Auto Spin</span>
                                    </button>
                                    <button type="button" id="btn-inline-spin-right" class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span id="inline-angle-badge" class="px-2 py-0.5 rounded-lg bg-cyan-500/15 text-cyan-300 font-mono font-bold text-xs border border-cyan-500/30">
                                        0°
                                    </span>
                                    <a href="{{ route('showrooms.bikes.360', [$showroom, $bike]) }}"
                                       class="px-3 py-1.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition shadow-md shadow-cyan-500/20 flex items-center gap-1">
                                        <span>Fullscreen 360</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Color & Trim Variants Selection -->
                @if($bike->variants->isNotEmpty())
                    <div class="glass-panel-dark rounded-3xl p-6 border border-slate-800 shadow-xl">
                        <h3 class="text-sm uppercase font-bold text-slate-400 tracking-wider mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            <span>Available Trims & Colors</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($bike->variants as $variant)
                                <div class="bg-slate-900/80 rounded-2xl p-4 border border-slate-800 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="w-7 h-7 rounded-xl border border-slate-700 shadow-inner flex-shrink-0"
                                              style="background-color: {{ $variant->color_hex ?? '#1A1A1A' }}"></span>
                                        <div>
                                            <span class="font-bold text-sm text-white block">{{ $variant->name }}</span>
                                            <span class="text-xs text-slate-400">{{ $variant->color_name }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-mono font-bold text-slate-200 block">
                                            @if($variant->price_difference > 0)
                                                + ₹{{ number_format((float) $variant->price_difference, 2) }}
                                            @else
                                                Included
                                            @endif
                                        </span>
                                        <span class="text-[10px] text-emerald-400 font-semibold">{{ $variant->stock_quantity }} in stock</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Narrative & Overview -->
                @if($bike->description)
                    <div class="glass-panel-dark rounded-3xl p-6 sm:p-8 border border-slate-800">
                        <h2 class="text-xl font-bold font-display text-white mb-4">Motorcycle Overview</h2>
                        <div class="prose prose-invert max-w-none text-slate-300 leading-relaxed text-sm sm:text-base space-y-4">
                            {!! nl2br(e($bike->description)) !!}
                        </div>
                    </div>
                @endif

                <!-- Comprehensive Technical Specifications Grid -->
                <div class="glass-panel-dark rounded-3xl p-6 sm:p-8 border border-slate-800">
                    <h2 class="text-xl font-bold font-display text-white mb-6">Technical Specifications</h2>

                    <!-- Spec Categories -->
                    <div class="space-y-6">

                        <!-- Mechanical & Engine -->
                        <div>
                            <h3 class="text-xs uppercase font-bold text-rose-400 tracking-wider mb-3">Powertrain & Engine</h3>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 flex justify-between">
                                    <dt class="text-slate-400">Displacement</dt>
                                    <dd class="font-semibold text-white">{{ $bike->engine_cc ? $bike->engine_cc . ' cc' : 'N/A' }}</dd>
                                </div>
                                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 flex justify-between">
                                    <dt class="text-slate-400">Max Power</dt>
                                    <dd class="font-semibold text-white">{{ $bike->power_bhp ? $bike->power_bhp . ' bhp' : 'N/A' }}</dd>
                                </div>
                                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 flex justify-between">
                                    <dt class="text-slate-400">Peak Torque</dt>
                                    <dd class="font-semibold text-white">{{ $bike->torque_nm ? $bike->torque_nm . ' Nm' : 'N/A' }}</dd>
                                </div>
                                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 flex justify-between">
                                    <dt class="text-slate-400">Fuel Type</dt>
                                    <dd class="font-semibold text-white capitalize">{{ $bike->fuel_type }}</dd>
                                </div>
                                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 flex justify-between sm:col-span-2">
                                    <dt class="text-slate-400">Transmission</dt>
                                    <dd class="font-semibold text-white">{{ $bike->transmission ?? 'Manual' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Performance & Dimensions -->
                        <div>
                            <h3 class="text-xs uppercase font-bold text-rose-400 tracking-wider mb-3">Performance & Dimensions</h3>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 flex justify-between">
                                    <dt class="text-slate-400">Fuel Efficiency</dt>
                                    <dd class="font-semibold text-white">{{ $bike->mileage_kmpl ? $bike->mileage_kmpl . ' km/l' : 'N/A' }}</dd>
                                </div>
                                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 flex justify-between">
                                    <dt class="text-slate-400">Top Speed</dt>
                                    <dd class="font-semibold text-white">{{ $bike->top_speed_kmh ? $bike->top_speed_kmh . ' km/h' : 'N/A' }}</dd>
                                </div>
                                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 flex justify-between">
                                    <dt class="text-slate-400">Curb Weight</dt>
                                    <dd class="font-semibold text-white">{{ $bike->curb_weight_kg ? $bike->curb_weight_kg . ' kg' : 'N/A' }}</dd>
                                </div>
                                <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 flex justify-between">
                                    <dt class="text-slate-400">Model Year</dt>
                                    <dd class="font-semibold text-white">{{ $bike->model_year }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Custom Equipment Specifications -->
                        @if(!empty($bike->specifications))
                            <div>
                                <h3 class="text-xs uppercase font-bold text-rose-400 tracking-wider mb-3">Chassis, Electronics & Equipment</h3>
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    @foreach($bike->specifications as $specKey => $specValue)
                                        <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 flex flex-col justify-between">
                                            <dt class="text-slate-400 font-medium mb-1">{{ $specKey }}</dt>
                                            <dd class="font-semibold text-white">{{ $specValue }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        @endif

                    </div>
                </div>

            </div>

            <!-- Right Column: Sticky Buyer Card & Dealership Details (4 cols) -->
            <div class="lg:col-span-5 xl:col-span-4 space-y-6">

                <!-- Primary Pricing & Booking Card -->
                <div class="glass-panel-dark rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl lg:sticky lg:top-24">

                    <!-- Brand & Name -->
                    <div class="mb-4">
                        <span class="text-xs font-bold text-rose-400 uppercase tracking-widest">{{ $bike->brand }}</span>
                        <h1 class="text-2xl sm:text-3xl font-black font-display text-white mt-1 leading-tight">{{ $bike->name }}</h1>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="font-mono text-xs text-slate-400 font-bold">SKU: {{ $bike->sku }}</span>
                            <span class="text-slate-600">•</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold {{ $bike->status->badgeClasses() }}">
                                {{ $bike->status->label() }}
                            </span>
                        </div>
                    </div>

                    <!-- Price Display -->
                    <div class="py-4 my-4 border-y border-slate-800/80">
                        <span class="text-xs uppercase font-bold text-slate-500 tracking-wider block">Ex-Showroom Price</span>
                        <div class="text-3xl sm:text-4xl font-mono font-black text-white mt-1">
                            {{ $bike->formatted_price }}
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">*Excludes RTO registration and mandatory insurance.</span>
                    </div>

                    <!-- Dealer Engagement Action Buttons -->
                    <div class="space-y-3">
                        <a href="{{ route('showrooms.enquiries.create', [$showroom, 'bike' => $bike->slug, 'type' => 'test_ride']) }}"
                           class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-slate-950 font-black text-sm shadow-lg shadow-amber-950/40 transition flex items-center justify-center gap-2 text-center" id="btn-book-test-ride">
                            <svg class="w-5 h-5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Book Free Test Ride</span>
                        </a>

                        <a href="{{ route('showrooms.enquiries.create', [$showroom, 'bike' => $bike->slug, 'type' => 'purchase']) }}"
                           class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700 text-white font-bold text-sm transition flex items-center justify-center gap-2 text-center" id="btn-request-quote">
                            <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Get On-Road Price Quote</span>
                        </a>
                        @if($showroom->getSetting('whatsapp_number'))
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $showroom->getSetting('whatsapp_number')) }}?text={{ urlencode('Hi! I want to check availability and purchase the ' . $bike->name . ' (SKU: ' . $bike->sku . ') from ' . $showroom->name . '.') }}"
                               target="_blank"
                               class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-950/40 transition flex items-center justify-center gap-2 text-center">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span>Contact Dealer on WhatsApp</span>
                            </a>
                        @endif

                        <a href="tel:{{ $showroom->phone }}"
                           class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-lg shadow-rose-600/30 transition flex items-center justify-center gap-2 text-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>Call {{ $showroom->phone ?? 'Showroom' }}</span>
                        </a>
                    </div>

                    <!-- Dealership Information Card -->
                    <div class="mt-6 pt-6 border-t border-slate-800 text-xs text-slate-400 space-y-3">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-slate-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <div>
                                <span class="font-bold text-white block">{{ $showroom->name }}</span>
                                <span>{{ $showroom->address_line1 }}, {{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                @if($showroom->isOpenNow())
                                    <span class="text-emerald-400 font-bold">Open Now</span>
                                @else
                                    <span class="text-rose-400 font-bold">Closed Now</span>
                                @endif
                                <span class="text-slate-500">• Visit for Test Rides</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- Similar / Related Motorcycles from this Showroom -->
        @if($relatedBikes->isNotEmpty())
            <div class="mt-16 sm:mt-24 pt-12 border-t border-slate-800">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <span class="text-xs uppercase font-bold text-rose-400 tracking-wider">Explore More</span>
                        <h2 class="text-2xl sm:text-3xl font-black font-display text-white mt-1">Similar Motorcycles</h2>
                    </div>
                    <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="text-xs font-bold text-slate-400 hover:text-white transition flex items-center gap-1">
                        <span>View All ({{ $showroom->publishedBikes()->count() }})</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedBikes as $relBike)
                        <div class="group glass-panel-dark rounded-2xl border border-slate-800 hover:border-rose-500/40 overflow-hidden flex flex-col transition duration-300">
                            <div class="aspect-[16/10] overflow-hidden bg-slate-900 relative">
                                <img src="{{ $relBike->primary_image_url }}" alt="{{ $relBike->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <span class="absolute top-2 right-2 px-2 py-0.5 rounded-lg text-[9px] font-semibold {{ $relBike->status->badgeClasses() }} backdrop-blur">
                                    {{ $relBike->status->label() }}
                                </span>
                            </div>
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">{{ $relBike->brand }}</span>
                                    <h4 class="font-bold text-sm text-white group-hover:text-rose-400 transition mt-0.5">
                                        <a href="{{ route('showrooms.bikes.show', [$showroom, $relBike]) }}">
                                            {{ $relBike->name }}
                                        </a>
                                    </h4>
                                </div>
                                <div class="mt-3 pt-3 border-t border-slate-800 flex items-center justify-between">
                                    <span class="font-mono font-bold text-sm text-white">{{ $relBike->formatted_price }}</span>
                                    <a href="{{ route('showrooms.bikes.show', [$showroom, $relBike]) }}" class="text-[11px] font-bold text-rose-400 hover:text-rose-300">
                                        View →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-8 text-center text-xs text-slate-500 mt-12">
        <div class="max-w-7xl mx-auto px-4">
            <p>© {{ date('Y') }} {{ $showroom->name }}. All rights reserved.</p>
        </div>
    </footer>
</div>

@if($has360Viewer)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabGallery = document.getElementById('tab-btn-gallery');
    const tab360 = document.getElementById('tab-btn-360');
    const viewGallery = document.getElementById('media-view-gallery');
    const view360 = document.getElementById('media-view-360');

    if (tabGallery && tab360 && viewGallery && view360) {
        tabGallery.addEventListener('click', function () {
            viewGallery.classList.remove('hidden');
            view360.classList.add('hidden');
            tabGallery.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-slate-800 text-white border border-slate-700 flex items-center gap-1.5';
            tab360.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-slate-900/60 hover:bg-cyan-500/10 text-cyan-400 hover:text-cyan-300 border border-cyan-500/30 flex items-center gap-1.5 shadow-sm';
            if (window._stopInlineAutoSpin) window._stopInlineAutoSpin();
        });

        tab360.addEventListener('click', function () {
            viewGallery.classList.add('hidden');
            view360.classList.remove('hidden');
            tab360.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-cyan-500 text-slate-950 border border-cyan-400 flex items-center gap-1.5 shadow-md shadow-cyan-500/20';
            tabGallery.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-slate-900/60 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 flex items-center gap-1.5';
        });
    }

    // Inline Turntable Engine
    const container = document.getElementById('inline-turntable-container');
    const frames = document.querySelectorAll('.inline-turntable-frame');
    const angleBadge = document.getElementById('inline-angle-badge');
    const btnPlay = document.getElementById('btn-inline-toggle-play');
    const playText = document.getElementById('inline-play-text');
    const btnSpinLeft = document.getElementById('btn-inline-spin-left');
    const btnSpinRight = document.getElementById('btn-inline-spin-right');
    const dragHint = document.getElementById('inline-drag-hint');
    const hotspots = document.querySelectorAll('.inline-hotspot-pin');

    if (!frames || frames.length === 0) return;

    let totalFrames = frames.length;
    let currentIndex = 0;
    let isDragging = false;
    let startX = 0;
    let isPlaying = false;
    let playInterval = null;
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

        if (angleBadge) angleBadge.textContent = currentAngle + '°';

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

    if (container) {
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
            const sensitivity = 8;
            if (Math.abs(deltaX) >= sensitivity) {
                const advance = Math.floor(deltaX / sensitivity);
                showFrame(currentIndex - advance);
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
    }

    function startAutoSpin() {
        isPlaying = true;
        if (playText) playText.textContent = 'Pause';
        playInterval = setInterval(() => {
            showFrame(currentIndex + 1);
        }, 120);
    }

    function stopAutoSpin() {
        isPlaying = false;
        if (playText) playText.textContent = 'Auto Spin';
        if (playInterval) clearInterval(playInterval);
    }

    window._stopInlineAutoSpin = stopAutoSpin;

    if (btnPlay) {
        btnPlay.addEventListener('click', function () {
            if (isPlaying) stopAutoSpin();
            else startAutoSpin();
        });
    }

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

    showFrame(0);
});
</script>
@endpush
@endif
@endsection
