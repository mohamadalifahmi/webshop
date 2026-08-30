@props([
    'type' => 'primary',
])

@php
    $map = [
        'primary' => 'bg-amber-600 hover:bg-amber-700 text-white focus-visible:ring-amber-500',
        'dark' => 'bg-gray-800 hover:bg-gray-900 text-white focus-visible:ring-gray-700',
        'secondary' => 'bg-gray-100 hover:bg-gray-200 text-gray-800 focus-visible:ring-gray-300',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white focus-visible:ring-red-500',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white focus-visible:ring-emerald-500',
    ];
@endphp

<button {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 '.$map[$type]]) }}>
    {{ $slot }}
</button>
