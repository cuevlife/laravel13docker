<x-owner-layout>
    <div class="space-y-8 animate-in fade-in duration-700 pb-20">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Projects</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['projects']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Client workspaces</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Members</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['memberships']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Project access links</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Profiles</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['templates']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Extraction templates</div>
            </div>
            <div class="premium-card p-5">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Slips</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['slips']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Processed documents</div>
            </div>
            <div class="premium-card p-5 border-l-4 border-l-amber-500">
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Archived</div>
                <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['archived']) }}</div>
                <div class="mt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">Inactive workspaces</div>
            </div>
        </div>

        <div class="premium-card p-6 md:p-8">
            <div>
                <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Create Project</div>
                <h2 class="mt-3 text-2xl font-black uppercase tracking-tight text-slate-900 dark:text-white">Provision New Workspace</h2>
                <p class="mt-2 text-sm font-bold text-slate-500 dark:text-slate-400">Create a company or client project with an optional primary owner from the SaaS back office.</p>
            </div>

            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-4">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" required class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Project / company name">
                <select name="user_id" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white">
                    <option value="">No primary owner yet</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}">{{ $owner->name }} / {{ $owner->email }}</option>
                    @endforeach
                </select>
                <input type="text" name="phone" value="{{ old('phone') }}" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Phone">
                <input type="text" name="tax_id" value="{{ old('tax_id') }}" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white" placeholder="Tax ID">
                <input type="text" name="address" value="{{ old('address') }}" class="rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-800 dark:border-white/10 dark:bg-[#1e1f22] dark:text-white xl:col-span-3" placeholder="Address / note">
                <button type="submit" class="rounded-[16px] bg-discord-green px-5 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-[#1f8b4c]">
                    Create Project
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-discord-main rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 dark:border-white/5 bg-slate-50/30 dark:bg-black/5 flex items-center justify-between">
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em]">Project Registry</span>
                <span class="text-[9px] font-bold text-discord-green uppercase">{{ count($projects) }} Workspaces</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-white/5 text-slate-400 dark:text-slate-600">
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Project</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Owner</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Members</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Profiles</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Slips</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach($projects as $project)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-black text-slate-900 dark:text-white">{{ $project->name }}</div>
                                    <div class="mt-2 text-[9px] font-bold uppercase tracking-[0.2em] text-slate-400">Project {{ str_pad((string) $project->id, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div class="mt-2 text-[9px] font-black uppercase tracking-[0.2em] {{ $project->status === 'active' ? 'text-emerald-500' : 'text-amber-500' }}">{{ $project->statusLabel() }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-black text-slate-900 dark:text-white">{{ $project->owner?->name ?? 'Unassigned' }}</div>
                                    <div class="mt-2 text-[9px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ $project->owner?->email ?? 'No owner' }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-black text-slate-900 dark:text-white">{{ number_format($project->users->count()) }}</div>
                                    <div class="mt-2 text-[9px] font-bold uppercase tracking-[0.2em] text-slate-400">Linked members</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-black text-slate-900 dark:text-white">{{ number_format($project->templates_count) }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-black text-slate-900 dark:text-white">{{ number_format($project->slips_count) }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex flex-col gap-2">
                                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $project->id) }}" class="inline-flex items-center justify-center rounded-[12px] border border-slate-200 px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-slate-700 transition hover:border-indigo-300 hover:text-indigo-600 dark:border-white/10 dark:text-white dark:hover:text-indigo-300">
                                            Open Detail
                                        </a>
                                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $project->id . '/status') }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $project->status === 'active' ? 'archived' : 'active' }}">
                                            <button type="submit" class="w-full rounded-[12px] {{ $project->status === 'active' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-500' }} px-3 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-white transition">
                                                {{ $project->status === 'active' ? 'Archive' : 'Restore' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-owner-layout>
