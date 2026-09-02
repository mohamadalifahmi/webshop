<div>
    <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-6">Platform Overview</h1>

    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <x-stat-card title="Total Platform Revenue" value="${{ number_format($totalRevenue, 2) }}" sub="All paid orders" icon="revenue" />
        <x-stat-card title="Commission This Month" value="${{ number_format($commissionThisMonth, 2) }}" sub="Lifetime: ${{ number_format($totalCommission, 2) }}" icon="commission" />
        <x-stat-card title="Active Sellers" value="{{ $activeSellers }}" sub="{{ $pendingSellers }} awaiting approval" icon="store" />
        <x-stat-card title="Pending Payouts" value="{{ $pendingPayoutsCount }}" sub="${{ number_format($pendingPayoutsTotal, 2) }} to transfer" icon="payout" />
    </section>

    <section class="mt-4 grid grid-cols-2 gap-4">
        <a href="{{ route('admin.products', ['statusFilter' => 'pending']) }}" wire:navigate class="rounded-xl border border-stargold-200 bg-stargold-50 p-4 hover:border-stargold-400 transition">
            <p class="text-sm font-bold text-stargold-800">{{ $pendingProducts }} products waiting for approval</p>
        </a>
        <a href="{{ route('admin.sellers', ['statusFilter' => 'pending']) }}" wire:navigate class="rounded-xl border border-blue-200 bg-blue-50 p-4 hover:border-blue-400 transition">
            <p class="text-sm font-bold text-blue-800">{{ $pendingSellers }} seller applications pending</p>
        </a>
    </section>

    <section class="mt-8 overflow-hidden rounded-2xl bg-white border border-gray-200">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h2 class="font-bold text-gray-900">Recent Orders</h2>
            <a href="{{ route('admin.orders') }}" wire:navigate class="text-xs font-semibold text-stargold-600 hover:text-stargold-700">View all →</a>
        </div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-400">
                <tr><th class="px-5 py-3">Order</th><th class="px-5 py-3">Buyer</th><th class="px-5 py-3 hidden sm:table-cell">Items</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Total</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($recentOrders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3.5 font-bold">{{ $order->order_number }}</td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $order->user?->name }}</td>
                        <td class="px-5 py-3.5 hidden sm:table-cell text-gray-500">{{ $order->items_count }}</td>
                        <td class="px-5 py-3.5"><x-status-badge :status="$order->payment_status" /></td>
                        <td class="px-5 py-3.5 text-right font-black">${{ number_format((float) $order->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state title="No orders yet" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
