<div class='flex items-center gap-2 rounded-full bg-[#f2f3f5] px-3 py-1.5 dark:bg-[#1e1f22]' 
     x-data='{ balance: {{ auth()->user()->tokens ?? 0 }} }' 
     @update-balance.window='fetch("{{ route("workspace.tokens.balance") }}").then(res => res.json()).then(data => balance = data.balance)'>
    <i data-lucide='badge-dollar-sign' class='h-4 w-4 text-discord-green'></i>
    <span class='text-xs font-black text-[#1e1f22] dark:text-white' x-text='balance.toLocaleString()'></span>
</div>
