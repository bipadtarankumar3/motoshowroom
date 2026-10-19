@extends('layouts.app')

@section('title', 'Applicant Tracking System (ATS) - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 space-y-6">

        <!-- Flash Feedback Notifications -->
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

        <!-- ATS Header & Action Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('showrooms.manage.careers.index', $showroom) }}" class="hover:text-slate-900 transition">Careers</a>
                    <span>/</span>
                    <span class="text-slate-700 font-semibold">ATS Candidates</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Applicant Tracking System (ATS)</h1>
                <p class="text-sm text-slate-500 mt-1">Review candidate resumes, advance application stages, and record interview decisions.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.manage.careers.index', $showroom) }}"
                   class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 hover:text-slate-900 font-bold text-xs shadow-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Manage Job Openings</span>
                </a>
            </div>
        </div>

        <!-- ATS Metric KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-xs">
                <span class="text-[11px] text-slate-500 font-medium block">Total Applicants</span>
                <span class="text-2xl font-black font-display text-slate-900 mt-1 block">{{ $totalCount }}</span>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-xs">
                <span class="text-[11px] text-sky-600 font-medium block">New Applications</span>
                <span class="text-2xl font-black font-display text-sky-600 mt-1 block">{{ $newCount }}</span>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-xs">
                <span class="text-[11px] text-amber-600 font-medium block">Reviewing & Shortlisted</span>
                <span class="text-2xl font-black font-display text-amber-600 mt-1 block">{{ $shortlistedCount }}</span>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-xs">
                <span class="text-[11px] text-purple-600 font-medium block">Interviews</span>
                <span class="text-2xl font-black font-display text-purple-600 mt-1 block">{{ $interviewCount }}</span>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-xs">
                <span class="text-[11px] text-emerald-600 font-medium block">Offers & Hired</span>
                <span class="text-2xl font-black font-display text-emerald-600 mt-1 block">{{ $hiredCount }}</span>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 mb-6 shadow-xs">
            <form method="GET" action="{{ route('showrooms.manage.applicants.index', $showroom) }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <div class="sm:col-span-5 relative">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search candidate name, email, phone..."
                           class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 pl-9 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <div class="sm:col-span-4">
                    <select name="job_opening_id" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        <option value="">All Job Openings</option>
                        @foreach($jobOpenings as $opening)
                            <option value="{{ $opening->id }}" @selected($jobOpeningId == $opening->id)>{{ $opening->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <select name="status" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        <option value="all">All Stages</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" @selected($status === $st->value)>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-1">
                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-2.5 px-3 rounded-xl transition shadow-xs">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- ATS Applicants Table -->
        <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4">Candidate Profile</th>
                            <th class="px-6 py-4">Applied Vacancy</th>
                            <th class="px-6 py-4">Experience</th>
                            <th class="px-6 py-4">Stage</th>
                            <th class="px-6 py-4">Date Applied</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @php $applicantList = $applicants ?? $applications ?? collect(); @endphp
                        @forelse($applicantList as $app)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                        <a href="{{ route('showrooms.manage.applicants.show', [$showroom, $app]) }}" class="hover:text-rose-600 transition">
                                            {{ $app->applicant_name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 font-mono">#{{ $app->applicant_number }}</span>
                                        @if($app->video_path)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 border border-rose-200 text-rose-700 text-[10px] font-bold">
                                                <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                <span>{{ $app->video_duration_seconds ? $app->video_duration_seconds . 's Video' : 'Video' }}</span>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 space-y-0.5">
                                        <span>{{ $app->applicant_email }}</span> &bull;
                                        <span class="font-mono">{{ $app->applicant_phone }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($app->jobOpening)
                                        <span class="text-slate-900 font-bold block">{{ $app->jobOpening->title }}</span>
                                        <span class="text-[10px] text-slate-500 block">{{ $app->jobOpening->department->label() }}</span>
                                    @elseif($app->desired_role)
                                        <span class="text-slate-900 font-bold block">{{ $app->desired_role }}</span>
                                        <span class="text-[10px] text-rose-600 font-medium block">Open Application</span>
                                    @else
                                        <span class="text-slate-400 italic">General Inquiry</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-800 font-bold block">{{ $app->experience_years }} yrs</span>
                                    @if($app->current_company)
                                        <span class="text-[11px] text-slate-500 block truncate max-w-[140px]">{{ $app->current_company }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $app->status->badgeClasses() }}">
                                        {{ $app->status->label() }}
                                    </span>
                                    @if($app->interview_at)
                                        <span class="text-[10px] text-purple-700 block mt-1 font-semibold">
                                            Interview: {{ $app->interview_at->format('M d, H:i') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-[11px]">
                                    {{ $app->created_at->format('M d, Y') }}
                                    <span class="text-slate-400 block text-[10px]">{{ $app->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($app->resume_path)
                                            <a href="{{ asset('storage/' . $app->resume_path) }}"
                                               target="_blank"
                                               class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition"
                                               title="View Resume / CV">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </a>
                                        @endif

                                        <a href="{{ route('showrooms.manage.applicants.show', [$showroom, $app]) }}"
                                           class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs">
                                            Review
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <p class="font-bold text-slate-800 text-sm">No Candidate Applications Found</p>
                                    <p class="text-xs text-slate-500 mt-1">Applications submitted on your dealership careers portal will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applicantList->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white">
                    {{ $applicantList->links() }}
                </div>
            @endif
        </div>

    </main>
</div>
@endsection
