<x-app-layout>
    <div x-data="storeManager()" class="space-y-8 animate-in fade-in duration-700 pb-20">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 bg-discord-green rounded-full"></div>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight leading-none">Stores Settings</h2>
                    <p class="text-[9px] md:text-[10px] font-bold text-[#80848e] uppercase tracking-[0.2em] mt-1.5">Manage Registered Brands</p>
                </div>
            </div>

            <button @click="openAddModal()" 
                    class="w-full sm:w-auto px-5 py-3 md:py-3.5 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-xl transition-all shadow-md shadow-green-500/20 active:scale-95 flex items-center justify-center gap-2">
                <i class="bi bi-plus-lg w-4 h-4"></i> 
                <span>Create Store</span>
            </button>
        </div>

        <!-- Stores Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap block sm:table">
                <thead class="hidden sm:table-header-group">
                    <tr class="border-b border-[#e3e5e8] dark:border-[#313338] text-[10px] uppercase font-black tracking-widest text-[#80848e] bg-transparent">
                        <th class="px-6 py-4">Store Name</th>
                        <th class="px-6 py-4">Project</th>
                        <th class="px-6 py-4">Address / Details</th>
                        <th class="px-6 py-4 text-center">Connected Templates</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="block sm:table-row-group divide-y divide-[#e3e5e8] dark:divide-[#313338]">
                    @forelse($stores as $store)
                        <tr class="block sm:table-row p-5 sm:p-0 hover:bg-[#e3e5e8]/30 dark:hover:bg-[#313338]/30 transition-colors group relative">
                            <td class="block sm:table-cell px-0 py-2 sm:px-4 sm:py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-[#2b2d31] flex items-center justify-center border border-[#e3e5e8] dark:border-[#313338] group-hover:border-discord-green/30 text-discord-green shadow-sm shrink-0 transition-colors">
                                        <i class="bi bi-shop w-5 h-5"></i>
                                    </div>
                                    <div class="pr-16 sm:pr-0">
                                        <h4 class="text-sm font-black text-[#1e1f22] dark:text-white truncate max-w-[200px] sm:max-w-none">{{ $store->name }}</h4>
                                        <p class="text-[10px] font-bold text-[#80848e] mt-0.5">Tax ID: {{ $store->tax_id ?: '---' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="block sm:table-cell px-0 py-2 sm:px-4 sm:py-4">
                                <div class="sm:hidden text-[9px] font-black uppercase text-[#80848e] tracking-widest mb-1">Project</div>
                                <div class="inline-flex items-center gap-2 rounded-xl border border-[#e3e5e8] bg-white px-3 py-2 text-[10px] font-black text-[#1e1f22] dark:border-[#313338] dark:bg-[#2b2d31] dark:text-white">
                                    <i class="bi bi-briefcase-fill w-3.5 h-3.5 text-discord-green"></i>
                                    <span>Project {{ str_pad((string) $store->id, 2, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </td>
                            <td class="block sm:table-cell px-0 py-2 sm:px-4 sm:py-4">
                                <div class="sm:hidden text-[9px] font-black uppercase text-[#80848e] tracking-widest mb-1">Address</div>
                                <p class="text-[11px] font-bold text-[#5c5e66] dark:text-[#b5bac1] truncate sm:max-w-[250px] whitespace-normal sm:whitespace-nowrap line-clamp-2 sm:line-clamp-none">{{ $store->address ?: 'No registered physical location.' }}</p>
                            </td>
                            <td class="flex items-center justify-between sm:table-cell px-0 py-2 sm:px-4 sm:py-4 sm:text-center mt-2 sm:mt-0 border-t border-dashed border-[#e3e5e8] dark:border-[#313338] sm:border-0 pt-3 sm:pt-4">
                                <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-[#80848e]">Templates</span>
                                <span class="px-3 py-1 bg-[#f2f3f5] dark:bg-[#313338] text-[#5c5e66] dark:text-[#f2f3f5] text-[10px] font-black rounded-[8px] border border-[#e3e5e8] dark:border-transparent">{{ $store->templates_count ?? 0 }} Profiles</span>
                            </td>
                            <td class="absolute top-5 right-5 sm:static sm:table-cell sm:px-4 sm:py-4">
                                <div class="flex items-center justify-end gap-1.5 sm:gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openEditModal({{ $store->id }}, '{{ addslashes($store->name) }}', '{{ addslashes($store->tax_id) }}', '{{ addslashes($store->address) }}')" class="p-2 bg-white dark:bg-[#313338] rounded-xl text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white transition-all shadow-sm relative z-20">
                                        <i class="bi bi-pencil-square w-4 h-4"></i>
                                    </button>
                                    <button @click="deleteStore({{ $store->id }})" class="p-2 bg-white dark:bg-[#313338] rounded-xl text-[#5c5e66] dark:text-[#b5bac1] hover:text-discord-red transition-all shadow-sm relative z-20">
                                        <i class="bi bi-trash-fill w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block sm:table-row">
                            <td class="block sm:table-cell px-6 py-16 text-center text-[#5c5e66] dark:text-[#b5bac1] w-full" sm-colspan="4">
                                <i class="bi bi-layout-text-window w-12 h-12 mx-auto mb-3 opacity-20"></i>
                                <span class="text-[11px] font-black uppercase tracking-[0.2em] block">No Stores Configured</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Store Modal -->
        <template x-teleport="body">
            <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center sm:px-4 pb-0 sm:pb-10 pt-10 px-0">
                <div class="absolute inset-0 bg-[#1e1f22]/80 backdrop-blur-sm" @click="closeModal()" 
                     x-show="modalOpen" x-transition.opacity></div>
                
                <div class="relative w-full max-w-md bg-white dark:bg-[#313338] rounded-t-xl sm:rounded-xl shadow-2xl p-8 transform transition-all mt-auto sm:mt-0 max-h-[90vh] overflow-y-auto"
                     x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-12 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-12 sm:scale-95">
                    
                    <button @click="closeModal()" class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] text-[#5c5e66] hover:text-discord-red transition-colors">
                        <i class="bi bi-x-lg w-4 h-4"></i>
                    </button>

                    <h3 class="text-2xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight mb-6" x-text="isEdit ? 'Edit Store' : 'New Store'"></h3>

                    <form @submit.prevent="submitForm" class="space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">Store Name *</label>
                            <input type="text" x-model="form.name" required class="w-full px-4 py-3 bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-xl text-[#1e1f22] dark:text-white font-bold focus:ring-2 focus:ring-discord-green transition-all" placeholder="e.g. Home, Client A, Branch 01">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">Tax ID</label>
                            <input type="text" x-model="form.tax_id" class="w-full px-4 py-3 bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-xl text-[#1e1f22] dark:text-white font-bold focus:ring-2 focus:ring-discord-green transition-all" placeholder="Optional">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">Address / Details</label>
                            <textarea x-model="form.address" rows="3" class="w-full px-4 py-3 bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-xl text-[#1e1f22] dark:text-white font-bold focus:ring-2 focus:ring-discord-green transition-all resize-none" placeholder="Optional"></textarea>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="closeModal()" class="flex-1 py-4 bg-[#f2f3f5] dark:bg-[#2b2d31] hover:bg-[#e3e5e8] dark:hover:bg-[#1e1f22] text-[#1e1f22] dark:text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-xl transition-colors">
                                Cancel
                            </button>
                            <button type="submit" :disabled="loading" class="flex-1 py-4 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-xl transition-all shadow-lg shadow-green-500/20 disabled:opacity-50">
                                <span x-show="!loading" x-text="isEdit ? 'Update' : 'Save'"></span>
                                <span x-show="loading">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
        function storeManager() {
            return {
                modalOpen: false,
                isEdit: false,
                loading: false,
                form: { id: null, name: '', tax_id: '', address: '' },
                
                openAddModal() {
                    this.isEdit = false;
                    this.form = { id: null, name: '', tax_id: '', address: '' };
                    this.modalOpen = true;
                },
                
                openEditModal(id, name, tax_id, address) {
                    this.isEdit = true;
                    this.form = { id, name, tax_id, address };
                    this.modalOpen = true;
                },

                closeModal() {
                    this.modalOpen = false;
                    setTimeout(() => {
                        this.form = { id: null, name: '', tax_id: '', address: '' };
                    }, 300);
                },

                async submitForm() {
                    this.loading = true;
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        const url = this.isEdit ? `/stores/${this.form.id}` : '/stores';
                        
                        const res = await fetch(url, {
                            method: this.isEdit ? 'PATCH' : 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });
                        
                        if (!res.ok) {
                            const payload = await res.json().catch(() => ({}));
                            const firstError = payload.errors ? Object.values(payload.errors)[0]?.[0] : null;
                            throw new Error(firstError || payload.message || 'Submission Failed');
                        }

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: this.isEdit ? 'Store updated.' : 'Store created.',
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

                async deleteStore(id) {
                    const confirm = await Swal.fire({
                        title: 'Are you sure?',
                        text: "This brand will be deleted. Any slips/templates connected might break.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ed4245',
                        cancelButtonColor: '#80848e',
                        confirmButtonText: 'Yes, delete it!',
                        background: document.documentElement.classList.contains('dark') ? '#313338' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f2f3f5' : '#1e1f22'
                    });

                    if (confirm.isConfirmed) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                            const res = await fetch(`/stores/${id}`, {
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
