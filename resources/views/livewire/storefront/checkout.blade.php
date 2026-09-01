<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="font-serif text-2xl sm:text-3xl font-bold text-white mb-6">Checkout</h1>

    <form wire:submit="placeOrder" class="grid gap-6 lg:grid-cols-5">
        <!-- Left: details -->
        <div class="lg:col-span-3 space-y-4 rounded-2xl glass p-5 sm:p-6">
            <h2 class="font-bold text-white">Shipping Details</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-white/60">Full name</span>
                    <input type="text" wire:model="name" class="w-full rounded-xl input-cosmic" />
                    @error('name') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-white/60">Phone</span>
                    <input type="text" wire:model="phone" placeholder="+961 ..." class="w-full rounded-xl input-cosmic" />
                    @error('phone') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                </label>
            </div>
            <label class="block text-sm">
                <span class="mb-1 block font-medium text-white/60">Governorate</span>
                <select wire:model.live="governorate" class="w-full rounded-xl input-cosmic">
                    <option value="">Select governorate...</option>
                    @foreach ($governorates as $gov)
                        <option value="{{ $gov }}">{{ $gov }}</option>
                    @endforeach
                </select>
                @error('governorate') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
            </label>
            <label class="block text-sm">
                <span class="mb-1 block font-medium text-white/60">Address</span>
                <textarea wire:model="address" rows="2" class="w-full rounded-xl input-cosmic" placeholder="Street, building, floor..."></textarea>
                @error('address') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
            </label>

            <h2 class="pt-4 font-bold text-white border-t border-white/5">Payment Method</h2>
            <div class="space-y-3">
                <label class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition {{ $paymentMethod === 'manual' ? 'border-cosmic-500 bg-cosmic-500/10' : 'border-white/10' }}">
                    <input type="radio" wire:model.live="paymentMethod" value="manual" class="mt-1 text-cosmic-500 focus:ring-cosmic-500 bg-space-800 border-white/20" />
                    <div>
                        <p class="font-semibold text-sm text-white/85">Bank Transfer / OMT / Whish</p>
                        <p class="text-xs text-white/40 mt-0.5">Upload your transfer proof — admin confirms within hours.</p>
                    </div>
                </label>

                @if (config('services.stripe.key'))
                    <label class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition {{ $paymentMethod === 'stripe' ? 'border-cosmic-500 bg-cosmic-500/10' : 'border-white/10' }}">
                        <input type="radio" wire:model.live="paymentMethod" value="stripe" class="mt-1 text-cosmic-500 focus:ring-cosmic-500 bg-space-800 border-white/20" />
                        <div>
                            <p class="font-semibold text-sm text-white/85">Card — Stripe Secure</p>
                            <p class="text-xs text-white/40 mt-0.5">Pay instantly with your card. Money auto-split to sellers.</p>
                        </div>
                    </label>
                @endif
            </div>

            @if ($paymentMethod === 'manual')
                <label class="block text-sm rounded-xl border border-dashed border-white/15 p-4">
                    <span class="mb-2 block font-medium text-white/60">Transfer proof (screenshot/photo)</span>
                    <input type="file" wire:model="proof" accept="image/*" class="block w-full text-sm text-white/50 file:mr-3 file:rounded-lg file:border-0 file:bg-cosmic-500 file:px-4 file:py-2 file:text-white file:text-xs file:font-semibold hover:file:bg-cosmic-600" />
                    @error('proof') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="proof" class="mt-2 text-xs text-cosmic-400">Uploading...</div>
                </label>
            @endif

            <label class="block text-sm pt-2">
                <span class="mb-1 block font-medium text-white/60">Note for sellers (optional)</span>
                <input type="text" wire:model="note" class="w-full rounded-xl input-cosmic" />
            </label>
        </div>

        <!-- Right: summary -->
        <aside class="lg:col-span-2 h-fit rounded-2xl glass p-5 sm:p-6 lg:sticky lg:top-24">
            <h2 class="font-bold text-white mb-4">Order Summary</h2>
            <ul class="space-y-3 text-sm max-h-56 overflow-y-auto pr-1">
                @foreach ($items as $item)
                    @if ($item->product)
                        <li class="flex justify-between gap-3">
                            <span class="text-white/50 line-clamp-1">{{ $item->product->name }} × {{ $item->quantity }}</span>
                            <span class="font-medium text-white/80 whitespace-nowrap">${{ number_format((float) ($item->product->price * $item->quantity), 2) }}</span>
                        </li>
                    @endif
                @endforeach
            </ul>
            <dl class="mt-4 space-y-2 border-t border-white/5 pt-4 text-sm">
                <div class="flex justify-between"><dt class="text-white/40">Subtotal</dt><dd class="text-white/80">${{ number_format($subtotal, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-white/40">Shipping {{ $governorate ? "to {$governorate}" : '' }}</dt><dd class="text-white/80">{{ $shippingFee > 0 ? '$'.number_format($shippingFee, 2) : ($governorate ? 'FREE' : '—') }}</dd></div>
                <div class="flex justify-between border-t border-white/5 pt-2 text-base font-black text-white">
                    <dt>Total</dt><dd>$ {{ number_format($subtotal + $shippingFee, 2) }}</dd></div>
                @php($rate = \App\Services\SettingsService::lbpExchangeRate())
                <p class="text-[11px] text-right text-white/30">≈ LBP {{ number_format((int) round(($subtotal + $shippingFee) * $rate)) }}</p>
            </dl>

            <x-ui-button type="primary" class="mt-5 w-full !py-3.5" wire:loading.attr="disabled" wire:target="placeOrder">
                <span wire:loading.remove wire:target="placeOrder">Place Order · Pay Once</span>
                <span wire:loading wire:target="placeOrder">Processing…</span>
            </x-ui-button>
            <p class="mt-3 text-center text-[11px] text-white/30">One payment for all sellers. We split it automatically.</p>
        </aside>
    </form>
</div>
