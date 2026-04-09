@extends('layouts.app')

@section('content')
    <div x-data="templateEditor(window.initialTemplateConfig)" class="w-full space-y-6 pb-20 px-4 sm:px-6 lg:px-8 mt-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'templates') }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="bi bi-arrow-left w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Profile: {{ $merchant->name }}</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Configure extraction rules and AI prompt logic.</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- View Switcher Toggle -->
                <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
                    <button @click="switchMode('ui')" :class="viewMode === 'ui' ? 'bg-white dark:bg-slate-700 shadow-sm text-rose-600' : 'text-slate-500 dark:text-slate-400'"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="bi bi-layout-sidebar w-4 h-4"></i> Builder
                    </button>
                    <button @click="switchMode('json')" :class="viewMode === 'json' ? 'bg-white dark:bg-slate-700 shadow-sm text-rose-600' : 'text-slate-500 dark:text-slate-400'"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="bi bi-code w-4 h-4"></i> JSON
                    </button>
                </div>

                <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 mx-1"></div>

                <input type="file" id="sampleInp" accept="image/*" class="hidden" @change="suggestFromImage($event)">
                <button @click="document.getElementById('sampleInp').click()" :disabled="suggesting"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-medium rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors shadow-sm active:scale-95 disabled:opacity-50">
                    <i x-show="!suggesting" class="bi bi-stars w-4 h-4"></i>
                    <i x-show="suggesting" class="bi bi-arrow-repeat w-4 h-4 animate-spin"></i>
                    <span x-text="suggesting ? 'Analyzing Slip...' : 'Auto-Scan Slip (Generate Template)'"></span>
                </button>
            </div>
        </div>

        <div class="space-y-6 mt-6">
            <!-- Profile Settings -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">General Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Assigned Store</label>
                        <select x-model="form.merchant_id" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500 transition-colors">
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Profile Name</label>
                        <input type="text" x-model="form.name" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">AI Logic Prompt (Optional)</label>
                        <textarea x-model="form.main_instruction" rows="1" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500 transition-colors" placeholder="e.g. Extract amounts as numbers..."></textarea>
                    </div>
                </div>
            </div>

            <!-- UI Builder Mode -->
            <div x-show="viewMode === 'ui'" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Extraction Headers</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Define the fields you want the AI to extract.</p>
                    </div>
                    <button @click="addField()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-colors">
                        <i class="bi bi-plus-lg w-4 h-4"></i> Add Header
                    </button>
                </div>

                <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                    <template x-for="(field, index) in fields" :key="index">
                        <div class="flex items-start gap-4 p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 hover:border-rose-500/50 transition-colors group">
                            <div class="mt-2 text-sm font-bold text-slate-400 dark:text-slate-500 w-6 text-center" x-text="index + 1"></div>
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Display Label (Excel)</label>
                                    <input type="text" x-model="field.label" @input="syncKey(field)" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500 transition-colors" placeholder="e.g. Total Amount">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">AI Technical Key</label>
                                    <input type="text" x-model="field.key" readonly class="w-full bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-500 font-mono focus:outline-none cursor-not-allowed">
                                </div>
                            </div>
                            <button @click="removeField(index)" class="mt-6 p-2 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors" title="Remove Header">
                                <i class="bi bi-trash-fill w-4 h-4"></i>
                            </button>
                        </div>
                    </template>
                    <div x-show="fields.length === 0" class="text-center py-12 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
                        <i class="bi bi-layout-text-window w-8 h-8 text-slate-400 mx-auto mb-3"></i>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">No headers configured</p>
                        <p class="text-sm text-slate-500 mt-1">Click "Add Header" or use Auto-Headers to get started.</p>
                    </div>
                </div>
            </div>

            <!-- JSON Config Mode -->
            <div x-show="viewMode === 'json'" class="bg-slate-900 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-800/50">
                    <span class="text-sm font-bold text-white flex items-center gap-2"><i class="bi bi-code w-4 h-4 text-rose-500"></i> Raw JSON Schema</span>
                    <button @click="prettifyJson()" class="p-2 text-slate-400 hover:text-white transition-colors rounded-lg hover:bg-slate-700">
                        <i class="bi bi-stars w-4 h-4"></i>
                    </button>
                </div>
                <textarea x-model="jsonContent" rows="20" class="w-full bg-transparent text-emerald-400 font-mono text-sm p-6 focus:ring-0 border-0 custom-scrollbar leading-relaxed"></textarea>
            </div>

            <!-- Save Action -->
            <div class="flex justify-end pt-4">
                <button @click="save()" :disabled="saving" class="inline-flex items-center gap-2 px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm active:scale-95 disabled:opacity-50">
                    <i x-show="!saving" class="bi bi-floppy-fill w-5 h-5"></i>
                    <i x-show="saving" class="bi bi-arrow-repeat w-5 h-5 animate-spin"></i>
                    <span x-text="saving ? 'Saving...' : 'Save Profile'"></span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.initialTemplateConfig = {
            promptFields: @json($promptFields),
            merchantId: '{{ $merchant->merchant_id }}',
            merchantName: @json($merchant->name),
            mainInstruction: @json($merchant->main_instruction),
            csrfToken: '{{ csrf_token() }}',
            suggestRoute: @json(\App\Support\WorkspaceUrl::current(request(), 'templates/suggest')),
            updateRoute: @json(\App\Support\WorkspaceUrl::current(request(), 'templates/update/' . $merchant->id)),
            indexRoute: @json(\App\Support\WorkspaceUrl::current(request(), 'templates'))
        };
    </script>
    <script src="{{ asset('js/admin/template-edit.js') }}"></script>
    @endpush
@endsection
