<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Perform pre-authorization checks.
     * Admin has full authority over all bookings.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any bookings list.
     */
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    /**
     * Determine whether the user can view the booking.
     * Staff can read all bookings of a meeting room (e.g. to see room schedule/availability).
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->isStaff();
    }

    /**
     * Determine whether the user can create bookings.
     * Staff can create their own bookings.
     */
    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    /**
     * Determine whether the user can update the booking.
     * Staff can update their own bookings only.
     */
    public function update(User $user, ?Booking $booking = null): bool
    {
        if ($booking === null) {
            return false;
        }

        return $user->isStaff() && $booking->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete (cancel, soft delete) the booking.
     * Staff can delete (cancel) their own bookings only.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->isStaff() && $booking->user_id === $user->id;
    }
}
