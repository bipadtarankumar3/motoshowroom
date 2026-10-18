<x-showroom-layout :showroom="$showroom">
    <!-- Showroom Public Header -->
    <header class="border-b border-slate-800 bg-slate-950/80 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-primary flex items-center justify-center font-black text-white text-lg font-display">
                    {{ strtoupper(substr($showroom->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-xl font-bold font-display text-white tracking-tight">{{ $showroom->name }}</h1>
                    <span class="text-xs text-slate-400">{{ $showroom->city ?? 'Authorized Dealership' }}</span>
                </div>
            </div>

            <div class="flex items-center gap-4 text-sm font-medium">
                <a href="#bikes" class="text-slate-300 hover:text-white transition">Bikes</a>
                <a href="#services" class="text-slate-300 hover:text-white transition">Services</a>
                <a href="#contact" class="text-slate-300 hover:text-white transition">Contact</a>

                @auth
                    @if (auth()->user()->canAccessTenant($showroom))
                        <a href="{{ route('showrooms.manage', $showroom) }}" class="px-4 py-2 rounded-xl bg-brand-primary text-white font-semibold text-xs transition shadow-lg">
                            Showroom Back-Office
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 text-xs font-semibold hover:text-white transition">
                        Staff Sign In
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Showroom Hero -->
    <section class="py-20 lg:py-28 relative overflow-hidden bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest bg-brand-primary/20 text-brand-primary border border-brand-primary/30 inline-block mb-6">
                Official Dealership Storefront
            </span>

            <h2 class="text-4xl sm:text-6xl font-black font-display text-white tracking-tight max-w-4xl mx-auto leading-tight">
                Welcome to <span class="text-brand-primary">{{ $showroom->name }}</span>
            </h2>

            <p class="mt-6 text-lg text-slate-300 max-w-2xl mx-auto">
                Explore the latest motorcycles, schedule interactive test rides, book servicing, and discover exclusive dealership offers.
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-4 text-sm text-slate-400">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>{{ $showroom->phone }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>{{ $showroom->email }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span>{{ $showroom->address_line1 }}, {{ $showroom->city }}</span>
                </div>
            </div>
        </div>
    </section>
</x-showroom-layout>
