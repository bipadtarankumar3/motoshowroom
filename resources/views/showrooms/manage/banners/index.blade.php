<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-800 transition">Showroom Back-Office</a>
                    <span>/</span>
                    <span class="text-slate-700 font-bold">Hero Banners</span>
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>Hero &amp; Promotional Banners</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $banners->count() }} Banners
                    </span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Upload high-res motorcycle banner images, configure headlines, and control active carousel slides on your homepage.</p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('showrooms.show', $showroom) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 hover:border-slate-300 text-slate-700 text-xs font-bold shadow-sm transition hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>View Live Storefront</span>
                </a>
                <a href="{{ route('showrooms.manage.website', $showroom) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Website Builder</span>
                </a>
            </div>
        </div>
    </x-slot>

    @include('showrooms.partials.manage-nav')

    <div class="py-8 bg-slate-50 min-h-screen" x-data="{
        showCreateModal: false,
        editingBanner: null,
        openEdit(b) {
            this.editingBanner = Object.assign({}, b);
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 text-sm shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>Please fix the following issues:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs pl-6">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Overview Metrics Strip --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Banners</p>
                        <p class="text-2xl font-black text-slate-900 mt-1">{{ $banners->count() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active in Homepage Carousel</p>
                        <p class="text-2xl font-black text-emerald-600 mt-1">{{ $banners->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dealership Brand Accent</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-5 h-5 rounded-full border border-slate-300 shadow-sm inline-block" style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }};"></span>
                            <span class="text-sm font-mono font-bold text-slate-800">{{ $showroom->brand_color_primary ?? '#dc2626' }}</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    </div>
                </div>
            </div>

            {{-- Main Banners Section --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-black text-slate-900">Homepage Hero Banners</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Only active banners rotate in the hero carousel. Reorder, upload photos, or adjust call-to-actions.</p>
                    </div>

                    <button type="button"
                            @click="showCreateModal = true"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-wider shadow-md shadow-red-600/20 transition-all cursor-pointer"
                            style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }};">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Add New Banner</span>
                    </button>
                </div>

                {{-- Banners Card Grid --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($banners as $index => $banner)
                        <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-sm bg-slate-50 flex flex-col group hover:shadow-md transition">
                            {{-- Image Preview Stage --}}
                            <div class="relative aspect-[16/9] bg-slate-900 overflow-hidden">
                                <img src="{{ $banner->display_image_url }}" alt="{{ $banner->title_prefix }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                
                                {{-- Dark vignette --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>

                                {{-- Badge --}}
                                <div class="absolute top-3 left-3">
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-black/60 text-white backdrop-blur-sm border border-white/20">
                                        {{ $banner->badge ?? 'HERO' }}
                                    </span>
                                </div>

                                {{-- Active Indicator --}}
                                <div class="absolute top-3 right-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $banner->is_active ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-slate-300' }}">
                                        {{ $banner->is_active ? 'Active' : 'Hidden' }}
                                    </span>
                                </div>

                                {{-- Headline preview --}}
                                <div class="absolute bottom-3 left-3 right-3 text-white">
                                    <p class="font-black text-lg leading-tight drop-shadow-md">
                                        {{ $banner->title_prefix }}
                                        <span class="text-red-400" style="color: {{ $showroom->brand_color_primary ?? '#f87171' }};">{{ $banner->title_highlight }}</span>
                                        {{ $banner->title_suffix }}
                                    </p>
                                    @if($banner->image_path)
                                        <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-white/20 text-white">Custom Upload</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Card Details --}}
                            <div class="p-4 flex-1 flex flex-col justify-between space-y-3 bg-white">
                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                    {{ $banner->subtitle }}
                                </p>

                                <div class="flex items-center gap-2 text-[11px] text-slate-500 border-t border-slate-100 pt-3">
                                    <span class="font-bold text-slate-700">Buttons:</span>
                                    <span class="px-2 py-0.5 rounded bg-slate-100 font-semibold">{{ $banner->btn_primary_text ?: 'Default CTA' }}</span>
                                    @if($banner->btn_secondary_text)
                                        <span class="px-2 py-0.5 rounded bg-slate-100 font-semibold">{{ $banner->btn_secondary_text }}</span>
                                    @endif
                                </div>

                                {{-- Actions Footer --}}
                                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                                    <form action="{{ route('showrooms.manage.banners.toggle-active', [$showroom, $banner]) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-xl text-xs font-bold cursor-pointer transition {{ $banner->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200' }}">
                                            {{ $banner->is_active ? 'Disable' : 'Activate' }}
                                        </button>
                                    </form>

                                    <div class="flex items-center gap-1.5">
                                        <button type="button"
                                                @click="openEdit({{ json_encode($banner) }})"
                                                class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold transition cursor-pointer">
                                            Edit
                                        </button>

                                        <form action="{{ route('showrooms.manage.banners.destroy', [$showroom, $banner]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this hero banner?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                    title="Delete Banner">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-slate-400">
                            No hero banners created yet. Click "Add New Banner" to add a hero slide.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             MODAL: CREATE BANNER (WITH FILE UPLOAD)
             ══════════════════════════════════════════════════════════════════ --}}
        <div x-show="showCreateModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="showCreateModal = false"
                 class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
                <form action="{{ route('showrooms.manage.banners.store', $showroom) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-black text-slate-900">Add New Hero Banner</h3>
                            <p class="text-xs text-slate-500">Upload a banner image and configure headlines for the hero slider.</p>
                        </div>
                        <button type="button" @click="showCreateModal = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                        {{-- Banner Image File Upload --}}
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Banner Image Upload <span class="text-rose-500">*</span></label>
                            <input type="file" name="image_file" accept="image/*"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                            <p class="text-[11px] text-slate-400">High-resolution motorcycle photo (1920x1080 recommended, max 10MB).</p>

                            <div class="pt-2 border-t border-slate-200">
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Or Choose / Enter Image URL</label>
                                <input type="text" name="image_url" placeholder="/images/hero/hero_adventure_red.jpg or https://..."
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 font-mono">
                            </div>
                        </div>

                        {{-- Badge & Sort --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Top Badge</label>
                                <input type="text" name="badge" value="NEW COLLECTION 2026"
                                       class="w-full px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sort Order</label>
                                <input type="number" name="sort_order" value="0" min="0"
                                       class="w-full px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-900">
                            </div>
                        </div>

                        {{-- Main Headline in 3 parts --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Hero Headline (3 Parts)</label>
                            <div class="grid grid-cols-3 gap-2">
                                <input type="text" name="title_prefix" placeholder="Prefix (e.g. Fuel Your)" value="Fuel Your"
                                       class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-bold">
                                <input type="text" name="title_highlight" placeholder="Highlight (e.g. Next)" value="Next"
                                       class="px-3 py-2 bg-red-50 border border-red-300 text-red-700 rounded-xl text-xs font-black">
                                <input type="text" name="title_suffix" placeholder="Suffix (e.g. Adventure)" value="Adventure"
                                       class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-bold">
                            </div>
                        </div>

                        {{-- Subtitle --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Subtitle</label>
                            <textarea name="subtitle" rows="2"
                                      class="w-full px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900">Explore the latest bikes with unmatched performance, style and technology.</textarea>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Primary Button Text</label>
                                <input type="text" name="btn_primary_text" value="Explore Bikes"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Primary Button URL</label>
                                <input type="text" name="btn_primary_url" placeholder="/bikes or custom URL"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Secondary Button Text</label>
                                <input type="text" name="btn_secondary_text" value="Book a Test Ride"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Secondary Button URL</label>
                                <input type="text" name="btn_secondary_url" placeholder="/enquiries/create"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono">
                            </div>
                        </div>

                        {{-- 3 Trust Feature Highlights --}}
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">3 Trust Feature Badges</label>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <input type="text" name="feature_1_title" value="Trusted Showroom" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-bold">
                                    <input type="text" name="feature_1_sub" value="100% Genuine Bikes" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-[11px] text-slate-500 mt-1">
                                </div>
                                <div>
                                    <input type="text" name="feature_2_title" value="After Sales Support" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-bold">
                                    <input type="text" name="feature_2_sub" value="Always Here for You" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-[11px] text-slate-500 mt-1">
                                </div>
                                <div>
                                    <input type="text" name="feature_3_title" value="Best Price Guarantee" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-bold">
                                    <input type="text" name="feature_3_sub" value="Unbeatable Offers" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-[11px] text-slate-500 mt-1">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input type="checkbox" name="is_active" id="create_banner_active" value="1" checked
                                   class="w-4 h-4 text-red-600 border-slate-300 rounded focus:ring-red-500">
                            <label for="create_banner_active" class="text-xs font-bold text-slate-700 select-none cursor-pointer">
                                Active in Homepage Hero Slider
                            </label>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black uppercase tracking-wider shadow-md">Create Hero Banner</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             MODAL: EDIT BANNER (WITH FILE REPLACEMENT)
             ══════════════════════════════════════════════════════════════════ --}}
        <div x-show="editingBanner !== null"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="editingBanner = null"
                 class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
                <template x-if="editingBanner">
                    <form :action="'{{ url('showrooms/'.$showroom->slug.'/manage/banners') }}/' + editingBanner.id"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-black text-slate-900">Edit Hero Banner</h3>
                                <p class="text-xs text-slate-500">Update banner headlines, buttons, or upload a new photo.</p>
                            </div>
                            <button type="button" @click="editingBanner = null" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                            {{-- Image Preview & Replacement --}}
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Current Hero Banner Image</label>
                                <div class="w-full h-36 rounded-xl bg-slate-900 overflow-hidden border border-slate-300">
                                    <img :src="editingBanner.image_path ? ('{{ Storage::disk('public')->url('') }}' + editingBanner.image_path) : (editingBanner.image_url || '/images/hero/hero_adventure_red.jpg')"
                                         alt="" class="w-full h-full object-cover">
                                </div>

                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider pt-2">Upload Replacement Image File</label>
                                <input type="file" name="image_file" accept="image/*"
                                       class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                                <p class="text-[11px] text-slate-400">Uploading a file will replace the current hero image.</p>

                                <div class="pt-2 border-t border-slate-200">
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Or Image URL</label>
                                    <input type="text" name="image_url" x-model="editingBanner.image_url"
                                           class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Top Badge</label>
                                    <input type="text" name="badge" x-model="editingBanner.badge"
                                           class="w-full px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sort Order</label>
                                    <input type="number" name="sort_order" x-model="editingBanner.sort_order" min="0"
                                           class="w-full px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-900">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Headline (3 Parts)</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <input type="text" name="title_prefix" x-model="editingBanner.title_prefix"
                                           class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-bold">
                                    <input type="text" name="title_highlight" x-model="editingBanner.title_highlight"
                                           class="px-3 py-2 bg-red-50 border border-red-300 text-red-700 rounded-xl text-xs font-black">
                                    <input type="text" name="title_suffix" x-model="editingBanner.title_suffix"
                                           class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-bold">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Subtitle</label>
                                <textarea name="subtitle" x-model="editingBanner.subtitle" rows="2"
                                          class="w-full px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Primary Button Text</label>
                                    <input type="text" name="btn_primary_text" x-model="editingBanner.btn_primary_text"
                                           class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Primary Button URL</label>
                                    <input type="text" name="btn_primary_url" x-model="editingBanner.btn_primary_url"
                                           class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Secondary Button Text</label>
                                    <input type="text" name="btn_secondary_text" x-model="editingBanner.btn_secondary_text"
                                           class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Secondary Button URL</label>
                                    <input type="text" name="btn_secondary_url" x-model="editingBanner.btn_secondary_url"
                                           class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono">
                                </div>
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <input type="checkbox" name="is_active" id="edit_banner_active" value="1" :checked="editingBanner.is_active"
                                       class="w-4 h-4 text-red-600 border-slate-300 rounded focus:ring-red-500">
                                <label for="edit_banner_active" class="text-xs font-bold text-slate-700 select-none cursor-pointer">
                                    Active in Homepage Hero Slider
                                </label>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="button" @click="editingBanner = null" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-100">Cancel</button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black uppercase tracking-wider shadow-md">Save Changes</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>

    </div>
</x-app-layout>
