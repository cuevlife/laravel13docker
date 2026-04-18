@extends('layouts.app')

@section('content')
    <div class="w-full py-8 px-4 sm:px-6 lg:px-8 animate-in fade-in duration-500">
        
        <!-- Master Container Card -->
        <div class="bg-white dark:bg-[#2b2d31] rounded-xl shadow-sm border border-black/[0.03] dark:border-white/[0.03] overflow-hidden">
            
            {{-- Header Section --}}
            <div class="px-8 py-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="w-10 h-10 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/[0.03] dark:border-white/[0.03] flex items-center justify-center text-[#80848e] hover:text-discord-green transition-all shadow-sm">
                            <i class="bi bi-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <h1 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">{{ __('User Details') }}</h1>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[9px] font-black uppercase tracking-widest text-[#80848e]">{{ __('Registry ID:') }}</span>
                                <span class="text-[9px] font-black uppercase tracking-widest text-discord-green">#{{ $user->id }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-xl {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} text-[9px] font-black uppercase tracking-widest border">
                            {{ strtoupper(__($user->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-10 space-y-12 divide-y divide-black/[0.03] dark:divide-white/[0.03]">
                
                <!-- Section 1: Token Pool Management -->
                <div class="pt-0 space-y-6" x-data="tokenAdjuster()">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-amber-400 rounded-full"></div>
                        <h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Token Balance Control') }}</h2>
                    </div>

                    <div class="p-6 rounded-xl border border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/10">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                            <!-- Balance Display -->
                            <div class="flex items-center gap-5 shrink-0">
                                <div class="h-14 w-14 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/[0.03] flex items-center justify-center text-amber-500 shadow-sm">
                                    <i class="bi bi-coin text-3xl"></i>
                                </div>
                                <div>
                                    <div class="text-[9px] font-black text-[#80848e] uppercase tracking-widest">{{ __('Available Balance') }}</div>
                                    <div class="text-3xl font-black text-[#1e1f22] dark:text-white leading-none mt-1">{{ number_format($user->tokens) }} <span class="text-[10px] font-bold text-[#80848e] ml-1 uppercase tracking-widest">{{ __('Tokens') }}</span></div>
                                </div>
                            </div>

                            <!-- Adjustment Form -->
                            <form @submit.prevent="submitForm" class="flex flex-col md:flex-row items-end gap-3 flex-1 lg:max-w-4xl">
                                <div class="flex-1 w-full">
                                    <label class="block text-[8px] font-black uppercase text-[#80848e] mb-2 ml-1 tracking-widest">{{ __('Operation') }}</label>
                                    <select x-model="form.operation" class="w-full h-11 rounded-xl border border-black/[0.05] bg-white dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white outline-none focus:ring-2 focus:ring-discord-green/10 transition-all cursor-pointer shadow-sm">
                                        <option value="add">{{ __('Deposit Tokens (+)') }}</option>
                                        <option value="deduct">{{ __('Withdraw Tokens (-)') }}</option>
                                        <option value="set">{{ __('Overwrite Balance (=)') }}</option>
                                    </select>
                                </div>
                                <div class="w-full md:w-36">
                                    <label class="block text-[8px] font-black uppercase text-[#80848e] mb-2 ml-1 tracking-widest">{{ __('Amount') }}</label>
                                    <input x-model="form.tokens" type="number" required class="w-full h-11 rounded-xl border border-black/[0.05] bg-white dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white outline-none focus:ring-2 focus:ring-discord-green/10 transition-all text-right shadow-sm">
                                </div>
                                <div class="flex-[1.5] w-full">
                                    <label class="block text-[8px] font-black uppercase text-[#80848e] mb-2 ml-1 tracking-widest">{{ __('Audit Note') }}</label>
                                    <input x-model="form.note" type="text" placeholder="{{ __('Internal remark...') }}" class="w-full h-11 rounded-xl border border-black/[0.05] bg-white dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white outline-none focus:ring-2 focus:ring-discord-green/10 transition-all shadow-sm">
                                </div>
                                <button type="submit" :disabled="loading" class="h-11 px-8 bg-discord-green text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#1f8b4c] transition active:scale-95 disabled:opacity-50 shadow-lg shadow-green-500/10">
                                    <span x-show="!loading">{{ __('Apply Update') }}</span>
                                    <span x-show="loading"><i class="bi bi-arrow-repeat animate-spin"></i></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Account Permissions -->
                <div class="pt-10 space-y-8">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                        <h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Permission Layer') }}</h2>
                    </div>
                    
                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/role') }}" class="w-full">
                        @csrf @method('PATCH')
                        
                        <div class="bg-[#f8fafb] dark:bg-black/10 p-6 rounded-xl border border-black/[0.03] dark:border-white/[0.03]">
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">
                                <div>
                                    <label class="block text-[8px] font-black uppercase text-[#80848e] mb-3 ml-1 tracking-widest">{{ __('Identity Profile') }}</label>
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/[0.03] flex items-center justify-center text-rose-500 font-black text-lg shadow-sm">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-[13px] font-black text-[#1e1f22] dark:text-white leading-none">{{ $user->name }}</div>
                                            <div class="text-[10px] font-bold text-[#80848e] mt-1.5 uppercase tracking-widest">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 xl:col-span-2">
                                    <div class="space-y-2">
                                        <label class="block text-[8px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Access Role') }}</label>
                                        <select name="role" class="w-full h-11 rounded-xl border border-black/[0.05] bg-white dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white focus:ring-2 focus:ring-discord-green/10 outline-none transition-all cursor-pointer shadow-sm">
                                            <option value="{{ \App\Models\User::ROLE_USER }}" @selected((int) $user->role === \App\Models\User::ROLE_USER)>{{ __('Standard User') }}</option>
                                            <option value="{{ \App\Models\User::ROLE_TENANT_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_TENANT_ADMIN)>{{ __('Tenant Admin') }}</option>
                                            <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_SUPER_ADMIN)>{{ __('Super Admin') }}</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[8px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Folder Allowance') }}</label>
                                        <div class="flex items-center gap-3">
                                            <input type="number" name="max_folders" value="{{ old('max_folders', $user->max_folders ?? 3) }}" min="1" max="100" class="flex-1 h-11 rounded-xl border border-black/[0.05] bg-white dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white focus:ring-2 focus:ring-discord-green/10 outline-none transition-all shadow-sm">
                                            <button type="submit" class="h-11 px-6 bg-[#1e1f22] dark:bg-white text-white dark:text-[#1e1f22] text-[10px] font-black uppercase tracking-widest rounded-xl hover:opacity-90 transition shadow-lg active:scale-95 whitespace-nowrap">{{ __('Commit Changes') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Section 3: Managed Folders (1:N Owner Model) -->
                <div class="pt-10 space-y-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                            <h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Managed Folder Entities') }}</h2>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-[10px] font-black text-[#80848e] uppercase tracking-widest">
                                {{ $workspaceSnapshots->count() }} / {{ $user->max_folders }} {{ __('Folders') }}
                            </div>
                            @if($workspaceSnapshots->count() < $user->max_folders)
                                <a href="{{ \App\Support\OwnerUrl::path(request(), 'folders/create?user_id=' . $user->id) }}" class="px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                    <i class="bi bi-plus-lg mr-1.5"></i>{{ __('Create New Folder') }}
                                </a>
                            @else
                                <button disabled class="px-4 py-2 bg-slate-50 text-slate-400 border border-slate-100 rounded-xl text-[9px] font-black uppercase tracking-widest cursor-not-allowed opacity-60">
                                    <i class="bi bi-lock-fill mr-1.5"></i>{{ __('Limit Reached') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @forelse($workspaceSnapshots as $workspace)
                            <div class="group flex items-center justify-between p-5 rounded-xl border border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/5 hover:bg-white dark:hover:bg-[#1e1f22] hover:border-discord-green/20 hover:shadow-sm transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-white dark:bg-[#2b2d31] flex items-center justify-center text-discord-green font-black text-lg border border-black/[0.03] shadow-inner">
                                        {{ substr($workspace->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-[13px] font-black text-[#1e1f22] dark:text-white truncate max-w-[150px]">{{ $workspace->name }}</div>
                                        <div class="text-[9px] font-black text-[#80848e] uppercase tracking-widest mt-1.5">{{ number_format($workspace->slips_count) }} {{ __('Slips') }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ \App\Support\OwnerUrl::path(request(), 'folders/' . $workspace->id) }}" class="h-8 w-8 rounded-xl bg-white dark:bg-[#2b2d31] border border-black/[0.05] flex items-center justify-center text-[#80848e] hover:text-discord-green transition-all shadow-sm">
                                        <i class="bi bi-gear-fill text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-16 text-center text-[10px] font-black text-[#80848e] italic uppercase tracking-widest bg-[#f8fafb] dark:bg-black/5 rounded-xl border border-dashed border-black/[0.05]">
                                {{ __('This account has not created any folders yet') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Section 4: Account Lifecycle -->
                <div class="pt-10 space-y-8">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-rose-500 rounded-full"></div>
                        <h2 class="text-[10px] font-black uppercase tracking-widest text-rose-600">{{ __('Danger Zone') }}</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Suspend Action --}}
                        <div class="p-6 rounded-xl border border-amber-100 bg-amber-50/30 dark:bg-amber-500/5 dark:border-amber-500/10 flex flex-col justify-between gap-4">
                            <div>
                                <h3 class="text-[10px] font-black uppercase text-amber-700 dark:text-amber-500 tracking-widest">{{ __('Suspend Access') }}</h3>
                                <p class="text-[9px] font-bold text-amber-600/80 dark:text-amber-600 uppercase tracking-widest mt-2 leading-relaxed">
                                    {{ $user->status === 'active' ? __('Revoke system access immediately for this entity.') : __('Restore and reactivate system access for this entity.') }}
                                </p>
                            </div>
                            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/status') }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}">
                                <button type="submit" class="w-full h-11 rounded-xl border border-amber-200 bg-white text-amber-600 text-[10px] font-black uppercase tracking-widest hover:bg-amber-600 hover:text-white transition-all shadow-sm active:scale-95">
                                    {{ $user->status === 'active' ? __('Suspend Account') : __('Reactivate Entity') }}
                                </button>
                            </form>
                        </div>

                        {{-- Terminate Action --}}
                        <div class="p-6 rounded-xl border border-rose-100 bg-rose-50/30 dark:bg-rose-500/5 dark:border-rose-500/10 flex flex-col justify-between gap-4">
                            <div>
                                <h3 class="text-[10px] font-black uppercase text-rose-700 dark:text-rose-500 tracking-widest">{{ __('Instant Terminate') }}</h3>
                                <p class="text-[9px] font-bold text-rose-600/80 dark:text-rose-600 uppercase tracking-widest mt-2 leading-relaxed">
                                    {{ __('Purge this entity and all associated data. This action is irreversible and immediate.') }}
                                </p>
                            </div>
                            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id) }}" onsubmit="return confirm('{{ __('WARNING: THIS ACTION IS IRREVERSIBLE. Are you sure you want to PERMANENTLY delete this account?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full h-11 rounded-xl bg-rose-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-lg shadow-rose-500/10 active:scale-95">
                                    {{ __('Terminate Entity') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Registration Info (Mini Footer) --}}
                    <div class="flex items-center gap-10 px-2 pt-2">
                        <div>
                            <span class="text-[8px] font-black uppercase tracking-widest text-[#80848e] block mb-1">{{ __('Registration Origin') }}</span>
                            <span class="text-[10px] font-black text-[#1e1f22] dark:text-slate-300 uppercase tracking-widest">{{ $user->created_at?->format('d M Y') }}</span>
                        </div>
                        <div class="w-px h-6 bg-black/[0.05] dark:bg-white/10"></div>
                        <div>
                            <span class="text-[8px] font-black uppercase tracking-widest text-[#80848e] block mb-1">{{ __('Last Interaction') }}</span>
                            <span class="text-[10px] font-black text-[#1e1f22] dark:text-slate-300 uppercase tracking-widest">{{ $user->last_login_at?->diffForHumans() ?? __('Inert Account') }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function tokenAdjuster() {
            return {
                loading: false,
                form: {
                    operation: 'add',
                    tokens: 0,
                    note: ''
                },
                async submitForm() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/tokens') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (response.ok) {
                            Swal.fire({
                                toast: true, position: 'top-end', icon: 'success',
                                title: data.message || 'Updated', showConfirmButton: false, timer: 1500
                            });
                            setTimeout(() => { window.location.reload(); }, 1500);
                        } else {
                            throw new Error(data.message || 'Error');
                        }
                    } catch (error) {
                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'error',
                            title: error.message, showConfirmButton: false, timer: 3000
                        });
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
    @endpush
@endsection
