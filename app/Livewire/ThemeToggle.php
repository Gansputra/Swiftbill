<?php

namespace App\Livewire;

use Livewire\Component;

class ThemeToggle extends Component
{
    public $isDarkMode = false;

    public function mount()
    {
        // By default, assume false unless user is logged in
        if (auth()->check()) {
            $this->isDarkMode = auth()->user()->dark_mode;
        }
    }

    public function toggleTheme()
    {
        $this->isDarkMode = !$this->isDarkMode;

        if (auth()->check()) {
            $user = auth()->user();
            $user->dark_mode = $this->isDarkMode;
            $user->save();
        }
        
        $this->js("
            if ('".$this->isDarkMode."' == 1) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        ");
    }

    public function render()
    {
        return view('livewire.theme-toggle');
    }
}
