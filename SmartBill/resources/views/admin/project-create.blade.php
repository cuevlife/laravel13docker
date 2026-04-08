<x-owner-layout>
    <div class="w-full py-6 px-4 sm:px-6 lg:px-10 animate-in fade-in duration-500">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-black/5 dark:border-white/5">
            <div class="flex items-center gap-4">
                <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="w-9 h-9 rounded-lg bg-white dark:bg-white/5 border border-black/10 dark:border-white/10 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all shadow-sm">
                    <i class="bi bi-arrow-left text-lg"></i>
                </a>
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">
                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="hover:text-indigo-600 transition">Folders</a>
                        <i class="bi bi-chevron-right text-[8px]"></i>
                        <span class="text-slate-600 dark:text-slate-300">New Folder</span>
                    </nav>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tightest">Initialize Data Folder</h1>
                </div>
            </div>
        </div>

        <div class="w-full">
            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="space-y-6">
                @csrf
                
                <!-- Basic Configuration Section -->
                <div class="bg-white dark:bg-discord-main p-8 rounded-xl border border-black/5 dark:border-white/5 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-4 bg-discord-green rounded-full"></div>
                        <h2 class="text-sm font-black uppercase tracking-widest text-slate-900 dark:text-white">Folder Configuration</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black uppercase text-slate-400 ml-1">Logical Name</label>
                            <input type="text" name="name" required class="w-full rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-discord-green/20 outline-none transition-all" placeholder="e.g. My Company Ltd.">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black uppercase text-slate-400 ml-1">System Subdomain</label>
                            <input type="text" name="subdomain" required class="w-full rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-discord-green/20 outline-none transition-all" placeholder="e.g. mycompany">
                        </div>
                    </div>
                </div>

                <!-- Ownership Section -->
                <div class="bg-white dark:bg-discord-main p-8 rounded-xl border border-black/5 dark:border-white/5 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                        <h2 class="text-sm font-black uppercase tracking-widest text-slate-900 dark:text-white">Primary Ownership</h2>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black uppercase text-slate-400 ml-1">Assign Primary Owner</label>
                        <select name="user_id" required class="w-full rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                            <option value="">Select an account to own this folder...</option>
                            @foreach($candidateOwners as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                        <p class="text-[9px] font-bold text-slate-400 uppercase mt-2 ml-1">The primary owner will have full administrative control over this folder's data.</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 pt-4">
                    <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-slate-500 hover:bg-black/5 rounded-lg transition-all">Cancel</a>
                    <button type="submit" class="px-10 py-2.5 bg-discord-green text-white text-xs font-black uppercase tracking-[0.2em] rounded-lg shadow-lg shadow-green-500/20 hover:bg-[#1f8b4c] transition-all">
                        Initialize Folder
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-owner-layout>