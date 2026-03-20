<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-bold text-lg text-slate-800 leading-tight">
                <i class="fas fa-brain mr-2 text-indigo-600 animate-pulse"></i>{{ __('AI Neural Slip Reader') }}
            </h2>
            <div class="flex space-x-3">
                <button onclick="exportAll()" class="inline-flex items-center px-5 py-2.5 bg-emerald-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 shadow-lg shadow-emerald-500/20 transition duration-300">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8 pb-20">
        
        <!-- Drag & Drop Upload Hero (High-Tech Style) -->
        <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl blur opacity-25 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-2xl p-8 shadow-sm border border-slate-100 overflow-hidden">
                <div class="md:flex items-center justify-between">
                    <div class="max-w-xl mb-6 md:mb-0">
                        <h3 class="text-2xl font-black text-slate-900 mb-2">📸 เริ่มการสแกนอัจฉริยะ</h3>
                        <p class="text-slate-500 leading-relaxed">เลือกชื่อร้านค้าแล้วลากไฟล์สลิปมาวางที่นี่ หรือคลิกเพื่อเลือกไฟล์ AI ของเราจะช่วยสกัดข้อมูลและแมปรหัสสินค้าให้คุณทันที</p>
                    </div>
                    
                    <div class="w-full md:w-80">
                        <form id="uploadForm" class="space-y-4">
                            @csrf
                            <div class="relative">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Merchant Destination</label>
                                <select name="merchant_id" id="merchant_id" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold text-slate-700 h-12 shadow-inner">
                                    @foreach($merchants as $merchant)
                                        <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div id="dropZone" class="relative cursor-pointer group/upload">
                                <input type="file" id="imageInput" name="image" class="hidden" accept="image/*">
                                <div onclick="document.getElementById('imageInput').click()" 
                                     class="flex flex-col items-center justify-center h-32 border-2 border-dashed border-slate-200 rounded-xl hover:border-indigo-400 hover:bg-indigo-50/30 transition-all duration-300 bg-slate-50/50">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-slate-300 group-hover/upload:text-indigo-500 group-hover/upload:scale-110 transition-transform mb-2"></i>
                                    <span class="text-xs font-bold text-slate-400 group-hover/upload:text-indigo-600">Drag & Drop Image</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Processing Queue (Real-time Feedback) -->
                <div id="processingQueue" class="mt-8 space-y-3 hidden border-t border-slate-50 pt-6">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center">
                        <span class="flex h-2 w-2 rounded-full bg-indigo-500 mr-2 animate-ping"></span>
                        Scanning In Progress
                    </h4>
                    <div id="queueItems"></div>
                </div>
            </div>
        </div>

        <!-- Result History (Minimalist High-Tech Table) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 flex items-center">
                    <i class="fas fa-layer-group mr-2 text-indigo-500"></i>Data Extraction History
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left" id="slipsTable">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Metadata</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Merchant / Mapping</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Extracted Content</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Net Value</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($slips as $slip)
                            <tr class="hover:bg-slate-50/30 transition duration-150 group" id="slip-{{ $slip->id }}">
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="text-sm font-bold text-slate-900">{{ $slip->processed_at->format('M d, Y') }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium tracking-wide uppercase">{{ $slip->processed_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center mr-3 border border-slate-200 group-hover:bg-white group-hover:shadow-sm transition">
                                            <i class="fas fa-store text-slate-400 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800">{{ $slip->merchant->name }}</div>
                                            <div class="text-[10px] px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-full font-black inline-block border border-emerald-100 uppercase mt-1">
                                                {{ $slip->extracted_data['shop_code'] ?? 'V-SET' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 max-w-xs">
                                    <div class="flex flex-wrap gap-1.5">
                                        @if(isset($slip->extracted_data['items']))
                                            @foreach(array_slice($slip->extracted_data['items'], 0, 3) as $item)
                                                <span class="text-[10px] font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 flex items-center">
                                                    {{ Str::limit($item['name'], 20) }}
                                                    <i class="fas fa-arrow-right mx-1.5 text-[8px] text-slate-300"></i>
                                                    <span class="text-indigo-600 font-black">{{ $item['code'] ?? 'N/A' }}</span>
                                                </span>
                                            @endforeach
                                            @if(count($slip->extracted_data['items']) > 3)
                                                <span class="text-[10px] font-black text-slate-400 px-2 py-1">... +{{ count($slip->extracted_data['items']) - 3 }}</span>
                                            @endif
                                        @else
                                            <span class="text-[10px] text-slate-400 italic">No itemized data</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap text-right">
                                    <div class="text-sm font-black text-indigo-600">฿{{ number_format($slip->extracted_data['final_total'] ?? 0, 2) }}</div>
                                    <div class="text-[10px] text-slate-400 uppercase tracking-tighter">Verified Result</div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="editSlip({{ $slip->id }})" class="h-9 w-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 transition flex items-center justify-center border border-slate-200">
                                            <i class="fas fa-pen text-xs"></i>
                                        </button>
                                        <button onclick="deleteSlip({{ $slip->id }})" class="h-9 w-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 transition flex items-center justify-center border border-slate-200">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-200">
                                            <i class="fas fa-inbox text-3xl"></i>
                                        </div>
                                        <h5 class="text-sm font-bold text-slate-400 uppercase tracking-widest">No Extraction History</h5>
                                        <p class="text-xs text-slate-300 mt-1">Processed data will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 bg-slate-50/50 border-t border-slate-100">
                {{ $slips->links() }}
            </div>
        </div>
    </div>

    <!-- Edit Modal (High-Tech Minimalist) -->
    <div id="editModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl w-full max-w-3xl overflow-hidden shadow-2xl transform transition-all scale-95 duration-300 border border-white/20">
            <div class="bg-slate-900 px-8 py-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black text-white uppercase tracking-wider">🛠️ Raw JSON Intelligence</h3>
                    <p class="text-slate-500 text-[10px] font-medium tracking-widest uppercase mt-1">Refine and Verify AI Output</p>
                </div>
                <button onclick="closeModal()" class="h-10 w-10 bg-white/5 rounded-full flex items-center justify-center text-white hover:bg-white/10 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-0 bg-slate-950">
                <textarea id="jsonArea" class="w-full bg-slate-950 text-emerald-400 font-mono text-sm p-8 focus:ring-0 border-0" rows="18" style="resize: none;"></textarea>
            </div>
            <div class="bg-white px-8 py-6 flex justify-between items-center border-t border-slate-100">
                <p class="text-[10px] text-slate-400 font-medium">WARNING: Editing JSON directly affects extraction mapping.</p>
                <div class="flex space-x-3">
                    <button onclick="closeModal()" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-200 transition">Cancel</button>
                    <button onclick="saveEdit()" class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-xs hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition">Save Changes</button>
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

        // --- UPLOAD HANDLER ---
        const imageInput = document.getElementById('imageInput');
        imageInput.onchange = (e) => processUpload(e.target.files[0]);

        async function processUpload(file) {
            if (!file) return;

            const qZone = document.getElementById('processingQueue');
            const qItems = document.getElementById('queueItems');
            const mId = document.getElementById('merchant_id').value;

            qZone.classList.remove('hidden');
            const qId = 'q-' + Date.now();
            qItems.insertAdjacentHTML('beforeend', `
                <div id="${qId}" class="flex items-center justify-between p-4 bg-indigo-50/50 rounded-xl border border-indigo-100 animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-file-image mr-3 text-indigo-400"></i>
                        <span class="text-xs font-bold text-indigo-700">${file.name}</span>
                    </div>
                    <span class="text-[10px] font-black text-indigo-500 uppercase">Analyzing with AI...</span>
                </div>
            `);

            const fd = new FormData();
            fd.append('image', file);
            fd.append('merchant_id', mId);
            fd.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch('{{ route("admin.slip-process") }}', {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.status === 'success') {
                    Toast.fire({ icon: 'success', title: 'Data Extracted Successfully' });
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message);
                }
            } catch (err) {
                Toast.fire({ icon: 'error', title: err.message });
                document.getElementById(qId).classList.replace('bg-indigo-50/50', 'bg-red-50/50');
                document.getElementById(qId).classList.replace('border-indigo-100', 'border-red-100');
                document.getElementById(qId).querySelector('span:last-child').innerText = 'ERROR';
                document.getElementById(qId).classList.remove('animate-pulse');
            }
        }

        // --- CRUD HANDLERS ---
        let currentEditId = null;
        function editSlip(id) {
            currentEditId = id;
            const slip = @json($slips->items()).find(s => s.id == id);
            document.getElementById('jsonArea').value = JSON.stringify(slip.extracted_data, null, 4);
            document.getElementById('editModal').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('editModal').firstElementChild.classList.remove('scale-95');
                document.getElementById('editModal').firstElementChild.classList.add('scale-100');
            }, 10);
        }

        function closeModal() {
            document.getElementById('editModal').firstElementChild.classList.add('scale-95');
            setTimeout(() => document.getElementById('editModal').classList.add('hidden'), 300);
        }

        async function saveEdit() {
            const fd = new FormData();
            fd.append('data', document.getElementById('jsonArea').value);
            fd.append('_token', '{{ csrf_token() }}');

            const res = await fetch(`/admin/slip-update/${currentEditId}`, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.status === 'success') {
                Toast.fire({ icon: 'success', title: 'Intelligence Updated' });
                location.reload();
            }
        }

        async function deleteSlip(id) {
            const res = await Swal.fire({
                title: 'Confirm Deletion?',
                text: "This intelligence will be purged from the neural link.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#f1f5f9',
                confirmButtonText: '<span class="text-white">Yes, Purge</span>',
                cancelButtonText: '<span class="text-slate-600">Cancel</span>',
                customClass: {
                    popup: 'rounded-3xl border-0 shadow-2xl',
                    confirmButton: 'rounded-xl px-8 py-3 font-bold uppercase text-xs',
                    cancelButton: 'rounded-xl px-8 py-3 font-bold uppercase text-xs'
                }
            });

            if (result.isConfirmed) {
                const delRes = await fetch(`/admin/slip-delete/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await delRes.json();
                if (data.status === 'success') {
                    document.getElementById(`slip-${id}`).remove();
                    Toast.fire({ icon: 'success', title: 'Data Purged' });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
