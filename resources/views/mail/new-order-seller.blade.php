<x-mail::message>
# New order to ship: {{ $order->order_number }}

You have new items to ship. Please mark them as shipped within **{{ \App\Services\SettingsService::shipDeadlineHours() }} hours**, otherwise the items are auto-cancelled and the buyer is refunded.

| Item | Qty | Your earning |
|------|-----|--------------|
@foreach($items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | ${{ number_format((float) $item->seller_earning, 2) }} |
@endforeach

**Ship to:** {{ $order->ship_to_name }}, {{ $order->address }}, {{ $order->governorate }} — Phone: {{ $order->ship_to_phone }}

<x-mail::button :url="url('/seller/orders')">
Ship It Now
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
