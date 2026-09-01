<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="font-serif text-2xl sm:text-3xl font-bold text-white mb-6">Your Cart</h1>

    @if ($items->isEmpty())
        <div class="rounded-2xl glass py-10">
            <x-empty-state title="Your cart is empty" description="Browse the marketplace and add something you love.">
                <x-slot:action>
                    <a href="{{ route('home') }}" wire:navigate><x-ui-button type="primary">Start Shopping</x-ui-button></a>
                </x-slot:action>
            </x-empty-state>
        </div>
    @else
        <div class="rounded-2xl glass divide-y divide-white/5">
            @foreach ($items as $item)
                <div class="flex gap-4 p-4 sm:p-5 items-center">
                    @if ($item->product)
                        <a href="{{ route('product.show', $item->product) }}" wire:navigate class="h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-xl bg-space-800">
                            @if ($img = $item->product->getFirstMediaUrl('images', 'thumb') ?: $item->product->getFirstMediaUrl('images'))
                                <img src="{{ $img }}" alt="{{ $item->product->name }}" width="96" height="96" loading="lazy" class="h-full w-full object-cover" />
                            @else
                                <div class="flex h-full items-center justify-center text-2xl opacity-20">📦</div>
                            @endif
                        </a>
                    @else
                        <div class="h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-xl bg-space-800 flex items-center justify-center text-2xl opacity-20">📦</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        @if ($item->product)
                            <a href="{{ route('product.show', $item->product) }}" wire:navigate class="font-semibold text-white/85 hover:text-cosmic-400 line-clamp-1 transition-colors">{{ $item->product->name }}</a>
                            <p class="text-xs text-white/30 mt-0.5">Sold by {{ $item->product->seller?->store_name ?? 'ASTRAGO MARKET' }}</p>
                        @else
                            <p class="font-semibold text-white/60 line-clamp-1">Product no longer available</p>
                            <p class="text-xs text-white/30 mt-0.5">This item was removed by its seller.</p>
                        @endif
                        <div class="mt-2 flex items-center gap-3">
                            @if ($item->product)
                            {{-- Quantity control - dark friendly, count clearly visible --}}
                            <div class="flex items-center rounded-xl overflow-hidden border border-white/15 bg-space-700/60 text-sm">
                                <button type="button" wire:click="updateQty({{ $item->id }}, {{ max(1, $item->quantity - 1) }})" class="px-2.5 py-1 text-white hover:bg-white/10 transition">-</button>
                                <span class="w-8 text-center font-semibold text-white">{{ $item->quantity }}</span>
                                <button type="button" wire:click="updateQty({{ $item->id }}, {{ min($item->product->stock, $item->quantity + 1) }})" class="px-2.5 py-1 text-white hover:bg-white/10 transition">+</button>
                            </div>
                            @endif
                            <button wire:click="removeItem({{ $item->id }})" wire:confirm="Remove this item?" class="text-xs font-medium text-red-400 hover:text-red-300 transition">Remove</button>
                        </div>
                    </div>
                    <div class="text-right">
                        @if ($item->product)
                            <p class="font-bold text-stargold">$ {{ number_format((float) ($item->product->price * $item->quantity), 2) }}</p>
                        @else
                            <p class="text-xs text-white/25">unavailable</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 rounded-2xl glass p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-sm text-white/40">Subtotal ({{ $items->sum('quantity') }} items)</p>
                <p class="text-2xl font-black text-white">${{ number_format($subtotal, 2) }}</p>
                <p class="text-xs text-white/25">Shipping calculated at checkout</p>
            </div>
            <a href="{{ route('checkout') }}"><x-ui-button type="primary" class="!px-10 !py-3.5">Checkout -&gt;</x-ui-button></a>
        </div>
    @endif
</div>
