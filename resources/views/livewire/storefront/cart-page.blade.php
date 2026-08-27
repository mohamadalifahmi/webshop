<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-black text-gray-900 mb-6">Your Cart</h1>

    @if ($items->isEmpty())
        <div class="rounded-2xl bg-white border border-gray-200">
            <x-empty-state title="Your cart is empty" description="Browse the marketplace and add something you love.">
                <x-slot:action>
                    <a href="{{ route('home') }}" wire:navigate><x-ui-button type="primary">Start Shopping</x-ui-button></a>
                </x-slot:action>
            </x-empty-state>
        </div>
    @else
        <div class="rounded-2xl bg-white border border-gray-200 divide-y divide-gray-100">
            @foreach ($items as $item)
                <div class="flex gap-4 p-4 sm:p-5 items-center">
                    <a href="{{ route('product.show', $item->product) }}" wire:navigate class="h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-xl bg-gray-100">
                        @if ($img = $item->product->getFirstMediaUrl('images', 'thumb') ?: $item->product->getFirstMediaUrl('images'))
                            <img src="{{ $img }}" alt="" class="h-full w-full object-cover" />
                        @else
                            <div class="flex h-full items-center justify-center text-2xl">📦</div>
                        @endif
                    </a>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('product.show', $item->product) }}" wire:navigate class="font-semibold text-gray-900 hover:text-amber-700 line-clamp-1">{{ $item->product->name }}</a>
                        <p class="text-xs text-gray-400 mt-0.5">Sold by {{ $item->product->seller?->store_name }}</p>
                        <div class="mt-2 flex items-center gap-3">
                            <div class="flex items-center rounded-lg border border-gray-300 overflow-hidden text-sm">
                                <button type="button" wire:click="updateQty({{ $item->id }}, {{ max(1, $item->quantity - 1) }})" class="px-2.5 py-1 hover:bg-gray-100">−</button>
                                <span class="w-8 text-center">{{ $item->quantity }}</span>
                                <button type="button" wire:click="updateQty({{ $item->id }}, {{ min($item->product->stock, $item->quantity + 1) }})" class="px-2.5 py-1 hover:bg-gray-100">+</button>
                            </div>
                            <button wire:click="removeItem({{ $item->id }})" wire:confirm="Remove this item?" class="text-xs font-medium text-red-500 hover:text-red-700">Remove</button>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">$ {{ number_format((float) ($item->product->price * $item->quantity), 2) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 rounded-2xl bg-white border border-gray-200 p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">Subtotal ({{ $items->sum('quantity') }} items)</p>
                <p class="text-2xl font-black text-gray-900">${{ number_format($subtotal, 2) }}</p>
                <p class="text-xs text-gray-400">Shipping calculated at checkout</p>
            </div>
            <a href="{{ route('checkout') }}"><x-ui-button type="primary" class="!px-10 !py-3.5">Checkout →</x-ui-button></a>
        </div>
    @endif
</div>
