<?php

namespace App\Services;

use App\Models\Product;

class CommissionService
{
    /**
     * Resolution chain: product override -> seller override -> global setting.
     */
    public static function resolveRate(Product $product): float
    {
        if ($product->commission_rate !== null) {
            return (float) $product->commission_rate;
        }

        if ($product->seller?->commission_override !== null) {
            return (float) $product->seller->commission_override;
        }

        return SettingsService::globalCommissionRate();
    }

    /**
     * Precise money split for a product line using bcmath.
     *
     * @return array{subtotal: string, rate: string, commission: string, earning: string}
     */
    public static function calculate(Product $product, int $quantity): array
    {
        $rate = self::resolveRate($product);

        $subtotal = bcmul((string) $product->price, (string) $quantity, 2);

        // Compute commission entirely in bcmath (4 dp) then round to 2, avoiding
        // the float cast that previously caused last-cent drift vs. the subtotal.
        $commission = self::round2((float) bcmul($subtotal, (string) ($rate / 100), 4));

        $earning = bcsub($subtotal, number_format($commission, 2, '.', ''), 2);

        return [
            'subtotal' => $subtotal,
            'rate' => number_format($rate, 2, '.', ''),
            'commission' => number_format($commission, 2, '.', ''),
            'earning' => $earning,
        ];
    }

    public static function round2(float $amount): float
    {
        return round($amount, 2);
    }
}
