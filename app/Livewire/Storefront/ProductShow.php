<?php

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class ProductShow extends Component
{
    public Product $product;

    public int $quantity = 1;

    public function mount(Product $product): void
    {
        abort_unless($product->status === 'active', 404);
        $this->product->load(['category', 'seller']);
    }

    public function addToCart(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $qty = min(max(1, $this->quantity), $this->product->stock);

        CartService::add(auth()->user(), $this->product->id, $qty);

        session()->flash('success', "Added {$qty} x {$this->product->name} to your cart.");

        $this->redirect(route('cart'), navigate: true);
    }

    public function render()
    {
        return view('livewire.storefront.product-show');
    }
}
