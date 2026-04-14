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

<div class="flex items-center space-x-5">
    {{-- DROPDOWN PROFILE MODERN --}}
    <x-dropdown align="right" width="56">
        <x-slot name="trigger">
            <button class="group flex items-center p-1 pr-3 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-full hover:border-indigo-500/50 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300 focus:outline-none">
                
                <div class="relative overflow-hidden rounded-full ring-2 ring-transparent group-hover:ring-indigo-500/30 transition-all duration-300">
                    <img class="h-9 w-9 rounded-full object-cover transform group-hover:scale-110 transition-transform duration-500" 
                         src="{{ auth()->user()->profile_photo_path ? asset('storage/' . auth()->user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&color=6366f1&background=EEF2FF&bold=true' }}" 
                         alt="{{ auth()->user()->name }}">
                    <div class="absolute inset-0 bg-indigo-500/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>

                <div class="hidden md:flex flex-col items-start ms-3 text-left leading-tight">
                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        {{ explode(' ', auth()->user()->name)[0] }}
                    </span>
                    <span class="text-[9px] text-slate-400 font-medium tracking-tight">Active Member</span>
                </div>

                <div class="ms-2 text-slate-300 group-hover:text-indigo-500 transition-colors">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="px-4 py-3 bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/50">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Signed in as</p>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate">{{ auth()->user()->email }}</p>
            </div>

            <div class="p-1">
                <x-dropdown-link :href="route('profile')" wire:navigate class="rounded-lg flex items-center px-3 py-2 text-sm text-slate-600 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    {{ __('View Profile') }}
                </x-dropdown-link>

                <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>

                <button wire:click="logout" class="w-full text-start">
                    <x-dropdown-link class="rounded-lg flex items-center px-3 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        {{ __('Sign Out') }}
                    </x-dropdown-link>
                </button>
            </div>
        </x-slot>
    </x-dropdown>
</div>