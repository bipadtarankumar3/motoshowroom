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

class ServiceBookingConfirmedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Showroom $showroom,
        public ServiceBooking $booking
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Service Appointment Confirmed (#{$this->booking->booking_number}) - {$this->showroom->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.services.confirmed',
        );
    }
}
