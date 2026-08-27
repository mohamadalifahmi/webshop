<?php

/*
|--------------------------------------------------------------------------
| QA TEST 3 — MULTI-SELLER CHECKOUT
| Action: Buyer adds product from 2 sellers -> Checkout -> Pay
| Expected: 1 Order with 2 OrderItems, each with correct commission split
|   $50 item @10% -> commission 5.00, earning 45.00
|   $20 item @10% -> commission 2.00, earning 18.00
|   Shipping Beirut $5 -> order total $75
*/

use App\Livewire\Storefront\Checkout;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use Database\Seeders\CatalogSeeder;
use Illuminate\Http\Testing\File;

it('creates one order with two items and correct commissions from two sellers', function () {
    $this->seed(CatalogSeeder::class);

    [$sellerAUser, $sellerA] = makeApprovedSeller('Seller A');
    [$sellerBUser, $sellerB] = makeApprovedSeller('Seller B');

    $shoes = makeActiveProduct($sellerA, 50, 10, 'Nike Shoes');
    $tshirt = makeActiveProduct($sellerB, 20, 10, 'T-Shirt');

    $buyer = User::factory()->create();
    $this->actingAs($buyer);

    CartService::add($buyer, $shoes->id, 1);
    CartService::add($buyer, $tshirt->id, 1);

    expect(CartService::subtotal($buyer))->toBe(70.0);

    Livewire::withQueryParams([])
        ->test(Checkout::class)
        ->set('name', 'Ali Buyer')
        ->set('phone', '+961 03 999999')
        ->set('governorate', 'Beirut')
        ->set('address', 'Hamra Street, Building 12')
        ->set('paymentMethod', 'manual')
        ->set('proof', File::fake()->image('proof.jpg'))
        ->call('placeOrder')
        ->assertHasNoErrors();

    $orders = Order::where('user_id', $buyer->id)->get();

    // ONE order, TWO items
    expect($orders)->toHaveCount(1);

    $order = $orders->first();
    expect($order->items)->toHaveCount(2)
        ->and((float) $order->subtotal)->toBe(70.0)
        ->and((float) $order->shipping_fee)->toBe(5.0)
        ->and((float) $order->total)->toBe(75.0);

    $itemA = $order->items->firstWhere('seller_id', $sellerA->id);
    $itemB = $order->items->firstWhere('seller_id', $sellerB->id);

    expect($itemA)->not->toBeNull()
        ->and((float) $itemA->unit_price)->toBe(50.0)
        ->and((float) $itemA->commission_rate)->toBe(10.0)
        ->and((float) $itemA->commission_amount)->toBe(5.0)
        ->and((float) $itemA->seller_earning)->toBe(45.0)
        ->and($itemB)->not->toBeNull()
        ->and((float) $itemB->commission_amount)->toBe(2.0)
        ->and((float) $itemB->seller_earning)->toBe(18.0);

    // Platform total commission = 7.00
    expect((float) $order->items()->sum('commission_amount'))->toBe(7.0);

    // Stock decremented atomically
    expect($shoes->fresh()->stock)->toBe(9)
        ->and($tshirt->fresh()->stock)->toBe(9);

    // Cart cleared after checkout
    expect(CartService::items($buyer))->toHaveCount(0);
});
