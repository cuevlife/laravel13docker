<x-ui.modal name="user-create" maxWidth="xl" :backdropClose="false">
    <div x-data="{
        loading: false,
        errors: {},
        formData: {
            name: '',
            username: '',
            email: '',
            password: '',
            password_confirmation: '',
            role: '{{ \App\Models\User::ROLE_USER }}',
            tokens: 0
        },
        async submit() {
            this.loading = true;
            this.errors = {};
            
            try {
                const response = await fetch('{{ route('admin.users.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (response.status === 422) {
                    this.errors = data.errors || {};
                    window.notify.error('{{ __('Please fix the validation errors.') }}');
                    return;
                }

                if (data.status === 'success') {
                    window.notify.success(data.message);
                    this.$dispatch('close-modal', { name: 'user-create' });
                    this.$dispatch('refresh-users');
                    
                    // Reset Form for next time
                    this.formData = {
                        name: '',
                        username: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        role: '{{ \App\Models\User::ROLE_USER }}',
                        tokens: 0
                    };
                } else {
                    window.notify.error(data.message || '{{ __('Something went wrong') }}');
                }
            } catch (e) {
                window.notify.error('{{ __('System Error: Please try again later.') }}');
            } finally {
                this.loading = false;
            }
        }
    }">
        {{-- Header Section --}}
        <div class="px-8 py-6 border-b border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 shadow-sm">
                    <i class="bi bi-person-plus-fill text-xl"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">{{ __('Register New User') }}</h3>
                    <p class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest mt-0.5">{{ __('Authorized Entity Provisioning') }}</p>
                </div>
            </div>
            <button @click="$dispatch('close-modal', { name: 'user-create' })" :disabled="loading" class="text-[#80848e] hover:text-[#1e1f22] dark:hover:text-white transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form @submit.prevent="submit">
            <div class="p-8 space-y-8">
                <!-- Details Section -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                        <h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Identity Details') }}</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Full Name') }}</label>
                            <input type="text" x-model="formData.name" required 
                                   :class="errors.name ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-black/[0.05]'"
                                   class="w-full h-11 rounded-xl border bg-[#f8fafb] dark:bg-[#1e1f22] px-4 text-xs font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/10 transition-all shadow-sm" placeholder="{{ __('e.g. John Doe') }}">
                            <p x-show="errors.name" x-text="errors.name ? errors.name[0] : ''" class="text-[9px] font-bold text-rose-500 ml-1 mt-1"></p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Username') }}</label>
                            <input type="text" x-model="formData.username" required 
                                   @input="formData.username = formData.username.toLowerCase().replace(/[^a-z0-9._-]/g, '')"
                                   :class="errors.username ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-black/[0.05]'"
                                   class="w-full h-11 rounded-xl border bg-[#f8fafb] dark:bg-[#1e1f22] px-4 text-xs font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/10 transition-all shadow-sm" placeholder="{{ __('e.g. john.doe') }}">
                            <p x-show="errors.username" x-text="errors.username ? errors.username[0] : ''" class="text-[9px] font-bold text-rose-500 ml-1 mt-1"></p>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Email Address') }}</label>
                        <input type="email" x-model="formData.email" required 
                               @input="formData.email = formData.email.toLowerCase()"
                               :class="errors.email ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-black/[0.05]'"
                               class="w-full h-11 rounded-xl border bg-[#f8fafb] dark:bg-[#1e1f22] px-4 text-xs font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/10 transition-all shadow-sm" placeholder="{{ __('john@company.com') }}">
                        <p x-show="errors.email" x-text="errors.email ? errors.email[0] : ''" class="text-[9px] font-bold text-rose-500 ml-1 mt-1"></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Password') }}</label>
                            <input type="password" x-model="formData.password" required 
                                   :class="errors.password ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-black/[0.05]'"
                                   class="w-full h-11 rounded-xl border bg-[#f8fafb] dark:bg-[#1e1f22] px-4 text-xs font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/10 transition-all shadow-sm">
                            <p x-show="errors.password" x-text="errors.password ? errors.password[0] : ''" class="text-[9px] font-bold text-rose-500 ml-1 mt-1"></p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Confirm Password') }}</label>
                            <input type="password" x-model="formData.password_confirmation" required 
                                   :class="errors.password_confirmation ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-black/[0.05]'"
                                   class="w-full h-11 rounded-xl border bg-[#f8fafb] dark:bg-[#1e1f22] px-4 text-xs font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/10 transition-all shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                        <h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Access Configuration') }}</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Initial Role') }}</label>
                            <select x-model="formData.role" required 
                                    class="w-full h-11 rounded-xl border border-black/[0.05] bg-[#f8fafb] dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white focus:ring-2 focus:ring-emerald-500/10 outline-none transition-all cursor-pointer shadow-sm">
                                <option value="{{ \App\Models\User::ROLE_USER }}">{{ __('Standard Staff') }}</option>
                                <option value="{{ \App\Models\User::ROLE_TENANT_ADMIN }}">{{ __('Folder Admin') }}</option>
                                <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}">{{ __('Super Admin') }}</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Initial Tokens') }}</label>
                            <input type="number" x-model="formData.tokens" min="0" 
                                   class="w-full h-11 rounded-xl border border-black/[0.05] bg-[#f8fafb] dark:bg-[#1e1f22] px-4 text-xs font-bold dark:text-white focus:ring-2 focus:ring-emerald-500/10 outline-none transition-all shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Section --}}
            <div class="px-8 py-6 border-t border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/5 flex items-center justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', { name: 'user-create' })" :disabled="loading" class="px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-[#80848e] hover:text-[#5c5e66] transition-all">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" :disabled="loading" class="px-8 py-3 bg-discord-green text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-green-500/20 hover:bg-[#1f8b4c] transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2">
                    <i x-show="loading" class="bi bi-arrow-repeat animate-spin"></i>
                    <span x-text="loading ? '{{ __('Processing...') }}' : '{{ __('Register Entity') }}'"></span>
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>
