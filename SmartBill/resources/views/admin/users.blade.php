<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-black italic tracking-tightest uppercase dark:text-white">{{ __('User List') }}</h2>
    </x-slot>

    <div class="animate-in fade-in duration-700">
        <div class="bg-white dark:bg-discord-main rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 dark:border-white/5 bg-slate-50/30 dark:bg-black/5 flex items-center justify-between">
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] italic">Authorized Entity Registry</span>
                <span class="text-[9px] font-bold text-discord-red uppercase">{{ count($users) }} Nodes Active</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-white/5 text-slate-400 dark:text-slate-600">
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest italic">Identity</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest italic">Network Alias</th>
                            <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest italic">Temporal Node</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-discord-black flex items-center justify-center text-rose-500 font-black shadow-inner">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-slate-800 dark:text-white uppercase italic leading-none">{{ $user->name }}</div>
                                            <div class="text-[9px] text-slate-400 font-bold mt-2 uppercase tracking-tighter">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="px-3 py-1 bg-slate-100 dark:bg-black/20 text-indigo-600 dark:text-indigo-400 rounded-lg text-[10px] font-black uppercase tracking-widest italic border border-slate-200 dark:border-white/5 shadow-sm">
                                        @ {{ $user->username }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-600 uppercase tracking-widest">{{ $user->created_at->format('d M Y') }}</div>
                                    <div class="text-[9px] text-slate-300 dark:text-slate-700 font-medium uppercase mt-1">Registry Log Established</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
