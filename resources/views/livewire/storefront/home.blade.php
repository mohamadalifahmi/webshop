<div>
    @if ($searching)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white">Search results{{ $q ? ' for "'.trim($q).'"' : '' }}</h1>
                    <p class="text-sm text-white/40 mt-1">{{ $results->count() }} products found</p>
                </div>
                <a href="{{ route('shop', ['q' => trim($q)]) }}" wire:navigate
                    class="shrink-0 text-sm font-semibold text-cosmic-400 hover:text-cosmic-300 transition">View all -&gt;</a>
            </div>

            @if ($results->isNotEmpty())
                <div class="grid grid-cols-2 gap-3 sm:gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($results as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @else
                <x-empty-state title="No products found" description="Try a different search term or browse all products." />
            @endif
        </section>
    @else

    {{-- HERO --}}
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 nebula-gradient"></div>

        <div class="shooting-star" style="top: 10%; left: 5%; width: 140px; animation-delay: 0s; animation-duration: 3.5s;"></div>
        <div class="shooting-star" style="top: 30%; left: 20%; width: 100px; animation-delay: 1.8s; animation-duration: 3s;"></div>
        <div class="shooting-star" style="top: 15%; left: 60%; width: 160px; animation-delay: 4s; animation-duration: 4s;"></div>
        <div class="shooting-star" style="top: 50%; left: 75%; width: 80px; animation-delay: 2.5s; animation-duration: 2.8s;"></div>

        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cosmic-500/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto" data-parallax>
            <div class="inline-flex items-center gap-2 rounded-full border border-cosmic-500/30 bg-cosmic-500/10 px-4 py-1.5 mb-8">
                <span class="h-1.5 w-1.5 rounded-full bg-stargold animate-pulse"></span>
                <span class="text-xs font-semibold text-cosmic-300 uppercase tracking-wider">The future of shopping</span>
            </div>

            <h1 class="font-serif text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-bold text-white text-glow leading-[0.95] tracking-tight">
                Shop Among<br class="hidden sm:block"> <span class="text-cosmic-400">Stars</span>
            </h1>

            <p class="mt-6 text-lg sm:text-xl text-white/40 max-w-xl mx-auto leading-relaxed">
                Technology meets taste. We vet every seller and product so you shop with total confidence.
            </p>

            <div class="relative mt-10 max-w-xl mx-auto">
                <div class="relative">
                    <input type="search" wire:model.live.debounce.250ms="q" autocomplete="off"
                        placeholder="Search products..."
                        class="w-full rounded-2xl input-cosmic px-6 py-4 pr-14 text-sm shadow-2xl" />
                    <span wire:loading wire:target="q" class="absolute right-5 top-1/2 -translate-y-1/2">
                        <svg class="animate-spin h-5 w-5 text-cosmic-400" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                        </svg>
                    </span>
                </div>
                @if ($searching)
                    <x-search-suggestions :suggestions="$suggestions" />
                @endif
            </div>

            {{-- Primary CTA is bigger with a glow, secondary is smaller --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('shop') }}" wire:navigate
                    class="btn-glow rounded-2xl px-12 py-5 text-base font-bold text-white tracking-wide">
                    Shop Now
                </a>
                <a href="#categories" class="rounded-xl px-6 py-2.5 text-sm font-semibold text-white/60 border border-white/15 hover:border-white/30 hover:text-white/80 transition-all duration-200">
                    Browse categories
                </a>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-float">
            <div class="w-5 h-8 rounded-full border border-white/20 flex items-start justify-center p-1">
                <div class="w-1 h-2 rounded-full bg-cosmic-400 animate-bounce"></div>
            </div>
        </div>
    </section>

    {{-- FILTERS --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 mt-4 grid grid-cols-2 md:flex gap-3 items-center reveal">
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

        <button wire:click="resetFilters" class="text-xs font-semibold text-white/50 hover:text-cosmic-400 whitespace-nowrap transition">Reset</button>
    </section>

    {{-- FEATURED CATEGORIES - asymmetric layout --}}
    @if ($featuredCategories->isNotEmpty())
    <section id="categories" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12 reveal">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-stargold mb-3">Shop by category</p>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white">Find what you need</h2>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 stagger-reveal">
            @foreach ($featuredCategories as $index => $cat)
                <a href="{{ route('shop', ['category' => $cat->id]) }}" wire:navigate
                    class="glass-card rounded-2xl p-6 text-center group cursor-pointer
                        {{ $index === 0 ? 'col-span-2 lg:col-span-2 py-8' : '' }}
                        {{ $index === 3 ? 'col-span-2 lg:col-span-2 py-8' : '' }}">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-cosmic-500/15 border border-cosmic-500/20 flex items-center justify-center text-2xl group-hover:bg-cosmic-500/25 group-hover:border-cosmic-500/40 transition-all duration-300">
                        @if ($cat->icon)
                            {{ $cat->icon }}
                        @else
                            <svg class="w-6 h-6 text-cosmic-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                            </svg>
                        @endif
                    </div>
                    <h3 class="text-sm font-bold text-white/80 group-hover:text-white transition-colors">{{ $cat->name }}</h3>
                    <p class="text-xs text-white/30 mt-1">{{ $cat->products_count }} {{ Str::plural('item', $cat->products_count) }}</p>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- TRENDING --}}
    <x-home-section
        title="Most Buying"
        subtitle="The most-bought products on AstraGo right now"
        svg='<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>'
        :products="$mostBought"
    />

    {{-- CATEGORY SECTIONS --}}
    @foreach ($sections as $category)
        <x-home-section
            :title="$category->name"
            :subtitle="$category->products_count.' '.(($category->products_count === 1) ? 'product' : 'products')"
            :icon="$category->icon"
            :products="$category->section_products"
            :link="route('shop', ['category' => $category->id])"
        />
    @endforeach

    {{-- WHY ASTRAGO - middle card lifted to break symmetry --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center mb-14 reveal">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-stargold mb-3">Why us</p>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white">Why AstraGo</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 stagger-reveal">
            <div class="glass-card rounded-3xl p-8 text-center group">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-cosmic-500/20 to-nebula-500/20 border border-cosmic-500/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-cosmic-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl font-bold text-white mb-3">Ships Fast</h3>
                <p class="text-sm text-white/65 leading-relaxed">Sellers ship quickly across Lebanon, and instant digital items arrive the second you pay.</p>
            </div>

            <div class="glass-card rounded-3xl p-8 text-center group sm:translate-y-8">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-nebula-500/20 to-cosmic-500/20 border border-nebula-500/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-nebula-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl font-bold text-white mb-3">Secure Payments</h3>
                <p class="text-sm text-white/65 leading-relaxed">Encrypted checkout and verified sellers. One payment, and we split it between your sellers automatically.</p>
            </div>

            <div class="glass-card rounded-3xl p-8 text-center group">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-stargold/20 to-cosmic-500/20 border border-stargold/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-stargold-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl font-bold text-white mb-3">Vetted Sellers</h3>
                <p class="text-sm text-white/65 leading-relaxed">Every store is reviewed before it goes live, so what you order is what you get.</p>
            </div>
        </div>
    </section>

    @endif
</div>
