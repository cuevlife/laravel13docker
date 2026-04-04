<x-owner-layout>
    <div class="space-y-8 animate-in fade-in duration-700 pb-20">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Project Detail</div>
                <h1 class="mt-3 text-3xl font-black uppercase tracking-tight text-slate-900 dark:text-white">{{ $merchant->name }}</h1>
                <p class="mt-3 text-sm font-bold text-slate-500 dark:text-slate-400">Project {{ str_pad((string) $merchant->id, 2, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="inline-flex items-center justify-center rounded-[16px] border border-slate-200 px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-slate-700 transition hover:border-slate-300 hover:text-slate-900 dark:border-white/10 dark:text-white">
                    Back to Projects
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Owner</div>
                <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ $merchant->owner?->name ?? 'Unassigned' }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] {{ $merchant->status === 'active' ? 'text-emerald-500' : 'text-amber-500' }}">{{ $merchant->statusLabel() }}</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Members</div>
                <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($merchant->users->count()) }}</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Profiles</div>
                <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($merchant->templates_count) }}</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Slips</div>
                <div class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($merchant->slips_count) }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="space-y-6">
                <div class="premium-card p-6 md:p-8">
                    <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Project Settings</h2>
                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $merchant->id) }}" class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="name" value="{{ old('name', $merchant->name) }}" required class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Project name">
                        <select name="user_id" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                            <option value="">No primary owner</option>
                            @foreach($candidateUsers as $candidateUser)
                                <option value="{{ $candidateUser->id }}" @selected((int) old('user_id', $merchant->user_id) === (int) $candidateUser->id)>{{ $candidateUser->name }} / {{ $candidateUser->email }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="phone" value="{{ old('phone', $merchant->phone) }}" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Phone">
                        <input type="text" name="tax_id" value="{{ old('tax_id', $merchant->tax_id) }}" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white md:col-span-2" placeholder="Tax ID">
                        <textarea name="address" rows="3" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white md:col-span-2" placeholder="Address / note">{{ old('address', $merchant->address) }}</textarea>
                        <button type="submit" class="rounded-[16px] bg-discord-green px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-[#1f8b4c] md:col-span-2">
                            Save Project
                        </button>
                    </form>
                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $merchant->id . '/status') }}" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-[1fr_auto]">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $merchant->status === 'active' ? 'archived' : 'active' }}">
                        <div class="rounded-[16px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-700 dark:border-white/10 dark:bg-white/[0.03] dark:text-white">
                            Workspace state: {{ $merchant->statusLabel() }}
                        </div>
                        <button type="submit" class="rounded-[16px] {{ $merchant->status === 'active' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-500' }} px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white transition">
                            {{ $merchant->status === 'active' ? 'Archive Project' : 'Restore Project' }}
                        </button>
                    </form>
                </div>

                <div class="premium-card p-6 md:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Members</h2>
                            <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Project access control</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $merchant->id . '/members') }}" class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-[1.2fr_0.8fr_auto]">
                        @csrf
                        <select name="user_id" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                            @foreach($candidateUsers as $candidateUser)
                                <option value="{{ $candidateUser->id }}">{{ $candidateUser->name }} / {{ $candidateUser->email }}</option>
                            @endforeach
                        </select>
                        <select name="workspace_role" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                            <option value="employee">Employee</option>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                        </select>
                        <button type="submit" class="rounded-[16px] bg-indigo-600 px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-indigo-500">
                            Add Member
                        </button>
                    </form>

                    <div class="mt-6 space-y-3">
                        @forelse($merchant->users as $member)
                            @php
                                $isPrimaryOwner = (int) $merchant->user_id === (int) $member->id;
                            @endphp
                            <div class="rounded-[20px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <div class="text-sm font-black text-slate-900 dark:text-white">{{ $member->name }}</div>
                                        <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ $member->email }} @if($member->username) / {{ $member->username }} @endif</div>
                                    </div>
                                    <div class="flex flex-col gap-3 lg:items-end">
                                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $merchant->id . '/members/' . $member->id) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="workspace_role" class="rounded-[12px] border border-slate-200 bg-white px-3 py-2 text-[11px] font-black uppercase tracking-[0.12em] text-slate-700 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                                                <option value="employee" @selected($member->pivot?->role === 'employee')>Employee</option>
                                                <option value="admin" @selected($member->pivot?->role === 'admin')>Admin</option>
                                                <option value="owner" @selected($member->pivot?->role === 'owner' || $isPrimaryOwner)>Owner</option>
                                            </select>
                                            <button type="submit" class="rounded-[12px] bg-slate-900 px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-white transition hover:bg-black">
                                                Update
                                            </button>
                                        </form>
                                        <div class="flex items-center gap-3">
                                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                                {{ $isPrimaryOwner ? 'Primary Owner' : strtoupper($member->pivot?->role ?? 'member') }}
                                            </div>
                                            @if(!$isPrimaryOwner)
                                                <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $merchant->id . '/members/' . $member->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-[12px] border border-rose-200 px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-rose-500 transition hover:bg-rose-50 dark:border-rose-500/20 dark:hover:bg-rose-500/10">
                                                        Remove
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[22px] border-2 border-dashed border-slate-200 px-5 py-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No members linked
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="premium-card p-6 md:p-8">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Profiles</h2>
                        <a href="{{ route('projects.open', ['project' => $merchant->id, 'next' => 'templates']) }}" class="inline-flex h-9 items-center justify-center rounded-[12px] bg-discord-green px-4 text-[10px] font-black uppercase tracking-[0.22em] text-white transition hover:bg-[#1f8b4c]">
                            <i data-lucide="settings-2" class="w-3.5 h-3.5 mr-2"></i> Manage Profiles
                        </a>
                    </div>
                    <div class="space-y-3">
                        @forelse($merchant->templates as $template)
                            <div class="rounded-[20px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                                <div class="text-sm font-black text-slate-900 dark:text-white">{{ $template->name }}</div>
                                <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ count($template->ai_fields ?? []) }} mapped fields</div>
                            </div>
                        @empty
                            <div class="rounded-[22px] border-2 border-dashed border-slate-200 px-5 py-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No profiles yet
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="premium-card p-6 md:p-8">
                    <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Recent Slip Activity</h2>
                    <div class="mt-6 space-y-3">
                        @forelse($recentSlips as $slip)
                            <div class="rounded-[20px] border border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/5 dark:bg-white/[0.03]">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <div class="text-sm font-black text-slate-900 dark:text-white">{{ $slip->template?->name ?? 'Template removed' }}</div>
                                        <div class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ $slip->user?->name ?? 'Unknown user' }}</div>
                                    </div>
                                    <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                        {{ optional($slip->processed_at)->format('d M Y H:i') ?? 'Pending' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[22px] border-2 border-dashed border-slate-200 px-5 py-10 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:border-white/10">
                                No slip activity yet
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-owner-layout>
