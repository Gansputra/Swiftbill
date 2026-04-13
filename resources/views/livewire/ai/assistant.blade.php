<div class="h-full flex flex-col bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-indigo-600 rounded-lg text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">SwiftBill Intelligence</h3>
        </div>
        <div class="flex items-center space-x-1">
            <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Active System</span>
        </div>
    </div>

    <!-- Chat History -->
    <div class="flex-grow overflow-y-auto p-6 space-y-6 bg-slate-50/30 dark:bg-slate-950/20" id="chat-box">
        @foreach($messages as $msg)
            <div class="{{ $msg['role'] === 'user' ? 'flex justify-end' : 'flex justify-start' }}">
                <div class="max-w-[85%] rounded-2xl px-5 py-3 text-xs leading-relaxed shadow-sm border {{ $msg['role'] === 'user' ? 'bg-indigo-600 text-white border-indigo-500 rounded-tr-none' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-100 dark:border-slate-700 rounded-tl-none' }}">
                   <p class="font-medium">{!! nl2br(e($msg['content'])) !!}</p>
                </div>
            </div>
        @endforeach

        @if($isLoading)
            <div class="flex justify-start">
                <div class="bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700 rounded-2xl rounded-tl-none px-5 py-3 shadow-sm">
                    <div class="flex space-x-1">
                        <div class="h-1.5 w-1.5 bg-indigo-400 rounded-full animate-bounce"></div>
                        <div class="h-1.5 w-1.5 bg-indigo-400 rounded-full animate-bounce delay-75"></div>
                        <div class="h-1.5 w-1.5 bg-indigo-400 rounded-full animate-bounce delay-150"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Input Section -->
    <div class="p-6 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
        <form wire:submit.prevent="askGemini" class="relative group">
            <input type="text" wire:model="question" placeholder="Analyze store data or request advice..." class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-xs py-4 px-6 pr-14 focus:ring-2 focus:ring-indigo-500 shadow-inner" {{ $isLoading ? 'disabled' : '' }}>
            <button type="submit" class="absolute right-2 top-2 bottom-2 px-4 bg-indigo-600 text-white rounded-xl hover:bg-slate-950 transition-all flex items-center justify-center disabled:opacity-50" {{ $isLoading ? 'disabled' : '' }}>
                @if($isLoading)
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                @else
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                @endif
            </button>
        </form>
        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-4 text-center">Engineered with Google Gemini Flash 1.5</p>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('scroll-down', () => {
                const box = document.getElementById('chat-box');
                box.scrollTo({ top: box.scrollHeight, behavior: 'smooth' });
            });
        });
    </script>
</div>
