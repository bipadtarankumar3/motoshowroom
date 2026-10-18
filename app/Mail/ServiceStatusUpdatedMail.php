<?php

namespace App\Mail;

use App\Models\ServiceBooking;
use App\Models\Showroom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceStatusUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Showroom $showroom,
        public ServiceBooking $booking
    ) {}

    public function envelope(): Envelope
    {
        $statusText = $this->booking->isReady() || $this->booking->isCompleted()
            ? 'Ready for Delivery'
            : $this->booking->status->label();

        return new Envelope(
            subject: "Update on Your Motorcycle: {$statusText} (#{$this->booking->booking_number}) - {$this->showroom->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.services.status-updated',
        );
    }
}
