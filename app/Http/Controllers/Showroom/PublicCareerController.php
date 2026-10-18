<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\ApplicationStatus;
use App\Enums\JobDepartment;
use App\Enums\JobType;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Showroom;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicCareerController extends Controller
{
    /**
     * Display public careers portal listing all active job openings for this dealership.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $search = $request->query('search');
        $department = $request->query('department');
        $jobType = $request->query('type');

        $query = $showroom->activeJobOpenings()
            ->search($search)
            ->filterDepartment($department)
            ->filterType($jobType)
            ->latest();

        $jobOpenings = $query->paginate(12)->withQueryString();

        $departments = JobDepartment::cases();
        $jobTypes = JobType::cases();

        return view('showrooms.careers.index', compact(
            'showroom',
            'jobOpenings',
            'departments',
            'jobTypes',
            'search',
            'department',
            'jobType'
        ));
    }

    /**
     * Display job vacancy details and candidate application form.
     */
    public function show(Showroom $showroom, JobOpening $jobOpening): View
    {
        abort_if($jobOpening->tenant_id !== $showroom->id, 404);
        abort_if(! $jobOpening->is_active, 404);

        $otherOpenings = $showroom->activeJobOpenings()
            ->where('id', '!=', $jobOpening->id)
            ->limit(3)
            ->get();

        return view('showrooms.careers.show', compact('showroom', 'jobOpening', 'otherOpenings'));
    }

    /**
     * Submit a candidate job application with resume file upload.
     */
    public function apply(Request $request, Showroom $showroom, JobOpening $jobOpening): RedirectResponse
    {
        abort_if($jobOpening->tenant_id !== $showroom->id, 404);
        abort_if(! $jobOpening->is_active, 404);

        $validated = $request->validate([
            'applicant_name' => ['required', 'string', 'max:150'],
            'applicant_email' => ['required', 'email', 'max:150'],
            'applicant_phone' => ['required', 'string', 'max:30'],
            'current_company' => ['nullable', 'string', 'max:150'],
            'experience_years' => ['required', 'numeric', 'min:0', 'max:50'],
            'resume_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5MB max
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'cover_letter' => ['nullable', 'string', 'max:3000'],
        ]);

        $resumePath = null;
        if ($request->hasFile('resume_file')) {
            $resumePath = $request->file('resume_file')->store('resumes/'.$showroom->id, 'public');
        }

        /** @var JobApplication $application */
        $application = $jobOpening->applications()->create([
            'tenant_id' => $showroom->id,
            'user_id' => Auth::id(),
            'applicant_name' => $validated['applicant_name'],
            'applicant_email' => $validated['applicant_email'],
            'applicant_phone' => $validated['applicant_phone'],
            'current_company' => $validated['current_company'] ?? null,
            'experience_years' => $validated['experience_years'],
            'resume_path' => $resumePath,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'status' => ApplicationStatus::New->value,
        ]);

        AuditLogger::log(
            'job_application.submitted',
            $application,
            null,
            [
                'applicant_number' => $application->applicant_number,
                'job_opening' => $jobOpening->title,
                'applicant_name' => $application->applicant_name,
            ],
            $showroom->id,
            Auth::user()
        );

        return redirect()->route('showrooms.careers.show', [
            'showroom' => $showroom,
            'jobOpening' => $jobOpening,
        ])->with(
            'success',
            "Application successfully submitted! Your candidate reference tracking number is #{$application->applicant_number}."
        );
    }
}
