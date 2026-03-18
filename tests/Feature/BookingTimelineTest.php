<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_weekly_booking_timeline(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');

        $admin = $this->createAdmin();
        $activeCustomer = $this->createVerifiedCustomer(['name' => 'Customer Aktif']);
        $delayedCustomer = $this->createVerifiedCustomer(['name' => 'Customer Menunggu Unit']);
        $queueCustomer = $this->createVerifiedCustomer(['name' => 'Customer Antrean']);
        $scheduledCustomer = $this->createVerifiedCustomer(['name' => 'Customer Terjadwal']);

        $activeVehicle = $this->createVehicle(['name' => 'Toyota Timeline Aman']);
        $problemVehicle = $this->createVehicle(['name' => 'Honda Timeline Padat']);
        $motorVehicle = $this->createVehicle(['name' => 'Yamaha Timeline Motor', 'vehicle_type' => 'motor']);

        $activeBooking = $this->createBookingFor($activeCustomer, [
            'vehicle_id' => $activeVehicle->id,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->createBookingFor($activeCustomer, [
            'vehicle_id' => $problemVehicle->id,
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $delayedBooking = $this->createBookingFor($delayedCustomer, [
            'vehicle_id' => $problemVehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $waitingListBooking = $this->createBookingFor($queueCustomer, [
            'vehicle_id' => $motorVehicle->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'waiting_list',
            'payment_status' => 'paid',
        ]);

        $scheduledBooking = $this->createBookingFor($scheduledCustomer, [
            'vehicle_id' => $motorVehicle->id,
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.bookings.timeline', ['week' => now()->toDateString()]));

        $response->assertOk()
            ->assertSee('Timeline Booking Mingguan')
            ->assertSee('Timeline Ringkas Booking')
            ->assertSee('Toyota Timeline Aman')
            ->assertSee('Honda Timeline Padat')
            ->assertSee('Yamaha Timeline Motor')
            ->assertSee('Sedang Disewa')
            ->assertSee('Menunggu Unit')
            ->assertSee('Antrean')
            ->assertSee('Terjadwal')
            ->assertSee(route('bookings.show', $activeBooking))
            ->assertSee(route('bookings.show', $delayedBooking))
            ->assertSee(route('bookings.show', $waitingListBooking))
            ->assertSee(route('bookings.show', $scheduledBooking));

        Carbon::setTestNow();
    }

    public function test_timeline_marks_confirmed_booking_on_maintenance_vehicle_as_maintenance_hold(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');

        $admin = $this->createAdmin();
        $customer = $this->createVerifiedCustomer(['name' => 'Customer Maintenance']);
        $vehicle = $this->createVehicle([
            'name' => 'Toyota Maintenance Hold',
            'status' => 'maintenance',
        ]);

        $booking = $this->createBookingFor($customer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'maintenance_hold_at' => now()->subHour(),
            'maintenance_hold_reason' => 'Unit masuk maintenance setelah inspeksi pengembalian.',
            'notes' => 'Unit masuk maintenance setelah inspeksi pengembalian.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.bookings.timeline', ['week' => now()->toDateString(), 'problem_only' => 1]))
            ->assertOk()
            ->assertSee('Toyota Maintenance Hold')
            ->assertSee('Tertahan Maintenance')
            ->assertSee(route('bookings.show', $booking));

        Carbon::setTestNow();
    }

    public function test_admin_can_filter_timeline_by_vehicle_type_search_and_problem_only(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');

        $admin = $this->createAdmin();
        $problemCustomer = $this->createVerifiedCustomer(['name' => 'Problem Search']);
        $otherCustomer = $this->createVerifiedCustomer(['name' => 'Customer Biasa']);

        $problemVehicle = $this->createVehicle(['name' => 'Suzuki Problem Mobil']);
        $safeVehicle = $this->createVehicle(['name' => 'Toyota Aman Mobil']);
        $motorVehicle = $this->createVehicle(['name' => 'Vespa Aman Motor', 'vehicle_type' => 'motor']);

        $this->createBookingFor($otherCustomer, [
            'vehicle_id' => $problemVehicle->id,
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->createBookingFor($problemCustomer, [
            'vehicle_id' => $problemVehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->createBookingFor($otherCustomer, [
            'vehicle_id' => $safeVehicle->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->createBookingFor($otherCustomer, [
            'vehicle_id' => $motorVehicle->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.bookings.timeline', [
                'week' => now()->toDateString(),
                'vehicle_type' => 'mobil',
                'search' => 'Problem Search',
                'problem_only' => 1,
            ]));

        $response->assertOk()
            ->assertSee('Suzuki Problem Mobil')
            ->assertSee('Menunggu Unit')
            ->assertDontSee('Toyota Aman Mobil')
            ->assertDontSee('Vespa Aman Motor');

        Carbon::setTestNow();
    }

    public function test_timeline_keeps_same_day_future_pickup_as_scheduled_until_pickup_time(): void
    {
        Carbon::setTestNow('2026-03-10 08:00:00');

        $admin = $this->createAdmin();
        $customer = $this->createVerifiedCustomer(['name' => 'Customer Jam Pickup']);
        $vehicle = $this->createVehicle(['name' => 'Toyota Belum Mulai Hari Ini']);

        $booking = $this->createBookingFor($customer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'pickup_time' => '09:00:00',
            'return_time' => '17:00:00',
            'duration_days' => 1,
            'total_price' => (float) $vehicle->daily_price,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.bookings.timeline', ['week' => now()->toDateString()]));

        $response->assertOk()
            ->assertSee('Toyota Belum Mulai Hari Ini')
            ->assertSee(route('bookings.show', $booking))
            ->assertViewHas('timelineVehicles', function ($timelineVehicles) use ($booking) {
                $event = collect($timelineVehicles)
                    ->flatMap(fn (array $row) => collect($row['lanes'])->flatten(1))
                    ->first(fn (array $event) => $event['booking']->id === $booking->id);

                return $event !== null && $event['label'] === 'Terjadwal';
            });

        Carbon::setTestNow();
    }

    public function test_customer_cannot_access_booking_timeline(): void
    {
        $customer = $this->createVerifiedCustomer();

        $this->actingAs($customer)
            ->get(route('admin.bookings.timeline'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    private function createVerifiedCustomer(array $overrides = []): User
    {
        /** @var User $user */
        $user = User::factory()->create(array_merge([
            'role' => 'customer',
            'ktp_status' => 'verified',
            'ktp_verified_at' => now(),
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

    private function createBookingFor(User $user, array $overrides = []): Booking
    {
        $vehicle = $this->createVehicle();
        $startDate = now()->addDays(2)->startOfDay();
        $endDate = now()->addDays(4)->startOfDay();

        $timestamps = [];

        foreach (['created_at', 'updated_at'] as $column) {
            if (array_key_exists($column, $overrides)) {
                $timestamps[$column] = $overrides[$column];
                unset($overrides[$column]);
            }
        }

        $booking = Booking::create(array_merge([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => $startDate,
            'pickup_time' => '09:00:00',
            'end_date' => $endDate,
            'return_time' => '17:00:00',
            'duration_days' => 3,
            'daily_price' => 250000,
            'total_price' => 750000,
            'status' => 'pending',
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
        ], $overrides));

        if ($timestamps !== []) {
            $booking->forceFill($timestamps)->saveQuietly();
        }

        return $booking;
    }

    private function createVehicle(array $overrides = []): Vehicle
    {
        static $sequence = 1;

        $vehicle = Vehicle::create(array_merge([
            'name' => 'Timeline Vehicle ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'BTL' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'transmission' => 'Manual',
            'year' => 2024,
            'daily_price' => 250000,
            'status' => 'available',
            'description' => 'Vehicle for weekly timeline tests.',
        ], $overrides));

        $sequence++;

        return $vehicle;
    }
}