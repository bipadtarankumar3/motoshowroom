<?php

namespace App\Services;

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
use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Models\Subscription;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ShowroomNotificationService
{
    /**
     * Dispatch notifications when a new customer enquiry or test ride is submitted.
     */
    public function notifyEnquiryCreated(Enquiry $enquiry): void
    {
        $showroom = $enquiry->showroom;
        if (! $showroom) {
            return;
        }

        // 1. Send confirmation acknowledgement to customer
        if (! empty($enquiry->customer_email)) {
            try {
                Mail::to($enquiry->customer_email)->send(new EnquiryReceivedMail($showroom, $enquiry));
            } catch (Exception $e) {
                Log::error("Failed to send EnquiryReceivedMail to {$enquiry->customer_email}: ".$e->getMessage());
            }
        }

        // 2. Send lead alert to dealership inbox / owner
        $staffRecipient = $showroom->email ?: $showroom->owner?->email;
        if (! empty($staffRecipient)) {
            try {
                Mail::to($staffRecipient)->send(new NewEnquiryAlertMail($showroom, $enquiry));
            } catch (Exception $e) {
                Log::error("Failed to send NewEnquiryAlertMail to {$staffRecipient}: ".$e->getMessage());
            }
        }
    }

    /**
     * Dispatch notification when service appointment is booked or confirmed.
     */
    public function notifyServiceBookingConfirmed(ServiceBooking $booking): void
    {
        $showroom = $booking->showroom;
        if (! $showroom || empty($booking->customer_email)) {
            return;
        }

        try {
            Mail::to($booking->customer_email)->send(new ServiceBookingConfirmedMail($showroom, $booking));
        } catch (Exception $e) {
            Log::error("Failed to send ServiceBookingConfirmedMail to {$booking->customer_email}: ".$e->getMessage());
        }
    }

    /**
     * Dispatch notification when service status is updated (especially ready for pickup).
     */
    public function notifyServiceStatusUpdated(ServiceBooking $booking): void
    {
        $showroom = $booking->showroom;
        if (! $showroom || empty($booking->customer_email)) {
            return;
        }

        try {
            Mail::to($booking->customer_email)->send(new ServiceStatusUpdatedMail($showroom, $booking));
        } catch (Exception $e) {
            Log::error("Failed to send ServiceStatusUpdatedMail to {$booking->customer_email}: ".$e->getMessage());
        }
    }

    /**
     * Dispatch payment receipt notification upon capture.
     */
    public function notifyPaymentCaptured(Payment $payment): void
    {
        $showroom = $payment->showroom;
        if (! $showroom) {
            return;
        }

        $recipientEmail = null;

        if ($payment->user && ! empty($payment->user->email)) {
            $recipientEmail = $payment->user->email;
        } elseif ($payment->payable_type === ServiceBooking::class && $payment->payable) {
            $recipientEmail = $payment->payable->customer_email;
        } elseif ($showroom->owner && ! empty($showroom->owner->email)) {
            $recipientEmail = $showroom->owner->email;
        } else {
            $recipientEmail = $showroom->email;
        }

        if (! empty($recipientEmail)) {
            try {
                Mail::to($recipientEmail)->send(new PaymentReceiptMail($showroom, $payment));
            } catch (Exception $e) {
                Log::error("Failed to send PaymentReceiptMail to {$recipientEmail}: ".$e->getMessage());
            }
        }
    }

    /**
     * Dispatch subscription expiry warning to showroom owner.
     */
    public function notifySubscriptionExpiring(Showroom $showroom, Subscription $subscription): void
    {
        $recipientEmail = $showroom->owner?->email ?: $showroom->email;
        if (empty($recipientEmail)) {
            return;
        }

        try {
            Mail::to($recipientEmail)->send(new SubscriptionExpiringMail($showroom, $subscription));
        } catch (Exception $e) {
            Log::error("Failed to send SubscriptionExpiringMail to {$recipientEmail}: ".$e->getMessage());
        }
    }

    /**
     * Dispatch notifications when a new job application is submitted (both applicant and dealership).
     */
    public function notifyJobApplicationSubmitted(JobApplication $application): void
    {
        $jobOpening = $application->jobOpening;
        $showroom = $application->showroom ?? $jobOpening?->showroom;
        if (! $showroom) {
            return;
        }

        // 1. Send confirmation acknowledgement to candidate (customer)
        if (! empty($application->applicant_email)) {
            try {
                Mail::to($application->applicant_email)->send(
                    new JobApplicationReceivedMail($showroom, $application, $jobOpening)
                );
            } catch (Exception $e) {
                Log::error("Failed to send JobApplicationReceivedMail to {$application->applicant_email}: ".$e->getMessage());
            }
        }

        // 2. Send candidate application alert to dealership owner / contact (user)
        $staffRecipient = $showroom->email ?: $showroom->owner?->email;
        if (! empty($staffRecipient)) {
            try {
                Mail::to($staffRecipient)->send(
                    new NewJobApplicationAlertMail($showroom, $application, $jobOpening)
                );
            } catch (Exception $e) {
                Log::error("Failed to send NewJobApplicationAlertMail to {$staffRecipient}: ".$e->getMessage());
            }
        }
    }
}
