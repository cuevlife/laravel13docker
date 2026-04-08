<x-app-layout>
    @php
        $usesTenantRoutes = request()->routeIs('admin.*');
        $indexRoute = $usesTenantRoutes ? route('admin.exports.index') : route('workspace.exports.index');
        $slipRoute = $usesTenantRoutes ? route('admin.slip.index') : route('workspace.slip.index');
        $downloadRoute = $usesTenantRoutes ? route('admin.slip.export', request()->query()) : route('workspace.slip.export', request()->query());
        $hasAdvancedFilters = collect([
            $activeFilters['date_from'] ?? '',
            $activeFilters['date_to'] ?? '',
            $activeFilters['template_id'] ?? '',
            $activeFilters['label'] ?? '',
        ])->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty() || (($activeFilters['archive_scope'] ?? 'active') !== 'active');
        $formatBeDate = function ($value, bool $includeTime = false) {
            if (!$value) {
                return '-';
            }

            try {
                $date = $value instanceof \Carbon\CarbonInterface
                    ? $value->copy()
                    : \Carbon\Carbon::parse($value);

                return $date->addYears(543)->format($includeTime ? 'd/m/Y H:i' : 'd/m/Y');
            } catch (\Throwable $e) {
                return (string) $value;
            }
        };
        $formatAmount = function ($value) {
            $numeric = (float) preg_replace('/[^0-9.]/', '', (string) $value);
            return 'THB ' . number_format($numeric, 2);
        };
    @endphp

    <div class="space-y-4 pb-20">
        <section class="rounded-xl border border-black/5 bg-white shadow-sm dark:border-white/10 dark:bg-[#2b2d31]">
            {{-- Download action bar --}}
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#e3e5e8] px-4 py-2.5 dark:border-[#313338] md:px-5">
                <a href="{{ $slipRoute }}" class="inline-flex h-8 items-center justify-center gap-2 rounded-xl border border-[#e3e5e8] bg-white px-3 text-[9px] font-black uppercase tracking-[0.14em] text-[#5c5e66] transition hover:border-discord-green/30 hover:text-discord-green dark:border-[#313338] dark:bg-[#1e1f22] dark:text-[#b5bac1] dark:hover:border-discord-green/50 dark:hover:text-[#7fe0a2]">
                    <i class="bi bi-arrow-left h-3.5 w-3.5"></i>
                    Back to Slip
                </a>
                @if(($stats['matching_slips'] ?? 0) > 0)
                    <a href="{{ $downloadRoute }}" class="inline-flex h-8 items-center justify-center gap-2 rounded-xl bg-[#162033] px-3 text-[9px] font-black uppercase tracking-[0.18em] text-white transition hover:bg-[#0f1727]">
                        <i class="bi bi-download h-3.5 w-3.5"></i>
                        Download Workbook
                    </a>
                @else
                    <span class="inline-flex h-8 items-center justify-center gap-2 rounded-xl bg-[#d5d6d9] px-3 text-[9px] font-black uppercase tracking-[0.18em] text-white opacity-80 dark:bg-[#313338]">
                        <i class="bi bi-download h-3.5 w-3.5"></i>
                        No Matching Slips
                    </span>
                @endif
            </div>


            <form method="GET" action="{{ $indexRoute }}" x-data="{ filtersOpen: {{ $hasAdvancedFilters ? 'true' : 'false' }} }" class="border-b border-[#e3e5e8] px-4 py-3 dark:border-[#313338] md:px-5 md:py-4">
                <div class="grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1.6fr)_220px_220px_auto]">
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#80848e]"></i>
                        <input type="text" name="q" value="{{ $activeFilters['q'] }}" placeholder="Search slip, profile, or collection" class="h-10 w-full rounded-xl border border-[#e3e5e8] bg-[#f8fafb] pl-10 pr-3 text-sm font-bold text-[#162033] outline-none transition focus:border-discord-green focus:ring-2 focus:ring-discord-green/15 dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white">
                    </div>
                    <div class="relative">
                        <select name="batch_id" class="h-10 w-full appearance-none rounded-xl border border-[#e3e5e8] bg-[#f8fafb] px-3 text-sm font-bold text-[#162033] outline-none transition focus:border-discord-green focus:ring-2 focus:ring-discord-green/15 dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white">
                            <option value="">All Collections</option>
                            @foreach($collections as $collection)
                                <option value="{{ $collection->id }}" @selected((string) $collection->id === ($activeFilters['batch_id'] ?? ''))>{{ $collection->name }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#80848e]"></i>
                    </div>
                    <div class="relative">
                        <select name="workflow_status" class="h-10 w-full appearance-none rounded-xl border border-[#e3e5e8] bg-[#f8fafb] px-3 text-sm font-bold text-[#162033] outline-none transition focus:border-discord-green focus:ring-2 focus:ring-discord-green/15 dark:border-[#313338] dark:bg-[#1e1f22] dark:text-white">
                            <option value="">All Statuses</option>
                            @foreach($workflowOptions as $workflowKey => $workflowLabel)
                                <option value="{{ $workflowKey }}" @selected($workflowKey === ($activeFilters['workflow_status'] ?? ''))>{{ $workflowLabel }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#80848e]"></i>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 xl:justify-end">
                        <button type="button" @click="filtersOpen = !filtersOpen" class="inline-flex h-9 items-center justify-center gap-2 rounded-xl border border-[#e3e5e8] bg-white px-3 text-[10px] font-black uppercase tracking-[0.18em] text-[#5c5e66] transition hover:border-[#162033]/15 hover:text-[#162033] dark:border-[#313338] dark:bg-[#1e1f22] dark:text-[#b5bac1] dark:hover:border-white/20 dark:hover:text-white">
                            <i class="bi bi-sliders h-4 w-4"></i>
                            <span x-text="filtersOpen ? 'Less Filters' : 'More Filters'"></span>
                            @if($hasAdvancedFilters)
                                <span class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-[#eef8f1] px-1.5 text-[10px] font-black text-[#23a559] dark:bg-[#23a559]/10 dark:text-[#7fe0a2]">On</span>
                            @endif
                        </button>
                        <a href="{{ $indexRoute }}" class="inline-flex h-9 items-center justify-center gap-2 rounded-xl border border-[#e3e5e8] bg-white px-3 text-[10px] font-black uppercase tracking-[0.18em] text-[#5c5e66] transition hover:border-rose-300 hover:bg-rose-50 hover:text-rose-500 dark:border-[#313338] dark:bg-[#1e1f22] dark:text-[#b5bac1] dark:hover:border-rose-500/50 dark:hover:bg-rose-500/10 dark:hover:text-rose-300">
                            <i class="bi bi-funnel-fill h-4 w-4"></i>
                            Clear
                        </a>
                        <button type="submit" class="inline-flex h-9 items-center justify-center gap-2 rounded-xl bg-[#162033] px-3 text-[10px] font-black uppercase tracking-[0.18em] text-white transition hover:bg-[#0f1727]">
                            <i class="bi bi-search h-4 w-4"></i>
                            Apply
                        </button>
                    </div>
                </div>

                <div x-show="filtersOpen" x-cloak class="mt-3 rounded-xl border border-[#e3e5e8] bg-[#fbfcfc] p-3 dark:border-[#313338] dark:bg-[#1e1f22]">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-[180px_180px_minmax(0,1fr)_minmax(0,1fr)_190px]">
                        <div>
                            <input type="text" name="date_from" value="{{ $activeFilters['date_from'] }}" data-export-date autocomplete="off" placeholder="From date" class="h-10 w-full rounded-xl border border-[#e3e5e8] bg-white px-3 text-sm font-bold text-[#162033] outline-none transition focus:border-discord-green focus:ring-2 focus:ring-discord-green/15 dark:border-[#313338] dark:bg-[#2b2d31] dark:text-white">
                        </div>
                        <div>
                            <input type="text" name="date_to" value="{{ $activeFilters['date_to'] }}" data-export-date autocomplete="off" placeholder="To date" class="h-10 w-full rounded-xl border border-[#e3e5e8] bg-white px-3 text-sm font-bold text-[#162033] outline-none transition focus:border-discord-green focus:ring-2 focus:ring-discord-green/15 dark:border-[#313338] dark:bg-[#2b2d31] dark:text-white">
                        </div>
                        <div class="relative">
                            <select name="template_id" class="h-10 w-full appearance-none rounded-xl border border-[#e3e5e8] bg-white px-3 text-sm font-bold text-[#162033] outline-none transition focus:border-discord-green focus:ring-2 focus:ring-discord-green/15 dark:border-[#313338] dark:bg-[#2b2d31] dark:text-white">
                                <option value="">All Profiles</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" @selected((string) $template->id === ($activeFilters['template_id'] ?? ''))>{{ $template->name }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#80848e]"></i>
                        </div>
                        <div>
                            <input type="text" name="label" value="{{ $activeFilters['label'] }}" placeholder="Filter by label" class="h-10 w-full rounded-xl border border-[#e3e5e8] bg-white px-3 text-sm font-bold text-[#162033] outline-none transition focus:border-discord-green focus:ring-2 focus:ring-discord-green/15 dark:border-[#313338] dark:bg-[#2b2d31] dark:text-white">
                        </div>
                        <div class="relative">
                            <select name="archive_scope" class="h-10 w-full appearance-none rounded-xl border border-[#e3e5e8] bg-white px-3 text-sm font-bold text-[#162033] outline-none transition focus:border-discord-green focus:ring-2 focus:ring-discord-green/15 dark:border-[#313338] dark:bg-[#2b2d31] dark:text-white">
                                <option value="active" @selected(($activeFilters['archive_scope'] ?? 'active') === 'active')>Active Only</option>
                                <option value="all" @selected(($activeFilters['archive_scope'] ?? 'active') === 'all')>Active + Archived</option>
                                <option value="archived" @selected(($activeFilters['archive_scope'] ?? 'active') === 'archived')>Archived Only</option>
                            </select>
                            <i class="bi bi-chevron-down pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#80848e]"></i>
                        </div>
                    </div>
                </div>
            </form>

            <div class="grid gap-3 border-b border-[#e3e5e8] px-4 py-3 dark:border-[#313338] md:grid-cols-2 xl:grid-cols-4 md:px-5">
                <div class="rounded-xl bg-[#f6faf7] px-3 py-3 dark:bg-[#1e1f22]">
                    <div class="text-[10px] font-black uppercase tracking-[0.18em] text-[#80848e]">Matching Slips</div>
                    <div class="mt-1 text-base font-black text-[#162033] dark:text-white">{{ number_format($stats['matching_slips'] ?? 0) }}</div>
                </div>
                <div class="rounded-xl bg-[#f6faf7] px-3 py-3 dark:bg-[#1e1f22]">
                    <div class="text-[10px] font-black uppercase tracking-[0.18em] text-[#80848e]">Exported In Scope</div>
                    <div class="mt-1 text-base font-black text-[#162033] dark:text-white">{{ number_format($stats['exported_matches'] ?? 0) }}</div>
                </div>
                <div class="rounded-xl bg-[#f6faf7] px-3 py-3 dark:bg-[#1e1f22]">
                    <div class="text-[10px] font-black uppercase tracking-[0.18em] text-[#80848e]">Archived In Scope</div>
                    <div class="mt-1 text-base font-black text-[#162033] dark:text-white">{{ number_format($stats['archived_matches'] ?? 0) }}</div>
                </div>
                <div class="rounded-xl bg-[#f6faf7] px-3 py-3 dark:bg-[#1e1f22]">
                    <div class="text-[10px] font-black uppercase tracking-[0.18em] text-[#80848e]">Collections</div>
                    <div class="mt-1 text-base font-black text-[#162033] dark:text-white">{{ number_format($stats['collections'] ?? 0) }}</div>
                </div>
            </div>

            <div class="grid gap-3 px-4 py-3 md:px-5 lg:grid-cols-[minmax(0,1.6fr)_320px]">
                <section class="rounded-xl border border-[#e3e5e8] p-4 dark:border-[#313338]">
                    <div class="flex items-center justify-between gap-3 border-b border-[#e3e5e8] pb-3 dark:border-[#313338]">
                        <div>
                            <div class="text-[10px] font-black uppercase tracking-[0.18em] text-[#80848e]">Matching Slips</div>
                            <div class="mt-1 text-base font-black text-[#162033] dark:text-white">Preview Before Export</div>
                        </div>
                        <div class="text-[10px] font-black uppercase tracking-[0.14em] text-[#80848e]">{{ number_format($previewSlips->count()) }} shown</div>
                    </div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-[#e3e5e8] text-[10px] font-black uppercase tracking-[0.22em] text-[#80848e] dark:border-[#313338]">
                                    <th class="px-3 py-3">Slip</th>
                                    <th class="px-3 py-3">Collection</th>
                                    <th class="px-3 py-3">Processed</th>
                                    <th class="px-3 py-3">Workflow</th>
                                    <th class="px-3 py-3 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e3e5e8] dark:divide-[#313338]">
                                @forelse($previewSlips as $slip)
                                    <tr>
                                        <td class="px-3 py-3 align-top">
                                            <div class="font-black text-[#162033] dark:text-white">{{ $slip->display_shop }}</div>
                                            <div class="mt-1 text-[11px] font-bold uppercase tracking-[0.14em] text-[#23a559]">{{ optional($slip->template)->name ?? 'Unknown' }}</div>
                                            <div class="mt-1 text-xs font-bold text-[#80848e]">Receipt: {{ $formatBeDate($slip->display_date) }}</div>
                                        </td>
                                        <td class="px-3 py-3 align-top text-xs font-black uppercase tracking-[0.14em] text-[#4f7cff]">{{ $slip->batch_name }}</td>
                                        <td class="px-3 py-3 align-top text-xs font-bold text-[#5c5e66] dark:text-[#b5bac1]">{{ $formatBeDate($slip->processed_at, true) }}</td>
                                        <td class="px-3 py-3 align-top text-xs font-black uppercase tracking-[0.14em] text-[#5c5e66] dark:text-[#b5bac1]">{{ $slip->workflow_status }}</td>
                                        <td class="px-3 py-3 align-top text-right text-sm font-black text-[#162033] dark:text-white">{{ $formatAmount($slip->display_amount) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-12 text-center text-sm font-bold text-slate-500 dark:text-slate-300">No slips matched the current filters.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="space-y-4">
                    <div class="rounded-xl border border-[#e3e5e8] p-4 dark:border-[#313338]">
                        <div class="text-[10px] font-black uppercase tracking-[0.18em] text-[#80848e]">Export Action</div>
                        <h2 class="mt-1 text-base font-black tracking-tight text-[#162033] dark:text-white">Download the current workbook</h2>
                        <p class="mt-2 text-sm font-bold text-slate-500 dark:text-slate-300">The export uses the filters above and marks matching slips as exported after the workbook is generated.</p>
                        <div class="mt-4">
                            @if(($stats['matching_slips'] ?? 0) > 0)
                                <a href="{{ $downloadRoute }}" class="inline-flex h-9 w-full items-center justify-center gap-2 rounded-xl bg-[#162033] px-3 text-[10px] font-black uppercase tracking-[0.18em] text-white transition hover:bg-[#0f1727]">
                                    <i class="bi bi-download h-4 w-4"></i>
                                    Download Workbook
                                </a>
                            @else
                                <div class="inline-flex h-9 w-full items-center justify-center gap-2 rounded-xl bg-[#d5d6d9] px-3 text-[10px] font-black uppercase tracking-[0.18em] text-white opacity-80 dark:bg-[#313338]">
                                    <i class="bi bi-download h-4 w-4"></i>
                                    No Data to Export
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-xl border border-[#e3e5e8] p-4 dark:border-[#313338]">
                        <div class="text-[10px] font-black uppercase tracking-[0.18em] text-[#80848e]">Recent Exports</div>
                        <div class="mt-3 space-y-2.5">
                            @forelse($recentExports as $slip)
                                <div class="rounded-xl bg-[#f8fafb] px-3 py-2.5 dark:bg-[#1e1f22]">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="text-sm font-black text-[#162033] dark:text-white">{{ $slip->display_shop }}</div>
                                            <div class="mt-1 text-[11px] font-bold uppercase tracking-[0.14em] text-[#23a559]">{{ $slip->batch_name }}</div>
                                        </div>
                                        <div class="text-right text-xs font-bold text-[#80848e]">{{ $formatAmount($slip->display_amount) }}</div>
                                    </div>
                                    <div class="mt-2 text-xs font-bold text-[#80848e]">Exported {{ $formatBeDate($slip->exported_at, true) }}</div>
                                </div>
                            @empty
                                <div class="rounded-xl bg-[#f8fafb] px-3 py-3 text-sm font-bold text-slate-500 dark:bg-[#1e1f22] dark:text-slate-300">No exported slips yet.</div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>

            <section class="border-t border-[#e3e5e8] px-4 py-3 dark:border-[#313338] md:px-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-[0.18em] text-[#80848e]">Collection Summary</div>
                        <div class="mt-1 text-base font-black text-[#162033] dark:text-white">Latest collections at a glance</div>
                    </div>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-[#e3e5e8] text-[10px] font-black uppercase tracking-[0.22em] text-[#80848e] dark:border-[#313338]">
                                <th class="px-3 py-3">Collection</th>
                                <th class="px-3 py-3">Scanned</th>
                                <th class="px-3 py-3 text-right">Slips</th>
                                <th class="px-3 py-3 text-right">Active</th>
                                <th class="px-3 py-3 text-right">Exported</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e3e5e8] dark:divide-[#313338]">
                            @forelse($collectionSummary as $collection)
                                @php
                                    $collectionLink = $slipRoute . '?' . http_build_query(['batch_id' => $collection->id]);
                                @endphp
                                <tr>
                                    <td class="px-3 py-3 align-top">
                                        <a href="{{ $collectionLink }}" class="font-black text-[#162033] transition hover:text-[#23a559] dark:text-white dark:hover:text-[#7fe0a2]">{{ $collection->name }}</a>
                                        <div class="mt-1 text-xs font-bold text-[#80848e]">{{ $collection->note ?: 'No note' }}</div>
                                    </td>
                                    <td class="px-3 py-3 align-top text-xs font-bold text-[#5c5e66] dark:text-[#b5bac1]">{{ $formatBeDate($collection->scanned_at, true) }}</td>
                                    <td class="px-3 py-3 align-top text-right text-sm font-black text-[#162033] dark:text-white">{{ number_format($collection->slips_count) }}</td>
                                    <td class="px-3 py-3 align-top text-right text-sm font-black text-[#162033] dark:text-white">{{ number_format($collection->active_slips_count) }}</td>
                                    <td class="px-3 py-3 align-top text-right text-sm font-black text-[#162033] dark:text-white">{{ number_format($collection->exported_slips_count) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-12 text-center text-sm font-bold text-slate-500 dark:text-slate-300">Collections will appear here after scans are organized.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
    </div>

    @push('scripts')
    <script>
        (() => {
            if (typeof flatpickr === 'undefined') return;

            const pad = (value) => String(value).padStart(2, '0');
            const defaultFormatter = flatpickr.formatDate;
            const defaultParser = flatpickr.parseDate;

            document.querySelectorAll('[data-export-date]').forEach((input) => {
                flatpickr(input, {
                    locale: (flatpickr.l10ns && flatpickr.l10ns.th) ? flatpickr.l10ns.th : 'th',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    dateFormat: 'Y-m-d',
                    defaultDate: input.value || null,
                    allowInput: false,
                    formatDate(date, format) {
                        if (format === 'Y-m-d') {
                            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
                        }

                        if (format === 'd/m/Y') {
                            return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear() + 543}`;
                        }

                        return defaultFormatter(date, format);
                    },
                    parseDate(dateStr, format) {
                        if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
                            return defaultParser(dateStr, 'Y-m-d');
                        }

                        const match = dateStr.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
                        if (match) {
                            return new Date(Number(match[3]) - 543, Number(match[2]) - 1, Number(match[1]));
                        }

                        return defaultParser(dateStr, format);
                    },
                });
            });
        })();
    </script>
    @endpush
</x-app-layout>
