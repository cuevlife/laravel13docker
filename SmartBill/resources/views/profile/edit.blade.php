<x-app-layout>
    @php
        $profileExitUrl = isset($activeTenant)
            ? \App\Support\WorkspaceUrl::current(request(), 'dashboard')
            : route('dashboard');
    @endphp
    <!-- Ultra-Minimal Premium Settings Hub -->
    <div x-data="{ activeTab: 'account' }" class="flex flex-col md:flex-row min-h-screen md:min-h-[calc(100vh-64px)] bg-[#fafafa] dark:bg-[#1e1f22] animate-in fade-in duration-700 pb-20 md:pb-0 font-sans tracking-tight relative">
        

        <!-- Minimal Sidebar -->
        <div class="w-full md:w-[280px] lg:w-[320px] p-6 md:p-10 shrink-0 flex flex-col gap-2 md:h-[calc(100vh-64px)] md:sticky md:top-0 border-r border-[#e3e5e8]/50 dark:border-[#313338]/50 overflow-y-auto">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#80848e] px-4 mb-4">Settings</h3>
            
            <button @click="activeTab = 'account'" 
                    class="w-full text-left px-4 py-3 rounded-[16px] text-xs font-bold transition-all flex items-center gap-3 relative overflow-hidden group"
                    :class="activeTab === 'account' ? 'bg-white dark:bg-[#2b2d31] text-[#1e1f22] dark:text-white shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-[#e3e5e8] dark:border-[#313338]' : 'text-[#5c5e66] dark:text-[#b5bac1] hover:bg-[#f2f3f5] dark:hover:bg-[#2b2d31]/50 border border-transparent'">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 bg-discord-green rounded-r-full transition-all duration-300" :class="activeTab === 'account' ? 'h-5 opacity-100' : 'h-0 opacity-0'"></div>
                <i class="bi bi-person-fill w-4 h-4 transition-transform group-hover:scale-110"></i> My Account
            </button>
            <button @click="activeTab = 'appearance'" 
                    class="w-full text-left px-4 py-3 rounded-[16px] text-xs font-bold transition-all flex items-center gap-3 relative overflow-hidden group"
                    :class="activeTab === 'appearance' ? 'bg-white dark:bg-[#2b2d31] text-[#1e1f22] dark:text-white shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-[#e3e5e8] dark:border-[#313338]' : 'text-[#5c5e66] dark:text-[#b5bac1] hover:bg-[#f2f3f5] dark:hover:bg-[#2b2d31]/50 border border-transparent'">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 bg-discord-green rounded-r-full transition-all duration-300" :class="activeTab === 'appearance' ? 'h-5 opacity-100' : 'h-0 opacity-0'"></div>
                <i class="bi bi-palette-fill w-4 h-4 transition-transform group-hover:scale-110"></i> Appearance
            </button>
            <button @click="activeTab = 'security'" 
                    class="w-full text-left px-4 py-3 rounded-[16px] text-xs font-bold transition-all flex items-center gap-3 relative overflow-hidden group"
                    :class="activeTab === 'security' ? 'bg-white dark:bg-[#2b2d31] text-[#1e1f22] dark:text-white shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-[#e3e5e8] dark:border-[#313338]' : 'text-[#5c5e66] dark:text-[#b5bac1] hover:bg-[#f2f3f5] dark:hover:bg-[#2b2d31]/50 border border-transparent'">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 bg-discord-green rounded-r-full transition-all duration-300" :class="activeTab === 'security' ? 'h-5 opacity-100' : 'h-0 opacity-0'"></div>
                <i class="bi bi-shield-fill w-4 h-4 transition-transform group-hover:scale-110"></i> Security
            </button>

        </div>

        <!-- Main Content Area -->
        <div class="flex-1 p-6 md:p-12 lg:p-16 max-w-4xl mx-auto w-full">
            <!-- Hidden on mobile because the mobile top nav handles the title -->
            <h2 class="hidden md:block text-2xl md:text-3xl font-black text-[#1e1f22] dark:text-white tracking-tight mb-10 transition-all duration-300" x-text="activeTab === 'account' ? 'Profile Details' : (activeTab === 'appearance' ? 'Interface Settings' : 'Security & Access')"></h2>

            <div x-show="activeTab === 'account'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-12">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
                
                <div class="h-px w-full bg-[#e3e5e8] dark:bg-[#313338] my-10"></div>

                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            <div x-show="activeTab === 'appearance'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="space-y-12" style="display: none;">
                <div class="max-w-2xl">
                    @include('profile.partials.appearance-settings-form')
                </div>
            </div>

            <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="space-y-12" style="display: none;">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <!-- Desktop Close/Escape Button -->
        <div class="hidden lg:flex flex-col items-center justify-start pt-10 pr-10 w-24 shrink-0">
            <a href="{{ $profileExitUrl }}" class="group flex flex-col items-center gap-2">
                <div class="w-10 h-10 border-2 border-[#80848e] rounded-full flex items-center justify-center text-[#80848e] group-hover:bg-[#80848e] group-hover:text-white dark:group-hover:bg-[#b5bac1] dark:group-hover:text-[#1e1f22] transition-all">
                    <i class="bi bi-x-lg w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-black uppercase text-[#80848e]">ESC</span>
            </a>
        </div>

    </div>
</x-app-layout>
