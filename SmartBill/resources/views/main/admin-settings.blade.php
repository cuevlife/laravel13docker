@extends('layouts.app')

@section('content')
    <div class="w-full px-2 py-4 sm:px-4 lg:px-6" x-data="adminSettings()">
        <div class="rounded-[2.5rem] bg-white p-8 sm:p-12 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] border border-black/[0.04] dark:bg-[#2b2d31] dark:border-white/5 transition-all">
            {{-- Header --}}
            <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="flex h-14 w-14 items-center justify-center rounded-[1.5rem] bg-discord-green/10 text-discord-green shadow-sm">
                        <i class="bi bi-cpu-fill h-7 w-7"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tighter">{{ __('AI System Control') }}</h1>
                        <p class="text-sm font-bold text-[#80848e] uppercase tracking-widest mt-1">{{ __('Global Extraction Engine Configuration') }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 bg-[#f2f3f5] dark:bg-[#1e1f22] p-1 rounded-2xl">
                    <button @click="activeTab = 'ai'" :class="activeTab === 'ai' ? 'bg-white dark:bg-[#2b2d31] text-rose-500 shadow-sm' : 'text-[#80848e]'" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">AI Engine</button>
                    <button @click="activeTab = 'mapping'" :class="activeTab === 'mapping' ? 'bg-white dark:bg-[#2b2d31] text-rose-500 shadow-sm' : 'text-[#80848e]'" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Mappings</button>
                    <button @click="activeTab = 'export'" :class="activeTab === 'export' ? 'bg-white dark:bg-[#2b2d31] text-rose-500 shadow-sm' : 'text-[#80848e]'" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Export</button>
                </div>
            </div>

            <form @submit.prevent="saveAllSettings()" class="space-y-10">
                
                {{-- TAB: AI Engine --}}
                <div x-show="activeTab === 'ai'" class="grid grid-cols-1 lg:grid-cols-12 gap-12 animate-in fade-in duration-500">
                    <div class="lg:col-span-8 space-y-10">
                        {{-- Model Selection --}}
                        <div class="group relative">
                            <div class="absolute -left-4 top-0 bottom-0 w-1 bg-rose-500 rounded-full opacity-0 group-focus-within:opacity-100 transition-opacity"></div>
                            <label for="gemini_model" class="flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.2em] text-[#80848e] mb-4 group-focus-within:text-rose-500 transition-colors">
                                <i class="bi bi-robot"></i>
                                {{ __('Active AI Model') }}
                            </label>
                            <div class="flex items-center gap-3">
                                <div class="relative flex-1">
                                    <input type="text" id="gemini_model" name="gemini_model" x-model="geminiModel"
                                           class="w-full rounded-xl border border-black/5 bg-[#f8fafb] px-6 py-4 text-sm font-bold text-[#313338] dark:bg-[#1e1f22] dark:text-white outline-none shadow-inner focus:border-rose-500/30 transition-all"
                                           placeholder="e.g., gemini-1.5-pro">
                                </div>
                                <button type="button" @click="saveActiveModel()" :disabled="savingModel"
                                        class="h-[54px] px-6 rounded-xl bg-rose-500 text-white font-black text-[10px] uppercase tracking-widest shadow-lg shadow-rose-500/20 hover:bg-rose-600 transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2">
                                    <i class="bi bi-lightning-charge-fill" x-show="!savingModel"></i>
                                    <i class="bi bi-arrow-repeat animate-spin" x-show="savingModel" x-cloak></i>
                                    <span x-text="savingModel ? '{{ __('Activating...') }}' : '{{ __('Active Model') }}'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Global Prompt Section --}}
                        <div class="group relative">
                            <div class="absolute -left-4 top-0 bottom-0 w-1 bg-discord-green rounded-full opacity-0 group-focus-within:opacity-100 transition-opacity"></div>
                            <label for="global_prompt" class="flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.2em] text-[#80848e] mb-4 group-focus-within:text-discord-green transition-colors">
                                <i class="bi bi-chat-left-text-fill"></i>
                                {{ __('Master Extraction Instruction') }}
                            </label>
                            <div class="relative">
                                <textarea id="global_prompt" name="global_prompt" rows="5" x-model="globalPrompt"
                                          class="w-full rounded-[1.5rem] border border-black/5 bg-[#f8fafb] p-6 text-sm font-bold text-[#313338] outline-none shadow-inner focus:border-discord-green/30 focus:bg-white dark:bg-[#1e1f22] dark:text-white dark:focus:bg-[#1e1f22] transition-all leading-relaxed"
                                          placeholder="Enter detailed AI instructions here..."></textarea>
                            </div>
                        </div>

                        {{-- AI Fields JSON Section --}}
                        <div class="group relative">
                            <div class="absolute -left-4 top-0 bottom-0 w-1 bg-discord-green rounded-full opacity-0 group-focus-within:opacity-100 transition-opacity"></div>
                            <div class="flex items-center justify-between mb-4">
                                <label for="global_ai_fields" class="flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.2em] text-[#80848e] group-focus-within:text-discord-green transition-colors">
                                    <i class="bi bi-braces"></i>
                                    {{ __('Schema Definition (JSON)') }}
                                </label>
                            </div>
                            <div class="relative">
                                <textarea id="global_ai_fields" name="global_ai_fields" rows="15" x-model="globalAiFields"
                                          class="w-full font-mono rounded-[1.5rem] border border-black/5 bg-[#1e1f22] p-8 text-xs text-emerald-400 outline-none shadow-2xl focus:border-discord-green/30 transition-all leading-relaxed custom-scrollbar"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div class="sticky top-8 space-y-6">
                            <div class="rounded-[2rem] bg-slate-50 dark:bg-black/20 p-8 border border-black/[0.02] dark:border-white/5 shadow-sm">
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-discord-green text-white shadow-lg shadow-green-500/20">
                                        <i class="bi bi-magic"></i>
                                    </div>
                                    <h3 class="text-sm font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">{{ __('Auto-Generator') }}</h3>
                                </div>
                                <p class="text-xs font-bold text-[#80848e] leading-relaxed uppercase tracking-wider mb-8">
                                    {{ __('Upload a sample receipt (e.g., examslip.jpg) and let Gemini automatically build the perfect schema and prompt for you.') }}
                                </p>
                                <input type="file" id="sample-slip" class="hidden" @change="handleSampleUpload" accept="image/*">
                                <button type="button" @click="document.getElementById('sample-slip').click()" :disabled="analyzing"
                                        class="w-full flex items-center justify-center gap-3 py-4 rounded-2xl bg-white dark:bg-[#1e1f22] border border-black/5 dark:border-white/10 text-discord-green font-black text-[10px] uppercase tracking-widest shadow-sm hover:shadow-md transition-all active:scale-95 disabled:opacity-50">
                                    <i class="bi bi-image-fill" x-show="!analyzing"></i>
                                    <i class="bi bi-arrow-repeat animate-spin" x-show="analyzing" x-cloak></i>
                                    <span x-text="analyzing ? '{{ __('Gemini is Thinking...') }}' : '{{ __('Analyze Sample Receipt') }}'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: Mappings --}}
                <div x-show="activeTab === 'mapping'" class="space-y-12 animate-in fade-in duration-500" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Vendor Mappings --}}
                        <div class="bg-[#f8fafb] dark:bg-black/10 p-8 rounded-[2rem] border border-black/[0.02] dark:border-white/5">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white flex items-center gap-2">
                                    <i class="bi bi-shop text-rose-500"></i>
                                    Vendor Code Mappings
                                </h3>
                                <button type="button" @click="vendorMappings.push({text:'', code:''})" class="text-[10px] font-black text-rose-500 uppercase tracking-widest">+ Add Row</button>
                            </div>
                            <div class="space-y-3">
                                <template x-for="(map, index) in vendorMappings" :key="index">
                                    <div class="flex items-center gap-3 bg-white dark:bg-[#1e1f22] p-3 rounded-xl shadow-sm border border-black/5">
                                        <input type="text" x-model="map.text" placeholder="Name in Slip" class="flex-1 bg-transparent border-0 text-xs font-bold focus:ring-0 text-[#313338] dark:text-white">
                                        <i class="bi bi-arrow-right text-[#80848e]"></i>
                                        <input type="text" x-model="map.code" placeholder="System Code" class="w-24 bg-[#f8fafb] dark:bg-black/20 rounded-lg px-3 py-1.5 text-xs font-black text-rose-500 border-0 focus:ring-0">
                                        <button type="button" @click="vendorMappings.splice(index, 1)" class="text-[#e3e5e8] hover:text-rose-500 transition-colors"><i class="bi bi-x-circle-fill"></i></button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Item Mappings --}}
                        <div class="bg-[#f8fafb] dark:bg-black/10 p-8 rounded-[2rem] border border-black/[0.02] dark:border-white/5">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white flex items-center gap-2">
                                    <i class="bi bi-box-seam text-discord-green"></i>
                                    Item Code Mappings
                                </h3>
                                <button type="button" @click="itemMappings.push({text:'', code:''})" class="text-[10px] font-black text-discord-green uppercase tracking-widest">+ Add Row</button>
                            </div>
                            <div class="space-y-3">
                                <template x-for="(map, index) in itemMappings" :key="index">
                                    <div class="flex items-center gap-3 bg-white dark:bg-[#1e1f22] p-3 rounded-xl shadow-sm border border-black/5">
                                        <input type="text" x-model="map.text" placeholder="Item Name in Slip" class="flex-1 bg-transparent border-0 text-xs font-bold focus:ring-0 text-[#313338] dark:text-white">
                                        <i class="bi bi-arrow-right text-[#80848e]"></i>
                                        <input type="text" x-model="map.code" placeholder="SKU Code" class="w-24 bg-[#f8fafb] dark:bg-black/20 rounded-lg px-3 py-1.5 text-xs font-black text-discord-green border-0 focus:ring-0">
                                        <button type="button" @click="itemMappings.splice(index, 1)" class="text-[#e3e5e8] hover:text-discord-green transition-colors"><i class="bi bi-x-circle-fill"></i></button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: Export --}}
                <div x-show="activeTab === 'export'" class="space-y-10 animate-in fade-in duration-500" x-cloak>
                    <div class="bg-[#f8fafb] dark:bg-black/10 p-8 rounded-[2rem] border border-black/[0.02] dark:border-white/5">
                        <div class="mb-8">
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white mb-2">Excel Export Designer</h3>
                            <p class="text-[10px] font-bold text-[#80848e] uppercase tracking-widest">Define the columns, headers, and order for generated workbooks.</p>
                        </div>

                        <div class="overflow-hidden border border-black/5 dark:border-white/5 rounded-2xl bg-white dark:bg-[#1e1f22]">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-black/5 dark:bg-white/5 text-[10px] font-black uppercase tracking-widest text-[#80848e]">
                                    <tr>
                                        <th class="px-6 py-4 w-20 text-center">Export</th>
                                        <th class="px-6 py-4">AI Source Field</th>
                                        <th class="px-6 py-4">Excel Header Label</th>
                                        <th class="px-6 py-4 w-24 text-center">Order</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                    <template x-for="(col, index) in exportColumns" :key="index">
                                        <tr class="group hover:bg-black/[0.01] dark:hover:bg-white/[0.01]">
                                            <td class="px-6 py-4 text-center">
                                                <input type="checkbox" x-model="col.enabled" class="h-4 w-4 rounded border-black/10 text-rose-500 focus:ring-0">
                                            </td>
                                            <td class="px-6 py-4 text-xs font-bold text-[#80848e]" x-text="col.key"></td>
                                            <td class="px-6 py-4">
                                                <input type="text" x-model="col.label" class="w-full bg-transparent border-0 p-0 text-xs font-black text-[#1e1f22] dark:text-white focus:ring-0">
                                            </td>
                                            <td class="px-6 py-4">
                                                <input type="number" x-model="col.order" class="w-full bg-black/5 dark:bg-white/5 rounded-lg px-2 py-1 text-center text-xs font-black border-0 focus:ring-0">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-[#80848e] mb-3">Default Export Filename</label>
                                <input type="text" x-model="excelFilename" class="w-full rounded-xl border border-black/5 bg-white px-6 py-4 text-sm font-bold text-[#313338] dark:bg-[#1e1f22] dark:text-white outline-none shadow-inner focus:border-discord-green/30 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-8 border-t border-black/[0.04] dark:border-white/[0.04] flex items-center justify-between">
                    <div class="hidden sm:flex items-center gap-3">
                        <i class="bi bi-check-all text-discord-green text-xl"></i>
                        <p class="text-[9px] font-black text-[#80848e] uppercase tracking-[0.2em]">Changes apply instantly to new scans</p>
                    </div>
                    
                    <button type="submit" :disabled="savingAll"
                            class="w-full sm:w-auto inline-flex h-14 items-center justify-center gap-3 rounded-full bg-discord-green px-10 text-[12px] font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-green-500/20 transition hover:bg-[#1f8b4c] hover:scale-[1.02] active:scale-95 disabled:opacity-50">
                        <i class="bi bi-cloud-arrow-up-fill text-lg" x-show="!savingAll"></i>
                        <i class="bi bi-arrow-repeat animate-spin text-lg" x-show="savingAll" x-cloak></i>
                        <span x-text="savingAll ? 'Saving Everything...' : '{{ __('Commit AI Configuration') }}'"></span>
                    </button>
                </div>
            </form>
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
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#1e1f22',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Alpine.data('adminSettings', () => ({
                activeTab: 'ai',
                globalPrompt: {!! json_encode($settings['global_prompt'] ?? '') !!},
                globalAiFields: {!! json_encode(is_array($settings['global_ai_fields'] ?? []) ? json_encode($settings['global_ai_fields'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($settings['global_ai_fields'] ?? '')) !!},
                geminiModel: {!! json_encode($settings['gemini_model'] ?? config('services.gemini.model', 'gemini-1.5-flash')) !!},
                vendorMappings: {!! json_encode($settings['vendor_mapping'] ?? []) !!},
                itemMappings: {!! json_encode($settings['item_mapping'] ?? []) !!},
                exportColumns: {!! json_encode($settings['export_columns'] ?? []) !!},
                excelFilename: {!! json_encode($settings['excel_filename'] ?? 'SmartBill_Export.xlsx') !!},
                
                analyzing: false,
                savingModel: false,
                savingAll: false,

                async saveActiveModel() {
                    if (!this.geminiModel) return;
                    this.savingModel = true;
                    await this.performUpdate();
                    this.savingModel = false;
                },

                async saveAllSettings() {
                    this.savingAll = true;
                    await this.performUpdate();
                    this.savingAll = false;
                },

                async performUpdate() {
                    try {
                        const response = await fetch('{{ route('admin.settings.update') }}', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                gemini_model: this.geminiModel,
                                global_prompt: this.globalPrompt,
                                global_ai_fields: this.globalAiFields,
                                vendor_mapping: this.vendorMappings,
                                item_mapping: this.itemMappings,
                                export_columns: this.exportColumns,
                                excel_filename: this.excelFilename
                            })
                        });

                        if (!response.ok) throw new Error('Failed to update settings');

                        Toast.fire({
                            icon: 'success',
                            title: 'Settings Saved',
                            text: 'Global configuration updated successfully.'
                        });
                    } catch (error) {
                        Toast.fire({ icon: 'error', title: 'Update Failed', text: error.message });
                    }
                },

                async handleSampleUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.analyzing = true;
                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const response = await fetch('{{ route('admin.settings.suggest') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'AI analysis failed');
                        }
                        
                        if (data.status === 'success') {
                            // Update UI fields
                            this.globalAiFields = JSON.stringify(data.ai_fields, null, 4);
                            
                            // Generate an improved prompt based on keys found
                            const keys = data.ai_fields.map(f => f.label).join(', ');
                            this.globalPrompt = `### ROLE: EXPERT RECEIPT ANALYST\n### TASK: EXTRACT ALL DATA POINTS FROM THIS IMAGE WITH 100% ACCURACY\n\nI need you to identify and extract the following fields: ${keys}.\n\n### EXTRACTION RULES:\n1. Output MUST be valid JSON.\n2. List all individual line items in an "items" array with 'name' and 'price'.\n3. Format dates as YYYY-MM-DD.\n4. Clean all numerical values (remove currency symbols and commas).\n5. If a field is not found, use null.\n\n### TARGET FIELDS GUIDANCE:\n${data.ai_fields.map(f => `- ${f.key}: ${f.label}`).join('\n')}`;

                            // Also refresh export columns
                            this.exportColumns = [
                                {key: 'processed_at', label: 'Scan Date', enabled: true, order: 1},
                                ...data.ai_fields.map((f, i) => ({
                                    key: f.key,
                                    label: f.label,
                                    enabled: true,
                                    order: i + 2
                                }))
                            ];

                            Toast.fire({
                                icon: 'success',
                                title: '{{ __('AI Analysis Complete') }}',
                                text: 'Generated new schema and prompt.'
                            });
                        }
                    } catch (error) {
                        Toast.fire({ icon: 'error', title: 'Analysis Error', text: error.message });
                    } finally {
                        this.analyzing = false;
                        event.target.value = ''; // Reset input
                    }
                }
            }));
        });
    </script>
    @endpush

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            border: 2px solid rgba(0, 0, 0, 0.2);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
@endsection
