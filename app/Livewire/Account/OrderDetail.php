<?php

namespace App\Livewire\Account;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class OrderDetail extends Component
{
    public Order $order;

    public function mount(string $number): void
    {
        $this->order = auth()->user()
            ->orders()
            ->where('order_number', $number)
            ->with(['items.seller:id,store_name'])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.account.order-detail');
    }
}
