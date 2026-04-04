<x-owner-layout>
    <div class="space-y-8 animate-in fade-in duration-700 pb-20">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Super Admin User Detail</div>
                <h1 class="mt-3 text-3xl font-black uppercase tracking-tight text-slate-900 dark:text-white">{{ $user->name }}</h1>
                <p class="mt-3 text-sm font-bold text-slate-500 dark:text-slate-400">{{ $user->email }} @if($user->username) <span class="uppercase tracking-[0.18em] text-slate-400">/ {{ $user->username }}</span> @endif</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="inline-flex items-center justify-center rounded-[16px] border border-slate-200 px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-slate-700 transition hover:border-slate-300 hover:text-slate-900 dark:border-white/10 dark:text-white">
                    Back to Users
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Role</div>
                <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ $user->roleLabel() }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] {{ $user->status === 'active' ? 'text-emerald-500' : 'text-amber-500' }}">{{ $user->statusLabel() }}</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Tokens</div>
                <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($user->tokens) }}</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Projects</div>
                <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($workspaceSnapshots->count()) }}</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Slips</div>
                <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($user->slips_count) }}</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Token Events</div>
                <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($user->token_logs_count) }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="space-y-6">
                <div class="premium-card p-6 md:p-8">
                    <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Management Controls</h2>
                    <div class="mt-6 space-y-4">
                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/role') }}" class="grid gap-3 md:grid-cols-[1fr_auto]">
                            @csrf
                            @method('PATCH')
                            <select name="role" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                                <option value="{{ \App\Models\User::ROLE_USER }}" @selected((int) $user->role === \App\Models\User::ROLE_USER)>User</option>
                                <option value="{{ \App\Models\User::ROLE_TENANT_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_TENANT_ADMIN)>Tenant Admin</option>
                                <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_SUPER_ADMIN)>Super Admin</option>
                            </select>
                            <button type="submit" class="rounded-[16px] bg-indigo-600 px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-indigo-500">
                                Update Role
                            </button>
                        </form>

                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/tokens') }}" class="grid gap-3 md:grid-cols-[0.8fr_0.8fr_1fr_auto]">
                            @csrf
                            <select name="operation" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black uppercase tracking-[0.14em] text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                                <option value="add">Add Tokens</option>
                                <option value="deduct">Deduct Tokens</option>
                                <option value="set">Set Exact Balance</option>
                            </select>
                            <input type="number" name="tokens" min="0" step="1" value="100" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Token amount">
                            <input type="text" name="note" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Optional admin note">
                            <button type="submit" class="rounded-[16px] bg-discord-green px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-[#1f8b4c]">
                                Apply Tokens
                            </button>
                        </form>

                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/status') }}" class="grid gap-3 md:grid-cols-[1fr_auto]">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}">
                            <div class="rounded-[16px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 dark:border-white/10 dark:bg-white/[0.03] dark:text-white">
                                Current account state: {{ $user->statusLabel() }}
                            </div>
                            <button type="submit" class="rounded-[16px] {{ $user->status === 'active' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-500' }} px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white transition">
                                {{ $user->status === 'active' ? 'Suspend User' : 'Reactivate User' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id) }}" class="grid gap-3 md:grid-cols-[1fr_auto]">
                            @csrf
                            @method('DELETE')
                            <div class="rounded-[16px] border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-black text-rose-600 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                                Delete only after reassigning any owned projects.
                            </div>
                            <button type="submit" class="rounded-[16px] bg-rose-500 px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-rose-600">
                                Delete User
                            </button>
                        </form>
                    </div>
                </div>

                <div class="premium-card p-6 md:p-8">
                    <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Token Control Snapshot</h2>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="rounded-[24px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Current Balance</div>
                            <div class="mt-3 text-xl font-black text-slate-900 dark:text-white">{{ number_format($user->tokens) }}</div>
                            <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">No recurring refill is active</div>
                        </div>
                        <div class="rounded-[24px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Approved Topups</div>
                            <div class="mt-3 text-xl font-black text-emerald-600">{{ number_format($usageSummary['approvedTopups']) }}</div>
                            <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Credits from approved slip requests</div>
                        </div>
                        <div class="rounded-[24px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Manual Credits</div>
                            <div class="mt-3 text-xl font-black text-emerald-600">{{ number_format($usageSummary['manualCredits']) }}</div>
                        </div>
                        <div class="rounded-[24px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Manual Debits</div>
                            <div class="mt-3 text-xl font-black text-rose-500">{{ number_format($usageSummary['manualDebits']) }}</div>
                        </div>
                        <div class="rounded-[24px] border border-slate-100 bg-slate-50/70 px-5 py-4 md:col-span-2 dark:border-white/5 dark:bg-white/[0.03]">
                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Usage Spent</div>
                            <div class="mt-3 text-xl font-black text-slate-900 dark:text-white">{{ number_format($usageSummary['totalTokenUsage']) }}</div>
                            <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Consumed by slip processing and workspace activity</div>
                        </div>
                    </div>
                </div>

                <div class="premium-card p-6 md:p-8">
                    <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Workspace Access</h2>
                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/workspaces') }}" class="mt-6 grid gap-3 lg:grid-cols-[1.4fr_1fr_auto]">
                        @csrf
                        <select name="merchant_id" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                            @foreach($availableMerchants as $merchantOption)
                                <option value="{{ $merchantOption->id }}">{{ $merchantOption->name }} / Project {{ str_pad((string) $merchantOption->id, 2, '0', STR_PAD_LEFT) }}</option>
                            @endforeach
                        </select>
                        <select name="workspace_role" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                            <option value="employee">Employee</option>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                        </select>
                        <button type="submit" class="rounded-[16px] bg-indigo-600 px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-indigo-500">
                            Link Project
                        </button>
                    </form>
                    <div class="mt-6 space-y-3">
                        @forelse($workspaceSnapshots as $workspace)
                            @php
                                $workspaceUrl = \App\Support\WorkspaceUrl::workspace(request(), $workspace, 'dashboard');
                                $isPrimaryOwner = (int) $workspace->user_id === (int) $user->id;
                            @endphp
                            <div class="rounded-[22px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                                <div class="flex items-center justify-between gap-4">
                                    <a href="{{ $workspaceUrl }}" target="_blank" class="min-w-0 flex-1 transition hover:text-indigo-600">
                                        <div class="text-sm font-black text-slate-900 dark:text-white">{{ $workspace->name }}</div>
                                        <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Project {{ str_pad((string) $workspace->id, 2, '0', STR_PAD_LEFT) }}</div>
                                    </a>
                                    <div class="text-right">
                                        <div class="text-[11px] font-black text-slate-900 dark:text-white">{{ number_format($workspace->templates_count) }} Profiles</div>
                                        <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ number_format($workspace->slips_count) }} Slips</div>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-between gap-4 border-t border-slate-200/70 pt-4 dark:border-white/5">
                                    <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                        Access {{ strtoupper($workspace->access_role ?: ($isPrimaryOwner ? 'owner' : 'legacy')) }}
                                    </div>
                                    @if(!$isPrimaryOwner)
                                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id . '/workspaces/' . $workspace->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-[12px] border border-rose-200 px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-rose-500 transition hover:bg-rose-50 dark:border-rose-500/20 dark:hover:bg-rose-500/10">
                                                Remove Access
                                            </button>
                                        </form>
                                    @else
                                        <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Primary Owner</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[22px] border-2 border-dashed border-slate-200 px-5 py-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No workspace linked
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="premium-card p-6 md:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Token Activity</h2>
                            <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Recent ledger entries</p>
                        </div>
                        <div class="rounded-full bg-slate-100 px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:bg-white/5 dark:text-slate-400">
                            Admin Credits {{ number_format($usageSummary['manualCredits']) }}
                        </div>
                    </div>
                    <div class="mt-6 space-y-3">
                        @forelse($tokenLogs as $log)
                            <div class="flex items-center justify-between gap-4 rounded-[20px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-black text-slate-900 dark:text-white">{{ $log->description ?: 'Token update' }}</div>
                                    <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ $log->type }} / {{ optional($log->created_at)->format('d M Y H:i') ?? 'Unknown time' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-black {{ $log->delta < 0 ? 'text-rose-500' : 'text-emerald-500' }}">{{ $log->delta > 0 ? '+' : '' }}{{ $log->delta }}</div>
                                    <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Balance {{ number_format($log->balance_after) }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[22px] border-2 border-dashed border-slate-200 px-5 py-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No token history yet
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="premium-card p-6 md:p-8">
                    <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Top-Up Requests</h2>
                    <div class="mt-6 space-y-3">
                        @forelse($topupRequests as $request)
                            <div class="rounded-[20px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="text-sm font-black text-slate-900 dark:text-white">{{ number_format($request->requested_tokens) }} Tokens</div>
                                    <div class="text-[10px] font-black uppercase tracking-[0.2em]
                                        @class([
                                            'text-amber-500' => $request->status === 'pending',
                                            'text-emerald-500' => $request->status === 'approved',
                                            'text-rose-500' => $request->status === 'rejected',
                                        ])">
                                        {{ strtoupper($request->status) }}
                                    </div>
                                </div>
                                <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                                    {{ optional($request->created_at)->format('d M Y H:i') ?? 'Unknown time' }}
                                    @if($request->reviewer)
                                        / Reviewed by {{ $request->reviewer->name }}
                                    @endif
                                </div>
                                @if($request->admin_note)
                                    <div class="mt-3 text-xs font-bold text-slate-500 dark:text-slate-400">{{ $request->admin_note }}</div>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-[22px] border-2 border-dashed border-slate-200 px-5 py-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No top-up history yet
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-owner-layout>
