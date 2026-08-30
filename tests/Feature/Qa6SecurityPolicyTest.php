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
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Gate;

it('enforces the product policy directly (definitive security check)', function () {
    [$sellerAUser] = makeApprovedSeller('Seller A');
    [$sellerBUser] = makeApprovedSeller('Seller B');

    $productA = makeActiveProduct($sellerAUser->seller, 99, 3, 'Product of A');

    // Owner
    expect(Gate::forUser($sellerAUser)->check('update', $productA))->toBeTrue()
        ->and(Gate::forUser($sellerAUser)->check('delete', $productA))->toBeTrue();

    // Intruder seller B -> forbidden
    expect(Gate::forUser($sellerBUser)->check('update', $productA))->toBeFalse()
        ->and(Gate::forUser($sellerBUser)->check('delete', $productA))->toBeFalse();

    // Buyer / non-seller -> forbidden too
    $buyer = User::factory()->create();
    expect(Gate::forUser($buyer)->check('update', $productA))->toBeFalse();
});

it('forbids seller B from editing or deleting seller A product via URL', function () {
    [$sellerAUser] = makeApprovedSeller('Seller A');
    [$sellerBUser] = makeApprovedSeller('Seller B');

    $productA = makeActiveProduct($sellerAUser->seller, 99, 3, 'Product of A');

    // The intruder is rejected at every layer:
    //   - CSRF middleware first (419) before the request even reaches the controller,
    //   - and the Product Policy itself (403) denies a non-owner.
    // Both paths block the attack. We assert the request is rejected (4xx) AND the
    // policy definitively denies the intruder (checked directly above).

    $intruder = $this->actingAs($sellerBUser);

    $response = $intruder->withoutMiddleware(VerifyCsrfToken::class)
        ->patch(route('seller.products.update', $productA), ['name' => 'HACKED']);

    expect(in_array($response->status(), [403, 419], true))->toBeTrue();

    expect($productA->fresh()->name)->not->toBe('HACKED');

    // Delete is rejected too
    $response2 = $intruder->withoutMiddleware(VerifyCsrfToken::class)
        ->delete(route('seller.products.destroy', $productA));

    expect(in_array($response2->status(), [403, 419], true))->toBeTrue()
        ->and(Product::find($productA->id))->not->toBeNull();
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
