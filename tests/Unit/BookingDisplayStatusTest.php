<?php

namespace Tests\Unit;

use App\Models\Booking;
use PHPUnit\Framework\TestCase;

class BookingDisplayStatusTest extends TestCase
{
    public function test_failed_payment_booking_uses_payment_failed_display_status(): void
    {
        $pendingFailedBooking = new Booking([
            'status' => 'pending',
            'payment_status' => 'failed',
        ]);

        $cancelledFailedBooking = new Booking([
            'status' => 'cancelled',
            'payment_status' => 'failed',
        ]);

        $this->assertSame('payment_failed', $pendingFailedBooking->getDisplayStatusKey());
        $this->assertSame('Pembayaran Ditolak', $pendingFailedBooking->getDisplayStatusLabel());
        $this->assertSame('payment_failed', $cancelledFailedBooking->getDisplayStatusKey());
        $this->assertSame('Pembayaran Ditolak', $cancelledFailedBooking->getDisplayStatusLabel());
    }
}