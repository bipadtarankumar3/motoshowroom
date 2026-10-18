@extends('layouts.app')

@section('title', 'Motorcycle Inventory - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Flash Feedback Messages -->
        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}" class="text-xs font-bold uppercase tracking-wider text-rose-600 underline hover:text-rose-700">Upgrade Plan</a>
            </div>
        @endif

        <!-- Inventory Header & Metrics -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-700 font-semibold">Inventory</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Motorcycle Inventory</h1>
                <p class="text-sm text-slate-500 mt-1">Catalog, specifications, pricing, media galleries, and stock availability.</p>
            </div>

            <div class="flex items-center gap-3">
                @if($hasReachedLimit)
                    <button disabled class="opacity-50 cursor-not-allowed px-4 py-2.5 rounded-xl bg-slate-200 border border-slate-300 text-slate-500 font-bold text-xs flex items-center gap-2" title="Plan limit reached">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Limit Reached</span>
                    </button>
                    <a href="{{ route('showrooms.billing.index', $showroom) }}" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition flex items-center gap-2">
                        <span>Upgrade Plan</span>
                    </a>
                @else
                    <a href="{{ route('showrooms.manage.bikes.create', $showroom) }}"
                       class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition flex items-center gap-2 group" id="btn-add-bike">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Add Motorcycle</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Quota & Quick Metrics Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Total Listings</span>
                <p class="text-2xl font-black font-display text-slate-900 mt-1">{{ $totalBikes }}</p>
                <span class="text-[10px] text-slate-500">Across all categories</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-emerald-600 tracking-wider">In Stock</span>
                <p class="text-2xl font-black font-display text-emerald-600 mt-1">{{ $inStockBikes }}</p>
                <span class="text-[10px] text-slate-500">Ready for instant delivery</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-amber-600 tracking-wider">Featured</span>
                <p class="text-2xl font-black font-display text-amber-600 mt-1">{{ $featuredBikes }}</p>
                <span class="text-[10px] text-slate-500">Highlighted on homepage</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-rose-600 tracking-wider">Plan Quota</span>
                    <span class="text-[10px] font-mono text-slate-500">{{ $totalBikes }} / {{ $maxLimit ?? '∞' }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 mt-3 overflow-hidden border border-slate-200">
                    <div class="bg-gradient-to-r from-rose-600 to-rose-400 h-2 rounded-full transition-all" style="width: {{ $quotaUsedPercent }}%"></div>
                </div>
                <span class="text-[10px] text-slate-500 mt-1 block">{{ $quotaUsedPercent }}% capacity utilized</span>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs mb-6">
            <form method="GET" action="{{ route('showrooms.manage.bikes.index', $showroom) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Query -->
                <div>
                    <label for="search" class="sr-only">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ $search }}"
                               placeholder="Search bike, SKU, brand..."
                               class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="sr-only">Category</label>
                    <select name="category" id="category"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->value }}" {{ $category === $cat->value ? 'selected' : '' }}>{{ $cat->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="sr-only">Status</label>
                    <select name="status" id="status"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        <option value="">All Availability Statuses</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ $status === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-xs font-semibold text-white transition text-center shadow-xs">
                        Filter
                    </button>
                    @if($search || $category || $status)
                        <a href="{{ route('showrooms.manage.bikes.index', $showroom) }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Inventory List / Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            @if($bikes->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto text-slate-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">No motorcycles found</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">
                        @if($search || $category || $status)
                            No motorcycles match your filter criteria. Try adjusting or clearing your filters.
                        @else
                            Start listing motorcycles in your showroom catalog to showcase on your branded website.
                        @endif
                    </p>
                    @if(! $hasReachedLimit)
                        <a href="{{ route('showrooms.manage.bikes.create', $showroom) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            <span>Add First Motorcycle</span>
                        </a>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
                            <tr>
                                <th class="py-3 px-4">Motorcycle</th>
                                <th class="py-3 px-4">Specs</th>
                                <th class="py-3 px-4">Category</th>
                                <th class="py-3 px-4">Price</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-center">Featured</th>
                                <th class="py-3 px-4 text-center">Storefront</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($bikes as $bike)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <!-- Motorcycle Info & Thumbnail -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-14 h-11 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0 relative">
                                                <img src="{{ $bike->primary_image_url }}" alt="{{ $bike->name }}" class="w-full h-full object-cover">
                                                @if($bike->is_featured)
                                                    <span class="absolute top-0.5 right-0.5 w-2 h-2 rounded-full bg-amber-500" title="Featured"></span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-1.5">
                                                    <a href="{{ route('showrooms.manage.bikes.edit', [$showroom, $bike]) }}" class="font-bold text-slate-900 hover:text-rose-600 transition block text-sm leading-snug">
                                                        {{ $bike->name }}
                                                    </a>
                                                    @if($bike->has_360_view)
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-cyan-50 text-cyan-700 border border-cyan-200">
                                                            360°
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 text-[10px] text-slate-500 mt-0.5">
                                                    <span class="font-mono text-slate-700 font-bold">{{ $bike->sku }}</span>
                                                    <span>•</span>
                                                    <span>{{ $bike->brand }} ({{ $bike->model_year }})</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Specs Summary -->
                                    <td class="py-3.5 px-4 text-slate-600">
                                        <div class="flex flex-col gap-0.5 text-[11px]">
                                            @if($bike->engine_cc)
                                                <span>{{ $bike->engine_cc }} cc @if($bike->power_bhp) • {{ $bike->power_bhp }} bhp @endif</span>
                                            @elseif($bike->fuel_type === 'electric')
                                                <span class="text-teal-700 font-semibold">Electric EV</span>
                                            @endif
                                            @if($bike->mileage_kmpl)
                                                <span class="text-slate-500 text-[10px]">{{ $bike->mileage_kmpl }} km/l</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Category Badge -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold {{ $bike->category->badgeClasses() }}">
                                            {{ $bike->category->label() }}
                                        </span>
                                    </td>

                                    <!-- Price -->
                                    <td class="py-3.5 px-4">
                                        <span class="font-mono font-bold text-slate-900 text-sm">{{ $bike->formatted_price }}</span>
                                    </td>

                                    <!-- Stock Status -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold {{ $bike->status->badgeClasses() }}">
                                            {{ $bike->status->label() }}
                                        </span>
                                    </td>

                                    <!-- Featured Toggle Button -->
                                    <td class="py-3.5 px-4 text-center">
                                        <form method="POST" action="{{ route('showrooms.manage.bikes.toggle-featured', [$showroom, $bike]) }}">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg transition {{ $bike->is_featured ? 'text-amber-500 bg-amber-50 hover:bg-amber-100 border border-amber-200' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-100' }}" title="{{ $bike->is_featured ? 'Unfeature' : 'Feature on homepage' }}">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Published Toggle Button -->
                                    <td class="py-3.5 px-4 text-center">
                                        <form method="POST" action="{{ route('showrooms.manage.bikes.toggle-published', [$showroom, $bike]) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold transition {{ $bike->is_published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200' }}">
                                                {{ $bike->is_published ? 'Published' : 'Hidden' }}
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Action Menu -->
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($bike->is_published)
                                                <a href="{{ route('showrooms.bikes.show', [$showroom, $bike]) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition" title="Preview Public Page">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            @endif

                                            <a href="{{ route('showrooms.manage.bikes.360', [$showroom, $bike]) }}" class="p-1.5 {{ $bike->has_360_view ? 'text-cyan-700 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200' : 'text-slate-500 hover:text-cyan-700 hover:bg-slate-100' }} rounded-lg transition" title="Interactive 360° Studio">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            </a>

                                            <a href="{{ route('showrooms.manage.bikes.edit', [$showroom, $bike]) }}" class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition" title="Edit Motorcycle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>

                                            <form method="POST" action="{{ route('showrooms.manage.bikes.destroy', [$showroom, $bike]) }}" onsubmit="return confirm('Are you sure you want to delete {{ addslashes($bike->name) }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete Motorcycle">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($bikes->hasPages())
                    <div class="p-4 border-t border-slate-200 bg-white">
                        {{ $bikes->links() }}
                    </div>
                @endif
            @endif
    </main>
    @include('showrooms.partials.manage-nav-close')
</div>
@endsection
