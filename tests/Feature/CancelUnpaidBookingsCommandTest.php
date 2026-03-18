<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CancelUnpaidBookingsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_cancels_booking_that_is_past_its_payment_deadline(): void
    {
        Carbon::setTestNow('2026-03-09 10:00:00');

        $customer = $this->createCustomer();
        $firstVehicle = $this->createVehicle([
            'status' => 'rented',
        ]);
        $secondVehicle = $this->createVehicle();

        $firstBooking = $this->createBookingFor($customer, $firstVehicle, [
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'duration_days' => 1,
            'total_price' => (float) $firstVehicle->daily_price,
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2),
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_proof' => null,
        ]);

        $secondBooking = $this->createBookingFor($customer, $secondVehicle, [
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2),
            'payment_proof' => null,
        ]);

        $this->artisan('bookings:cancel-unpaid --hours=1')
            ->expectsOutput('Found 2 unpaid booking(s) past their payment deadline.')
            ->expectsOutput('Successfully cancelled 2 booking(s).')
            ->assertExitCode(0);

        $firstBooking->refresh();
        $secondBooking->refresh();
        $firstVehicle->refresh();

        $this->assertSame('cancelled', $firstBooking->status);
        $this->assertSame('cancelled', $secondBooking->status);
        $this->assertStringContainsString('melewati batas waktu pembayaran', (string) $firstBooking->notes);
        $this->assertStringContainsString('melewati batas waktu pembayaran', (string) $secondBooking->notes);
        $this->assertSame('rented', $firstVehicle->status);

        Carbon::setTestNow();
    }

    public function test_command_skips_recent_or_proven_bookings(): void
    {
        Carbon::setTestNow('2026-03-09 10:00:00');

        $customer = $this->createCustomer();

        $recentVehicle = $this->createVehicle();
        $recentBooking = $this->createBookingFor($customer, $recentVehicle, [
            'created_at' => now()->subMinutes(30),
            'updated_at' => now()->subMinutes(30),
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'duration_days' => 1,
            'total_price' => (float) $recentVehicle->daily_price,
            'payment_proof' => null,
        ]);

        $proofVehicle = $this->createVehicle();
        $proofBooking = $this->createBookingFor($customer, $proofVehicle, [
            'created_at' => now()->subHours(30),
            'updated_at' => now()->subHours(30),
            'payment_proof' => 'proofs/existing-proof.jpg',
        ]);

        $this->artisan('bookings:cancel-unpaid --hours=1')
            ->expectsOutput('No unpaid bookings to cancel.')
            ->assertExitCode(0);

        $this->assertSame('pending', $recentBooking->fresh()->status);
        $this->assertSame('pending', $proofBooking->fresh()->status);

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
            'name' => 'Command Vehicle ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'BCMD' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'transmission' => 'Manual',
            'year' => 2024,
            'daily_price' => 300000,
            'status' => 'available',
            'description' => 'Vehicle for command tests.',
        ], $overrides));

        $sequence++;

        return $vehicle;
    }

    private function createBookingFor(User $customer, Vehicle $vehicle, array $overrides = []): Booking
    {
        $timestamps = [];

        foreach (['created_at', 'updated_at'] as $column) {
            if (array_key_exists($column, $overrides)) {
                $timestamps[$column] = $overrides[$column];
                unset($overrides[$column]);
            }
        }

        $booking = new Booking(array_merge([
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
            'payment_proof' => null,
            'notes' => null,
        ], $overrides));

        $booking->save();

        if ($timestamps !== []) {
            $booking->forceFill($timestamps)->saveQuietly();
        }

        return $booking;
    }
}