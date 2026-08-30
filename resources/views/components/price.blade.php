@props(['amount'])

@php
    $currency = session('currency', 'USD');
@endphp

<span class="inline-flex flex-col leading-tight">
    <span class="font-semibold text-gray-900">
        {{ $currency === 'USD' ? \App\Support\MoneyFormat::usd($amount) : \App\Support\MoneyFormat::lbp($amount) }}
    </span>
    <span class="text-xs text-gray-400">
        {{ $currency === 'USD' ? \App\Support\MoneyFormat::lbp($amount) : \App\Support\MoneyFormat::usd($amount) }}
    </span>
</span>
