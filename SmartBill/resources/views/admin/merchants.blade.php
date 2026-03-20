<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-black italic tracking-tightest uppercase dark:text-white">{{ __('Merchants') }}</h2>
    </x-slot>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 animate-in fade-in duration-700">
        <!-- Left: Template Form -->
        <div class="xl:col-span-4 space-y-6">
            <div class="bg-white dark:bg-discord-main p-8 rounded-[2rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none">
                <div class="mb-8">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase italic tracking-tight">New Template</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-1">Define extraction rules</p>
                </div>
                
                <form action="{{ route('admin.merchants.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2 group">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest ml-1 italic">Template Name</label>
                        <input type="text" name="name" class="w-full bg-slate-50 dark:bg-discord-darker border-0 rounded-2xl h-12 px-5 text-xs font-black text-slate-700 dark:text-white focus:ring-2 focus:ring-rose-500 shadow-inner" placeholder="e.g. Grocery Bills" required>
                    </div>
                    <button type="submit" class="w-full py-4 bg-rose-500 hover:bg-rose-600 text-white font-black rounded-2xl shadow-xl shadow-rose-900/20 transition-all transform active:scale-95 text-[10px] uppercase tracking-[0.2em]">
                        Create Template
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Template Registry -->
        <div class="xl:col-span-8">
            <div class="bg-white dark:bg-discord-main rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-2xl shadow-slate-200/20 dark:shadow-none overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 dark:border-white/5 bg-slate-50/30 dark:bg-black/5 flex items-center justify-between">
                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.4em] italic">Active Templates</span>
                    <span class="text-[9px] font-bold text-discord-green uppercase">{{ count($templates) }} Ready</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-white/5 text-slate-400 dark:text-slate-600">
                                <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest italic">Identity</th>
                                <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest italic">AI Instructions</th>
                                <th class="px-8 py-4 text-[9px] font-black uppercase tracking-widest text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            @foreach($templates as $temp)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-black text-slate-800 dark:text-white italic uppercase">{{ $temp->name }}</div>
                                        <div class="text-[9px] font-bold text-rose-500 uppercase mt-2 tracking-widest">{{ count($temp->ai_fields) }} Fields Configured</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-xs text-slate-500 dark:text-slate-400 italic line-clamp-1">{{ $temp->main_instruction }}</p>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <button onclick="openCalibrate({{ $temp->id }})" class="px-6 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[9px] font-black rounded-xl hover:scale-105 transition-all shadow-lg uppercase tracking-widest">
                                            Setup AI
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

    <!-- Calibration Modal (Setup AI Rules) -->
    <div id="calibModal" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-6 bg-discord-black/80 backdrop-blur-sm hidden" x-cloak>
        <div class="bg-white dark:bg-discord-main w-full max-w-4xl sm:rounded-[3rem] overflow-hidden shadow-2xl transition-all border border-white/5">
            <div class="p-8 border-b border-slate-100 dark:border-white/5 flex justify-between items-end bg-slate-50/50 dark:bg-black/5">
                <div>
                    <span id="calibName" class="text-[10px] font-black text-rose-500 uppercase tracking-[0.3em] mb-2 block italic">Template Name</span>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic tracking-tightest">AI Neural Config</h3>
                </div>
                <button onclick="closeCalib()" class="p-2 text-slate-400 hover:rotate-90 transition-all"><i data-lucide="x" class="w-6 h-6"></i></button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 bg-slate-50 dark:bg-discord-black p-2 gap-2">
                <div class="p-8 space-y-8 text-left">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest ml-1 italic">Main AI Instruction</label>
                        <textarea id="main_instruction" class="w-full bg-white dark:bg-discord-darker border-0 rounded-2xl p-4 text-xs font-bold text-slate-700 dark:text-white shadow-inner focus:ring-2 focus:ring-rose-500" rows="4"></textarea>
                    </div>
                    
                    <div class="p-6 bg-emerald-500/5 rounded-2xl border border-emerald-500/10">
                        <h4 class="text-[10px] font-black text-discord-green uppercase tracking-widest mb-3">Field Mapping Logic</h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-relaxed font-bold italic opacity-80 uppercase">
                            Toggle fields in the JSON editor to the right. Use "true" to enable or "false" to disable.
                        </p>
                    </div>
                </div>
                
                <div class="p-0">
                    <textarea id="ai_fields" class="w-full h-[400px] bg-white dark:bg-discord-darker text-emerald-500 font-mono text-[11px] p-8 focus:ring-0 border-0 custom-scrollbar" spellcheck="false"></textarea>
                </div>
            </div>
            
            <div class="p-8 bg-white dark:bg-discord-main border-t border-slate-50 dark:border-white/5 flex justify-end items-center gap-4">
                <button onclick="closeCalib()" class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">Discard</button>
                <button onclick="saveCalib()" class="px-10 py-4 bg-discord-green text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-emerald-950/30 active:scale-95 transition-all">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        let activeTempId = null;
        const templates = @json($templates);

        function openCalibrate(id) {
            activeTempId = id;
            const temp = templates.find(t => t.id == id);
            document.getElementById('calibName').innerText = temp.name;
            document.getElementById('main_instruction').value = temp.main_instruction;
            document.getElementById('ai_fields').value = JSON.stringify(temp.ai_fields || {}, null, 4);
            document.getElementById('calibModal').classList.remove('hidden');
            lucide.createIcons();
        }

        function closeCalib() { document.getElementById('calibModal').classList.add('hidden'); }

        async function saveCalib() {
            const fd = new FormData();
            fd.append('main_instruction', document.getElementById('main_instruction').value);
            fd.append('ai_fields', document.getElementById('ai_fields').value);
            fd.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch(`/admin/merchants-update/${activeTempId}`, { method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} });
                const data = await res.json();
                if (data.status === 'success') { Toast.fire({ icon: 'success', title: 'Neural Config Updated' }); location.reload(); }
            } catch(e) { Toast.fire({ icon: 'error', title: 'Update Failed' }); }
        }
    </script>
    @endpush
</x-app-layout>
