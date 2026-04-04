<div class="space-y-5 pb-20">
    <!-- Header Summary -->
    @if($batch_id)
        @php $currentBatch = $batches->firstWhere('id', $batch_id); @endphp
        @if($currentBatch)
            <section class="rounded-[1rem] border border-[#23a559]/10 bg-white px-4 py-3 shadow-sm dark:border-[#23a559]/20 dark:bg-[#2b2d31]">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 text-[9px] font-black uppercase tracking-[0.14em] text-[#80848e]">
                            <span class="inline-flex rounded-full bg-[#eef8f1] px-2.5 py-1 text-[#23a559] dark:bg-[#23a559]/10 dark:text-[#7fe0a2]">Collection Focus</span>
                        </div>
                        <h2 class="mt-2 text-base font-black tracking-tight text-[#162033] dark:text-white">{{ $currentBatch->name }}</h2>
                        <p class="mt-1 max-w-3xl truncate text-xs font-bold text-slate-500 dark:text-slate-300">{{ $currentBatch->note ?: 'Grouped receipts for better organization.' }}</p>
                    </div>
                    <button wire:click="$set('batch_id', '')" class="inline-flex h-9 items-center justify-center gap-2 rounded-[0.85rem] border border-[#e3e5e8] bg-white px-3 text-[9px] font-black uppercase tracking-[0.14em] text-[#5c5e66] transition hover:text-[#162033] dark:border-[#313338] dark:bg-[#1e1f22] dark:text-[#b5bac1] dark:hover:text-white">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i> Show All Slips
                    </button>
                </div>
            </section>
        @endif
    @endif

    <section class="rounded-[1rem] border border-black/5 bg-white shadow-sm dark:border-white/10 dark:bg-[#2b2d31]">
        <!-- View Mode Toggle -->
        <div class="flex items-center gap-1 border-b border-[#e3e5e8] px-4 py-2 dark:border-[#313338]">
            <button wire:click="setArchiveScope('active')" 
                    class="h-8 px-4 text-[10px] font-black uppercase tracking-widest rounded-full transition-all {{ $archive_scope === 'active' ? 'bg-discord-green text-white shadow-md' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
                Active Inbox
            </button>
            <button wire:click="setArchiveScope('archived')" 
                    class="h-8 px-4 text-[10px] font-black uppercase tracking-widest rounded-full transition-all {{ $archive_scope === 'archived' ? 'bg-[#ed4245] text-white shadow-md' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }}">
                Historical Archive
            </button>

            <div class="ml-auto flex items-center gap-2">
                @if($archive_scope === 'active')
                    <button type="button" wire:click="openBatchModal" class="inline-flex h-8 items-center justify-center gap-2 rounded-[0.75rem] border border-[#e3e5e8] bg-white px-3 text-[9px] font-black uppercase tracking-[0.14em] text-[#5c5e66] transition hover:text-discord-green dark:border-[#313338] dark:bg-[#1e1f22] dark:text-[#b5bac1] dark:hover:text-[#7fe0a2]">
                        <i data-lucide="folder-plus" class="h-3.5 w-3.5"></i> New Collection
                    </button>
                    <button type="button" x-on:click="$dispatch('gentle-open-scan')" class="inline-flex h-8 items-center justify-center gap-2 rounded-[0.85rem] bg-discord-green px-4 text-[9px] font-black uppercase tracking-[0.18em] text-white transition hover:bg-[#1f8b4c] shadow-lg shadow-green-500/10">
                        <i data-lucide="scan-line" class="w-3.5 h-3.5"></i>
                        <span>Scan Receipt</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Search & Quick Filters -->
        <div class="border-b border-[#e3e5e8] px-4 py-3 dark:border-[#313338] md:px-5 md:py-4">
            <div class="grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1.6fr)_220px_220px_auto]">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[#80848e]"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search UID, profile, or collection..." class="h-10 w-full rounded-[0.85rem] border border-[#e3e5e8] bg-[#f8fafb] pl-10 pr-3 text-sm font-bold text-[#162033] outline-none transition focus:border-discord-green dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white">
                </div>
                <div class="relative">
                    <select wire:model.live="batch_id" class="w-full appearance-none rounded-[0.85rem] border border-[#e3e5e8] bg-[#f8fafb] px-3 py-2.5 text-sm font-bold text-[#162033] outline-none transition dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white">
                        <option value="">All Collections</option>
                        @foreach($batches as $batch) <option value="{{ $batch->id }}">{{ $batch->name }}</option> @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#80848e]"></i>
                </div>
                <div class="relative">
                    <select wire:model.live="workflow_status" class="w-full appearance-none rounded-[0.85rem] border border-[#e3e5e8] bg-[#f8fafb] px-3 py-2.5 text-sm font-bold text-[#162033] outline-none transition dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white">
                        <option value="">All Statuses</option>
                        @foreach($workflowOptions as $key => $label) <option value="{{ $key }}">{{ $label }}</option> @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#80848e]"></i>
                </div>
                <button wire:click="$set('search', ''); $set('batch_id', ''); $set('workflow_status', '')" class="inline-flex h-9 items-center justify-center gap-2 rounded-[0.85rem] border border-rose-200 bg-rose-50 px-3 text-[10px] font-black uppercase tracking-[0.18em] text-rose-500 hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10">
                    <i data-lucide="filter-x" class="h-4 w-4"></i> Clear
                </button>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="border-b border-[#e3e5e8] px-4 py-3 dark:border-[#313338] md:px-5">
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex h-8 items-center rounded-[0.75rem] bg-[#f5f9f6] px-3 text-[10px] font-black uppercase tracking-[0.18em] text-[#23a559] dark:bg-[#1e1f22]">{{ count($selectedIds) }} Selected</div>
                <div class="flex flex-wrap items-center gap-1.5 ml-2">
                    <button wire:click="setBulkAction('mark_reviewed')" @disabled(count($selectedIds) === 0) class="inline-flex h-9 w-9 items-center justify-center rounded-[0.85rem] border border-black/5 bg-white transition hover:bg-[#f8f9fb] disabled:opacity-40 dark:border-white/5 dark:bg-[#1e1f22] dark:hover:bg-[#2b2d31]" title="Mark Reviewed">
                        <i data-lucide="check-circle-2" class="h-4 w-4 text-discord-green"></i>
                    </button>
                    <button wire:click="setBulkAction('mark_approved')" @disabled(count($selectedIds) === 0) class="inline-flex h-9 w-9 items-center justify-center rounded-[0.85rem] border border-black/5 bg-white transition hover:bg-[#f8f9fb] disabled:opacity-40 dark:border-white/5 dark:bg-[#1e1f22] dark:hover:bg-[#2b2d31]" title="Mark Approved">
                        <i data-lucide="shield-check" class="h-4 w-4 text-blue-500"></i>
                    </button>
                    @if($archive_scope === 'archived')
                        <button wire:click="setBulkAction('restore')" @disabled(count($selectedIds) === 0) class="inline-flex h-9 w-9 items-center justify-center rounded-[0.85rem] border border-black/5 bg-white transition hover:bg-[#f8f9fb] disabled:opacity-40 dark:border-white/5 dark:bg-[#1e1f22] dark:hover:bg-[#2b2d31]" title="Restore">
                            <i data-lucide="archive-restore" class="h-4 w-4 text-amber-500"></i>
                        </button>
                    @else
                        <button wire:click="setBulkAction('archive')" @disabled(count($selectedIds) === 0) class="inline-flex h-9 w-9 items-center justify-center rounded-[0.85rem] border border-black/5 bg-white transition hover:bg-[#f8f9fb] disabled:opacity-40 dark:border-white/5 dark:bg-[#1e1f22] dark:hover:bg-[#2b2d31]" title="Archive">
                            <i data-lucide="archive" class="h-4 w-4 text-slate-400"></i>
                        </button>
                    @endif
                    <div class="w-px h-6 bg-[#e3e5e8] dark:bg-[#313338] mx-1"></div>
                    <button wire:click="setBulkAction('export')" @disabled(count($selectedIds) === 0) class="inline-flex h-9 w-9 items-center justify-center rounded-[0.85rem] border border-black/5 bg-white transition hover:bg-[#f8f9fb] disabled:opacity-40 dark:border-white/5 dark:bg-[#1e1f22] dark:hover:bg-[#2b2d31]" title="Export Selected">
                        <i data-lucide="download" class="h-4 w-4 text-indigo-500"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-2 py-1 md:px-4">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-[#e3e5e8] text-[10px] font-black uppercase tracking-[0.22em] text-[#80848e] dark:border-[#313338]">
                        <th class="px-3 py-3 w-10"><input type="checkbox" @click="$wire.toggleAll(@js($slips->pluck('id')->toArray()))" class="h-4 w-4 rounded border-[#cfd4db] text-[#23a559]"></th>
                        <th class="px-3 py-3 cursor-pointer group" wire:click="sortBy('uid')">
                            <div class="flex items-center gap-1">
                                Source
                                @if($sortField === 'uid')
                                    <i data-lucide="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="h-3.5 w-3.5 text-discord-green"></i>
                                @else
                                    <i data-lucide="chevrons-up-down" class="h-3 w-3 opacity-0 group-hover:opacity-50 transition-opacity"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-3 py-3">Collection</th>
                        
                        <th class="px-3 py-3 cursor-pointer group" wire:click="sortBy('processed_at')">
                            <div class="flex items-center gap-1">
                                Receipt Date
                                @if($sortField === 'processed_at')
                                    <i data-lucide="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="h-3.5 w-3.5 text-discord-green"></i>
                                @else
                                    <i data-lucide="chevrons-up-down" class="h-3 w-3 opacity-0 group-hover:opacity-50 transition-opacity"></i>
                                @endif
                            </div>
                        </th>

                        <th class="px-3 py-3">Processed</th>
                        <th class="px-3 py-3">Workflow</th>
                        <th class="px-3 py-3 text-right">Amount</th>
                        <th class="px-3 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e5e8] dark:divide-[#313338]">
                    @forelse($slips as $slip)
                        <tr class="transition hover:bg-[#fafcfa] dark:hover:bg-white/[0.02] {{ in_array($slip->id, $selectedIds) ? 'bg-emerald-50/60 dark:bg-emerald-500/5' : '' }}">
                            <td class="px-3 py-3 align-top"><input type="checkbox" wire:model.live="selectedIds" value="{{ $slip->id }}" class="h-4 w-4 rounded border-[#cfd4db] text-[#23a559]"></td>
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
                                                    Check Info
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            </td>
                            <td class="px-3 py-3 align-top"><span class="inline-flex rounded-full bg-[#f3f7ff] px-2.5 py-1 text-[9px] font-black uppercase tracking-[0.16em] text-[#4f7cff] dark:bg-white/[0.06] dark:text-[#a8bcff]">{{ $slip->batch?->name ?? 'Inbox' }}</span></td>
                            <td class="px-3 py-3 align-top font-bold text-[#5c5e66] dark:text-[#b5bac1]">{{ $slip->display_date }}</td>
                            <td class="px-3 py-3 align-top font-bold text-[#5c5e66] dark:text-[#b5bac1]">{{ $slip->processed_at->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-3 align-top"><span class="text-[10px] font-black uppercase tracking-[0.12em] text-[#162033] dark:text-white px-2 py-1 bg-[#f8fafb] dark:bg-[#1e1f22] rounded-[0.8rem]">{{ $workflowOptions[$slip->workflow_status] ?? $slip->workflow_status }}</span></td>
                            <td class="px-3 py-3 align-top text-right text-sm font-black text-[#162033] dark:text-white">THB {{ number_format($slip->display_amount, 2) }}</td>
                            <td class="px-3 py-3 align-top text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openSlipDetail('{{ $slip->uid }}')" class="p-1 text-slate-400 hover:text-[#162033] dark:hover:text-white" title="View"><i data-lucide="eye" class="h-4 w-4"></i></button>
                                    @if($archive_scope === 'active')
                                        <button wire:click="reprocessSlip({{ $slip->id }})" class="p-1 text-slate-400 hover:text-discord-green" title="Re-scan with AI"><i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i></button>
                                        <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'slips/edit/' . $slip->uid) }}" class="p-1 text-slate-400 hover:text-blue-500" title="Edit"><i data-lucide="edit-3" class="h-4 w-4"></i></a>
                                        <button wire:click="toggleArchive({{ $slip->id }}, true)" class="p-1 text-slate-400 hover:text-amber-500" title="Archive"><i data-lucide="archive" class="h-4 w-4"></i></button>
                                    @else
                                        <button wire:click="toggleArchive({{ $slip->id }}, false)" class="p-1 text-slate-400 hover:text-discord-green" title="Restore"><i data-lucide="archive-restore" class="h-4 w-4"></i></button>
                                    @endif
                                    <button wire:click="deleteSlip({{ $slip->id }})" 
                                            wire:confirm="Are you sure you want to delete this slip? This action cannot be undone."
                                            class="p-1 text-slate-400 hover:text-rose-500" title="Delete"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-14 text-center"><div class="text-[10px] font-black uppercase tracking-[0.2em] text-[#80848e]">No slips found</div></td></tr>
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
                <p class="text-[11px] text-[#5c5e66] dark:text-[#b5bac1] leading-relaxed mb-5 font-medium">Assign the scan to a collection now, add labels if needed, and SmartBill will keep the queue organized for review.</p>
                
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
                            <p class="mt-3 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">{{ $currentScanCount }} / {{ $totalScanCount }} Completed</p>
                        </div>
                    @endif

                    <!-- Image Upload Section -->
                    <div class="space-y-1.5 w-full" x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true; progress = 0" x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
                        <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">Receipt Images (Multi-select) *</label>
                        
                        <div x-show="isUploading" class="mb-2 h-1 w-full bg-[#f2f3f5] dark:bg-[#1e1f22] rounded-full overflow-hidden">
                            <div class="h-full bg-discord-green transition-all duration-300" :style="`width: ${progress}%`"></div>
                        </div>

                        <div class="relative mt-1">
                            @if($fileQueue)
                                <div class="w-full p-2 rounded-[24px] border-2 border-discord-green bg-black/5 relative">
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-[200px] overflow-y-auto p-1">
                                        @foreach($fileQueue as $index => $image)
                                            <div wire:key="queue-file-{{ $image->getClientOriginalName() }}-{{ $loop->index }}" class="aspect-square rounded-[12px] overflow-hidden border border-white/20 shadow-sm relative group/item">
                                                @php $previewUrl = null; try { $previewUrl = $image->temporaryUrl(); } catch (\Exception $e) { } @endphp
                                                @if($previewUrl) <img src="{{ $previewUrl }}" class="w-full h-full object-cover">
                                                @else <div class="w-full h-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center"><i data-lucide="image" class="w-4 h-4 text-slate-400"></i></div> @endif
                                                
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
                                            <i data-lucide="plus" class="w-4 h-4"></i><span class="text-[8px] font-black mt-1">Add More</span>
                                            <input type="file" wire:model="fileQueue" accept="image/jpeg,image/png,image/webp" multiple class="hidden">
                                        </label>
                                    </div>
                                    <div class="mt-2 py-1 px-3 flex items-center justify-between border-t border-black/5 dark:border-white/5 pt-2 relative z-20">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ count($fileQueue) }} Files Selected</p>
                                        <button type="button" x-on:click="$wire.set('fileQueue', [])" class="text-[9px] font-black text-rose-500 uppercase tracking-widest hover:underline cursor-pointer">Clear All</button>
                                    </div>
                                </div>
                            @else
                                <div class="relative group">
                                    <input type="file" wire:model="fileQueue" accept="image/jpeg,image/png,image/webp" multiple required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="w-full min-h-[120px] px-5 py-6 bg-[#f2f3f5] dark:bg-[#1e1f22] border-2 border-dashed border-[#e3e5e8] dark:border-[#313338] rounded-[24px] flex flex-col items-center justify-center text-center transition-all group-hover:border-discord-green/50 group-hover:bg-discord-green/5">
                                        <div class="w-10 h-10 rounded-full bg-white dark:bg-[#2b2d31] flex items-center justify-center shadow-sm mb-3 text-discord-green"><i data-lucide="image-plus" class="w-4 h-4"></i></div>
                                        <p class="text-xs font-bold text-[#1e1f22] dark:text-white mb-1">Click to upload receipts</p>
                                        <p class="text-[10px] font-medium text-[#80848e]">Select multiple JPG, PNG, WEBP</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @error('fileQueue.*') <span class="text-[10px] font-bold text-rose-500 pl-2">{{ $message }}</span> @enderror
                    </div>

                    <!-- Profile & Collection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5 relative">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">Extraction Profile *</label>
                            <select wire:model="scanForm.template_id" required class="h-10 w-full px-3 bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[0.85rem] text-[#1e1f22] dark:text-white font-bold text-xs focus:ring-2 focus:ring-discord-green/50 appearance-none outline-none">
                                <option value="auto">Auto-Detect Store</option>
                                @foreach($templates as $template) <option value="{{ $template->id }}">{{ $template->name }}</option> @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-4 top-[38px] w-4 h-4 text-[#80848e] pointer-events-none"></i>
                        </div>
                        <div class="space-y-1.5 relative">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">Existing Collection</label>
                            <select wire:model="scanForm.batch_id" class="h-10 w-full px-3 bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[0.85rem] text-[#1e1f22] dark:text-white font-bold text-xs focus:ring-2 focus:ring-discord-green/50 appearance-none outline-none">
                                <option value="">Use today's inbox</option>
                                @foreach($batches as $batch) <option value="{{ $batch->id }}">{{ $batch->name }}</option> @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-4 top-[38px] w-4 h-4 text-[#80848e] pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5"><label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">New Collection</label><input type="text" wire:model="scanForm.batch_name" placeholder="Optional" class="h-10 w-full rounded-[0.85rem] border-0 bg-[#f2f3f5] dark:bg-[#1e1f22] px-3 text-xs font-bold text-[#1e1f22] dark:text-white outline-none focus:ring-2 focus:ring-discord-green/50"></div>
                        <div class="space-y-1.5"><label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">Labels</label><input type="text" wire:model="scanForm.labels" placeholder="e.g. urgent, branch-a" class="h-10 w-full rounded-[0.85rem] border-0 bg-[#f2f3f5] dark:bg-[#1e1f22] px-3 text-xs font-bold text-[#1e1f22] dark:text-white outline-none focus:ring-2 focus:ring-discord-green/50"></div>
                    </div>

                    <!-- Custom Prompt / Instructions -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between pl-2">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest">Additional AI Instructions</label>
                            <span class="text-[8px] font-bold text-discord-green bg-discord-green/10 px-1.5 py-0.5 rounded uppercase">Experimental</span>
                        </div>
                        <textarea wire:model="scanForm.custom_instruction" rows="2" 
                                  placeholder="e.g. 'Extract all items and specify tax per item' or 'Find the branch code'"
                                  class="w-full px-5 py-3 bg-[#f2f3f5] dark:bg-[#1e1f22] border-2 border-transparent focus:border-discord-green rounded-[15px] text-sm text-[#1e1f22] dark:text-white transition-all outline-none resize-none"></textarea>
                    </div>

                    @if (session()->has('scan_error'))
                        <div class="p-3 rounded-[0.85rem] bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20"><p class="text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest mb-1">AI Error</p><p class="text-xs font-bold text-rose-700 dark:text-rose-300">{{ session('scan_error') }}</p></div>
                    @endif

                    <div class="pt-1 flex gap-2">
                        <button type="button" wire:click="closeScanModal" class="flex-1 py-3 bg-[#f2f3f5] dark:bg-[#2b2d31] text-[#1e1f22] dark:text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[0.85rem]">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="flex-1 py-3 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[0.85rem] transition-all shadow-lg disabled:opacity-50 flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="submitScan">Start Scan</span>
                            <span wire:loading wire:target="submitScan" class="flex items-center gap-2"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span x-text="$wire.scanStatus"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($batchModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="fixed inset-0 bg-white/5 dark:bg-black/5 backdrop-blur-xl" wire:click="closeBatchModal"></div>
            <div class="relative z-10 w-full max-w-md rounded-[1.5rem] bg-white p-6 shadow-2xl dark:bg-[#313338] border border-white/20 dark:border-white/10">
                <h3 class="text-lg font-black uppercase tracking-tight text-[#1e1f22] dark:text-white">New Collection</h3>
                <div class="mt-5 space-y-4">
                    <div><label class="mb-2 block text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">Collection Name</label><input type="text" wire:model="batchForm.name" class="h-10 w-full rounded-[0.85rem] border border-[#e3e5e8] bg-[#f8fafb] px-3 text-sm font-bold text-[#162033] outline-none dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white"></div>
                    <div><label class="mb-2 block text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">Note</label><textarea wire:model="batchForm.note" rows="3" class="w-full rounded-[0.85rem] border border-[#e3e5e8] bg-[#f8fafb] px-3 py-2.5 text-sm font-bold text-[#162033] outline-none dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white"></textarea></div>
                    <div class="flex gap-2"><button wire:click="closeBatchModal" class="flex-1 py-2.5 bg-[#f2f3f5] text-[10px] font-black uppercase tracking-[0.18em] rounded-[0.85rem]">Cancel</button><button wire:click="submitBatch" class="flex-1 py-2.5 bg-[#162033] text-[10px] font-black uppercase tracking-[0.18em] text-white rounded-[0.85rem]">Create</button></div>
                </div>
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
                <h3 class="text-lg font-black text-[#162033] dark:text-white">{{ $activeSlip?->display_shop ?? 'Slip Detail' }}</h3>
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
                                <div class="text-[10px] font-black uppercase tracking-[0.16em] text-[#80848e]">Total Amount</div>
                                <div class="mt-1.5 text-lg font-black text-[#162033] dark:text-discord-green">
                                    THB {{ number_format($activeSlip->display_amount, 2) }}
                                </div>
                            </div>
                            <div class="rounded-[1rem] bg-[#f8fafb] p-4 dark:bg-[#1e1f22]">
                                <div class="text-[10px] font-black uppercase tracking-[0.16em] text-[#80848e]">Workflow Status</div>
                                <div class="mt-1.5 inline-flex text-xs font-black text-[#162033] dark:text-white uppercase px-3 py-1 bg-white dark:bg-[#2b2d31] rounded-full border border-[#e3e5e8] dark:border-[#313338]">
                                    {{ $activeSlip->workflow_status }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-[#80848e]">Extracted Intelligence</h4>
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
                <div class="flex flex-col gap-4">
                    @if($archive_scope === 'active')
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Adjust AI Extraction (Optional)</label>
                            <textarea wire:model="reprocess_instruction" rows="1" 
                                      placeholder="e.g. 'try to find the tax id again' or 'items are in a list'"
                                      class="w-full px-4 py-2 bg-white dark:bg-[#2b2d31] border border-[#e3e5e8] dark:border-[#313338] rounded-[0.75rem] text-xs font-bold text-[#162033] dark:text-white outline-none focus:border-discord-green transition-all resize-none"></textarea>
                        </div>
                    @endif
                    
                    <div class="flex justify-end gap-3">
                        <button @click="open = false" class="px-6 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#162033] dark:hover:text-white transition-colors">Close</button>
                        
                        @if($archive_scope === 'active')
                            <button wire:click="reprocessSlip({{ $activeSlip?->id }})" 
                                    wire:loading.attr="disabled"
                                    class="px-4 py-2 bg-[#f2f3f5] dark:bg-[#2b2d31] text-[11px] font-black uppercase tracking-[0.18em] text-slate-600 dark:text-slate-300 rounded-[0.85rem] hover:bg-discord-green hover:text-white transition-all flex items-center gap-2 disabled:opacity-50">
                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5" wire:loading.class="animate-spin" wire:target="reprocessSlip"></i>
                                <span wire:loading.remove wire:target="reprocessSlip">Re-scan</span>
                                <span wire:loading wire:target="reprocessSlip">Processing...</span>
                            </button>
                            <a href="{{ \App\Support\WorkspaceUrl::current(request(), 'slips/edit/' . $activeSlip?->uid) }}" class="px-6 py-2 bg-[#162033] dark:bg-discord-green text-white text-[11px] font-black uppercase tracking-[0.18em] rounded-[0.85rem] shadow-lg hover:opacity-90 transition-all flex items-center gap-2">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                Edit Data
                            </a>
                        @else
                            <button wire:click="toggleArchive({{ $activeSlip?->id }}, false)" class="px-6 py-2 bg-discord-green text-white text-[11px] font-black uppercase tracking-[0.18em] rounded-[0.85rem] shadow-lg hover:opacity-90 transition-all flex items-center gap-2">
                                <i data-lucide="archive-restore" class="w-4 h-4"></i>
                                Restore to Inbox
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true, didOpen: (toast) => { toast.addEventListener('mouseenter', Swal.stopTimer); toast.addEventListener('mouseleave', Swal.resumeTimer); } });
            Livewire.on('notify', (event) => { const data = Array.isArray(event) ? event[0] : event; Swal.close(); if (typeof data === 'string') { Toast.fire({ icon: 'success', title: data }); } else { Toast.fire({ icon: data.type || 'success', title: data.title || 'Notification', text: data.message || '', timer: data.loading ? 15000 : 3000, timerProgressBar: !data.loading, didOpen: (toast) => { if (data.loading) Swal.showLoading(); } }); } });
            Livewire.on('trigger-next-scan', (event) => { const index = event.index ?? event[0].index; setTimeout(() => { @this.processSingleReceipt(index); }, 600); });
            Livewire.hook('message.processed', (message, component) => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
        });
    </script>
</div>
