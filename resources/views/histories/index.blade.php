@include('common.navigations.header')

<div class="flex flex-col gap-6 pb-8">

    <x-page-header title="{{ __('History and Reports') }}" description="{{ __('Browse and export calibration history records.') }}" icon="files">
        <x-slot name="actions">
            <x-export-dropdown
                excel-url="{{ url('reports/histories-reports?export_type=excel') }}"
                pdf-url="{{ url('reports/histories-reports?export_type=pdf') }}" />
        </x-slot>
    </x-page-header>

    @php
        $role = session('role') ?? 'user';
        $initialDisplayId = request('display_id');
        $historyText = [
            'allFacilities' => __('All facilities'),
            'allWorkgroups' => __('All workgroups'),
            'allWorkstations' => __('All workstations'),
            'allDisplaysInScope' => __('All displays in scope'),
            'pleaseSelect' => __('Please select'),
            'noOptionsFound' => __('No options found'),
            'noDisplaysFound' => __('No displays found'),
            'option' => __('option'),
            'options' => __('options'),
            'display' => __('Display'),
            'displays' => __('Displays'),
            'selected' => __('selected'),
            'historySummary' => __('History Summary'),
            'loadingReportSummary' => __('Loading report summary...'),
            'failedToLoadHistorySummary' => __('Failed to load history summary.'),
            'detailedSummaryForTask' => __('Detailed summary for the selected task execution.'),
            'noStructuredSummary' => __('No structured summary is available for this history record.'),
            'facility' => __('Facility'),
            'workgroup' => __('Workgroup'),
            'workstation' => __('Workstation'),
            'displayLabel' => __('Display'),
            'performedAt' => __('Performed At'),
            'result' => __('Result'),
            'section' => __('Section'),
            'reviewScoredChecks' => __('Review scored checks, question answers, and comments captured for this task.'),
            'score' => __('Score'),
            'limit' => __('Limit'),
            'measured' => __('Measured'),
            'status' => __('Status'),
            'comment' => __('Comment'),
        ];
    @endphp

    <style>
        .history-directory-shell {
            border-radius: 2rem;
            border: 1px solid #d5e0ec;
            background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
            box-shadow: 0 26px 64px -46px rgba(15, 23, 42, 0.34);
        }
        .history-create-shell {
            border-radius: 1.5rem;
            border: 1px solid #dce8f4;
            background: linear-gradient(180deg, #f9fcff 0%, #ffffff 100%);
            box-shadow: 0 14px 38px -28px rgba(15, 23, 42, 0.22);
        }
        .history-jobs-shell {
            border-radius: 2rem;
            border: 1px solid #d5e0ec;
            background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
            box-shadow: 0 26px 64px -46px rgba(15, 23, 42, 0.34);
            overflow: hidden;
        }
        .history-jobs-head {
            padding: 18px 18px 12px;
        }
        .history-table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 18px;
            border-top: 1px solid #e3ecf5;
            border-bottom: 1px solid #e3ecf5;
            background: #f8fbff;
        }
        .history-table-search {
            width: min(440px, 100%);
            height: 42px;
            border-radius: 999px;
            border: 1px solid #c9d8e8;
            padding: 0 16px;
            font-size: 14px;
            font-weight: 600;
            color: #12263a;
            background: #fff;
        }
        .history-table-search:focus {
            outline: none;
            border-color: #1d9bf0;
            box-shadow: 0 0 0 3px rgba(29, 155, 240, 0.16);
        }
        .history-table-wrap {
            overflow-x: auto;
            background: #fff;
        }
        .history-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 940px;
            table-layout: fixed;
        }
        .history-table th {
            padding: 13px 16px;
            text-align: left;
            border-bottom: 1px solid #d8e4f0;
            background: #e9f1fa;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #4d647d;
            white-space: nowrap;
        }
        .history-table td {
            padding: 11px 16px;
            border-bottom: 1px solid #edf2f8;
            font-size: 14px;
            color: #334155;
            vertical-align: middle;
            background: #fff;
        }
        .history-table tbody tr:hover td {
            background: #f7fbff;
        }
        .history-table th:nth-child(1), .history-table td:nth-child(1) { width: 24%; }
        .history-table th:nth-child(2), .history-table td:nth-child(2) { width: 14%; }
        .history-table th:nth-child(3), .history-table td:nth-child(3) { width: 34%; }
        .history-table th:nth-child(4), .history-table td:nth-child(4) { width: 18%; text-align: center; }
        .history-table th:nth-child(5), .history-table td:nth-child(5) { width: 10%; text-align: center; }
        .history-table td:nth-child(4) {
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
            padding-right: 10px;
        }
        .history-table td:nth-child(5) {
            padding-left: 24px;
            padding-right: 14px;
        }
        .history-table th:nth-child(4),
        .history-table th:nth-child(5) {
            padding-left: 16px;
            padding-right: 16px;
        }
        .history-table th:nth-child(4) .history-sort-btn,
        .history-table th:nth-child(5) .history-sort-btn {
            margin: 0 auto;
            justify-content: center;
            width: auto;
        }
        .history-table td:nth-child(2) {
            color: #475569;
            line-height: 1.45;
        }
        .history-table td:nth-child(3) {
            line-height: 1.45;
        }
        .history-record-title {
            display: block;
            width: 100%;
            text-align: left;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.45;
            color: #0284c7;
        }
        .history-record-title:hover {
            color: #0369a1;
        }
        .history-scope-cell {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }
        .history-scope-title {
            display: block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: left;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
            color: #0284c7;
        }
        .history-scope-title:hover {
            color: #0369a1;
        }
        .history-scope-meta {
            margin-top: 0.2rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.2rem 0.36rem;
            min-width: 0;
            font-size: 11px;
            color: #94a3b8;
        }
        .history-scope-meta-button {
            display: inline-flex;
            align-items: center;
            gap: 0;
            border: 0;
            background: transparent;
            padding: 0;
            font: inherit;
            font-weight: 600;
            color: #64748b;
            transition: color 160ms ease;
            min-width: 0;
            cursor: pointer;
        }
        .history-scope-meta-button:hover,
        .history-scope-meta-button:focus-visible {
            color: #0284c7;
            outline: none;
        }
        .history-scope-meta-badge {
            width: 0;
            height: 18px;
            border-radius: 999px;
            background: #1d9bf0;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: 800;
            line-height: 1;
            opacity: 0;
            transform: translateX(-6px);
            overflow: hidden;
            margin-right: 0;
            transition: width 180ms ease, opacity 180ms ease, transform 180ms ease, margin-right 180ms ease;
            flex: 0 0 auto;
        }
        .history-scope-meta-button:hover .history-scope-meta-badge,
        .history-scope-meta-button:focus-visible .history-scope-meta-badge {
            width: 18px;
            opacity: 1;
            transform: translateX(0);
            margin-right: 6px;
        }
        .history-scope-meta-label {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .history-scope-separator {
            color: #cbd5e1;
            font-weight: 700;
        }
        .history-result-pill {
            min-width: 82px;
            justify-content: center;
        }
        .history-table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 16px 14px;
            border-top: 1px solid #dbe7f3;
            background: #f7fbff;
        }
        .history-pager {
            display: inline-flex;
            gap: 8px;
            align-items: center;
        }
        .history-page-btn {
            height: 32px;
            min-width: 32px;
            border-radius: 999px;
            border: 1px solid #c7d6e7;
            background: #ffffff;
            color: #2c4158;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            padding: 0 12px;
            transition: all .18s ease;
        }
        .history-page-btn:hover:not(:disabled) {
            border-color: #1d9bf0;
            color: #0f5f9f;
            background: #f0f8ff;
        }
        .history-page-btn:disabled {
            opacity: .45;
            cursor: not-allowed;
        }
        .history-empty {
            padding: 24px 16px;
            text-align: center;
            color: #5f7388;
            font-size: 14px;
            border-bottom: 1px solid #edf2f8;
        }
        .history-sort-btn {
            border: 0;
            background: transparent;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #5a7087;
            font: inherit;
            letter-spacing: inherit;
            text-transform: inherit;
            cursor: pointer;
            border-radius: 999px;
            padding: 2px 8px;
            margin-left: -8px;
            transition: all .18s ease;
        }
        .history-sort-btn:hover {
            background: #f2f7fd;
            color: #2f4d6a;
        }
        .history-sort-btn.is-active {
            background: #e2edf9;
            color: #24486b;
        }
        .history-sort-indicator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 999px;
            background: #d4e3f4;
            color: #2f5477;
            font-size: 10px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: 0;
            transition: all .18s ease;
        }
        .history-sort-btn.is-active .history-sort-indicator {
            background: #2f6fae;
            color: #fff;
        }
        @media (max-width: 768px) {
            .history-table-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            .history-table-search {
                width: 100%;
            }
            .history-table-footer {
                flex-direction: column;
                align-items: stretch;
            }
            .history-pager {
                justify-content: flex-end;
            }
            #history-table-meta {
                text-align: left !important;
            }
        }
    </style>

    <section class="history-directory-shell p-6">
    <div class="history-create-shell mb-6 p-6">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,220px)_minmax(0,220px)_minmax(0,220px)_minmax(0,260px)_1fr]">
            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Facility') }}</label>
                <div class="relative">
                    <select id="facility_field" class="hidden" onchange="fetch_workgroups(this)">
                        @if($role === 'super')
                            <option value="">{{ __('All facilities') }}</option>
                            @foreach($facilities as $facility)
                                <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                            @endforeach
                        @else
                            @foreach($facilities as $facility)
                                <option value="{{ $facility['id'] }}">{{ $facility['name'] }}</option>
                            @endforeach
                        @endif
                    </select>

                    <button
                        id="history-facility-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        <span id="history-facility-label" class="truncate">{{ $role === 'super' ? __('All facilities') : ($facilities[0]['name'] ?? __('Please select')) }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div id="history-facility-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="history-facility-search" type="text" placeholder="{{ __('Search facilities...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="history-facility-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="history-facility-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Workgroup') }}</label>
                <div class="relative">
                    <select id="workgroups_field" class="hidden" onchange="fetch_workstations(this)">
                        <option value="">{{ __('All workgroups') }}</option>
                    </select>

                    <button
                        id="history-workgroup-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        <span id="history-workgroup-label" class="truncate">{{ __('All workgroups') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div id="history-workgroup-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="history-workgroup-search" type="text" placeholder="{{ __('Search workgroups...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="history-workgroup-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="history-workgroup-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Workstation') }}</label>
                <div class="relative">
                    <select id="workstations_field" class="hidden" onchange="fetch_displays_checklist(this)">
                        <option value="">{{ __('All workstations') }}</option>
                    </select>

                    <button
                        id="history-workstation-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        <span id="history-workstation-label" class="truncate">{{ __('All workstations') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div id="history-workstation-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="history-workstation-search" type="text" placeholder="{{ __('Search workstations...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="history-workstation-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="history-workstation-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Displays') }}</label>
                <div class="relative">
                    <button
                        id="history-displays-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        <span id="history-displays-label" class="truncate">{{ __('All displays in scope') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div id="history-displays-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="history-displays-search" type="text" placeholder="{{ __('Search displays...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="history-displays-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="displays_field" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="flex items-end justify-end">
                <button
                    id="reset-history-filters"
                    type="button"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    {{ __('Reset Filters') }}
                </button>
            </div>
        </div>
    </div>

    <div class="history-jobs-shell">
        <div class="history-jobs-head">
            <div class="space-y-2">
                <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('History Reports') }}</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ __('Calibration and QA history') }}</h2>
                <p class="mt-1 max-w-3xl text-sm text-slate-500">{{ __('Browse recent history records and open detailed report summaries.') }}</p>
            </div>
        </div>
        <div class="history-table-toolbar">
            <input id="history-table-search" type="text" class="history-table-search transition-all placeholder-gray-400" placeholder="{{ __('Search histories...') }}">
            <div id="history-table-meta" class="text-right text-[12px] font-semibold text-slate-500"></div>
        </div>

        <div class="history-table-wrap">
            <table class="history-table">
                <thead>
                    <tr>
                        <th><button type="button" data-history-sort="name" class="history-sort-btn"><span>{{ __('Task Name') }}</span><span class="history-sort-indicator" data-history-sort-indicator="name">â†•</span></button></th>
                        <th><button type="button" data-history-sort="pattern" class="history-sort-btn"><span>{{ __('Pattern') }}</span><span class="history-sort-indicator" data-history-sort-indicator="pattern">â†•</span></button></th>
                        <th><button type="button" data-history-sort="display_name" class="history-sort-btn"><span>{{ __('Display Scope') }}</span><span class="history-sort-indicator" data-history-sort-indicator="display_name">â†•</span></button></th>
                        <th><button type="button" data-history-sort="time" class="history-sort-btn"><span>{{ __('Performed Date/Time') }}</span><span class="history-sort-indicator" data-history-sort-indicator="time">â†“</span></button></th>
                        <th><button type="button" data-history-sort="result" class="history-sort-btn"><span>{{ __('Result') }}</span><span class="history-sort-indicator" data-history-sort-indicator="result">â†•</span></button></th>
                    </tr>
                </thead>
                <tbody id="history-table-body"></tbody>
            </table>
            <div class="history-table-footer">
                <div id="history-table-summary" class="text-[12px] font-semibold text-slate-500"></div>
                <div class="history-pager">
                    <button id="history-page-prev" type="button" class="history-page-btn">{{ __('Previous') }}</button>
                    <span id="history-page-label" class="text-[12px] font-semibold text-slate-500"></span>
                    <button id="history-page-next" type="button" class="history-page-btn">{{ __('Next') }}</button>
                </div>
            </div>
        </div>
    </div>
    </section>

</div>

<div id="history-summary-modal" class="fixed inset-0 z-[120] hidden">
    <div data-history-summary-overlay class="absolute inset-0 bg-slate-950/45 backdrop-blur-[2px] opacity-0 transition-opacity duration-200"></div>
    <div data-history-summary-stage class="absolute inset-0 flex items-center justify-center p-4 md:p-6">
        <div data-history-summary-panel class="relative flex max-h-[88vh] w-full max-w-5xl translate-y-4 scale-[0.985] flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_32px_90px_rgba(15,23,42,0.24)] opacity-0 transition-all duration-200">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-500">{{ __('History Summary') }}</p>
                    <h2 id="history-summary-title" class="mt-1 truncate text-2xl font-semibold text-slate-900">{{ __('Loading report...') }}</h2>
                    <p id="history-summary-subtitle" class="mt-2 text-sm text-slate-500">{{ __('Preparing history summary.') }}</p>
                </div>
                <button type="button" data-history-summary-close class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 text-slate-400 transition hover:border-slate-300 hover:text-slate-700">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <div id="history-summary-body" class="flex-1 overflow-y-auto px-6 py-5">
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                    {{ __('Loading history summary...') }}
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <a id="history-summary-print" href="#" target="_blank" rel="noopener" class="inline-flex h-11 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="printer" class="h-4 w-4"></i>
                    {{ __('Print Preview') }}
                </a>
                <button type="button" data-history-summary-close class="inline-flex h-11 items-center rounded-2xl bg-sky-500 px-5 text-sm font-semibold text-white transition hover:bg-sky-600">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const text = @json($historyText);
    const isSuperHistoryRole = @json($role === 'super');
    const initialFacilityValue = isSuperHistoryRole ? '' : '{{ $facilities[0]['id'] ?? '' }}';
    const initialFacilityLabel = isSuperHistoryRole ? text.allFacilities : ('{{ $facilities[0]['name'] ?? '' }}' || text.pleaseSelect);
    const historyFilterState = {
        facilitySearch: '',
        workgroupSearch: '',
        workstationSearch: '',
        displaysSearch: '',
        activeDropdown: null,
    };

    function parseNativeSelectOptions(selectId, includeBlank = true) {
        const select = document.getElementById(selectId);
        if (!select) return [];
        return Array.from(select.options || [])
            .filter((option, index) => includeBlank || index > 0)
            .map((option) => ({
                value: option.value,
                label: option.textContent.trim(),
            }));
    }

    function closeHistoryDropdowns() {
        historyFilterState.activeDropdown = null;
        ['history-facility-panel', 'history-workgroup-panel', 'history-workstation-panel', 'history-displays-panel']
            .forEach((id) => document.getElementById(id)?.classList.add('hidden'));
    }

    function toggleHistoryDropdown(type) {
        historyFilterState.activeDropdown = historyFilterState.activeDropdown === type ? null : type;
        const map = {
            facility: 'history-facility-panel',
            workgroup: 'history-workgroup-panel',
            workstation: 'history-workstation-panel',
            displays: 'history-displays-panel',
        };

        Object.entries(map).forEach(([key, id]) => {
            document.getElementById(id)?.classList.toggle('hidden', historyFilterState.activeDropdown !== key);
        });
    }

    function renderHistoryNativeOptions(selectId, optionsId, hintId, query, onPick, emptyText = text.noOptionsFound) {
        const options = parseNativeSelectOptions(selectId).filter((item) => item.label.toLowerCase().includes((query || '').trim().toLowerCase()));
        const hint = document.getElementById(hintId);
        const box = document.getElementById(optionsId);
        if (hint) hint.textContent = options.length ? `${options.length} ${options.length === 1 ? text.option : text.options}` : emptyText;
        if (!box) return;

        box.innerHTML = options.length
            ? options.map((item) => `<button type="button" data-value="${Perfectlum.escapeHtml(item.value)}" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 hover:bg-sky-50 hover:text-sky-700">${Perfectlum.escapeHtml(item.label)}</button>`).join('')
            : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${emptyText}</div>`;

        box.querySelectorAll('button[data-value]').forEach((button) => {
            button.addEventListener('click', () => onPick(button.dataset.value));
        });
    }

    function setHistorySelectValue(selectId, labelId, value, fallbackLabel = 'Please select') {
        const select = document.getElementById(selectId);
        const label = document.getElementById(labelId);
        if (!select) return;
        select.value = value;
        const text = select.options[select.selectedIndex]?.textContent?.trim() || fallbackLabel;
        if (label) label.textContent = text;
    }

    function updateDisplaysHint() {
        const host = document.getElementById('displays_field');
        const hint = document.getElementById('history-displays-hint');
        if (!host || !hint) return;
        const options = Array.from(host.querySelectorAll('.form-check'));
        const visible = options.filter((item) => !item.classList.contains('hidden'));
        hint.textContent = visible.length ? `${visible.length} ${visible.length === 1 ? text.option : text.options}` : text.noDisplaysFound;
    }

    function filterDisplaysList() {
        const host = document.getElementById('displays_field');
        if (!host) return;
        const query = (historyFilterState.displaysSearch || '').trim().toLowerCase();
        host.querySelectorAll('.form-check').forEach((item) => {
            const label = item.querySelector('label')?.textContent?.trim().toLowerCase() || '';
            item.classList.toggle('hidden', query && !label.includes(query));
        });
        updateDisplaysHint();
    }

    function syncDisplayLabel() {
        const host = document.getElementById('displays_field');
        const label = document.getElementById('history-displays-label');
        if (!host || !label) return;

        const checked = Array.from(host.querySelectorAll('input[type="checkbox"]:checked'));
        if (!checked.length) {
            label.textContent = text.allDisplaysInScope;
            return;
        }

        if (checked.length === 1) {
            const selectedLabel = checked[0].closest('.form-check')?.querySelector('label')?.textContent?.trim() || `1 ${text.display} ${text.selected}`;
            label.textContent = selectedLabel;
            return;
        }

        label.textContent = `${checked.length} ${text.displays} ${text.selected}`;
    }

    const historiesTableState = {
        page: 1,
        limit: 25,
        total: 0,
        rows: [],
        loading: false,
        fetching: false,
        search: '',
        searchTimer: null,
        sortKey: 'time',
        sortOrder: 'desc',
    };

    function refreshHistoriesGrid() {
        historiesTableState.page = 1;
        loadHistoriesTable();
    }

    const historySummaryModal = {
        root: null,
        overlay: null,
        stage: null,
        panel: null,
        body: null,
        title: null,
        subtitle: null,
        printLink: null,
        activeId: null,
        init() {
            this.root = document.getElementById('history-summary-modal');
            if (!this.root) return;
            if (this.root.parentElement !== document.body) {
                document.body.appendChild(this.root);
            }
            this.overlay = this.root.querySelector('[data-history-summary-overlay]');
            this.stage = this.root.querySelector('[data-history-summary-stage]');
            this.panel = this.root.querySelector('[data-history-summary-panel]');
            this.body = document.getElementById('history-summary-body');
            this.title = document.getElementById('history-summary-title');
            this.subtitle = document.getElementById('history-summary-subtitle');
            this.printLink = document.getElementById('history-summary-print');

            this.root.querySelectorAll('[data-history-summary-close]').forEach((button) => {
                button.addEventListener('click', () => this.close());
            });

            this.overlay?.addEventListener('click', () => this.close());
            this.stage?.addEventListener('click', (event) => {
                if (event.target === this.stage) {
                    this.close();
                }
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && this.root && !this.root.classList.contains('hidden')) {
                    this.close();
                }
            });
        },
        openSkeleton(id, name) {
            this.activeId = id;
            this.title.textContent = name || text.historySummary;
            this.subtitle.textContent = text.loadingReportSummary;
            this.body.innerHTML = `<div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">${Perfectlum.escapeHtml(text.loadingReportSummary)}</div>`;
            this.printLink.setAttribute('href', '#');
            this.root.classList.remove('hidden');
            requestAnimationFrame(() => {
                this.overlay?.classList.remove('opacity-0');
                this.panel?.classList.remove('translate-y-4', 'scale-[0.985]', 'opacity-0');
            });
            document.body.classList.add('overflow-hidden');
            if (window.lucide) window.lucide.createIcons();
        },
        close() {
            if (!this.root || this.root.classList.contains('hidden')) return;
            this.overlay?.classList.add('opacity-0');
            this.panel?.classList.add('translate-y-4', 'scale-[0.985]', 'opacity-0');
            window.setTimeout(() => {
                this.root.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 200);
        },
        renderBadge(label, tone) {
            const map = {
                success: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                danger: 'bg-rose-50 text-rose-700 ring-rose-200',
                warning: 'bg-amber-50 text-amber-700 ring-amber-200',
                neutral: 'bg-slate-100 text-slate-600 ring-slate-200',
            };
            const cls = map[tone] || map.neutral;
            return `<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ${cls}">${Perfectlum.escapeHtml(label || '-')}</span>`;
        },
        renderInfoGrid(items) {
            if (!items?.length) return '';
            return `<section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">${items.map((item) => `
                <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">${Perfectlum.escapeHtml(item.label || '-')}</p>
                    <p class="mt-2 break-words text-sm font-medium text-slate-800">${Perfectlum.escapeHtml(item.value || '-')}</p>
                </div>`).join('')}</section>`;
        },
        renderSection(section) {
            const scores = Array.isArray(section.scores) ? section.scores : [];
            const questions = Array.isArray(section.questions) ? section.questions : [];
            const comment = section.comment || '';
            return `
                <section class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_12px_40px_-32px_rgba(15,23,42,0.24)]">
                    <h3 class="text-lg font-semibold text-slate-900">${Perfectlum.escapeHtml(section.name || text.section)}</h3>
                    <p class="mt-1 text-sm text-slate-500">${Perfectlum.escapeHtml(text.reviewScoredChecks)}</p>
                    ${scores.length ? `
                        <div class="mt-5 overflow-hidden rounded-[1.25rem] border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr class="text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                                        <th class="px-4 py-3">${Perfectlum.escapeHtml(text.score)}</th>
                                        <th class="px-4 py-3">${Perfectlum.escapeHtml(text.limit)}</th>
                                        <th class="px-4 py-3">${Perfectlum.escapeHtml(text.measured)}</th>
                                        <th class="px-4 py-3">${Perfectlum.escapeHtml(text.status)}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    ${scores.map((score) => `
                                        <tr>
                                            <td class="px-4 py-3 font-medium text-slate-800">${Perfectlum.escapeHtml(score.name || '-')}</td>
                                            <td class="px-4 py-3 text-slate-600">${Perfectlum.escapeHtml(score.limit || '-')}</td>
                                            <td class="px-4 py-3 text-slate-600">${Perfectlum.escapeHtml(score.measured || '-')}</td>
                                            <td class="px-4 py-3">${this.renderBadge(score.statusLabel || '-', score.statusTone || 'neutral')}</td>
                                        </tr>`).join('')}
                                </tbody>
                            </table>
                        </div>` : ''}
                    ${questions.length ? `<div class="mt-5 grid gap-3">${questions.map((question) => `
                        <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm font-medium text-slate-800">${Perfectlum.escapeHtml(question.text || '-')}</p>
                                ${this.renderBadge(question.answer || '-', question.tone || 'neutral')}
                            </div>
                        </div>`).join('')}</div>` : ''}
                    ${comment ? `
                        <div class="mt-5 rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">${Perfectlum.escapeHtml(text.comment)}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-700">${Perfectlum.escapeHtml(comment)}</p>
                        </div>` : ''}
                </section>`;
        },
        render(payload) {
            this.title.textContent = payload.name || text.historySummary;
            this.subtitle.textContent = `${payload.performedAt || '-'} â€¢ ${payload.display?.display || '-'}`;
            this.printLink.setAttribute('href', payload.printUrl || '#');
            const displayInfo = [
                { label: text.facility, value: payload.display?.facility || '-' },
                { label: text.workgroup, value: payload.display?.workgroup || '-' },
                { label: text.workstation, value: payload.display?.workstation || '-' },
                { label: text.displayLabel, value: payload.display?.display || '-' },
                { label: text.performedAt, value: payload.performedAt || '-' },
                { label: text.result, value: payload.resultLabel || '-' },
            ];
            this.body.innerHTML = `
                <div class="space-y-5">
                    <section class="flex flex-wrap items-center gap-3 rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4">
                        ${this.renderBadge(payload.resultLabel || 'Unknown', payload.resultTone || 'neutral')}
                        <span class="text-sm text-slate-500">${Perfectlum.escapeHtml(text.detailedSummaryForTask)}</span>
                    </section>
                    ${this.renderInfoGrid(displayInfo)}
                    ${payload.header?.length ? this.renderInfoGrid(payload.header) : ''}
                    ${payload.sections?.length ? payload.sections.map((section) => this.renderSection(section)).join('') : `<div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">${Perfectlum.escapeHtml(text.noStructuredSummary)}</div>`}
                </div>`;
            if (window.lucide) window.lucide.createIcons();
        },
        async load(id, name) {
            this.openSkeleton(id, name);
            try {
                const payload = await Perfectlum.request(`/api/history-modal/${id}`);
                if (this.activeId !== id) return;
                this.render(payload);
            } catch (error) {
                this.body.innerHTML = `<div class="rounded-[1.5rem] border border-rose-200 bg-rose-50 px-4 py-8 text-center text-sm text-rose-600">${Perfectlum.escapeHtml(text.failedToLoadHistorySummary)}</div>`;
            }
        }
    };

    function historiesApiUrl() {
        const facId = document.getElementById('facility_field')?.value || '';
        const wgId  = document.getElementById('workgroups_field')?.value || '';
        const wsId  = document.getElementById('workstations_field')?.value || '';
        const checkedDisplayIds = Array.from(document.querySelectorAll('#displays_field input[name="displays[]"]:checked'))
            .map((input) => input.value)
            .filter(Boolean);

        const url = new URL('{{ url("api/histories") }}', window.location.origin);
        if (facId) url.searchParams.set('facility_id', facId);
        if (wgId) url.searchParams.set('workgroup_id', wgId);
        if (wsId) url.searchParams.set('workstation_id', wsId);
        if (checkedDisplayIds.length) url.searchParams.set('display_ids', checkedDisplayIds.join(','));
        if (@json($initialDisplayId)) url.searchParams.set('display_id', @json($initialDisplayId));
        if (historiesTableState.search) url.searchParams.set('search', historiesTableState.search);
        url.searchParams.set('page', String(historiesTableState.page));
        url.searchParams.set('limit', String(historiesTableState.limit));
        if (historiesTableState.sortKey) {
            url.searchParams.set('sort', historiesTableState.sortKey);
            url.searchParams.set('order', historiesTableState.sortOrder);
        }
        return `${url.pathname}${url.search}`;
    }

    function renderHistoryResultBadge(result) {
        const isPass = String(result || '').toLowerCase() === 'passed';
        const cls = isPass
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-rose-50 text-rose-700 border-rose-200';
        return `<span class="history-result-pill inline-flex rounded-full border px-2.5 py-0.5 text-xs font-bold ${cls}">${isPass ? 'Passed' : 'Failed'}</span>`;
    }

    function renderHistoryScopeButton(type, id, label, badge) {
        const safeLabel = String(label || '').trim();
        const numericId = Number(id) || 0;
        if (!numericId || !safeLabel || safeLabel === '-') {
            return '';
        }

        return `
            <button type="button" data-history-open="${Perfectlum.escapeHtml(type)}" data-history-id="${numericId}" class="history-scope-meta-button">
                <span class="history-scope-meta-badge">${Perfectlum.escapeHtml(badge)}</span>
                <span class="history-scope-meta-label">${Perfectlum.escapeHtml(safeLabel)}</span>
            </button>
        `;
    }

    function renderHistoryScopeStatic(label, badge) {
        const safeLabel = String(label || '').trim();
        if (!safeLabel || safeLabel === '-') {
            return '';
        }

        return `
            <span class="history-scope-meta-button">
                <span class="history-scope-meta-badge">${Perfectlum.escapeHtml(badge)}</span>
                <span class="history-scope-meta-label">${Perfectlum.escapeHtml(safeLabel)}</span>
            </span>
        `;
    }

    function renderHistoriesRows() {
        const body = document.getElementById('history-table-body');
        if (!body) return;
        if (historiesTableState.loading) {
            body.innerHTML = `<tr><td colspan="5" class="history-empty">Loading...</td></tr>`;
            return;
        }
        if (!historiesTableState.rows.length) {
            body.innerHTML = `<tr><td colspan="5" class="history-empty">No matching records found</td></tr>`;
            return;
        }

        body.innerHTML = historiesTableState.rows.map((r) => {
            const facility = renderHistoryScopeStatic(r.facName, 'F');
            const workgroup = renderHistoryScopeButton('workgroup', r.wgId, r.wgName, 'WG');
            const workstation = renderHistoryScopeButton('workstation', r.wsId, r.wsName, 'WS');
            const scopeMeta = [facility, workgroup, workstation]
                .filter(Boolean)
                .join('<span class="history-scope-separator">•</span>');

            return `
                <tr>
                    <td>
                        <button type="button" data-history-summary-open="${Number(r.id) || 0}" data-history-summary-name="${Perfectlum.escapeHtml(r.name || '-')}" class="history-record-title transition hover:underline">${Perfectlum.escapeHtml(r.name || '-')}</button>
                    </td>
                    <td><span class="text-sm text-slate-600">${Perfectlum.escapeHtml(r.pattern || '-')}</span></td>
                    <td>
                        <div class="history-scope-cell">
                            <button type="button" data-history-open="display" data-history-id="${Number(r.displayId) || 0}" class="history-scope-title transition hover:underline">${Perfectlum.escapeHtml(r.displayName || '-')}</button>
                            <div class="history-scope-meta">${scopeMeta}</div>
                        </div>
                    </td>
                    <td><span class="text-xs font-semibold text-slate-600">${Perfectlum.escapeHtml(r.time || '-')}</span></td>
                    <td>${renderHistoryResultBadge(r.result)}</td>
                </tr>
            `;
        }).join('');
    }

    function renderHistoriesPager() {
        const totalPages = Math.max(1, Math.ceil(historiesTableState.total / historiesTableState.limit));
        const currentPage = Math.min(historiesTableState.page, totalPages);
        const from = historiesTableState.total === 0 ? 0 : ((currentPage - 1) * historiesTableState.limit) + 1;
        const to = Math.min(historiesTableState.total, currentPage * historiesTableState.limit);
        const meta = document.getElementById('history-table-meta');
        const summary = document.getElementById('history-table-summary');
        const label = document.getElementById('history-page-label');
        const prev = document.getElementById('history-page-prev');
        const next = document.getElementById('history-page-next');
        if (meta) meta.textContent = `${historiesTableState.total} results`;
        if (summary) summary.textContent = `Showing ${from}-${to} of ${historiesTableState.total} results`;
        if (label) label.textContent = `Page ${currentPage} / ${totalPages}`;
        if (prev) prev.disabled = historiesTableState.loading || historiesTableState.fetching || currentPage <= 1;
        if (next) next.disabled = historiesTableState.loading || historiesTableState.fetching || currentPage >= totalPages;
    }

    function updateHistorySortIndicators() {
        document.querySelectorAll('[data-history-sort]').forEach((button) => {
            const key = button.getAttribute('data-history-sort');
            button.classList.toggle('is-active', key === historiesTableState.sortKey);
        });
        document.querySelectorAll('[data-history-sort-indicator]').forEach((node) => {
            const key = node.getAttribute('data-history-sort-indicator');
            if (key === historiesTableState.sortKey) {
                node.textContent = historiesTableState.sortOrder === 'asc' ? 'â†‘' : 'â†“';
            } else {
                node.textContent = 'â†•';
            }
        });
    }

    function updateHistorySortIndicatorsV2() {
        document.querySelectorAll('[data-history-sort]').forEach((button) => {
            const key = button.getAttribute('data-history-sort');
            button.classList.toggle('is-active', key === historiesTableState.sortKey);
        });
        document.querySelectorAll('[data-history-sort-indicator]').forEach((node) => {
            const key = node.getAttribute('data-history-sort-indicator');
            if (key === historiesTableState.sortKey) {
                node.innerHTML = historiesTableState.sortOrder === 'asc' ? '&#8593;' : '&#8595;';
            } else {
                node.innerHTML = '&#8597;';
            }
        });
    }

    async function loadHistoriesTable() {
        if (historiesTableState.fetching) return;
        historiesTableState.fetching = true;
        historiesTableState.loading = true;
        renderHistoriesPager();
        renderHistoriesRows();
        try {
            const payload = await Perfectlum.request(historiesApiUrl());
            historiesTableState.rows = Array.isArray(payload?.data) ? payload.data : [];
            historiesTableState.total = Number(payload?.total || historiesTableState.rows.length || 0);
            const totalPages = Math.max(1, Math.ceil(historiesTableState.total / historiesTableState.limit));
            if (historiesTableState.page > totalPages) {
                historiesTableState.page = totalPages;
                return loadHistoriesTable();
            }
        } catch (error) {
            historiesTableState.rows = [];
            historiesTableState.total = 0;
            const body = document.getElementById('history-table-body');
            if (body) {
                body.innerHTML = `<tr><td colspan="5" class="history-empty text-rose-600">${Perfectlum.escapeHtml(error.message || 'Unable to load data')}</td></tr>`;
            }
        } finally {
            historiesTableState.fetching = false;
            historiesTableState.loading = false;
            renderHistoriesRows();
            renderHistoriesPager();
        }
    }

    function openHistoryHierarchy(type, id) {
        const numericId = Number(id) || 0;
        if (!numericId) return;
        window.dispatchEvent(new CustomEvent('open-hierarchy', {
            detail: { type, id: numericId }
        }));
    }

    // JS functions for filter-panel (as expected by x-filter-panel)
    window.fetch_workgroups = function(el) {
        let id = el.value;
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('id', id);
        Perfectlum.postForm('{{ url("fetch-groups") }}', formData)
            .then(data => {
                if (data.success) {
                    let wgField = document.getElementById('workgroups_field');
                    if(wgField) {
                        wgField.innerHTML = `<option value="">${text.allWorkgroups}</option>` + data.content;
                        setHistorySelectValue('workgroups_field', 'history-workgroup-label', '', text.allWorkgroups);
                        let wsField = document.getElementById('workstations_field');
                        if (wsField) {
                            wsField.innerHTML = `<option value="">${text.allWorkstations}</option>`;
                            setHistorySelectValue('workstations_field', 'history-workstation-label', '', text.allWorkstations);
                        }
                        const displaysField = document.getElementById('displays_field');
                        if (displaysField) {
                            displaysField.innerHTML = `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${Perfectlum.escapeHtml(text.allDisplaysInScope)}</div>`;
                            syncDisplayLabel();
                            updateDisplaysHint();
                        }
                        renderHistoryNativeOptions('workgroups_field', 'history-workgroup-options', 'history-workgroup-hint', historyFilterState.workgroupSearch, (value) => {
                            setHistorySelectValue('workgroups_field', 'history-workgroup-label', value, 'All workgroups');
                            closeHistoryDropdowns();
                            fetch_workstations(document.getElementById('workgroups_field'));
                        }, 'No workgroups found');
                        refreshHistoriesGrid();
                    }
                }
            });
    };

    window.fetch_workstations = function(el) {
        let id = el.value;
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('id', id);
        Perfectlum.postForm('{{ url("fetch-workstations") }}', formData)
            .then(data => {
                if (data.success) {
                    let wsField = document.getElementById('workstations_field');
                    if(wsField) {
                        wsField.innerHTML = `<option value="">${text.allWorkstations}</option>` + data.content;
                        setHistorySelectValue('workstations_field', 'history-workstation-label', '', text.allWorkstations);
                        const displaysField = document.getElementById('displays_field');
                        if (displaysField) {
                            displaysField.innerHTML = `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${Perfectlum.escapeHtml(text.allDisplaysInScope)}</div>`;
                            syncDisplayLabel();
                            updateDisplaysHint();
                        }
                        renderHistoryNativeOptions('workstations_field', 'history-workstation-options', 'history-workstation-hint', historyFilterState.workstationSearch, (value) => {
                            setHistorySelectValue('workstations_field', 'history-workstation-label', value, 'All workstations');
                            closeHistoryDropdowns();
                            fetch_displays_checklist(document.getElementById('workstations_field'));
                        }, 'No workstations found');
                        refreshHistoriesGrid();
                    }
                }
            });
    };

    window.fetch_displays_checklist = function(el) {
        let id = el.value;
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('id', id);
        Perfectlum.postForm('{{ url("fetch-displays-checklist") }}', formData)
            .then(data => {
                if (data.success) {
                    let dField = document.getElementById('displays_field');
                    if(dField) {
                        dField.innerHTML = data.content;
                        dField.querySelectorAll('input[name="displays[]"]').forEach((input) => {
                            input.addEventListener('change', () => {
                                syncDisplayLabel();
                                refreshHistoriesGrid();
                            });
                        });
                        syncDisplayLabel();
                        filterDisplaysList();
                        refreshHistoriesGrid();
                    }
                }
            });
    };

    function initHistoriesGrid() {
        const tableBody = document.getElementById('history-table-body');
        if (!tableBody || tableBody.dataset.historyInitialized === '1') return;
        tableBody.dataset.historyInitialized = '1';
        historySummaryModal.init();
        document.getElementById('history-facility-trigger')?.addEventListener('click', () => {
            toggleHistoryDropdown('facility');
            renderHistoryNativeOptions('facility_field', 'history-facility-options', 'history-facility-hint', historyFilterState.facilitySearch, (value) => {
                setHistorySelectValue('facility_field', 'history-facility-label', value, '{{ $role === "super" ? "All facilities" : ($facilities[0]["name"] ?? "Please select") }}');
                closeHistoryDropdowns();
                fetch_workgroups(document.getElementById('facility_field'));
            }, 'No facilities found');
        });
        document.getElementById('history-workgroup-trigger')?.addEventListener('click', () => {
            toggleHistoryDropdown('workgroup');
            renderHistoryNativeOptions('workgroups_field', 'history-workgroup-options', 'history-workgroup-hint', historyFilterState.workgroupSearch, (value) => {
                setHistorySelectValue('workgroups_field', 'history-workgroup-label', value, 'All workgroups');
                closeHistoryDropdowns();
                fetch_workstations(document.getElementById('workgroups_field'));
            }, 'No workgroups found');
        });
        document.getElementById('history-workstation-trigger')?.addEventListener('click', () => {
            toggleHistoryDropdown('workstation');
            renderHistoryNativeOptions('workstations_field', 'history-workstation-options', 'history-workstation-hint', historyFilterState.workstationSearch, (value) => {
                setHistorySelectValue('workstations_field', 'history-workstation-label', value, 'All workstations');
                closeHistoryDropdowns();
                fetch_displays_checklist(document.getElementById('workstations_field'));
            }, 'No workstations found');
        });
        document.getElementById('history-displays-trigger')?.addEventListener('click', () => {
            toggleHistoryDropdown('displays');
            filterDisplaysList();
        });

        document.getElementById('history-facility-search')?.addEventListener('input', (event) => {
            historyFilterState.facilitySearch = event.target.value || '';
            renderHistoryNativeOptions('facility_field', 'history-facility-options', 'history-facility-hint', historyFilterState.facilitySearch, (value) => {
                setHistorySelectValue('facility_field', 'history-facility-label', value, '{{ $role === "super" ? "All facilities" : ($facilities[0]["name"] ?? "Please select") }}');
                closeHistoryDropdowns();
                fetch_workgroups(document.getElementById('facility_field'));
            }, 'No facilities found');
        });
        document.getElementById('history-workgroup-search')?.addEventListener('input', (event) => {
            historyFilterState.workgroupSearch = event.target.value || '';
            renderHistoryNativeOptions('workgroups_field', 'history-workgroup-options', 'history-workgroup-hint', historyFilterState.workgroupSearch, (value) => {
                setHistorySelectValue('workgroups_field', 'history-workgroup-label', value, 'All workgroups');
                closeHistoryDropdowns();
                fetch_workstations(document.getElementById('workgroups_field'));
            }, 'No workgroups found');
        });
        document.getElementById('history-workstation-search')?.addEventListener('input', (event) => {
            historyFilterState.workstationSearch = event.target.value || '';
            renderHistoryNativeOptions('workstations_field', 'history-workstation-options', 'history-workstation-hint', historyFilterState.workstationSearch, (value) => {
                setHistorySelectValue('workstations_field', 'history-workstation-label', value, 'All workstations');
                closeHistoryDropdowns();
                fetch_displays_checklist(document.getElementById('workstations_field'));
            }, 'No workstations found');
        });
        document.getElementById('history-displays-search')?.addEventListener('input', (event) => {
            historyFilterState.displaysSearch = event.target.value || '';
            filterDisplaysList();
        });
        document.getElementById('history-table-search')?.addEventListener('input', (event) => {
            clearTimeout(historiesTableState.searchTimer);
            historiesTableState.searchTimer = window.setTimeout(() => {
                historiesTableState.search = String(event.target.value || '').trim();
                historiesTableState.page = 1;
                loadHistoriesTable();
            }, 350);
        });
        document.querySelectorAll('[data-history-sort]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const key = button.getAttribute('data-history-sort');
                if (!key) return;
                if (historiesTableState.sortKey === key) {
                    historiesTableState.sortOrder = historiesTableState.sortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    historiesTableState.sortKey = key;
                    historiesTableState.sortOrder = key === 'time' ? 'desc' : 'asc';
                }
                historiesTableState.page = 1;
                updateHistorySortIndicatorsV2();
                loadHistoriesTable();
            });
        });
        document.getElementById('history-page-prev')?.addEventListener('click', () => {
            if (historiesTableState.page <= 1 || historiesTableState.loading || historiesTableState.fetching) return;
            historiesTableState.page -= 1;
            loadHistoriesTable();
        });
        document.getElementById('history-page-next')?.addEventListener('click', () => {
            const totalPages = Math.max(1, Math.ceil(historiesTableState.total / historiesTableState.limit));
            if (historiesTableState.page >= totalPages || historiesTableState.loading || historiesTableState.fetching) return;
            historiesTableState.page += 1;
            loadHistoriesTable();
        });

        document.getElementById('reset-history-filters')?.addEventListener('click', () => {
            historyFilterState.facilitySearch = '';
            historyFilterState.workgroupSearch = '';
            historyFilterState.workstationSearch = '';
            historyFilterState.displaysSearch = '';
            setHistorySelectValue('facility_field', 'history-facility-label', initialFacilityValue, initialFacilityLabel);
            if (document.getElementById('history-facility-search')) document.getElementById('history-facility-search').value = '';
            if (document.getElementById('history-workgroup-search')) document.getElementById('history-workgroup-search').value = '';
            if (document.getElementById('history-workstation-search')) document.getElementById('history-workstation-search').value = '';
            if (document.getElementById('history-displays-search')) document.getElementById('history-displays-search').value = '';
            fetch_workgroups(document.getElementById('facility_field'));
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('#history-facility-trigger') &&
                !event.target.closest('#history-facility-panel') &&
                !event.target.closest('#history-workgroup-trigger') &&
                !event.target.closest('#history-workgroup-panel') &&
                !event.target.closest('#history-workstation-trigger') &&
                !event.target.closest('#history-workstation-panel') &&
                !event.target.closest('#history-displays-trigger') &&
                !event.target.closest('#history-displays-panel')) {
                closeHistoryDropdowns();
            }
        });

        syncDisplayLabel();
        updateDisplaysHint();
        renderHistoryNativeOptions('facility_field', 'history-facility-options', 'history-facility-hint', historyFilterState.facilitySearch, (value) => {
            setHistorySelectValue('facility_field', 'history-facility-label', value, '{{ $role === "super" ? "All facilities" : ($facilities[0]["name"] ?? "Please select") }}');
            closeHistoryDropdowns();
            fetch_workgroups(document.getElementById('facility_field'));
        }, 'No facilities found');
        renderHistoryNativeOptions('workgroups_field', 'history-workgroup-options', 'history-workgroup-hint', historyFilterState.workgroupSearch, (value) => {
            setHistorySelectValue('workgroups_field', 'history-workgroup-label', value, 'All workgroups');
            closeHistoryDropdowns();
            fetch_workstations(document.getElementById('workgroups_field'));
        }, 'No workgroups found');
        renderHistoryNativeOptions('workstations_field', 'history-workstation-options', 'history-workstation-hint', historyFilterState.workstationSearch, (value) => {
            setHistorySelectValue('workstations_field', 'history-workstation-label', value, 'All workstations');
            closeHistoryDropdowns();
            fetch_displays_checklist(document.getElementById('workstations_field'));
        }, 'No workstations found');

        if (document.getElementById('facility_field')?.value) {
            fetch_workgroups(document.getElementById('facility_field'));
        }

        document.getElementById('history-table-body')?.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-history-summary-open]');
            if (trigger) {
                event.preventDefault();
                historySummaryModal.load(Number(trigger.dataset.historySummaryOpen), trigger.dataset.historySummaryName || 'History Summary');
                return;
            }
            const openTrigger = event.target.closest('[data-history-open]');
            if (!openTrigger) return;
            event.preventDefault();
            openHistoryHierarchy(openTrigger.dataset.historyOpen, openTrigger.dataset.historyId);
        });

        updateHistorySortIndicatorsV2();
        loadHistoriesTable();
    }

    window.historyPageCleanup = function () {
        try {
            historySummaryModal.close();
        } catch (_) {}
        try {
            if (historySummaryModal.root && historySummaryModal.root.parentElement === document.body) {
                historySummaryModal.root.remove();
            }
        } catch (_) {}
    };

    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', initHistoriesGrid) : initHistoriesGrid();
})();
</script>

@include('common.navigations.footer')


