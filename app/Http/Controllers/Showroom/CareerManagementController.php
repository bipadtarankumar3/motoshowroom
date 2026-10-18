<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\ApplicationStatus;
use App\Enums\JobDepartment;
use App\Enums\JobType;
use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Models\Showroom;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CareerManagementController extends Controller
{
    /**
     * Display a listing of dealership job openings and recruitment KPIs.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $hasModuleAccess = $showroom->canAccessFeature('has_careers_module');

        $search = $request->query('search');
        $department = $request->query('department');
        $jobType = $request->query('type');
        $status = $request->query('status'); // 'all', 'active', 'inactive'

        $query = $showroom->jobOpenings()
            ->withCount('applications')
            ->search($search)
            ->filterDepartment($department)
            ->filterType($jobType);

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $jobOpenings = $query->latest()->paginate(15)->withQueryString();

        $totalOpenings = $showroom->jobOpenings()->count();
        $activeOpenings = $showroom->activeJobOpenings()->count();
        $totalApplications = $showroom->jobApplications()->count();
        $scheduledInterviews = $showroom->jobApplications()
            ->where('status', ApplicationStatus::InterviewScheduled->value)
            ->count();

        $departments = JobDepartment::cases();
        $jobTypes = JobType::cases();

        return view('showrooms.manage.careers.index', compact(
            'showroom',
            'hasModuleAccess',
            'jobOpenings',
            'totalOpenings',
            'activeOpenings',
            'totalApplications',
            'scheduledInterviews',
            'departments',
            'jobTypes',
            'search',
            'department',
            'jobType',
            'status'
        ));
    }

    /**
     * Show the form for creating a new job opening.
     */
    public function create(Showroom $showroom): View|RedirectResponse
    {
        if (! $showroom->canAccessFeature('has_careers_module')) {
            return redirect()->route('showrooms.manage.careers.index', $showroom)
                ->with('error', 'Your current subscription plan does not include the Careers & Job Openings module. Please upgrade to post vacancies.');
        }

        $departments = JobDepartment::cases();
        $jobTypes = JobType::cases();

        return view('showrooms.manage.careers.create', compact('showroom', 'departments', 'jobTypes'));
    }

    /**
     * Store a newly created job opening in storage.
     */
    public function store(Request $request, Showroom $showroom): RedirectResponse
    {
        if (! $showroom->canAccessFeature('has_careers_module')) {
            return redirect()->route('showrooms.manage.careers.index', $showroom)
                ->with('error', 'Your current subscription plan does not include the Careers module. Please upgrade to post vacancies.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'department' => ['required', 'string', Rule::in(array_column(JobDepartment::cases(), 'value'))],
            'job_type' => ['required', 'string', Rule::in(array_column(JobType::cases(), 'value'))],
            'experience_level' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:150'],
            'salary_range' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:20'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        /** @var JobOpening $opening */
        $opening = $showroom->jobOpenings()->create([
            'tenant_id' => $showroom->id,
            'title' => $validated['title'],
            'department' => $validated['department'],
            'job_type' => $validated['job_type'],
            'experience_level' => $validated['experience_level'],
            'location' => $validated['location'] ?? $showroom->city,
            'salary_range' => $validated['salary_range'] ?? null,
            'description' => $validated['description'],
            'requirements' => $validated['requirements'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'deadline' => $validated['deadline'] ?? null,
        ]);

        AuditLogger::log(
            'job_opening.created',
            $opening,
            null,
            $opening->only(['title', 'department', 'job_type', 'is_active']),
            $showroom->id
        );

        return redirect()->route('showrooms.manage.careers.index', $showroom)
            ->with('success', "Job opening \"{$opening->title}\" has been published successfully.");
    }

    /**
     * Show the form for editing the specified job opening.
     */
    public function edit(Showroom $showroom, JobOpening $jobOpening): View|RedirectResponse
    {
        abort_if($jobOpening->tenant_id !== $showroom->id, 404);

        $departments = JobDepartment::cases();
        $jobTypes = JobType::cases();

        return view('showrooms.manage.careers.edit', compact('showroom', 'jobOpening', 'departments', 'jobTypes'));
    }

    /**
     * Update the specified job opening in storage.
     */
    public function update(Request $request, Showroom $showroom, JobOpening $jobOpening): RedirectResponse
    {
        abort_if($jobOpening->tenant_id !== $showroom->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'department' => ['required', 'string', Rule::in(array_column(JobDepartment::cases(), 'value'))],
            'job_type' => ['required', 'string', Rule::in(array_column(JobType::cases(), 'value'))],
            'experience_level' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:150'],
            'salary_range' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:20'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'deadline' => ['nullable', 'date'],
        ]);

        $oldValues = $jobOpening->only(['title', 'department', 'job_type', 'is_active', 'salary_range', 'deadline']);

        $jobOpening->update([
            'title' => $validated['title'],
            'department' => $validated['department'],
            'job_type' => $validated['job_type'],
            'experience_level' => $validated['experience_level'],
            'location' => $validated['location'] ?? null,
            'salary_range' => $validated['salary_range'] ?? null,
            'description' => $validated['description'],
            'requirements' => $validated['requirements'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'is_active' => $request->boolean('is_active', false),
            'deadline' => $validated['deadline'] ?? null,
        ]);

        AuditLogger::log(
            'job_opening.updated',
            $jobOpening,
            $oldValues,
            $jobOpening->fresh()->only(['title', 'department', 'job_type', 'is_active', 'salary_range', 'deadline']),
            $showroom->id
        );

        return redirect()->route('showrooms.manage.careers.index', $showroom)
            ->with('success', "Job opening \"{$jobOpening->title}\" updated successfully.");
    }

    /**
     * Remove the specified job opening from storage.
     */
    public function destroy(Showroom $showroom, JobOpening $jobOpening): RedirectResponse
    {
        abort_if($jobOpening->tenant_id !== $showroom->id, 404);

        $title = $jobOpening->title;
        $jobOpening->delete();

        AuditLogger::log('job_opening.deleted', $jobOpening, ['title' => $title], null, $showroom->id);

        return redirect()->route('showrooms.manage.careers.index', $showroom)
            ->with('success', "Job opening \"{$title}\" has been deleted.");
    }

    /**
     * Toggle the active status of the specified job opening.
     */
    public function toggleActive(Showroom $showroom, JobOpening $jobOpening): RedirectResponse
    {
        abort_if($jobOpening->tenant_id !== $showroom->id, 404);

        $jobOpening->update([
            'is_active' => ! $jobOpening->is_active,
        ]);

        $statusStr = $jobOpening->is_active ? 'activated' : 'deactivated';

        AuditLogger::log(
            'job_opening.status_toggled',
            $jobOpening,
            ['is_active' => ! $jobOpening->is_active],
            ['is_active' => $jobOpening->is_active],
            $showroom->id
        );

        return redirect()->back()->with('success', "Job opening \"{$jobOpening->title}\" {$statusStr}.");
    }
}
