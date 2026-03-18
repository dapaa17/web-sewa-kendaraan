<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleBrowseAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_browse_date_filter_hides_maintenance_and_overlapping_vehicles(): void
    {
        $user = $this->createCustomer();
        $startDate = now()->addDays(10)->toDateString();
        $endDate = now()->addDays(12)->toDateString();

        $availableVehicle = $this->createVehicle(['name' => 'Toyota Available']);
        $maintenanceVehicle = $this->createVehicle([
            'name' => 'Toyota Maintenance',
            'status' => 'maintenance',
        ]);
        $blockedVehicle = $this->createVehicle(['name' => 'Toyota Blocked']);

        $this->createBookingForVehicle($blockedVehicle, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('vehicles.browse', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]));

        $response->assertOk();
        $response->assertSee($availableVehicle->name);
        $response->assertDontSee($maintenanceVehicle->name);
        $response->assertDontSee($blockedVehicle->name);
        $response->assertSee('Tersedia di tanggal ini');
    }

    public function test_browse_date_filter_keeps_failed_payment_bookings_available(): void
    {
        $user = $this->createCustomer();
        $startDate = now()->addDays(14)->toDateString();
        $endDate = now()->addDays(16)->toDateString();

        $vehicle = $this->createVehicle(['name' => 'Toyota Failed Payment']);

        $this->createBookingForVehicle($vehicle, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'payment_status' => 'failed',
        ]);

        $this->actingAs($user)
            ->get(route('vehicles.browse', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]))
            ->assertOk()
            ->assertSee($vehicle->name);
    }

    public function test_browse_date_filter_can_show_rented_vehicle_if_selected_dates_do_not_overlap(): void
    {
        $user = $this->createCustomer();
        $vehicle = $this->createVehicle([
            'name' => 'Toyota Future Ready',
            'status' => 'rented',
        ]);

        $this->createBookingForVehicle($vehicle, [
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $startDate = now()->addDays(10)->toDateString();
        $endDate = now()->addDays(12)->toDateString();

        $this->actingAs($user)
            ->get(route('vehicles.browse', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]))
            ->assertOk()
            ->assertSee($vehicle->name)
            ->assertSee('Tersedia di tanggal ini');
    }

    public function test_browse_date_filter_can_show_overlapping_paid_booking_as_waiting_list_option(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');

        $user = $this->createCustomer();
        $startDate = now()->addDay()->toDateString();
        $endDate = now()->addDays(2)->toDateString();

        $vehicle = $this->createVehicle([
            'name' => 'Toyota Waiting Queue',
            'status' => 'rented',
        ]);

        $this->createBookingForVehicle($vehicle, [
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($user)
            ->get(route('vehicles.browse', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]))
            ->assertOk()
            ->assertSee($vehicle->name)
            ->assertSee('Bisa Antre');

        Carbon::setTestNow();
    }

    public function test_browse_date_filter_hides_future_confirmed_paid_booking_that_is_not_waiting_list_candidate(): void
    {
        $user = $this->createCustomer();
        $startDate = now()->addDays(10)->toDateString();
        $endDate = now()->addDays(12)->toDateString();

        $vehicle = $this->createVehicle([
            'name' => 'Toyota Future Confirmed',
            'status' => 'available',
        ]);

        $this->createBookingForVehicle($vehicle, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($user)
            ->get(route('vehicles.browse', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]))
            ->assertOk()
            ->assertDontSee($vehicle->name);
    }

    public function test_browse_without_date_filter_uses_current_rental_status_for_active_booking(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');

        $user = $this->createCustomer();
        $vehicle = $this->createVehicle([
            'name' => 'Toyota Realtime Browse',
            'status' => 'available',
        ]);

        $this->createBookingForVehicle($vehicle, [
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($user)
            ->get(route('vehicles.browse'))
            ->assertOk()
            ->assertSee($vehicle->name)
            ->assertSee('Disewa');
    }

    public function test_vehicle_show_uses_current_rental_status_for_active_booking(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');

        $user = $this->createCustomer();
        $vehicle = $this->createVehicle([
            'name' => 'Toyota Realtime Detail',
            'status' => 'available',
        ]);

        $this->createBookingForVehicle($vehicle, [
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($user)
            ->get(route('vehicles.show', $vehicle))
            ->assertOk()
            ->assertSee('Sedang Disewa')
            ->assertSee('Kendaraan sedang dipakai pada jadwal lain');
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
            'name' => 'Browse Vehicle ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'BBROWSE' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
            'transmission' => 'Manual',
            'year' => 2024,
            'daily_price' => 300000,
            'status' => 'available',
            'description' => 'Vehicle for browse availability testing.',
        ], $overrides));

        $sequence++;

        return $vehicle;
    }

    private function createBookingForVehicle(Vehicle $vehicle, array $overrides = []): Booking
    {
        $customer = $this->createCustomer();

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