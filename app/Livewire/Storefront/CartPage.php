<?php

namespace App\Livewire\Storefront;

use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.storefront')]
class CartPage extends Component
{
    public function getItemsProperty()
    {
        if (! auth()->check()) {
            return collect();
        }

        return CartService::items(auth()->user());
    }

    #[On('cart-updated')]
    public function render()
    {
        return view('livewire.storefront.cart-page', [
            'items' => $this->items,
            'subtotal' => auth()->check() ? CartService::subtotal(auth()->user()) : 0,
        ]);
    }

    public function updateQty(int $itemId, int $quantity): void
    {
        CartService::updateQuantity(auth()->user(), $itemId, $quantity);
        $this->dispatch('cart-updated');
    }

    public function removeItem(int $itemId): void
    {
        CartService::remove(auth()->user(), $itemId);
        $this->dispatch('cart-updated');
    }
}
