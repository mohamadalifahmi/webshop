<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    public static function forUser($user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    public static function add($user, int $productId, int $quantity = 1): void
    {
        $product = Product::active()->findOrFail($productId);

        $cart = self::forUser($user);
        $existing = CartItem::where('cart_id', $cart->id)->where('product_id', $product->id)->first();

        $quantity = max(1, $quantity);

        DB::transaction(function () use ($cart, $product, $quantity, $existing) {
            if ($existing) {
                $newQty = min($existing->quantity + $quantity, $product->stock);
                $existing->update(['quantity' => $newQty]);
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => min($quantity, $product->stock),
                ]);
            }
        });
    }

    public static function updateQuantity($user, int $itemId, int $quantity): void
    {
        $item = CartItem::whereHas('cart', fn ($q) => $q->where('user_id', $user->id))->findOrFail($itemId);

        if ($quantity <= 0) {
            $item->delete();

            return;
        }

        $item->update(['quantity' => min($quantity, max(1, $item->product->stock))]);
    }

    public static function remove($user, int $itemId): void
    {
        CartItem::whereHas('cart', fn ($q) => $q->where('user_id', $user->id))->where('id', $itemId)->delete();
    }

    public static function items($user): Collection
    {
        return self::forUser($user)->items()->with(['product.category', 'product.seller'])->get();
    }

    public static function subtotal($user): float
    {
        return (float) self::items($user)->sum(fn ($item) => $item->product->price * $item->quantity);
    }

    public static function clear($user): void
    {
        self::forUser($user)->items()->delete();
    }
}
