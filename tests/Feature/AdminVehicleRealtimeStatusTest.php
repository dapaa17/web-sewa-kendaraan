<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVehicleRealtimeStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_vehicle_management_page_uses_current_rental_status(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');

        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $vehicle = $this->createVehicle([
            'name' => 'Admin Realtime Vehicle',
            'status' => 'available',
        ]);

        $this->createBookingForVehicle($vehicle, $customer, [
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => (float) $vehicle->daily_price * 2,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.vehicles.index'))
            ->assertOk()
            ->assertSee($vehicle->name)
            ->assertSee('Rented');
    }

    public function test_admin_dashboard_counts_use_current_rental_status(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');

        $admin = $this->createAdmin();
        $customer = $this->createCustomer();

        $rentedVehicle = $this->createVehicle([
            'name' => 'Dashboard Rented Vehicle',
            'status' => 'available',
        ]);

        $availableVehicle = $this->createVehicle([
            'name' => 'Dashboard Available Vehicle',
            'status' => 'available',
        ]);

        $maintenanceVehicle = $this->createVehicle([
            'name' => 'Dashboard Maintenance Vehicle',
            'status' => 'maintenance',
        ]);

        $this->createBookingForVehicle($rentedVehicle, $customer, [
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => (float) $rentedVehicle->daily_price * 2,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('totalVehicles', 3)
            ->assertViewHas('availableVehicles', 1)
            ->assertViewHas('rentedVehicles', 1)
            ->assertViewHas('maintenanceVehicles', 1);
    }

    public function test_vehicle_update_does_not_require_manual_status_and_resyncs_it(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');

        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $vehicle = $this->createVehicle([
            'name' => 'Editable Realtime Vehicle',
            'status' => 'available',
        ]);

        $this->createBookingForVehicle($vehicle, $customer, [
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'duration_days' => 2,
            'total_price' => (float) $vehicle->daily_price * 2,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.vehicles.update', $vehicle), [
                'name' => 'Editable Realtime Vehicle Updated',
                'vehicle_type' => $vehicle->vehicle_type,
                'plat_number' => $vehicle->plat_number,
                'transmission' => 'Otomatis',
                'year' => 2025,
                'daily_price' => 450000,
                'description' => 'Updated description',
            ]);

        $response->assertRedirect(route('admin.vehicles.index'));
        $response->assertSessionHas('success', 'Kendaraan berhasil diperbarui');

        $vehicle->refresh();

        $this->assertSame('Editable Realtime Vehicle Updated', $vehicle->name);
        $this->assertSame('Otomatis', $vehicle->transmission);
        $this->assertSame(2025, $vehicle->year);
        $this->assertSame('450000.00', $vehicle->daily_price);
        $this->assertSame('rented', $vehicle->status);
    }

    public function test_same_day_future_pickup_marks_vehicle_as_rented_after_payment(): void
    {
        Carbon::setTestNow('2026-03-10 08:00:00');

        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $vehicle = $this->createVehicle([
            'name' => 'Future Pickup Vehicle',
            'status' => 'available',
        ]);

        $this->createBookingForVehicle($vehicle, $customer, [
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'pickup_time' => '09:00:00',
            'return_time' => '17:00:00',
            'duration_days' => 1,
            'total_price' => (float) $vehicle->daily_price,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $vehicle->refresh();

        $this->assertSame('rented', $vehicle->current_rental_status);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('availableVehicles', 0)
            ->assertViewHas('rentedVehicles', 1);
    }

    private function createAdmin(array $overrides = []): User
    {
        /** @var User $user */
        $user = User::factory()->create(array_merge([
            'role' => 'admin',
        ], $overrides));

        return $user;
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
            'name' => 'Admin Vehicle ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'BADMIN' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
            'transmission' => 'Manual',
            'year' => 2024,
            'daily_price' => 300000,
            'status' => 'available',
            'description' => 'Vehicle for admin realtime status testing.',
        ], $overrides));

        $sequence++;

        return $vehicle;
    }

    private function createBookingForVehicle(Vehicle $vehicle, User $customer, array $overrides = []): Booking
    {
        return Booking::create(array_merge([
            'user_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'duration_days' => 2,
            'daily_price' => (float) $vehicle->daily_price,
            'total_price' => (float) $vehicle->daily_price * 2,
            'status' => 'pending',
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
        ], $overrides));
    }
}