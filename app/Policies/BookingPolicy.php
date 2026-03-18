<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Determine whether the user can view the booking.
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    /**
     * Determine whether the user can update the booking.
     */
    public function update(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can cancel the booking.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id && $booking->canBeCancelled();
    }

    /**
     * Determine whether the user can enter the payment flow.
     */
    public function pay(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id && $booking->canEnterPaymentFlow();
    }

    /**
     * Determine whether the user can verify a booking payment.
     */
    public function verifyPayment(User $user, Booking $booking): bool
    {
        return $user->isAdmin() && $booking->canBeVerified();
    }

    /**
     * Determine whether the user can complete a booking.
     */
    public function complete(User $user, Booking $booking): bool
    {
        return $user->isAdmin() && $booking->canBeCompleted();
    }

    /**
     * Determine whether the user can reschedule a booking held by maintenance.
     */
    public function reschedule(User $user, Booking $booking): bool
    {
        return $user->isAdmin() && $booking->canBeRescheduledByAdmin();
    }

    /**
     * Determine whether the user can delete the booking.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }
}