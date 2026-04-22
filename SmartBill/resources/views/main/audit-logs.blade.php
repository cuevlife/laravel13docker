@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8 animate-in fade-in duration-500" x-data="auditRegistry()">
        
        <x-ui.card>
            {{-- Header Section --}}
            <div class="px-8 py-8 border-b border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/10">
                <x-ui.page-header 
                    :title="__('Global Audit')" 
                    :subtitle="__('Centralized Registry of All Activities')" 
                    icon="bi-journal-text"
                >
                    <x-slot:actions>
                        <x-ui.back-button :href="route('admin.users')" :title="__('Back to Dashboard')" />
                        
                        {{-- Tab Switcher --}}
                        <div class="flex items-center bg-white dark:bg-[#1e1f22] p-1 rounded-xl border border-black/[0.05] dark:border-white/5 shadow-sm">
                            <button @click="activeTab = 'audit'; currentPage = 1;"
                                    :class="activeTab === 'audit' ? 'bg-indigo-600 text-white shadow-md' : 'text-[#80848e] hover:text-[#5c5e66]'"
                                    class="px-5 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">
                                {{ __('Activity Logs') }}
                            </button>
                            <button @click="activeTab = 'tokens'; currentPage = 1;"
                                    :class="activeTab === 'tokens' ? 'bg-indigo-600 text-white shadow-md' : 'text-[#80848e] hover:text-[#5c5e66]'"
                                    class="px-5 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">
                                {{ __('Token Logs') }}
                            </button>
                        </div>
                    </x-slot:actions>
                </x-ui.page-header>
            </div>

            <div class="p-8 md:p-10 space-y-8">
                <!-- Filters Section -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-12">
                    <div class="relative sm:col-span-11">
                        <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-[#80848e] z-10 text-sm"></i>
                        <input type="text" x-model.debounce.300ms="filters.q" placeholder="{{ __('Search by operator, event or description...') }}"
                               class="h-12 w-full rounded-xl border border-black/[0.05] bg-[#f8fafb] dark:bg-[#1e1f22] pl-12 pr-4 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500/10 transition-all dark:text-white shadow-sm">
                    </div>
                    <div class="sm:col-span-1">
                        <x-ui.button variant="danger" size="lg" icon="bi-arrow-counterclockwise" @click="resetFilters()" title="{{ __('Reset Filters') }}" class="w-full h-12" />
                    </div>
                </div>

                <!-- Table Section -->
                <div class="overflow-hidden relative min-h-[400px]">
                    <div class="overflow-x-auto">
                        {{-- Activity Logs Table --}}
                        <table x-show="activeTab === 'audit'" x-transition.opacity.duration.300ms class="w-full text-left text-[11px] font-bold text-[#1e1f22] dark:text-[#b5bac1]">
                            <thead class="bg-black/[0.02] dark:bg-white/[0.02] text-[9px] font-black uppercase tracking-widest text-[#80848e]">
                                <tr>
                                    <th class="px-6 py-4">{{ __('Operator') }}</th>
                                    <th class="px-6 py-4 text-center" style="width: 150px;">{{ __('Event') }}</th>
                                    <th class="px-6 py-4">{{ __('Description') }}</th>
                                    <th class="px-6 py-4 text-center" style="width: 120px;">{{ __('Source IP') }}</th>
                                    <th class="px-6 py-4 text-right" style="width: 150px;">{{ __('Timestamp') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/[0.03] dark:divide-white/[0.03]">
                                <template x-for="log in paginatedData" :key="log.id">
                                    <tr class="group transition hover:bg-[#fafcfa] dark:hover:bg-white/[0.01]">
                                        <td class="px-6 py-5 align-top">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 shrink-0 overflow-hidden rounded-lg border border-black/[0.03] dark:border-white/[0.03] bg-white dark:bg-[#2b2d31] flex items-center justify-center text-[10px] font-black text-indigo-500 shadow-sm" x-text="log.user_name.substring(0, 1).toUpperCase()"></div>
                                                <div class="flex flex-col">
                                                    <span class="text-[#1e1f22] dark:text-white leading-tight" x-text="log.user_name"></span>
                                                    <span class="text-[8px] text-[#80848e] uppercase tracking-widest mt-0.5" x-text="'@' + log.username"></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 align-top text-center pt-6">
                                            <span class="inline-flex items-center rounded-lg border border-black/5 bg-black/[0.02] px-2 py-0.5 text-[8px] font-black uppercase tracking-widest text-[#5c5e66] dark:text-[#b5bac1] dark:bg-white/5" x-text="log.event ? log.event.replace(/_/g, ' ') : ''"></span>
                                        </td>
                                        <td class="px-6 py-5 align-top pt-6 text-[#1e1f22] dark:text-white">
                                            <span class="block truncate max-w-xs xl:max-w-md" x-text="log.description" :title="log.description"></span>
                                        </td>
                                        <td class="px-6 py-5 align-top text-center pt-6 text-[#80848e] font-mono text-[10px]" x-text="log.ip_address || '-'"></td>
                                        <td class="px-6 py-5 align-top text-right text-[#80848e] font-medium">
                                            <span class="block text-[10px]" x-text="formatDate(log.created_at)"></span>
                                            <span class="block text-[8px] opacity-50" x-text="formatTime(log.created_at)"></span>     
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        {{-- Token Logs Table --}}
                        <table x-show="activeTab === 'tokens'" x-transition.opacity.duration.300ms class="w-full text-left text-[11px] font-bold text-[#1e1f22] dark:text-[#b5bac1]">
                            <thead class="bg-black/[0.02] dark:bg-white/[0.02] text-[9px] font-black uppercase tracking-widest text-[#80848e]">
                                <tr>
                                    <th class="px-6 py-4">{{ __('Subject') }}</th>
                                    <th class="px-6 py-4">{{ __('Description') }}</th>
                                    <th class="px-6 py-4 text-center" style="width: 150px;">{{ __('Type') }}</th>
                                    <th class="px-6 py-4 text-right" style="width: 100px;">{{ __('Amount') }}</th>
                                    <th class="px-6 py-4 text-right" style="width: 100px;">{{ __('Balance') }}</th>
                                    <th class="px-6 py-4 text-right" style="width: 150px;">{{ __('Timestamp') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/[0.03] dark:divide-white/[0.03]">
                                <template x-for="log in paginatedData" :key="log.id">
                                    <tr class="group transition hover:bg-[#fafcfa] dark:hover:bg-white/[0.01]">
                                        <td class="px-6 py-5 align-top">
                                            <div class="flex flex-col">
                                                <span class="text-[#1e1f22] dark:text-white leading-tight" x-text="log.user_name"></span>
                                                <span class="text-[8px] text-[#80848e] uppercase tracking-widest mt-0.5" x-text="'@' + log.username"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 align-top text-[#1e1f22] dark:text-white pt-6">
                                            <span class="block truncate max-w-xs xl:max-w-md" x-text="log.description" :title="log.description"></span>
                                        </td>
                                        <td class="px-6 py-5 align-top text-center pt-6">
                                            <span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-[8px] font-black uppercase tracking-widest shadow-sm"
                                                  :class="['manual_credit', 'manual_topup_approved', 'manual_settlement'].includes(log.type) ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100'"
                                                  x-text="log.type.replace(/_/g, ' ')"></span>
                                        </td>
                                        <td class="px-6 py-5 align-top text-right pt-6" :class="log.delta < 0 ? 'text-rose-500' : 'text-discord-green'" x-text="(log.delta > 0 ? '+' : '') + Number(log.delta).toLocaleString()"></td>
                                        <td class="px-6 py-5 align-top text-right text-[#80848e] pt-6" x-text="Number(log.balance_after).toLocaleString()"></td>
                                        <td class="px-6 py-5 align-top text-right text-[#80848e] font-medium pt-6">
                                            <span class="block text-[10px]" x-text="formatDate(log.created_at)"></span>
                                            <span class="block text-[8px] opacity-50" x-text="formatTime(log.created_at)"></span>     
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <template x-if="filteredData.length === 0">
                            <div class="py-24 text-center">
                                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-xl bg-[#f8fafb] border border-black/[0.02] shadow-sm dark:bg-[#1e1f22] dark:border-white/5 text-3xl text-[#80848e]">
                                    <i class="bi bi-search"></i>
                                </div>
                                <h3 class="text-[13px] font-black text-[#1e1f22] dark:text-white">{{ __('No records found') }}</h3>   
                                <p class="mt-1 text-xs font-bold text-[#80848e]">{{ __('Search using other terms or check back later') }}</p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Pagination & Summary -->
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-black/[0.04] pt-8 dark:border-white/[0.04]" x-show="activeCollection.length > 0">
                    <div class="flex items-center gap-4">
                        <x-ui.dropdown
                            width="w-32"
                            model="perPage"
                            position="top"
                            :options="[
                                ['v' => 20, 'l' => '20 / ' . __('Page')],
                                ['v' => 50, 'l' => '50 / ' . __('Page')],
                                ['v' => 100, 'l' => '100 / ' . __('Page')]
                            ]"
                        />

                        <div class="text-[11px] font-bold text-[#80848e]">
                            {{ __('Showing') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="paginatedData.length"></span> {{ __('of') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="filteredData.length"></span> {{ __('Activity Records') }}
                        </div>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="flex items-center gap-1.5">
                        <template x-for="link in generatePagination()">
                            <button @click="if(link.page) currentPage = link.page"
                                    :disabled="!link.page || link.active"
                                    class="h-9 min-w-[36px] rounded-xl px-2 text-[10px] font-black uppercase transition-all"
                                    :class="{
                                        'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20': link.active,
                                        'bg-[#f8fafb] text-[#5c5e66] hover:bg-black/5 dark:bg-[#1e1f22] dark:text-[#b5bac1]': !link.active && link.page,
                                        'opacity-30 cursor-not-allowed': !link.page && link.label !== '...'
                                    }"
                                    x-html="formatPaginationLabel(link.label)">
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('auditRegistry', () => ({
                activeTab: 'audit',
                allAuditLogs: {!! json_encode($auditLogs->map(fn($l) => [
                    'id' => $l->id,
                    'user_name' => $l->user->name ?? 'System',
                    'username' => $l->user->username ?? 'system',
                    'event' => $l->event,
                    'description' => $l->description,
                    'ip_address' => $l->ip_address,
                    'created_at' => $l->created_at->toIso8601String()
                ])) !!},
                allTokenLogs: {!! json_encode($tokenLogs->map(fn($l) => [
                    'id' => $l->id,
                    'user_name' => $l->user->name ?? 'System',
                    'username' => $l->user->username ?? 'system',
                    'description' => $l->description,
                    'type' => $l->type,
                    'delta' => $l->delta,
                    'balance_after' => $l->balance_after,
                    'created_at' => $l->created_at->toIso8601String()
                ])) !!},
                currentPage: 1,
                perPage: 20,
                filters: { q: '' },

                init() {
                    this.$watch('filters', () => { this.currentPage = 1; }, { deep: true });
                    this.$watch('activeTab', () => { this.currentPage = 1; this.filters.q = ''; });
                },

                get activeCollection() {
                    return this.activeTab === 'audit' ? this.allAuditLogs : this.allTokenLogs;
                },

                get filteredData() {
                    let term = this.filters.q.toLowerCase().trim();
                    if (!term) return this.activeCollection;

                    return this.activeCollection.filter(i =>
                        i.user_name.toLowerCase().includes(term) ||
                        i.username.toLowerCase().includes(term) ||
                        i.description.toLowerCase().includes(term) ||
                        (i.event && i.event.toLowerCase().includes(term)) ||
                        (i.type && i.type.toLowerCase().includes(term))
                    );
                },

                get paginatedData() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredData.slice(start, start + parseInt(this.perPage));
                },

                get totalPages() {
                    return Math.ceil(this.filteredData.length / this.perPage) || 1;
                },

                generatePagination() {
                    const links = [];
                    const current = parseInt(this.currentPage);
                    const total = parseInt(this.totalPages);
                    links.push({ label: 'Previous', active: false, page: current > 1 ? current - 1 : null });
                    if (total <= 7) {
                        for (let i = 1; i <= total; i++) links.push({ label: i.toString(), active: current === i, page: i });     
                    } else {
                        if (current <= 4) {
                            for (let i = 1; i <= 5; i++) links.push({ label: i.toString(), active: current === i, page: i });     
                            links.push({ label: '...', active: false, page: null });
                            links.push({ label: total.toString(), active: false, page: total });
                        } else if (current > total - 4) {
                            links.push({ label: '1', active: false, page: 1 });
                            links.push({ label: '...', active: false, page: null });
                            for (let i = total - 4; i <= total; i++) links.push({ label: i.toString(), active: current === i, page: i });
                        } else {
                            links.push({ label: '1', active: false, page: 1 });
                            links.push({ label: '...', active: false, page: null });
                            for (let i = current - 1; i <= current + 1; i++) links.push({ label: i.toString(), active: current === i, page: i });
                            links.push({ label: '...', active: false, page: null });
                            links.push({ label: total.toString(), active: false, page: total });
                        }
                    }
                    links.push({ label: 'Next', active: false, page: current < total ? current + 1 : null });
                    return links;
                },

                formatPaginationLabel(label) {
                    if (label === 'Previous') return '&laquo;';
                    if (label === 'Next') return '&raquo;';
                    return label;
                },

                resetFilters() { this.filters.q = ''; this.perPage = 20; },
                formatDate(iso) { return new Date(iso).toLocaleDateString('th-TH', { day: '2-digit', month: '2-digit', year: 'numeric' }); },
                formatTime(iso) { return new Date(iso).toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' น.' }
            }));
        });
    </script>
    @endpush
@endsection