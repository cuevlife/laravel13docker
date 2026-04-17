@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8 animate-in fade-in duration-500" x-data="adminSettings()">
        <div class="rounded-xl bg-white p-6 sm:p-8 shadow-sm border border-black/[0.04] dark:bg-[#2b2d31] dark:border-white/5 transition-all">
            {{-- Header --}}
            <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-black/[0.03] dark:border-white/[0.03] pb-8">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-discord-green/10 text-discord-green text-2xl shadow-sm">
                        <i class="bi bi-cpu-fill"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">{{ __('AI System Control') }}</h1>
                        <p class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest mt-1">{{ __('Global Extraction Engine Configuration') }}</p>
                    </div>
                </div>
                <button type="button" @click="saveAllSettings()" :disabled="saving"
                        class="h-12 px-8 rounded-xl bg-discord-green text-white font-black text-[11px] uppercase tracking-[0.2em] shadow-lg shadow-green-500/20 hover:bg-[#1f8b4c] transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2">
                    <i class="bi bi-cloud-check-fill" x-show="!saving"></i>
                    <i class="bi bi-arrow-repeat animate-spin" x-show="saving" x-cloak></i>
                    <span x-text="saving ? 'Saving...' : 'Save All Settings'"></span>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 py-6">
                
                {{-- Left Side: API Key Pool --}}
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white flex items-center gap-2">
                            <i class="bi bi-key-fill text-indigo-500"></i>
                            Gemini API Pool
                        </h3>
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]" x-text="form.api_keys.length + ' Keys Active'"></span>
                    </div>

                    <div class="bg-[#f8fafb] dark:bg-black/10 p-6 rounded-xl border border-black/[0.03] dark:border-white/[0.03] space-y-4">
                        <div class="space-y-3">
                            <template x-for="(key, index) in form.api_keys" :key="index">
                                <div class="flex flex-col gap-1.5 animate-in slide-in-from-left-2 duration-300">
                                    <div class="flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <i class="bi bi-shield-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="password" x-model="form.api_keys[index]" 
                                                   class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] pl-10 pr-4 py-3 text-xs font-bold dark:text-white outline-none focus:ring-2 focus:ring-discord-green/20 transition-all shadow-sm" 
                                                   placeholder="AIzaSy...">
                                        </div>
                                        <button @click="form.api_keys.splice(index, 1)" class="h-10 w-10 flex items-center justify-center rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-end px-1" x-show="usage[form.api_keys[index]] !== undefined">
                                        <div class="text-[9px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-1.5">
                                            <span class="h-1 w-1 rounded-full bg-indigo-400"></span>
                                            Calls: <span class="text-indigo-500" x-text="usage[form.api_keys[index]] || 0"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <button @click="form.api_keys.push('')" class="w-full py-4 rounded-xl border-2 border-dashed border-black/5 dark:border-white/5 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:border-discord-green/30 hover:text-discord-green transition-all bg-white dark:bg-transparent">
                                <i class="bi bi-plus-lg mr-1"></i> Add New API Key
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Model Settings --}}
                <div class="space-y-6">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white flex items-center gap-2">
                        <i class="bi bi-robot text-discord-green"></i>
                        Model Configuration
                    </h3>

                    <div class="bg-[#f8fafb] dark:bg-black/10 p-6 rounded-xl border border-black/[0.03] dark:border-white/[0.03] space-y-6">
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Active Model ID</label>
                            <input type="text" x-model="form.gemini_model" 
                                   class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-6 py-4 text-sm font-black text-[#1e1f22] dark:text-white outline-none focus:ring-2 focus:ring-discord-green/20 transition-all shadow-sm" 
                                   placeholder="e.g. gemini-1.5-flash">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: document.documentElement.classList.contains('dark') ? '#2b2d31' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#1e1f22'
            });

            Alpine.data('adminSettings', () => ({
                saving: false,
                usage: {!! json_encode($settings['api_key_usage'] ?? []) !!},
                form: {
                    gemini_model: {!! json_encode($settings['gemini_model'] ?? 'gemini-1.5-flash') !!},
                    api_keys: {!! json_encode($settings['gemini_api_keys'] ?? ['']) !!}
                },

                async saveAllSettings() {
                    this.saving = true;
                    try {
                        const response = await fetch('{{ route('admin.settings.update') }}', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });

                        if (!response.ok) throw new Error('Failed to update system configuration');

                        Toast.fire({
                            icon: 'success',
                            title: 'Configuration Saved',
                            text: 'AI settings updated successfully.'
                        });
                    } catch (error) {
                        Toast.fire({ icon: 'error', title: 'Update Failed', text: error.message });
                    } finally {
                        this.saving = false;
                    }
                }
            }));
        });
    </script>
    @endpush
@endsection
