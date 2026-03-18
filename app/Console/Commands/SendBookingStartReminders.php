<?php

namespace App\Console\Commands;

use App\Mail\BookingStartReminderMail;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBookingStartReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:send-start-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Send H-1 reminder emails for confirmed bookings that start tomorrow';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $bookings = Booking::query()
            ->with(['user', 'vehicle'])
            ->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->whereDate('start_date', now()->addDay()->toDateString())
            ->whereNull('reminder_sent_at')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No booking reminders to send.');

            return self::SUCCESS;
        }

        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->user)->send(new BookingStartReminderMail($booking));

                $booking->update([
                    'reminder_sent_at' => now(),
                ]);

                $this->line("Reminder sent for booking #{$booking->id}.");
            } catch (\Throwable $exception) {
                report($exception);
                $this->error("Failed to send reminder for booking #{$booking->id}.");
            }
        }

        $this->info('Booking start reminders processed.');

        return self::SUCCESS;
    }
}