<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="flex items-center space-x-4">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="inline-flex items-center p-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl hover:border-indigo-500 transition focus:outline-none shadow-sm">
                
                <div class="relative">
                    {{-- LOGIKA CEK FOTO: Jika ada path di database, pakai asset storage. Jika kosong, pakai UI-Avatars --}}
                    <img class="h-8 w-8 rounded-full object-cover border border-slate-100 dark:border-slate-700" 
                         src="{{ auth()->user()->profile_photo_path ? asset('storage/' . auth()->user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&color=6366f1&background=EEF2FF&bold=true' }}" 
                         alt="{{ auth()->user()->name }}">
                    
                    <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-white dark:ring-slate-900"></span>
                </div>

                <div class="ms-1.5 pr-1 text-slate-400">
                    <svg class="h-3.5 w-3.5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="block px-4 py-2 text-xs text-slate-400 border-b border-slate-100 dark:border-slate-800">
                Logged in as: <span class="font-bold text-slate-700 dark:text-slate-200">{{ auth()->user()->name }}</span>
            </div>

            <x-dropdown-link :href="route('profile')" wire:navigate>
                {{ __('Profile') }}
            </x-dropdown-link>

            <button wire:click="logout" class="w-full text-start">
                <x-dropdown-link>
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </button>
        </x-slot>
    </x-dropdown>
</div>