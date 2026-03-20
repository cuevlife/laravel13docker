<x-app-layout>
    <x-slot name="header">
        <span class="flex items-center space-x-2">
            <i data-lucide="shield-check" class="w-4 h-4 text-indigo-500"></i>
            <span class="font-bold text-slate-800 dark:text-slate-200 uppercase tracking-widest text-xs">Access Control Registry</span>
        </span>
    </x-slot>

    <div class="bg-white dark:bg-[#0b0f1a] rounded-3xl border border-slate-200/60 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-white/5 flex items-center justify-between bg-slate-50/30 dark:bg-white/5">
            <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Authorized Entities</h3>
            <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-tighter">{{ count($users) }} Nodes Active</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-white/5">
                        <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Identification</th>
                        <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Network Alias</th>
                        <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Email Link</th>
                        <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Creation Temporal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition group">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center text-[10px] font-black text-slate-500">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">@ {{ $user->username }}</span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">{{ $user->created_at->format('d M Y') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
