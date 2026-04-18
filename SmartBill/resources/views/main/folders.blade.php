@extends('layouts.app')

@section('content')
    <div class="w-full py-8 px-4 sm:px-6 lg:px-8 animate-in fade-in duration-500" x-data="folderRegistry()">
        
        <!-- Master Container Card -->
        <div class="bg-white dark:bg-[#2b2d31] rounded-xl shadow-sm border border-black/[0.05] dark:border-white/5 overflow-hidden">
            
            {{-- Header Section --}}
            <div class="px-8 py-10 border-b border-black/[0.03] dark:border-white/[0.03]">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-discord-green/5 text-discord-green text-2xl border border-discord-green/10">
                            <i class="bi bi-folder2-open"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tightest">{{ __('Folder Management') }}</h1>
                            <p class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest mt-1">{{ __('Control Plane / Logical Workspace Units') }}</p>
                        </div>
                    </div>
                    
                    <a href="{{ \App\Support\OwnerUrl::path(request(), 'folders/create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-discord-green px-6 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-green-500/20 transition hover:bg-[#1f8b4c] active:scale-95">
                        <i class="bi bi-plus-lg text-base"></i>
                        <span>{{ __('Create Folder') }}</span>
                    </a>
                </div>
            </div>

            <div class="p-8 space-y-10">
                <!-- Filters Section (Pure White) -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-12">
                    <div class="relative sm:col-span-6">
                        <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-[#80848e] z-10"></i>
                        <input type="text" x-model.debounce.300ms="filters.q" placeholder="{{ __('Search folders (Name, Subdomain, Owner)...') }}" 
                               class="h-12 w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] pl-14 pr-4 text-xs font-bold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-discord-green/10 focus:border-discord-green/30 transition-all">
                    </div>
                    
                    <div class="sm:col-span-3">
                        <select x-model="filters.status" class="h-12 w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 text-xs font-bold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-discord-green/10 focus:border-discord-green/30 transition-all cursor-pointer">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="archived">{{ __('Archived') }}</option>
                        </select>
                    </div>

                    <div class="sm:col-span-3">
                        <button @click="resetFilters()" class="flex h-12 w-full items-center justify-center gap-2 rounded-xl border border-rose-100 bg-white text-[10px] font-black uppercase tracking-widest text-rose-500 shadow-sm transition hover:bg-rose-500 hover:text-white dark:border-rose-500/20 dark:bg-transparent">
                            <i class="bi bi-arrow-counterclockwise text-xs"></i> {{ __('Reset Filters') }}
                        </button>
                    </div>
                </div>

                <!-- Table Section (Clean White) -->
                <div class="overflow-hidden border border-black/[0.05] dark:border-white/5 rounded-xl bg-white dark:bg-[#2b2d31]">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-[11px] font-bold text-[#1e1f22] dark:text-[#b5bac1]">
                            <thead class="bg-white dark:bg-white/[0.02] text-[9px] font-black uppercase tracking-widest text-[#80848e] border-b border-black/[0.03] dark:border-white/[0.03]">
                                <tr>
                                    <th class="px-6 py-4 w-[40px]">
                                        <input type="checkbox" @click="toggleSelectAll()" :checked="selectedFolders.length === filteredFolders.length && filteredFolders.length > 0" class="h-4 w-4 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm transition-all">
                                    </th>
                                    <th class="px-6 py-4">{{ __('Folder Details') }}</th>
                                    <th class="px-6 py-4 text-center w-[180px]">{{ __('Owner') }}</th>
                                    <th class="px-6 py-4 text-center w-[120px]">{{ __('Data Volume') }}</th>
                                    <th class="px-6 py-4 text-center w-[120px]">{{ __('Status') }}</th>
                                    <th class="px-6 py-4 text-right w-[120px]">{{ __('Operations') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/[0.03] dark:divide-white/[0.03]">
                                <template x-for="folder in filteredFolders" :key="folder.id">
                                    <tr class="group transition hover:bg-black/[0.005] dark:hover:bg-white/[0.01]">
                                        <td class="px-6 py-5 align-top">
                                            <input type="checkbox" x-model="selectedFolders" :value="folder.id" class="folder-checkbox h-4 w-4 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm">
                                        </td>
                                        <td class="px-6 py-5 align-top">
                                            <div class="flex items-start gap-4">
                                                <div class="h-[52px] w-[52px] shrink-0 overflow-hidden rounded-xl border border-black/5 shadow-sm dark:border-white/5 bg-white dark:bg-[#1e1f22] flex items-center justify-center text-lg font-black text-discord-green" x-text="folder.name.substring(0, 1).toUpperCase()">
                                                </div>
                                                <div class="flex flex-col pt-0.5">
                                                    <span class="text-[13px] font-black leading-tight text-[#1e1f22] dark:text-white transition-colors group-hover:text-indigo-600" x-text="folder.name"></span>
                                                    <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                                        <span class="text-[9px] font-black tracking-[0.1em] text-[#80848e] uppercase" x-text="'/' + folder.subdomain"></span>
                                                        <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                                                        <span class="inline-flex items-center text-[8px] font-black uppercase tracking-widest text-slate-400" x-text="folder.tax_id || 'NO-TAX-ID'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 align-top text-center pt-6">
                                            <div class="flex flex-col items-center" x-if="folder.owner">
                                                <span class="text-[11px] font-black text-[#1e1f22] dark:text-white" x-text="folder.owner.name"></span>
                                                <span class="text-[9px] text-[#80848e]" x-text="folder.owner.email"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 align-top text-center pt-6 text-[11px] font-bold text-[#5c5e66] dark:text-[#b5bac1]">
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-black/[0.03] dark:border-white/[0.03] bg-white dark:bg-transparent">
                                                <span class="text-[11px] font-black text-[#1e1f22] dark:text-white" x-text="Number(folder.slips_count).toLocaleString()"></span>
                                                <span class="text-[8px] font-black uppercase tracking-widest text-[#80848e]">{{ __('Slips') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 align-top text-center pt-6">
                                            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-[8px] font-black uppercase tracking-widest border"
                                                :class="{
                                                    'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10': folder.status === 'active',
                                                    'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10': folder.status !== 'active'
                                                }"
                                                x-text="folder.status.toUpperCase()">
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 align-top text-right pt-6">
                                            <div class="flex items-center justify-end gap-1.5 opacity-0 transition-opacity group-hover:opacity-100">
                                                <a :href="'{{ \App\Support\OwnerUrl::base(request()) }}/folders/' + folder.id" class="h-8 w-8 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/10 flex items-center justify-center text-[#80848e] transition hover:text-indigo-600 shadow-sm">
                                                    <i class="bi bi-pencil-square text-sm"></i>
                                                </a>
                                                <a :href="'/folders/open/' + folder.id" target="_blank" class="h-8 w-8 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/10 flex items-center justify-center text-[#80848e] transition hover:text-discord-green shadow-sm" title="Launch Workspace">
                                                    <i class="bi bi-box-arrow-up-right text-xs"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Stats -->
                <div class="flex items-center justify-between px-2 pt-4">
                    <div class="text-[9px] font-black text-[#80848e] uppercase tracking-[0.2em]">
                        {{ __('Total Capacity') }}: <span class="text-slate-900 dark:text-white" x-text="allFolders.length"></span> {{ __('Active Folders') }}
                    </div>
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
                            'slips_count' => $p->slips_count,
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
@endsection
