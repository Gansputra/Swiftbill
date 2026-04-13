<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <livewire:dashboard.overview />
            
            <div class="bg-gray-50 dark:bg-gray-900 p-8 rounded-3xl border dark:border-gray-800">
                <h2 class="text-3xl font-extrabold mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-indigo-500 to-purple-600 text-transparent bg-clip-text">Business Intelligence AI</span>
                    <span class="ml-3 text-xs uppercase bg-white dark:bg-gray-800 px-2 py-1 rounded-full border dark:border-gray-700">Beta</span>
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    <div class="md:col-span-8">
                        <livewire:ai.assistant />
                    </div>
                    <div class="md:col-span-4 bg-white dark:bg-gray-800 p-6 rounded-xl border dark:border-gray-700 shadow-sm">
                        <h4 class="font-bold mb-4">Try asking:</h4>
                        <ul class="space-y-3 text-sm text-gray-500">
                            <li>"What are my best-selling products?"</li>
                            <li>"Is my stock sufficient for next week?"</li>
                            <li>"How is my daily revenue compared to average?"</li>
                            <li>"Give me 3 tips to improve sales."</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
