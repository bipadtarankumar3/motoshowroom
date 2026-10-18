<x-app-layout :title="'Book Service - ' . $showroom->name">
    {{-- Branded Dealership Header --}}
    @include('showrooms.partials.header')

<div class="min-h-screen bg-slate-50 text-slate-900 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">

        <!-- Back to Storefront Navigation -->
        <div class="flex items-center justify-between text-xs text-slate-500">
            <a href="{{ route('showrooms.show', $showroom) }}" class="inline-flex items-center gap-1.5 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to {{ $showroom->name }}</span>
            </a>
            <a href="{{ route('showrooms.services.track', $showroom) }}" class="inline-flex items-center gap-1.5 text-rose-600 hover:text-rose-700 font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span>Track Existing Service Status</span>
            </a>
        </div>

        <!-- Hero Header -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Authorized Workshop & Service Bay
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-black font-display text-slate-900 tracking-tight">
                        Schedule Motorcycle Service
                    </h1>
                    <p class="text-sm text-slate-600 max-w-xl">
                        Expert certified mechanics, computerized diagnostics, genuine OEM replacement parts, and real-time live bay repair tracking.
                    </p>
                </div>

                <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 shrink-0">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-lg">
                        {{ $capacity }}
                    </div>
                    <div class="text-xs">
                        <p class="font-bold text-slate-900">Active Service Bays</p>
                        <p class="text-slate-500">Guaranteed slot concurrency</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Success Notification -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
                <div class="font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please resolve the following before proceeding:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Booking Form -->
        <form method="POST" action="{{ route('showrooms.services.store', $showroom) }}" class="space-y-8" id="service-booking-form">
            @csrf

            <!-- Section 1: Choose Service Package -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 text-xs font-black flex items-center justify-center">1</span>
                        <span>Select Service Package</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Choose the service regimen recommended for your motorcycle.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($packages as $pkg)
                        <label class="relative flex flex-col p-4 rounded-2xl border cursor-pointer transition select-none group
                            {{ old('service_type', $selectedPackage) === $pkg->value ? 'bg-rose-50/70 border-rose-500 shadow-sm' : 'bg-slate-50/50 border-slate-200 hover:border-slate-300' }}">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="service_type" value="{{ $pkg->value }}"
                                           {{ old('service_type', $selectedPackage) === $pkg->value ? 'checked' : '' }}
                                           class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500"
                                           onchange="updateEstimatedCost('{{ $pkg->estimatedPrice() }}')">
                                    <span class="font-bold text-sm text-slate-900 group-hover:text-rose-600 transition">{{ $pkg->label() }}</span>
                                </div>
                                <span class="text-xs font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                    ₹{{ number_format($pkg->estimatedPrice()) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed pl-6">{{ $pkg->description() }}</p>
                            <div class="mt-3 pl-6 flex items-center gap-2 text-[11px] text-slate-500">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Est. Bay Duration: ~{{ $pkg->estimatedDurationMinutes() }} mins</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Section 2: Vehicle & Motorcycle Information -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 text-xs font-black flex items-center justify-center">2</span>
                        <span>Motorcycle Identification</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Provide your vehicle registration plate and current kilometer reading.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="bike_make_model" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Motorcycle Make & Model <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="bike_make_model" id="bike_make_model" required
                               value="{{ old('bike_make_model', $selectedBike ? ($selectedBike->brand . ' ' . $selectedBike->name) : '') }}"
                               placeholder="e.g. Yamaha R15 V4 / Royal Enfield Hunter 350"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500 transition">
                    </div>

                    <div>
                        <label for="registration_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Vehicle Registration Number <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="registration_number" id="registration_number" required
                               value="{{ old('registration_number') }}"
                               placeholder="e.g. KA-05-EM-9921"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm uppercase font-mono tracking-wider focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500 transition">
                    </div>

                    <div>
                        <label for="odometer_reading" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Odometer Reading (KM)
                        </label>
                        <input type="number" name="odometer_reading" id="odometer_reading" min="0" max="500000"
                               value="{{ old('odometer_reading') }}"
                               placeholder="e.g. 14500"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500 transition">
                    </div>

                    @if ($selectedBike)
                        <input type="hidden" name="bike_id" value="{{ $selectedBike->id }}">
                    @endif

                    <div>
                        <label for="customer_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Specific Issues / Symptoms
                        </label>
                        <input type="text" name="customer_notes" id="customer_notes"
                               value="{{ old('customer_notes') }}"
                               placeholder="e.g. Chain noise, front brake lever feels spongy"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500 transition">
                    </div>
                </div>
            </div>

            <!-- Section 3: Date & Service Bay Slot Concurrency -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 text-xs font-black flex items-center justify-center">3</span>
                        <span>Service Bay Date & Time Window</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Appointments are capped by physical bay capacity to ensure zero workshop wait time.</p>
                </div>

                <div class="space-y-4">
                    <div class="max-w-xs">
                        <label for="booking_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Calendar Appointment Date <span class="text-rose-600">*</span>
                        </label>
                        <input type="date" name="booking_date" id="booking_date" required
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('booking_date', $selectedDate) }}"
                               onchange="fetchSlotsForDate(this.value)"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-sm focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Select Available Bay Slot <span class="text-rose-600">*</span>
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3" id="slots-container">
                            @foreach ($slots as $slotData)
                                @php
                                    $isAvail = $slotData['available'];
                                    $slotVal = $slotData['slot'];
                                    $rem = $slotData['remaining'];
                                @endphp
                                <label class="p-3.5 rounded-xl border flex flex-col justify-between transition cursor-pointer select-none
                                    {{ $isAvail ? 'bg-white border-slate-200 hover:border-emerald-500/50 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50 shadow-xs' : 'bg-slate-100 border-slate-200 opacity-50 cursor-not-allowed' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <input type="radio" name="time_slot" value="{{ $slotVal }}"
                                                   {{ old('time_slot') === $slotVal || ($loop->first && !old('time_slot') && $isAvail) ? 'checked' : '' }}
                                                   {{ ! $isAvail ? 'disabled' : '' }}
                                                   class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500">
                                            <span class="text-xs font-bold font-mono text-slate-900">{{ $slotVal }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-right">
                                        @if ($isAvail)
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                {{ $rem }} / {{ $capacity }} Bays Open
                                            </span>
                                        @else
                                            <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                                Fully Booked
                                            </span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Customer Details & Assurance -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 text-xs font-black flex items-center justify-center">4</span>
                        <span>Customer Contact Information</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Service milestone updates and pickup readiness alerts will be sent here.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="customer_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Full Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="customer_name" id="customer_name" required
                               value="{{ old('customer_name', auth()->user()?->name) }}"
                               placeholder="e.g. Ramesh Chandra"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500 transition">
                    </div>

                    <div>
                        <label for="customer_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Email Address <span class="text-rose-600">*</span>
                        </label>
                        <input type="email" name="customer_email" id="customer_email" required
                               value="{{ old('customer_email', auth()->user()?->email) }}"
                               placeholder="e.g. ramesh@example.com"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500 transition">
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Mobile Phone <span class="text-rose-600">*</span>
                        </label>
                        <input type="tel" name="customer_phone" id="customer_phone" required
                               value="{{ old('customer_phone', auth()->user()?->phone) }}"
                               placeholder="e.g. +91 9876543210"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm font-mono focus:border-rose-500 focus:bg-white focus:ring-1 focus:ring-rose-500 transition">
                    </div>
                </div>

                <!-- Trust Badges -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-slate-100">
                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>OEM Parts</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Computerized Scan</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Transparent Price</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Live Status Tracker</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-slate-500">Estimated Service Cost</p>
                        <p class="text-2xl font-black font-display text-emerald-600" id="cost-display">
                            ₹{{ number_format(App\Enums\ServiceType::from(old('service_type', $selectedPackage))->estimatedPrice()) }}
                        </p>
                    </div>

                    <button type="submit" id="btn-submit-service-booking"
                            class="px-8 py-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                        <span>Confirm Service Appointment</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
function updateEstimatedCost(price) {
    document.getElementById('cost-display').innerText = '₹' + Number(price).toLocaleString('en-IN');
}

function fetchSlotsForDate(date) {
    const url = '{{ route("showrooms.services.slots", $showroom) }}?date=' + encodeURIComponent(date);
    fetch(url)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('slots-container');
            let html = '';
            for (const [key, slot] of Object.entries(data.slots)) {
                const isAvail = slot.available;
                const rem = slot.remaining;
                const cap = data.capacity;
                html += `
                    <label class="p-3.5 rounded-xl border flex flex-col justify-between transition cursor-pointer select-none
                        ${isAvail ? 'bg-white border-slate-200 hover:border-emerald-500/50 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50 shadow-xs' : 'bg-slate-100 border-slate-200 opacity-50 cursor-not-allowed'}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="time_slot" value="${slot.slot}"
                                       ${isAvail ? '' : 'disabled'}
                                       class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500">
                                <span class="text-xs font-bold font-mono text-slate-900">${slot.slot}</span>
                            </div>
                        </div>
                        <div class="mt-2 text-right">
                            ${isAvail
                                ? `<span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">${rem} / ${cap} Bays Open</span>`
                                : `<span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">Fully Booked</span>`
                            }
                        </div>
                    </label>
                `;
            }
            container.innerHTML = html;
        })
        .catch(err => console.error('Error fetching slots:', err));
}
</script>

    {{-- Motorcycle Showroom Footer --}}
    @include('showrooms.partials.footer')
</x-app-layout>
