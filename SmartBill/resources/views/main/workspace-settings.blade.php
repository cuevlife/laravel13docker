@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8 animate-in fade-in duration-500">
        <div class="space-y-8">
            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black text-[#1e1f22] dark:text-white tracking-tight uppercase">Folder Settings</h1>
                    <p class="text-[10px] font-bold text-[#80848e] uppercase tracking-widest mt-1">Manage members for {{ $tenant->name }}</p>
                </div>
                <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                    <i class="bi bi-people-fill text-lg"></i>
                </div>
            </div>

            {{-- Members List Section --}}
            <div class="bg-white dark:bg-[#2b2d31] rounded-xl border border-black/[0.05] dark:border-white/5 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-black/[0.03] dark:border-white/[0.03] flex items-center justify-between bg-[#f8fafb] dark:bg-black/10">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white">Members & Permissions</h3>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $members->count() }} Users</span>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Quick Add Member --}}
                    <form method="POST" action="{{ route('workspace.settings.members.add') }}" class="flex flex-col sm:flex-row gap-2">
                        @csrf
                        <div class="flex-1 relative">
                            <i class="bi bi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="email" name="email" required placeholder="staff@company.com" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-black/5 bg-[#f8fafb] dark:bg-[#1e1f22] dark:text-white text-sm font-bold outline-none focus:ring-1 focus:ring-discord-green/30 transition-all">
                        </div>
                        <div class="flex gap-2">
                            <select name="role" class="px-3 py-2.5 rounded-xl border border-black/5 bg-[#f8fafb] dark:bg-[#1e1f22] dark:text-white text-xs font-bold outline-none focus:ring-1 focus:ring-discord-green/30 transition-all">
                                <option value="employee">Employee</option>
                                <option value="admin">Admin</option>
                            </select>
                            <button type="submit" class="px-6 py-2.5 bg-discord-green text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-[#1f8b4c] transition shadow-md shadow-green-500/20 active:scale-95">
                                Add Member
                            </button>
                        </div>
                    </form>
                    @error('email')
                        <p class="mt-2 text-[9px] font-bold text-rose-500 ml-1 uppercase">{{ $message }}</p>
                    @enderror

                    {{-- Members Table-like List --}}
                    <div class="divide-y divide-black/[0.03] dark:divide-white/[0.03] border-t border-black/[0.03] dark:border-white/[0.03]">
                        @foreach($members as $member)
                            @php
                                $mRole = $member->pivot->role ?? 'Member';
                                $isMe = (int)$member->id === (int)auth()->id();
                                $canDelete = (int)$tenant->user_id !== (int)$member->id; // Primary owner cannot be removed
                            @endphp
                            <div class="flex items-center justify-between py-4 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#f2f3f5] dark:bg-[#1e1f22] flex items-center justify-center text-xs font-black text-slate-400 border border-black/5 uppercase">
                                        {{ substr($member->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-[13px] font-black text-[#1e1f22] dark:text-white flex items-center gap-2">
                                            {{ $member->name }}
                                            @if($isMe)
                                                <span class="px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 text-[8px] uppercase font-black">You</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">{{ $member->email }}</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4">
                                    <span class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-lg border
                                        {{ $mRole === 'owner' ? 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10' : 
                                          ($mRole === 'admin' ? 'bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-500/10' : 
                                          'bg-slate-50 text-slate-400 border-slate-100 dark:bg-white/5') }}">
                                        {{ $mRole }}
                                    </span>

                                    @if($canDelete && !$isMe)
                                        <form method="POST" action="{{ route('workspace.settings.members.remove', $member->id) }}" onsubmit="return confirm('Remove this member?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-all">
                                                <i class="bi bi-person-x-fill text-lg"></i>
                                            </button>
                                        </form>
                                    @else
                                        <div class="w-8 h-8"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Compact Folder Metadata --}}
            <div class="flex items-center justify-between px-2 pt-4">
                <div class="flex items-center gap-4">
                    <div class="text-[9px] font-black uppercase tracking-widest text-slate-400">Owner: <span class="text-[#1e1f22] dark:text-white">{{ $tenant->owner->name ?? '---' }}</span></div>
                    <div class="text-[9px] font-black uppercase tracking-widest text-slate-400">Created: <span class="text-[#1e1f22] dark:text-white">{{ $tenant->created_at?->format('d/m/Y') }}</span></div>
                </div>
                <div class="text-[9px] font-black uppercase tracking-widest text-slate-400 italic">Folder ID: #{{ $tenant->id }}</div>
            </div>
        </div>
    </div>
@endsection
