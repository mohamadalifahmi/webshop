<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csp-nonce" content="{{ $cspNonce ?? '' }}">
    <title>{{ $title ?? 'Admin' }} — ASTRAGO MARKET</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛒</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles(['nonce' => $cspNonce ?? ''])
</head>
<body x-data class="font-sans antialiased bg-gray-100 text-gray-800">
<div class="flex min-h-screen">
    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full lg:translate-x-0 transition-transform bg-gray-900 text-gray-300 flex flex-col">
        <a href="{{ route('home') }}" wire:navigate class="flex h-16 items-center gap-2 border-b border-gray-800 px-6 shrink-0">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-600 font-black text-white">S</span>
            <div>
                <p class="text-sm font-black text-white leading-none">ASTRAGO MARKET</p>
                <p class="text-[10px] uppercase tracking-widest text-amber-500 mt-1">Admin Panel</p>
            </div>
        </a>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4 text-sm font-medium">
            @php($route = request()->route()?->getName())
            <a href="{{ route('admin.dashboard') }}" wire:navigate
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ $route === 'admin.dashboard' ? 'bg-amber-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('admin.sellers') }}" wire:navigate
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ $route === 'admin.sellers' ? 'bg-amber-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                🏪 Sellers
            </a>
            <a href="{{ route('admin.products') }}" wire:navigate
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ $route === 'admin.products' ? 'bg-amber-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                🛍️ Products
            </a>
            <a href="{{ route('admin.orders') }}" wire:navigate
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ $route === 'admin.orders' ? 'bg-amber-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                🧾 Orders & Payments
            </a>
            <a href="{{ route('admin.payouts') }}" wire:navigate
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ $route === 'admin.payouts' ? 'bg-amber-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                💸 Payouts
            </a>
            <a href="{{ route('admin.settings') }}" wire:navigate
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ $route === 'admin.settings' ? 'bg-amber-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                ⚙️ Site Settings
            </a>
        </nav>

        <div class="border-t border-gray-800 p-4 text-xs text-gray-500">
            God Mode · {{ auth()->user()->name }}
        </div>
    </aside>

    <div class="flex flex-1 flex-col lg:pl-64 min-w-0">
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6">
            <button onclick="document.getElementById('admin-sidebar').classList.toggle('-translate-x-full')" class="lg:hidden p-2 text-gray-500">☰</button>
            <p class="hidden lg:block text-sm font-semibold text-gray-500">{{ $title ?? '' }}</p>
            <div class="flex items-center gap-4 text-sm">
                <span class="hidden sm:inline text-gray-400">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-red-500 hover:text-red-700 font-medium">Log out</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 max-w-7xl w-full mx-auto">
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">✓ {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">✕ {{ session('error') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>
@livewireScripts(['nonce' => $cspNonce ?? ''])
@stack('scripts')
</body>
</html>