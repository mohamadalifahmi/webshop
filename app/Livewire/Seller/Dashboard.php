<?php

namespace App\Livewire\Seller;

use App\Models\OrderItem;
use App\Models\Transaction;
use App\Services\PayoutService;
use App\Services\SettingsService;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.seller')]
class Dashboard extends Component
{
    public function render()
    {
        $seller = auth()->user()->seller;

        $monthStart = Carbon::now()->startOfMonth();

        $monthSales = (float) OrderItem::where('seller_id', $seller->id)
            ->whereHas('order', fn ($q) => $q->where('payment_status', 'paid'))
            ->where('created_at', '>=', $monthStart)
            ->sum('subtotal');

        $totalCommission = (float) Transaction::where('seller_id', $seller->id)
            ->where('type', Transaction::TYPE_COMMISSION)
            ->sum('amount');

        $awaitingShipment = OrderItem::where('seller_id', $seller->id)
            ->where('shipment_status', 'awaiting')
            ->whereHas('order', fn ($q) => $q->where('payment_status', 'paid'))
            ->count();

        return view('livewire.seller.dashboard', [
            'seller' => $seller,
            'balance' => $seller->balance,
            'available' => PayoutService::availableBalance($seller),
            'onHold' => PayoutService::onHold($seller),
            'minPayout' => SettingsService::minPayout(),
            'monthSales' => $monthSales,
            'totalCommission' => $totalCommission,
            'awaitingShipment' => $awaitingShipment,
        ]);
    }
}
