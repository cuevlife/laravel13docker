@extends('layouts.app')

@section('content')
    <div x-data="slipEditor(window.initialSlipConfig)" class="w-full px-2 py-4 sm:px-4 lg:px-6 animate-in fade-in duration-500">
        
        <!-- Main Unified Editor Card (Registry Style) -->
        <div class="rounded-xl bg-white p-6 sm:p-8 shadow-sm border border-black/[0.04] dark:bg-[#2b2d31] dark:border-white/5 transition-all">
            
            <!-- Header Section (Internal) -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-black/[0.04] dark:border-white/[0.04] pb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'slips') }}" class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-500/5 text-[#80848e] hover:text-rose-500 border border-black/5 dark:border-white/5 transition-all">
                        <i class="bi bi-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">Intelligence Editor</h2>
                            <span class="px-2 py-0.5 rounded-lg bg-discord-green/10 text-discord-green text-[9px] font-black uppercase tracking-widest">#{{ $slip->id }}</span>
                        </div>
                        <p class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest mt-1" x-text="'Unique ID: ' + originalData.uid"></p>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Mode Switcher -->
                    <div class="flex bg-[#f2f3f5] dark:bg-[#1e1f22] rounded-xl p-1">
                        <button @click="switchMode('ui')" :class="viewMode === 'ui' ? 'bg-white text-discord-green shadow-sm dark:bg-[#2b2d31]' : 'text-[#80848e]'"
                                class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">UI</button>
                        <button @click="switchMode('json')" :class="viewMode === 'json' ? 'bg-white text-discord-green shadow-sm dark:bg-[#2b2d31]' : 'text-[#80848e]'"
                                class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">JSON</button>
                    </div>

                    <button @click="showImage = true; modalActive = true" class="h-10 px-4 bg-white dark:bg-[#1e1f22] border border-black/5 dark:border-white/5 rounded-xl shadow-sm text-[#80848e] hover:text-discord-green transition-all" title="View Document">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </button>

                    <button @click="save()" :disabled="saving"
                            class="h-10 px-6 bg-discord-green text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-500/20 hover:bg-[#1f8b4c] active:scale-95 transition-all disabled:opacity-50 flex items-center gap-2">
                        <i x-show="!saving" class="bi bi-shield-check text-sm"></i>
                        <i x-show="saving" class="bi bi-arrow-repeat text-sm animate-spin"></i>
                        <span x-text="saving ? 'Saving...' : 'Save & Sync'"></span>
                    </button>
                </div>
            </div>

            <!-- UI Editor Content -->
            <div x-show="viewMode === 'ui'" class="space-y-10 animate-in fade-in duration-500">
                <!-- Field Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="column in columns" :key="column.key">
                        <div class="space-y-2">
                            <label :for="'field-'+column.key" class="text-[10px] font-black uppercase tracking-widest text-[#80848e] ml-1" x-text="column.label"></label>
                            <input :id="'field-'+column.key" type="text" x-model="fields[column.key]"
                                   class="w-full bg-[#f8fafb] dark:bg-[#1e1f22] border border-black/5 dark:border-white/5 rounded-xl px-4 py-3 text-sm font-bold text-[#313338] dark:text-white outline-none focus:border-discord-green/30 transition-all shadow-inner">
                        </div>
                    </template>
                </div>

                <!-- Items Section -->
                <div x-show="showItems" class="space-y-6 pt-6 border-t border-black/[0.04] dark:border-white/[0.04]">
                    <!-- Math Validator Warning -->
                    <div x-show="mathMismatch" x-cloak class="px-5 py-3 bg-rose-50 border border-rose-200 dark:bg-rose-500/5 dark:border-rose-500/20 rounded-xl flex items-center gap-3 shadow-sm">
                        <i class="bi bi-exclamation-triangle-fill text-rose-500 text-lg"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest text-rose-600 dark:text-rose-400" x-text="mathMismatch"></p>
                    </div>

                    <div class="flex items-center justify-between">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-[#80848e] flex items-center gap-2">
                            <i class="bi bi-list-stars text-sm"></i> Transaction Items
                        </h3>
                        <button @click="addItem()" class="h-8 px-4 rounded-xl bg-discord-green/10 text-discord-green text-[9px] font-black uppercase tracking-widest hover:bg-discord-green hover:text-white transition-all">+ Add Item</button>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        <template x-for="(item, index) in items" :key="item.uid">
                            <div class="bg-[#f8fafb] dark:bg-[#1e1f22] p-4 rounded-xl border border-black/5 dark:border-white/5 flex items-center gap-4 group transition-all" :class="{'border-rose-300 dark:border-rose-500/20 bg-rose-50/30': mathMismatch}">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black bg-white dark:bg-[#2b2d31] text-[#80848e] shadow-sm" x-text="index + 1"></div>
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="text" x-model="item.name" :id="'item-name-' + index" class="w-full bg-transparent border-0 p-0 text-sm font-bold focus:ring-0 text-[#313338] dark:text-white" placeholder="Item name...">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-[10px] font-black text-[#80848e]">฿</span>
                                        <input type="text" x-model="item.price" :id="'item-price-' + index" class="w-28 bg-transparent border-0 p-0 text-sm font-black text-discord-green text-right focus:ring-0" placeholder="0.00">
                                    </div>
                                </div>
                                <button @click="removeItem(index)" class="text-[#e3e5e8] hover:text-rose-500 transition-colors">
                                    <i class="bi bi-trash-fill text-sm"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- JSON Editor Content -->
            <div x-show="viewMode === 'json'" class="animate-in fade-in duration-500 pt-4">
                <div class="bg-[#1e1f22] rounded-xl overflow-hidden border border-black/10 shadow-inner">
                    <div class="px-6 py-3 bg-black/20 border-b border-white/5 flex justify-between items-center">
                        <span class="text-[9px] font-black uppercase tracking-widest text-discord-green flex items-center gap-2">
                            <i class="bi bi-code-slash"></i> Raw AI Extraction
                        </span>
                        <button @click="prettifyJson()" class="h-7 px-3 rounded-lg bg-white/5 text-[9px] font-black text-white/40 hover:text-white hover:bg-white/10 transition-all uppercase tracking-widest">Prettify</button>
                    </div>
                    <textarea x-model="jsonContent" rows="20" class="w-full bg-transparent text-emerald-400 font-mono text-xs p-8 focus:ring-0 border-0 leading-relaxed custom-scrollbar"></textarea>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <template x-teleport="body">
            <div x-show="showImage" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/80 backdrop-blur-sm" x-transition.opacity x-cloak>
                <button @click="showImage = false; modalActive = false" class="absolute top-6 right-6 h-11 w-11 flex items-center justify-center bg-white/10 hover:bg-rose-500 rounded-xl text-white transition-all shadow-xl z-10">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
                <div class="max-w-full max-h-full overflow-hidden rounded-xl shadow-2xl border border-white/10 bg-[#1e1f22]" @click.stop>
                    <img src="{{ asset('storage/' . $slip->image_path) }}" class="max-w-full max-h-[90vh] object-contain block" alt="slip document">
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
        window.initialSlipConfig = {
            originalData: @json($slip->extracted_data),
            columns: @json($exportColumns),
            updateRoute: @json(\App\Support\WorkspaceUrl::current(request(), 'slips/update/' . $slip->id)),
            indexRoute: @json(\App\Support\WorkspaceUrl::current(request(), 'slips')),
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    <script src="{{ asset('js/admin/slip-edit.js') }}"></script>
    @endpush
@endsection
