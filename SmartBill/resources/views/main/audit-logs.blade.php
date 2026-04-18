@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8 animate-in fade-in duration-500" x-data="{ activeTab: 'audit' }">
        
        <!-- Master Container Card -->
        <div class="rounded-xl bg-white p-6 sm:p-8 shadow-sm border border-black/[0.04] dark:bg-[#2b2d31] dark:border-white/5 transition-all">
            
            {{-- Header Section --}}
            <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-500 text-2xl shadow-sm">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">{{ __('Global System Audit') }}</h1>
                        <p class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest mt-1">{{ __('Centralized Registry of All Administrative Activities') }}</p>
                    </div>
                </div>

                {{-- Tab Switcher --}}
                <div class="flex items-center bg-[#f2f3f5] dark:bg-[#1e1f22] p-1 rounded-xl shadow-inner border border-black/5 dark:border-white/5">
                    <button @click="activeTab = 'audit'" 
                            :class="activeTab === 'audit' ? 'bg-white dark:bg-[#2b2d31] text-[#1e1f22] dark:text-white shadow-sm' : 'text-[#80848e] hover:text-[#5c5e66]'"
                            class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                        {{ __('Activity Logs') }}
                    </button>
                    <button @click="activeTab = 'tokens'" 
                            :class="activeTab === 'tokens' ? 'bg-white dark:bg-[#2b2d31] text-[#1e1f22] dark:text-white shadow-sm' : 'text-[#80848e] hover:text-[#5c5e66]'"
                            class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                        {{ __('Token Logs') }}
                    </button>
                </div>
            </div>

            {{-- Audit Logs Table --}}
            <div x-show="activeTab === 'audit'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="overflow-hidden border border-black/[0.05] dark:border-white/5 rounded-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-black/[0.02] dark:bg-white/[0.02] text-[9px] font-black uppercase tracking-widest text-[#80848e]">
                                <tr>
                                    <th class="px-6 py-4">{{ __('Operator') }}</th>
                                    <th class="px-6 py-4">{{ __('Event') }}</th>
                                    <th class="px-6 py-4">{{ __('Description') }}</th>
                                    <th class="px-6 py-4">{{ __('Source IP') }}</th>
                                    <th class="px-6 py-4 text-right">{{ __('Timestamp') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5 dark:divide-white/5 text-[11px] font-bold">
                                @forelse($auditLogs as $log)
                                    <tr class="hover:bg-black/[0.01] dark:hover:bg-white/[0.01] transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 font-black text-[10px]">
                                                    {{ substr($log->user->name ?? '?', 0, 1) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-[#1e1f22] dark:text-white leading-none">{{ $log->user->name ?? 'System' }}</span>
                                                    <span class="text-[8px] text-[#80848e] uppercase tracking-widest mt-1">{{ $log->user->username ?? 'system' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 rounded-lg text-[8px] uppercase font-black border border-black/5 bg-black/[0.02] text-[#5c5e66] dark:text-[#b5bac1] dark:bg-white/5">
                                                {{ str_replace('_', ' ', $log->event) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-[#1e1f22] dark:text-white">
                                            <span class="block truncate max-w-xs xl:max-w-md" title="{{ $log->description }}">{{ $log->description }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-[#80848e] text-[10px] font-mono">
                                            {{ $log->ip_address ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-[#80848e] font-medium">
                                            <span class="block text-[10px]">{{ optional($log->created_at)->format('d M Y') }}</span>
                                            <span class="block text-[8px] opacity-50">{{ optional($log->created_at)->format('H:i:s') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center text-[#80848e] italic uppercase tracking-widest text-[10px] font-black">
                                            {{ __('No activity logs recorded yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-6">
                    {{ $auditLogs->links() }}
                </div>
            </div>

            {{-- Token Logs Table --}}
            <div x-show="activeTab === 'tokens'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="overflow-hidden border border-black/[0.05] dark:border-white/5 rounded-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-black/[0.02] dark:bg-white/[0.02] text-[9px] font-black uppercase tracking-widest text-[#80848e]">
                                <tr>
                                    <th class="px-6 py-4">{{ __('Subject') }}</th>
                                    <th class="px-6 py-4">{{ __('Description') }}</th>
                                    <th class="px-4 py-4 text-center">{{ __('Type') }}</th>
                                    <th class="px-4 py-4 text-right">{{ __('Amount') }}</th>
                                    <th class="px-4 py-4 text-right">{{ __('Balance') }}</th>
                                    <th class="px-6 py-4 text-right">{{ __('Timestamp') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5 dark:divide-white/5 text-[11px] font-bold">
                                @forelse($tokenLogs as $log)
                                    <tr class="hover:bg-black/[0.01] dark:hover:bg-white/[0.01] transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-[#1e1f22] dark:text-white leading-none">{{ $log->user->name ?? 'System' }}</span>
                                                <span class="text-[8px] text-[#80848e] uppercase tracking-widest mt-1">{{ $log->user->username ?? 'system' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-[#1e1f22] dark:text-white">
                                            <span class="block truncate max-w-xs xl:max-w-md" title="{{ $log->description }}">{{ $log->description }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="px-2 py-0.5 rounded-lg text-[8px] uppercase font-black border {{ in_array($log->type, ['manual_credit', 'manual_topup_approved', 'manual_settlement']) ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                                {{ str_replace('_', ' ', $log->type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right {{ $log->delta < 0 ? 'text-rose-500' : 'text-discord-green' }}">
                                            {{ $log->delta > 0 ? '+' : '' }}{{ number_format($log->delta) }}
                                        </td>
                                        <td class="px-4 py-4 text-right text-[#80848e]">{{ number_format($log->balance_after) }}</td>
                                        <td class="px-6 py-4 text-right text-[#80848e] font-medium">
                                            <span class="block text-[10px]">{{ optional($log->created_at)->format('d M Y') }}</span>
                                            <span class="block text-[8px] opacity-50">{{ optional($log->created_at)->format('H:i:s') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-16 text-center text-[#80848e] italic uppercase tracking-widest text-[10px] font-black">
                                            {{ __('No token transactions recorded yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-6">
                    {{ $tokenLogs->links() }}
                </div>
            </div>

        </div>
    </div>
@endsection
