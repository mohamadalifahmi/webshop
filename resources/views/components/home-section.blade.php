@props([
    'title',
    'icon' => null,
    'subtitle' => null,
    'link' => null,
    'products' => [],
])

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16 reveal">
    <div class="mb-6 flex items-end justify-between gap-4">
        <div class="flex items-center gap-3">
            @if ($icon)
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cosmic-500/10 border border-cosmic-500/15 text-2xl">{{ $icon }}</span>
            @endif
            <div>
                <h2 class="text-xl sm:text-2xl font-serif font-bold text-white">{{ $title }}</h2>
                @if ($subtitle)
                    <p class="text-sm text-white/30 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if ($link)
            <a href="{{ $link }}" wire:navigate class="shrink-0 text-sm font-semibold text-cosmic-400 hover:text-cosmic-300 transition">View all -&gt;</a>
        @endif
    </div>

    @if (is_object($products) && count($products) > 0)
        <div class="grid grid-cols-2 gap-3 sm:gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @else
        <x-empty-state title="No products yet" description="Check back soon - new arrivals are on the way." />
    @endif
</section>
