@props(['product'])

<article class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition">
    <a href="{{ route('product.show', $product) }}" wire:navigate class="block aspect-square overflow-hidden bg-gray-100 relative">
        @if ($image = $product->getFirstMediaUrl('images', 'thumb') ?: $product->getFirstMediaUrl('images'))
            <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy"
                class="h-full w-full object-cover group-hover:scale-105 transition duration-300" />
        @else
            <div class="flex h-full w-full items-center justify-center text-4xl">📦</div>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-3">
        <a href="{{ route('product.show', $product) }}" wire:navigate
            class="line-clamp-2 text-sm font-semibold text-gray-900 hover:text-amber-700">{{ $product->name }}</a>
        <p class="mt-1 text-[11px] uppercase tracking-wide text-gray-400">{{ $product->category?->name }} · {{ $product->seller?->store_name }}</p>
        <div class="mt-auto pt-3 flex items-end justify-between">
            <x-price :amount="$product->price" />
            @if ($product->stock <= 3)
                <span class="text-[10px] font-bold text-red-500">Only {{ $product->stock }} left!</span>
            @endif
        </div>
    </div>
</article>