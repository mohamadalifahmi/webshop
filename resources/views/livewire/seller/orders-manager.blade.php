<div>
    <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-6">Orders to Ship</h1>

    @php($filters = ['' => 'All', 'awaiting' => 'Awaiting Shipment', 'shipped' => 'Shipped', 'delivered' => 'Delivered'])
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach ($filters as $key => $label)
            <button wire:click="$set('shipmentFilter', '{{ $key }}')"
                class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ $shipmentFilter === $key ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-amber-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl bg-white border border-gray-200">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-400">
                <tr>
                    <th class="px-5 py-3">Order</th>
                    <th class="px-5 py-3 hidden md:table-cell">Item</th>
                    <th class="px-5 py-3 hidden lg:table-cell">Ship to</th>
                    <th class="px-5 py-3">Earning</th>
                    <th class="px-5 py-3">Shipment</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-5 py-4">
                            <p class="font-bold text-gray-900">{{ $item->order->order_number }}</p>
                            <p class="text-[11px] text-gray-400">Paid {{ optional($item->order->paid_at)->format('d M') }}</p>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <p class="line-clamp-1 font-medium text-gray-800">{{ $item->product_name }}</p>
                            <p class="text-[11px] text-gray-400">× {{ $item->quantity }} · {{ $item->product_sku }}</p>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell text-xs text-gray-500">
                            <p class="font-medium text-gray-700">{{ $item->order->ship_to_name }}</p>
                            <p>{{ $item->order->governorate }}</p>
                            <p>{{ $item->order->ship_to_phone }}</p>
                        </td>
                        <td class="px-5 py-4"><span class="font-bold text-emerald-600">${{ number_format((float) $item->seller_earning, 2) }}</span></td>
                        <td class="px-5 py-4">
                            <x-status-badge :status="$item->shipment_status === 'awaiting' ? 'awaiting' : $item->shipment_status" />
                            @if ($item->tracking_number)
                                <p class="mt-1 font-mono text-[11px] text-gray-500">{{ $item->tracking_number }}</p>
                            @endif
                            @if ($item->shipment_status === 'awaiting')
                                <p class="mt-1 text-[10px] text-red-400 font-semibold">Deadline: {{ optional($item->cancel_deadline_at)->format('d M, H:i') }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2 text-xs font-semibold">
                                @if ($item->shipment_status === 'awaiting')
                                    <button wire:click="openShipModal({{ $item->id }})"
                                        class="rounded-lg bg-amber-600 px-3.5 py-2 text-white hover:bg-amber-700">Mark as Shipped</button>
                                @elseif ($item->shipment_status === 'shipped')
                                    <button wire:click="confirmDelivered({{ $item->id }})"
                                        class="rounded-lg bg-teal-600 px-3.5 py-2 text-white hover:bg-teal-700">Mark Delivered</button>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <x-empty-state title="Nothing to ship" description="New paid orders will appear here with a 48-hour shipping deadline." />
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    <!-- Ship modal -->
    @if ($showShipModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showShipModal', false)"></div>
            <form wire:submit="confirmShip" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-bold text-gray-900">Enter Tracking Number</h2>
                <p class="mt-1 text-xs text-gray-400">Buyer receives an email instantly with this number.</p>
                <input type="text" wire:model="trackingNumber" placeholder="e.g. ARX123" autofocus
                    class="mt-4 w-full rounded-lg border-gray-300 focus:ring-amber-500 font-mono" />
                @error('trackingNumber') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                <div class="mt-5 flex gap-3 justify-end">
                    <button type="button" wire:click="$set('showShipModal', false)" class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-800">Cancel</button>
                    <x-ui-button type="primary" wire:loading.attr="disabled" wire:target="confirmShip">Confirm Shipment</x-ui-button>
                </div>
            </form>
        </div>
    @endif
</div>
