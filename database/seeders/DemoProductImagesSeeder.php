<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoProductImagesSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            'IPH15PM-256' => 'demo-products/iphone15promax.jpg',
            'IPH15CASE-SIL' => 'demo-products/iphone15case.jpg',
            'ANK-PC20K' => 'demo-products/anker20000.jpg',
            'SM-GW6-44' => 'demo-products/galaxywatch6.jpg',
            'NK-AF1-WHT' => 'demo-products/af1white.jpg',
            'MY-DRESS-FLR' => 'demo-products/floraldress.jpg',
            'MY-BAG-LTH' => 'demo-products/leatherbag.jpg',
            'AD-TS-RUN' => 'demo-products/adidastshirt.jpg',
            'KR-EVOO-1L' => 'demo-products/oliveoil.jpg',
            'KR-ESP-DLX' => 'demo-products/espressomachine.jpg',
            'KR-ARGAN-3' => 'demo-products/arganoil.jpg',
        ];

        foreach ($images as $sku => $file) {
            if (! Storage::disk('public')->exists($file)) {
                continue;
            }

            $product = Product::where('sku', $sku)->first();

            if ($product && $product->getMedia('images')->isEmpty()) {
                $product->addMediaFromDisk($file, 'public')->toMediaCollection('images');
            }
        }
    }
}