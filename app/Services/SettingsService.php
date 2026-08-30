<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = Cache::rememberForever('soukelkom.settings', fn () => Setting::pluck('value', 'key')->all());

        return $settings[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('soukelkom.settings');
    }

    public static function globalCommissionRate(): float
    {
        return (float) self::get('global_commission_rate', '10');
    }

    public static function lbpExchangeRate(): float
    {
        return (float) self::get('lbp_exchange_rate', '89500');
    }

    public static function minPayout(): float
    {
        return (float) self::get('min_payout', '50');
    }

    public static function shipDeadlineHours(): int
    {
        return (int) self::get('ship_deadline_hours', '48');
    }

    public static function holdDaysAfterDelivery(): int
    {
        return (int) self::get('hold_days_after_delivery', '14');
    }
}
