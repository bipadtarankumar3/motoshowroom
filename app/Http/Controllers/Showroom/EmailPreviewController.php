<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\ApplicationStatus;
use App\Enums\BookingStatus;
use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Enums\JobDepartment;
use App\Enums\JobType;
use App\Enums\PaymentStatus;
use App\Enums\ServiceType;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Mail\EnquiryReceivedMail;
use App\Mail\JobApplicationReceivedMail;
use App\Mail\NewEnquiryAlertMail;
use App\Mail\NewJobApplicationAlertMail;
use App\Mail\PaymentReceiptMail;
use App\Mail\ServiceBookingConfirmedMail;
use App\Mail\ServiceStatusUpdatedMail;
use App\Mail\SubscriptionExpiringMail;
use App\Models\Enquiry;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;

class EmailPreviewController extends Controller
{
    /**
     * Display directory of all dealership transactional email templates.
     */
    public function index(Showroom $showroom): View
    {
        $templates = [
            [
                'id' => 'enquiry_received',
                'title' => 'Customer Enquiry Acknowledgement',
                'description' => 'Sent to customer confirming their purchase enquiry or test ride request.',
                'badge' => 'Customer Alert',
                'color' => 'rose',
            ],
            [
                'id' => 'enquiry_alert',
                'title' => 'Dealership New Lead Alert',
                'description' => 'Sent to dealership sales staff notifying of a new customer lead.',
                'badge' => 'Staff Alert',
                'color' => 'amber',
            ],
            [
                'id' => 'service_confirmed',
                'title' => 'Service Appointment Confirmation',
                'description' => 'Sent to customer with booking number, time slot, bay assignment, and live tracker.',
                'badge' => 'Customer Alert',
                'color' => 'emerald',
            ],
            [
                'id' => 'service_status_updated',
                'title' => 'Service Status Progression & Ready Pickup',
                'description' => 'Sent to customer when motorcycle moves stages or is ready for delivery.',
                'badge' => 'Customer Alert',
                'color' => 'sky',
            ],
            [
                'id' => 'payment_receipt',
                'title' => 'Payment Receipt / Tax Invoice',
                'description' => 'Sent upon successful Razorpay capture with payment details & HMAC verification.',
                'badge' => 'Financial Receipt',
                'color' => 'emerald',
            ],
            [
                'id' => 'career_received',
                'title' => 'Job Application Acknowledgement',
                'description' => 'Sent to applicant confirming submission of their resume and candidate profile.',
                'badge' => 'Candidate Alert',
                'color' => 'sky',
            ],
            [
                'id' => 'career_alert',
                'title' => 'Dealership New Applicant Alert',
                'description' => 'Sent to showroom recruitment desk notifying of a new job candidate.',
                'badge' => 'Staff Alert',
                'color' => 'rose',
            ],
            [
                'id' => 'subscription_expiring',
                'title' => 'Subscription Renewal Warning',
                'description' => 'Sent to showroom owner when dealership plan is expiring in <= 7 days.',
                'badge' => 'Showroom Owner',
                'color' => 'rose',
            ],
        ];

        return view('showrooms.manage.email-previews.index', compact('showroom', 'templates'));
    }

    /**
     * Render preview of a specific email template with realistic mock/live data.
     */
    public function show(Request $request, Showroom $showroom, string $type): Mailable|View
    {
        return match ($type) {
            'enquiry_received' => $this->previewEnquiryReceived($showroom),
            'enquiry_alert' => $this->previewEnquiryAlert($showroom),
            'service_confirmed' => $this->previewServiceConfirmed($showroom),
            'service_status_updated' => $this->previewServiceStatusUpdated($showroom),
            'payment_receipt' => $this->previewPaymentReceipt($showroom),
            'career_received' => $this->previewCareerReceived($showroom),
            'career_alert' => $this->previewCareerAlert($showroom),
            'subscription_expiring' => $this->previewSubscriptionExpiring($showroom),
            default => abort(404, 'Unknown email notification template type.'),
        };
    }

    private function previewEnquiryReceived(Showroom $showroom): EnquiryReceivedMail
    {
        $enquiry = $showroom->enquiries()->latest()->first() ?? new Enquiry([
            'tenant_id' => $showroom->id,
            'enquiry_number' => 'ENQ-'.date('Ymd').'-SAMPLE',
            'type' => EnquiryType::TestRide->value,
            'customer_name' => 'Aditya Roy',
            'customer_email' => 'aditya@example.com',
            'customer_phone' => '+91 9876543210',
            'preferred_date' => now()->addDays(2),
            'preferred_time_slot' => '11:00 AM - 12:00 PM',
            'customer_notes' => 'Interested in financing options and standard warranty inclusions.',
            'status' => EnquiryStatus::New->value,
        ]);

        if (! $enquiry->exists) {
            $enquiry->id = 1;
            $enquiry->exists = true;
        }

        return new EnquiryReceivedMail($showroom, $enquiry);
    }

    private function previewEnquiryAlert(Showroom $showroom): NewEnquiryAlertMail
    {
        $enquiry = $showroom->enquiries()->latest()->first() ?? new Enquiry([
            'tenant_id' => $showroom->id,
            'enquiry_number' => 'ENQ-'.date('Ymd').'-HOTLEAD',
            'type' => EnquiryType::Purchase->value,
            'customer_name' => 'Pooja Hegde',
            'customer_email' => 'pooja@example.com',
            'customer_phone' => '+91 9123456789',
            'customer_notes' => 'Looking to purchase Ninja ZX-10R in cash this weekend.',
            'status' => EnquiryStatus::New->value,
        ]);

        if (! $enquiry->exists) {
            $enquiry->id = 1;
            $enquiry->exists = true;
        }

        return new NewEnquiryAlertMail($showroom, $enquiry);
    }

    private function previewServiceConfirmed(Showroom $showroom): ServiceBookingConfirmedMail
    {
        $booking = $showroom->serviceBookings()->latest()->first() ?? new ServiceBooking([
            'tenant_id' => $showroom->id,
            'booking_number' => 'SRV-'.date('Ymd').'-APPT',
            'customer_name' => 'Rahul Sharma',
            'customer_email' => 'rahul@example.com',
            'customer_phone' => '+91 9988776655',
            'bike_make_model' => 'Kawasaki Z900 (2025)',
            'registration_number' => 'KA-05-MM-1234',
            'service_type' => ServiceType::GeneralService->value,
            'booking_date' => now()->addDay(),
            'time_slot' => '10:00:00',
            'bay_number' => 'Bay 02 - Express',
            'estimated_cost' => 3500.00,
            'status' => BookingStatus::Confirmed->value,
        ]);

        if (! $booking->exists) {
            $booking->id = 1;
            $booking->exists = true;
        }

        return new ServiceBookingConfirmedMail($showroom, $booking);
    }

    private function previewServiceStatusUpdated(Showroom $showroom): ServiceStatusUpdatedMail
    {
        $booking = $showroom->serviceBookings()->latest()->first() ?? new ServiceBooking([
            'tenant_id' => $showroom->id,
            'booking_number' => 'SRV-'.date('Ymd').'-READY',
            'customer_name' => 'Rahul Sharma',
            'customer_email' => 'rahul@example.com',
            'customer_phone' => '+91 9988776655',
            'bike_make_model' => 'Kawasaki Z900 (2025)',
            'registration_number' => 'KA-05-MM-1234',
            'service_type' => ServiceType::GeneralService->value,
            'booking_date' => now(),
            'time_slot' => '10:00:00',
            'bay_number' => 'Bay 02 - Express',
            'status' => BookingStatus::ReadyForCollection->value,
            'final_cost' => 3850.00,
            'payment_status' => PaymentStatus::Pending->value,
            'technician_notes' => 'Complete oil change, brake pads inspected, chain lubed and tensioned. Pressure wash completed. Vehicle ready for collection.',
        ]);

        if (! $booking->exists) {
            $booking->id = 1;
            $booking->exists = true;
        }

        return new ServiceStatusUpdatedMail($showroom, $booking);
    }

    private function previewPaymentReceipt(Showroom $showroom): PaymentReceiptMail
    {
        $payment = $showroom->payments()->latest()->first() ?? new Payment([
            'id' => 101,
            'tenant_id' => $showroom->id,
            'payable_type' => SubscriptionPlan::class,
            'payable_id' => 1,
            'purpose' => 'subscription_purchase',
            'amount' => 4999.00,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_prev_sample_123',
            'razorpay_payment_id' => 'pay_prev_sample_987',
            'status' => PaymentStatus::Captured->value,
            'signature_verified' => true,
            'method' => 'upi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! $payment->exists) {
            $payment->id = 1;
            $payment->exists = true;
        }

        return new PaymentReceiptMail($showroom, $payment);
    }

    private function previewSubscriptionExpiring(Showroom $showroom): SubscriptionExpiringMail
    {
        $plan = SubscriptionPlan::first() ?? new SubscriptionPlan([
            'name' => 'Apex Pro Dealership',
            'slug' => 'apex-pro',
            'monthly_price' => 4999.00,
        ]);

        $subscription = $showroom->subscription ?? new Subscription([
            'showroom_id' => $showroom->id,
            'plan_id' => $plan->id ?? 1,
            'status' => SubscriptionStatus::Active->value,
            'billing_interval' => 'monthly',
            'current_period_start' => now()->subMonth(),
            'current_period_end' => now()->addDays(5),
        ]);

        $subscription->setRelation('plan', $plan);

        return new SubscriptionExpiringMail($showroom, $subscription);
    }

    private function previewCareerReceived(Showroom $showroom): JobApplicationReceivedMail
    {
        $jobOpening = $showroom->jobOpenings()->latest()->first() ?? new JobOpening([
            'id' => 1,
            'tenant_id' => $showroom->id,
            'title' => 'Master Diagnostic Technician',
            'slug' => 'master-diagnostic-technician',
            'department' => JobDepartment::Workshop,
            'job_type' => JobType::FullTime,
            'experience_level' => '3-5 years',
            'location' => $showroom->city ?? 'Bengaluru',
            'salary_range' => '₹45,000 - ₹65,000 / month',
            'is_active' => true,
        ]);

        if (! $jobOpening->exists) {
            $jobOpening->id = 1;
            $jobOpening->exists = true;
        }

        $application = $showroom->jobApplications()->latest()->first() ?? new JobApplication([
            'id' => 1,
            'tenant_id' => $showroom->id,
            'job_opening_id' => $jobOpening->id,
            'applicant_number' => 'APP-'.date('Ymd').'-SAMPLE',
            'applicant_name' => 'Kunal Sharma',
            'applicant_email' => 'kunal.sharma@example.com',
            'applicant_phone' => '+91 9988776655',
            'experience_years' => 4.5,
            'current_company' => 'Apex MotoWorks',
            'portfolio_url' => 'https://linkedin.com/in/sample-technician',
            'cover_letter' => 'Passionate diagnostic technician with certified OEM experience in multi-cylinder superbike engine rebuilds.',
            'status' => ApplicationStatus::New,
        ]);

        if (! $application->exists) {
            $application->id = 1;
            $application->exists = true;
        }

        return new JobApplicationReceivedMail($showroom, $application, $jobOpening);
    }

    private function previewCareerAlert(Showroom $showroom): NewJobApplicationAlertMail
    {
        $jobOpening = $showroom->jobOpenings()->latest()->first() ?? new JobOpening([
            'id' => 1,
            'tenant_id' => $showroom->id,
            'title' => 'Senior Superbike Consultant',
            'slug' => 'senior-superbike-consultant',
            'department' => JobDepartment::Sales,
            'job_type' => JobType::FullTime,
            'experience_level' => '2-4 years',
            'location' => $showroom->city ?? 'Mumbai',
            'salary_range' => '₹50,000 - ₹80,000 / month + Incentives',
            'is_active' => true,
        ]);

        if (! $jobOpening->exists) {
            $jobOpening->id = 1;
            $jobOpening->exists = true;
        }

        $application = $showroom->jobApplications()->latest()->first() ?? new JobApplication([
            'id' => 1,
            'tenant_id' => $showroom->id,
            'job_opening_id' => $jobOpening->id,
            'applicant_number' => 'APP-'.date('Ymd').'-ALERT',
            'applicant_name' => 'Priya Nair',
            'applicant_email' => 'priya.nair@example.com',
            'applicant_phone' => '+91 9822334455',
            'experience_years' => 3.0,
            'current_company' => 'Velocity Motors',
            'portfolio_url' => 'https://linkedin.com/in/sample-consultant',
            'cover_letter' => 'Over 3 years of consultative track record exceeding quarterly sales targets for premium superbike models.',
            'status' => ApplicationStatus::New,
        ]);

        if (! $application->exists) {
            $application->id = 1;
            $application->exists = true;
        }

        return new NewJobApplicationAlertMail($showroom, $application, $jobOpening);
    }
}
