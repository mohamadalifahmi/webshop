<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Services\PaymentService;
use DomainException;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class OrdersMonitor extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public ?int $viewingId = null;

    public function viewProof(int $orderId): ?string
    {
        $order = Order::findOrFail($orderId);

        return $order->payment_proof_path ? Storage::disk('public')->url($order->payment_proof_path) : null;
    }

    public function approvePayment(int $orderId): void
    {
        $order = Order::findOrFail($orderId);

        try {
            PaymentService::markPaid($order, auth()->user());
            session()->flash('success', "Order {$order->order_number} marked PAID. Earnings distribution triggered.");
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.orders-monitor', [
            'orders' => Order::query()
                ->with(['user:id,name,email', 'items.seller:id,store_name'])
                ->withCount('items')
                ->when($this->statusFilter !== '', fn ($q) => $q->where('payment_status', $this->statusFilter))
                ->latest()
                ->paginate(10),
        ]);
    }
}
