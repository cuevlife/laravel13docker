<x-owner-layout>
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8" x-data="folderRegistry()">
        <div class="rounded-xl bg-white p-6 sm:p-8 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] border border-black/[0.04] dark:bg-[#2b2d31] dark:border-white/5">
            <!-- Header Section -->
            <div class="mb-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-[1.2rem] bg-discord-green/10 text-discord-green text-2xl">
                        <i class="bi bi-folder2-open"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">Folder Management</h1>
                        <p class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest mt-1">Control Plane / Logical Workspace Units</p>
                    </div>
                </div>
                
                <button @click="showCreateModal = true" class="inline-flex h-11 items-center justify-center gap-2 rounded-full bg-discord-green px-6 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-green-500/20 transition hover:bg-[#1f8b4c]">
                    <i class="bi bi-plus-lg text-base"></i>
                    <span>Create Folder</span>
                </button>
            </div>

            <!-- Filters Section -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-12">
                <div class="relative sm:col-span-8">
                    <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-[#80848e]"></i>
                    <input type="text" x-model.debounce.300ms="filters.q" placeholder="ค้นหาโฟลเดอร์ (ชื่อ, subdomain)..." class="h-11 w-full rounded-full border border-black/5 bg-white pl-14 pr-4 text-xs font-bold outline-none shadow-sm focus:border-discord-green/30 dark:bg-[#1e1f22] dark:text-white">
                </div>
                
                <div class="sm:col-span-3">
                    <select x-model="filters.status" class="h-11 w-full rounded-full border border-black/5 bg-white px-4 text-xs font-bold outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white">
                        <option value="">ทุกสถานะ (Status)</option>
                        <option value="active">Active</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div class="sm:col-span-1">
                    <button @click="resetFilters()" class="flex h-11 w-full items-center justify-center gap-2 rounded-full border border-rose-100 bg-rose-50 text-[11px] font-black uppercase tracking-widest text-rose-500 shadow-sm transition hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10">
                        <i class="bi bi-funnel-fill text-xs"></i> ล้าง
                    </button>
                </div>
            </div>

            <!-- Bulk Actions Row -->
            <div class="mb-4 flex flex-wrap items-center justify-between gap-4 border-t border-black/[0.04] pt-4 dark:border-white/[0.04]">
                <div class="flex items-center gap-3">
                    <div class="inline-flex h-9 items-center rounded-full bg-[#f2f7ff] px-4 text-[10px] font-black text-[#4f86f7]">
                        <span x-text="selectedFolders.length">0</span> รายการที่เลือก
                    </div>
                    
                    <div class="flex items-center gap-1">
                        <button :disabled="selectedFolders.length === 0" class="flex h-9 w-9 items-center justify-center rounded-full border border-rose-100 bg-rose-50 text-rose-500 transition hover:bg-rose-100 shadow-sm dark:border-rose-500/20 dark:bg-rose-500/10 disabled:opacity-50">
                            <i class="bi bi-trash-fill text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-hidden relative min-h-[400px]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[11px] font-bold text-[#1e1f22] dark:text-[#b5bac1]">
                        <thead class="border-y border-black/[0.04] text-[10px] font-black uppercase tracking-widest text-[#80848e] dark:border-white/[0.04]">
                            <tr>
                                <th class="px-4 py-4 w-10">
                                    <input type="checkbox" @click="toggleSelectAll()" :checked="selectedFolders.length === filteredFolders.length && filteredFolders.length > 0" class="h-4 w-4 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm">
                                </th>
                                <th class="px-4 py-4">Folder Details</th>
                                <th class="px-4 py-4 text-center">Owner</th>
                                <th class="px-4 py-4 text-center">Data Volume</th>
                                <th class="px-4 py-4 text-center">Status</th>
                                <th class="px-4 py-4 text-right">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/[0.04] dark:divide-white/[0.04]">
                            <template x-for="folder in filteredFolders" :key="folder.id">
                                <tr class="group transition hover:bg-[#fafcfa] dark:hover:bg-white/[0.02]">
                                    <td class="px-4 py-5 align-top">
                                        <input type="checkbox" x-model="selectedFolders" :value="folder.id" class="folder-checkbox h-4 w-4 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm">
                                    </td>
                                    <td class="px-4 py-5 align-top">
                                        <div class="flex items-start gap-4">
                                            <div class="h-[52px] w-[52px] shrink-0 overflow-hidden rounded-[1rem] border border-black/5 shadow-sm dark:border-white/5 bg-white dark:bg-[#1e1f22] flex items-center justify-center text-lg font-black text-discord-green" x-text="folder.name.substring(0, 1).toUpperCase()">
                                            </div>
                                            <div class="flex flex-col pt-0.5">
                                                <span class="text-[13px] font-black leading-tight text-[#1e1f22] dark:text-white transition-colors group-hover:text-[#4f86f7]" x-text="folder.name"></span>
                                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                                    <span class="text-[10px] font-black tracking-widest text-[#80848e] uppercase" x-text="'/' + folder.subdomain"></span>
                                                    <span class="h-1 w-1 rounded-full bg-[#e3e5e8]"></span>
                                                    <span class="inline-flex items-center rounded-md bg-slate-50 px-1.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-slate-500 dark:bg-white/5" x-text="folder.tax_id || 'NO-TAX-ID'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6">
                                        <div class="flex flex-col items-center" x-if="folder.owner">
                                            <span class="text-[11px] font-black text-[#1e1f22] dark:text-white" x-text="folder.owner.name"></span>
                                            <span class="text-[9px] text-[#80848e]" x-text="folder.owner.email"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6 text-[11px] font-bold text-[#5c5e66] dark:text-[#b5bac1]">
                                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-slate-100 dark:bg-white/5">
                                            <span class="text-[11px] font-black" x-text="Number(folder.slips_count).toLocaleString()"></span>
                                            <span class="text-[8px] font-black uppercase tracking-widest text-[#80848e]">Slips</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-black uppercase tracking-widest border"
                                            :class="{
                                                'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10': folder.status === 'active',
                                                'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10': folder.status !== 'active'
                                            }"
                                            x-text="folder.status.toUpperCase()">
                                        </span>
                                    </td>
                                    <td class="px-4 py-5 align-top text-right pt-6">
                                        <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                            <a :href="'{{ \App\Support\OwnerUrl::base(request()) }}/projects/' + folder.id" class="flex h-8 w-8 items-center justify-center rounded-full text-[#80848e] transition hover:bg-black/5 hover:text-[#1e1f22] dark:hover:bg-white/5 dark:hover:text-white">
                                                <i class="bi bi-gear-wide-connected text-sm"></i>
                                            </a>
                                            <a :href="'/projects/open/' + folder.id" target="_blank" class="flex h-8 w-8 items-center justify-center rounded-full text-[#80848e] transition hover:bg-black/5 hover:text-[#1e1f22] dark:hover:bg-white/5 dark:hover:text-white" title="Launch Workspace">
                                                <i class="bi bi-box-arrow-up-right text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredFolders.length === 0">
                                <tr>
                                    <td colspan="6" class="py-24 text-center">
                                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-xl bg-[#f8fafb] border border-black/[0.02] shadow-sm dark:bg-[#1e1f22] dark:border-white/5 text-3xl text-[#80848e]">
                                            <i class="bi bi-folder2"></i>
                                        </div>
                                        <h3 class="text-[13px] font-black text-[#1e1f22] dark:text-white">ไม่พบโฟลเดอร์</h3>
                                        <p class="mt-1 text-xs font-bold text-[#80848e]">ลองค้นหาด้วยคำอื่น หรือกด Create Folder เพื่อเพิ่มใหม่</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Stats -->
            <div class="mt-6 flex items-center justify-between border-t border-black/[0.04] pt-6 dark:border-white/[0.04]">
                <div class="text-[11px] font-bold text-[#80848e]">
                    Showing <span class="font-black text-[#1e1f22] dark:text-white" x-text="filteredFolders.length"></span> of <span class="font-black text-[#1e1f22] dark:text-white" x-text="allFolders.length"></span> logical units
                </div>
            </div>
        </div>

        <!-- Create Folder Modal -->
        <div x-show="showCreateModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition.opacity x-cloak>
            <div class="bg-white dark:bg-[#2b2d31] w-full max-w-xl rounded-xl shadow-2xl overflow-hidden border border-black/5" @click.away="showCreateModal = false">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-[1.2rem] bg-discord-green/10 text-discord-green text-2xl">
                                <i class="bi bi-plus-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight">Create Folder</h2>
                                <p class="text-xs font-bold text-[#80848e]">Provision a new data processing unit.</p>
                            </div>
                        </div>
                        <button @click="showCreateModal = false" class="text-[#80848e] hover:text-rose-500 transition">
                            <i class="bi bi-x-lg text-xl"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="mt-6 space-y-4">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-[#80848e] ml-2">Folder Name</label>
                            <input type="text" name="name" required class="w-full rounded-xl border-none bg-[#f8fafb] px-4 py-3 text-sm font-black text-[#1e1f22] dark:bg-[#1e1f22] dark:text-white shadow-inner" placeholder="My Company Ltd.">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-[#80848e] ml-2">System Subdomain</label>
                            <input type="text" name="subdomain" required class="w-full rounded-xl border-none bg-[#f8fafb] px-4 py-3 text-sm font-black text-[#1e1f22] dark:bg-[#1e1f22] dark:text-white shadow-inner" placeholder="mycompany">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-[#80848e] ml-2">Primary Owner (Email)</label>
                            <select name="user_id" required class="w-full rounded-xl border-none bg-[#f8fafb] px-4 py-3 text-sm font-black text-[#1e1f22] dark:bg-[#1e1f22] dark:text-white shadow-inner">
                                <option value="">Select owner...</option>
                                @foreach($candidateOwners as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="showCreateModal = false" class="px-6 py-2.5 text-[11px] font-black uppercase tracking-widest text-[#5c5e66] hover:bg-black/5 dark:text-[#b5bac1] transition rounded-full">Cancel</button>
                            <button type="submit" class="px-6 py-2.5 rounded-full bg-discord-green text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-green-500/20 transition hover:bg-[#1f8b4c]">Initialize Folder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('folderRegistry', () => ({
                allFolders: [],
                filteredFolders: [],
                selectedFolders: [],
                showCreateModal: false,
                filters: {
                    q: '',
                    status: ''
                },

                init() {
                    const rawFolders = {!! json_encode($merchants->map(function($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'subdomain' => $p->subdomain,
                            'tax_id' => $p->tax_id,
                            'status' => $p->status,
                            'slips_count' => $p->slips()->count(),
                            'owner' => $p->owner ? [
                                'name' => $p->owner->name,
                                'email' => $p->owner->email
                            ] : null
                        ];
                    })) !!};
                    
                    this.allFolders = rawFolders;
                    this.filteredFolders = rawFolders;

                    this.$watch('filters', (value) => {
                        this.applyFilters();
                    }, { deep: true });
                },

                applyFilters() {
                    let result = this.allFolders;
                    
                    if (this.filters.q.trim() !== '') {
                        const term = this.filters.q.toLowerCase();
                        result = result.filter(f => 
                            f.name.toLowerCase().includes(term) || 
                            f.subdomain.toLowerCase().includes(term) ||
                            (f.owner && f.owner.email.toLowerCase().includes(term)) ||
                            (f.owner && f.owner.name.toLowerCase().includes(term))
                        );
                    }

                    if (this.filters.status !== '') {
                        result = result.filter(f => f.status === this.filters.status);
                    }

                    this.filteredFolders = result;
                },

                resetFilters() {
                    this.filters.q = '';
                    this.filters.status = '';
                },

                toggleSelectAll() {
                    if (this.selectedFolders.length < this.filteredFolders.length) {
                        this.selectedFolders = this.filteredFolders.map(f => f.id);
                    } else {
                        this.selectedFolders = [];
                    }
                }
            }));
        });
    </script>
    @endpush
</x-owner-layout>
