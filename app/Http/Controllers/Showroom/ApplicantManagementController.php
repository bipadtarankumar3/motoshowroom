<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Showroom;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicantManagementController extends Controller
{
    /**
     * Display a listing of candidate job applications (Applicant Tracking System ledger).
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $jobOpeningId = $request->query('job_opening_id');

        $query = $showroom->jobApplications()
            ->with(['jobOpening'])
            ->search($search)
            ->filterStatus($status);

        if (! empty($jobOpeningId) && $jobOpeningId !== 'all') {
            $query->where('job_opening_id', $jobOpeningId);
        }

        $applications = $query->latest()->paginate(15)->withQueryString();

        $totalCount = $showroom->jobApplications()->count();
        $newCount = $showroom->jobApplications()->where('status', ApplicationStatus::New->value)->count();
        $shortlistedCount = $showroom->jobApplications()->whereIn('status', [
            ApplicationStatus::Reviewing->value,
            ApplicationStatus::Shortlisted->value,
        ])->count();
        $interviewCount = $showroom->jobApplications()->where('status', ApplicationStatus::InterviewScheduled->value)->count();
        $hiredCount = $showroom->jobApplications()->where('status', ApplicationStatus::Hired->value)->count();

        $statuses = ApplicationStatus::cases();
        $jobOpenings = $showroom->jobOpenings()->select(['id', 'title'])->orderBy('title')->get();

        return view('showrooms.manage.careers.applicants.index', compact(
            'showroom',
            'applications',
            'totalCount',
            'newCount',
            'shortlistedCount',
            'interviewCount',
            'hiredCount',
            'statuses',
            'jobOpenings',
            'search',
            'status',
            'jobOpeningId'
        ));
    }

    /**
     * Display candidate profile, resume details, and recruitment workflow actions.
     */
    public function show(Showroom $showroom, JobApplication $applicant): View
    {
        abort_if($applicant->tenant_id !== $showroom->id, 404);

        $applicant->load(['jobOpening', 'user']);
        $statuses = ApplicationStatus::cases();

        return view('showrooms.manage.careers.applicants.show', compact('showroom', 'applicant', 'statuses'));
    }

    /**
     * Update the candidate status, schedule interview, or append recruiter notes.
     */
    public function update(Request $request, Showroom $showroom, JobApplication $applicant): RedirectResponse
    {
        abort_if($applicant->tenant_id !== $showroom->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(array_column(ApplicationStatus::cases(), 'value'))],
            'interview_at' => ['nullable', 'date'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldValues = $applicant->only(['status', 'interview_at', 'internal_notes', 'rejection_reason']);

        $applicant->update([
            'status' => $validated['status'],
            'interview_at' => $validated['interview_at'] ?? $applicant->interview_at,
            'internal_notes' => $validated['internal_notes'] ?? null,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        AuditLogger::log(
            'job_application.updated',
            $applicant,
            $oldValues,
            $applicant->fresh()->only(['status', 'interview_at', 'internal_notes', 'rejection_reason']),
            $showroom->id
        );

        return redirect()->back()->with(
            'success',
            "Applicant #{$applicant->applicant_number} ({$applicant->applicant_name}) updated to \"{$applicant->fresh()->status->label()}\"."
        );
    }

    /**
     * Remove the specified candidate application from the showroom records.
     */
    public function destroy(Showroom $showroom, JobApplication $applicant): RedirectResponse
    {
        abort_if($applicant->tenant_id !== $showroom->id, 404);

        $appNumber = $applicant->applicant_number;
        $applicant->delete();

        AuditLogger::log('job_application.deleted', $applicant, ['applicant_number' => $appNumber], null, $showroom->id);

        return redirect()->route('showrooms.manage.applicants.index', $showroom)
            ->with('success', "Application #{$appNumber} has been removed.");
    }
}
