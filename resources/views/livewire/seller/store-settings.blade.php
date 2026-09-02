<div class="max-w-2xl">
    <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-6">Store Settings</h1>

    <form wire:submit="save" class="rounded-2xl bg-white border border-gray-200 p-5 sm:p-6 space-y-4">
        <label class="block text-sm">
            <span class="mb-1 block font-medium text-gray-700">Store name</span>
            <input type="text" wire:model="storeName" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500" />
            @error('storeName') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
        </label>
        <label class="block text-sm">
            <span class="mb-1 block font-medium text-gray-700">Description</span>
            <textarea wire:model="description" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500"></textarea>
        </label>
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block text-sm">
                <span class="mb-1 block font-medium text-gray-700">Phone / WhatsApp</span>
                <input type="text" wire:model="phone" class="w-full rounded-lg border-gray-300 focus:ring-stargold-500" />
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
        <x-ui-button type="primary">Save Changes</x-ui-button>
    </form>
</div>
