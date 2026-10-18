@extends('layouts.app')

@section('title', 'Motorcycle Inventory - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    <!-- Showroom Branded Topbar / Header -->
    <header class="border-b border-slate-200 bg-white/90 backdrop-blur sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Showroom Logo & Identity -->
                <a href="{{ route('showrooms.show', $showroom) }}" class="flex items-center gap-3 group">
                    @if($showroom->logo_url)
                        <img src="{{ $showroom->logo_url }}" alt="{{ $showroom->name }}" class="h-10 w-auto rounded-xl object-contain bg-white border border-slate-200 p-1">
                    @else
                        <span class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-white text-base shadow-md group-hover:scale-105 transition-transform"
                              style="background: linear-gradient(135deg, {{ $showroom->brand_color_primary ?? '#dc2626' }}, {{ $showroom->brand_color_accent ?? '#f43f5e' }})">
                            {{ strtoupper(substr($showroom->name, 0, 1)) }}
                        </span>
                    @endif
                    <div>
                        <span class="font-display font-black text-lg sm:text-xl tracking-tight text-slate-900 block leading-none">{{ $showroom->name }}</span>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest block mt-0.5">{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-6 text-sm font-semibold">
                    <a href="{{ route('showrooms.show', $showroom) }}" class="text-slate-600 hover:text-slate-900 transition">Home</a>
                    <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="text-rose-600 font-bold transition">Motorcycles</a>
                    <a href="{{ route('showrooms.show', $showroom) }}#about" class="text-slate-600 hover:text-slate-900 transition">About Us</a>
                    <a href="{{ route('showrooms.show', $showroom) }}#contact" class="text-slate-600 hover:text-slate-900 transition">Contact</a>
                </div>

                <!-- Contact & WhatsApp Hotline -->
                <div class="flex items-center gap-3">
                    @if($showroom->getSetting('whatsapp_number'))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $showroom->getSetting('whatsapp_number')) }}"
                           target="_blank"
                           class="flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-xs font-bold transition shadow-xs">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            <span class="hidden sm:inline">WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        <!-- Catalog Header Banner -->
        <div class="mb-8 sm:mb-12">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-2">
                <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-slate-900 transition">Home</a>
                <span>/</span>
                <span class="text-rose-600 font-bold">Inventory</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-black font-display text-slate-900 tracking-tight">Motorcycle Collection</h1>
            <p class="text-sm sm:text-base text-slate-600 mt-2 max-w-2xl">
                Browse our lineup of road, track, and touring machines. Every motorcycle is certified, inspected, and ready for the open road.
            </p>
        </div>

        <!-- Filter & Search Controls -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200 mb-8 shadow-sm">
            <form method="GET" action="{{ route('showrooms.bikes.index', $showroom) }}" class="space-y-4">
                <!-- Top Row: Search & Sort -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Search by bike model or keyword..."
                               class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white">
                    </div>

                    <!-- Brand Filter -->
                    <div class="sm:col-span-3">
                        <select name="brand" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-rose-500 focus:bg-white">
                            <option value="">All Brands</option>
                            @foreach($brands as $b)
                                <option value="{{ $b }}" {{ $brand === $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div class="sm:col-span-3">
                        <select name="sort" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-rose-500 focus:bg-white">
                            <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Latest Arrivals</option>
                            <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="year_desc" {{ $sort === 'year_desc' ? 'selected' : '' }}>Model Year (Newest)</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="sm:col-span-1">
                        <button type="submit" class="w-full h-full py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-xs flex items-center justify-center">
                            Filter
                        </button>
                    </div>
                </div>

                <!-- Bottom Row: Category Filter Pills -->
                <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 mr-2 uppercase tracking-wider text-[10px]">Category:</span>
                    <a href="{{ route('showrooms.bikes.index', array_merge(['showroom' => $showroom], request()->except(['category', 'page']))) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ empty($category) ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        All
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('showrooms.bikes.index', array_merge(['showroom' => $showroom], request()->except('page'), ['category' => $cat->value])) }}"
                           class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ $category === $cat->value ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            {{ $cat->label() }}
                        </a>
                    @endforeach
                </div>
            </form>
        </div>

        <!-- Motorcycles Grid -->
        @if($bikes->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 my-8 shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto text-slate-400 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">No motorcycles found</h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mb-6">We currently do not have any motorcycles matching your search criteria. Try modifying your filters.</p>
                <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-semibold text-slate-700 transition">
                    <span>Clear Filters</span>
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($bikes as $bike)
                    <div class="group bg-white rounded-3xl border border-slate-200 hover:border-rose-400 overflow-hidden flex flex-col transition-all duration-300 hover:shadow-xl">

                        <!-- Photo & Badges Container -->
                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                            <img src="{{ $bike->primary_image_url }}" alt="{{ $bike->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                            <!-- Top Overlay Badges -->
                            <div class="absolute top-3 inset-x-3 flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $bike->category->badgeClasses() }} shadow-xs">
                                    {{ $bike->category->label() }}
                                </span>

                                <div class="flex items-center gap-1.5">
                                    @if($bike->is_featured)
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-400 text-amber-950 shadow-xs">
                                            Featured
                                        </span>
                                    @endif
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold {{ $bike->status->badgeClasses() }} shadow-xs">
                                        {{ $bike->status->label() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Model Year Pill -->
                            <div class="absolute bottom-3 left-3">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white/95 backdrop-blur text-slate-700 border border-slate-200 shadow-xs">
                                    {{ $bike->brand }} • {{ $bike->model_year }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="font-display font-bold text-lg sm:text-xl text-slate-900 group-hover:text-rose-600 transition leading-snug">
                                    <a href="{{ route('showrooms.bikes.show', [$showroom, $bike]) }}">
                                        {{ $bike->name }}
                                    </a>
                                </h3>

                                <!-- Technical Specification Pills Strip -->
                                <div class="grid grid-cols-3 gap-2 my-4 pt-3 border-t border-slate-100 text-center">
                                    <div class="bg-slate-50 p-2 rounded-xl border border-slate-200">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-500 font-bold block">Engine</span>
                                        <span class="text-xs font-bold text-slate-800 mt-0.5 block">
                                            {{ $bike->engine_cc ? $bike->engine_cc . ' cc' : ($bike->fuel_type === 'electric' ? 'EV' : 'N/A') }}
                                        </span>
                                    </div>
                                    <div class="bg-slate-50 p-2 rounded-xl border border-slate-200">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-500 font-bold block">Power</span>
                                        <span class="text-xs font-bold text-slate-800 mt-0.5 block">
                                            {{ $bike->power_bhp ? $bike->power_bhp . ' bhp' : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="bg-slate-50 p-2 rounded-xl border border-slate-200">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-500 font-bold block">Mileage</span>
                                        <span class="text-xs font-bold text-slate-800 mt-0.5 block">
                                            {{ $bike->mileage_kmpl ? $bike->mileage_kmpl . ' km/l' : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Price & Call to Action -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block">Price</span>
                                    <span class="font-mono font-black text-xl text-slate-900">{{ $bike->formatted_price }}</span>
                                </div>

                                <a href="{{ route('showrooms.bikes.show', [$showroom, $bike]) }}"
                                   class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-600 hover:text-white text-slate-800 font-bold text-xs transition border border-slate-200 flex items-center gap-1.5 shadow-xs">
                                    <span>Details</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($bikes->hasPages())
                <div class="mt-12">
                    {{ $bikes->links() }}
                </div>
            @endif
        @endif
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-8 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            <p>© {{ date('Y') }} {{ $showroom->name }}. All rights reserved.</p>
        </div>
    </footer>
</div>
@endsection
