@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8 animate-in fade-in duration-500" x-data="userRegistry()" @refresh-users.window="fetchUsers()">
        
        <!-- Stats Grid (High Density) -->
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="rounded-xl bg-white p-4 shadow-sm border border-black/[0.03] dark:bg-[#2b2d31] dark:border-white/[0.03]">
                <p class="text-[9px] font-black uppercase tracking-widest text-[#80848e]">{{ __('Active Users') }}</p>
                <h3 class="mt-1 text-2xl font-black text-[#1e1f22] dark:text-white" x-text="allUsers.filter(u => u.status === 'active').length"></h3>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm border border-black/[0.03] dark:bg-[#2b2d31] dark:border-white/[0.03]">
                <p class="text-[9px] font-black uppercase tracking-widest text-[#80848e]">{{ __('Total Folders') }}</p>
                <h3 class="mt-1 text-2xl font-black text-[#1e1f22] dark:text-white" x-text="allUsers.reduce((acc, u) => acc + (u.merchants_count || 0), 0)"></h3>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm border border-black/[0.03] dark:bg-[#2b2d31] dark:border-white/[0.03]">
                <p class="text-[9px] font-black uppercase tracking-widest text-[#80848e]">{{ __('Total Slips') }}</p>
                <h3 class="mt-1 text-2xl font-black text-[#1e1f22] dark:text-white" x-text="allUsers.reduce((acc, u) => acc + (u.slips_count || 0), 0)"></h3>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm border border-black/[0.03] dark:bg-[#2b2d31] dark:border-white/[0.03]">
                <p class="text-[9px] font-black uppercase tracking-widest text-[#80848e]">{{ __('System Tokens') }}</p>
                <h3 class="mt-1 text-2xl font-black text-indigo-500" x-text="allUsers.reduce((acc, u) => acc + (u.tokens || 0), 0).toLocaleString()"></h3>
            </div>
        </div>

        <!-- Master Container Card -->
        <div class="rounded-xl bg-white p-6 sm:p-8 shadow-sm border border-black/[0.03] dark:bg-[#2b2d31] dark:border-white/[0.03]">
            
            <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-500 text-2xl shadow-sm">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">{{ __('User Management') }}</h1>
                        <p class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest mt-1">{{ __('Authorized Entities & Access Control') }}</p>
                    </div>
                </div>

                <x-ui.button variant="success" size="lg" icon="bi bi-person-plus-fill" @click="$dispatch('open-modal', { name: 'user-create' })">
                    {{ __('Create User') }}
                </x-ui.button>
            </div>

            <!-- Filters Section -->
            <div class="mb-8 grid grid-cols-1 gap-3 sm:grid-cols-12">
                <div class="relative sm:col-span-11">
                    <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-[#80848e] z-10 text-sm"></i>
                    <input type="text" x-model.debounce.300ms="filters.q" placeholder="{{ __('Search users by name, username or email...') }}" 
                           class="h-10 w-full rounded-xl border border-black/[0.05] bg-[#f8fafb] dark:bg-[#1e1f22] pl-12 pr-4 text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500/10 transition-all dark:text-white">
                </div>
                
                <div class="sm:col-span-1">
                    <x-ui.button variant="danger" size="md" icon="bi bi-arrow-counterclockwise" @click="resetFilters()" title="{{ __('Reset Filters') }}" class="w-full h-10" />
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-hidden relative min-h-[400px]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[11px] font-bold text-[#1e1f22] dark:text-[#b5bac1]">
                        <thead class="border-y border-black/[0.03] text-[9px] font-black uppercase tracking-widest text-[#80848e] dark:border-white/[0.03]">
                            <tr>
                                <th class="px-4 py-4">{{ __('Identity') }}</th>
                                <th class="px-4 py-4 text-center">{{ __('Role') }}</th>
                                <th class="px-4 py-4 text-center">{{ __('Folders') }}</th>
                                <th class="px-4 py-4 text-center">{{ __('Usage') }}</th>
                                <th class="px-4 py-4 text-center">{{ __('Registered') }}</th>
                                <th class="px-4 py-4 text-right" style="width: 100px;">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/[0.03] dark:divide-white/[0.03]">
                            <template x-for="user in paginatedUsers" :key="user.id">
                                <tr class="group transition hover:bg-[#fafcfa] dark:hover:bg-white/[0.01]" :class="user.status === 'suspended' ? 'bg-rose-50/50 dark:bg-rose-500/5' : ''">
                                    <td class="px-4 py-5 align-top">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 shrink-0 overflow-hidden rounded-xl border border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-[#1e1f22] flex items-center justify-center text-xs font-black text-indigo-500" x-text="user.name.substring(0, 1).toUpperCase()"></div>
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-2"><span class="text-[#1e1f22] dark:text-white leading-tight" x-text="user.name"></span><template x-if="user.status === 'suspended'"><x-ui.badge variant="danger" class="!px-1.5 !py-0.5 !text-[7px] animate-pulse">
                                                            {{ __('Suspended') }}
                                                        </x-ui.badge></template></div>
                                                <span class="text-[8px] text-[#80848e] uppercase tracking-widest mt-0.5" x-text="user.email"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6">
                                        <span class="inline-flex items-center rounded-lg border border-black/5 bg-black/[0.02] px-2 py-0.5 text-[8px] font-black uppercase tracking-widest text-[#5c5e66] dark:text-[#b5bac1] dark:bg-white/5" x-text="getRoleLabel(user.role)"></span>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6">
                                        <div class="flex flex-col items-center">
                                            <span class="text-[#1e1f22] dark:text-white" x-text="(user.merchants_count || 0) + ' / ' + (user.max_folders || 0)"></span>
                                            <span class="text-[8px] text-[#80848e] uppercase tracking-widest">{{ __('Entities') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6">
                                        <div class="flex flex-col items-center">
                                            <span class="text-indigo-500" x-text="Number(user.slips_count || 0).toLocaleString() + ' {{ __('Slips') }}'"></span>
                                            <span class="text-[8px] text-[#80848e] uppercase tracking-widest" x-text="Number(user.tokens || 0).toLocaleString() + ' {{ __('Tokens') }}'"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6">
                                        <span class="text-[#80848e] text-[10px]" x-text="formatDate(user.created_at)"></span>
                                    </td>
                                    <td class="px-4 py-5 align-top text-right pt-4">
                                        <x-ui.button variant="ghost" size="sm" icon="bi bi-pencil-square" ::href="'/admin/users/' + user.id">
                                            {{ __('Details') }}
                                        </x-ui.button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredUsers.length === 0">
                                <tr>
                                    <td colspan="6" class="py-24 text-center">
                                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-xl bg-[#f8fafb] border border-black/[0.02] shadow-sm dark:bg-[#1e1f22] dark:border-white/5 text-3xl text-[#80848e]">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <h3 class="text-[13px] font-black text-[#1e1f22] dark:text-white">{{ __('No users found') }}</h3>
                                        <p class="mt-1 text-xs font-bold text-[#80848e]">{{ __('Search using other terms or click Add Account to add new') }}</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination & Summary -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-black/[0.04] pt-6 dark:border-white/[0.04]" x-show="allUsers.length > 0">
                <div class="flex items-center gap-4">
                    <x-ui.dropdown 
                        width="w-24" 
                        model="perPage" 
                        position="top"
                        :options="[
                            ['v' => 20, 'l' => '20 / ' . __('Page')],
                            ['v' => 50, 'l' => '50 / ' . __('Page')],
                            ['v' => 100, 'l' => '100 / ' . __('Page')]
                        ]" 
                    />

                    <div class="text-[11px] font-bold text-[#80848e]">
                        {{ __('Showing') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="paginatedUsers.length"></span> {{ __('of') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="filteredUsers.length"></span> {{ __('Account Entities') }}
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <template x-for="link in generatePagination()">
                        <button @click="if(link.page) currentPage = link.page" 
                                :disabled="!link.page || link.active"
                                class="h-8 min-w-[32px] rounded-xl px-3 text-[10px] font-black uppercase transition-all border border-transparent"
                                :class="{
                                    'bg-discord-green text-white shadow-lg shadow-green-500/20': link.active,
                                    'bg-[#f8fafb] text-[#5c5e66] hover:bg-black/5 dark:bg-white/5 dark:text-[#949ba4] dark:hover:bg-white/10': !link.active && link.page,
                                    'opacity-20 cursor-not-allowed dark:text-[#4f545c]': !link.page && link.label !== '...'
                                }"
                                x-html="formatPaginationLabel(link.label)">
                        </button>
                    </template>
                </div>
            </div>
        </div>

        @include('main.user-create')
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userRegistry', () => ({
                allUsers: [],
                filteredUsers: [],
                currentPage: 1,
                perPage: 20,
                is_loading: false,
                stats: {!! json_encode($stats) !!},
                filters: {
                    q: '',
                    role: '',
                    status: ''
                },

                init() {
                    this.fetchUsers();

                    this.$watch('filters', (value) => {
                        this.applyFilters();
                        this.currentPage = 1; 
                    }, { deep: true });

                    this.$watch('perPage', (value) => {
                        this.currentPage = 1;
                    });
                },

                async fetchUsers() {
                    this.is_loading = true;
                    try {
                        const response = await fetch('{{ route('admin.users') }}', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const json = await response.json();
                        if (json.status === 'success') {
                            this.allUsers = json.users;
                            this.stats = json.stats;
                            this.applyFilters();
                        }
                    } catch (error) {
                        console.error('Fetch error:', error);
                    } finally {
                        this.is_loading = false;
                    }
                },

                get paginatedUsers() {
                    const start = (this.currentPage - 1) * this.perPage;
                    const end = start + parseInt(this.perPage);
                    return this.filteredUsers.slice(start, end);
                },

                get totalPages() {
                    return Math.ceil(this.filteredUsers.length / this.perPage) || 1;
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
                    if (label === 'Previous') return '&laquo; {{ __("Previous") }}';
                    if (label === 'Next') return '{{ __("Next") }} &raquo;';
                    return label;
                },

                applyFilters() {
                    let result = this.allUsers;
                    if (this.filters.q.trim() !== '') {
                        const term = this.filters.q.toLowerCase();
                        result = result.filter(u => 
                            u.name.toLowerCase().includes(term) || 
                            u.email.toLowerCase().includes(term) || 
                            (u.username && u.username.toLowerCase().includes(term)) ||
                            (u.folder_names && u.folder_names.toLowerCase().includes(term))
                        );
                    }
                    if (this.filters.role !== '') result = result.filter(u => u.role == parseInt(this.filters.role));
                    if (this.filters.status !== '') result = result.filter(u => u.status === this.filters.status);
                    this.filteredUsers = result;
                },

                resetFilters() {
                    this.filters.q = ''; this.filters.role = ''; this.filters.status = '';
                },

                getRoleLabel(roleId) {
                    switch (parseInt(roleId)) {
                        case 9: return '{{ __('Super Admin') }}';
                        case 5: return '{{ __('Tenant Admin') }}';
                        default: return '{{ __('User') }}';
                    }
                },

                formatDate(isoString) {
                    if (!isoString) return '-';
                    const date = new Date(isoString);
                    return date.toLocaleDateString('{{ app()->getLocale() == "th" ? "th-TH" : "en-GB" }}', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                }
            }));
        });
    </script>
    @endpush
@endsection
