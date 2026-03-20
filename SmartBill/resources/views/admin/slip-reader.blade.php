<x-app-layout>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-black text-slate-800 dark:text-white uppercase italic tracking-tighter">{{ __('Scan Slips') }}</h1>
            <button onclick="exportData()" class="p-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg">
                <i data-lucide="download" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modern Upload Block (App Style) -->
        <div class="bg-white dark:bg-[#1e1f22] rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-white/5 space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ __('Select Merchant') }}</label>
                <select id="merchant_id" class="w-full bg-slate-50 dark:bg-discord-black border-0 rounded-2xl h-14 px-4 text-sm font-bold focus:ring-2 focus:ring-discord-green transition-all">
                    @foreach($merchants as $merchant)
                        <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="dropZone" class="relative group cursor-pointer">
                <input type="file" id="imageInput" class="hidden" accept="image/*">
                <div onclick="document.getElementById('imageInput').click()" 
                     class="h-48 border-2 border-dashed border-slate-200 dark:border-white/10 rounded-[2rem] bg-slate-50 dark:bg-discord-black flex flex-col items-center justify-center transition-all hover:bg-emerald-500/5 hover:border-emerald-500">
                    <div class="w-16 h-16 bg-white dark:bg-[#1e1f22] rounded-2xl flex items-center justify-center shadow-lg mb-4 group-active:scale-90 transition-transform">
                        <i data-lucide="image-plus" class="w-8 h-8 text-discord-green"></i>
                    </div>
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ __('Upload Document') }}</span>
                </div>
            </div>

            <div id="queueZone" class="hidden space-y-3 pt-2"></div>
        </div>

        <!-- Compact Registry List -->
        <div class="space-y-4">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">{{ __('Processing Registry') }}</h3>
            
            @forelse($slips as $slip)
                <div class="bg-white dark:bg-[#1e1f22] p-4 rounded-2xl border border-slate-200 dark:border-white/5 flex items-center justify-between group active:bg-slate-50 dark:active:bg-white/5 transition-colors" id="slip-{{ $slip->id }}">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-discord-black flex items-center justify-center text-rose-500 font-black text-[10px]">
                            {{ strtoupper(substr($slip->merchant->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-800 dark:text-white leading-none uppercase italic">{{ $slip->merchant->name }}</p>
                            <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase tracking-tighter">{{ $slip->processed_at->format('d M, H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-emerald-500 italic">฿ {{ number_format($slip->extracted_data['final_total'] ?? 0, 2) }}</p>
                        <div class="flex items-center justify-end space-x-2 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="editRec({{ $slip->id }})" class="text-slate-400"><i data-lucide="more-horizontal" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <i data-lucide="inbox" class="w-12 h-12 text-slate-200 dark:text-white/5 mx-auto mb-4"></i>
                    <p class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest">No Records Found</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Mobile-style Modal -->
    <div id="jsonMod" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm hidden" x-cloak>
        <div class="bg-white dark:bg-discord-darker w-full max-w-lg rounded-t-[2rem] sm:rounded-[2rem] overflow-hidden shadow-2xl transition-all">
            <div class="p-6 border-b border-slate-100 dark:border-white/5 flex justify-between items-center">
                <h3 class="text-xs font-black dark:text-white uppercase tracking-widest italic">Edit Data</h3>
                <button onclick="closeMod()" class="text-slate-400"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <textarea id="jsonInput" class="w-full bg-slate-50 dark:bg-discord-black text-emerald-500 font-mono text-[11px] p-6 focus:ring-0 border-0" rows="12"></textarea>
            <div class="p-6 safe-bottom">
                <button onclick="saveRec()" class="w-full py-4 bg-discord-green text-white rounded-xl font-black text-xs uppercase tracking-widest">{{ __('Save Changes') }}</button>
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
            q.insertAdjacentHTML('beforeend', `<div id="${id}" class="p-4 bg-emerald-500/5 rounded-2xl border border-emerald-500/20 flex justify-between items-center animate-pulse"><span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest italic">Scanning: ${file.name}</span></div>`);

            const fd = new FormData();
            fd.append('image', file); fd.append('merchant_id', mId); fd.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch('{{ route("admin.slip-process") }}', { method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} });
                const data = await res.json();
                if(data.status === 'success') { Toast.fire({ icon: 'success', title: 'Data Commited' }); setTimeout(() => location.reload(), 1000); }
                else throw new Error(data.message);
            } catch(e) {
                Toast.fire({ icon: 'error', title: 'Link Failed' });
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
