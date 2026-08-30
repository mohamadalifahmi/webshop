<x-mail::message>
# Item cancelled & refund issued

Unfortunately, the seller did not ship **{{ $item->product_name }} x{{ $item->quantity }}** within the allowed time window, so this item was cancelled automatically from order **{{ $item->order->order_number }}**.

Your refund of **${{ number_format((float) ($item->unit_price * $item->quantity), 2) }}** will be processed back to your original payment method.

We are sorry for the inconvenience.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
