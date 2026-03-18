<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_review_for_completed_paid_booking(): void
    {
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createCompletedBookingFor($customer);

        $this->actingAs($customer)
            ->get(route('reviews.create', $booking))
            ->assertOk()
            ->assertSeeText($booking->vehicle->name)
            ->assertSeeText('Tulis Review Kendaraan');

        $response = $this->actingAs($customer)
            ->post(route('reviews.store', $booking), [
                'rating' => 5,
                'title' => 'Unit bersih dan nyaman',
                'review_text' => 'Kondisi kendaraan rapi, pickup cepat, dan selama dipakai tidak ada kendala berarti.',
            ]);

        $response->assertRedirect(route('bookings.show', $booking));
        $response->assertSessionHas('success', 'Review berhasil dikirim dan sedang menunggu moderasi admin.');

        $review = Review::first();

        $this->assertNotNull($review);
        $this->assertSame($booking->id, $review->booking_id);
        $this->assertSame($customer->id, $review->user_id);
        $this->assertSame(Review::STATUS_PENDING, $review->status);
        $this->assertSame(0, $review->helpful_count);
    }

    public function test_customer_can_edit_rejected_review_and_send_it_back_to_pending(): void
    {
        $customer = $this->createVerifiedCustomer();
        $booking = $this->createCompletedBookingFor($customer);
        $review = $this->createReviewFor($booking, [
            'status' => Review::STATUS_REJECTED,
            'admin_note' => 'Tolong perjelas pengalaman selama memakai unit.',
            'moderated_at' => now(),
            'moderated_by' => $this->createAdmin()->id,
        ]);

        $response = $this->actingAs($customer)
            ->put(route('reviews.update', $review), [
                'rating' => 4,
                'title' => 'Sudah saya perjelas',
                'review_text' => 'Mobil enak dipakai, AC dingin, dan proses serah terima dari admin berlangsung cepat.',
            ]);

        $response->assertRedirect(route('reviews.index'));
        $response->assertSessionHas('success', 'Review berhasil diperbarui dan dikirim ulang untuk moderasi admin.');

        $review->refresh();

        $this->assertSame(Review::STATUS_PENDING, $review->status);
        $this->assertSame('Sudah saya perjelas', $review->title);
        $this->assertNull($review->admin_note);
        $this->assertNull($review->moderated_at);
        $this->assertNull($review->moderated_by);
    }

    public function test_admin_can_view_and_moderate_reviews(): void
    {
        $admin = $this->createAdmin();
        $booking = $this->createCompletedBookingFor($this->createVerifiedCustomer());
        $review = $this->createReviewFor($booking, [
            'title' => 'Perlu ditinjau admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reviews.index'))
            ->assertOk()
            ->assertSeeText('Moderasi Ulasan')
            ->assertSeeText('Perlu ditinjau admin');

        $this->actingAs($admin)
            ->post(route('admin.reviews.reject', $review), [
                'admin_note' => 'Mohon hindari kalimat yang terlalu singkat.',
            ])
            ->assertRedirect();

        $review->refresh();

        $this->assertSame(Review::STATUS_REJECTED, $review->status);
        $this->assertSame('Mohon hindari kalimat yang terlalu singkat.', $review->admin_note);
        $this->assertSame($admin->id, $review->moderated_by);

        $this->actingAs($admin)
            ->post(route('admin.reviews.approve', $review), [
                'admin_note' => 'Sudah oke untuk ditampilkan.',
            ])
            ->assertRedirect();

        $review->refresh();

        $this->assertSame(Review::STATUS_APPROVED, $review->status);
        $this->assertSame('Sudah oke untuk ditampilkan.', $review->admin_note);

        $this->actingAs($admin)
            ->delete(route('admin.reviews.destroy', $review))
            ->assertRedirect(route('admin.reviews.index'));

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_vehicle_pages_show_average_rating_and_top_review(): void
    {
        $viewer = $this->createVerifiedCustomer();
        $vehicle = $this->createVehicle();

        $firstBooking = $this->createCompletedBookingFor($this->createVerifiedCustomer(), [
            'vehicle_id' => $vehicle->id,
        ]);
        $secondBooking = $this->createCompletedBookingFor($this->createVerifiedCustomer(), [
            'vehicle_id' => $vehicle->id,
        ]);

        $this->createReviewFor($firstBooking, [
            'rating' => 5,
            'title' => 'Paling nyaman untuk perjalanan jauh',
            'review_text' => 'Kabinnya bersih, suspensi nyaman, dan proses booking sampai serah terima sangat rapi.',
            'status' => Review::STATUS_APPROVED,
            'helpful_count' => 5,
        ]);

        $this->createReviewFor($secondBooking, [
            'rating' => 4,
            'title' => 'Overall bagus',
            'review_text' => 'Mesin halus, konsumsi BBM oke, hanya ingin kursinya sedikit lebih empuk.',
            'status' => Review::STATUS_APPROVED,
            'helpful_count' => 1,
        ]);

        $this->actingAs($viewer)
            ->get(route('vehicles.show', $vehicle))
            ->assertOk()
            ->assertSeeText('Rating Pelanggan')
            ->assertSeeText('4,5')
            ->assertSeeText('Paling nyaman untuk perjalanan jauh')
            ->assertSeeText('Ulasan Pelanggan');

        $this->actingAs($viewer)
            ->get(route('vehicles.browse'))
            ->assertOk()
            ->assertSeeText('2 ulasan')
            ->assertSeeText('Paling nyaman untuk perjalanan jauh');
    }

    public function test_customer_can_toggle_helpful_on_other_customers_approved_review(): void
    {
        $owner = $this->createVerifiedCustomer();
        $viewer = $this->createVerifiedCustomer();
        $booking = $this->createCompletedBookingFor($owner);
        $review = $this->createReviewFor($booking, [
            'status' => Review::STATUS_APPROVED,
        ]);

        $this->actingAs($viewer)
            ->post(route('reviews.helpful', $review))
            ->assertRedirect();

        $review->refresh();
        $this->assertSame(1, $review->helpful_count);

        $this->actingAs($viewer)
            ->post(route('reviews.helpful', $review))
            ->assertRedirect();

        $review->refresh();
        $this->assertSame(0, $review->helpful_count);
    }

    public function test_review_submission_is_rate_limited_per_user_and_vehicle(): void
    {
        $customer = $this->createVerifiedCustomer();
        $vehicle = $this->createVehicle();
        $firstBooking = $this->createCompletedBookingFor($customer, ['vehicle_id' => $vehicle->id]);
        $secondBooking = $this->createCompletedBookingFor($customer, ['vehicle_id' => $vehicle->id]);

        $payload = [
            'rating' => 5,
            'title' => 'Review pertama aman',
            'review_text' => 'Kendaraan enak dipakai dan admin sangat responsif sejak awal hingga pengembalian unit.',
        ];

        $this->actingAs($customer)
            ->post(route('reviews.store', $firstBooking), $payload)
            ->assertRedirect(route('bookings.show', $firstBooking));

        $this->actingAs($customer)
            ->from(route('reviews.create', $secondBooking))
            ->post(route('reviews.store', $secondBooking), $payload)
            ->assertRedirect(route('reviews.create', $secondBooking))
            ->assertSessionHasErrors('review_text');

        $this->assertSame(1, Review::count());
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

    private function createVehicle(array $overrides = []): Vehicle
    {
        static $sequence = 0;
        $sequence++;

        return Vehicle::create(array_merge([
            'name' => 'Toyota Review ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'B ' . str_pad((string) (1200 + $sequence), 4, '0', STR_PAD_LEFT) . ' RVW',
            'transmission' => 'Otomatis',
            'year' => 2024,
            'daily_price' => 350000,
            'status' => 'available',
            'description' => 'Unit test untuk review kendaraan.',
        ], $overrides));
    }

    private function createCompletedBookingFor(User $user, array $overrides = []): Booking
    {
        $vehicle = array_key_exists('vehicle_id', $overrides)
            ? Vehicle::findOrFail($overrides['vehicle_id'])
            : $this->createVehicle();

        return Booking::create(array_merge([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->subDays(5)->toDateString(),
            'pickup_time' => '09:00:00',
            'end_date' => now()->subDays(3)->toDateString(),
            'return_time' => '17:00:00',
            'actual_return_date' => now()->subDays(3)->toDateString(),
            'duration_days' => 3,
            'daily_price' => $vehicle->daily_price,
            'total_price' => $vehicle->daily_price * 3,
            'status' => 'completed',
            'payment_method' => 'transfer_proof',
            'payment_status' => 'paid',
        ], $overrides));
    }

    private function createReviewFor(Booking $booking, array $overrides = []): Review
    {
        return Review::create(array_merge([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'vehicle_id' => $booking->vehicle_id,
            'rating' => 5,
            'title' => 'Review kendaraan yang sangat baik',
            'review_text' => 'Kendaraan nyaman, bersih, dan proses pengambilan sampai pengembalian berjalan lancar.',
            'status' => Review::STATUS_PENDING,
        ], $overrides));
    }
}