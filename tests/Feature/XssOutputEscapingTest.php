<?php

use App\Livewire\Admin\ProductsModeration;
use App\Livewire\Seller\ProductsManager;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CatalogSeeder;

/*
|--------------------------------------------------------------------------
| SECURITY SUITE — XSS output escaping (Blade {{ }} is the shield)
|--------------------------------------------------------------------------
*/

it('escapes script payloads in product names instead of executing them', function () {
    $this->seed(CatalogSeeder::class);

    [$sellerUser, $seller] = makeApprovedSeller();
    $admin = makeAdmin();

    $payload = '<script>alert("pwned")</script>Safe';

    $category = Category::firstOrCreate(['slug' => 'xss'], ['name' => 'XSS']);

    // Seller creates a product with an XSS payload in the name
    $this->actingAs($sellerUser);

    Livewire::test(ProductsManager::class)
        ->set('name', $payload)
        ->set('description', 'desc')
        ->set('price', '10')
        ->set('stock', '5')
        ->set('categoryId', $category->id)
        ->call('save');

    // Admin approves it so it lands on the public catalog
    $this->actingAs($admin);
    $product = Product::where('name', $payload)->firstOrFail();
    Livewire::test(ProductsModeration::class)
        ->call('approve', $product->id);

    $response = $this->get(route('home'));

    // The raw script tag must NOT appear
    $response->assertDontSee('<script>alert("pwned")</script>', false);

    // The escaped entity form must appear
    $response->assertSee('&lt;script&gt;', false);
});

it('keeps stored user names escaped on the order detail page', function () {
    $user = User::factory()->create(['name' => '<b>Hax</b>']);

    $this->actingAs($user)
        ->get(route('account.dashboard'))
        ->assertOk()
        ->assertDontSee('<b>Hax</b>', false);
});
