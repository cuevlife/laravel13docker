<x-app-layout>
    <div class="space-y-10">
        <!-- Minimalist Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase italic tracking-tightest">{{ __('Scan Slips') }}</h1>
            <button onclick="exportData()" class="p-3 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl hover:scale-110 transition-all active:scale-95">
                <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Integrated Upload Node -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <!-- Left: Select & Info -->
            <div class="lg:col-span-4 flex flex-col justify-between bg-white dark:bg-discord-main p-8 rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-sm">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] ml-1">{{ __('Select Merchant') }}</label>
                        <select id="merchant_id" class="w-full bg-slate-50 dark:bg-discord-black border-0 rounded-2xl h-14 px-6 text-sm font-bold text-slate-700 dark:text-white focus:ring-2 focus:ring-discord-green transition-all shadow-inner">
                            @foreach($merchants as $merchant)
                                <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium leading-relaxed italic px-1">
                        Select a gateway node to route your document intelligence mapping.
                    </p>
                </div>
                
                <div class="hidden lg:block pt-8">
                    <div class="flex items-center space-x-3 text-emerald-500 opacity-50">
                        <i data-lucide="fingerprint" class="w-4 h-4 icon-pulse-slow"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest italic">Encryption Active</span>
                    </div>
                </div>
            </div>

            <!-- Right: The Drop Zone -->
            <div class="lg:col-span-8">
                <div id="dropZone" class="group relative h-full min-h-[240px] cursor-pointer">
                    <input type="file" id="imageInput" class="hidden" accept="image/*">
                    <div onclick="document.getElementById('imageInput').click()" 
                         class="h-full border-2 border-dashed border-slate-200 dark:border-white/10 rounded-[3rem] bg-white/50 dark:bg-discord-main/30 flex flex-col items-center justify-center transition-all hover:bg-emerald-500/5 hover:border-emerald-500 group-active:scale-[0.98]">
                        
                        <div class="relative mb-6">
                            <div class="absolute inset-0 bg-emerald-500 blur-2xl opacity-0 group-hover:opacity-20 transition-opacity"></div>
                            <div class="relative w-20 h-20 bg-white dark:bg-discord-black rounded-3xl flex items-center justify-center shadow-2xl transition-all group-hover:-translate-y-2">
                                <i data-lucide="image-plus" class="w-10 h-10 text-emerald-500 icon-float"></i>
                            </div>
                        </div>
                        <span class="text-sm font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] italic">{{ __('Upload Document') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Neural Queue (Dynamic) -->
        <div id="queueZone" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4"></div>

        <!-- Registry List: Fluid and Clean -->
        <div class="space-y-6 pt-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] italic">{{ __('Processing Registry') }}</h3>
                <span class="text-[10px] font-black text-discord-green uppercase">{{ $slips->total() }} Units</span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($slips as $slip)
                    <div class="group bg-white dark:bg-discord-main p-6 rounded-[2rem] border border-slate-100 dark:border-white/5 flex flex-col justify-between transition-all hover:shadow-xl hover:scale-[1.02] active:bg-slate-50 dark:active:bg-white/5" id="slip-{{ $slip->id }}">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-discord-black flex items-center justify-center text-rose-500 shadow-inner group-hover:rotate-12 transition-transform">
                                    <span class="text-[10px] font-black uppercase tracking-tightest">{{ substr($slip->merchant->name, 0, 2) }}</span>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-black text-slate-800 dark:text-white leading-none uppercase italic truncate">{{ $slip->merchant->name }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold mt-1 uppercase tracking-widest">{{ $slip->processed_at->format('d M, H:i') }}</p>
                                </div>
                            </div>
                            <button onclick="editRec({{ $slip->id }})" class="p-2 text-slate-300 hover:text-indigo-500 transition-colors">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-end justify-between border-t border-slate-50 dark:border-white/5 pt-4">
                            <div>
                                <span class="text-[9px] font-black text-slate-300 dark:text-slate-700 uppercase tracking-widest block mb-1">Neural Code</span>
                                <span class="text-[10px] font-black text-emerald-500 uppercase">{{ $slip->extracted_data['shop_code'] ?? 'PENDING' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] font-black text-slate-300 dark:text-slate-700 uppercase tracking-widest block mb-1">Value</span>
                                <p class="text-lg font-black text-emerald-500 italic animate-text-green">฿ {{ number_format($slip->extracted_data['final_total'] ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="sm:col-span-2 xl:col-span-3 py-20 text-center">
                        <i data-lucide="ghost" class="w-12 h-12 text-slate-200 dark:text-white/5 mx-auto mb-4 icon-float"></i>
                        <p class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.5em]">Neural Link Empty</p>
                    </div>
                @endforelse
            </div>
            
            <div class="pt-6">
                {{ $slips->links() }}
            </div>
        </div>
    </div>

    <!-- The Calibration Module (Modal) -->
    <div id="jsonMod" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-6 bg-discord-black/80 backdrop-blur-sm hidden" x-cloak>
        <div class="bg-white dark:bg-discord-main w-full max-w-xl sm:rounded-[3rem] overflow-hidden shadow-2xl transition-all border border-white/5">
            <div class="p-8 border-b border-slate-50 dark:border-white/5 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest italic">Calibration Registry</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">Manual node override active</p>
                </div>
                <button onclick="closeMod()" class="p-2 text-slate-400 hover:rotate-90 transition-all"><i data-lucide="x" class="w-6 h-6"></i></button>
            </div>
            <div class="p-2 bg-slate-50 dark:bg-discord-black">
                <textarea id="jsonInput" class="w-full bg-white dark:bg-discord-black text-emerald-500 font-mono text-[11px] p-8 focus:ring-0 border-0 custom-scrollbar" rows="12" spellcheck="false"></textarea>
            </div>
            <div class="p-8 space-y-4">
                <button onclick="saveRec()" class="w-full py-5 bg-discord-green text-white rounded-2xl font-black text-xs uppercase tracking-[0.3em] shadow-xl shadow-emerald-950/20 active:scale-95 transition-all">
                    Commit Updates
                </button>
                <button onclick="closeMod()" class="w-full text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">Discard changes</button>
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
            if(['dragenter', 'dragover'].includes(e)) dz.classList.add('border-emerald-500', 'bg-emerald-500/5');
            else dz.classList.remove('border-emerald-500', 'bg-emerald-500/5');
        }));

        dz.addEventListener('drop', (e) => { const fs = e.dataTransfer.files; if(fs.length) handleUpload(fs[0]); });
        inp.onchange = (e) => handleUpload(e.target.files[0]);

        async function handleUpload(file) {
            const mId = document.getElementById('merchant_id').value;
            const q = document.getElementById('queueZone');
            q.classList.remove('hidden');
            const id = 'q-'+Date.now();
            q.insertAdjacentHTML('beforeend', `<div id="${id}" class="p-6 bg-white dark:bg-discord-main rounded-3xl border border-emerald-500/20 flex justify-between items-center animate-pulse"><div class="flex items-center space-x-4 text-emerald-500"><i data-lucide="loader-2" class="w-5 h-5 icon-spin-slow"></i><span class="text-[10px] font-black uppercase tracking-widest">Neural Mapping: ${file.name}</span></div><span class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.3em]">ANALYZING</span></div>`);
            lucide.createIcons();

            const fd = new FormData();
            fd.append('image', file); fd.append('merchant_id', mId); fd.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch('{{ route("admin.slip-process") }}', { method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} });
                const data = await res.json();
                if(data.status === 'success') { Toast.fire({ icon: 'success', title: 'Data Commited' }); setTimeout(() => location.reload(), 800); }
                else throw new Error(data.message);
            } catch(e) {
                Toast.fire({ icon: 'error', title: 'Protocol Failed' });
                document.getElementById(id).remove();
            }
        }

        let curId = null;
        function editRec(id) {
            curId = id;
            const rec = @json($slips->items()).find(s => s.id == id);
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
