<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-black text-gray-900 mb-6">Checkout</h1>

    <form wire:submit="placeOrder" class="grid gap-6 lg:grid-cols-5">
        <!-- Left: details -->
        <div class="lg:col-span-3 space-y-4 rounded-2xl bg-white border border-gray-200 p-5 sm:p-6">
            <h2 class="font-bold text-gray-900">Shipping Details</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Full name</span>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-gray-300 focus:ring-amber-500" />
                    @error('name') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Phone</span>
                    <input type="text" wire:model="phone" placeholder="+961 ..." class="w-full rounded-lg border-gray-300 focus:ring-amber-500" />
                    @error('phone') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>
            <label class="block text-sm">
                <span class="mb-1 block font-medium text-gray-700">Governorate</span>
                <select wire:model.live="governorate" class="w-full rounded-lg border-gray-300 focus:ring-amber-500">
                    <option value="">Select governorate...</option>
                    @foreach ($governorates as $gov)
                        <option value="{{ $gov }}">{{ $gov }}</option>
                    @endforeach
                </select>
                @error('governorate') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block text-sm">
                <span class="mb-1 block font-medium text-gray-700">Address</span>
                <textarea wire:model="address" rows="2" class="w-full rounded-lg border-gray-300 focus:ring-amber-500" placeholder="Street, building, floor..."></textarea>
                @error('address') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            </label>

            <h2 class="pt-4 font-bold text-gray-900 border-t border-gray-100">Payment Method</h2>
            <div class="space-y-3">
                <label class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition {{ $paymentMethod === 'manual' ? 'border-amber-500 bg-amber-50' : 'border-gray-200' }}">
                    <input type="radio" wire:model.live="paymentMethod" value="manual" class="mt-1 text-amber-600 focus:ring-amber-500" />
                    <div>
                        <p class="font-semibold text-sm text-gray-900">Bank Transfer / OMT / Whish</p>
                        <p class="text-xs text-gray-500 mt-0.5">Upload your transfer proof — admin confirms within hours.</p>
                    </div>
                </label>

                @if (config('services.stripe.key'))
                    <label class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition {{ $paymentMethod === 'stripe' ? 'border-amber-500 bg-amber-50' : 'border-gray-200' }}">
                        <input type="radio" wire:model.live="paymentMethod" value="stripe" class="mt-1 text-amber-600 focus:ring-amber-500" />
                        <div>
                            <p class="font-semibold text-sm text-gray-900">Card — Stripe Secure</p>
                            <p class="text-xs text-gray-500 mt-0.5">Pay instantly with your card. Money auto-split to sellers.</p>
                        </div>
                    </label>
                @endif
            </div>

            @if ($paymentMethod === 'manual')
                <label class="block text-sm rounded-xl border border-dashed border-gray-300 p-4">
                    <span class="mb-2 block font-medium text-gray-700">Transfer proof (screenshot/photo)</span>
                    <input type="file" wire:model="proof" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-white file:text-xs file:font-semibold hover:file:bg-gray-700" />
                    @error('proof') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="proof" class="mt-2 text-xs text-amber-600">Uploading...</div>
                </label>
            @endif

            <label class="block text-sm pt-2">
                <span class="mb-1 block font-medium text-gray-700">Note for sellers (optional)</span>
                <input type="text" wire:model="note" class="w-full rounded-lg border-gray-300 focus:ring-amber-500" />
            </label>
        </div>

        <!-- Right: summary -->
        <aside class="lg:col-span-2 h-fit rounded-2xl bg-white border border-gray-200 p-5 sm:p-6 lg:sticky lg:top-24">
            <h2 class="font-bold text-gray-900 mb-4">Order Summary</h2>
            <ul class="space-y-3 text-sm max-h-56 overflow-y-auto pr-1">
                @foreach ($items as $item)
                    <li class="flex justify-between gap-3">
                        <span class="text-gray-600 line-clamp-1">{{ $item->product->name }} × {{ $item->quantity }}</span>
                        <span class="font-medium whitespace-nowrap">${{ number_format((float) ($item->product->price * $item->quantity), 2) }}</span>
                    </li>
                @endforeach
            </ul>
            <dl class="mt-4 space-y-2 border-t border-gray-100 pt-4 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Subtotal</dt><dd>${{ number_format($subtotal, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Shipping {{ $governorate ? "to {$governorate}" : '' }}</dt><dd>{{ $shippingFee > 0 ? '$'.number_format($shippingFee, 2) : ($governorate ? 'FREE' : '—') }}</dd></div>
                <div class="flex justify-between border-t border-gray-100 pt-2 text-base font-black text-gray-900">
                    <dt>Total</dt><dd>$ {{ number_format($subtotal + $shippingFee, 2) }}</dd></div>
                @php($rate = \App\Services\SettingsService::lbpExchangeRate())
                <p class="text-[11px] text-right text-gray-400">≈ LBP {{ number_format((int) round(($subtotal + $shippingFee) * $rate)) }}</p>
            </dl>

            <x-ui-button type="primary" class="mt-5 w-full !py-3.5" wire:loading.attr="disabled" wire:target="placeOrder">
                <span wire:loading.remove wire:target="placeOrder">Place Order · Pay Once</span>
                <span wire:loading wire:target="placeOrder">Processing…</span>
            </x-ui-button>
            <p class="mt-3 text-center text-[11px] text-gray-400">One payment for all sellers. We split it automatically.</p>
        </aside>
    </form>
</div>
