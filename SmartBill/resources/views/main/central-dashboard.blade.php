@extends('layouts.app')

@section('content')
    <div class="w-full px-4 pb-12 sm:px-6 lg:px-8 relative z-10" x-data="{ 
        allFolders: [],
        folders: [],
        search: '', 
        is_loading: false,
        deleteId: null,
        deleteName: '',
        deleteConfirmation: '',
        
        // Create/Edit State
        modalMode: 'create', // 'create' or 'edit'
        editId: null,
        folderForm: {
            name: '',
            logo: null,
            logoPreview: null
        },

        ownedFoldersCount: {{ $initialData['meta']['owned_folders_count'] ?? 0 }},
        maxFolders: {{ $initialData['meta']['max_folders'] ?? 3 }},
        showAll: false,
        isSuperAdmin: {{ ($initialData['meta']['is_super_admin'] ?? false) ? "true" : "false" }},

        // Translations for JS
        trans: {
            success: '{{ __('Success!') }}',
            folder_created: '{{ __('Folder created successfully') }}',
            folder_updated: '{{ __('Folder updated successfully') }}',
            deleted: '{{ __('Deleted!') }}',
            folder_deleted: '{{ __('Folder deleted successfully') }}',
            error: '{{ __('Error') }}',
            an_error: '{{ __('An error occurred') }}',
            limit_reached: '{{ __('Folder limit reached') }}',
            limit_msg: '{{ __('You have reached your maximum folder limit.') }}',
            show_all: '{{ __('Show All Folders') }}',
            show_mine: '{{ __('Show My Folders') }}'
        },
        
        async init() {
            await this.fetchInitialData();
        },

        handleLogoChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.folderForm.logo = file;
                this.folderForm.logoPreview = URL.createObjectURL(file);
            }
        },

        async fetchInitialData() {
            this.is_loading = true;
            let url = new URL(window.location.href);
            if (this.showAll) url.searchParams.set('all', '1');
            
            try {
                const response = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await response.json();
                this.allFolders = json.data;
                if (json.meta) {
                    this.ownedFoldersCount = json.meta.owned_folders_count;
                    this.maxFolders = json.meta.max_folders;
                    this.isSuperAdmin = json.meta.is_super_admin;
                }
                this.filterFolders();
            } catch (error) {
                console.error('Fetch error:', error);
            } finally {
                this.is_loading = false;
            }
        },

        async toggleShowAll() {
            this.showAll = !this.showAll;
            await this.fetchInitialData();
        },

        openCreateModal() {
            if (this.ownedFoldersCount >= this.maxFolders) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: this.trans.limit_reached,
                    text: this.trans.limit_msg + ' (' + this.maxFolders + ')',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }
            this.modalMode = 'create';
            this.folderForm.name = '';
            this.folderForm.logo = null;
            this.folderForm.logoPreview = null;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'folder-form' } }));
        },

        openEditModal(folder) {
            this.modalMode = 'edit';
            this.editId = folder.id;
            this.folderForm.name = folder.name;
            this.folderForm.logo = null;
            this.folderForm.logoPreview = folder.logo_url;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'folder-form' } }));
        },

        filterFolders() {
            if (this.search.trim() === '') {
                this.folders = [...this.allFolders];
            } else {
                const term = this.search.toLowerCase();
                this.folders = this.allFolders.filter(f => f.name.toLowerCase().includes(term));
            }
        },
        
        async submitForm() {
            if(!this.folderForm.name) return;
            this.is_loading = true;
            const formData = new FormData();
            formData.append('name', this.folderForm.name);
            if(this.folderForm.logo) formData.append('logo', this.folderForm.logo);
            
            let url = '{{ route('workspace.folders.store') }}';
            let method = 'POST';

            if (this.modalMode === 'edit') {
                url = '/folders/' + this.editId;
                formData.append('_method', 'PATCH');
            }
            
            try {
                const res = await fetch(url, {
                    method: 'POST', // Use POST with _method PATCH for file uploads
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                const data = await res.json();
                if(data.status === 'success') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: this.modalMode === 'create' ? this.trans.folder_created : this.trans.folder_updated,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    }).then(() => {
                        if (this.modalMode === 'create') {
                            window.location.href = data.redirect;
                        } else {
                            window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'folder-form' } }));
                            this.fetchInitialData();
                        }
                    });
                } else { 
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: this.trans.error,
                        text: data.message || this.trans.an_error,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    this.is_loading = false; 
                }
            } catch (e) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: this.trans.error,
                    text: this.trans.an_error,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                this.is_loading = false;
            }
        },

        async submitDelete() {
            if(this.deleteConfirmation !== this.deleteName) return;
            this.is_loading = true;
            try {
                const res = await fetch('/folders/' + this.deleteId, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ confirmation: this.deleteConfirmation })
                });
                const data = await res.json();
                if(data.status === 'success') { 
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: this.trans.folder_deleted,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: 'folder-delete' } }));
                    await this.fetchInitialData();
                    this.deleteConfirmation = '';
                } else { 
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: this.trans.error,
                        text: data.message || this.trans.an_error,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    this.is_loading = false; 
                }
            } catch (e) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: this.trans.error,
                    text: this.trans.an_error,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                this.is_loading = false;
            }
        }
    }" @open-delete.window="deleteId = $event.detail.id; deleteName = $event.detail.name; window.dispatchEvent(new CustomEvent('open-modal', { detail: { name: 'folder-delete' } })); deleteConfirmation = ''">
        
        <!-- Usage Summary & Search Section -->
        <div class="mb-12 mt-6 flex flex-col items-center justify-center">
            <div class="flex w-full max-w-2xl items-center gap-3">
                {{-- Search Bar --}}
                <div class="relative flex-1" x-data="{ focused: false }">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="absolute left-6 top-1/2 h-5 w-5 -translate-y-1/2 text-[#80848e] transition-colors" :class="focused ? 'text-discord-green' : ''"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" x-model="search" @input="filterFolders()" @focus="focused = true" @blur="focused = false" placeholder="{{ __('Search Folders...') }}" class="h-14 w-full rounded-full border border-black/[0.04] bg-white pl-14 pr-6 text-sm font-bold text-[#1e1f22] shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] outline-none transition focus:border-discord-green/30 dark:border-white/5 dark:bg-[#1e1f22] dark:text-white">
                    <div x-show="is_loading" class="absolute right-6 top-1/2 -translate-y-1/2" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#23a559" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    </div>
                </div>

                {{-- Admin Toggle: All Folders (Right Side) --}}
                <template x-if="isSuperAdmin">
                    <div class="flex shrink-0 items-center gap-1 p-1 bg-white dark:bg-[#1e1f22] rounded-full border border-black/[0.05] dark:border-white/5 shadow-sm h-14">
                        <button @click="toggleShowAll()" 
                                class="h-full px-5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap"
                                :class="!showAll ? 'bg-discord-green text-white shadow-md' : 'text-[#80848e] hover:bg-black/5 dark:hover:bg-white/5'">
                            {{ __('My') }}
                        </button>
                        <button @click="toggleShowAll()" 
                                class="h-full px-5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap"
                                :class="showAll ? 'bg-indigo-500 text-white shadow-md' : 'text-[#80848e] hover:bg-black/5 dark:hover:bg-white/5'">
                            {{ __('All') }}
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Folder Grid -->
        <div class="flex flex-wrap justify-center gap-6">
             @php
                $canCreate = auth()->user()->isSuperAdmin() || (auth()->user()->role === 1); // Only Admin/Specific role can create
             @endphp
             
             @if($canCreate)
             <!-- Add Folder Card -->
             <div class="group relative flex flex-col items-center justify-between h-[300px] w-[220px] rounded-xl bg-white p-6 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] border border-dashed border-[#e3e5e8] dark:bg-[#2b2d31] dark:border-white/10 transition-all hover:border-discord-green/50 cursor-pointer" @click="openCreateModal()">
                <!-- Folder Limit Badge -->
                <div class="absolute top-5 left-5 px-2 py-0.5 rounded-lg bg-black/5 dark:bg-white/5 text-[9px] font-black text-[#80848e] uppercase tracking-widest">
                    <span x-text="ownedFoldersCount + ' / ' + maxFolders"></span>
                </div>
                <div class="flex-1 flex flex-col items-center justify-center w-full">
                    <div class="flex h-20 w-20 items-center justify-center rounded-xl bg-[#f2f7ff] dark:bg-white/5 text-discord-green transition-transform group-hover:scale-105">
                        <i class="bi bi-plus-lg text-4xl"></i>
                    </div>
                    <h3 class="mt-4 text-[18px] font-black text-[#1e1f22] dark:text-white">{{ __('Add Folder') }}</h3>
                </div>
                
                <x-ui.button variant="success" size="sm" icon="bi bi-plus-lg" @click="openCreateModal()">
                    {{ __('New Folder') }}
                </x-ui.button>
            </div>
            @endif

            <!-- Folders Loop -->
            <template x-for="folder in folders" :key="folder.id" x-transition>
                <div class="group relative flex flex-col items-center justify-between h-[300px] w-[220px] rounded-xl bg-white p-6 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] border border-black/[0.02] dark:bg-[#2b2d31] dark:border-white/[0.03] transition-all hover:shadow-md hover:border-discord-green/20">
                    
                    <!-- Slip Count Badge (Top Left) -->
                    <div class="absolute top-5 left-5 px-2 py-0.5 rounded-lg bg-black/5 dark:bg-white/5 text-[9px] font-black text-[#80848e] uppercase tracking-widest">
                        <span x-text="folder.slips_count.toLocaleString() + ' {{ __('Slips') }}'"></span>
                    </div>

                    <!-- Actions (Top Right) -->
                    <div class="absolute top-5 right-5 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                        <template x-if="folder.is_owner">
                            <div class="flex items-center gap-1.5">
                                <button @click="openEditModal(folder)" 
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-black/[0.08] dark:border-white/10 bg-white dark:bg-[#1e1f22] text-[#80848e] shadow-sm transition hover:text-discord-green"
                                        title="{{ __('Edit Folder') }}">
                                    <i class="bi bi-pencil-fill text-[10px]"></i>
                                </button>
                                <button @click="$dispatch('open-delete', {id: folder.id, name: folder.name })" 
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-black/[0.08] dark:border-white/10 bg-white dark:bg-[#1e1f22] text-[#80848e] shadow-sm transition hover:bg-discord-red/10 hover:text-discord-red"
                                        title="{{ __('Delete Folder') }}">
                                    <i class="bi bi-trash3-fill text-[10px]"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    <div class="flex-1 flex flex-col items-center justify-center w-full">
                        <!-- Icon/Logo Area -->
                        <div class="relative h-20 w-20 overflow-hidden rounded-xl shadow-[0_8px_20px_-6px_rgba(35,165,89,0.3)] transition-transform group-hover:scale-110 bg-gradient-to-br from-[#12a170] to-[#0a6646]">
                            <template x-if="folder.logo_url">
                                <img :src="folder.logo_url" class="h-full w-full object-cover" loading="lazy">
                            </template>
                            <template x-if="!folder.logo_url">
                                <div class="flex h-full w-full items-center justify-center text-3xl font-black text-white" x-text="folder.name.charAt(0).toUpperCase()"></div>
                            </template>
                        </div>

                        <h3 class="mt-4 text-[18px] font-black text-[#1e1f22] dark:text-white truncate w-full text-center px-2" x-text="folder.name"></h3>
                    </div>
                    
                    <!-- Open Folder Button -->
                    <x-ui.button variant="success" size="sm" icon="bi bi-arrow-right" ::href="folder.open_url">
                        {{ __('Open Folder') }}
                    </x-ui.button>
                </div>
            </template>
        </div>

        <!-- Folder Form Modal (Create/Edit) -->
        <x-ui.modal name="folder-form" maxWidth="sm" :backdropClose="false">
            <div class="p-8">
                <div class="mb-8 text-center">
                    <h3 class="text-xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight" x-text="modalMode === 'create' ? '{{ __('Create Folder') }}' : '{{ __('Edit Folder') }}'"></h3>
                </div>
                
                <div class="space-y-6">
                    <div class="flex flex-col items-center">
                        <label class="group relative flex h-28 w-28 cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-[#e3e5e8] bg-[#f8fafb] transition hover:border-discord-green dark:border-[#313338] dark:bg-[#1e1f22]">
                            <template x-if="folderForm.logoPreview">
                                <img :src="folderForm.logoPreview" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!folderForm.logoPreview">
                                <div class="flex flex-col items-center text-[#80848e]">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-image"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                    <span class="mt-1 text-[10px] font-black uppercase tracking-widest">{{ __('Logo') }}</span>
                                </div>
                            </template>
                            <input type="file" class="hidden" @change="handleLogoChange">
                        </label>
                    </div>

                    <div>
                        <label class="mb-1.5 ml-1 block text-[10px] font-black uppercase tracking-widest text-[#5c5e66] dark:text-[#949ba4]">{{ __('Folder Name') }}</label>
                        <input type="text" x-model="folderForm.name" placeholder="{{ __('Enter folder name...') }}" class="w-full rounded-xl border border-[#e3e5e8] bg-[#f8fafb] px-4 py-3 text-sm font-bold outline-none transition focus:border-discord-green/50 dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button @click="$dispatch('close-modal', {name: 'folder-form'})" :disabled="is_loading" class="h-12 flex-1 rounded-xl text-[11px] font-black uppercase tracking-widest text-[#5c5e66] hover:bg-black/5 dark:text-[#b5bac1] transition">{{ __('Cancel') }}</button>
                        <button @click="submitForm()" :disabled="is_loading || !folderForm.name" class="h-12 flex-1 rounded-xl bg-discord-green text-[11px] font-black uppercase tracking-widest text-white transition hover:bg-[#1f8b4c] disabled:opacity-50 shadow-lg shadow-green-500/20">
                            <span x-text="modalMode === 'create' ? '{{ __('Create') }}' : '{{ __('Save') }}'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </x-ui.modal>

        <!-- Delete Folder Modal -->
        <x-ui.modal name="folder-delete" maxWidth="sm" :backdropClose="false">
            <div class="p-8">
                <div class="mb-6 text-center text-discord-red">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto h-12 w-12"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                    <h3 class="mt-2 text-xl font-black uppercase tracking-tight">{{ __('Delete Folder') }}</h3>
                </div>
                
                <p class="mb-6 text-center text-xs font-bold leading-relaxed text-[#5c5e66] dark:text-[#b5bac1]">
                    {{ __('Delete Permanent Warning') }} <span class="font-black text-discord-red underline" x-text="deleteName"></span>
                </p>

                <input type="text" x-model="deleteConfirmation" placeholder="{{ __('Type to confirm') }}" class="mb-6 w-full rounded-xl border border-discord-red/20 bg-discord-red/5 px-4 py-4 text-center text-sm font-bold text-discord-red outline-none focus:border-discord-red/50">

                <div class="flex flex-col gap-2">
                    <button @click="submitDelete()" :disabled="is_loading || deleteConfirmation !== deleteName" class="h-12 w-full rounded-xl bg-discord-red text-[11px] font-black uppercase tracking-widest text-white transition hover:bg-[#da373c] disabled:opacity-50 shadow-lg shadow-red-500/20">
                        {{ __('Delete Folder') }}
                    </button>
                    <button @click="$dispatch('close-modal', {name: 'folder-delete'})" class="h-12 w-full rounded-xl text-[11px] font-black uppercase tracking-widest text-[#5c5e66] dark:text-[#b5bac1]">{{ __('Go Back') }}</button>
                </div>
            </div>
        </x-ui.modal>
    </div>
@endsection
