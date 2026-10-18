@extends('layouts.app')

@section('title', 'Motorcycle Inventory - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    @include('showrooms.partials.header')

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

    @include('showrooms.partials.footer')
</div>
@endsection
