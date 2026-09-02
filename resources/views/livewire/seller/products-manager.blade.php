<div>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-xl sm:text-2xl font-black text-gray-900">My Products</h1>
        <button wire:click="create" class="inline-flex items-center rounded-lg bg-stargold-500 text-deep px-4 py-2 text-sm font-semibold text-white hover:bg-stargold-700 transition">+ New Product</button>
    </div>

    @php($filters = ['' => 'All', 'draft' => 'Draft', 'pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected'])
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach ($filters as $key => $label)
            <button wire:click="$set('statusFilter', '{{ $key }}')" wire:loading.attr="disabled"
                class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ $statusFilter === $key ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-stargold-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    @if ($showForm)
        <form wire:submit="save" class="mb-6 rounded-2xl bg-white border-2 border-stargold-400/60 p-5 sm:p-6 space-y-4 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-gray-900">{{ $editingId ? 'Edit Product' : 'New Product' }}</h2>
                <span class="text-[11px] text-gray-400">Submitting sets status to <b>pending</b> for admin review.</span>
            </div>

            <label class="block text-sm">
                <span class="mb-1 block font-medium text-gray-700">Name</span>
                <input type="text" wire:model="name" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500" />
                @error('name') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            </label>

            <label class="block text-sm">
                <span class="mb-1 block font-medium text-gray-700">Description</span>
                <textarea wire:model="description" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500"></textarea>
                @error('description') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
            </label>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Price ($)</span>
                    <input type="number" step="0.01" min="0.01" wire:model="price" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500" />
                    @error('price') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Stock</span>
                    <input type="number" min="0" wire:model="stock" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500" />
                    @error('stock') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Category</span>
                    <select wire:model="categoryId" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500">
                        <option value="">Select...</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">SKU (optional)</span>
                    <input type="text" wire:model="sku" placeholder="auto-generated" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500" />
                    @error('sku') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>

            <label class="block text-sm">
                <span class="mb-1 block font-medium text-gray-700">Images (up to 6) — required for approval</span>
                <input type="file" wire:model="images" multiple accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-white file:text-xs file:font-semibold hover:file:bg-gray-700" />
                @error('images.*') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                @error('images') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                <div wire:loading wire:target="images" class="mt-2 text-xs text-stargold-600 animate-pulse">Uploading…</div>
                @if ($images)
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($images as $img)
                            <img src="{{ $img->temporaryUrl() }}" class="h-16 w-16 rounded-lg object-cover border border-gray-200" />
                        @endforeach
                    </div>
                @endif
            </label>

            <div class="flex gap-3 pt-1">
                <x-ui-button type="primary">{{ $editingId ? 'Update & Resubmit' : 'Submit for Approval' }}</x-ui-button>
                <button type="button" wire:click="$set('showForm', false)" class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    @endif

    <div class="overflow-hidden rounded-2xl bg-white border border-gray-200">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-400">
                <tr>
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3 hidden md:table-cell">Price / Stock</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-11 w-11 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                    @if ($img = $product->getFirstMediaUrl('images', 'webp-thumb') ?: $product->getFirstMediaUrl('images'))
                                        <img src="{{ $img }}" class="h-full w-full object-cover" />
                                    @else
                                        <div class="flex h-full items-center justify-center text-gray-300"><x-icon-package class="h-6 w-6" /></div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 line-clamp-1">{{ $product->name }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $product->sku }} · {{ $product->category?->name }}</p>
                                    @if ($product->status === 'rejected')
                                        <p class="text-[11px] text-red-500 mt-0.5">✕ {{ $product->rejection_reason }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <p class="font-bold">${{ number_format((float) $product->price, 2) }}</p>
                            <p class="text-xs {{ $product->stock > 0 ? 'text-gray-400' : 'text-red-500 font-bold' }}">{{ $product->stock }} in stock</p>
                        </td>
                        <td class="px-5 py-4"><x-status-badge :status="$product->status" /></td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2 text-xs font-semibold">
                                <button wire:click="edit({{ $product->id }})" class="rounded-lg bg-gray-100 px-3 py-1.5 text-gray-700 hover:bg-gray-200">Edit</button>
                                <button wire:click="delete({{ $product->id }})" wire:confirm="Delete this product permanently?"
                                    class="rounded-lg bg-red-50 px-3 py-1.5 text-red-600 hover:bg-red-100">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4"><x-empty-state title="No products yet" description="Create your first product and submit it for approval." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</div>
