<?php

namespace App\Policies;

use App\Enums\EnrollmentStatus;
use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Super Admin bypass for all student operations.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the student details.
     */
    public function view(User $user, Student $student): bool
    {
        if ($user->isParent()) {
            $parentProfile = $user->parentProfile;

            return $parentProfile !== null && $student->parent_id === $parentProfile->id;
        }

        if ($user->isMentor()) {
            $mentor = $user->mentor;
            if (! $mentor) {
                return false;
            }

            $isAssignedMentor = $student->mentors()
                ->where('mentors.id', $mentor->id)
                ->where('mentor_student.is_active', true)
                ->exists();

            $hasActiveEnrollment = $student->enrollments()
                ->where('mentor_id', $mentor->id)
                ->where('status', EnrollmentStatus::ACTIVE->value)
                ->exists();

            return $isAssignedMentor || $hasActiveEnrollment;
        }

        if ($user->isStudent()) {
            $studentRecord = $user->student;

            return $studentRecord !== null && $studentRecord->id === $student->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the student profile.
     */
    public function update(User $user, Student $student): bool
    {
        if ($user->isParent()) {
            $parentProfile = $user->parentProfile;

            return $parentProfile !== null && $student->parent_id === $parentProfile->id;
        }

        return false;
    }

    /**
     * Determine whether the user can export or view academic reports.
     */
    public function exportReport(User $user, Student $student): bool
    {
        return $this->view($user, $student);
    }

    /**
     * Determine whether the user can request or reset password for the student account.
     */
    public function resetPassword(User $user, Student $student): bool
    {
        if ($user->isParent()) {
            $parentProfile = $user->parentProfile;

            return $parentProfile !== null && $student->parent_id === $parentProfile->id;
        }

        return false;
    }
}
