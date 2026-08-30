    <!-- Top bar -->
    <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/95 backdrop-blur">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-4">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2 shrink-0">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-600 font-black text-white">S</span>
                    <span class="text-xl font-black tracking-tight text-gray-900">SOUK<span class="text-amber-600">ELKOM</span></span>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                    <a href="{{ route('shop') }}" wire:navigate class="{{ request()->routeIs('shop') ? 'text-amber-600' : 'hover:text-amber-600' }}">Shop</a>
                    @auth
                        <a href="{{ route('account.dashboard') }}" wire:navigate class="{{ request()->routeIs('account.*', 'profile') ? 'text-amber-600' : 'hover:text-amber-600' }}">My Account</a>
                        <a href="{{ route('account.orders') }}" wire:navigate class="{{ request()->routeIs('account.orders*') ? 'text-amber-600' : 'hover:text-amber-600' }}">Orders</a>
                    @endauth
                    @can('create', \App\Models\Product::class)
                        <a href="{{ auth()->user()->seller && auth()->user()->seller->status === 'approved' ? route('seller.dashboard') : route('seller.application.show') }}" wire:navigate class="hover:text-amber-600">Seller Hub</a>
                    @endcan
                </nav>

                <div class="flex items-center gap-3">
                    <button
                        onclick="window.location.href = window.location.pathname + (window.location.search.includes('currency=LBP') ? '' : '?currency=LBP')"
                        title="Toggle USD/LBP"
                        class="hidden sm:inline-flex items-center rounded-full border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-600 hover:border-amber-400 hover:text-amber-600 transition">
                        USD / LBP
                    </button>

                    <a href="{{ auth()->check() ? route('cart') : route('login') }}" wire:navigate
                        class="relative inline-flex items-center rounded-lg p-2 text-gray-600 hover:bg-gray-100 hover:text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                        @auth
                            <livewire:cart-count />
                        @endauth
                    </a>

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        @auth
                            <button @click="open = !open"
                                class="flex items-center gap-2 rounded-full border border-gray-200 py-1 pl-1 pr-3 hover:border-amber-300 transition">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-800 text-xs font-bold text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                            </button>
                            <div x-show="open" x-transition.opacity class="absolute right-0 mt-2 w-52 origin-top-right rounded-xl border border-gray-100 bg-white shadow-lg py-1 text-sm z-50" style="display:none">
                                <div class="border-b border-gray-100 px-4 py-2 text-xs text-gray-400 truncate">{{ auth()->user()->email }}</div>
                                @role('admin')
                                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="block px-4 py-2 text-gray-700 hover:bg-amber-50 hover:text-amber-700">Admin Panel</a>
                                @endrole
                                @if(auth()->user()->seller)
                                    <a href="{{ auth()->user()->seller->status === 'approved' ? route('seller.dashboard') : route('seller.application.show') }}" wire:navigate class="block px-4 py-2 text-gray-700 hover:bg-amber-50 hover:text-amber-700">Seller Hub</a>
                                @else
                                    <a href="{{ route('become-seller') }}" wire:navigate class="block px-4 py-2 font-semibold text-amber-700 hover:bg-amber-50">Become a Seller</a>
                                @endif
                                <a href="{{ route('account.dashboard') }}" wire:navigate class="block px-4 py-2 text-gray-700 hover:bg-amber-50">My Dashboard</a>
                                <a href="{{ route('profile') }}" wire:navigate class="block px-4 py-2 text-gray-700 hover:bg-amber-50">Account Settings</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-red-600 hover:bg-red-50">Log out</button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" wire:navigate class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 transition">Sign in</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>
