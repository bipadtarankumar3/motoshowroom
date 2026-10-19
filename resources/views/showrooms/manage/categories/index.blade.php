@extends('layouts.app')

@section('title', 'Category Master — ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans"
     x-data="{
         showCreateModal: false,
         editingCategory: null,
         searchQuery: '',
         createPreviewUrl: '',
         editPreviewUrl: '',
         openEdit(cat) {
             this.editingCategory = Object.assign({}, cat);
             this.editPreviewUrl = cat.display_image_url;
         },
         matchesSearch(name, slug, subtitle) {
             if (!this.searchQuery) return true;
             const q = this.searchQuery.toLowerCase();
             return (name && name.toLowerCase().includes(q)) ||
                    (slug && slug.toLowerCase().includes(q)) ||
                    (subtitle && subtitle.toLowerCase().includes(q));
         }
     }">

    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 space-y-6">

        {{-- ═══ TOP BREADCRUMB & HEADER BAR ═══ --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Showroom Back-Office</a>
                    <span>/</span>
                    <span class="text-rose-600 font-bold">Category Catalog</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight flex items-center gap-3">
                    <span>Motorcycle Category Master</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-slate-200 text-slate-800 border border-slate-300">
                        {{ $categories->count() }}
                    </span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Configure classifications, banner photography, and sort orders displayed across your storefront category carousel and catalog filters.
                </p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('showrooms.show', $showroom) }}#categories"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:border-slate-300 text-slate-700 text-xs font-bold shadow-2xs transition hover:bg-slate-50 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>View Storefront Strip</span>
                </a>

                <button type="button"
                        @click="showCreateModal = true; createPreviewUrl = ''"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black uppercase tracking-wider shadow-md transition cursor-pointer hover:scale-[1.01] active:scale-[0.99]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Add New Category</span>
                </button>
            </div>
        </div>

        {{-- ═══ FLASH MESSAGES ═══ --}}
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 text-xs sm:text-sm shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm shadow-xs space-y-1">
                <div class="font-bold flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>Please review the form errors:</span>
                </div>
                <ul class="list-disc list-inside text-xs pl-6">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ═══ OVERVIEW METRICS STRIP (FULL WIDTH) ═══ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Categories</p>
                    <p class="text-3xl font-black font-display text-slate-900 mt-1">{{ $categories->count() }}</p>
                    <span class="text-[11px] text-slate-500">Active catalog classifications</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-700 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active on Storefront</p>
                    <p class="text-3xl font-black font-display text-emerald-600 mt-1">{{ $categories->where('is_active', true)->count() }}</p>
                    <span class="text-[11px] text-slate-500">Visible in homepage strip</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bikes Assigned</p>
                    <p class="text-3xl font-black font-display text-slate-900 mt-1">{{ $categories->sum('bikes_count') }}</p>
                    <span class="text-[11px] text-slate-500">Total linked motorcycles</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
        </div>

        {{-- ═══ FULL WIDTH CATEGORY CATALOG TABLE CONTAINER ═══ --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">

            {{-- Table Filter & Search Header --}}
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-0.5">
                    <h2 class="text-base font-black text-slate-900">All Showroom Categories</h2>
                    <p class="text-xs text-slate-500">Click actions to update images, adjust homepage visibility, or re-order catalog positioning.</p>
                </div>

                {{-- Live Search Filter Input --}}
                <div class="w-full md:w-80 relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="Filter by name, slug, tag..."
                           class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition">
                </div>
            </div>

            {{-- Categories Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 min-w-[850px]">
                    <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-400 uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-4 px-6 w-28">Banner</th>
                            <th class="py-4 px-6">Category Details</th>
                            <th class="py-4 px-6">Slug Identifier</th>
                            <th class="py-4 px-6 text-center">Assigned Models</th>
                            <th class="py-4 px-6 text-center">Storefront Status</th>
                            <th class="py-4 px-6 text-center">Sort</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-slate-50/70 transition-colors"
                                x-show="matchesSearch('{{ addslashes($cat->name) }}', '{{ addslashes($cat->slug) }}', '{{ addslashes($cat->subtitle ?? '') }}')">

                                {{-- Image Thumbnail --}}
                                <td class="py-4 px-6">
                                    <div class="w-20 h-14 rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden relative shadow-2xs group flex items-center justify-center">
                                        <img src="{{ $cat->display_image_url }}"
                                             alt="{{ $cat->name }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                </td>

                                {{-- Name & Subtitle --}}
                                <td class="py-4 px-6">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900 text-sm">{{ $cat->name }}</span>
                                            @if($cat->image_path)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    Custom Image
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 line-clamp-1">
                                            {{ $cat->subtitle ?: ($cat->description ?: 'No subtitle or description configured') }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Slug --}}
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 font-mono text-[11px] font-semibold">
                                        {{ $cat->slug }}
                                    </span>
                                </td>

                                {{-- Bikes Count Link --}}
                                <td class="py-4 px-6 text-center">
                                    <a href="{{ route('showrooms.bikes.index', [$showroom, 'category' => $cat->slug]) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 transition">
                                        <span>{{ $cat->bikes_count }}</span>
                                        <span class="text-slate-400 font-normal">bikes</span>
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </td>

                                {{-- Homepage Active Status --}}
                                <td class="py-4 px-6 text-center">
                                    <form action="{{ route('showrooms.manage.categories.toggle-active', [$showroom, $cat]) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold cursor-pointer transition {{ $cat->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $cat->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            <span>{{ $cat->is_active ? 'Active on Strip' : 'Hidden' }}</span>
                                        </button>
                                    </form>
                                </td>

                                {{-- Sort Order --}}
                                <td class="py-4 px-6 text-center font-mono font-bold text-slate-500">
                                    #{{ $cat->sort_order }}
                                </td>

                                {{-- Actions --}}
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                                @click="openEdit({{ json_encode([
                                                    'id' => $cat->id,
                                                    'name' => $cat->name,
                                                    'slug' => $cat->slug,
                                                    'subtitle' => $cat->subtitle,
                                                    'description' => $cat->description,
                                                    'image_url' => $cat->image_url,
                                                    'display_image_url' => $cat->display_image_url,
                                                    'is_active' => $cat->is_active,
                                                    'sort_order' => $cat->sort_order,
                                                ]) }})"
                                                class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition cursor-pointer"
                                                title="Edit Category Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>

                                        <form action="{{ route('showrooms.manage.categories.destroy', [$showroom, $cat]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete category \'{{ $cat->name }}\'? Any assigned motorcycles will have their category unlinked.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-100 transition cursor-pointer"
                                                    title="Delete Category">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="max-w-sm mx-auto space-y-3">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        </div>
                                        <p class="text-sm font-bold text-slate-900">No categories created yet</p>
                                        <p class="text-xs text-slate-500">Categories help organize your motorcycles into Sports, Cruiser, Adventure, or Electric strips on your storefront.</p>
                                        <button type="button"
                                                @click="showCreateModal = true"
                                                class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs uppercase tracking-wider">
                                            Create First Category
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Footer Summary --}}
            <div class="p-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Showing {{ $categories->count() }} configured motorcycle categories</span>
                <span>Drag sort or update sort order values to re-arrange position</span>
            </div>
        </div>

    </main>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: CREATE CATEGORY
         ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="showCreateModal"
         x-cloak
         class="fixed inset-0 z-70 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="z-index: 70;">
        <div @click.away="showCreateModal = false"
             class="w-full max-w-xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-8">
            <form action="{{ route('showrooms.manage.categories.store', $showroom) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black font-display text-slate-900">Add New Category</h3>
                        <p class="text-xs text-slate-500">Create a motorcycle classification and assign catalog banner.</p>
                    </div>
                    <button type="button" @click="showCreateModal = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. Sports Bikes, Dual Sport, Naked"
                               class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400 font-semibold">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">URL Slug <span class="text-slate-400 font-normal">(Auto-generated if blank)</span></label>
                            <input type="text" name="slug" placeholder="e.g. sports-bikes"
                                   class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ $categories->count() + 1 }}" min="0"
                                   class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Subtitle / Catchphrase</label>
                        <input type="text" name="subtitle" placeholder="e.g. Track & Superbikes, Dual Sport & Off-Road"
                               class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400">
                    </div>

                    {{-- Image File Upload --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Category Image File Upload</label>
                        <input type="file" name="image_file" accept="image/*"
                               class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                        <p class="text-[11px] text-slate-400">Accepted formats: JPG, PNG, WEBP, SVG (Max 5MB). File upload takes priority over external URL.</p>

                        <div class="pt-2 border-t border-slate-200">
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Or External Image URL</label>
                            <input type="url" name="image_url" placeholder="https://images.unsplash.com/..."
                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description <span class="text-slate-400 font-normal">(Optional)</span></label>
                        <textarea name="description" rows="2" placeholder="Brief info about this motorcycle category..."
                                  class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400"></textarea>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <input type="checkbox" name="is_active" id="create_active" value="1" checked
                               class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 cursor-pointer">
                        <label for="create_active" class="text-xs font-bold text-slate-700 select-none cursor-pointer">
                            Show this category on storefront homepage strip
                        </label>
                    </div>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-100 cursor-pointer">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black uppercase tracking-wider shadow-md cursor-pointer">Create Category</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: EDIT CATEGORY
         ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="editingCategory !== null"
         x-cloak
         class="fixed inset-0 z-70 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="z-index: 70;">
        <div @click.away="editingCategory = null"
             class="w-full max-w-xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-8">
            <template x-if="editingCategory">
                <form :action="'{{ url('showrooms/'.$showroom->slug.'/manage/categories') }}/' + editingCategory.id"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-black font-display text-slate-900">Edit Category</h3>
                            <p class="text-xs text-slate-500">Update classification title, photo banner, and sort rank.</p>
                        </div>
                        <button type="button" @click="editingCategory = null" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" x-model="editingCategory.name" required
                                   class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400 font-semibold">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">URL Slug</label>
                                <input type="text" name="slug" x-model="editingCategory.slug" required
                                       class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sort Order</label>
                                <input type="number" name="sort_order" x-model="editingCategory.sort_order" min="0"
                                       class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Subtitle / Catchphrase</label>
                            <input type="text" name="subtitle" x-model="editingCategory.subtitle"
                                   class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400">
                        </div>

                        {{-- Current Image Preview & File Upload --}}
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Current Category Image Preview</label>
                            <div class="w-full h-36 rounded-2xl bg-slate-200 overflow-hidden border border-slate-300 relative shadow-2xs">
                                <img :src="editingCategory.display_image_url" alt="" class="w-full h-full object-cover">
                            </div>

                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider pt-2">Upload Replacement Image File</label>
                            <input type="file" name="image_file" accept="image/*"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                            <p class="text-[11px] text-slate-400">Uploading a file will replace the current image. Max 5MB.</p>

                            <div class="pt-2 border-t border-slate-200">
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Or External Image URL</label>
                                <input type="url" name="image_url" x-model="editingCategory.image_url"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description</label>
                            <textarea name="description" x-model="editingCategory.description" rows="2"
                                      class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400"></textarea>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input type="checkbox" name="is_active" id="edit_active" value="1" :checked="editingCategory.is_active"
                                   class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 cursor-pointer">
                            <label for="edit_active" class="text-xs font-bold text-slate-700 select-none cursor-pointer">
                                Active on Storefront Homepage
                            </label>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="editingCategory = null" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-100 cursor-pointer">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black uppercase tracking-wider shadow-md cursor-pointer">Save Changes</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
