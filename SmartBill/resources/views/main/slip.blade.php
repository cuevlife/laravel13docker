@extends('layouts.app')

@section('content')
    <div class="w-full px-2 py-4 sm:px-4 lg:px-6" x-data="slipRegistry()">
        <div class="rounded-xl bg-white p-6 sm:p-8 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] border border-black/[0.04] dark:bg-[#2b2d31] dark:border-white/5">
            <!-- Header Section -->
            <div class="mb-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-discord-green/10 text-discord-green">
                        <i class="bi bi-inbox-fill h-6 w-6"></i>
                    </div>
                    <h1 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">Workspace Inbox</h1>
                </div>
                
                <button @click="triggerScan()" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-discord-green px-6 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-green-500/20 transition hover:bg-[#1f8b4c]">
                    <i class="bi bi-qr-code-scan h-4 w-4"></i>
                    <span>Scan Receipt</span>
                </button>
            </div>

            <!-- Filters Section -->
            <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-12">
                <div class="relative sm:col-span-6">
                    <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-[#80848e] z-10"></i>
                    <input type="text" x-model="filters.q" @input.debounce.500ms="fetchSlips()" placeholder="ค้นหาด่วน (ชื่อร้าน, UID, ยอดเงิน)..." class="h-10 w-full rounded-xl border border-black/5 bg-white pl-14 pr-4 text-xs font-bold outline-none shadow-sm focus:border-discord-green/30 dark:bg-[#1e1f22] dark:text-white transition-all">
                </div>
                
                <div class="sm:col-span-2">
                    <select x-model="filters.workflow_status" @change="fetchSlips()" class="h-10 w-full rounded-xl border border-black/5 bg-white px-3 text-xs font-bold outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white transition-all">
                        <option value="">ทุกสถานะ</option>
                        @foreach($workflowOptions ?? [] as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <div class="relative">
                        <i class="bi bi-calendar absolute left-3 top-1/2 -translate-y-1/2 text-[#80848e] z-10"></i>
                        <input type="text" id="date-range-picker" placeholder="เลือกวันที่..." class="h-10 w-full rounded-xl border border-black/5 bg-white pl-10 pr-3 text-xs font-bold outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white transition-all">
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <button @click="resetFilters()" class="flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-rose-100 bg-rose-50 text-[10px] font-black uppercase tracking-widest text-rose-500 shadow-sm transition hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10">
                        <i class="bi bi-arrow-counterclockwise text-xs"></i> ล้างค่า
                    </button>
                </div>
            </div>

            <!-- Bulk Actions Row -->
            <div class="mb-4 flex flex-wrap items-center justify-between gap-4 border-t border-black/[0.04] pt-4 dark:border-white/[0.04]">
                <div class="flex items-center gap-3">
                    <div class="inline-flex h-9 items-center rounded-xl bg-discord-green/10 px-4 text-[10px] font-black text-discord-green">
                        <span x-text="selectedSlips.length">0</span> รายการที่เลือก
                    </div>
                    
                    <div class="flex items-center gap-1">
                        <button @click="bulkAction('mark_approved')" :disabled="selectedSlips.length === 0" class="flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-600 transition hover:bg-emerald-100 shadow-sm disabled:opacity-50" title="Approve Selected">
                            <i class="bi bi-check-all h-5 w-5"></i>
                        </button>
                        <button @click="bulkAction('delete')" :disabled="selectedSlips.length === 0" class="flex h-9 w-9 items-center justify-center rounded-xl border border-rose-100 bg-rose-50 text-rose-500 transition hover:bg-rose-100 shadow-sm disabled:opacity-50" title="Delete Selected">
                            <i class="bi bi-trash-fill h-4 w-4"></i>
                        </button>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button @click="fetchExportHistory()"
                       class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-white dark:bg-[#1e1f22] text-[#80848e] hover:text-indigo-600 px-4 text-[10px] font-black uppercase tracking-widest transition shadow-sm border border-black/5 dark:border-white/5">
                        <i class="bi bi-clock-history"></i>
                        <span>History</span>
                    </button>

                    <button @click="openExportDesigner()"
                       class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-white dark:bg-[#1e1f22] text-[#80848e] hover:text-discord-green px-4 text-[10px] font-black uppercase tracking-widest transition shadow-sm border border-black/5 dark:border-white/5">
                        <i class="bi bi-gear-fill"></i>
                        <span>Designer</span>
                    </button>

                    <button @click="if(selectedSlips.length > 0) window.location.href = '{{ route('workspace.slip.export') }}?' + new URLSearchParams({...filters, ids: selectedSlips.join(',')}).toString()"
                       :disabled="selectedSlips.length === 0"
                       class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-discord-green/10 text-discord-green hover:bg-discord-green/20 px-6 text-[10px] font-black uppercase tracking-widest transition shadow-sm border border-discord-green/20 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="bi bi-file-earmark-spreadsheet-fill text-sm"></i>
                        <span>Export Excel</span>
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="overflow-hidden relative min-h-[400px]">
                <div x-show="is_loading" class="absolute inset-0 bg-white/50 backdrop-blur-[2px] z-20 flex items-center justify-center dark:bg-black/20" x-cloak>
                    <i class="bi bi-arrow-repeat h-8 w-8 animate-spin text-discord-green"></i>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[11px] font-bold text-[#1e1f22] dark:text-[#b5bac1]">
                        <thead class="border-y border-black/[0.04] text-[10px] font-black uppercase tracking-widest text-[#80848e] dark:border-white/[0.04]">
                            <tr>
                                <th class="px-4 py-4 w-[40px]">
                                    <input type="checkbox" @click="toggleSelectAll()" :checked="selectedSlips.length === slips.length && slips.length > 0" class="h-4 w-4 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm">
                                </th>
                                <th class="px-4 py-4 min-w-[200px]">รายละเอียดสลิป</th>
                                <th class="px-4 py-4 text-center w-[120px]">วันที่ในสลิป <i class="bi bi-chevron-down text-discord-green"></i></th>
                                <th class="px-4 py-4 text-center w-[140px]">ประมวลผลเมื่อ</th>
                                <th class="px-4 py-4 text-center w-[140px]">สถานะ</th>
                                <th class="px-4 py-4 text-right w-[120px]">ยอดเงินรวม</th>
                                <th class="px-4 py-4 text-right w-[100px]">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/[0.04] dark:divide-white/[0.04]">
                            <template x-for="slip in slips" :key="slip.id">
                                <tr class="group transition hover:bg-[#fafcfa] dark:hover:bg-white/[0.02]">
                                    <td class="px-4 py-5 align-top">
                                        <input type="checkbox" x-model="selectedSlips" :value="slip.id" class="slip-checkbox h-4 w-4 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm">
                                    </td>
                                    <td class="px-4 py-5 align-top">
                                        <div class="flex items-start gap-4">
                                            <div class="h-[52px] w-[52px] shrink-0 overflow-hidden rounded-xl border border-black/5 shadow-sm dark:border-white/5 bg-white dark:bg-[#1e1f22]">
                                                <img :src="'/storage/' + slip.image_path" class="h-full w-full object-cover opacity-90 transition-opacity group-hover:opacity-100">
                                            </div>
                                            <div class="flex flex-col pt-0.5">
                                                <span class="text-[13px] font-black leading-tight text-[#1e1f22] dark:text-white transition-colors group-hover:text-[#4f86f7]" x-text="slip.display_shop"></span>
                                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                                    <span class="text-[10px] font-black tracking-widest text-[#80848e]" x-text="slip.uid"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 align-top text-center text-[11px] font-black text-[#5c5e66] dark:text-[#b5bac1] pt-6" x-text="slip.display_date"></td>
                                    <td class="px-4 py-5 align-top text-center text-[11px] font-black text-[#5c5e66] dark:text-[#b5bac1] pt-6" x-text="formatDate(slip.processed_at)"></td>
                                    <td class="px-4 py-5 align-top text-center pt-5">
                                        <span class="inline-flex items-center rounded-xl px-3 py-1 text-[9px] font-black uppercase tracking-widest"
                                              :class="{
                                                  'bg-[#e0f5ea] text-[#12a170]': slip.workflow_status === 'reviewed',
                                                  'bg-[#f2f7ff] text-[#4f86f7]': slip.workflow_status === 'exported',
                                                  'bg-emerald-100 text-emerald-700': slip.workflow_status === 'approved',
                                                  'bg-slate-50 text-slate-600': slip.workflow_status !== 'reviewed' && slip.workflow_status !== 'exported' && slip.workflow_status !== 'approved'
                                              }"
                                              x-text="slip.workflow_status === 'reviewed' ? 'แสกนแล้ว (AI)' : (slip.workflow_status === 'exported' ? 'ส่งออก Excel แล้ว' : (slip.workflow_status === 'approved' ? 'อนุมัติแล้ว' : slip.workflow_status))">
                                        </span>
                                    </td>
                                    <td class="px-4 py-5 align-top text-right pt-6">
                                        <span class="text-[13px] font-black tracking-tight text-[#1e1f22] dark:text-white" x-text="'THB ' + Number(slip.display_amount).toLocaleString(undefined, {minimumFractionDigits: 2})"></span>
                                    </td>
                                    <td class="px-4 py-5 align-top text-right pt-5">
                                        <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                            <a :href="'/workspace/slips/edit/' + slip.id" class="flex h-8 w-8 items-center justify-center rounded-xl text-[#80848e] transition hover:bg-black/5 hover:text-[#1e1f22] dark:hover:bg-white/5 dark:hover:text-white">
                                                <i class="bi bi-eye-fill h-4 w-4"></i>
                                            </a>
                                            <a :href="'/workspace/slips/edit/' + slip.id" class="flex h-8 w-8 items-center justify-center rounded-xl text-[#80848e] transition hover:bg-black/5 hover:text-[#1e1f22] dark:hover:bg-white/5 dark:hover:text-white">
                                                <i class="bi bi-pencil-square h-4 w-4"></i>
                                            </a>
                                            <button @click="deleteSlip(slip.id)" class="flex h-8 w-8 items-center justify-center rounded-xl text-discord-red transition hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                                <i class="bi bi-trash-fill h-4 w-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="slips.length === 0 && !is_loading">
                                <tr>
                                    <td colspan="7" class="py-24 text-center">
                                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-xl bg-[#f8fafb] border border-black/[0.02] shadow-sm dark:bg-[#1e1f22] dark:border-white/5">
                                            <i class="bi bi-receipt h-8 w-8 text-[#80848e]"></i>
                                        </div>
                                        <h3 class="text-[13px] font-black text-[#1e1f22] dark:text-white">ไม่พบสลิปในโฟลเดอร์นี้</h3>
                                        <p class="mt-1 text-xs font-bold text-[#80848e]">ลองค้นหาด้วยคำอื่น หรือกด Scan Receipt เพื่อเพิ่มสลิปใหม่</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between border-t border-black/[0.04] pt-6 dark:border-white/[0.04]" x-show="pagination && pagination.total > 0">
                <div class="text-[11px] font-bold text-[#80848e]">
                    Showing <span class="font-black text-[#1e1f22] dark:text-white" x-text="slips.length"></span> of <span class="font-black text-[#1e1f22] dark:text-white" x-text="pagination.total"></span> slips
                </div>
                <div class="flex items-center gap-2">
                    <template x-for="link in pagination.links">
                        <button @click="fetchSlips(link.url)" 
                                :disabled="!link.url || link.active"
                                class="h-8 min-w-[32px] rounded-xl px-2 text-[10px] font-black uppercase transition-all"
                                :class="{
                                    'bg-discord-green text-white shadow-lg shadow-green-500/20': link.active,
                                    'bg-[#f8fafb] text-[#5c5e66] hover:bg-black/5 dark:bg-[#1e1f22] dark:text-[#b5bac1]': !link.active && link.url,
                                    'opacity-30 cursor-not-allowed': !link.url
                                }"
                                x-html="link.label">
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Scan Modal -->
        <div x-show="scanModalOpen" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             x-transition.opacity
             x-cloak>
            <div class="bg-white dark:bg-[#2b2d31] w-full max-w-2xl rounded-xl shadow-2xl border border-black/5 overflow-hidden"
                 @click.away="!isScanning && (scanModalOpen = false)">
                
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-discord-green/10 text-discord-green">
                                <i class="bi bi-qr-code-scan h-6 w-6"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight">Auto-Scan Receipt</h2>
                                <p class="text-xs font-bold text-[#80848e]">เลือกไฟล์รูปสลิปเพื่อแสกนและบันทึกอัตโนมัติ</p>
                            </div>
                        </div>
                        <button @click="scanModalOpen = false" :disabled="isScanning" class="text-[#80848e] hover:text-rose-500 transition disabled:opacity-50">
                            <i class="bi bi-x-lg h-6 w-6"></i>
                        </button>
                    </div>

                    <!-- Dropzone -->
                    <label class="group relative flex flex-col items-center justify-center py-12 border-2 border-dashed border-[#e3e5e8] dark:border-[#313338] rounded-xl bg-[#f8fafb] dark:bg-[#1e1f22] cursor-pointer hover:border-discord-green/50 transition-colors mb-6">
                        <input type="file" multiple accept="image/*" class="hidden" @change="handleFileSelect">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-white dark:bg-[#2b2d31] shadow-sm mb-4 group-hover:scale-110 transition-transform">
                            <i class="bi bi-image h-8 w-8 text-discord-green"></i>
                        </div>
                        <p class="text-sm font-black text-[#1e1f22] dark:text-white">คลิกเพื่อเลือก หรือลากไฟล์มาวางที่นี่</p>
                        <p class="text-[10px] font-bold text-[#80848e] mt-1">รองรับ JPG, PNG (สูงสุด 10MB ต่อไฟล์)</p>
                    </label>

                    <!-- File Queue -->
                    <div class="max-h-[250px] overflow-y-auto space-y-3 pr-2 custom-scrollbar">
                        <template x-for="(f, index) in scanFiles" :key="index">
                            <div class="flex items-center gap-4 p-4 rounded-xl border border-black/5 bg-white dark:bg-[#232428] dark:border-white/5 shadow-sm">
                                <div class="h-10 w-10 shrink-0 rounded-xl bg-[#f8fafb] dark:bg-[#1e1f22] flex items-center justify-center border border-black/5">
                                    <i class="bi bi-image h-5 w-5 text-[#80848e]"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11px] font-black text-[#1e1f22] dark:text-white truncate" x-text="f.name"></span>
                                        <span class="text-[10px] font-bold text-[#80848e]" x-text="f.size"></span>
                                    </div>
                                    <div class="w-full h-1.5 bg-black/5 dark:bg-white/5 rounded-xl overflow-hidden">
                                        <div class="h-full bg-discord-green transition-all duration-500" 
                                             :style="'width: ' + (f.status === 'completed' || f.status === 'duplicate' ? '100%' : (f.status === 'uploading' ? '50%' : '0%'))"></div>
                                    </div>
                                    <div class="mt-1 flex items-center justify-between">
                                        <span class="text-[9px] font-black uppercase tracking-widest"
                                              :class="{
                                                  'text-[#80848e]': f.status === 'pending',
                                                  'text-[#4f86f7]': f.status === 'uploading',
                                                  'text-discord-green': f.status === 'completed',
                                                  'text-amber-500': f.status === 'duplicate',
                                                  'text-rose-500': f.status === 'error'
                                              }"
                                              x-text="f.status === 'error' ? 'Error: ' + f.error : (f.status === 'duplicate' ? 'มีสลิปนี้อยู่แล้ว' : f.status)"></span>
                                        <button @click="removeFile(index)" x-show="f.status !== 'uploading'" class="text-[#80848e] hover:text-rose-500 transition p-1">
                                            <i class="bi bi-trash-fill h-3.5 w-3.5"></i>
                                        </button>
                                        <div x-show="f.status === 'uploading'" class="p-1">
                                            <i class="bi bi-arrow-repeat h-3.5 w-3.5 text-[#4f86f7] animate-spin"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <template x-if="scanFiles.length === 0">
                            <div class="py-8 text-center text-[#80848e]">
                                <i class="bi bi-inbox-fill h-8 w-8 mx-auto mb-2 opacity-50"></i>
                                <p class="text-xs font-bold">ยังไม่มีไฟล์ในคิวแสกน</p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="bg-[#f2f3f5] dark:bg-[#232428] p-6 flex items-center justify-between border-t border-black/5 dark:border-white/5">
                    <div class="text-[10px] font-black uppercase tracking-widest text-[#80848e]">
                        <span x-text="scanFiles.filter(f => f.status === 'completed' || f.status === 'duplicate').length"></span> / 
                        <span x-text="scanFiles.length"></span> เสร็จสิ้น
                    </div>
                    <div class="flex gap-3">
                        <button @click="scanFiles = []; scanModalOpen = false" 
                                :disabled="isScanning"
                                class="px-6 py-2.5 text-[11px] font-black uppercase tracking-widest text-[#5c5e66] hover:bg-black/5 dark:text-[#b5bac1] transition rounded-xl disabled:opacity-50">
                            ปิดหน้าต่าง
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Designer Modal -->
        <div x-show="exportModalOpen" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             x-transition.opacity
             x-cloak>
            <div class="bg-white dark:bg-[#2b2d31] w-full max-w-2xl rounded-xl shadow-2xl border border-black/5 overflow-hidden"
                 @click.away="!savingExport && (exportModalOpen = false)">
                
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-discord-green/10 text-discord-green shadow-sm">
                                <i class="bi bi-gear-fill text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-tight">Export Designer</h2>
                                <p class="text-[10px] font-bold text-[#80848e] uppercase tracking-widest">จัดลำดับและเปลี่ยนชื่อหัวตาราง Excel</p>
                            </div>
                        </div>
                        <button @click="exportModalOpen = false" :disabled="savingExport" class="h-8 w-8 flex items-center justify-center rounded-full text-[#80848e] hover:bg-black/5 dark:hover:bg-white/5 transition disabled:opacity-50">
                            <i class="bi bi-x-lg text-sm"></i>
                        </button>
                    </div>

                    {{-- Filename Customization --}}
                    <div class="bg-[#f8fafb] dark:bg-black/20 p-4 rounded-xl border border-black/5 dark:border-white/5">
                        <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Default Excel Filename</label>
                        <div class="relative">
                            <i class="bi bi-file-earmark-excel absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" x-model="exportFilename" placeholder="e.g. My_Export.xlsx" 
                                   class="w-full bg-white dark:bg-[#1e1f22] border border-black/10 dark:border-white/10 rounded-xl pl-11 pr-4 py-3 text-xs font-bold text-[#1e1f22] dark:text-white outline-none focus:ring-2 focus:ring-discord-green/20 transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="overflow-hidden border border-black/5 dark:border-white/5 rounded-xl bg-[#f8fafb] dark:bg-[#1e1f22]">
                        <div class="max-h-[45vh] overflow-y-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse">
                                <thead class="sticky top-0 bg-[#f8fafb] dark:bg-[#1e1f22] shadow-[0_1px_0_rgba(0,0,0,0.05)] z-10 text-[9px] font-black uppercase tracking-widest text-[#80848e]">
                                    <tr>
                                        <th class="px-4 py-3 w-12 text-center">#</th>
                                        <th class="px-4 py-3 w-16 text-center">Export</th>
                                        <th class="px-4 py-3">Source Field</th>
                                        <th class="px-4 py-3">Label in Excel</th>
                                    </tr>
                                </thead>
                                <tbody id="export-sortable-body" class="divide-y divide-black/5 dark:divide-white/5">
                                    <template x-for="(col, index) in exportColumns" :key="col.key">
                                        <tr class="group hover:bg-black/[0.02] dark:hover:bg-white/[0.02] cursor-move bg-white dark:bg-[#2b2d31]" :data-id="col.key">
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <i class="bi bi-grip-vertical text-[#e3e5e8] group-hover:text-discord-green transition-colors"></i>
                                                    <span class="text-[9px] font-black text-[#80848e]" x-text="index + 1"></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <input type="checkbox" x-model="col.enabled" class="h-4 w-4 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm transition-all cursor-pointer">
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-[10px] font-black text-[#1e1f22] dark:text-white uppercase tracking-tight" x-text="col.key"></span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text" x-model="col.label" 
                                                       class="w-full bg-[#f2f3f5] dark:bg-black/20 border-0 rounded-lg px-3 py-1.5 text-[11px] font-bold text-[#1e1f22] dark:text-white focus:ring-1 focus:ring-discord-green/30 outline-none transition-all" 
                                                       placeholder="Header Name...">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-[#f2f3f5] dark:bg-[#232428] px-6 py-4 flex items-center justify-between border-t border-black/5 dark:border-white/5">
                    <p class="text-[9px] font-black text-[#80848e] uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-info-circle-fill"></i>
                        {{ __('Affects only this folder') }}
                    </p>
                    <div class="flex gap-2">
                        <button @click="exportModalOpen = false" :disabled="savingExport" class="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-[#5c5e66] hover:bg-black/5 dark:text-[#b5bac1] transition rounded-xl">ยกเลิก</button>
                        <button @click="saveExportSettings()" :disabled="savingExport" class="px-6 py-2 bg-discord-green text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-500/20 hover:bg-[#1f8b4c] transition flex items-center gap-2">
                            <i x-show="savingExport" class="bi bi-arrow-repeat animate-spin"></i>
                            <span x-text="savingExport ? 'กำลังบันทึก...' : 'บันทึกค่า'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export History Modal -->
        <div x-show="exportHistoryOpen" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             x-transition.opacity
             x-cloak>
            <div class="bg-white dark:bg-[#2b2d31] w-full max-w-2xl rounded-xl shadow-2xl border border-black/5 overflow-hidden"
                 @click.away="exportHistoryOpen = false">
                
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-500 shadow-sm">
                                <i class="bi bi-clock-history text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-tight">Export History</h2>
                                <p class="text-[10px] font-bold text-[#80848e] uppercase tracking-widest">ประวัติการส่งออกข้อมูล Excel ล่าสุด</p>
                            </div>
                        </div>
                        <button @click="exportHistoryOpen = false" class="h-8 w-8 flex items-center justify-center rounded-full text-[#80848e] hover:bg-black/5 dark:hover:bg-white/5 transition">
                            <i class="bi bi-x-lg text-sm"></i>
                        </button>
                    </div>

                    <div class="overflow-hidden border border-black/5 dark:border-white/5 rounded-xl bg-[#f8fafb] dark:bg-[#1e1f22]">
                        <div class="max-h-[45vh] overflow-y-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse">
                                <thead class="sticky top-0 bg-[#f8fafb] dark:bg-[#1e1f22] shadow-[0_1px_0_rgba(0,0,0,0.05)] z-10 text-[9px] font-black uppercase tracking-widest text-[#80848e]">
                                    <tr>
                                        <th class="px-4 py-3">File Name</th>
                                        <th class="px-4 py-3 text-center">Slips</th>
                                        <th class="px-4 py-3 text-center">Mode</th>
                                        <th class="px-4 py-3 text-right">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                    <template x-for="exp in exportHistory" :key="exp.id">
                                        <tr class="group hover:bg-black/[0.02] dark:hover:bg-white/[0.02]">
                                            <td class="px-4 py-3">
                                                <div class="text-[11px] font-black text-[#1e1f22] dark:text-white truncate max-w-[250px]" x-text="exp.file_name"></div>
                                                <div class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest" x-text="'BY ' + exp.user_name"></div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="text-[10px] font-black text-[#1e1f22] dark:text-white" x-text="exp.slips_count"></span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-1.5 py-0.5 rounded text-[8px] uppercase border" 
                                                      :class="exp.mode === 'granular' ? 'bg-indigo-50 text-indigo-600 border-indigo-100' : 'bg-slate-50 text-slate-600 border-slate-100'"
                                                      x-text="exp.mode"></span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="text-[10px] font-bold text-[#5c5e66] dark:text-[#b5bac1]" x-text="exp.exported_at"></div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="exportHistory.length === 0">
                                        <tr>
                                            <td colspan="4" class="px-4 py-10 text-center text-[#80848e] italic uppercase tracking-widest text-[9px]">No export history found</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-[#f2f3f5] dark:bg-[#232428] px-6 py-4 flex items-center justify-end border-t border-black/5 dark:border-white/5">
                    <button @click="exportHistoryOpen = false" class="px-6 py-2 bg-white dark:bg-[#1e1f22] text-[#1e1f22] dark:text-white border border-black/10 dark:border-white/10 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-sm hover:bg-black/5 transition">ปิดหน้าต่าง</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('slipRegistry', () => ({
                slips: [],
                pagination: { total: 0, links: [] },
                is_loading: false,
                filters: {
                    q: {!! json_encode($activeFilters['q'] ?? '') !!},
                    workflow_status: {!! json_encode($activeFilters['workflow_status'] ?? '') !!},
                    date_from: '',
                    date_to: '',
                    sort: {!! json_encode($activeFilters['sort'] ?? 'latest') !!},
                    batch_id: {!! json_encode($activeFilters['batch_id'] ?? '') !!}
                },
                selectedSlips: [],
                scanModalOpen: false,
                isScanning: false,
                scanFiles: [],
                exportColumns: @json($exportColumns),
                exportFilename: {!! json_encode($tenant->config['excel_filename'] ?? '') !!},
                exportModalOpen: false,
                savingExport: false,
                exportHistoryOpen: false,
                exportHistory: [],

                async init() {
                    this.setupDatePicker();
                    await this.fetchSlips();
                    this.initSortable();
                },

                initSortable() {
                    this.$nextTick(() => {
                        const el = document.getElementById('export-sortable-body');
                        if (el && typeof Sortable !== 'undefined') {
                            Sortable.create(el, {
                                animation: 150,
                                handle: '.bi-grip-vertical',
                                onEnd: (evt) => {
                                    const rows = Array.from(el.querySelectorAll('tr'));
                                    const newOrder = rows.map((row, i) => {
                                        const key = row.getAttribute('data-id');
                                        const col = this.exportColumns.find(c => c.key === key);
                                        return { ...col, order: i + 1 };
                                    });
                                    this.exportColumns = newOrder;
                                }
                            });
                        }
                    });
                },

                openExportDesigner() {
                    this.exportModalOpen = true;
                    this.initSortable();
                },

                async fetchExportHistory() {
                    this.exportHistoryOpen = true;
                    try {
                        const res = await fetch('{{ route('workspace.slip.export-history') }}');
                        const json = await res.json();
                        this.exportHistory = json.data || [];
                    } catch (e) {
                        console.error('Failed to fetch history', e);
                    }
                },

                async saveExportSettings() {
                    this.savingExport = true;
                    try {
                        const res = await fetch('{{ route('workspace.slip.export-settings') }}', {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ 
                                export_columns: this.exportColumns,
                                excel_filename: this.exportFilename
                            })
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Save failed');

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Settings Saved',
                            text: 'Export configuration updated for this folder.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        this.exportModalOpen = false;
                    } catch (e) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error',
                            text: e.message,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } finally {
                        this.savingExport = false;
                    }
                },

                setupDatePicker() {
                    if (typeof flatpickr !== 'undefined') {
                        flatpickr('#date-range-picker', {
                            mode: 'range',
                            dateFormat: 'd/m/Y',
                            locale: 'th',
                            onClose: (selectedDates, dateStr) => {
                                if (selectedDates.length === 2) {
                                    const format = (d) => {
                                        const year = d.getFullYear();
                                        const month = String(d.getMonth() + 1).padStart(2, '0');
                                        const day = String(d.getDate()).padStart(2, '0');
                                        return `${year}-${month}-${day}`;
                                    };
                                    this.filters.date_from = format(selectedDates[0]);
                                    this.filters.date_to = format(selectedDates[1]);
                                    this.fetchSlips();
                                }
                            }
                        });
                    }
                },

                async fetchSlips(url = null) {
                    this.is_loading = true;
                    if (!url) {
                        url = new URL(window.location.origin + window.location.pathname);
                        Object.keys(this.filters).forEach(key => {
                            if (this.filters[key] !== '' && this.filters[key] !== null) {
                                url.searchParams.set(key, this.filters[key]);
                            }
                        });
                    }

                    try {
                        const response = await fetch(url, { 
                            headers: { 
                                'Accept': 'application/json', 
                                'X-Requested-With': 'XMLHttpRequest' 
                            } 
                        });
                        const json = await response.json();
                        this.slips = json.data || [];
                        this.pagination = json.pagination || { total: 0, links: [] };
                        window.history.pushState({}, '', url);
                    } catch (error) {
                        console.error('Fetch error:', error);
                    } finally {
                        this.is_loading = false;
                    }
                },

                toggleSelectAll() {
                    if (this.selectedSlips.length < this.slips.length) {
                        this.selectedSlips = this.slips.map(s => s.id);
                    } else {
                        this.selectedSlips = [];
                    }
                },

                triggerScan() {
                    this.scanModalOpen = true;
                },

                resetFilters() {
                    this.filters = { q: '', workflow_status: '', date_from: '', date_to: '', sort: 'latest' };
                    const picker = document.querySelector('#date-range-picker');
                    if (picker && picker._flatpickr) picker._flatpickr.clear();
                    this.fetchSlips();
                },

                formatDate(dateStr) {
                    if (!dateStr) return '-';
                    try {
                        const date = new Date(dateStr);
                        const d = String(date.getDate()).padStart(2, '0');
                        const m = String(date.getMonth() + 1).padStart(2, '0');
                        const y = date.getFullYear();
                        const h = String(date.getHours()).padStart(2, '0');
                        const min = String(date.getMinutes()).padStart(2, '0');
                        return `${d}/${m}/${y} ${h}:${min}`;
                    } catch (e) { return dateStr; }
                },

                getCsrfToken() {
                    const meta = document.querySelector('meta[name=csrf-token]');
                    return meta ? meta.content : '';
                },

                async deleteSlip(id) {
                    if(!confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสลิปนี้?')) return;
                    const originalSlips = [...this.slips];
                    const originalTotal = this.pagination.total;
                    this.slips = this.slips.filter(slip => slip.id !== id);
                    this.pagination.total = Math.max(0, (this.pagination.total || 1) - 1);

                    try {
                        const res = await fetch('/workspace/slips/delete/' + id, {
                            method: 'DELETE',
                            headers: { 
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': this.getCsrfToken()
                            }
                        });
                        if (!res.ok) throw new Error('Server error');
                        const data = await res.json();
                        if(data.status !== 'success') throw new Error(data.message || 'Delete failed');
                        if(this.slips.length < 5 && this.pagination.total > 0) this.fetchSlips();
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'ลบสลิปเรียบร้อยแล้ว',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } catch (error) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: error.message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        this.slips = originalSlips;
                        this.pagination.total = originalTotal;
                    }
                },

                async bulkAction(action) {
                    if (this.selectedSlips.length === 0) return;
                    if (action === 'delete' && !confirm('คุณแน่ใจหรือไม่ว่าต้องการลบ ' + this.selectedSlips.length + ' รายการที่เลือก?')) return;
                    
                    this.is_loading = true;
                    try {
                        const res = await fetch('/workspace/slips/bulk', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken()
                            },
                            body: JSON.stringify({ slip_ids: this.selectedSlips, bulk_action: action })
                        });
                        if (!res.ok) throw new Error('Bulk action failed');
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'ดำเนินการสำเร็จ',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        
                        this.selectedSlips = [];
                        this.fetchSlips(); 
                    } catch (error) {
                        alert('เกิดข้อผิดพลาด');
                    } finally {
                        this.is_loading = false;
                    }
                },

                handleFileSelect(event) {
                    const files = Array.from(event.target.files);
                    files.forEach(file => {
                        this.scanFiles.push({ file, name: file.name, size: (file.size / 1024).toFixed(2) + ' KB', status: 'pending', progress: 0, error: null });
                    });
                    event.target.value = ''; 
                    this.processQueue();
                },

                async processQueue() {
                    if (this.isScanning) return;
                    const pendingFile = this.scanFiles.find(f => f.status === 'pending');
                    if (!pendingFile) {
                        this.isScanning = false;
                        const completedCount = this.scanFiles.filter(f => f.status === 'completed').length;
                        const duplicateCount = this.scanFiles.filter(f => f.status === 'duplicate').length;
                        const errorCount = this.scanFiles.filter(f => f.status === 'error').length;
                        if (completedCount > 0 || duplicateCount > 0 || errorCount > 0) {
                            if (window.Swal) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 4000,
                                    timerProgressBar: true,
                                    title: 'Scan Summary',
                                    html: `<div class="text-[11px] font-bold text-left mt-1">
                                        ${completedCount > 0 ? `<div class="text-discord-green">แสกนสำเร็จ: ${completedCount}</div>` : ''}
                                        ${duplicateCount > 0 ? `<div class="text-amber-500">ไฟล์ซ้ำ: ${duplicateCount}</div>` : ''}
                                        ${errorCount > 0 ? `<div class="text-rose-500">ผิดพลาด: ${errorCount}</div>` : ''}
                                    </div>`,
                                    icon: errorCount > 0 ? 'warning' : (duplicateCount > 0 ? 'info' : 'success'),
                                    background: document.documentElement.classList.contains('dark') ? '#2b2d31' : '#ffffff',
                                    color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#1e1f22'
                                });
                            }
                        }
                        return;
                    }

                    this.isScanning = true;
                    pendingFile.status = 'uploading';
                    const formData = new FormData();
                    formData.append('image', pendingFile.file);

                    try {
                        const res = await fetch('/workspace/slips/process', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.getCsrfToken(), 'Accept': 'application/json' },
                            body: formData
                        });
                        
                        if (res.status === 429) {
                            const errorData = await res.json();
                            pendingFile.status = 'error';
                            pendingFile.error = 'Rate Limit';
                            this.isScanning = false;
                            
                            Swal.fire({
                                icon: 'warning',
                                title: 'API Rate Limit Reached',
                                text: errorData.message || 'The system has reached its AI processing limit. Please try again in a few minutes or tomorrow.',
                                confirmButtonColor: '#ed4245',
                                background: document.documentElement.classList.contains('dark') ? '#2b2d31' : '#ffffff',
                                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#1e1f22'
                            });
                            return; // Stop queue processing
                        }

                        if (!res.ok) {
                            const errorData = await res.json().catch(() => ({}));
                            throw new Error(errorData.message || 'Process error');
                        }
                        const data = await res.json();
                        if (data.status === 'duplicate') {
                            pendingFile.status = 'duplicate';
                            pendingFile.progress = 100;
                        } else {
                            pendingFile.status = 'completed';
                            pendingFile.progress = 100;
                            await this.fetchSlips();
                        }
                    } catch (error) {
                        pendingFile.status = 'error';
                        pendingFile.error = error.message;
                    } finally {
                        this.isScanning = false;
                        this.processQueue();
                    }
                },

                removeFile(index) {
                    if (this.scanFiles[index].status === 'uploading') return;
                    this.scanFiles.splice(index, 1);
                }
            }));
        });
    </script>
    @endpush
@endsection
