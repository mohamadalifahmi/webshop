<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-black text-gray-900 mb-6">My Orders</h1>

    @php($orders = auth()->user()->orders()->withCount('items')->latest()->paginate(10))

    @if ($orders->isEmpty())
        <div class="rounded-2xl bg-white border border-gray-200">
            <x-empty-state title="No orders yet" description="When you buy something it will show up here.">
                <x-slot:action><a href="{{ route('home') }}" wire:navigate><x-ui-button type="primary">Browse Products</x-ui-button></a></x-slot:action>
            </x-empty-state>
        </div>
    @else
        <div class="rounded-2xl bg-white border border-gray-200 divide-y divide-gray-100">
            @foreach ($orders as $order)
                <a href="{{ route('account.orders.show', $order->order_number) }}" wire:navigate
                   class="flex flex-wrap items-center gap-3 p-5 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-40">
                        <p class="font-bold text-gray-900">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }} · {{ $order->items_count }} items</p>
                    </div>
                    <x-status-badge :status="$order->status" />
                    <x-status-badge :status="$order->payment_status" />
                    <span class="font-black text-gray-900">${{ number_format((float) $order->total, 2) }}</span>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>
