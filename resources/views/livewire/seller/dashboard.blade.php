<div>
    <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-1">Welcome back, {{ auth()->user()->name }} 👋</h1>
    <p class="text-sm text-gray-400 mb-6">{{ $seller->store_name }} · Commission {{ $seller->commission_override ?? \App\Services\SettingsService::globalCommissionRate() }}%</p>

    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <x-stat-card title="Withdrawable Balance" value="${{ number_format($available, 2) }}" sub="Min payout ${{ number_format($minPayout, 2) }}"
            icon="💵" />
        <x-stat-card title="On Hold" value="${{ number_format($onHold, 2) }}" sub="Releases 14 days after delivery"
            icon="⏳" />
        <x-stat-card title="This Month Sales" value="${{ number_format($monthSales, 2) }}" sub="Paid orders subtotal"
            icon="📈" />
        <x-stat-card title="Total Commission Paid" value="${{ number_format($totalCommission, 2) }}" sub="Platform fees lifetime"
            icon="🏛️" />
    </section>

    @php($grossBalance = (float) $balance)
    <p class="mt-3 text-xs text-gray-400">Gross credited balance: <span class="font-semibold text-gray-600">${{ number_format($grossBalance, 2) }}</span> — withdrawable = gross − pending payouts − on-hold earnings.</p>

    <section class="mt-8 grid gap-4 md:grid-cols-2">
        <a href="{{ route('seller.products') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-amber-300 transition group">
            <p class="text-2xl">📦</p>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-amber-700">My Products</p>
            <p class="text-sm text-gray-400 mt-0.5">List new items, track approvals.</p>
        </a>
        <a href="{{ route('seller.orders') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-amber-300 transition group">
            <p class="text-2xl">🚚</p>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-amber-700">Orders to Ship</p>
            <p class="text-sm text-gray-400 mt-0.5">@if ($awaitingShipment > 0) <span class="font-bold text-red-500">{{ $awaitingShipment }} waiting!</span> @else All caught up. @endif</p>
        </a>
        <a href="{{ route('seller.payouts') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-amber-300 transition group">
            <p class="text-2xl">💵</p>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-amber-700">Request Payout</p>
            <p class="text-sm text-gray-400 mt-0.5">Withdraw your available balance.</p>
        </a>
        <a href="{{ route('seller.settings') }}" wire:navigate class="rounded-2xl bg-white border border-gray-200 p-5 hover:border-amber-300 transition group">
            <p class="text-2xl">⚙️</p>
            <p class="mt-2 font-bold text-gray-900 group-hover:text-amber-700">Store Settings</p>
            <p class="text-sm text-gray-400 mt-0.5">Name, description, contact.</p>
        </a>
    </section>
</div>
