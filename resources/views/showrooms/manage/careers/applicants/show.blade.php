@extends('layouts.app')

@section('title', 'Candidate: ' . $applicant->applicant_name . ' - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-4">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-white transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.careers.index', $showroom) }}" class="hover:text-white transition">Careers</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.applicants.index', $showroom) }}" class="hover:text-white transition">ATS Candidates</a>
            <span>/</span>
            <span class="text-slate-200">#{{ $applicant->applicant_number }}</span>
        </div>

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

        <!-- Candidate Header Card -->
        <div class="rounded-3xl bg-slate-900 border border-slate-800 p-6 sm:p-8 shadow-2xl mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-500 to-rose-700 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-rose-900/30 shrink-0">
                        {{ strtoupper(substr($applicant->applicant_name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">{{ $applicant->applicant_name }}</h1>
                            <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $applicant->status->badgeClasses() }}">
                                {{ $applicant->status->label() }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
                            Applied for <strong class="text-white">{{ $applicant->jobOpening?->title ?? 'General Application' }}</strong>
                            &bull; Reference <span class="font-mono text-rose-400 font-bold">#{{ $applicant->applicant_number }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if($applicant->resume_path)
                        <a href="{{ asset('storage/' . $applicant->resume_path) }}"
                           target="_blank"
                           class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-600/30 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Download Resume / CV</span>
                        </a>
                    @endif

                    @if($applicant->portfolio_url)
                        <a href="{{ $applicant->portfolio_url }}"
                           target="_blank"
                           class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 font-bold text-xs transition flex items-center gap-2">
                            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Portfolio Link</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left: Profile Details & Cover Letter -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Applicant Quick Facts -->
                <div class="rounded-3xl bg-slate-900/80 border border-slate-800 p-6 shadow-xl">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Candidate Contact & Qualifications
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800/80">
                            <span class="text-slate-500 block">Email Address</span>
                            <a href="mailto:{{ $applicant->applicant_email }}" class="text-white font-bold hover:text-rose-400 mt-0.5 block truncate">
                                {{ $applicant->applicant_email }}
                            </a>
                        </div>

                        <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800/80">
                            <span class="text-slate-500 block">Phone / Mobile</span>
                            <a href="tel:{{ $applicant->applicant_phone }}" class="text-white font-bold hover:text-rose-400 mt-0.5 block">
                                {{ $applicant->applicant_phone }}
                            </a>
                        </div>

                        <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800/80">
                            <span class="text-slate-500 block">Total Experience</span>
                            <span class="text-emerald-400 font-black text-sm mt-0.5 block">
                                {{ $applicant->experience_years }} Years
                            </span>
                        </div>

                        <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800/80">
                            <span class="text-slate-500 block">Current Employer</span>
                            <span class="text-slate-200 font-bold mt-0.5 block">
                                {{ $applicant->current_company ?: 'Not provided' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Cover Letter -->
                <div class="rounded-3xl bg-slate-900/80 border border-slate-800 p-6 shadow-xl">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Candidate Cover Note / Pitch
                    </h3>
                    @if($applicant->cover_letter)
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs text-slate-300 leading-relaxed whitespace-pre-line">
                            {{ $applicant->cover_letter }}
                        </div>
                    @else
                        <p class="text-xs text-slate-500 italic">No cover letter was submitted with this application.</p>
                    @endif
                </div>

                <!-- Role Details Snippet -->
                @if($applicant->jobOpening)
                    <div class="rounded-3xl bg-slate-900/60 border border-slate-800 p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-bold text-white uppercase tracking-wider">Position Applied For</h4>
                            <a href="{{ route('showrooms.manage.careers.edit', [$showroom, $applicant->jobOpening]) }}" class="text-xs text-rose-400 hover:underline">Edit Vacancy &rarr;</a>
                        </div>
                        <p class="text-sm font-bold text-white">{{ $applicant->jobOpening->title }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ $applicant->jobOpening->department->label() }} &bull; {{ $applicant->jobOpening->job_type->label() }} &bull; {{ $applicant->jobOpening->location }}</p>
                    </div>
                @endif

            </div>

            <!-- Right: Status Advancement & Recruiter Actions Form -->
            <div class="lg:col-span-5">
                <div class="rounded-3xl bg-slate-900 border border-slate-800 p-6 sm:p-8 shadow-2xl space-y-6">
                    <div>
                        <h3 class="text-base font-bold font-display text-white">Recruitment Workflow & ATS Stage</h3>
                        <p class="text-xs text-slate-400 mt-1">Advance this candidate through the hiring stages or log interview feedback.</p>
                    </div>

                    <form method="POST" action="{{ route('showrooms.manage.applicants.update', [$showroom, $applicant]) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Pipeline Status -->
                        <div>
                            <label for="status" class="block text-xs font-semibold text-slate-300 mb-1">
                                Candidate Pipeline Stage <span class="text-rose-500">*</span>
                            </label>
                            <select id="status"
                                    name="status"
                                    required
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-rose-500">
                                @foreach($statuses as $st)
                                    <option value="{{ $st->value }}" @selected(old('status', $applicant->status->value) === $st->value)>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Schedule Interview -->
                        <div>
                            <label for="interview_at" class="block text-xs font-semibold text-slate-300 mb-1">
                                Interview Date & Time
                            </label>
                            <input type="datetime-local"
                                   id="interview_at"
                                   name="interview_at"
                                   value="{{ old('interview_at', $applicant->interview_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                            <span class="text-[10px] text-slate-500 block mt-1">Set date & time when candidate is invited for technical/HR interview.</span>
                        </div>

                        <!-- Internal Recruiter Notes -->
                        <div>
                            <label for="internal_notes" class="block text-xs font-semibold text-slate-300 mb-1">
                                Internal Notes & Interview Scorecard
                            </label>
                            <textarea id="internal_notes"
                                      name="internal_notes"
                                      rows="4"
                                      placeholder="Record feedback, diagnostic skills assessment, salary expectations, noticeable strengths..."
                                      class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">{{ old('internal_notes', $applicant->internal_notes) }}</textarea>
                        </div>

                        <!-- Rejection Reason -->
                        <div>
                            <label for="rejection_reason" class="block text-xs font-semibold text-slate-300 mb-1">
                                Rejection Reason (Internal)
                            </label>
                            <textarea id="rejection_reason"
                                      name="rejection_reason"
                                      rows="2"
                                      placeholder="Note why candidate was declined (e.g. salary expectation mismatch, insufficient diagnostic experience)..."
                                      class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">{{ old('rejection_reason', $applicant->rejection_reason) }}</textarea>
                        </div>

                        <!-- Save Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    id="btn-update-applicant"
                                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-rose-600/30 transition">
                                Update Candidate Status
                            </button>
                        </div>
                    </form>

                    <!-- Delete Candidate -->
                    <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                        <span class="text-[11px] text-slate-500">Delete candidate record</span>
                        <form method="POST" action="{{ route('showrooms.manage.applicants.destroy', [$showroom, $applicant]) }}" onsubmit="return confirm('Permanently remove candidate #{{ $applicant->applicant_number }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 font-semibold underline">
                                Delete Application
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>

    </main>
</div>
@endsection
