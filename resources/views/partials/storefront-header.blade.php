    <!-- Navigation -->
    <header class="fixed top-0 left-0 right-0 z-50 glass">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 sm:h-20 items-center justify-between gap-4">
                <!-- Logo -->
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2.5 shrink-0 group">
                    {{-- Handcrafted mark: a shooting-star spark with a small cart orbit --}}
                    <svg class="h-8 w-8 text-cosmic-500 group-hover:text-stargold transition-colors duration-300" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 2L19 12H29L21 18L24 28L16 22L8 28L11 18L3 12H13L16 2Z" fill="currentColor"/>
                        <circle cx="16" cy="19" r="4.5" stroke="#0A0A0F" stroke-width="2"/>
                        <path d="M11.5 19.5h9M16 15v9M12 16.5l3-4M20 16.5l-3-4M12 21.5l3 4M20 21.5l-3 4" stroke="#0A0A0F" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                    <span class="text-lg sm:text-xl font-black tracking-wider">
                        <span class="text-white">ASTRA</span><span class="text-cosmic-500">GO</span>
                        <span class="hidden sm:inline text-xs font-medium text-white/40 tracking-widest uppercase ml-1">Market</span>
                    </span>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-white/60">
                    <a href="{{ route('home') }}" wire:navigate class="hover:text-white transition-colors duration-200 {{ request()->routeIs('home') ? 'text-white' : '' }}">Home</a>
                    <a href="{{ route('shop') }}" wire:navigate class="hover:text-white transition-colors duration-200 {{ request()->routeIs('shop') ? 'text-white' : '' }}">Shop</a>
                    @auth
                        <a href="{{ route('account.dashboard') }}" wire:navigate class="hover:text-white transition-colors duration-200 {{ request()->routeIs('account.*', 'profile') ? 'text-white' : '' }}">Account</a>
                        <a href="{{ route('account.orders') }}" wire:navigate class="hover:text-white transition-colors duration-200 {{ request()->routeIs('account.orders*') ? 'text-white' : '' }}">Orders</a>
                    @endauth
                    @can('create', \App\Models\Product::class)
                        <a href="{{ auth()->user()->seller && auth()->user()->seller->status === 'approved' ? route('seller.dashboard') : route('seller.application.show') }}" wire:navigate class="hover:text-white transition-colors duration-200">Sell</a>
                    @endcan
                </nav>

                <!-- Right Actions -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Currency Toggle -->
                    <div class="hidden sm:flex items-center rounded-full border border-white/10 overflow-hidden text-xs font-bold">
                        @php($currency = session('currency', 'USD'))
                        @foreach (['USD', 'LBP'] as $cur)
                            <a href="{{ route('currency', $cur) }}"
                                class="px-3 py-1.5 transition-all duration-200 {{ $currency === $cur ? 'bg-cosmic-500 text-white' : 'text-white/50 hover:text-cosmic-400' }}">
                                {{ $cur }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Cart -->
                    <a href="{{ auth()->check() ? route('cart') : route('login') }}" wire:navigate
                        class="relative inline-flex items-center rounded-xl p-2.5 text-white/50 hover:text-white hover:bg-white/5 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                        @auth
                            <livewire:cart-count />
                        @endauth
                    </a>

                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        @auth
                            <button @click="open = !open"
                                class="flex items-center gap-2 rounded-xl border border-white/10 py-1.5 pl-1.5 pr-3 hover:border-cosmic-500/40 transition-all duration-200">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-cosmic-500 text-xs font-bold text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3 text-white/40">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition.opacity class="absolute right-0 mt-3 w-56 origin-top-right rounded-xl border border-white/10 bg-space-800/95 backdrop-blur-xl shadow-2xl py-1 text-sm z-50" style="display:none">
                                <div class="border-b border-white/5 px-4 py-2.5 text-xs text-white/30 truncate">{{ auth()->user()->email }}</div>
                                @role('admin')
                                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="block px-4 py-2.5 text-white/70 hover:bg-cosmic-500/10 hover:text-cosmic-400 transition-colors">Admin Panel</a>
                                @endrole
                                @if(auth()->user()->seller)
                                    <a href="{{ auth()->user()->seller->status === 'approved' ? route('seller.dashboard') : route('seller.application.show') }}" wire:navigate class="block px-4 py-2.5 text-white/70 hover:bg-cosmic-500/10 hover:text-cosmic-400 transition-colors">Seller Hub</a>
                                @else
                                    <a href="{{ route('become-seller') }}" wire:navigate class="block px-4 py-2.5 font-semibold text-stargold hover:bg-stargold/10 transition-colors">Become a Seller</a>
                                @endif
                                <a href="{{ route('account.dashboard') }}" wire:navigate class="block px-4 py-2.5 text-white/70 hover:bg-cosmic-500/10 hover:text-cosmic-400 transition-colors">Dashboard</a>
                                <a href="{{ route('profile') }}" wire:navigate class="block px-4 py-2.5 text-white/70 hover:bg-cosmic-500/10 hover:text-cosmic-400 transition-colors">Settings</a>
                                <div class="border-t border-white/5 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2.5 text-red-400 hover:bg-red-500/10 transition-colors">Sign Out</button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" wire:navigate class="rounded-xl bg-cosmic-500/20 border border-cosmic-500/30 px-4 py-2 text-sm font-semibold text-cosmic-300 hover:bg-cosmic-500/30 hover:border-cosmic-500/50 transition-all duration-200">Sign In</a>
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
                    <button x-data="{ mobileOpen: false }" @click="mobileOpen = !mobileOpen; $dispatch('toggle-mobile', { open: mobileOpen })"
                        class="md:hidden inline-flex items-center justify-center rounded-xl p-2.5 text-white/50 hover:text-white hover:bg-white/5 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div x-data="{ mobileOpen: false }" @toggle-mobile.window="mobileOpen = $event.detail.open" x-show="mobileOpen" x-transition.opacity class="md:hidden border-t border-white/5" style="display:none">
            <nav class="px-4 py-4 space-y-1">
                <a href="{{ route('home') }}" wire:navigate class="block rounded-xl px-4 py-3 text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white transition-all">Home</a>
                <a href="{{ route('shop') }}" wire:navigate class="block rounded-xl px-4 py-3 text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white transition-all">Shop</a>
                @auth
                    <a href="{{ route('account.dashboard') }}" wire:navigate class="block rounded-xl px-4 py-3 text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white transition-all">Account</a>
                    <a href="{{ route('account.orders') }}" wire:navigate class="block rounded-xl px-4 py-3 text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white transition-all">Orders</a>
                @endauth
            </nav>
        </div>
    </header>
    <!-- Spacer for fixed header -->
    <div class="h-16 sm:h-20"></div>
