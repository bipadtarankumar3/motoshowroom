@extends('layouts.app')

@section('title', 'Book a Test Ride & Enquiries - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white">

    @include('showrooms.partials.header')

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-6">
            <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-slate-900 transition">Home</a>
            <span>/</span>
            <span class="text-rose-600 font-bold">Book Test Ride / Enquiry</span>
        </div>

        <!-- Success Flash Message -->
        @if (session('success'))
            <div class="mb-8 rounded-3xl bg-emerald-50 border border-emerald-200 p-6 text-emerald-800 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Enquiry Received Successfully!</h3>
                        <p class="text-xs sm:text-sm text-emerald-700 mt-1 leading-relaxed">{{ session('success') }}</p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-xs">
                                Browse More Motorcycles
                            </a>
                            <a href="{{ route('showrooms.show', $showroom) }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-50 transition shadow-xs">
                                Return to Dealership Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($scratchParticipation) && $scratchParticipation)
            <!-- Bike Booking Scratch & Win Card Reward -->
            <div class="mb-8 rounded-3xl bg-gradient-to-r from-purple-50 via-white to-purple-50 border-2 border-purple-200 p-6 sm:p-7 shadow-sm relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-36 h-36 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 relative z-10">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-purple-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 uppercase tracking-wider">
                                    Rider Scratch Card
                                </span>
                                @if($scratchParticipation->is_scratched)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Revealed</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse">Unscratched</span>
                                @endif
                            </div>
                            <h3 class="text-lg font-black font-display text-slate-900 mt-1">
                                @if($scratchParticipation->is_scratched)
                                    Prize Revealed: {{ $scratchParticipation->prize?->title ?? 'Showroom Voucher' }}
                                @else
                                    🎉 Scratch & Win Booking Reward!
                                @endif
                            </h3>
                            <p class="text-xs text-slate-600 mt-0.5">
                                @if($scratchParticipation->is_scratched)
                                    @if($scratchParticipation->coupon_code)
                                        Use code <strong class="font-mono text-purple-700">{{ $scratchParticipation->coupon_code }}</strong> on your motorcycle purchase/booking.
                                    @else
                                        Thank you for participating! Present your booking confirmation upon showroom visit.
                                    @endif
                                @else
                                    You've unlocked an interactive dealership scratch card for this bike booking! Scratch now to win riding accessories, helmet vouchers, or purchase discounts!
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center gap-2">
                        <a href="{{ route('showrooms.scratch.play', [$showroom, $scratchParticipation]) }}"
                           class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-purple-600/20 transition flex items-center justify-center gap-2">
                            @if($scratchParticipation->is_scratched)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>View Voucher</span>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Scratch Card Now</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Heading -->
        <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
            <span class="text-xs font-bold uppercase tracking-widest text-rose-600">Direct Dealership Access</span>
            <h1 class="text-3xl sm:text-4xl font-black font-display text-slate-900 tracking-tight">Schedule Test Ride & Purchase Enquiry</h1>
            <p class="text-xs sm:text-sm text-slate-600">
                Experience track-tuned acceleration and premium ergonomics. Reserve a priority test ride or request an instant on-road price quotation.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800">
                <div class="font-bold text-sm mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
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
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm">
            <form method="POST" action="{{ route('showrooms.enquiries.store', $showroom) }}" class="space-y-8" id="customer-enquiry-form">
                @csrf

                <!-- Section 1: Enquiry Type Selector -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">What are you looking for?</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="cursor-pointer border rounded-2xl p-4 flex items-center gap-3 transition {{ old('type', $type) === 'test_ride' ? 'border-amber-500 bg-amber-50/70 shadow-xs' : 'border-slate-200 bg-slate-50/50 hover:bg-slate-50' }}">
                            <input type="radio" name="type" value="test_ride" {{ old('type', $type) === 'test_ride' ? 'checked' : '' }} class="sr-only">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-slate-900 block">Test Ride</span>
                                <span class="text-[11px] text-slate-500 block">Experience on road</span>
                            </div>
                        </label>

                        <label class="cursor-pointer border rounded-2xl p-4 flex items-center gap-3 transition {{ old('type', $type) === 'purchase' ? 'border-emerald-500 bg-emerald-50/70 shadow-xs' : 'border-slate-200 bg-slate-50/50 hover:bg-slate-50' }}">
                            <input type="radio" name="type" value="purchase" {{ old('type', $type) === 'purchase' ? 'checked' : '' }} class="sr-only">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-slate-900 block">Purchase Quotation</span>
                                <span class="text-[11px] text-slate-500 block">Pricing & finance</span>
                            </div>
                        </label>

                        <label class="cursor-pointer border rounded-2xl p-4 flex items-center gap-3 transition {{ old('type', $type) === 'general' ? 'border-sky-500 bg-sky-50/70 shadow-xs' : 'border-slate-200 bg-slate-50/50 hover:bg-slate-50' }}">
                            <input type="radio" name="type" value="general" {{ old('type', $type) === 'general' ? 'checked' : '' }} class="sr-only">
                            <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-slate-900 block">General Consultation</span>
                                <span class="text-[11px] text-slate-500 block">Expert advice</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Section 2: Motorcycle Selection -->
                <div>
                    <label for="bike_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Select Motorcycle Model <span class="text-slate-500 font-normal">(Optional if general inquiry)</span></label>
                    <select name="bike_id" id="bike_id"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-rose-500 focus:bg-white">
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
                        <label for="customer_name" class="block text-xs font-semibold text-slate-700 mb-1.5">Full Name <span class="text-rose-600">*</span></label>
                        <input type="text" name="customer_name" id="customer_name"
                               value="{{ old('customer_name', auth()->user()?->name) }}" required
                               placeholder="e.g. John Doe"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-rose-500 focus:bg-white">
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-xs font-semibold text-slate-700 mb-1.5">Phone Number <span class="text-rose-600">*</span></label>
                        <input type="tel" name="customer_phone" id="customer_phone"
                               value="{{ old('customer_phone', auth()->user()?->phone) }}" required
                               placeholder="e.g. +91 9876543210"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-rose-500 focus:bg-white font-mono">
                    </div>

                    <div>
                        <label for="customer_email" class="block text-xs font-semibold text-slate-700 mb-1.5">Email Address <span class="text-rose-600">*</span></label>
                        <input type="email" name="customer_email" id="customer_email"
                               value="{{ old('customer_email', auth()->user()?->email) }}" required
                               placeholder="e.g. john@example.com"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-rose-500 focus:bg-white">
                    </div>
                </div>

                <!-- Section 4: Test Ride Date & Slot -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="preferred_date" class="block text-xs font-semibold text-slate-700 mb-1.5">Preferred Date</label>
                        <input type="date" name="preferred_date" id="preferred_date"
                               value="{{ old('preferred_date', date('Y-m-d', strtotime('+1 day'))) }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-rose-500 focus:bg-white">
                    </div>

                    <div>
                        <label for="preferred_time_slot" class="block text-xs font-semibold text-slate-700 mb-1.5">Preferred Time Window</label>
                        <select name="preferred_time_slot" id="preferred_time_slot"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-rose-500 focus:bg-white">
                            <option value="Morning (10:00 AM - 12:00 PM)" {{ old('preferred_time_slot') === 'Morning (10:00 AM - 12:00 PM)' ? 'selected' : '' }}>Morning (10:00 AM - 12:00 PM)</option>
                            <option value="Afternoon (12:00 PM - 03:00 PM)" {{ old('preferred_time_slot') === 'Afternoon (12:00 PM - 03:00 PM)' ? 'selected' : '' }}>Afternoon (12:00 PM - 03:00 PM)</option>
                            <option value="Evening (03:00 PM - 06:00 PM)" {{ old('preferred_time_slot') === 'Evening (03:00 PM - 06:00 PM)' ? 'selected' : '' }}>Evening (03:00 PM - 06:00 PM)</option>
                            <option value="Late Evening (06:00 PM - 08:00 PM)" {{ old('preferred_time_slot') === 'Late Evening (06:00 PM - 08:00 PM)' ? 'selected' : '' }}>Late Evening (06:00 PM - 08:00 PM)</option>
                        </select>
                    </div>

                    <div>
                        <label for="driving_license_number" class="block text-xs font-semibold text-slate-700 mb-1.5">Driving License # <span class="text-slate-500 font-normal">(Test Ride)</span></label>
                        <input type="text" name="driving_license_number" id="driving_license_number"
                               value="{{ old('driving_license_number') }}"
                               placeholder="e.g. DL-0420110012345"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-rose-500 focus:bg-white font-mono">
                    </div>
                </div>

                <!-- Section 5: Specific Requests / Notes -->
                <div>
                    <label for="customer_notes" class="block text-xs font-semibold text-slate-700 mb-1.5">Additional Inquiries or Requirements <span class="text-slate-500 font-normal">(Optional)</span></label>
                    <textarea name="customer_notes" id="customer_notes" rows="3"
                              placeholder="Any specific trim, exchange of current bike, insurance or finance questions..."
                              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-rose-500 focus:bg-white">{{ old('customer_notes') }}</textarea>
                </div>

                <!-- Dealership Assurance Badges -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs text-slate-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Official Dealership Warranty</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Prompt Response Within 2 Hours</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Zero Commitment Test Ride</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-100">
                    <button type="submit"
                            class="w-full py-4 px-6 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-black text-sm tracking-wide shadow-md shadow-rose-600/20 transition flex items-center justify-center gap-2 group" id="btn-submit-enquiry">
                        <span>Confirm & Submit Request</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>

    </main>

    @include('showrooms.partials.footer')
</div>
@endsection
