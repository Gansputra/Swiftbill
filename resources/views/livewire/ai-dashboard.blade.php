<div>
    <x-slot name="header">
        AI Insights
    </x-slot>

    <div class="h-full flex flex-col space-y-6">
        <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                AI Assistant Dashboard
            </h2>
            <p class="text-xs text-slate-500 uppercase tracking-widest font-bold mt-1">Smart Business Intelligence powered by Gemini</p>
        </div>
    </div>

    <div class="flex-grow flex flex-col overflow-hidden h-[calc(100vh-140px)] w-full max-w-4xl mx-auto">
        
        <!-- AI Chatbot (Full Width) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm flex flex-col overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    Natural Language Reporting
                </h3>
            </div>
            
            <div class="flex-grow overflow-y-auto p-6 space-y-6" id="chat-container">
                @foreach($chatMessages as $msg)
                    @if($msg['role'] === 'ai')
                        <div class="flex items-start gap-4 pr-12">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <div class="bg-slate-50 rounded-2xl rounded-tl-sm p-4 text-sm text-slate-800 shadow-sm border border-slate-100">
                                    <div class="prose prose-sm prose-p:leading-relaxed prose-p:my-1">
                                        {!! str($msg['content'])->markdown() !!}
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1 ml-2">{{ $msg['time'] ?? '' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-4 justify-end pl-12">
                            <div class="text-right">
                                <div class="bg-indigo-600 rounded-2xl rounded-tr-sm p-4 text-sm text-white shadow-sm">
                                    {{ $msg['content'] }}
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1 mr-2 text-right">{{ $msg['time'] ?? '' }}</p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        </div>
                    @endif
                @endforeach
                
                <div wire:loading wire:target="sendChatMessage" class="flex items-center gap-4 pr-12 opacity-50">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0 text-indigo-600">
                        <svg class="w-4 h-4 animate-ping" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                    </div>
                    <div class="text-xs font-bold uppercase tracking-widest text-slate-400">Gemini is thinking...</div>
                </div>
            </div>


            <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-3">
                <div class="flex flex-wrap gap-2">
                    <button wire:click="$set('userMessage', 'What are my best-selling products?')" class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition border border-indigo-100 dark:border-indigo-800/30">Best Sellers</button>
                    <button wire:click="$set('userMessage', 'Is my stock sufficient for next week?')" class="px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-amber-100 dark:hover:bg-amber-900/40 transition border border-amber-100 dark:border-amber-800/30">Stock Check</button>
                    <button wire:click="$set('userMessage', 'How is my daily revenue compared to average?')" class="px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-emerald-100 transition border border-emerald-100">Revenue</button>
                </div>
                <form wire:submit.prevent="sendChatMessage" class="flex items-end gap-3">
                    <div class="flex-grow relative">
                        <textarea wire:model="userMessage" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-sm py-3 px-4 focus:ring-2 focus:ring-indigo-500 resize-none block" rows="2" placeholder="Ask about today's sales, stock warnings, etc..."></textarea>
                        @error('userMessage') <span class="absolute -top-5 left-2 text-[10px] text-rose-500 font-bold">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" wire:loading.attr="disabled" class="p-4 bg-indigo-600 hover:bg-slate-900 text-white rounded-2xl shadow-lg shadow-indigo-100 dark:shadow-none transition disabled:opacity-50 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
            </div>
        </div>

</div>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.hook('morph.updated', ({ component }) => {
            if(component.name === 'ai-dashboard') {
                const container = document.getElementById('chat-container');
                if(container) {
                    container.scrollTop = container.scrollHeight;
                }
            }
        });
    });
</script>
</div>
