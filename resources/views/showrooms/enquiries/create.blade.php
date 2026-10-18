@extends('layouts.app')

@section('title', 'Book a Test Ride & Enquiries - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    <!-- Showroom Branded Topbar / Header -->
    <header class="border-b border-slate-800/80 bg-slate-950/90 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Showroom Identity -->
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
                    <a href="{{ route('showrooms.enquiries.create', $showroom) }}" class="text-rose-400 font-bold transition">Test Ride & Enquiries</a>
                    <a href="{{ route('showrooms.show', $showroom) }}#contact" class="text-slate-300 hover:text-white transition">Contact</a>
                </div>

                <!-- Phone Support -->
                @if($showroom->phone)
                    <a href="tel:{{ $showroom->phone }}" class="flex items-center gap-2 text-xs font-bold text-slate-300 hover:text-white transition">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>{{ $showroom->phone }}</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-6">
            <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-white transition">Home</a>
            <span>/</span>
            <span class="text-rose-400">Book Test Ride / Enquiry</span>
        </div>

        <!-- Success Flash Message -->
        @if (session('success'))
            <div class="mb-8 rounded-3xl bg-emerald-950/80 border border-emerald-500/40 p-6 text-emerald-200 shadow-2xl shadow-emerald-950/30">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-white">Enquiry Received Successfully!</h3>
                        <p class="text-xs sm:text-sm text-emerald-300 mt-1 leading-relaxed">{{ session('success') }}</p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition">
                                Browse More Motorcycles
                            </a>
                            <a href="{{ route('showrooms.show', $showroom) }}" class="px-4 py-2 bg-slate-900 border border-slate-700 text-slate-200 text-xs font-semibold rounded-xl hover:text-white transition">
                                Return to Dealership Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Heading -->
        <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
            <span class="text-xs font-bold uppercase tracking-widest text-rose-400">Direct Dealership Access</span>
            <h1 class="text-3xl sm:text-4xl font-black font-display text-white tracking-tight">Schedule Test Ride & Purchase Enquiry</h1>
            <p class="text-xs sm:text-sm text-slate-400">
                Experience track-tuned acceleration and premium ergonomics. Reserve a priority test ride or request an instant on-road price quotation.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-950/80 border border-rose-500/40 p-4 text-rose-300">
                <div class="font-bold text-sm mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please review the highlighted fields:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="glass-panel-dark rounded-3xl p-6 sm:p-10 border border-slate-800 shadow-2xl">
            <form method="POST" action="{{ route('showrooms.enquiries.store', $showroom) }}" class="space-y-8" id="customer-enquiry-form">
                @csrf

                <!-- Section 1: Enquiry Type Selector -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">What are you looking for?</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="cursor-pointer border rounded-2xl p-4 flex items-center gap-3 transition {{ old('type', $type) === 'test_ride' ? 'border-amber-500 bg-amber-500/10' : 'border-slate-800 bg-slate-900/60 hover:bg-slate-900' }}">
                            <input type="radio" name="type" value="test_ride" {{ old('type', $type) === 'test_ride' ? 'checked' : '' }} class="sr-only">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-white block">Test Ride</span>
                                <span class="text-[11px] text-slate-400 block">Experience on road</span>
                            </div>
                        </label>

                        <label class="cursor-pointer border rounded-2xl p-4 flex items-center gap-3 transition {{ old('type', $type) === 'purchase' ? 'border-emerald-500 bg-emerald-500/10' : 'border-slate-800 bg-slate-900/60 hover:bg-slate-900' }}">
                            <input type="radio" name="type" value="purchase" {{ old('type', $type) === 'purchase' ? 'checked' : '' }} class="sr-only">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-white block">Purchase Quotation</span>
                                <span class="text-[11px] text-slate-400 block">Pricing & finance</span>
                            </div>
                        </label>

                        <label class="cursor-pointer border rounded-2xl p-4 flex items-center gap-3 transition {{ old('type', $type) === 'general' ? 'border-sky-500 bg-sky-500/10' : 'border-slate-800 bg-slate-900/60 hover:bg-slate-900' }}">
                            <input type="radio" name="type" value="general" {{ old('type', $type) === 'general' ? 'checked' : '' }} class="sr-only">
                            <div class="w-8 h-8 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-white block">General Consultation</span>
                                <span class="text-[11px] text-slate-400 block">Expert advice</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Section 2: Motorcycle Selection -->
                <div>
                    <label for="bike_id" class="block text-xs font-semibold text-slate-300 mb-1.5">Select Motorcycle Model <span class="text-slate-500 font-normal">(Optional if general inquiry)</span></label>
                    <select name="bike_id" id="bike_id"
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-rose-500">
                        <option value="">-- General Dealership Inquiry / Not Decided Yet --</option>
                        @foreach($bikes as $bike)
                            <option value="{{ $bike->id }}" {{ (old('bike_id') == $bike->id || ($selectedBike && $selectedBike->id == $bike->id)) ? 'selected' : '' }}>
                                {{ $bike->brand }} {{ $bike->name }} ({{ $bike->formatted_price }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Section 3: Customer Information -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="customer_name" class="block text-xs font-semibold text-slate-300 mb-1.5">Full Name <span class="text-rose-400">*</span></label>
                        <input type="text" name="customer_name" id="customer_name"
                               value="{{ old('customer_name', auth()->user()?->name) }}" required
                               placeholder="e.g. John Doe"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-rose-500">
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-xs font-semibold text-slate-300 mb-1.5">Phone Number <span class="text-rose-400">*</span></label>
                        <input type="tel" name="customer_phone" id="customer_phone"
                               value="{{ old('customer_phone', auth()->user()?->phone) }}" required
                               placeholder="e.g. +91 9876543210"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-rose-500 font-mono">
                    </div>

                    <div>
                        <label for="customer_email" class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address <span class="text-rose-400">*</span></label>
                        <input type="email" name="customer_email" id="customer_email"
                               value="{{ old('customer_email', auth()->user()?->email) }}" required
                               placeholder="e.g. john@example.com"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-rose-500">
                    </div>
                </div>

                <!-- Section 4: Test Ride Date & Slot (Shown always, required for test ride) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="preferred_date" class="block text-xs font-semibold text-slate-300 mb-1.5">Preferred Date</label>
                        <input type="date" name="preferred_date" id="preferred_date"
                               value="{{ old('preferred_date', date('Y-m-d', strtotime('+1 day'))) }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-rose-500">
                    </div>

                    <div>
                        <label for="preferred_time_slot" class="block text-xs font-semibold text-slate-300 mb-1.5">Preferred Time Window</label>
                        <select name="preferred_time_slot" id="preferred_time_slot"
                                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-rose-500">
                            <option value="Morning (10:00 AM - 12:00 PM)" {{ old('preferred_time_slot') === 'Morning (10:00 AM - 12:00 PM)' ? 'selected' : '' }}>Morning (10:00 AM - 12:00 PM)</option>
                            <option value="Afternoon (12:00 PM - 03:00 PM)" {{ old('preferred_time_slot') === 'Afternoon (12:00 PM - 03:00 PM)' ? 'selected' : '' }}>Afternoon (12:00 PM - 03:00 PM)</option>
                            <option value="Evening (03:00 PM - 06:00 PM)" {{ old('preferred_time_slot') === 'Evening (03:00 PM - 06:00 PM)' ? 'selected' : '' }}>Evening (03:00 PM - 06:00 PM)</option>
                            <option value="Late Evening (06:00 PM - 08:00 PM)" {{ old('preferred_time_slot') === 'Late Evening (06:00 PM - 08:00 PM)' ? 'selected' : '' }}>Late Evening (06:00 PM - 08:00 PM)</option>
                        </select>
                    </div>

                    <div>
                        <label for="driving_license_number" class="block text-xs font-semibold text-slate-300 mb-1.5">Driving License # <span class="text-slate-500 font-normal">(Test Ride)</span></label>
                        <input type="text" name="driving_license_number" id="driving_license_number"
                               value="{{ old('driving_license_number') }}"
                               placeholder="e.g. DL-0420110012345"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-rose-500 font-mono">
                    </div>
                </div>

                <!-- Section 5: Specific Requests / Notes -->
                <div>
                    <label for="customer_notes" class="block text-xs font-semibold text-slate-300 mb-1.5">Additional Inquiries or Requirements <span class="text-slate-500 font-normal">(Optional)</span></label>
                    <textarea name="customer_notes" id="customer_notes" rows="3"
                              placeholder="Any specific trim, exchange of current bike, insurance or finance questions..."
                              class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">{{ old('customer_notes') }}</textarea>
                </div>

                <!-- Dealership Assurance Badges -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs text-slate-400">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Official Dealership Warranty</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Prompt Response Within 2 Hours</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Zero Commitment Test Ride</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-800">
                    <button type="submit"
                            class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-black text-sm tracking-wide shadow-xl shadow-rose-600/30 transition flex items-center justify-center gap-2 group" id="btn-submit-enquiry">
                        <span>Confirm & Submit Request</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-8 text-center text-xs text-slate-500 mt-12">
        <div class="max-w-7xl mx-auto px-4">
            <p>© {{ date('Y') }} {{ $showroom->name }}. All rights reserved.</p>
        </div>
    </footer>
</div>
@endsection
