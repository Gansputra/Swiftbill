<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $photo; 
    public $croppedPhoto; 

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
            'croppedPhoto' => ['nullable', 'image', 'max:1024'], 
        ]);

        if ($this->croppedPhoto) {
            $path = $this->croppedPhoto->store('profile-photos', 'public');
            $user->forceFill([
                'profile_photo_path' => $path,
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

<section x-data="{ 
    showModal: false,
    imageSrc: null,
    previewSrc: null,
    cropper: null,
    
    fileSelected(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = () => {
            this.imageSrc = reader.result;
            this.showModal = true;
            
            this.$nextTick(() => {
                if(this.cropper) this.cropper.destroy();
                this.cropper = new Cropper(this.$refs.cropperImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                });
            });
        };
        reader.readAsDataURL(file);
    },
    
    saveCrop() {
        if (!this.cropper) return;
        
        const canvas = this.cropper.getCroppedCanvas({ width: 500, height: 500 });
        this.previewSrc = canvas.toDataURL('image/jpeg'); // Local preview
        
        canvas.toBlob((blob) => {
            // Kita kasih nama file 'crop.jpg' biar Livewire nggak bingung ekstensinya
            @this.upload('croppedPhoto', blob, (uploadedFilename) => {
                this.showModal = false;
                this.cropper.destroy();
            }, (error) => {
                console.error('Upload failed', error);
            });
        }, 'image/jpeg');
    }
}">
    <header>
        <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">
            {{ __('Profile Integrity') }}
        </h2>
        <p class="mt-1 text-xs text-slate-500 font-medium tracking-wide">
            {{ __("Manage your identity and authentication credentials.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-8 space-y-8" enctype="multipart/form-data">
        <div class="flex flex-col md:flex-row items-center gap-8 p-8 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] shadow-sm">
            <div class="relative group">
                <div class="h-24 w-24 rounded-full overflow-hidden border-4 border-slate-50 dark:border-slate-800 shadow-xl relative transition-transform group-hover:scale-105 duration-500 flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                    <template x-if="previewSrc">
                        <img class="h-full w-full object-cover" :src="previewSrc">
                    </template>
                    <template x-if="!previewSrc">
                        <img class="h-full w-full object-cover"
                            src="{{ auth()->user()->profile_photo_path ? asset('storage/'.auth()->user()->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=6366f1&background=EEF2FF&bold=true' }}">
                    </template>
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <x-heroicon-o-camera class="w-8 h-8 text-white" />
                    </div>
                </div>
                <div class="absolute bottom-0 right-0 w-6 h-6 bg-emerald-500 border-4 border-white dark:border-slate-900 rounded-full"></div>
            </div>

            <div class="flex-grow space-y-3 text-center md:text-left">
                <label class="px-6 py-3 bg-indigo-600 hover:bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest cursor-pointer transition-all inline-flex items-center gap-2 shadow-xl shadow-indigo-500/20">
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                    Update Portrait
                    <input type="file" @change="fileSelected" class="hidden" accept="image/*" />
                </label>
                <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest pl-2">Square Crop • High Precision</p>
                <x-input-error class="mt-2 text-xs" :messages="$errors->get('croppedPhoto')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
                <x-input-label for="name" :value="__('Display Name')" class="text-[10px] font-black uppercase tracking-widest ml-1" />
                <input wire:model="name" id="name" type="text" class="w-full bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800 rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all shadow-sm outline-none" required />
                <x-input-error class="mt-2 text-xs" :messages="$errors->get('name')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="email" :value="__('Email Network')" class="text-[10px] font-black uppercase tracking-widest ml-1" />
                <input wire:model="email" id="email" type="email" class="w-full bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800 rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all shadow-sm outline-none" required />
                <x-input-error class="mt-2 text-xs" :messages="$errors->get('email')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-6">
            <button type="submit" class="px-10 py-4 bg-slate-900 dark:bg-indigo-600 text-white rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 dark:hover:bg-white dark:hover:text-indigo-600 transition-all shadow-2xl">
                Synchronize Profile
            </button>
            
            <x-action-message class="text-xs font-bold text-emerald-500 italic" on="profile-updated">
                {{ __('Update Propagated Successfully.') }}
            </x-action-message>
        </div>
    </form>

    <div x-show="showModal" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center overflow-hidden p-4">
        <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 backdrop-blur-xl"></div>
        
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="scale-95 opacity-0" 
             x-transition:enter-end="scale-100 opacity-100" 
             class="relative bg-white dark:bg-slate-900 rounded-[3rem] shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-8 border-b border-slate-50 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tighter">Adjust Portrait</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">Fine-tune your identity</p>
                </div>
                <button @click="showModal = false" class="text-slate-400 hover:text-rose-500 transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            <div class="flex-1 overflow-hidden p-6 bg-slate-100/50 dark:bg-black/20">
                <div class="h-[400px] w-full rounded-2xl overflow-hidden">
                    <img x-ref="cropperImage" :src="imageSrc" class="max-w-full block">
                </div>
            </div>

            <div class="p-8 border-t border-slate-50 dark:border-slate-800 flex items-center justify-end gap-3">
                <button @click="showModal = false" class="px-8 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-all">Cancel</button>
                <button @click="saveCrop" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/30 hover:bg-slate-900 transition-all">
                    Finalize & Crop
                </button>
            </div>
        </div>
    </div>
</section>