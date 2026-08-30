<div class="grid gap-8 lg:grid-cols-2">
    <!-- Gallery -->
    <section>
        <div class="aspect-square overflow-hidden rounded-2xl border border-gray-200 bg-gray-100">
            @if ($image = $product->getFirstMediaUrl('images', 'thumb') ?: $product->getFirstMediaUrl('images'))
                <img src="{{ $image }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
            @else
                <div class="flex h-full items-center justify-center text-7xl">📦</div>
            @endif
        </div>
        @if ($product->getMedia('images')->count() > 1)
            <div class="mt-3 grid grid-cols-5 gap-2">
                @foreach ($product->getMedia('images') as $media)
                    <img src="{{ $media->getUrl('thumb') }}" alt="" class="aspect-square rounded-lg object-cover border border-gray-200" />
                @endforeach
            </div>
        @endif
    </section>

    <!-- Info -->
    <section>
        <p class="text-xs font-semibold uppercase tracking-widest text-amber-600">{{ $product->category?->name }}</p>
        <h1 class="mt-1 text-2xl sm:text-3xl font-black text-gray-900">{{ $product->name }}</h1>

        <div class="mt-4 flex items-center gap-3">
            <x-price :amount="$product->price" />
        </div>

        <div class="mt-3 flex items-center gap-4 text-sm">
            @if ($product->stock > 0)
                <span class="font-medium text-emerald-600">● In stock ({{ $product->stock }})</span>
            @else
                <span class="font-medium text-red-500">● Out of stock</span>
            @endif
            <span class="text-gray-400">SKU: {{ $product->sku }}</span>
        </div>

        <div class="mt-5 rounded-xl bg-white border border-gray-200 p-4 flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-900 text-white text-xs font-bold">
                    {{ strtoupper(substr($product->seller?->store_name ?? 'S', 0, 1)) }}
                </span>
                <div>
                    <p class="font-semibold text-gray-900">{{ $product->seller?->store_name }}</p>
                    <p class="text-xs text-gray-400">Verified seller · {{ $product->seller?->governorate ?? 'Lebanon' }}</p>
                </div>
            </div>
        </div>

        @if ($product->stock > 0)
            <form wire:submit="addToCart" class="mt-5 flex gap-3">
                <div class="flex items-center rounded-lg border border-gray-300 overflow-hidden">
                    <button type="button" wire:click="decrementQuantity" class="px-3.5 py-2.5 hover:bg-gray-100 text-lg leading-none">−</button>
                    <input type="number" wire:model.live="quantity" min="1" max="{{ $product->stock }}" class="w-14 border-0 text-center focus:ring-0" />
                    <button type="button" wire:click="incrementQuantity" class="px-3.5 py-2.5 hover:bg-gray-100 text-lg leading-none">+</button>
                </div>
                <x-ui-button type="primary" class="flex-1 !py-3">Add to Cart</x-ui-button>
            </form>
        @endif

        <article class="mt-6 prose prose-sm max-w-none text-gray-600 whitespace-pre-line">{{ $product->description }}</article>
    </section>
</div>
