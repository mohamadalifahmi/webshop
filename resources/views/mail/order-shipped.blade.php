<x-mail::message>
# Your order shipped!

**{{ $item->product_name }} x{{ $item->quantity }}** is on its way.

**Tracking number:** `{{ $item->tracking_number }}`

You can follow your shipment using the tracking number above with the carrier.

<x-mail::button :url="url('/account/orders')">
My Orders
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
