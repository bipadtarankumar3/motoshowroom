<?php

namespace App\Mail;

use App\Models\Showroom;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $daysRemaining;

    public function __construct(
        public Showroom $showroom,
        public Subscription $subscription
    ) {
        $this->daysRemaining = max(0, (int) now()->diffInDays($this->subscription->current_period_end, false));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Action Required: Your Showroom Plan Expires in {$this->daysRemaining} Day(s) - {$this->showroom->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscriptions.expiring',
            with: [
                'daysRemaining' => $this->daysRemaining,
            ],
        );
    }
}
