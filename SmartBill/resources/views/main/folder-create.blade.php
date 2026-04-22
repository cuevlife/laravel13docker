@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-6 sm:px-6 lg:px-8 animate-in fade-in duration-500">
        
        <!-- Master Container Card (Premium Minimal - High Density) -->
        <div class="bg-white dark:bg-[#2b2d31] rounded-xl shadow-sm border border-black/[0.05] dark:border-white/5 overflow-hidden">
            
            {{-- Header Section (Compact & Professional) --}}
            <div class="px-6 py-5 border-b border-black/[0.03] dark:border-white/[0.03] flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.users') }}" class="w-8 h-8 rounded-lg bg-white dark:bg-[#1e1f22] border border-black/10 dark:border-white/10 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all">
                        <i class="bi bi-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tightest">{{ __('Register New Folder') }}</h1>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ __('Step 1 of 1') }}</span>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.folders.store') }}" class="space-y-10">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        {{-- Section Title --}}
                        <div class="space-y-1">
                            <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">{{ __('Folder Identity') }}</h2>
                            <p class="text-[10px] font-medium text-slate-400 leading-relaxed uppercase tracking-tight">{{ __('Define the business name and system identifier.') }}</p>
                        </div>

                        {{-- Form Fields --}}
                        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black uppercase text-slate-500 ml-1 tracking-widest">{{ __('Business Name') }}</label>
                                <input type="text" name="name" required class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-2.5 text-sm font-bold dark:text-white focus:ring-2 focus:ring-discord-green/20 outline-none transition-all shadow-sm" placeholder="{{ __('e.g. My Company Ltd.') }}">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black uppercase text-slate-500 ml-1 tracking-widest">{{ __('System Slug') }}</label>
                                <input type="text" name="subdomain" required class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-2.5 text-sm font-bold dark:text-white focus:ring-2 focus:ring-discord-green/20 outline-none transition-all shadow-sm" placeholder="{{ __('unique-id') }}">
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-black/[0.03] dark:bg-white/[0.03]"></div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        {{-- Section Title --}}
                        <div class="space-y-1">
                            <h2 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">{{ __('Management') }}</h2>
                            <p class="text-[10px] font-medium text-slate-400 leading-relaxed uppercase tracking-tight">{{ __('Assign a primary owner to this folder.') }}</p>
                        </div>

                        {{-- Form Fields --}}
                        <div class="lg:col-span-2 max-w-xl">
                            <div class="space-y-1.5 relative">
                                <label class="block text-[9px] font-black uppercase text-slate-500 ml-1 tracking-widest">{{ __('Primary Manager') }}</label>
                                <select name="user_id" required class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-2.5 text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all shadow-sm appearance-none cursor-pointer">
                                    <option value="">{{ __('Select an account...') }}</option>
                                    @foreach($candidateOwners as $u)
                                        <option value="{{ $u->id }}" @selected($u->id == ($preselectedUserId ?? null))>{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-4 top-[30px] pointer-events-none text-slate-300">
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions (Compact) --}}
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-black/[0.03] dark:border-white/[0.03]">
                        <a href="{{ !empty($preselectedUserId) ? route('admin.users.show', ['user' => $preselectedUserId]) : route('admin.users') }}" class="px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-rose-500 transition-all">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="px-10 py-2.5 bg-discord-green text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-lg shadow-green-500/20 hover:bg-[#1f8b4c] transition-all active:scale-95">
                            {{ __('Register Folder') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
