<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->isStaff() && $user->id === $booking->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->isStaff() && $user->id === $booking->user_id;
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->isStaff() && $user->id === $booking->user_id;
    }

    public function restore(User $user, Booking $booking): bool
    {
        return $user->isStaff() && $user->id === $booking->user_id;
    }

    public function forceDelete(User $user, Booking $booking): bool
    {
        return $user->isStaff() && $user->id === $booking->user_id;
    }
}
