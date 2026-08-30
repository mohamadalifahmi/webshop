<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $sellersData = [
            [
                'user' => ['name' => 'Ahmed Electronics', 'email' => 'ahmed@soukelkom.test'],
                'store' => ['store_name' => 'Ahmed Electronics', 'governorate' => 'Beirut', 'description' => 'Latest smartphones, laptops and gadgets at the best local prices.'],
                'products' => [
                    ['name' => 'iPhone 15 Pro Max 256GB', 'sku' => 'IPH15PM-256', 'price' => 1199, 'stock' => 8, 'cat' => 'electronics', 'desc' => "Titanium. So strong. So light. So Pro.\n\nA17 Pro chip with 6-core GPU, 48MP main camera, USB-C. Official US spec, brand new sealed."],
                    ['name' => 'iPhone 15 Silicone Case', 'sku' => 'IPH15CASE-SIL', 'price' => 12, 'stock' => 40, 'cat' => 'electronics', 'desc' => 'Soft-touch silicone case for iPhone 15. Precise fit, wireless charging compatible.'],
                    ['name' => 'Anker PowerCore 20000mAh', 'sku' => 'ANK-PC20K', 'price' => 39, 'stock' => 25, 'cat' => 'electronics', 'desc' => 'High-speed portable charger with 18W PowerIQ. Perfect for Lebanon power cuts.'],
                    ['name' => 'Samsung Galaxy Watch 6', 'sku' => 'SM-GW6-44', 'price' => 249, 'stock' => 6, 'cat' => 'electronics', 'desc' => 'Sleep tracking, body composition, and advanced fitness coaching on your wrist.'],
                ],
            ],
            [
                'user' => ['name' => 'Maya Fashion', 'email' => 'maya@soukelkom.test'],
                'store' => ['store_name' => "Maya's Boutique", 'governorate' => 'Mount Lebanon', 'description' => 'Curated fashion from Beirut — dresses, sneakers and accessories for every occasion.'],
                'products' => [
                    ['name' => 'Nike Air Force 1 White', 'sku' => 'NK-AF1-WHT', 'price' => 95, 'stock' => 14, 'cat' => 'fashion', 'desc' => "The radiance lives on in the Nike Air Force 1 '07.\n\nCrisp leather, bold color, classic look that never goes out of style."],
                    ['name' => 'Summer Floral Dress', 'sku' => 'MY-DRESS-FLR', 'price' => 45, 'stock' => 20, 'cat' => 'fashion', 'desc' => 'Lightweight flowing floral dress. Breathable fabric perfect for Lebanese summers.'],
                    ['name' => 'Leather Crossbody Bag', 'sku' => 'MY-BAG-LTH', 'price' => 60, 'stock' => 9, 'cat' => 'fashion', 'desc' => 'Genuine soft leather crossbody bag with adjustable strap and gold hardware.'],
                    ['name' => 'Adidas Running T-Shirt', 'sku' => 'AD-TS-RUN', 'price' => 28, 'stock' => 30, 'cat' => 'sports', 'desc' => 'AEROREADY moisture-managing fabric keeps you dry through the toughest runs.'],
                ],
            ],
            [
                'user' => ['name' => 'Kareem Home', 'email' => 'kareem@soukelkom.test'],
                'store' => ['store_name' => 'Kareem Home & Kitchen', 'governorate' => 'North Lebanon', 'description' => 'Everything for your home — from olive oil to coffee machines.'],
                'products' => [
                    ['name' => 'Extra Virgin Olive Oil 1L (Tripoli)', 'sku' => 'KR-EVOO-1L', 'price' => 14, 'stock' => 50, 'cat' => 'groceries', 'desc' => "Cold-pressed first harvest extra virgin olive oil from northern Lebanon groves.\n\nAcidity < 0.5%. Bottled at source."],
                    ['name' => 'Espresso Machine Deluxe', 'sku' => 'KR-ESP-DLX', 'price' => 189, 'stock' => 4, 'cat' => 'home-kitchen', 'desc' => '15-bar Italian pump espresso machine with steam wand for perfect cappuccino.'],
                    ['name' => 'Argan Hair Oil Set', 'sku' => 'KR-ARGAN-3', 'price' => 32, 'stock' => 0, 'cat' => 'beauty-care', 'desc' => 'Set of 3 pure Moroccan argan oil treatments for damaged hair repair.'],
                ],
            ],
        ];

        $sellerRole = Role::findByName('seller', 'web');
        $categoryIds = Category::pluck('id', 'slug');

        foreach ($sellersData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['user']['email']],
                ['name' => $data['user']['name'], 'password' => 'password'],
            );
            $user->assignRole($sellerRole);

            DB::table('users')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);

            $storeName = $data['store']['store_name'];

            $seller = Seller::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'store_name' => $storeName,
                    'slug' => Str::slug($storeName).'-'.Str::lower(Str::random(4)),
                    'description' => $data['store']['description'],
                    'phone' => '+961 70 '.random_int(100000, 999999),
                    'governorate' => $data['store']['governorate'],
                    'status' => 'approved',
                    'approved_at' => now(),
                ],
            );

            foreach ($data['products'] as $productData) {
                Product::updateOrCreate(
                    ['sku' => $productData['sku']],
                    [
                        'seller_id' => $seller->id,
                        'category_id' => $categoryIds[$productData['cat']],
                        'name' => $productData['name'],
                        'slug' => Str::slug($productData['name']).'-'.Str::lower(Str::random(4)),
                        'description' => $productData['desc'],
                        'price' => number_format((float) $productData['price'], 2, '.', ''),
                        'stock' => $productData['stock'],
                        'status' => 'active',
                        'published_at' => now()->subDays(random_int(0, 10)),
                    ],
                );
            }

            Seller::whereKey($seller->id)->first();
        }
    }
}
