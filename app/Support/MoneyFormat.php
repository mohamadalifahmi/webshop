<?php

namespace App\Support;

use App\Services\SettingsService;

class MoneyFormat
{
    public static function usd(float|string|null $amount): string
    {
        return '$'.number_format((float) $amount, 2);
    }

    public static function lbp(float|string|null $amount): string
    {
        return number_format((int) round(((float) $amount) * SettingsService::lbpExchangeRate())).' LBP';
    }
}
