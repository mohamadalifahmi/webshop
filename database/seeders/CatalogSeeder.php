<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ShippingRate;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'icon' => '📱'],
            ['name' => 'Fashion', 'slug' => 'fashion', 'icon' => '👕'],
            ['name' => 'Home & Kitchen', 'slug' => 'home-kitchen', 'icon' => '🏠'],
            ['name' => 'Beauty & Care', 'slug' => 'beauty-care', 'icon' => '💄'],
            ['name' => 'Sports', 'slug' => 'sports', 'icon' => '⚽'],
            ['name' => 'Groceries', 'slug' => 'groceries', 'icon' => '🫒'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }

        $rates = [
            'Beirut' => 5,
            'Mount Lebanon' => 6,
            'North Lebanon' => 7,
            'South Lebanon' => 7,
            'Bekaa' => 7.5,
            'Nabatieh' => 8,
            'Akkar' => 8.5,
        ];

        foreach ($rates as $governorate => $fee) {
            ShippingRate::updateOrCreate(
                ['governorate' => $governorate],
                ['fee' => $fee, 'is_active' => true],
            );
        }
    }
}
