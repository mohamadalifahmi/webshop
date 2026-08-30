<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCount extends Component
{
    #[On('cart-updated')]
    public function render()
    {
        $count = 0;

        if (auth()->check()) {
            try {
                $count = CartService::items(auth()->user())->sum('quantity');
            } catch (\Throwable) {
                $count = 0;
            }
        }

        return view('livewire.cart-count', ['count' => $count]);
    }
}
