<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csp-nonce" content="{{ $cspNonce ?? '' }}">
    <title>Sign in — ASTRAGO MARKET</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>✦</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles(['nonce' => $cspNonce ?? ''])
</head>
<body x-data class="bg-deep text-white antialiased font-sans">
<div class="flex min-h-screen flex-col items-center justify-center px-4 py-10 relative">
    <a href="{{ route('home') }}" wire:navigate class="mb-8 flex items-center gap-2.5">
        <svg class="h-9 w-9 text-cosmic-500" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 2L19 12H29L21 18L24 28L16 22L8 28L11 18L3 12H13L16 2Z" fill="currentColor"/>
            <circle cx="16" cy="19" r="4.5" stroke="#0A0A0F" stroke-width="2"/>
            <path d="M11.5 19.5h9M16 15v9M12 16.5l3-4M20 16.5l-3-4M12 21.5l3 4M20 21.5l-3 4" stroke="#0A0A0F" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        <span class="text-2xl font-black tracking-wider">
            <span class="text-white">ASTRA</span><span class="text-cosmic-500">GO</span>
            <span class="text-xs font-medium text-white/40 tracking-widest uppercase ml-1">Market</span>
        </span>
    </a>

    <div class="w-full max-w-md rounded-2xl glass p-6 sm:p-8">
        {{ $slot }}
    </div>

    <p class="mt-6 text-center text-xs text-white/30">ASTRAGO MARKET · Lebanon & MENA</p>
</div>
@livewireScripts(['nonce' => $cspNonce ?? ''])
@stack('scripts')
</body>
</html>
