@extends('layouts.app')

@section('content')
    <div class="w-full py-8 px-4 sm:px-6 lg:px-8 animate-in fade-in duration-500">
        
        <x-ui.card>
                        {{-- Header Section --}}
            <div class="px-8 py-8 border-b border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/10">
                <x-ui.page-header 
                    :title="__('User Details')" 
                    :subtitle="__('Registry ID:') . ' #' . $user->id"
                >
                    <x-slot:icon_slot>
                        <x-ui.back-button :href="route('admin.users')" :title="__('Back to Users')" />
                    </x-slot:icon_slot>
                    <x-slot:actions>
                        <span class="px-3 py-1 rounded-xl {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} text-[9px] font-black uppercase tracking-widest border shadow-sm">
                            {{ strtoupper(__($user->status)) }}
                        </span>
                    </x-slot:actions>
                </x-ui.page-header>
            </div>

            <div class="p-8 md:p-10 space-y-12 divide-y divide-black/[0.03] dark:divide-white/[0.03]">

                <!-- Section 1: Token Pool Management -->
                <div class="pt-0 space-y-6" x-data="tokenAdjuster()">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-amber-400 rounded-full"></div>
                        <h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Token Balance Control') }}</h2>
                    </div>

                    <div class="p-6 rounded-xl border border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/10">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                            <!-- Balance Display -->
                            <div class="flex items-center gap-5 shrink-0">
                                <div class="h-14 w-14 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/[0.03] flex items-center justify-center text-amber-500 shadow-sm">
                                    <i class="bi bi-coin text-3xl"></i>
                                </div>
                                <div>
                                    <div class="text-[9px] font-black text-[#80848e] uppercase tracking-widest">{{ __('Available Balance') }}</div>
                                    <div class="text-3xl font-black text-[#1e1f22] dark:text-white leading-none mt-1" x-text="numberFormat(user.tokens) + ' '"></div>
                                    <div class="text-[10px] font-bold text-[#80848e] mt-1 uppercase tracking-widest">{{ __('Tokens') }}</div>
                                </div>
                            </div>

                            <!-- Adjustment Form -->
                            <form @submit.prevent="submitForm" class="flex flex-col md:flex-row items-end gap-3 flex-1 lg:max-w-4xl">
                                <div class="flex-1 w-full">
                                    <label class="block text-[8px] font-black uppercase text-[#80848e] mb-2 ml-1 tracking-widest">{{ __('Operation') }}</label>
                                    <select x-model="form.operation" class="w-full h-11 rounded-xl border border-black/[0.05] bg-white dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white outline-none focus:ring-2 focus:ring-discord-green/10 transition-all cursor-pointer shadow-sm">
                                        <option value="add">{{ __('Deposit Tokens (+)') }}</option>
                                        <option value="deduct">{{ __('Withdraw Tokens (-)') }}</option>
                                        <option value="set">{{ __('Overwrite Balance (=)') }}</option>
                                    </select>
                                </div>
                                <div class="w-full md:w-36">
                                    <label class="block text-[8px] font-black uppercase text-[#80848e] mb-2 ml-1 tracking-widest">{{ __('Amount') }}</label>
                                    <input x-model="form.tokens" type="number" required class="w-full h-11 rounded-xl border border-black/[0.05] bg-white dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white outline-none focus:ring-2 focus:ring-discord-green/10 transition-all text-right shadow-sm">
                                </div>
                                <div class="flex-[1.5] w-full">
                                    <label class="block text-[8px] font-black uppercase text-[#80848e] mb-2 ml-1 tracking-widest">{{ __('Audit Note') }}</label>
                                    <input x-model="form.note" type="text" placeholder="{{ __('Internal remark...') }}" class="w-full h-11 rounded-xl border border-black/[0.05] bg-white dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white outline-none focus:ring-2 focus:ring-discord-green/10 transition-all shadow-sm">
                                </div>
                                <x-ui.button type="submit" variant="success" ::disabled="loading" class="h-11 shadow-lg shadow-green-500/20">
                                    <span x-show="!loading">{{ __('Apply Update') }}</span>
                                    <span x-show="loading"><i class="bi bi-arrow-repeat animate-spin"></i></span>
                                </x-ui.button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Account Permissions -->
                <div class="pt-10 space-y-8">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                        <h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Permission Layer') }}</h2>
                    </div>

                    <form method="POST" action="{{ route('admin.users.role', ['user' => $user->id]) }}" class="w-full">
                        @csrf @method('PATCH')

                        <div class="bg-[#f8fafb] dark:bg-black/10 p-6 rounded-xl border border-black/[0.03] dark:border-white/[0.03]">
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">
                                <div>
                                    <label class="block text-[8px] font-black uppercase text-[#80848e] mb-3 ml-1 tracking-widest">{{ __('Identity Profile') }}</label>
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/[0.03] flex items-center justify-center text-rose-500 font-black text-lg shadow-sm">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-[13px] font-black text-[#1e1f22] dark:text-white leading-none">{{ $user->name }}</div>
                                            <div class="text-[10px] font-bold text-[#80848e] mt-1.5 uppercase tracking-widest">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 xl:col-span-2">
                                    <div class="space-y-2">
                                        <label class="block text-[8px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Access Role') }}</label>
                                        <select name="role" class="w-full h-11 rounded-xl border border-black/[0.05] bg-white dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white focus:ring-2 focus:ring-discord-green/10 outline-none transition-all cursor-pointer shadow-sm">
                                            <option value="{{ \App\Models\User::ROLE_USER }}" @selected((int) $user->role === \App\Models\User::ROLE_USER)>{{ __('Standard User') }}</option>
                                            <option value="{{ \App\Models\User::ROLE_TENANT_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_TENANT_ADMIN)>{{ __('Tenant Admin') }}</option>
                                            <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}" @selected((int) $user->role === \App\Models\User::ROLE_SUPER_ADMIN)>{{ __('Super Admin') }}</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[8px] font-black uppercase text-[#80848e] ml-1 tracking-widest">{{ __('Folder Allowance') }}</label>
                                        <div class="flex items-center gap-3">
                                            <input type="number" name="max_folders" value="{{ old('max_folders', $user->max_folders ?? 3) }}" min="1" max="100" class="flex-1 h-11 rounded-xl border border-black/[0.05] bg-white dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white focus:ring-2 focus:ring-discord-green/10 outline-none transition-all shadow-sm">
                                            <x-ui.button type="submit" variant="primary" class="h-11">
                                                {{ __('Commit Changes') }}
                                            </x-ui.button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Section 3: Managed Folders -->
                <div class="pt-10 space-y-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                            <h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Managed Folder Entities') }}</h2>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-[10px] font-black text-[#80848e] uppercase tracking-widest">
                                {{ $workspaceSnapshots->count() }} / {{ $user->max_folders }} {{ __('Folders') }}
                            </div>
                            @if($workspaceSnapshots->count() < $user->max_folders)
                                <x-ui.button variant="success" size="sm" icon="bi-plus-lg" @click="$dispatch('open-modal', { name: 'create-folder-modal' })">
                                    {{ __('Create New Folder') }}
                                </x-ui.button>
                            @else
                                <x-ui.button disabled variant="ghost" size="sm" icon="bi-lock-fill" class="opacity-60">
                                    {{ __('Limit Reached') }}
                                </x-ui.button>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @forelse($workspaceSnapshots as $workspace)
                            <div class="group flex items-center justify-between p-5 rounded-xl border border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/5 hover:bg-white dark:hover:bg-[#1e1f22] hover:border-discord-green/20 hover:shadow-sm transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="relative w-12 h-12 overflow-hidden rounded-xl shadow-[0_8px_20px_-6px_rgba(35,165,89,0.3)] transition-transform group-hover:scale-110 bg-gradient-to-br from-[#12a170] to-[#0a6646] flex items-center justify-center border border-black/[0.03]">
                                        @if($workspace->logo_url)
                                            <img src="{{ $workspace->logo_url }}" class="h-full w-full object-cover" loading="lazy">
                                        @else
                                            <span class="text-lg font-black text-white">{{ strtoupper(substr($workspace->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-[13px] font-black text-[#1e1f22] dark:text-white truncate max-w-[150px]">{{ $workspace->name }}</div>
                                        <div class="text-[9px] font-black text-[#80848e] uppercase tracking-widest mt-1.5">{{ number_format($workspace->slips_count) }} {{ __('Slips') }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">      
                                    <a href="{{ route('admin.folders.show', ['merchant' => $workspace->id]) }}" class="h-8 w-8 rounded-xl bg-white dark:bg-[#2b2d31] border border-black/[0.05] flex items-center justify-center text-[#80848e] hover:text-discord-green transition-all shadow-sm">
                                        <i class="bi bi-gear-fill text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-16 text-center text-[10px] font-black text-[#80848e] italic uppercase tracking-widest bg-[#f8fafb] dark:bg-black/5 rounded-xl border border-dashed border-black/[0.05]">
                                {{ __('This account has not created any folders yet') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Section 4: Account Lifecycle -->
                <div class="pt-10 space-y-8 pb-10">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-4 bg-rose-500 rounded-full"></div>
                        <h2 class="text-[10px] font-black uppercase tracking-widest text-rose-600">{{ __('Danger Zone') }}</h2>   
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-6 rounded-xl border border-amber-100 bg-amber-50/30 dark:bg-amber-500/5 dark:border-amber-500/10 flex flex-col justify-between gap-4">
                            <div>
                                <h3 class="text-[10px] font-black uppercase text-amber-700 dark:text-amber-500 tracking-widest">{{ __('Suspend Access') }}</h3>
                                <p class="text-[9px] font-bold text-amber-600/80 dark:text-amber-600 uppercase tracking-widest mt-2 leading-relaxed">
                                    {{ $user->status === 'active' ? __('Revoke system access immediately for this entity.') : __('Restore and reactivate system access for this entity.') }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('admin.users.status', ['user' => $user->id]) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}">
                                <x-ui.button type="submit" variant="warning" class="w-full">
                                    {{ $user->status === 'active' ? __('Suspend Account') : __('Reactivate Entity') }}
                                </x-ui.button>
                            </form>
                        </div>

                        <div class="p-6 rounded-xl border border-rose-100 bg-rose-50/30 dark:bg-rose-500/5 dark:border-rose-500/10 flex flex-col justify-between gap-4">
                            <div>
                                <h3 class="text-[10px] font-black uppercase text-rose-700 dark:text-rose-500 tracking-widest">{{ __('Instant Terminate') }}</h3>
                                <p class="text-[9px] font-bold text-rose-600/80 dark:text-rose-600 uppercase tracking-widest mt-2 leading-relaxed">
                                    {{ __('Purge this entity and all associated data. This action is irreversible and immediate.') }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('admin.users.destroy', ['user' => $user->id]) }}" onsubmit="return confirm('{{ __('WARNING: THIS ACTION IS IRREVERSIBLE. Are you sure you want to PERMANENTLY delete this account?') }}')">        
                                @csrf @method('DELETE')
                                <x-ui.button type="submit" variant="danger" class="w-full">
                                    {{ __('Terminate Entity') }}
                                </x-ui.button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- Folder Creation Modal -->
    <x-ui.modal name="create-folder-modal" maxWidth="md">
        <div class="p-8" x-data="folderCreator()">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                <h2 class="text-[10px] font-black uppercase tracking-widest text-[#1e1f22] dark:text-white">{{ __('Create New Folder') }}</h2>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <div class="flex flex-col items-center mb-4">
                    <label class="group relative flex h-24 w-24 cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-black/10 bg-[#f8fafb] dark:bg-[#1e1f22] transition hover:border-discord-green">
                        <template x-if="logoPreview">
                            <img :src="logoPreview" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!logoPreview">
                            <div class="flex flex-col items-center text-[#80848e]">
                                <i class="bi bi-image text-2xl"></i>
                                <span class="mt-1 text-[8px] font-black uppercase tracking-widest">{{ __('Logo') }}</span>        
                            </div>
                        </template>
                        <input type="file" class="hidden" @change="handleLogoChange">
                    </label>
                </div>

                <div>
                    <label class="block text-[8px] font-black uppercase text-[#80848e] mb-2 ml-1 tracking-widest">{{ __('Folder Name') }}</label>
                    <input x-model="form.name" type="text" required placeholder="{{ __('Enter folder name...') }}"
                           class="w-full h-11 rounded-xl border border-black/[0.05] bg-[#f8fafb] dark:bg-[#1e1f22] px-4 text-[11px] font-black dark:text-white outline-none focus:ring-2 focus:ring-discord-green/10 transition-all shadow-sm">
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="$dispatch('close-modal', { name: 'create-folder-modal' })"
                            class="px-6 py-2.5 text-[9px] font-black uppercase tracking-widest text-[#80848e] hover:text-[#1e1f22] dark:hover:text-white transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <x-ui.button type="submit" variant="success" ::disabled="loading" class="px-8">
                        <span x-show="!loading">{{ __('Create') }}</span>
                        <span x-show="loading"><i class="bi bi-arrow-repeat animate-spin text-sm leading-none"></i></span>        
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>

    @push('scripts')
    <script>
        function folderCreator() {
            return {
                loading: false,
                logoPreview: null,
                logoFile: null,
                form: { name: '', user_id: '{{ $user->id }}' },
                handleLogoChange(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.logoFile = file;
                        this.logoPreview = URL.createObjectURL(file);
                    }
                },
                async submitForm() {
                    if(!this.form.name) return;
                    this.loading = true;
                    const formData = new FormData();
                    formData.append('name', this.form.name);
                    formData.append('user_id', this.form.user_id);
                    if(this.logoFile) formData.append('logo', this.logoFile);
                    try {
                        const response = await fetch('{{ route('admin.folders.store') }}', {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: formData
                        });
                        const data = await response.json();
                        if (response.ok) {
                            window.notify.success(data.message || '{{ __('Success') }}');
                            setTimeout(() => { window.location.reload(); }, 1500);
                        } else { throw new Error(data.message || '{{ __('Error') }}'); }
                    } catch (error) { window.notify.error(error.message); }
                    finally { this.loading = false; }
                }
            }
        }

        function tokenAdjuster() {
            return {
                loading: false,
                user: {!! json_encode(['id' => $user->id, 'tokens' => $user->tokens]) !!},
                form: { operation: 'add', tokens: 0, note: '' },
                numberFormat(val) { return new Intl.NumberFormat().format(val); },
                async submitForm() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('admin.users.tokens', ['user' => $user->id]) }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify(this.form)
                        });
                        const data = await response.json();
                        if (response.ok) {
                            window.notify.success('{{ __('Tokens Adjusted') }}');
                            let amt = parseInt(this.form.tokens);
                            if(this.form.operation === 'add') this.user.tokens = parseInt(this.user.tokens) + amt;
                            else if(this.form.operation === 'deduct') this.user.tokens = Math.max(0, parseInt(this.user.tokens) - amt);
                            else if(this.form.operation === 'set') this.user.tokens = amt;
                            this.form.tokens = 0;
                            this.form.note = '';
                        } else { throw new Error('{{ __('Error') }}'); }
                    } catch (error) { window.notify.error(error.message); }
                    finally { this.loading = false; }
                }
            }
        }
    </script>
    @endpush
@endsection