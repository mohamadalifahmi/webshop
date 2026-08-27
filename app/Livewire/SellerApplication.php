<?php

namespace App\Livewire;

use App\Services\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class SellerApplication extends Component
{
    public string $storeName = '';

    public string $description = '';

    public string $phone = '';

    public string $governorate = '';

    public function mount(): void
    {
        $existing = auth()->user()->seller;

        if ($existing) {
            $this->storeName = $existing->store_name;
            $this->description = (string) $existing->description;
            $this->phone = (string) $existing->phone;
            $this->governorate = (string) $existing->governorate;
        }
    }

    public function submit(): void
    {
        $validated = $this->validate([
            'storeName' => 'required|string|max:150',
            'description' => 'nullable|string|max:2000',
            'phone' => 'required|string|max:30',
            'governorate' => ['required', Rule::in(ShippingService::governorates())],
        ]);

        $user = auth()->user();

        DB::transaction(function () use ($validated, $user) {
            if (! $user->hasRole('seller')) {
                $user->assignRole('seller');
            }

            $seller = $user->seller()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'store_name' => $validated['storeName'],
                    'slug' => Str::slug($validated['storeName']).'-'.Str::lower(Str::random(5)),
                    'description' => $validated['description'] ?: null,
                    'phone' => $validated['phone'],
                    'governorate' => $validated['governorate'],
                    'status' => 'pending',
                ],
            );

            if ($seller->status === 'rejected') {
                $seller->update([
                    'status' => 'pending',
                    'rejection_reason' => null,
                    'store_name' => $validated['storeName'],
                    'description' => $validated['description'] ?: null,
                    'phone' => $validated['phone'],
                    'governorate' => $validated['governorate'],
                ]);
            }
        });

        session()->flash('success', 'Application submitted! We will review it shortly and email you.');
    }

    public function render()
    {
        return view('livewire.seller-application', [
            'seller' => auth()->user()->seller,
            'governorates' => ShippingService::governorates(),
        ]);
    }
}
