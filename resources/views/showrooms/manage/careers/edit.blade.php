@extends('layouts.app')

@section('title', 'Edit Job Vacancy - ' . $jobOpening->title)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.careers.index', $showroom) }}" class="hover:text-slate-900 transition">Careers</a>
            <span>/</span>
            <span class="text-slate-700 font-semibold truncate max-w-sm">Edit: {{ $jobOpening->title }}</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Edit Job Opening</h1>
                <p class="text-sm text-slate-500 mt-1">Update role specifications, requirements, or application deadline for {{ $jobOpening->title }}.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('showrooms.careers.show', [$showroom, $jobOpening]) }}" target="_blank"
                   class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-xs font-bold text-slate-700 hover:text-slate-900 transition flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>View Public Page</span>
                </a>
                <a href="{{ route('showrooms.manage.careers.index', $showroom) }}"
                   class="px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 hover:text-slate-900 hover:bg-slate-50 text-xs font-bold transition shadow-2xs">
                    <span>Back to Openings</span>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('showrooms.manage.careers.update', [$showroom, $jobOpening]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                {{-- Main Form Details (8 Cols) --}}
                <div class="lg:col-span-8 space-y-6">
                    <div class="rounded-3xl bg-white border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">1</span>
                                <span>Role Overview & Classification</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-1">Specify title, department, contract arrangement, and work seniority level.</p>
                        </div>

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-xs font-semibold text-slate-700 mb-1">
                                Job Title / Position Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   value="{{ old('title', $jobOpening->title) }}"
                                   required
                                   class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            @error('title')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Department & Type & Experience Level Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="department" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Department <span class="text-rose-500">*</span>
                                </label>
                                <select id="department"
                                        name="department"
                                        required
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->value }}" @selected(old('department', $jobOpening->department->value) === $dept->value)>{{ $dept->label() }}</option>
                                    @endforeach
                                </select>
                                @error('department')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="job_type" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Contract Type <span class="text-rose-500">*</span>
                                </label>
                                <select id="job_type"
                                        name="job_type"
                                        required
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                                    @foreach($jobTypes as $jt)
                                        <option value="{{ $jt->value }}" @selected(old('job_type', $jobOpening->job_type->value) === $jt->value)>{{ $jt->label() }}</option>
                                    @endforeach
                                </select>
                                @error('job_type')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="experience_level" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Seniority Level <span class="text-rose-500">*</span>
                                </label>
                                <select id="experience_level"
                                        name="experience_level"
                                        required
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                                    <option value="entry" @selected(old('experience_level', $jobOpening->experience_level) === 'entry')>Entry Level (0-2 yrs)</option>
                                    <option value="mid" @selected(old('experience_level', $jobOpening->experience_level) === 'mid')>Mid Level (2-5 yrs)</option>
                                    <option value="senior" @selected(old('experience_level', $jobOpening->experience_level) === 'senior')>Senior Level (5+ yrs)</option>
                                    <option value="lead" @selected(old('experience_level', $jobOpening->experience_level) === 'lead')>Team Lead / Manager</option>
                                </select>
                            </div>
                        </div>

                        <!-- Location & Salary & Deadline Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="location" class="block text-xs font-semibold text-slate-700 mb-1">Work Location</label>
                                <input type="text"
                                       id="location"
                                       name="location"
                                       value="{{ old('location', $jobOpening->location) }}"
                                       placeholder="e.g. {{ $showroom->city }}, Workshop Bay 1"
                                       class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            </div>

                            <div>
                                <label for="salary_range" class="block text-xs font-semibold text-slate-700 mb-1">Salary / Compensation</label>
                                <input type="text"
                                       id="salary_range"
                                       name="salary_range"
                                       value="{{ old('salary_range', $jobOpening->salary_range) }}"
                                       placeholder="e.g. ₹35,000 - ₹55,000 / mo"
                                       class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            </div>

                            <div>
                                <label for="deadline" class="block text-xs font-semibold text-slate-700 mb-1">Application Deadline</label>
                                <input type="date"
                                       id="deadline"
                                       name="deadline"
                                       value="{{ old('deadline', $jobOpening->deadline?->format('Y-m-d')) }}"
                                       class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            </div>
                        </div>
                    </div>

                    {{-- Description & Qualifications Card --}}
                    <div class="rounded-3xl bg-white border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">2</span>
                                <span>Duties, Skills & Candidate Perks</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-1">Provide comprehensive details so applicants understand the role and requirements.</p>
                        </div>

                        <!-- Job Description -->
                        <div>
                            <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                                Role Overview & Responsibilities <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="5"
                                      required
                                      class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition leading-relaxed">{{ old('description', $jobOpening->description) }}</textarea>
                            @error('description')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Requirements -->
                        <div>
                            <label for="requirements" class="block text-xs font-semibold text-slate-700 mb-1">
                                Required Qualifications & Technical Skills
                            </label>
                            <textarea id="requirements"
                                      name="requirements"
                                      rows="4"
                                      class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition leading-relaxed">{{ old('requirements', $jobOpening->requirements) }}</textarea>
                        </div>

                        <!-- Benefits -->
                        <div>
                            <label for="benefits" class="block text-xs font-semibold text-slate-700 mb-1">
                                Benefits, Allowances & Perks
                            </label>
                            <textarea id="benefits"
                                      name="benefits"
                                      rows="3"
                                      class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition leading-relaxed">{{ old('benefits', $jobOpening->benefits) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Right Sidebar (4 Cols) --}}
                <div class="lg:col-span-4 space-y-6 sticky top-16">
                    <!-- Publishing & Status Card -->
                    <div class="rounded-3xl bg-white border border-slate-200/80 p-6 shadow-xs space-y-5">
                        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">Publishing Status</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $jobOpening->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                {{ $jobOpening->is_active ? 'Active on Portal' : 'Inactive Draft' }}
                            </span>
                        </div>

                        <!-- Active Toggle -->
                        <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <input type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   @checked(old('is_active', $jobOpening->is_active))
                                   class="w-4 h-4 mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
                            <label for="is_active" class="text-xs font-medium text-slate-700 cursor-pointer select-none">
                                <span class="font-bold text-slate-900 block mb-0.5">Accept Applications</span>
                                Active listing accepting candidate applications on public portal
                            </label>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="pt-2 flex flex-col gap-2.5">
                            <button type="submit"
                                    id="btn-update-vacancy"
                                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-rose-600/20 transition flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Save Changes</span>
                            </button>
                            <a href="{{ route('showrooms.manage.careers.index', $showroom) }}"
                               class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-xs font-bold text-center transition">
                                Discard Changes
                            </a>
                        </div>
                    </div>

                    <!-- Applicants Count Quick Card -->
                    <div class="rounded-3xl bg-slate-50 border border-slate-200/80 p-6 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Candidate Pipeline</h4>
                            <span class="text-xs font-extrabold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">{{ $jobOpening->applications_count ?? $jobOpening->applications()->count() }} Applicants</span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Review and manage candidates who submitted resumes for this specific vacancy.
                        </p>
                        <a href="{{ route('showrooms.manage.applicants.index', [$showroom, 'job' => $jobOpening->id]) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 hover:text-rose-700 pt-1">
                            <span>View Candidates</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </form>

    </main>
</div>
@endsection
