<?php

namespace App\Livewire\Seller;

use App\Models\Payout;
use App\Services\PayoutService;
use App\Services\SettingsService;
use DomainException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.seller')]
class PayoutsManager extends Component
{
    public string $amount = '';

    public string $bankName = '';

    public string $iban = '';

    public function requestPayout(): void
    {
        $validated = $this->validate([
            'amount' => 'required|numeric|min:1',
            'bankName' => 'required|string|max:120',
            'iban' => 'required|string|max:34',
        ]);

        $seller = auth()->user()->seller;

        try {
            PayoutService::request($seller, (float) $validated['amount'], [
                'bank_name' => $validated['bankName'],
                'iban' => $validated['iban'],
            ]);

            session()->flash('success', "Payout request of {$validated['amount']} submitted. Waiting for admin approval.");
            $this->reset(['amount', 'bankName', 'iban']);
        } catch (DomainException $e) {
            $this->addError('amount', $e->getMessage());
        }
    }

    public function render()
    {
        $seller = auth()->user()->seller;

        return view('livewire.seller.payouts-manager', [
            'payouts' => Payout::where('seller_id', $seller->id)->latest('requested_at')->paginate(8),
            'available' => PayoutService::availableBalance($seller),
            'minPayout' => SettingsService::minPayout(),
            'hasPending' => Payout::where('seller_id', $seller->id)->where('status', 'pending')->exists(),
        ]);
    }
}
