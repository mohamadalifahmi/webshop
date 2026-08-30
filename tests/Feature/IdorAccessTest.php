<?php

/*
|--------------------------------------------------------------------------
| SECURITY SUITE — Layer 4: IDOR / authorization isolation
|--------------------------------------------------------------------------
*/

use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use Database\Seeders\CatalogSeeder;

it('blocks a seller from viewing another store order detail page', function () {
    $this->seed(CatalogSeeder::class);

    [$sellerAUser, $sellerA] = makeApprovedSeller('Seller A');
    [$sellerBUser] = makeApprovedSeller('Seller B');

    $product = makeActiveProduct($sellerA, 25, 5, 'Seller A Only');

    $buyer = User::factory()->create();
    $this->actingAs($buyer);
    CartService::add($buyer, $product->id, 1);

    $order = OrderService::place($buyer, 'manual', [
        'name' => 'X', 'phone' => 'Y', 'governorate' => 'Beirut', 'address' => 'Z', 'note' => null,
    ]);

    // Seller B (not the buyer, not the owner) cannot see the buyer's order page
    $this->actingAs($sellerBUser)
        ->get(route('account.orders.show', $order->order_number))
        ->assertStatus(404);
});

it('blocks a buyer from reading another buyers order through id manipulation', function () {
    $this->seed(CatalogSeeder::class);

    [$sellerUser, $seller] = makeApprovedSeller();
    $product = makeActiveProduct($seller, 10, 5, 'Widgit');

    $buyerA = User::factory()->create();
    $this->actingAs($buyerA);
    CartService::add($buyerA, $product->id, 1);
    $orderA = OrderService::place($buyerA, 'manual', [
        'name' => 'A', 'phone' => '1', 'governorate' => 'Beirut', 'address' => 'A', 'note' => null,
    ]);

    $buyerB = User::factory()->create();
    $this->actingAs($buyerB)
        ->get(route('account.orders.show', $orderA->order_number))
        ->assertStatus(404);
});
