<section x-data="passwordUpdater()">
    <form @submit.prevent="submitForm" class="mt-6 space-y-6">
        <div class="space-y-4">
            <div>
                <label class="block text-[10px] font-black text-[#5c5e66] dark:text-[#80848e] uppercase tracking-widest mb-2 ml-1">{{ __('Current Password') }}</label>
                <x-ui.input type="password" x-model="form.current_password" required autocomplete="current-password" />
                <template x-if="errors.current_password">
                    <p class="mt-2 text-[10px] text-discord-red font-bold" x-text="errors.current_password[0]"></p>
                </template>
            </div>

            <div>
                <label class="block text-[10px] font-black text-[#5c5e66] dark:text-[#80848e] uppercase tracking-widest mb-2 ml-1">{{ __('New Password') }}</label>
                <x-ui.input type="password" x-model="form.password" required autocomplete="new-password" />
                <template x-if="errors.password">
                    <p class="mt-2 text-[10px] text-discord-red font-bold" x-text="errors.password[0]"></p>
                </template>
            </div>

            <div>
                <label class="block text-[10px] font-black text-[#5c5e66] dark:text-[#80848e] uppercase tracking-widest mb-2 ml-1">{{ __('Confirm Password') }}</label>
                <x-ui.input type="password" x-model="form.password_confirmation" required autocomplete="new-password" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-6">
            <x-ui.button type="submit" variant="success" size="lg" ::disabled="loading" class="w-full sm:w-auto px-10 shadow-lg shadow-green-500/10">
                <span x-show="!loading">{{ __('Update Password') }}</span>
                <span x-show="loading"><i class="bi bi-arrow-repeat animate-spin"></i></span>
            </x-ui.button>
        </div>
    </form>

    <script>
        function passwordUpdater() {
            return {
                loading: false,
                form: { current_password: '', password: '', password_confirmation: '' },
                errors: {},
                async submitForm() {
                    this.loading = true;
                    this.errors = {};
                    try {
                        const response = await fetch('{{ route('password.update') }}', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.form)
                        });

                        if (response.ok) {
                            window.notify.success('{{ __('Password updated successfully.') }}');
                            this.form = { current_password: '', password: '', password_confirmation: '' };
                        } else {
                            const data = await response.json();
                            this.errors = data.errors || {};
                            // แสดง Alert ก้อนใหญ่ถ้ามี Error
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('Update Failed') }}',
                                text: data.message || '{{ __('Please check your input and try again.') }}',
                                confirmButtonColor: '#ed4245',
                                background: document.documentElement.classList.contains('dark') ? '#2b2d31' : '#ffffff',
                                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#1e1f22'
                            });
                        }
                    } catch (error) {
                        window.notify.error('{{ __('Something went wrong.') }}');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</section>