<div id="slip-table-container">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-[11px] font-bold text-[#1e1f22] dark:text-[#b5bac1]">
            <thead class="border-y border-black/[0.04] text-[10px] font-black uppercase tracking-widest text-[#80848e] dark:border-white/[0.04]">
                <tr>
                    <th class="px-4 py-4 w-10">
                        <input type="checkbox" @click="toggleSelectAll()" :checked="selectedSlips.length === document.querySelectorAll('.slip-checkbox').length && selectedSlips.length > 0" class="h-4 w-4 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm transition hover:border-discord-green/50">
                    </th>
                    <th class="px-4 py-4">รายละเอียดสลิป</th>
                    <th class="px-4 py-4 text-center">วันที่ในสลิป <i data-lucide="chevron-down" class="inline h-3 w-3 text-discord-green"></i></th>
                    <th class="px-4 py-4 text-center">ประมวลผลเมื่อ</th>
                    <th class="px-4 py-4 text-center">สถานะ</th>
                    <th class="px-4 py-4 text-right">ยอดเงินรวม</th>
                    <th class="px-4 py-4 text-right">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-black/[0.04] dark:divide-white/[0.04]">
                @forelse($slips as $slip)
                    <tr class="group transition hover:bg-[#fafcfa] dark:hover:bg-white/[0.02]">
                        <td class="px-4 py-5 align-top">
                            <input type="checkbox" x-model="selectedSlips" value="{{ $slip->id }}" class="slip-checkbox h-4 w-4 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm transition hover:border-discord-green/50">
                        </td>
                        <td class="px-4 py-5 align-top">
                            <div class="flex items-start gap-4">
                                <div class="h-[52px] w-[52px] shrink-0 overflow-hidden rounded-[1rem] border border-black/5 shadow-sm dark:border-white/5 bg-white dark:bg-[#1e1f22]">
                                    <img src="{{ asset('storage/' . $slip->image_path) }}" class="h-full w-full object-cover opacity-90 transition-opacity group-hover:opacity-100">
                                </div>
                                <div class="flex flex-col pt-0.5">
                                    <span class="text-[13px] font-black leading-tight text-[#1e1f22] dark:text-white transition-colors group-hover:text-[#4f86f7]">{{ $slip->display_shop }}</span>
                                    <div class="mt-1 flex flex-wrap items-center gap-2">
                                        <span class="text-[10px] font-black tracking-widest text-[#80848e]">{{ $slip->uid }}</span>
                                        <span class="h-1 w-1 rounded-full bg-[#e3e5e8]"></span>
                                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-1.5 py-0.5 text-[8px] font-black uppercase tracking-widest text-emerald-600 dark:bg-emerald-500/10">Auto: General Retail</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-5 align-top text-center text-[11px] font-black text-[#5c5e66] dark:text-[#b5bac1] pt-6">
                            {{ $slip->display_date }}
                        </td>
                        <td class="px-4 py-5 align-top text-center text-[11px] font-black text-[#5c5e66] dark:text-[#b5bac1] pt-6">
                            {{ $slip->processed_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-5 align-top text-center pt-5">
                            @php
                                $statusClasses = match($slip->workflow_status) {
                                    'reviewed' => 'bg-[#e0f5ea] text-[#12a170] dark:bg-emerald-500/10 dark:text-emerald-400',
                                    'exported' => 'bg-[#f2f7ff] text-[#4f86f7] dark:bg-blue-500/10 dark:text-[#4f86f7]',
                                    default => 'bg-slate-50 text-slate-600 dark:bg-slate-500/10'
                                };
                                $statusLabel = match($slip->workflow_status) {
                                    'reviewed' => 'แสกนแล้ว (AI)',
                                    'exported' => 'ส่งออก Excel แล้ว',
                                    default => $slip->workflow_status
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-[9px] font-black uppercase tracking-widest {{ $statusClasses }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-5 align-top text-right pt-6">
                            <span class="text-[13px] font-black tracking-tight text-[#1e1f22] dark:text-white">THB {{ number_format($slip->display_amount, 2) }}</span>
                        </td>
                        <td class="px-4 py-5 align-top text-right pt-5">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <button class="flex h-8 w-8 items-center justify-center rounded-full text-[#80848e] transition hover:bg-black/5 hover:text-[#1e1f22] dark:hover:bg-white/5 dark:hover:text-white">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </button>
                                <button class="flex h-8 w-8 items-center justify-center rounded-full text-[#80848e] transition hover:bg-black/5 hover:text-[#1e1f22] dark:hover:bg-white/5 dark:hover:text-white">
                                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                </button>
                                <button class="flex h-8 w-8 items-center justify-center rounded-full text-[#80848e] transition hover:bg-black/5 hover:text-[#1e1f22] dark:hover:bg-white/5 dark:hover:text-white">
                                    <i data-lucide="edit-3" class="h-4 w-4"></i>
                                </button>
                                <button @click="deleteSlip(slip.id)" class="flex h-8 w-8 items-center justify-center rounded-full text-discord-red transition hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-24 text-center">
                            <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-[2rem] bg-[#f8fafb] border border-black/[0.02] shadow-sm dark:bg-[#1e1f22] dark:border-white/5">
                                <i data-lucide="receipt" class="h-8 w-8 text-[#80848e]"></i>
                            </div>
                            <h3 class="text-[13px] font-black text-[#1e1f22] dark:text-white">ไม่พบสลิปในโฟลเดอร์นี้</h3>
                            <p class="mt-1 text-xs font-bold text-[#80848e]">ลองค้นหาด้วยคำอื่น หรือกด Scan Receipt เพื่อเพิ่มสลิปใหม่</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($slips->hasPages())
        <div class="mt-6 px-4">
            {{ $slips->links() }}
        </div>
    @endif
</div>
