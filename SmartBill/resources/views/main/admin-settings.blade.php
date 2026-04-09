@extends('layouts.app')

@section('content')
    <div class="w-full px-2 py-4 sm:px-4 lg:px-6">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-black/5 dark:bg-[#1e1f22] dark:border-white/5 transition-colors">
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-xl font-bold text-[#313338] dark:text-white transition-colors uppercase tracking-widest">Global Extraction Settings</h1>
                <p class="text-sm text-[#80848e] transition-colors">Configure the AI instructions and fields used for all slip scans across the system.</p>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    {{-- Global Prompt --}}
                    <div>
                        <label for="global_prompt" class="block text-xs font-black uppercase tracking-widest text-[#80848e] mb-2">Main Instruction (Prompt)</label>
                        <textarea id="global_prompt" name="global_prompt" rows="4" 
                                  class="w-full rounded-xl border border-black/5 bg-[#f8fafb] p-4 text-sm font-medium outline-none focus:border-indigo-500/30 dark:bg-[#1e1f22] dark:text-white transition-all">{{ old('global_prompt', $settings['global_prompt'] ?? '') }}</textarea>
                        <p class="mt-2 text-[10px] text-[#80848e]">This instruction is sent to the AI to guide the data extraction process. Be specific about what you want to find.</p>
                    </div>

                    {{-- AI Fields JSON --}}
                    <div>
                        <label for="global_ai_fields" class="block text-xs font-black uppercase tracking-widest text-[#80848e] mb-2">AI Fields Definition (JSON)</label>
                        <textarea id="global_ai_fields" name="global_ai_fields" rows="10" 
                                  class="w-full font-mono rounded-xl border border-black/5 bg-[#f8fafb] p-4 text-xs outline-none focus:border-indigo-500/30 dark:bg-[#1e1f22] dark:text-white transition-all">{{ old('global_ai_fields', $settings['global_ai_fields'] ?? '') }}</textarea>
                        <p class="mt-2 text-[10px] text-[#80848e]">Define the structure of the data to extract. Use a JSON array of objects with <code>key</code>, <code>label</code>, and <code>type</code>.</p>
                    </div>

                    <div class="pt-4 border-t border-black/5 dark:border-white/5 flex justify-end">
                        <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-full bg-indigo-600 px-8 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-700">
                            <i class="bi bi-shield-check h-4 w-4"></i>
                            <span>Save Global Settings</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
