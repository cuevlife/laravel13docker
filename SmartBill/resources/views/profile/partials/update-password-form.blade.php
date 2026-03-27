<section>
    <header>
        <h2 class="text-sm font-black text-[#1e1f22] dark:text-[#f2f3f5] uppercase tracking-widest">
            {{ __('Update Password') }}
        </h2>
        <p class="mt-1 text-xs text-[#5c5e66] dark:text-[#b5bac1]">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="space-y-4">
            <div>
                <label for="update_password_current_password" class="block text-[10px] font-black text-[#5c5e66] dark:text-[#80848e] uppercase tracking-widest mb-2">{{ __('Current Password') }}</label>
                <input id="update_password_current_password" name="current_password" type="password" class="w-full bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[12px] px-4 py-3 text-xs font-bold text-[#1e1f22] dark:text-white focus:ring-2 focus:ring-discord-green/50 transition-all outline-none" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-[10px] text-discord-red font-bold" />
            </div>

            <div>
                <label for="update_password_password" class="block text-[10px] font-black text-[#5c5e66] dark:text-[#80848e] uppercase tracking-widest mb-2">{{ __('New Password') }}</label>
                <input id="update_password_password" name="password" type="password" class="w-full bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[12px] px-4 py-3 text-xs font-bold text-[#1e1f22] dark:text-white focus:ring-2 focus:ring-discord-green/50 transition-all outline-none" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-[10px] text-discord-red font-bold" />
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block text-[10px] font-black text-[#5c5e66] dark:text-[#80848e] uppercase tracking-widest mb-2">{{ __('Confirm Password') }}</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[12px] px-4 py-3 text-xs font-bold text-[#1e1f22] dark:text-white focus:ring-2 focus:ring-discord-green/50 transition-all outline-none" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-[10px] text-discord-red font-bold" />
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 pt-6">
            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[14px] transition-all shadow-md active:scale-95 text-center">
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-[11px] font-black text-discord-green uppercase tracking-widest">
                    {{ __('✓ Password updated.') }}
                </p>
            @endif
        </div>
    </form>
</section>
