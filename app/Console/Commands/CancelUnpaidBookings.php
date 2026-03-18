<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelUnpaidBookings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:cancel-unpaid {--hours=1 : Maximum hours before auto-cancel}';

    /**
     * The console command description.
     */
    protected $description = 'Cancel bookings that have passed their payment deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');

        $unpaidBookings = Booking::with('user')
            ->where('status', 'pending')
            ->where('payment_status', 'pending')
            ->whereNull('payment_proof')
            ->get()
            ->filter(fn (Booking $booking) => $booking->isPastDeadline($hours))
            ->values();

        $count = $unpaidBookings->count();

        if ($count === 0) {
            $this->info('No unpaid bookings to cancel.');
            return 0;
        }

        $this->info("Found {$count} unpaid booking(s) past their payment deadline.");

        foreach ($unpaidBookings as $booking) {
            $booking->update([
                'status' => 'cancelled',
                'notes' => 'Otomatis dibatalkan karena melewati batas waktu pembayaran.',
            ]);

            $this->line("Cancelled booking #{$booking->id} for {$booking->user->name}");
        }

        $this->info("Successfully cancelled {$count} booking(s).");

        return 0;
    }
}
