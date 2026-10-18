@extends('layouts.app')

@section('title', 'Edit Job Vacancy - ' . $jobOpening->title)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-2">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-white transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.careers.index', $showroom) }}" class="hover:text-white transition">Careers</a>
            <span>/</span>
            <span class="text-slate-200 truncate max-w-sm">Edit: {{ $jobOpening->title }}</span>
        </div>

        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">Edit Job Opening</h1>
                <p class="text-sm text-slate-400 mt-1">Update role specifications, requirements, or application deadline.</p>
            </div>
            <a href="{{ route('showrooms.careers.show', [$showroom, $jobOpening]) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-bold text-slate-300 hover:text-white transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>View Public Page</span>
            </a>
        </div>

        <div class="rounded-3xl bg-slate-900 border border-slate-800 p-6 sm:p-8 shadow-2xl">
            <form method="POST" action="{{ route('showrooms.manage.careers.update', [$showroom, $jobOpening]) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-semibold text-slate-300 mb-1">
                        Job Title / Position Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title', $jobOpening->title) }}"
                           required
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                    @error('title')
                        <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department & Type & Experience Level Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="department" class="block text-xs font-semibold text-slate-300 mb-1">
                            Department <span class="text-rose-500">*</span>
                        </label>
                        <select id="department"
                                name="department"
                                required
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-rose-500">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->value }}" @selected(old('department', $jobOpening->department->value) === $dept->value)>{{ $dept->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="job_type" class="block text-xs font-semibold text-slate-300 mb-1">
                            Contract Type <span class="text-rose-500">*</span>
                        </label>
                        <select id="job_type"
                                name="job_type"
                                required
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-rose-500">
                            @foreach($jobTypes as $jt)
                                <option value="{{ $jt->value }}" @selected(old('job_type', $jobOpening->job_type->value) === $jt->value)>{{ $jt->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="experience_level" class="block text-xs font-semibold text-slate-300 mb-1">
                            Seniority Level <span class="text-rose-500">*</span>
                        </label>
                        <select id="experience_level"
                                name="experience_level"
                                required
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-rose-500">
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
                        <label for="location" class="block text-xs font-semibold text-slate-300 mb-1">Work Location</label>
                        <input type="text"
                               id="location"
                               name="location"
                               value="{{ old('location', $jobOpening->location) }}"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                    </div>

                    <div>
                        <label for="salary_range" class="block text-xs font-semibold text-slate-300 mb-1">Salary / Compensation</label>
                        <input type="text"
                               id="salary_range"
                               name="salary_range"
                               value="{{ old('salary_range', $jobOpening->salary_range) }}"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                    </div>

                    <div>
                        <label for="deadline" class="block text-xs font-semibold text-slate-300 mb-1">Application Deadline</label>
                        <input type="date"
                               id="deadline"
                               name="deadline"
                               value="{{ old('deadline', $jobOpening->deadline?->format('Y-m-d')) }}"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                    </div>
                </div>

                <!-- Job Description -->
                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-300 mb-1">
                        Role Overview & Responsibilities <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="5"
                              required
                              class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">{{ old('description', $jobOpening->description) }}</textarea>
                    @error('description')
                        <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Requirements -->
                <div>
                    <label for="requirements" class="block text-xs font-semibold text-slate-300 mb-1">
                        Required Qualifications & Technical Skills
                    </label>
                    <textarea id="requirements"
                              name="requirements"
                              rows="4"
                              class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">{{ old('requirements', $jobOpening->requirements) }}</textarea>
                </div>

                <!-- Benefits -->
                <div>
                    <label for="benefits" class="block text-xs font-semibold text-slate-300 mb-1">
                        Benefits, Allowances & Perks
                    </label>
                    <textarea id="benefits"
                              name="benefits"
                              rows="3"
                              class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">{{ old('benefits', $jobOpening->benefits) }}</textarea>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox"
                           id="is_active"
                           name="is_active"
                           value="1"
                           @checked(old('is_active', $jobOpening->is_active))
                           class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-rose-600 focus:ring-rose-500">
                    <label for="is_active" class="text-xs font-medium text-slate-300 cursor-pointer">
                        Position is currently active and open for applications
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="pt-6 border-t border-slate-800 flex items-center justify-end gap-3">
                    <a href="{{ route('showrooms.manage.careers.index', $showroom) }}"
                       class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition">
                        Cancel
                    </a>
                    <button type="submit"
                            id="btn-update-vacancy"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-rose-600/30 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>
@endsection
