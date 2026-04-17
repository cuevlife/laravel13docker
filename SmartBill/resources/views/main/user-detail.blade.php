@extends('layouts.app')

@section('content')
    <div class="w-full py-8 px-4 sm:px-6 lg:px-8 animate-in fade-in duration-500">
        
        <!-- Master Container Card -->
        <div class="bg-white dark:bg-[#2b2d31] rounded-xl shadow-sm border border-black/[0.05] dark:border-white/5 overflow-hidden">
            
            {{-- Header Section --}}
            <div class="px-8 py-10 border-b border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="w-10 h-10 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/10 dark:border-white/10 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all shadow-sm">
                            <i class="bi bi-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tightest">Edit User Profile</h1>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Management ID:</span>
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-500">#{{ $user->id }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-lg {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }} text-[9px] font-black uppercase tracking-widest border">
                            {{ $user->status }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-12 space-y-16 divide-y divide-black/[0.03] dark:divide-white/[0.03]">
                
                <!-- Section 1: Token Management -->
                <div class="pt-0 space-y-6" x-data="tokenAdjuster()">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-5 bg-amber-400 rounded-full"></div>
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Token Balance</h2>
                    </div>

                    <div class="p-8 rounded-xl border border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/10">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                            <!-- Simple Balance Display -->
                            <div class="flex items-center gap-5 shrink-0">
                                <div class="h-14 w-14 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/5 flex items-center justify-center text-amber-500 shadow-sm">
                                    <i class="bi bi-coin text-3xl"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Available Balance</div>
                                    <div class="text-3xl font-black text-slate-900 dark:text-white leading-none mt-1">{{ number_format($user->tokens) }} <span class="text-xs font-bold text-slate-400 ml-1 uppercase tracking-widest">Tokens</span></div>
                                </div>
                            </div>

                            <!-- Standard Adjustment Form -->
                            <form @submit.prevent="submitForm" class="flex flex-col md:flex-row items-end gap-4 flex-1 lg:max-w-4xl">
                                <div class="flex-1 w-full">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Adjustment Type</label>
                                    <select x-model="form.operation" class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-3 text-xs font-bold dark:text-white outline-none focus:ring-1 focus:ring-indigo-500/30 transition-all cursor-pointer shadow-sm">
                                        <option value="add">Add Tokens (+)</option>
                                        <option value="deduct">Deduct Tokens (-)</option>
                                        <option value="set">Set Exact Balance (=)</option>
                                    </select>
                                </div>
                                <div class="w-full md:w-36">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Amount</label>
                                    <input x-model="form.tokens" type="number" required class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-3 text-xs font-bold dark:text-white outline-none focus:ring-1 focus:ring-indigo-500/30 transition-all text-right shadow-sm">
                                </div>
                                <div class="flex-[1.5] w-full">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Transaction Note</label>
                                    <input x-model="form.note" type="text" placeholder="Explain this change..." class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-3 text-xs font-bold dark:text-white outline-none focus:ring-1 focus:ring-indigo-500/30 transition-all shadow-sm">
                                </div>
                                <button type="submit" :disabled="loading" class="h-[46px] px-10 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:opacity-90 transition active:scale-95 disabled:opacity-50 shadow-md">
                                    <span x-show="!loading">Update</span>
                                    <span x-show="loading"><i class="bi bi-arrow-repeat animate-spin"></i></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Account Permissions -->
                <div class="pt-12 space-y-8">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-5 bg-indigo-500 rounded-full"></div>
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Account Permissions</h2>
                    </div>
                    
                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/role') }}" class="w-full">
                        @csrf @method('PATCH')
                        
                        <div class="bg-[#f8fafb] dark:bg-black/10 p-8 rounded-xl border border-black/[0.03] dark:border-white/[0.03]">
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-10 items-start">
                                <div>
                                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-3 ml-1 tracking-widest">User Identity</label>
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/5 flex items-center justify-center text-slate-400 font-black text-sm">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-base font-black text-slate-900 dark:text-white leading-none">{{ $user->name }}</div>
                                            <div class="text-[11px] font-bold text-slate-400 mt-1 uppercase tracking-tight">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 xl:col-span-2">
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black uppercase text-slate-400 ml-1 tracking-widest">System Access Role</label>
                                        <select name="role" class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-5 py-3.5 text-xs font-bold dark:text-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all cursor-pointer shadow-sm">
                                            <option value="{{ \App\Models\User::ROLE_USER }}" @selected((int) $user->role === \App\Models\User::ROLE_USER)>Standard User</option>
                                            <option value="{{ \App\Models\User::ROLE_TENANT_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_TENANT_ADMIN)>Tenant Admin</option>
                                            <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_SUPER_ADMIN)>Super Admin</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black uppercase text-slate-400 ml-1 tracking-widest">Folder Creation Limit</label>
                                        <div class="flex items-center gap-4">
                                            <input type="number" name="max_folders" value="{{ old('max_folders', $user->max_folders ?? 3) }}" min="1" max="100" class="flex-1 rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-5 py-3.5 text-xs font-bold dark:text-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all shadow-sm">
                                            <button type="submit" class="h-[50px] px-8 bg-[#1e1f22] dark:bg-white text-white dark:text-[#1e1f22] text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:opacity-90 transition shadow-md active:scale-95 whitespace-nowrap">Save Permissions</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Section 3: Folder Access -->
                <div class="pt-12 space-y-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-5 bg-emerald-500 rounded-full"></div>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Workspace Folders</h2>
                        </div>
                        <button x-data x-on:click="$dispatch('open-link-modal')" class="px-5 py-2.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                            <i class="bi bi-plus-circle-fill mr-2"></i>Link New Folder
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse($workspaceSnapshots as $workspace)
                            @php $isPrimaryOwner = (int) $workspace->user_id === (int) $user->id; @endphp
                            <div class="group flex items-center justify-between p-6 rounded-xl border border-black/[0.04] dark:border-white/[0.04] bg-[#f8fafb] dark:bg-black/5 hover:bg-white dark:hover:bg-[#1e1f22] hover:border-indigo-500/20 hover:shadow-md transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-xl bg-white dark:bg-[#2b2d31] flex items-center justify-center text-indigo-600 font-black text-lg border border-black/5 shadow-inner">
                                        {{ substr($workspace->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-slate-900 dark:text-white truncate max-w-[150px]">{{ $workspace->name }}</div>
                                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1.5">{{ $isPrimaryOwner ? 'Primary Owner' : 'Staff Member' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ \App\Support\OwnerUrl::path(request(), 'folders/' . $workspace->id) }}" class="h-9 w-9 rounded-xl bg-white dark:bg-[#2b2d31] border border-black/10 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all shadow-sm">
                                        <i class="bi bi-gear-fill"></i>
                                    </a>
                                    @if(!$isPrimaryOwner)
                                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/workspaces/' . $workspace->id) }}" onsubmit="return confirm('Remove access?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="h-9 w-9 rounded-xl bg-white dark:bg-[#2b2d31] border border-black/10 flex items-center justify-center text-slate-300 hover:text-rose-500 transition-all shadow-sm">
                                                <i class="bi bi-x-lg text-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-20 text-center text-[11px] font-black text-slate-400 italic uppercase tracking-[0.3em] bg-[#f8fafb] dark:bg-black/5 rounded-xl border border-dashed border-black/5">
                                No folders linked to this account
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Section 4: Account Actions & Status -->
                <div class="pt-12 space-y-8">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-5 bg-rose-500 rounded-full"></div>
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-rose-600">Danger Zone</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Suspend Action --}}
                        <div class="p-6 rounded-xl border border-amber-100 bg-amber-50/30 dark:bg-amber-500/5 dark:border-amber-500/10 flex flex-col justify-between gap-4">
                            <div>
                                <h3 class="text-xs font-black uppercase text-amber-700 dark:text-amber-500 tracking-widest">Account Status</h3>
                                <p class="text-[10px] font-bold text-amber-600/80 dark:text-amber-600 uppercase tracking-tight mt-1 leading-relaxed">
                                    {{ $user->status === 'active' ? 'Suspend this account to temporarily block system access.' : 'Re-enable access for this account.' }}
                                </p>
                            </div>
                            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/status') }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}">
                                <button type="submit" class="w-full py-3 rounded-xl border border-amber-200 bg-white text-amber-600 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-amber-600 hover:text-white transition-all shadow-sm active:scale-95">
                                    {{ $user->status === 'active' ? 'Suspend Account' : 'Reactivate User' }}
                                </button>
                            </form>
                        </div>

                        {{-- Terminate Action --}}
                        <div class="p-6 rounded-xl border border-rose-100 bg-rose-50/30 dark:bg-rose-500/5 dark:border-rose-500/10 flex flex-col justify-between gap-4">
                            <div>
                                <h3 class="text-xs font-black uppercase text-rose-700 dark:text-rose-500 tracking-widest">Terminate Account</h3>
                                <p class="text-[10px] font-bold text-rose-600/80 dark:text-rose-600 uppercase tracking-tight mt-1 leading-relaxed">
                                    Permanently remove this user and all associated data. This action cannot be undone.
                                </p>
                            </div>
                            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id) }}" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this account?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full py-3 rounded-xl bg-rose-600 text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-rose-700 transition-all shadow-md active:scale-95">
                                    Terminate Permanently
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Registration Info (Mini Footer) --}}
                    <div class="flex items-center gap-10 px-2">
                        <div>
                            <span class="text-[8px] font-black uppercase tracking-[0.25em] text-slate-400 block mb-1">Registration Date</span>
                            <span class="text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-tight">{{ $user->created_at?->format('d M Y') }}</span>
                        </div>
                        <div class="w-px h-6 bg-black/5 dark:bg-white/10"></div>
                        <div>
                            <span class="text-[8px] font-black uppercase tracking-[0.25em] text-slate-400 block mb-1">Recent Activity</span>
                            <span class="text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-tight">{{ $user->last_login_at?->diffForHumans() ?? 'No activity recorded' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Link Modal -->
    <div x-data="{ open: false }" x-on:open-link-modal.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#1e1f22]/80 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white dark:bg-[#2b2d31] w-full max-w-md rounded-xl shadow-2xl p-10 border border-black/5" @click.away="open = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <h2 class="text-2xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight mb-2">Link Folder</h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-10 leading-relaxed">Assign this account to an existing folder with staff permissions.</p>
            
            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/workspaces') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="workspace_role" value="employee">
                <div class="space-y-2">
                    <label class="block text-[9px] font-black uppercase text-slate-400 ml-1 tracking-widest">Select Target Folder</label>
                    <div class="relative">
                        <select name="merchant_id" required class="w-full rounded-xl border border-black/10 bg-[#f8fafb] dark:bg-[#1e1f22] px-5 py-4 text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all appearance-none shadow-inner">
                            @foreach($availableMerchants as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>
                <div class="flex flex-col gap-3 pt-4">
                    <button type="submit" class="w-full py-4 rounded-xl bg-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition active:scale-95">Complete Connection</button>
                    <button type="button" @click="open = false" class="w-full py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition">Cancel</button>
                </div>
            </form>
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
