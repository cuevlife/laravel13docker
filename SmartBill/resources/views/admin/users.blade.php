<x-owner-layout>
    <x-slot name="header">
        <h2 class="text-xl font-black tracking-tightest uppercase dark:text-white">SaaS Control Center</h2>
    </x-slot>

    <div class="space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Users</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['users']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Registered accounts</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Admins</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['admins']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Tenant operators</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Token Pool</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['tokens']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Current balance across users</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Active Users</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['activeUsers']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Accounts available for login</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Pending Topups</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['pendingTopups']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Waiting for owner review</div>
            </div>
            <div class="premium-card p-5 border-l-4 border-l-amber-500">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Suspended</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['suspended']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Accounts blocked from login</div>
            </div>
        </div>

        <div class="premium-card p-6 md:p-8">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Create User</div>
                    <h2 class="mt-3 text-2xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Provision New Account</h2>
                    <p class="mt-2 text-sm font-bold text-slate-500 dark:text-slate-400">Create a user, assign the first role, optional starting project, and opening token balance in one step.</p>
                </div>
            </div>

            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-4">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" required class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Full name">
                <input type="text" name="username" value="{{ old('username') }}" required class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="username">
                <input type="email" name="email" value="{{ old('email') }}" required class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="email@example.com">
                <select name="role" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                    <option value="{{ \App\Models\User::ROLE_USER }}">User</option>
                    <option value="{{ \App\Models\User::ROLE_TENANT_ADMIN }}">Tenant Admin</option>
                    <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}">Super Admin</option>
                </select>

                <input type="password" name="password" required class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Password">
                <input type="password" name="password_confirmation" required class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Confirm password">
                <input type="number" name="tokens" min="0" step="1" value="{{ old('tokens', 0) }}" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Opening tokens">

                <select name="merchant_id" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white xl:col-span-2">
                    <option value="">No initial project</option>
                    @foreach($merchants as $merchant)
                        <option value="{{ $merchant->id }}">{{ $merchant->name }} / Project {{ str_pad((string) $merchant->id, 2, '0', STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
                <select name="workspace_role" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                    <option value="employee">Project Employee</option>
                    <option value="admin">Project Admin</option>
                    <option value="owner">Project Owner</option>
                </select>
                <button type="submit" class="rounded-[16px] bg-discord-green px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-[#1f8b4c]">
                    Create User
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-discord-main rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 dark:border-white/5 bg-slate-50/30 dark:bg-black/5 flex items-center justify-between">
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em]">Tenant Registry</span>
                <span class="text-[9px] font-bold text-discord-red uppercase">{{ count($users) }} Nodes Active</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-white/5 text-slate-400 dark:text-slate-600">
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Identity</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Access Layer</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Token Mode</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Workspaces</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Tokens</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Activity</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Controls</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Temporal Node</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-discord-black flex items-center justify-center text-rose-500 font-black shadow-inner">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-slate-900 dark:text-white uppercase leading-none">{{ $user->name }}</div>
                                            <div class="text-[9px] text-slate-400 font-bold mt-2 uppercase tracking-tighter">{{ $user->email }}</div>
                                            <div class="text-[9px] text-slate-300 dark:text-slate-600 font-bold mt-2 uppercase tracking-[0.2em]">@ {{ $user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex flex-col gap-2">
                                        <span class="px-3 py-1 bg-slate-100 dark:bg-black/20 text-indigo-600 dark:text-indigo-400 rounded-lg text-[10px] font-black uppercase tracking-widest border border-slate-200 dark:border-white/5 shadow-sm">
                                            {{ $user->roleLabel() }}
                                        </span>
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border shadow-sm
                                            {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20' : 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-500/20' }}">
                                            {{ $user->statusLabel() }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-black text-slate-900 dark:text-white uppercase leading-none">Manual Control</div>
                                    <div class="text-[9px] text-slate-400 font-bold mt-2 uppercase tracking-[0.2em]">{{ number_format($user->token_logs_count) }} Ledger Events</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    @if($user->merchants->isNotEmpty())
                                        <div x-data="{ open: false }" class="relative">
                                            <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-black/20 text-slate-600 dark:text-slate-400 rounded-lg text-[10px] font-black uppercase tracking-widest border border-slate-200 dark:border-white/5 shadow-sm hover:bg-slate-100 transition-colors">
                                                {{ $user->merchants->count() }} Workspaces
                                                <i data-lucide="chevron-down" class="w-3 h-3" :class="{'rotate-180': open}"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-2 w-48 bg-white dark:bg-[#2b2d31] border border-slate-200 dark:border-white/5 rounded-xl shadow-xl z-50 py-2">
                                                @foreach($user->merchants as $m)
                                                    @php
                                                        $mUrl = \App\Support\WorkspaceUrl::workspace(request(), $m, 'dashboard');
                                                    @endphp
                                                    <a href="{{ $mUrl }}" target="_blank" class="block px-4 py-2 text-[10px] font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-indigo-600 transition-colors">
                                                        {{ $m->name }} (Project {{ str_pad((string) $m->id, 2, '0', STR_PAD_LEFT) }})
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-400 italic">No stores</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-black text-slate-900 dark:text-white">{{ number_format($user->tokens) }}</div>
                                    <div class="text-[9px] text-slate-400 font-bold mt-2 uppercase tracking-[0.2em]">Available balance</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-black text-slate-900 dark:text-white">{{ number_format($user->slips_count) }} Slips</div>
                                    <div class="text-[9px] text-slate-400 font-bold mt-2 uppercase tracking-[0.2em]">{{ number_format($user->token_topup_requests_count) }} Topup Requests</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col gap-3 min-w-[220px]">
                                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/role') }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" class="flex-1 rounded-[12px] border border-slate-200 bg-white px-3 py-2 text-[11px] font-black uppercase tracking-[0.12em] text-slate-700 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                                                <option value="{{ \App\Models\User::ROLE_USER }}" @selected((int) $user->role === \App\Models\User::ROLE_USER)>User</option>
                                                <option value="{{ \App\Models\User::ROLE_TENANT_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_TENANT_ADMIN)>Tenant Admin</option>
                                                <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_SUPER_ADMIN)>Super Admin</option>
                                            </select>
                                            <button type="submit" class="rounded-[12px] bg-indigo-600 px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-white transition hover:bg-indigo-500">
                                                Role
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/tokens') }}" class="flex items-center gap-2">
                                            @csrf
                                            <select name="operation" class="w-24 rounded-[12px] border border-slate-200 bg-white px-3 py-2 text-[10px] font-black uppercase tracking-[0.12em] text-slate-700 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                                                <option value="add">Add</option>
                                                <option value="deduct">Deduct</option>
                                                <option value="set">Set</option>
                                            </select>
                                            <input type="number" name="tokens" min="0" step="1" value="50" class="w-24 rounded-[12px] border border-slate-200 bg-white px-3 py-2 text-[11px] font-black text-slate-700 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                                            <button type="submit" class="rounded-[12px] bg-discord-green px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-white transition hover:bg-[#1f8b4c]">
                                                Apply Tokens
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/status') }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}">
                                            <button type="submit" class="flex-1 rounded-[12px] {{ $user->status === 'active' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-500' }} px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-white transition">
                                                {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
                                            </button>
                                        </form>

                                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id) }}" class="inline-flex items-center justify-center rounded-[12px] border border-slate-200 px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-slate-700 transition hover:border-indigo-300 hover:text-indigo-600 dark:border-white/10 dark:text-white dark:hover:text-indigo-300">
                                            Open Detail
                                        </a>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-600 uppercase tracking-widest">{{ optional($user->created_at)->format('d M Y') ?? 'Unknown date' }}</div>
                                    <div class="text-[9px] text-slate-300 dark:text-slate-700 font-medium uppercase mt-1">Registry Log Established</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-owner-layout>

