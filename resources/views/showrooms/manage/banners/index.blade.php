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

    <div class="py-8 bg-slate-50 min-h-screen px-4 sm:px-6 lg:px-8 xl:px-10" x-data="{
        showCreateModal: false,
        editingBanner: null,
        createImagePreview: null,
        editImagePreview: null,
        openEdit(b) {
            this.editingBanner = Object.assign({}, b);
            this.editImagePreview = null;
            if (this.$refs.editFileInput) {
                this.$refs.editFileInput.value = '';
            }
        },
        handleCreateFile(e) {
            const file = e.target.files[0];
            if (file) {
                this.createImagePreview = URL.createObjectURL(file);
            }
        },
        clearCreateImage() {
            this.createImagePreview = null;
            if (this.$refs.createFileInput) {
                this.$refs.createFileInput.value = '';
            }
            if (this.$refs.createUrlInput) {
                this.$refs.createUrlInput.value = '';
            }
        },
        handleEditFile(e) {
            const file = e.target.files[0];
            if (file) {
                this.editImagePreview = URL.createObjectURL(file);
            }
        },
        clearEditImage() {
            this.editImagePreview = null;
            if (this.$refs.editFileInput) {
                this.$refs.editFileInput.value = '';
            }
            if (this.$refs.editUrlInput) {
                this.$refs.editUrlInput.value = '';
            }
            if (this.editingBanner) {
                this.editingBanner.image_url = '';
            }
        }
    }">
        <div class="w-full space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 text-sm shadow-xs">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs space-y-1">
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Slides</p>
                        <p class="text-2xl sm:text-3xl font-black font-display text-slate-900 mt-1">{{ $banners->count() }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active in Carousel</p>
                        <p class="text-2xl sm:text-3xl font-black font-display text-emerald-600 mt-1">{{ $banners->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Inactive / Hidden</p>
                        <p class="text-2xl sm:text-3xl font-black font-display text-slate-500 mt-1">{{ $banners->where('is_active', false)->count() }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Brand Highlight</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-5 h-5 rounded-lg border border-slate-300 shadow-xs inline-block" style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }};"></span>
                            <span class="text-sm font-mono font-bold text-slate-800">{{ $showroom->brand_color_primary ?? '#dc2626' }}</span>
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    </div>
                </div>
            </div>

            {{-- Main Banners Section --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black font-display text-slate-900 tracking-tight">Homepage Hero Banners</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Active banners rotate in the hero carousel on {{ $showroom->name }} storefront. Reorder, upload custom high-resolution slides, or configure lead conversion CTA buttons.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button"
                                @click="showCreateModal = true"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl btn-brand-primary text-xs font-black uppercase tracking-wider shadow-md transition-all cursor-pointer active:scale-95"
                                style="box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Add New Banner</span>
                        </button>
                    </div>
                </div>

                {{-- Banners Full-Width Responsive Card Grid with Rich Details --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($banners as $index => $banner)
                        <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-xs bg-slate-50 flex flex-col group hover:shadow-md hover:border-slate-300 transition duration-200">
                            {{-- Image Preview Stage --}}
                            <div class="relative aspect-[16/9] bg-slate-900 overflow-hidden">
                                <img src="{{ $banner->display_image_url }}" alt="{{ $banner->title_prefix }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                
                                {{-- Vignette Overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>

                                {{-- Top Left Badge & Order Pill --}}
                                <div class="absolute top-3 left-3 flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-black/60 text-white backdrop-blur-md border border-white/20">
                                        {{ $banner->badge ?? 'HERO' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-white/20 text-white backdrop-blur-sm border border-white/10">
                                        Slot #{{ $banner->sort_order }}
                                    </span>
                                </div>

                                {{-- Top Right Active Pill --}}
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $banner->is_active ? 'bg-emerald-500 text-white shadow-sm' : 'bg-slate-700/80 text-slate-300 backdrop-blur-sm' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $banner->is_active ? 'bg-white animate-pulse' : 'bg-slate-400' }}"></span>
                                        {{ $banner->is_active ? 'Active Slide' : 'Hidden' }}
                                    </span>
                                </div>

                                {{-- Headline preview --}}
                                <div class="absolute bottom-3 left-3 right-3 text-white">
                                    <p class="font-black text-lg leading-snug drop-shadow-md">
                                        {{ $banner->title_prefix }}
                                        <span class="text-rose-400" style="color: {{ $showroom->brand_color_primary ?? '#f87171' }};">{{ $banner->title_highlight }}</span>
                                        {{ $banner->title_suffix }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if($banner->image_path)
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-white/20 text-white backdrop-blur-sm border border-white/20">Uploaded Image File</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-white/10 text-slate-200 backdrop-blur-sm border border-white/10">Hosted Web Asset</span>
                                        @endif
                                        <span class="text-[10px] text-slate-300 font-mono">{{ $banner->created_at?->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Detailed Card Body --}}
                            <div class="p-5 flex-1 flex flex-col justify-between space-y-4 bg-white">
                                <div class="space-y-3">
                                    <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                        {{ $banner->subtitle }}
                                    </p>

                                    {{-- Call-To-Action Buttons Breakdown --}}
                                    <div class="space-y-1.5 border-t border-slate-100 pt-3">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-slate-400 font-medium">Primary CTA:</span>
                                            <div class="flex items-center gap-1.5 truncate max-w-[200px]">
                                                <span class="font-bold text-slate-900 truncate">{{ $banner->btn_primary_text ?: 'Default CTA' }}</span>
                                                <span class="text-[10px] font-mono text-slate-400 truncate">({{ $banner->btn_primary_url ?: '/bikes' }})</span>
                                            </div>
                                        </div>
                                        @if($banner->btn_secondary_text)
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-slate-400 font-medium">Secondary CTA:</span>
                                                <div class="flex items-center gap-1.5 truncate max-w-[200px]">
                                                    <span class="font-bold text-slate-700 truncate">{{ $banner->btn_secondary_text }}</span>
                                                    <span class="text-[10px] font-mono text-slate-400 truncate">({{ $banner->btn_secondary_url ?: '/enquiries' }})</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Trust Feature Highlights Badges in Listing --}}
                                    <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100 grid grid-cols-3 gap-2 text-center">
                                        <div class="truncate">
                                            <p class="text-[10px] font-bold text-slate-800 truncate">{{ $banner->feature_1_title ?? 'Verified' }}</p>
                                            <p class="text-[9px] text-slate-400 truncate">{{ $banner->feature_1_sub ?? '100% Genuine' }}</p>
                                        </div>
                                        <div class="truncate border-x border-slate-200 px-1">
                                            <p class="text-[10px] font-bold text-slate-800 truncate">{{ $banner->feature_2_title ?? 'Workshop' }}</p>
                                            <p class="text-[9px] text-slate-400 truncate">{{ $banner->feature_2_sub ?? 'Support Bays' }}</p>
                                        </div>
                                        <div class="truncate">
                                            <p class="text-[10px] font-bold text-slate-800 truncate">{{ $banner->feature_3_title ?? 'Offers' }}</p>
                                            <p class="text-[9px] text-slate-400 truncate">{{ $banner->feature_3_sub ?? 'Best Value' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions Footer --}}
                                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                                    <form action="{{ route('showrooms.manage.banners.toggle-active', [$showroom, $banner]) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold cursor-pointer transition {{ $banner->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200' }}">
                                            {{ $banner->is_active ? 'Disable Slide' : 'Activate Slide' }}
                                        </button>
                                    </form>

                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                @click="openEdit({{ json_encode($banner) }})"
                                                class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold transition cursor-pointer">
                                            Edit Slide
                                        </button>

                                        <form action="{{ route('showrooms.manage.banners.destroy', [$showroom, $banner]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this hero banner slide?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                    title="Delete Banner">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center text-slate-400 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="font-bold text-slate-700 text-sm">No hero banners created yet</p>
                            <p class="text-xs text-slate-400 mt-1">Click "Add New Banner" to create your first visual slide for your public storefront.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             BIG MODAL: CREATE BANNER (EXTRA WIDE & SPACIOUS VERTICAL COMFORT)
             ══════════════════════════════════════════════════════════════════ --}}
        <div x-show="showCreateModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5 lg:p-6">
            <div @click.away="showCreateModal = false"
                 class="w-full max-w-6xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all my-4 sm:my-6">
                <form action="{{ route('showrooms.manage.banners.store', $showroom) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                        <div>
                            <h3 class="text-xl font-black font-display text-slate-900 tracking-tight">Add New Hero Banner Slide</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Upload a high-resolution motorcycle image and configure headlines, badges, and trust pills.</p>
                        </div>
                        <button type="button" @click="showCreateModal = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-200/60 transition cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-5 sm:p-7 space-y-5 max-h-[85vh] overflow-y-auto">
                        {{-- Top Two-Column Grid: Image Upload & Preview on Left, Core Headlines & Badges on Right --}}
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                            {{-- Left 5 Cols: Banner Image File Upload & Live Preview Stage --}}
                            <div class="lg:col-span-5 p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3.5 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Hero Banner Image <span class="text-rose-500">*</span>
                                        </label>
                                        <span class="text-[10px] font-semibold text-slate-400">1920x1080 (16:9) • Max 10MB</span>
                                    </div>

                                    <template x-if="createImagePreview">
                                        <div class="w-full aspect-[16/8] rounded-xl overflow-hidden bg-slate-900 border border-slate-200 shadow-sm relative mb-3 group">
                                            <img :src="createImagePreview" alt="Upload Preview" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                            <div class="absolute top-2 right-2 flex items-center gap-1.5">
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-black/70 text-white backdrop-blur-sm">Selected Local Photo</span>
                                                <button type="button"
                                                        @click="clearCreateImage()"
                                                        class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-sm flex items-center gap-1 cursor-pointer transition active:scale-95"
                                                        title="Remove selected image">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    <span>Clear</span>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!createImagePreview">
                                        <div class="w-full aspect-[16/8] rounded-xl bg-slate-100 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 mb-3 p-3 text-center">
                                            <svg class="w-8 h-8 text-slate-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span class="text-[11px] font-medium">Select photo or paste image link below</span>
                                        </div>
                                    </template>

                                    <div class="flex items-center gap-2">
                                        <input type="file" name="image_file" accept="image/*"
                                               x-ref="createFileInput"
                                               @change="handleCreateFile($event)"
                                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer border border-slate-200 rounded-xl bg-white p-1 transition">
                                        <template x-if="createImagePreview">
                                            <button type="button"
                                                    @click="clearCreateImage()"
                                                    class="shrink-0 px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Clear Image</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div class="pt-2.5 border-t border-slate-200">
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Or Choose / Enter Public Image URL</label>
                                    <div class="relative">
                                        <input type="text" name="image_url" placeholder="/images/hero/hero_adventure_red.jpg or https://..."
                                               x-ref="createUrlInput"
                                               @input="if (!$event.target.value.startsWith('blob:')) createImagePreview = $event.target.value"
                                               class="w-full px-3 py-2 pr-8 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 font-mono">
                                    </div>
                                </div>
                            </div>

                            {{-- Right 7 Cols: Headlines, Badges & Subtitle --}}
                            <div class="lg:col-span-7 space-y-3.5">
                                {{-- Badge & Sort Order --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Top Banner Badge</label>
                                        <input type="text" name="badge" value="NEW COLLECTION 2026"
                                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono font-bold focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Slide Sort Order</label>
                                        <input type="number" name="sort_order" value="0" min="0"
                                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                    </div>
                                </div>

                                {{-- Main Headline in 3 parts --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Hero Headline (3 Interactive Parts)</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                        <div>
                                            <span class="text-[10px] text-slate-400 font-bold block mb-0.5">Prefix</span>
                                            <input type="text" name="title_prefix" placeholder="Fuel Your" value="Fuel Your"
                                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-red-600 font-bold block mb-0.5">Brand Highlight</span>
                                            <input type="text" name="title_highlight" placeholder="Next" value="Next"
                                                   class="w-full px-3 py-2 bg-red-50 border border-red-300 text-red-700 rounded-xl text-xs font-black focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 font-bold block mb-0.5">Suffix</span>
                                            <input type="text" name="title_suffix" placeholder="Adventure" value="Adventure"
                                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        </div>
                                    </div>
                                </div>

                                {{-- Subtitle --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Subtitle &amp; Commercial Pitch</label>
                                    <textarea name="subtitle" rows="2"
                                              class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 leading-relaxed">Explore the latest bikes with unmatched performance, style and technology.</textarea>
                                </div>

                                <div class="flex items-center gap-2.5 pt-1">
                                    <input type="checkbox" name="is_active" id="create_banner_active" value="1" checked
                                           class="w-4 h-4 text-red-600 border-slate-300 rounded focus:ring-red-500">
                                    <label for="create_banner_active" class="text-xs font-bold text-slate-700 select-none cursor-pointer">
                                        Active in Homepage Hero Slider
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider block">Primary Button (Main CTA)</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Button Label</label>
                                        <input type="text" name="btn_primary_text" value="Explore Bikes"
                                               class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Destination URL</label>
                                        <input type="text" name="btn_primary_url" placeholder="/bikes"
                                               class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                    </div>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider block">Secondary Button (Lead CTA)</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Button Label</label>
                                        <input type="text" name="btn_secondary_text" value="Book a Test Ride"
                                               class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Destination URL</label>
                                        <input type="text" name="btn_secondary_url" placeholder="/enquiries/create"
                                               class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 3 Trust Feature Highlights --}}
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">3 Trust Feature Badges</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="bg-white p-2.5 rounded-xl border border-slate-200">
                                    <input type="text" name="feature_1_title" value="Trusted Showroom" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold">
                                    <input type="text" name="feature_1_sub" value="100% Genuine Bikes" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] text-slate-500 mt-1">
                                </div>
                                <div class="bg-white p-2.5 rounded-xl border border-slate-200">
                                    <input type="text" name="feature_2_title" value="After Sales Support" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold">
                                    <input type="text" name="feature_2_sub" value="Always Here for You" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] text-slate-500 mt-1">
                                </div>
                                <div class="bg-white p-2.5 rounded-xl border border-slate-200">
                                    <input type="text" name="feature_3_title" value="Best Price Guarantee" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold">
                                    <input type="text" name="feature_3_sub" value="Unbeatable Offers" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] text-slate-500 mt-1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-100 transition cursor-pointer">Cancel</button>
                        <button type="submit" class="px-7 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black uppercase tracking-wider shadow-md transition cursor-pointer">Create Hero Banner</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             BIG MODAL: EDIT BANNER (EXTRA WIDE & SPACIOUS VERTICAL COMFORT)
             ══════════════════════════════════════════════════════════════════ --}}
        <div x-show="editingBanner !== null"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5 lg:p-6">
            <div @click.away="editingBanner = null"
                 class="w-full max-w-6xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all my-4 sm:my-6">
                <template x-if="editingBanner">
                    <form :action="'{{ url('showrooms/'.$showroom->slug.'/manage/banners') }}/' + editingBanner.id"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                            <div>
                                <h3 class="text-xl font-black font-display text-slate-900 tracking-tight">Edit Hero Banner Slide</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Update banner headlines, buttons, or upload a replacement photo.</p>
                            </div>
                            <button type="button" @click="editingBanner = null" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-200/60 transition cursor-pointer">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-5 sm:p-7 space-y-5 max-h-[85vh] overflow-y-auto">
                            {{-- Top Two-Column Grid: Image Preview & Replacement on Left, Headlines on Right --}}
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                                {{-- Left 5 Cols: Image Preview & Replacement --}}
                                <div class="lg:col-span-5 p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3.5 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Current Hero Banner Image</label>
                                            <span class="text-[10px] font-semibold text-slate-400">Live Aspect Preview</span>
                                        </div>
                                        <div class="w-full aspect-[16/8] rounded-xl bg-slate-900 overflow-hidden border border-slate-300 shadow-sm relative mb-3 group">
                                            <template x-if="editImagePreview">
                                                <img :src="editImagePreview" alt="Replacement Preview" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!editImagePreview">
                                                <img :src="editingBanner.image_path ? ('{{ Storage::disk('public')->url('') }}' + editingBanner.image_path) : (editingBanner.image_url || '/images/hero/hero_adventure_red.jpg')"
                                                     alt="" class="w-full h-full object-cover">
                                            </template>

                                            <template x-if="editImagePreview">
                                                <div class="absolute top-2 right-2 flex items-center gap-1.5">
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-black/70 text-white backdrop-blur-sm">Replacement Preview</span>
                                                    <button type="button"
                                                            @click="clearEditImage()"
                                                            class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-sm flex items-center gap-1 cursor-pointer transition active:scale-95"
                                                            title="Revert replacement file">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        <span>Clear</span>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>

                                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Upload Replacement File</label>
                                        <div class="flex items-center gap-2">
                                            <input type="file" name="image_file" accept="image/*"
                                                   x-ref="editFileInput"
                                                   @change="handleEditFile($event)"
                                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer border border-slate-200 rounded-xl bg-white p-1 transition">
                                            <template x-if="editImagePreview">
                                                <button type="button"
                                                        @click="clearEditImage()"
                                                        class="shrink-0 px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    <span>Clear</span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="pt-2.5 border-t border-slate-200">
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Or Image URL</label>
                                        <input type="text" name="image_url" x-model="editingBanner.image_url"
                                               x-ref="editUrlInput"
                                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                    </div>
                                </div>

                                {{-- Right 7 Cols: Headlines, Badges & Subtitle --}}
                                <div class="lg:col-span-7 space-y-3.5">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Top Banner Badge</label>
                                            <input type="text" name="badge" x-model="editingBanner.badge"
                                                   class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono font-bold focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Slide Sort Order</label>
                                            <input type="number" name="sort_order" x-model="editingBanner.sort_order" min="0"
                                                   class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Hero Headline (3 Interactive Parts)</label>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                            <div>
                                                <span class="text-[10px] text-slate-400 font-bold block mb-0.5">Prefix</span>
                                                <input type="text" name="title_prefix" x-model="editingBanner.title_prefix"
                                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                            </div>
                                            <div>
                                                <span class="text-[10px] text-red-600 font-bold block mb-0.5">Brand Highlight</span>
                                                <input type="text" name="title_highlight" x-model="editingBanner.title_highlight"
                                                       class="w-full px-3 py-2 bg-red-50 border border-red-300 text-red-700 rounded-xl text-xs font-black focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                            </div>
                                            <div>
                                                <span class="text-[10px] text-slate-400 font-bold block mb-0.5">Suffix</span>
                                                <input type="text" name="title_suffix" x-model="editingBanner.title_suffix"
                                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Subtitle &amp; Commercial Pitch</label>
                                        <textarea name="subtitle" x-model="editingBanner.subtitle" rows="2"
                                                  class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 leading-relaxed"></textarea>
                                    </div>

                                    <div class="flex items-center gap-2.5 pt-1">
                                        <input type="checkbox" name="is_active" id="edit_banner_active" value="1" :checked="editingBanner.is_active"
                                               class="w-4 h-4 text-red-600 border-slate-300 rounded focus:ring-red-500">
                                        <label for="edit_banner_active" class="text-xs font-bold text-slate-700 select-none cursor-pointer">
                                            Active in Homepage Hero Slider
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider block">Primary Button (Main CTA)</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Button Label</label>
                                            <input type="text" name="btn_primary_text" x-model="editingBanner.btn_primary_text"
                                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Destination URL</label>
                                            <input type="text" name="btn_primary_url" x-model="editingBanner.btn_primary_url"
                                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider block">Secondary Button (Lead CTA)</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Button Label</label>
                                            <input type="text" name="btn_secondary_text" x-model="editingBanner.btn_secondary_text"
                                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-semibold focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Destination URL</label>
                                            <input type="text" name="btn_secondary_url" x-model="editingBanner.btn_secondary_url"
                                                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 3 Trust Feature Highlights --}}
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">3 Trust Feature Badges</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="bg-white p-2.5 rounded-xl border border-slate-200">
                                        <input type="text" name="feature_1_title" x-model="editingBanner.feature_1_title" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold">
                                        <input type="text" name="feature_1_sub" x-model="editingBanner.feature_1_sub" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] text-slate-500 mt-1">
                                    </div>
                                    <div class="bg-white p-2.5 rounded-xl border border-slate-200">
                                        <input type="text" name="feature_2_title" x-model="editingBanner.feature_2_title" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold">
                                        <input type="text" name="feature_2_sub" x-model="editingBanner.feature_2_sub" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] text-slate-500 mt-1">
                                    </div>
                                    <div class="bg-white p-2.5 rounded-xl border border-slate-200">
                                        <input type="text" name="feature_3_title" x-model="editingBanner.feature_3_title" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold">
                                        <input type="text" name="feature_3_sub" x-model="editingBanner.feature_3_sub" class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] text-slate-500 mt-1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="button" @click="editingBanner = null" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-100 transition cursor-pointer">Cancel</button>
                            <button type="submit" class="px-7 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black uppercase tracking-wider shadow-md transition cursor-pointer">Save Changes</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>

    </div>
    @include('showrooms.partials.manage-nav-close')
</x-app-layout>
