<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\MaintenanceSchedule;
use App\Models\PricingRule;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_vehicle_availability_api_returns_daily_statuses_for_booked_and_maintenance_dates(): void
    {
        Carbon::setTestNow('2026-03-01 10:00:00');

        $user = $this->createCustomer();
        $vehicle = $this->createVehicle([
            'daily_price' => 500000,
            'base_price' => 500000,
            'weekend_multiplier' => 1.2,
            'peak_season_multiplier' => 1.4,
            'low_season_multiplier' => 0.8,
        ]);

        $this->createBookingForVehicle($vehicle, [
            'start_date' => '2026-03-10',
            'end_date' => '2026-03-12',
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        MaintenanceSchedule::create([
            'vehicle_id' => $vehicle->id,
            'start_date' => '2026-03-15',
            'end_date' => '2026-03-17',
            'reason' => 'Servis berkala',
        ]);

        $response = $this->actingAs($user)->getJson(route('api.vehicle.availability', $vehicle, false) . '?month=3&year=2026');

        $response->assertOk();

        $dates = collect($response->json('dates'));
        $bookedDate = $dates->firstWhere('date', '2026-03-10');
        $maintenanceDate = $dates->firstWhere('date', '2026-03-15');
        $weekendDate = $dates->firstWhere('date', '2026-03-07');

        $this->assertSame('booked', $bookedDate['status']);
        $this->assertSame('maintenance', $maintenanceDate['status']);
        $this->assertSame('Servis berkala', $maintenanceDate['reason']);
        $this->assertEquals(600000.0, $weekendDate['final_price']);
    }

    public function test_price_api_and_booking_store_use_dynamic_daily_pricing(): void
    {
        Carbon::setTestNow('2026-03-01 10:00:00');

        $user = $this->createCustomer();
        $vehicle = $this->createVehicle([
            'daily_price' => 500000,
            'base_price' => 500000,
            'weekend_multiplier' => 1.2,
            'peak_season_multiplier' => 1.4,
            'low_season_multiplier' => 0.8,
        ]);

        $priceResponse = $this->actingAs($user)->getJson(route('api.vehicle.price', $vehicle, false) . '?start_date=2026-03-06&end_date=2026-03-08');

        $priceResponse
            ->assertOk()
            ->assertJsonPath('duration_days', 3)
            ->assertJsonPath('total', 1800000)
            ->assertJsonPath('daily_prices.0.price', 600000)
            ->assertJsonPath('daily_prices.1.price', 600000)
            ->assertJsonPath('daily_prices.2.price', 600000);

        $bookingResponse = $this->actingAs($user)->post(route('bookings.store'), [
            'vehicle_id' => $vehicle->id,
            'start_date' => '2026-03-06',
            'end_date' => '2026-03-08',
        ]);

        $booking = Booking::query()->latest('id')->first();

        $bookingResponse->assertRedirect(route('bookings.show', $booking));
        $this->assertNotNull($booking);
        $this->assertSame(3, $booking->duration_days);
        $this->assertEquals(1800000.0, (float) $booking->total_price);
        $this->assertEquals(600000.0, (float) $booking->daily_price);
    }

    public function test_admin_can_block_unblock_dates_and_store_pricing_rule(): void
    {
        Carbon::setTestNow('2026-03-01 10:00:00');

        $admin = $this->createAdmin();
        $vehicle = $this->createVehicle();

        $this->actingAs($admin)
            ->postJson(route('admin.calendar.block-dates'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => '2026-03-20',
                'end_date' => '2026-03-22',
                'reason' => 'Pembersihan unit',
                'notes' => 'Cek interior',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Tanggal maintenance berhasil diblokir.');

        $this->assertTrue(
            MaintenanceSchedule::query()
                ->where('vehicle_id', $vehicle->id)
                ->get()
                ->contains(fn (MaintenanceSchedule $schedule) => $schedule->start_date->toDateString() === '2026-03-20'
                    && $schedule->end_date->toDateString() === '2026-03-22'
                    && $schedule->reason === 'Pembersihan unit')
        );

        $this->actingAs($admin)
            ->postJson(route('admin.calendar.pricing-rules'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => '2026-03-24',
                'end_date' => '2026-03-31',
                'discount_percentage' => 15,
                'type' => 'custom',
                'description' => 'Promo akhir bulan',
                'active' => true,
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Aturan harga berhasil disimpan.');

        $this->assertDatabaseHas('pricing_rules', [
            'vehicle_id' => $vehicle->id,
            'discount_percentage' => 15,
            'type' => 'custom',
            'description' => 'Promo akhir bulan',
            'active' => 1,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.calendar.unblock-dates'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => '2026-03-21',
                'end_date' => '2026-03-21',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Blok maintenance berhasil dibuka.');

        $remainingSchedules = MaintenanceSchedule::query()
            ->where('vehicle_id', $vehicle->id)
            ->get();

        $this->assertTrue(
            $remainingSchedules->contains(fn (MaintenanceSchedule $schedule) => $schedule->start_date->toDateString() === '2026-03-20'
                && $schedule->end_date->toDateString() === '2026-03-20')
        );

        $this->assertTrue(
            $remainingSchedules->contains(fn (MaintenanceSchedule $schedule) => $schedule->start_date->toDateString() === '2026-03-22'
                && $schedule->end_date->toDateString() === '2026-03-22')
        );
    }

    public function test_customer_can_view_vehicle_calendar_page(): void
    {
        Carbon::setTestNow('2026-03-01 10:00:00');

        $customer = $this->createCustomer();
        $vehicle = $this->createVehicle();

        $this->actingAs($customer)
            ->get(route('vehicles.calendar', $vehicle))
            ->assertOk()
            ->assertSee('Kalender Ketersediaan', false)
            ->assertSee('calendarBookingForm', false);
    }

    public function test_admin_can_view_fleet_calendar_page(): void
    {
        Carbon::setTestNow('2026-03-01 10:00:00');

        $admin = $this->createAdmin();
        $this->createVehicle([
            'name' => 'Armada Kalender',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.calendar.index'))
            ->assertOk()
            ->assertSee('Kalender Armada Real-Time', false)
            ->assertSee('fleetBoard', false);
    }

    public function test_customer_is_redirected_away_from_admin_calendar_page(): void
    {
        Carbon::setTestNow('2026-03-01 10:00:00');

        $customer = $this->createCustomer();

        $this->actingAs($customer)
            ->get(route('admin.calendar.index'))
            ->assertRedirect(route('dashboard'));
    }

    private function createCustomer(array $overrides = []): User
    {
        /** @var User $user */
        $user = User::factory()->create(array_merge([
            'role' => 'customer',
        ], $overrides));

        return $user;
    }

    private function createAdmin(array $overrides = []): User
    {
        /** @var User $user */
        $user = User::factory()->create(array_merge([
            'role' => 'admin',
        ], $overrides));

        return $user;
    }

    private function createVehicle(array $overrides = []): Vehicle
    {
        static $sequence = 1;

        $vehicle = Vehicle::create(array_merge([
            'name' => 'Calendar Vehicle ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'BCAL' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'transmission' => 'Manual',
            'year' => 2024,
            'daily_price' => 350000,
            'status' => 'available',
            'description' => 'Vehicle for calendar testing.',
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
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'duration_days' => 3,
            'daily_price' => (float) $vehicle->daily_price,
            'total_price' => (float) $vehicle->daily_price * 3,
            'status' => 'pending',
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
        ], $overrides));
    }
}