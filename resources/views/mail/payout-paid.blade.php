<x-mail::message>
# Payout sent: ${{ number_format((float) $payout->amount, 2) }}

Your payout request #{{ $payout->id }} has been processed and sent via {{ $payout->method }}.

**Amount:** ${{ number_format((float) $payout->amount, 2) }}
**Processed at:** {{ $payout->processed_at?->format('Y-m-d H:i') }}

The amount should reach your bank account shortly.

<x-mail::button :url="url('/seller/payouts')">
Payout History
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
