<?php

namespace App\Livewire\Account;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        $ordersQuery = $user->orders()->where('payment_status', 'paid');

        $totalOrders = $ordersQuery->count();
        $totalSpent = (float) (clone $ordersQuery)->sum('total');
        $monthSpent = (float) (clone $ordersQuery)->where('paid_at', '>=', Carbon::now()->startOfMonth())->sum('total');
        $onTheWay = Order::where('user_id', $user->id)
            ->whereIn('status', ['paid', 'partially_shipped', 'shipped'])
            ->count();

        return view('livewire.account.dashboard', [
            'totalOrders' => $totalOrders,
            'totalSpent' => $totalSpent,
            'monthSpent' => $monthSpent,
            'onTheWay' => $onTheWay,
            'recentOrders' => $user->orders()->withCount('items')->latest()->take(5)->get(),
        ]);
    }
}
