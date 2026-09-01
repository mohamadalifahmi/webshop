<div class="max-w-3xl">
    <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-6">Site Settings</h1>

    <form wire:submit="save" class="space-y-6">
        <section class="rounded-2xl bg-white border border-gray-200 p-5 sm:p-6">
            <h2 class="font-bold text-gray-900 mb-4">Money Rules</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Global commission %</span>
                    <input type="number" min="0" max="100" step="0.01" wire:model="globalCommissionRate" class="w-full rounded-lg border-gray-300 focus:ring-amber-500" />
                    @error('globalCommissionRate') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Min payout ($)</span>
                    <input type="number" min="0" step="0.01" wire:model="minPayout" class="w-full rounded-lg border-gray-300 focus:ring-amber-500" />
                    @error('minPayout') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">LBP exchange rate</span>
                    <input type="number" min="1" step="0.5" wire:model="lbpExchangeRate" class="w-full rounded-lg border-gray-300 focus:ring-amber-500" />
                    @error('lbpExchangeRate') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-gray-200 p-5 sm:p-6">
            <h2 class="font-bold text-gray-900 mb-4">Shipping & Escrow</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Ship deadline (hours)</span>
                    <input type="number" min="1" wire:model="shipDeadlineHours" class="w-full rounded-lg border-gray-300 focus:ring-amber-500" />
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Hold after delivery (days)</span>
                    <input type="number" min="0" wire:model="holdDaysAfterDelivery" class="w-full rounded-lg border-gray-300 focus:ring-amber-500" />
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Default shipping fee ($)</span>
                    <input type="number" min="0" step="0.01" wire:model="defaultShippingFee" class="w-full rounded-lg border-gray-300 focus:ring-amber-500" />
                </label>
            </div>
            @error('shipDeadlineHours') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            @error('holdDaysAfterDelivery') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
        </section>

        <section class="rounded-2xl bg-white border border-gray-200 p-5 sm:p-6">
            <h2 class="font-bold text-gray-900 mb-4">Shipping Rates by Governorate</h2>

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($rates as $i => $rate)
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-gray-400">
                            Governorate #{{ $i + 1 }}
                        </label>
                        <input type="text" wire:model.debounce.400ms="rates.{{ $i }}.governorate"
                            value="{{ $rate['governorate'] }}"
                            class="w-full rounded-lg border-gray-300 bg-white text-sm font-semibold text-gray-800 focus:border-amber-500 focus:ring-amber-500" />
                        @error("rates.{$i}.governorate") <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror

                        <label class="mb-1 mt-2.5 block text-[10px] font-bold uppercase tracking-widest text-gray-400">
                            Shipping Fee ($)
                        </label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-gray-400">$</span>
                            <input type="number" step="0.01" min="0" wire:model.debounce.400ms="rates.{{ $i }}.fee"
                                value="{{ $rate['fee'] }}"
                                class="w-full rounded-lg border-gray-300 bg-white pl-7 text-sm font-semibold text-gray-800 focus:border-amber-500 focus:ring-amber-500" />
                        </div>
                        @error("rates.{$i}.fee") <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                @endforeach
            </div>
        </section>

        <x-ui-button type="primary" class="!px-8 !py-3">Save All Settings</x-ui-button>
    </form>
</div>
