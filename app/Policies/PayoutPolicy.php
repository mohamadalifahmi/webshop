<?php

namespace App\Policies;

use App\Models\Payout;
use App\Models\User;

class PayoutPolicy
{
    public function view(User $user, Payout $payout): bool
    {
        return $user->isAdmin() || ($user->seller && $payout->seller_id === $user->seller->id);
    }

    public function process(User $user, Payout $payout): bool
    {
        return $user->isAdmin();
    }
}
