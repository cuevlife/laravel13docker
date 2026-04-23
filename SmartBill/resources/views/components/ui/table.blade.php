@props(['headers' => []])
<div class="overflow-hidden border border-black/[0.05] dark:border-white/5 rounded-xl bg-white dark:bg-[#1e1f22] shadow-sm">
    <div class="overflow-x-auto">
        <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse']) }}>
            @if(!empty($headers))
                <thead class="bg-black/[0.02] dark:bg-white/[0.01] text-[9px] font-black uppercase tracking-widest text-slate-500">
                    <tr>
                        @foreach($headers as $header)
                            <th class="px-6 py-4">{{ __($header) }}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody {{ $attributes->get('tbody-attributes') }} class="divide-y divide-black/5 dark:divide-white/5">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>