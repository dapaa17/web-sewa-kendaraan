<?php

namespace Tests\Feature;

use App\Mail\BookingReadyForPickupMail;
use App\Mail\BookingRescheduledMail;
use App\Mail\PaymentFailedMail;
use App\Mail\PaymentVerifiedMail;
use App\Mail\WaitingListActivatedMail;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_proof_method_is_rejected_in_payment_flow(): void
    {
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($customer);

        $response = $this->actingAs($customer)
            ->post(route('bookings.process-payment', $booking), [
                'payment_method' => 'transfer_proof',
            ]);

        $response->assertSessionHasErrors('payment_method');
        $this->assertSame('transfer_proof', $booking->fresh()->payment_method);
    }

    public function test_verified_customer_can_choose_whatsapp_confirmation_and_open_whatsapp_page(): void
    {
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($customer);

        $response = $this->actingAs($customer)
            ->post(route('bookings.process-payment', $booking), [
                'payment_method' => 'whatsapp',
            ]);

        $response->assertRedirect(route('bookings.whatsapp-payment', $booking));
        $this->assertSame('whatsapp', $booking->fresh()->payment_method);

        $this->actingAs($customer)
            ->get(route('payments.whatsapp-confirmation', $booking))
            ->assertOk()
            ->assertSee($booking->vehicle->name)
            ->assertSee('Hubungi Admin via WhatsApp');
    }

    public function test_unverified_customer_is_redirected_from_payment_flow(): void
    {
        $customer = $this->createCustomer([
            'ktp_status' => 'pending',
            'ktp_verified_at' => null,
        ]);
        $booking = $this->createBookingFor($customer);

        $this->actingAs($customer)
            ->get(route('bookings.payment', $booking))
            ->assertRedirect(route('profile.ktp'))
            ->assertSessionHas('warning', 'Silakan verifikasi KTP terlebih dahulu sebelum melakukan pembayaran.');

        $this->actingAs($customer)
            ->get(route('payments.whatsapp-confirmation', $booking))
            ->assertRedirect(route('profile.ktp'))
            ->assertSessionHas('warning', 'Silakan verifikasi KTP terlebih dahulu sebelum melakukan pembayaran.');
    }

    public function test_transfer_proof_page_route_is_not_available(): void
    {
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($customer);

        $this->actingAs($customer)
            ->get('/bookings/' . $booking->id . '/transfer-proof')
            ->assertNotFound();
    }

    public function test_upload_proof_route_is_not_available(): void
    {
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($customer);

        $this->actingAs($customer)
            ->post('/bookings/' . $booking->id . '/upload-proof', [
                'payment_proof' => UploadedFile::fake()->create('proof.jpg', 120, 'image/jpeg'),
            ])
            ->assertNotFound();
    }

    public function test_admin_can_verify_payment_and_mark_vehicle_rented_when_rental_has_started(): void
    {
        Carbon::setTestNow('2026-03-11 10:00:00');
        Mail::fake();

        $customer = $this->createVerifiedCustomer();
        $admin = $this->createAdmin();
        $booking = $this->createBookingFor($customer, [
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'payment_method' => 'transfer_proof',
            'payment_proof' => 'proofs/existing-proof.jpg',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.verify-payment', $booking), [
                'verified' => '1',
                'notes' => 'Pembayaran valid',
            ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $response->assertSessionHas('success', 'Pembayaran berhasil diverifikasi');

        $booking->refresh();
        $booking->vehicle->refresh();

        $this->assertSame('confirmed', $booking->status);
        $this->assertSame('paid', $booking->payment_status);
        $this->assertSame('Pembayaran valid', $booking->notes);
        $this->assertSame('rented', $booking->vehicle->status);

        Mail::assertSent(PaymentVerifiedMail::class, function (PaymentVerifiedMail $mail) use ($customer, $booking) {
            return $mail->hasTo($customer->email)
                && $mail->booking->id === $booking->id
                && $mail->booking->status === 'confirmed';
        });

        Carbon::setTestNow();
    }

    public function test_admin_keeps_future_confirmed_booking_vehicle_available_until_start_date(): void
    {
        Mail::fake();

        $customer = $this->createVerifiedCustomer();
        $admin = $this->createAdmin();
        $booking = $this->createBookingFor($customer, [
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'payment_method' => 'transfer_proof',
            'payment_proof' => 'proofs/future-proof.jpg',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.verify-payment', $booking), [
                'verified' => '1',
                'notes' => 'Siap untuk tanggal sewa nanti',
            ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $response->assertSessionHas('success', 'Pembayaran berhasil diverifikasi');

        $booking->refresh();
        $booking->vehicle->refresh();

        $this->assertSame('confirmed', $booking->status);
        $this->assertSame('paid', $booking->payment_status);
        $this->assertSame('available', $booking->vehicle->status);
    }

    public function test_customer_sees_future_confirmed_booking_as_terjadwal(): void
    {
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($customer, [
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($customer)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSee('Terjadwal')
            ->assertSee('Arti status: Terjadwal')
            ->assertSee('booking sudah dikonfirmasi dan aman')
            ->assertSee('akan mulai pada');

        $this->actingAs($customer)
            ->get(route('bookings.index'))
            ->assertOk()
            ->assertSee('📅 Terjadwal');
    }

    public function test_customer_sees_same_day_future_booking_as_terjadwal_until_pickup_time(): void
    {
        Carbon::setTestNow('2026-03-11 08:00:00');

        $customer = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($customer, [
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'pickup_time' => '09:00:00',
            'return_time' => '17:00:00',
            'duration_days' => 1,
            'total_price' => 250000,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->assertTrue($booking->fresh()->hasNotStartedYet());
        $this->assertFalse($booking->fresh()->isActive());

        $this->actingAs($customer)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSee('Terjadwal')
            ->assertSee('akan mulai pada 11 Mar 2026 pukul 09:00')
            ->assertDontSee('Sedang Disewa');

        Carbon::setTestNow();
    }

    public function test_booking_list_can_filter_scheduled_bookings(): void
    {
        Carbon::setTestNow('2026-03-11 10:00:00');

        $customer = $this->createVerifiedCustomer();
        $scheduledVehicle = $this->createVehicle(['name' => 'Toyota Terjadwal Aman', 'status' => 'available']);
        $delayedVehicle = $this->createVehicle(['name' => 'Toyota Menunggu Unit', 'status' => 'rented']);

        $scheduledBooking = $this->createBookingFor($customer, [
            'vehicle_id' => $scheduledVehicle->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->createBookingFor($customer, [
            'vehicle_id' => $delayedVehicle->id,
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->createBookingFor($customer, [
            'vehicle_id' => $delayedVehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($customer)
            ->get(route('bookings.index', ['status' => 'scheduled']))
            ->assertOk()
            ->assertSee('Terjadwal')
            ->assertSee('Toyota Terjadwal Aman')
            ->assertSee('Booking #' . $scheduledBooking->id)
            ->assertDontSee('Toyota Menunggu Unit');

        Carbon::setTestNow();
    }

    public function test_customer_sees_booking_as_waiting_for_vehicle_return_when_previous_booking_is_not_completed(): void
    {
        Carbon::setTestNow('2026-03-11 10:00:00');

        $admin = $this->createAdmin();
        $currentCustomer = $this->createVerifiedCustomer(['email' => 'current-return@example.com']);
        $nextCustomer = $this->createVerifiedCustomer(['email' => 'next-return@example.com']);
        $vehicle = $this->createVehicle(['status' => 'rented']);

        $this->createBookingFor($currentCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $delayedBooking = $this->createBookingFor($nextCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->assertTrue($delayedBooking->fresh()->isAwaitingVehicleReturn());
        $this->assertFalse($delayedBooking->fresh()->isActive());

        $this->actingAs($nextCustomer)
            ->get(route('bookings.show', $delayedBooking))
            ->assertOk()
            ->assertSee('Arti status: Menunggu Pengembalian Unit')
            ->assertSee('Menunggu Pengembalian Unit')
            ->assertSee('unit masih menunggu pengembalian dari customer sebelumnya');

        $this->actingAs($nextCustomer)
            ->get(route('bookings.index'))
            ->assertOk()
            ->assertSee('Menunggu Unit Kembali');

        $this->actingAs($nextCustomer)
            ->get(route('bookings.index', ['status' => 'active']))
            ->assertOk()
            ->assertSee('Tidak Ada Booking Aktif');

        $this->actingAs($admin)
            ->get(route('admin.bookings.complete-form', $delayedBooking))
            ->assertForbidden();

        Carbon::setTestNow();
    }

    public function test_booking_list_can_filter_bookings_waiting_for_vehicle_return(): void
    {
        Carbon::setTestNow('2026-03-11 10:00:00');

        $customer = $this->createVerifiedCustomer();
        $delayedVehicle = $this->createVehicle(['name' => 'Toyota Menunggu Unit', 'status' => 'rented']);
        $otherVehicle = $this->createVehicle(['name' => 'Honda Booking Normal', 'status' => 'available']);

        $this->createBookingFor($customer, [
            'vehicle_id' => $delayedVehicle->id,
            'start_date' => now()->subDays(3)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $delayedBooking = $this->createBookingFor($customer, [
            'vehicle_id' => $delayedVehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->createBookingFor($customer, [
            'vehicle_id' => $otherVehicle->id,
            'start_date' => now()->addDays(4)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($customer)
            ->get(route('bookings.index', ['status' => 'awaiting_return']))
            ->assertOk()
            ->assertSee('Menunggu Unit')
            ->assertSee('jadwal booking bentrok dengan kendaraan yang masih dipakai')
            ->assertSee('kendaraan sebelumnya belum dikembalikan.')
            ->assertSee('Toyota Menunggu Unit')
            ->assertSee('Booking #' . $delayedBooking->id)
            ->assertDontSee('Honda Booking Normal');

        Carbon::setTestNow();
    }

    public function test_admin_can_verify_paid_overlap_into_waiting_list(): void
    {
        Mail::fake();

        $admin = $this->createAdmin();
        $currentCustomer = $this->createVerifiedCustomer();
        $waitingCustomer = $this->createVerifiedCustomer();
        $vehicle = $this->createVehicle(['status' => 'rented']);

        $this->createBookingFor($currentCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $waitingBooking = $this->createBookingFor($waitingCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'payment_method' => 'transfer_proof',
            'payment_proof' => 'proofs/waiting-proof.jpg',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.verify-payment', $waitingBooking), [
                'verified' => '1',
                'notes' => 'Masuk antrean',
            ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $response->assertSessionHas('success', 'Pembayaran berhasil diverifikasi dan booking masuk antrean');

        $waitingBooking->refresh();
        $vehicle->refresh();

        $this->assertSame('waiting_list', $waitingBooking->status);
        $this->assertSame('paid', $waitingBooking->payment_status);
        $this->assertSame('Masuk antrean', $waitingBooking->notes);
        $this->assertSame('rented', $vehicle->status);

        Mail::assertSent(PaymentVerifiedMail::class, function (PaymentVerifiedMail $mail) use ($waitingCustomer, $waitingBooking) {
            return $mail->hasTo($waitingCustomer->email)
                && $mail->booking->id === $waitingBooking->id
                && $mail->booking->status === 'waiting_list';
        });
    }

    public function test_admin_can_reject_payment_and_keep_vehicle_unrented(): void
    {
        Mail::fake();

        $customer = $this->createVerifiedCustomer();
        $admin = $this->createAdmin();
        $booking = $this->createBookingFor($customer, [
            'payment_method' => 'transfer_proof',
            'payment_proof' => 'proofs/existing-proof.jpg',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.verify-payment', $booking), [
                'verified' => '0',
                'notes' => 'Bukti transfer tidak valid',
            ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $response->assertSessionHas('success', 'Pembayaran ditolak');

        $booking->refresh();
        $booking->vehicle->refresh();

        $this->assertSame('cancelled', $booking->status);
        $this->assertSame('failed', $booking->payment_status);
        $this->assertNull($booking->payment_proof);
        $this->assertSame('Bukti transfer tidak valid', $booking->notes);
        $this->assertSame('available', $booking->vehicle->status);

        $this->actingAs($customer)
            ->get(route('bookings.payment', $booking))
            ->assertForbidden();

        $this->actingAs($customer)
            ->post(route('bookings.process-payment', $booking), [
                'payment_method' => 'whatsapp',
            ])
            ->assertForbidden();

        $this->actingAs($customer)
            ->delete(route('bookings.cancel', $booking))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSee('Pembayaran ditolak admin')
            ->assertDontSee('Pilih Metode')
            ->assertDontSee('Lanjutkan Pembayaran')
            ->assertDontSee('Batalkan Booking');

        Mail::assertSent(PaymentFailedMail::class, function (PaymentFailedMail $mail) use ($customer, $booking) {
            return $mail->hasTo($customer->email)
                && $mail->booking->id === $booking->id
                && $mail->errorMessage === 'Bukti transfer tidak valid';
        });
    }

    public function test_failed_payment_booking_moves_from_pending_filter_to_cancelled_filter(): void
    {
        $customer = $this->createVerifiedCustomer();

        $pendingBooking = $this->createBookingFor($customer, [
            'vehicle_id' => $this->createVehicle(['name' => 'Toyota Pending Murni'])->id,
            'payment_status' => 'pending',
        ]);

        $failedBooking = $this->createBookingFor($customer, [
            'vehicle_id' => $this->createVehicle(['name' => 'Toyota Pembayaran Ditolak'])->id,
            'status' => 'pending',
            'payment_status' => 'failed',
        ]);

        $this->actingAs($customer)
            ->get(route('bookings.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee('Toyota Pending Murni')
            ->assertDontSee('Toyota Pembayaran Ditolak');

        $this->actingAs($customer)
            ->get(route('bookings.index', ['status' => 'cancelled']))
            ->assertOk()
            ->assertSee('Toyota Pembayaran Ditolak')
            ->assertSee('Pembayaran Ditolak')
            ->assertDontSee('Toyota Pending Murni');

        $this->assertSame('pending', $pendingBooking->fresh()->status);
        $this->assertSame('failed', $failedBooking->fresh()->payment_status);
    }

    public function test_admin_cannot_complete_booking_before_rental_has_started(): void
    {
        Carbon::setTestNow('2026-03-09 10:00:00');

        $customer = $this->createVerifiedCustomer();
        $admin = $this->createAdmin();
        $booking = $this->createBookingFor($customer, [
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.bookings.complete-form', $booking))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('admin.bookings.complete', $booking), [
                'return_date' => now()->toDateString(),
                'return_condition_status' => 'excellent',
            ])
            ->assertForbidden();

        Carbon::setTestNow();
    }

    public function test_completing_active_booking_promotes_next_waiting_list_booking(): void
    {
        Carbon::setTestNow('2026-03-09 10:00:00');
        Mail::fake();

        $admin = $this->createAdmin();
        $currentCustomer = $this->createVerifiedCustomer();
        $waitingCustomer = $this->createVerifiedCustomer();
        $vehicle = $this->createVehicle(['status' => 'rented']);

        $currentBooking = $this->createBookingFor($currentCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $waitingBooking = $this->createBookingFor($waitingCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'duration_days' => 3,
            'status' => 'waiting_list',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.complete', $currentBooking), [
                'return_date' => now()->toDateString(),
                'return_condition_status' => 'excellent',
            ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $response->assertSessionHas('success', 'Booking selesai, 1 booking antrean diaktifkan.');

        $currentBooking->refresh();
        $waitingBooking->refresh();
        $vehicle->refresh();

        $this->assertSame('completed', $currentBooking->status);
        $this->assertSame('confirmed', $waitingBooking->status);
        $this->assertSame(now()->toDateString(), $waitingBooking->start_date->toDateString());
        $this->assertSame(now()->addDays(2)->toDateString(), $waitingBooking->end_date->toDateString());
        $this->assertStringContainsString('antrean', strtolower((string) $waitingBooking->notes));
        $this->assertSame('rented', $vehicle->status);

        Mail::assertSent(WaitingListActivatedMail::class, function (WaitingListActivatedMail $mail) use ($waitingBooking) {
            return $mail->hasTo($waitingBooking->user->email)
                && $mail->booking->is($waitingBooking);
        });

        Carbon::setTestNow();
    }

    public function test_completing_active_booking_promotes_future_waiting_list_without_marking_vehicle_rented_yet(): void
    {
        Carbon::setTestNow('2026-03-09 10:00:00');
        Mail::fake();

        $admin = $this->createAdmin();
        $currentCustomer = $this->createVerifiedCustomer();
        $waitingCustomer = $this->createVerifiedCustomer(['email' => 'future-waiting@example.com']);
        $vehicle = $this->createVehicle(['status' => 'rented']);

        $currentBooking = $this->createBookingFor($currentCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $waitingBooking = $this->createBookingFor($waitingCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'duration_days' => 3,
            'status' => 'waiting_list',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.bookings.complete', $currentBooking), [
                'return_date' => now()->toDateString(),
                'return_condition_status' => 'excellent',
            ])
            ->assertRedirect(route('admin.bookings.index'));

        $waitingBooking->refresh();
        $vehicle->refresh();

        $this->assertSame('confirmed', $waitingBooking->status);
        $this->assertSame(now()->addDays(3)->toDateString(), $waitingBooking->start_date->toDateString());
        $this->assertSame('available', $vehicle->status);

        Carbon::setTestNow();
    }

    public function test_completing_overdue_booking_sends_ready_notification_for_delayed_confirmed_booking(): void
    {
        Carbon::setTestNow('2026-03-11 10:00:00');
        Mail::fake();

        $admin = $this->createAdmin();
        $currentCustomer = $this->createVerifiedCustomer(['email' => 'delayed-current@example.com']);
        $nextCustomer = $this->createVerifiedCustomer(['email' => 'delayed-next@example.com']);
        $vehicle = $this->createVehicle(['status' => 'rented']);

        $currentBooking = $this->createBookingFor($currentCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->subDays(3)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $delayedBooking = $this->createBookingFor($nextCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.complete', $currentBooking), [
                'return_date' => now()->toDateString(),
                'return_condition_status' => 'excellent',
            ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $response->assertSessionHas('success', 'Booking selesai, booking berikutnya sekarang sudah aktif.');

        $delayedBooking->refresh();
        $vehicle->refresh();

        $this->assertTrue($delayedBooking->isActive());
        $this->assertSame('rented', $vehicle->status);

        Mail::assertSent(BookingReadyForPickupMail::class, function (BookingReadyForPickupMail $mail) use ($nextCustomer, $delayedBooking) {
            return $mail->hasTo($nextCustomer->email)
                && $mail->booking->is($delayedBooking);
        });

        Carbon::setTestNow();
    }

    public function test_completing_overdue_booking_prioritizes_delayed_confirmed_booking_over_waiting_list(): void
    {
        Carbon::setTestNow('2026-03-11 10:00:00');
        Mail::fake();

        $admin = $this->createAdmin();
        $currentCustomer = $this->createVerifiedCustomer(['email' => 'priority-current@example.com']);
        $delayedCustomer = $this->createVerifiedCustomer(['email' => 'priority-delayed@example.com']);
        $queueCustomer = $this->createVerifiedCustomer(['email' => 'priority-queue@example.com']);
        $vehicle = $this->createVehicle(['status' => 'rented']);

        $currentBooking = $this->createBookingFor($currentCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->subDays(3)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $delayedBooking = $this->createBookingFor($delayedCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $waitingBooking = $this->createBookingFor($queueCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'waiting_list',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.bookings.complete', $currentBooking), [
                'return_date' => now()->toDateString(),
                'return_condition_status' => 'excellent',
            ])
            ->assertRedirect(route('admin.bookings.index'));

        $delayedBooking->refresh();
        $waitingBooking->refresh();
        $vehicle->refresh();

        $this->assertTrue($delayedBooking->isActive());
        $this->assertSame('waiting_list', $waitingBooking->status);
        $this->assertSame('rented', $vehicle->status);

        Mail::assertSent(BookingReadyForPickupMail::class, function (BookingReadyForPickupMail $mail) use ($delayedBooking) {
            return $mail->booking->is($delayedBooking);
        });

        Mail::assertNotSent(WaitingListActivatedMail::class, function (WaitingListActivatedMail $mail) use ($waitingBooking) {
            return $mail->booking->is($waitingBooking);
        });

        Carbon::setTestNow();
    }

    public function test_admin_can_complete_booking_with_return_inspection_details_and_photo(): void
    {
        Storage::fake('public');
        Carbon::setTestNow('2026-03-10 10:00:00');

        $admin = $this->createAdmin();
        $customer = $this->createVerifiedCustomer(['email' => 'inspection@example.com']);
        $booking = $this->createBookingFor($customer, [
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.complete', $booking), [
                'return_date' => now()->toDateString(),
                'return_condition_status' => 'minor_issue',
                'return_fuel_level' => 'half',
                'return_odometer' => 15432,
                'return_checklist' => ['documents_received', 'main_key_received'],
                'return_damage_fee' => 75000,
                'return_notes' => 'Ada lecet baru di bumper depan.',
                'return_photo' => UploadedFile::fake()->create('return-photo.jpg', 120, 'image/jpeg'),
            ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $response->assertSessionHas('success', 'Booking selesai, kendaraan tersedia kembali. Biaya tambahan inspeksi: Rp 75.000');

        $booking->refresh();

        $this->assertSame('completed', $booking->status);
    $this->assertSame('minor_issue', $booking->return_condition_status);
        $this->assertSame('half', $booking->return_fuel_level);
        $this->assertSame(15432, $booking->return_odometer);
        $this->assertSame(['documents_received', 'main_key_received'], $booking->return_checklist);
        $this->assertSame(75000.0, (float) $booking->return_damage_fee);
        $this->assertSame('Ada lecet baru di bumper depan.', $booking->return_notes);
        $this->assertNotNull($booking->return_photo);

        $this->assertTrue(Storage::disk('public')->exists($booking->return_photo));

        $this->actingAs($admin)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSee('Checklist Pengembalian')
            ->assertSee('Ada catatan kecil')
            ->assertSee('Dokumen kendaraan diterima')
            ->assertSee('Ada lecet baru di bumper depan.')
            ->assertSee('Rp75.000');

        Carbon::setTestNow();
    }

    public function test_completing_booking_with_attention_needed_moves_vehicle_to_maintenance_and_pauses_following_bookings(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');
        Mail::fake();

        $admin = $this->createAdmin();
        $currentCustomer = $this->createVerifiedCustomer(['email' => 'maintenance-current@example.com']);
        $nextCustomer = $this->createVerifiedCustomer(['email' => 'maintenance-next@example.com']);
        $queueCustomer = $this->createVerifiedCustomer(['email' => 'maintenance-queue@example.com']);
        $vehicle = $this->createVehicle(['status' => 'rented']);

        $currentBooking = $this->createBookingFor($currentCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $delayedBooking = $this->createBookingFor($nextCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $waitingBooking = $this->createBookingFor($queueCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'status' => 'waiting_list',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.complete', $currentBooking), [
                'return_date' => now()->toDateString(),
                'return_condition_status' => 'needs_attention',
                'return_notes' => 'Suspensi depan harus dicek ulang.',
            ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $response->assertSessionHas('success', 'Booking selesai, kendaraan dipindahkan ke maintenance. 2 booking lanjutan menunggu penyesuaian admin.');

        $currentBooking->refresh();
        $delayedBooking->refresh();
        $waitingBooking->refresh();
        $vehicle->refresh();

        $this->assertSame('completed', $currentBooking->status);
        $this->assertSame('maintenance', $vehicle->status);
        $this->assertSame('confirmed', $delayedBooking->status);
        $this->assertSame('waiting_list', $waitingBooking->status);
        $this->assertNotNull($delayedBooking->maintenance_hold_at);
        $this->assertNotNull($waitingBooking->maintenance_hold_at);
        $this->assertNotNull($delayedBooking->maintenance_hold_reason);
        $this->assertTrue($delayedBooking->isBlockedByMaintenance());
        $this->assertStringContainsString('maintenance', strtolower((string) $delayedBooking->notes));
        $this->assertStringContainsString('maintenance', strtolower((string) $waitingBooking->notes));

        Mail::assertNotSent(BookingReadyForPickupMail::class);
        Mail::assertNotSent(WaitingListActivatedMail::class);

        Carbon::setTestNow();
    }

    public function test_admin_can_filter_and_reschedule_maintenance_hold_bookings(): void
    {
        Carbon::setTestNow('2026-03-10 10:00:00');
        Mail::fake();

        $admin = $this->createAdmin();
        $customer = $this->createVerifiedCustomer(['email' => 'maintenance-held@example.com']);
        $otherCustomer = $this->createVerifiedCustomer(['email' => 'maintenance-normal@example.com']);
        $heldVehicle = $this->createVehicle([
            'name' => 'Toyota Hold Reschedule',
            'status' => 'maintenance',
        ]);
        $otherVehicle = $this->createVehicle([
            'name' => 'Honda Aman Normal',
            'status' => 'available',
        ]);

        $heldBooking = $this->createBookingFor($customer, [
            'vehicle_id' => $heldVehicle->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'maintenance_hold_at' => now()->subHour(),
            'maintenance_hold_reason' => 'Unit masuk maintenance setelah inspeksi pengembalian.',
            'notes' => 'Unit masuk maintenance setelah inspeksi pengembalian.',
        ]);

        $this->createBookingFor($otherCustomer, [
            'vehicle_id' => $otherVehicle->id,
            'start_date' => now()->addDays(4)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.bookings.index', ['status' => 'maintenance_hold']))
            ->assertOk()
            ->assertSee('Tertahan Maintenance')
            ->assertSee('Toyota Hold Reschedule')
            ->assertSee(route('admin.bookings.reschedule-form', $heldBooking))
            ->assertDontSee('Honda Aman Normal');

        $this->actingAs($admin)
            ->get(route('admin.bookings.reschedule-form', $heldBooking))
            ->assertOk()
            ->assertSee('Jadwalkan Ulang Booking')
            ->assertSee('Unit masuk maintenance setelah inspeksi pengembalian.');

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.reschedule', $heldBooking), [
                'start_date' => now()->addDays(6)->toDateString(),
                'end_date' => now()->addDays(8)->toDateString(),
                'pickup_time' => '10:00',
                'return_time' => '18:00',
                'admin_note' => 'Customer setuju pindah jadwal.',
            ]);

        $response->assertRedirect(route('bookings.show', $heldBooking));
        $response->assertSessionHas('success', 'Jadwal booking berhasil diperbarui dan tidak lagi tertahan oleh maintenance.');

        $heldBooking->refresh();

        $this->assertSame('confirmed', $heldBooking->status);
        $this->assertNull($heldBooking->maintenance_hold_at);
        $this->assertNull($heldBooking->maintenance_hold_reason);
        $this->assertSame(now()->addDays(6)->toDateString(), $heldBooking->start_date->toDateString());
        $this->assertSame(now()->addDays(8)->toDateString(), $heldBooking->end_date->toDateString());
        $this->assertSame('10:00', $heldBooking->pickup_time_label);
        $this->assertSame('18:00', $heldBooking->return_time_label);
        $this->assertFalse($heldBooking->canBeRescheduledByAdmin());
        $this->assertStringContainsString('Jadwal booking disesuaikan admin', (string) $heldBooking->notes);
        $this->assertStringContainsString('Customer setuju pindah jadwal.', (string) $heldBooking->notes);
        $this->assertSame('booking_rescheduled', $heldBooking->getResendableNotificationType());

        Mail::assertSent(BookingRescheduledMail::class, function (BookingRescheduledMail $mail) use ($customer, $heldBooking) {
            return $mail->hasTo($customer->email)
                && $mail->booking->id === $heldBooking->id
                && $mail->booking->start_date->isSameDay($heldBooking->start_date)
                && $mail->booking->getMaintenanceRescheduleAdminNote() === 'Customer setuju pindah jadwal.';
        });

        $this->actingAs($admin)
            ->get(route('admin.bookings.index', ['status' => 'maintenance_hold']))
            ->assertOk()
            ->assertDontSee('Toyota Hold Reschedule');

        Carbon::setTestNow();
    }

    public function test_booking_detail_shows_waiting_list_position_for_customer_and_admin_queue(): void
    {
        $admin = $this->createAdmin();
        $currentCustomer = $this->createVerifiedCustomer();
        $waitingCustomerOne = $this->createVerifiedCustomer(['email' => 'queue-one@example.com']);
        $waitingCustomerTwo = $this->createVerifiedCustomer(['email' => 'queue-two@example.com']);
        $vehicle = $this->createVehicle(['status' => 'rented']);

        $currentBooking = $this->createBookingFor($currentCustomer, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $firstWaitingBooking = $this->createBookingFor($waitingCustomerOne, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'status' => 'waiting_list',
            'payment_status' => 'paid',
        ]);

        $secondWaitingBooking = $this->createBookingFor($waitingCustomerTwo, [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDays(4)->toDateString(),
            'end_date' => now()->addDays(6)->toDateString(),
            'status' => 'waiting_list',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($waitingCustomerTwo)
            ->get(route('bookings.show', $secondWaitingBooking))
            ->assertOk()
            ->assertSee('Posisi antrean Anda saat ini: #2.');

        $this->actingAs($admin)
            ->get(route('bookings.show', $currentBooking))
            ->assertOk()
            ->assertSee('Antrean Booking Kendaraan Ini')
            ->assertSeeInOrder([
                'Antrean #1',
                $waitingCustomerOne->name,
                'Antrean #2',
                $waitingCustomerTwo->name,
            ]);

        $this->assertSame(1, $firstWaitingBooking->getWaitingListPosition());
        $this->assertSame(2, $secondWaitingBooking->getWaitingListPosition());
    }

    public function test_admin_can_resend_payment_notification_from_booking_detail(): void
    {
        Mail::fake();

        $admin = $this->createAdmin();
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($customer, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.resend-notification', $booking));

        $response->assertRedirect(route('bookings.show', $booking));
        $response->assertSessionHas('success', 'Email status pembayaran berhasil dikirim ulang.');

        Mail::assertSent(PaymentVerifiedMail::class, function (PaymentVerifiedMail $mail) use ($customer, $booking) {
            return $mail->hasTo($customer->email)
                && $mail->booking->id === $booking->id;
        });
    }

    public function test_admin_can_resend_waiting_list_activation_email(): void
    {
        Mail::fake();

        $admin = $this->createAdmin();
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createBookingFor($customer, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'notes' => 'Booking diaktifkan dari antrean pada 09 Mar 2026.',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.resend-notification', $booking));

        $response->assertRedirect(route('bookings.show', $booking));
        $response->assertSessionHas('success', 'Email aktivasi antrean berhasil dikirim ulang.');

        Mail::assertSent(WaitingListActivatedMail::class, function (WaitingListActivatedMail $mail) use ($customer, $booking) {
            return $mail->hasTo($customer->email)
                && $mail->booking->id === $booking->id;
        });
    }

    public function test_admin_can_resend_reschedule_notification_after_maintenance_adjustment(): void
    {
        Mail::fake();

        $admin = $this->createAdmin();
        $customer = $this->createVerifiedCustomer(['email' => 'reschedule-mail@example.com']);
        $booking = $this->createBookingFor($customer, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'notes' => 'Unit masuk maintenance setelah inspeksi pengembalian.' . PHP_EOL
                . 'Jadwal booking disesuaikan admin ke 16 Mar 2026 10:00 - 18 Mar 2026 18:00 setelah maintenance. Catatan admin: Customer setuju pindah jadwal.',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.bookings.resend-notification', $booking));

        $response->assertRedirect(route('bookings.show', $booking));
        $response->assertSessionHas('success', 'Email jadwal baru berhasil dikirim ulang.');

        Mail::assertSent(BookingRescheduledMail::class, function (BookingRescheduledMail $mail) use ($customer, $booking) {
            return $mail->hasTo($customer->email)
                && $mail->booking->id === $booking->id
                && $mail->booking->getMaintenanceRescheduleAdminNote() === 'Customer setuju pindah jadwal.';
        });
    }

    public function test_admin_booking_list_only_shows_payment_review_for_verifiable_booking(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createVerifiedCustomer();

        $reviewableBooking = $this->createBookingFor($customer, [
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
            'payment_proof' => 'proofs/reviewable-proof.jpg',
        ]);

        $waitingProofBooking = $this->createBookingFor($customer, [
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
            'payment_proof' => null,
        ]);

        $whatsAppBooking = $this->createBookingFor($customer, [
            'payment_method' => 'whatsapp',
            'payment_status' => 'pending',
            'payment_proof' => null,
        ]);

        $nonReviewableBooking = $this->createBookingFor($customer, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.bookings.index'));

        $response->assertOk()
            ->assertSee('Tinjau Pembayaran')
            ->assertSee('Bukti Transfer')
            ->assertSee('Belum Konfirmasi')
            ->assertSee('Belum ada konfirmasi pembayaran')
            ->assertSee('WhatsApp')
            ->assertSee(route('bookings.show', $reviewableBooking));

        $this->assertSame(2, substr_count($response->getContent(), 'Tinjau Pembayaran'));
        $this->assertStringContainsString((string) $waitingProofBooking->id, $response->getContent());
        $this->assertStringContainsString((string) $whatsAppBooking->id, $response->getContent());
        $this->assertStringNotContainsString((string) $nonReviewableBooking->id . '</strong></td>', $response->getContent());
    }

    public function test_admin_booking_list_prioritizes_waiting_proof_bookings_by_nearest_deadline(): void
    {
        Carbon::setTestNow('2026-03-10 12:00:00');

        $admin = $this->createAdmin();
        $customer = $this->createVerifiedCustomer();

        $urgentBooking = $this->createBookingFor($customer, [
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
            'payment_proof' => null,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'duration_days' => 1,
            'total_price' => 275000,
            'created_at' => now()->subMinutes(50),
            'updated_at' => now()->subMinutes(50),
            'vehicle_id' => $this->createVehicle(['name' => 'Toyota Deadline Mepet'])->id,
        ]);

        $normalBooking = $this->createBookingFor($customer, [
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
            'payment_proof' => null,
            'created_at' => now()->subMinutes(25),
            'updated_at' => now()->subMinutes(25),
            'vehicle_id' => $this->createVehicle(['name' => 'Honda Menunggu Bukti Biasa'])->id,
        ]);

        $otherBooking = $this->createBookingFor($customer, [
            'payment_method' => 'whatsapp',
            'payment_status' => 'pending',
            'payment_proof' => null,
            'created_at' => now()->subMinutes(10),
            'updated_at' => now()->subMinutes(10),
            'vehicle_id' => $this->createVehicle(['name' => 'Suzuki WhatsApp Cepat'])->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.bookings.index'));

        $response->assertOk()
            ->assertSee('Perlu Follow Up')
            ->assertSee('Toyota Deadline Mepet')
            ->assertSee('Honda Menunggu Bukti Biasa')
            ->assertSee('Suzuki WhatsApp Cepat');

        $content = $response->getContent();

        Assert::assertNotFalse(strpos($content, 'Toyota Deadline Mepet'));
        Assert::assertNotFalse(strpos($content, 'Honda Menunggu Bukti Biasa'));
        Assert::assertNotFalse(strpos($content, 'Suzuki WhatsApp Cepat'));

        $urgentPosition = strpos($content, 'Toyota Deadline Mepet');
        $normalPosition = strpos($content, 'Honda Menunggu Bukti Biasa');
        $otherPosition = strpos($content, 'Suzuki WhatsApp Cepat');

        Assert::assertLessThan($normalPosition, $urgentPosition);
        Assert::assertLessThan($otherPosition, $normalPosition);

        $this->assertSame($urgentBooking->id, $urgentBooking->fresh()->id);
        $this->assertSame($normalBooking->id, $normalBooking->fresh()->id);
        $this->assertSame($otherBooking->id, $otherBooking->fresh()->id);

        Carbon::setTestNow();
    }

    public function test_admin_can_filter_bookings_waiting_for_payment_confirmation(): void
    {
        Carbon::setTestNow('2026-03-10 12:00:00');

        $admin = $this->createAdmin();
        $customer = $this->createVerifiedCustomer();

        $urgentVehicle = $this->createVehicle(['name' => 'Toyota Menunggu Bukti Cepat']);
        $waitingProofVehicle = $this->createVehicle(['name' => 'Honda Bukti Reguler']);
        $reviewableVehicle = $this->createVehicle(['name' => 'Honda Sudah Upload']);
        $whatsAppVehicle = $this->createVehicle(['name' => 'Suzuki WhatsApp']);

        $urgentWaitingProofBooking = $this->createBookingFor($customer, [
            'vehicle_id' => $urgentVehicle->id,
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
            'payment_proof' => null,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'duration_days' => 1,
            'total_price' => (float) $urgentVehicle->daily_price,
            'created_at' => now()->subMinutes(50),
            'updated_at' => now()->subMinutes(50),
        ]);

        $waitingProofBooking = $this->createBookingFor($customer, [
            'vehicle_id' => $waitingProofVehicle->id,
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
            'payment_proof' => null,
            'created_at' => now()->subMinutes(25),
            'updated_at' => now()->subMinutes(25),
        ]);

        $this->createBookingFor($customer, [
            'vehicle_id' => $reviewableVehicle->id,
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
            'payment_proof' => 'proofs/reviewable-proof.jpg',
        ]);

        $this->createBookingFor($customer, [
            'vehicle_id' => $whatsAppVehicle->id,
            'payment_method' => 'whatsapp',
            'payment_status' => 'pending',
            'payment_proof' => null,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.bookings.index', ['status' => 'awaiting_proof']));

        $response->assertOk()
            ->assertSee('Menunggu Konfirmasi')
            ->assertSee('Toyota Menunggu Bukti Cepat')
            ->assertSee('Honda Bukti Reguler')
            ->assertSee('Suzuki WhatsApp')
            ->assertSee('Belum ada konfirmasi pembayaran')
            ->assertSee('Tinjau Pembayaran')
            ->assertSee('Perlu Follow Up')
            ->assertSee('Booking #' . $waitingProofBooking->id)
            ->assertDontSee('Honda Sudah Upload')
            ->assertDontSee('Honda Sudah Upload');

        $content = $response->getContent();
        $urgentPosition = strpos($content, 'Toyota Menunggu Bukti Cepat');
        $regularPosition = strpos($content, 'Honda Bukti Reguler');

        Assert::assertNotFalse($urgentPosition);
        Assert::assertNotFalse($regularPosition);
        Assert::assertLessThan($regularPosition, $urgentPosition);
        $this->assertStringContainsString((string) $urgentWaitingProofBooking->id, $content);

        Carbon::setTestNow();
    }

    public function test_admin_can_filter_bookings_that_are_past_payment_deadline(): void
    {
        Carbon::setTestNow('2026-03-10 12:00:00');

        $admin = $this->createAdmin();
        $customer = $this->createVerifiedCustomer();

        $olderOverdueVehicle = $this->createVehicle(['name' => 'Toyota Telat Paling Lama']);
        $recentOverdueVehicle = $this->createVehicle(['name' => 'Suzuki WhatsApp Telat']);
        $safeVehicle = $this->createVehicle(['name' => 'Honda Belum Telat']);

        $olderOverdueBooking = $this->createBookingFor($customer, [
            'vehicle_id' => $olderOverdueVehicle->id,
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
            'payment_proof' => null,
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2),
        ]);

        $recentOverdueBooking = $this->createBookingFor($customer, [
            'vehicle_id' => $recentOverdueVehicle->id,
            'payment_method' => 'whatsapp',
            'payment_status' => 'pending',
            'payment_proof' => null,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'duration_days' => 1,
            'total_price' => (float) $recentOverdueVehicle->daily_price,
            'created_at' => now()->subMinutes(90),
            'updated_at' => now()->subMinutes(90),
        ]);

        $this->createBookingFor($customer, [
            'vehicle_id' => $safeVehicle->id,
            'payment_method' => 'transfer_proof',
            'payment_status' => 'pending',
            'payment_proof' => null,
            'created_at' => now()->subMinutes(30),
            'updated_at' => now()->subMinutes(30),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.bookings.index', ['status' => 'overdue_payment']));

        $response->assertOk()
            ->assertSee('Lewat Deadline')
            ->assertSee('Toyota Telat Paling Lama')
            ->assertSee('Suzuki WhatsApp Telat')
            ->assertSee('Daftar ini diurutkan dari booking yang paling lama melewati batas pembayaran.')
            ->assertDontSee('Honda Belum Telat');

        $content = $response->getContent();
        $olderPosition = strpos($content, 'Toyota Telat Paling Lama');
        $recentPosition = strpos($content, 'Suzuki WhatsApp Telat');

        Assert::assertNotFalse($olderPosition);
        Assert::assertNotFalse($recentPosition);
        Assert::assertLessThan($recentPosition, $olderPosition);
        $this->assertStringContainsString((string) $olderOverdueBooking->id, $content);
        $this->assertStringContainsString((string) $recentOverdueBooking->id, $content);

        Carbon::setTestNow();
    }

    private function createVerifiedCustomer(array $overrides = []): User
    {
        return $this->createCustomer(array_merge([
            'ktp_status' => 'verified',
            'ktp_verified_at' => now(),
        ], $overrides));
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

    private function createBookingFor(User $user, array $overrides = []): Booking
    {
        $vehicleId = $overrides['vehicle_id'] ?? null;
        unset($overrides['vehicle_id']);

        $timestamps = [];

        foreach (['created_at', 'updated_at'] as $column) {
            if (array_key_exists($column, $overrides)) {
                $timestamps[$column] = $overrides[$column];
                unset($overrides[$column]);
            }
        }

        $vehicle = $vehicleId ? Vehicle::findOrFail($vehicleId) : $this->createVehicle();
        $startDate = now()->addDays(5)->toDateString();
        $endDate = now()->addDays(7)->toDateString();

        $booking = Booking::create(array_merge([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_days' => 3,
            'daily_price' => (float) $vehicle->daily_price,
            'total_price' => (float) $vehicle->daily_price * 3,
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
            'name' => 'Payment Flow Vehicle ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'BPAY' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'transmission' => 'Manual',
            'year' => 2024,
            'daily_price' => 275000,
            'status' => 'available',
            'description' => 'Vehicle for payment flow tests.',
        ], $overrides));

        $sequence++;

        return $vehicle;
    }
}