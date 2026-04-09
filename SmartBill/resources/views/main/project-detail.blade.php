@extends('layouts.app')

@section('content')
    <div class="w-full py-6 px-4 sm:px-6 lg:px-10 animate-in fade-in duration-500">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-black/5 dark:border-white/5">
            <div class="flex items-center gap-4">
                <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="w-9 h-9 rounded-lg bg-white dark:bg-white/5 border border-black/10 dark:border-white/10 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all shadow-sm">
                    <i class="bi bi-arrow-left text-lg"></i>
                </a>
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">
                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'projects') }}" class="hover:text-indigo-600 transition">Folders</a>
                        <i class="bi bi-chevron-right text-[8px]"></i>
                        <span class="text-slate-600 dark:text-slate-300">Edit Folder</span>
                    </nav>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tightest">Folder Configuration</h1>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-md {{ $merchant->status === 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }} text-[10px] font-bold uppercase tracking-wide border">
                    {{ $merchant->status }}
                </span>
            </div>
        </div>

        <div class="space-y-6">
            
            <!-- Folder Settings -->
            <div class="bg-white dark:bg-discord-main p-6 rounded-xl border border-black/5 dark:border-white/5 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Folder Details</h2>
                <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $merchant->id) }}" class="space-y-4">
                    @csrf @method('PATCH')
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5">Folder Name</label>
                            <input type="text" name="name" value="{{ old('name', $merchant->name) }}" required class="w-full rounded-lg border border-black/10 bg-white px-3 py-2 text-sm dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5">System Subdomain</label>
                            <input type="text" value="{{ $merchant->subdomain }}" disabled class="w-full rounded-lg border border-black/5 bg-slate-50 px-3 py-2 text-sm text-slate-500 dark:bg-black/20 dark:border-white/5 shadow-inner cursor-not-allowed">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5">Tax ID</label>
                            <input type="text" name="tax_id" value="{{ old('tax_id', $merchant->tax_id) }}" class="w-full rounded-lg border border-black/10 bg-white px-3 py-2 text-sm dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1.5">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $merchant->phone) }}" class="w-full rounded-lg border border-black/10 bg-white px-3 py-2 text-sm dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 transition shadow-sm">Save Changes</button>
                    </div>
                </form>
            </div>

            <!-- Members -->
            <div class="bg-white dark:bg-discord-main rounded-xl border border-black/5 dark:border-white/5 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-black/5 dark:border-white/5 flex items-center justify-between bg-slate-50/50 dark:bg-white/[0.02]">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Member Access</h2>
                    <span class="text-xs text-slate-500">{{ $merchant->users->count() }} Linked</span>
                </div>

                <div class="p-4 bg-white dark:bg-black/10 border-b border-black/5 dark:border-white/5">
                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $merchant->id . '/members') }}" class="flex flex-col sm:flex-row items-center gap-3">
                        @csrf
                        <select name="user_id" class="flex-1 w-full sm:w-auto rounded-lg border border-black/10 bg-white px-3 py-2 text-sm dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-1 focus:ring-indigo-500">
                            <option value="">Select user to add...</option>
                            @foreach($candidateUsers as $candidateUser)
                                <option value="{{ $candidateUser->id }}">{{ $candidateUser->name }} ({{ $candidateUser->email }})</option>
                            @endforeach
                        </select>
                        <select name="workspace_role" class="w-full sm:w-32 rounded-lg border border-black/10 bg-white px-3 py-2 text-sm dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-1 focus:ring-indigo-500">
                            <option value="employee">Employee</option>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                        </select>
                        <button type="submit" class="w-full sm:w-auto bg-slate-900 text-white px-4 py-2 h-[38px] rounded-lg text-xs font-bold hover:bg-black transition shadow-sm">Add</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-white/[0.02] text-xs text-slate-500 border-b border-black/5 dark:border-white/5">
                            <tr>
                                <th class="px-6 py-2.5 font-bold">User Identity</th>
                                <th class="px-6 py-2.5 font-bold">Role</th>
                                <th class="px-6 py-2.5 text-right font-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5 dark:divide-white/5">
                            @forelse($merchant->users as $member)
                                @php $isPrimaryOwner = (int) $merchant->user_id === (int) $member->id; @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-white/[0.02] transition">
                                    <td class="px-6 py-3">
                                        <span class="font-bold text-slate-900 dark:text-white block">{{ $member->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $member->email }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $merchant->id . '/members/' . $member->id) }}" class="flex items-center gap-2">
                                            @csrf @method('PATCH')
                                            <select name="workspace_role" onchange="this.form.submit()" class="rounded-md border border-black/10 bg-white px-2 py-1 text-xs dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-1 focus:ring-indigo-500">
                                                <option value="employee" @selected($member->pivot?->role === 'employee')>Employee</option>
                                                <option value="admin" @selected($member->pivot?->role === 'admin')>Admin</option>
                                                <option value="owner" @selected($member->pivot?->role === 'owner' || $isPrimaryOwner)>Owner</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        @if($isPrimaryOwner)
                                            <span class="text-[10px] font-bold uppercase text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded">Primary</span>
                                        @else
                                            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $merchant->id . '/members/' . $member->id) }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs font-bold text-rose-500 hover:text-rose-700 transition">Remove</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">No members attached.</td>
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
                    <p class="text-xs text-rose-500/70 mt-0.5">Archive or restore this folder.</p>
                </div>
                <div class="flex gap-3">
                    <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'projects/' . $merchant->id . '/status') }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $merchant->status === 'active' ? 'archived' : 'active' }}">
                        <button type="submit" class="px-4 py-2 rounded-lg border border-rose-200 text-rose-600 text-xs font-bold hover:bg-white transition shadow-sm bg-rose-50/50">
                            {{ $merchant->status === 'active' ? 'Archive Folder' : 'Restore Folder' }}
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
