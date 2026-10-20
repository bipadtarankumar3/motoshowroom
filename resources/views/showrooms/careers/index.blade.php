@extends('layouts.app')

@section('title', 'Careers & Job Openings - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    @include('showrooms.partials.header')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-10 xl:px-16 py-8 sm:py-12 space-y-10">

        <!-- Modern Full-Width Careers Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-white via-slate-50 to-rose-50/30 border border-slate-200 p-8 sm:p-12 lg:p-16 shadow-xs">
            <div class="absolute -right-20 -bottom-20 w-96 h-96 rounded-full bg-rose-500/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="max-w-3xl space-y-4">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-slate-900 transition">Home</a>
                        <span>/</span>
                        <span class="font-bold" style="color: {{ $showroom->primary_color }};">Careers</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black font-display text-slate-900 tracking-tight leading-tight">
                        Build Your Career With <span style="color: {{ $showroom->primary_color }};">{{ $showroom->name }}</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl">
                        Join an elite crew of motorcycle enthusiasts, certified master technicians, and customer experience champions. Apply to active openings or pitch yourself directly with our 1-minute video interview.
                    </p>

                    <!-- Feature Badges -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full badge-brand-primary text-xs font-bold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            1-Min Video Pitch
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Authorized Dealership
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold">
                            <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Performance Incentives
                        </span>
                    </div>
                </div>

                <!-- Action Card in Hero -->
                <div class="lg:w-96 shrink-0 bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider block mb-1" style="color: {{ $showroom->primary_color }};">Fast-Track Application</span>
                    <h3 class="text-lg font-bold font-display text-slate-900 mb-2">No Open Opening Matches?</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-5">
                        Submit an open application with a 60-second video pitch. Our hiring directors review spontaneous pitches weekly.
                    </p>
                    <a href="{{ route('showrooms.careers.general-apply', $showroom) }}"
                       class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-5 rounded-2xl btn-brand-primary font-bold text-xs uppercase tracking-wider shadow-md transition hover:scale-[1.02] active:scale-[0.98]"
                       style="box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Record 1-Min Video Pitch</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar (Modern Full Width) -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 shadow-xs">
            <form method="GET" action="{{ route('showrooms.careers.index', $showroom) }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                
                <!-- Keyword Search -->
                <div class="sm:col-span-5 relative">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search position by role, skills, keywords..."
                           class="w-full bg-slate-50 border border-slate-300 rounded-2xl px-4 py-3 pl-11 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20 transition">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Department Filter -->
                <div class="sm:col-span-3">
                    <select name="department" class="w-full bg-slate-50 border border-slate-300 rounded-2xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20">
                        <option value="all">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->value }}" @selected($department === $dept->value)>{{ $dept->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Contract Type Filter -->
                <div class="sm:col-span-2">
                    <select name="type" class="w-full bg-slate-50 border border-slate-300 rounded-2xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20">
                        <option value="all">All Job Types</option>
                        @foreach($jobTypes as $jt)
                            <option value="{{ $jt->value }}" @selected($jobType === $jt->value)>{{ $jt->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit"
                            class="w-full btn-brand-primary font-bold text-xs uppercase tracking-wider py-3.5 px-4 rounded-2xl shadow-xs transition"
                            style="box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;">
                        Filter
                    </button>
                    @if($search || ($department && $department !== 'all') || ($jobType && $jobType !== 'all'))
                        <a href="{{ route('showrooms.careers.index', $showroom) }}" class="p-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 rounded-2xl transition" title="Clear filters">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Job Openings Grid (Modern Full-Width Responsive Layout) -->
        @if($jobOpenings->count() > 0)
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-black font-display text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Current Open Vacancies</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-xs font-bold">{{ $jobOpenings->total() }}</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                    @foreach($jobOpenings as $job)
                        <div class="rounded-3xl bg-white border border-slate-200 p-6 flex flex-col justify-between hover:border-slate-400 hover:shadow-xl transition-all duration-200 group shadow-xs">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="text-xs px-2.5 py-1 rounded-lg font-bold {{ $job->department->badgeClasses() }}">
                                        {{ $job->department->label() }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium">
                                        {{ $job->job_type->label() }}
                                    </span>
                                </div>

                                <h3 class="text-xl font-bold font-display text-slate-900 transition mb-3 line-clamp-2">
                                    <a href="{{ route('showrooms.careers.show', [$showroom, $job]) }}">
                                        {{ $job->title }}
                                    </a>
                                </h3>

                                <div class="flex flex-wrap items-center gap-y-1.5 gap-x-3 text-xs text-slate-500 mb-4">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        {{ $job->location ?: $showroom->city }}
                                    </span>
                                    @if($job->salary_range)
                                        <span class="flex items-center gap-1 text-emerald-600 font-bold">
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $job->salary_range }}
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        {{ ucfirst($job->experience_level) }}
                                    </span>
                                </div>

                                <p class="text-xs text-slate-600 line-clamp-3 mb-6 leading-relaxed">
                                    {{ Str::limit(strip_tags($job->description), 140) }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                @if($job->deadline)
                                    <span class="text-[11px] text-slate-500">
                                        Apply by <strong class="text-slate-800">{{ $job->deadline->format('M d, Y') }}</strong>
                                    </span>
                                @else
                                    <span class="text-[11px] text-emerald-600 font-bold flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Immediate Opening
                                    </span>
                                @endif

                                <a href="{{ route('showrooms.careers.show', [$showroom, $job]) }}"
                                   class="inline-flex items-center gap-1 text-xs font-bold hover:underline"
                                   style="color: {{ $showroom->primary_color }};">
                                    <span>Details & Pitch</span>
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
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('showrooms.careers.general-apply', $showroom) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Send 1-Min Video Pitch & Open Application</span>
                    </a>
                    <a href="{{ route('showrooms.careers.index', $showroom) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-bold text-slate-700 transition">
                        Reset Filters
                    </a>
                </div>
            </div>
        @endif

    </main>

    @include('showrooms.partials.footer')
</div>
@endsection
