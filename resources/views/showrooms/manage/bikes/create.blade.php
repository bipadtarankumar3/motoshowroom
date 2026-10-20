@extends('layouts.app')

@section('title', 'Add Motorcycle - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}" class="hover:text-slate-900 transition">Inventory</a>
            <span>/</span>
            <span class="text-slate-700 font-semibold">New Motorcycle</span>
        </div>

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Add New Motorcycle</h1>
            <p class="text-sm text-slate-500 mt-1">Create a detailed motorcycle listing complete with technical specs, photo gallery, and color variants.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800 shadow-xs">
                <div class="font-bold text-sm mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please review the errors below:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('showrooms.manage.bikes.store', $showroom) }}" enctype="multipart/form-data" class="space-y-8" id="bike-form">
            @csrf

            <!-- Section 1: Basic Identity & Classification -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">1</span>
                    <span>Basic Details & Classification</span>
                </h2>
                <p class="text-xs text-slate-500 mb-6">Essential model identity and commercial classification.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- Bike Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Motorcycle Model Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               placeholder="e.g. Yamaha YZF-R1M Carbon"
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Brand -->
                    <div>
                        <label for="brand" class="block text-xs font-semibold text-slate-700 mb-1.5">Manufacturer Brand <span class="text-rose-500">*</span></label>
                        <input type="text" name="brand" id="brand" value="{{ old('brand') }}" required
                               placeholder="e.g. Yamaha, Ducati, KTM"
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Model Year -->
                    <div>
                        <label for="model_year" class="block text-xs font-semibold text-slate-700 mb-1.5">Model Year <span class="text-rose-500">*</span></label>
                        <input type="number" name="model_year" id="model_year" value="{{ old('model_year', date('Y')) }}" min="1980" max="2035" required
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Category Master -->
                    <div x-data="{
                        selectedCategory: '{{ old('category', $customCategories->first()?->slug ?? 'sports') }}',
                        customMap: {
                            @foreach($customCategories as $cc)
                                '{{ $cc->slug }}': {{ $cc->id }},
                            @endforeach
                        }
                    }">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="category" class="block text-xs font-semibold text-slate-700">Category Master <span class="text-rose-500">*</span></label>
                            <a href="{{ route('showrooms.manage.categories.index', $showroom) }}" target="_blank" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 inline-flex items-center gap-1">
                                + Manage Category Master
                            </a>
                        </div>
                        <input type="hidden" name="category_id" :value="customMap[selectedCategory] || ''">
                        <select name="category" id="category" x-model="selectedCategory" required
                                class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            @if(isset($customCategories) && $customCategories->isNotEmpty())
                                @foreach($customCategories as $cat)
                                    <option value="{{ $cat->slug }}" {{ old('category') === $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->name }} @if($cat->subtitle) &bull; {{ $cat->subtitle }} @endif
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled selected>No active categories in Master. Please add one first.</option>
                            @endif
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Classified directly under your dealership's Category Master.</p>
                    </div>

                    <!-- SKU -->
                    <div>
                        <label for="sku" class="block text-xs font-semibold text-slate-700 mb-1.5">SKU / Inventory Code <span class="text-slate-400 font-normal">(Optional)</span></label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku') }}"
                               placeholder="Auto-generated if empty"
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-mono">
                    </div>

                    <!-- Base Price -->
                    <div>
                        <label for="price" class="block text-xs font-semibold text-slate-700 mb-1.5">Base Price (₹ INR) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-sm">₹</span>
                            <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" required
                                   placeholder="250000.00"
                                   class="w-full pl-8 pr-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">Stock Status <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" required
                                class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            @foreach($statuses as $st)
                                <option value="{{ $st->value }}" {{ old('status', 'in_stock') === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Visibility Toggles -->
                    <div class="flex items-center gap-6 pt-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            <span class="text-xs font-semibold text-slate-700">Featured Showcase</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', '1') ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            <span class="text-xs font-semibold text-slate-700">Publish Immediately</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 2: Technical Specifications -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">2</span>
                    <span>Performance & Technical Specifications</span>
                </h2>
                <p class="text-xs text-slate-500 mb-6">Key mechanical metrics for buyer comparison.</p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <!-- Engine CC -->
                    <div>
                        <label for="engine_cc" class="block text-xs font-semibold text-slate-700 mb-1.5">Engine (cc)</label>
                        <input type="number" name="engine_cc" id="engine_cc" value="{{ old('engine_cc') }}"
                               placeholder="e.g. 998"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Power BHP -->
                    <div>
                        <label for="power_bhp" class="block text-xs font-semibold text-slate-700 mb-1.5">Power (BHP)</label>
                        <input type="number" step="0.01" name="power_bhp" id="power_bhp" value="{{ old('power_bhp') }}"
                               placeholder="e.g. 200.00"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Torque NM -->
                    <div>
                        <label for="torque_nm" class="block text-xs font-semibold text-slate-700 mb-1.5">Torque (Nm)</label>
                        <input type="number" step="0.01" name="torque_nm" id="torque_nm" value="{{ old('torque_nm') }}"
                               placeholder="e.g. 113.30"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Fuel Type -->
                    <div>
                        <label for="fuel_type" class="block text-xs font-semibold text-slate-700 mb-1.5">Fuel Type <span class="text-rose-500">*</span></label>
                        <select name="fuel_type" id="fuel_type" required
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="petrol" {{ old('fuel_type', 'petrol') === 'petrol' ? 'selected' : '' }}>Petrol</option>
                            <option value="electric" {{ old('fuel_type') === 'electric' ? 'selected' : '' }}>Electric</option>
                            <option value="hybrid" {{ old('fuel_type') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            <option value="diesel" {{ old('fuel_type') === 'diesel' ? 'selected' : '' }}>Diesel</option>
                        </select>
                    </div>

                    <!-- Transmission -->
                    <div>
                        <label for="transmission" class="block text-xs font-semibold text-slate-700 mb-1.5">Transmission</label>
                        <input type="text" name="transmission" id="transmission" value="{{ old('transmission') }}"
                               placeholder="e.g. 6-Speed Manual"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Mileage -->
                    <div>
                        <label for="mileage_kmpl" class="block text-xs font-semibold text-slate-700 mb-1.5">Mileage (km/l)</label>
                        <input type="number" step="0.01" name="mileage_kmpl" id="mileage_kmpl" value="{{ old('mileage_kmpl') }}"
                               placeholder="e.g. 15.50"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Top Speed -->
                    <div>
                        <label for="top_speed_kmh" class="block text-xs font-semibold text-slate-700 mb-1.5">Top Speed (km/h)</label>
                        <input type="number" name="top_speed_kmh" id="top_speed_kmh" value="{{ old('top_speed_kmh') }}"
                               placeholder="e.g. 299"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Curb Weight -->
                    <div>
                        <label for="curb_weight_kg" class="block text-xs font-semibold text-slate-700 mb-1.5">Curb Weight (kg)</label>
                        <input type="number" name="curb_weight_kg" id="curb_weight_kg" value="{{ old('curb_weight_kg') }}"
                               placeholder="e.g. 201"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>
                </div>

                <!-- Custom Key-Value Specs -->
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Additional Specifications & Features (Key / Value)</label>
                    <div class="space-y-2" id="specs-container">
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="spec_keys[]" value="Brakes & ABS" placeholder="e.g. Brakes & ABS"
                                   class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <input type="text" name="spec_values[]" value="Dual Channel Bosch ABS with Brembo Stylema" placeholder="Value"
                                   class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="spec_keys[]" value="Suspension" placeholder="e.g. Suspension"
                                   class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <input type="text" name="spec_values[]" value="Ohlins Electronic Racing Suspension" placeholder="Value"
                                   class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Description & Story -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">3</span>
                    <span>Marketing Story & Description</span>
                </h2>
                <p class="text-xs text-slate-500 mb-4">Engaging description displayed on the public storefront.</p>

                <textarea name="description" id="description" rows="4"
                          placeholder="Highlight the motorcycle's pedigree, technology, track capabilities, or riding comfort..."
                          class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">{{ old('description') }}</textarea>
            </div>

            <!-- Section 4: Bike Photos, Thumbnail & 360° Files -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs" x-data="{
                thumbnail: null,
                files: [],
                frames360: [],
                viewerType: 'image_sequence',
                model3dName: '',
                video360Name: '',
                handleThumbnailSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.thumbnail = {
                            name: file.name,
                            size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                            preview: URL.createObjectURL(file)
                        };
                    }
                },
                removeThumbnail() {
                    this.thumbnail = null;
                    const input = document.getElementById('thumbnail_file');
                    if (input) input.value = '';
                },
                handleFileSelect(event) {
                    this.files = Array.from(event.target.files).map(f => ({
                        name: f.name,
                        size: (f.size / 1024 / 1024).toFixed(2) + ' MB',
                        preview: URL.createObjectURL(f)
                    }));
                },
                removeGalleryFile(idx) {
                    this.files.splice(idx, 1);
                    const input = document.getElementById('bike_files');
                    if (input && this.files.length === 0) {
                        input.value = '';
                    }
                },
                handleFramesSelect(event) {
                    const selected = Array.from(event.target.files);
                    const count = selected.length;
                    this.frames360 = selected.map((f, i) => ({
                        name: f.name,
                        angle: count > 0 ? Math.round(i * (360 / count)) + '°' : '0°',
                        preview: URL.createObjectURL(f)
                    }));
                },
                removeFrame(idx) {
                    this.frames360.splice(idx, 1);
                    const input = document.getElementById('frames_360');
                    if (input && this.frames360.length === 0) {
                        input.value = '';
                    }
                },
                handleModel3dSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.model3dName = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                    }
                },
                handleVideo360Select(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.video360Name = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                    }
                }
            }">
                <h2 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">4</span>
                    <span>Motorcycle Photos, Thumbnail & 360° Studio Files</span>
                </h2>
                <p class="text-xs text-slate-500 mb-6">Upload the main cover thumbnail, normal gallery photos, and configure interactive 360° rotation or 3D models.</p>

                <!-- 4A: Dedicated Main Thumbnail / Cover Photo Upload -->
                <div class="mb-6 p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900">Cover Thumbnail Photo</h3>
                                <p class="text-[11px] text-slate-500">The primary card cover shown on catalog listings, search, and homepage showcase.</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-100">Primary</span>
                    </div>

                    <div x-show="!thumbnail" class="p-5 rounded-xl bg-slate-50 border-2 border-dashed border-slate-300 hover:border-rose-400 transition text-center cursor-pointer group">
                        <label for="thumbnail_file" class="cursor-pointer block">
                            <div class="w-10 h-10 mx-auto rounded-xl bg-white text-rose-600 flex items-center justify-center mb-2 shadow-2xs group-hover:scale-105 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-rose-600">Click to select motorcycle cover thumbnail</span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">JPG, PNG, WebP up to 10MB</span>
                        </label>
                        <input type="file" name="thumbnail" id="thumbnail_file" accept="image/*" @change="handleThumbnailSelect($event)" class="hidden">
                    </div>

                    <!-- Selected Thumbnail Preview -->
                    <div x-show="thumbnail" class="flex items-center justify-between p-3.5 bg-emerald-50/50 rounded-xl border border-emerald-200">
                        <div class="flex items-center gap-3.5">
                            <img :src="thumbnail ? thumbnail.preview : ''" class="w-20 h-14 object-cover rounded-lg border border-slate-200 shadow-2xs">
                            <div>
                                <span class="text-[10px] font-bold text-emerald-700 block">Cover Ready</span>
                                <p class="text-xs font-bold text-slate-900 truncate max-w-xs" x-text="thumbnail ? thumbnail.name : ''"></p>
                                <p class="text-[10px] text-slate-500" x-text="thumbnail ? thumbnail.size : ''"></p>
                            </div>
                        </div>
                        <button type="button" @click="removeThumbnail()" class="px-2.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg transition border border-rose-200">
                            Remove
                        </button>
                    </div>
                </div>

                <!-- 4B: Multiple Normal Bike Gallery Photos Upload -->
                <div class="mb-6 p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900">Motorcycle Photo Gallery</h3>
                                <p class="text-[11px] text-slate-500">Upload multiple regular photos for customer photo carousel (angles, cockpit, engine).</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">Carousel</span>
                    </div>

                    <div class="p-5 rounded-xl bg-slate-50 border-2 border-dashed border-slate-300 hover:border-indigo-400 transition text-center cursor-pointer group">
                        <label for="bike_files" class="cursor-pointer block">
                            <div class="w-9 h-9 mx-auto rounded-xl bg-white text-indigo-600 flex items-center justify-center mb-1.5 shadow-2xs group-hover:scale-105 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <span class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-indigo-600">Click to upload multiple gallery photos</span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Select multiple images together (JPG, PNG, WebP up to 10MB each)</span>
                        </label>
                        <input type="file" name="bike_files[]" id="bike_files" multiple accept="image/*" @change="handleFileSelect($event)" class="hidden">
                    </div>

                    <!-- Selected Gallery Files Preview Grid -->
                    <template x-if="files.length > 0">
                        <div class="mt-4 pt-4 border-t border-slate-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-800" x-text="files.length + ' photo(s) selected for upload'"></span>
                                <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Ready to upload upon saving
                                </span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                <template x-for="(file, idx) in files" :key="idx">
                                    <div class="relative rounded-xl border border-slate-200 overflow-hidden bg-white shadow-2xs group">
                                        <img :src="file.preview" class="w-full h-20 object-cover">
                                        <button type="button" @click="removeGalleryFile(idx)"
                                                class="absolute top-1 right-1 w-5 h-5 rounded-full bg-slate-900/80 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition shadow-2xs"
                                                title="Remove photo">
                                            &times;
                                        </button>
                                        <div class="p-1.5 text-[10px] truncate text-slate-700 bg-white" x-text="file.name"></div>
                                        <div class="px-1.5 pb-1 text-[9px] text-slate-400" x-text="file.size"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- 4C: Interactive 360° & 3D Media Suite -->
                <div class="mb-6 p-5 rounded-2xl bg-slate-50/70 border border-slate-200 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 pb-3 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900">Interactive 360° & 3D Media Suite</h3>
                                <p class="text-[11px] text-slate-500">Choose between sequential turntable frames, 3D WebGL CAD models, or turntable videos.</p>
                            </div>
                        </div>

                        <label class="inline-flex items-center gap-2 cursor-pointer select-none shrink-0 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs hover:border-slate-300 transition">
                            <input type="checkbox" name="has_360_view" value="1" checked class="w-4 h-4 rounded text-slate-900 focus:ring-slate-900 border-slate-300">
                            <span class="text-xs font-semibold text-slate-800">Enable 360° View</span>
                        </label>
                    </div>

                    <!-- Type Selector Segmented Control -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1.5 mb-4 p-1.5 bg-slate-200/60 rounded-2xl border border-slate-200">
                        <!-- Option 1: Turntable Frames -->
                        <button type="button" @click="viewerType = 'image_sequence'"
                                :class="viewerType === 'image_sequence' ? 'bg-white text-slate-900 shadow-sm border border-slate-200 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium'"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl text-left transition cursor-pointer">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition"
                                 :class="viewerType === 'image_sequence' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs leading-tight truncate">Turntable Frames</div>
                                <div class="text-[10px] text-slate-500 truncate">Sequential photos</div>
                            </div>
                        </button>

                        <!-- Option 2: 3D Model -->
                        <button type="button" @click="viewerType = 'gltf_model'"
                                :class="viewerType === 'gltf_model' ? 'bg-white text-slate-900 shadow-sm border border-slate-200 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium'"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl text-left transition cursor-pointer">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition"
                                 :class="viewerType === 'gltf_model' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs leading-tight truncate">3D WebGL Model</div>
                                <div class="text-[10px] text-slate-500 truncate">.GLB / .GLTF file</div>
                            </div>
                        </button>

                        <!-- Option 3: Turntable Video -->
                        <button type="button" @click="viewerType = 'video_loop'"
                                :class="viewerType === 'video_loop' ? 'bg-white text-slate-900 shadow-sm border border-slate-200 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium'"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl text-left transition cursor-pointer">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition"
                                 :class="viewerType === 'video_loop' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs leading-tight truncate">Turntable Video</div>
                                <div class="text-[10px] text-slate-500 truncate">MP4 / WebM loop</div>
                            </div>
                        </button>
                    </div>

                    <!-- Hidden input to submit selected viewer_type -->
                    <input type="hidden" name="viewer_type" :value="viewerType">

                    <!-- TYPE 1 VIEW: Multi-Frame Turntable Sequence -->
                    <div x-show="viewerType === 'image_sequence'" class="space-y-3">
                        <div class="p-6 rounded-2xl bg-white border-2 border-dashed border-slate-300 hover:border-slate-400 transition group text-center cursor-pointer shadow-2xs">
                            <label for="frames_360" class="cursor-pointer block">
                                <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 text-slate-900 flex items-center justify-center mb-2.5 group-hover:scale-105 transition shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-slate-700 block">Select 360° turntable frame images</span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">Select sequential photos (e.g. 16, 24, 36, or 72 frames) ordered clockwise (0° to 360°)</span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-2.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-semibold border border-slate-200">
                                    JPG, PNG, WebP up to 10MB per frame
                                </span>
                            </label>
                            <input type="file" name="frames_360[]" id="frames_360" multiple accept="image/*" @change="handleFramesSelect($event)" class="hidden">
                        </div>

                        <!-- 360 Frames Preview -->
                        <template x-if="frames360.length > 0">
                            <div class="mt-3 pt-3 border-t border-slate-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-slate-900" x-text="frames360.length + ' sequential 360° frames loaded'"></span>
                                    <span class="text-[11px] text-emerald-600 font-semibold">Angles auto-calculated clockwise</span>
                                </div>
                                <div class="grid grid-cols-3 sm:grid-cols-6 md:grid-cols-8 gap-2 max-h-48 overflow-y-auto p-1.5 bg-white rounded-xl border border-slate-200">
                                    <template x-for="(frame, idx) in frames360" :key="idx">
                                        <div class="relative rounded-lg border border-slate-200 overflow-hidden bg-white shadow-2xs text-center group">
                                            <img :src="frame.preview" class="w-full h-14 object-cover">
                                            <button type="button" @click="removeFrame(idx)"
                                                    class="absolute top-1 right-1 w-4 h-4 rounded-full bg-slate-900/80 hover:bg-rose-600 text-white flex items-center justify-center text-[10px] transition shadow-2xs"
                                                    title="Remove frame">
                                                &times;
                                            </button>
                                            <div class="text-[9px] font-bold text-slate-800 py-0.5 bg-slate-100" x-text="frame.angle"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- TYPE 2 VIEW: 3D WebGL CAD Model (.GLB / .GLTF) -->
                    <div x-show="viewerType === 'gltf_model'" class="space-y-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
                        <div class="p-5 rounded-xl border-2 border-dashed border-slate-300 hover:border-slate-400 transition text-center cursor-pointer bg-slate-50/50 group">
                            <label for="model_3d_file" class="cursor-pointer block">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 text-slate-900 flex items-center justify-center mb-2 group-hover:scale-105 transition shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-slate-700">Click to upload 3D Model File (.GLB or .GLTF)</span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">Compatible with Three.js WebGL orbit viewer & Google &lt;model-viewer&gt; (up to 50MB)</span>
                            </label>
                            <input type="file" name="model_3d_file" id="model_3d_file" accept=".glb,.gltf" @change="handleModel3dSelect($event)" class="hidden">
                        </div>

                        <div x-show="model3dName" class="flex items-center gap-2 p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-xs font-semibold text-emerald-800">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span x-text="'Selected 3D file: ' + model3dName"></span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Or Enter 3D Model CDN / Storage URL</label>
                            <input type="text" name="model_3d_path" value="{{ old('model_3d_path') }}"
                                   :disabled="viewerType !== 'gltf_model'"
                                   placeholder="e.g. /models/motorcycle.glb or https://cdn.example.com/models/motorcycle.glb"
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-500">
                        </div>
                    </div>

                    <!-- TYPE 3 VIEW: Turntable Video Loop -->
                    <div x-show="viewerType === 'video_loop'" class="space-y-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
                        <div class="p-5 rounded-xl border-2 border-dashed border-slate-300 hover:border-slate-400 transition text-center cursor-pointer bg-slate-50/50 group">
                            <label for="video_360_file" class="cursor-pointer block">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 text-slate-900 flex items-center justify-center mb-2 group-hover:scale-105 transition shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-slate-700">Click to upload 360° Rotating Video (.MP4 / .WebM)</span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">High definition 360 turntable video loop (up to 50MB)</span>
                            </label>
                            <input type="file" name="video_360_file" id="video_360_file" accept="video/mp4,video/webm,video/quicktime" @change="handleVideo360Select($event)" class="hidden">
                        </div>

                        <div x-show="video360Name" class="flex items-center gap-2 p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-xs font-semibold text-emerald-800">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span x-text="'Selected video file: ' + video360Name"></span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Or Provide 360 Video Stream URL</label>
                            <input type="text" name="model_3d_path" value="{{ old('model_3d_path') }}"
                                   :disabled="viewerType !== 'video_loop'"
                                   placeholder="https://cdn.example.com/videos/turntable-360.mp4"
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-500">
                        </div>
                    </div>
                </div>

                <!-- 4D: Direct Image URLs Fallback (Optional) -->
                <div class="p-4 rounded-xl bg-slate-50/60 border border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-700">Or Add Direct Image URLs (Optional Fallback)</span>
                    </div>
                    <div class="space-y-3" id="images-container">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center bg-white p-3 rounded-xl border border-slate-200">
                            <div class="sm:col-span-8">
                                <label class="block text-[11px] text-slate-500 mb-1">Image URL 1 (Optional)</label>
                                <input type="url" name="images[0][image_url]" value="{{ old('images.0.image_url') }}"
                                       placeholder="https://images.unsplash.com/..."
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                            <div class="sm:col-span-4">
                                <label class="block text-[11px] text-slate-500 mb-1">Alt Caption</label>
                                <input type="text" name="images[0][alt_text]" value="{{ old('images.0.alt_text', 'Front Angle View') }}"
                                       placeholder="e.g. Front 3/4 View"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <input type="hidden" name="images[0][is_primary]" value="1">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center bg-white p-3 rounded-xl border border-slate-200">
                            <div class="sm:col-span-8">
                                <label class="block text-[11px] text-slate-500 mb-1">Image URL 2 (Optional)</label>
                                <input type="url" name="images[1][image_url]" value="{{ old('images.1.image_url') }}"
                                       placeholder="https://images.unsplash.com/..."
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                            <div class="sm:col-span-4">
                                <label class="block text-[11px] text-slate-500 mb-1">Alt Caption</label>
                                <input type="text" name="images[1][alt_text]" value="{{ old('images.1.alt_text', 'Side Profile') }}"
                                       placeholder="e.g. Side Profile"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <input type="hidden" name="images[1][is_primary]" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Trim & Color Variants -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">5</span>
                    <span>Color & Trim Variants</span>
                </h2>
                <p class="text-xs text-slate-500 mb-4">Define available colorways, price adjustments, and stock quantity.</p>

                <div class="space-y-3" id="variants-container">
                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200 items-end">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] text-slate-500 mb-1">Variant Name</label>
                            <input type="text" name="variants[0][name]" value="Standard ABS" placeholder="e.g. Standard ABS"
                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Color Name</label>
                            <input type="text" name="variants[0][color_name]" value="Racing Blue" placeholder="e.g. Racing Blue"
                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Color Swatch</label>
                            <input type="color" name="variants[0][color_hex]" value="#1e40af"
                                   class="w-full h-8 bg-white border border-slate-300 rounded-xl cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Stock Qty</label>
                            <input type="number" name="variants[0][stock_quantity]" value="2" min="0"
                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <input type="hidden" name="variants[0][price_difference]" value="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-semibold text-slate-700 transition">
                    Cancel
                </a>

                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition flex items-center gap-2" id="btn-save-bike">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Save & Publish Motorcycle</span>
                </button>
            </div>
        </form>
    </main>
</div>
@endsection
