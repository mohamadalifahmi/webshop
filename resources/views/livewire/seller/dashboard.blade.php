<div>
    <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-1">Welcome back, {{ auth()->user()->name }}</h1>
    <p class="text-sm text-gray-400 mb-6">{{ $seller->store_name }} · Commission {{ $seller->commission_override ?? \App\Services\SettingsService::globalCommissionRate() }}%</p>

    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <x-stat-card title="Withdrawable Balance" value="${{ number_format($available, 2) }}" sub="Min payout ${{ number_format($minPayout, 2) }}"
            icon="balance" />
        <x-stat-card title="On Hold" value="${{ number_format($onHold, 2) }}" sub="Releases 14 days after delivery"
            icon="calendar" />
        <x-stat-card title="This Month Sales" value="${{ number_format($monthSales, 2) }}" sub="Paid orders subtotal"
            icon="growth" />
        <x-stat-card title="Total Commission Paid" value="${{ number_format($totalCommission, 2) }}" sub="Platform fees lifetime"
            icon="commission" />
    </section>

    @php($grossBalance = (float) $balance)
    <p class="mt-3 text-xs text-gray-400">Gross credited balance: <span class="font-semibold text-gray-600">${{ number_format($grossBalance, 2) }}</span> — withdrawable = gross − pending payouts − on-hold earnings.</p>

    <section class="mt-8 grid gap-4 md:grid-cols-2">
        <a href="{{ route('seller.products') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-stargold-300 transition group">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-500"><x-icon-package class="h-5 w-5" /></span>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-stargold-700">My Products</p>
            <p class="text-sm text-gray-400 mt-0.5">List new items, track approvals.</p>
        </a>
        <a href="{{ route('seller.orders') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-stargold-300 transition group">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
            </span>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-stargold-700">Orders to Ship</p>
            <p class="text-sm text-gray-400 mt-0.5">@if ($awaitingShipment > 0) <span class="font-bold text-red-500">{{ $awaitingShipment }} waiting!</span> @else All caught up. @endif</p>
        </a>
        <a href="{{ route('seller.payouts') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-stargold-300 transition group">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
            </span>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-stargold-700">Request Payout</p>
            <p class="text-sm text-gray-400 mt-0.5">Withdraw your available balance.</p>
        </a>
        <a href="{{ route('seller.settings') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-stargold-300 transition group">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-stargold-700">Store Settings</p>
            <p class="text-sm text-gray-400 mt-0.5">Name, description, contact.</p>
        </a>
    </section>
</div>