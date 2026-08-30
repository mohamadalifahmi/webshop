<?php

/*
|--------------------------------------------------------------------------
| QA TEST 2 — PRODUCT LIFECYCLE
| Action: Seller creates product -> Admin Approves
| Expected: Product appears on homepage (and rejection path emails the reason)
*/

use App\Livewire\Admin\ProductsModeration;
use App\Livewire\Seller\ProductsManager;
use App\Livewire\Storefront\ProductShow;
use App\Mail\ProductRejectedMail;
use App\Models\Category;
use App\Models\Product;

it('creates a product as pending, then shows it on the homepage once approved', function () {
    [$sellerUser, $seller] = makeApprovedSeller();
    $admin = makeAdmin();

    $category = Category::firstOrCreate(['slug' => 'electronics'], ['name' => 'Electronics']);

    // Step 1: seller creates a product -> goes to pending, NOT visible yet
    $this->actingAs($sellerUser);

    Livewire::test(ProductsManager::class)
        ->set('name', 'iPhone 15')
        ->set('description', 'Brand new iPhone 15.')
        ->set('price', '1000')
        ->set('stock', '5')
        ->set('categoryId', $category->id)
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::where('name', 'iPhone 15')->firstOrFail();
    expect($product->status)->toBe('pending')
        ->and($product->sku)->not->toBe('');

    $this->get(route('home'))->assertDontSee('iPhone 15');

    // Step 2: admin approves -> live on homepage
    $this->actingAs($admin);

    Livewire::test(ProductsModeration::class)
        ->set('statusFilter', '')
        ->call('approve', $product->id);

    expect($product->fresh()->status)->toBe('active');

    $this->get(route('home'))->assertSee('iPhone 15');
});

it('rejects products with a reason and notifies the seller by email', function () {
    Mail::fake();

    [$sellerUser] = makeApprovedSeller();
    $admin = makeAdmin();
    $product = makeActiveProduct($sellerUser->seller);

    $this->actingAs($admin);

    Livewire::test(ProductsModeration::class)
        ->set('statusFilter', '')
        ->set('rejectingId', $product->id)
        ->set('rejectionReason', 'No image attached to the listing.')
        ->call('confirmReject')
        ->assertHasNoErrors();

    expect($product->fresh()->status)->toBe('rejected')
        ->and($product->fresh()->rejection_reason)->toBe('No image attached to the listing.');

    Mail::assertQueued(ProductRejectedMail::class);
});

it('increments and decrements the product page quantity within stock limits', function () {
    [$user, $seller] = makeApprovedSeller();
    $product = makeActiveProduct($seller, 95, 3);

    Livewire::test(ProductShow::class, ['product' => $product->fresh()])
        ->assertSet('quantity', 1)
        ->call('incrementQuantity')
        ->assertSet('quantity', 2)
        ->call('incrementQuantity')
        ->assertSet('quantity', 3)
        ->call('incrementQuantity')
        ->assertSet('quantity', 3)
        ->call('decrementQuantity')
        ->assertSet('quantity', 2)
        ->call('decrementQuantity')
        ->call('decrementQuantity')
        ->assertSet('quantity', 1)
        ->call('decrementQuantity')
        ->assertSet('quantity', 1);
});
