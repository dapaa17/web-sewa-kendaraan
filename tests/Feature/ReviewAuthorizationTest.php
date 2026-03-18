<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_review_another_users_completed_booking(): void
    {
        $owner = $this->createVerifiedCustomer();
        $intruder = $this->createVerifiedCustomer();
        $booking = $this->createCompletedBookingFor($owner);

        $this->actingAs($intruder)
            ->get(route('reviews.create', $booking))
            ->assertForbidden();

        $this->actingAs($intruder)
            ->post(route('reviews.store', $booking), [
                'rating' => 5,
                'title' => 'Tidak boleh lolos',
                'review_text' => 'User lain tidak boleh bisa mengirim review untuk booking ini sama sekali.',
            ])
            ->assertForbidden();
    }

    public function test_customer_cannot_edit_or_delete_another_users_review(): void
    {
        $owner = $this->createVerifiedCustomer();
        $intruder = $this->createVerifiedCustomer();
        $review = $this->createReviewFor($this->createCompletedBookingFor($owner));

        $this->actingAs($intruder)
            ->get(route('reviews.edit', $review))
            ->assertForbidden();

        $this->actingAs($intruder)
            ->delete(route('reviews.destroy', $review))
            ->assertForbidden();
    }

    public function test_owner_cannot_edit_delete_or_like_approved_review(): void
    {
        $owner = $this->createVerifiedCustomer();
        $review = $this->createReviewFor($this->createCompletedBookingFor($owner), [
            'status' => Review::STATUS_APPROVED,
        ]);

        $this->actingAs($owner)
            ->get(route('reviews.edit', $review))
            ->assertForbidden();

        $this->actingAs($owner)
            ->delete(route('reviews.destroy', $review))
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('reviews.helpful', $review))
            ->assertForbidden();
    }

    public function test_customer_cannot_access_admin_review_pages_or_actions(): void
    {
        $customer = $this->createVerifiedCustomer();
        $review = $this->createReviewFor($this->createCompletedBookingFor($customer));

        $this->actingAs($customer)
            ->get(route('admin.reviews.index'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');

        $this->actingAs($customer)
            ->post(route('admin.reviews.approve', $review), [
                'admin_note' => 'Tidak boleh bisa approve.',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');

        $this->actingAs($customer)
            ->post(route('admin.reviews.reject', $review), [
                'admin_note' => 'Tidak boleh bisa reject.',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');

        $this->actingAs($customer)
            ->delete(route('admin.reviews.destroy', $review))
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

    private function createVehicle(array $overrides = []): Vehicle
    {
        static $sequence = 0;
        $sequence++;

        return Vehicle::create(array_merge([
            'name' => 'Toyota Guard ' . $sequence,
            'vehicle_type' => 'mobil',
            'plat_number' => 'B ' . str_pad((string) (2200 + $sequence), 4, '0', STR_PAD_LEFT) . ' GRD',
            'transmission' => 'Otomatis',
            'year' => 2024,
            'daily_price' => 325000,
            'status' => 'available',
            'description' => 'Unit guard test review.',
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
            'start_date' => now()->subDays(4)->toDateString(),
            'pickup_time' => '09:00:00',
            'end_date' => now()->subDays(2)->toDateString(),
            'return_time' => '17:00:00',
            'actual_return_date' => now()->subDays(2)->toDateString(),
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
            'title' => 'Review untuk pengujian authorization',
            'review_text' => 'Review ini dipakai untuk memastikan policy dan middleware review berjalan benar.',
            'status' => Review::STATUS_PENDING,
        ], $overrides));
    }
}