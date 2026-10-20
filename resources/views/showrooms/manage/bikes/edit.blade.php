@extends('layouts.app')

@section('title', 'Edit ' . $bike->name . ' - ' . $showroom->name)

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
            <span class="text-slate-700 font-semibold">Edit {{ $bike->name }}</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Edit Motorcycle</h1>
                <p class="text-sm text-slate-500 mt-1">Updating specifications, pricing, media gallery, or availability for {{ $bike->name }}.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.manage.bikes.360', [$showroom, $bike]) }}"
                   class="px-4 py-2 rounded-xl bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 text-xs font-semibold text-cyan-800 transition flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>360° Studio</span>
                </a>

                @if($bike->is_published)
                    <a href="{{ route('showrooms.bikes.show', [$showroom, $bike]) }}" target="_blank"
                       class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-xs font-semibold text-slate-700 hover:text-slate-900 transition flex items-center gap-2 shadow-xs">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>View Storefront</span>
                    </a>
                @endif

                <form method="POST" action="{{ route('showrooms.manage.bikes.destroy', [$showroom, $bike]) }}" onsubmit="return confirm('Are you sure you want to delete this motorcycle?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 text-xs font-semibold transition flex items-center gap-2 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800 shadow-xs">
                <div class="font-bold text-sm mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('showrooms.manage.bikes.update', [$showroom, $bike]) }}" enctype="multipart/form-data" class="space-y-8" id="edit-bike-form">
            @csrf
            @method('PUT')

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
                        <input type="text" name="name" id="name" value="{{ old('name', $bike->name) }}" required
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Brand -->
                    <div>
                        <label for="brand" class="block text-xs font-semibold text-slate-700 mb-1.5">Manufacturer Brand <span class="text-rose-500">*</span></label>
                        <input type="text" name="brand" id="brand" value="{{ old('brand', $bike->brand) }}" required
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Model Year -->
                    <div>
                        <label for="model_year" class="block text-xs font-semibold text-slate-700 mb-1.5">Model Year <span class="text-rose-500">*</span></label>
                        <input type="number" name="model_year" id="model_year" value="{{ old('model_year', $bike->model_year) }}" min="1980" max="2035" required
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Category Master -->
                    @php
                        $currentSlug = old('category', $bike->categoryMaster?->slug ?? (is_object($bike->category) ? $bike->category->value : $bike->category));
                    @endphp
                    <div x-data="{
                        selectedCategory: '{{ $currentSlug }}',
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
                                    <option value="{{ $cat->slug }}" {{ $currentSlug === $cat->slug ? 'selected' : '' }}>
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
                        <label for="sku" class="block text-xs font-semibold text-slate-700 mb-1.5">SKU / Inventory Code <span class="text-rose-500">*</span></label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku', $bike->sku) }}" required
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-mono">
                    </div>

                    <!-- Base Price -->
                    <div>
                        <label for="price" class="block text-xs font-semibold text-slate-700 mb-1.5">Base Price (₹ INR) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-sm">₹</span>
                            <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $bike->price) }}" required
                                   class="w-full pl-8 pr-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">Stock Status <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" required
                                class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            @foreach($statuses as $st)
                                <option value="{{ $st->value }}" {{ old('status', $bike->status->value) === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Visibility Toggles -->
                    <div class="flex items-center gap-6 pt-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $bike->is_featured) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            <span class="text-xs font-semibold text-slate-700">Featured Showcase</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $bike->is_published) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            <span class="text-xs font-semibold text-slate-700">Published on Storefront</span>
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
                        <input type="number" name="engine_cc" id="engine_cc" value="{{ old('engine_cc', $bike->engine_cc) }}"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Power BHP -->
                    <div>
                        <label for="power_bhp" class="block text-xs font-semibold text-slate-700 mb-1.5">Power (BHP)</label>
                        <input type="number" step="0.01" name="power_bhp" id="power_bhp" value="{{ old('power_bhp', $bike->power_bhp) }}"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Torque NM -->
                    <div>
                        <label for="torque_nm" class="block text-xs font-semibold text-slate-700 mb-1.5">Torque (Nm)</label>
                        <input type="number" step="0.01" name="torque_nm" id="torque_nm" value="{{ old('torque_nm', $bike->torque_nm) }}"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Fuel Type -->
                    <div>
                        <label for="fuel_type" class="block text-xs font-semibold text-slate-700 mb-1.5">Fuel Type <span class="text-rose-500">*</span></label>
                        <select name="fuel_type" id="fuel_type" required
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="petrol" {{ old('fuel_type', $bike->fuel_type) === 'petrol' ? 'selected' : '' }}>Petrol</option>
                            <option value="electric" {{ old('fuel_type', $bike->fuel_type) === 'electric' ? 'selected' : '' }}>Electric</option>
                            <option value="hybrid" {{ old('fuel_type', $bike->fuel_type) === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            <option value="diesel" {{ old('fuel_type', $bike->fuel_type) === 'diesel' ? 'selected' : '' }}>Diesel</option>
                        </select>
                    </div>

                    <!-- Transmission -->
                    <div>
                        <label for="transmission" class="block text-xs font-semibold text-slate-700 mb-1.5">Transmission</label>
                        <input type="text" name="transmission" id="transmission" value="{{ old('transmission', $bike->transmission) }}"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Mileage -->
                    <div>
                        <label for="mileage_kmpl" class="block text-xs font-semibold text-slate-700 mb-1.5">Mileage (km/l)</label>
                        <input type="number" step="0.01" name="mileage_kmpl" id="mileage_kmpl" value="{{ old('mileage_kmpl', $bike->mileage_kmpl) }}"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Top Speed -->
                    <div>
                        <label for="top_speed_kmh" class="block text-xs font-semibold text-slate-700 mb-1.5">Top Speed (km/h)</label>
                        <input type="number" name="top_speed_kmh" id="top_speed_kmh" value="{{ old('top_speed_kmh', $bike->top_speed_kmh) }}"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <!-- Curb Weight -->
                    <div>
                        <label for="curb_weight_kg" class="block text-xs font-semibold text-slate-700 mb-1.5">Curb Weight (kg)</label>
                        <input type="number" name="curb_weight_kg" id="curb_weight_kg" value="{{ old('curb_weight_kg', $bike->curb_weight_kg) }}"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>
                </div>

                <!-- Custom Key-Value Specs -->
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Additional Specifications & Features</label>
                    <div class="space-y-2" id="specs-container">
                        @php
                            $specs = $bike->specifications ?? ['Brakes' => 'Dual Channel ABS', 'Suspension' => 'Adjustable USD Fork'];
                        @endphp
                        @foreach($specs as $sKey => $sVal)
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="spec_keys[]" value="{{ $sKey }}" placeholder="Specification Name"
                                       class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <input type="text" name="spec_values[]" value="{{ $sVal }}" placeholder="Specification Detail"
                                       class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                        @endforeach
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
                          class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">{{ old('description', $bike->description) }}</textarea>
            </div>

            <!-- Section 4: Photo Gallery, Thumbnail & 360° Studio Files -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs" x-data="bikeMediaEditManager()">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-5 mb-6 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">4</span>
                            <span>Motorcycle Media & 360° Studio</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Manage cover thumbnail, high-resolution photo gallery, and interactive 360° turntable presentation.</p>
                    </div>
                    @if($bike->has_360_view || $bike->frames360->count() > 0 || !empty($bike->model_3d_path))
                        <a href="{{ route('showrooms.manage.bikes.360', [$showroom, $bike]) }}" target="_blank"
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-800 text-xs font-bold hover:bg-cyan-100 transition shadow-2xs self-start sm:self-auto">
                            <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Open 360° Studio ({{ $bike->frames360->count() }} frames)</span>
                        </a>
                    @endif
                </div>

                <!-- 4A: Main Cover / Thumbnail Photo -->
                <div class="mb-6 p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900">Cover Thumbnail Photo</h3>
                                <p class="text-[11px] text-slate-500">The primary motorcycle image displayed on catalog cards, featured showcases, and search results.</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-100">Primary</span>
                    </div>

                    @php
                        $primaryImage = $bike->primaryImage ?? $bike->images->first();
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                        @if($primaryImage)
                            <div x-show="!thumbnail" class="md:col-span-5 flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <img src="{{ $primaryImage->image_url }}" alt="Current Primary Cover" class="w-24 h-18 object-cover rounded-lg border border-slate-200 bg-white shadow-2xs" onerror="this.src='https://placehold.co/120x90?text=Cover'">
                                <div class="min-w-0 flex-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 mb-1">
                                        Active Showcase Cover
                                    </span>
                                    <p class="text-xs font-semibold text-slate-800 truncate">{{ $primaryImage->alt_text ?? 'Current Main Cover' }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ $primaryImage->image_url }}</p>
                                </div>
                            </div>
                        @endif

                        <div :class="{{ $primaryImage ? "'md:col-span-7'" : "'md:col-span-12'" }}">
                            <!-- Dropzone to upload/replace -->
                            <div x-show="!thumbnail" class="p-4 rounded-xl bg-slate-50 border-2 border-dashed border-slate-300 hover:border-rose-400 transition text-center cursor-pointer group">
                                <label for="thumbnail_file_edit" class="cursor-pointer block">
                                    <span class="text-xs font-bold text-slate-900 group-hover:text-rose-600">
                                        {{ $primaryImage ? 'Click to upload a new cover thumbnail' : 'Click to select motorcycle cover thumbnail' }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">JPG, PNG, WebP up to 10MB</span>
                                </label>
                                <input type="file" name="thumbnail" id="thumbnail_file_edit" accept="image/*" @change="handleThumbnailSelect($event)" class="hidden">
                            </div>

                            <!-- Selected New Thumbnail Preview -->
                            <div x-show="thumbnail" class="flex items-center justify-between p-3 bg-emerald-50/50 rounded-xl border border-emerald-200">
                                <div class="flex items-center gap-3">
                                    <img :src="thumbnail ? thumbnail.preview : ''" class="w-16 h-12 object-cover rounded-lg border border-slate-200 shadow-2xs">
                                    <div>
                                        <span class="text-[10px] font-bold text-emerald-700 block">New Cover Ready</span>
                                        <p class="text-xs font-bold text-slate-900 truncate max-w-xs" x-text="thumbnail ? thumbnail.name : ''"></p>
                                        <p class="text-[10px] text-slate-500" x-text="thumbnail ? thumbnail.size : ''"></p>
                                    </div>
                                </div>
                                <button type="button" @click="removeThumbnail()" class="px-2.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg transition border border-rose-200">
                                    Revert
                                </button>
                            </div>
                        </div>
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
                                <p class="text-[11px] text-slate-500">Upload multiple high-resolution photos for the customer photo carousel.</p>
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

                    <!-- Selected Files Preview Grid -->
                    <template x-if="files.length > 0">
                        <div class="mt-4 pt-4 border-t border-slate-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-800" x-text="files.length + ' new photo(s) selected for upload'"></span>
                                <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Ready to append upon saving
                                </span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                <template x-for="(file, idx) in files" :key="idx">
                                    <div class="relative rounded-xl border border-slate-200 overflow-hidden bg-white shadow-2xs group">
                                        <img :src="file.preview" class="w-full h-20 object-cover">
                                        <button type="button" @click="removeGalleryFile(idx)"
                                                class="absolute top-1 right-1 w-5 h-5 rounded-full bg-slate-900/80 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition shadow-2xs"
                                                title="Remove this photo">
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
                            <input type="hidden" name="has_360_view" value="0">
                            <input type="checkbox" name="has_360_view" value="1" {{ old('has_360_view', $bike->has_360_view) ? 'checked' : '' }} class="w-4 h-4 rounded text-slate-900 focus:ring-slate-900 border-slate-300">
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

                    <!-- TYPE 1: Turntable Image Sequence Dropzone & Frames Suite -->
                    <div x-show="viewerType === 'image_sequence'" class="space-y-4">

                        <!-- 1. Existing Turntable Frames (if any) -->
                        @if($bike->frames360->isNotEmpty())
                            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-3">
                                <div class="flex items-center justify-between flex-wrap gap-2 pb-2.5 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full bg-cyan-500 animate-pulse"></div>
                                        <h4 class="text-xs font-bold text-slate-900">
                                            Current Turntable Frames ({{ $bike->frames360->count() }} frames)
                                        </h4>
                                        <span class="text-[11px] text-slate-500 hidden sm:inline">• Ordered 0° to 360°</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('showrooms.manage.bikes.360', [$showroom, $bike]) }}"
                                           target="_blank"
                                           class="px-3 py-1 rounded-xl text-xs font-bold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 transition flex items-center gap-1.5 shadow-2xs">
                                            <span>Open in 360° Studio</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 sm:grid-cols-6 md:grid-cols-9 gap-2">
                                    @foreach($bike->frames360 as $frame)
                                        <div id="frame-card-{{ $frame->id }}"
                                             x-show="!deletedFrameIds.includes({{ $frame->id }})"
                                             class="relative rounded-xl border border-slate-200 overflow-hidden bg-slate-50 shadow-2xs text-center group transition">
                                            <img src="{{ $frame->image_url }}" alt="{{ $frame->alt_text }}" class="w-full h-16 object-cover bg-white">
                                            <button type="button"
                                                    @click="deleteExistingFrame({{ $frame->id }}, '{{ route('showrooms.manage.bikes.360.frames.destroy', [$showroom, $bike, $frame]) }}')"
                                                    class="absolute top-1 right-1 w-5 h-5 rounded-full bg-slate-900/80 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition shadow-2xs cursor-pointer opacity-80 group-hover:opacity-100"
                                                    title="Delete this frame">
                                                &times;
                                            </button>
                                            <div class="text-[9px] font-bold font-mono text-slate-700 py-0.5 bg-slate-100/90 border-t border-slate-200">
                                                {{ $frame->frame_angle ?? 0 }}°
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <template x-for="id in deletedFrameIds" :key="id">
                                    <input type="hidden" name="delete_frames[]" :value="id">
                                </template>
                            </div>
                        @endif

                        <!-- 2. Add / Upload New 360 Frames (Switcher between File Upload & Paste URLs) -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-3.5">
                            <div class="flex items-center justify-between flex-wrap gap-2 pb-2.5 border-b border-slate-100">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">Add 360° Turntable Frames</h4>
                                    <p class="text-[11px] text-slate-500">Upload new sequential photos or paste direct image URLs (ordered clockwise 0° to 360°).</p>
                                </div>

                                <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 border border-slate-200">
                                    <button type="button" @click="frameInputMode = 'upload'"
                                            :class="frameInputMode === 'upload' ? 'bg-white text-slate-900 font-bold shadow-2xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900'"
                                            class="px-3 py-1 rounded-lg text-xs transition flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Upload Files</span>
                                    </button>
                                    <button type="button" @click="frameInputMode = 'urls'"
                                            :class="frameInputMode === 'urls' ? 'bg-white text-slate-900 font-bold shadow-2xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900'"
                                            class="px-3 py-1 rounded-lg text-xs transition flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        <span>Paste URLs</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Mode A: File Upload Dropzone -->
                            <div x-show="frameInputMode === 'upload'" class="space-y-3">
                                <div class="p-6 rounded-2xl bg-slate-50/60 border-2 border-dashed border-slate-300 hover:border-slate-400 transition group text-center cursor-pointer shadow-2xs">
                                    <label for="frames_360" class="cursor-pointer block">
                                        <div class="w-12 h-12 mx-auto rounded-2xl bg-white text-slate-900 flex items-center justify-center mb-2.5 group-hover:scale-105 transition shadow-2xs border border-slate-200">
                                            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </div>
                                        <span class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-slate-700 block">Click to select 360° turntable frame images</span>
                                        <span class="text-[11px] text-slate-500 block mt-0.5">Select sequential photos (e.g. 16, 24, 36, or 72 frames) ordered clockwise (0° to 360°)</span>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-2.5 rounded-full bg-white text-slate-600 text-[10px] font-semibold border border-slate-200">
                                            JPG, PNG, WebP up to 10MB per frame
                                        </span>
                                    </label>
                                    <input type="file" name="frames_360[]" id="frames_360" multiple accept="image/*" @change="handleFramesSelect($event)" class="hidden">
                                </div>

                                <!-- 360 Frames Preview Grid for newly selected files -->
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
                                                            class="absolute top-1 right-1 w-4 h-4 rounded-full bg-slate-900/80 hover:bg-rose-600 text-white flex items-center justify-center text-[10px] transition shadow-2xs cursor-pointer"
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

                            <!-- Mode B: Bulk URLs Textarea -->
                            <div x-show="frameInputMode === 'urls'" class="space-y-3">
                                <div>
                                    <label for="frames_360_urls" class="block text-xs font-bold text-slate-800 mb-1">
                                        Paste Frame Image URLs (one per line)
                                    </label>
                                    <textarea name="frames_360_urls" id="frames_360_urls" rows="5"
                                              x-model="bulkFrameUrls"
                                              placeholder="https://cdn.example.com/bike-360-00.jpg&#10;https://cdn.example.com/bike-360-01.jpg&#10;https://cdn.example.com/bike-360-02.jpg"
                                              class="w-full text-xs font-mono rounded-xl border border-slate-300 p-3 text-slate-900 focus:border-slate-900 focus:ring-slate-900 bg-white placeholder-slate-400"></textarea>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500">
                                    <span>Angles will be distributed evenly across 360° in the order listed.</span>
                                    <span class="font-bold text-slate-700" x-text="bulkFrameUrlsCount + ' URLs detected'"></span>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- TYPE 2: 3D WebGL CAD Model (.GLB / .GLTF) -->
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
                            <input type="text" name="model_3d_path" value="{{ old('model_3d_path', $bike->viewer_type === 'gltf_model' ? $bike->model_3d_path : '') }}"
                                   :disabled="viewerType !== 'gltf_model'"
                                   placeholder="e.g. /models/motorcycle.glb or https://cdn.example.com/models/motorcycle.glb"
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-500">
                        </div>
                    </div>

                    <!-- TYPE 3: Turntable Video Loop -->
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
                            <input type="text" name="model_3d_path" value="{{ old('model_3d_path', $bike->viewer_type === 'video_loop' ? $bike->model_3d_path : '') }}"
                                   :disabled="viewerType !== 'video_loop'"
                                   placeholder="https://cdn.example.com/videos/turntable-360.mp4"
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-500">
                        </div>
                    </div>
                </div>

                <!-- 4D: Current Gallery Photos & URLs -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-800">Current Gallery Images & External URLs</label>
                            <p class="text-[11px] text-slate-500">Manage, edit captions, or remove existing photos from the motorcycle gallery.</p>
                        </div>
                        <button type="button" @click="addPhotoUrlRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Add Photo URL</span>
                        </button>
                    </div>

                    <div class="space-y-3" id="images-container">
                        @forelse($bike->images as $idx => $img)
                            <div class="gallery-image-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-center bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs group hover:border-slate-300 transition" id="image-row-{{ $img->id }}">
                                <div class="sm:col-span-2 flex items-center justify-center relative">
                                    <img src="{{ $img->image_url }}" alt="{{ $img->alt_text }}" class="w-16 h-12 rounded-lg object-cover border border-slate-200 bg-white" onerror="this.src='https://placehold.co/100x75?text=Image'">
                                    @if($img->is_primary)
                                        <span class="absolute -top-1.5 -left-1.5 bg-rose-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-2xs">Cover</span>
                                    @endif
                                </div>
                                <div class="sm:col-span-5">
                                    <label class="block text-[11px] text-slate-500 mb-1">Image URL {{ $idx + 1 }} @if($img->is_primary) (Primary Photo) @endif</label>
                                    <input type="url" name="images[{{ $idx }}][image_url]" value="{{ $img->image_url }}"
                                           class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                </div>
                                <div class="sm:col-span-4">
                                    <label class="block text-[11px] text-slate-500 mb-1">Caption</label>
                                    <input type="text" name="images[{{ $idx }}][alt_text]" value="{{ $img->alt_text }}"
                                           class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                    <input type="hidden" name="images[{{ $idx }}][is_primary]" value="{{ $img->is_primary ? '1' : '0' }}">
                                </div>
                                <div class="sm:col-span-1 flex items-center justify-center sm:pt-4">
                                    <button type="button"
                                            @click="deleteExistingImage({{ $img->id }}, '{{ route('showrooms.manage.bikes.images.destroy', [$showroom, $bike, $img]) }}', $el.closest('#image-row-{{ $img->id }}'))"
                                            :disabled="deletingImageId === {{ $img->id }}"
                                            class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 transition cursor-pointer"
                                            title="Delete this photo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="gallery-image-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-center bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                                <div class="sm:col-span-8">
                                    <label class="block text-[11px] text-slate-500 mb-1">Primary Image URL</label>
                                    <input type="url" name="images[0][image_url]" value="https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=1200&q=80"
                                           class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                </div>
                                <div class="sm:col-span-4">
                                    <label class="block text-[11px] text-slate-500 mb-1">Caption</label>
                                    <input type="text" name="images[0][alt_text]" value="Showcase View"
                                           class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                    <input type="hidden" name="images[0][is_primary]" value="1">
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Section 5: Color Variants -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">5</span>
                    <span>Color & Trim Variants</span>
                </h2>
                <p class="text-xs text-slate-500 mb-4">Manage colors, price variations, and inventory quantities.</p>

                <div class="space-y-3" id="variants-container">
                    @forelse($bike->variants as $vIdx => $v)
                        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200 items-end">
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] text-slate-500 mb-1">Variant Name</label>
                                <input type="text" name="variants[{{ $vIdx }}][name]" value="{{ $v->name }}"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Color Name</label>
                                <input type="text" name="variants[{{ $vIdx }}][color_name]" value="{{ $v->color_name }}"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Color Swatch</label>
                                <input type="color" name="variants[{{ $vIdx }}][color_hex]" value="{{ $v->color_hex ?? '#1e40af' }}"
                                       class="w-full h-8 bg-white border border-slate-300 rounded-xl cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Stock Qty</label>
                                <input type="number" name="variants[{{ $vIdx }}][stock_quantity]" value="{{ $v->stock_quantity }}" min="0"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <input type="hidden" name="variants[{{ $vIdx }}][price_difference]" value="{{ $v->price_difference }}">
                            </div>
                        </div>
                    @empty
                        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200 items-end">
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] text-slate-500 mb-1">Variant Name</label>
                                <input type="text" name="variants[0][name]" value="Standard Edition"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Color Name</label>
                                <input type="text" name="variants[0][color_name]" value="Matte Black"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Color Swatch</label>
                                <input type="color" name="variants[0][color_hex]" value="#1a1a1a"
                                       class="w-full h-8 bg-white border border-slate-300 rounded-xl cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-1">Stock Qty</label>
                                <input type="number" name="variants[0][stock_quantity]" value="1" min="0"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <input type="hidden" name="variants[0][price_difference]" value="0">
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Action Bar -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-semibold text-slate-700 transition">
                    Cancel
                </a>

                <button type="submit"
                        class="px-6 py-2.5 rounded-xl btn-brand-primary font-bold text-xs shadow-md transition flex items-center gap-2"
                        style="box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;"
                        id="btn-update-bike">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Update Motorcycle</span>
                </button>
            </div>
        </form>
    </main>
</div>

<script>
function bikeMediaEditManager() {
    return {
        thumbnail: null,
        files: [],
        frames360: [],
        frameInputMode: 'upload',
        bulkFrameUrls: '',
        deletedFrameIds: [],
        viewerType: '{{ old('viewer_type', ($bike->viewer_type === 'external_embed' ? 'gltf_model' : ($bike->viewer_type ?? 'image_sequence'))) }}',
        model3dName: '',
        video360Name: '',
        deletingImageId: null,
        get bulkFrameUrlsCount() {
            if (!this.bulkFrameUrls) return 0;
            return this.bulkFrameUrls.split('\n').filter(l => l.trim().length > 0).length;
        },
        async deleteExistingFrame(frameId, deleteUrl) {
            if (!confirm('Are you sure you want to delete this 360° frame?')) {
                return;
            }
            if (!this.deletedFrameIds.includes(frameId)) {
                this.deletedFrameIds.push(frameId);
            }
            try {
                await fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
            } catch (e) {
                console.error('Frame delete error:', e);
            }
        },
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
            const input = document.getElementById('thumbnail_file_edit');
            if (input) input.value = '';
        },
        handleFileSelect(event) {
            const selected = Array.from(event.target.files);
            this.files = selected.map(file => ({
                name: file.name,
                size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                preview: URL.createObjectURL(file)
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
            this.frames360 = selected.map((file, idx) => ({
                name: file.name,
                angle: count > 0 ? Math.round((idx / count) * 360) + '°' : '0°',
                preview: URL.createObjectURL(file)
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
        },
        async deleteExistingImage(imageId, deleteUrl, el) {
            if (!confirm('Are you sure you want to remove this photo from the gallery?')) {
                return;
            }
            this.deletingImageId = imageId;
            try {
                const res = await fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                if (res.ok) {
                    el.style.opacity = '0';
                    el.style.transform = 'scale(0.95)';
                    setTimeout(() => el.remove(), 200);
                } else {
                    alert('Failed to remove image. Please try again.');
                }
            } catch (e) {
                el.remove();
            } finally {
                this.deletingImageId = null;
            }
        },
        addPhotoUrlRow() {
            const container = document.getElementById('images-container');
            if (!container) return;
            const count = container.querySelectorAll('.gallery-image-row').length + Date.now();
            const newRow = document.createElement('div');
            newRow.className = 'gallery-image-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-center bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs';
            newRow.innerHTML = `
                <div class="sm:col-span-2 flex items-center justify-center text-xs text-slate-400 font-semibold">New Photo</div>
                <div class="sm:col-span-5">
                    <label class="block text-[11px] text-slate-500 mb-1">Image URL</label>
                    <input type="url" name="images[${count}][image_url]" placeholder="https://images.unsplash.com/..." class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                </div>
                <div class="sm:col-span-4">
                    <label class="block text-[11px] text-slate-500 mb-1">Caption</label>
                    <input type="text" name="images[${count}][alt_text]" placeholder="Angle or feature description" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    <input type="hidden" name="images[${count}][is_primary]" value="0">
                </div>
                <div class="sm:col-span-1 flex items-center justify-center sm:pt-4">
                    <button type="button" onclick="this.closest('.gallery-image-row').remove()" class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 transition cursor-pointer" title="Remove row">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
        }
    };
}
</script>
@endsection
