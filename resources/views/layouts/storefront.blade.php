@php
    // Per-page SEO meta + Open Graph, all safe (defaults provided). Every page
    // component shares these via view()->share() in its render().
    $pageTitle       = $pageTitle       ?? 'ASTRAGO MARKET — Shop Among Stars';
    $pageDescription = $pageDescription ?? 'ASTRAGO MARKET — The luxury space-tech marketplace. Vetted local sellers, one secure checkout, and shipping handled by each store.';
    $pageCanonical   = $pageCanonical   ?? url()->current();
    $pageOgImage     = $pageOgImage     ?? route('home');
    $pageRobots      = $pageRobots      ?? 'index, follow';
    $siteName        = 'ASTRAGO MARKET';
    $fallbackImg     = 'data:image/svg+xml,'.rawurlencode("<svg xmlns='http://www.w3.org/2000/svg' width='1200' height='630'><rect width='100%25' height='100%25' fill='%230A0A0F'/><text x='50%25' y='50%25' fill='%236B46C1' font-family='Arial' font-size='72' text-anchor='middle'>ASTRA</text><text x='50%25' y='50%25' fill='%23fff' font-size='72' font-family='Arial' text-anchor='middle' dominant-baseline='central' dx='90'>GO</text></svg>");
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csp-nonce" content="{{ $cspNonce ?? '' }}">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="{{ $pageRobots }}">
    <link rel="canonical" href="{{ $pageCanonical }}">
    <title>{{ $pageTitle }}</title>

    <!-- Open Graph / Twitter -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:image" content="{{ $pageOgImage === route('home') ? $fallbackImg : $pageOgImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">

    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>✦</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles(['nonce' => $cspNonce ?? ''])
    @stack('head')
</head>
<body x-data class="bg-deep text-white antialiased font-sans">
    <!-- Particle background canvas -->
    <canvas id="particle-canvas"></canvas>

    <div class="relative z-10 min-h-screen flex flex-col">
        @include('partials.storefront-header')

        <!-- Flash messages -->
        <main class="flex-1">
            @if (session('success'))
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-6">
                    <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300 backdrop-blur-sm">
                        ✦ {{ session('success') }}
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-6">
                    <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-300 backdrop-blur-sm">
                        ✕ {{ session('error') }}
                    </div>
                </div>
            @endif

            {{ $slot }}
        </main>

        @include('partials.storefront-footer')
    </div>
    @livewireScripts(['nonce' => $cspNonce ?? ''])
    @stack('scripts')
</body>
</html>
