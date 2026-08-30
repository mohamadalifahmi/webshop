<?php

/*
|--------------------------------------------------------------------------
| QA TEST 7 — HOMEPAGE SECTIONS + MOST-BUYING ALGORITHM
| Action: Homepage is grouped into titled sections, topped by "Most Buying"
| Expected: Section titles render; ranking follows total quantity sold;
|           no orders -> falls back to newest active products
*/

use App\Livewire\Storefront\Home;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Str;

function makeOrderItem(Seller $seller, Product $product, int $quantity, string $status = 'delivered'): void
{
    $buyer = User::factory()->create();
    $order = Order::create([
        'user_id' => $buyer->id,
        'order_number' => Order::generateNumber(),
        'status' => $status,
        'subtotal' => (float) $product->price * $quantity,
        'shipping_fee' => 5,
        'total' => (float) $product->price * $quantity + 5,
        'currency' => 'USD',
        'payment_method' => 'manual',
        'payment_status' => 'paid',
        'ship_to_name' => $buyer->name,
        'ship_to_phone' => '+961 70 000000',
        'governorate' => 'Beirut',
        'address' => 'Hamra, Street 42',
        'paid_at' => now(),
    ]);

    $order->items()->create([
        'seller_id' => $seller->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'product_sku' => $product->sku,
        'unit_price' => $product->price,
        'quantity' => $quantity,
        'subtotal' => (float) $product->price * $quantity,
        'commission_rate' => 0.1,
        'commission_amount' => round((float) $product->price * $quantity * 0.1, 2),
        'seller_earning' => round((float) $product->price * $quantity * 0.9, 2),
        'shipment_status' => 'delivered',
    ]);
}

function makeCategory(string $name): Category
{
    return Category::create([
        'name' => $name,
        'slug' => Str::slug($name).Str::lower(Str::random(3)),
        'icon' => '🛒',
    ]);
}

it('renders grouped sections with a titled "Most Buying" section on the home page', function () {
    $category = makeCategory('Gadgets');
    $product = makeActiveProduct(makeApprovedSeller()[1], 30, 5, 'Gadget One');
    $product->update(['category_id' => $category->id]);

    Livewire::test(Home::class)
        ->assertSee('Most Buying')
        ->assertSee('Gadgets')
        ->assertSee('Gadget One');

    $this->get(route('home'))->assertOk();
    $this->get(route('shop'))->assertOk();
});

it('ranks most-bought products by total quantity sold, keeping the group order', function () {
    [$userA, $sellerA] = makeApprovedSeller('Alpha');
    [$userB, $sellerB] = makeApprovedSeller('Beta');
    [$userC, $sellerC] = makeApprovedSeller('Gamma');

    $hot = makeActiveProduct($sellerA, 10, 50, 'Hot Seller');
    $mid = makeActiveProduct($sellerB, 20, 50, 'Middle Shop');
    $cold = makeActiveProduct($sellerC, 30, 50, 'Cold Product');

    makeOrderItem($sellerA, $hot, 5);
    makeOrderItem($sellerB, $mid, 1);

    Livewire::test(Home::class)
        ->call('mostBought')
        ->assertReturned(function (array $products) use ($hot, $mid, $cold) {
            expect(array_column($products, 'id'))->toBe([$hot->id, $mid->id])
                ->and(array_column($products, 'id'))->not->toContain($cold->id);

            return true;
        });
});

it('falls back to newest active products when there are no orders yet', function () {
    [$user, $seller] = makeApprovedSeller();
    $newer = makeActiveProduct($seller, 10, 5, 'Brand New Item');
    $older = makeActiveProduct($seller, 20, 5, 'Older Item');

    expect(Order::count())->toBe(0);

    $result = app(Home::class)->mostBought();

    expect($result->pluck('name')->toArray())->toBe(['Brand New Item', 'Older Item']);
});

it('keeps pending and inactive products out of homepage sections', function () {
    $category = makeCategory('Beauty');
    [$user, $seller] = makeApprovedSeller();

    $visible = makeActiveProduct($seller, 15, 5, 'Visible Item');
    $hidden = makeActiveProduct($seller, 25, 5, 'Hidden Pending Item');

    Product::whereKey($hidden->id)->update(['status' => 'pending', 'category_id' => $category->id]);
    Product::whereKey($visible->id)->update(['category_id' => $category->id]);

    Livewire::test(Home::class)
        ->assertSee('Visible Item')
        ->assertDontSee('Hidden Pending Item');
});

it('search-as-you-type shows suggestions and live results without a button', function () {
    $category = makeCategory('Gadgets');
    [$user, $seller] = makeApprovedSeller();

    $anker = makeActiveProduct($seller, 39, 20, 'Anker PowerCore');
    $samsung = makeActiveProduct($seller, 21, 20, 'Samsung Galaxy S24');
    Product::whereKey($anker->id)->update(['category_id' => $category->id]);
    Product::whereKey($samsung->id)->update(['category_id' => $category->id]);

    Livewire::test(Home::class)
        ->set('q', 'anker')
        ->assertSee('Search results')
        ->assertSee('Anker PowerCore')
        ->assertDontSee('Samsung Galaxy S24')
        ->assertDontSee('Most Buying');

    // Clearing the query brings the grouped homepage back
    Livewire::test(Home::class)
        ->set('q', '')
        ->assertDontSee('Search results')
        ->assertSee('Most Buying');
});