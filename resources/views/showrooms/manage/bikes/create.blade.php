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

        <form method="POST" action="{{ route('showrooms.manage.bikes.store', $showroom) }}" class="space-y-8" id="bike-form">
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

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-xs font-semibold text-slate-700 mb-1.5">Category <span class="text-rose-500">*</span></label>
                        <select name="category" id="category" required
                                class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->value }}" {{ old('category') === $cat->value ? 'selected' : '' }}>{{ $cat->label() }}</option>
                            @endforeach
                        </select>
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

            <!-- Section 4: Photo Gallery -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">4</span>
                    <span>High-Resolution Photo Gallery</span>
                </h2>
                <p class="text-xs text-slate-500 mb-4">Add direct image URLs to display in the showcase gallery.</p>

                <div class="space-y-3" id="images-container">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <div class="sm:col-span-8">
                            <label class="block text-[11px] text-slate-500 mb-1">Image URL 1 (Primary Photo)</label>
                            <input type="url" name="images[0][image_url]" value="{{ old('images.0.image_url', 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=1200&q=80') }}"
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

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <div class="sm:col-span-8">
                            <label class="block text-[11px] text-slate-500 mb-1">Image URL 2</label>
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
