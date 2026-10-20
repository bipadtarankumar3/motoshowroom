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

class StaffCreatedNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Showroom $showroom,
        public User $staffUser,
        public ShowroomMembership $membership,
        public User $creator
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Staff Member Added: {$this->staffUser->name} — {$this->showroom->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.staff.created_alert',
        );
    }
}
