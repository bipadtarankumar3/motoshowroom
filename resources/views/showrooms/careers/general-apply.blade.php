@extends('layouts.app')

@section('title', 'Open Application & 1-Minute Video Pitch - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    @include('showrooms.partials.header')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-10 xl:px-16 py-8 sm:py-12 space-y-8">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-6">
            <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-slate-900 transition">Home</a>
            <span>/</span>
            <a href="{{ route('showrooms.careers.index', $showroom) }}" class="hover:text-slate-900 transition">Careers</a>
            <span>/</span>
            <span class="text-rose-600 font-bold">Open Application & Video Pitch</span>
        </div>

        <!-- Hero Header -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-white via-slate-50 to-slate-100 border border-slate-200 p-8 sm:p-12 mb-10 shadow-sm">
            <div class="absolute -right-16 -bottom-16 w-80 h-80 rounded-full bg-rose-500/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-600 text-xs font-bold uppercase tracking-wider mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Spontaneous Application & 1-Min Video Pitch
                </span>
                <h1 class="text-3xl sm:text-5xl font-black font-display text-slate-900 tracking-tight leading-tight">
                    Pitch Yourself to <span class="text-rose-600">{{ $showroom->name }}</span>
                </h1>
                <p class="text-base sm:text-lg text-slate-600 mt-4 leading-relaxed">
                    Don't see an exact vacancy listed? We are always on the lookout for talented motorcycle specialists, technicians, advisors, and riders. Fill out your details, record your 1-minute video pitch, and join our candidate pool.
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-8 rounded-2xl bg-emerald-50 border border-emerald-200 p-5 text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold">Application Received!</h4>
                        <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
                <a href="{{ route('showrooms.careers.index', $showroom) }}" class="text-xs font-bold text-emerald-800 hover:underline">
                    Back to Careers &rarr;
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            <!-- Left: Pitch Guidance & Dealership Culture -->
            <div class="lg:col-span-5 space-y-6">

                <!-- 1-Min Video Guide Card -->
                <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4 border border-rose-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold font-display text-slate-900 mb-2">How the 1-Minute Pitch Works</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-6">
                        A short 60-second video lets our hiring directors meet the person behind the resume. You don't need fancy equipment—your phone or laptop camera is perfect!
                    </p>

                    <div class="space-y-4 text-xs text-slate-700">
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-800 font-bold flex items-center justify-center text-[11px] shrink-0">1</span>
                            <div>
                                <strong class="text-slate-900 block">Introduce Yourself</strong>
                                <span class="text-slate-500">Share your name, your location, and your area of expertise.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-800 font-bold flex items-center justify-center text-[11px] shrink-0">2</span>
                            <div>
                                <strong class="text-slate-900 block">Motorcycle Passion</strong>
                                <span class="text-slate-500">Tell us what drives your passion for bikes and customer excellence.</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-800 font-bold flex items-center justify-center text-[11px] shrink-0">3</span>
                            <div>
                                <strong class="text-slate-900 block">Preview & Re-record Anytime</strong>
                                <span class="text-slate-500">Watch your pitch immediately. If you want another take, just click re-record before submitting.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dealership Highlights -->
                <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-xs">
                    <h3 class="text-base font-bold font-display text-slate-900 mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        Why Join {{ $showroom->name }}
                    </h3>
                    <div class="space-y-3 text-xs text-slate-600">
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Work with industry-leading motorcycle brands and high-performance machines.</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>State-of-the-art service bays, diagnostic tools, and OEM certifications.</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Performance bonuses, team track days, and friendly motorcycle community events.</span>
                        </p>
                    </div>
                </div>

            </div>

            <!-- Right: Application Form -->
            <div class="lg:col-span-7">
                <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-md">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold font-display text-slate-900">General Candidate Application</h2>
                        <p class="text-xs text-slate-500 mt-1">Fill out your profile and submit your 1-minute video pitch to our talent team.</p>
                    </div>

                    <form method="POST"
                          action="{{ route('showrooms.careers.store-general', $showroom) }}"
                          enctype="multipart/form-data"
                          class="space-y-5">
                        @csrf

                        <!-- Full Name -->
                        <div>
                            <label for="applicant_name" class="block text-xs font-semibold text-slate-700 mb-1">
                                Full Legal Name <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   id="applicant_name"
                                   name="applicant_name"
                                   value="{{ old('applicant_name', auth()->user()?->name) }}"
                                   required
                                   placeholder="e.g. Priya Sharma"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500">
                            @error('applicant_name')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email & Phone Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="applicant_email" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Email Address <span class="text-rose-600">*</span>
                                </label>
                                <input type="email"
                                       id="applicant_email"
                                       name="applicant_email"
                                       value="{{ old('applicant_email', auth()->user()?->email) }}"
                                       required
                                       placeholder="you@domain.com"
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500">
                                @error('applicant_email')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="applicant_phone" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Phone Number <span class="text-rose-600">*</span>
                                </label>
                                <input type="text"
                                       id="applicant_phone"
                                       name="applicant_phone"
                                       value="{{ old('applicant_phone', auth()->user()?->phone) }}"
                                       required
                                       placeholder="+91 98765 43210"
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500 font-mono">
                                @error('applicant_phone')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Desired Role -->
                        <div>
                            <label for="desired_role" class="block text-xs font-semibold text-slate-700 mb-1">
                                Desired Role or Department <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   id="desired_role"
                                   name="desired_role"
                                   value="{{ old('desired_role') }}"
                                   required
                                   placeholder="e.g. Master Motorcycle Technician, Sales Consultant, Service Advisor..."
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500">
                            @error('desired_role')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Company & Experience Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="current_company" class="block text-xs font-semibold text-slate-700 mb-1">Current Employer</label>
                                <input type="text"
                                       id="current_company"
                                       name="current_company"
                                       value="{{ old('current_company') }}"
                                       placeholder="Optional (e.g. BMW Motorrad)"
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500">
                            </div>

                            <div>
                                <label for="experience_years" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Total Experience (Years) <span class="text-rose-600">*</span>
                                </label>
                                <input type="number"
                                       id="experience_years"
                                       name="experience_years"
                                       value="{{ old('experience_years', 1) }}"
                                       min="0"
                                       max="50"
                                       step="0.5"
                                       required
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500">
                                @error('experience_years')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Resume Upload -->
                        <div>
                            <label for="resume_file" class="block text-xs font-semibold text-slate-700 mb-1">
                                Resume / CV Attachment (PDF or DOC, max 5MB)
                            </label>
                            <input type="file"
                                   id="resume_file"
                                   name="resume_file"
                                   accept=".pdf,.doc,.docx"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer">
                            @error('resume_file')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 1-Minute Video Pitch Recorder -->
                        @include('showrooms.careers.partials.video-recorder')

                        <!-- Portfolio / LinkedIn -->
                        <div>
                            <label for="portfolio_url" class="block text-xs font-semibold text-slate-700 mb-1">Portfolio or LinkedIn Profile</label>
                            <input type="url"
                                   id="portfolio_url"
                                   name="portfolio_url"
                                   value="{{ old('portfolio_url') }}"
                                   placeholder="https://linkedin.com/in/username"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500">
                            @error('portfolio_url')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cover Letter / Notes -->
                        <div>
                            <label for="cover_letter" class="block text-xs font-semibold text-slate-700 mb-1">Cover Note / Introduction</label>
                            <textarea id="cover_letter"
                                      name="cover_letter"
                                      rows="3"
                                      placeholder="Briefly tell us why you want to join our dealership..."
                                      class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500">{{ old('cover_letter') }}</textarea>
                            @error('cover_letter')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    id="btn-submit-general-application"
                                    class="w-full py-3.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-rose-600/20 transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                Submit Application & Video Pitch
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </main>

    @include('showrooms.partials.footer')

</div>
@endsection
