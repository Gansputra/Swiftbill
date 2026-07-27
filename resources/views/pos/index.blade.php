<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kasir (POS)') }}
        </h2>
    </x-slot>

    <div class="h-full relative">
        <livewire:pos.point-of-sale />
    </div>
</x-app-layout>
