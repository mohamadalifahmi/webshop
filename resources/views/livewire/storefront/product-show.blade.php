@push('head')
<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": {{ json_encode($product->name) }},
    "description": {{ json_encode(\Illuminate\Support\Str::limit(strip_tags((string) $product->description), 500)) }},
    "image": {{ json_encode($product->getFirstMediaUrl('images') ? url($product->getFirstMediaUrl('images')) : '') }},
    "sku": {{ json_encode($product->sku) }},
    "brand": { "@type": "Brand", "name": {{ json_encode($product->seller?->store_name ?? 'ASTRAGO MARKET') }} },
    "offers": {
        "@type": "Offer",
        "url": {{ json_encode(route('product.show', $product)) }},
        "priceCurrency": "USD",
        "price": {{ json_encode(number_format((float) $product->price, 2, '.', '')) }},
        "availability": {{ json_encode($product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock') }},
        "itemCondition": "https://schema.org/NewCondition"
    }
}
</script>
@endpush
<div class="grid gap-8 lg:grid-cols-2">
    <!-- Gallery -->
    <section>
        <div class="aspect-square overflow-hidden rounded-2xl border border-white/10 bg-space-800">
            @if ($image = $product->getFirstMediaUrl('images', 'thumb') ?: $product->getFirstMediaUrl('images'))
                <img src="{{ $image }}" alt="{{ $product->name }}" width="800" height="800" fetchpriority="high"
                    class="h-full w-full object-cover" />
            @else
                <div class="flex h-full items-center justify-center text-7xl opacity-20">📦</div>
            @endif
        </div>
        @if ($product->getMedia('images')->count() > 1)
            <div class="mt-3 grid grid-cols-5 gap-2">
                @foreach ($product->getMedia('images') as $media)
                    <img src="{{ $media->getUrl('thumb') }}" alt="{{ $product->name }} — thumbnail {{ $loop->index + 1 }}"
                        width="80" height="80" loading="lazy" decoding="async" class="aspect-square rounded-lg object-cover border border-white/10" />
                @endforeach
            </div>
        @endif
    </section>

    <!-- Info -->
    <section>
        <p class="text-xs font-semibold uppercase tracking-widest text-stargold">{{ $product->category?->name }}</p>
        <h1 class="mt-1 text-2xl sm:text-3xl font-serif font-bold text-white">{{ $product->name }}</h1>

        <div class="mt-4 flex items-center gap-3">
            <x-price :amount="$product->price" />
        </div>

        <div class="mt-3 flex items-center gap-4 text-sm">
            @if ($product->stock > 0)
                <span class="font-medium text-emerald-400">● In stock ({{ $product->stock }})</span>
            @else
                <span class="font-medium text-red-400">● Out of stock</span>
            @endif
            <span class="text-white/30">SKU: {{ $product->sku }}</span>
        </div>

        <div class="mt-5 rounded-2xl glass p-4 flex items-center justify-between">
            <div class="flex items-center gap-3 text-sm">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cosmic-500 text-white text-xs font-bold">
                    {{ strtoupper(substr($product->seller?->store_name ?? 'S', 0, 1)) }}
                </span>
                <div>
                    <p class="font-semibold text-white/80">{{ $product->seller?->store_name }}</p>
                    <p class="text-xs text-white/30">Verified seller · {{ $product->seller?->governorate ?? 'Lebanon' }}</p>
                </div>
            </div>
        </div>

        @if ($product->stock > 0)
            @if ($added)
                {{-- Inline confirmation — shopper stays on the page, no redirect --}}
                <div class="mt-5 flex items-center gap-3 rounded-xl border border-emerald-500/25 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                    <span>Added {{ $quantity }} to your cart.</span>
                    <a href="{{ route('cart') }}" wire:navigate class="ml-auto font-semibold text-cosmic-300 hover:text-cosmic-200">View cart -&gt;</a>
                </div>
            @endif

            <form wire:submit="addToCart" class="mt-5 flex gap-3">
                {{-- Quantity control — dark friendly, text clearly visible --}}
                <div class="flex items-center rounded-xl overflow-hidden border border-white/15 bg-space-700/60">
                    <button type="button" wire:click="decrementQuantity" class="px-4 py-2.5 text-white hover:bg-white/10 transition text-lg leading-none">−</button>
                    <input type="number" wire:model.live="quantity" min="1" max="{{ $product->stock }}"
                        class="w-14 border-0 bg-transparent text-center font-semibold text-white focus:ring-0 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
                    <button type="button" wire:click="incrementQuantity" class="px-4 py-2.5 text-white hover:bg-white/10 transition text-lg leading-none">+</button>
                </div>
                <x-ui-button type="primary" class="flex-1 !py-3.5">Add to Cart</x-ui-button>
            </form>
        @endif

        <article class="mt-6 text-sm leading-relaxed text-white/75 whitespace-pre-line">{{ $product->description }}</article>
    </section>
</div>
