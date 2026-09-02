<div>
    <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-6">Manage Sellers</h1>

    @php($filters = ['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'suspended' => 'Suspended'])
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach ($filters as $key => $label)
            <button wire:click="$set('statusFilter', '{{ $key }}')"
                class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ $statusFilter === $key ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-stargold-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white border border-gray-200">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-400">
                <tr>
                    <th class="px-5 py-3">Store</th>
                    <th class="px-5 py-3 hidden md:table-cell">Owner</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 hidden lg:table-cell">Commission %</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($sellers as $seller)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-5 py-4">
                            <p class="font-bold text-gray-900">{{ $seller->store_name }}</p>
                            <p class="text-[11px] text-gray-400">{{ $seller->governorate ?? '' }} · balance ${{ number_format((float) $seller->balance, 2) }}</p>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell text-xs text-gray-500">
                            <p class="font-medium text-gray-700">{{ $seller->user?->name }}</p>
                            <p>{{ $seller->user?->email }}</p>
                            <p>{{ $seller->phone }}</p>
                        </td>
                        <td class="px-5 py-4"><x-status-badge :status="$seller->status" /></td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <div class="flex items-center gap-1.5">
                                <input type="number" min="0" max="100" step="0.01"
                                    wire:model.live.debounce.500ms="overrides.{{ $seller->id }}"
                                    placeholder="{{ $globalRate }} (global)"
                                    value="{{ $seller->commission_override ?? '' }}"
                                    class="w-24 rounded-lg border-gray-300 focus:ring-stargold-500 text-xs" />
                                <button wire:click="saveOverride({{ $seller->id }})" class="rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-bold hover:bg-gray-200">Save</button>
                            </div>
                            @if ($seller->commission_override !== null)
                                <p class="mt-1 text-[10px] font-bold text-emerald-600">Override active: {{ $seller->commission_override }}%</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap justify-end gap-2 text-xs font-semibold">
                                @if ($seller->status === 'pending')
                                    <button wire:click="approve({{ $seller->id }})" wire:loading.attr="disabled" wire:target="approve"
                                        class="rounded-lg bg-emerald-600 px-3.5 py-2 text-white hover:bg-emerald-700">✓ Approve</button>
                                    <button wire:click="openRejectModal({{ $seller->id }})"
                                        class="rounded-lg bg-red-50 px-3.5 py-2 text-red-600 hover:bg-red-100">Reject</button>
                                @elseif ($seller->status === 'approved')
                                    <button wire:click="toggleSuspend({{ $seller->id }})" wire:confirm="Suspend this seller?"
                                        class="rounded-lg bg-stargold-50 px-3.5 py-2 text-stargold-700 hover:bg-stargold-100">Suspend</button>
                                @elseif ($seller->status === 'suspended')
                                    <button wire:click="toggleSuspend({{ $seller->id }})"
                                        class="rounded-lg bg-emerald-600 px-3.5 py-2 text-white hover:bg-emerald-700">Reactivate</button>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state title="No sellers in this state" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $sellers->links() }}</div>

    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showRejectModal', false)"></div>
            <form wire:submit="confirmReject" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-bold text-gray-900">Rejection Reason</h2>
                <p class="mt-1 text-xs text-gray-400">The seller will see this reason.</p>
                <textarea wire:model="rejectionReason" rows="3" autofocus placeholder="e.g. Missing business documents..."
                    class="mt-4 w-full rounded-lg border-gray-300 focus:ring-stargold-500"></textarea>
                @error('rejectionReason') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                <div class="mt-5 flex gap-3 justify-end">
                    <button type="button" wire:click="$set('showRejectModal', false)" class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-800">Cancel</button>
                    <x-ui-button type="danger">Confirm Rejection</x-ui-button>
                </div>
            </form>
        </div>
    @endif
</div>
