@extends('layouts.app')

@section('title', 'Careers & Job Openings - ' . $showroom->name)

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
                    <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="text-slate-600 hover:text-slate-900 transition">Motorcycles</a>
                    <a href="{{ route('showrooms.services.create', $showroom) }}" class="text-slate-600 hover:text-slate-900 transition">Service & Repair</a>
                    <a href="{{ route('showrooms.careers.index', $showroom) }}" class="text-rose-600 font-bold transition">Careers</a>
                    <a href="{{ route('showrooms.show', $showroom) }}#contact" class="text-slate-600 hover:text-slate-900 transition">Contact</a>
                </div>

                <!-- Storefront Action Link -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('showrooms.show', $showroom) }}"
                       class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-semibold text-slate-700 transition">
                        Back to Showroom
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        <!-- Careers Hero Header -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-white via-slate-50 to-slate-100 border border-slate-200 p-8 sm:p-12 mb-10 shadow-sm">
            <div class="absolute -right-16 -bottom-16 w-80 h-80 rounded-full bg-rose-500/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-3">
                    <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-slate-900 transition">Home</a>
                    <span>/</span>
                    <span class="text-rose-600 font-bold">Careers</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-black font-display text-slate-900 tracking-tight leading-tight">
                    Build Your Career With <span class="text-rose-600">{{ $showroom->name }}</span>
                </h1>
                <p class="text-base sm:text-lg text-slate-600 mt-4 leading-relaxed">
                    Join a passionate crew of motorcycle enthusiasts, certified master technicians, and customer experience champions. Explore open vacancies below and shape the future of two-wheel retail.
                </p>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 mb-8 shadow-sm">
            <form method="GET" action="{{ route('showrooms.careers.index', $showroom) }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                
                <!-- Keyword Search -->
                <div class="sm:col-span-5 relative">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search position by role, skills, keywords..."
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 pl-10 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500 transition">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Department Filter -->
                <div class="sm:col-span-3">
                    <select name="department" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-3 text-sm text-slate-800 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500">
                        <option value="all">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->value }}" @selected($department === $dept->value)>{{ $dept->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Contract Type Filter -->
                <div class="sm:col-span-2">
                    <select name="type" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-3 text-sm text-slate-800 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500">
                        <option value="all">All Job Types</option>
                        @foreach($jobTypes as $jt)
                            <option value="{{ $jt->value }}" @selected($jobType === $jt->value)>{{ $jt->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider py-3.5 px-4 rounded-xl shadow-xs transition">
                        Filter
                    </button>
                    @if($search || ($department && $department !== 'all') || ($jobType && $jobType !== 'all'))
                        <a href="{{ route('showrooms.careers.index', $showroom) }}" class="p-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 rounded-xl transition" title="Clear filters">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Job Openings Grid -->
        @if($jobOpenings->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($jobOpenings as $job)
                    <div class="rounded-2xl bg-white border border-slate-200 p-6 flex flex-col justify-between hover:border-rose-400 hover:shadow-lg transition group shadow-xs">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs px-2.5 py-1 rounded-lg font-semibold {{ $job->department->badgeClasses() }}">
                                    {{ $job->department->label() }}
                                </span>
                                <span class="text-xs text-slate-500 font-medium">
                                    {{ $job->job_type->label() }}
                                </span>
                            </div>

                            <h3 class="text-xl font-bold font-display text-slate-900 group-hover:text-rose-600 transition mb-2">
                                {{ $job->title }}
                            </h3>

                            <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-500 mb-4">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    {{ $job->location ?: $showroom->city }}
                                </span>
                                @if($job->salary_range)
                                    <span class="flex items-center gap-1 text-emerald-600 font-semibold">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $job->salary_range }}
                                    </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ ucfirst($job->experience_level) }} Level
                                </span>
                            </div>

                            <p class="text-sm text-slate-600 line-clamp-3 mb-6 leading-relaxed">
                                {{ Str::limit(strip_tags($job->description), 140) }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            @if($job->deadline)
                                <span class="text-[11px] text-slate-500">
                                    Apply by <strong class="text-slate-800">{{ $job->deadline->format('M d, Y') }}</strong>
                                </span>
                            @else
                                <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Immediate Opening
                                </span>
                            @endif

                            <a href="{{ route('showrooms.careers.show', [$showroom, $job]) }}"
                               class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 group-hover:text-rose-700 hover:underline">
                                View Details & Apply
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $jobOpenings->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="rounded-3xl bg-white border border-slate-200 p-12 text-center max-w-xl mx-auto shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4 border border-rose-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-xl font-bold font-display text-slate-900 mb-2">No Open Vacancies Found</h3>
                <p class="text-sm text-slate-600 mb-6">
                    We currently don't have active job openings matching your search criteria. Check back soon or visit our dealership in {{ $showroom->city }}.
                </p>
                <a href="{{ route('showrooms.careers.index', $showroom) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-bold text-slate-700 transition">
                    Reset Filters
                </a>
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-8 mt-12 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} {{ $showroom->name }}. Powered by MotoShowroom SaaS.</p>
    </footer>

</div>
@endsection
