@extends('layouts.app')

@section('content')
    <div class="w-full py-8 px-4 sm:px-6 lg:px-8 animate-in fade-in duration-500">
        
        <!-- Master Container Card -->
        <div class="bg-white dark:bg-[#2b2d31] rounded-xl shadow-sm border border-black/[0.05] dark:border-white/5 overflow-hidden">
            
            {{-- Header Section --}}
            <div class="px-8 py-10 border-b border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/5">
                <div class="flex items-center gap-5">
                    <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="w-10 h-10 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/10 dark:border-white/10 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all shadow-sm">
                        <i class="bi bi-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">
                            <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="hover:text-indigo-600 transition">Users</a>
                            <i class="bi bi-chevron-right text-[8px]"></i>
                            <span class="text-slate-600 dark:text-slate-300">New Account</span>
                        </nav>
                        <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tightest">Register New User</h1>
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-12">
                <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="space-y-12">
                    @csrf
                    
                    <!-- Details Section -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-5 bg-indigo-500 rounded-full"></div>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">User Details</h2>
                        </div>

                        <div class="bg-[#f8fafb] dark:bg-black/10 p-8 rounded-xl border border-black/[0.03] dark:border-white/[0.03] space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 ml-1">Full Name</label>
                                    <input type="text" name="name" required class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-3 text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all shadow-sm" placeholder="e.g. John Doe">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 ml-1">Username (Login ID)</label>
                                    <input type="text" name="username" required class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-3 text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all shadow-sm" placeholder="e.g. john.doe">
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black uppercase text-slate-400 ml-1">Email Address</label>
                                <input type="email" name="email" required class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-3 text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all shadow-sm" placeholder="john@company.com">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 ml-1">Password</label>
                                    <input type="password" name="password" required class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-3 text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all shadow-sm">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 ml-1">Confirm Password</label>
                                    <input type="password" name="password_confirmation" required class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-3 text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Section -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-5 bg-emerald-500 rounded-full"></div>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Access Settings</h2>
                        </div>

                        <div class="bg-[#f8fafb] dark:bg-black/10 p-8 rounded-xl border border-black/[0.03] dark:border-white/[0.03] grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black uppercase text-slate-400 ml-1">Initial Role</label>
                                <select name="role" required class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-3 text-sm font-bold dark:text-white focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all cursor-pointer">
                                    <option value="{{ \App\Models\User::ROLE_USER }}">Standard Staff</option>
                                    <option value="{{ \App\Models\User::ROLE_TENANT_ADMIN }}">Folder Admin</option>
                                    <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}">Super Admin</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black uppercase text-slate-400 ml-1">Initial Tokens</label>
                                <input type="number" name="tokens" value="0" min="0" class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-3 text-sm font-bold dark:text-white focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-6 border-t border-black/[0.03]">
                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="px-8 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="w-full sm:w-auto px-10 py-3.5 bg-discord-green text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-lg shadow-green-500/20 hover:bg-[#1f8b4c] transition-all active:scale-95">
                            Create User Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
