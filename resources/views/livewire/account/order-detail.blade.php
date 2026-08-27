<div class="max-w-3xl mx-auto">
    <a href="{{ route('account.orders') }}" wire:navigate class="text-sm text-gray-400 hover:text-amber-600">← Back to my orders</a>

    <div class="mt-4 rounded-2xl bg-white border border-gray-200 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-5 sm:p-6">
            <div>
                <h1 class="text-xl font-black text-gray-900">{{ $order->order_number }}</h1>
                <p class="mt-1 text-xs text-gray-400">Placed {{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="flex gap-2">
                <x-status-badge :status="$order->status" />
                <x-status-badge :status="$order->payment_status" />
            </div>
        </div>

        <ul class="divide-y divide-gray-100">
            @foreach ($order->items as $item)
                <li class="p-5 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $item->product_name }} <span class="text-gray-400">× {{ $item->quantity }}</span></p>
                            <p class="mt-0.5 text-xs text-gray-400">Sold by {{ $item->seller?->store_name ?? 'Store closed' }}</p>
                        </div>
                        <span class="font-bold">${{ number_format((float) $item->subtotal, 2) }}</span>
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <x-status-badge :status="$item->shipment_status === 'awaiting' ? 'awaiting' : $item->shipment_status" />
                        @if ($item->tracking_number)
                            <span class="rounded-lg bg-gray-100 px-2.5 py-1 font-mono text-xs font-bold text-gray-700">Tracking: {{ $item->tracking_number }}</span>
                        @endif
                        @if ($item->shipment_status === 'awaiting' && $order->payment_status === 'paid')
                            <span class="text-[11px] text-amber-600">Seller must ship before {{ optional($item->cancel_deadline_at)->format('d M, H:i') }}</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>

        <dl class="space-y-1.5 border-t border-gray-100 bg-gray-50 p-5 sm:p-6 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Subtotal</dt><dd>${{ number_format((float) $order->subtotal, 2) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Shipping ({{ $order->governorate }})</dt><dd>${{ number_format((float) $order->shipping_fee, 2) }}</dd></div>
            <div class="flex justify-between text-base font-black"><dt>Total paid</dt><dd>${{ number_format((float) $order->total, 2) }}</dd></div>
        </dl>
    </div>

    <div class="mt-4 rounded-2xl bg-white border border-gray-200 p-5 sm:p-6 text-sm">
        <h2 class="font-bold text-gray-900 mb-2">Delivery address</h2>
        <p class="text-gray-600">{{ $order->ship_to_name }} · {{ $order->ship_to_phone }}</p>
        <p class="text-gray-500 mt-1">{{ $order->address }}, {{ $order->governorate }}</p>
    </div>
</div>
