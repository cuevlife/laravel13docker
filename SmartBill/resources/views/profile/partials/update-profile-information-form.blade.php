<section>
    <header>
        <h2 class="text-sm font-black text-[#1e1f22] dark:text-[#f2f3f5] uppercase tracking-widest">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-xs text-[#5c5e66] dark:text-[#b5bac1]">
            {{ __("Update your account's profile information and email address. Note that your user role cannot be changed here.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="space-y-4">
            <div>
                <label for="name" class="block text-[10px] font-black text-[#5c5e66] dark:text-[#80848e] uppercase tracking-widest mb-2">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" class="w-full bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[12px] px-4 py-3 text-xs font-bold text-[#1e1f22] dark:text-white focus:ring-2 focus:ring-discord-green/50 transition-all outline-none" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                <x-input-error class="mt-2 text-[10px] text-discord-red font-bold" :messages="$errors->get('name')" />
            </div>

            <div>
                <label for="email" class="block text-[10px] font-black text-[#5c5e66] dark:text-[#80848e] uppercase tracking-widest mb-2">{{ __('Email Address') }}</label>
                <input id="email" name="email" type="email" class="w-full bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[12px] px-4 py-3 text-xs font-bold text-[#1e1f22] dark:text-white focus:ring-2 focus:ring-discord-green/50 transition-all outline-none" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                <x-input-error class="mt-2 text-[10px] text-discord-red font-bold" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-4 p-4 bg-discord-red/10 border border-discord-red/20 rounded-[12px]">
                        <p class="text-xs text-discord-red font-bold">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification" class="underline hover:text-red-700 transition-colors">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-xs text-discord-green font-bold">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 pt-6">
            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[14px] transition-all shadow-md active:scale-95 text-center">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-[11px] font-black text-discord-green uppercase tracking-widest">
                    {{ __('✓ Saved successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>
