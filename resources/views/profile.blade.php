<x-app-layout title="My Account">
    <div class="mx-auto max-w-3xl">
        <h1 class="mb-6 text-xl font-black text-gray-900 sm:text-2xl">My Account</h1>

        <div class="space-y-5">
            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-7">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-gray-400">Profile information</h2>
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-7">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-gray-400">Update password</h2>
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </section>

            <section class="rounded-2xl border border-red-200 bg-white p-5 sm:p-7">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-red-400">Danger zone</h2>
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
