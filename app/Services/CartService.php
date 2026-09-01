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
            // Lock the current stock row inside the transaction so concurrent
            // "add" calls can never oversell against a stale stock value.
            $locked = Product::active()->lockForUpdate()->find($product->id);

            if ($locked && $locked->stock <= 0) {
                return;
            }

            $maxStock = $locked ? (int) $locked->stock : (int) $product->stock;

            if ($existing) {
                $newQty = min($existing->quantity + $quantity, $maxStock);
                $existing->update(['quantity' => $newQty]);
            } elseif ($maxStock > 0) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => min($quantity, $maxStock),
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

        // A missing/deleted product can't be capped to a stock — delete the line.
        if (! $item->product) {
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
        return CartItem::query()
            ->where('cart_id', self::forUser($user)->id)
            ->with(['product' => fn ($q) => $q->withTrashed()->with(['category', 'seller'])])
            ->get();
    }

    public static function subtotal($user): float
    {
        $total = '0';
        foreach (self::items($user) as $item) {
            // Tolerate lines whose product no longer exists (seller deleted it).
            if (! $item->product) {
                continue;
            }
            $line = bcmul((string) $item->product->price, (string) $item->quantity, 2);
            $total = bcadd($total, $line, 2);
        }

        return (float) $total;
    }

    public static function clear($user): void
    {
        self::forUser($user)->items()->delete();
    }
}
