@extends('layouts.app')

@section('title', 'Post Job Vacancy - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.careers.index', $showroom) }}" class="hover:text-slate-900 transition">Careers</a>
            <span>/</span>
            <span class="text-slate-700 font-semibold">Post Vacancy</span>
        </div>

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Create Job Opening</h1>
            <p class="text-sm text-slate-500 mt-1">Publish a new position to attract talent to your showroom dealership.</p>
        </div>

        <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('showrooms.manage.careers.store', $showroom) }}" class="space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-semibold text-slate-700 mb-1">
                        Job Title / Position Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           required
                           placeholder="e.g. Lead Motorcycle Technician / Diagnostic Specialist"
                           class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
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
                                class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->value }}" @selected(old('department') === $dept->value)>{{ $dept->label() }}</option>
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
                                class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            @foreach($jobTypes as $jt)
                                <option value="{{ $jt->value }}" @selected(old('job_type') === $jt->value)>{{ $jt->label() }}</option>
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
                                class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                            <option value="entry" @selected(old('experience_level') === 'entry')>Entry Level (0-2 yrs)</option>
                            <option value="mid" @selected(old('experience_level', 'mid') === 'mid')>Mid Level (2-5 yrs)</option>
                            <option value="senior" @selected(old('experience_level') === 'senior')>Senior Level (5+ yrs)</option>
                            <option value="lead" @selected(old('experience_level') === 'lead')>Team Lead / Manager</option>
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
                               value="{{ old('location', $showroom->city) }}"
                               placeholder="e.g. {{ $showroom->city }}, Workshop Bay 1"
                               class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <div>
                        <label for="salary_range" class="block text-xs font-semibold text-slate-700 mb-1">Salary / Compensation</label>
                        <input type="text"
                               id="salary_range"
                               name="salary_range"
                               value="{{ old('salary_range') }}"
                               placeholder="e.g. ₹35,000 - ₹55,000 / mo"
                               class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>

                    <div>
                        <label for="deadline" class="block text-xs font-semibold text-slate-700 mb-1">Application Deadline</label>
                        <input type="date"
                               id="deadline"
                               name="deadline"
                               value="{{ old('deadline') }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>
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
                              placeholder="Detail the daily duties, key deliverables, and team dynamics..."
                              class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">{{ old('description') }}</textarea>
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
                              placeholder="List technical certifications, required bike brands experience, licensing..."
                              class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">{{ old('requirements') }}</textarea>
                </div>

                <!-- Benefits -->
                <div>
                    <label for="benefits" class="block text-xs font-semibold text-slate-700 mb-1">
                        Benefits, Allowances & Perks
                    </label>
                    <textarea id="benefits"
                              name="benefits"
                              rows="3"
                              placeholder="e.g. Performance incentives, fuel allowance, health insurance, employee gear discounts..."
                              class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">{{ old('benefits') }}</textarea>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox"
                           id="is_active"
                           name="is_active"
                           value="1"
                           @checked(old('is_active', true))
                           class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    <label for="is_active" class="text-xs font-medium text-slate-700 cursor-pointer">
                        Immediately publish and accept candidate applications on the public careers portal
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
                    <a href="{{ route('showrooms.manage.careers.index', $showroom) }}"
                       class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-bold transition">
                        Cancel
                    </a>
                    <button type="submit"
                            id="btn-save-vacancy"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-rose-600/20 transition">
                        Publish Vacancy
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>
@endsection
