@props([
    'title',
    'value',
    'sub' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 bg-white p-5 shadow-sm']) }}>
    <div class="flex items-center justify-between">
        <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
        @if ($icon)
            <span class="text-amber-500">{{ $icon }}</span>
        @endif
    </div>
    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $value }}</p>
    @if ($sub)
        <p class="mt-1 text-xs text-gray-400">{{ $sub }}</p>
    @endif
</div>
