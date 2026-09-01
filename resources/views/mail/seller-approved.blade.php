<x-mail::message>
# Congratulations, {{ $seller->store_name }} is approved!

Your store is now live on ASTRAGO MARKET. You can start listing products and selling right away.

<x-mail::button :url="url('/seller')">
Go to Seller Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
