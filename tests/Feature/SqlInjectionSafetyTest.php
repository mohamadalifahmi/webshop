<?php

/*
|--------------------------------------------------------------------------
| SECURITY SUITE — SQL Injection safety (Eloquent prepared statements)
|--------------------------------------------------------------------------
*/

use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\DemoCatalogSeeder;
use Database\Seeders\RoleSeeder;

it('neutralizes SQL injection payloads in search queries', function () {
    $this->seed([RoleSeeder::class, CatalogSeeder::class, DemoCatalogSeeder::class]);

    // Classic boolean-based and union-based payloads
    $payloads = [
        "' OR '1'='1",
        "' UNION SELECT null,null,null--",
        "1'; DROP TABLE products;--",
        '‘ OR 1=1 --',
    ];

    foreach ($payloads as $payload) {
        $response = $this->get(route('shop', ['q' => $payload]));

        $response->assertOk();

        // The catalog must NOT return every product (no injection leak)
        $response->assertDontSee('Ahmed Electronics', false);
    }

    // Products table still intact after all attempted injections
    expect(Product::count())->toBeGreaterThan(0);
});

it('ignores unsafe quantity values when adding to cart', function () {
    [$sellerUser, $seller] = makeApprovedSeller();
    $product = makeActiveProduct($seller, 5, 3, 'Safe Stock');

    $buyer = User::factory()->create();

    // SQLi-ish or absurd quantities get clamped, never exploded
    try {
        CartService::add($buyer, $product->id, 99999);
        CartService::add($buyer, $product->id, -5);
    } catch (Throwable) {
        $this->fail('Cart should never throw on malicious quantities.');
    }

    expect(CartService::items($buyer)->sum('quantity'))->toBeLessThanOrEqual(3);
});
