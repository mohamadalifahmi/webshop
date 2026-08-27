@props([
    'status',
])

@php
    $map = [
        'draft' => 'bg-gray-100 text-gray-600 ring-gray-200',
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'pending_payment' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'awaiting_proof' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'under_review' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'awaiting' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'shipped' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'delivered' => 'bg-teal-50 text-teal-700 ring-teal-200',
        'completed' => 'bg-teal-50 text-teal-700 ring-teal-200',
        'rejected' => 'bg-red-50 text-red-700 ring-red-200',
        'suspended' => 'bg-red-50 text-red-700 ring-red-200',
        'cancelled' => 'bg-red-50 text-red-700 ring-red-200',
        'refunded' => 'bg-purple-50 text-purple-700 ring-purple-200',
    ];
    $classes = $map[$status] ?? 'bg-gray-100 text-gray-600 ring-gray-200';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset uppercase tracking-wide '.$classes]) }}>
    {{ str_replace('_', ' ', $status) }}
</span>
