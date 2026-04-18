@extends('layouts.app')

@section('content')
    @php
        $profileExitUrl = isset($activeTenant)
            ? \App\Support\WorkspaceUrl::current(request(), 'dashboard')
            : route('dashboard');
    @endphp
    <!-- Ultra-Minimal Premium Settings Hub -->
    <div x-data="{ 
        activeTab: window.location.hash ? window.location.hash.substring(1) : 'account',
        init() {
            this.$watch('activeTab', value => window.location.hash = value);
            window.addEventListener('hashchange', () => {
                const hash = window.location.hash.substring(1);
                if (hash && ['account', 'security', 'billing'].includes(hash)) {
                    this.activeTab = hash;
                }
            });
        }
    }" class="flex flex-col md:flex-row min-h-screen md:min-h-[calc(100vh-64px)] bg-slate-50 dark:bg-[#0B0E14] animate-in fade-in duration-700 pb-20 md:pb-0 font-sans tracking-tight relative">
        

        <!-- Minimal Sidebar -->
        <div class="w-full md:w-[280px] lg:w-[320px] p-6 md:p-10 shrink-0 flex flex-col gap-2 md:h-[calc(100vh-64px)] md:sticky md:top-0 border-r border-[#e3e5e8]/50 dark:border-[#313338]/50 overflow-y-auto">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#80848e] px-4 mb-4">{{ __('Settings') }}</h3>
            
            <button @click="activeTab = 'account'" 
                    class="w-full text-left px-4 py-3 rounded-[16px] text-xs font-bold transition-all flex items-center gap-3 relative overflow-hidden group"
                    :class="activeTab === 'account' ? 'bg-white dark:bg-[#2b2d31] text-[#1e1f22] dark:text-white shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-[#e3e5e8] dark:border-[#313338]' : 'text-[#5c5e66] dark:text-[#b5bac1] hover:bg-[#f2f3f5] dark:hover:bg-[#2b2d31]/50 border border-transparent'">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 bg-discord-green rounded-r-full transition-all duration-300" :class="activeTab === 'account' ? 'h-5 opacity-100' : 'h-0 opacity-0'"></div>
                <i class="bi bi-person-fill w-4 h-4 transition-transform group-hover:scale-110"></i> {{ __('My Account') }}
            </button>
            <button @click="activeTab = 'security'" 
                    class="w-full text-left px-4 py-3 rounded-[16px] text-xs font-bold transition-all flex items-center gap-3 relative overflow-hidden group"
                    :class="activeTab === 'security' ? 'bg-white dark:bg-[#2b2d31] text-[#1e1f22] dark:text-white shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-[#e3e5e8] dark:border-[#313338]' : 'text-[#5c5e66] dark:text-[#b5bac1] hover:bg-[#f2f3f5] dark:hover:bg-[#2b2d31]/50 border border-transparent'">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 bg-discord-green rounded-r-full transition-all duration-300" :class="activeTab === 'security' ? 'h-5 opacity-100' : 'h-0 opacity-0'"></div>
                <i class="bi bi-shield-fill w-4 h-4 transition-transform group-hover:scale-110"></i> {{ __('Security') }}
            </button>
            <button @click="activeTab = 'billing'" 
                    class="w-full text-left px-4 py-3 rounded-[16px] text-xs font-bold transition-all flex items-center gap-3 relative overflow-hidden group"
                    :class="activeTab === 'billing' ? 'bg-white dark:bg-[#2b2d31] text-[#1e1f22] dark:text-white shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-[#e3e5e8] dark:border-[#313338]' : 'text-[#5c5e66] dark:text-[#b5bac1] hover:bg-[#f2f3f5] dark:hover:bg-[#2b2d31]/50 border border-transparent'">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 bg-discord-green rounded-r-full transition-all duration-300" :class="activeTab === 'billing' ? 'h-5 opacity-100' : 'h-0 opacity-0'"></div>
                <i class="bi bi-credit-card-fill w-4 h-4 transition-transform group-hover:scale-110"></i> {{ __('Billing & Tokens') }}
            </button>

        </div>

        <!-- Main Content Area -->
        <div class="flex-1 p-6 md:p-12 lg:p-16 w-full">
            <!-- Hidden on mobile because the mobile top nav handles the title -->
            <h2 class="hidden md:block text-2xl md:text-3xl font-black text-[#1e1f22] dark:text-white tracking-tight mb-10 transition-all duration-300" x-text="activeTab === 'account' ? '{{ __('Profile Details') }}' : (activeTab === 'security' ? '{{ __('Security & Access') }}' : '{{ __('Billing & Tokens') }}')"></h2>

            <div x-show="activeTab === 'account'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-12">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="space-y-12" style="display: none;">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div x-show="activeTab === 'billing'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="space-y-8" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-6 rounded-xl bg-white dark:bg-[#2b2d31] border border-black/[0.05] dark:border-white/5 shadow-sm">
                        <div class="text-[9px] font-black uppercase tracking-[0.2em] text-[#80848e] mb-3">{{ __('Current Balance') }}</div>
                        <div class="flex items-center gap-3">
                            <i class="bi bi-coin text-discord-green text-2xl"></i>
                            <div class="text-2xl font-black text-[#1e1f22] dark:text-white uppercase">{{ number_format($user->tokens) }} <span class="text-[10px] text-[#80848e]">{{ __('Tokens') }}</span></div>
                        </div>
                    </div>
                    <div class="p-6 rounded-xl bg-white dark:bg-[#2b2d31] border border-black/[0.05] dark:border-white/5 shadow-sm">
                        <div class="text-[9px] font-black uppercase tracking-[0.2em] text-[#80848e] mb-3">{{ __('Usage This Month') }}</div>
                        <div class="flex items-center gap-3">
                            <i class="bi bi-graph-up-arrow text-indigo-500 text-2xl"></i>
                            <div class="text-2xl font-black text-[#1e1f22] dark:text-white uppercase">{{ number_format($usageThisMonth) }} <span class="text-[10px] text-[#80848e]">{{ __('Slips') }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-[#06C755]/5 to-transparent p-8 rounded-xl border border-[#06C755]/20 shadow-sm flex flex-col md:flex-row items-center gap-8">
                    <div class="w-16 h-16 bg-[#06C755] rounded-xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-[#06C755]/20">
                        <i class="bi bi-line text-3xl"></i>
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <h4 class="text-lg font-black text-[#1e1f22] dark:text-white mb-1">{{ __('Top up tokens via LINE Official') }}</h4>
                        <p class="text-xs font-bold text-[#5c5e66] dark:text-[#b5bac1] leading-relaxed">{{ __('Contact staff to top up tokens instantly') }}</p>
                    </div>
                    <a href="https://line.me/ti/p/@vetmanage" target="_blank" class="px-8 py-3 rounded-xl bg-[#06C755] text-white text-[11px] font-black uppercase tracking-widest hover:bg-[#05b34c] transition-all active:scale-95 shadow-md shadow-green-500/10">
                        {{ __('Chat via LINE') }}
                    </a>
                </div>

                <div class="h-px w-full bg-[#e3e5e8] dark:bg-[#313338] my-10"></div>

                <div class="space-y-6 pb-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-clock-history text-[#80848e]"></i>
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white">{{ __('Usage History') }}</h3>
                        </div>
                        <span class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest bg-black/[0.03] dark:bg-white/[0.03] px-2 py-1 rounded-lg">{{ __('Grouped by hour') }}</span>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-black/[0.05] dark:border-white/5 bg-white dark:bg-[#2b2d31]">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-black/[0.02] dark:bg-white/[0.02] text-[9px] font-black uppercase tracking-widest text-[#80848e]">
                                    <tr>
                                        <th class="px-4 py-3">{{ __('Description') }}</th>
                                        <th class="px-4 py-3 text-center">{{ __('Type') }}</th>
                                        <th class="px-4 py-3 text-right">{{ __('Delta') }}</th>
                                        <th class="px-4 py-3 text-right">{{ __('Balance') }}</th>
                                        <th class="px-4 py-3 text-right">{{ __('Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/5 dark:divide-white/5 text-[11px] font-bold">
                                    @forelse($tokenLogs as $log)
                                        <tr class="hover:bg-black/[0.01] dark:hover:bg-white/[0.01]">
                                            <td class="px-4 py-3 text-[#1e1f22] dark:text-white">
                                                <span>{{ $log->description }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-1.5 py-0.5 rounded text-[8px] uppercase border {{ $log->type === 'usage' ? 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10' : 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10' }}">
                                                    {{ $log->type }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right {{ $log->delta < 0 ? 'text-rose-500' : 'text-discord-green' }}">
                                                {{ $log->delta > 0 ? '+' : '' }}{{ $log->delta }}
                                            </td>
                                            <td class="px-4 py-3 text-right text-[#80848e]">{{ number_format($log->balance_after) }}</td>
                                            <td class="px-4 py-3 text-right text-[#80848e] text-[9px]">{{ optional($log->created_at)->format('H:i') }} <span class="opacity-50">({{ optional($log->created_at)->format('d/m') }})</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-10 text-center text-[#80848e] italic uppercase tracking-widest text-[9px]">{{ __('No transactions yet') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Close/Escape Button -->
        <div class="hidden lg:flex flex-col items-center justify-start pt-10 pr-10 w-24 shrink-0">
            <a href="{{ $profileExitUrl }}" class="group flex flex-col items-center gap-2">
                <div class="w-10 h-10 border-2 border-[#80848e] rounded-full flex items-center justify-center text-[#80848e] group-hover:bg-[#80848e] group-hover:text-white dark:group-hover:bg-[#b5bac1] dark:group-hover:text-[#1e1f22] transition-all">
                    <i class="bi bi-x-lg text-lg leading-none"></i>
                </div>
                <span class="text-[10px] font-black uppercase text-[#80848e]">{{ __('ESC') }}</span>
            </a>
        </div>

    </div>
@endsection
