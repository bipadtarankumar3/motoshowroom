@extends('layouts.app')

@section('title', 'Careers & Job Openings - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Flash Feedback Notifications -->
        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-950/80 border border-emerald-500/40 p-4 text-emerald-300 flex items-center justify-between shadow-lg shadow-emerald-950/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl bg-rose-950/80 border border-rose-500/40 p-4 text-rose-300 flex items-center justify-between shadow-lg shadow-rose-950/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 flex items-center justify-center text-rose-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}" class="text-xs font-bold uppercase tracking-wider text-rose-400 underline hover:text-white">Upgrade Plan</a>
            </div>
        @endif

        @if (! $hasModuleAccess)
            <div class="mb-8 rounded-3xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/30 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">Careers & Job Openings Module is Locked</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Your current subscription tier does not include dealership talent recruitment and job posting. Upgrade to publish vacancies and receive candidate applications.</p>
                    </div>
                </div>
                <a href="{{ route('showrooms.billing.index', $showroom) }}"
                   class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 transition whitespace-nowrap">
                    Upgrade Subscription
                </a>
            </div>
        @endif

        <!-- Careers Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-white transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-200">Careers & ATS</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">Dealership Talent Recruitment</h1>
                <p class="text-sm text-slate-400 mt-1">Manage job vacancies, track candidate applications, and schedule interview rounds.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.manage.applicants.index', $showroom) }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-white font-bold text-xs shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>ATS Candidates ({{ $totalApplications }})</span>
                </a>

                @if ($hasModuleAccess)
                    <a href="{{ route('showrooms.manage.careers.create', $showroom) }}"
                       id="btn-add-opening"
                       class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs shadow-lg shadow-rose-600/30 transition flex items-center gap-2 group">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Post Vacancy</span>
                    </a>
                @else
                    <button disabled class="opacity-50 cursor-not-allowed px-5 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-slate-400 font-bold text-xs flex items-center gap-2" title="Plan upgrade required">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Post Vacancy</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg">
                <span class="text-xs text-slate-400 font-medium block">Total Openings</span>
                <span class="text-2xl sm:text-3xl font-black font-display text-white mt-1 block">{{ $totalOpenings }}</span>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg">
                <span class="text-xs text-slate-400 font-medium block">Active Postings</span>
                <span class="text-2xl sm:text-3xl font-black font-display text-emerald-400 mt-1 block">{{ $activeOpenings }}</span>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg">
                <span class="text-xs text-slate-400 font-medium block">Applications Received</span>
                <span class="text-2xl sm:text-3xl font-black font-display text-rose-400 mt-1 block">{{ $totalApplications }}</span>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-lg">
                <span class="text-xs text-slate-400 font-medium block">Interviews Scheduled</span>
                <span class="text-2xl sm:text-3xl font-black font-display text-purple-400 mt-1 block">{{ $scheduledInterviews }}</span>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 mb-6 backdrop-blur">
            <form method="GET" action="{{ route('showrooms.manage.careers.index', $showroom) }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                
                <div class="sm:col-span-4 relative">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search vacancy title or keywords..."
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 pl-9 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <div class="sm:col-span-3">
                    <select name="department" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="all">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->value }}" @selected($department === $dept->value)>{{ $dept->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <select name="type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="all">All Types</option>
                        @foreach($jobTypes as $jt)
                            <option value="{{ $jt->value }}" @selected($jobType === $jt->value)>{{ $jt->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="all">All Statuses</option>
                        <option value="active" @selected($status === 'active')>Active Only</option>
                        <option value="inactive" @selected($status === 'inactive')>Inactive Only</option>
                    </select>
                </div>

                <div class="sm:col-span-1 flex items-center gap-1">
                    <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs py-2.5 px-3 rounded-xl transition">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Job Openings Table -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 text-slate-400 font-semibold border-b border-slate-800 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4">Role & Department</th>
                            <th class="px-6 py-4">Type & Level</th>
                            <th class="px-6 py-4">Location & Salary</th>
                            <th class="px-6 py-4">Applicants</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-medium">
                        @forelse($jobOpenings as $job)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-white text-sm">{{ $job->title }}</div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $job->department->badgeClasses() }}">
                                            {{ $job->department->label() }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 font-mono">/careers/{{ $job->slug }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-200 block">{{ $job->job_type->label() }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ ucfirst($job->experience_level) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-200 block">{{ $job->location ?: $showroom->city }}</span>
                                    <span class="text-[11px] text-emerald-400 font-semibold block">{{ $job->salary_range ?: 'Not specified' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('showrooms.manage.applicants.index', [$showroom, 'job_opening_id' => $job->id]) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white transition font-bold">
                                        <span>{{ $job->applications_count }}</span>
                                        <span class="text-[10px] text-slate-400 font-normal">Candidates</span>
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('showrooms.manage.careers.toggle-active', [$showroom, $job]) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ $job->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:bg-slate-700' }}" title="Click to toggle status">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $job->is_active ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                            <span>{{ $job->is_active ? 'Active' : 'Inactive' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('showrooms.careers.show', [$showroom, $job]) }}"
                                           target="_blank"
                                           class="p-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white transition"
                                           title="View Public Vacancy Page">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>

                                        <a href="{{ route('showrooms.manage.careers.edit', [$showroom, $job]) }}"
                                           class="p-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white transition"
                                           title="Edit Opening">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <form method="POST" action="{{ route('showrooms.manage.careers.destroy', [$showroom, $job]) }}" onsubmit="return confirm('Delete this job vacancy? All associated applicant records will be preserved.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 transition" title="Delete Opening">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="font-bold text-white text-sm">No Job Vacancies Found</p>
                                    <p class="text-xs text-slate-400 mt-1">Click "Post Vacancy" above to start hiring for your showroom.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($jobOpenings->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $jobOpenings->links() }}
                </div>
            @endif
        </div>

    </main>
</div>
@endsection
