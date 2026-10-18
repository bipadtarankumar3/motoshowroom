<?php

namespace App\Mail;

use App\Models\Enquiry;
use App\Models\Showroom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEnquiryAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Showroom $showroom,
        public Enquiry $enquiry
    ) {}

    public function envelope(): Envelope
    {
        $subjectPrefix = $this->enquiry->isTestRide() ? 'Test Ride Booking' : 'New Customer Lead';

        return new Envelope(
            subject: "[Action Required] {$subjectPrefix}: {$this->enquiry->customer_name} (#{$this->enquiry->enquiry_number})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.enquiries.alert',
        );
    }
}
