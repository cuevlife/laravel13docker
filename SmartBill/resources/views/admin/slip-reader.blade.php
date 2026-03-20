<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight italic uppercase">Scan Slips</h1>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1 italic">Neural data extraction active</p>
            </div>
            <button onclick="exportData()" class="flex items-center space-x-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-[11px] font-black uppercase tracking-[0.2em] shadow-lg shadow-indigo-900/20 transition-all">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Download CSV</span>
            </button>
        </div>

        <!-- Simple Upload Node -->
        <div class="discord-card p-8 rounded-lg border border-white/5 relative overflow-hidden group">
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7">
                    <h2 class="text-lg font-black text-white uppercase italic">Upload Document</h2>
                    <p class="text-sm text-slate-400 mt-2 font-medium">Select a merchant store and drop your slip image here. AI will handle the mapping.</p>
                </div>

                <div class="lg:col-span-5 space-y-4">
                    <select id="merchant_id" class="w-full bg-[#0f172a] border-white/5 rounded-md text-xs font-bold text-slate-300 h-12 focus:ring-emerald-500 transition-all">
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                        @endforeach
                    </select>

                    <div id="dropZone" class="relative cursor-pointer group/drop">
                        <input type="file" id="imageInput" class="hidden" accept="image/*">
                        <div onclick="document.getElementById('imageInput').click()" 
                             class="h-24 border-2 border-dashed border-white/5 rounded-md bg-[#0f172a]/50 flex flex-col items-center justify-center transition-all hover:border-emerald-500 hover:bg-emerald-500/5 group-hover/drop:scale-[1.01]">
                            <i data-lucide="plus-circle" class="w-6 h-6 text-slate-500 mb-1 group-hover/drop:text-emerald-500"></i>
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest group-hover/drop:text-emerald-500">Drop Image Here</span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="queueZone" class="mt-6 hidden border-t border-white/5 pt-6 space-y-2"></div>
        </div>

        <!-- Discord Style Registry Table -->
        <div class="discord-card rounded-lg border border-white/5 overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 bg-black/10 flex items-center justify-between">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Processing Registry</h3>
                <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest italic">{{ $slips->total() }} units live</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-black/5">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Timestamp</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Merchant</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Items</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Value</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Manage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($slips as $slip)
                            <tr class="hover:bg-white/[0.02] transition group" id="slip-{{ $slip->id }}">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-xs font-bold text-slate-300 italic">{{ $slip->processed_at->format('d M Y') }}</div>
                                    <div class="text-[10px] text-slate-600 font-medium mt-0.5 tracking-tighter uppercase">{{ $slip->processed_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-xs font-black text-white italic uppercase tracking-tight">{{ $slip->merchant->name }}</div>
                                    <div class="text-[9px] font-bold text-rose-500 uppercase mt-0.5">{{ $slip->extracted_data['shop_code'] ?? 'NODE_UNSET' }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach(array_slice($slip->extracted_data['items'] ?? [], 0, 2) as $item)
                                            <span class="text-[9px] font-bold px-2 py-1 bg-black/20 rounded border border-white/5 text-slate-400">
                                                {{ Str::limit($item['name'], 12) }} <span class="text-emerald-500 mx-1">→</span> {{ $item['code'] ?? '?' }}
                                            </span>
                                        @endforeach
                                        @if(count($slip->extracted_data['items'] ?? []) > 2)
                                            <span class="text-[9px] font-bold text-slate-600 italic">+{{ count($slip->extracted_data['items']) - 2 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right whitespace-nowrap">
                                    <div class="text-xs font-black text-emerald-400 tracking-tighter italic">฿ {{ number_format($slip->extracted_data['final_total'] ?? 0, 2) }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="editRec({{ $slip->id }})" class="p-2 text-slate-500 hover:text-white transition-colors">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="deleteRec({{ $slip->id }})" class="p-2 text-slate-500 hover:text-rose-500 transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center italic text-slate-600 text-xs tracking-widest uppercase">Registry Empty</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($slips->hasPages())
                <div class="px-6 py-4 bg-black/10 border-t border-white/5">
                    {{ $slips->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Minimal JSON Modal -->
    <div id="jsonMod" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/80 backdrop-blur-sm hidden" x-cloak>
        <div class="discord-card rounded-lg w-full max-w-2xl overflow-hidden shadow-2xl border border-white/10">
            <div class="px-8 py-4 border-b border-white/5 flex justify-between items-center bg-black/20">
                <h3 class="text-xs font-black text-white uppercase italic tracking-widest">Calibration Override</h3>
                <button onclick="closeMod()" class="text-slate-500 hover:text-white transition-colors"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <textarea id="jsonInput" class="w-full bg-[#0f172a] text-emerald-400 font-mono text-[11px] p-8 focus:ring-0 border-0" rows="15" spellcheck="false"></textarea>
            <div class="p-6 bg-black/20 border-t border-white/5 flex justify-end space-x-3">
                <button onclick="closeMod()" class="px-6 py-2 text-xs font-bold text-slate-500 uppercase tracking-widest hover:text-white transition-colors">Cancel</button>
                <button onclick="saveRec()" class="px-8 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-black uppercase tracking-widest shadow-lg transition-all">Save Intelligence</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        const dz = document.getElementById('dropZone');
        const inp = document.getElementById('imageInput');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => dz.addEventListener(e, (ev) => {
            ev.preventDefault(); ev.stopPropagation();
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
            q.insertAdjacentHTML('beforeend', `<div id="${id}" class="p-4 bg-black/20 rounded border border-white/5 flex justify-between items-center animate-pulse"><span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Syncing Data: ${file.name}</span><span class="text-[9px] font-black text-emerald-500 uppercase">PROCESSING...</span></div>`);

            const fd = new FormData();
            fd.append('image', file);
            fd.append('merchant_id', mId);
            fd.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch('{{ route("admin.slip-process") }}', { method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} });
                const data = await res.json();
                if(data.status === 'success') { Toast.fire({ icon: 'success', title: 'Data Synced' }); setTimeout(() => location.reload(), 1000); }
                else throw new Error(data.message);
            } catch(e) {
                Toast.fire({ icon: 'error', title: 'Protocol Failed' });
                document.getElementById(id).classList.remove('animate-pulse');
                document.getElementById(id).classList.add('border-rose-500/20', 'bg-rose-500/5');
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
        async function deleteRec(id) {
            if(confirm('Purge data unit?')) {
                await fetch(`/admin/slip-delete/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' } });
                location.reload();
            }
        }
        function exportData() { window.location.href = '{{ route("admin.slip-export") }}'; }
    </script>
    @endpush
</x-app-layout>
