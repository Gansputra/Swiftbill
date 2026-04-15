<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">
            {{ __('Account Core') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
            <!-- Profile Information (Bento Style) -->
            <div class="p-2">
                <livewire:profile.update-profile-information-form />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Security Settings -->
                <div class="p-10 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[3rem] shadow-sm">
                    <livewire:profile.update-password-form />
                </div>

                <!-- Danger Zone -->
                <div class="p-10 bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 rounded-[3rem] shadow-sm">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
