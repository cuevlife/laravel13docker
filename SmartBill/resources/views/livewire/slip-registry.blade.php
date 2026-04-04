<div class="space-y-5 pb-20" x-data="{ 
    selected: @entangle('selectedIds'), 
    localSearch: '',
    matchesSearch(el) {
        if (!this.localSearch) return true;
        const text = el.innerText.toLowerCase();
        const search = this.localSearch.toLowerCase();
        return text.includes(search);
    }
}">
    <section class="rounded-[1rem] border border-black/5 bg-white shadow-sm dark:border-white/10 dark:bg-[#2b2d31]">
        <!-- Table Header / Actions -->
        <div class="flex items-center gap-1 border-b border-[#e3e5e8] px-4 py-3 dark:border-[#313338]">
            <div class="flex items-center gap-2">
                <i data-lucide="inbox" class="w-4 h-4 text-discord-green"></i>
                <h2 class="text-sm font-black uppercase tracking-widest text-[#162033] dark:text-white">Workspace Inbox</h2>
            </div>

            <div class="ml-auto flex items-center gap-2">
                <button type="button" x-on:click="$dispatch('gentle-open-scan')" class="inline-flex h-9 items-center justify-center gap-2 rounded-[0.85rem] bg-discord-green px-4 text-[10px] font-black uppercase tracking-[0.18em] text-white transition hover:bg-[#1f8b4c] shadow-lg shadow-green-500/10">
                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                    <span>Scan Receipt</span>
                </button>
            </div>
        </div>

        <!-- Search & Quick Filters -->
        <div class="border-b border-[#e3e5e8] px-4 py-3 dark:border-[#313338] md:px-5 md:py-4">
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1fr)_240px_280px_auto]">
                    <div class="relative" wire:ignore wire:key="filter-search-box">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[#80848e]"></i>
                        <input type="text" 
                               x-model="localSearch"
                               @input.debounce.500ms="$wire.set('search', localSearch)"
                               placeholder="ค้นหาด่วน (ชื่อร้าน, UID, ยอดเงิน)..." 
                               class="h-10 w-full rounded-[0.85rem] border border-[#e3e5e8] bg-[#f8fafb] pl-10 pr-3 text-sm font-bold text-[#162033] outline-none transition focus:border-discord-green dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white">
                    </div>

                    <div class="relative" wire:ignore wire:key="filter-status-box">
                        <select wire:model.live="workflow_status" class="w-full appearance-none rounded-[0.85rem] border border-[#e3e5e8] bg-[#f8fafb] px-3 py-2.5 text-sm font-bold text-[#162033] outline-none transition dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white">
                            <option value="">ทุกสถานะ</option>
                            @foreach($workflowOptions as $key => $label) 
                                @if($key !== \App\Models\Slip::WORKFLOW_ARCHIVED && $key !== \App\Models\Slip::WORKFLOW_PENDING)
                                    <option value="{{ $key }}">{{ $label }}</option> 
                                @endif
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#80848e]"></i>
                    </div>

                    <div class="relative" wire:ignore wire:key="filter-date-range">
                        <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-discord-green"></i>
                        <input type="text" 
                               id="date_range_picker" 
                               placeholder="ช่วงวันที่ พ.ศ. (เริ่ม — จบ)" 
                               class="date-be-range h-10 w-full rounded-[0.85rem] border border-[#e3e5e8] bg-[#f8fafb] pl-10 pr-3 text-sm font-bold text-[#162033] outline-none transition focus:border-discord-green dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white cursor-pointer">
                    </div>

                    <button wire:click="$set('search', ''); $set('workflow_status', ''); $set('date_from', ''); $set('date_to', ''); localSearch = ''" class="inline-flex h-10 items-center justify-center gap-2 rounded-[0.85rem] border border-rose-200 bg-rose-50 px-5 text-[10px] font-black uppercase tracking-[0.18em] text-rose-500 hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10" title="ล้างการค้นหา">
                        <i data-lucide="filter-x" class="h-4 w-4"></i> ล้าง
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="border-b border-[#e3e5e8] px-4 py-3 dark:border-[#313338] md:px-5">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <div class="inline-flex h-8 items-center rounded-[0.75rem] bg-[#f5f9f6] px-3 text-[10px] font-black uppercase tracking-[0.18em] text-[#23a559] dark:bg-[#1e1f22]"><span x-text="selected.length"></span> รายการที่เลือก</div>
                    <div class="flex items-center gap-1.5 ml-2">
                        <button wire:click="setBulkAction('mark_reviewed')" :disabled="selected.length === 0" class="inline-flex h-9 w-9 items-center justify-center rounded-[0.85rem] border border-black/5 bg-white transition hover:bg-[#f8f9fb] disabled:opacity-40 dark:border-white/5 dark:bg-[#1e1f22] dark:hover:bg-[#2b2d31]" title="ทำเครื่องหมายว่าแสกนแล้ว">
                            <i data-lucide="check-circle-2" class="h-4 w-4 text-discord-green"></i>
                        </button>
                        <button wire:click="setBulkAction('mark_approved')" :disabled="selected.length === 0" class="inline-flex h-9 w-9 items-center justify-center rounded-[0.85rem] border border-black/5 bg-white transition hover:bg-[#f8f9fb] disabled:opacity-40 dark:border-white/5 dark:bg-[#1e1f22] dark:hover:bg-[#2b2d31]" title="ยืนยันความถูกต้อง">
                            <i data-lucide="shield-check" class="h-4 w-4 text-blue-500"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button wire:click="setBulkAction('export')" :disabled="selected.length === 0" class="inline-flex h-10 items-center justify-center gap-2 rounded-[0.85rem] bg-indigo-600 px-6 text-[11px] font-black uppercase tracking-[0.2em] text-white transition hover:bg-indigo-700 disabled:opacity-40 shadow-lg shadow-indigo-500/20 group" title="ดาวน์โหลดไฟล์ Excel">
                        <i data-lucide="download" class="h-4 w-4 group-hover:translate-y-0.5 transition-transform"></i>
                        <span>Excel Report</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-2 py-1 md:px-4">
            <table class="min-w-full text-left text-sm" wire:loading.class="opacity-50 transition-opacity">
                <thead>
                    <tr class="border-b border-[#e3e5e8] text-[10px] font-black uppercase tracking-[0.22em] text-[#80848e] dark:border-[#313338]">
                        <th class="px-3 py-3 w-10">
                            <input type="checkbox" 
                                   @change="if($el.checked) { selected = @js($slips->pluck('id')->map(fn($id) => (string)$id)->toArray()) } else { selected = [] }"
                                   :checked="selected.length > 0 && selected.length === @js($slips->count())"
                                   class="h-4 w-4 rounded border-[#cfd4db] text-[#23a559] focus:ring-0 cursor-pointer">
                        </th>
                        <th class="px-3 py-3 cursor-pointer group" wire:click="sortBy('uid')">
                            <div class="flex items-center gap-1">
                                รายละเอียดสลิป
                                @if($sortField === 'uid')
                                    <i data-lucide="chevron-up" class="h-3.5 w-3.5 text-discord-green" :class="{ 'rotate-180': '{{ $sortDirection }}' === 'desc' }"></i>
                                @else
                                    <i data-lucide="chevrons-up-down" class="h-3 w-3 opacity-0 group-hover:opacity-50 transition-opacity"></i>
                                @endif
                            </div>
                        </th>
                        
                        <th class="px-3 py-3 cursor-pointer group" wire:click="sortBy('processed_at')">
                            <div class="flex items-center gap-1">
                                วันที่ในสลิป
                                @if($sortField === 'processed_at')
                                    <i data-lucide="chevron-up" class="h-3.5 w-3.5 text-discord-green" :class="{ 'rotate-180': '{{ $sortDirection }}' === 'desc' }"></i>
                                @else
                                    <i data-lucide="chevrons-up-down" class="h-3 w-3 opacity-0 group-hover:opacity-50 transition-opacity"></i>
                                @endif
                            </div>
                        </th>

                        <th class="px-3 py-3">ประมวลผลเมื่อ</th>
                        <th class="px-3 py-3">สถานะ</th>
                        <th class="px-3 py-3 text-right">ยอดเงินรวม</th>
                        <th class="px-3 py-3 text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e5e8] dark:divide-[#313338]">
                    @forelse($slips as $slip)
                        <tr wire:key="slip-row-{{ $slip->id }}" 
                            x-show="matchesSearch($el)"
                            class="transition hover:bg-[#fafcfa] dark:hover:bg-white/[0.02]" 
                            :class="selected.includes('{{ $slip->id }}') ? 'bg-emerald-50/60 dark:bg-emerald-500/5' : ''">
                            <td class="px-3 py-3 align-top">
                                <input type="checkbox" x-model="selected" value="{{ $slip->id }}" class="h-4 w-4 rounded border-[#cfd4db] text-[#23a559] focus:ring-0 cursor-pointer">
                            </td>
                            <td class="px-3 py-3 align-top">
                                <button type="button" wire:click="openSlipDetail('{{ $slip->uid }}')" class="flex min-w-[240px] items-start gap-3 text-left">
                                    <div class="h-10 w-10 overflow-hidden rounded-[0.8rem] border border-[#e3e5e8] bg-white shadow-sm dark:border-[#313338] dark:bg-[#1e1f22]">
                                        <img src="{{ asset('storage/' . $slip->image_path) }}" alt="slip" class="h-full w-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-black text-[#162033] dark:text-white">{{ $slip->display_shop }}</div>
                                        <div class="mt-1 flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.14em]">
                                            <span class="text-slate-400">{{ $slip->uid }}</span>
                                            <span class="text-slate-300">•</span>
                                            <span class="px-1.5 py-0.5 rounded bg-discord-green/10 text-discord-green text-[9px]">{{ optional($slip->template)->name ?? 'Unknown' }}</span>
                                            @if(!($slip->extracted_data['__metadata']['is_reliable'] ?? true))
                                                <span class="px-1.5 py-0.5 rounded bg-rose-500/10 text-rose-500 text-[9px] font-black flex items-center gap-1">
                                                    <i data-lucide="alert-circle" class="w-2.5 h-2.5"></i>
                                                    ต้องตรวจสอบ
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            </td>
                            <td class="px-3 py-3 align-top font-bold text-[#5c5e66] dark:text-[#b5bac1]">{{ $slip->display_date }}</td>
                            <td class="px-3 py-3 align-top font-bold text-[#5c5e66] dark:text-[#b5bac1]">{{ $slip->processed_at->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-3 align-top">
                                @php
                                    $statusColor = match($slip->workflow_status) {
                                        \App\Models\Slip::WORKFLOW_EXPORTED => 'bg-indigo-500/10 text-indigo-500',
                                        \App\Models\Slip::WORKFLOW_APPROVED => 'bg-blue-500/10 text-blue-500',
                                        \App\Models\Slip::WORKFLOW_REVIEWED => 'bg-discord-green/10 text-discord-green',
                                        default => 'bg-[#f8fafb] dark:bg-[#1e1f22] text-slate-400'
                                    };
                                @endphp
                                <span class="text-[10px] font-black uppercase tracking-[0.12em] px-2 py-1 {{ $statusColor }} rounded-[0.8rem]">{{ $workflowOptions[$slip->workflow_status] ?? $slip->workflow_status }}</span>
                            </td>
                            <td class="px-3 py-3 align-top text-right text-sm font-black text-[#162033] dark:text-white">THB {{ number_format($slip->display_amount, 2) }}</td>
                            <td class="px-3 py-3 align-top text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openSlipDetail('{{ $slip->uid }}')" class="p-1 text-slate-400 hover:text-[#162033] dark:hover:text-white" title="ดูรายละเอียด"><i data-lucide="eye" class="h-4 w-4"></i></button>
                                    <button wire:click="reprocessSlip({{ $slip->id }})" class="p-1 text-slate-400 hover:text-discord-green" title="แสกนซ้ำด้วย AI"><i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i></button>
                                    <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'slips/edit/' . $slip->uid) }}" class="p-1 text-slate-400 hover:text-blue-500" title="แก้ไขข้อมูล"><i data-lucide="edit-3" class="h-4 w-4"></i></a>
                                    <button wire:click="deleteSlip({{ $slip->id }})" 
                                            wire:confirm="คุณแน่ใจหรือไม่ว่าต้องการลบสลิปนี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้"
                                            class="p-1 text-slate-400 hover:text-rose-500" title="ลบทิ้ง"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-14 text-center"><div class="text-[10px] font-black uppercase tracking-[0.2em] text-[#80848e]">ไม่พบข้อมูลสลิป</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#e3e5e8] px-4 py-4 dark:border-[#313338] md:px-5">{{ $slips->links() }}</div>
    </section>

    <!-- Scan Modal -->
    @if($scanModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8 sm:py-10">
            <div class="fixed inset-0 bg-white/5 dark:bg-black/5 backdrop-blur-xl" wire:click="closeScanModal"></div>
            <div class="relative z-10 w-full max-w-lg bg-white dark:bg-[#313338] rounded-[1.5rem] shadow-2xl p-6 max-h-[90vh] overflow-y-auto border border-white/20 dark:border-white/10">
                <button type="button" wire:click="closeScanModal" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] text-[#5c5e66] hover:text-discord-red transition-colors z-20">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
                <h3 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-tight mb-1">Process Receipt</h3>
                <p class="text-[11px] text-[#5c5e66] dark:text-[#b5bac1] leading-relaxed mb-5 font-medium">SmartBill จะวิเคราะห์สลิปและดึงข้อมูลให้อัตโนมัติตามโปรไฟล์ที่คุณเลือก</p>
                
                <form wire:submit.prevent="submitScan" class="space-y-6">
                    <!-- Processing Overlay -->
                    @if($isScanning)
                        <div class="absolute inset-0 z-[60] flex flex-col items-center justify-center bg-white/95 dark:bg-[#313338]/95 backdrop-blur-md rounded-[1.5rem] transition-all duration-500">
                            <div class="relative mb-6">
                                <div class="w-20 h-20 rounded-full border-4 border-discord-green/20 border-t-discord-green animate-spin"></div>
                                <div class="absolute inset-0 flex items-center justify-center"><i data-lucide="zap" class="w-8 h-8 text-discord-green animate-pulse"></i></div>
                            </div>
                            <h4 class="text-xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight mb-2">AI Processing</h4>
                            <p class="text-sm font-bold text-discord-green animate-pulse">{{ $scanStatus }}</p>
                            <div class="mt-8 w-48 h-1.5 bg-[#f2f3f5] dark:bg-[#1e1f22] rounded-full overflow-hidden">
                                <div class="h-full bg-discord-green transition-all duration-500" style="width: {{ ($totalScanCount > 0) ? ($currentScanCount / $totalScanCount) * 100 : 0 }}%"></div>
                            </div>
                            <p class="mt-3 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">{{ $currentScanCount }} / {{ $totalScanCount }} รายการสำเร็จ</p>
                        </div>
                    @endif

                    <!-- Image Upload Section -->
                    <div class="space-y-1.5 w-full" x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true; progress = 0" x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
                        <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">รูปภาพสลิป (เลือกได้หลายรูป) *</label>
                        
                        <div x-show="isUploading" class="mb-2 h-1 w-full bg-[#f2f3f5] dark:bg-[#1e1f22] rounded-full overflow-hidden">
                            <div class="h-full bg-discord-green transition-all duration-300" :style="`width: ${progress}%`"></div>
                        </div>

                        <div class="relative mt-1">
                            @if($fileQueue)
                                <div class="w-full p-2 rounded-[24px] border-2 border-discord-green bg-black/5 relative">
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-[200px] overflow-y-auto p-1">
                                        @foreach($fileQueue as $index => $image)
                                            <div wire:key="queue-file-{{ $image }}-{{ $loop->index }}" class="aspect-square rounded-[12px] overflow-hidden border border-white/20 shadow-sm relative group/item">
                                                <img src="{{ asset('storage/livewire-tmp/' . $image) }}" class="w-full h-full object-cover">
                                                
                                                <!-- Increased click area for remove button -->
                                                <div class="absolute top-0 right-0 p-1.5 z-30">
                                                    <button type="button" 
                                                            x-on:click.stop="$wire.removeFromQueue({{ $index }})"
                                                            class="w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-rose-600 transition-transform active:scale-90">
                                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                        <label class="aspect-square rounded-[12px] bg-discord-green/10 flex flex-col items-center justify-center text-discord-green border border-dashed border-discord-green/30 cursor-pointer hover:bg-discord-green/20 transition-colors">
                                            <i data-lucide="plus" class="w-4 h-4"></i><span class="text-[8px] font-black mt-1">เพิ่มรูป</span>
                                            <input type="file" wire:model="uploadTemp" accept="image/jpeg,image/png,image/webp" multiple class="hidden">
                                        </label>
                                    </div>
                                    <div class="mt-2 py-1 px-3 flex items-center justify-between border-t border-black/5 dark:border-white/5 pt-2 relative z-20">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">เลือกแล้ว {{ count($fileQueue) }} ไฟล์</p>
                                        <button type="button" x-on:click="$wire.clearFileQueue()" class="text-[9px] font-black text-rose-500 uppercase tracking-widest hover:underline cursor-pointer">ล้างทั้งหมด</button>
                                    </div>
                                </div>
                            @else
                                <div class="relative group">
                                    <input type="file" wire:model="uploadTemp" accept="image/jpeg,image/png,image/webp" multiple required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="w-full min-h-[120px] px-5 py-6 bg-[#f2f3f5] dark:bg-[#1e1f22] border-2 border-dashed border-[#e3e5e8] dark:border-[#313338] rounded-[24px] flex flex-col items-center justify-center text-center transition-all group-hover:border-discord-green/50 group-hover:bg-discord-green/5">
                                        <div class="w-10 h-10 rounded-full bg-white dark:bg-[#2b2d31] flex items-center justify-center shadow-sm mb-3 text-discord-green"><i data-lucide="image-plus" class="w-4 h-4"></i></div>
                                        <p class="text-xs font-bold text-[#1e1f22] dark:text-white mb-1">คลิกเพื่ออัปโหลดสลิป</p>
                                        <p class="text-[10px] font-medium text-[#80848e]">เลือกได้หลายรูป JPG, PNG, WEBP</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @error('uploadTemp.*') <span class="text-[10px] font-bold text-rose-500 pl-2">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5 relative">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">โปรไฟล์การดึงข้อมูล *</label>
                            <select wire:model="scanForm.template_id" required class="h-10 w-full px-3 bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[0.85rem] text-[#1e1f22] dark:text-white font-bold text-xs focus:ring-2 focus:ring-discord-green/50 appearance-none outline-none">
                                <option value="auto">ตรวจจับร้านค้าอัตโนมัติ</option>
                                @foreach($templates as $template) <option value="{{ $template->id }}">{{ $template->name }}</option> @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-4 top-[38px] w-4 h-4 text-[#80848e] pointer-events-none"></i>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">ป้ายกำกับ (Labels)</label>
                            <input type="text" wire:model="scanForm.labels" placeholder="เช่น ด่วน, อาหาร" class="h-10 w-full rounded-[0.85rem] border-0 bg-[#f2f3f5] dark:bg-[#1e1f22] px-3 text-xs font-bold text-[#1e1f22] dark:text-white outline-none focus:ring-2 focus:ring-discord-green/50">
                        </div>
                    </div>

                    @if (session()->has('scan_error'))
                        <div class="p-3 rounded-[0.85rem] bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20"><p class="text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest mb-1">AI Error</p><p class="text-xs font-bold text-rose-700 dark:text-rose-300">{{ session('scan_error') }}</p></div>
                    @endif

                    <div class="pt-1 flex gap-2">
                        <button type="button" wire:click="closeScanModal" class="flex-1 py-3 bg-[#f2f3f5] dark:bg-[#2b2d31] text-[#1e1f22] dark:text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[0.85rem]">ยกเลิก</button>
                        <button type="submit" wire:loading.attr="disabled" class="flex-1 py-3 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[0.85rem] transition-all shadow-lg disabled:opacity-50 flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="submitScan">เริ่มแสกน</span>
                            <span wire:loading wire:target="submitScan" class="flex items-center gap-2"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span x-text="$wire.scanStatus"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Slip Detail Modal -->
    <div x-data="{ open: @entangle('detailOpen') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
        <div class="fixed inset-0 bg-white/5 dark:bg-black/5 backdrop-blur-xl" @click="open = false" x-transition.opacity></div>
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             class="relative z-10 flex h-full w-full max-w-2xl max-h-[90vh] flex-col bg-white rounded-[1.5rem] shadow-2xl dark:bg-[#2b2d31] border border-white/20 dark:border-white/10 overflow-hidden">
            
            <div class="flex items-center justify-between border-b border-[#e3e5e8] px-6 py-4 dark:border-[#313338]">
                <h3 class="text-lg font-black text-[#162033] dark:text-white">{{ $activeSlip?->display_shop ?? 'รายละเอียดสลิป' }}</h3>
                <button @click="open = false" class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                @if($activeSlip)
                    <div class="grid gap-8">
                        <div class="overflow-hidden rounded-[1.25rem] border border-[#e3e5e8] bg-white shadow-sm dark:border-[#313338] dark:bg-[#1e1f22]">
                            <img src="{{ asset('storage/' . $activeSlip->image_path) }}" class="w-full h-auto object-contain max-h-[60vh]">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-[1rem] bg-[#f8fafb] p-4 dark:bg-[#1e1f22]">
                                <div class="text-[10px] font-black uppercase tracking-[0.16em] text-[#80848e]">วันที่ในสลิป</div>
                                <div class="mt-1.5 text-sm font-bold text-[#162033] dark:text-white">
                                    {{ $activeSlip->display_date_be }}
                                </div>
                            </div>
                            <div class="rounded-[1rem] bg-[#f8fafb] p-4 dark:bg-[#1e1f22]">
                                <div class="text-[10px] font-black uppercase tracking-[0.16em] text-[#80848e]">สถานะ Workflow</div>
                                <div class="mt-1.5 inline-flex text-xs font-black text-[#162033] dark:text-white uppercase px-3 py-1 bg-white dark:bg-[#2b2d31] rounded-full border border-[#e3e5e8] dark:border-[#313338]">
                                    {{ $workflowOptions[$activeSlip->workflow_status] ?? $activeSlip->workflow_status }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-[#80848e]">ข้อมูลที่ดึงได้จาก AI</h4>
                                <span class="h-px flex-1 bg-[#e3e5e8] dark:bg-[#313338] ml-4"></span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($activeSlip->display_fields as $field)
                                    <div class="rounded-[1rem] border border-[#e3e5e8] p-4 bg-white dark:bg-transparent dark:border-[#313338]">
                                        <div class="text-[10px] font-black uppercase tracking-widest text-[#80848e] mb-1">{{ $field['label'] }}</div>
                                        <div class="text-xs font-bold text-[#1e1f22] dark:text-white leading-relaxed whitespace-pre-line break-words">{{ $field['value'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-[#f8fafb] dark:bg-[#1e1f22] px-6 py-4 border-t border-[#e3e5e8] dark:border-[#313338]">
                <div class="flex justify-end gap-3">
                    <button @click="open = false" class="px-6 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#162033] dark:hover:text-white transition-colors">ปิดหน้าต่าง</button>
                    
                    <button wire:click="reprocessSlip({{ $activeSlip?->id }})" 
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-[#f2f3f5] dark:bg-[#2b2d31] text-[11px] font-black uppercase tracking-[0.18em] text-slate-600 dark:text-slate-300 rounded-[0.85rem] hover:bg-discord-green hover:text-white transition-all flex items-center gap-2 disabled:opacity-50">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5" wire:loading.class="animate-spin" wire:target="reprocessSlip"></i>
                        <span wire:loading.remove wire:target="reprocessSlip">แสกนซ้ำ</span>
                        <span wire:loading wire:target="reprocessSlip">กำลังประมวลผล...</span>
                    </button>
                    <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'slips/edit/' . $activeSlip?->uid) }}" class="px-6 py-2 bg-[#162033] dark:bg-discord-green text-white text-[11px] font-black uppercase tracking-[0.18em] rounded-[0.85rem] shadow-lg hover:opacity-90 transition-all flex items-center gap-2">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        แก้ไขข้อมูล
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Flatpickr Premium B.E. Styles */
        .flatpickr-be-year-select {
            cursor: pointer;
            outline: none;
            transition: all 0.2s;
            border-radius: 6px;
            padding: 2px 8px;
        }
        .flatpickr-be-year-select:hover {
            background: rgba(35, 165, 89, 0.1);
            color: #23a559 !important;
        }
        .dark .flatpickr-calendar { background: #2b2d31; border-color: rgba(255,255,255,0.1); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.4); }
        .dark .flatpickr-day { color: #dbdee1; }
        .dark .flatpickr-day.today { border-color: #23a559; }
        .dark .flatpickr-day.selected { background: #23a559; border-color: #23a559; }
        .dark .flatpickr-current-month, .dark .flatpickr-month { color: white; fill: white; }
        .dark .flatpickr-be-year-select { color: white; background: #1e1f22; }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true, didOpen: (toast) => { toast.addEventListener('mouseenter', Swal.stopTimer); toast.addEventListener('mouseleave', Swal.resumeTimer); } });
            Livewire.on('notify', (event) => { const data = Array.isArray(event) ? event[0] : event; Swal.close(); if (typeof data === 'string') { Toast.fire({ icon: 'success', title: data }); } else { Toast.fire({ icon: data.type || 'success', title: data.title || 'Notification', text: data.message || '', timer: data.loading ? 15000 : 3000, timerProgressBar: !data.loading, didOpen: (toast) => { if (data.loading) Swal.showLoading(); } }); } });
            Livewire.on('trigger-next-scan', (event) => { const index = event.index ?? event[0].index; setTimeout(() => { @this.processSingleReceipt(index); }, 600); });
            Livewire.on('trigger-download', (event) => { const url = event.url ?? event[0].url; window.location.href = url; });
            
            const initFlatpickr = () => {
                // Single Date Pickers (if any left)
                document.querySelectorAll('.date-be').forEach(el => {
                    if (el._flatpickr) el._flatpickr.destroy();
                    flatpickr(el, {
                        locale: 'th',
                        altInput: true,
                        altFormat: 'j F Y',
                        dateFormat: 'Y-m-d',
                        formatDate(date, format) {
                            if (format === 'j F Y') {
                                const months = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
                                return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear() + 543}`;
                            }
                            return flatpickr.formatDate(date, format);
                        },
                        onReady: (dObj, dStr, instance) => updateYearToBE(instance),
                        onMonthChange: (dObj, dStr, instance) => updateYearToBE(instance),
                        onYearChange: (dObj, dStr, instance) => updateYearToBE(instance),
                        onChange: (selectedDates, dateStr) => {
                            el.dispatchEvent(new Event('input'));
                        }
                    });
                });

                // Range Date Picker (Direct)
                document.querySelectorAll('.date-be-range').forEach(el => {
                    if (el._flatpickr) el._flatpickr.destroy();
                    flatpickr(el, {
                        mode: 'range',
                        locale: 'th',
                        altInput: true,
                        altFormat: 'j M y',
                        dateFormat: 'Y-m-d',
                        formatDate(date, format) {
                            const months = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
                            return `${date.getDate()} ${months[date.getMonth()]} ${String(date.getFullYear() + 543).slice(-2)}`;
                        },
                        onReady: (dObj, dStr, instance) => updateYearToBE(instance),
                        onMonthChange: (dObj, dStr, instance) => updateYearToBE(instance),
                        onYearChange: (dObj, dStr, instance) => updateYearToBE(instance),
                        onClose: (selectedDates) => {
                            if (selectedDates.length === 2) {
                                const start = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                                const end = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
                                @this.set('date_from', start);
                                @this.set('date_to', end);
                            }
                        }
                    });
                });
            };

            const updateYearToBE = (instance) => {
                const yearInput = instance.currentYearElement;
                if (yearInput) {
                    const adYear = instance.currentYear;
                    let beSelect = instance.calendarContainer.querySelector('.numInputWrapper .flatpickr-be-year-select');
                    if (!beSelect) {
                        beSelect = document.createElement('select');
                        beSelect.className = 'flatpickr-be-year-select h-full border-0 bg-transparent text-sm font-bold text-[#162033] dark:text-white focus:ring-0 cursor-pointer py-0 pr-6 pl-1';
                        beSelect.style.appearance = 'none';
                        const currentBE = new Date().getFullYear() + 543;
                        for (let y = currentBE + 1; y >= currentBE - 10; y--) {
                            const opt = document.createElement('option');
                            opt.value = y - 543;
                            opt.textContent = y;
                            opt.className = 'bg-white dark:bg-[#2b2d31]';
                            beSelect.appendChild(opt);
                        }
                        yearInput.style.display = 'none';
                        yearInput.parentNode.insertBefore(beSelect, yearInput);
                        beSelect.addEventListener('change', (e) => {
                            instance.changeYear(parseInt(e.target.value));
                        });
                    }
                    beSelect.value = adYear;
                }
            };

            initFlatpickr();
            Livewire.hook('morph.updated', (el, component) => { initFlatpickr(); });
        });
    </script>
</div>