<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('seller');
    }

    public function view(User $user, Product $product): bool
    {
        return $this->manage($user, $product);
    }

    public function create(User $user): bool
    {
        return $user->isSeller();
    }

    public function update(User $user, Product $product): bool
    {
        return $this->manage($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->manage($user, $product);
    }

    private function manage(User $user, Product $product): bool
    {
        return $user->isAdmin() || ($user->seller && $product->seller_id === $user->seller->id);
    }
}
