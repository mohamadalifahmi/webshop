<div>
    <!-- Search hero -->
    <section class="rounded-2xl bg-gradient-to-r from-gray-900 via-gray-800 to-amber-900 px-6 py-10 sm:px-10 mb-8">
        <h1 class="text-2xl sm:text-4xl font-black text-white">The Local Marketplace<br class="sm:hidden"> Where Everyone Wins.</h1>
        <p class="mt-2 text-sm sm:text-base text-gray-300">Thousands of products from Lebanon's best local sellers. Ship by seller, pay once.</p>

        <div class="mt-6 max-w-xl relative">
            <div class="relative">
                <input type="search" wire:model.live.debounce.350ms="q" autocomplete="off" placeholder="Search products... try 'iph'"
                    class="w-full rounded-full border-0 bg-white px-5 py-3.5 pr-12 text-sm shadow-lg focus:ring-2 focus:ring-amber-500 placeholder:text-gray-400" />
                <span wire:loading wire:target="q" class="absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin h-5 w-5 text-amber-600" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                </span>
            </div>
            <x-search-suggestions :suggestions="$suggestions" />
        </div>
    </section>

    <!-- Filters -->
    <section class="mb-6 grid grid-cols-2 md:flex gap-3 items-center">
        <select wire:model.live="category" class="rounded-lg border-gray-300 text-sm py-2 focus:ring-amber-500 w-full">
            <option value="">All categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>

        <input type="number" min="0" step="0.01" wire:model.live.debounce.500ms="minPrice" placeholder="Min $" class="rounded-lg border-gray-300 text-sm py-2 w-full focus:ring-amber-500" />
        <input type="number" min="0" step="0.01" wire:model.live.debounce.500ms="maxPrice" placeholder="Max $" class="rounded-lg border-gray-300 text-sm py-2 w-full focus:ring-amber-500" />

        <select wire:model.live="sort" class="rounded-lg border-gray-300 text-sm py-2 focus:ring-amber-500 w-full">
            <option value="newest">Newest</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
        </select>

        <button wire:click="resetFilters" class="text-xs font-semibold text-gray-400 hover:text-amber-600 whitespace-nowrap">Reset ✕</button>
    </section>

    @if ($products->isEmpty())
        <x-empty-state title="No products found" description="Try a different search term or clear your filters." />
    @else
        <section class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </section>

        <div class="mt-8">{{ $products->links() }}</div>
    @endif
</div>
