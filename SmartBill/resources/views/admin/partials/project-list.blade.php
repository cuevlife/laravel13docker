@forelse($stores as $store)
    <div class="group relative flex flex-col items-center justify-between h-[360px] w-[260px] rounded-[3rem] bg-white p-8 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] border border-black/[0.02] dark:bg-[#2b2d31]">
        <!-- Delete Button (Top Right as per image) -->
        @if(auth()->user()->isSuperAdmin() || (int)$store->user_id === (int)auth()->id())
            <button @click="$dispatch('open-delete', {id: '{{ $store->id }}', name: '{{ $store->name }}' })" 
                    class="absolute top-6 right-6 flex h-8 w-8 items-center justify-center rounded-full border border-black/[0.05] bg-white text-[#80848e] transition hover:bg-discord-red hover:text-white dark:bg-[#1e1f22]">
                <i  class="bi bi-trash-fill h-4 w-4"></i>
            </button>
        @endif

        <div class="flex-1 flex flex-col items-center justify-center">
            <!-- Icon/Logo Area -->
            <div class="relative h-28 w-28 overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-[#4f86f7] to-[#2d62ed] shadow-lg">
                @if(isset($store->config['logo']))
                    <img src="{{ asset('storage/' . $store->config['logo']) }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center text-4xl font-black text-white">
                        {{ strtoupper(substr($store->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <h3 class="mt-6 text-2xl font-black text-[#1e1f22] dark:text-white truncate w-full text-center px-2">
                {{ $store->name }}
            </h3>
        </div>
        
        <!-- Open Folder Button (Pill style as per image) -->
        <a href="{{ \App\Support\WorkspaceUrl::workspace(request(), $store, 'dashboard') }}" 
           class="flex items-center gap-2 rounded-full bg-[#f2f7ff] px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-[#4f86f7] transition hover:bg-[#4f86f7] hover:text-white">
            Open Folder <i  class="bi bi-arrow-right h-3.5 w-3.5"></i>
        </a>
    </div>
@empty
    <!-- Empty state already handled by parent loop if needed, but keeping for AJAX results -->
@endforelse
