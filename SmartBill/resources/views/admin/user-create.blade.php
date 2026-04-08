<x-owner-layout>
    <div class="w-full py-6 px-4 sm:px-6 lg:px-10 animate-in fade-in duration-500">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-black/5 dark:border-white/5">
            <div class="flex items-center gap-4">
                <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="w-9 h-9 rounded-lg bg-white dark:bg-white/5 border border-black/10 dark:border-white/10 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all shadow-sm">
                    <i class="bi bi-arrow-left text-lg"></i>
                </a>
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">
                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="hover:text-indigo-600 transition">Users</a>
                        <i class="bi bi-chevron-right text-[8px]"></i>
                        <span class="text-slate-600 dark:text-slate-300">New Account</span>
                    </nav>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tightest">Add User Account</h1>
                </div>
            </div>
        </div>

        <div class="w-full">
            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="space-y-6">
                @csrf
                
                <!-- Identity Section -->
                <div class="bg-white dark:bg-discord-main p-8 rounded-xl border border-black/5 dark:border-white/5 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-4 bg-discord-green rounded-full"></div>
                        <h2 class="text-sm font-black uppercase tracking-widest text-slate-900 dark:text-white">Account Identity</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black uppercase text-slate-400 ml-1">Full Display Name</label>
                            <input type="text" name="name" required class="w-full rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-discord-green/20 outline-none transition-all" placeholder="John Doe">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black uppercase text-slate-400 ml-1">Unique Username</label>
                            <input type="text" name="username" required class="w-full rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-discord-green/20 outline-none transition-all" placeholder="johndoe">
                        </div>
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="block text-[10px] font-black uppercase text-slate-400 ml-1">Email Address</label>
                            <input type="email" name="email" required class="w-full rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-discord-green/20 outline-none transition-all" placeholder="john@example.com">
                        </div>
                    </div>
                </div>

                <!-- Access & Quota Section -->
                <div class="bg-white dark:bg-discord-main p-8 rounded-xl border border-black/5 dark:border-white/5 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-4 bg-indigo-500 rounded-full"></div>
                        <h2 class="text-sm font-black uppercase tracking-widest text-slate-900 dark:text-white">Access & Provisioning</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black uppercase text-slate-400 ml-1">System Role</label>
                            <select name="role" class="w-full rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                                <option value="{{ \App\Models\User::ROLE_USER }}">Standard User</option>
                                <option value="{{ \App\Models\User::ROLE_TENANT_ADMIN }}">Tenant Admin</option>
                                <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}">Super Admin</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black uppercase text-slate-400 ml-1">Opening Tokens</label>
                            <input type="number" name="tokens" value="50" min="0" class="w-full rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="bg-white dark:bg-discord-main p-8 rounded-xl border border-black/5 dark:border-white/5 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-4 bg-rose-500 rounded-full"></div>
                        <h2 class="text-sm font-black uppercase tracking-widest text-slate-900 dark:text-white">Security Credentials</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black uppercase text-slate-400 ml-1">Account Password</label>
                            <input type="password" name="password" required class="w-full rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-rose-500/20 outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black uppercase text-slate-400 ml-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" required class="w-full rounded-lg border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-rose-500/20 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-8 mt-4 border-t border-black/[0.03] dark:border-white/5">
                    <a href="{{ \App\Support\OwnerUrl::path(request(), 'users') }}" class="px-6 py-3 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-600 hover:bg-black/5 rounded-xl transition-all">
                        Discard Changes
                    </a>
                    <button type="submit" class="flex items-center gap-2 px-10 py-3 bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-xl hover:bg-black transition-all active:scale-95">
                        <i class="bi bi-person-plus-fill"></i>
                        Provision Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-owner-layout>