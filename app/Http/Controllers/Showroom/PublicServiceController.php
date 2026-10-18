<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\ServiceType;
use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PublicServiceController extends Controller
{
    /**
     * Display service appointment booking form with packages and real-time bay availability.
     */
    public function create(Request $request, Showroom $showroom): View
    {
        $selectedDate = $request->query('date', now()->addDay()->format('Y-m-d'));
        $packages = ServiceType::cases();
        $selectedPackage = $request->query('package', ServiceType::GeneralService->value);
        if (! in_array($selectedPackage, array_column(ServiceType::cases(), 'value'), true)) {
            $selectedPackage = ServiceType::GeneralService->value;
        }

        $bikeSlug = $request->query('bike');
        $selectedBike = null;
        if (! empty($bikeSlug)) {
            $selectedBike = $showroom->publishedBikes()->where('slug', $bikeSlug)->first();
        }

        $slots = $this->buildSlotAvailability($showroom, $selectedDate);
        $capacity = $showroom->service_bay_capacity;

        return view('showrooms.services.create', compact(
            'showroom',
            'packages',
            'selectedPackage',
            'selectedBike',
            'selectedDate',
            'slots',
            'capacity'
        ));
    }

    /**
     * API endpoint returning slot availability counts for a selected calendar date.
     */
    public function slots(Request $request, Showroom $showroom): JsonResponse
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        $slots = $this->buildSlotAvailability($showroom, $date);

        return response()->json([
            'date' => $date,
            'capacity' => $showroom->service_bay_capacity,
            'slots' => $slots,
        ]);
    }

    /**
     * Store newly requested service booking appointment and prevent bay concurrency overbooking.
     */
    public function store(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'bike_make_model' => ['required', 'string', 'max:150'],
            'registration_number' => ['required', 'string', 'max:50'],
            'odometer_reading' => ['nullable', 'integer', 'min:0', 'max:500000'],
            'service_type' => ['required', 'string', Rule::in(array_column(ServiceType::cases(), 'value'))],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot' => ['required', 'string', Rule::in(ServiceBooking::TIME_SLOTS)],
            'bike_id' => [
                'nullable',
                Rule::exists('bikes', 'id')->where('tenant_id', $showroom->id),
            ],
            'customer_notes' => ['nullable', 'string', 'max:1500'],
        ]);

        // Concurrency Guard: Ensure showroom bay capacity is not exceeded for the requested slot
        if (! ServiceBooking::isSlotAvailable($showroom, $validated['booking_date'], $validated['time_slot'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'time_slot' => 'The selected time slot is fully booked for this date (all service bays occupied). Please select another slot or calendar day.',
                ]);
        }

        $serviceTypeEnum = ServiceType::from($validated['service_type']);

        $booking = $showroom->serviceBookings()->create([
            'user_id' => Auth::id(),
            'bike_id' => $validated['bike_id'] ?? null,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'bike_make_model' => $validated['bike_make_model'],
            'registration_number' => strtoupper(trim($validated['registration_number'])),
            'odometer_reading' => $validated['odometer_reading'] ?? null,
            'service_type' => $validated['service_type'],
            'booking_date' => $validated['booking_date'],
            'time_slot' => $validated['time_slot'],
            'status' => BookingStatus::Pending->value,
            'estimated_cost' => $serviceTypeEnum->estimatedPrice(),
            'payment_status' => PaymentStatus::Pending->value,
            'customer_notes' => $validated['customer_notes'] ?? null,
        ]);

        AuditLogger::log(
            'service_booking.created',
            $booking,
            null,
            [
                'booking_number' => $booking->booking_number,
                'customer_name' => $booking->customer_name,
                'registration_number' => $booking->registration_number,
                'service_type' => $booking->service_type->value,
                'booking_date' => $booking->booking_date->format('Y-m-d'),
                'time_slot' => $booking->time_slot,
            ],
            $showroom->id,
            Auth::user()
        );

        return redirect()->route('showrooms.services.track', [
            'showroom' => $showroom,
            'booking' => $booking->booking_number,
        ])->with(
            'success',
            "Appointment successfully scheduled! Your service tracking reference is #{$booking->booking_number}."
        );
    }

    /**
     * Customer service status tracker and live repair milestone progress.
     */
    public function track(Request $request, Showroom $showroom, ?string $booking = null): View
    {
        $searchRef = $booking ?? $request->query('booking');
        $searched = false;
        $bookingRecord = null;

        if (! empty($searchRef)) {
            $searched = true;
            $bookingRecord = $showroom->serviceBookings()
                ->where('booking_number', strtoupper(trim($searchRef)))
                ->orWhere(function ($query) use ($showroom, $searchRef): void {
                    $query->where('tenant_id', $showroom->id)
                        ->where('registration_number', strtoupper(trim($searchRef)));
                })
                ->latest()
                ->first();
        }

        return view('showrooms.services.track', compact('showroom', 'bookingRecord', 'searchRef', 'searched'));
    }

    /**
     * Helper to compute slot occupancy and availability.
     *
     * @return array<string, array{slot: string, occupied: int, capacity: int, available: bool, remaining: int}>
     */
    protected function buildSlotAvailability(Showroom $showroom, string $date): array
    {
        $capacity = $showroom->service_bay_capacity;
        $slots = [];

        foreach (ServiceBooking::TIME_SLOTS as $slot) {
            $occupied = ServiceBooking::getSlotOccupancy($showroom->id, $date, $slot);
            $remaining = max(0, $capacity - $occupied);

            $slots[$slot] = [
                'slot' => $slot,
                'occupied' => $occupied,
                'capacity' => $capacity,
                'available' => $remaining > 0,
                'remaining' => $remaining,
            ];
        }

        return $slots;
    }
}
