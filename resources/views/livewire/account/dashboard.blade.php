<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-xl font-black text-gray-900 sm:text-2xl">Hey, {{ auth()->user()->name }} 👋</h1>
            <p class="mt-1 text-sm text-gray-400">Your personal shopping dashboard.</p>
        </div>
        <a href="{{ route('home') }}" wire:navigate><x-ui-button type="primary">Continue Shopping</x-ui-button></a>
    </div>

    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <x-stat-card title="Paid Orders" value="{{ $totalOrders }}" sub="Lifetime purchases" icon="🧾" />
        <x-stat-card title="Total Spent" value="${{ number_format($totalSpent, 2) }}" sub="{{ number_format((int) round($totalSpent * \App\Services\SettingsService::lbpExchangeRate())) }} LBP" icon="💳" />
        <x-stat-card title="This Month" value="${{ number_format($monthSpent, 2) }}" sub="Spent since the 1st" icon="📅" />
        <x-stat-card title="On The Way" value="{{ $onTheWay }}" sub="Being shipped to you" icon="🚚" />
    </section>

    @if (! auth()->user()->seller)
        <section class="mt-6 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 to-white p-5 sm:p-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="font-bold text-gray-900">Got something to sell? 🛍️</p>
                <p class="text-sm text-gray-500 mt-0.5">Open your own store on SOUKELKOM in minutes — approval usually takes less than a day.</p>
            </div>
            <a href="{{ route('become-seller') }}" wire:navigate><x-ui-button type="dark">Become a Seller</x-ui-button></a>
        </section>
    @endif

    <section class="mt-8">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Recent Orders</h2>
            <a href="{{ route('account.orders') }}" wire:navigate class="text-xs font-semibold text-amber-600 hover:text-amber-700">View all →</a>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white border border-gray-200">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-400">
                    <tr><th class="px-5 py-3">Order</th><th class="px-5 py-3 hidden sm:table-cell">Items</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Total</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('account.orders.show', $order->order_number) }}" wire:navigate class="font-bold text-gray-900 hover:text-amber-700">{{ $order->order_number }}</a>
                                <p class="text-[11px] text-gray-400">{{ $order->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="px-5 py-3.5 hidden sm:table-cell text-gray-500">{{ $order->items_count }}</td>
                            <td class="px-5 py-3.5"><x-status-badge :status="$order->status" /></td>
                            <td class="px-5 py-3.5 text-right font-black">${{ number_format((float) $order->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">
                            <x-empty-state title="No orders yet" description="Your first order will show up here.">
                                <x-slot:action><a href="{{ route('home') }}" wire:navigate><x-ui-button type="primary">Browse Products</x-ui-button></a></x-slot:action>
                            </x-empty-state>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-8 grid gap-4 sm:grid-cols-3">
        <a href="{{ route('account.orders') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-amber-300 transition group">
            <p class="text-2xl">📦</p>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-amber-700">All Orders</p>
        </a>
        <a href="{{ route('cart') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-amber-300 transition group">
            <p class="text-2xl">🛒</p>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-amber-700">My Cart</p>
        </a>
        <a href="{{ route('profile') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-amber-300 transition group">
            <p class="text-2xl">⚙️</p>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-amber-700">Account Settings</p>
        </a>
    </section>
</div>
