<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_view_another_users_booking(): void
    {
        $owner = $this->createVerifiedCustomer();
        $intruder = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($owner);

        $this->actingAs($intruder)
            ->get(route('bookings.show', $booking))
            ->assertForbidden();
    }

    public function test_customer_cannot_access_another_users_payment_page(): void
    {
        $owner = $this->createVerifiedCustomer();
        $intruder = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($owner);

        $this->actingAs($intruder)
            ->get(route('bookings.payment', $booking))
            ->assertForbidden();
    }

    public function test_customer_cannot_process_another_users_payment(): void
    {
        $owner = $this->createVerifiedCustomer();
        $intruder = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($owner);

        $this->actingAs($intruder)
            ->post(route('bookings.process-payment', $booking), [
                'payment_method' => 'whatsapp',
            ])
            ->assertForbidden();

        $this->assertSame('transfer_proof', $booking->fresh()->payment_method);
    }

    public function test_customer_cannot_open_another_users_whatsapp_confirmation_page(): void
    {
        $owner = $this->createVerifiedCustomer();
        $intruder = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($owner, [
            'payment_method' => 'whatsapp',
        ]);

        $this->actingAs($intruder)
            ->get(route('payments.whatsapp-confirmation', $booking))
            ->assertForbidden();
    }

    public function test_owner_can_access_their_own_payment_page_when_ktp_is_verified(): void
    {
        $owner = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($owner);

        $this->actingAs($owner)
            ->get(route('bookings.payment', $booking))
            ->assertOk();
    }

    public function test_customer_cannot_cancel_a_confirmed_booking(): void
    {
        $owner = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($owner, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($owner)
            ->delete(route('bookings.cancel', $booking))
            ->assertForbidden();
    }

    public function test_customer_cannot_reenter_payment_flow_while_payment_is_waiting_verification(): void
    {
        $owner = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($owner, [
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
            'payment_proof' => 'proofs/waiting-proof.jpg',
        ]);

        $this->actingAs($owner)
            ->get(route('bookings.payment', $booking))
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('bookings.process-payment', $booking), [
                'payment_method' => 'whatsapp',
            ])
            ->assertForbidden();
    }

    public function test_customer_cannot_retry_or_cancel_booking_after_admin_rejects_payment(): void
    {
        $owner = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($owner, [
            'payment_method' => 'whatsapp',
            'payment_status' => 'failed',
            'notes' => 'Pembayaran ditolak karena melewati batas waktu 1 jam.',
        ]);

        $this->actingAs($owner)
            ->get(route('bookings.payment', $booking))
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('bookings.process-payment', $booking), [
                'payment_method' => 'transfer_proof',
            ])
            ->assertForbidden();

        $this->actingAs($owner)
            ->delete(route('bookings.cancel', $booking))
            ->assertForbidden();
    }

    public function test_customer_cannot_access_payment_flow_after_payment_deadline_has_passed(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');

        $owner = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($owner, [
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'duration_days' => 1,
            'total_price' => 250000,
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2),
        ]);

        $this->actingAs($owner)
            ->get(route('bookings.payment', $booking))
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('bookings.process-payment', $booking), [
                'payment_method' => 'whatsapp',
            ])
            ->assertForbidden();

        Carbon::setTestNow();
    }

    public function test_admin_can_view_any_booking(): void
    {
        $owner = $this->createVerifiedCustomer();
        $admin = $this->createAdmin();
        $booking = $this->createBookingFor($owner);

        $this->actingAs($admin)
            ->get(route('bookings.show', $booking))
            ->assertOk();
    }

    public function test_admin_customer_booking_index_route_redirects_to_admin_booking_index(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('bookings.index'))
            ->assertRedirect(route('admin.bookings.index'));
    }

    public function test_admin_customer_booking_index_route_preserves_active_filter_on_redirect(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('bookings.index', ['status' => 'awaiting_proof']))
            ->assertRedirect(route('admin.bookings.index', ['status' => 'awaiting_proof']));
    }

    public function test_customer_cannot_resend_admin_booking_notifications(): void
    {
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($customer, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($customer)
            ->post(route('admin.bookings.resend-notification', $booking))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    public function test_customer_cannot_access_admin_reschedule_form(): void
    {
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($customer, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'maintenance_hold_at' => now(),
            'maintenance_hold_reason' => 'Unit masuk maintenance setelah inspeksi pengembalian.',
        ]);

        $this->actingAs($customer)
            ->get(route('admin.bookings.reschedule-form', $booking))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    public function test_customer_cannot_process_admin_reschedule(): void
    {
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($customer, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'maintenance_hold_at' => now(),
            'maintenance_hold_reason' => 'Unit masuk maintenance setelah inspeksi pengembalian.',
        ]);

        $originalStartDate = $booking->start_date->toDateString();

        $this->actingAs($customer)
            ->post(route('admin.bookings.reschedule', $booking), [
                'start_date' => now()->addDays(7)->toDateString(),
                'end_date' => now()->addDays(9)->toDateString(),
                'pickup_time' => '10:00',
                'return_time' => '18:00',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');

        $booking->refresh();

        $this->assertSame($originalStartDate, $booking->start_date->toDateString());
        $this->assertNotNull($booking->maintenance_hold_at);
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
            'end_date' => $endDate,
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
            'name' => 'Test Vehicle ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'BTEST' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'transmission' => 'Manual',
            'year' => 2024,
            'daily_price' => 250000,
            'status' => 'available',
            'description' => 'Test vehicle for authorization scenarios.',
        ], $overrides));

        $sequence++;

        return $vehicle;
    }
}