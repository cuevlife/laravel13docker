@extends('layouts.app')

@section('content')
    <div class="w-full px-2 py-4 sm:px-4 lg:px-6" x-data="slipRegistry()">
        <div class="rounded-xl bg-white p-6 sm:p-8 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] border border-black/[0.04] dark:bg-[#2b2d31] dark:border-white/5">
            <!-- Header Section -->
            <div class="mb-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-discord-green/10 text-discord-green">
                        <i class="bi bi-inbox-fill text-2xl leading-none"></i>
                    </div>
                    <h1 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-widest">{{ __('Workspace Inbox') }}</h1>
                </div>
                
                <button @click="triggerScan()" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-discord-green px-6 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-green-500/20 transition hover:bg-[#1f8b4c]">
                    <i class="bi bi-qr-code-scan text-sm leading-none"></i>
                    <span>{{ __('Scan Receipt') }}</span>
                </button>
            </div>

            <!-- Filters Section -->
            <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-12">
                <div class="relative sm:col-span-5">
                    <i class="bi bi-search absolute left-5 top-1/2 z-10 -translate-y-1/2 text-sm leading-none text-[#80848e]"></i>
                    <input type="text" x-model="filters.q" @input.debounce.500ms="fetchSlips()" placeholder="{{ __('Search by Shop, UID or Amount...') }}" class="h-10 w-full rounded-xl border border-black/5 bg-white pl-12 pr-4 text-xs font-bold outline-none shadow-sm focus:border-discord-green/30 dark:bg-[#1e1f22] dark:text-white transition-all">
                </div>

                <div class="sm:col-span-2">
                    <select x-model="filters.sort" @change="fetchSlips()" class="h-10 w-full rounded-xl border border-black/5 bg-white px-3 text-xs font-bold outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white transition-all">
                        <option value="processed_at_desc">{{ __('Latest Scan') }}</option>
                        <option value="processed_at_asc">{{ __('Oldest Scan') }}</option>
                        <option value="date_desc">{{ __('Slip Date: Newest') }}</option>
                        <option value="date_asc">{{ __('Slip Date: Oldest') }}</option>
                        <option value="amount_desc">{{ __('Amount: High-Low') }}</option>
                        <option value="amount_asc">{{ __('Amount: Low-High') }}</option>
                        <option value="shop_asc">{{ __('Shop: A-Z') }}</option>
                        <option value="shop_desc">{{ __('Shop: Z-A') }}</option>
                    </select>
                </div>
                
                <div class="sm:col-span-2">
                    <select x-model="filters.workflow_status" @change="fetchSlips()" class="h-10 w-full rounded-xl border border-black/5 bg-white px-3 text-xs font-bold outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white transition-all">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach($workflowOptions ?? [] as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <div class="relative">
                        <i class="bi bi-calendar absolute left-3 top-1/2 z-10 -translate-y-1/2 text-sm leading-none text-[#80848e]"></i>
                        <input type="text" id="date-range-picker" placeholder="{{ __('Select Date...') }}" class="h-10 w-full rounded-xl border border-black/5 bg-white pl-10 pr-3 text-xs font-bold outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white transition-all">
                    </div>
                </div>

                <div class="sm:col-span-1">
                    <button @click="resetFilters()" class="flex h-10 w-full items-center justify-center rounded-xl border border-rose-100 bg-rose-50 text-rose-500 shadow-sm transition hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10" title="{{ __('Reset Filters') }}">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Export Actions Row -->
            <div class="mb-4 flex flex-wrap items-center justify-end gap-2 border-t border-black/[0.04] pt-4 dark:border-white/[0.04]">
                <button @click="fetchExportHistory()"
                   class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-white dark:bg-[#1e1f22] text-[#80848e] hover:text-indigo-600 px-4 text-[10px] font-black uppercase tracking-widest transition shadow-sm border border-black/5 dark:border-white/5">
                    <i class="bi bi-clock-history"></i>
                    <span>{{ __('History') }}</span>
                </button>

                <button @click="openExportDesigner()"
                   class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-white dark:bg-[#1e1f22] text-[#80848e] hover:text-discord-green px-4 text-[10px] font-black uppercase tracking-widest transition shadow-sm border border-black/5 dark:border-white/5">
                    <i class="bi bi-gear-fill"></i>
                    <span>{{ __('Designer') }}</span>
                </button>

                <button @click="window.location.href = '{{ route('workspace.slip.export') }}?' + new URLSearchParams(filters).toString()"
                   class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-discord-green/10 text-discord-green hover:bg-discord-green/20 px-6 text-[10px] font-black uppercase tracking-widest transition shadow-sm border border-discord-green/20">
                    <i class="bi bi-file-earmark-spreadsheet-fill text-sm"></i>
                    <span>{{ __('Export Excel') }}</span>
                </button>
            </div>

            <!-- Grid Section -->
            <div class="overflow-hidden relative min-h-[400px]">
                <div x-show="is_loading" class="absolute inset-0 bg-white/50 backdrop-blur-[2px] z-20 flex items-center justify-center dark:bg-black/20" x-cloak>
                    <i class="bi bi-arrow-repeat animate-spin text-3xl leading-none text-discord-green"></i>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    <template x-for="slip in slips" :key="slip.id">
                        <div class="flex flex-col bg-white dark:bg-[#1e1f22] border border-black/5 dark:border-white/5 rounded-xl shadow-sm hover:shadow-md transition-all overflow-hidden group">
                            
                            <!-- Image Area -->
                            <div class="relative h-40 bg-[#f8fafb] dark:bg-[#2b2d31] overflow-hidden">
                                <img :src="'/storage/' + slip.image_path" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity">
                                <!-- Status Badge -->
                                <div class="absolute top-2 right-2" x-show="slip.workflow_status !== 'reviewed'">
                                    <span class="inline-flex items-center rounded-lg px-2 py-1 text-[9px] font-black uppercase tracking-widest shadow-sm backdrop-blur-md bg-white/90 dark:bg-black/50"
                                          :class="{
                                              'text-[#4f86f7]': slip.workflow_status === 'exported',
                                              'text-emerald-600': slip.workflow_status === 'approved',
                                              'text-slate-600 dark:text-slate-300': !['reviewed', 'exported', 'approved'].includes(slip.workflow_status)
                                          }"
                                          x-text="slip.workflow_status === 'exported' ? '{{ __('Exported to Excel') }}' : (slip.workflow_status === 'approved' ? '{{ __('Approved') }}' : slip.workflow_status)">
                                    </span>
                                </div>
                            </div>

                            <!-- Content Area -->
                            <div class="flex flex-col flex-1 p-4">
                                <div class="mb-3">
                                    <h3 class="text-sm font-black text-[#1e1f22] dark:text-white truncate" x-text="slip.display_shop"></h3>
                                    <p class="text-[9px] font-bold text-[#80848e] tracking-widest uppercase mt-0.5" x-text="slip.uid"></p>
                                </div>

                                <div class="grid grid-cols-2 gap-2 mt-auto mb-4 border-y border-black/[0.04] dark:border-white/[0.04] py-3">
                                    <div>
                                        <p class="text-[9px] font-black text-[#80848e] uppercase tracking-widest mb-0.5">{{ __('Slip Date') }}</p>
                                        <p class="text-[11px] font-bold text-[#313338] dark:text-[#b5bac1] truncate" x-text="slip.display_date"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[9px] font-black text-[#80848e] uppercase tracking-widest mb-0.5">{{ __('Amount') }}</p>
                                        <p class="text-[11px] font-black text-[#1e1f22] dark:text-white truncate" x-text="'฿' + Number(slip.display_amount).toLocaleString(undefined, {minimumFractionDigits: 2})"></p>
                                    </div>
                                </div>

                                <!-- Footer Actions -->
                                <div class="flex items-center justify-between mt-auto">
                                    <p class="text-[9px] font-bold text-[#80848e]" x-text="formatDate(slip.processed_at)"></p>
                                    
                                    <div class="flex gap-1">
                                        <a :href="'/workspace/slips/edit/' + slip.id" class="flex h-8 w-8 items-center justify-center rounded-lg bg-black/5 text-[#5c5e66] hover:bg-discord-green/10 hover:text-discord-green dark:bg-white/5 dark:text-[#b5bac1] dark:hover:bg-discord-green/20 transition-all" title="{{ __('View') }}">
                                            <i class="bi bi-eye-fill text-xs"></i>
                                        </a>
                                        <button @click="deleteSlip(slip.id)" class="flex h-8 w-8 items-center justify-center rounded-lg bg-black/5 text-[#5c5e66] hover:bg-rose-500/10 hover:text-rose-500 dark:bg-white/5 dark:text-[#b5bac1] dark:hover:bg-rose-500/20 transition-all" title="{{ __('Delete') }}">
                                            <i class="bi bi-trash-fill text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <template x-if="slips.length === 0 && !is_loading">
                    <div class="py-24 text-center">
                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-xl bg-[#f8fafb] border border-black/[0.02] shadow-sm dark:bg-[#1e1f22] dark:border-white/5">
                            <i class="bi bi-receipt text-3xl leading-none text-[#80848e]"></i>
                        </div>
                        <h3 class="text-[13px] font-black text-[#1e1f22] dark:text-white">{{ __('No slips found in this folder') }}</h3>
                        <p class="mt-1 text-xs font-bold text-[#80848e]">{{ __('Search using other terms or click Scan Receipt to add new') }}</p>
                    </div>
                </template>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-black/[0.04] pt-6 dark:border-white/[0.04]" x-show="pagination && pagination.total > 0">
                <div class="flex items-center gap-4">
                    <div class="text-[11px] font-bold text-[#80848e]">
                        {{ __('Showing') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="slips.length"></span> {{ __('of') }} <span class="font-black text-[#1e1f22] dark:text-white" x-text="pagination.total"></span> {{ __('Slips') }}
                    </div>
                    
                    <select x-model="filters.per_page" @change="fetchSlips()" class="h-8 rounded-lg border border-black/5 bg-[#f8fafb] px-2 text-[10px] font-black outline-none shadow-sm dark:bg-[#1e1f22] dark:text-white transition-all">
                        <option value="20">20 / {{ __('Page') }}</option>
                        <option value="50">50 / {{ __('Page') }}</option>
                        <option value="100">100 / {{ __('Page') }}</option>
                    </select>
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
                                x-html="formatPaginationLabel(link.label)">
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
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-discord-green/10 text-discord-green">
                                <i class="bi bi-qr-code-scan text-2xl leading-none"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight">{{ __('Auto-Scan Receipt') }}</h2>
                                <p class="text-xs font-bold text-[#80848e]">{{ __('Select image files to scan and save automatically') }}</p>
                            </div>
                        </div>
                        <button @click="scanModalOpen = false" :disabled="isScanning" class="text-[#80848e] hover:text-rose-500 transition disabled:opacity-50">
                            <i class="bi bi-x-lg text-xl leading-none"></i>
                        </button>
                    </div>

                    <!-- Dropzone -->
                    <label class="group relative flex border-2 border-dashed border-[#e3e5e8] dark:border-[#313338] rounded-xl bg-[#f8fafb] dark:bg-[#1e1f22] cursor-pointer hover:border-discord-green/50 transition-all mb-6"
                           :class="scanFiles.length > 0 ? 'flex-row items-center gap-4 py-3 px-6' : 'flex-col items-center justify-center py-12'">
                        <input type="file" multiple accept="image/*" class="hidden" @change="handleFileSelect">
                        
                        <div class="flex items-center justify-center rounded-xl bg-white dark:bg-[#2b2d31] shadow-sm transition-transform group-hover:scale-110"
                             :class="scanFiles.length > 0 ? 'h-9 w-9 shrink-0' : 'h-16 w-16 mb-4'">
                            <i class="bi bi-image text-discord-green" :class="scanFiles.length > 0 ? 'text-base' : 'text-2xl'"></i>
                        </div>
                        
                        <div :class="scanFiles.length > 0 ? 'text-left' : 'text-center'">
                            <p class="font-black text-[#1e1f22] dark:text-white" :class="scanFiles.length > 0 ? 'text-[11px]' : 'text-sm'" x-text="scanFiles.length > 0 ? '{{ __('Click to select or drag additional files here') }}' : '{{ __('Click to select or drag and drop files here') }}'"></p>
                            <p x-show="scanFiles.length === 0" class="font-bold text-[#80848e] mt-0.5 text-[10px]">{{ __('Supports JPG, PNG (Max 10MB per file)') }}</p>
                        </div>
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
                                       <div class="flex items-center gap-2 overflow-hidden">
                                           <span class="text-[11px] font-black text-[#1e1f22] dark:text-white truncate" x-text="f.name"></span>
                                           <span x-show="f.duration" class="text-[9px] font-black text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 px-1.5 py-0.5 rounded-md" x-text="f.duration + 's'"></span>
                                       </div>
                                       <span class="text-[10px] font-bold text-[#80848e]" x-text="f.size"></span>
                                    </div>                                    <div class="w-full h-1.5 bg-black/5 dark:bg-white/5 rounded-xl overflow-hidden">
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
                                              x-text="f.status === 'error' ? '{{ __('Error') }}: ' + f.error : (f.status === 'duplicate' ? '{{ __('Duplicate slip found') }}' : f.status)"></span>
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
                                <p class="text-xs font-bold">{{ __('No files in scan queue yet') }}</p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="bg-[#f2f3f5] dark:bg-[#232428] p-6 flex items-center justify-between border-t border-black/5 dark:border-white/5">
                    <div class="text-[10px] font-black uppercase tracking-widest text-[#80848e]">
                        <span x-text="scanFiles.filter(f => f.status === 'completed' || f.status === 'duplicate').length"></span> / 
                        <span x-text="scanFiles.length"></span> {{ __('Finished') }}
                    </div>
                    <div class="flex gap-3">
                        <button @click="scanFiles = []; scanModalOpen = false" 
                                :disabled="isScanning"
                                class="px-6 py-2.5 text-[11px] font-black uppercase tracking-widest text-[#5c5e66] hover:bg-black/5 dark:text-[#b5bac1] transition rounded-xl disabled:opacity-50">
                            {{ __('Close Window') }}
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
                            <i class="bi bi-gear-fill text-lg leading-none"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-tight">{{ __('Export Designer') }}</h2>
                                <p class="text-[10px] font-bold text-[#80848e] uppercase tracking-widest">{{ __('Order and rename Excel table headers') }}</p>
                            </div>
                        </div>
                        <button @click="exportModalOpen = false" :disabled="savingExport" class="h-8 w-8 flex items-center justify-center rounded-full text-[#80848e] hover:bg-black/5 dark:hover:bg-white/5 transition disabled:opacity-50">
                            <i class="bi bi-x-lg text-sm"></i>
                        </button>
                    </div>

                    {{-- Filename Customization --}}
                    <div class="bg-[#f8fafb] dark:bg-black/20 p-4 rounded-xl border border-black/5 dark:border-white/5">
                        <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">{{ __('Default Excel Filename') }}</label>
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
                                        <th class="px-4 py-3 w-16 text-center">
                                            <div class="flex flex-col items-center gap-1">
                                                <span>{{ __('Export') }}</span>
                                                <input type="checkbox" @click="toggleAllExportColumns()" :checked="exportColumns.every(c => c.enabled)" class="h-3.5 w-3.5 rounded border-black/10 text-discord-green focus:ring-0 shadow-sm transition-all cursor-pointer">
                                            </div>
                                        </th>
                                        <th class="px-4 py-3">{{ __('Source Field') }}</th>
                                        <th class="px-4 py-3">{{ __('Label in Excel') }}</th>
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
                                                       placeholder="{{ __('Header Name...') }}">
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
                        <button @click="exportModalOpen = false" :disabled="savingExport" class="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-[#5c5e66] hover:bg-black/5 dark:text-[#b5bac1] transition rounded-xl">{{ __('Cancel') }}</button>
                        <button @click="saveExportSettings()" :disabled="savingExport" class="px-6 py-2 bg-discord-green text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-green-500/20 hover:bg-[#1f8b4c] transition flex items-center gap-2">
                            <i x-show="savingExport" class="bi bi-arrow-repeat animate-spin"></i>
                            <span x-text="savingExport ? '{{ __('Saving...') }}' : '{{ __('Save Value') }}'"></span>
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
                                <h2 class="text-lg font-black text-[#1e1f22] dark:text-white uppercase tracking-tight">{{ __('Export History') }}</h2>
                                <p class="text-[10px] font-bold text-[#80848e] uppercase tracking-widest">{{ __('Latest Excel export history') }}</p>
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
                                        <th class="px-4 py-3">{{ __('File Name') }}</th>
                                        <th class="px-4 py-3 text-center">{{ __('Slips') }}</th>
                                        <th class="px-4 py-3 text-center">{{ __('Mode') }}</th>
                                        <th class="px-4 py-3 text-right">{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                    <template x-for="exp in exportHistory" :key="exp.id">
                                        <tr class="group hover:bg-black/[0.02] dark:hover:bg-white/[0.02]">
                                            <td class="px-4 py-3">
                                                <div class="text-[11px] font-black text-[#1e1f22] dark:text-white truncate max-w-[250px]" x-text="exp.file_name"></div>
                                                <div class="text-[9px] font-bold text-[#80848e] uppercase tracking-widest" x-text="'{{ __('BY') }} ' + exp.user_name"></div>
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
                                            <td colspan="4" class="px-4 py-10 text-center text-[#80848e] italic uppercase tracking-widest text-[9px]">{{ __('No export history found') }}</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-[#f2f3f5] dark:bg-[#232428] px-6 py-4 flex items-center justify-end border-t border-black/5 dark:border-white/5">
                    <button @click="exportHistoryOpen = false" class="px-6 py-2 bg-white dark:bg-[#1e1f22] text-[#1e1f22] dark:text-white border border-black/10 dark:border-white/10 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-sm hover:bg-black/5 transition">{{ __('Close Window') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .flatpickr-calendar.flatpickr-buddhist-year .flatpickr-current-month .numInput.cur-year {
            min-width: 72px;
        }
    </style>
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
                    sort: {!! json_encode($activeFilters['sort'] ?? 'processed_at_desc') !!},
                    per_page: {!! json_encode(request('per_page', 20)) !!}
                },
                selectedSlips: [],
                scanModalOpen: false,
                isScanning: false,
                scanFiles: [],
                exportColumns: @json($exportColumns),
                exportFilename: {!! json_encode($tenant->config['excel_filename'] ?? 'SmartBill_Export_' . now()->format('Y-m-d')) !!},
                exportModalOpen: false,
                savingExport: false,
                exportHistoryOpen: false,
                exportHistory: [],

                async init() {
                    this.setupDatePicker();
                    // Normalize legacy sort values
                    if (this.filters.sort === 'latest') this.filters.sort = 'processed_at_desc';
                    if (this.filters.sort === 'oldest') this.filters.sort = 'processed_at_asc';
                    
                    await this.fetchSlips();
                    this.initSortable();
                },

                toggleSort(column) {
                    let currentSort = this.filters.sort;
                    // Normalize for comparison
                    if (currentSort === 'latest') currentSort = 'processed_at_desc';
                    if (currentSort === 'oldest') currentSort = 'processed_at_asc';

                    if (currentSort.startsWith(column)) {
                        this.filters.sort = currentSort.endsWith('_asc') ? column + '_desc' : column + '_asc';
                    } else {
                        // Default directions
                        if (['processed_at', 'date', 'amount'].includes(column)) {
                            this.filters.sort = column + '_desc';
                        } else {
                            this.filters.sort = column + '_asc';
                        }
                    }
                    this.fetchSlips();
                },

                sortIcon(column) {
                    let currentSort = this.filters.sort;
                    if (currentSort === 'latest') currentSort = 'processed_at_desc';
                    if (currentSort === 'oldest') currentSort = 'processed_at_asc';

                    if (!currentSort.startsWith(column)) return 'bi-arrow-down-up opacity-20 text-[#80848e]';
                    return currentSort.endsWith('_asc') ? 'bi-arrow-up text-discord-green' : 'bi-arrow-down text-discord-green';
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

                toggleAllExportColumns() {
                    const allEnabled = this.exportColumns.every(c => c.enabled);
                    this.exportColumns.forEach(c => c.enabled = !allEnabled);
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
                        const currentLocale = @json(app()->getLocale());
                        const isThaiLocale = currentLocale === 'th';

                        flatpickr('#date-range-picker', {
                            mode: 'range',
                            dateFormat: 'd/m/Y',
                            locale: isThaiLocale ? 'th' : 'default',
                            formatDate: (date, format, locale) => {
                                if (format === 'd/m/Y' && isThaiLocale) {
                                    const day = String(date.getDate()).padStart(2, '0');
                                    const month = String(date.getMonth() + 1).padStart(2, '0');
                                    const year = date.getFullYear() + 543;
                                    return `${day}/${month}/${year}`;
                                }

                                return flatpickr.formatDate(date, format, locale);
                            },
                            onReady: (selectedDates, dateStr, instance) => {
                                if (isThaiLocale) {
                                    instance.calendarContainer.classList.add('flatpickr-buddhist-year');
                                } else {
                                    instance.calendarContainer.classList.remove('flatpickr-buddhist-year');
                                }
                            },
                            onOpen: (selectedDates, dateStr, instance) => {
                                if (isThaiLocale) {
                                    instance.currentYearElement.value = instance.currentYear + 543;
                                }
                            },
                            onMonthChange: (selectedDates, dateStr, instance) => {
                                if (isThaiLocale) {
                                    instance.currentYearElement.value = instance.currentYear + 543;
                                }
                            },
                            onYearChange: (selectedDates, dateStr, instance) => {
                                if (isThaiLocale) {
                                    instance.currentYearElement.value = instance.currentYear + 543;
                                }
                            },
                            onValueUpdate: (selectedDates, dateStr, instance) => {
                                if (isThaiLocale && instance.currentYearElement) {
                                    instance.currentYearElement.value = instance.currentYear + 543;
                                }
                            },
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
                    this.filters = { q: '', workflow_status: '', date_from: '', date_to: '', sort: 'processed_at_desc', batch_id: '', per_page: 20 };
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

                formatPaginationLabel(label) {
                    if (label.includes('Previous')) {
                        return label.replace('Previous', '{{ __("Previous") }}');
                    }
                    if (label.includes('Next')) {
                        return label.replace('Next', '{{ __("Next") }}');
                    }
                    return label;
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
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: error.message,
                            showConfirmButton: false,
                            timer: 3000
                        });
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
                    const startTime = performance.now();
                    const formData = new FormData();
                    formData.append('image', pendingFile.file);

                    try {
                        const res = await fetch('/workspace/slips/process', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.getCsrfToken(), 'Accept': 'application/json' },
                            body: formData
                        });
                        
                        const endTime = performance.now();
                        pendingFile.duration = ((endTime - startTime) / 1000).toFixed(1);

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
