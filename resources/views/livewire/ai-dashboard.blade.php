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

    <div class="flex-grow grid grid-cols-1 lg:grid-cols-2 gap-8 overflow-hidden h-[calc(100vh-140px)]">
        
        <!-- Left Panel: Market Basket & Forecasting -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm flex flex-col overflow-hidden relative">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Business Insights
                </h3>
                <button wire:click="generateInsights" wire:loading.attr="disabled" class="px-6 py-2 bg-indigo-600 hover:bg-slate-900 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest transition shadow-lg shadow-indigo-100 dark:shadow-none flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="generateInsights">Generate Analysis</span>
                    <span wire:loading wire:target="generateInsights">Analyzing 30 Days Data...</span>
                </button>
            </div>
            
            <div class="flex-grow overflow-y-auto p-6 relative">
                <div wire:loading wire:target="generateInsights" class="absolute inset-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm z-10 flex flex-col items-center justify-center">
                    <div class="w-16 h-16 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mb-4"></div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest animate-pulse">Running AI Algorithms...</p>
                </div>

                @if($insightsMarkdown)
                    <div class="prose prose-sm dark:prose-invert max-w-none prose-headings:font-bold prose-headings:tracking-tight prose-a:text-indigo-600 prose-li:marker:text-indigo-500">
                        {!! str($insightsMarkdown)->markdown() !!}
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center opacity-30 text-center px-8">
                        <svg class="w-24 h-24 text-slate-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2 uppercase tracking-widest">No Insights Yet</h4>
                        <p class="text-xs font-bold text-slate-500 leading-relaxed uppercase tracking-widest">Click the generate button above to run Sales Forecasting and Market Basket Analysis using Google Gemini AI.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Panel: AI Chatbot -->
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
                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0 text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800 rounded-2xl rounded-tl-sm p-4 text-sm text-slate-800 dark:text-slate-200 shadow-sm border border-slate-100 dark:border-slate-700/50">
                                <div class="prose prose-sm dark:prose-invert prose-p:leading-relaxed prose-p:my-1">
                                    {!! str($msg['content'])->markdown() !!}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-4 justify-end pl-12">
                            <div class="bg-indigo-600 rounded-2xl rounded-tr-sm p-4 text-sm text-white shadow-sm">
                                {{ $msg['content'] }}
                            </div>
                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center flex-shrink-0 text-slate-500">
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
                    <button wire:click="$set('userMessage', 'What are my best-selling products?')" class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition border border-indigo-100 dark:border-indigo-800/30">🏆 Best Sellers</button>
                    <button wire:click="$set('userMessage', 'Is my stock sufficient for next week?')" class="px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-amber-100 dark:hover:bg-amber-900/40 transition border border-amber-100 dark:border-amber-800/30">📦 Stock Check</button>
                    <button wire:click="$set('userMessage', 'How is my daily revenue compared to average?')" class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition border border-emerald-100 dark:border-emerald-800/30">📊 Revenue</button>
                    <button wire:click="$set('userMessage', 'Give me 3 tips to improve sales.')" class="px-3 py-1.5 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-full text-[10px] font-bold uppercase tracking-wider hover:bg-rose-100 dark:hover:bg-rose-900/40 transition border border-rose-100 dark:border-rose-800/30">💡 Sales Tips</button>
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
