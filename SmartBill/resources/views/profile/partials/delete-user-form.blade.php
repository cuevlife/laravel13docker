<section class="space-y-6">
    <header>
        <h2 class="text-sm font-black text-discord-red uppercase tracking-widest flex items-center gap-2">
            <i class="bi bi-exclamation-triangle w-4 h-4"></i>
            {{ __('Delete Account') }}
        </h2>
        <p class="mt-2 text-xs text-[#5c5e66] dark:text-[#b5bac1] font-bold">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="px-6 py-3 bg-transparent border-2 border-discord-red text-discord-red hover:bg-discord-red hover:text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[14px] transition-all shadow-sm active:scale-95 text-center flex items-center gap-2">
        <i class="bi bi-trash-fill w-4 h-4"></i>
        {{ __('Delete My Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="bg-white dark:bg-[#313338] rounded-[24px] overflow-hidden shadow-2xl">
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6 md:p-8">
                @csrf
                @method('delete')

                <h2 class="text-lg font-black text-[#1e1f22] dark:text-[#f2f3f5] uppercase tracking-tight mb-2">
                    {{ __('Are you absolutely sure?') }}
                </h2>

                <p class="text-xs text-[#5c5e66] dark:text-[#b5bac1] mb-6">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <div class="space-y-4">
                    <label for="password" class="block text-[10px] font-black text-[#5c5e66] dark:text-[#80848e] uppercase tracking-widest mb-2">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password" class="w-full bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[12px] px-4 py-3 text-xs font-bold text-[#1e1f22] dark:text-white focus:ring-2 focus:ring-discord-red/50 transition-all outline-none" placeholder="{{ __('Verify Password') }}" />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-[10px] text-discord-red font-bold" />
                </div>

                <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4">
                    <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-transparent text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[14px] transition-all text-center">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="px-6 py-3 bg-discord-red hover:bg-[#da373c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[14px] transition-all shadow-md active:scale-95 text-center flex items-center justify-center gap-2">
                        <i class="bi bi-trash-fill w-4 h-4"></i> {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</section>
