<x-owner-layout>
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8 animate-in fade-in duration-700 pb-20">
        <div class="bg-white dark:bg-[#2b2d31] rounded-xl p-6 md:p-8 border border-black/[0.04] dark:border-white/5 border-l-4 border-l-amber-500 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-2xl md:text-3xl font-black text-[#1e1f22] dark:text-[#f2f3f5] uppercase tracking-tight leading-none">Top-Up Review Queue</h2>
                    <p class="text-[10px] font-bold text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-[0.2em] mt-3">Approve or reject customer transfer slips</p>
                </div>
                <div class="inline-flex items-center gap-3 px-5 py-3 rounded-xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em]">
                    <i class="bi bi-cash-coin text-base"></i> {{ $requests->where('status', 'pending')->count() }} Pending
                </div>
            </div>
        </div>

        <div class="space-y-5 mt-8">
            @forelse($requests as $request)
                <div class="bg-white dark:bg-[#2b2d31] rounded-xl p-6 border border-black/[0.04] dark:border-white/5 shadow-sm">
                    <div class="grid grid-cols-1 xl:grid-cols-[0.8fr_1.2fr] gap-6">
                        <div>
                            <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#1e1f22]">
                                <img src="{{ asset('storage/' . $request->payment_slip_path) }}" alt="Payment Slip" class="h-full w-full object-cover">
                            </div>
                        </div>
                        <div class="space-y-5">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($request->requested_tokens) }} Tokens</div>
                                    <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ $request->user->name }} · {{ $request->user->email }}</div>
                                </div>
                                <span class="rounded-full px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] {{ $request->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : ($request->status === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300') }}">
                                    {{ $request->status }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="rounded-xl bg-slate-50 px-4 py-4 dark:bg-white/[0.03]">
                                    <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Amount Paid</div>
                                    <div class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ $request->amount_paid ? number_format($request->amount_paid, 2) . ' ' . $request->currency : 'Not specified' }}</div>
                                </div>
                                <div class="rounded-xl bg-slate-50 px-4 py-4 dark:bg-white/[0.03]">
                                    <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Requested At</div>
                                    <div class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ optional($request->created_at)->format('d M Y H:i') ?? 'Unknown time' }}</div>
                                </div>
                                <div class="rounded-xl bg-slate-50 px-4 py-4 dark:bg-white/[0.03]">
                                    <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Reviewed By</div>
                                    <div class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ $request->reviewer?->name ?? 'Pending' }}</div>
                                </div>
                            </div>

                            @if($request->note)
                                <div class="rounded-xl bg-slate-50 px-4 py-4 text-sm font-medium text-slate-600 dark:bg-white/[0.03] dark:text-slate-300">
                                    {{ $request->note }}
                                </div>
                            @endif

                            @if($request->admin_note)
                                <div class="rounded-xl bg-slate-900/5 px-4 py-4 text-sm font-medium text-slate-600 dark:bg-white/[0.04] dark:text-slate-300">
                                    Admin note: {{ $request->admin_note }}
                                </div>
                            @endif

                            @if($request->status === 'pending')
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'topups/' . $request->id . '/approve') }}" class="space-y-3">
                                        @csrf
                                        <textarea name="admin_note" rows="2" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white shadow-inner" placeholder="Optional approval note"></textarea>
                                        <button type="submit" class="w-full rounded-xl bg-discord-green px-5 py-4 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-[#1f8b4c] shadow-lg shadow-green-500/20">
                                            Approve and Credit Tokens
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'topups/' . $request->id . '/reject') }}" class="space-y-3">
                                        @csrf
                                        <textarea name="admin_note" rows="2" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white shadow-inner" placeholder="Optional rejection reason"></textarea>
                                        <button type="submit" class="w-full rounded-xl bg-rose-500 px-5 py-4 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-rose-600 shadow-lg shadow-rose-500/20">
                                            Reject Request
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-[#2b2d31] rounded-xl p-12 text-center border border-black/[0.04] dark:border-white/5 shadow-sm">
                    <div class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">No top-up requests found</div>
                </div>
            @endforelse
        </div>
    </div>
</x-owner-layout>
