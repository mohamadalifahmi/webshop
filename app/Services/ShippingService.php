<?php

namespace App\Services;

use App\Models\ShippingRate;

class ShippingService
{
    public static function feeFor(string $governorate): float
    {
        $rate = ShippingRate::where('governorate', $governorate)->where('is_active', true)->first();

        if ($rate) {
            return (float) $rate->fee;
        }

        return (float) SettingsService::get('default_shipping_fee', '0');
    }

    public static function governorates(): array
    {
        return ShippingRate::where('is_active', true)->orderBy('governorate')->pluck('governorate')->all();
    }
}
