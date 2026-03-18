<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->booking->loadMissing(['user', 'vehicle']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->booking->isWaitingList()
                ? 'Pembayaran Anda Sudah Diverifikasi dan Masuk Antrean'
                : 'Pembayaran Anda Sudah Diverifikasi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-success',
        );
    }
}