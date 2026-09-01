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

    public bool $added = false;

    public function mount(Product $product): void
    {
        abort_unless($product->status === 'active', 404);
        $this->product->load(['category', 'seller']);
    }

    public function incrementQuantity(): void
    {
        $this->quantity = min($this->quantity + 1, max(1, $this->product->stock));
    }

    public function decrementQuantity(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    public function addToCart(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $qty = min(max(1, $this->quantity), $this->product->stock);

        CartService::add(auth()->user(), $this->product->id, $qty);

        $this->added = true;

        // Keep the shopper on the product page — the header cart count
        // updates live. They can open the cart any time by clicking it.
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $product = $this->product;
        $mediaUrl = $product->getFirstMediaUrl('images');

        view()->share([
            'pageTitle' => $product->name.' — ASTRAGO MARKET',
            'pageDescription' => \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 160),
            'pageCanonical' => route('product.show', $product),
            // Social crawlers require an absolute og:image — derive it from the
            // current request host so it always matches where the page is served.
            'pageOgImage' => $mediaUrl
                ? url($mediaUrl)
                : (request()->schemeAndHttpHost().'/favicon.ico'),
            'pageRobots' => 'index, follow',
        ]);

        return view('livewire.storefront.product-show');
    }
}
