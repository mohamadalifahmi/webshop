<?php

namespace Database\Seeders;

use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'global_commission_rate' => '10',
            'lbp_exchange_rate' => '89500',
            'min_payout' => '50',
            'ship_deadline_hours' => '48',
            'hold_days_after_delivery' => '14',
            'default_shipping_fee' => '0',
        ];

        foreach ($defaults as $key => $value) {
            if (SettingsService::get($key) === null) {
                SettingsService::set($key, $value);
            }
        }
    }
}
