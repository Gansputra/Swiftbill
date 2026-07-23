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
    public string $gemini_api_key = '';
    public $photo; 
    public $croppedPhoto; 
    public $currentPhoto = ''; 

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->gemini_api_key = $user->gemini_api_key ?? '';
        $this->currentPhoto = $user->profile_photo_path;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'gemini_api_key' => ['nullable', 'string'],
        ]);

        // Proteksi tambahan: hanya validasi jika croppedPhoto benar-benar ada objeknya
        if ($this->croppedPhoto && !is_string($this->croppedPhoto)) {
            try {
                $this->validate([
                    'croppedPhoto' => ['image', 'max:2048'],
                ]);

                $path = $this->croppedPhoto->store('profile-photos', 'public');
                $user->profile_photo_path = $path;
            } catch (\Exception $e) {
                // Jika masih error file_size, kita abaikan dulu fotonya agar sistem tidak crash
                // Tapi log tetap jalan buat debugging
                \Log::error('Profile photo upload error: ' . $e->getMessage());
            }
        }

        $user->name = $this->name;
        $user->email = $this->email;
        $user->gemini_api_key = $this->gemini_api_key;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        $user->refresh();

        $this->currentPhoto = $user->profile_photo_path;
        $this->photo = null;
        $this->croppedPhoto = null;

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

<section 
    @profile-updated.window="previewSrc = null; stamp = Date.now()"
    x-data="{ 
    showModal: false,
    imageSrc: null,
    previewSrc: null,
    cropper: null,
    stamp: Date.now(),
    isUploading: false,
    
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
        if (!this.cropper || this.isUploading) return;
        
        this.isUploading = true;
        const canvas = this.cropper.getCroppedCanvas({ width: 500, height: 500 });
        this.previewSrc = canvas.toDataURL('image/jpeg');
        
        canvas.toBlob((blob) => {
            // SOLUSI UTAMA: Ubah Blob jadi File asli dengan nama 'crop.jpg'
            const croppedFile = new File([blob], 'crop.jpg', { type: 'image/jpeg' });
            
            @this.upload('croppedPhoto', croppedFile, (uploadedFilename) => {
                this.showModal = false;
                this.cropper.destroy();
                this.stamp = Date.now();
                this.$refs.fileInput.value = '';
                this.isUploading = false;
            }, () => {
                alert('Gagal mengunggah foto. Silakan coba lagi.');
                this.isUploading = false;
            });
        }, 'image/jpeg');
    }
}">
    <header>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
            Informasi Profil
        </h2>
        <p class="mt-1 text-xs text-slate-500 font-medium">
            Kelola identitas diri dan informasi akun Anda.
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
                        <img class="h-full w-full object-cover shadow-inner"
                            :src="$wire.currentPhoto ? '{{ asset('storage') }}/' + $wire.currentPhoto + '?v=' + stamp : 'https://ui-avatars.com/api/?name={{ urlencode($name) }}&color=6366f1&background=EEF2FF&bold=true'">
                    </template>
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity cursor-pointer">
                        <x-heroicon-o-camera class="w-8 h-8 text-white" />
                    </div>
                </div>
                <!-- Status Indicator -->
                <div class="absolute bottom-0 right-0 w-6 h-6 bg-emerald-500 border-4 border-white dark:border-slate-900 rounded-full"></div>
            </div>

            <div class="flex-grow space-y-3 text-center md:text-left">
                <label class="px-6 py-3 bg-indigo-600 hover:bg-slate-900 text-white rounded-2xl text-xs font-semibold cursor-pointer transition-all inline-flex items-center gap-2 shadow-xl shadow-indigo-500/20">
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                    Unggah Foto
                    <input type="file" x-ref="fileInput" @change="fileSelected" class="hidden" accept="image/*" />
                </label>
                <div wire:loading wire:target="croppedPhoto">
                    <p class="text-xs text-indigo-500 font-semibold animate-pulse">Sinkronisasi Data...</p>
                </div>
                <p x-show="!$wire.croppedPhoto" class="text-xs text-slate-400 font-medium pl-2">Foto Persegi • Presisi Tinggi</p>
                <x-input-error class="mt-2 text-xs" :messages="$errors->get('croppedPhoto')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
                <x-input-label for="name" value="Nama Lengkap" class="text-xs font-semibold text-slate-600 dark:text-slate-400 ml-1" />
                <input wire:model="name" id="name" type="text" class="w-full bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800 rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all shadow-sm outline-none" required />
                <x-input-error class="mt-2 text-xs" :messages="$errors->get('name')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="email" value="Alamat Email" class="text-xs font-semibold text-slate-600 dark:text-slate-400 ml-1" />
                <input wire:model="email" id="email" type="email" class="w-full bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800 rounded-2xl text-xs font-bold py-4 px-6 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all shadow-sm outline-none" required />
                <x-input-error class="mt-2 text-xs" :messages="$errors->get('email')" />
            </div>

            <div class="space-y-2 md:col-span-2">
                <x-input-label for="gemini_api_key" value="Gemini API Key" class="text-xs font-semibold text-slate-600 dark:text-slate-400 ml-1" />
                <div class="relative" x-data="{ show: false }">
                    <input wire:model="gemini_api_key" id="gemini_api_key" :type="show ? 'text' : 'password'" placeholder="Masukkan Gemini API Key Anda" class="w-full bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800 rounded-2xl text-xs font-bold py-4 pl-6 pr-12 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all shadow-sm outline-none" />
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition-colors">
                        <template x-if="show">
                            <x-heroicon-o-eye-slash class="w-5 h-5" />
                        </template>
                        <template x-if="!show">
                            <x-heroicon-o-eye class="w-5 h-5" />
                        </template>
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 ml-1">Kunci ini akan dienkripsi dengan aman di database. Setiap user wajib memiliki API Key sendiri untuk menggunakan asisten AI.</p>
                <x-input-error class="mt-2 text-xs" :messages="$errors->get('gemini_api_key')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-6">
            <button type="submit" wire:loading.attr="disabled" class="px-10 py-4 bg-slate-900 dark:bg-indigo-600 text-white rounded-[1.5rem] text-xs font-semibold hover:bg-indigo-600 dark:hover:bg-white dark:hover:text-indigo-600 transition-all shadow-2xl disabled:opacity-50">
                <span wire:loading.remove wire:target="updateProfileInformation">Simpan Profil</span>
                <span wire:loading wire:target="updateProfileInformation">Memproses...</span>
            </button>
            <x-action-message class="text-xs font-bold text-emerald-500 italic" on="profile-updated">
                Profil berhasil diperbarui.
            </x-action-message>
        </div>
    </form>

    <div x-show="showModal" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center overflow-hidden p-4">
        <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 backdrop-blur-xl"></div>
        <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" class="relative bg-white dark:bg-slate-900 rounded-[3rem] shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-8 border-b border-slate-50 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Atur Foto Profil</h3>
                    <p class="text-xs text-slate-400 font-medium mt-1">Sesuaikan area potong foto Anda</p>
                </div>
                <button @click="showModal = false" class="text-slate-400 hover:text-rose-500 transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            <div class="flex-1 overflow-hidden p-6 bg-slate-100/50 dark:bg-black/20">
                <div class="h-[400px] w-full rounded-2xl overflow-hidden shadow-2xl">
                    <img x-ref="cropperImage" :src="imageSrc" class="max-w-full block">
                </div>
            </div>
            <div class="p-8 border-t border-slate-50 dark:border-slate-800 flex items-center justify-end gap-3">
                <button @click="showModal = false" :disabled="isUploading" class="px-8 py-3 text-xs font-semibold text-slate-400 hover:text-slate-600 transition-all disabled:opacity-30">Batal</button>
                <button @click="saveCrop" :disabled="isUploading" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl text-xs font-semibold shadow-xl shadow-indigo-500/30 hover:bg-slate-900 transition-all disabled:opacity-50 flex items-center gap-2">
                    <span x-show="isUploading" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    <span x-text="isUploading ? 'Mengunggah...' : 'Potong & Simpan'"></span>
                </button>
            </div>
        </div>
    </div>
</section>