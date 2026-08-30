<div>
    <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-6">Manage Payouts</h1>

    @php($filters = ['pending' => 'Pending', 'paid' => 'Paid', 'rejected' => 'Rejected', '' => 'All'])
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach ($filters as $key => $label)
            <button wire:click="$set('statusFilter', '{{ $key }}')"
                class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ $statusFilter === $key ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-amber-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white border border-gray-200">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-400">
                <tr>
                    <th class="px-5 py-3">Seller</th>
                    <th class="px-5 py-3 hidden md:table-cell">Bank details</th>
                    <th class="px-5 py-3">Amount</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Process</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($payouts as $payout)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-5 py-4">
                            <p class="font-bold">{{ $payout->seller?->store_name }}</p>
                            <p class="text-[11px] text-gray-400">{{ $payout->seller?->user?->email }}</p>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell text-xs text-gray-500">
                            {{ $payout->bank_details['bank_name'] ?? '—' }}
                            <span class="block font-mono text-[11px]">{{ $payout->bank_details['iban'] ?? '' }}</span>
                        </td>
                        <td class="px-5 py-4 font-black">${{ number_format((float) $payout->amount, 2) }}</td>
                        <td class="px-5 py-4"><x-status-badge :status="$payout->status" /></td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap justify-end gap-2 text-xs font-semibold">
                                @if ($payout->status === 'pending')
                                    <button wire:click="markPaid({{ $payout->id }})" wire:confirm="Confirm you sent this transfer?" wire:loading.attr="disabled" wire:target="markPaid"
                                        class="rounded-lg bg-emerald-600 px-3.5 py-2 text-white hover:bg-emerald-700">✓ Mark as Paid</button>
                                    <button wire:click="openRejectModal({{ $payout->id }})"
                                        class="rounded-lg bg-red-50 px-3.5 py-2 text-red-600 hover:bg-red-100">Reject</button>
                                @else
                                    <span class="text-gray-300 text-[11px]">{{ optional($payout->processed_at)->format('d M Y') }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state title="No payouts in this state" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $payouts->links() }}</div>

    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showRejectModal', false)"></div>
            <form wire:submit="confirmReject" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-bold text-gray-900">Reject Payout</h2>
                <textarea wire:model="adminNote" rows="3" autofocus placeholder="Reason shown to the seller..."
                    class="mt-4 w-full rounded-lg border-gray-300 focus:ring-amber-500"></textarea>
                @error('adminNote') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                <div class="mt-5 flex gap-3 justify-end">
                    <button type="button" wire:click="$set('showRejectModal', false)" class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-800">Cancel</button>
                    <x-ui-button type="danger">Confirm Rejection</x-ui-button>
                </div>
            </form>
        </div>
    @endif
</div>
