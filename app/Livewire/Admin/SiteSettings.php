<?php

namespace App\Livewire\Admin;

use App\Models\ShippingRate;
use App\Services\SettingsService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class SiteSettings extends Component
{
    public string $globalCommissionRate = '10';

    public string $lbpExchangeRate = '89500';

    public string $minPayout = '50';

    public string $shipDeadlineHours = '48';

    public string $holdDaysAfterDelivery = '14';

    public string $defaultShippingFee = '0';

    /** @var array<int, array{id: int, governorate: string, fee: string}> */
    public array $rates = [];

    public function mount(): void
    {
        $this->globalCommissionRate = SettingsService::get('global_commission_rate', '10');
        $this->lbpExchangeRate = SettingsService::get('lbp_exchange_rate', '89500');
        $this->minPayout = SettingsService::get('min_payout', '50');
        $this->shipDeadlineHours = SettingsService::get('ship_deadline_hours', '48');
        $this->holdDaysAfterDelivery = SettingsService::get('hold_days_after_delivery', '14');
        $this->defaultShippingFee = SettingsService::get('default_shipping_fee', '0');

        $this->rates = ShippingRate::orderBy('governorate')
            ->get()
            ->map(fn ($r) => ['id' => $r->id, 'governorate' => $r->governorate, 'fee' => (string) $r->fee])
            ->all();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'globalCommissionRate' => 'required|numeric|min:0|max:100',
            'lbpExchangeRate' => 'required|numeric|min:1',
            'minPayout' => 'required|numeric|min:0',
            'shipDeadlineHours' => 'required|integer|min:1',
            'holdDaysAfterDelivery' => 'required|integer|min:0',
            'defaultShippingFee' => 'required|numeric|min:0',
            'rates.*.fee' => 'required|numeric|min:0',
            'rates.*.governorate' => 'required|string|max:60',
        ]);

        DB::transaction(function () use ($validated) {
            SettingsService::set('global_commission_rate', number_format((float) $validated['globalCommissionRate'], 2, '.', ''));
            SettingsService::set('lbp_exchange_rate', number_format((float) $validated['lbpExchangeRate'], 2, '.', ''));
            SettingsService::set('min_payout', number_format((float) $validated['minPayout'], 2, '.', ''));
            SettingsService::set('ship_deadline_hours', (string) (int) $validated['shipDeadlineHours']);
            SettingsService::set('hold_days_after_delivery', (string) (int) $validated['holdDaysAfterDelivery']);
            SettingsService::set('default_shipping_fee', number_format((float) $validated['defaultShippingFee'], 2, '.', ''));

            foreach ($this->rates as $rate) {
                ShippingRate::whereKey($rate['id'])->update([
                    'governorate' => trim($rate['governorate']),
                    'fee' => number_format((float) $rate['fee'], 2, '.', ''),
                ]);
            }
        });

        session()->flash('success', 'Site settings saved.');
    }

    public function render()
    {
        return view('livewire.admin.site-settings');
    }
}
