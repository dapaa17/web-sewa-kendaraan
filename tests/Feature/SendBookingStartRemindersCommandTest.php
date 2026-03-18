<?php

namespace Tests\Feature;

use App\Mail\BookingStartReminderMail;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendBookingStartRemindersCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_sends_h_minus_one_reminder_once_for_eligible_booking(): void
    {
        Carbon::setTestNow('2026-03-09 10:00:00');
        Mail::fake();

        $customer = $this->createCustomer();
        $vehicle = $this->createVehicle();
        $booking = $this->createBookingFor($customer, $vehicle, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
        ]);

        $this->artisan('bookings:send-start-reminders')
            ->expectsOutput("Reminder sent for booking #{$booking->id}.")
            ->expectsOutput('Booking start reminders processed.')
            ->assertExitCode(0);

        $booking->refresh();

        $this->assertNotNull($booking->reminder_sent_at);
        Mail::assertSent(BookingStartReminderMail::class, function (BookingStartReminderMail $mail) use ($customer, $booking) {
            return $mail->hasTo($customer->email)
                && $mail->booking->id === $booking->id;
        });

        $this->artisan('bookings:send-start-reminders')
            ->expectsOutput('No booking reminders to send.')
            ->assertExitCode(0);

        Carbon::setTestNow();
    }

    public function test_command_skips_waiting_list_and_non_tomorrow_bookings(): void
    {
        Carbon::setTestNow('2026-03-09 10:00:00');
        Mail::fake();

        $customer = $this->createCustomer();

        $tomorrowVehicle = $this->createVehicle(['name' => 'Tomorrow Waitlist']);
        $futureVehicle = $this->createVehicle(['name' => 'Future Confirmed']);

        $waitingListBooking = $this->createBookingFor($customer, $tomorrowVehicle, [
            'status' => 'waiting_list',
            'payment_status' => 'paid',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
        ]);

        $futureBooking = $this->createBookingFor($customer, $futureVehicle, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
        ]);

        $this->artisan('bookings:send-start-reminders')
            ->expectsOutput('No booking reminders to send.')
            ->assertExitCode(0);

        $this->assertNull($waitingListBooking->fresh()->reminder_sent_at);
        $this->assertNull($futureBooking->fresh()->reminder_sent_at);
        Mail::assertNothingSent();

        Carbon::setTestNow();
    }

    private function createCustomer(array $overrides = []): User
    {
        /** @var User $user */
        $user = User::factory()->create(array_merge([
            'role' => 'customer',
        ], $overrides));

        return $user;
    }

    private function createVehicle(array $overrides = []): Vehicle
    {
        static $sequence = 1;

        $vehicle = Vehicle::create(array_merge([
            'name' => 'Reminder Vehicle ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'BREM' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'transmission' => 'Manual',
            'year' => 2024,
            'daily_price' => 310000,
            'status' => 'available',
            'description' => 'Vehicle for reminder command tests.',
        ], $overrides));

        $sequence++;

        return $vehicle;
    }

    private function createBookingFor(User $customer, Vehicle $vehicle, array $overrides = []): Booking
    {
        return Booking::create(array_merge([
            'user_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'duration_days' => 3,
            'daily_price' => (float) $vehicle->daily_price,
            'total_price' => (float) $vehicle->daily_price * 3,
            'status' => 'pending',
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
        ], $overrides));
    }
}