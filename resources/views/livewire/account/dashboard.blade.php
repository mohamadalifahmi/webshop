<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-xl font-black text-gray-900 sm:text-2xl">Hey, {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-sm text-gray-400">Your personal shopping dashboard.</p>
        </div>
        <a href="{{ route('home') }}" wire:navigate><x-ui-button type="primary">Continue Shopping</x-ui-button></a>
    </div>

    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <x-stat-card title="Paid Orders" value="{{ $totalOrders }}" sub="Lifetime purchases" icon="shopping" />
        <x-stat-card title="Total Spent" value="${{ number_format($totalSpent, 2) }}" sub="{{ number_format((int) round($totalSpent * \App\Services\SettingsService::lbpExchangeRate())) }} LBP" icon="card" />
        <x-stat-card title="This Month" value="${{ number_format($monthSpent, 2) }}" sub="Spent since the 1st" icon="calendar" />
        <x-stat-card title="On The Way" value="{{ $onTheWay }}" sub="Being shipped to you" icon="truck" />
    </section>

    @if (! auth()->user()->seller)
        <section class="mt-6 rounded-2xl border border-stargold-200 bg-gradient-to-r from-stargold-50 to-white p-5 sm:p-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="font-bold text-gray-900">Got something to sell?</p>
                <p class="text-sm text-gray-500 mt-0.5">Open your own store on ASTRAGO MARKET in minutes — approval usually takes less than a day.</p>
            </div>
            <a href="{{ route('become-seller') }}" wire:navigate><x-ui-button type="dark">Become a Seller</x-ui-button></a>
        </section>
    @endif

    <section class="mt-8">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Recent Orders</h2>
            <a href="{{ route('account.orders') }}" wire:navigate class="text-xs font-semibold text-stargold-600 hover:text-stargold-700">View all →</a>
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
                                <a href="{{ route('account.orders.show', $order->order_number) }}" wire:navigate class="font-bold text-gray-900 hover:text-stargold-700">{{ $order->order_number }}</a>
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
        <a href="{{ route('account.orders') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-stargold-300 transition group">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-500"><x-icon-package class="h-5 w-5" /></span>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-stargold-700">All Orders</p>
        </a>
        <a href="{{ route('cart') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-stargold-300 transition group">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
            </span>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-stargold-700">My Cart</p>
        </a>
        <a href="{{ route('profile') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-stargold-300 transition group">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-stargold-700">Account Settings</p>
        </a>
    </section>
</div>
