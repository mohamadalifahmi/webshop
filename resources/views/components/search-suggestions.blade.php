@props(['suggestions' => []])

@if ($suggestions instanceof \Illuminate\Support\Collection && $suggestions->isNotEmpty())
    <ul class="absolute left-0 right-0 top-full z-50 mt-2 max-h-80 overflow-y-auto rounded-2xl border border-white/10 bg-space-800/95 backdrop-blur-xl py-1 shadow-2xl">
        @foreach ($suggestions as $product)
            <li>
                <a href="{{ route('product.show', $product) }}" wire:navigate
                    class="flex items-center gap-3 px-4 py-3 hover:bg-cosmic-500/10 transition-colors">
                    @if ($img = $product->getFirstMediaUrl('images', 'thumb') ?: $product->getFirstMediaUrl('images'))
                        <img src="{{ $img }}" alt="{{ $product->name }}" width="44" height="44" loading="lazy" decoding="async" class="h-11 w-11 shrink-0 rounded-xl object-cover" />
                    @else
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/5 text-xl">📦</span>
                    @endif
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-white/80">{{ $product->name }}</span>
                        <span class="mt-0.5 flex items-center gap-1.5 text-xs text-white/30">
                            @if ($product->category?->name)
                                <span class="truncate">{{ $product->category->name }}</span>
                                <span>·</span>
                            @endif
                            <x-price :amount="$product->price" />
                        </span>
                    </span>
                </a>
            </li>
        @endforeach
    </ul>
@endif
