@props([
    'type' => 'primary',
])

@php
    $map = [
        'primary' => 'bg-cosmic-500 hover:bg-cosmic-600 text-white focus-visible:ring-cosmic-500 shadow-lg shadow-cosmic-500/20',
        'dark' => 'bg-space-700 hover:bg-space-800 text-white focus-visible:ring-white/40',
        'secondary' => 'bg-white/10 hover:bg-white/15 text-white focus-visible:ring-white/30',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white focus-visible:ring-red-500',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white focus-visible:ring-emerald-500',
    ];
@endphp

<button {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-deep disabled:opacity-50 '.$map[$type]]) }}>
    {{ $slot }}
</button>
