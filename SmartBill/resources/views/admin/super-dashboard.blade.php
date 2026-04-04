<x-owner-layout>
    <div class="space-y-8 animate-in fade-in duration-700 pb-20">
        <div class="premium-card p-6 md:p-8 border-l-4 border-l-indigo-500">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">SaaS Control Tower</div>
                    <h1 class="mt-3 text-3xl md:text-4xl font-black uppercase tracking-tight text-slate-900 dark:text-white">System Overview</h1>
                    <p class="mt-3 max-w-2xl text-sm font-bold text-slate-500 dark:text-slate-400">Monitor workspace growth, user access, manual token operations, and top-up approvals from one central owner dashboard.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="inline-flex items-center justify-center rounded-[16px] border border-slate-200 px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-slate-700 transition hover:border-indigo-300 hover:text-indigo-600 dark:border-white/10 dark:text-white">
                        Manage Projects
                    </a>
                    <a href="{{ \App\Support\OwnerUrl::path(request(), 'topups') }}" class="inline-flex items-center justify-center rounded-[16px] border border-slate-200 px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-slate-700 transition hover:border-indigo-300 hover:text-indigo-600 dark:border-white/10 dark:text-white">
                        Review Topups
                    </a>
                    <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="inline-flex items-center justify-center rounded-[16px] bg-discord-green px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-[#1f8b4c]">
                        Manage Users
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Users</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['users']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ number_format($stats['activeUsers']) }} active accounts</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Projects</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['projects']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ number_format($stats['slips']) }} total slips</div>
            </div>
            <div class="premium-card p-5 border-l-4 border-l-amber-500">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Token Pool</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['tokens']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ number_format($stats['monthlyCredits']) }} credited this month</div>
            </div>
            <div class="premium-card p-5 border-l-4 border-l-rose-500">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Pending Topups</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['pendingTopups']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ number_format($stats['monthlyUsage']) }} usage this month</div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="space-y-6">
                <div class="premium-card p-6 md:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Most Active Projects</h2>
                            <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Sorted by processed slips</p>
                        </div>
                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600">View All</a>
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse($activeProjects as $project)
                            <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $project->id) }}" class="flex items-center justify-between gap-4 rounded-[20px] border border-slate-100 bg-slate-50/70 px-5 py-4 transition hover:border-indigo-200 hover:bg-white dark:border-white/5 dark:bg-white/[0.03] dark:hover:bg-white/[0.06]">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-black text-slate-900 dark:text-white">{{ $project->name }}</div>
                                    <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Project {{ str_pad((string) $project->id, 2, '0', STR_PAD_LEFT) }} / {{ $project->owner?->name ?? 'No owner' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-black text-slate-900 dark:text-white">{{ number_format($project->slips_count) }}</div>
                                    <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ number_format($project->templates_count) }} profiles</div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-[22px] border-2 border-dashed border-slate-200 px-5 py-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No projects yet
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="premium-card p-6 md:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Top-Up Queue</h2>
                            <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Latest requests requiring action</p>
                        </div>
                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'topups') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600">Review Queue</a>
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse($topupRequests as $request)
                            <div class="rounded-[20px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-black text-slate-900 dark:text-white">{{ $request->user->name }} / {{ number_format($request->requested_tokens) }} tokens</div>
                                        <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ optional($request->created_at)->format('d M Y H:i') ?? 'Unknown time' }}</div>
                                    </div>
                                    <div class="text-[10px] font-black uppercase tracking-[0.2em]
                                        @class([
                                            'text-amber-500' => $request->status === 'pending',
                                            'text-emerald-500' => $request->status === 'approved',
                                            'text-rose-500' => $request->status === 'rejected',
                                        ])">
                                        {{ strtoupper($request->status) }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[22px] border-2 border-dashed border-slate-200 px-5 py-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No top-up requests yet
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="premium-card p-6 md:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Highest Balances</h2>
                            <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Users with the most tokens available</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse($highBalanceUsers as $user)
                            <a href="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id) }}" class="flex items-center justify-between gap-4 rounded-[20px] border border-slate-100 bg-slate-50/70 px-5 py-4 transition hover:border-indigo-200 hover:bg-white dark:border-white/5 dark:bg-white/[0.03] dark:hover:bg-white/[0.06]">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-black text-slate-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ $user->roleLabel() }} / {{ $user->statusLabel() }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-black text-slate-900 dark:text-white">{{ number_format($user->tokens) }}</div>
                                    <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">tokens</div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-[22px] border-2 border-dashed border-slate-200 px-5 py-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No users yet
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="premium-card p-6 md:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Recent Accounts</h2>
                            <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Newest users entering the SaaS</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse($recentUsers as $user)
                            <a href="{{ \App\Support\OwnerUrl::path(request(), 'users/' . $user->id) }}" class="flex items-center justify-between gap-4 rounded-[20px] border border-slate-100 bg-slate-50/70 px-5 py-4 transition hover:border-indigo-200 hover:bg-white dark:border-white/5 dark:bg-white/[0.03] dark:hover:bg-white/[0.06]">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-black text-slate-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ optional($user->created_at)->format('d M Y') ?? 'Unknown date' }}</div>
                                </div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ $user->roleLabel() }}</div>
                            </a>
                        @empty
                            <div class="rounded-[22px] border-2 border-dashed border-slate-200 px-5 py-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No accounts yet
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="premium-card p-6 md:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Token Activity</h2>
                            <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Latest ledger movements</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse($tokenLogs as $log)
                            <div class="rounded-[20px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-black text-slate-900 dark:text-white">{{ $log->description ?: 'Token update' }}</div>
                                        <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ $log->type }} / {{ optional($log->created_at)->format('d M Y H:i') ?? 'Unknown time' }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-black {{ $log->delta < 0 ? 'text-rose-500' : 'text-emerald-500' }}">{{ $log->delta > 0 ? '+' : '' }}{{ $log->delta }}</div>
                                        <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Balance {{ number_format($log->balance_after) }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[22px] border-2 border-dashed border-slate-200 px-5 py-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No token events yet
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-owner-layout>
