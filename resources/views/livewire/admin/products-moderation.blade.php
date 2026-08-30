<div>
    <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-6">Manage Products</h1>

    @php($filters = ['pending' => 'Pending Review', 'active' => 'Active', 'rejected' => 'Rejected', 'draft' => 'Draft', '' => 'All'])
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
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3 hidden md:table-cell">Seller</th>
                    <th class="px-5 py-3 hidden sm:table-cell">Price</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Moderate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($products as $product)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                    @if ($img = $product->getFirstMediaUrl('images', 'thumb') ?: $product->getFirstMediaUrl('images'))
                                        <img src="{{ $img }}" class="h-full w-full object-cover" />
                                    @else
                                        <div class="flex h-full items-center justify-center">📦</div>
                                    @endif
                                </div>
                                <div class="min-w-0 max-w-56">
                                    <p class="line-clamp-1 font-semibold text-gray-900">{{ $product->name }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $product->category?->name }} · {{ $product->sku }}</p>
                                    @if ($product->status === 'rejected')
                                        <p class="mt-0.5 line-clamp-1 text-[11px] text-red-400">{{ $product->rejection_reason }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell text-xs text-gray-500">{{ $product->seller?->store_name }}</td>
                        <td class="px-5 py-4 hidden sm:table-cell font-bold">${{ number_format((float) $product->price, 2) }}</td>
                        <td class="px-5 py-4"><x-status-badge :status="$product->status" /></td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap justify-end gap-2 text-xs font-semibold">
                                @if (in_array($product->status, ['pending', 'rejected']))
                                    <button wire:click="approve({{ $product->id }})"
                                        class="rounded-lg bg-emerald-600 px-3.5 py-2 text-white hover:bg-emerald-700">✓ Approve</button>
                                    <button wire:click="openRejectModal({{ $product->id }})"
                                        class="rounded-lg bg-red-50 px-3.5 py-2 text-red-600 hover:bg-red-100">Reject</button>
                                @elseif ($product->status === 'active')
                                    <button wire:click="openRejectModal({{ $product->id }})" wire:confirm="Unpublish this product?"
                                        class="rounded-lg bg-gray-100 px-3.5 py-2 text-gray-600 hover:bg-gray-200">Unpublish</button>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state title="Nothing here" description="No products in this state." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>

    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="absolute inset-0 bg-black/50" wire:click="$set('showRejectModal', false)"></div>
            <form wire:submit="confirmReject" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-bold text-gray-900">Rejection Reason</h2>
                <p class="mt-1 text-xs text-gray-400">The seller receives this by email.</p>
                <textarea wire:model="rejectionReason" rows="3" autofocus placeholder="e.g. No image attached..."
                    class="mt-4 w-full rounded-lg border-gray-300 focus:ring-amber-500"></textarea>
                @error('rejectionReason') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                <div class="mt-5 flex gap-3 justify-end">
                    <button type="button" wire:click="$set('showRejectModal', false)" class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-800">Cancel</button>
                    <x-ui-button type="danger">Confirm Rejection</x-ui-button>
                </div>
            </form>
        </div>
    @endif
</div>
