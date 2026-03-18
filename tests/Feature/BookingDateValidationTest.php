<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BookingDateValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_creation_rejects_end_date_before_start_date(): void
    {
        $user = $this->createCustomer();
        $vehicle = $this->createVehicle();

        $response = $this->actingAs($user)
            ->from(route('bookings.create', $vehicle))
            ->post(route('bookings.store'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => now()->addDays(10)->toDateString(),
                'end_date' => now()->addDays(8)->toDateString(),
            ]);

        $response
            ->assertRedirect(route('bookings.create', $vehicle))
            ->assertSessionHasErrors('end_date');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_booking_creation_allows_same_day_rental_as_one_day_booking(): void
    {
        $user = $this->createCustomer();
        $vehicle = $this->createVehicle();
        $sameDate = now()->addDays(10)->toDateString();

        $response = $this->actingAs($user)
            ->post(route('bookings.store'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => $sameDate,
                'end_date' => $sameDate,
            ]);

        $booking = Booking::first();

        $response->assertRedirect(route('bookings.show', $booking));
        $this->assertNotNull($booking);
        $this->assertSame(1, $booking->duration_days);
        $this->assertSame($sameDate, $booking->start_date->toDateString());
        $this->assertSame($sameDate, $booking->end_date->toDateString());
        $this->assertSame('09:00:00', $booking->pickup_time);
        $this->assertSame('17:00:00', $booking->return_time);
    }

    public function test_booking_creation_stores_selected_pickup_and_return_time(): void
    {
        $user = $this->createCustomer();
        $vehicle = $this->createVehicle();

        $response = $this->actingAs($user)
            ->post(route('bookings.store'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => now()->addDays(10)->toDateString(),
                'end_date' => now()->addDays(12)->toDateString(),
                'pickup_time' => '08:30',
                'return_time' => '18:15',
            ]);

        $booking = Booking::first();

        $response->assertRedirect(route('bookings.show', $booking));
        $this->assertNotNull($booking);
        $this->assertSame('08:30:00', $booking->pickup_time);
        $this->assertSame('18:15:00', $booking->return_time);
    }

    public function test_same_day_booking_rejects_return_time_before_pickup_time(): void
    {
        $user = $this->createCustomer();
        $vehicle = $this->createVehicle();
        $sameDate = now()->addDays(10)->toDateString();

        $response = $this->actingAs($user)
            ->from(route('bookings.create', $vehicle))
            ->post(route('bookings.store'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => $sameDate,
                'end_date' => $sameDate,
                'pickup_time' => '15:00',
                'return_time' => '10:00',
            ]);

        $response
            ->assertRedirect(route('bookings.create', $vehicle))
            ->assertSessionHasErrors('return_time');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_availability_api_rejects_invalid_date_range(): void
    {
        $user = $this->createCustomer();
        $vehicle = $this->createVehicle();

        $response = $this->actingAs($user)
            ->getJson(route('api.vehicle.availability', $vehicle, false) . '?start_date=' . now()->addDays(10)->toDateString() . '&end_date=' . now()->addDays(8)->toDateString());

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('end_date');
    }

    public function test_valid_booking_dates_can_still_be_created(): void
    {
        $user = $this->createCustomer();
        $vehicle = $this->createVehicle();

        $response = $this->actingAs($user)
            ->post(route('bookings.store'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => now()->addDays(10)->toDateString(),
                'end_date' => now()->addDays(12)->toDateString(),
            ]);

        $booking = Booking::first();

        $response->assertRedirect(route('bookings.show', $booking));
        $this->assertNotNull($booking);
        $this->assertSame(3, $booking->duration_days);
    }

    public function test_booking_creation_rejects_dates_that_become_unavailable_before_submit(): void
    {
        $user = $this->createCustomer();
        $vehicle = $this->createVehicle();
        $startDate = now()->addDays(10)->toDateString();
        $endDate = now()->addDays(12)->toDateString();

        $this->createBookingForVehicle($vehicle, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->from(route('bookings.create', $vehicle))
            ->post(route('bookings.store'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

        $response
            ->assertRedirect(route('bookings.create', $vehicle))
            ->assertSessionHasErrors('end_date');

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_booking_creation_rejects_when_same_vehicle_request_is_already_in_progress(): void
    {
        $user = $this->createCustomer();
        $vehicle = $this->createVehicle();
        $lock = Cache::lock('vehicle-booking:' . $vehicle->id, 10);

        $this->assertTrue($lock->get());

        try {
            $response = $this->actingAs($user)
                ->from(route('bookings.create', $vehicle))
                ->post(route('bookings.store'), [
                    'vehicle_id' => $vehicle->id,
                    'start_date' => now()->addDays(10)->toDateString(),
                    'end_date' => now()->addDays(12)->toDateString(),
                ]);

            $response
                ->assertRedirect(route('bookings.create', $vehicle))
                ->assertSessionHasErrors('end_date');

            $this->assertDatabaseCount('bookings', 0);
        } finally {
            $lock->release();
        }
    }

    public function test_booking_creation_allows_overlap_with_paid_booking_as_waiting_list_candidate(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');

        $user = $this->createCustomer();
        $vehicle = $this->createVehicle(['status' => 'rented']);
        $startDate = now()->addDay()->toDateString();
        $endDate = now()->addDays(2)->toDateString();

        $this->createBookingForVehicle($vehicle, [
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($user)
            ->post(route('bookings.store'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

        $this->assertDatabaseCount('bookings', 2);

        $newBooking = Booking::query()->latest('id')->first();

        $response->assertRedirect(route('bookings.show', $newBooking));
        $response->assertSessionHas('success');
        $this->assertSame('pending', $newBooking?->status);

        Carbon::setTestNow();
    }

    public function test_booking_creation_rejects_overlap_with_future_confirmed_paid_booking(): void
    {
        $user = $this->createCustomer();
        $vehicle = $this->createVehicle(['status' => 'available']);
        $startDate = now()->addDays(10)->toDateString();
        $endDate = now()->addDays(12)->toDateString();

        $this->createBookingForVehicle($vehicle, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($user)
            ->from(route('bookings.create', $vehicle))
            ->post(route('bookings.store'), [
                'vehicle_id' => $vehicle->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

        $response
            ->assertRedirect(route('bookings.create', $vehicle))
            ->assertSessionHasErrors('end_date');

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_availability_api_allows_same_day_range(): void
    {
        $user = $this->createCustomer();
        $vehicle = $this->createVehicle();
        $sameDate = now()->addDays(10)->toDateString();

        $response = $this->actingAs($user)
            ->getJson(route('api.vehicle.availability', $vehicle, false) . '?start_date=' . $sameDate . '&end_date=' . $sameDate);

        $response
            ->assertOk()
            ->assertJsonPath('available', true);
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
            'name' => 'Date Validation Vehicle ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'BDATE' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'transmission' => 'Manual',
            'year' => 2024,
            'daily_price' => 250000,
            'status' => 'available',
            'description' => 'Vehicle for booking date validation tests.',
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