<x-app-layout>
    <div x-data="slipRegistry()" class="space-y-8 animate-in fade-in duration-700 pb-20">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 bg-discord-green rounded-full"></div>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight leading-none">Slip Registry</h2>
                    <p class="text-[9px] md:text-[10px] font-bold text-[#80848e] uppercase tracking-[0.2em] mt-1.5">Node Data Repository</p>
                </div>
            </div>

            <!-- Fast Actions -->
            <div class="flex flex-row items-center gap-2">
                <button @click="showFilters = !showFilters" 
                        class="p-3 md:py-3.5 md:px-4 bg-white dark:bg-[#2b2d31] border border-[#e3e5e8] dark:border-[#313338] rounded-[14px] shadow-sm text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white hover:border-[#1e1f22]/20 dark:hover:border-white/20 transition-all flex items-center justify-center shrink-0" 
                        :class="{'border-discord-green text-discord-green dark:text-discord-green bg-discord-green/5 dark:bg-discord-green/10': showFilters}" title="Toggle Filters">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </button>
                <a href="{{ route('admin.slip.export', [], false) }}" class="p-3 md:py-3.5 md:px-4 bg-white dark:bg-[#2b2d31] border border-[#e3e5e8] dark:border-[#313338] rounded-[14px] shadow-sm text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white hover:border-[#1e1f22]/20 dark:hover:border-white/20 transition-all flex items-center justify-center shrink-0" title="Export Data">
                    <i data-lucide="download" class="w-4 h-4"></i>
                </a>
                <button @click="openScanModal()" 
                        class="flex-1 sm:flex-none px-5 py-3 md:py-3.5 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[14px] transition-all shadow-md shadow-green-500/20 active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="scan-line" class="w-4 h-4"></i> 
                    <span class="sm:hidden">Scan</span>
                    <span class="hidden sm:inline">Scan Receipt</span>
                </button>
            </div>
        </div>

        <!-- Filter Hub -->
        <div x-show="showFilters" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             style="display: none;"
             class="bg-white dark:bg-[#2b2d31] border border-[#e3e5e8] dark:border-[#313338] rounded-[20px] p-4 md:p-5 mb-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 md:gap-4 items-center">
                <!-- Search -->
                <div class="relative group lg:col-span-5">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[#80848e] group-focus-within:text-discord-green transition-colors"></i>
                <input type="text" x-model="searchQuery" placeholder="Search Slips..." class="w-full bg-white dark:bg-[#2b2d31] border-0 rounded-[14px] pl-11 pr-4 py-3.5 text-xs font-bold text-[#1e1f22] dark:text-white focus:ring-2 focus:ring-discord-green/50 transition-all outline-none shadow-sm">
            </div>

            <!-- Date -->
            <div class="relative lg:col-span-3">
                <input type="date" x-model="dateFilter" class="w-full bg-white dark:bg-[#2b2d31] border-0 rounded-[14px] px-4 py-3.5 text-xs font-bold text-[#1e1f22] dark:text-white focus:ring-2 focus:ring-discord-green/50 transition-all outline-none cursor-pointer shadow-sm">
            </div>

            <!-- Profile -->
            <div class="relative lg:col-span-3">
                <select x-model="templateFilter" class="w-full px-4 py-3.5 bg-white dark:bg-[#2b2d31] border-0 rounded-[14px] text-[#1e1f22] dark:text-white font-bold text-xs focus:ring-2 focus:ring-discord-green/50 transition-all outline-none appearance-none shadow-sm">
                    <option value="">All Profiles...</option>
                    @foreach($templates as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->merchant->name ?? 'Unlinked' }})</option>
                    @endforeach
                </select>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                    <i data-lucide="chevron-down" class="w-4 h-4 text-[#80848e]"></i>
                </div>
            </div>

                <!-- Clear -->
                <button type="button" @click="resetFilters()" class="w-full h-full min-h-[44px] bg-[#f2f3f5] dark:bg-[#1e1f22] hover:bg-[#e3e5e8] dark:hover:bg-[#313338] text-[#5c5e66] dark:text-[#b5bac1] border border-[#e3e5e8] dark:border-[#313338] text-[10px] md:text-[11px] font-black uppercase tracking-widest rounded-[14px] transition-all shadow-sm lg:col-span-1 flex items-center justify-center pt-1" title="Clear Filters">
                    <i data-lucide="filter-x" class="w-4 h-4 sm:hidden lg:block mb-1"></i>
                    <span class="hidden sm:inline lg:hidden mb-1">Clear</span>
                </button>
            </div>
        </div>

        <!-- List Section -->
        <div class="space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap block sm:table">
                    <thead class="hidden sm:table-header-group">
                        <tr class="border-b border-[#e3e5e8] dark:border-[#313338] text-[10px] uppercase font-black tracking-widest text-[#80848e] bg-transparent">
                            <th class="px-6 py-4">Image / Source</th>
                            <th class="px-6 py-4">Receipt Date</th>
                            <th class="px-6 py-4">Processed</th>
                            <th class="px-6 py-4 text-right">Value</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="block sm:table-row-group divide-y divide-[#e3e5e8] dark:divide-[#313338]">
                        @forelse($slips as $slip)
                            @php
                                $processedDate = optional($slip->processed_at)->format('Y-m-d');
                                $searchIndex = strtolower(trim(implode(' ', array_filter([$slip->template->merchant->name ?? '', $slip->display_shop, $processedDate]))));
                            @endphp
                            <tr class="block sm:table-row p-5 sm:p-0 hover:bg-[#e3e5e8]/30 dark:hover:bg-[#313338]/30 transition-colors group relative"
                                x-show="matchesFilter('{{ $searchIndex }}', '{{ $processedDate }}', '{{ $slip->slip_template_id }}')">
                                <td class="block sm:table-cell px-0 py-2 sm:px-4 sm:py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-[12px] bg-white dark:bg-[#2b2d31] flex items-center justify-center overflow-hidden border border-[#e3e5e8] dark:border-[#313338] group-hover:border-discord-green/30 transition-colors shrink-0 shadow-sm">
                                            <img src="{{ asset('storage/' . $slip->image_path) }}" class="w-full h-full object-cover transition-transform hover:scale-110 cursor-zoom-in" alt="slip">
                                        </div>
                                        <div class="min-w-0 pr-16 sm:pr-0">
                                            <span class="text-sm font-black text-[#1e1f22] dark:text-white truncate block">{{ $slip->display_shop }}</span>
                                            <span class="text-[9px] font-bold text-discord-green uppercase tracking-tighter">{{ optional($slip->template)->name ?? 'Unknown' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="flex items-center justify-between sm:table-cell px-0 py-2 sm:px-4 sm:py-4 mt-2 sm:mt-0">
                                    <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-[#80848e]">Receipt Date</span>
                                    <span class="text-xs font-bold text-[#5c5e66] dark:text-[#b5bac1]">{{ $slip->display_date }}</span>
                                </td>
                                <td class="flex items-center justify-between sm:table-cell px-0 py-2 sm:px-4 sm:py-4">
                                    <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-[#80848e]">Processed</span>
                                    <span class="text-[10px] font-bold text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-tighter">{{ $slip->processed_at->format('d M y | H:i') }}</span>
                                </td>
                                <td class="flex items-center justify-between sm:table-cell px-0 py-2 sm:px-4 sm:py-4 sm:text-right border-t border-dashed border-[#e3e5e8] dark:border-[#313338] sm:border-0 pt-3 sm:pt-4">
                                    <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-[#80848e]">Value</span>
                                    <span class="text-lg sm:text-xl font-black text-[#1e1f22] dark:text-white">฿{{ is_numeric($slip->display_amount) ? number_format($slip->display_amount, 2) : $slip->display_amount }}</span>
                                </td>
                                <td class="absolute top-5 right-5 sm:static sm:table-cell sm:px-4 sm:py-4">
                                    <div class="flex items-center justify-end gap-1.5 sm:gap-2 relative z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.slip.edit', $slip->id, false) }}" class="p-2 bg-white dark:bg-[#313338] rounded-[10px] text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white transition-all shadow-sm">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        <button @click="deleteSlip({{ $slip->id }})" class="p-2 bg-white dark:bg-[#313338] rounded-[10px] text-[#5c5e66] dark:text-[#b5bac1] hover:text-discord-red transition-all shadow-sm relative z-20">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="block sm:table-row">
                                <td class="block sm:table-cell px-6 py-16 text-center text-[#5c5e66] dark:text-[#b5bac1] w-full" sm-colspan="5">
                                    <i data-lucide="database-zap" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                                    <span class="text-[11px] font-black uppercase tracking-[0.2em] block">Registry Empty</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-8">
                {{ $slips->links() }}
            </div>
        </div>



        <!-- Scan Modal -->
        <template x-teleport="body">
            <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center sm:px-4 pb-0 sm:pb-10 pt-10 px-0">
                <div class="absolute inset-0 bg-[#1e1f22]/80 backdrop-blur-sm" @click="closeModal()" 
                     x-show="modalOpen" x-transition.opacity></div>
                
                <div class="relative w-full max-w-md bg-white dark:bg-[#313338] rounded-t-[2.5rem] sm:rounded-[2.5rem] shadow-2xl p-8 transform transition-all mt-auto sm:mt-0 max-h-[90vh] overflow-y-auto"
                     x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-12 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-12 sm:scale-95">
                    
                    <button type="button" @click="closeModal()" class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] text-[#5c5e66] hover:text-discord-red transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>

                    <h3 class="text-2xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight mb-2">Process Receipt</h3>
                    <p class="text-[11px] text-[#5c5e66] dark:text-[#b5bac1] leading-relaxed mb-6 font-medium">Upload a clear image of your receipt. The AI will extract data based on the Template schema.</p>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- File Upload -->
                        <div class="space-y-1.5 w-full">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">Receipt Image *</label>
                            
                            <!-- Custom File Upload Area -->
                            <div class="relative group mt-1">
                                <input type="file" @change="handleFileChange" accept="image/jpeg,image/png,image/webp" required
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" id="imageInput">
                                
                                <div class="w-full min-h-[140px] px-6 py-8 bg-[#f2f3f5] dark:bg-[#1e1f22] border-2 border-dashed border-[#e3e5e8] dark:border-[#313338] rounded-[24px] flex flex-col items-center justify-center text-center transition-all group-hover:border-discord-green/50 group-hover:bg-discord-green/5">
                                    <div class="w-12 h-12 rounded-full bg-white dark:bg-[#2b2d31] flex items-center justify-center shadow-sm mb-3 text-discord-green">
                                        <i data-lucide="image-plus" class="w-5 h-5"></i>
                                    </div>
                                    <p class="text-xs font-bold text-[#1e1f22] dark:text-white mb-1" x-text="fileName ? fileName : 'Click or drag image to upload'"></p>
                                    <p class="text-[10px] font-medium text-[#80848e]">JPG, PNG, WEBP (Max 5MB)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Template Selection -->
                        <div class="space-y-1.5 relative">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">Extraction Profile *</label>
                            <select x-model="form.template_id" required class="w-full px-4 py-4 bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[16px] text-[#1e1f22] dark:text-white font-bold text-xs focus:ring-2 focus:ring-discord-green/50 transition-all appearance-none outline-none">
                                <option value="" disabled selected>Select a processing profile...</option>
                                @foreach($templates as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->merchant->name ?? 'Unlinked' }})</option>
                                @endforeach
                            </select>
                            <div class="absolute right-5 top-[34px] pointer-events-none">
                                <i data-lucide="chevron-down" class="w-4 h-4 text-[#80848e]"></i>
                            </div>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="closeModal()" class="flex-1 py-4 bg-[#f2f3f5] dark:bg-[#2b2d31] hover:bg-[#e3e5e8] dark:hover:bg-[#1e1f22] text-[#1e1f22] dark:text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[16px] transition-colors">
                                Cancel
                            </button>
                            <button type="submit" :disabled="loading" class="flex-1 py-4 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[16px] transition-all shadow-lg shadow-green-500/20 disabled:opacity-50 flex items-center justify-center gap-2">
                                <template x-if="loading">
                                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                                </template>
                                <span x-text="loading ? 'Processing...' : 'Start Extraction'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
        function slipRegistry() {
            return {
                showFilters: false,
                searchQuery: '',
                dateFilter: '',
                templateFilter: '',

                modalOpen: false,
                loading: false,
                fileObj: null,
                fileName: '',
                form: { template_id: '' },
                
                matchesFilter(searchIndex, dateStr, templateId) {
                    if (this.searchQuery && !searchIndex.includes(this.searchQuery.toLowerCase().trim())) return false;
                    if (this.dateFilter && dateStr !== this.dateFilter) return false;
                    if (this.templateFilter && templateId !== this.templateFilter) return false;
                    return true;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.dateFilter = '';
                    this.templateFilter = '';
                },

                openScanModal() {
                    this.form.template_id = '';
                    this.fileObj = null;
                    this.fileName = '';
                    this.modalOpen = true;
                },

                closeModal() {
                    this.modalOpen = false;
                },

                handleFileChange(e) {
                    if (e.target.files.length > 0) {
                        this.fileObj = e.target.files[0];
                        this.fileName = this.fileObj.name;
                    } else {
                        this.fileObj = null;
                        this.fileName = '';
                    }
                },

                async submitForm() {
                    if (!this.fileObj) return Swal.fire('Error', 'Please select an image file first', 'error');
                    
                    this.loading = true;
                    try {
                        const formData = new FormData();
                        formData.append('image', this.fileObj);
                        formData.append('template_id', this.form.template_id);
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        
                        const res = await fetch('/slips/process', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Processing Failed');

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Slip Processed',
                            showConfirmButton: false,
                            timer: 2000,
                            background: document.documentElement.classList.contains('dark') ? '#313338' : '#ffffff',
                            color: document.documentElement.classList.contains('dark') ? '#f2f3f5' : '#1e1f22'
                        });
                        
                        setTimeout(() => window.location.reload(), 500);
                    } catch (e) {
                        Swal.fire('Error', e.message, 'error');
                        this.loading = false;
                    }
                },

                async deleteSlip(id) {
                    const confirm = await Swal.fire({
                        title: 'Delete this slip?',
                        text: "This process cannot be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ed4245',
                        cancelButtonColor: '#80848e',
                        confirmButtonText: 'Yes, delete it',
                        background: document.documentElement.classList.contains('dark') ? '#313338' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f2f3f5' : '#1e1f22'
                    });

                    if (confirm.isConfirmed) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                            const res = await fetch(`/slips/delete/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                            });
                            
                            if (res.ok) window.location.reload();
                        } catch (e) {
                            Swal.fire('Error', 'Delete Failed.', 'error');
                        }
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
