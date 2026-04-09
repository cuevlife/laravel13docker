@extends('layouts.app')

@section('content')
    <div x-data="slipEditor(window.initialSlipConfig)" class="w-full space-y-6 pb-20 px-4 sm:px-6 lg:px-8 mt-8 animate-in fade-in duration-500">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'slips') }}" class="p-2 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-rose-50 transition-all">
                    <i class="bi bi-arrow-left w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Intelligence Editor</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Refining Document #{{ $slip->id }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="flex bg-slate-100 rounded-xl p-1 border border-slate-200">
                    <button @click="switchMode('ui')" :class="viewMode === 'ui' ? 'bg-white text-rose-500 shadow-sm' : 'text-slate-400'"
                            class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">UI</button>
                    <button @click="switchMode('json')" :class="viewMode === 'json' ? 'bg-white text-rose-500 shadow-sm' : 'text-slate-400'"
                            class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">JSON</button>
                </div>

                <button @click="showImage = true; modalActive = true" class="p-2.5 bg-white border border-slate-200 rounded-xl shadow-sm text-slate-400 hover:text-rose-500 transition-all">
                    <i class="bi bi-arrows-fullscreen w-5 h-5"></i>
                </button>
            </div>
        </div>

        <!-- UI Editor -->
        <div x-show="viewMode === 'ui'" class="space-y-8 animate-in slide-in-from-bottom-4 duration-500">
            <!-- Metadata Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <template x-for="column in columns" :key="column.key">
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl hover:border-rose-500/20 transition-all group">
                        <label class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 block group-hover:text-rose-500 transition-colors" x-text="column.label"></label>
                        <input :id="'field-'+column.key" type="text" x-model="fields[column.key]"
                               class="w-full bg-slate-50 border-0 rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-rose-500/10 transition-all"
                               placeholder="...">
                    </div>
                </template>
            </div>

            <!-- Items Section -->
            <div x-show="showItems" class="space-y-4">
                <!-- Math Validator Warning -->
                <div x-show="mathMismatch" x-cloak class="px-5 py-3 mb-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-3 shadow-sm animate-in fade-in slide-in-from-top-2">
                    <i class="bi bi-exclamation-triangle w-5 h-5 text-rose-500"></i>
                    <p class="text-xs font-black uppercase tracking-wide text-rose-600" x-text="mathMismatch"></p>
                </div>

                <div class="flex items-center justify-between px-4">
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">Transaction Items</h3>
                    <button @click="addItem()" class="text-[10px] font-black uppercase tracking-widest text-rose-500 hover:scale-105 transition-transform">+ New Entry</button>
                </div>
                <div class="grid grid-cols-1 gap-3">
                    <template x-for="(item, index) in items" :key="item.uid">
                        <div class="bg-white p-4 pl-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-6 group hover:shadow-md transition-all" :class="{'border-rose-300 bg-rose-50/50': mathMismatch}">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-[10px] font-black transition-colors" :class="mathMismatch ? 'bg-rose-100 text-rose-500' : 'bg-slate-50 text-slate-300 group-hover:text-rose-500'" x-text="index + 1"></div>
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" x-model="item.name" :id="'item-name-' + index" @keydown.enter.prevent="document.getElementById('item-price-' + index)?.focus()" class="bg-transparent border-0 p-0 text-sm font-bold focus:ring-0 transition-colors" :class="mathMismatch ? 'text-rose-900' : 'text-slate-700'" placeholder="Description...">
                                <div class="flex items-center justify-end gap-2">
                                    <span class="text-[10px] font-black text-rose-500">฿</span>
                                    <input type="text" x-model="item.price" :id="'item-price-' + index" @keydown.enter.prevent="handleEnterPress(index)" class="w-28 bg-transparent border-0 p-0 text-base font-black text-emerald-500 text-right focus:ring-0" placeholder="0.00">
                                </div>
                            </div>
                            <button @click="removeItem(index)" class="p-2 text-slate-200 hover:text-rose-500 transition-colors">
                                <i class="bi bi-trash-fill w-4 h-4"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- JSON Editor -->
        <div x-show="viewMode === 'json'" class="animate-in zoom-in-95 duration-500">
            <div class="bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-2xl border border-slate-800">
                <div class="px-8 py-4 bg-slate-800/50 border-b border-slate-800 flex justify-between items-center">
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500 flex items-center gap-2">
                        <i class="bi bi-code w-4 h-4"></i> Source Config
                    </span>
                    <button @click="prettifyJson()" class="p-2 text-white/20 hover:text-white transition-all"><i class="bi bi-stars w-4 h-4"></i></button>
                </div>
                <textarea x-model="jsonContent" rows="22" class="w-full bg-transparent text-emerald-400 font-mono text-xs p-10 focus:ring-0 border-0 leading-relaxed"></textarea>
            </div>
        </div>

        <!-- Save Hub -->
        <div class="pt-10">
            <button @click="save()" :disabled="saving"
                    class="w-full py-6 bg-slate-900 text-white rounded-[2rem] font-black text-[11px] uppercase tracking-[0.6em] shadow-xl hover:bg-rose-500 hover:scale-[1.01] active:scale-95 transition-all disabled:opacity-20 flex items-center justify-center gap-4">
                <i x-show="!saving" class="bi bi-shield-check w-5 h-5"></i>
                <i x-show="saving" class="bi bi-arrow-repeat w-5 h-5 animate-spin"></i>
                <span x-text="saving ? 'Committing...' : 'Save & Sync Registry'"></span>
            </button>
        </div>

        <!-- Image Modal -->
        <template x-teleport="body">
            <div x-show="showImage" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-xl" x-cloak>
                <button @click="showImage = false; modalActive = false" class="absolute top-8 right-8 p-4 bg-white/10 hover:bg-rose-500 rounded-2xl text-white transition-all">
                    <i class="bi bi-x-lg w-6 h-6"></i>
                </button>
                <div class="max-w-full max-h-full overflow-hidden rounded-xl shadow-2xl border border-white/10">
                    <img src="{{ asset('storage/' . $slip->image_path) }}" class="max-w-full max-h-[85vh] object-contain" alt="slip">
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
