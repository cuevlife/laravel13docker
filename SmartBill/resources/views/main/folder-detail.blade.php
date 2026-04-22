@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8 animate-in fade-in duration-500" x-data="folderEditor()">
        <x-ui.card>
            {{-- Header Section --}}
            <div class="px-8 py-8 border-b border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/10">
                <x-ui.page-header :title="__('Folder Settings')" :subtitle="$merchant->name">
                    <x-slot:icon_slot>
                        <x-ui.back-button :href="route('admin.users.show', ['user' => $merchant->user_id])" :title="__('Back to Account')" />
                    </x-slot:icon_slot>
                    <x-slot:actions>
                        <x-ui.badge :variant="$merchant->status === 'active' ? 'success' : 'warning'">
                            {{ strtoupper(__($merchant->status)) }}
                        </x-ui.badge>
                    </x-slot:actions>
                </x-ui.page-header>
            </div>

            <div class="p-8 md:p-10 space-y-12 divide-y divide-black/[0.03] dark:divide-white/[0.03]">
                <!-- 1. Folder Identity -->
                <div class="pt-0 space-y-6">
                    <div class="flex items-center gap-3"><div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div><h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Identity Configuration') }}</h2></div>
                    <div class="p-6 rounded-xl border border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                            <div class="space-y-1.5 flex-1">
                                <label class="block text-[9px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Display Name') }}</label>
                                <x-ui.input type="text" x-model="form.name" required />
                            </div>
                            <div class="space-y-1.5 w-full md:w-48">
                                <label class="block text-[9px] font-black uppercase text-rose-500 ml-1 tracking-widest">{{ __('Slip Capacity') }}</label>
                                <x-ui.input type="number" x-model="form.max_slips" required min="1" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Recent Slips Table -->
                <div class="pt-10 space-y-6">
                    <div class="flex items-center gap-3"><div class="w-1.5 h-4 bg-discord-green rounded-full"></div><h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Recent Activity') }}</h2></div>
                    <x-ui.table :headers="['Document', 'Shop', 'Amount', 'Date', '']">
                        @foreach($recentSlips as $slip)
                            <tr class="group hover:bg-black/[0.01] dark:hover:bg-white/[0.01]" id="slip-row-{{ $slip->id }}">
                                <td class="px-6 py-4 font-black text-indigo-500 text-xs">{{ $slip->uid }}</td>
                                <td class="px-6 py-4 text-xs dark:text-white">{{ $slip->extracted_data['shop_name'] ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs font-black dark:text-white">{{ number_format($slip->amount ?? 0, 2) }}</td>
                                <td class="px-6 py-4 text-[10px] text-slate-400">{{ $slip->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" @click="deleteSlip({{ $slip->id }})" class="text-slate-300 hover:text-rose-500 transition-colors">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        @if($recentSlips->isEmpty())
                            <tr><td colspan="5" class="py-12 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest italic">{{ __('No recent slips found') }}</td></tr>
                        @endif
                    </x-ui.table>
                </div>

                <!-- 3. Data Schema -->
                <div class="pt-10 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3"><div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div><h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Data Fields') }}</h2></div>
                        <div class="flex items-center gap-2">
                            <input type="file" id="sample-slip" class="hidden" @change="handleSampleUpload" accept="image/*">     
                            <x-ui.button variant="ghost" size="sm" icon="bi-magic" @click="document.getElementById('sample-slip').click()" ::disabled="analyzing"><span x-text="analyzing ? '{{ __('Analyzing...') }}' : '{{ __('Auto-Suggest') }}'"></span></x-ui.button>
                            <x-ui.button variant="success" size="sm" icon="bi-plus-lg" @click="addField()">{{ __('Add Field') }}</x-ui.button>
                        </div>
                    </div>
                    <x-ui.table :headers="['Key', 'Label', 'Type', 'Instruction', '']">
                        <template x-for="(field, index) in aiFields" :key="index">
                            <tr class="hover:bg-black/[0.01]">
                                <td class="px-6 py-3"><input type="text" x-model="field.key" @input="field.key = field.key.toLowerCase().replace(/[^a-z0-9_]/g, '')" class="w-full bg-transparent border-0 p-0 text-xs font-black text-rose-500 focus:ring-0"></td>
                                <td class="px-6 py-3"><input type="text" x-model="field.label" class="w-full bg-transparent border-0 p-0 text-xs font-bold dark:text-white focus:ring-0"></td>
                                <td class="px-6 py-3">
                                    <select x-model="field.type" class="w-full bg-slate-50 dark:bg-black/20 border-0 rounded-lg px-2 py-1 text-[10px] font-black uppercase text-slate-500 focus:ring-0 cursor-pointer">
                                        <option value="text">TEXT</option><option value="number">NUMBER</option><option value="date">DATE</option><option value="array">ARRAY</option>
                                    </select>
                                </td>
                                <td class="px-6 py-3"><input type="text" x-model="field.hint" class="w-full bg-transparent border-0 p-0 text-[10px] font-bold text-slate-400 focus:ring-0 italic"></td>
                                <td class="px-6 py-3 text-center"><button type="button" @click="aiFields.splice(index, 1)" class="text-slate-300 hover:text-rose-500 transition-colors"><i class="bi bi-trash3-fill"></i></button></td>
                            </tr>
                        </template>
                    </x-ui.table>
                </div>

                <!-- 4. Administrative Actions -->
                <div class="pt-10 space-y-6 pb-10">
                    <div class="flex items-center gap-3"><div class="w-1.5 h-4 bg-rose-500 rounded-full"></div><h2 class="text-[10px] font-black uppercase tracking-widest text-rose-600">{{ __('Administrative Actions') }}</h2></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-6 rounded-xl border border-amber-100 bg-amber-50/30 dark:bg-amber-500/5 flex flex-col justify-between gap-6">
                            <h3 class="text-[10px] font-black uppercase text-amber-700 tracking-widest">{{ __('Archive Status') }}</h3>
                            <form method="POST" action="{{ route('admin.folders.status', ['merchant' => $merchant->id]) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $merchant->status === 'active' ? 'archived' : 'active' }}">
                                <x-ui.button type="submit" variant="warning" class="w-full">{{ $merchant->status === 'active' ? __('Archive Folder') : __('Restore Folder') }}</x-ui.button>
                            </form>
                        </div>
                        <div class="p-6 rounded-xl border border-rose-100 bg-rose-50/30 dark:bg-rose-500/5 flex flex-col justify-between gap-6">
                            <h3 class="text-[10px] font-black uppercase text-rose-700 tracking-widest">{{ __('Permanent Deletion') }}</h3>
                            <form method="POST" action="{{ route('admin.folders.destroy', ['merchant' => $merchant->id]) }}" onsubmit="return confirm('{{ __('WARNING: THIS ACTION IS IRREVERSIBLE. Proceed?') }}')">
                                @csrf @method('DELETE')
                                <x-ui.button type="submit" variant="danger" class="w-full">{{ __('Terminate Folder') }}</x-ui.button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-black/[0.03] flex justify-end">
                    <x-ui.button type="button" @click="saveAll()" variant="primary" size="lg" ::disabled="saving">
                        <span x-show="!saving">{{ __('Update Configuration') }}</span>
                        <span x-show="saving"><i class="bi bi-arrow-repeat animate-spin"></i></span>
                    </x-ui.button>
                </div>
            </div>
        </x-ui.card>
    </div>
@endsection

@push('scripts')
<script>
    function folderEditor() {
        return {
            saving: false, analyzing: false,
            form: { name: {!! json_encode($merchant->name) !!}, max_slips: {!! json_encode($merchant->max_slips) !!} },
            aiFields: {!! json_encode($schemaFields) !!},
            addField() { this.aiFields.push({ key: 'field_' + Date.now(), label: 'New Field', type: 'text', hint: '' }); },
            async deleteSlip(id) {
                const confirmed = await Swal.fire({
                    title: '{{ __('Delete this slip?') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ed4245',
                    cancelButtonColor: '#4e5058',
                    confirmButtonText: 'Yes, delete it!'
                });
                if (!confirmed.isConfirmed) return;

                try {
                    const response = await fetch('/workspace/slips/delete/' + id, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    if (response.ok) {
                        document.getElementById('slip-row-' + id).remove();
                        window.notify.success('{{ __('Slip deleted successfully.') }}');
                    } else {
                        const data = await response.json();
                        throw new Error(data.message || '{{ __('Failed to delete') }}');
                    }
                } catch (e) { window.notify.error(e.message); }
            },
            async handleSampleUpload(e) {
                const file = e.target.files[0];
                if (!file) return;
                this.analyzing = true;
                const formData = new FormData();
                formData.append('image', file);
                try {
                    const res = await fetch('{{ route('admin.settings.suggest') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.aiFields = data.ai_fields;
                        window.notify.success('{{ __('Analysis Complete') }}');
                    } else { throw new Error(data.message || '{{ __('Error') }}'); }
                } catch (e) { window.notify.error(e.message); }
                finally { this.analyzing = false; e.target.value = ''; }
            },
            async saveAll() {
                this.saving = true;
                try {
                    const res = await fetch('{{ route('admin.folders.update', ['merchant' => $merchant->id]) }}', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ ...this.form, ai_fields: this.aiFields })
                    });
                    if (res.ok) window.notify.success('{{ __('Updated') }}');
                    else throw new Error('{{ __('Error') }}');
                } catch (e) { window.notify.error(e.message); }
                finally { this.saving = false; }
            }
        }
    }
</script>
@endpush