<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-slate-800 leading-tight">
            <i class="fas fa-microchip mr-2 text-emerald-600"></i>{{ __('Merchant Neural Configuration') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pb-20">
        
        <!-- Left: Merchant List -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <h3 class="text-xl font-black text-slate-900 mb-2">🏪 Register Merchant</h3>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-widest mb-6">Create New Data Source</p>
                
                <form action="{{ route('admin.merchants.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Entity Name</label>
                        <input type="text" name="name" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm font-bold text-slate-700 h-12" placeholder="e.g. 7-Eleven, Makro" required>
                    </div>
                    <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 hover:bg-emerald-700 transition transform hover:-translate-y-0.5">
                        <i class="fas fa-plus-circle mr-2"></i>Link New Merchant
                    </button>
                </form>
            </div>

            <div class="bg-slate-900 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden group">
                <div class="relative z-10">
                    <h3 class="text-lg font-black mb-2 uppercase tracking-tighter">System Intel</h3>
                    <p class="text-xs text-slate-400 leading-relaxed font-light">
                        Configure how the AI maps extracted strings to your internal ERP codes. 
                        Choose a merchant from the list to begin neural calibration.
                    </p>
                </div>
                <i class="fas fa-bolt absolute -right-4 -bottom-4 text-7xl text-white/5 group-hover:text-white/10 transition-all duration-700 rotate-12"></i>
            </div>
        </div>

        <!-- Right: Config & Mapping -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Active Merchants Registry</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Identity</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Mapping Status</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Neural Calibrate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($merchants as $merchant)
                                <tr class="hover:bg-slate-50/30 transition group">
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-bold text-slate-900">{{ $merchant->name }}</div>
                                        <div class="text-[10px] font-black text-emerald-600 uppercase mt-1">{{ $merchant->config['vendor_code'] ?? 'PENDING_INIT' }}</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center space-x-2">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span class="text-xs font-bold text-slate-500">{{ count($merchant->config['item_code_mapping'] ?? []) }} Node Mappings</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <button onclick="openCalibrate({{ $merchant->id }})" class="px-6 py-2.5 bg-slate-900 text-white text-[10px] font-black rounded-xl hover:bg-indigo-600 transition shadow-lg shadow-slate-900/10 uppercase tracking-widest">
                                            Calibrate
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

    <!-- Calibration Modal (High-Tech Style) -->
    <div id="calibModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl w-full max-w-4xl overflow-hidden shadow-2xl border border-white/20">
            <div class="bg-slate-900 px-10 py-8 flex justify-between items-end border-b border-white/5">
                <div>
                    <span id="calibName" class="text-xs font-black text-indigo-400 uppercase tracking-[0.2em] mb-2 block">7-ELEVEN</span>
                    <h3 class="text-2xl font-black text-white">Neural Calibration Module</h3>
                </div>
                <button onclick="closeCalib()" class="h-12 w-12 bg-white/5 rounded-2xl flex items-center justify-center text-white hover:bg-red-500 transition-all duration-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 bg-slate-950 p-2 gap-2">
                <div class="p-8 space-y-6">
                    <div class="bg-white/5 p-6 rounded-2xl border border-white/5">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Master Vendor Entity Code</label>
                        <input type="text" id="vCode" class="w-full bg-slate-900 border-white/10 rounded-xl text-white font-black text-lg focus:ring-indigo-500 focus:border-indigo-500 h-14" placeholder="V-000">
                    </div>
                    
                    <div class="bg-indigo-600/10 p-6 rounded-2xl border border-indigo-500/20">
                        <h4 class="text-xs font-black text-indigo-400 uppercase mb-2">Pro Tip</h4>
                        <p class="text-[10px] text-slate-400 leading-relaxed font-medium uppercase tracking-tight">
                            The item code mapping should be in JSON format where the key is the string from the receipt and the value is your ERP code.
                        </p>
                    </div>
                </div>
                
                <div class="p-0">
                    <textarea id="mCodeArea" class="w-full h-full bg-slate-950 text-indigo-400 font-mono text-sm p-8 focus:ring-0 border-0" placeholder='{ "Item Name": "ERP_CODE" }' style="resize: none; min-height: 400px;"></textarea>
                </div>
            </div>
            
            <div class="bg-white px-10 py-8 flex justify-between items-center border-t border-slate-100">
                <p class="text-[10px] text-slate-400 font-medium italic">Changes will be pushed to neural link immediately.</p>
                <div class="flex space-x-3">
                    <button onclick="closeCalib()" class="px-8 py-3 bg-slate-100 text-slate-500 rounded-xl font-bold text-xs hover:bg-slate-200 transition">Discard</button>
                    <button onclick="saveCalib()" class="px-10 py-3 bg-indigo-600 text-white rounded-xl font-bold text-xs hover:bg-indigo-700 shadow-xl shadow-indigo-500/30 transition">Sync Intelligence</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        let activeMerchantId = null;
        const merchants = @json($merchants);

        function openCalibrate(id) {
            activeMerchantId = id;
            const merchant = merchants.find(m => m.id == id);
            document.getElementById('calibName').innerText = merchant.name;
            document.getElementById('vCode').value = merchant.config.vendor_code || '';
            document.getElementById('mCodeArea').value = JSON.stringify(merchant.config.item_code_mapping || {}, null, 4);
            
            document.getElementById('calibModal').classList.remove('hidden');
        }

        function closeCalib() {
            document.getElementById('calibModal').classList.add('hidden');
        }

        async function saveCalib() {
            const fd = new FormData();
            fd.append('vendor_code', document.getElementById('vCode').value);
            fd.append('item_code_mapping', document.getElementById('mCodeArea').value);
            fd.append('_token', '{{ csrf_token() }}');

            const res = await fetch(`/admin/merchants-update/${activeMerchantId}`, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.status === 'success') {
                Toast.fire({ icon: 'success', title: 'Neural Sync Complete' });
                location.reload();
            }
        }
    </script>
    @endpush
</x-app-layout>
