<div class="max-w-4xl">
    <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-6">Categories</h1>

    @if (session()->has('success'))
        <div class="mb-4 rounded-xl bg-emerald-900/50 border border-emerald-700/40 text-emerald-200 text-sm px-4 py-3">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 rounded-xl bg-red-900/50 border border-red-700/40 text-red-200 text-sm px-4 py-3">{{ session('error') }}</div>
    @endif

    <form wire:submit.prevent="addCategory" class="mb-8 rounded-2xl bg-white border border-gray-200 p-5 sm:p-6 shadow-sm">
        <h2 class="font-bold text-gray-900 mb-4">Add New Category</h2>
        <div class="grid gap-4 sm:grid-cols-3">
                <label class="block text-sm">
                    <span class="mb-1 block font-medium text-gray-700">Category Name</span>
                <input type="text" wire:model="newName" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-2 py-1.5 text-sm focus:ring-cosmic focus:border-cosmic placeholder:text-gray-400" placeholder="Luxury watches" />
                @error('newName') <span class="mt-1 block text-xs text-red-400">{{ $message }}</span> @enderror
            </label>
            <label class="block text-sm">
                <span class="mb-1 block font-medium text-gray-700">Icon (emoji or symbol)</span>
                <input type="text" wire:model="newIcon" maxlength="20" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 px-2 py-1.5 text-sm focus:ring-cosmic focus:border-cosmic placeholder:text-gray-400" placeholder="⌚" />
            </label>
        </div>
        <div class="mt-4">
            <x-ui-button type="primary">Add Category</x-ui-button>
        </div>
    </form>

    <section class="rounded-2xl bg-white border border-gray-200 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-widest">
                <tr>
                    <th class="text-left px-4 py-3 font-medium">Name</th>
                    <th class="text-left px-4 py-3 font-medium">Slug</th>
                    <th class="text-left px-4 py-3 font-medium">Icon</th>
                    <th class="text-right px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($categories as $cat)
                    <tr class="hover:bg-gray-50 transition">
                        @if ($editingId === $cat->id)
                        <td class="px-4 py-3">
                            <input type="text" wire:model.defer="editName" class="rounded-lg border border-gray-300 bg-white text-gray-900 px-2 py-1 w-full text-sm" />
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $cat->slug }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            <input type="text" wire:model.defer="editIcon" maxlength="20" class="rounded-lg border border-gray-300 bg-white text-gray-900 px-2 py-1 w-20 text-sm" />
                        </td>
                            <input type="text" wire:model.defer="editIcon" maxlength="20" class="rounded-lg border border-gray-300 bg-white text-gray-900 px-2 py-1 w-20 text-sm" />
                        </td>
                        <td class="text-right px-4 py-3 space-x-2">
                            <button wire:click="saveEdit" class="text-xs font-semibold text-cosmic hover:text-cosmic/80">Save</button>
                            <button wire:click="$set('editingId', null)" class="text-xs text-gray-500 hover:text-gray-700">Cancel</button>
                        </td>
                        @else
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $cat->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $cat->slug }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $cat->icon ?? '-' }}</td>
                        <td class="text-right px-4 py-3 space-x-2">
                            <button wire:click="edit({{ $cat->id }})" class="text-xs font-medium text-stargold-600 hover:text-stargold-700">Edit</button>
                            <button wire:click="deleteCategory({{ $cat->id }})" wire:confirm="Delete this category?" class="text-xs text-red-500 hover:text-red-600">Delete</button>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>