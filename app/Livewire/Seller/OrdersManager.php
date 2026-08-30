<?php

namespace App\Livewire\Seller;

use App\Models\OrderItem;
use App\Services\OrderService;
use App\Services\SettingsService;
use DomainException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.seller')]
class OrdersManager extends Component
{
    public string $shipmentFilter = '';

    public bool $showShipModal = false;

    public ?int $shippingItemId = null;

    public string $trackingNumber = '';

    public function openShipModal(int $itemId): void
    {
        $this->shippingItemId = $itemId;
        $this->trackingNumber = '';
        $this->showShipModal = true;
    }

    public function confirmShip(): void
    {
        $validated = $this->validate([
            'trackingNumber' => 'required|string|max:80',
            'shippingItemId' => 'required|integer',
        ]);

        $item = OrderItem::where('seller_id', auth()->user()->seller->id)
            ->whereHas('order', fn ($q) => $q->where('payment_status', 'paid'))
            ->findOrFail($validated['shippingItemId']);

        try {
            OrderService::markShipped($item, trim($this->trackingNumber));
            session()->flash('success', "Item shipped. Buyer notified with tracking [{$item->tracking_number}].");
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->showShipModal = false;
    }

    public function confirmDelivered(int $itemId): void
    {
        $item = OrderItem::where('seller_id', auth()->user()->seller->id)
            ->where('shipment_status', 'shipped')
            ->findOrFail($itemId);

        OrderService::markDelivered($item);
        session()->flash('success', 'Marked as delivered. Earnings release in '.SettingsService::holdDaysAfterDelivery().' days.');
    }

    public function render()
    {
        return view('livewire.seller.orders-manager', [
            'items' => OrderItem::query()
                ->select('order_items.*')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('order_items.seller_id', auth()->user()->seller->id)
                ->whereIn('orders.payment_status', ['paid'])
                ->when($this->shipmentFilter !== '', fn ($q) => $q->where('order_items.shipment_status', $this->shipmentFilter))
                ->with(['order.user:id,name,email', 'seller:id,store_name'])
                ->orderByDesc('orders.paid_at')
                ->paginate(10),
        ]);
    }
}
