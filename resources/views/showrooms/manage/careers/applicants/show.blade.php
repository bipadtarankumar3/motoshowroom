@extends('layouts.app')

@section('title', 'Candidate: ' . $applicant->applicant_name . ' - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-4">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.careers.index', $showroom) }}" class="hover:text-slate-900 transition">Careers</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.applicants.index', $showroom) }}" class="hover:text-slate-900 transition">ATS Candidates</a>
            <span>/</span>
            <span class="text-slate-700 font-semibold">#{{ $applicant->applicant_number }}</span>
        </div>

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

        <!-- Candidate Header Card -->
        <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-2xl shadow-md shrink-0"
                         style="background-color: {{ $showroom->primary_color }}; color: {{ $showroom->primary_text_color }}; box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;">
                        {{ strtoupper(substr($applicant->applicant_name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">{{ $applicant->applicant_name }}</h1>
                            <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $applicant->status->badgeClasses() }}">
                                {{ $applicant->status->label() }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Applied for <strong class="text-slate-900">{{ $applicant->jobOpening?->title ?? ($applicant->desired_role . ' (Open Application)') }}</strong>
                            &bull; Reference <span class="font-mono font-bold" style="color: {{ $showroom->primary_color }};">#{{ $applicant->applicant_number }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if($applicant->resume_path)
                        <a href="{{ asset('storage/' . $applicant->resume_path) }}"
                           target="_blank"
                           class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Download Resume / CV</span>
                        </a>
                    @endif

                    @if($applicant->portfolio_url)
                        <a href="{{ $applicant->portfolio_url }}"
                           target="_blank"
                           class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 border border-slate-300 font-bold text-xs transition flex items-center gap-2 shadow-xs">
                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
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
                <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Candidate Contact & Qualifications
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block">Email Address</span>
                            <a href="mailto:{{ $applicant->applicant_email }}" class="text-slate-900 font-bold hover:text-rose-600 mt-0.5 block truncate">
                                {{ $applicant->applicant_email }}
                            </a>
                        </div>

                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block">Phone / Mobile</span>
                            <a href="tel:{{ $applicant->applicant_phone }}" class="text-slate-900 font-bold hover:text-rose-600 mt-0.5 block">
                                {{ $applicant->applicant_phone }}
                            </a>
                        </div>

                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block">Total Experience</span>
                            <span class="text-emerald-700 font-black text-sm mt-0.5 block">
                                {{ $applicant->experience_years }} Years
                            </span>
                        </div>

                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block">Current Employer</span>
                            <span class="text-slate-800 font-bold mt-0.5 block">
                                {{ $applicant->current_company ?: 'Not provided' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 1-Minute Video Pitch Card -->
                <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Candidate 1-Minute Video Pitch
                        </h3>
                        @if($applicant->video_path)
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 font-bold text-[11px] border border-rose-200 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                    <span>{{ $applicant->video_duration_seconds ? $applicant->video_duration_seconds . 's' : 'Recorded' }}</span>
                                </span>
                                <a href="{{ $applicant->display_video_url }}"
                                   download
                                   class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Download</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    @if($applicant->video_path)
                        <div class="rounded-2xl overflow-hidden bg-black border border-slate-300 shadow-sm relative">
                            <video controls
                                   preload="metadata"
                                   playsinline
                                   class="w-full aspect-video bg-black rounded-2xl"
                                   src="{{ $applicant->display_video_url }}">
                                Your browser does not support HTML5 video tag.
                            </video>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Recorded by candidate during career application submission.</span>
                        </p>
                    @else
                        <div class="p-5 rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-center">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">No 1-minute video pitch was recorded for this candidate.</p>
                        </div>
                    @endif
                </div>

                <!-- Cover Letter -->
                <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Candidate Cover Note / Pitch
                    </h3>
                    @if($applicant->cover_letter)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $applicant->cover_letter }}
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">No cover letter was submitted with this application.</p>
                    @endif
                </div>

                <!-- Role Details Snippet -->
                @if($applicant->jobOpening)
                    <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-xs">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Position Applied For</h4>
                            <a href="{{ route('showrooms.manage.careers.edit', [$showroom, $applicant->jobOpening]) }}" class="text-xs text-rose-600 hover:underline">Edit Vacancy &rarr;</a>
                        </div>
                        <p class="text-sm font-bold text-slate-900">{{ $applicant->jobOpening->title }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $applicant->jobOpening->department->label() }} &bull; {{ $applicant->jobOpening->job_type->label() }} &bull; {{ $applicant->jobOpening->location }}</p>
                    </div>
                @elseif($applicant->desired_role)
                    <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-xs">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">Desired Role (Open Application)</h4>
                        <p class="text-sm font-bold text-slate-900">{{ $applicant->desired_role }}</p>
                        <p class="text-xs text-slate-500 mt-1">Spontaneous talent pool submission without a specific vacancy opening.</p>
                    </div>
                @endif

            </div>

            <!-- Right: Status Advancement & Recruiter Actions Form -->
            <div class="lg:col-span-5">
                <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6">
                    <div>
                        <h3 class="text-base font-bold font-display text-slate-900">Recruitment Workflow & ATS Stage</h3>
                        <p class="text-xs text-slate-500 mt-1">Advance this candidate through the hiring stages or log interview feedback.</p>
                    </div>

                    <form method="POST" action="{{ route('showrooms.manage.applicants.update', [$showroom, $applicant]) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Pipeline Status -->
                        <div>
                            <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">
                                Candidate Pipeline Stage <span class="text-rose-500">*</span>
                            </label>
                            <select id="status"
                                    name="status"
                                    required
                                    class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                @foreach($statuses as $st)
                                    <option value="{{ $st->value }}" @selected(old('status', $applicant->status->value) === $st->value)>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Schedule Interview -->
                        <div>
                            <label for="interview_at" class="block text-xs font-semibold text-slate-700 mb-1">
                                Interview Date & Time
                            </label>
                            <input type="datetime-local"
                                   id="interview_at"
                                   name="interview_at"
                                   value="{{ old('interview_at', $applicant->interview_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <span class="text-[10px] text-slate-500 block mt-1">Set date & time when candidate is invited for technical/HR interview.</span>
                        </div>

                        <!-- Internal Recruiter Notes -->
                        <div>
                            <label for="internal_notes" class="block text-xs font-semibold text-slate-700 mb-1">
                                Internal Notes & Interview Scorecard
                            </label>
                            <textarea id="internal_notes"
                                      name="internal_notes"
                                      rows="4"
                                      placeholder="Record feedback, diagnostic skills assessment, salary expectations, noticeable strengths..."
                                      class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">{{ old('internal_notes', $applicant->internal_notes) }}</textarea>
                        </div>

                        <!-- Rejection Reason -->
                        <div>
                            <label for="rejection_reason" class="block text-xs font-semibold text-slate-700 mb-1">
                                Rejection Reason (Internal)
                            </label>
                            <textarea id="rejection_reason"
                                      name="rejection_reason"
                                      rows="2"
                                      placeholder="Note why candidate was declined (e.g. salary expectation mismatch, insufficient diagnostic experience)..."
                                      class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">{{ old('rejection_reason', $applicant->rejection_reason) }}</textarea>
                        </div>

                        <!-- Save Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    id="btn-update-applicant"
                                    class="w-full py-3 px-4 rounded-xl btn-brand-primary font-bold text-xs uppercase tracking-wider shadow-md transition"
                                    style="box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;">
                                Update Candidate Status
                            </button>
                        </div>
                    </form>

                    <!-- Delete Candidate -->
                    <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                        <span class="text-[11px] text-slate-500">Delete candidate record</span>
                        <form method="POST" action="{{ route('showrooms.manage.applicants.destroy', [$showroom, $applicant]) }}" onsubmit="return confirm('Permanently remove candidate #{{ $applicant->applicant_number }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 font-semibold underline">
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
