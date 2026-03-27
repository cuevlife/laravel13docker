<x-app-layout>
    <div x-data="templateRegistry()" class="space-y-8 animate-in fade-in duration-700 pb-20">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 bg-discord-green rounded-full"></div>
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight leading-none">Extraction Profiles</h2>
                    <p class="text-[9px] md:text-[10px] font-bold text-[#80848e] uppercase tracking-[0.2em] mt-1.5">Node Prompt Configurations</p>
                </div>
            </div>

            <button @click="openAddModal()" 
                    class="w-full sm:w-auto px-5 py-3 md:py-3.5 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[14px] transition-all shadow-md shadow-green-500/20 active:scale-95 flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> 
                <span>Create Profile</span>
            </button>
        </div>

        <!-- Templates Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap block sm:table">
                <thead class="hidden sm:table-header-group">
                    <tr class="border-b border-[#e3e5e8] dark:border-[#313338] text-[10px] uppercase font-black tracking-widest text-[#80848e] bg-transparent">
                        <th class="px-6 py-4">Profile Name</th>
                        <th class="px-6 py-4">Linked Store</th>
                        <th class="px-6 py-4">Knowledge Schema</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="block sm:table-row-group divide-y divide-[#e3e5e8] dark:divide-[#313338]">
                    @forelse($templates as $template)
                        <tr class="block sm:table-row p-5 sm:p-0 hover:bg-[#e3e5e8]/30 dark:hover:bg-[#313338]/30 transition-colors group relative">
                            <td class="block sm:table-cell px-0 py-2 sm:px-4 sm:py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-[12px] bg-white dark:bg-[#2b2d31] flex items-center justify-center border border-[#e3e5e8] dark:border-[#313338] group-hover:border-discord-green/30 text-discord-green shadow-sm shrink-0 transition-colors">
                                        <i data-lucide="file-json" class="w-5 h-5"></i>
                                    </div>
                                    <div class="pr-16 sm:pr-0">
                                        <h4 class="text-sm font-black text-[#1e1f22] dark:text-white truncate max-w-[200px] sm:max-w-none">{{ $template->name }}</h4>
                                    </div>
                                </div>
                            </td>
                            <td class="flex items-center justify-between sm:table-cell px-0 py-2 sm:px-4 sm:py-4 mt-2 sm:mt-0">
                                <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-[#80848e]">Store</span>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="store" class="w-4 h-4 text-[#5c5e66] dark:text-[#b5bac1] hidden sm:block"></i>
                                    <p class="text-[11px] font-bold text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest">{{ $template->merchant->name ?? 'Unlinked' }}</p>
                                </div>
                            </td>
                            <td class="block sm:table-cell px-0 py-2 sm:px-4 sm:py-4 border-t border-dashed border-[#e3e5e8] dark:border-[#313338] sm:border-0 pt-3 sm:pt-4">
                                <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-[#80848e] mb-2 block">Schema Fields</span>
                                <div class="flex items-center gap-1.5 flex-wrap max-w-full sm:max-w-[250px]">
                                    @foreach(array_slice($template->ai_fields ?? [], 0, 3) as $field)
                                        <span class="px-2 py-1 bg-[#f2f3f5] dark:bg-[#313338] border border-[#e3e5e8] dark:border-transparent rounded-md text-[9px] font-black text-[#80848e] dark:text-[#b5bac1] uppercase tracking-tighter">{{ $field['label'] ?? $field['key'] }}</span>
                                    @endforeach
                                    @if(count($template->ai_fields ?? []) > 3)
                                        <span class="px-2 py-1 bg-discord-green/10 border border-discord-green/20 rounded-md text-[9px] font-black text-discord-green uppercase tracking-tighter">+{{ count($template->ai_fields) - 3 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="absolute top-5 right-5 sm:static sm:table-cell sm:px-4 sm:py-4">
                                <div class="flex items-center justify-end gap-1.5 sm:gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.templates.edit', $template->id, false) }}" class="p-2 bg-white dark:bg-[#313338] rounded-[10px] text-[#5c5e66] dark:text-[#b5bac1] hover:text-[#1e1f22] dark:hover:text-white transition-all shadow-sm relative z-20">
                                        <span class="text-[10px] font-black pr-1 uppercase tracking-widest hidden lg:inline-block">Schema</span>
                                        <i data-lucide="settings-2" class="w-4 h-4 lg:hidden"></i>
                                    </a>
                                    <button @click="deleteTemplate({{ $template->id }})" class="p-2 bg-white dark:bg-[#313338] rounded-[10px] text-[#5c5e66] dark:text-[#b5bac1] hover:text-discord-red transition-all shadow-sm relative z-20">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block sm:table-row">
                            <td class="block sm:table-cell px-6 py-16 text-center text-[#5c5e66] dark:text-[#b5bac1] w-full" sm-colspan="4">
                                <i data-lucide="layout-template" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                                <span class="text-[11px] font-black uppercase tracking-[0.2em] block">No Profiles Configured</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Profile Modal -->
        <template x-teleport="body">
            <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center sm:px-4 pb-0 sm:pb-10 pt-10 px-0">
                <div class="absolute inset-0 bg-[#1e1f22]/80 backdrop-blur-sm" @click="closeModal()" 
                     x-show="modalOpen" x-transition.opacity></div>
                
                <div class="relative w-full max-w-md bg-white dark:bg-[#313338] rounded-t-[2.5rem] sm:rounded-[2.5rem] shadow-2xl p-8 transform transition-all mt-auto sm:mt-0 max-h-[90vh] overflow-y-auto"
                     x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-12 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-12 sm:scale-95">
                    
                    <button @click="closeModal()" class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center rounded-full bg-[#f2f3f5] dark:bg-[#2b2d31] text-[#5c5e66] hover:text-discord-red transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>

                    <h3 class="text-2xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight mb-6">New Profile</h3>
                    <p class="text-[11px] text-[#5c5e66] dark:text-[#b5bac1] leading-relaxed mb-6 font-medium">Create a new schema to teach the AI what to extract from receipt images. You must link it to a pre-registered Store.</p>

                    <form @submit.prevent="submitForm" class="space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">Profile Name *</label>
                            <input type="text" x-model="form.name" required class="w-full px-4 py-3 bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[16px] text-[#1e1f22] dark:text-white font-bold focus:ring-2 focus:ring-discord-green transition-all" placeholder="e.g. Makro Tax Invoice">
                        </div>

                        <div class="space-y-1.5 relative">
                            <label class="text-[10px] font-black text-[#5c5e66] dark:text-[#b5bac1] uppercase tracking-widest pl-2">Linked Store *</label>
                            <select x-model="form.merchant_id" required class="w-full px-4 py-3 bg-[#f2f3f5] dark:bg-[#1e1f22] border-0 rounded-[16px] text-[#1e1f22] dark:text-white font-bold focus:ring-2 focus:ring-discord-green transition-all appearance-none outline-none">
                                <option value="" disabled>Select a Store...</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-[32px] pointer-events-none">
                                <i data-lucide="chevron-down" class="w-4 h-4 text-[#80848e]"></i>
                            </div>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="closeModal()" class="flex-1 py-4 bg-[#f2f3f5] dark:bg-[#2b2d31] hover:bg-[#e3e5e8] dark:hover:bg-[#1e1f22] text-[#1e1f22] dark:text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[16px] transition-colors">
                                Cancel
                            </button>
                            <button type="submit" :disabled="loading" class="flex-1 py-4 bg-discord-green hover:bg-[#1f8b4c] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[16px] transition-all shadow-lg shadow-green-500/20 disabled:opacity-50">
                                <span x-show="!loading">Create</span>
                                <span x-show="loading">Creating...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
        function templateRegistry() {
            return {
                modalOpen: false,
                loading: false,
                form: { name: '', merchant_id: '' },
                
                openAddModal() {
                    this.form = { name: '', merchant_id: '' };
                    this.modalOpen = true;
                },

                closeModal() {
                    this.modalOpen = false;
                    setTimeout(() => this.form = { name: '', merchant_id: '' }, 300);
                },

                async submitForm() {
                    this.loading = true;
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        const res = await fetch('/templates/store', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });
                        
                        if (!res.ok) throw new Error('Creation Failed');

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Profile Created',
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

                async deleteTemplate(id) {
                    const confirm = await Swal.fire({
                        title: 'Delete this profile?',
                        text: "Slips relying on this template might lose formatting instructions.",
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
                            const res = await fetch(`/templates/delete/${id}`, {
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
