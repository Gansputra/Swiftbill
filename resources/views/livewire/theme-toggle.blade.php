<button wire:click="toggleTheme" type="button" class="text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-lg text-sm p-2.5 transition">
    @if($isDarkMode)
    <!-- Sun (Light Mode Icon) -->
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 4.22a1 1 0 011.415 1.415l-.708.708a1 1 0 01-1.414-1.414l.707-.707zm-9.85 9.85a1 1 0 011.415 1.415l-.708.708a1 1 0 01-1.414-1.414l.707-.707zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm6-6a1 1 0 011 1h1a1 1 0 110 2h-1a1 1 0 01-1-1zm-12 0a1 1 0 011 1H4a1 1 0 110 2H3a1 1 0 01-1-1zm9.85 4.85a1 1 0 01-1.415-1.415l.708-.708a1 1 0 011.414 1.414l-.707.707zm-7.02-7.02a1 1 0 01-1.415-1.415l.708-.708a1 1 0 011.414 1.414l-.707.707zM10 6a4 4 0 100 8 4 4 0 000-8z"></path></svg>
    @else
    <!-- Moon (Dark Mode Icon) -->
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
    @endif
</button>
