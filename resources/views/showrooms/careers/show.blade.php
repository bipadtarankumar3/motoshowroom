@extends('layouts.app')

@section('title', $jobOpening->title . ' - Careers at ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    {{-- Branded Dealership Header --}}
    @include('showrooms.partials.header')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-10 xl:px-16 py-8 sm:py-12 space-y-8">

        <!-- Modern Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-slate-900 transition">Home</a>
            <span>/</span>
            <a href="{{ route('showrooms.careers.index', $showroom) }}" class="hover:text-slate-900 transition">Careers</a>
            <span>/</span>
            <span class="text-rose-600 font-bold truncate max-w-xs sm:max-w-md">{{ $jobOpening->title }}</span>
        </div>

        <!-- Flash Success Notification -->
        @if (session('success'))
            <div class="rounded-3xl bg-emerald-50 border border-emerald-200 p-6 text-emerald-800 shadow-xs">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-base text-slate-900">Application Received!</h4>
                        <p class="text-sm mt-1 text-emerald-700 leading-relaxed">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Full-Width Modern Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 xl:gap-10">

            <!-- Left / Main: Job Overview & Description -->
            <div class="lg:col-span-7 xl:col-span-7 space-y-8">

                <!-- Job Header Card -->
                <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-10 shadow-xs">
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="text-xs px-3 py-1 rounded-lg font-bold {{ $jobOpening->department->badgeClasses() }}">
                            {{ $jobOpening->department->label() }}
                        </span>
                        <span class="text-xs px-3 py-1 rounded-lg font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $jobOpening->job_type->label() }}
                        </span>
                        <span class="text-xs px-3 py-1 rounded-lg font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ ucfirst($jobOpening->experience_level) }} Level
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black font-display text-slate-900 tracking-tight leading-tight mb-6">
                        {{ $jobOpening->title }}
                    </h1>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 pt-6 border-t border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400 font-semibold block uppercase tracking-wider text-[10px]">Work Location</span>
                            <span class="font-bold text-slate-900 text-sm mt-1 block">{{ $jobOpening->location ?: $showroom->city }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block uppercase tracking-wider text-[10px]">Compensation</span>
                            <span class="font-black text-emerald-600 text-sm mt-1 block">{{ $jobOpening->salary_range ?: 'Competitive / Negotiable' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block uppercase tracking-wider text-[10px]">Application Deadline</span>
                            <span class="font-bold text-slate-900 text-sm mt-1 block">
                                {{ $jobOpening->deadline ? $jobOpening->deadline->format('M d, Y') : 'Open until filled' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Description & Responsibilities -->
                <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-10 shadow-xs space-y-8">
                    <div>
                        <h2 class="text-lg font-bold font-display text-slate-900 mb-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                            Role Overview
                        </h2>
                        <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                            {{ $jobOpening->description }}
                        </div>
                    </div>

                    @if($jobOpening->requirements)
                        <div class="pt-8 border-t border-slate-100">
                            <h2 class="text-lg font-bold font-display text-slate-900 mb-4 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                Required Qualifications & Skills
                            </h2>
                            <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                                {{ $jobOpening->requirements }}
                            </div>
                        </div>
                    @endif

                    @if($jobOpening->benefits)
                        <div class="pt-8 border-t border-slate-100">
                            <h2 class="text-lg font-bold font-display text-slate-900 mb-4 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                What We Offer & Perks
                            </h2>
                            <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                                {{ $jobOpening->benefits }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Other Openings Carousel / Grid -->
                @if($otherOpenings->count() > 0)
                    <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-xs">
                        <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center justify-between">
                            <span>Other Openings at {{ $showroom->name }}</span>
                            <a href="{{ route('showrooms.careers.index', $showroom) }}" class="text-xs text-rose-600 hover:underline">View All &rarr;</a>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($otherOpenings as $other)
                                <a href="{{ route('showrooms.careers.show', [$showroom, $other]) }}"
                                   class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200 hover:border-rose-400 hover:bg-white transition group">
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 group-hover:text-rose-600 transition">{{ $other->title }}</h4>
                                        <span class="text-[11px] text-slate-500 mt-0.5 block">{{ $other->department->label() }} &bull; {{ $other->job_type->label() }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-rose-600 group-hover:translate-x-1 transition-transform">&rarr;</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right: Application Submission Form -->
            <div class="lg:col-span-5 xl:col-span-5">
                <div class="sticky top-24 rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-md space-y-6">
                    <div>
                        <span class="text-xs font-bold text-rose-600 uppercase tracking-wider block mb-1">Direct Submission</span>
                        <h2 class="text-xl sm:text-2xl font-black font-display text-slate-900">Apply for this Position</h2>
                        <p class="text-xs text-slate-500 mt-1">Submit your profile and 1-minute video pitch directly to our hiring team.</p>
                    </div>

                    <form method="POST"
                          action="{{ route('showrooms.careers.apply', [$showroom, $jobOpening]) }}"
                          enctype="multipart/form-data"
                          class="space-y-4">
                        @csrf

                        <!-- Full Name -->
                        <div>
                            <label for="applicant_name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Full Legal Name <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   id="applicant_name"
                                   name="applicant_name"
                                   value="{{ old('applicant_name', auth()->user()?->name) }}"
                                   required
                                   placeholder="e.g. Rahul Sharma"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20 transition">
                            @error('applicant_name')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email & Phone Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="applicant_email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Email Address <span class="text-rose-600">*</span>
                                </label>
                                <input type="email"
                                       id="applicant_email"
                                       name="applicant_email"
                                       value="{{ old('applicant_email', auth()->user()?->email) }}"
                                       required
                                       placeholder="you@domain.com"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20 transition">
                                @error('applicant_email')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="applicant_phone" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Phone Number <span class="text-rose-600">*</span>
                                </label>
                                <input type="text"
                                       id="applicant_phone"
                                       name="applicant_phone"
                                       value="{{ old('applicant_phone', auth()->user()?->phone) }}"
                                       required
                                       placeholder="+91 98765 43210"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20 transition font-mono">
                                @error('applicant_phone')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Company & Experience Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="current_company" class="block text-xs font-semibold text-slate-700 mb-1.5">Current Employer</label>
                                <input type="text"
                                       id="current_company"
                                       name="current_company"
                                       value="{{ old('current_company') }}"
                                       placeholder="Optional (e.g. Honda Motors)"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20 transition">
                            </div>

                            <div>
                                <label for="experience_years" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Experience (Yrs) <span class="text-rose-600">*</span>
                                </label>
                                <input type="number"
                                       id="experience_years"
                                       name="experience_years"
                                       value="{{ old('experience_years', 1) }}"
                                       min="0"
                                       max="50"
                                       step="0.5"
                                       required
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20 transition">
                                @error('experience_years')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Resume Upload -->
                        <div>
                            <label for="resume_file" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Resume / CV Attachment (PDF or DOC, max 5MB)
                            </label>
                            <input type="file"
                                   id="resume_file"
                                   name="resume_file"
                                   accept=".pdf,.doc,.docx"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer">
                            @error('resume_file')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Portfolio / LinkedIn -->
                        <div>
                            <label for="portfolio_url" class="block text-xs font-semibold text-slate-700 mb-1.5">Portfolio or LinkedIn Profile</label>
                            <input type="url"
                                   id="portfolio_url"
                                   name="portfolio_url"
                                   value="{{ old('portfolio_url') }}"
                                   placeholder="https://linkedin.com/in/username"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20 transition">
                            @error('portfolio_url')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 1-Minute Video Pitch Recorder -->
                        @include('showrooms.careers.partials.video-recorder')

                        <!-- Cover Letter / Notes -->
                        <div>
                            <label for="cover_letter" class="block text-xs font-semibold text-slate-700 mb-1.5">Cover Note / Introduction</label>
                            <textarea id="cover_letter"
                                      name="cover_letter"
                                      rows="3"
                                      placeholder="Briefly describe why you are the ideal fit for this role..."
                                      class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20 transition">{{ old('cover_letter') }}</textarea>
                            @error('cover_letter')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    id="btn-submit-application"
                                    class="w-full py-4 px-6 rounded-2xl btn-brand-primary font-bold text-xs uppercase tracking-wider shadow-lg transition hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer"
                                    style="box-shadow: 0 4px 14px 0 {{ $showroom->primary_color }}33;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                <span>Submit Job Application</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </main>

    {{-- Motorcycle Showroom Footer --}}
    @include('showrooms.partials.footer')

</div>
@endsection
