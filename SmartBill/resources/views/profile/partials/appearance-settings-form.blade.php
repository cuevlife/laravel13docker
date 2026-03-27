<section>
    <header>
        <h2 class="text-sm font-black text-[#1e1f22] dark:text-[#f2f3f5] uppercase tracking-widest">
            {{ __('Appearance & Preferences') }}
        </h2>
        <p class="mt-1 text-xs text-[#5c5e66] dark:text-[#b5bac1]">
            {{ __('Customize how SmartBill looks and behaves for your account. These settings are synced across your devices.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Theme Selection -->
        <div class="space-y-4 pt-6">
            <h3 class="text-[10px] font-black text-[#80848e] uppercase tracking-widest">{{ __('Theme Settings') }}</h3>
            
            @php
                $settings = auth()->user()->settings ?? [];
                $theme = $settings['theme'] ?? 'system';
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="relative cursor-pointer group">
                    <input type="radio" name="settings[theme]" value="light" class="peer sr-only" {{ $theme === 'light' ? 'checked' : '' }}>
                    <div class="h-24 bg-[#f2f3f5] rounded-[16px] border-2 border-transparent peer-checked:border-discord-green peer-checked:shadow-[0_0_15px_rgba(35,165,89,0.3)] transition-all flex items-center justify-center overflow-hidden">
                        <div class="w-full h-full p-4 flex flex-col gap-2">
                            <div class="h-4 w-1/2 bg-white rounded-full"></div>
                            <div class="flex-1 bg-white rounded-[8px]"></div>
                        </div>
                    </div>
                    <div class="text-center mt-2 text-xs font-black text-[#5c5e66] dark:text-[#80848e] peer-checked:text-[#1e1f22] dark:peer-checked:text-white transition-colors">
                        Light Mode
                    </div>
                </label>

                <label class="relative cursor-pointer group">
                    <input type="radio" name="settings[theme]" value="dark" class="peer sr-only" {{ $theme === 'dark' ? 'checked' : '' }}>
                    <div class="h-24 bg-[#1e1f22] rounded-[16px] border-2 border-transparent peer-checked:border-discord-green peer-checked:shadow-[0_0_15px_rgba(35,165,89,0.3)] transition-all flex items-center justify-center overflow-hidden">
                        <div class="w-full h-full p-4 flex flex-col gap-2">
                            <div class="h-4 w-1/2 bg-[#2b2d31] rounded-full"></div>
                            <div class="flex-1 bg-[#2b2d31] rounded-[8px]"></div>
                        </div>
                    </div>
                    <div class="text-center mt-2 text-xs font-black text-[#5c5e66] dark:text-[#80848e] peer-checked:text-[#1e1f22] dark:peer-checked:text-white transition-colors">
                        Dark Mode
                    </div>
                </label>
                
                <label class="relative cursor-pointer group">
                    <input type="radio" name="settings[theme]" value="system" class="peer sr-only" {{ $theme === 'system' ? 'checked' : '' }}>
                    <div class="h-24 bg-gradient-to-r from-[#f2f3f5] to-[#1e1f22] rounded-[16px] border-2 border-transparent peer-checked:border-discord-green peer-checked:shadow-[0_0_15px_rgba(35,165,89,0.3)] transition-all flex items-center justify-center overflow-hidden">
                        <div class="w-full h-full p-4 flex gap-4">
                            <div class="flex-1 flex flex-col gap-2">
                                <div class="h-4 w-1/2 bg-white rounded-full"></div>
                                <div class="flex-1 bg-white rounded-[8px]"></div>
                            </div>
                            <div class="flex-1 flex flex-col gap-2">
                                <div class="h-4 w-1/2 bg-[#2b2d31] rounded-full"></div>
                                <div class="flex-1 bg-[#2b2d31] rounded-[8px]"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2 text-xs font-black text-[#5c5e66] dark:text-[#80848e] peer-checked:text-[#1e1f22] dark:peer-checked:text-white transition-colors">
                        Sync with System
                    </div>
                </label>
            </div>
            <p x-data x-init="$watch('[...document.querySelectorAll(`input[name=\'settings[theme]\']`)].find(e => e.checked)?.value', val => { if(val==='dark'){darkMode=true;localStorage.setItem('theme','dark')} else if(val==='light'){darkMode=false;localStorage.setItem('theme','light')} else { localStorage.removeItem('theme'); darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches; } })" class="text-[10px] text-[#5c5e66] dark:text-[#80848e] italic mt-2">
                * Note: Selecting a theme applies it instantly for preview, but you must click 'Save Settings' to permanently sync to your account.
            </p>
        </div>

        <!-- Notification Toggles -->
        <div class="space-y-4 pt-8">
            <h3 class="text-[10px] font-black text-[#80848e] uppercase tracking-widest mb-4">{{ __('Advanced Preferences') }}</h3>
            
            <div class="flex items-center justify-between p-4 bg-[#f2f3f5] dark:bg-[#1e1f22] rounded-[14px]">
                <div>
                    <h4 class="text-xs font-bold text-[#1e1f22] dark:text-[#f2f3f5]">Compact List View</h4>
                    <p class="text-[10px] text-[#5c5e66] dark:text-[#b5bac1] mt-0.5">Use smaller padding inside data tables (not yet implemented)</p>
                </div>
                <!-- Checkbox styling as toggle switch -->
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="settings[compact_view]" value="0">
                    <input type="checkbox" name="settings[compact_view]" value="1" class="sr-only peer" {{ !empty($settings['compact_view']) && $settings['compact_view'] !== '0' ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-[#d5d6d9] dark:bg-[#313338] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[#e3e5e8] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-discord-green"></div>
                </label>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 pt-8">
            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[14px] transition-all shadow-md active:scale-95 text-center">
                {{ __('Save Settings') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-[11px] font-black text-discord-green uppercase tracking-widest">
                    {{ __('✓ Settings Synced.') }}
                </p>
            @endif
        </div>
    </form>
</section>
