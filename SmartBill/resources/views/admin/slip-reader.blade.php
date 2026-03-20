<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-black italic tracking-tightest uppercase dark:text-white">{{ __('Scan Slips') }}</h2>
    </x-slot>

    <div class="space-y-8 animate-in fade-in duration-700">
        <!-- Action Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_15px_rgba(35,165,90,0.4)]"></span>
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic">Neural Link: Active</span>
            </div>
            <button onclick="exportData()" class="flex items-center space-x-2 px-5 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl transition-all text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>{{ __('Download CSV') }}</span>
            </button>
        </div>

        <!-- Integrated Upload Node -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <!-- Left: Config -->
            <div class="lg:col-span-4 bg-white dark:bg-discord-main p-8 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none flex flex-col justify-between">
                <div class="space-y-6">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] ml-1 italic">{{ __('Select Merchant') }}</label>
                        <select id="template_id" class="w-full bg-slate-50 dark:bg-discord-darker border-0 rounded-2xl h-12 px-4 text-xs font-black text-slate-700 dark:text-white focus:ring-2 focus:ring-discord-green shadow-inner">
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold leading-relaxed italic px-1 uppercase tracking-tight">
                        Route your document intelligence mapping through a verified node.
                    </p>
                </div>
            </div>

            <!-- Right: Dropzone -->
            <div class="lg:col-span-8">
                <div id="dropZone" class="group relative h-full min-h-[200px] cursor-pointer">
                    <input type="file" id="imageInput" class="hidden" accept="image/*">
                    <div onclick="document.getElementById('imageInput').click()" 
                         class="h-full border-2 border-dashed border-slate-200 dark:border-white/10 rounded-[2.5rem] bg-white/50 dark:bg-discord-main/30 flex flex-col items-center justify-center transition-all hover:border-discord-green hover:bg-emerald-500/5 group-active:scale-[0.98]">
                        <i data-lucide="image-plus" class="w-10 h-10 text-slate-300 dark:text-slate-700 group-hover:text-discord-green transition-all group-hover:-translate-y-1 mb-3"></i>
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] italic">{{ __('Upload Document') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="queueZone" class="hidden space-y-3"></div>

        <!-- Registry List (History) -->
        <div class="space-y-6 pt-4">
            <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] ml-2 italic">{{ __('Processing Registry') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($slips as $slip)
                    <div class="bg-white dark:bg-discord-main p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none transition-all hover:scale-[1.02] group" id="slip-{{ $slip->id }}">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-discord-darker flex items-center justify-center text-rose-500 shadow-inner group-hover:rotate-12 transition-transform">
                                    <span class="text-[10px] font-black italic">{{ strtoupper(substr($slip->template->name ?? 'NA', 0, 2)) }}</span>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-black text-slate-800 dark:text-white uppercase italic truncate">{{ $slip->template->name ?? 'Untitled' }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold mt-1 uppercase tracking-tighter">{{ $slip->processed_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <button onclick="editRec({{ $slip->id }})" class="p-2 text-slate-300 hover:text-indigo-500 transition-colors">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-end justify-between border-t border-slate-50 dark:border-white/5 pt-4">
                            <div>
                                <span class="text-[8px] font-black text-slate-300 dark:text-slate-700 uppercase tracking-widest block mb-1">Node Code</span>
                                <span class="text-[10px] font-black text-rose-500 uppercase">{{ $slip->extracted_data['shop_code'] ?? 'NODE_UNSET' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[8px] font-black text-slate-300 dark:text-slate-700 uppercase tracking-widest block mb-1 italic">Net Value</span>
                                <p class="text-xl font-black text-emerald-500 italic leading-none">฿ {{ number_format($slip->extracted_data['final_total'] ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 xl:col-span-3 py-20 text-center bg-white dark:bg-discord-main rounded-[2rem] border border-dashed border-slate-200 dark:border-white/10 opacity-50">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em] italic">Registry Empty</p>
                    </div>
                @endforelse
            </div>
            
            <div class="pt-6">
                {{ $slips->links() }}
            </div>
        </div>
    </div>

    <!-- Calibration Modal -->
    <div id="jsonMod" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-6 bg-discord-black/80 backdrop-blur-sm hidden" x-cloak>
        <div class="bg-white dark:bg-discord-main w-full max-w-xl sm:rounded-[3rem] overflow-hidden shadow-2xl transition-all border border-white/5">
            <div class="p-8 border-b border-slate-50 dark:border-white/5 flex justify-between items-center">
                <h3 class="text-xs font-black dark:text-white uppercase tracking-widest italic">Calibration Registry</h3>
                <button onclick="closeMod()" class="p-2 text-slate-400 hover:rotate-90 transition-all"><i data-lucide="x" class="w-6 h-6"></i></button>
            </div>
            <div class="p-2 bg-slate-50 dark:bg-discord-black">
                <textarea id="jsonInput" class="w-full bg-white dark:bg-discord-black text-emerald-500 font-mono text-[11px] p-8 focus:ring-0 border-0 custom-scrollbar" rows="12"></textarea>
            </div>
            <div class="p-8 space-y-4">
                <button onclick="saveRec()" class="w-full py-5 bg-discord-green text-white rounded-2xl font-black text-xs uppercase tracking-[0.3em] shadow-xl shadow-emerald-950/20 active:scale-95 transition-all">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const Toast = Swal.mixin({ toast: true, position: 'top', showConfirmButton: false, timer: 3000 });
        const dz = document.getElementById('dropZone');
        const inp = document.getElementById('imageInput');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => dz.addEventListener(e, (ev) => {
            ev.preventDefault();
            if(['dragenter', 'dragover'].includes(e)) dz.classList.add('border-discord-green', 'bg-emerald-500/5');
            else dz.classList.remove('border-discord-green', 'bg-emerald-500/5');
        }));

        dz.addEventListener('drop', (e) => { const fs = e.dataTransfer.files; if(fs.length) handleUpload(fs[0]); });
        inp.onchange = (e) => handleUpload(e.target.files[0]);

        async function handleUpload(file) {
            const tId = document.getElementById('template_id').value;
            const q = document.getElementById('queueZone');
            q.classList.remove('hidden');
            const id = 'q-'+Date.now();
            q.insertAdjacentHTML('beforeend', `<div id="${id}" class="p-6 bg-white dark:bg-discord-main rounded-3xl border border-emerald-500/20 flex justify-between items-center animate-pulse shadow-xl"><div class="flex items-center space-x-4"><i data-lucide="loader-2" class="w-5 h-5 text-emerald-500 animate-spin"></i><span class="text-[10px] font-black uppercase tracking-widest italic dark:text-white">Mapping: ${file.name}</span></div><span class="text-[9px] font-black text-emerald-500">ANALYZING</span></div>`);
            lucide.createIcons();

            const fd = new FormData();
            fd.append('image', file); fd.append('template_id', tId); fd.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch('{{ route("admin.slip-process") }}', { method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} });
                const data = await res.json();
                if(data.status === 'success') { Toast.fire({ icon: 'success', title: 'Intelligence Synced' }); setTimeout(() => location.reload(), 1000); }
                else throw new Error(data.message);
            } catch(e) {
                Toast.fire({ icon: 'error', title: 'Protocol Failure' });
                document.getElementById(id).remove();
            }
        }

        let curId = null;
        function editRec(id) {
            curId = id;
            const items = @json($slips->items());
            const rec = items.find(s => s.id == id);
            document.getElementById('jsonInput').value = JSON.stringify(rec.extracted_data, null, 4);
            document.getElementById('jsonMod').classList.remove('hidden');
            lucide.createIcons();
        }
        function closeMod() { document.getElementById('jsonMod').classList.add('hidden'); }
        async function saveRec() {
            const fd = new FormData();
            fd.append('data', document.getElementById('jsonInput').value);
            fd.append('_token', '{{ csrf_token() }}');
            await fetch(`/admin/slip-update/${curId}`, { method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} });
            location.reload();
        }
        function exportData() { window.location.href = '{{ route("admin.slip-export") }}'; }
    </script>
    @endpush
</x-app-layout>
