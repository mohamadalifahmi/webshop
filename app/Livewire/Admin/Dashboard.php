<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Payout;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function render()
    {
        $monthStart = Carbon::now()->startOfMonth();

        $totalRevenue = (float) Order::where('payment_status', 'paid')->sum('total');
        $totalCommission = (float) Transaction::where('type', Transaction::TYPE_COMMISSION)->sum('amount');
        $commissionThisMonth = (float) Transaction::where('type', Transaction::TYPE_COMMISSION)
            ->where('created_at', '>=', $monthStart)
            ->sum('amount');
        $activeSellers = Seller::approved()->count();
        $pendingPayouts = Payout::where('status', 'pending')->get();
        $pendingProducts = Product::where('status', 'pending')->count();
        $pendingSellers = Seller::where('status', 'pending')->count();

        return view('livewire.admin.dashboard', [
            'totalRevenue' => $totalRevenue,
            'totalCommission' => $totalCommission,
            'commissionThisMonth' => $commissionThisMonth,
            'activeSellers' => $activeSellers,
            'pendingPayoutsCount' => $pendingPayouts->count(),
            'pendingPayoutsTotal' => (float) $pendingPayouts->sum('amount'),
            'pendingProducts' => $pendingProducts,
            'pendingSellers' => $pendingSellers,
            'recentOrders' => Order::with('user:id,name')->withCount('items')->latest()->take(8)->get(),
        ]);
    }
}
