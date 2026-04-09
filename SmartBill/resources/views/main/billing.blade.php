@extends('layouts.app')

@section('content')
    <div class="space-y-8 animate-in fade-in duration-700 pb-20">
        <div class="premium-card p-6 md:p-8 border-l-4 border-l-rose-500">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-2xl md:text-3xl font-black text-[#1e1f22] dark:text-[#f2f3f5] uppercase tracking-tight leading-none">Token Wallet</h2>
                    <p class="text-[10px] font-bold text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-[0.2em] mt-3">Manual token control and recent usage</p>
                </div>
                <div class="inline-flex items-center gap-3 px-5 py-3 rounded-xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em]">
                    <i class="bi bi-coin w-4 h-4"></i> {{ number_format($user->tokens) }} Tokens Ready
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-bold text-slate-600 dark:border-white/10 dark:bg-white/[0.03] dark:text-slate-300">
            Tokens are managed directly by super admin. If your team uses manual transfer confirmation, upload the payment slip below and wait for approval.
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="premium-card p-6">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Token Authority</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">Manual</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Super admin controls every balance</div>
            </div>
            <div class="premium-card p-6">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Used This Month</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($usageThisMonth) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Successful scan deductions</div>
            </div>
            <div class="premium-card p-6">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Pending Requests</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($topupRequests->where('status', 'pending')->count()) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Awaiting review from super admin</div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-6">
            <div class="premium-card p-6 md:p-8">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Recent Token Activity</h3>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mt-2">Transparent usage history</p>
                    </div>
                </div>

                @if($tokenLogs->isEmpty())
                    <div class="rounded-xl border-2 border-dashed border-slate-200 dark:border-white/10 p-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">
                        No token activity yet
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($tokenLogs as $log)
                            <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-100 dark:border-white/5 bg-slate-50/70 dark:bg-white/[0.03] px-5 py-4">
                                <div>
                                    <div class="text-sm font-black text-slate-900 dark:text-white">{{ $log->description ?: 'Token update' }}</div>
                                    <div class="mt-1 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ optional($log->created_at)->format('d M Y H:i') ?? 'Unknown time' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-black {{ $log->delta < 0 ? 'text-rose-500' : 'text-emerald-500' }}">{{ $log->delta > 0 ? '+' : '' }}{{ $log->delta }}</div>
                                    <div class="mt-1 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Balance {{ number_format($log->balance_after) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="premium-card p-6 md:p-8">
                    <h3 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Request Token Top-Up</h3>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mt-2 mb-6">Upload payment slip for manual approval</p>

                    <form method="POST" action="{{ route('billing.topups.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Requested Tokens</label>
                            <input type="number" name="requested_tokens" min="1" step="1" value="100" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                        </div>
                        <div>
                            <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Transferred Amount</label>
                            <input type="number" name="amount_paid" min="0" step="0.01" placeholder="Optional" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                        </div>
                        <div>
                            <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Payment Slip</label>
                            <input type="file" name="payment_slip" accept="image/*" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 file:mr-4 file:rounded-full file:border-0 file:bg-discord-green file:px-4 file:py-2 file:text-xs file:font-black file:uppercase file:tracking-[0.2em] file:text-white dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                        </div>
                        <div>
                            <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Note</label>
                            <textarea name="note" rows="3" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Optional note for admin"></textarea>
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-discord-green px-5 py-4 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-[#1f8b4c]">
                            Submit Request
                        </button>
                    </form>
                </div>

                <div class="premium-card p-6 md:p-8">
                    <h3 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Token Policy</h3>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mt-2 mb-6">Current operating model</p>
                    <div class="space-y-3">
                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-5 py-4 text-sm font-bold text-slate-600 dark:border-white/5 dark:bg-white/[0.03] dark:text-slate-300">
                            There is no recurring subscription or monthly token refill in the current flow.
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-5 py-4 text-sm font-bold text-slate-600 dark:border-white/5 dark:bg-white/[0.03] dark:text-slate-300">
                            Super admin can add, deduct, or set your token balance directly from the owner console.
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-5 py-4 text-sm font-bold text-slate-600 dark:border-white/5 dark:bg-white/[0.03] dark:text-slate-300">
                            Slip top-up requests stay available as an optional manual approval flow when your team transfers money outside the app.
                        </div>
                    </div>
                </div>

                <div class="premium-card p-6 md:p-8">
                    <h3 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Recent Top-Up Requests</h3>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mt-2 mb-6">Approval queue history</p>
                    <div class="space-y-3">
                        @forelse($topupRequests as $request)
                            <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-black text-slate-900 dark:text-white">{{ number_format($request->requested_tokens) }} Tokens</div>
                                        <div class="mt-1 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ optional($request->created_at)->format('d M Y H:i') ?? 'Unknown time' }}</div>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] {{ $request->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : ($request->status === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300') }}">
                                        {{ $request->status }}
                                    </span>
                                </div>
                                @if($request->note)
                                    <p class="mt-3 text-sm font-medium text-slate-500 dark:text-slate-300">{{ $request->note }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-200 px-5 py-6 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No top-up requests yet
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

