<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\ServiceType;
use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Services\AuditLogger;
use App\Services\ShowroomNotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceBookingController extends Controller
{
    /**
     * Display a listing of service appointments and live workshop bays for dealership staff.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $search = $request->query('search');
        $date = $request->query('date');
        $status = $request->query('status');
        $serviceType = $request->query('service_type');

        $query = $showroom->serviceBookings()
            ->with(['assignedTechnician', 'user', 'bike'])
            ->search($search)
            ->filterDate($date)
            ->filterStatus($status)
            ->when(! empty($serviceType) && $serviceType !== 'all', fn ($q) => $q->where('service_type', $serviceType))
            ->latest('booking_date');

        $bookings = $query->paginate(15)->withQueryString();

        $todayTotal = $showroom->serviceBookings()->whereDate('booking_date', today())->count();
        $inServiceCount = $showroom->serviceBookings()->where('status', BookingStatus::InService->value)->count();
        $readyCount = $showroom->serviceBookings()->where('status', BookingStatus::ReadyForCollection->value)->count();
        $pendingCount = $showroom->serviceBookings()->where('status', BookingStatus::Pending->value)->count();

        $showroom->seedDefaultServicesIfNeeded();
        $customServices = $showroom->services()->orderBy('sort_order')->orderBy('id')->get();
        $totalServicesCount = $customServices->count();
        $paymentEnabledCount = $customServices->where('is_payment_enabled', true)->count();
        $payAtBayCount = $customServices->where('is_payment_enabled', false)->count();
        $activeCatalogCount = $customServices->where('is_active', true)->count();
        $activeTab = $request->query('tab', 'bookings');

        $statuses = BookingStatus::cases();
        $types = ServiceType::cases();
        $technicians = $showroom->users()->get();

        return view('showrooms.manage.services.index', compact(
            'showroom',
            'bookings',
            'todayTotal',
            'inServiceCount',
            'readyCount',
            'pendingCount',
            'statuses',
            'types',
            'technicians',
            'search',
            'date',
            'status',
            'serviceType',
            'customServices',
            'totalServicesCount',
            'paymentEnabledCount',
            'payAtBayCount',
            'activeCatalogCount',
            'activeTab'
        ));
    }

    /**
     * Display the specified service booking job card.
     */
    public function show(Showroom $showroom, ServiceBooking $booking): View
    {
        abort_if($booking->tenant_id !== $showroom->id, 404);

        $booking->load(['assignedTechnician', 'user', 'bike.images']);
        $statuses = BookingStatus::cases();
        $paymentStatuses = PaymentStatus::cases();
        $technicians = $showroom->users()->get();

        return view('showrooms.manage.services.show', compact(
            'showroom',
            'booking',
            'statuses',
            'paymentStatuses',
            'technicians'
        ));
    }

    /**
     * Update the workshop service lifecycle status, bay number, technician, and job card notes.
     */
    public function update(Request $request, Showroom $showroom, ServiceBooking $booking): RedirectResponse
    {
        abort_if($booking->tenant_id !== $showroom->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(array_column(BookingStatus::cases(), 'value'))],
            'bay_number' => ['nullable', 'string', 'max:30'],
            'assigned_to_user_id' => [
                'nullable',
                Rule::exists('showroom_memberships', 'user_id')->where('showroom_id', $showroom->id),
            ],
            'final_cost' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['nullable', 'string', Rule::in(array_column(PaymentStatus::cases(), 'value'))],
            'technician_notes' => ['nullable', 'string', 'max:2500'],
        ]);

        $oldValues = $booking->only([
            'status',
            'bay_number',
            'assigned_to_user_id',
            'final_cost',
            'payment_status',
            'technician_notes',
        ]);

        $updates = [
            'status' => $validated['status'],
            'bay_number' => $validated['bay_number'] ?? $booking->bay_number,
            'assigned_to_user_id' => $validated['assigned_to_user_id'] ?? null,
            'technician_notes' => $validated['technician_notes'] ?? null,
        ];

        if (array_key_exists('final_cost', $validated) && $validated['final_cost'] !== null) {
            $updates['final_cost'] = $validated['final_cost'];
        }

        if (! empty($validated['payment_status'])) {
            $updates['payment_status'] = $validated['payment_status'];
        }

        // Automatic timestamp milestone progression
        $newStatus = $validated['status'];
        if ($newStatus === BookingStatus::Confirmed->value && empty($booking->confirmed_at)) {
            $updates['confirmed_at'] = now();
        }
        if ($newStatus === BookingStatus::CheckedIn->value && empty($booking->checked_in_at)) {
            $updates['checked_in_at'] = now();
        }
        if ($newStatus === BookingStatus::InService->value && empty($booking->in_service_at)) {
            $updates['in_service_at'] = now();
        }
        if ($newStatus === BookingStatus::ReadyForCollection->value && empty($booking->ready_at)) {
            $updates['ready_at'] = now();
        }
        if ($newStatus === BookingStatus::Completed->value && empty($booking->completed_at)) {
            $updates['completed_at'] = now();
        }

        $booking->update($updates);

        AuditLogger::log(
            'service_booking.updated',
            $booking,
            $oldValues,
            $booking->fresh()->only([
                'status',
                'bay_number',
                'assigned_to_user_id',
                'final_cost',
                'payment_status',
                'technician_notes',
            ]),
            $showroom->id
        );

        if ($oldValues['status'] !== $validated['status']) {
            app(ShowroomNotificationService::class)->notifyServiceStatusUpdated($booking->fresh());
        }

        return redirect()->back()->with(
            'success',
            "Service Job #{$booking->booking_number} updated to \"{$booking->fresh()->status->label()}\"."
        );
    }

    /**
     * Remove the specified service booking (soft delete).
     */
    public function destroy(Showroom $showroom, ServiceBooking $booking): RedirectResponse
    {
        abort_if($booking->tenant_id !== $showroom->id, 404);

        $num = $booking->booking_number;
        $booking->delete();

        AuditLogger::log('service_booking.deleted', $booking, ['booking_number' => $num], null, $showroom->id);

        return redirect()->route('showrooms.manage.services.index', $showroom)
            ->with('success', "Service booking #{$num} removed.");
    }

    /**
     * Delete multiple selected service bookings.
     */
    public function bulkDestroy(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:service_bookings,id'],
        ]);

        $count = $showroom->serviceBookings()->whereIn('id', $validated['ids'])->delete();

        AuditLogger::log('service_booking.bulk_deleted', null, ['ids' => $validated['ids']], null, $showroom->id);

        return redirect()->route('showrooms.manage.services.index', $showroom)
            ->with('success', "{$count} service booking(s) removed.");
    }

    /**
     * Export showroom service bookings to CSV.
     */
    public function export(Request $request, Showroom $showroom)
    {
        $query = $showroom->serviceBookings()->with(['assignedTechnician']);

        if ($request->filled('ids')) {
            $ids = is_array($request->input('ids'))
                ? $request->input('ids')
                : explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } else {
            if ($request->filled('search')) {
                $query->search($request->input('search'));
            }
            if ($request->filled('status')) {
                $query->filterStatus($request->input('status'));
            }
            if ($request->filled('bay')) {
                $query->filterBay((int) $request->input('bay'));
            }
        }

        $bookings = $query->latest()->get();
        $filename = Str::slug($showroom->name).'_services_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Booking Ref', 'Customer Name', 'Phone', 'Motorcycle', 'Reg Plate', 'Service Type', 'Date', 'Time Slot', 'Bay', 'Status', 'Payment', 'Cost']);

            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $b->booking_number,
                    $b->customer_name,
                    $b->customer_phone,
                    $b->bike_make_model,
                    $b->registration_number ?? 'N/A',
                    $b->service_type,
                    $b->booking_date?->format('Y-m-d') ?? 'N/A',
                    $b->time_slot,
                    $b->bay_number ? "Bay {$b->bay_number}" : 'Unassigned',
                    $b->status?->value ?? $b->status,
                    $b->payment_status?->value ?? $b->payment_status,
                    $b->final_cost ?? $b->estimated_cost ?? 0,
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
