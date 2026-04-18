@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8 animate-in fade-in duration-500" x-data="userRegistry()">
        <div class="rounded-xl bg-white p-6 sm:p-8 shadow-sm border border-black/[0.04] dark:bg-[#2b2d31] dark:border-white/5">
            <!-- Header Section -->
            <div class="mb-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-discord-green/10 text-discord-green text-2xl">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">{{ __('User Management') }}</h1>
                        <p class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest mt-1">{{ __('Control Plane / System Registry') }}</p>
                    </div>
                </div>
                
                <a href="{{ \App\Support\OwnerUrl::path(request(), 'users/create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-discord-green px-6 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-green-500/20 transition hover:bg-[#1f8b4c]">
                    <i class="bi bi-person-plus-fill text-base"></i>
                    <span>{{ __('Add Account') }}</span>
                </a>
            </div>

            <!-- Filters Section -->
            <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-12">
                <div class="relative sm:col-span-6">
                    <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-[#80848e] z-10"></i>
                    <input type="text" x-model.debounce.300ms="filters.q" placeholder="ค้นหา (ชื่อ, อีเมล)..." 
                           class="h-10 w-full rounded-xl border border-black/5 bg-white pl-14 pr-4 text-xs font-bold outline-none shadow-sm focus:border-discord-green/30 dark:bg-[#1e1f22] dark:text-white transition-all">
                </div>
                
                <div class="sm:col-span-2">
                    <select x-model="filters.role" class="h-10 w-full rounded-xl border border-black/5 bg-white px-3 text-xs font-bold outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white transition-all">
                        <option value="">ทุกระดับสิทธิ์</option>
                        <option value="1">User</option>
                        <option value="5">Admin</option>
                        <option value="9">Super</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <select x-model="filters.status" class="h-10 w-full rounded-xl border border-black/5 bg-white px-3 text-xs font-bold outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white transition-all">
                        <option value="">ทุกสถานะ</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <button @click="resetFilters()" class="flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-rose-100 bg-rose-50 text-[10px] font-black uppercase tracking-widest text-rose-500 shadow-sm transition hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10">
                        <i class="bi bi-arrow-counterclockwise text-xs"></i> ล้างค่า
                    </button>
                </div>
            </div>

            <!-- Bulk Actions Row -->
            <div class="mb-4 flex flex-wrap items-center justify-between gap-4 border-t border-black/[0.04] pt-4 dark:border-white/[0.04]">
                <div class="flex items-center gap-3">
                    <div class="inline-flex h-9 items-center rounded-xl bg-[#f2f7ff] px-4 text-[10px] font-black text-[#4f86f7]">
                        <span x-text="selectedUsers.length">0</span> {{ __('selected') }}
                    </div>
                    
                    <div class="flex items-center gap-1">
                        <button class="flex h-9 w-9 items-center justify-center rounded-xl border border-black/5 bg-white text-[#80848e] transition hover:border-[#4f86f7] hover:text-[#4f86f7] shadow-sm dark:bg-[#1e1f22]">
                            <i class="bi bi-shield-check text-sm"></i>
                        </button>
                        <button :disabled="selectedUsers.length === 0" class="flex h-9 w-9 items-center justify-center rounded-xl border border-rose-100 bg-rose-50 text-rose-500 transition hover:bg-rose-100 shadow-sm dark:border-rose-500/20 dark:bg-rose-500/10 disabled:opacity-50">
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
                                <th class="px-4 py-4" style="width: 40px;">
                                    <input type="checkbox" @click="toggleSelectAll()" :checked="selectedUsers.length === filteredUsers.length && filteredUsers.length > 0" class="h-4 w-4 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm">
                                </th>
                                <th class="px-4 py-4">{{ __('Identity') }}</th>
                                <th class="px-4 py-4 text-center" style="width: 150px;">{{ __('Access Layer') }}</th>
                                <th class="px-4 py-4 text-center" style="width: 120px;">{{ __('Tokens') }}</th>
                                <th class="px-4 py-4 text-center" style="width: 100px;">{{ __('Workspaces') }}</th>
                                <th class="px-4 py-4 text-center" style="width: 100px;">{{ __('Slips') }}</th>
                                <th class="px-4 py-4 text-right" style="width: 100px;">{{ __('Operations') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/[0.04] dark:divide-white/[0.04]">
                            <template x-for="user in filteredUsers" :key="user.id">
                                <tr class="group transition hover:bg-[#fafcfa] dark:hover:bg-white/[0.02]">
                                    <td class="px-4 py-5 align-top">
                                        <input type="checkbox" x-model="selectedUsers" :value="user.id" class="user-checkbox h-4 w-4 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm">
                                    </td>
                                    <td class="px-4 py-5 align-top">
                                        <div class="flex items-start gap-4">
                                            <div class="h-[52px] w-[52px] shrink-0 overflow-hidden rounded-xl border border-black/5 shadow-sm dark:border-white/5 bg-white dark:bg-[#1e1f22] flex items-center justify-center text-lg font-black text-rose-500" x-text="user.name.substring(0, 1).toUpperCase()">
                                            </div>
                                            <div class="flex flex-col pt-0.5">
                                                <span class="text-[13px] font-black leading-tight text-[#1e1f22] dark:text-white transition-colors group-hover:text-[#4f86f7]" x-text="user.name"></span>
                                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                                    <span class="text-[10px] font-black tracking-widest text-[#80848e]" x-text="user.email"></span>
                                                    <template x-if="user.username">
                                                        <div class="flex items-center gap-2">
                                                            <span class="h-1 w-1 rounded-full bg-[#e3e5e8]"></span>
                                                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-1.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-emerald-600 dark:bg-emerald-500/10" x-text="'@' + user.username"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="inline-flex items-center rounded-full border border-slate-200 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-slate-500 dark:border-white/10 dark:text-slate-300" x-text="getRoleLabel(user.role)">
                                            </span>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-black uppercase tracking-widest border border-black/10"
                                                :class="{
                                                    'bg-emerald-50 text-emerald-600 border-emerald-100': user.status === 'active',
                                                    'bg-amber-50 text-amber-600 border-amber-100': user.status !== 'active'
                                                }"
                                                x-text="user.status === 'active' ? 'ACTIVE' : 'SUSPENDED'">
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6">
                                        <span class="text-[13px] font-black tracking-tight text-[#1e1f22] dark:text-white" x-text="Number(user.tokens).toLocaleString()"></span>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6 text-[11px] font-bold text-[#5c5e66] dark:text-[#b5bac1]">
                                        <span x-text="user.merchants_count"></span>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center pt-6 text-[11px] font-bold text-[#5c5e66] dark:text-[#b5bac1]">
                                        <span x-text="user.slips_count"></span>
                                    </td>
                                    <td class="px-4 py-5 align-top text-right pt-6">
                                        <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                            <a :href="'{{ \App\Support\OwnerUrl::base(request()) }}/users/' + user.id" class="flex h-8 w-8 items-center justify-center rounded-xl text-[#80848e] transition hover:bg-black/5 hover:text-[#1e1f22] dark:hover:bg-white/5 dark:hover:text-white">
                                                <i class="bi bi-pencil-square text-sm"></i>
                                            </a>
                                            <form method="POST" :action="'{{ \App\Support\OwnerUrl::base(request()) }}/users/' + user.id + '/status'" class="m-0">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" :value="user.status === 'active' ? 'suspended' : 'active'">
                                                <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-xl text-[#80848e] transition hover:bg-black/5 dark:hover:bg-white/5" :class="user.status === 'active' ? 'hover:text-amber-500' : 'hover:text-emerald-500'" :title="user.status === 'active' ? '{{ __('Suspend User') }}' : '{{ __('Reactivate User') }}'">
                                                    <i :class="user.status === 'active' ? 'bi bi-person-x-fill' : 'bi bi-person-check-fill'" class="text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredUsers.length === 0">
                                <tr>
                                    <td colspan="7" class="py-24 text-center">
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

            <!-- Footer Stats -->
            <div class="mt-6 flex items-center justify-between border-t border-black/[0.04] pt-6 dark:border-white/[0.04]">
                <div class="text-[11px] font-bold text-[#80848e]">
                    {{ __('Showing') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="filteredUsers.length"></span> {{ __('of') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="allUsers.length"></span> {{ __('users') }}
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
                selectedUsers: [],
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
                            'merchants_count' => $u->merchants()->count(),
                            'slips_count' => $u->slips_count,
                            'created_at' => optional($u->created_at)->format('Y-m-d H:i:s')
                        ];
                    })) !!};
                    
                    this.allUsers = rawUsers;
                    this.filteredUsers = rawUsers;

                    this.$watch('filters', (value) => {
                        this.applyFilters();
                    }, { deep: true });
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

                toggleSelectAll() {
                    if (this.selectedUsers.length < this.filteredUsers.length) {
                        this.selectedUsers = this.filteredUsers.map(u => u.id);
                    } else {
                        this.selectedUsers = [];
                    }
                },

                getRoleLabel(roleId) {
                    switch (parseInt(roleId)) {
                        case 9: return 'Super Admin';
                        case 5: return 'Tenant Admin';
                        default: return 'User';
                    }
                }
            }));
        });
    </script>
    @endpush
@endsection
