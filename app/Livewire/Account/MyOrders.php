<?php

namespace App\Livewire\Account;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class MyOrders extends Component
{
    public function render()
    {
        return view('livewire.account.my-orders');
    }
}
