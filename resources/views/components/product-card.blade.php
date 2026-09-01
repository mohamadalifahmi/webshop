@props(['product'])

<article class="group flex flex-col overflow-hidden rounded-2xl glass-card">
    <a href="{{ route('product.show', $product) }}" wire:navigate class="block aspect-square overflow-hidden bg-space-800 relative">
        @if ($image = $product->getFirstMediaUrl('images', 'thumb') ?: $product->getFirstMediaUrl('images'))
            <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy" decoding="async"
                width="400" height="400" class="h-full w-full object-cover group-hover:scale-105 transition duration-500" />
        @else
            <div class="flex h-full w-full items-center justify-center text-4xl opacity-20">📦</div>
        @endif
        @if ($product->stock <= 3 && $product->stock > 0)
            <span class="absolute top-3 right-3 text-[10px] font-bold text-stargold bg-deep/80 backdrop-blur-sm rounded-full px-2.5 py-1 border border-stargold/20">Only {{ $product->stock }} left</span>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-4">
        <a href="{{ route('product.show', $product) }}" wire:navigate
            class="line-clamp-2 text-sm font-semibold text-white/80 hover:text-cosmic-400 transition-colors duration-200">{{ $product->name }}</a>
        <p class="mt-1.5 text-[11px] uppercase tracking-wider text-white/25">{{ $product->category?->name }} · {{ $product->seller?->store_name }}</p>
        <div class="mt-auto pt-3 flex items-end justify-between">
            <x-price :amount="$product->price" />
        </div>
    </div>
</article>
