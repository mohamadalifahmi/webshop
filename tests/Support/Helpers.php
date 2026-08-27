<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

if (! function_exists('makeApprovedSeller')) {
    /**
     * Create a user bound to an approved seller account.
     */
    function makeApprovedSeller(string $name = 'Test Seller', ?float $commissionOverride = null): array
    {
        $user = User::factory()->create(['name' => $name]);
        $user->assignRole(Role::findOrCreate('seller', 'web'));

        $seller = Seller::create([
            'user_id' => $user->id,
            'store_name' => $name.' Store',
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'status' => 'approved',
            'commission_override' => $commissionOverride,
            'approved_at' => now(),
        ]);

        return [$user, $seller];
    }
}

if (! function_exists('makeAdmin')) {
    function makeAdmin(): User
    {
        $admin = User::factory()->create(['email' => 'god@soukelkom.test']);
        $admin->assignRole(Role::findOrCreate('admin', 'web'));

        return $admin;
    }
}

if (! function_exists('makeActiveProduct')) {
    function makeActiveProduct(Seller $seller, float $price = 50, int $stock = 10, string $name = 'Test Widget'): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'test'],
            ['name' => 'Test Category'],
        );

        return Product::create([
            'seller_id' => $seller->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'sku' => strtoupper(Str::random(10)),
            'description' => 'A test product description.',
            'price' => number_format($price, 2, '.', ''),
            'stock' => $stock,
            'status' => 'active',
            'published_at' => now(),
        ]);
    }
}
