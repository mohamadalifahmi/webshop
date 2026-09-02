<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-1">
        <form wire:submit="requestPayout" class="rounded-2xl bg-white border border-gray-200 p-5 sm:p-6 space-y-4 lg:sticky lg:top-24">
            <h2 class="font-bold text-gray-900">Request Payout</h2>
            <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                <p class="text-xs font-medium text-emerald-700">Available now</p>
                <p class="text-2xl font-black text-emerald-800">${{ number_format($available, 2) }}</p>
                <p class="mt-1 text-[11px] text-emerald-600">Minimum request: ${{ number_format($minPayout, 2) }} · one pending request at a time</p>
            </div>

            @if ($hasPending)
                <div class="rounded-lg bg-stargold-50 border border-stargold-200 px-4 py-3 text-xs text-stargold-700 font-medium">
                    ⏳ You already have a pending payout awaiting admin approval.
                </div>
            @else
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Amount ($)</span>
                    <input type="number" step="0.01" min="1" wire:model="amount" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500" />
                    @error('amount') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Bank name</span>
                    <input type="text" wire:model="bankName" placeholder="e.g. Byblos Bank" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500" />
                    @error('bankName') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">IBAN</span>
                    <input type="text" wire:model="iban" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500 font-mono" />
                    @error('iban') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
                <x-ui-button type="primary" class="w-full !py-3" wire:loading.attr="disabled" wire:target="requestPayout">Request Payout</x-ui-button>
            @endif
        </form>
    </div>

    <div class="lg:col-span-2">
        <h2 class="mb-4 font-bold text-gray-900">Payout History</h2>
        <div class="overflow-hidden rounded-2xl bg-white border border-gray-200">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-400">
                    <tr>
                        <th class="px-5 py-3">#</th>
                        <th class="px-5 py-3">Amount</th>
                        <th class="px-5 py-3 hidden sm:table-cell">Requested</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 hidden md:table-cell">Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($payouts as $payout)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 font-bold text-gray-900">{{ $payout->id }}</td>
                            <td class="px-5 py-4">${{ number_format((float) $payout->amount, 2) }}</td>
                            <td class="px-5 py-4 hidden sm:table-cell text-xs text-gray-400">{{ $payout->requested_at?->format('d M Y') }}</td>
                            <td class="px-5 py-4"><x-status-badge :status="$payout->status" /></td>
                            <td class="px-5 py-4 hidden md:table-cell text-xs text-gray-400 line-clamp-1">{{ $payout->admin_note ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state title="No payouts yet" description="Your withdrawal requests will be listed here." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $payouts->links() }}</div>
    </div>
</div>
