<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\JobDepartment;
use App\Enums\JobType;
use App\Mail\JobApplicationReceivedMail;
use App\Mail\NewJobApplicationAlertMail;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CareerAndJobApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected User $ownerA;

    protected User $ownerB;

    protected User $candidateUser;

    protected Showroom $showroomA;

    protected Showroom $showroomB;

    protected SubscriptionPlan $basicPlan;

    protected SubscriptionPlan $proPlan;

    protected JobOpening $openingA;

    protected JobOpening $openingB;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->basicPlan = SubscriptionPlan::create([
            'name' => 'Starter Tier',
            'slug' => 'starter-tier',
            'monthly_price' => 2999.00,
            'annual_price' => 29990.00,
            'currency' => 'INR',
            'has_careers_module' => false,
            'is_active' => true,
        ]);

        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Professional Enterprise',
            'slug' => 'pro-enterprise',
            'monthly_price' => 7999.00,
            'annual_price' => 79990.00,
            'currency' => 'INR',
            'has_careers_module' => true,
            'is_active' => true,
        ]);

        $this->ownerA = User::create([
            'name' => 'Rajesh Sharma',
            'email' => 'rajesh@apex.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->ownerB = User::create([
            'name' => 'Vikram Malhotra',
            'email' => 'vikram@velocity.example',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->candidateUser = User::create([
            'name' => 'Arun Kumar',
            'email' => 'arun@candidate.example',
            'phone' => '+91 98765 43210',
            'password' => 'Password123!',
            'role' => 'user',
            'status' => 'active',
        ]);

        $this->showroomA = Showroom::create([
            'name' => 'Apex Moto Hub',
            'slug' => 'apex-moto',
            'email' => 'contact@apex.example',
            'phone' => '+91 8023456789',
            'city' => 'Bangalore',
            'state' => 'Karnataka',
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        $this->showroomB = Showroom::create([
            'name' => 'Velocity Superbikes',
            'slug' => 'velocity-superbikes',
            'email' => 'contact@velocity.example',
            'phone' => '+91 1123456789',
            'city' => 'New Delhi',
            'state' => 'Delhi',
            'status' => 'active',
            'public_website_enabled' => true,
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroomA->id,
            'user_id' => $this->ownerA->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        ShowroomMembership::create([
            'showroom_id' => $this->showroomB->id,
            'user_id' => $this->ownerB->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        Subscription::create([
            'showroom_id' => $this->showroomA->id,
            'plan_id' => $this->proPlan->id,
            'status' => 'active',
            'billing_interval' => 'monthly',
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);

        Subscription::create([
            'showroom_id' => $this->showroomB->id,
            'plan_id' => $this->proPlan->id,
            'status' => 'active',
            'billing_interval' => 'monthly',
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);

        $this->openingA = JobOpening::create([
            'tenant_id' => $this->showroomA->id,
            'title' => 'Senior Motorcycle Diagnostic Master',
            'slug' => 'senior-motorcycle-diagnostic-master',
            'department' => JobDepartment::Workshop->value,
            'job_type' => JobType::FullTime->value,
            'experience_level' => 'senior',
            'location' => 'Bangalore Workshop',
            'salary_range' => '₹45,000 - ₹65,000 / mo',
            'description' => 'Perform ECU diagnostics, valve clearance adjustments, and suspension overhauls on 400cc+ motorcycles.',
            'requirements' => '5+ years experience with multi-brand superbikes. Certified technician diploma.',
            'benefits' => 'Health insurance, annual performance bonus, track-day sponsorship.',
            'is_active' => true,
            'deadline' => now()->addDays(30),
        ]);

        $this->openingB = JobOpening::create([
            'tenant_id' => $this->showroomB->id,
            'title' => 'Showroom Sales Executive',
            'slug' => 'showroom-sales-executive',
            'department' => JobDepartment::Sales->value,
            'job_type' => JobType::FullTime->value,
            'experience_level' => 'mid',
            'location' => 'New Delhi Flagship',
            'salary_range' => '₹30,000 - ₹45,000 / mo',
            'description' => 'Guide prospective riders through our superbike collection and coordinate test-rides.',
            'requirements' => '2+ years automotive retail experience.',
            'benefits' => 'High sales commissions, riding gear allowances.',
            'is_active' => true,
        ]);
    }

    public function test_public_visitor_can_browse_active_job_openings(): void
    {
        JobOpening::create([
            'tenant_id' => $this->showroomA->id,
            'title' => 'Inactive Parts Manager',
            'slug' => 'inactive-parts-manager',
            'department' => JobDepartment::Parts->value,
            'job_type' => JobType::FullTime->value,
            'experience_level' => 'mid',
            'description' => 'Manage inventory spare parts and performance upgrades.',
            'is_active' => false,
        ]);

        $response = $this->get(route('showrooms.careers.index', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('Build Your Career With');
        $response->assertSee('Senior Motorcycle Diagnostic Master');
        $response->assertSee('Workshop &amp; Technical Service', false);
        $response->assertDontSee('Inactive Parts Manager');
        $response->assertDontSee('Showroom Sales Executive'); // Showroom B's opening
    }

    public function test_public_visitor_can_filter_job_openings_by_department_and_type(): void
    {
        JobOpening::create([
            'tenant_id' => $this->showroomA->id,
            'title' => 'Community Social Media Intern',
            'slug' => 'community-social-media-intern',
            'department' => JobDepartment::Marketing->value,
            'job_type' => JobType::Internship->value,
            'experience_level' => 'entry',
            'description' => 'Create Instagram reels and ride-out community content.',
            'is_active' => true,
        ]);

        // Filter by Marketing
        $responseMarketing = $this->get(route('showrooms.careers.index', [
            'showroom' => $this->showroomA,
            'department' => 'marketing',
        ]));
        $responseMarketing->assertStatus(200);
        $responseMarketing->assertSee('Community Social Media Intern');
        $responseMarketing->assertDontSee('Senior Motorcycle Diagnostic Master');

        // Filter by Workshop
        $responseWorkshop = $this->get(route('showrooms.careers.index', [
            'showroom' => $this->showroomA,
            'department' => 'workshop',
        ]));
        $responseWorkshop->assertStatus(200);
        $responseWorkshop->assertSee('Senior Motorcycle Diagnostic Master');
        $responseWorkshop->assertDontSee('Community Social Media Intern');
    }

    public function test_public_visitor_can_search_job_openings_by_keyword(): void
    {
        $response = $this->get(route('showrooms.careers.index', [
            'showroom' => $this->showroomA,
            'search' => 'diagnostic',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Senior Motorcycle Diagnostic Master');

        $responseEmpty = $this->get(route('showrooms.careers.index', [
            'showroom' => $this->showroomA,
            'search' => 'nonexistentrole12345',
        ]));

        $responseEmpty->assertStatus(200);
        $responseEmpty->assertSee('No Open Vacancies Found');
    }

    public function test_public_visitor_can_view_job_opening_details(): void
    {
        $response = $this->get(route('showrooms.careers.show', [
            'showroom' => $this->showroomA,
            'jobOpening' => $this->openingA,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Senior Motorcycle Diagnostic Master');
        $response->assertSee('Apply for this Position');
        $response->assertSee('₹45,000 - ₹65,000 / mo');
        $response->assertSee('Perform ECU diagnostics');
        $response->assertSee('Health insurance, annual performance bonus');
    }

    public function test_candidate_can_submit_job_application_with_resume_upload(): void
    {
        Mail::fake();

        $resumeFile = UploadedFile::fake()->create('curriculum_vitae.pdf', 250, 'application/pdf');

        $payload = [
            'applicant_name' => 'Vikrant Desai',
            'applicant_email' => 'vikrant@rider.example',
            'applicant_phone' => '+91 99887 76655',
            'current_company' => 'Ducati Service Center',
            'experience_years' => 6.5,
            'portfolio_url' => 'https://linkedin.com/in/vikrant-desai',
            'cover_letter' => 'I have 6+ years specializing in electronic diagnostics and suspension tuning for track bikes.',
            'resume_file' => $resumeFile,
        ];

        $response = $this->post(route('showrooms.careers.apply', [
            'showroom' => $this->showroomA,
            'jobOpening' => $this->openingA,
        ]), $payload);

        $response->assertSessionHas('success');
        $response->assertRedirect(route('showrooms.careers.show', [
            'showroom' => $this->showroomA,
            'jobOpening' => $this->openingA,
        ]));

        $this->assertDatabaseHas('job_applications', [
            'tenant_id' => $this->showroomA->id,
            'job_opening_id' => $this->openingA->id,
            'applicant_name' => 'Vikrant Desai',
            'applicant_email' => 'vikrant@rider.example',
            'status' => ApplicationStatus::New->value,
            'current_company' => 'Ducati Service Center',
        ]);

        /** @var JobApplication $application */
        $application = JobApplication::where('applicant_email', 'vikrant@rider.example')->firstOrFail();
        $this->assertStringStartsWith('APP-', $application->applicant_number);
        $this->assertNotNull($application->resume_path);
        Storage::disk('public')->assertExists($application->resume_path);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'job_application.submitted',
        ]);

        Mail::assertQueued(JobApplicationReceivedMail::class, function (JobApplicationReceivedMail $mail) use ($application): bool {
            return $mail->hasTo('vikrant@rider.example')
                && $mail->application->id === $application->id
                && $mail->jobOpening->id === $this->openingA->id
                && $mail->showroom->id === $this->showroomA->id;
        });

        Mail::assertQueued(NewJobApplicationAlertMail::class, function (NewJobApplicationAlertMail $mail) use ($application): bool {
            return $mail->hasTo($this->showroomA->email)
                && $mail->application->id === $application->id
                && $mail->jobOpening->id === $this->openingA->id
                && $mail->showroom->id === $this->showroomA->id;
        });
    }

    public function test_back_office_careers_index_displays_vacancies_and_kpi_metrics(): void
    {
        JobApplication::create([
            'tenant_id' => $this->showroomA->id,
            'job_opening_id' => $this->openingA->id,
            'applicant_number' => 'APP-20261018-TEST',
            'applicant_name' => 'Manish Paul',
            'applicant_email' => 'manish@example.com',
            'applicant_phone' => '+91 98765 00000',
            'status' => ApplicationStatus::InterviewScheduled->value,
            'interview_at' => now()->addDays(2),
        ]);

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.careers.index', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('Dealership Talent Recruitment');
        $response->assertSee('Senior Motorcycle Diagnostic Master');
        $response->assertSee('ATS Candidates (1)');
        $response->assertSee('Interviews Scheduled');
    }

    public function test_back_office_careers_create_is_guarded_by_has_careers_module_plan_entitlement(): void
    {
        // Downgrade showroom A to starter tier without careers module
        $this->showroomA->subscription()->update([
            'plan_id' => $this->basicPlan->id,
        ]);

        $responseCreate = $this->actingAs($this->ownerA)->get(route('showrooms.manage.careers.create', $this->showroomA));
        $responseCreate->assertRedirect(route('showrooms.manage.careers.index', $this->showroomA));
        $responseCreate->assertSessionHas('error');

        $responseStore = $this->actingAs($this->ownerA)->post(route('showrooms.manage.careers.store', $this->showroomA), [
            'title' => 'Blocked Position',
            'department' => JobDepartment::Sales->value,
            'job_type' => JobType::FullTime->value,
            'experience_level' => 'mid',
            'description' => 'This should be blocked by the subscription plan entitlement.',
        ]);

        $responseStore->assertRedirect(route('showrooms.manage.careers.index', $this->showroomA));
        $responseStore->assertSessionHas('error');
        $this->assertDatabaseMissing('job_openings', ['title' => 'Blocked Position']);
    }

    public function test_back_office_owner_can_create_new_job_opening(): void
    {
        $payload = [
            'title' => 'Senior Customer Relationship Manager',
            'department' => JobDepartment::CustomerRelations->value,
            'job_type' => JobType::FullTime->value,
            'experience_level' => 'senior',
            'location' => 'Bangalore Indiranagar',
            'salary_range' => '₹40,000 - ₹50,000 / mo',
            'description' => 'Lead premium customer post-delivery relationships, rider club meetups, and service feedback.',
            'requirements' => '3+ years in luxury automotive CRM.',
            'benefits' => 'Incentives and festival bonuses.',
            'is_active' => '1',
            'deadline' => now()->addMonth()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->ownerA)
            ->post(route('showrooms.manage.careers.store', $this->showroomA), $payload);

        $response->assertSessionHas('success');
        $response->assertRedirect(route('showrooms.manage.careers.index', $this->showroomA));

        $this->assertDatabaseHas('job_openings', [
            'tenant_id' => $this->showroomA->id,
            'title' => 'Senior Customer Relationship Manager',
            'department' => JobDepartment::CustomerRelations->value,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'job_opening.created',
        ]);
    }

    public function test_back_office_owner_can_edit_and_update_job_opening(): void
    {
        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.careers.edit', [
            'showroom' => $this->showroomA,
            'jobOpening' => $this->openingA,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Edit Job Opening');
        $response->assertSee($this->openingA->title);

        $updatePayload = [
            'title' => 'Lead Master Superbike Technician',
            'department' => JobDepartment::Workshop->value,
            'job_type' => JobType::FullTime->value,
            'experience_level' => 'lead',
            'location' => 'Bangalore Central Hub',
            'salary_range' => '₹60,000 - ₹80,000 / mo',
            'description' => 'Updated master technician overview with team leadership responsibilities.',
            'is_active' => '1',
        ];

        $responseUpdate = $this->actingAs($this->ownerA)->put(route('showrooms.manage.careers.update', [
            'showroom' => $this->showroomA,
            'jobOpening' => $this->openingA,
        ]), $updatePayload);

        $responseUpdate->assertSessionHas('success');
        $responseUpdate->assertRedirect(route('showrooms.manage.careers.index', $this->showroomA));

        $this->assertDatabaseHas('job_openings', [
            'id' => $this->openingA->id,
            'title' => 'Lead Master Superbike Technician',
            'salary_range' => '₹60,000 - ₹80,000 / mo',
            'experience_level' => 'lead',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'job_opening.updated',
        ]);
    }

    public function test_back_office_owner_can_toggle_job_opening_active_status(): void
    {
        $this->assertTrue($this->openingA->is_active);

        $response = $this->actingAs($this->ownerA)->post(route('showrooms.manage.careers.toggle-active', [
            'showroom' => $this->showroomA,
            'jobOpening' => $this->openingA,
        ]));

        $response->assertSessionHas('success');
        $this->assertFalse($this->openingA->fresh()->is_active);

        // Toggle back
        $this->actingAs($this->ownerA)->post(route('showrooms.manage.careers.toggle-active', [
            'showroom' => $this->showroomA,
            'jobOpening' => $this->openingA,
        ]));

        $this->assertTrue($this->openingA->fresh()->is_active);
    }

    public function test_back_office_owner_can_soft_delete_job_opening(): void
    {
        $response = $this->actingAs($this->ownerA)->delete(route('showrooms.manage.careers.destroy', [
            'showroom' => $this->showroomA,
            'jobOpening' => $this->openingA,
        ]));

        $response->assertSessionHas('success');
        $response->assertRedirect(route('showrooms.manage.careers.index', $this->showroomA));

        $this->assertSoftDeleted('job_openings', [
            'id' => $this->openingA->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'job_opening.deleted',
        ]);
    }

    public function test_back_office_ats_index_displays_candidate_applications_and_metrics(): void
    {
        $app1 = JobApplication::create([
            'tenant_id' => $this->showroomA->id,
            'job_opening_id' => $this->openingA->id,
            'applicant_number' => 'APP-20261018-0001',
            'applicant_name' => 'Deepak Sen',
            'applicant_email' => 'deepak@example.com',
            'applicant_phone' => '+91 91234 56789',
            'status' => ApplicationStatus::New->value,
            'experience_years' => 3.0,
        ]);

        $app2 = JobApplication::create([
            'tenant_id' => $this->showroomA->id,
            'job_opening_id' => $this->openingA->id,
            'applicant_number' => 'APP-20261018-0002',
            'applicant_name' => 'Kavita Joshi',
            'applicant_email' => 'kavita@example.com',
            'applicant_phone' => '+91 99887 11223',
            'status' => ApplicationStatus::Shortlisted->value,
            'experience_years' => 5.0,
        ]);

        $response = $this->actingAs($this->ownerA)->get(route('showrooms.manage.applicants.index', $this->showroomA));

        $response->assertStatus(200);
        $response->assertSee('Applicant Tracking System (ATS)');
        $response->assertSee('Deepak Sen');
        $response->assertSee('Kavita Joshi');
        $response->assertSee('APP-20261018-0001');

        // Test filtering by status
        $responseFilter = $this->actingAs($this->ownerA)->get(route('showrooms.manage.applicants.index', [
            'showroom' => $this->showroomA,
            'status' => ApplicationStatus::Shortlisted->value,
        ]));
        $responseFilter->assertStatus(200);
        $responseFilter->assertSee('Kavita Joshi');
        $responseFilter->assertDontSee('Deepak Sen');
    }

    public function test_back_office_recruiter_can_advance_candidate_status_and_schedule_interview(): void
    {
        $app = JobApplication::create([
            'tenant_id' => $this->showroomA->id,
            'job_opening_id' => $this->openingA->id,
            'applicant_number' => 'APP-20261018-9999',
            'applicant_name' => 'Alok Nath',
            'applicant_email' => 'alok@example.com',
            'applicant_phone' => '+91 95555 44444',
            'status' => ApplicationStatus::New->value,
            'experience_years' => 4.5,
        ]);

        $responseView = $this->actingAs($this->ownerA)->get(route('showrooms.manage.applicants.show', [
            'showroom' => $this->showroomA,
            'applicant' => $app,
        ]));

        $responseView->assertStatus(200);
        $responseView->assertSee('Alok Nath');
        $responseView->assertSee('Recruitment Workflow');

        $interviewTime = now()->addDays(3)->setTime(11, 0)->format('Y-m-d\TH:i');

        $responseUpdate = $this->actingAs($this->ownerA)->put(route('showrooms.manage.applicants.update', [
            'showroom' => $this->showroomA,
            'applicant' => $app,
        ]), [
            'status' => ApplicationStatus::InterviewScheduled->value,
            'interview_at' => $interviewTime,
            'internal_notes' => 'Passed preliminary screening. Scheduled for in-person technical diagnostic test in Bay 2.',
        ]);

        $responseUpdate->assertSessionHas('success');

        $this->assertDatabaseHas('job_applications', [
            'id' => $app->id,
            'status' => ApplicationStatus::InterviewScheduled->value,
            'internal_notes' => 'Passed preliminary screening. Scheduled for in-person technical diagnostic test in Bay 2.',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'job_application.updated',
        ]);
    }

    public function test_back_office_recruiter_can_soft_delete_applicant(): void
    {
        $app = JobApplication::create([
            'tenant_id' => $this->showroomA->id,
            'job_opening_id' => $this->openingA->id,
            'applicant_number' => 'APP-20261018-DEL',
            'applicant_name' => 'Temporary Candidate',
            'applicant_email' => 'temp@example.com',
            'applicant_phone' => '+91 91111 22222',
            'status' => ApplicationStatus::Rejected->value,
        ]);

        $response = $this->actingAs($this->ownerA)->delete(route('showrooms.manage.applicants.destroy', [
            'showroom' => $this->showroomA,
            'applicant' => $app,
        ]));

        $response->assertSessionHas('success');
        $response->assertRedirect(route('showrooms.manage.applicants.index', $this->showroomA));

        $this->assertSoftDeleted('job_applications', [
            'id' => $app->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $this->showroomA->id,
            'action' => 'job_application.deleted',
        ]);
    }

    public function test_multi_tenant_isolation_prevents_access_or_modification_across_showrooms(): void
    {
        $appA = JobApplication::create([
            'tenant_id' => $this->showroomA->id,
            'job_opening_id' => $this->openingA->id,
            'applicant_number' => 'APP-20261018-ISOL',
            'applicant_name' => 'Tenant A Candidate',
            'applicant_email' => 'candidateA@example.com',
            'applicant_phone' => '+91 98765 11111',
            'status' => ApplicationStatus::New->value,
        ]);

        // Owner B tries to access Showroom A's job opening in back-office
        $responseEdit = $this->actingAs($this->ownerB)->get(route('showrooms.manage.careers.edit', [
            'showroom' => $this->showroomB,
            'jobOpening' => $this->openingA,
        ]));
        $responseEdit->assertStatus(404);

        // Owner B tries to update Showroom A's job opening
        $responseUpdate = $this->actingAs($this->ownerB)->put(route('showrooms.manage.careers.update', [
            'showroom' => $this->showroomB,
            'jobOpening' => $this->openingA,
        ]), [
            'title' => 'Malicious Hijack',
            'department' => JobDepartment::Sales->value,
            'job_type' => JobType::FullTime->value,
            'experience_level' => 'mid',
            'description' => 'Cross-tenant tampering attempt.',
        ]);
        $responseUpdate->assertStatus(404);

        // Owner B tries to access Showroom A's applicant profile
        $responseAppView = $this->actingAs($this->ownerB)->get(route('showrooms.manage.applicants.show', [
            'showroom' => $this->showroomB,
            'applicant' => $appA,
        ]));
        $responseAppView->assertStatus(404);

        // Owner B tries to delete Showroom A's applicant
        $responseAppDelete = $this->actingAs($this->ownerB)->delete(route('showrooms.manage.applicants.destroy', [
            'showroom' => $this->showroomB,
            'applicant' => $appA,
        ]));
        $responseAppDelete->assertStatus(404);

        $this->assertDatabaseHas('job_applications', [
            'id' => $appA->id,
            'deleted_at' => null,
        ]);
    }
}
