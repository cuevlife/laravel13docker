@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8 sm:px-6 lg:px-8 animate-in fade-in duration-500" x-data="folderEditor()">
        
        <!-- Master Container Card -->
        <div class="bg-white dark:bg-[#2b2d31] rounded-xl shadow-sm border border-black/[0.05] dark:border-white/5 overflow-hidden">
            
            {{-- Header Section (Inside Card) --}}
            <div class="px-8 py-8 border-b border-black/[0.03] dark:border-white/[0.03] bg-[#f8fafb] dark:bg-black/10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <a href="{{ \App\Support\OwnerUrl::path(request(), 'folders') }}" class="w-10 h-10 rounded-xl bg-white dark:bg-[#1e1f22] border border-black/10 dark:border-white/10 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all shadow-sm">
                            <i class="bi bi-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">
                                <a href="{{ \App\Support\OwnerUrl::path(request(), 'folders') }}" class="hover:text-indigo-600 transition">Folders</a>
                                <i class="bi bi-chevron-right text-[8px]"></i>
                                <span class="text-slate-600 dark:text-slate-300">Folder Configuration</span>
                            </nav>
                            <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tightest">Settings & Access</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-lg {{ $merchant->status === 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }} text-[10px] font-black uppercase tracking-widest border shadow-sm">
                            Status: {{ $merchant->status }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-10">
                
                <!-- 1. Folder Details -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-info-circle-fill text-indigo-500"></i>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white">Folder Identity</h2>
                        </div>
                        <button type="button" @click="saveAll()" :disabled="saving" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-md shadow-indigo-500/20 disabled:opacity-50 flex items-center gap-2">
                            <i x-show="saving" class="bi bi-arrow-repeat animate-spin"></i>
                            <span x-text="saving ? 'Saving...' : 'Update Configuration'"></span>
                        </button>
                    </div>

                    <div class="space-y-5 bg-[#f8fafb] dark:bg-black/10 p-6 rounded-xl border border-black/5 dark:border-white/5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black uppercase text-slate-400 ml-1">Logical Name</label>
                                <input type="text" x-model="form.name" required class="w-full rounded-xl border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black uppercase text-slate-400 ml-1">System Subdomain</label>
                                <input type="text" value="{{ $merchant->subdomain }}" disabled class="w-full rounded-xl border border-black/5 bg-[#f2f3f5] px-4 py-2.5 text-sm font-bold text-slate-400 dark:bg-black/20 dark:border-white/5 shadow-inner cursor-not-allowed">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black uppercase text-slate-400 ml-1">Tax ID</label>
                                <input type="text" x-model="form.tax_id" class="w-full rounded-xl border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black uppercase text-slate-400 ml-1">Phone</label>
                                <input type="text" x-model="form.phone" class="w-full rounded-xl border border-black/10 bg-white px-4 py-2.5 text-sm font-bold dark:bg-[#1e1f22] dark:border-white/10 dark:text-white focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black uppercase text-rose-500 ml-1">Max Capacity (Slips)</label>
                                <input type="number" x-model="form.max_slips" required min="1" class="w-full rounded-xl border border-rose-200 bg-white px-4 py-2.5 text-sm font-black text-rose-600 dark:bg-[#1e1f22] dark:border-white/10 focus:ring-2 focus:ring-rose-500/20 outline-none transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. AI Data Schema Designer -->
                <div class="space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-cpu-fill text-indigo-500"></i>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white">AI Extraction Rules</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="file" id="sample-slip" class="hidden" @change="handleSampleUpload" accept="image/*">
                            <button type="button" @click="document.getElementById('sample-slip').click()" :disabled="analyzing"
                                    class="h-9 px-4 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 text-[9px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all flex items-center gap-2">
                                <i class="bi bi-magic" x-show="!analyzing"></i>
                                <i class="bi bi-arrow-repeat animate-spin" x-show="analyzing" x-cloak></i>
                                <span x-text="analyzing ? 'Analyzing...' : 'Auto-Suggest Fields'"></span>
                            </button>
                            <button type="button" @click="addField()" class="h-9 px-4 rounded-xl bg-[#1e1f22] dark:bg-white text-white dark:text-[#1e1f22] text-[9px] font-black uppercase tracking-widest hover:opacity-90 transition-all">
                                <i class="bi bi-plus-lg mr-1"></i> Add Target Field
                            </button>
                        </div>
                    </div>

                    <div class="overflow-hidden border border-black/[0.05] dark:border-white/5 rounded-xl bg-white dark:bg-[#1e1f22]">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-black/[0.02] dark:bg-white/[0.02] text-[9px] font-black uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="px-6 py-4">Technical Key</th>
                                    <th class="px-6 py-4">Display Label</th>
                                    <th class="px-6 py-4 w-32">Type</th>
                                    <th class="px-6 py-4">Extraction Instruction</th>
                                    <th class="px-6 py-4 w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                <template x-for="(field, index) in aiFields" :key="index">
                                    <tr class="group hover:bg-black/[0.01] dark:hover:bg-white/[0.01] transition-colors">
                                        <td class="px-6 py-3">
                                            <input type="text" x-model="field.key" placeholder="tax_id" @input="field.key = field.key.toLowerCase().replace(/[^a-z0-9_]/g, '')"
                                                   class="w-full bg-transparent border-0 p-0 text-xs font-black text-rose-500 focus:ring-0">
                                        </td>
                                        <td class="px-6 py-3">
                                            <input type="text" x-model="field.label" placeholder="TAX ID"
                                                   class="w-full bg-transparent border-0 p-0 text-xs font-bold text-slate-900 dark:text-white focus:ring-0">
                                        </td>
                                        <td class="px-6 py-3">
                                            <select x-model="field.type" class="w-full bg-slate-50 dark:bg-black/20 border-0 rounded-lg px-2 py-1 text-[10px] font-black uppercase text-slate-500 focus:ring-0 cursor-pointer">
                                                <option value="text">Text</option>
                                                <option value="number">Number</option>
                                                <option value="date">Date</option>
                                                <option value="array">Array</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-3">
                                            <input type="text" x-model="field.hint" placeholder="Find the taxpayer number..."
                                                   class="w-full bg-transparent border-0 p-0 text-[10px] font-bold text-slate-400 focus:ring-0 italic">
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <button type="button" @click="aiFields.splice(index, 1)" class="text-slate-300 hover:text-rose-500 transition-colors">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <template x-if="aiFields.length === 0">
                            <div class="p-10 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                                No specific rules defined for this folder.
                            </div>
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <!-- 3. Member Access -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-people-fill text-indigo-500"></i>
                                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-[#1e1f22] dark:text-white">Folder Members</h2>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $merchant->users->count() }} Users</span>
                        </div>

                        <div class="bg-[#f8fafb] dark:bg-black/10 p-6 rounded-xl border border-black/5 dark:border-white/5 space-y-6">
                            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'folders/' . $merchant->id . '/members') }}" class="flex flex-col sm:flex-row gap-2">
                                @csrf
                                <select name="user_id" class="flex-1 rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-4 py-2.5 text-sm font-bold dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all appearance-none shadow-sm">
                                    <option value="">Add user...</option>
                                    @foreach($candidateUsers as $candidateUser)
                                        <option value="{{ $candidateUser->id }}">{{ $candidateUser->name }}</option>
                                    @endforeach
                                </select>
                                <select name="workspace_role" class="w-full sm:w-28 rounded-xl border border-black/10 bg-white dark:bg-[#1e1f22] px-3 py-2.5 text-xs font-black uppercase dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-sm">
                                    <option value="employee">Staff</option>
                                    <option value="admin">Admin</option>
                                    <option value="owner">Owner</option>
                                </select>
                                <button type="submit" class="bg-[#1e1f22] dark:bg-white text-white dark:text-[#1e1f22] px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition shadow-md active:scale-95">Link</button>
                            </form>

                            <div class="divide-y divide-black/[0.03] dark:divide-white/[0.03]">
                                @foreach($merchant->users as $member)
                                    @php $isPrimaryOwner = (int) $merchant->user_id === (int) $member->id; @endphp
                                    <div class="flex items-center justify-between py-3 group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-white dark:bg-[#2b2d31] flex items-center justify-center text-indigo-600 font-black text-[10px] border border-black/5">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-[12px] font-bold text-[#1e1f22] dark:text-white">{{ $member->name }}</div>
                                                <div class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">{{ $member->email }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded border border-black/5 dark:border-white/5 {{ $isPrimaryOwner ? 'text-emerald-600 bg-emerald-50' : 'text-slate-500 bg-slate-50' }}">
                                                {{ $isPrimaryOwner ? 'Primary' : $member->pivot?->role }}
                                            </span>
                                            @if(!$isPrimaryOwner)
                                                <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'folders/' . $merchant->id . '/members/' . $member->id) }}" onsubmit="return confirm('Remove access?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-slate-300 hover:text-rose-500 transition-colors">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 4. Danger Zone -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-shield-lock-fill text-rose-500"></i>
                            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-rose-600">Danger Zone</h2>
                        </div>
                        
                        <div class="bg-rose-50/50 dark:bg-rose-500/5 p-6 rounded-xl border border-rose-100 dark:border-rose-500/10 flex flex-col items-start justify-between h-full min-h-[180px]">
                            <div class="mb-4">
                                <h3 class="text-[11px] font-black uppercase text-rose-600 tracking-widest">Archive Status</h3>
                                <p class="text-[10px] font-bold text-rose-500/70 uppercase tracking-tight mt-1 leading-relaxed">Archived folders are hidden from standard users and stop consuming active resources.</p>
                            </div>
                            <form method="POST" action="{{ \App\Support\OwnerUrl::path(request(), 'folders/' . $merchant->id . '/status') }}" class="w-full">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $merchant->status === 'active' ? 'archived' : 'active' }}">
                                <button type="submit" class="w-full h-12 rounded-xl border border-rose-200 text-rose-600 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-white transition shadow-sm bg-rose-50/50 active:scale-95">
                                    {{ $merchant->status === 'active' ? 'Archive Folder' : 'Restore Folder' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function folderEditor() {
        return {
            saving: false,
            analyzing: false,
            form: {
                name: {!! json_encode($merchant->name) !!},
                tax_id: {!! json_encode($merchant->tax_id) !!},
                phone: {!! json_encode($merchant->phone) !!},
                max_slips: {!! json_encode($merchant->max_slips ?? 10000) !!}
            },
            aiFields: {!! json_encode($schemaFields) !!},

            addField() {
                this.aiFields.push({ key: 'new_field_' + Date.now(), label: 'New Field', type: 'text', hint: '' });
            },

            async handleSampleUpload(e) {
                const file = e.target.files[0];
                if (!file) return;

                this.analyzing = true;
                const formData = new FormData();
                formData.append('image', file);

                try {
                    const res = await fetch('{{ route('admin.settings.suggest') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        this.aiFields = data.ai_fields;
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Analysis Complete', text: 'Schema updated based on receipt.', showConfirmButton: false, timer: 3000 });
                    } else {
                        throw new Error(data.message || 'Analysis failed');
                    }
                } catch (e) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Error', text: e.message, showConfirmButton: false, timer: 3000 });
                } finally {
                    this.analyzing = false;
                    e.target.value = '';
                }
            },

            async saveAll() {
                this.saving = true;
                try {
                    const res = await fetch('{{ \App\Support\OwnerUrl::path(request(), 'folders/' . $merchant->id) }}', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            ...this.form,
                            ai_fields: this.aiFields
                        })
                    });

                    if (res.ok) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Saved!', text: 'Folder configuration updated.', showConfirmButton: false, timer: 2000 });
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        const data = await res.json();
                        throw new Error(data.message || 'Update failed');
                    }
                } catch (e) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Error', text: e.message, showConfirmButton: false, timer: 3000 });
                } finally {
                    this.saving = false;
                }
            }
        }
    }
</script>
@endpush
