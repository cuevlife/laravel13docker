@extends('layouts.app')

@section('content')
    <div class="w-full px-4 pb-12 sm:px-6 lg:px-8 relative z-10" x-data="{ 
        allFolders: [],
        folders: [],
        search: '', 
        is_loading: false,
        createModalOpen: false,
        deleteModalOpen: false,
        deleteId: null,
        deleteName: '',
        deleteConfirmation: '',
        newProjectName: '',
        newProjectLogo: null,
        newProjectLogoPreview: null,

        // Translations for JS
        trans: {
            success: '{{ __('Success!') }}',
            folder_created: '{{ __('Folder created successfully') }}',
            deleted: '{{ __('Deleted!') }}',
            folder_deleted: '{{ __('Folder deleted successfully') }}',
            error: '{{ __('Error') }}',
            an_error: '{{ __('An error occurred') }}'
        },
        
        async init() {
            await this.fetchInitialData();
        },

        handleLogoChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.newProjectLogo = file;
                this.newProjectLogoPreview = URL.createObjectURL(file);
            }
        },

        async fetchInitialData() {
            this.is_loading = true;
            let url = new URL(window.location.href);
            try {
                const response = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await response.json();
                this.allFolders = json.data;
                this.filterFolders();
            } catch (error) {
                console.error('Fetch error:', error);
            } finally {
                this.is_loading = false;
            }
        },

        filterFolders() {
            if (this.search.trim() === '') {
                this.folders = [...this.allFolders];
            } else {
                const term = this.search.toLowerCase();
                this.folders = this.allFolders.filter(f => f.name.toLowerCase().includes(term));
            }
        },
        
        async submitCreate() {
            if(!this.newProjectName) return;
            this.is_loading = true;
            const formData = new FormData();
            formData.append('name', this.newProjectName);
            if(this.newProjectLogo) formData.append('logo', this.newProjectLogo);
            
            try {
                const res = await fetch('{{ route('workspace.projects.store') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                const data = await res.json();
                if(data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: this.trans.success,
                        text: this.trans.folder_created,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else { 
                    Swal.fire({
                        icon: 'error',
                        title: this.trans.error,
                        text: data.message || this.trans.an_error,
                        confirmButtonColor: '#4f86f7'
                    });
                    this.is_loading = false; 
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: this.trans.error,
                    text: this.trans.an_error,
                    confirmButtonColor: '#4f86f7'
                });
                this.is_loading = false;
            }
        },

        async submitDelete() {
            if(this.deleteConfirmation !== this.deleteName) return;
            this.is_loading = true;
            try {
                const res = await fetch('/projects/' + this.deleteId, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ confirmation: this.deleteConfirmation })
                });
                const data = await res.json();
                if(data.status === 'success') { 
                    Swal.fire({
                        icon: 'success',
                        title: this.trans.deleted,
                        text: this.trans.folder_deleted,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    this.deleteModalOpen = false;
                    await this.fetchInitialData();
                    this.deleteConfirmation = '';
                } else { 
                    Swal.fire({
                        icon: 'error',
                        title: this.trans.error,
                        text: data.message || this.trans.an_error,
                        confirmButtonColor: '#4f86f7'
                    });
                    this.is_loading = false; 
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: this.trans.error,
                    text: this.trans.an_error,
                    confirmButtonColor: '#4f86f7'
                });
                this.is_loading = false;
            }
        }
    }" @open-delete.window="deleteId = $event.detail.id; deleteName = $event.detail.name; deleteModalOpen = true; deleteConfirmation = ''">
        
        <!-- Search Section -->
        <div class="mb-12 mt-6 flex flex-col items-center justify-center">
            <div class="relative w-full max-w-xl" x-data="{ focused: false }">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="absolute left-6 top-1/2 h-5 w-5 -translate-y-1/2 text-[#80848e] transition-colors" :class="focused ? 'text-[#4f86f7]' : ''"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" x-model="search" @input="filterFolders()" @focus="focused = true" @blur="focused = false" placeholder="{{ __('Search Folders...') }}" class="h-14 w-full rounded-full border border-black/[0.04] bg-white pl-14 pr-6 text-sm font-bold text-[#1e1f22] shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] outline-none transition focus:border-[#4f86f7]/30 dark:border-white/5 dark:bg-[#1e1f22] dark:text-white">
                <div x-show="is_loading" class="absolute right-6 top-1/2 -translate-y-1/2" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f86f7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                </div>
            </div>
        </div>

        <!-- Folder Grid -->
        <div class="flex flex-wrap justify-center gap-6">
             <!-- Add Folder Card -->
             <div class="group relative flex flex-col items-center justify-between h-[280px] w-[220px] rounded-[2.5rem] bg-white p-6 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] border border-dashed border-[#e3e5e8] dark:bg-[#2b2d31] dark:border-[#313338] transition-all hover:border-[#4f86f7]/50 cursor-pointer" @click="createModalOpen = true">
                <div class="flex-1 flex flex-col items-center justify-center w-full">
                    <div class="flex h-20 w-20 items-center justify-center rounded-[2rem] bg-[#f2f7ff] text-[#4f86f7] transition-transform group-hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    </div>
                    <h3 class="mt-4 text-[18px] font-black text-[#1e1f22] dark:text-white">{{ __('Add Folder') }}</h3>
                </div>
                
                <button class="flex items-center gap-2 rounded-full bg-[#f2f7ff] px-5 py-2 text-[9px] font-black uppercase tracking-widest text-[#4f86f7] transition hover:bg-[#4f86f7] hover:text-white">
                    {{ __('New Folder') }} <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                </button>
            </div>

            <!-- Folders Loop -->
            <template x-for="folder in folders" :key="folder.id">
                <div class="group relative flex flex-col items-center justify-between h-[280px] w-[220px] rounded-[2.5rem] bg-white p-6 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] border border-black/[0.02] dark:bg-[#2b2d31]">
                    <!-- Delete Button -->
                    <template x-if="folder.is_owner">
                        <button @click="$dispatch('open-delete', {id: folder.id, name: folder.name })" 
                                class="absolute top-5 right-5 flex h-9 w-9 items-center justify-center rounded-full border border-black/[0.08] bg-white text-[#80848e] shadow-md transition hover:bg-rose-50 hover:text-rose-500 hover:border-rose-200 dark:bg-[#1e1f22] dark:border-white/10 dark:hover:bg-rose-500/10 z-20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        </button>
                    </template>

                    <div class="flex-1 flex flex-col items-center justify-center w-full">
                        <!-- Icon/Logo Area -->
                        <div class="relative h-20 w-20 overflow-hidden rounded-[2rem] shadow-[0_8px_20px_-6px_rgba(35,165,89,0.3)] transition-transform group-hover:scale-105 bg-gradient-to-br from-[#12a170] to-[#0a6646]">
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
                    <a :href="folder.open_url" 
                       class="flex items-center gap-2 rounded-full bg-[#f2f7ff] px-5 py-2 text-[9px] font-black uppercase tracking-widest text-[#4f86f7] transition hover:bg-[#4f86f7] hover:text-white">
                        {{ __('Open Folder') }} 
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
            </template>
        </div>

        <!-- Create Modal -->
        <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-white/60 backdrop-blur-xl dark:bg-black/60" @click="!is_loading && (createModalOpen = false)"></div>
            <div class="relative z-10 w-full max-w-sm overflow-hidden rounded-[3rem] bg-white p-8 shadow-2xl dark:bg-[#2b2d31]">
                <div class="mb-8 text-center">
                    <h3 class="text-xl font-black text-[#1e1f22] dark:text-white uppercase tracking-tight">{{ __('Create Folder') }}</h3>
                </div>
                
                <div class="space-y-6">
                    <div class="flex flex-col items-center">
                        <label class="group relative flex h-28 w-28 cursor-pointer items-center justify-center overflow-hidden rounded-[2.5rem] border-2 border-dashed border-[#e3e5e8] bg-[#f8fafb] transition hover:border-[#4f86f7] dark:border-[#313338] dark:bg-[#1e1f22]">
                            <template x-if="newProjectLogoPreview">
                                <img :src="newProjectLogoPreview" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!newProjectLogoPreview">
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
                        <input type="text" x-model="newProjectName" placeholder="{{ __('Enter folder name...') }}" class="w-full rounded-[1.2rem] border border-[#e3e5e8] bg-[#f8fafb] px-4 py-3 text-sm font-bold outline-none transition focus:border-[#4f86f7]/50 dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button @click="createModalOpen = false" :disabled="is_loading" class="h-12 flex-1 rounded-full text-[11px] font-black uppercase tracking-widest text-[#5c5e66] hover:bg-black/5 dark:text-[#b5bac1] transition">{{ __('Cancel') }}</button>
                        <button @click="submitCreate()" :disabled="is_loading || !newProjectName" class="h-12 flex-1 rounded-full bg-[#4f86f7] text-[11px] font-black uppercase tracking-widest text-white transition hover:bg-[#3d6de0] disabled:opacity-50 shadow-lg shadow-blue-500/20">
                            {{ __('Create') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-white/40 backdrop-blur-md dark:bg-black/40" @click="!is_loading && (deleteModalOpen = false)"></div>
            <div class="relative z-10 w-full max-w-sm rounded-[3rem] bg-white p-8 shadow-2xl dark:bg-[#313338]">
                <div class="mb-6 text-center text-discord-red">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto h-12 w-12"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                    <h3 class="mt-2 text-xl font-black uppercase tracking-tight">{{ __('Delete Folder') }}</h3>
                </div>
                
                <p class="mb-6 text-center text-xs font-bold leading-relaxed text-[#5c5e66] dark:text-[#b5bac1]">
                    {{ __('Delete Permanent Warning') }} <span class="font-black text-discord-red underline" x-text="deleteName"></span>
                </p>

                <input type="text" x-model="deleteConfirmation" placeholder="{{ __('Type to confirm') }}" class="mb-6 w-full rounded-[1.2rem] border border-discord-red/20 bg-discord-red/5 px-4 py-4 text-center text-sm font-bold text-discord-red outline-none focus:border-discord-red/50">

                <div class="flex flex-col gap-2">
                    <button @click="submitDelete()" :disabled="is_loading || deleteConfirmation !== deleteName" class="h-12 w-full rounded-full bg-discord-red text-[11px] font-black uppercase tracking-widest text-white transition hover:bg-red-600 disabled:opacity-50 shadow-lg shadow-red-500/20">
                        {{ __('Delete Folder') }}
                    </button>
                    <button @click="deleteModalOpen = false" class="h-12 w-full text-[11px] font-black uppercase tracking-widest text-[#5c5e66] dark:text-[#b5bac1]">{{ __('Go Back') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
