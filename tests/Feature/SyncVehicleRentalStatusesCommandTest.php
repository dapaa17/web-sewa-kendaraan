<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SyncVehicleRentalStatusesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_marks_vehicle_rented_when_confirmed_booking_has_started(): void
    {
        Carbon::setTestNow('2026-03-09 10:00:00');

        $customer = $this->createCustomer();
        $vehicle = $this->createVehicle(['status' => 'available']);

        $this->createBookingFor($customer, $vehicle, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
        ]);

        $this->artisan('vehicles:sync-rental-statuses')
            ->expectsOutput("Vehicle #{$vehicle->id} synced to rented.")
            ->expectsOutput('Updated 1 vehicle(s). Rented: 1. Available: 0.')
            ->assertExitCode(0);

        $this->assertSame('rented', $vehicle->fresh()->status);

        Carbon::setTestNow();
    }

    public function test_command_marks_vehicle_available_when_next_confirmed_booking_has_not_started_yet(): void
    {
        Carbon::setTestNow('2026-03-09 10:00:00');

        $customer = $this->createCustomer();
        $vehicle = $this->createVehicle(['status' => 'rented']);

        $this->createBookingFor($customer, $vehicle, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
        ]);

        $this->artisan('vehicles:sync-rental-statuses')
            ->expectsOutput("Vehicle #{$vehicle->id} synced to available.")
            ->expectsOutput('Updated 1 vehicle(s). Rented: 0. Available: 1.')
            ->assertExitCode(0);

        $this->assertSame('available', $vehicle->fresh()->status);

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
            'name' => 'Sync Vehicle ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'BSYNC' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'transmission' => 'Manual',
            'year' => 2024,
            'daily_price' => 275000,
            'status' => 'available',
            'description' => 'Vehicle for sync command tests.',
        ], $overrides));

        $sequence++;

        return $vehicle;
    }

    private function createBookingFor(User $customer, Vehicle $vehicle, array $overrides = []): Booking
    {
        return Booking::create(array_merge([
            'user_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'duration_days' => 3,
            'daily_price' => (float) $vehicle->daily_price,
            'total_price' => (float) $vehicle->daily_price * 3,
            'status' => 'pending',
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
        ], $overrides));
    }
}