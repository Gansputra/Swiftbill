<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use Livewire\Attributes\On; 
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $userPhoto = '';
    public $userName = '';
    public $userEmail = '';
    public $userRole = '';

    public function mount()
    {
        $this->updateData();
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    #[On('profile-updated')]
    public function updateData()
    {
        $user = Auth::user();
        if ($user) {
            $user->refresh();
            $this->userPhoto = $user->profile_photo_path;
            $this->userName = $user->name;
            $this->userEmail = $user->email;
            $this->userRole = $user->role ?? 'User';
        }
    }
}; ?>

<div class="flex items-center" x-data="{ stamp: Date.now() }" @profile-updated.window="stamp = Date.now()">
    {{-- DROPDOWN PROFILE MODERN --}}
    <x-dropdown align="right" width="64">
        <x-slot name="trigger">
            <button class="group flex items-center p-1.5 pr-4 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200 dark:border-slate-800/80 rounded-[16px] hover:border-slate-300 dark:hover:border-slate-700 hover:bg-slate-100/70 dark:hover:bg-slate-800/70 hover:shadow-md hover:shadow-slate-200/50 dark:hover:shadow-black/20 transition-all duration-250 ease-in-out cursor-pointer focus:outline-none">
                
                {{-- Round Avatar (44px) --}}
                <div class="relative flex-shrink-0">
                    <img class="h-[44px] w-[44px] rounded-full object-cover ring-2 ring-indigo-500/20 group-hover:ring-indigo-500/50 transform group-hover:scale-105 transition-all duration-300" 
                         :src="$wire.userPhoto ? '{{ asset('storage') }}/' + $wire.userPhoto + '?v=' + stamp : 'https://ui-avatars.com/api/?name=' + encodeURIComponent($wire.userName) + '&color=6366f1&background=EEF2FF&bold=true'" 
                         alt="{{ auth()->user()->name }}">
                    <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900"></span>
                </div>

                {{-- User Name & Role --}}
                <div class="hidden sm:flex flex-col items-start ms-3 text-left leading-tight">
                    <span class="text-sm font-bold text-slate-900 dark:text-slate-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate max-w-[130px]" x-text="$wire.userName">
                    </span>
                    <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 capitalize mt-0.5" x-text="$wire.userRole">
                    </span>
                </div>

                {{-- Chevron Down Icon --}}
                <div class="ms-3 text-slate-400 group-hover:text-indigo-500 transition-all duration-300 transform group-aria-expanded:rotate-180">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            {{-- Header Profile Info inside Dropdown --}}
            <div class="p-4 bg-slate-50/80 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center space-x-3">
                    <img class="h-12 w-12 rounded-full object-cover ring-2 ring-indigo-500/30 flex-shrink-0" 
                         :src="$wire.userPhoto ? '{{ asset('storage') }}/' + $wire.userPhoto + '?v=' + stamp : 'https://ui-avatars.com/api/?name=' + encodeURIComponent($wire.userName) + '&color=6366f1&background=EEF2FF&bold=true'" 
                         alt="{{ auth()->user()->name }}">
                    <div class="flex-grow min-w-0">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate" x-text="$wire.userName"></h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate mb-1" x-text="$wire.userEmail"></p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/40 capitalize" x-text="$wire.userRole">
                        </span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="p-2 space-y-1">
                <x-dropdown-link :href="route('profile')" wire:navigate class="rounded-xl flex items-center px-3.5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-indigo-50/80 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-200">
                    <svg class="w-4 h-4 me-2.5 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ __('Lihat Profil') }}
                </x-dropdown-link>

                <div class="my-1.5 border-t border-slate-100 dark:border-slate-800/80"></div>

                <button wire:click="logout" class="w-full text-start group/logout focus:outline-none">
                    <div class="rounded-xl flex items-center px-3.5 py-2.5 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-700 dark:hover:text-rose-300 transition-all duration-200">
                        <svg class="w-4 h-4 me-2.5 text-rose-500 group-hover/logout:text-rose-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        {{ __('Keluar') }}
                    </div>
                </button>
            </div>
        </x-slot>
    </x-dropdown>
</div>