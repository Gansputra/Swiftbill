<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads; // Wajib untuk upload file

new class extends Component
{
    use WithFileUploads; // Mengaktifkan fitur upload

    public string $name = '';
    public string $email = '';
    public $photo; // Variable untuk menampung file foto baru

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:1024'], // Validasi: Harus gambar, maks 1MB
        ]);

        // Logika Simpan Foto jika ada upload baru
        if ($this->photo) {
            $path = $this->photo->store('profile-photos', 'public');
            $user->forceFill([
                'profile_photo_path' => $path, // Pastikan kolom ini ada di database nanti
            ])->save();
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function sendVerification(): void
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }
        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6" enctype="multipart/form-data">
        {{-- BAGIAN UPLOAD FOTO PROFIL --}}
        <div class="flex items-center gap-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
            <div class="relative">
                @if ($photo)
                    {{-- Preview Foto yang baru dipilih --}}
                    <img class="h-20 w-20 rounded-full object-cover border-4 border-white dark:border-slate-900 shadow-sm" src="{{ $photo->temporaryUrl() }}">
                @else
                    {{-- Foto lama atau inisial --}}
                    <img class="h-20 w-20 rounded-full object-cover border-4 border-white dark:border-slate-900 shadow-sm" 
                         src="{{ auth()->user()->profile_photo_path ? asset('storage/'.auth()->user()->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=6366f1&background=EEF2FF&bold=true' }}">
                @endif
            </div>
            
            <div>
                <label class="block">
                    <span class="sr-only">Choose profile photo</span>
                    <input type="file" wire:model="photo" class="block w-full text-sm text-slate-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-xs file:font-bold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100
                    "/>
                </label>
                <p class="mt-2 text-[10px] text-slate-400">JPG, PNG, atau GIF (Maks. 1MB)</p>
                <x-input-error class="mt-2" :messages="$errors->get('photo')" />
            </div>
        </div>

        {{-- INPUT NAMA --}}
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- INPUT EMAIL --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>