<?php

namespace App\Mail;

use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Showroom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApplicationReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Showroom $showroom,
        public JobApplication $application,
        public ?JobOpening $jobOpening = null
    ) {}

    public function envelope(): Envelope
    {
        $roleTitle = $this->jobOpening?->title ?? ($this->application->desired_role ?: 'General Application');

        return new Envelope(
            subject: "Application Received: {$roleTitle} (#{$this->application->applicant_number}) - {$this->showroom->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.careers.received',
        );
    }
}
