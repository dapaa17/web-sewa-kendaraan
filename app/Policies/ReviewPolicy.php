<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine whether the user can view the review listing.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the review.
     */
    public function view(User $user, Review $review): bool
    {
        return $user->isAdmin()
            || $review->user_id === $user->id
            || $review->isApproved();
    }

    /**
     * Determine whether the user can create a review for the booking.
     */
    public function create(User $user, Booking $booking): bool
    {
        return $user->isCustomer()
            && $user->id === $booking->user_id
            && $booking->canBeReviewed();
    }

    /**
     * Determine whether the user can update the review.
     */
    public function update(User $user, Review $review): bool
    {
        return $review->user_id === $user->id
            && $review->canBeManagedByOwner();
    }

    /**
     * Determine whether the user can delete the review.
     */
    public function delete(User $user, Review $review): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $review->user_id === $user->id
            && $review->canBeManagedByOwner();
    }

    /**
     * Determine whether the user can moderate the review.
     */
    public function moderate(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can mark the review as helpful.
     */
    public function markHelpful(User $user, Review $review): bool
    {
        return $review->isApproved() && $review->user_id !== $user->id;
    }
}