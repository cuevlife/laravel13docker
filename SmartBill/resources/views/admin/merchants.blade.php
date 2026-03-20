<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-black italic tracking-tightest uppercase dark:text-white">{{ __('Merchants') }}</h2>
    </x-slot>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 animate-in fade-in duration-700">
        <!-- Left: Registry Form -->
        <div class="xl:col-span-4 space-y-6">
            <div class="bg-white dark:bg-discord-main p-8 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none">
                <div class="mb-8">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase italic tracking-tight">{{ __('Register Merchant') }}</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-1">Deploy New Data Node</p>
                </div>
                
                <form action="{{ route('admin.merchants.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2 group">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest ml-1 italic">Entity Identity</label>
                        <input type="text" name="name" class="w-full bg-slate-50 dark:bg-discord-darker border-0 rounded-2xl h-12 px-5 text-xs font-black text-slate-700 dark:text-white focus:ring-2 focus:ring-rose-500 shadow-inner" placeholder="Node Name..." required>
                    </div>
                    <button type="submit" class="w-full py-4 bg-rose-500 hover:bg-rose-600 text-white font-black rounded-2xl shadow-xl shadow-rose-900/20 transition-all transform active:scale-95 text-[10px] uppercase tracking-[0.2em]">
                        {{ __('Link New Merchant') }}
                    </button>
                </form>
            </div>

            <div class="bg-slate-900 dark:bg-discord-black rounded-[2rem] p-8 text-white relative overflow-hidden group border border-white/5">
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-all"></div>
                <div class="relative z-10">
                    <h4 class="text-xs font-black uppercase tracking-widest italic mb-2">Registry Note</h4>
                    <p class="text-[10px] text-slate-400 leading-relaxed italic opacity-80">
                        Calibrate your merchant nodes to synchronize automated item code mapping protocols.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right: Active Registry -->
        <div class="xl:col-span-8">
            <div class="bg-white dark:bg-discord-main rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 dark:border-white/5 bg-slate-50/30 dark:bg-black/5 flex items-center justify-between">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] italic">Active Node Registry</span>
                    <span class="text-[9px] font-bold text-discord-green uppercase">{{ count($merchants) }} Synchronized</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-white/5">
                                <th class="px-8 py-4 text-[9px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest">Identification</th>
                                <th class="px-8 py-4 text-[9px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest">Mapping Nodes</th>
                                <th class="px-8 py-4 text-[9px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest text-center">Protocol</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            @foreach($merchants as $merchant)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-black text-slate-800 dark:text-white italic uppercase leading-none">{{ $merchant->name }}</div>
                                        <div class="text-[9px] font-bold text-rose-500 uppercase mt-2 tracking-widest">{{ $merchant->config['vendor_code'] ?? 'NODE_UNSET' }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                            <span class="text-xs font-black text-slate-500 dark:text-slate-400 italic underline decoration-discord-green/30">{{ count($merchant->config['item_code_mapping'] ?? []) }} Node Pairs</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <button onclick="openCalibrate({{ $merchant->id }})" class="px-6 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[9px] font-black rounded-xl hover:scale-105 active:scale-95 transition-all uppercase tracking-widest shadow-lg">
                                            {{ __('Calibrate') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Calibration Module (Modal) -->
    <div id="calibModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-6 bg-discord-black/80 backdrop-blur-sm hidden" x-cloak>
        <div class="bg-white dark:bg-discord-main w-full max-w-4xl sm:rounded-[3rem] overflow-hidden shadow-2xl transition-all border border-white/5">
            <div class="p-8 border-b border-slate-50 dark:border-white/5 flex justify-between items-end bg-slate-50/50 dark:bg-black/5">
                <div>
                    <span id="calibName" class="text-[10px] font-black text-rose-500 uppercase tracking-[0.3em] mb-2 block italic">7-ELEVEN</span>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic tracking-tightest">Calibration Protocol</h3>
                </div>
                <button onclick="closeCalib()" class="p-2 text-slate-400 hover:rotate-90 transition-all"><i data-lucide="x" class="w-6 h-6"></i></button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 bg-slate-50 dark:bg-discord-black p-2 gap-2">
                <div class="p-8 space-y-8">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest ml-1 italic">Master Vendor Code</label>
                        <input type="text" id="vCode" class="w-full bg-white dark:bg-discord-darker border-0 rounded-2xl h-14 px-6 text-xl font-black text-rose-500 shadow-inner focus:ring-2 focus:ring-rose-500" placeholder="V-000">
                    </div>
                    
                    <div class="p-6 bg-rose-500/5 rounded-2xl border border-rose-500/10">
                        <h4 class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-3">Intelligence Manual</h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-relaxed font-bold italic opacity-80 uppercase tracking-tight">
                            Define your mapping in RAW JSON format. The key represents the string extracted from the document, and the value represents your internal ERP identifier.
                        </p>
                    </div>
                </div>
                
                <div class="p-0">
                    <textarea id="mCodeArea" class="w-full h-[400px] bg-white dark:bg-discord-darker text-emerald-500 font-mono text-[11px] p-8 focus:ring-0 border-0 custom-scrollbar" placeholder='{ "Key": "Value" }' spellcheck="false"></textarea>
                </div>
            </div>
            
            <div class="p-8 bg-white dark:bg-discord-main border-t border-slate-50 dark:border-white/5 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-[9px] font-black text-slate-300 dark:text-slate-700 uppercase tracking-[0.4em] italic">Linked via Neural Calibration Protocol</p>
                <div class="flex space-x-3 w-full sm:w-auto">
                    <button onclick="closeCalib()" class="flex-1 sm:flex-none px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">Discard</button>
                    <button onclick="saveCalib()" class="flex-1 sm:flex-none px-10 py-4 bg-discord-green text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-emerald-950/30 active:scale-95 transition-all">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const Toast = Swal.mixin({ toast: true, position: 'top', showConfirmButton: false, timer: 3000 });
        let activeMerchantId = null;
        const merchants = @json($merchants);

        function openCalibrate(id) {
            activeMerchantId = id;
            const merchant = merchants.find(m => m.id == id);
            document.getElementById('calibName').innerText = merchant.name;
            document.getElementById('vCode').value = merchant.config.vendor_code || '';
            document.getElementById('mCodeArea').value = JSON.stringify(merchant.config.item_code_mapping || {}, null, 4);
            document.getElementById('calibModal').classList.remove('hidden');
            lucide.createIcons();
        }

        function closeCalib() { document.getElementById('calibModal').classList.add('hidden'); }

        async function saveCalib() {
            const fd = new FormData();
            fd.append('vendor_code', document.getElementById('vCode').value);
            fd.append('item_code_mapping', document.getElementById('mCodeArea').value);
            fd.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch(`/admin/merchants-update/${activeMerchantId}`, { method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} });
                const data = await res.json();
                if (data.status === 'success') { Toast.fire({ icon: 'success', title: 'Intelligence Synced' }); location.reload(); }
            } catch(e) { Toast.fire({ icon: 'error', title: 'Protocol Failure' }); }
        }
    </script>
    @endpush
</x-app-layout>
