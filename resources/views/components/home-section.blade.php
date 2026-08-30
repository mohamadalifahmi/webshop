@props([
    'title',
    'icon' => null,
    'subtitle' => null,
    'link' => null,
    'products' => [],
])

<section class="mb-12">
    <div class="mb-4 flex items-end justify-between gap-4">
        <div class="flex items-center gap-3">
            @if ($icon)
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-2xl">{{ $icon }}</span>
            @endif
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900">{{ $title }}</h2>
                @if ($subtitle)
                    <p class="text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if ($link)
            <a href="{{ $link }}" wire:navigate class="shrink-0 text-sm font-semibold text-amber-600 hover:text-amber-700 transition">View all →</a>
        @endif
    </div>

    @if (is_object($products) && count($products) > 0)
        <div class="grid grid-cols-2 gap-3 sm:gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @else
        <x-empty-state title="No products yet" description="Check back soon — new products are on the way." />
    @endif
</section>