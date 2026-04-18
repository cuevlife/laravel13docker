@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8 animate-in fade-in duration-500" x-data="userRegistry()">
        
        <!-- Stats Grid (High Density) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white dark:bg-[#2b2d31] p-5 rounded-xl border border-black/[0.03] dark:border-white/[0.03] shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-[#80848e] mb-1">{{ __('Total Users') }}</p>
                <p class="text-xl font-black text-[#1e1f22] dark:text-white">{{ number_format($stats['users']) }}</p>
            </div>
            <div class="bg-white dark:bg-[#2b2d31] p-5 rounded-xl border border-black/[0.03] dark:border-white/[0.03] shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-[#80848e] mb-1">{{ __('Active') }}</p>
                <p class="text-xl font-black text-discord-green">{{ number_format($stats['activeUsers']) }}</p>
            </div>
            <div class="bg-white dark:bg-[#2b2d31] p-5 rounded-xl border border-black/[0.03] dark:border-white/[0.03] shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-[#80848e] mb-1">{{ __('Suspended') }}</p>
                <p class="text-xl font-black text-rose-500">{{ number_format($stats['suspended']) }}</p>
            </div>
            <div class="bg-white dark:bg-[#2b2d31] p-5 rounded-xl border border-black/[0.03] dark:border-white/[0.03] shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-[#80848e] mb-1">{{ __('Admins') }}</p>
                <p class="text-xl font-black text-indigo-500">{{ number_format($stats['admins']) }}</p>
            </div>
            <div class="bg-white dark:bg-[#2b2d31] p-5 rounded-xl border border-black/[0.03] dark:border-white/[0.03] shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-[#80848e] mb-1">{{ __('Total Tokens') }}</p>
                <p class="text-xl font-black text-amber-500">{{ number_format($stats['tokens']) }}</p>
            </div>
            <div class="bg-white dark:bg-[#2b2d31] p-5 rounded-xl border border-black/[0.03] dark:border-white/[0.03] shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-[#80848e] mb-1">{{ __('Total Slips') }}</p>
                <p class="text-xl font-black text-[#5c5e66] dark:text-[#b5bac1]">{{ number_format($stats['slips']) }}</p>
            </div>
        </div>

        <!-- Master Container Card -->
        <div class="rounded-xl bg-white p-6 sm:p-8 shadow-sm border border-black/[0.03] dark:bg-[#2b2d31] dark:border-white/[0.03]">
            <!-- Header Section -->
            <div class="mb-10 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-discord-green/10 text-discord-green text-2xl">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">{{ __('User Management') }}</h1>
                        <p class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest mt-1">{{ __('Control Plane / System Registry') }}</p>
                    </div>
                </div>
                
                <a href="{{ \App\Support\OwnerUrl::path(request(), 'users/create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-discord-green px-6 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-green-500/20 transition hover:bg-[#1f8b4c] active:scale-95">
                    <i class="bi bi-person-plus-fill text-base"></i>
                    <span>{{ __('Add Account') }}</span>
                </a>
            </div>

            <!-- Filters Section -->
            <div class="mb-8 grid grid-cols-1 gap-3 sm:grid-cols-12">
                <div class="relative sm:col-span-5">
                    <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-[#80848e] z-10 text-sm"></i>
                    <input type="text" x-model.debounce.300ms="filters.q" placeholder="{{ __('Search by name, email or username...') }}" 
                           class="h-10 w-full rounded-xl border border-black/[0.05] bg-[#f8fafb] dark:bg-[#1e1f22] pl-12 pr-4 text-xs font-bold outline-none focus:ring-2 focus:ring-discord-green/10 transition-all dark:text-white">
                </div>
                
                <div class="sm:col-span-3">
                    <select x-model="filters.role" class="h-10 w-full rounded-xl border border-black/[0.05] bg-[#f8fafb] dark:bg-[#1e1f22] px-3 text-xs font-bold outline-none focus:ring-2 focus:ring-discord-green/10 transition-all dark:text-white">
                        <option value="">{{ __('All Roles') }}</option>
                        <option value="1">{{ __('User') }}</option>
                        <option value="5">{{ __('Admin (Tenant)') }}</option>
                        <option value="9">{{ __('Super Admin') }}</option>
                        </select>
                        </div>

                        <div class="sm:col-span-3">
                        <select x-model="filters.status" class="h-10 w-full rounded-xl border border-black/[0.05] bg-[#f8fafb] dark:bg-[#1e1f22] px-3 text-xs font-bold outline-none focus:ring-2 focus:ring-discord-green/10 transition-all dark:text-white">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="active">{{ __('Active') }}</option>
                        <option value="suspended">{{ __('Suspended') }}</option>
                        </select>
                        </div>

                <div class="sm:col-span-1">
                    <button @click="resetFilters()" class="flex h-10 w-full items-center justify-center rounded-xl border border-rose-100 bg-rose-50 text-rose-500 shadow-sm transition hover:bg-rose-100 dark:border-rose-500/10 dark:bg-rose-500/5" title="{{ __('Reset Filters') }}">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-hidden relative min-h-[400px]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[11px] font-bold text-[#1e1f22] dark:text-[#b5bac1]">
                        <thead class="border-y border-black/[0.03] text-[9px] font-black uppercase tracking-widest text-[#80848e] dark:border-white/[0.03]">
                            <tr>
                                <th class="px-4 py-4">{{ __('Identity') }}</th>
                                <th class="px-4 py-4 text-center" style="width: 150px;">{{ __('Access Layer') }}</th>
                                <th class="px-4 py-4 text-center" style="width: 120px;">{{ __('Tokens') }}</th>
                                <th class="px-4 py-4 text-center" style="width: 100px;">{{ __('Folders') }}</th>
                                <th class="px-4 py-4 text-center" style="width: 100px;">{{ __('Slips') }}</th>
                                <th class="px-4 py-4 text-right" style="width: 100px;">{{ __('Operations') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/[0.03] dark:divide-white/[0.03]">
                            <template x-for="user in paginatedUsers" :key="user.id">
                                <tr class="group transition hover:bg-[#fafcfa] dark:hover:bg-white/[0.01]">
                                    <td class="px-4 py-5 align-top">
                                        <div class="flex items-start gap-4">
                                            <div class="h-[52px] w-[52px] shrink-0 overflow-hidden rounded-xl border border-black/[0.03] shadow-sm dark:border-white/[0.03] bg-[#f8fafb] dark:bg-[#1e1f22] flex items-center justify-center text-lg font-black text-rose-500" x-text="user.name.substring(0, 1).toUpperCase()">
                                            </div>
                                            <div class="flex flex-col pt-0.5">
                                                <span class="text-[13px] font-black leading-tight text-[#1e1f22] dark:text-white transition-colors group-hover:text-discord-green" x-text="user.name"></span>
                                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                                    <span class="text-[10px] font-black tracking-widest text-[#80848e]" x-text="user.email"></span>
                                                    <template x-if="user.username">
                                                        <div class="flex items-center gap-2">
                                                            <span class="h-1 w-1 rounded-full bg-[#e3e5e8] dark:bg-white/10"></span>
                                                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-1.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-emerald-600 dark:bg-emerald-500/10" x-text="'@' + user.username"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6">
                                        <div class="flex flex-col items-center gap-1.5">
                                            <span class="inline-flex items-center rounded-full border border-slate-200 px-2.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-slate-500 dark:border-white/10 dark:text-slate-300" x-text="getRoleLabel(user.role)">
                                            </span>
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[8px] font-black uppercase tracking-widest border"
                                                :class="{
                                                    'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20': user.status === 'active',
                                                    'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:border-amber-500/20': user.status !== 'active'
                                                }"
                                                x-text="user.status === 'active' ? '{{ __('ACTIVE') }}' : '{{ __('SUSPENDED') }}'">
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6">
                                        <span class="text-[13px] font-black tracking-tight text-[#1e1f22] dark:text-white" x-text="Number(user.tokens).toLocaleString()"></span>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6 text-[11px] font-bold text-[#5c5e66] dark:text-[#b5bac1]">
                                        <span x-text="user.merchants_count"></span> / <span x-text="user.max_folders"></span>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6 text-[11px] font-bold text-[#5c5e66] dark:text-[#b5bac1]">
                                        <span x-text="user.slips_count"></span>
                                    </td>
                                    <td class="px-4 py-5 align-top text-right pt-6">
                                        <div class="flex items-center justify-end gap-1.5 opacity-0 transition-opacity group-hover:opacity-100">
                                            <a :href="'{{ \App\Support\OwnerUrl::base(request()) }}/users/' + user.id" class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#f8fafb] dark:bg-[#1e1f22] border border-black/[0.05] text-[#80848e] transition hover:text-discord-green hover:border-discord-green/30 shadow-sm">
                                                <i class="bi bi-pencil-square text-sm"></i>
                                            </a>
                                            <form method="POST" :action="'{{ \App\Support\OwnerUrl::base(request()) }}/users/' + user.id + '/status'" class="m-0">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" :value="user.status === 'active' ? 'suspended' : 'active'">
                                                <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#f8fafb] dark:bg-[#1e1f22] border border-black/[0.05] text-[#80848e] transition shadow-sm" :class="user.status === 'active' ? 'hover:text-amber-500 hover:border-amber-500/30' : 'hover:text-emerald-500 hover:border-emerald-500/30'" :title="user.status === 'active' ? '{{ __('Suspend User') }}' : '{{ __('Reactivate User') }}'">
                                                    <i :class="user.status === 'active' ? 'bi bi-person-x-fill' : 'bi bi-person-check-fill'" class="text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
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

            <!-- Pagination & Summary (Standard Style) -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-black/[0.04] pt-6 dark:border-white/[0.04]" x-show="allUsers.length > 0">
                <!-- Summary & Per Page (Left Side) -->
                <div class="flex items-center gap-4">
                    <div class="text-[11px] font-bold text-[#80848e]">
                        {{ __('Showing') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="paginatedUsers.length"></span> {{ __('of') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="filteredUsers.length"></span> {{ __('Account Entities') }}
                    </div>
                    
                    <select x-model="perPage" class="h-8 rounded-lg border border-black/5 bg-[#f8fafb] px-2 text-[10px] font-black outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white transition-all">
                        <option value="20">20 / {{ __('Page') }}</option>
                        <option value="50">50 / {{ __('Page') }}</option>
                        <option value="100">100 / {{ __('Page') }}</option>
                    </select>
                </div>

                <!-- Pagination Controls (Right Side) -->
                <div class="flex items-center gap-2">
                    <template x-for="link in generatePagination()">
                        <button @click="if(link.page) currentPage = link.page" 
                                :disabled="!link.page || link.active"
                                class="h-8 min-w-[32px] rounded-xl px-2 text-[10px] font-black uppercase transition-all"
                                :class="{
                                    'bg-discord-green text-white shadow-lg shadow-green-500/20': link.active,
                                    'bg-[#f8fafb] text-[#5c5e66] hover:bg-black/5 dark:bg-[#1e1f22] dark:text-[#b5bac1]': !link.active && link.page,
                                    'opacity-30 cursor-not-allowed': !link.page && link.label !== '...'
                                }"
                                x-html="formatPaginationLabel(link.label)">
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userRegistry', () => ({
                allUsers: [],
                filteredUsers: [],
                currentPage: 1,
                perPage: 20,
                filters: {
                    q: '',
                    role: '',
                    status: ''
                },

                init() {
                    const rawUsers = {!! json_encode($users->map(function($u) {
                        return [
                            'id' => $u->id,
                            'name' => $u->name,
                            'email' => $u->email,
                            'username' => $u->username,
                            'role' => $u->role,
                            'status' => $u->status,
                            'tokens' => $u->tokens,
                            'merchants_count' => $u->merchants_count,
                            'max_folders' => $u->max_folders,
                            'slips_count' => $u->slips_count,
                            'created_at' => optional($u->created_at)->format('Y-m-d H:i:s')
                        ];
                    })) !!};
                    
                    this.allUsers = rawUsers;
                    this.filteredUsers = rawUsers;

                    this.$watch('filters', (value) => {
                        this.applyFilters();
                        this.currentPage = 1; // Reset to first page on filter change
                    }, { deep: true });

                    this.$watch('perPage', (value) => {
                        this.currentPage = 1;
                    });
                },

                get paginatedUsers() {
                    const start = (this.currentPage - 1) * this.perPage;
                    const end = start + this.perPage;
                    return this.filteredUsers.slice(start, end);
                },

                get totalPages() {
                    return Math.ceil(this.filteredUsers.length / this.perPage) || 1;
                },

                generatePagination() {
                    const links = [];
                    const current = parseInt(this.currentPage);
                    const total = parseInt(this.totalPages);
                    
                    // Previous
                    links.push({
                        label: 'Previous',
                        active: false,
                        page: current > 1 ? current - 1 : null
                    });

                    // Logic to show limited pages
                    if (total <= 7) {
                        for (let i = 1; i <= total; i++) {
                            links.push({ label: i.toString(), active: current === i, page: i });
                        }
                    } else {
                        if (current <= 4) {
                            for (let i = 1; i <= 5; i++) {
                                links.push({ label: i.toString(), active: current === i, page: i });
                            }
                            links.push({ label: '...', active: false, page: null });
                            links.push({ label: total.toString(), active: false, page: total });
                        } else if (current > total - 4) {
                            links.push({ label: '1', active: false, page: 1 });
                            links.push({ label: '...', active: false, page: null });
                            for (let i = total - 4; i <= total; i++) {
                                links.push({ label: i.toString(), active: current === i, page: i });
                            }
                        } else {
                            links.push({ label: '1', active: false, page: 1 });
                            links.push({ label: '...', active: false, page: null });
                            for (let i = current - 1; i <= current + 1; i++) {
                                links.push({ label: i.toString(), active: current === i, page: i });
                            }
                            links.push({ label: '...', active: false, page: null });
                            links.push({ label: total.toString(), active: false, page: total });
                        }
                    }

                    // Next
                    links.push({
                        label: 'Next',
                        active: false,
                        page: current < total ? current + 1 : null
                    });

                    return links;
                },

                formatPaginationLabel(label) {
                    if (label === 'Previous') return '&laquo; Previous';
                    if (label === 'Next') return 'Next &raquo;';
                    return label;
                },

                applyFilters() {
                    let result = this.allUsers;
                    
                    if (this.filters.q.trim() !== '') {
                        const term = this.filters.q.toLowerCase();
                        result = result.filter(u => 
                            u.name.toLowerCase().includes(term) || 
                            u.email.toLowerCase().includes(term) ||
                            (u.username && u.username.toLowerCase().includes(term))
                        );
                    }

                    if (this.filters.role !== '') {
                        result = result.filter(u => u.role == parseInt(this.filters.role));
                    }

                    if (this.filters.status !== '') {
                        result = result.filter(u => u.status === this.filters.status);
                    }

                    this.filteredUsers = result;
                },

                resetFilters() {
                    this.filters.q = '';
                    this.filters.role = '';
                    this.filters.status = '';
                },

                getRoleLabel(roleId) {
                    switch (parseInt(roleId)) {
                        case 9: return '{{ __('Super Admin') }}';
                        case 5: return '{{ __('Tenant Admin') }}';
                        default: return '{{ __('User') }}';
                    }
                }
            }));
        });
    </script>
    @endpush
@endsection
