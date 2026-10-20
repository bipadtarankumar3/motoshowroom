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
        $timeframe = $request->query('timeframe');
        $date = $request->query('date');
        $assignedTo = $request->query('assigned_to');

        $query = $showroom->enquiries()
            ->with(['bike', 'assignedStaff'])
            ->search($search)
            ->filterType($type)
            ->filterStatus($status)
            ->when(! empty($assignedTo) && $assignedTo !== 'all', fn ($q) => $q->where('assigned_to_user_id', $assignedTo))
            ->when(! empty($date), fn ($q) => $q->whereDate('preferred_date', $date))
            ->when(! empty($timeframe) && $timeframe !== 'all', function ($q) use ($timeframe) {
                match ($timeframe) {
                    'today' => $q->where(function ($sub) {
                        $sub->whereDate('preferred_date', today())
                            ->orWhere(function ($sub2) {
                                $sub2->whereNull('preferred_date')->whereDate('created_at', today());
                            });
                    }),
                    'tomorrow' => $q->whereDate('preferred_date', today()->addDay()),
                    'this_week' => $q->where(function ($sub) {
                        $sub->whereBetween('preferred_date', [today()->startOfWeek(), today()->endOfWeek()])
                            ->orWhereBetween('created_at', [today()->startOfWeek(), today()->endOfWeek()]);
                    }),
                    'upcoming' => $q->whereDate('preferred_date', '>=', today()),
                    'past' => $q->whereDate('preferred_date', '<', today()),
                    'new' => $q->where('status', EnquiryStatus::New->value),
                    'scheduled' => $q->where('status', EnquiryStatus::TestRideScheduled->value),
                    'booked' => $q->whereIn('status', [EnquiryStatus::Booked->value, EnquiryStatus::Completed->value]),
                    default => null,
                };
            })
            ->latest();

        $enquiries = $query->paginate(15)->withQueryString();

        $totalCount = $showroom->enquiries()->count();
        $todayCount = $showroom->enquiries()->where(function ($sub) {
            $sub->whereDate('preferred_date', today())
                ->orWhere(function ($sub2) {
                    $sub2->whereNull('preferred_date')->whereDate('created_at', today());
                });
        })->count();
        $tomorrowCount = $showroom->enquiries()->whereDate('preferred_date', today()->addDay())->count();
        $thisWeekCount = $showroom->enquiries()->where(function ($sub) {
            $sub->whereBetween('preferred_date', [today()->startOfWeek(), today()->endOfWeek()])
                ->orWhereBetween('created_at', [today()->startOfWeek(), today()->endOfWeek()]);
        })->count();
        $newCount = $showroom->enquiries()->where('status', EnquiryStatus::New->value)->count();
        $scheduledCount = $showroom->enquiries()->where('status', EnquiryStatus::TestRideScheduled->value)->count();
        $completedCount = $showroom->enquiries()->whereIn('status', [
            EnquiryStatus::Completed->value,
            EnquiryStatus::Booked->value,
        ])->count();

        $types = EnquiryType::cases();
        $statuses = EnquiryStatus::cases();
        $staffMembers = $showroom->users()->get();

        return view('showrooms.manage.enquiries.index', compact(
            'showroom',
            'enquiries',
            'totalCount',
            'todayCount',
            'tomorrowCount',
            'thisWeekCount',
            'newCount',
            'scheduledCount',
            'completedCount',
            'types',
            'statuses',
            'staffMembers',
            'search',
            'type',
            'status',
            'timeframe',
            'date',
            'assignedTo'
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

    /**
     * Delete multiple selected enquiries.
     */
    public function bulkDestroy(Request $request, Showroom $showroom): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:enquiries,id'],
        ]);

        $count = $showroom->enquiries()->whereIn('id', $validated['ids'])->delete();

        AuditLogger::log('enquiry.bulk_deleted', null, ['ids' => $validated['ids']], null, $showroom->id);

        return redirect()->route('showrooms.manage.enquiries.index', $showroom)
            ->with('success', "{$count} enquiry/lead(s) removed.");
    }

    /**
     * Export showroom enquiries to CSV.
     */
    public function export(Request $request, Showroom $showroom)
    {
        $query = $showroom->enquiries()->with(['bike', 'assignedTo']);

        if ($request->filled('ids')) {
            $ids = is_array($request->input('ids'))
                ? $request->input('ids')
                : explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } else {
            if ($request->filled('search')) {
                $query->search($request->input('search'));
            }
            if ($request->filled('type')) {
                $query->where('type', $request->input('type'));
            }
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
        }

        $enquiries = $query->latest()->get();
        $filename = Str::slug($showroom->name).'_enquiries_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($enquiries) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Ref No', 'Customer Name', 'Phone', 'Email', 'Type', 'Motorcycle', 'Status', 'Scheduled At', 'Assigned Staff', 'Created At']);

            foreach ($enquiries as $e) {
                fputcsv($handle, [
                    $e->enquiry_number,
                    $e->name,
                    $e->phone,
                    $e->email ?? 'N/A',
                    $e->type?->value ?? $e->type,
                    $e->bike?->name ?? 'General Enquiry',
                    $e->status?->value ?? $e->status,
                    $e->scheduled_at?->format('Y-m-d H:i') ?? 'N/A',
                    $e->assignedTo?->name ?? 'Unassigned',
                    $e->created_at?->format('Y-m-d H:i') ?? 'N/A',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
