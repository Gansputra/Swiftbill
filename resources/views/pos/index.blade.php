<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Point of Sale') }}
        </h2>
    </x-slot>

    <div class="h-screen bg-gray-100 dark:bg-gray-900 border-t dark:border-gray-700">
        <livewire:pos.point-of-sale />
    </div>
</x-app-layout>
