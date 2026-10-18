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

class EnquiryReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Showroom $showroom,
        public Enquiry $enquiry
    ) {}

    public function envelope(): Envelope
    {
        $subjectPrefix = $this->enquiry->isTestRide() ? 'Test Ride Request' : 'Motorcycle Enquiry';

        return new Envelope(
            subject: "{$subjectPrefix} Received (#{$this->enquiry->enquiry_number}) - {$this->showroom->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.enquiries.received',
        );
    }
}
