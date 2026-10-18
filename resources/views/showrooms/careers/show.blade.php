@extends('layouts.app')

@section('title', $jobOpening->title . ' - Careers at ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    <!-- Showroom Branded Topbar / Header -->
    <header class="border-b border-slate-800/80 bg-slate-950/90 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Showroom Logo & Identity -->
                <a href="{{ route('showrooms.show', $showroom) }}" class="flex items-center gap-3 group">
                    @if($showroom->logo_url)
                        <img src="{{ $showroom->logo_url }}" alt="{{ $showroom->name }}" class="h-10 w-auto rounded-xl object-contain bg-slate-900 border border-slate-800 p-1">
                    @else
                        <span class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-white text-base shadow-lg group-hover:scale-105 transition-transform"
                              style="background: linear-gradient(135deg, {{ $showroom->brand_color_primary ?? '#dc2626' }}, {{ $showroom->brand_color_accent ?? '#f43f5e' }})">
                            {{ strtoupper(substr($showroom->name, 0, 1)) }}
                        </span>
                    @endif
                    <div>
                        <span class="font-display font-black text-lg sm:text-xl tracking-tight text-white block leading-none">{{ $showroom->name }}</span>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest block mt-0.5">{{ $showroom->city }}, {{ $showroom->state ?? 'IN' }}</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-6 text-sm font-semibold">
                    <a href="{{ route('showrooms.show', $showroom) }}" class="text-slate-300 hover:text-white transition">Home</a>
                    <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="text-slate-300 hover:text-white transition">Motorcycles</a>
                    <a href="{{ route('showrooms.services.create', $showroom) }}" class="text-slate-300 hover:text-white transition">Service & Repair</a>
                    <a href="{{ route('showrooms.careers.index', $showroom) }}" class="text-rose-400 font-bold transition">Careers</a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('showrooms.careers.index', $showroom) }}"
                       class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        All Vacancies
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-6">
            <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-white transition">Home</a>
            <span>/</span>
            <a href="{{ route('showrooms.careers.index', $showroom) }}" class="hover:text-white transition">Careers</a>
            <span>/</span>
            <span class="text-rose-400 truncate max-w-xs sm:max-w-md">{{ $jobOpening->title }}</span>
        </div>

        <!-- Flash Success Notification -->
        @if (session('success'))
            <div class="mb-8 rounded-2xl bg-emerald-950/80 border border-emerald-500/40 p-6 text-emerald-300 shadow-xl shadow-emerald-950/30">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-base text-white">Application Received!</h4>
                        <p class="text-sm mt-1 text-emerald-300/90 leading-relaxed">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left / Main: Job Overview & Description -->
            <div class="lg:col-span-7 space-y-8">

                <!-- Job Header Card -->
                <div class="rounded-3xl bg-slate-900/80 border border-slate-800 p-6 sm:p-8 shadow-xl">
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="text-xs px-3 py-1 rounded-lg font-bold {{ $jobOpening->department->badgeClasses() }}">
                            {{ $jobOpening->department->label() }}
                        </span>
                        <span class="text-xs px-3 py-1 rounded-lg font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                            {{ $jobOpening->job_type->label() }}
                        </span>
                        <span class="text-xs px-3 py-1 rounded-lg font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                            {{ ucfirst($jobOpening->experience_level) }} Level
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black font-display text-white tracking-tight leading-tight mb-4">
                        {{ $jobOpening->title }}
                    </h1>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-800/80 text-xs">
                        <div>
                            <span class="text-slate-500 block">Work Location</span>
                            <span class="font-bold text-slate-200 mt-0.5 block">{{ $jobOpening->location ?: $showroom->city }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Compensation</span>
                            <span class="font-bold text-emerald-400 mt-0.5 block">{{ $jobOpening->salary_range ?: 'Competitive / Performance' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Application Deadline</span>
                            <span class="font-bold text-slate-200 mt-0.5 block">
                                {{ $jobOpening->deadline ? $jobOpening->deadline->format('M d, Y') : 'Open until filled' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Description & Responsibilities -->
                <div class="rounded-3xl bg-slate-900/60 border border-slate-800 p-6 sm:p-8 shadow-xl space-y-6">
                    <div>
                        <h2 class="text-lg font-bold font-display text-white mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Role Overview
                        </h2>
                        <div class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                            {{ $jobOpening->description }}
                        </div>
                    </div>

                    @if($jobOpening->requirements)
                        <div class="pt-6 border-t border-slate-800">
                            <h2 class="text-lg font-bold font-display text-white mb-3 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Required Qualifications & Skills
                            </h2>
                            <div class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                                {{ $jobOpening->requirements }}
                            </div>
                        </div>
                    @endif

                    @if($jobOpening->benefits)
                        <div class="pt-6 border-t border-slate-800">
                            <h2 class="text-lg font-bold font-display text-white mb-3 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                What We Offer & Perks
                            </h2>
                            <div class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                                {{ $jobOpening->benefits }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Other Openings -->
                @if($otherOpenings->count() > 0)
                    <div class="rounded-3xl bg-slate-900/40 border border-slate-800 p-6">
                        <h3 class="text-base font-bold text-white mb-4">Other Openings at {{ $showroom->name }}</h3>
                        <div class="space-y-3">
                            @foreach($otherOpenings as $other)
                                <a href="{{ route('showrooms.careers.show', [$showroom, $other]) }}"
                                   class="flex items-center justify-between p-3 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
                                    <div>
                                        <h4 class="text-xs font-bold text-white">{{ $other->title }}</h4>
                                        <span class="text-[11px] text-slate-400">{{ $other->department->label() }} &bull; {{ $other->job_type->label() }}</span>
                                    </div>
                                    <span class="text-xs font-semibold text-rose-400">&rarr;</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right: Application Submission Form -->
            <div class="lg:col-span-5">
                <div class="sticky top-24 rounded-3xl bg-slate-900 border border-slate-800 p-6 sm:p-8 shadow-2xl">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold font-display text-white">Apply for this Position</h2>
                        <p class="text-xs text-slate-400 mt-1">Submit your profile and resume directly to our dealership hiring desk.</p>
                    </div>

                    <form method="POST"
                          action="{{ route('showrooms.careers.apply', [$showroom, $jobOpening]) }}"
                          enctype="multipart/form-data"
                          class="space-y-4">
                        @csrf

                        <!-- Full Name -->
                        <div>
                            <label for="applicant_name" class="block text-xs font-semibold text-slate-300 mb-1">
                                Full Legal Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   id="applicant_name"
                                   name="applicant_name"
                                   value="{{ old('applicant_name', auth()->user()?->name) }}"
                                   required
                                   placeholder="e.g. Rahul Sharma"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                            @error('applicant_name')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email & Phone Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="applicant_email" class="block text-xs font-semibold text-slate-300 mb-1">
                                    Email Address <span class="text-rose-500">*</span>
                                </label>
                                <input type="email"
                                       id="applicant_email"
                                       name="applicant_email"
                                       value="{{ old('applicant_email', auth()->user()?->email) }}"
                                       required
                                       placeholder="you@domain.com"
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                                @error('applicant_email')
                                    <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="applicant_phone" class="block text-xs font-semibold text-slate-300 mb-1">
                                    Phone Number <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="applicant_phone"
                                       name="applicant_phone"
                                       value="{{ old('applicant_phone', auth()->user()?->phone) }}"
                                       required
                                       placeholder="+91 98765 43210"
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                                @error('applicant_phone')
                                    <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Company & Experience Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="current_company" class="block text-xs font-semibold text-slate-300 mb-1">Current Employer</label>
                                <input type="text"
                                       id="current_company"
                                       name="current_company"
                                       value="{{ old('current_company') }}"
                                       placeholder="Optional (e.g. Honda Motors)"
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                            </div>

                            <div>
                                <label for="experience_years" class="block text-xs font-semibold text-slate-300 mb-1">
                                    Total Experience (Yrs) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number"
                                       id="experience_years"
                                       name="experience_years"
                                       value="{{ old('experience_years', 1) }}"
                                       min="0"
                                       max="50"
                                       step="0.5"
                                       required
                                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                                @error('experience_years')
                                    <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Resume Upload -->
                        <div>
                            <label for="resume_file" class="block text-xs font-semibold text-slate-300 mb-1">
                                Resume / CV Attachment (PDF or DOC, max 5MB)
                            </label>
                            <input type="file"
                                   id="resume_file"
                                   name="resume_file"
                                   accept=".pdf,.doc,.docx"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-500/10 file:text-rose-400 hover:file:bg-rose-500/20 cursor-pointer">
                            @error('resume_file')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Portfolio / LinkedIn -->
                        <div>
                            <label for="portfolio_url" class="block text-xs font-semibold text-slate-300 mb-1">Portfolio or LinkedIn Profile</label>
                            <input type="url"
                                   id="portfolio_url"
                                   name="portfolio_url"
                                   value="{{ old('portfolio_url') }}"
                                   placeholder="https://linkedin.com/in/username"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                            @error('portfolio_url')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cover Letter / Notes -->
                        <div>
                            <label for="cover_letter" class="block text-xs font-semibold text-slate-300 mb-1">Cover Note / Introduction</label>
                            <textarea id="cover_letter"
                                      name="cover_letter"
                                      rows="3"
                                      placeholder="Briefly describe why you are the ideal fit for this role..."
                                      class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">{{ old('cover_letter') }}</textarea>
                            @error('cover_letter')
                                <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    id="btn-submit-application"
                                    class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-rose-600/30 transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                Submit Job Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-8 mt-12 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} {{ $showroom->name }}. Powered by MotoShowroom SaaS.</p>
    </footer>

</div>
@endsection
