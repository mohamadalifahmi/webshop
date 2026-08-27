<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SOUKELKOM') }} — {{ __('Sign in') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛒</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">
<div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
    <a href="{{ route('home') }}" wire:navigate class="mb-8 flex items-center gap-2.5">
        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-600 text-2xl font-black text-white shadow-lg shadow-amber-600/30">S</span>
        <span class="text-2xl font-black tracking-tight text-gray-900">SOUK<span class="text-amber-600">ELKOM</span></span>
    </a>

    <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
        {{ $slot }}
    </div>

    <p class="mt-6 text-center text-xs text-gray-400">The Local Marketplace Where Everyone Wins · Lebanon & MENA</p>
</div>
@stack('scripts')
</body>
</html>
