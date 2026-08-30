<x-mail::message>
# Product rejected: {{ $product->name }}

Our team reviewed your product and rejected it.

**Reason:** {{ $product->rejection_reason ?? 'Not specified' }}

You can edit the product and resubmit it for approval from your dashboard.

<x-mail::button :url="url('/seller/products')">
My Products
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
