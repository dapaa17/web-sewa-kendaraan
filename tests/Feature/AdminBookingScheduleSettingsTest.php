<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingScheduleSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_booking_settings_page(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan Admin')
            ->assertSee('Default Jadwal Booking')
            ->assertSee('Rentang jadwal default')
            ->assertSee('Jadwal Booking');
    }

    public function test_admin_can_update_default_booking_schedule_times(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->post(route('admin.settings.booking-schedule'), [
                'pickup_time' => '08:30',
                'return_time' => '18:30',
            ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success', 'Jam default booking berhasil diperbarui.');

        $this->assertDatabaseHas('app_settings', [
            'key' => 'booking_default_pickup_time',
            'value' => '08:30',
        ]);
        $this->assertDatabaseHas('app_settings', [
            'key' => 'booking_default_return_time',
            'value' => '18:30',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertViewHas('bookingScheduleDefaults', [
                'pickup_time' => '08:30',
                'return_time' => '18:30',
            ]);
    }

    public function test_booking_form_and_store_use_admin_schedule_defaults(): void
    {
        AppSetting::setValue('booking_default_pickup_time', '08:15');
        AppSetting::setValue('booking_default_return_time', '19:45');

        $customer = $this->createCustomer();
        $vehicle = $this->createVehicle();

        $this->actingAs($customer)
            ->get(route('bookings.create', $vehicle))
            ->assertOk()
            ->assertSee('value="08:15"', false)
            ->assertSee('value="19:45"', false);

        $response = $this->actingAs($customer)
            ->post(route('bookings.store'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => now()->addDays(5)->toDateString(),
                'end_date' => now()->addDays(6)->toDateString(),
            ]);

        $booking = Booking::query()->latest('id')->first();

        $response->assertRedirect(route('bookings.show', $booking));
        $this->assertNotNull($booking);
        $this->assertSame('08:15:00', $booking->pickup_time);
        $this->assertSame('19:45:00', $booking->return_time);
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
            'ktp_status' => 'verified',
            'ktp_verified_at' => now(),
        ], $overrides));

        return $user;
    }

    private function createVehicle(array $overrides = []): Vehicle
    {
        static $sequence = 1;

        $vehicle = Vehicle::create(array_merge([
            'name' => 'Schedule Setting Vehicle ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'BSET' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'transmission' => 'Manual',
            'year' => 2024,
            'daily_price' => 325000,
            'status' => 'available',
            'description' => 'Vehicle for booking schedule settings tests.',
        ], $overrides));

        $sequence++;

        return $vehicle;
    }
}