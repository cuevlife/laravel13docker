@extends('layouts.app')

@section('content')
    <div class="w-full py-6 px-4 sm:px-6 lg:px-10 animate-in fade-in duration-500">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-black/5 dark:border-white/5">
            <div class="flex items-center gap-4">
                <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="w-9 h-9 rounded-lg bg-white dark:bg-white/5 border border-black/10 dark:border-white/10 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all shadow-sm">
                    <i class="bi bi-arrow-left text-lg"></i>
                </a>
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">
                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="hover:text-indigo-600 transition">Users</a>
                        <i class="bi bi-chevron-right text-[8px]"></i>
                        <span class="text-slate-600 dark:text-slate-300">Edit User</span>
                    </nav>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tightest">User Profile</h1>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-md {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }} text-[10px] font-bold uppercase tracking-wide border">
                    {{ $user->status }}
                </span>
            </div>
        </div>

        <div class="space-y-6">
            
            <!-- Identity & Role -->
            <div class="bg-white dark:bg-discord-main p-6 rounded-xl border border-black/5 dark:border-white/5 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Identity & Access</h2>
                <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/role') }}" class="space-y-4">
                    @csrf @method('PATCH')
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5">Full Name</label>
                            <input type="text" value="{{ $user->name }}" disabled class="w-full rounded-lg border border-black/5 bg-slate-50 px-3 py-2 text-sm text-slate-500 dark:bg-black/20 dark:border-white/5 shadow-inner cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5">Email</label>
                            <input type="text" value="{{ $user->email }}" disabled class="w-full rounded-lg border border-black/5 bg-slate-50 px-3 py-2 text-sm text-slate-500 dark:bg-black/20 dark:border-white/5 shadow-inner cursor-not-allowed">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5">System Role</label>
                        <div class="flex gap-2">
                            <select name="role" class="flex-1 rounded-lg border border-black/10 bg-white px-3 py-2 text-sm dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-1 focus:ring-indigo-500">
                                <option value="{{ \App\Models\User::ROLE_USER }}" @selected((int) $user->role === \App\Models\User::ROLE_USER)>Standard User</option>
                                <option value="{{ \App\Models\User::ROLE_TENANT_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_TENANT_ADMIN)>Tenant Admin</option>
                                <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_SUPER_ADMIN)>Super Admin</option>
                            </select>
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 transition shadow-sm">Save</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tokens -->
            <div class="bg-white dark:bg-discord-main p-6 rounded-xl border border-black/5 dark:border-white/5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Token Balance</h2>
                    <span class="text-sm font-black text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 px-3 py-1 rounded-md">{{ number_format($user->tokens) }} Tokens</span>
                </div>
                
                <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/tokens') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Operation</label>
                        <select name="operation" class="w-full rounded-lg border border-black/10 bg-white px-3 py-2 text-sm dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-1 focus:ring-indigo-500">
                            <option value="add">Add (+)</option>
                            <option value="deduct">Deduct (-)</option>
                            <option value="set">Set Exact (=)</option>
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Amount</label>
                        <input type="number" name="tokens" value="0" required class="w-full rounded-lg border border-black/10 bg-white px-3 py-2 text-sm dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Note</label>
                        <input type="text" name="note" placeholder="Optional" class="w-full rounded-lg border border-black/10 bg-white px-3 py-2 text-sm dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="bg-slate-900 text-white px-4 py-2 h-[38px] rounded-lg text-xs font-bold hover:bg-black transition shadow-sm">Apply</button>
                </form>
            </div>

            <!-- Folder Access -->
            <div class="bg-white dark:bg-discord-main rounded-xl border border-black/5 dark:border-white/5 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-black/5 dark:border-white/5 flex items-center justify-between bg-slate-50/50 dark:bg-white/[0.02]">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Folder Access</h2>
                    <button x-data x-on:click="$dispatch('open-link-modal')" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition flex items-center gap-1">
                        <i class="bi bi-plus-lg text-xs"></i> Link Folder
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-white/[0.02] text-xs text-slate-500 border-b border-black/5 dark:border-white/5">
                            <tr>
                                <th class="px-6 py-2.5 font-bold">Folder Name</th>
                                <th class="px-6 py-2.5 font-bold text-center">Slips</th>
                                <th class="px-6 py-2.5 text-right font-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5 dark:divide-white/5">
                            @forelse($workspaceSnapshots as $workspace)
                                @php $isPrimaryOwner = (int) $workspace->user_id === (int) $user->id; @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-white/[0.02] transition">
                                    <td class="px-6 py-3">
                                        <span class="font-bold text-slate-900 dark:text-white">{{ $workspace->name }}</span>
                                        <span class="text-xs text-slate-400 ml-2">/{{ $workspace->subdomain }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-center text-xs text-slate-500">
                                        {{ number_format($workspace->slips_count) }}
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        @if(!$isPrimaryOwner)
                                            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/workspaces/' . $workspace->id) }}">
                                                @csrf @method('DELETE')
                                                <button class="text-xs font-bold text-rose-500 hover:text-rose-700 transition">Remove</button>
                                            </form>
                                        @else
                                            <span class="text-[10px] font-bold uppercase text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded">Owner</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">No folders linked to this user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-rose-50/50 dark:bg-rose-500/5 p-6 rounded-xl border border-rose-100 dark:border-rose-500/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-rose-600">Danger Zone</h3>
                    <p class="text-xs text-rose-500/70 mt-0.5">Suspend or permanently delete this account.</p>
                </div>
                <div class="flex gap-3">
                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/status') }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}">
                        <button type="submit" class="px-4 py-2 rounded-lg border border-rose-200 text-rose-600 text-xs font-bold hover:bg-white transition shadow-sm bg-rose-50/50">
                            {{ $user->status === 'active' ? 'Suspend' : 'Reactivate' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition shadow-sm">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Link Modal -->
    <div x-data="{ open: false }" x-on:open-link-modal.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-discord-main w-full max-w-sm rounded-xl shadow-xl p-6 border border-black/5" @click.away="open = false">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4">Link Folder</h2>
            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/workspaces') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="workspace_role" value="employee">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5">Select Folder</label>
                    <select name="merchant_id" required class="w-full rounded-lg border border-black/10 bg-white px-3 py-2 text-sm dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-1 focus:ring-indigo-500">
                        @foreach($availableMerchants as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="open = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-xs font-bold shadow-sm hover:bg-indigo-700">Link</button>
                </div>
            </form>
        </div>
    </div>
@endsection
