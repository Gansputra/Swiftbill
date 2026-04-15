<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $userPhoto = '';
    public $userName = '';
    public $userRole = '';

    public function mount()
    {
        $this->updateData();
    }

    #[On('profile-updated')]
    public function updateData()
    {
        $user = Auth::user();
        if ($user) {
            $user->refresh();
            $this->userPhoto = $user->profile_photo_path;
            $this->userName = $user->name;
            $this->userRole = $user->role;
        }
    }
}; ?>

<div class="flex items-center space-x-3 px-3 py-2 bg-slate-50 dark:bg-slate-800/50 rounded-xl transition-all" x-data="{ stamp: Date.now() }" @profile-updated.window="stamp = Date.now()">
    <img class="h-8 w-8 rounded-full object-cover border border-white dark:border-slate-700 shadow-sm" 
         :src="$wire.userPhoto ? '{{ asset('storage') }}/' + $wire.userPhoto + '?v=' + stamp : 'https://ui-avatars.com/api/?name=' + encodeURIComponent($wire.userName) + '&color=6366f1&background=EEF2FF&bold=true'" 
         alt="{{ $userName }}">
    
    <div class="flex-grow min-w-0 text-left">
        <p class="text-sm font-semibold truncate dark:text-white" x-text="$wire.userName"></p>
        <p class="text-[10px] text-slate-400 uppercase font-bold" x-text="$wire.userRole"></p>
    </div>
</div>
