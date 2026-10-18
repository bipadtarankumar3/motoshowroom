@extends('layouts.app')

@section('title', 'Enquiry #' . $enquiry->enquiry_number . ' - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-2">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-white transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.enquiries.index', $showroom) }}" class="hover:text-white transition">Enquiries</a>
            <span>/</span>
            <span class="text-slate-200">#{{ $enquiry->enquiry_number }}</span>
        </div>

        <!-- Flash Feedback Messages -->
        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-950/80 border border-emerald-500/40 p-4 text-emerald-300 flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Header Card -->
        <div class="glass-panel-dark rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-bold text-slate-400 bg-slate-900 border border-slate-800 px-2.5 py-1 rounded-lg">
                            {{ $enquiry->enquiry_number }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $enquiry->type->badgeClasses() }}">
                            {{ $enquiry->type->label() }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $enquiry->status->badgeClasses() }}">
                            {{ $enquiry->status->label() }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black font-display text-white mt-3">{{ $enquiry->customer_name }}</h1>
                    <p class="text-xs text-slate-400 mt-1">
                        Received on {{ $enquiry->created_at->format('M d, Y \a\t h:i A') }} ({{ $enquiry->created_at->diffForHumans() }})
                    </p>
                </div>

                <!-- Instant Communication Toolbar -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="tel:{{ $enquiry->customer_phone }}"
                       class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-200 hover:text-white transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>Call Customer</span>
                    </a>

                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $enquiry->customer_phone) }}?text={{ urlencode('Hello ' . $enquiry->customer_name . '! This is ' . $showroom->name . ' reaching out regarding your enquiry #' . $enquiry->enquiry_number . '.') }}"
                       target="_blank"
                       class="px-4 py-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 text-emerald-300 text-xs font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>WhatsApp</span>
                    </a>

                    <a href="mailto:{{ $enquiry->customer_email }}"
                       class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-200 hover:text-white transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Email</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Column: Lead Info, Motorcycle & Messages (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Customer Details Card -->
                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800">
                    <h2 class="text-sm uppercase font-bold text-slate-400 tracking-wider mb-4">Customer Contact Information</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800">
                            <dt class="text-slate-400">Full Name</dt>
                            <dd class="font-bold text-white text-sm mt-0.5">{{ $enquiry->customer_name }}</dd>
                        </div>
                        <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800">
                            <dt class="text-slate-400">Phone Number</dt>
                            <dd class="font-mono font-bold text-white text-sm mt-0.5">{{ $enquiry->customer_phone }}</dd>
                        </div>
                        <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800">
                            <dt class="text-slate-400">Email Address</dt>
                            <dd class="font-medium text-slate-200 text-sm mt-0.5">{{ $enquiry->customer_email }}</dd>
                        </div>
                        <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800">
                            <dt class="text-slate-400">User Account</dt>
                            <dd class="font-medium text-slate-200 text-sm mt-0.5">
                                @if($enquiry->user)
                                    <span class="text-emerald-400 font-semibold">Registered User (#{{ $enquiry->user_id }})</span>
                                @else
                                    <span class="text-slate-500">Guest Visitor</span>
                                @endif
                            </dd>
                        </div>

                        @if($enquiry->driving_license_number)
                            <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 sm:col-span-2">
                                <dt class="text-slate-400">Driving License Number (Verified for Test Ride)</dt>
                                <dd class="font-mono font-bold text-amber-400 text-sm mt-0.5">{{ $enquiry->driving_license_number }}</dd>
                            </div>
                        @endif

                        @if($enquiry->preferred_date)
                            <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 sm:col-span-2">
                                <dt class="text-slate-400">Customer Requested Date & Time</dt>
                                <dd class="font-semibold text-white mt-0.5">
                                    {{ $enquiry->preferred_date->format('l, F j, Y') }}
                                    @if($enquiry->preferred_time_slot)
                                        <span class="text-slate-400 ml-2">Slot: {{ $enquiry->preferred_time_slot }}</span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Motorcycle Information Card -->
                @if($enquiry->bike)
                    <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800">
                        <h2 class="text-sm uppercase font-bold text-slate-400 tracking-wider mb-4">Requested Motorcycle Model</h2>
                        <div class="flex items-center gap-4 bg-slate-900/80 p-4 rounded-xl border border-slate-800">
                            <div class="w-20 h-16 rounded-xl overflow-hidden bg-slate-950 border border-slate-800 flex-shrink-0">
                                <img src="{{ $enquiry->bike->primary_image_url }}" alt="{{ $enquiry->bike->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $enquiry->bike->brand }}</span>
                                    <span class="text-slate-600">•</span>
                                    <span class="font-mono text-xs text-slate-400 font-bold">SKU: {{ $enquiry->bike->sku }}</span>
                                </div>
                                <h3 class="font-display font-bold text-base text-white mt-0.5">{{ $enquiry->bike->name }}</h3>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="font-mono font-black text-rose-400 text-sm">{{ $enquiry->bike->formatted_price }}</span>
                                    <a href="{{ route('showrooms.manage.bikes.edit', [$showroom, $enquiry->bike]) }}" class="text-xs text-slate-300 hover:text-white underline">
                                        View in Inventory →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Customer Message / Notes -->
                @if($enquiry->customer_notes)
                    <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800">
                        <h2 class="text-sm uppercase font-bold text-slate-400 tracking-wider mb-2">Customer Requirements & Inquiries</h2>
                        <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 text-slate-300 text-sm leading-relaxed">
                            {{ $enquiry->customer_notes }}
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right Column: Lead Status & Staff Assignment Form (5 cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Management Action Box -->
                <div class="glass-panel-dark rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl">
                    <h2 class="text-base font-bold font-display text-white mb-6">Manage Lead Lifecycle</h2>

                    <form method="POST" action="{{ route('showrooms.manage.enquiries.update', [$showroom, $enquiry]) }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <!-- Lifecycle Status -->
                        <div>
                            <label for="status" class="block text-xs font-semibold text-slate-300 mb-1.5">Enquiry Status <span class="text-rose-400">*</span></label>
                            <select name="status" id="status" required
                                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-rose-500">
                                @foreach($statuses as $st)
                                    <option value="{{ $st->value }}" {{ old('status', $enquiry->status->value) === $st->value ? 'selected' : '' }}>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assign Staff Member -->
                        <div>
                            <label for="assigned_to_user_id" class="block text-xs font-semibold text-slate-300 mb-1.5">Assigned Staff Specialist</label>
                            <select name="assigned_to_user_id" id="assigned_to_user_id"
                                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-rose-500">
                                <option value="">-- Unassigned --</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ old('assigned_to_user_id', $enquiry->assigned_to_user_id) == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} ({{ $staff->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Scheduled Test Ride Appointment -->
                        <div>
                            <label for="scheduled_at" class="block text-xs font-semibold text-slate-300 mb-1.5">Confirmed Test Ride Schedule</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                                   value="{{ old('scheduled_at', $enquiry->scheduled_at ? $enquiry->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-rose-500 font-mono">
                            <span class="text-[10px] text-slate-500 mt-1 block">Set when test ride appointment is confirmed with customer.</span>
                        </div>

                        <!-- Staff Internal Notes -->
                        <div>
                            <label for="staff_notes" class="block text-xs font-semibold text-slate-300 mb-1.5">Dealership Internal Follow-up Notes</label>
                            <textarea name="staff_notes" id="staff_notes" rows="4"
                                      placeholder="Log customer preference, financing interest, test-ride feedback, or reason for status update..."
                                      class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">{{ old('staff_notes', $enquiry->staff_notes) }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-xs shadow-lg shadow-rose-600/30 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Save Lead Updates</span>
                        </button>
                    </form>
                </div>

                <!-- Milestone Timestamps -->
                <div class="glass-panel-dark rounded-2xl p-6 border border-slate-800 text-xs text-slate-400 space-y-2">
                    <span class="uppercase tracking-wider font-bold text-slate-500 text-[10px] block mb-2">Milestone History</span>
                    <div class="flex justify-between py-1 border-b border-slate-800/60">
                        <span>Lead Received</span>
                        <span class="text-white font-medium">{{ $enquiry->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($enquiry->contacted_at)
                        <div class="flex justify-between py-1 border-b border-slate-800/60">
                            <span>First Contact</span>
                            <span class="text-indigo-400 font-medium">{{ $enquiry->contacted_at->format('M d, Y h:i A') }}</span>
                        </div>
                    @endif
                    @if($enquiry->scheduled_at)
                        <div class="flex justify-between py-1 border-b border-slate-800/60">
                            <span>Ride Scheduled</span>
                            <span class="text-amber-400 font-medium">{{ $enquiry->scheduled_at->format('M d, Y h:i A') }}</span>
                        </div>
                    @endif
                    @if($enquiry->completed_at)
                        <div class="flex justify-between py-1">
                            <span>Booking / Delivery</span>
                            <span class="text-emerald-400 font-medium">{{ $enquiry->completed_at->format('M d, Y h:i A') }}</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </main>
</div>
@endsection
