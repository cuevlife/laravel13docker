@extends('layouts.app')

@section('content')
    <div class="w-full px-2 py-4 sm:px-4 lg:px-6" x-data="slipRegistry()">
        <x-ui.card>
            {{-- 1. Header Section --}}
            <div class="px-8 py-8 border-b border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/10">
                <x-ui.page-header :title="__('Workspace Inbox')" :subtitle="$tenant->name" icon="bi-inbox-fill">
                    <x-slot:actions>
                        <x-ui.button variant="ghost" size="lg" icon="bi-sliders2" @click="openDataSettings()" />
                        <x-ui.button variant="success" size="lg" icon="bi-qr-code-scan" @click="triggerScan()">
                            {{ __('Scan Receipt') }}
                        </x-ui.button>
                    </x-slot:actions>
                </x-ui.page-header>
            </div>

            <div class="p-8 space-y-8">
                {{-- 2. Filters --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-11">
                        <x-ui.input x-model.debounce.500ms="filters.q" placeholder="{{ __('Search slips...') }}" icon="bi-search" />
                    </div>
                    <div class="md:col-span-1">
                        <x-ui.button variant="danger" class="w-full h-12" icon="bi-arrow-counterclockwise" @click="resetFilters()" />
                    </div>
                </div>

                {{-- 3. Data Table --}}
                <x-ui.table :headers="['UID', 'Shop', 'Amount', 'Date', 'Status', '']">
                    <template x-for="slip in slips" :key="slip.id">
                        <tr class="group hover:bg-black/[0.01] dark:hover:bg-white/[0.01]" x-transition>
                            <td class="px-6 py-4 font-black text-indigo-500 text-xs" x-text="slip.uid"></td>
                            <td class="px-6 py-4 text-xs dark:text-white" x-text="slip.display_shop"></td>
                            <td class="px-6 py-4 text-xs font-black dark:text-white" x-text="numberFormat(slip.display_amount)"></td>
                            <td class="px-6 py-4 text-[10px] text-slate-400" x-text="formatDate(slip.created_at)"></td>
                            <td class="px-6 py-4">
                                <x-ui.badge :variant="slip.workflow_status === 'approved' ? 'success' : 'warning'" x-text="slip.workflow_status"></x-ui.badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="deleteSlip(slip.id)" class="text-slate-300 hover:text-rose-500 transition-colors"><i class="bi bi-trash-fill"></i></button>
                            </td>
                        </tr>
                    </template>
                </x-ui.table>
            </div>
        </x-ui.card>

        {{-- 4. Export Designer Modal --}}
        <x-ui.modal name="data-settings-modal" maxWidth="2xl">
            <div class="p-8 space-y-8">
                <x-ui.page-header :title="__('Data Designer')" :subtitle="__('Configure Export Columns & Order')" />
                
                <div class="space-y-4">
                    <div class="bg-[#f8fafb] dark:bg-black/20 p-4 rounded-xl border border-black/5 dark:border-white/5">
                        <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">{{ __('Excel Filename') }}</label>
                        <x-ui.input x-model="exportFilename" />
                    </div>

                    <x-ui.table :headers="['Order', 'Enable', 'Field Key', 'Display Label']">
                        <tbody id="sortable-columns">
                            <template x-for="(col, index) in exportColumns" :key="col.key">
                                <tr class="bg-white dark:bg-[#1e1f22]" :data-id="col.key">
                                    <td class="px-6 py-3 text-center cursor-move handle">
                                        <i class="bi bi-grip-vertical text-slate-300"></i>
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <input type="checkbox" x-model="col.enabled" class="rounded border-black/10 text-discord-green">
                                    </td>
                                    <td class="px-6 py-3 text-xs font-black text-indigo-500 uppercase" x-text="col.key"></td>
                                    <td class="px-6 py-3">
                                        <x-ui.input x-model="col.label" class="!h-8 !text-[10px]" />
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </x-ui.table>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-black/[0.03]">
                    <x-ui.button variant="ghost" @click="$dispatch('close-modal', {name: 'data-settings-modal'})">{{ __('Cancel') }}</x-ui.button>
                    <x-ui.button variant="success" @click="saveExportSettings()" ::disabled="savingExport">
                        <span x-show="!savingExport">{{ __('Save Configuration') }}</span>
                        <span x-show="savingExport"><i class="bi bi-arrow-repeat animate-spin"></i></span>
                    </x-ui.button>
                </div>
            </div>
        </x-ui.modal>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('slipRegistry', () => ({
                slips: {!! json_encode($slips->items()) !!},
                filters: { q: '', workflow_status: '', sort: 'created_at_desc' },
                exportColumns: {!! json_encode($exportColumns ?? []) !!},
                exportFilename: 'export',
                savingExport: false,
                is_loading: false,

                init() {
                    this.initSortable();
                },

                initSortable() {
                    this.$nextTick(() => {
                        const el = document.getElementById('sortable-columns');
                        if (el) {
                            Sortable.create(el, {
                                handle: '.handle',
                                animation: 150,
                                onEnd: (evt) => {
                                    // อ่านตำแหน่งใหม่จาก DOM
                                    const rows = Array.from(el.querySelectorAll('tr[data-id]'));
                                    const newOrder = rows.map((r, i) => {
                                        const col = this.exportColumns.find(c => c.key === r.getAttribute('data-id'));
                                        return { ...col, order: i + 1 };
                                    });
                                    // บังคับอัปเดต Alpine State
                                    this.exportColumns = [];
                                    this.$nextTick(() => { this.exportColumns = newOrder; });
                                }
                            });
                        }
                    });
                },

                async saveExportSettings() {
                    this.savingExport = true;
                    try {
                        const res = await fetch('{{ route('workspace.slip.export-settings') }}', {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ export_columns: this.exportColumns, excel_filename: this.exportFilename })
                        });
                        if (res.ok) window.notify.success('Saved!');
                    } catch (e) { window.notify.error('Failed'); }
                    finally { this.savingExport = false; }
                },

                openDataSettings() {
                    this.$dispatch('open-modal', { name: 'data-settings-modal' });
                    this.initSortable();
                },

                async fetchSlips() { /* AJAX logic here */ },
                deleteSlip(id) { /* AJAX logic here */ },
                formatDate(iso) { return new Date(iso).toLocaleDateString('th-TH'); },
                numberFormat(val) { return new Intl.NumberFormat().format(val); }
            }));
        });
    </script>
@endpush