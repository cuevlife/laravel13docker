<div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
    <div class="hidden lg:flex h-16 items-center justify-between border-b border-[#e3e5e8]/60 dark:border-[#313338]/60 mb-8">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-6 bg-discord-green rounded-full shadow-[0_0_10px_rgba(35,165,89,0.3)]"></div>
            <div>
                <div class="text-sm font-black text-[#1e1f22] dark:text-[#f2f3f5] uppercase tracking-widest leading-none">Project Hub</div>
                <div class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#80848e] mt-1">Choose project before entering workspace</div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#f2f3f5] dark:bg-[#2b2d31] rounded-full border border-[#e3e5e8] dark:border-[#313338] shadow-sm">
                <i data-lucide="coins" class="w-4 h-4 text-amber-500"></i>
                <span class="text-[11px] font-black tracking-widest text-[#1e1f22] dark:text-[#f2f3f5]">{{ number_format(auth()->user()->tokens) }}</span>
            </div>

            <a href="{{ route('lang.switch', app()->getLocale() == 'th' ? 'en' : 'th') }}" class="flex items-center gap-2 px-3 py-1.5 bg-[#e3e5e8] dark:bg-[#2b2d31] rounded-[8px] text-[10px] font-black uppercase tracking-widest text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white transition-all shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                <i data-lucide="languages" class="w-3.5 h-3.5"></i>
                <span>{{ app()->getLocale() == 'th' ? 'TH' : 'EN' }}</span>
            </a>

            <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="inline-flex h-10 w-10 items-center justify-center rounded-[12px] border border-[#e3e5e8] bg-white text-[#1e1f22] transition hover:border-[#1e1f22]/20 hover:bg-[#f8f9fb] dark:border-[#313338] dark:bg-[#2b2d31] dark:text-white dark:hover:bg-[#313338]">
                <i data-lucide="sun-moon" class="h-4 w-4"></i>
            </button>

            @if(auth()->user()->isSuperAdmin())
                <a href="{{ \App\Support\OwnerUrl::path(request(), 'dashboard') }}" class="inline-flex items-center gap-2 rounded-[12px] border border-[#e3e5e8] bg-white px-3 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-[#1e1f22] transition hover:border-[#1e1f22]/20 hover:bg-[#f8f9fb] dark:border-[#313338] dark:bg-[#2b2d31] dark:text-white dark:hover:bg-[#313338]">
                    <i data-lucide="shield-check" class="h-4 w-4"></i>
                    Control Plane
                </a>
            @endif

            <div class="h-6 w-px bg-[#e3e5e8] dark:bg-[#313338]"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-[12px] border border-[#e3e5e8] bg-white text-[#1e1f22] transition hover:border-rose-300 hover:bg-rose-50 hover:text-rose-500 dark:border-[#313338] dark:bg-[#2b2d31] dark:text-white dark:hover:bg-rose-500/10 dark:hover:text-rose-300">
                    <i data-lucide="log-out" class="h-4 w-4"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Mobile Header (Simplified) -->
    <div class="lg:hidden sticky top-0 z-40 bg-[#fafafa]/90 dark:bg-[#111827]/90 backdrop-blur-md border-b border-[#e3e5e8] dark:border-[#313338] px-4 py-3 flex items-center justify-between w-full shadow-sm mb-6 rounded-[20px]">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-7 h-7 rounded-full bg-discord-green flex items-center justify-center text-white font-black text-[10px] shadow-sm shadow-discord-green/20 shrink-0">
                SB
            </div>
            <div class="min-w-0">
                <h1 class="text-xs font-black text-[#1e1f22] dark:text-white uppercase tracking-widest truncate">Project Hub</h1>
                <p class="text-[9px] font-black uppercase tracking-[0.18em] text-[#80848e] truncate">Choose project</p>
            </div>
        </div>
        
        <div class="flex items-center gap-2 shrink-0">
            <div class="flex items-center gap-1.5 px-2.5 py-1 bg-[#f2f3f5] dark:bg-[#2b2d31] rounded-full border border-[#e3e5e8] dark:border-[#313338] shadow-sm">
                <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-500"></i>
                <span class="text-[10px] font-black tracking-widest text-[#1e1f22] dark:text-[#f2f3f5]">{{ number_format(auth()->user()->tokens) }}</span>
            </div>

            <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white transition-colors focus:outline-none">
                <i data-lucide="moon" x-show="!darkMode" class="w-4 h-4"></i>
                <i data-lucide="sun" x-show="darkMode" class="w-4 h-4" x-cloak></i>
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-[#e3e5e8] dark:border-[#313338] bg-white dark:bg-[#2b2d31] text-[#5c5e66] dark:text-[#b5bac1] hover:text-rose-500 dark:hover:text-rose-300 transition-all shadow-sm">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="flex flex-1 flex-col items-center justify-center py-10">
        <div class="max-w-3xl text-center">
            <div class="mx-auto inline-flex items-center gap-2 rounded-full bg-white/70 px-4 py-2 text-[10px] font-black uppercase tracking-[0.24em] text-[#4f7cff] shadow-sm backdrop-blur dark:bg-white/[0.05] dark:text-[#8eabff]">
                <i data-lucide="layout-grid" class="h-3.5 w-3.5"></i>
                Project Hub
            </div>
            <div class="mt-8 relative max-w-xl mx-auto">
                <div class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-slate-400 dark:text-slate-500"></i>
                </div>
                <input type="text" 
                       wire:model.live="search"
                       placeholder="ค้นหาโปรเจกต์..." 
                       class="block w-full pl-14 pr-6 py-4 bg-white/70 dark:bg-white/10 backdrop-blur-md border border-white/50 dark:border-white/5 text-lg font-black text-[#162033] dark:text-white rounded-full focus:ring-4 focus:ring-discord-green/20 focus:border-discord-green/50 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500 shadow-sm">
            </div>
        </div>

        <div class="mt-12 grid w-full max-w-7xl grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
            <button type="button" wire:click="openCreateModal" class="hub-card flex min-h-[250px] flex-col items-center justify-center border-2 border-dashed border-[#4f7cff]/20 px-8 py-10 text-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-[2rem] bg-[#f3f7ff] text-[#4f7cff] shadow-inner dark:bg-white/[0.06] dark:text-[#8eabff]">
                    <i data-lucide="plus" class="h-10 w-10"></i>
                </div>
                <div class="mt-6 text-[10px] font-black uppercase tracking-[0.28em] text-slate-400">Create</div>
                <h2 class="mt-3 text-2xl font-black tracking-tight text-[#162033] dark:text-white">Add Project</h2>
                <p class="mt-4 line-clamp-2 text-sm font-bold leading-relaxed text-slate-500 dark:text-slate-300">
                    Add a new workspace with just a project name, then start using it immediately.
                </p>
                <div class="mt-8 inline-flex items-center gap-2 rounded-full bg-[#f3f7ff] px-4 py-2 text-[10px] font-black uppercase tracking-[0.22em] text-[#4f7cff] dark:bg-white/[0.06] dark:text-[#8eabff]">
                    <span>New Project</span>
                    <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                </div>
            </button>

            @foreach($stores as $store)
                @php
                    $storeUrl = \App\Support\WorkspaceUrl::workspace(request(), $store, 'dashboard');
                    $membership = $store->users->first();
                    $canDeleteStore = auth()->user()->isSuperAdmin()
                        || (int) $store->user_id === (int) auth()->id()
                        || $membership?->pivot?->role === 'owner';
                    $storeInitials = collect(explode(' ', trim($store->name)))
                        ->filter()
                        ->map(fn ($part) => mb_substr($part, 0, 1))
                        ->take(2)
                        ->implode('');
                    $storeInitials = $storeInitials !== '' ? $storeInitials : str_pad((string) $store->id, 2, '0', STR_PAD_LEFT);
                @endphp

                <div class="group relative">
                    @if($canDeleteStore)
                        <button
                            type="button"
                            wire:click.stop="openDeleteModal({{ $store->id }}, '{{ addslashes($store->name) }}')"
                            class="absolute right-4 top-4 z-10 inline-flex h-11 w-11 items-center justify-center rounded-[1rem] border border-black/10 bg-white/90 text-slate-400 transition hover:border-rose-300 hover:text-rose-500 dark:border-white/10 dark:bg-[#17202e]/90 dark:text-slate-300 dark:hover:border-rose-300 dark:hover:text-rose-300"
                            aria-label="Delete {{ $store->name }}"
                        >
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </button>
                    @endif

                    <a href="{{ $storeUrl }}" class="hub-card flex min-h-[250px] flex-col items-center justify-center px-8 py-10 text-center transition duration-300 hover:-translate-y-1.5 hover:border-[#4f7cff]/20 hover:shadow-[0_28px_70px_rgba(79,124,255,0.16)]">
                        <div class="flex h-24 w-24 items-center justify-center rounded-[2rem] bg-gradient-to-br from-[#4f7cff] to-[#1ea97c] text-2xl font-black uppercase tracking-[0.12em] text-white shadow-[0_18px_40px_rgba(79,124,255,0.28)] transition duration-300 group-hover:scale-105">
                            {{ $storeInitials }}
                        </div>
                        <div class="mt-6 text-[10px] font-black uppercase tracking-[0.28em] text-slate-400">Project {{ str_pad((string) $store->id, 2, '0', STR_PAD_LEFT) }}</div>
                        <h2 class="mt-3 text-2xl font-black tracking-tight text-[#162033] dark:text-white">{{ $store->name }}</h2>
                        <p class="mt-4 line-clamp-2 text-sm font-bold leading-relaxed text-slate-500 dark:text-slate-300">
                            {{ $store->address ?: 'AI slip scanning workspace for profiles, scans, exports, and team operations.' }}
                        </p>
                        <div class="mt-8 inline-flex items-center gap-2 rounded-full bg-[#f3f7ff] px-4 py-2 text-[10px] font-black uppercase tracking-[0.22em] text-[#4f7cff] transition group-hover:bg-[#4f7cff] group-hover:text-white dark:bg-white/[0.06] dark:text-[#8eabff] dark:group-hover:bg-[#4f7cff] dark:group-hover:text-white">
                            <span>Open Project</span>
                            <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        @if($stores->isEmpty())
            <div class="mt-16 text-center py-16 w-full opacity-60">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-white/40 dark:bg-white/5 border border-white/50 dark:border-white/5 shadow-inner mb-6">
                    <i data-lucide="search-x" class="h-10 w-10 text-slate-400"></i>
                </div>
                <p class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">ไม่พบโปรเจกต์ที่คุณค้นหา</p>
            </div>
        @endif
    </div>

    <!-- Create Modal -->
    <div x-data="{ open: @entangle('createOpen') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-[#162033]/70 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative z-10 w-full max-w-md rounded-[2rem] border border-black/5 bg-white p-8 shadow-[0_30px_80px_rgba(22,32,51,0.20)] dark:border-white/10 dark:bg-[#17202e]">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-[0.24em] text-[#4f7cff]">Quick Create</div>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-[#162033] dark:text-white">Add Project</h2>
                    <p class="mt-3 text-sm font-bold leading-relaxed text-slate-500 dark:text-slate-300">
                        Enter the project name and we will create the workspace and open it right away.
                    </p>
                </div>

                <button type="button" @click="open = false" class="inline-flex h-11 w-11 items-center justify-center rounded-[1rem] border border-black/10 text-[#162033] transition hover:border-rose-300 hover:text-rose-500 dark:border-white/10 dark:text-white">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>

            <form wire:submit.prevent="submitCreate" class="mt-8 space-y-5">
                <div>
                    <label for="project-name" class="mb-2 block text-[10px] font-black uppercase tracking-[0.24em] text-slate-400">Project Name</label>
                    <input id="project-name" wire:model.defer="name" type="text" required maxlength="255" placeholder="e.g. Home, Client A, Branch 01"
                           class="w-full rounded-[1.2rem] border border-black/10 bg-[#f7f9ff] px-5 py-4 text-base font-black text-[#162033] outline-none transition focus:border-[#4f7cff] focus:ring-2 focus:ring-[#4f7cff]/20 dark:border-white/10 dark:bg-white/[0.05] dark:text-white">
                    <p class="mt-3 text-[11px] font-bold text-slate-500 dark:text-slate-300">
                        Start with the project name first. You can fill the rest later.
                    </p>
                    @if($errorMessage)
                        <p class="mt-3 text-[11px] font-black text-rose-500">{{ $errorMessage }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="open = false" class="flex-1 rounded-[1.2rem] border border-black/10 px-5 py-4 text-[11px] font-black uppercase tracking-[0.22em] text-[#162033] transition hover:border-[#162033] hover:bg-black/5 dark:border-white/10 dark:text-white dark:hover:bg-white/[0.05]">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="flex-1 rounded-[1.2rem] bg-[#162033] px-5 py-4 text-[11px] font-black uppercase tracking-[0.22em] text-white transition hover:bg-[#0f1727] disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="submitCreate">Add</span>
                        <span wire:loading wire:target="submitCreate">Creating...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-data="{ open: @entangle('deleteOpen') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-[#162033]/70 backdrop-blur-sm" @click="open = false"></div>

        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative z-10 w-full max-w-md rounded-[2rem] border border-black/5 bg-white p-8 shadow-[0_30px_80px_rgba(22,32,51,0.20)] dark:border-white/10 dark:bg-[#17202e]">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-[0.24em] text-rose-500">Delete Project</div>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-[#162033] dark:text-white">Confirm Delete</h2>
                    <p class="mt-3 text-sm font-bold leading-relaxed text-slate-500 dark:text-slate-300">
                        To delete this workspace, type the exact project name below.
                    </p>
                </div>

                <button type="button" @click="open = false" class="inline-flex h-11 w-11 items-center justify-center rounded-[1rem] border border-black/10 text-[#162033] transition hover:border-rose-300 hover:text-rose-500 dark:border-white/10 dark:text-white">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>

            <form wire:submit.prevent="submitDelete" class="mt-8 space-y-5">
                <div class="rounded-[1.2rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-600 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200">
                    <div class="text-[10px] font-black uppercase tracking-[0.24em]">Project Name</div>
                    <div class="mt-2 text-lg font-black">{{ $deleteName }}</div>
                </div>

                <div>
                    <label for="delete-project-name" class="mb-2 block text-[10px] font-black uppercase tracking-[0.24em] text-slate-400">Type Project Name To Confirm</label>
                    <input id="delete-project-name" wire:model.defer="deleteConfirmation" type="text" required maxlength="255" placeholder="Type the exact project name"
                           class="w-full rounded-[1.2rem] border border-black/10 bg-[#f7f9ff] px-5 py-4 text-base font-black text-[#162033] outline-none transition focus:border-rose-400 focus:ring-2 focus:ring-rose-400/20 dark:border-white/10 dark:bg-white/[0.05] dark:text-white">
                    @if($deleteErrorMessage)
                        <p class="mt-3 text-[11px] font-black text-rose-500">{{ $deleteErrorMessage }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="open = false" class="flex-1 rounded-[1.2rem] border border-black/10 px-5 py-4 text-[11px] font-black uppercase tracking-[0.22em] text-[#162033] transition hover:border-[#162033] hover:bg-black/5 dark:border-white/10 dark:text-white dark:hover:bg-white/[0.05]">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="flex-1 rounded-[1.2rem] bg-rose-500 px-5 py-4 text-[11px] font-black uppercase tracking-[0.22em] text-white transition hover:bg-rose-600 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="submitDelete">Delete</span>
                        <span wire:loading wire:target="submitDelete">Deleting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (message) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: message[0],
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            });
        });
    </script>
</div>
