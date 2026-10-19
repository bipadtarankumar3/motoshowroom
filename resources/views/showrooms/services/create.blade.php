<x-app-layout :title="'Book Service - ' . $showroom->name">
    {{-- Branded Dealership Header --}}
    @include('showrooms.partials.header')

<div class="min-h-screen bg-slate-50 text-slate-900 py-8 px-4 sm:px-6 lg:px-8 xl:px-12 w-full max-w-[1680px] mx-auto"
     x-data="{
        selectedServiceId: '{{ old('showroom_service_id', $selectedServiceId) }}',
        selectedServiceName: '{{ $currentCustomService ? addslashes($currentCustomService->name) : 'Periodic General Maintenance' }}',
        selectedPrice: {{ $currentCustomService ? (float) $currentCustomService->price : 1499.00 }},
        selectedDuration: '{{ $currentCustomService ? $currentCustomService->formattedDuration() : '90 mins' }}',
        selectedBadge: '{{ $currentCustomService ? $currentCustomService->badge : 'Popular' }}',
        isPaymentRequired: {{ ($currentCustomService && $currentCustomService->is_payment_enabled) ? 'true' : 'false' }},
        bikeMakeModel: '{{ old('bike_make_model', $selectedBike ? ($selectedBike->brand . ' ' . $selectedBike->name) : '') }}',
        registrationNumber: '{{ old('registration_number', '') }}',
        bookingDate: '{{ old('booking_date', $selectedDate) }}',
        timeSlot: '{{ old('time_slot', '09:00 - 11:00') }}',
        termsModalOpen: false,
        termsAccepted: {{ old('terms_accepted') ? 'true' : 'false' }},
        selectService(id, name, price, duration, badge, isPayment) {
            this.selectedServiceId = id;
            this.selectedServiceName = name;
            this.selectedPrice = Number(price);
            this.selectedDuration = duration;
            this.selectedBadge = badge;
            this.isPaymentRequired = Boolean(isPayment);
        }
     }">
    <div class="space-y-6">

        <!-- Top Breadcrumbs & Utility Navigation -->
        <div class="flex items-center justify-between text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-slate-900 transition flex items-center gap-1.5 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>{{ $showroom->name }}</span>
                </a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Workshop & Service Booking</span>
            </div>

            <a href="{{ route('showrooms.services.track', $showroom) }}" class="inline-flex items-center gap-1.5 text-rose-600 hover:text-rose-700 font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span>Track Existing Service Status</span>
            </a>
        </div>

        <!-- Full-Width Hero Banner -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -bottom-20 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-3 max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Authorized Dealership Service Bay
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                            <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                            Live Bay Telemetry & Scheduling
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            OEM Genuine Parts Only
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black font-display text-slate-900 tracking-tight">
                        Schedule Motorcycle Service
                    </h1>
                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                        Computerized diagnostics, factory-trained master technicians, fixed estimates, and live digital job card tracking from check-in to delivery.
                    </p>
                </div>

                <!-- Telemetry Bay Counter Widget -->
                <div class="flex sm:flex-row items-center gap-4 bg-slate-50/80 p-5 rounded-2xl border border-slate-200 shrink-0 shadow-2xs">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black text-2xl font-mono shadow-md shadow-emerald-600/20">
                        {{ $capacity }}
                    </div>
                    <div class="text-xs space-y-0.5">
                        <p class="font-bold text-slate-900 text-sm">Active Service Bays</p>
                        <p class="text-slate-500">Guaranteed slot concurrency</p>
                        <div class="flex items-center gap-1 text-[11px] text-emerald-700 font-semibold pt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Live automated allocation</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1 shadow-xs">
                <div class="font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please resolve the following before scheduling:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Form & Two-Column Split Layout -->
        <form method="POST" action="{{ route('showrooms.services.store', $showroom) }}" id="service-booking-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- ============================================== -->
                <!-- LEFT COLUMN: BOOKING STEPS 1-4 (8 COLS ON LG)  -->
                <!-- ============================================== -->
                <div class="lg:col-span-8 space-y-8">

                    <!-- STEP 1: SERVICE PACKAGE SELECTION -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">1</span>
                                    <span>Select Service Package</span>
                                </h2>
                                <p class="text-xs text-slate-500 mt-1">Choose the service regimen tailored for your motorcycle.</p>
                            </div>

                            <span class="text-xs text-slate-600 font-semibold bg-slate-50 px-3 py-1 rounded-full border border-slate-200">
                                {{ $customServices->count() }} Authorized Packages
                            </span>
                        </div>

                        <!-- 3-Column Wide Custom Services Grid -->
                        @if ($customServices->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                @foreach ($customServices as $svc)
                                    <label class="relative flex flex-col justify-between p-5 rounded-2xl border cursor-pointer transition select-none group"
                                           :class="selectedServiceId == '{{ $svc->id }}' ? 'bg-emerald-50/40 border-emerald-500 shadow-xs ring-2 ring-emerald-500/20' : 'bg-slate-50/60 border-slate-200 hover:border-slate-300 hover:bg-white'"
                                           @click="selectService('{{ $svc->id }}', '{{ addslashes($svc->name) }}', '{{ $svc->price }}', '{{ $svc->formattedDuration() }}', '{{ $svc->badge }}', {{ $svc->is_payment_enabled ? 'true' : 'false' }})">

                                        <div class="space-y-3">
                                            <!-- Top Badges & Duration -->
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    @if ($svc->badge)
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $svc->badgeClasses() }}">
                                                            {{ $svc->badge }}
                                                        </span>
                                                    @endif
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">
                                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span>~{{ $svc->formattedDuration() }}</span>
                                                    </span>
                                                </div>

                                                <input type="radio" name="showroom_service_id" value="{{ $svc->id }}"
                                                       :checked="selectedServiceId == '{{ $svc->id }}'"
                                                       class="w-4 h-4 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                            </div>

                                            <!-- Title & Price -->
                                            <div>
                                                <h3 class="font-bold text-sm text-slate-900 group-hover:text-emerald-700 transition leading-snug">
                                                    {{ $svc->name }}
                                                </h3>
                                                <div class="mt-2 flex items-baseline gap-1.5">
                                                    <span class="text-2xl font-black font-display text-emerald-700">
                                                        {{ $svc->formattedPrice() }}
                                                    </span>
                                                    <span class="text-[10px] text-slate-400 uppercase font-mono">INR</span>
                                                </div>
                                            </div>

                                            <!-- Payment Requirement Pill -->
                                            <div>
                                                @if ($svc->is_payment_enabled)
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                                        💳 Online Advance Required
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                                        ⚡ Pay at Workshop Bay
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Description -->
                                            @if ($svc->description)
                                                <p class="text-xs text-slate-600 leading-relaxed pt-1 line-clamp-3">
                                                    {{ $svc->description }}
                                                </p>
                                            @endif

                                            <!-- Inclusions Bullets -->
                                            @if (! empty($svc->features))
                                                <ul class="pt-2 border-t border-slate-100 space-y-1 text-[11px] text-slate-600">
                                                    @foreach (array_slice((array) $svc->features, 0, 4) as $item)
                                                        <li class="flex items-start gap-1.5">
                                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                            <span class="leading-tight">{{ $item }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-slate-100/80 flex items-center justify-between text-[11px] font-semibold text-emerald-700">
                                            <span x-text="selectedServiceId == '{{ $svc->id }}' ? '✓ Selected Package' : 'Select Package'"></span>
                                            <span class="text-slate-400 group-hover:translate-x-0.5 transition-transform">→</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <!-- Fallback Standard Catalog -->
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                @foreach ($packages as $pkg)
                                    <label class="p-4 rounded-2xl border cursor-pointer transition select-none bg-slate-50 border-slate-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-bold text-sm text-slate-900">{{ $pkg->label() }}</span>
                                            <input type="radio" name="service_type" value="{{ $pkg->value }}"
                                                   {{ old('service_type', $selectedPackage) === $pkg->value ? 'checked' : '' }}
                                                   class="w-4 h-4 text-emerald-600 border-slate-300">
                                        </div>
                                        <p class="text-xs text-slate-600">{{ $pkg->description() }}</p>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- STEP 2: MOTORCYCLE IDENTIFICATION -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                        <div class="pb-4 border-b border-slate-100">
                            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">2</span>
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
                                       x-model="bikeMakeModel"
                                       value="{{ old('bike_make_model', $selectedBike ? ($selectedBike->brand . ' ' . $selectedBike->name) : '') }}"
                                       placeholder="e.g. Yamaha R15 V4 / Royal Enfield Hunter 350"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500 transition">
                            </div>

                            <div>
                                <label for="registration_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Vehicle Registration Plate <span class="text-rose-600">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 font-bold text-[10px] text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 uppercase font-mono">IND</span>
                                    <input type="text" name="registration_number" id="registration_number" required
                                           x-model="registrationNumber"
                                           value="{{ old('registration_number') }}"
                                           placeholder="KA-05-EM-9921"
                                           class="w-full pl-14 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm uppercase font-mono tracking-wider focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500 transition">
                                </div>
                            </div>

                            <div>
                                <label for="odometer_reading" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Current Odometer (KM) <span class="text-slate-400 font-normal lowercase">(optional)</span>
                                </label>
                                <input type="number" name="odometer_reading" id="odometer_reading" min="0" max="500000"
                                       value="{{ old('odometer_reading') }}"
                                       placeholder="e.g. 12500"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm font-mono focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500 transition">
                            </div>

                            <div>
                                <label for="bike_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Inventory Model Match <span class="text-slate-400 font-normal lowercase">(optional)</span>
                                </label>
                                <select name="bike_id" id="bike_id"
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-sm focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500 transition">
                                    <option value="">-- Other Model / Non-Inventory --</option>
                                    @foreach ($showroom->publishedBikes as $bike)
                                        <option value="{{ $bike->id }}" {{ (old('bike_id') == $bike->id || ($selectedBike && $selectedBike->id === $bike->id)) ? 'selected' : '' }}>
                                            {{ $bike->brand }} {{ $bike->name }} ({{ $bike->model_year }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: DATE & BAY SLOT SCHEDULING -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                        <div class="pb-4 border-b border-slate-100">
                            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">3</span>
                                <span>Appointment Date & Workshop Bay</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-1">Select your preferred date to view real-time concurrent bay availability.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                            <div class="space-y-2">
                                <label for="booking_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Select Date <span class="text-rose-600">*</span>
                                </label>
                                <input type="date" name="booking_date" id="booking_date" required
                                       min="{{ now()->format('Y-m-d') }}"
                                       x-model="bookingDate"
                                       value="{{ old('booking_date', $selectedDate) }}"
                                       onchange="fetchSlotsForDate(this.value)"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-sm focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500 transition">
                                <p class="text-[11px] text-slate-500">Service bays are open Monday to Saturday.</p>
                            </div>

                            <!-- Live Slots Radio Grid -->
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Available Time Window <span class="text-rose-600">*</span>
                                </label>

                                <div class="grid grid-cols-2 gap-3" id="slots-container">
                                    @foreach ($slots as $slotMeta)
                                        @php
                                            $isAvail = $slotMeta['available'];
                                            $rem = $slotMeta['remaining'];
                                        @endphp
                                        <label class="p-3.5 rounded-xl border flex flex-col justify-between transition cursor-pointer select-none
                                            {{ $isAvail ? 'bg-white border-slate-200 hover:border-emerald-500/50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50 shadow-2xs' : 'bg-slate-100 border-slate-200 opacity-50 cursor-not-allowed' }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <input type="radio" name="time_slot" value="{{ $slotMeta['slot'] }}"
                                                           @click="timeSlot = '{{ $slotMeta['slot'] }}'"
                                                           {{ old('time_slot', '09:00 - 11:00') === $slotMeta['slot'] ? 'checked' : '' }}
                                                           {{ $isAvail ? '' : 'disabled' }}
                                                           class="w-4 h-4 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                                    <span class="text-xs font-bold font-mono text-slate-900">{{ $slotMeta['slot'] }}</span>
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

                    <!-- STEP 4: CUSTOMER CONTACT & REPAIR NOTES -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                        <div class="pb-4 border-b border-slate-100">
                            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-black flex items-center justify-center">4</span>
                                <span>Customer Information & Special Instructions</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-1">We'll send your digital tracking job card reference via SMS and Email.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="customer_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Full Name <span class="text-rose-600">*</span>
                                </label>
                                <input type="text" name="customer_name" id="customer_name" required
                                       value="{{ old('customer_name', auth()->user()?->name) }}"
                                       placeholder="e.g. Ramesh Kumar"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500 transition">
                            </div>

                            <div>
                                <label for="customer_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Email Address <span class="text-rose-600">*</span>
                                </label>
                                <input type="email" name="customer_email" id="customer_email" required
                                       value="{{ old('customer_email', auth()->user()?->email) }}"
                                       placeholder="e.g. ramesh@example.com"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500 transition">
                            </div>

                            <div>
                                <label for="customer_phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Mobile Phone <span class="text-rose-600">*</span>
                                </label>
                                <input type="tel" name="customer_phone" id="customer_phone" required
                                       value="{{ old('customer_phone', auth()->user()?->phone) }}"
                                       placeholder="e.g. +91 9876543210"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm font-mono focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500 transition">
                            </div>
                        </div>

                        <div>
                            <label for="customer_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Technician Diagnostic Instructions & Fault Notes <span class="text-slate-400 font-normal lowercase">(optional)</span>
                            </label>
                            <textarea name="customer_notes" id="customer_notes" rows="3"
                                      placeholder="Describe any abnormal brake noise, engine hesitation, chain slack, electrical issues, or custom accessories..."
                                      class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500 transition">{{ old('customer_notes') }}</textarea>
                        </div>
                    </div>

                    <!-- TRUST PILLARS FOOTER -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-xs text-slate-900">100% OEM Parts</p>
                                <p class="text-[10px] text-slate-500">Genuine components only</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-xs text-slate-900">Diagnostic Scan</p>
                                <p class="text-[10px] text-slate-500">ECU computerized check</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-xs text-slate-900">Transparent Estimate</p>
                                <p class="text-[10px] text-slate-500">No surprise hidden bills</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-xs text-slate-900">Live Bay Tracker</p>
                                <p class="text-[10px] text-slate-500">Real-time status updates</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ============================================== -->
                <!-- RIGHT COLUMN: STICKY BOOKING SUMMARY & CHECKOUT-->
                <!-- ============================================== -->
                <div class="lg:col-span-4 sticky top-24 space-y-5">

                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-md space-y-5 relative overflow-hidden">
                        <div class="absolute -right-12 -top-12 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

                        <!-- Card Header -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 uppercase tracking-wider">
                                    Appointment Estimate
                                </span>
                                <h3 class="text-lg font-black font-display text-slate-900 mt-1">Booking Overview</h3>
                            </div>
                            <span class="text-xs font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                Instant Bay Lock
                            </span>
                        </div>

                        <!-- Selected Package Summary -->
                        <div class="p-4 rounded-2xl bg-slate-50/90 border border-slate-200 space-y-2.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Package Details</span>
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-900 text-sm" x-text="selectedServiceName"></h4>
                                <span class="text-xs font-bold text-slate-500" x-text="'~' + selectedDuration"></span>
                            </div>

                            <!-- Payment Gate Requirement Pill -->
                            <div class="pt-1">
                                <div x-show="isPaymentRequired" class="p-2 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-2 text-xs font-bold text-emerald-800">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    <span>Online Advance Required via Razorpay</span>
                                </div>
                                <div x-show="!isPaymentRequired" class="p-2 rounded-xl bg-slate-100 border border-slate-200 flex items-center gap-2 text-xs font-bold text-slate-700">
                                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Pay at Workshop Bay Upon Delivery</span>
                                </div>
                            </div>
                        </div>

                        <!-- Scheduled Slot & Motorcycle Summary -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2 text-slate-600">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Date & Slot:</span>
                                <span class="font-bold text-slate-900" x-text="bookingDate + ' (' + timeSlot + ')'"></span>
                            </div>
                            <div class="flex items-center justify-between" x-show="bikeMakeModel">
                                <span class="text-slate-500">Motorcycle:</span>
                                <span class="font-bold text-slate-900 truncate max-w-[160px]" x-text="bikeMakeModel"></span>
                            </div>
                            <div class="flex items-center justify-between" x-show="registrationNumber">
                                <span class="text-slate-500">Plate #:</span>
                                <span class="font-mono font-bold text-slate-800 bg-white px-1.5 py-0.5 rounded border border-slate-200" x-text="registrationNumber"></span>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-2 pt-1 border-t border-slate-100 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span>Labor & Diagnostics</span>
                                <span class="font-mono text-slate-800" x-text="'₹' + Number(selectedPrice).toLocaleString('en-IN')"></span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Consumables & Inspection</span>
                                <span class="font-semibold text-emerald-700">Included</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Applicable Taxes</span>
                                <span class="font-semibold text-emerald-700">Included</span>
                            </div>

                            <div class="pt-3 border-t border-slate-200 flex items-baseline justify-between">
                                <div>
                                    <span class="text-xs text-slate-500 block">Total Due Amount</span>
                                    <span class="text-[10px] text-slate-400 font-mono">100% Transparent</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-3xl font-black font-display text-emerald-700">
                                        ₹<span x-text="Number(selectedPrice).toLocaleString('en-IN')"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & Conditions Acceptance Checkbox -->
                        <div class="p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-2">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="terms_accepted" id="terms_accepted_checkbox" value="1" required
                                       x-model="termsAccepted"
                                       class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 mt-0.5 cursor-pointer">
                                <div class="text-[11px] text-slate-700 leading-snug">
                                    <span class="font-bold text-slate-900 block">Accept Workshop Terms & Policies <span class="text-rose-600">*</span></span>
                                    <span>I agree to OEM parts standards, vehicle test ride authorization, and free 2-hr cancellation.</span>
                                </div>
                            </label>
                            <div class="text-right pt-0.5">
                                <button type="button" @click="termsModalOpen = true" class="text-[11px] text-emerald-700 hover:text-emerald-800 font-bold underline">
                                    Read Full Service Terms &amp; Policies
                                </button>
                            </div>
                        </div>

                        <!-- Big Submit Button -->
                        <div>
                            <button type="submit" id="btn-submit-service-booking"
                                    class="w-full py-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                                <span x-text="isPaymentRequired ? 'Confirm & Book Service Appointment' : 'Confirm Service Appointment'"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                            <p class="text-[10px] text-center text-slate-400 mt-2">
                                Instant confirmation • Pay online or at workshop counter • 100% Genuine
                            </p>
                        </div>
                    </div>

                    <!-- Dealership Support Contact -->
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs text-xs flex items-center justify-between">
                        <div>
                            <p class="font-bold text-slate-900">Need Assistance?</p>
                            <p class="text-slate-500 text-[11px]">Direct Service Desk Advisor</p>
                        </div>
                        @if ($showroom->phone)
                            <a href="tel:{{ $showroom->phone }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 font-bold text-slate-800 transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>{{ $showroom->phone }}</span>
                            </a>
                        @endif
                    </div>

                </div>

            </div>
        </form>

    </div>

    <!-- Terms and Conditions Modal (Light Theme) -->
    <div x-cloak x-show="termsModalOpen"
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="termsModalOpen = false"></div>
        <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 text-left shadow-2xl border border-slate-200 my-8 overflow-hidden"
                 @click.away="termsModalOpen = false">

                <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                            Dealership Policies
                        </span>
                        <h3 class="text-xl font-black font-display text-slate-900 mt-1">Workshop Terms &amp; Conditions</h3>
                        <p class="text-xs text-slate-500">{{ $showroom->name }} Workshop &amp; Service Center</p>
                    </div>
                    <button type="button" @click="termsModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="py-5 space-y-4 max-h-[60vh] overflow-y-auto text-xs text-slate-600 leading-relaxed pr-2">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-sm">1. Scope of Work &amp; Estimate Approvals</h4>
                        <p>
                            All service packages cover the labor and procedures outlined in the selected package. Any supplementary repairs, replacement worn parts, or consumables discovered during the vehicle diagnostic inspection will only be performed after verbal or digital approval from the customer.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-sm">2. Genuine OEM Parts &amp; Workmanship Warranty</h4>
                        <p>
                            The workshop strictly uses 100% genuine Original Equipment Manufacturer (OEM) parts and certified fluids. All mechanical repairs carry a standard 6-month / 5,000 km workmanship warranty against installation defects.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-sm">3. Advance Payments, Cancellation &amp; Rescheduling</h4>
                        <p>
                            For packages requiring online advance payment, funds are held securely via Razorpay. Appointments can be rescheduled or cancelled with 100% full refund up to 2 hours before the scheduled bay time slot.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-sm">4. Vehicle Custody &amp; Diagnostic Road Testing</h4>
                        <p>
                            Authorized certified technicians are permitted to conduct road tests within manufacturer safety limits to calibrate fuel maps, evaluate braking efficiency, and verify repairs before hand-off. The dealership maintains active garage-keeper insurance coverage for all vehicles under repair.
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                    <button type="button" @click="termsAccepted = true; termsModalOpen = false"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs">
                        I Understand &amp; Accept Terms
                    </button>
                    <button type="button" @click="termsModalOpen = false"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
                        ${isAvail ? 'bg-white border-slate-200 hover:border-emerald-500/50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50 shadow-2xs' : 'bg-slate-100 border-slate-200 opacity-50 cursor-not-allowed'}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="time_slot" value="${slot.slot}"
                                       @click="timeSlot = '${slot.slot}'"
                                       ${isAvail ? '' : 'disabled'}
                                       class="w-4 h-4 text-emerald-600 border-slate-300 focus:ring-emerald-500">
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
