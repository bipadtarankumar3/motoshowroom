<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Showroom;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    /**
     * Display a paginated listing of customer enquiries and test rides for showroom staff.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $search = $request->query('search');
        $type = $request->query('type');
        $status = $request->query('status');

        $query = $showroom->enquiries()
            ->with(['bike', 'assignedStaff'])
            ->search($search)
            ->filterType($type)
            ->filterStatus($status)
            ->latest();

        $enquiries = $query->paginate(15)->withQueryString();

        $totalCount = $showroom->enquiries()->count();
        $newCount = $showroom->enquiries()->where('status', EnquiryStatus::New->value)->count();
        $scheduledCount = $showroom->enquiries()->where('status', EnquiryStatus::TestRideScheduled->value)->count();
        $completedCount = $showroom->enquiries()->whereIn('status', [
            EnquiryStatus::Completed->value,
            EnquiryStatus::Booked->value,
        ])->count();

        $types = EnquiryType::cases();
        $statuses = EnquiryStatus::cases();

        return view('showrooms.manage.enquiries.index', compact(
            'showroom',
            'enquiries',
            'totalCount',
            'newCount',
            'scheduledCount',
            'completedCount',
            'types',
            'statuses',
            'search',
            'type',
            'status'
        ));
    }

    /**
     * Display the specified customer enquiry detail.
     */
    public function show(Showroom $showroom, Enquiry $enquiry): View
    {
        abort_if($enquiry->tenant_id !== $showroom->id, 404);

        $enquiry->load(['bike.images', 'assignedStaff', 'user']);
        $statuses = EnquiryStatus::cases();
        $staffMembers = $showroom->users()->get();

        return view('showrooms.manage.enquiries.show', compact('showroom', 'enquiry', 'statuses', 'staffMembers'));
    }

    /**
     * Update the lead lifecycle status, staff assignment, or follow-up notes.
     */
    public function update(Request $request, Showroom $showroom, Enquiry $enquiry): RedirectResponse
    {
        abort_if($enquiry->tenant_id !== $showroom->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(array_column(EnquiryStatus::cases(), 'value'))],
            'assigned_to_user_id' => [
                'nullable',
                Rule::exists('showroom_memberships', 'user_id')->where('showroom_id', $showroom->id),
            ],
            'staff_notes' => ['nullable', 'string', 'max:2500'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $oldValues = $enquiry->only(['status', 'assigned_to_user_id', 'staff_notes', 'scheduled_at']);

        // Automatic timestamp milestones
        $updates = [
            'status' => $validated['status'],
            'assigned_to_user_id' => $validated['assigned_to_user_id'] ?? null,
            'staff_notes' => $validated['staff_notes'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? $enquiry->scheduled_at,
        ];

        if ($validated['status'] === EnquiryStatus::Contacted->value && empty($enquiry->contacted_at)) {
            $updates['contacted_at'] = now();
        }

        if (in_array($validated['status'], [EnquiryStatus::Completed->value, EnquiryStatus::Booked->value], true) && empty($enquiry->completed_at)) {
            $updates['completed_at'] = now();
        }

        $enquiry->update($updates);

        AuditLogger::log(
            'enquiry.updated',
            $enquiry,
            $oldValues,
            $enquiry->fresh()->only(['status', 'assigned_to_user_id', 'staff_notes', 'scheduled_at']),
            $showroom->id
        );

        return redirect()->back()->with(
            'success',
            "Enquiry #{$enquiry->enquiry_number} updated to \"{$enquiry->fresh()->status->label()}\"."
        );
    }

    /**
     * Remove the specified enquiry from dealership ledger (soft delete).
     */
    public function destroy(Showroom $showroom, Enquiry $enquiry): RedirectResponse
    {
        abort_if($enquiry->tenant_id !== $showroom->id, 404);

        $num = $enquiry->enquiry_number;
        $enquiry->delete();

        AuditLogger::log('enquiry.deleted', $enquiry, ['enquiry_number' => $num], null, $showroom->id);

        return redirect()->route('showrooms.manage.enquiries.index', $showroom)
            ->with('success', "Enquiry #{$num} removed.");
    }
}
