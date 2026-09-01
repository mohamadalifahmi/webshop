<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function updateQuantity(Request $request, CartItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:1000000',
        ]);

        // Ownership + stock clamping happen inside the service.
        CartService::updateQuantity(auth()->user(), $item->id, (int) $validated['quantity']);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, CartItem $item)
    {
        CartService::remove(auth()->user(), $item->id);

        return back()->with('success', 'Item removed from your cart.');
    }
}