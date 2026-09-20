<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Super Admin bypass for payment and financial records.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the payment invoice.
     */
    public function view(User $user, Payment $payment): bool
    {
        if ($user->isParent()) {
            $parentProfile = $user->parentProfile;
            if (! $parentProfile) {
                return false;
            }

            return $parentProfile->students()->where('students.id', $payment->student_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can make an online payment for this invoice.
     */
    public function pay(User $user, Payment $payment): bool
    {
        return $this->view($user, $payment);
    }
}
