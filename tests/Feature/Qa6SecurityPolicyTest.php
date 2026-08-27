<?php

/*
|--------------------------------------------------------------------------
| QA TEST 6 — SECURITY
| Action: Seller A tries to edit Seller B product URL
| Expected: 403 Forbidden
*/

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;

it('forbids seller B from editing or deleting seller A product via URL', function () {
    [$sellerAUser] = makeApprovedSeller('Seller A');
    [$sellerBUser] = makeApprovedSeller('Seller B');

    $productA = makeActiveProduct($sellerAUser->seller, 99, 3, 'Product of A');

    // Seller B hits Seller A's product update URL
    $this->actingAs($sellerBUser)
        ->patch(route('seller.products.update', $productA), ['name' => 'HACKED'])
        ->assertForbidden();

    expect($productA->fresh()->name)->not->toBe('HACKED');

    // Delete is forbidden too
    $this->actingAs($sellerBUser)
        ->delete(route('seller.products.destroy', $productA))
        ->assertForbidden();

    expect(Product::find($productA->id))->not->toBeNull();

    // Owner CAN edit through the same URL
    $this->actingAs($sellerAUser)
        ->patch(route('seller.products.update', $productA), ['name' => 'Renamed by owner'])
        ->assertRedirect();

    expect($productA->fresh()->name)->toBe('Renamed by owner');
});

it('forbids non-sellers from entering the seller hub and sellers from the admin panel', function () {
    $plainBuyer = User::factory()->create();

    $this->actingAs($plainBuyer)
        ->get(route('seller.dashboard'))
        ->assertForbidden();

    [$sellerUser] = makeApprovedSeller();

    $this->actingAs($sellerUser)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('isolates seller order data between stores', function () {
    [$sellerAUser] = makeApprovedSeller('Seller A');
    [$sellerBUser] = makeApprovedSeller('Seller B');

    $productA = makeActiveProduct($sellerAUser->seller, 30, 10, 'Only For A');

    $buyer = User::factory()->create();
    $this->actingAs($buyer);
    CartService::add($buyer, $productA->id, 1);

    $order = OrderService::place($buyer, 'manual', [
        'name' => 'Buyer X',
        'phone' => '+961 03 000000',
        'governorate' => 'Beirut',
        'address' => 'Downtown',
        'note' => null,
    ]);

    // Seller B's scoped queries must NOT see Seller A's items
    $itemsForB = OrderItem::query()
        ->join('orders', 'orders.id', '=', 'order_items.order_id')
        ->where('order_items.seller_id', $sellerBUser->seller->id)
        ->count();

    expect($itemsForB)->toBe(0)
        ->and($order->items()->where('seller_id', $sellerAUser->seller->id)->exists())->toBeTrue()
        ->and((float) Seller::find($sellerBUser->seller->id)->balance)->toBe(0.0);
});
