<?php

namespace App\Policies;

use App\Models\MeetingRoom;
use App\Models\User;

class MeetingRoomPolicy
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

    public function view(User $user, MeetingRoom $meetingRoom): bool
    {
        return $user->isStaff();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, MeetingRoom $meetingRoom): bool
    {
        return false;
    }

    public function delete(User $user, MeetingRoom $meetingRoom): bool
    {
        return false;
    }

    public function restore(User $user, MeetingRoom $meetingRoom): bool
    {
        return false;
    }

    public function forceDelete(User $user, MeetingRoom $meetingRoom): bool
    {
        return false;
    }
}
