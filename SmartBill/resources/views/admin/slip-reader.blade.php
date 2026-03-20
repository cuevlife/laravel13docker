<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <span class="flex items-center space-x-2">
                <i data-lucide="zap" class="w-4 h-4 text-indigo-500"></i>
                <span class="font-bold text-slate-800 dark:text-slate-200 uppercase tracking-widest text-xs">AI Slip Analysis</span>
            </span>
            <button onclick="exportData()" class="flex items-center space-x-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl transition-all text-xs font-bold border border-emerald-500/20">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export Intelligence</span>
            </button>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Neural Input Node (Upload) -->
        <div class="bg-white dark:bg-[#0b0f1a] rounded-3xl p-8 border border-slate-200/60 dark:border-white/5 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 text-slate-50 dark:text-white/5 pointer-events-none group-hover:scale-110 transition-transform duration-700">
                <i data-lucide="brain-circuit" class="w-32 h-32"></i>
            </div>
            
            <div class="relative z-10 md:flex items-start justify-between gap-12">
                <div class="max-w-md mb-8 md:mb-0">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white">Initialize Neural Scan</h2>
                    <p class="text-sm text-slate-400 dark:text-slate-500 mt-2 leading-relaxed font-light uppercase tracking-tight">
                        Select a merchant node and deploy your slip image. Our neural engine will map items to your ERP codes automatically.
                    </p>
                </div>

                <div class="w-full md:w-80 space-y-4">
                    <div class="relative">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Merchant Node</label>
                        <select id="merchant_id" class="w-full bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-300 h-12 shadow-inner focus:ring-indigo-500">
                            @foreach($merchants as $merchant)
                                <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="dropZone" class="relative group/drop cursor-pointer">
                        <input type="file" id="imageInput" class="hidden" accept="image/*">
                        <div onclick="document.getElementById('imageInput').click()" 
                             class="h-32 border-2 border-dashed border-slate-200 dark:border-white/10 rounded-3xl bg-slate-50/50 dark:bg-white/5 flex flex-col items-center justify-center transition-all hover:border-indigo-400 hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 group-hover/drop:scale-[1.02]">
                            <i data-lucide="cloud-lightning" class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2 transition-colors group-hover/drop:text-indigo-500"></i>
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">Deploy File Here</span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="queueZone" class="mt-8 hidden border-t border-slate-100 dark:border-white/5 pt-6 space-y-3"></div>
        </div>

        <!-- Intelligence Registry (History) -->
        <div class="bg-white dark:bg-[#0b0f1a] rounded-3xl border border-slate-200/60 dark:border-white/5 shadow-sm overflow-hidden transition-all duration-500">
            <div class="p-6 border-b border-slate-100 dark:border-white/5 flex items-center justify-between bg-slate-50/30 dark:bg-white/5">
                <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Processing History</h3>
                <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-tighter">{{ $slips->total() }} Records Live</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-white/5">
                            <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Temporal Metadata</th>
                            <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Merchant/Node</th>
                            <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">Neural Mappings</th>
                            <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Net Value</th>
                            <th class="px-8 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Protocol</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse($slips as $slip)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition group" id="slip-{{ $slip->id }}">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $slip->processed_at->format('d M Y') }}</div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 uppercase mt-1">{{ $slip->processed_at->format('H:i:s') }} UTC</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $slip->merchant->name }}</div>
                                    <div class="text-[9px] font-black text-emerald-500 dark:text-emerald-400 uppercase mt-1 tracking-widest">
                                        {{ $slip->extracted_data['shop_code'] ?? 'NODE_NULL' }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach(array_slice($slip->extracted_data['items'] ?? [], 0, 2) as $item)
                                            <span class="text-[9px] font-bold px-2 py-1 bg-slate-100 dark:bg-white/5 rounded-lg border border-slate-200 dark:border-white/5 text-slate-500 dark:text-slate-400">
                                                {{ Str::limit($item['name'], 15) }} <i data-lucide="arrow-right" class="w-2 h-2 inline mx-1"></i> 
                                                <span class="text-indigo-500">{{ $item['code'] ?? '?' }}</span>
                                            </span>
                                        @endforeach
                                        @if(count($slip->extracted_data['items'] ?? []) > 2)
                                            <span class="text-[9px] font-bold text-slate-300">+{{ count($slip->extracted_data['items']) - 2 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right whitespace-nowrap">
                                    <div class="text-xs font-black text-indigo-600 dark:text-indigo-400 tracking-tight">฿ {{ number_format($slip->extracted_data['final_total'] ?? 0, 2) }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="editRec({{ $slip->id }})" class="p-2 text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-lg transition-all">
                                            <i data-lucide="file-json" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="deleteRec({{ $slip->id }})" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center italic text-slate-300 dark:text-slate-600 text-xs tracking-widest uppercase">Registry Empty - Awaiting Scan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-slate-50/30 dark:bg-white/5 border-t border-slate-100 dark:border-white/5">
                {{ $slips->links() }}
            </div>
        </div>
    </div>

    <!-- JSON Mod (Dark Minimalist) -->
    <div id="jsonMod" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-950/60 backdrop-blur-md hidden" x-cloak>
        <div class="bg-white dark:bg-[#0b0f1a] rounded-[2.5rem] w-full max-w-2xl overflow-hidden shadow-2xl border border-slate-200/60 dark:border-white/5">
            <div class="p-8 border-b border-slate-100 dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-white/5">
                <div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-[0.2em]">Registry Calibration</h3>
                    <p class="text-[10px] text-slate-400 uppercase mt-1">Manual Intelligence Override</p>
                </div>
                <button onclick="closeMod()" class="text-slate-400 hover:rotate-90 transition-transform duration-300"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <textarea id="jsonInput" class="w-full bg-white dark:bg-slate-950 text-emerald-500 font-mono text-[11px] p-8 focus:ring-0 border-0" rows="15" spellcheck="false"></textarea>
            <div class="p-8 bg-white dark:bg-[#0b0f1a] border-t border-slate-100 dark:border-white/5 flex justify-end space-x-3">
                <button onclick="closeMod()" class="px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Discard</button>
                <button onclick="saveRec()" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-indigo-500/30 hover:scale-[1.02] transition">Commit Update</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        
        // --- DRAG & DROP LOGIC ---
        const dz = document.getElementById('dropZone');
        const inp = document.getElementById('imageInput');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => dz.addEventListener(e, (ev) => {
            ev.preventDefault(); ev.stopPropagation();
            if(['dragenter', 'dragover'].includes(e)) dz.classList.add('border-indigo-400', 'bg-indigo-50/30');
            else dz.classList.remove('border-indigo-400', 'bg-indigo-50/30');
        }));

        dz.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if(files.length) handleUpload(files[0]);
        });

        inp.onchange = (e) => handleUpload(e.target.files[0]);

        async function handleUpload(file) {
            const mId = document.getElementById('merchant_id').value;
            const q = document.getElementById('queueZone');
            q.classList.remove('hidden');
            const id = 'q-'+Date.now();
            q.insertAdjacentHTML('beforeend', `<div id="${id}" class="p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/5 flex justify-between items-center animate-pulse"><span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Neural Link Active: ${file.name}</span><span class="text-[9px] font-black text-indigo-500">PROCESSING...</span></div>`);

            const fd = new FormData();
            fd.append('image', file);
            fd.append('merchant_id', mId);
            fd.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch('{{ route("admin.slip-process") }}', { method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} });
                const data = await res.json();
                if(data.status === 'success') { Toast.fire({ icon: 'success', title: 'Data Commited' }); setTimeout(() => location.reload(), 1000); }
                else throw new Error(data.message);
            } catch(e) {
                Toast.fire({ icon: 'error', title: 'Link Failed' });
                document.getElementById(id).classList.remove('animate-pulse');
                document.getElementById(id).classList.add('border-red-500/20', 'bg-red-500/5');
            }
        }

        // --- CRUD LOGIC ---
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
        async function deleteRec(id) {
            if(confirm('Purge intelligence?')) {
                await fetch(`/admin/slip-delete/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' } });
                location.reload();
            }
        }
        function exportData() { window.location.href = '{{ route("admin.slip-export") }}'; }
    </script>
    @endpush
</x-app-layout>
