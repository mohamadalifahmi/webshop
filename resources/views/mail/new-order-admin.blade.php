<x-mail::message>
# New order received: {{ $order->order_number }}

A buyer just placed an order on SOUKELKOM.

| Item | Qty | Subtotal |
|------|-----|----------|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | ${{ number_format((float) $item->subtotal, 2) }} |
@endforeach

**Order total:** ${{ number_format((float) $order->total, 2) }} (shipping: ${{ number_format((float) $order->shipping_fee, 2) }})

<x-mail::button :url="url('/admin/orders')">
Open Admin Orders
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
