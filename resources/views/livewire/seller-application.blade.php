<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-black text-gray-900 mb-6">Become a Seller</h1>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">✓ {{ session('success') }}</div>
    @endif

    @php($status = $seller?->status)

    @if ($status === 'pending' && ! session('success'))
        <div class="rounded-2xl bg-white border border-gray-200 p-8 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-stargold-100 text-2xl">⏳</div>
            <h2 class="mt-4 text-lg font-bold text-gray-900">Application under review</h2>
            <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                Your store <span class="font-semibold">{{ $seller->store_name }}</span> is waiting for admin approval.
                You will receive an email as soon as it is approved.
            </p>
            <a href="{{ route('home') }}" wire:navigate class="mt-6 inline-block"><x-ui-button type="secondary">Back to shopping</x-ui-button></a>
        </div>
    @elseif ($status === 'rejected')
        <div class="rounded-2xl bg-white border border-red-200 p-6 mb-6">
            <p class="text-sm font-bold text-red-700">Your previous application was rejected.</p>
            <p class="mt-1 text-sm text-red-600/80">Reason: {{ $seller->rejection_reason ?? 'Not specified' }}</p>
            <p class="mt-2 text-sm text-gray-500">Update your details below and resubmit.</p>
        </div>
    @endif

    @if (! $status || $status === 'rejected')
        <form wire:submit="submit" class="rounded-2xl bg-white border border-gray-200 p-5 sm:p-6 space-y-4">
            <label class="block text-sm">
                <span class="mb-1 block font-medium text-gray-700">Store name</span>
                <input type="text" wire:model="storeName" placeholder="e.g. Ahmed Electronics" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500" />
                @error('storeName') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block text-sm">
                <span class="mb-1 block font-medium text-gray-700">About your store</span>
                <textarea wire:model="description" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500" placeholder="What do you sell?"></textarea>
                @error('description') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            </label>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Phone / WhatsApp</span>
                    <input type="text" wire:model="phone" placeholder="+961 70 123456" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500" />
                    @error('phone') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Governorate</span>
                    <select wire:model="governorate" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500">
                        <option value="">Select...</option>
                        @foreach ($governorates as $gov)
                            <option value="{{ $gov }}">{{ $gov }}</option>
                        @endforeach
                    </select>
                    @error('governorate') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>
            <div class="pt-2 flex items-center justify-between gap-4">
                <p class="text-[11px] text-gray-400">Commission starts at {{ \App\Services\SettingsService::globalCommissionRate() }}% per sale.</p>
                <x-ui-button type="primary" wire:loading.attr="disabled" wire:target="submit">Submit Application</x-ui-button>
            </div>
        </form>
    @endif

    @if ($status === 'suspended')
        <div class="rounded-2xl bg-white border border-red-200 p-6 text-sm text-red-700">
            Your store is suspended. Contact support@soukelkom.test.
        </div>
    @endif

    @if ($status === 'approved')
        <div class="rounded-2xl bg-white border border-emerald-200 p-8 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h2 class="mt-3 text-lg font-bold text-gray-900">You are an approved seller!</h2>
            <a href="{{ route('seller.dashboard') }}" wire:navigate class="mt-5 inline-block"><x-ui-button type="primary">Open Seller Hub →</x-ui-button></a>
        </div>
    @endif
</div>
