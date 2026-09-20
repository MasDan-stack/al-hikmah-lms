<?php

namespace App\Policies;

use App\Models\Mentor;
use App\Models\User;

class MentorPolicy
{
    /**
     * Super Admin bypass for all mentor management tasks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view mentor details.
     */
    public function view(User $user, Mentor $mentor): bool
    {
        if ($user->isMentor()) {
            return $user->mentor !== null && $user->mentor->id === $mentor->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view or print salary / honorarium slips.
     */
    public function viewSalarySlip(User $user, Mentor $mentor): bool
    {
        if ($user->isMentor()) {
            return $user->mentor !== null && $user->mentor->id === $mentor->id;
        }

        return false;
    }

    /**
     * Determine whether the user can mark salary as paid.
     * Handled exclusively by Super Admin via before().
     */
    public function markSalaryPaid(User $user, Mentor $mentor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update mentor profile / banking info.
     */
    public function update(User $user, Mentor $mentor): bool
    {
        if ($user->isMentor()) {
            return $user->mentor !== null && $user->mentor->id === $mentor->id;
        }

        return false;
    }
}
