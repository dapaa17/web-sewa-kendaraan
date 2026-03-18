<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public ?string $errorMessage = null)
    {
        $this->booking->loadMissing(['user', 'vehicle']);
        $this->errorMessage ??= 'Pembayaran belum dapat kami verifikasi.';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran Booking Anda Ditolak',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-failed',
            with: [
                'errorMessage' => $this->errorMessage,
            ],
        );
    }
}