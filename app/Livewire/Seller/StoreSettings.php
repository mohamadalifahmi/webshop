<?php

namespace App\Livewire\Seller;

use App\Services\ShippingService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.seller')]
class StoreSettings extends Component
{
    public string $storeName = '';

    public string $description = '';

    public string $phone = '';

    public string $governorate = '';

    public function mount(): void
    {
        $seller = auth()->user()->seller;
        $this->storeName = $seller->store_name;
        $this->description = (string) $seller->description;
        $this->phone = (string) $seller->phone;
        $this->governorate = (string) $seller->governorate;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'storeName' => 'required|string|max:150',
            'description' => 'nullable|string|max:2000',
            'phone' => 'nullable|string|max:30',
            'governorate' => ['nullable', 'in:'.implode(',', ShippingService::governorates())],
        ]);

        auth()->user()->seller->update([
            'store_name' => $validated['storeName'],
            'description' => $validated['description'] ?: null,
            'phone' => $validated['phone'] ?: null,
            'governorate' => $validated['governorate'] ?: null,
        ]);

        session()->flash('success', 'Store settings saved.');
    }

    public function render()
    {
        return view('livewire.seller.store-settings', [
            'governorates' => ShippingService::governorates(),
        ]);
    }
}
