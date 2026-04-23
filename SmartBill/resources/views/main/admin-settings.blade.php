@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8 animate-in fade-in duration-500">
        <x-ui.card x-data="adminSettings()" class="p-6 sm:p-8">
            {{-- Header --}}
            <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-white/5 border border-black/5 dark:border-white/5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-all shadow-sm group">
                <i class="bi bi-arrow-left transition-transform group-hover:-translate-x-1"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">{{ __('Back') }}</span>
            </a>

            <x-ui.page-header 
                :title="__('AI System Control')" 
                :subtitle="__('Global Extraction Engine Configuration')" 
                icon="bi-cpu-fill"
                variant="success"
                class="mb-10 pb-8 border-b border-black/[0.03] dark:border-white/[0.03]"
            >
                
            </x-ui.page-header>

            <div class="space-y-12 py-6">
                
                {{-- Section 1: AI Engine Configuration (Grid 2 Cols) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    {{-- Left Side: API Key Pool --}}
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white flex items-center gap-2">
                                <i class="bi bi-key-fill text-indigo-500"></i>
                                {{ __('Gemini API Pool') }}
                            </h3>
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]" x-text="form.api_keys.length + ' ' + '{{ __('Keys Active') }}'"></span>
                        </div>

                        <div class="bg-[#f8fafb] dark:bg-black/10 p-6 rounded-xl border border-black/[0.03] dark:border-white/[0.03] space-y-4">
                            <div class="space-y-3">
                                <template x-for="(key, index) in form.api_keys" :key="index">
                                    <div class="flex flex-col gap-1.5 animate-in slide-in-from-left-2 duration-300">
                                        <div class="flex items-center gap-2">
                                            <div class="relative flex-1">
                                                <i class="bi bi-shield-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                <x-ui.input type="password" x-model="form.api_keys[index]"
                                                       class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] pl-10 pr-4 py-3 text-xs font-bold dark:text-white outline-none focus:ring-2 focus:ring-discord-green/20 transition-all shadow-sm"     
                                                       placeholder="{{ __('AIzaSy...') }}" />
                                            </div>
                                            <button @click="form.api_keys.splice(index, 1)" class="h-10 w-10 flex items-center justify-center rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-end px-1" x-show="usage[form.api_keys[index]] !== undefined">
                                            <div class="text-[9px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-1.5">
                                                <span class="h-1 w-1 rounded-full bg-indigo-400"></span>
                                                {{ __('Calls:') }} <span class="text-indigo-500" x-text="usage[form.api_keys[index]] || 0"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <button @click="form.api_keys.push('')" class="w-full py-4 rounded-xl border-2 border-dashed border-black/5 dark:border-white/5 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:border-discord-green/30 hover:text-discord-green transition-all bg-white dark:bg-transparent">
                                    <i class="bi bi-plus-lg mr-1"></i> {{ __('Add New API Key') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: Model Settings --}}
                    <div class="space-y-6">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white flex items-center gap-2">
                            <i class="bi bi-robot text-discord-green"></i>
                            {{ __('Model Configuration') }}
                        </h3>

                        <div class="bg-[#f8fafb] dark:bg-black/10 p-6 rounded-xl border border-black/[0.03] dark:border-white/[0.03] space-y-6">
                            <div class="space-y-2">
                                <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">{{ __('Active Model ID') }}</label>
                                <x-ui.input type="text" x-model="form.gemini_model"
                                       class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-6 py-4 text-sm font-black text-[#1e1f22] dark:text-white outline-none focus:ring-2 focus:ring-discord-green/20 transition-all shadow-sm"
                                       placeholder="{{ __('e.g. gemini-1.5-flash') }}" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Excel Export Configuration (Full Width) --}}
                <div class="space-y-6 border-t border-black/[0.03] dark:border-white/[0.03] pt-12">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white flex items-center gap-2">
                        <i class="bi bi-file-earmark-excel-fill text-discord-green"></i>
                        {{ __('Excel Export Settings') }}
                    </h3>

                    <div class="bg-[#f8fafb] dark:bg-black/10 p-8 rounded-xl border border-black/[0.03] dark:border-white/[0.03]">
                        <div class="max-w-xl">
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-3 ml-1">{{ __('Default Export Style') }}</label>
                            <select x-model="form.excel_export_style" class="w-full rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-6 py-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-discord-green/20 transition-all shadow-sm cursor-pointer">
                                <option value="flat">{{ __('Data Analytics (One Sheet, Full Rows)') }}</option>
                                <option value="master_detail">{{ __('Master-Detail (Two Sheets)') }}</option>
                            </select>
                            <p class="mt-4 text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">        
                                {{ __('Flat style is recommended for data integration with other systems.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Default Fields Management (Full Width) --}}
                <div class="space-y-6 border-t border-black/[0.03] dark:border-white/[0.03] pt-12">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white flex items-center gap-2">
                            <i class="bi bi-list-check text-indigo-500"></i>
                            {{ __('Default System Fields') }}
                        </h3>
                        <button type="button" @click="form.default_fields.push({key: '', label: '', type: 'text'})"
                                class="h-8 px-4 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 text-[9px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all">
                            <i class="bi bi-plus-lg"></i> {{ __('Add Default Field') }}
                        </button>
                    </div>

                    <div class="overflow-hidden border border-black/[0.05] dark:border-white/5 rounded-xl bg-white dark:bg-[#1e1f22]">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-black/[0.02] dark:bg-white/[0.02] text-[9px] font-black uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="px-6 py-4">{{ __('Technical Key') }}</th>
                                    <th class="px-6 py-4">{{ __('Display Label') }}</th>
                                    <th class="px-6 py-4 w-48">{{ __('Type') }}</th>
                                    <th class="px-6 py-4 w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                <template x-for="(field, index) in form.default_fields" :key="index">
                                    <tr class="group hover:bg-black/[0.01] dark:hover:bg-white/[0.01] transition-colors">
                                        <td class="px-6 py-4">
                                            <x-ui.input type="text" x-model="field.key" placeholder="e.g. shop_name" @input="field.key = field.key.toLowerCase().replace(/[^a-z0-9_]/g, '')"
                                                   class="w-full bg-transparent border-0 p-0 text-xs font-black text-rose-500 focus:ring-0" />
                                        </td>
                                        <td class="px-6 py-4">
                                            <x-ui.input type="text" x-model="field.label" placeholder="e.g. Store Name"
                                                   class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-900 dark:text-white focus:ring-0" />
                                        </td>
                                        <td class="px-6 py-4">
                                            <select x-model="field.type" class="w-full bg-slate-50 dark:bg-black/20 border-0 rounded-lg px-3 py-2 text-[10px] font-black uppercase text-slate-500 focus:ring-0 cursor-pointer">
                                                <option value="text">TEXT</option>
                                                <option value="number">NUMBER</option>
                                                <option value="date">DATE</option>
                                                <option value="array">ARRAY (Items)</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button type="button" @click="form.default_fields.splice(index, 1)" class="text-slate-300 hover:text-rose-500 transition-colors">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <template x-if="form.default_fields.length === 0">
                            <div class="p-10 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest italic"> 
                                {{ __('No default fields defined. New folders will start empty.') }}
                            </div>
                        </template>
                    </div>
                </div>
            {{-- Form Footer Actions --}}
            <div class="mt-12 pt-8 border-t border-black/[0.03] dark:border-white/[0.03] flex justify-end">
                <button type="button" @click="saveAllSettings()" :disabled="saving"
                        class="h-12 px-10 rounded-xl bg-discord-green text-white font-black text-[11px] uppercase tracking-[0.2em] shadow-lg shadow-green-500/20 hover:bg-[#1f8b4c] transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2"> 
                    <i class="bi bi-cloud-check-fill" x-show="!saving"></i>
                    <i class="bi bi-arrow-repeat animate-spin" x-show="saving" x-cloak></i>
                    <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save All Settings') }}'"></span>
                </button>
            </div>
            </div>
        </x-ui.card>
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
                    api_keys: {!! json_encode($settings['gemini_api_keys'] ?? []) !!},
                    excel_export_style: {!! json_encode($settings['excel_export_style'] ?? 'flat') !!},
                    default_fields: {!! json_encode($settings['default_fields'] ?? []) !!}
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

                        if (!response.ok) throw new Error('{{ __('Failed to update system configuration') }}');

                        Toast.fire({
                            icon: 'success',
                            title: '{{ __('Configuration Saved') }}',
                            text: '{{ __('AI settings updated successfully.') }}'
                        });
                    } catch (error) {
                        Toast.fire({ icon: 'error', title: '{{ __('Update Failed') }}', text: error.message });
                    } finally {
                        this.saving = false;
                    }
                }
            }));
        });
    </script>
    @endpush
@endsection