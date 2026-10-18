@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 py-8">
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 space-y-6">

        <!-- Top Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.services.index', $showroom) }}" class="hover:text-slate-900 transition">Workshop</a>
            <span>/</span>
            <span class="text-slate-700 font-semibold">Job Card #{{ $booking->booking_number }}</span>
        </div>

        <!-- Notification Banner -->
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">
                        Job Card #{{ $booking->booking_number }}
                    </h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold {{ $booking->status->badgeClasses() }}">
                        {{ $booking->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    Booked on {{ $booking->created_at->format('M d, Y h:i A') }} • Scheduled for <span class="text-slate-900 font-medium">{{ $booking->booking_date->format('M d, Y') }}</span> ({{ $booking->time_slot }})
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('showrooms.services.track', [$showroom, 'booking' => $booking->booking_number]) }}" target="_blank"
                   class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-xs font-semibold text-sky-700 hover:text-sky-900 transition flex items-center gap-1.5 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Customer Tracker</span>
                </a>

                <form method="POST" action="{{ route('showrooms.manage.services.destroy', [$showroom, $booking]) }}"
                      onsubmit="return confirm('Archive this service appointment?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-semibold transition shadow-xs" title="Archive Job Card">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Col (2 cols): Workshop Job Controls & Form -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Quick Stage Progression Bar -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Quick Stage Progression</h3>
                    <div class="flex flex-wrap gap-2">
                        @if ($booking->status === \App\Enums\BookingStatus::Pending)
                            <form method="POST" action="{{ route('showrooms.manage.services.update', [$showroom, $booking]) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ \App\Enums\BookingStatus::Confirmed->value }}">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Confirm Bay Slot</span>
                                </button>
                            </form>
                        @endif

                        @if (in_array($booking->status, [\App\Enums\BookingStatus::Pending, \App\Enums\BookingStatus::Confirmed]))
                            <form method="POST" action="{{ route('showrooms.manage.services.update', [$showroom, $booking]) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ \App\Enums\BookingStatus::CheckedIn->value }}">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                                    <span>Check In Bike</span>
                                </button>
                            </form>
                        @endif

                        @if (in_array($booking->status, [\App\Enums\BookingStatus::Confirmed, \App\Enums\BookingStatus::CheckedIn]))
                            <form method="POST" action="{{ route('showrooms.manage.services.update', [$showroom, $booking]) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ \App\Enums\BookingStatus::InService->value }}">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Move into Bay (In Service)</span>
                                </button>
                            </form>
                        @endif

                        @if ($booking->status === \App\Enums\BookingStatus::InService)
                            <form method="POST" action="{{ route('showrooms.manage.services.update', [$showroom, $booking]) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ \App\Enums\BookingStatus::ReadyForCollection->value }}">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Mark Ready for Pickup</span>
                                </button>
                            </form>
                        @endif

                        @if ($booking->status === \App\Enums\BookingStatus::ReadyForCollection)
                            <form method="POST" action="{{ route('showrooms.manage.services.update', [$showroom, $booking]) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ \App\Enums\BookingStatus::Completed->value }}">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Deliver & Close Job Card</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Update Form -->
                <form method="POST" action="{{ route('showrooms.manage.services.update', [$showroom, $booking]) }}" class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
                    @csrf
                    @method('PUT')

                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Update Job Card & Status</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Status Selector -->
                        <div>
                            <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Current Lifecycle Status <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                @foreach ($statuses as $st)
                                    <option value="{{ $st->value }}" {{ $booking->status === $st ? 'selected' : '' }}>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bay Number -->
                        <div>
                            <label for="bay_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Assigned Workshop Bay
                            </label>
                            <input type="text" name="bay_number" id="bay_number"
                                   value="{{ old('bay_number', $booking->bay_number) }}"
                                   placeholder="e.g. Bay 1, Bay 2, Quick Lube Bay"
                                   class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 placeholder-slate-400 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                        </div>

                        <!-- Assigned Technician -->
                        <div>
                            <label for="assigned_to_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Assigned Technician / Mechanic
                            </label>
                            <select name="assigned_to_user_id" id="assigned_to_user_id"
                                    class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                <option value="">-- Unassigned --</option>
                                @foreach ($technicians as $staff)
                                    <option value="{{ $staff->id }}" {{ $booking->assigned_to_user_id === $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} ({{ $staff->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Final Cost -->
                        <div>
                            <label for="final_cost" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Final Invoiced Amount (₹)
                            </label>
                            <input type="number" step="0.01" name="final_cost" id="final_cost"
                                   value="{{ old('final_cost', $booking->final_cost ?? $booking->estimated_cost) }}"
                                   class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 font-mono focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Payment Settlement Status
                            </label>
                            <select name="payment_status" id="payment_status"
                                    class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
                                @foreach ($paymentStatuses as $ps)
                                    <option value="{{ $ps->value }}" {{ $booking->payment_status === $ps ? 'selected' : '' }}>
                                        {{ $ps->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Technician Diagnostic Notes -->
                    <div>
                        <label for="technician_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Technician Diagnostic Notes & Spares Used
                        </label>
                        <textarea name="technician_notes" id="technician_notes" rows="4"
                                  placeholder="Record parts replaced, torque specs checked, oil grades used, customer feedback..."
                                  class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 font-mono placeholder-slate-400 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">{{ old('technician_notes', $booking->technician_notes) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" id="btn-save-job-card"
                                class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-rose-600/20 transition">
                            Save Changes
                        </button>
                    </div>
                </form>

            </div>

            <!-- Right Col (1 col): Vehicle, Customer, and Milestone Log -->
            <div class="space-y-6">

                <!-- Vehicle Card -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Vehicle Details</h3>

                    <div class="space-y-2">
                        <div>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider">Motorcycle</span>
                            <p class="text-sm font-bold text-slate-900">{{ $booking->bike_make_model }}</p>
                        </div>

                        <div>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider">Registration Number</span>
                            <p class="text-xs font-mono font-bold text-rose-600">{{ $booking->registration_number }}</p>
                        </div>

                        @if ($booking->odometer_reading)
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase tracking-wider">Odometer Reading</span>
                                <p class="text-xs font-mono text-slate-700">{{ number_format($booking->odometer_reading) }} km</p>
                            </div>
                        @endif

                        <div>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider">Package</span>
                            <p class="text-xs text-emerald-700 font-semibold">{{ $booking->service_type->label() }}</p>
                        </div>

                        @if ($booking->customer_notes)
                            <div class="pt-2 border-t border-slate-100">
                                <span class="text-[10px] text-slate-500 uppercase tracking-wider">Customer Reported Symptoms</span>
                                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed italic">"{{ $booking->customer_notes }}"</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Customer Card -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Customer Details</h3>
                    <div class="space-y-2 text-xs">
                        <div>
                            <p class="font-bold text-slate-900 text-sm">{{ $booking->customer_name }}</p>
                            @if ($booking->user)
                                <span class="text-[10px] text-emerald-700 font-medium">Registered SaaS User</span>
                            @endif
                        </div>
                        <div class="pt-1">
                            <span class="text-slate-500 text-[10px] uppercase">Phone</span>
                            <p class="font-mono text-slate-700"><a href="tel:{{ $booking->customer_phone }}" class="hover:underline">{{ $booking->customer_phone }}</a></p>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[10px] uppercase">Email</span>
                            <p class="text-slate-700"><a href="mailto:{{ $booking->customer_email }}" class="hover:underline">{{ $booking->customer_email }}</a></p>
                        </div>
                    </div>
                </div>

                <!-- Milestone Timestamps Card -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Milestone History</h3>
                    <div class="space-y-2 text-[11px] font-mono text-slate-600">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Booked:</span>
                            <span class="text-slate-800">{{ $booking->created_at->format('M d, H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Confirmed:</span>
                            <span class="{{ $booking->confirmed_at ? 'text-blue-700' : 'text-slate-400' }}">
                                {{ $booking->confirmed_at ? $booking->confirmed_at->format('M d, H:i') : '--' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Checked In:</span>
                            <span class="{{ $booking->checked_in_at ? 'text-indigo-700' : 'text-slate-400' }}">
                                {{ $booking->checked_in_at ? $booking->checked_in_at->format('M d, H:i') : '--' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">In Service:</span>
                            <span class="{{ $booking->in_service_at ? 'text-amber-700' : 'text-slate-400' }}">
                                {{ $booking->in_service_at ? $booking->in_service_at->format('M d, H:i') : '--' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Ready for Pickup:</span>
                            <span class="{{ $booking->ready_at ? 'text-teal-700' : 'text-slate-400' }}">
                                {{ $booking->ready_at ? $booking->ready_at->format('M d, H:i') : '--' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Completed:</span>
                            <span class="{{ $booking->completed_at ? 'text-emerald-700' : 'text-slate-400' }}">
                                {{ $booking->completed_at ? $booking->completed_at->format('M d, H:i') : '--' }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
