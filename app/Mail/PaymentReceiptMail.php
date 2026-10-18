<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\Showroom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Showroom $showroom,
        public Payment $payment
    ) {}

    public function envelope(): Envelope
    {
        $receiptId = '#PAY-'.str_pad((string) $this->payment->id, 6, '0', STR_PAD_LEFT);

        return new Envelope(
            subject: "Payment Receipt ({$receiptId}): ₹".number_format($this->payment->amount)." Captured - {$this->showroom->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payments.receipt',
        );
    }
}
