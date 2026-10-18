@extends('layouts.app')

@section('title', 'Applicant Tracking System (ATS) - ' . $showroom->name)

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

        <!-- ATS Header & Action Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-1">
                    <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-white transition">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('showrooms.manage.careers.index', $showroom) }}" class="hover:text-white transition">Careers</a>
                    <span>/</span>
                    <span class="text-slate-200">ATS Candidates</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">Applicant Tracking System (ATS)</h1>
                <p class="text-sm text-slate-400 mt-1">Review candidate resumes, advance application stages, and record interview decisions.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.manage.careers.index', $showroom) }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-white font-bold text-xs shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Manage Job Openings</span>
                </a>
            </div>
        </div>

        <!-- ATS Metric KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 shadow-lg">
                <span class="text-[11px] text-slate-400 font-medium block">Total Applicants</span>
                <span class="text-2xl font-black font-display text-white mt-1 block">{{ $totalCount }}</span>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 shadow-lg">
                <span class="text-[11px] text-slate-400 font-medium block">New Applications</span>
                <span class="text-2xl font-black font-display text-sky-400 mt-1 block">{{ $newCount }}</span>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 shadow-lg">
                <span class="text-[11px] text-slate-400 font-medium block">Reviewing & Shortlisted</span>
                <span class="text-2xl font-black font-display text-amber-400 mt-1 block">{{ $shortlistedCount }}</span>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 shadow-lg">
                <span class="text-[11px] text-slate-400 font-medium block">Interviews Scheduled</span>
                <span class="text-2xl font-black font-display text-purple-400 mt-1 block">{{ $interviewCount }}</span>
            </div>

            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-4 shadow-lg">
                <span class="text-[11px] text-slate-400 font-medium block">Hired Candidates</span>
                <span class="text-2xl font-black font-display text-emerald-400 mt-1 block">{{ $hiredCount }}</span>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 mb-6 backdrop-blur">
            <form method="GET" action="{{ route('showrooms.manage.applicants.index', $showroom) }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                
                <!-- Search term -->
                <div class="sm:col-span-4 relative">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search by name, email, phone, #APP..."
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 pl-9 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Job Opening filter -->
                <div class="sm:col-span-4">
                    <select name="job_opening_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="all">All Job Vacancies</option>
                        @foreach($jobOpenings as $op)
                            <option value="{{ $op->id }}" @selected($jobOpeningId == $op->id)>{{ $op->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status filter -->
                <div class="sm:col-span-3">
                    <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-rose-500">
                        <option value="all">All Pipeline Stages</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" @selected($status === $st->value)>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter button -->
                <div class="sm:col-span-1">
                    <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs py-2.5 px-3 rounded-xl transition">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- ATS Applicants Table -->
        <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 text-slate-400 font-semibold border-b border-slate-800 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4">Applicant & Contact</th>
                            <th class="px-6 py-4">Applied Position</th>
                            <th class="px-6 py-4">Experience & Company</th>
                            <th class="px-6 py-4">Stage / Status</th>
                            <th class="px-6 py-4">Submission Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-medium">
                        @forelse($applications as $app)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-white text-sm">{{ $app->applicant_name }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $app->applicant_email }} &bull; {{ $app->applicant_phone }}</div>
                                    <span class="text-[10px] font-mono font-bold text-rose-400 mt-1 inline-block">#{{ $app->applicant_number }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-white font-bold block">{{ $app->jobOpening?->title ?? 'N/A' }}</span>
                                    @if($app->jobOpening)
                                        <span class="text-[10px] px-2 py-0.5 rounded font-bold mt-1 inline-block {{ $app->jobOpening->department->badgeClasses() }}">
                                            {{ $app->jobOpening->department->label() }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-200 block font-bold">{{ $app->experience_years }} yrs exp</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $app->current_company ?: 'Not specified' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold inline-block {{ $app->status->badgeClasses() }}">
                                        {{ $app->status->label() }}
                                    </span>
                                    @if($app->interview_at)
                                        <span class="text-[10px] text-purple-400 block mt-1">
                                            📅 {{ $app->interview_at->format('M d, Y h:i A') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-400">
                                    {{ $app->created_at->format('M d, Y') }}
                                    <span class="text-[10px] text-slate-500 block">{{ $app->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('showrooms.manage.applicants.show', [$showroom, $app]) }}"
                                           class="px-3 py-1.5 rounded-xl bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 hover:text-rose-300 border border-rose-500/20 text-xs font-bold transition flex items-center gap-1">
                                            <span>Review</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>

                                        <form method="POST" action="{{ route('showrooms.manage.applicants.destroy', [$showroom, $app]) }}" onsubmit="return confirm('Remove candidate application #{{ $app->applicant_number }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 transition" title="Delete Candidate">
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
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="font-bold text-white text-sm">No Candidate Applications Yet</p>
                                    <p class="text-xs text-slate-400 mt-1">Applications submitted by visitors on your showroom careers portal will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>

    </main>
</div>
@endsection
