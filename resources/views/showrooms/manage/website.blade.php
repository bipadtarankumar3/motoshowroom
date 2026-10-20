<x-app-layout :title="'Website Builder - ' . $showroom->name">
    @include('showrooms.partials.manage-nav')

    <div class="bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 xl:px-10">
        <div class="w-full space-y-8">

            <!-- Breadcrumbs & Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                        <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Showroom Back-Office</a>
                        <span>/</span>
                        <span class="text-rose-600 font-semibold">Website Builder</span>
                    </div>
                    <h1 class="text-3xl font-black font-display text-slate-900 tracking-tight">Storefront Customizer</h1>
                    <p class="text-sm text-slate-500 mt-1">Design your public customer-facing website with custom hero banners, branding, hours, and social channels.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                       class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold shadow-xs transition flex items-center gap-2" id="btn-preview-storefront">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Preview Storefront</span>
                    </a>
                </div>
            </div>

            <!-- Customizer Form -->
            <form method="POST" action="{{ route('showrooms.manage.website.update', $showroom) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Section 1: Hero Banner & Brand Colors -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                            Hero Banner & Visual Identity
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label for="headline" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Storefront Hero Headline *</label>
                            <input type="text" name="headline" id="headline" value="{{ old('headline', $showroom->headline) }}" required
                                   placeholder="e.g. Experience The Pure Thrill of Two Wheels"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 @error('headline') border-rose-500 @enderror">
                            @error('headline') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="tagline" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Storefront Sub-Headline / Tagline *</label>
                            <input type="text" name="tagline" id="tagline" value="{{ old('tagline', $showroom->tagline) }}" required
                                   placeholder="e.g. Authorized Superbike Dealer & Certified Service Center"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 @error('tagline') border-rose-500 @enderror">
                            @error('tagline') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="banner_url" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Hero Background Banner Image URL</label>
                            <input type="text" name="banner_url" id="banner_url" value="{{ old('banner_url', $showroom->banner_url) }}"
                                   placeholder="https://images.unsplash.com/photo-1558981403-c5f9899a28bc"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <span class="text-[11px] text-slate-500 mt-1 block">High-resolution landscape image (1920x1080 recommended).</span>
                        </div>

                        <div>
                            <label for="brand_color_primary" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Primary Brand Color (Hex)</label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="primary_picker" value="{{ old('brand_color_primary', $showroom->brand_color_primary ?? '#dc2626') }}"
                                       oninput="document.getElementById('brand_color_primary').value = this.value"
                                       class="w-10 h-10 rounded-lg cursor-pointer bg-white border border-slate-300 p-0">
                                <input type="text" name="brand_color_primary" id="brand_color_primary" value="{{ old('brand_color_primary', $showroom->brand_color_primary ?? '#dc2626') }}"
                                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 font-mono text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                        </div>

                        <div>
                            <label for="brand_color_accent" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Accent / Dark Brand Color (Hex)</label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="accent_picker" value="{{ old('brand_color_accent', $showroom->brand_color_accent ?? '#0f172a') }}"
                                       oninput="document.getElementById('brand_color_accent').value = this.value"
                                       class="w-10 h-10 rounded-lg cursor-pointer bg-white border border-slate-300 p-0">
                                <input type="text" name="brand_color_accent" id="brand_color_accent" value="{{ old('brand_color_accent', $showroom->brand_color_accent ?? '#0f172a') }}"
                                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 font-mono text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            </div>
                        </div>

                        <div class="md:col-span-2 pt-2 border-t border-slate-100">
                            <label class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Instant Brand Color Themes</label>
                            <p class="text-xs text-slate-500 mb-3">Choose a pre-tuned dealership theme. Changes update instantly across your storefront and user back-office.</p>
                            <div class="flex flex-wrap items-center gap-2">
                                @php
                                    $palettes = [
                                        ['name' => 'Crimson Red', 'primary' => '#dc2626', 'accent' => '#0f172a'],
                                        ['name' => 'Blaze Orange', 'primary' => '#f97316', 'accent' => '#0f172a'],
                                        ['name' => 'Racing Blue', 'primary' => '#2563eb', 'accent' => '#0f172a'],
                                        ['name' => 'Emerald Green', 'primary' => '#059669', 'accent' => '#0f172a'],
                                        ['name' => 'Royal Purple', 'primary' => '#7c3aed', 'accent' => '#0f172a'],
                                        ['name' => 'Amber Gold', 'primary' => '#d97706', 'accent' => '#0f172a'],
                                        ['name' => 'Neon Cyan', 'primary' => '#06b6d4', 'accent' => '#0f172a'],
                                        ['name' => 'Rose Pink', 'primary' => '#e11d48', 'accent' => '#0f172a'],
                                    ];
                                @endphp
                                @foreach($palettes as $p)
                                    <button type="button"
                                            onclick="document.getElementById('brand_color_primary').value = '{{ $p['primary'] }}'; document.getElementById('primary_picker').value = '{{ $p['primary'] }}'; document.getElementById('brand_color_accent').value = '{{ $p['accent'] }}'; document.getElementById('accent_picker').value = '{{ $p['accent'] }}';"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-slate-400 bg-white text-xs font-semibold text-slate-700 transition shadow-xs active:scale-95">
                                        <span class="w-3.5 h-3.5 rounded-full shadow-xs" style="background-color: {{ $p['primary'] }}"></span>
                                        <span>{{ $p['name'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dedicated Category & Banner Hub Navigation -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Hero Banners Card -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                    Hero Showcase
                                </span>
                                <span class="text-xs font-bold text-slate-500 font-mono">{{ $showroom->banners()->count() }} Banners</span>
                            </div>
                            <h3 class="text-base font-black text-slate-900 mt-2">Hero Showcase Banners</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Upload cinematic hero banners with custom photos, interactive badges, headlines, and call-to-action buttons for the homepage slider.
                            </p>
                        </div>
                        <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs text-slate-400">Rotates dynamically on home</span>
                            <a href="{{ route('showrooms.manage.banners.index', $showroom) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
                                <span>Manage Banners</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Category Master Card -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                    Inventory Taxonomy
                                </span>
                                <span class="text-xs font-bold text-slate-500 font-mono">{{ $showroom->categories()->count() }} Categories</span>
                            </div>
                            <h3 class="text-base font-black text-slate-900 mt-2">Category Master</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Upload category images, manage custom titles and subtitles, reorder categories, and classify inventory motorcycles directly under your master.
                            </p>
                        </div>
                        <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs text-slate-400">Used across inventory &amp; frontend</span>
                            <a href="{{ route('showrooms.manage.categories.index', $showroom) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
                                <span>Manage Categories</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Section: Homepage Sections Visibility & Content Control -->
                @php
                    $defaultSections = [
                        'show_hero' => true,
                        'show_categories' => true,
                        'show_featured_bikes' => true,
                        'show_services' => false,
                        'show_promo_banner' => false,
                        'show_trust_pillars' => true,
                        'show_testimonials' => false,
                        'show_about' => true,
                        'show_stats' => false,
                        'show_news' => true,
                        'categories_title' => 'Browse By Category',
                        'featured_bikes_title' => 'Premier Showcase',
                        'featured_bikes_count' => 6,
                    ];
                    $savedSections = is_array($homepageSections ?? null) ? $homepageSections : [];
                    $sectionsData = array_merge($defaultSections, $savedSections);
                    // Explicitly disable the 4 removed sections
                    $sectionsData['show_services'] = false;
                    $sectionsData['show_promo_banner'] = false;
                    $sectionsData['show_stats'] = false;
                    $sectionsData['show_testimonials'] = false;
                @endphp

                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6" id="homepage-sections-management"
                     x-data="{
                        sections: {{ json_encode($sectionsData) }}
                     }">

                    {{-- Hidden Serialized Homepage Sections JSON --}}
                    <input type="hidden" name="homepage_sections" :value="JSON.stringify(sections)">

                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                            Full Homepage Sections & Layout Management
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Toggle visibility, customize section titles, inventory counts, and promotional banners across your public storefront.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- 1. Hero Showcase -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">Hero Banners Section</span>
                                <span class="text-[11px] text-slate-500">Full-bleed slider with high-impact superbike hero cards.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" x-model="sections.show_hero" class="sr-only peer">
                                <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <!-- 2. Category Strip -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-900">Category Strip</span>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" x-model="sections.show_categories" class="sr-only peer">
                                    <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                                </label>
                            </div>
                            <input type="text" x-model="sections.categories_title" placeholder="Browse By Category"
                                   class="w-full px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-xs">
                        </div>

                        <!-- 3. Featured Inventory -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-900">Featured Inventory Showcase</span>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" x-model="sections.show_featured_bikes" class="sr-only peer">
                                    <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                                </label>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" x-model="sections.featured_bikes_title" placeholder="Premier Showcase"
                                       class="flex-1 px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-xs">
                                <input type="number" x-model="sections.featured_bikes_count" min="3" max="24"
                                       class="w-20 px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-xs font-mono">
                            </div>
                        </div>



                        <!-- 4. Trust Pillars ("Why Us") -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">Dealership Trust Pillars</span>
                                <span class="text-[11px] text-slate-500">Certified inspections, transparent pricing, warranties.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" x-model="sections.show_trust_pillars" class="sr-only peer">
                                <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <!-- 5. About & Showroom Hours -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">About Dealership Story & Hours Matrix</span>
                                <span class="text-[11px] text-slate-500">Showcases dealership narrative, location map, and operating hours.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" x-model="sections.show_about" class="sr-only peer">
                                <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <!-- 6. Moto Journalism & Latest News -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between md:col-span-2">
                            <div>
                                <span class="font-bold text-xs text-slate-900 block">Moto Journalism & Latest News</span>
                                <span class="text-[11px] text-slate-500">Display buyer's guides, safety standards, and road trip maintenance tips.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" x-model="sections.show_news" class="sr-only peer">
                                <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Section 2: About Our Showroom Story -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        About Our Showroom Story
                    </h2>

                    <div>
                        <label for="about_text" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">About Us Narrative</label>
                        <textarea name="about_text" id="about_text" rows="4"
                                  class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 leading-relaxed">{{ old('about_text', $showroom->about_text) }}</textarea>
                        <span class="text-[11px] text-slate-500 mt-1 block">Presented prominently to customers on your public website.</span>
                    </div>
                </div>

                <!-- Section 3: Operating Schedule Matrix -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <div>
                        <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            Weekly Operating Hours Matrix
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Powers the dynamic "Open Now / Closed" indicator on your website.</p>
                    </div>

                    <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 overflow-hidden">
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            @php
                                $dayData = $operatingHours[$day] ?? ['open' => '09:30', 'close' => '20:00', 'is_closed' => false];
                            @endphp
                            <div class="p-3 bg-white hover:bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <span class="font-bold text-slate-900 capitalize w-28">{{ $day }}</span>

                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <label class="text-[11px] text-slate-500">Open:</label>
                                        <input type="time" name="operating_hours[{{ $day }}][open]" value="{{ $dayData['open'] ?? '09:30' }}"
                                               class="px-2 py-1 rounded bg-white border border-slate-300 text-slate-900 text-xs font-mono">
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <label class="text-[11px] text-slate-500">Close:</label>
                                        <input type="time" name="operating_hours[{{ $day }}][close]" value="{{ $dayData['close'] ?? '20:00' }}"
                                               class="px-2 py-1 rounded bg-white border border-slate-300 text-slate-900 text-xs font-mono">
                                    </div>
                                </div>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="operating_hours[{{ $day }}][is_closed]" value="1" {{ !empty($dayData['is_closed']) ? 'checked' : '' }}
                                           class="rounded text-rose-600 focus:ring-rose-500 bg-white border-slate-300">
                                    <span class="text-slate-600 text-xs">Closed</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Section 4: Contact Hotline & Social Channels -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold font-display text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-sky-600"></span>
                        Customer Hotlines & Social Channels
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="whatsapp_number" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">WhatsApp Hotline (with country code)</label>
                            <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $showroom->getSetting('whatsapp_number')) }}"
                                   placeholder="+919876543210"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <span class="text-[11px] text-slate-500 mt-1 block">Enables instant 1-click WhatsApp customer chat button on storefront.</span>
                        </div>

                        <div>
                            <label for="google_maps_url" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Google Maps Embed Link</label>
                            <input type="text" name="google_maps_url" id="google_maps_url" value="{{ old('google_maps_url', $showroom->getSetting('google_maps_url')) }}"
                                   placeholder="https://maps.google.com/..."
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label for="social_instagram" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">Instagram Profile URL</label>
                            <input type="text" name="social_links[instagram]" id="social_instagram" value="{{ old('social_links.instagram', $socialLinks['instagram'] ?? '') }}"
                                   placeholder="https://instagram.com/yourhandle"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label for="social_youtube" class="block text-xs uppercase font-bold text-slate-700 tracking-wider mb-2">YouTube Channel URL</label>
                            <input type="text" name="social_links[youtube]" id="social_youtube" value="{{ old('social_links.youtube', $socialLinks['youtube'] ?? '') }}"
                                   placeholder="https://youtube.com/@yourchannel"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>
                    </div>
                </div>

                <!-- Section 5: Storefront Online Publishing -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="font-bold text-slate-900 text-base block">Publish Storefront Online</span>
                        <span class="text-xs text-slate-500">When enabled, customers can discover and browse your showroom at /showrooms/{{ $showroom->slug }}.</span>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" name="public_website_enabled" value="1" {{ old('public_website_enabled', $showroom->public_website_enabled) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <!-- Submit & Cancel -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="px-7 py-3 rounded-xl btn-brand-primary font-bold text-sm shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95"
                            style="box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;"
                            id="btn-save-website">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Save Storefront Settings</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
    @include('showrooms.partials.manage-nav-close')
</x-app-layout>
