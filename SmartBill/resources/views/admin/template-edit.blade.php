<x-app-layout>
    <div x-data="templateEditor()" class="max-w-5xl mx-auto space-y-6 pb-20 px-4 sm:px-6 lg:px-8 mt-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.templates.index', [], false) }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
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
                        <i data-lucide="layout" class="w-4 h-4"></i> Builder
                    </button>
                    <button @click="switchMode('json')" :class="viewMode === 'json' ? 'bg-white dark:bg-slate-700 shadow-sm text-rose-600' : 'text-slate-500 dark:text-slate-400'"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <i data-lucide="code" class="w-4 h-4"></i> JSON
                    </button>
                </div>

                <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 mx-1"></div>

                <input type="file" id="sampleInp" accept="image/*" class="hidden" @change="suggestFromImage($event)">
                <button @click="document.getElementById('sampleInp').click()" :disabled="suggesting"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-medium rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors shadow-sm active:scale-95 disabled:opacity-50">
                    <i x-show="!suggesting" data-lucide="sparkles" class="w-4 h-4"></i>
                    <i x-show="suggesting" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    <span x-text="suggesting ? 'Analyzing...' : 'Auto-Headers'"></span>
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
                        <i data-lucide="plus" class="w-4 h-4"></i> Add Header
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
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </template>
                    <div x-show="fields.length === 0" class="text-center py-12 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
                        <i data-lucide="layout-template" class="w-8 h-8 text-slate-400 mx-auto mb-3"></i>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">No headers configured</p>
                        <p class="text-sm text-slate-500 mt-1">Click "Add Header" or use Auto-Headers to get started.</p>
                    </div>
                </div>
            </div>

            <!-- JSON Config Mode -->
            <div x-show="viewMode === 'json'" class="bg-slate-900 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-800/50">
                    <span class="text-sm font-bold text-white flex items-center gap-2"><i data-lucide="code" class="w-4 h-4 text-rose-500"></i> Raw JSON Schema</span>
                    <button @click="prettifyJson()" class="p-2 text-slate-400 hover:text-white transition-colors rounded-lg hover:bg-slate-700">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </button>
                </div>
                <textarea x-model="jsonContent" rows="20" class="w-full bg-transparent text-emerald-400 font-mono text-sm p-6 focus:ring-0 border-0 custom-scrollbar leading-relaxed"></textarea>
            </div>

            <!-- Save Action -->
            <div class="flex justify-end pt-4">
                <button @click="save()" :disabled="saving" class="inline-flex items-center gap-2 px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm active:scale-95 disabled:opacity-50">
                    <i x-show="!saving" data-lucide="save" class="w-5 h-5"></i>
                    <i x-show="saving" data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    <span x-text="saving ? 'Saving...' : 'Save Profile'"></span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        function templateEditor() {
            const initialFields = @json($promptFields).map(f => ({ key: f.key, label: f.label || f.key, type: f.type || 'text' }));
            return {
                viewMode: 'ui', jsonContent: JSON.stringify(initialFields, null, 4),
                form: { 
                    merchant_id: '{{ $merchant->merchant_id }}',
                    name: @json($merchant->name), 
                    main_instruction: @json($merchant->main_instruction) 
                },
                fields: initialFields, saving: false, suggesting: false,
                init() { lucide.createIcons(); },
                switchMode(mode) {
                    if (mode === 'json') { this.jsonContent = JSON.stringify(this.fields, null, 4); } else {
                        try { const parsed = JSON.parse(this.jsonContent); this.fields = parsed.map(f => ({ key: f.key || this.generateKey(f.label || ''), label: f.label || f.key || 'Header', type: f.type || 'text' })); }
                        catch (e) { Toast.fire({ icon: 'error', title: 'Invalid JSON' }); return; }
                    }
                    this.viewMode = mode; this.$nextTick(() => lucide.createIcons());
                },
                generateKey(label) { return label.toLowerCase().replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').replace(/^_+|_+$/g, ''); },
                syncKey(field) { field.key = this.generateKey(field.label); },
                addField() { this.fields.push({ key: '', label: '', type: 'text' }); this.$nextTick(() => lucide.createIcons()); },
                removeField(index) { this.fields.splice(index, 1); },
                prettifyJson() { try { this.jsonContent = JSON.stringify(JSON.parse(this.jsonContent), null, 4); } catch (e) { Toast.fire({ icon: 'error', title: 'Malformed JSON' }); } },
                async suggestFromImage(event) {
                    const file = event.target.files[0]; if (!file) return;
                    this.suggesting = true; const fd = new FormData(); fd.append('image', file); fd.append('_token', '{{ csrf_token() }}');
                    try {
                        const res = await fetch('{{ route('admin.templates.suggest', [], false) }}', { method: 'POST', body: fd });
                        const data = await res.json(); if (!res.ok) throw new Error(data.message || 'AI failed');
                        this.fields = data.ai_fields.map(f => ({ key: f.key, label: f.label, type: f.type }));
                        this.jsonContent = JSON.stringify(this.fields, null, 4); Toast.fire({ icon: 'success', title: 'Headers Detected' });
                    } catch (e) { Toast.fire({ icon: 'error', title: e.message }); } finally { this.suggesting = false; event.target.value = ''; this.$nextTick(() => lucide.createIcons()); }
                },
                async save() {
                    this.saving = true;
                    try {
                        let finalFields = this.viewMode === 'json' ? JSON.parse(this.jsonContent) : this.fields;
                        const fd = new FormData();
                        fd.append('merchant_id', this.form.merchant_id);
                        fd.append('name', this.form.name);
                        fd.append('main_instruction', this.form.main_instruction);
                        fd.append('ai_fields', JSON.stringify(finalFields));
                        fd.append('export_layout', JSON.stringify(finalFields.map(f => ({ key: f.key, label: f.label, enabled: true }))));
                        fd.append('_method', 'PATCH'); fd.append('_token', '{{ csrf_token() }}');
                        const res = await fetch('{{ route('admin.templates.update', $merchant->id, false) }}', { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } });
                        if (!res.ok) throw new Error('Persistence failed');
                        Toast.fire({ icon: 'success', title: 'Profile Updated' });
                        setTimeout(() => window.location.href = '{{ route('admin.templates.index', [], false) }}', 900);
                    } catch (e) { Toast.fire({ icon: 'error', title: e.message }); } finally { this.saving = false; }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
