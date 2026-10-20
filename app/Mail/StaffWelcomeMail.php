<?php

namespace App\Mail;

use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Showroom $showroom,
        public User $staffUser,
        public ShowroomMembership $membership,
        public ?string $plainPassword = null,
        public ?User $creator = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Welcome to {$this->showroom->name} Staff Team — Access Details",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.staff.welcome',
        );
    }
}
