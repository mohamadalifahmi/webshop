<div>
    <!-- Catalog hero -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-white">Shop the market</h1>
        <p class="mt-2 text-white/40">Fresh arrivals from Lebanon's local sellers, in one place.</p>

        <div class="mt-6 max-w-xl relative">
            <div class="relative">
                <input type="search" wire:model.live.debounce.350ms="q" autocomplete="off" placeholder="Search products..."
                    class="w-full rounded-2xl input-cosmic px-5 py-3.5 pr-12 text-sm" />
                <span wire:loading wire:target="q" class="absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin h-5 w-5 text-cosmic-400" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                </span>
            </div>
            <x-search-suggestions :suggestions="$suggestions" />
        </div>
    </section>

    <!-- Filters -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 grid grid-cols-2 md:flex gap-3 items-center">
        <select wire:model.live="category" class="rounded-xl input-cosmic text-sm py-2.5 w-full">
            <option value="">All Categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>

        <input type="number" min="0" step="0.01" wire:model.live.debounce.500ms="minPrice" placeholder="Min $" class="rounded-xl input-cosmic text-sm py-2.5 w-full" />
        <input type="number" min="0" step="0.01" wire:model.live.debounce.500ms="maxPrice" placeholder="Max $" class="rounded-xl input-cosmic text-sm py-2.5 w-full" />

        <select wire:model.live="sort" class="rounded-xl input-cosmic text-sm py-2.5 w-full">
            <option value="newest">Newest</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
        </select>

        <button wire:click="resetFilters" class="text-xs font-semibold text-white/30 hover:text-cosmic-400 whitespace-nowrap transition">Reset</button>
    </section>

    @if ($products->isEmpty())
        <div class="max-w-7xl mx-auto px-4">
            <x-empty-state title="No products found" description="Try a different search term or clear your filters." />
        </div>
    @else
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </section>

        <div class="max-w-7xl mx-auto px-4 pb-16">{{ $products->links() }}</div>
    @endif
</div>
