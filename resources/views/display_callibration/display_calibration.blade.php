@include('common.navigations.header')

@php
    $canManageDisplayCalibration = in_array(($role ?? session('role')), ['super', 'admin'], true);
@endphp

<style>
    .calibration-directory-shell {
        border-radius: 2rem;
        border: 1px solid #d5e0ec;
        background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        box-shadow: 0 26px 64px -46px rgba(15, 23, 42, 0.34);
    }
    .calibration-create-shell {
        position: relative;
        overflow: visible;
        isolation: isolate;
        z-index: 60;
        border-radius: 1.5rem;
        border: 1px solid #dce8f4;
        background: linear-gradient(180deg, #f9fcff 0%, #ffffff 100%);
        box-shadow: 0 14px 38px -28px rgba(15, 23, 42, 0.22);
    }

    .calibration-hierarchy-field {
        position: relative;
        z-index: 10;
    }

    .calibration-hierarchy-field:focus-within {
        z-index: 120;
    }

    .calibration-hierarchy-panel {
        z-index: 140;
    }
    .calibration-jobs-shell {
        position: relative;
        z-index: 1;
        border-radius: 2rem;
        border: 1px solid #d5e0ec;
        background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        box-shadow: 0 26px 64px -46px rgba(15, 23, 42, 0.34);
        overflow: hidden;
    }
    .calibration-jobs-head {
        padding: 18px 18px 12px;
    }
    .calibration-table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 18px;
        border-top: 1px solid #e3ecf5;
        border-bottom: 1px solid #e3ecf5;
        background: #f8fbff;
    }
    .calibration-table-search-wrap {
        position: relative;
        width: min(440px, 100%);
    }
    .calibration-table-search {
        width: 100%;
        height: 42px;
        border-radius: 999px;
        border: 1px solid #c9d8e8;
        padding: 0 44px 0 16px;
        font-size: 14px;
        font-weight: 600;
        color: #12263a;
        background: #fff;
    }
    .calibration-table-search:focus {
        outline: none;
        border-color: #1d9bf0;
        box-shadow: 0 0 0 3px rgba(29, 155, 240, 0.16);
    }
    .calibration-table-search-clear {
        position: absolute;
        top: 50%;
        right: 10px;
        display: inline-flex;
        width: 26px;
        height: 26px;
        transform: translateY(-50%);
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        color: #64748b;
        transition: background 0.15s ease, color 0.15s ease;
    }
    .calibration-table-search-clear:hover {
        background: #e8f2fb;
        color: #0f172a;
    }
    .calibration-table-search-clear[hidden] {
        display: none;
    }
    .calibration-table-wrap {
        overflow-x: auto;
        background: #fff;
    }
    [data-calibration-task-menu].is-floating {
        position: fixed !important;
        z-index: 2147483647 !important;
    }
    .calibration-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1180px;
        table-layout: fixed;
    }
    .calibration-table th:nth-child(1),
    .calibration-table td:nth-child(1) {
        width: 40%;
    }
    .calibration-table th:nth-child(2),
    .calibration-table td:nth-child(2) {
        width: 14%;
        text-align: center;
    }
    .calibration-table th:nth-child(3),
    .calibration-table td:nth-child(3) {
        width: 14%;
        text-align: center;
    }
    .calibration-table th:nth-child(4),
    .calibration-table td:nth-child(4) {
        width: 12%;
        text-align: center;
    }
    .calibration-table th:nth-child(5),
    .calibration-table td:nth-child(5) {
        width: 12%;
        text-align: center;
    }
    .calibration-table th:nth-child(6),
    .calibration-table td:nth-child(6) {
        width: 8%;
        text-align: center;
    }
    .calibration-table th {
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
    .calibration-table th:nth-child(4),
    .calibration-table th:nth-child(5) {
        text-align: center;
    }
    .calibration-table th:nth-child(4) [data-calibration-sort],
    .calibration-table th:nth-child(5) [data-calibration-sort] {
        width: 100%;
        justify-content: center;
        margin-left: 0;
    }
    .calibration-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #edf2f8;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
        background: #fff;
    }
    .calibration-table tbody tr:hover td {
        background: #f7fbff;
    }
    .calibration-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 16px 14px;
        border-top: 1px solid #dbe7f3;
        background: #f7fbff;
    }
    .calibration-pager {
        display: inline-flex;
        gap: 8px;
        align-items: center;
    }
    .calibration-page-btn {
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
    .calibration-page-btn:hover:not(:disabled) {
        border-color: #1d9bf0;
        color: #0f5f9f;
        background: #f0f8ff;
    }
    .calibration-page-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }
    .calibration-empty {
        padding: 24px 16px;
        text-align: center;
        color: #5f7388;
        font-size: 14px;
        border-bottom: 1px solid #edf2f8;
    }
    #tasks-grid .gridjs-head {
        display: none !important;
    }
    #tasks-grid .gridjs-wrapper,
    #tasks-grid .gridjs-table,
    #tasks-grid .gridjs-thead,
    #tasks-grid .gridjs-tbody {
        border: 0 !important;
        box-shadow: none !important;
        background: transparent !important;
    }
    #tasks-grid .gridjs-table {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }
    #tasks-grid .gridjs-th {
        padding: 13px 16px !important;
        text-align: left !important;
        border-bottom: 1px solid #d8e4f0 !important;
        background: #e9f1fa !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        letter-spacing: .18em !important;
        text-transform: uppercase !important;
        color: #4d647d !important;
        white-space: nowrap !important;
    }
    #tasks-grid [data-calibration-sort],
    .calibration-table [data-calibration-sort],
    .display-sort-btn {
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
    #tasks-grid [data-calibration-sort]:hover,
    .calibration-table [data-calibration-sort]:hover,
    .display-sort-btn:hover {
        background: #f2f7fd;
        color: #2f4d6a;
    }
    #tasks-grid [data-calibration-sort].is-active,
    .calibration-table [data-calibration-sort].is-active,
    .display-sort-btn.is-active {
        background: #e2edf9;
        color: #24486b;
    }
    #tasks-grid [data-calibration-sort-indicator],
    .calibration-table [data-calibration-sort-indicator],
    .display-sort-indicator {
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
    #tasks-grid [data-calibration-sort].is-active [data-calibration-sort-indicator],
    .calibration-table [data-calibration-sort].is-active [data-calibration-sort-indicator],
    .display-sort-btn.is-active .display-sort-indicator {
        background: #2f6fae;
        color: #ffffff;
    }
    #tasks-grid .gridjs-th:last-child,
    #tasks-grid .gridjs-td:last-child {
        text-align: center !important;
    }
    #tasks-grid .gridjs-td {
        padding: 12px 16px !important;
        border-bottom: 1px solid #edf2f8 !important;
        font-size: 14px !important;
        color: #334155 !important;
        vertical-align: middle !important;
        background: #fff !important;
    }
    .calibration-display-cell {
        max-width: 100%;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .calibration-display-title {
        display: block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.2;
        color: #0f172a;
    }
    .calibration-display-meta {
        margin-top: 0.28rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.2rem 0.36rem;
        min-width: 0;
        font-size: 11px;
        color: #94a3b8;
    }
    .calibration-display-meta-button {
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
    .calibration-display-meta-button:hover,
    .calibration-display-meta-button:focus-visible {
        color: #0284c7;
        outline: none;
    }
    .calibration-display-meta-badge {
        width: 0;
        height: 18px;
        border-radius: 999px;
        background: #1d9bf0;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0;
        line-height: 1;
        opacity: 0;
        transform: translateX(-6px);
        overflow: hidden;
        margin-right: 0;
        transition: width 180ms ease, opacity 180ms ease, transform 180ms ease, margin-right 180ms ease;
        flex: 0 0 auto;
    }
    .calibration-display-meta-button:hover .calibration-display-meta-badge,
    .calibration-display-meta-button:focus-visible .calibration-display-meta-badge {
        width: 18px;
        opacity: 1;
        transform: translateX(0);
        margin-right: 6px;
    }
    .calibration-display-meta-label {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .calibration-display-separator {
        color: #cbd5e1;
        font-weight: 700;
    }
    .calibration-cell-center {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100%;
        text-align: center;
    }
    .calibration-date-text {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        white-space: nowrap;
    }
    .calibration-date-text.is-overdue {
        color: #e11d48;
    }
    .calibration-created-text {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        white-space: nowrap;
    }
    .calibration-created-text.is-muted {
        color: #94a3b8;
        font-weight: 500;
    }
    #tasks-grid .gridjs-tr:hover .gridjs-td {
        background: #f7fbff !important;
    }
    #tasks-grid .gridjs-footer {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 10px !important;
        padding: 12px 16px 14px !important;
        border-top: 1px solid #dbe7f3 !important;
        background: #f7fbff !important;
    }
    #tasks-grid .gridjs-pagination .gridjs-pages button {
        height: 32px !important;
        min-width: 32px !important;
        border-radius: 999px !important;
        border: 1px solid #c7d6e7 !important;
        background: #ffffff !important;
        color: #2c4158 !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        padding: 0 12px !important;
    }
    #tasks-grid .gridjs-pagination .gridjs-pages button:hover:not(:disabled) {
        border-color: #1d9bf0 !important;
        color: #0f5f9f !important;
        background: #f0f8ff !important;
    }
    #tasks-grid .gridjs-pagination .gridjs-pages button.gridjs-currentPage {
        border-color: #1d9bf0 !important;
        background: #e7f4ff !important;
        color: #0f5f9f !important;
    }
    #tasks-grid .gridjs-pagination .gridjs-pages button:disabled {
        opacity: .45 !important;
        cursor: not-allowed !important;
    }
    #tasks-grid .gridjs-pagination .gridjs-summary {
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #64748b !important;
    }
    @media (max-width: 960px) {
        .calibration-table-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .calibration-table-search {
            width: 100%;
        }
    }
</style>

{{-- ===================== DISPLAY CALIBRATION PAGE ===================== --}}
<div class="space-y-6 pb-8 font-inter theme-lum">

    <section class="rounded-[2rem] border border-slate-200 bg-white px-7 py-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
        <div class="flex items-start gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-slate-200 bg-sky-50 text-sky-600 shadow-sm">
                <i data-lucide="monitor-play" class="h-6 w-6"></i>
            </div>
            <div class="space-y-2">
                <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Admin Workspace') }}</p>
                <h1 class="text-4xl font-extrabold tracking-tight text-slate-900">{{ __('Calibrate Display') }}</h1>
                <p class="max-w-3xl text-sm text-slate-500">
                    {{ __('Create calibration tasks by scope and monitor recent jobs in one place.') }}
                </p>
            </div>
        </div>
    </section>

    <section class="calibration-directory-shell p-6">

    @if($canManageDisplayCalibration)
    {{-- ── NEW CALIBRATION TASK FORM ── --}}
    <div class="calibration-create-shell mb-6 p-5">
    <div class="mb-4">
        <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Create Calibration') }}</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ __('Run calibration by hierarchy') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('Select scope, choose displays, then start calibration.') }}</p>
    </div>
    <form method="post" action="" class="w-full" id="display-calibration-quick-form">
        {{csrf_field()}}
        <input type="hidden" name="calibrate" value="1">

        {{-- Top Filter Row --}}
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
            
            {{-- 1. Facility --}}
            <div class="flex flex-col gap-1.5 calibration-hierarchy-field">
                <label class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Facility') }}</label>
                <div class="relative">
                    <select name="facility" id="calibrate-facility-native" required class="hidden" onchange="fetch_workgroups(this);">
                        <option value="">{{ __('Please select') }}</option>
                        @if (!$user->hasRole('super'))
                            @foreach($facilities as $fc)
                            <option value="{{$fc['id']}}">{{$fc['name']}}</option>
                            @endforeach
                        @else
                            @foreach($facilities as $fc)
                            <option value="{{$fc->id}}">{{$fc->name}}</option>
                            @endforeach
                        @endif
                    </select>
                    <button type="button" id="calibrate-facility-trigger" onclick="window.openCalibrateHierarchyDropdown && window.openCalibrateHierarchyDropdown('facility')"
                            class="flex w-full h-[42px] items-center justify-between rounded-lg border border-gray-200 bg-white px-4 text-[13px] text-gray-700 shadow-sm transition-all hover:border-gray-300 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 cursor-pointer">
                        <span id="calibrate-facility-label" class="truncate">{{ __('Please select') }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div id="calibrate-facility-panel" class="calibration-hierarchy-panel absolute left-0 right-0 top-[calc(100%+0.5rem)] hidden rounded-2xl border border-gray-200 bg-white p-3 shadow-xl">
                        <input id="calibrate-facility-search" type="text" placeholder="{{ __('Search facilities...') }}" class="mb-2 h-10 w-full rounded-xl border border-gray-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="calibrate-facility-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="calibrate-facility-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            {{-- 2. Workgroup --}}
            <div class="flex flex-col gap-1.5 calibration-hierarchy-field">
                <label class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Workgroup') }}</label>
                <div class="relative">
                    <select name="workgroup" id="workgroups_field" onchange="fetch_workstations(this)" class="hidden">
                        <option value="">{{ __('Select Facility first') }}</option>
                    </select>
                    <button type="button" id="calibrate-workgroup-trigger" onclick="window.openCalibrateHierarchyDropdown && window.openCalibrateHierarchyDropdown('workgroup')"
                            class="flex w-full h-[42px] items-center justify-between rounded-lg border border-gray-200 bg-white px-4 text-[13px] text-gray-700 shadow-sm transition-all hover:border-gray-300 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 cursor-pointer disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400">
                        <span id="calibrate-workgroup-label" class="truncate">{{ __('Select Facility first') }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div id="calibrate-workgroup-panel" class="calibration-hierarchy-panel absolute left-0 right-0 top-[calc(100%+0.5rem)] hidden rounded-2xl border border-gray-200 bg-white p-3 shadow-xl">
                        <input id="calibrate-workgroup-search" type="text" placeholder="{{ __('Search workgroups...') }}" class="mb-2 h-10 w-full rounded-xl border border-gray-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="calibrate-workgroup-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="calibrate-workgroup-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            {{-- 3. Workstation --}}
            <div class="flex flex-col gap-1.5 calibration-hierarchy-field">
                <label class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Workstation') }}</label>
                <div class="relative">
                    <select name="workstation" id="workstations_field" onchange="fetch_displays_checklist(this)" class="hidden">
                        <option value="">{{ __('Select Workgroup first') }}</option>
                    </select>
                    <button type="button" id="calibrate-workstation-trigger" onclick="window.openCalibrateHierarchyDropdown && window.openCalibrateHierarchyDropdown('workstation')"
                            class="flex w-full h-[42px] items-center justify-between rounded-lg border border-gray-200 bg-white px-4 text-[13px] text-gray-700 shadow-sm transition-all hover:border-gray-300 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 cursor-pointer disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400">
                        <span id="calibrate-workstation-label" class="truncate">{{ __('Select Workgroup first') }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div id="calibrate-workstation-panel" class="calibration-hierarchy-panel absolute left-0 right-0 top-[calc(100%+0.5rem)] hidden rounded-2xl border border-gray-200 bg-white p-3 shadow-xl">
                        <input id="calibrate-workstation-search" type="text" placeholder="{{ __('Search workstations...') }}" class="mb-2 h-10 w-full rounded-xl border border-gray-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="calibrate-workstation-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="calibrate-workstation-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            {{-- 4. Display (Checklist Dropdown) --}}
            <div class="flex flex-col gap-1.5 relative calibration-hierarchy-field">
                <label class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Displays') }}</label>
                <button type="button" id="displays-dropdown" onclick="window.openCalibrateHierarchyDropdown && window.openCalibrateHierarchyDropdown('displays')"
                        class="w-full h-[42px] px-4 flex items-center justify-between rounded-lg text-[13px] outline-none border border-gray-200 bg-white text-gray-700 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 shadow-sm transition-all cursor-pointer">
                    <span id="calibrate-displays-label">{{ __('Select Workstation first') }}</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                </button>
                <div class="calibration-hierarchy-panel absolute top-[66px] left-0 w-full bg-white border border-gray-200 rounded-2xl shadow-xl hidden"
                     id="displays_field">
                    <div class="p-3 border-b border-gray-100">
                        <input id="calibrate-displays-search" type="text" placeholder="{{ __('Search displays...') }}" class="h-10 w-full rounded-xl border border-gray-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    </div>
                    <div id="calibrate-displays-options" class="max-h-60 overflow-y-auto">
                        <div class="px-4 py-3 text-[13px] text-gray-500">{{ __('Select Workstation first') }}</div>
                    </div>
                </div>
            </div>

            <div class="flex items-end">
                <button type="submit" id="submit_btn" disabled
                        class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-sky-500 px-6 text-sm font-semibold text-white shadow-[0_16px_30px_rgba(14,165,233,0.24)] transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-white/80 disabled:shadow-none">
                    <i data-lucide="play" class="h-4 w-4"></i>
                    {{ __('Calibrate') }}
                </button>
            </div>
        </div>
    </form>
    </div>
    @endif

    <div class="calibration-jobs-shell">
        <div class="calibration-jobs-head">
            <div class="space-y-2">
                <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Calibration Tasks') }}</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ __('Recent calibration jobs') }}</h2>
                <p class="mt-1 max-w-3xl text-sm text-slate-500">{{ __('Latest calibration tasks only.') }}</p>
            </div>
        </div>

        <div class="calibration-table-toolbar">
            <div class="calibration-table-search-wrap">
                <input type="text" id="gridjs-custom-search" placeholder="{{ __('Search calibration jobs...') }}" class="calibration-table-search transition-all placeholder-gray-400">
                <button type="button" id="calibration-table-search-clear" class="calibration-table-search-clear" aria-label="{{ __('Clear search') }}" hidden>
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
            <div id="calibration-table-meta" class="text-[12px] font-semibold text-slate-500"></div>
        </div>

        {{-- Tasks Table Wrapper --}}
        <div class="calibration-table-wrap">
            <table class="calibration-table">
                <thead>
                    <tr>
                        <th><button type="button" data-calibration-sort="display_name"><span>{{ __('Display') }}</span><span data-calibration-sort-indicator="display_name">↕</span></button></th>
                        <th><button type="button" data-calibration-sort="task_name"><span>{{ __('Task Type') }}</span><span data-calibration-sort-indicator="task_name">↕</span></button></th>
                        <th><button type="button" data-calibration-sort="schedule_name"><span>{{ __('Schedule Type') }}</span><span data-calibration-sort-indicator="schedule_name">↕</span></button></th>
                        <th><button type="button" data-calibration-sort="due_at"><span>{{ __('Due Date') }}</span><span data-calibration-sort-indicator="due_at">↕</span></button></th>
                        <th><button type="button" data-calibration-sort="created_at"><span>{{ __('Created') }}</span><span data-calibration-sort-indicator="created_at">↓</span></button></th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="calibration-tasks-body"></tbody>
            </table>
            <div class="calibration-table-footer">
                <div id="calibration-table-summary" class="text-[12px] font-semibold text-slate-500"></div>
                <div class="calibration-pager">
                    <button id="calibration-page-prev" type="button" class="calibration-page-btn">{{ __('Previous') }}</button>
                    <span id="calibration-page-label" class="text-[12px] font-semibold text-slate-500"></span>
                    <button id="calibration-page-next" type="button" class="calibration-page-btn">{{ __('Next') }}</button>
                </div>
            </div>
        </div>
    </div>

</section>
</div>

<div id="calibration-job-modal" class="fixed inset-0 z-[9200] hidden">
    <div id="calibration-job-backdrop" data-calibration-job-dismiss="1" class="absolute inset-0 bg-slate-900/45 backdrop-blur-sm"></div>
    <div class="pointer-events-none absolute inset-0 flex items-center justify-center p-4 sm:p-6">
        <div class="pointer-events-auto relative w-full max-w-4xl overflow-hidden rounded-[1.75rem] border border-slate-200 bg-[#F8FAFC] shadow-[0_24px_80px_rgba(15,23,42,0.24)]">
            <div class="relative overflow-hidden bg-gradient-to-r from-[#1175FF] to-[#0A62F0] px-8 py-7 text-white">
                <div class="absolute inset-0 opacity-[0.18]" style="background-image: radial-gradient(rgba(255,255,255,1) 1.4px, transparent 1.4px); background-size: 16px 16px;"></div>
                <button type="button" id="calibration-job-close" data-calibration-job-dismiss="1" class="absolute right-6 top-6 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/10 text-white transition hover:bg-black/20">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
                <div class="relative z-10 flex items-start gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm">
                        <i data-lucide="shield-check" class="h-6 w-6"></i>
                    </div>
                    <div class="space-y-2">
                        <p class="text-[11px] font-black uppercase tracking-[0.28em] text-white/80">{{ __('Calibration Job') }}</p>
                        <h3 id="calibration-job-title" class="text-3xl font-extrabold tracking-tight">{{ __('Loading…') }}</h3>
                        <p id="calibration-job-subtitle" class="max-w-2xl text-sm text-white/80"></p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 p-8 lg:grid-cols-[1.4fr_0.9fr]">
                <div class="space-y-6">
                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">{{ __('Job Summary') }}</p>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Task') }}</p>
                                <p id="calibration-job-task" class="mt-2 text-base font-bold text-slate-900"></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Schedule') }}</p>
                                <p id="calibration-job-schedule" class="mt-2 text-base font-bold text-slate-900"></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Due') }}</p>
                                <p id="calibration-job-due" class="mt-2 text-base font-bold text-slate-900"></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Created') }}</p>
                                <p id="calibration-job-created" class="mt-2 text-base font-bold text-slate-900"></p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">{{ __('Selected Scope') }}</p>
                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <button type="button" id="calibration-job-display-link" class="rounded-[1.25rem] border border-sky-200 bg-sky-50 px-4 py-4 text-left transition hover:border-sky-300 hover:bg-sky-100/70">
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-sky-500">{{ __('Display') }}</p>
                                <p id="calibration-job-display" class="mt-2 text-sm font-bold text-slate-900"></p>
                            </button>
                            <button type="button" id="calibration-job-workstation-link" class="rounded-[1.25rem] border border-emerald-200 bg-emerald-50 px-4 py-4 text-left transition hover:border-emerald-300 hover:bg-emerald-100/70">
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-emerald-500">{{ __('Workstation') }}</p>
                                <p id="calibration-job-workstation" class="mt-2 text-sm font-bold text-slate-900"></p>
                            </button>
                            <button type="button" id="calibration-job-workgroup-link" class="rounded-[1.25rem] border border-violet-200 bg-violet-50 px-4 py-4 text-left transition hover:border-violet-300 hover:bg-violet-100/70">
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-violet-500">{{ __('Workgroup') }}</p>
                                <p id="calibration-job-workgroup" class="mt-2 text-sm font-bold text-slate-900"></p>
                            </button>
                            <button type="button" id="calibration-job-facility-link" class="rounded-[1.25rem] border border-amber-200 bg-amber-50 px-4 py-4 text-left transition hover:border-amber-300 hover:bg-amber-100/70">
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-500">{{ __('Facility') }}</p>
                                <p id="calibration-job-facility" class="mt-2 text-sm font-bold text-slate-900"></p>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">{{ __('Execution Context') }}</p>
                        <div class="mt-5 space-y-4">
                            <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Status') }}</p>
                                <p id="calibration-job-status" class="mt-2 text-sm font-bold text-slate-900"></p>
                            </div>
                            <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Created by') }}</p>
                                <p id="calibration-job-created-by" class="mt-2 text-sm font-bold text-slate-900"></p>
                            </div>
                            <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Start window') }}</p>
                                <p id="calibration-job-start-at" class="mt-2 text-sm font-bold text-slate-900"></p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">{{ __('Notes') }}</p>
                        <p class="mt-4 text-sm leading-6 text-slate-600">{{ __('Shows the saved schedule details for this calibration task.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL: DELETE CONFIRM (Alpine Version) ── --}}
<div x-data="{ open: false, taskId: null }"
     x-init="$nextTick(() => { if ($el.parentElement !== document.body) document.body.appendChild($el); })"
     @delete-task.window="open = true; taskId = $event.detail.id"
     class="fixed inset-0 pointer-events-none"
     style="z-index: 2147483647;">
    <div x-show="open" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 pointer-events-auto bg-slate-950/45 backdrop-blur-[2px] transition-opacity"
         style="z-index: 2147483647;"></div>
    
    <div x-show="open" x-cloak
         class="fixed inset-0 pointer-events-auto flex min-h-screen w-screen items-center justify-center overflow-y-auto px-4 py-8"
         style="z-index: 2147483647;">
        <div class="flex min-h-full w-full items-center justify-center text-center">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.away="open = false"
                 class="relative w-full max-w-md transform overflow-hidden rounded-[2rem] border border-slate-200 bg-white text-left shadow-2xl transition-all">
                
                <div class="bg-white px-6 py-6 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-500 shadow-sm">
                        <i data-lucide="trash-2" class="h-6 w-6 text-red-500"></i>
                    </div>
                    <h3 class="text-base font-semibold leading-6 text-gray-900">{{ __('Delete Task') }}</h3>
                    <div class="mt-2 text-sm text-gray-500">
                        {{ __('This action cannot be undone. Are you sure?') }}
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button type="button" @click="open = false" class="inline-flex min-w-[100px] justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">{{ __('Cancel') }}</button>
                    <button type="button" @click="confirmDelete(taskId); open = false;" class="inline-flex min-w-[100px] justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $calibrationText = [
        'display' => __('Display'),
        'workstation' => __('Workstation'),
        'workgroup' => __('Workgroup'),
        'facility' => __('Facility'),
        'taskType' => __('Task Type'),
        'scheduleType' => __('Schedule Type'),
        'dueDate' => __('Due Date'),
        'created' => __('Created'),
        'actions' => __('Actions'),
        'notRecorded' => __('Not recorded'),
        'system' => __('System'),
        'status' => __('Status'),
        'createdBy' => __('Created by'),
        'startWindow' => __('Start window'),
        'searchCalibrationTasks' => __('Search calibration jobs...'),
        'previous' => __('Previous'),
        'next' => __('Next'),
        'showing' => __('Showing'),
        'results' => __('results'),
        'loading' => __('Loading...'),
        'noMatchingRecordsFound' => __('No matching records found'),
        'unableToLoadData' => __('Unable to load data'),
        'selectWorkstation' => __('Select Workstation'),
        'selectWorkgroupFirst' => __('Select Workgroup first'),
        'selectWorkstationFirst' => __('Select Workstation first'),
        'pleaseSelect' => __('Please select'),
        'noOptionsFound' => __('No options found'),
        'noWorkstationsFound' => __('No workstations found'),
        'deletionFailed' => __('Deletion failed'),
        'taskDeletedSuccessfully' => __('Task deleted successfully'),
    ];
@endphp
<script>
    (function () {
    const calibrationText = @json($calibrationText);
    const calibrateHierarchyState = {
        facilitySearch: '',
        workgroupSearch: '',
        workstationSearch: '',
        displaysSearch: '',
        activeDropdown: null,
    };
    const canManageCalibrationTasks = @json($canManageDisplayCalibration);
    const calibrationTaskRows = new Map();
    const calibrationGridState = {
        sortKey: 'created_at',
        sortOrder: 'desc',
    };
    const calibrationTableState = {
        page: 1,
        limit: 10,
        total: 0,
        loading: false,
        search: '',
        searchTimer: null,
    };

    function calibrationTasksUrl(keyword = '', page = null, limit = null) {
        const url = new URL('{{ url("api/calibration-tasks") }}', window.location.origin);
        if (keyword) {
            url.searchParams.set('search', keyword);
        }
        url.searchParams.set('sort', calibrationGridState.sortKey);
        url.searchParams.set('order', calibrationGridState.sortOrder);
        if (page !== null && page !== undefined) {
            url.searchParams.set('page', String(page));
        }
        if (limit !== null && limit !== undefined) {
            url.searchParams.set('limit', String(limit));
        }
        return `${url.pathname}${url.search}`;
    }

    function updateCalibrationSortIndicators() {
        document.querySelectorAll('[data-calibration-sort]').forEach((button) => {
            const key = button.getAttribute('data-calibration-sort');
            button.classList.toggle('is-active', key === calibrationGridState.sortKey);
        });
        document.querySelectorAll('[data-calibration-sort-indicator]').forEach((node) => {
            const key = node.getAttribute('data-calibration-sort-indicator');
            if (key === calibrationGridState.sortKey) {
                node.textContent = calibrationGridState.sortOrder === 'asc' ? '↑' : '↓';
            } else {
                node.textContent = '↕';
            }
        });
    }

    function openHierarchyEntity(type, id) {
        const numericId = Number(id) || 0;
        if (!numericId) return;
        window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type, id: numericId } }));
    }

    function openCalibrationJobModal(taskId) {
        const item = calibrationTaskRows.get(String(taskId));
        const modal = document.getElementById('calibration-job-modal');
        if (!item || !modal) return;

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        document.getElementById('calibration-job-title').textContent = item.displayName || calibrationText.display;
        document.getElementById('calibration-job-subtitle').textContent = `${item.taskName || '-'} • ${item.scheduleName || '-'}`;
        document.getElementById('calibration-job-task').textContent = item.taskName || '-';
        document.getElementById('calibration-job-schedule').textContent = item.scheduleName || '-';
        document.getElementById('calibration-job-due').textContent = item.dueAt || calibrationText.notRecorded;
        document.getElementById('calibration-job-created').textContent = item.createdAt || calibrationText.notRecorded;
        document.getElementById('calibration-job-display').textContent = item.displayName || '-';
        document.getElementById('calibration-job-workstation').textContent = item.wsName || '-';
        document.getElementById('calibration-job-workgroup').textContent = item.wgName || '-';
        document.getElementById('calibration-job-facility').textContent = item.facName || '-';
        document.getElementById('calibration-job-status').textContent = item.statusLabel || 'Active';
        document.getElementById('calibration-job-created-by').textContent = item.createdBy || calibrationText.system;
        document.getElementById('calibration-job-start-at').textContent = item.startAt || calibrationText.notRecorded;

        document.getElementById('calibration-job-display-link').onclick = () => openHierarchyEntity('display', item.displayId);
        document.getElementById('calibration-job-workstation-link').onclick = () => openHierarchyEntity('workstation', item.workstationId);
        document.getElementById('calibration-job-workgroup-link').onclick = () => openHierarchyEntity('workgroup', item.workgroupId);
        document.getElementById('calibration-job-facility-link').onclick = () => openHierarchyEntity('facility', item.facilityId);

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        window.lucide?.createIcons();
    }

    function closeCalibrationJobModal() {
        const modal = document.getElementById('calibration-job-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function parseNativeSelectOptions(selectId) {
        const select = document.getElementById(selectId);
        if (!select) return [];
        return Array.from(select.options || []).map((option) => ({
            value: option.value,
            label: option.textContent.trim(),
        }));
    }

    function toggleCalibrateDropdown(type) {
        const panels = {
            facility: document.getElementById('calibrate-facility-panel'),
            workgroup: document.getElementById('calibrate-workgroup-panel'),
            workstation: document.getElementById('calibrate-workstation-panel'),
            displays: document.getElementById('displays_field'),
        };
        calibrateHierarchyState.activeDropdown = calibrateHierarchyState.activeDropdown === type ? null : type;
        Object.entries(panels).forEach(([key, panel]) => {
            if (!panel) return;
            panel.classList.toggle('hidden', calibrateHierarchyState.activeDropdown !== key);
        });
    }

    function closeCalibrateDropdowns() {
        calibrateHierarchyState.activeDropdown = null;
        ['calibrate-facility-panel', 'calibrate-workgroup-panel', 'calibrate-workstation-panel', 'displays_field']
            .forEach((id) => document.getElementById(id)?.classList.add('hidden'));
    }

    window.openCalibrateHierarchyDropdown = function (type) {
        if (type === 'facility') {
            renderCalibrateNativeOptions('calibrate-facility-native', 'calibrate-facility-options', 'calibrate-facility-hint', calibrateHierarchyState.facilitySearch, (value) => {
                const select = document.getElementById('calibrate-facility-native');
                select.value = value;
                document.getElementById('calibrate-facility-label').textContent = select.options[select.selectedIndex]?.textContent || 'Please select';
                fetch_workgroups(select);
                closeCalibrateDropdowns();
            }, 'No facilities found');
        } else if (type === 'workgroup') {
            renderCalibrateNativeOptions('workgroups_field', 'calibrate-workgroup-options', 'calibrate-workgroup-hint', calibrateHierarchyState.workgroupSearch, (value) => {
                const select = document.getElementById('workgroups_field');
                select.value = value;
                document.getElementById('calibrate-workgroup-label').textContent = select.options[select.selectedIndex]?.textContent || 'Select Workgroup';
                fetch_workstations(select);
                closeCalibrateDropdowns();
            }, 'No workgroups found');
        } else if (type === 'workstation') {
            renderCalibrateNativeOptions('workstations_field', 'calibrate-workstation-options', 'calibrate-workstation-hint', calibrateHierarchyState.workstationSearch, (value) => {
                const select = document.getElementById('workstations_field');
                select.value = value;
                document.getElementById('calibrate-workstation-label').textContent = select.options[select.selectedIndex]?.textContent || 'Select Workstation';
                fetch_displays_checklist(select);
                closeCalibrateDropdowns();
            }, 'No workstations found');
        }

        toggleCalibrateDropdown(type);
    };

    function renderCalibrationTaskActions(taskId) {
        if (!canManageCalibrationTasks) {
            return '<span class="text-xs text-slate-400">No actions</span>';
        }

        return `
            <div class="relative flex justify-center">
                <button type="button"
                        data-calibration-task-toggle="${taskId}"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
                <div data-calibration-task-menu="${taskId}" class="absolute right-0 top-full z-20 mt-2 hidden w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                    <button type="button" data-calibration-task-edit="${taskId}" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i data-lucide="calendar-clock" class="h-4 w-4 text-sky-500"></i>
                        Schedule Task
                    </button>
                    <button type="button" data-calibration-task-delete="${taskId}" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                        Delete Task
                    </button>
                </div>
            </div>
        `;
    }

    function closeCalibrationTaskMenus() {
        document.querySelectorAll('[data-calibration-task-menu]').forEach((menu) => {
            menu.classList.add('hidden');
            menu.classList.remove('is-floating');
            menu.style.top = '';
            menu.style.left = '';
            menu.style.right = '';
        });
    }

    function positionCalibrationTaskMenu(toggle, menu) {
        if (!toggle || !menu) return;

        if (menu.parentElement !== document.body) {
            document.body.appendChild(menu);
        }

        const rect = toggle.getBoundingClientRect();
        const menuWidth = 176;
        const gap = 8;
        const viewportPadding = 12;
        const left = Math.max(
            viewportPadding,
            Math.min(window.innerWidth - menuWidth - viewportPadding, rect.right - menuWidth)
        );
        const menuHeight = 104;
        const preferredTop = rect.bottom + gap;
        const top = preferredTop + menuHeight > window.innerHeight - viewportPadding
            ? Math.max(viewportPadding, rect.top - menuHeight - gap)
            : preferredTop;

        menu.classList.add('is-floating');
        menu.style.left = `${left}px`;
        menu.style.top = `${top}px`;
        menu.style.right = 'auto';
    }

    function resetCalibrationQuickForm() {
        const facility = document.getElementById('calibrate-facility-native');
        const workgroup = document.getElementById('workgroups_field');
        const workstation = document.getElementById('workstations_field');
        const displaysBox = document.getElementById('calibrate-displays-options');

        if (facility) facility.value = '';
        if (workgroup) workgroup.innerHTML = '<option value="">Select Facility first</option>';
        if (workstation) workstation.innerHTML = '<option value="">Select Workgroup first</option>';

        document.getElementById('calibrate-facility-label').textContent = calibrationText.pleaseSelect;
        document.getElementById('calibrate-workgroup-label').textContent = calibrationText.selectWorkgroupFirst ? 'Select Facility first' : 'Select Facility first';
        document.getElementById('calibrate-workstation-label').textContent = calibrationText.selectWorkgroupFirst;
        document.getElementById('calibrate-displays-label').textContent = calibrationText.selectWorkstationFirst;
        if (displaysBox) {
            displaysBox.innerHTML = `<div class="px-4 py-3 text-[13px] text-gray-500">${calibrationText.selectWorkstationFirst}</div>`;
        }

        calibrateHierarchyState.facilitySearch = '';
        calibrateHierarchyState.workgroupSearch = '';
        calibrateHierarchyState.workstationSearch = '';
        calibrateHierarchyState.displaysSearch = '';
        ['calibrate-facility-search', 'calibrate-workgroup-search', 'calibrate-workstation-search', 'calibrate-displays-search']
            .forEach((id) => {
                const input = document.getElementById(id);
                if (input) input.value = '';
            });

        closeCalibrateDropdowns();
        updateCalibrateSubmitState();
    }

    function renderCalibrateNativeOptions(selectId, optionsId, hintId, query, onPick, emptyText = calibrationText.noOptionsFound) {
        const items = parseNativeSelectOptions(selectId).filter((item, index) => index > 0);
        const filtered = items.filter((item) => item.label.toLowerCase().includes((query || '').trim().toLowerCase()));
        const hint = document.getElementById(hintId);
        const box = document.getElementById(optionsId);
        if (hint) hint.textContent = filtered.length ? `${filtered.length} option${filtered.length === 1 ? '' : 's'}` : emptyText;
        if (!box) return;
        box.innerHTML = filtered.length
            ? filtered.map((item) => `<button type="button" data-value="${Perfectlum.escapeHtml(item.value)}" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 hover:bg-sky-50 hover:text-sky-700">${Perfectlum.escapeHtml(item.label)}</button>`).join('')
            : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${emptyText}</div>`;

        box.querySelectorAll('button[data-value]').forEach((button) => {
            button.addEventListener('click', () => onPick(button.dataset.value));
        });
    }

    function updateCalibrateSubmitState() {
        const button = document.getElementById('submit_btn');
        if (!button) return;
        const facility = document.getElementById('calibrate-facility-native')?.value || '';
        const workgroup = document.getElementById('workgroups_field')?.value || '';
        const workstation = document.getElementById('workstations_field')?.value || '';
        const box = document.getElementById('calibrate-displays-options');
        let hasDisplay = false;
        if (box) {
            const regular = Array.from(box.querySelectorAll('input[name="displays[]"]:not([data-select-all="1"])'));
            const selected = regular.filter((item) => item.checked);
            const selectAll = box.querySelector('input[name="displays[]"][data-select-all="1"]');
            hasDisplay = selected.length > 0 || (selectAll && selectAll.checked && regular.length > 0);
        }
        button.disabled = !(facility && workgroup && workstation && hasDisplay);
    }

    function updateCalibrateDisplaysLabel() {
        const list = Array.from(document.querySelectorAll('#calibrate-displays-options input[name="displays[]"]:not([data-select-all="1"])'));
        const checked = list.filter((item) => item.checked);
        const label = document.getElementById('calibrate-displays-label');
        if (!label) return;
        if (!list.length) {
            label.textContent = 'No displays found';
            return;
        }
        if (!checked.length || checked.length === list.length) {
            label.textContent = 'All displays in scope';
            return;
        }
        label.textContent = checked.length === 1
            ? (checked[0].dataset.label || '1 display selected')
            : `${checked.length} displays selected`;
        updateCalibrateSubmitState();
    }

    function fetch_workgroups(th) {
        var formData = new FormData();
        formData.append('_token', '{{csrf_token()}}');
        formData.append('id', th.value || '');

        fetch("{{ url('fetch-groups') }}", {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    document.getElementById('workgroups_field').innerHTML = data.content;
                    document.getElementById('workstations_field').innerHTML = '<option value="">Select Workgroup first</option>';
                    document.getElementById('calibrate-workgroup-label').textContent = 'Select Workgroup';
                    document.getElementById('calibrate-workstation-label').textContent = 'Select Workgroup first';
                    document.getElementById('calibrate-displays-options').innerHTML = '<div class="px-4 py-3 text-[13px] text-gray-500">Select Workstation first</div>';
                    document.getElementById('calibrate-displays-label').textContent = 'Select Workstation first';
                    updateCalibrateSubmitState();
                    renderCalibrateNativeOptions('workgroups_field', 'calibrate-workgroup-options', 'calibrate-workgroup-hint', calibrateHierarchyState.workgroupSearch, (value) => {
                        const select = document.getElementById('workgroups_field');
                        select.value = value;
                        document.getElementById('calibrate-workgroup-label').textContent = select.options[select.selectedIndex]?.textContent || 'Select Workgroup';
                        fetch_workstations(select);
                        closeCalibrateDropdowns();
                    }, 'No workgroups found');
                }
            });
    }

    function fetch_workstations(th) {
        const formData = new FormData();
        formData.append('_token', '{{csrf_token()}}');
        formData.append('id', th.value || '');

        fetch("{{ url('fetch-workstations') }}", {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    document.getElementById('workstations_field').innerHTML = data.content;
                    document.getElementById('calibrate-workstation-label').textContent = 'Select Workstation';
                    document.getElementById('calibrate-displays-options').innerHTML = '<div class="px-4 py-3 text-[13px] text-gray-500">Select Workstation first</div>';
                    document.getElementById('calibrate-displays-label').textContent = 'Select Workstation first';
                    updateCalibrateSubmitState();
                    renderCalibrateNativeOptions('workstations_field', 'calibrate-workstation-options', 'calibrate-workstation-hint', calibrateHierarchyState.workstationSearch, (value) => {
                        const select = document.getElementById('workstations_field');
                        select.value = value;
                        document.getElementById('calibrate-workstation-label').textContent = select.options[select.selectedIndex]?.textContent || 'Select Workstation';
                        fetch_displays_checklist(select);
                        closeCalibrateDropdowns();
                    }, 'No workstations found');
                }
            });
    }

    function fetch_displays_checklist(th) {
        var formData = new FormData();
        formData.append('_token', '{{csrf_token()}}');
        formData.append('id', th.value || '');

        fetch("{{ url('fetch-displays-checklist') }}", {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    let content = data.content;
                    if(content === '') content = '<div class="px-4 py-3 text-[13px] text-gray-500">No displays found.</div>';

                    content = content.replace(/form-check-input/g, "w-4 h-4 text-sky-500 border-gray-300 rounded focus:ring-sky-500 cursor-pointer");
                    content = content.replace(/form-check-label/g, "ml-3 text-[13px] text-gray-700 cursor-pointer select-none flex-1");
                    content = content.replace(/form-check mb-0 py-1 px-4/g, "flex items-center px-4 py-2.5 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0");

                    const box = document.getElementById('calibrate-displays-options');
                    box.innerHTML = content;

                    box.querySelectorAll('input[name="displays[]"], input[name="displays2[]"]').forEach((input) => {
                        if (!input.value) {
                            input.setAttribute('data-select-all', '1');
                        } else {
                            input.setAttribute('name', 'displays[]');
                            const label = input.parentElement?.querySelector('label')?.textContent?.trim() || 'Display';
                            input.dataset.label = label;
                        }
                        input.addEventListener('change', function () {
                            if (this.dataset.selectAll === '1') {
                                const checked = this.checked;
                                box.querySelectorAll('input[name="displays[]"]:not([data-select-all="1"])').forEach((item) => {
                                    item.checked = checked;
                                });
                            }
                            updateCalibrateDisplaysLabel();
                        });
                    });

                    updateCalibrateDisplaysLabel();
                    updateCalibrateSubmitState();
                }
            });
    }

    function filterCalibrateDisplays() {
        const query = (document.getElementById('calibrate-displays-search')?.value || '').trim().toLowerCase();
        const rows = document.querySelectorAll('#calibrate-displays-options > div');
        rows.forEach((row) => {
            const label = row.textContent.toLowerCase();
            row.style.display = !query || label.includes(query) ? '' : 'none';
        });
    }

    function initCalibrateSearchableDropdowns() {
        const facilityTrigger = document.getElementById('calibrate-facility-trigger');
        const workgroupTrigger = document.getElementById('calibrate-workgroup-trigger');
        const workstationTrigger = document.getElementById('calibrate-workstation-trigger');
        const displaysTrigger = document.getElementById('displays-dropdown');

        document.getElementById('calibrate-facility-search')?.addEventListener('input', (e) => {
            calibrateHierarchyState.facilitySearch = e.target.value || '';
            renderCalibrateNativeOptions('calibrate-facility-native', 'calibrate-facility-options', 'calibrate-facility-hint', calibrateHierarchyState.facilitySearch, (value) => {
                const select = document.getElementById('calibrate-facility-native');
                select.value = value;
                document.getElementById('calibrate-facility-label').textContent = select.options[select.selectedIndex]?.textContent || 'Please select';
                fetch_workgroups(select);
                closeCalibrateDropdowns();
            }, 'No facilities found');
        });
        document.getElementById('calibrate-workgroup-search')?.addEventListener('input', (e) => {
            calibrateHierarchyState.workgroupSearch = e.target.value || '';
            renderCalibrateNativeOptions('workgroups_field', 'calibrate-workgroup-options', 'calibrate-workgroup-hint', calibrateHierarchyState.workgroupSearch, (value) => {
                const select = document.getElementById('workgroups_field');
                select.value = value;
                document.getElementById('calibrate-workgroup-label').textContent = select.options[select.selectedIndex]?.textContent || 'Select Workgroup';
                fetch_workstations(select);
                closeCalibrateDropdowns();
            }, 'No workgroups found');
        });
        document.getElementById('calibrate-workstation-search')?.addEventListener('input', (e) => {
            calibrateHierarchyState.workstationSearch = e.target.value || '';
            renderCalibrateNativeOptions('workstations_field', 'calibrate-workstation-options', 'calibrate-workstation-hint', calibrateHierarchyState.workstationSearch, (value) => {
                const select = document.getElementById('workstations_field');
                select.value = value;
                document.getElementById('calibrate-workstation-label').textContent = select.options[select.selectedIndex]?.textContent || calibrationText.selectWorkstation;
                fetch_displays_checklist(select);
                closeCalibrateDropdowns();
            }, calibrationText.noWorkstationsFound);
        });
        document.getElementById('calibrate-displays-search')?.addEventListener('input', filterCalibrateDisplays);

        document.addEventListener('click', (event) => {
            if (
                !document.getElementById('calibrate-facility-panel')?.contains(event.target) &&
                !facilityTrigger?.contains(event.target) &&
                !document.getElementById('calibrate-workgroup-panel')?.contains(event.target) &&
                !workgroupTrigger?.contains(event.target) &&
                !document.getElementById('calibrate-workstation-panel')?.contains(event.target) &&
                !workstationTrigger?.contains(event.target) &&
                !document.getElementById('displays_field')?.contains(event.target) &&
                !displaysTrigger?.contains(event.target)
            ) {
                closeCalibrateDropdowns();
            }
        });
    }

    // Legacy Grid.js implementation (disabled after custom table migration)
    if (false) {
    document.addEventListener("DOMContentLoaded", function () {
        initCalibrateSearchableDropdowns();
        updateCalibrateSubmitState();
        const quickCalibrationForm = document.getElementById('display-calibration-quick-form');
        const customSearchParams = new URLSearchParams(window.location.search);
        const searchInput = document.getElementById('gridjs-custom-search');
        if (customSearchParams.has('keywords')) {
            searchInput.value = customSearchParams.get('keywords');
        }

        quickCalibrationForm?.addEventListener('submit', function (event) {
            event.preventDefault();

            if (typeof window.openTaskEditorWithPayload !== 'function') {
                return;
            }

            const facilityId = document.getElementById('calibrate-facility-native')?.value || '';
            const workgroupId = document.getElementById('workgroups_field')?.value || '';
            const workstationId = document.getElementById('workstations_field')?.value || '';
            const checkedDisplays = Array.from(document.querySelectorAll('#calibrate-displays-options input[name="displays[]"]:checked:not([data-select-all="1"])'))
                .map((input) => String(input.value || '').trim())
                .filter(Boolean);

            if (!facilityId) {
                window.notify?.('failed', 'Please select a facility first.');
                return;
            }

            window.openTaskEditorWithPayload({
                id: 0,
                tasktype: 'cal',
                quick_calibration: '1',
                lock_tasktype: '1',
                facility2: facilityId,
                workgroup2: workgroupId,
                workstation2: workstationId,
                displays: checkedDisplays
            }, {
                title: 'Calibrate Display',
                subtitle: checkedDisplays.length
                    ? `Set the schedule window for ${checkedDisplays.length} selected display${checkedDisplays.length > 1 ? 's' : ''}.`
                    : 'Set the schedule window for the current hierarchy scope before creating the calibration task.',
            });
        });

        window.grid = Perfectlum.createGrid('tasks-grid', {
            columns: [
                {
                    name: gridjs.html(`<button type="button" data-calibration-sort="display_name"><span>${Perfectlum.escapeHtml(calibrationText.display)}</span><span data-calibration-sort-indicator="display_name">↕</span></button>`),
                    width: '40%',
                    formatter: (cell, row) => gridjs.html(`
                        <div data-calibration-row-trigger="${Perfectlum.escapeHtml(row.cells[9].data)}" class="calibration-display-cell cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50">
                            <button type="button" onclick="event.stopPropagation(); openHierarchyEntity('display', ${Number(row.cells[10].data) || 0})" class="calibration-display-title text-left transition hover:text-sky-600 hover:underline">${Perfectlum.escapeHtml(cell)}</button>
                            <div class="calibration-display-meta">
                                <button type="button" onclick="event.stopPropagation(); openHierarchyEntity('workstation', ${Number(row.cells[11].data) || 0})" class="meta-link transition hover:text-emerald-600 hover:underline">${Perfectlum.escapeHtml(row.cells[1].data)}</button>
                                <span class="shrink-0">•</span>
                                <button type="button" onclick="event.stopPropagation(); openHierarchyEntity('workgroup', ${Number(row.cells[12].data) || 0})" class="meta-link transition hover:text-violet-600 hover:underline">${Perfectlum.escapeHtml(row.cells[2].data)}</button>
                                <span class="shrink-0">•</span>
                                <button type="button" onclick="event.stopPropagation(); openHierarchyEntity('facility', ${Number(row.cells[13].data) || 0})" class="meta-link transition hover:text-amber-600 hover:underline">${Perfectlum.escapeHtml(row.cells[3].data)}</button>
                            </div>
                        </div>
                    `)
                },
                { name: calibrationText.workstation, hidden: true },
                { name: calibrationText.workgroup, hidden: true },
                { name: calibrationText.facility, hidden: true },
                {
                    name: gridjs.html(`<button type="button" data-calibration-sort="task_name"><span>${Perfectlum.escapeHtml(calibrationText.taskType)}</span><span data-calibration-sort-indicator="task_name">↕</span></button>`),
                    width: '14%',
                    formatter: (cell, row) => gridjs.html(`<div data-calibration-row-trigger="${Perfectlum.escapeHtml(row.cells[9].data)}" class="cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50"><span class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">${Perfectlum.escapeHtml(cell)}</span></div>`)
                },
                {
                    name: gridjs.html(`<button type="button" data-calibration-sort="schedule_name"><span>${Perfectlum.escapeHtml(calibrationText.scheduleType)}</span><span data-calibration-sort-indicator="schedule_name">↕</span></button>`),
                    width: '14%',
                    formatter: (cell, row) => gridjs.html(`<div data-calibration-row-trigger="${Perfectlum.escapeHtml(row.cells[9].data)}" class="cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50"><span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">${Perfectlum.escapeHtml(cell)}</span></div>`)
                },
                {
                    name: gridjs.html(`<button type="button" data-calibration-sort="due_at"><span>${Perfectlum.escapeHtml(calibrationText.dueDate)}</span><span data-calibration-sort-indicator="due_at">↕</span></button>`),
                    width: '12%',
                    formatter: (cell, row) => {
                        const value = String(cell || '-');
                        return gridjs.html(`<div data-calibration-row-trigger="${Perfectlum.escapeHtml(row.cells[9].data)}" class="cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50"><span class="text-sm font-semibold text-slate-900 whitespace-nowrap">${Perfectlum.escapeHtml(value)}</span></div>`);
                    }
                },
                {
                    name: gridjs.html(`<button type="button" data-calibration-sort="created_at"><span>${Perfectlum.escapeHtml(calibrationText.created)}</span><span data-calibration-sort-indicator="created_at">↓</span></button>`),
                    width: '12%',
                    formatter: (cell, row) => {
                        const value = String(cell || 'Not recorded');
                        const textClass = value === 'Not recorded' ? 'text-sm text-slate-400 whitespace-nowrap' : 'text-sm font-semibold text-slate-900 whitespace-nowrap';
                        return gridjs.html(`<div data-calibration-row-trigger="${Perfectlum.escapeHtml(row.cells[9].data)}" class="cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50"><span class="${textClass}">${Perfectlum.escapeHtml(value)}</span></div>`);
                    }
                },
                {
                    name: calibrationText.actions,
                    width: '92px',
                    sort: false,
                    formatter: (_, row) => gridjs.html(renderCalibrationTaskActions(row.cells[9].data))
                },
                {
                    name: 'id', // Hidden column for ID
                    hidden: true
                },
                {
                    name: 'display_id',
                    hidden: true
                },
                {
                    name: 'workstation_id',
                    hidden: true
                },
                {
                    name: 'workgroup_id',
                    hidden: true
                },
                {
                    name: 'facility_id',
                    hidden: true
                }
            ],
            server: {
                url: calibrationTasksUrl(searchInput.value || ''),
                then: data => {
                    setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
                    setTimeout(updateCalibrationSortIndicators, 0);
                    
                    if (data && data.data) {
                        calibrationTaskRows.clear();
                        data.data.forEach((item) => calibrationTaskRows.set(String(item.id), item));
                        return data.data.map(item => [
                            item.displayName,
                            item.wsName,
                            item.wgName,
                            item.facName,
                            item.taskName,
                            item.scheduleName,
                            item.dueAt,
                            item.createdAt,
                            '', // Actions
                            item.id, // Hidden task ID
                            item.displayId,
                            item.workstationId,
                            item.workgroupId,
                            item.facilityId
                        ]);
                    }
                    return [];
                },
                total: data => data.total || 0,
                handle: (res) => {
                    if (res.status === 404) return {data: []};
                    if (res.ok) return res.json();
                    throw Error(calibrationText.unableToLoadData);
                }
            },
            pagination: {
                enabled: true,
                limit: 10,
                server: {
                    url: (prev, page, limit) => {
                        const params = new URLSearchParams(prev.split('?')[1] || '');
                        const keyword = params.get('search') || (searchInput.value || '');
                        return calibrationTasksUrl(keyword, page + 1, limit);
                    }
                }
            },
            sort: false,
            search: false,
            className: {
                table: 'w-full text-sm text-left',
                thead: 'bg-gray-50/50',
                th: 'px-7 py-4 text-xs font-black uppercase tracking-wider text-gray-400 border-b border-gray-100 bg-transparent align-middle',
                td: 'px-7 py-5 border-b border-gray-50 bg-transparent align-middle',
                container: 'group',
                pagination: 'flex justify-between items-center text-xs font-medium text-gray-500'
            },
            language: {
                search: { placeholder: calibrationText.searchCalibrationTasks },
                pagination: {
                    previous: calibrationText.previous,
                    next: calibrationText.next,
                    showing: calibrationText.showing,
                    results: () => calibrationText.results,
                },
                loading: calibrationText.loading,
                noRecordsFound: calibrationText.noMatchingRecordsFound,
                error: calibrationText.unableToLoadData,
            }
        });

        // Hide default gridjs search completely since we are using custom one 
        const defaultSearch = document.querySelector('.gridjs-search');
        if(defaultSearch) defaultSearch.style.display = 'none';
        updateCalibrationSortIndicators();

        document.addEventListener('click', function (event) {
            const sortButton = event.target.closest('[data-calibration-sort]');
            if (!sortButton) return;
            event.preventDefault();

            const key = sortButton.getAttribute('data-calibration-sort');
            if (!key) return;
            if (calibrationGridState.sortKey === key) {
                calibrationGridState.sortOrder = calibrationGridState.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                calibrationGridState.sortKey = key;
                calibrationGridState.sortOrder = (key === 'due_at' || key === 'created_at') ? 'desc' : 'asc';
            }
            updateCalibrationSortIndicators();
            window.grid.updateConfig({
                server: {
                    url: calibrationTasksUrl(searchInput.value || ''),
                    then: window.grid.config.server.then,
                    handle: window.grid.config.server.handle
                }
            }).forceRender();
        });

        // Connect custom search input to Grid.js
        let searchTimeout;
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const keyword = e.target.value;
                const url = new URL(window.location);
                if (keyword) {
                    url.searchParams.set('keywords', keyword);
                } else {
                    url.searchParams.delete('keywords');
                }
                window.history.replaceState({}, '', url);

                // Re-fetch data
                window.grid.updateConfig({
                    server: {
                        url: calibrationTasksUrl(keyword),
                        then: window.grid.config.server.then,
                        handle: window.grid.config.server.handle
                    }
                }).forceRender();
            }, 500);
        });
    });
    }

    function renderCalibrationDisplayCell(item) {
        return `
            <div data-calibration-row-trigger="${Perfectlum.escapeHtml(item.id)}" class="calibration-display-cell cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50">
                <button type="button" onclick="event.stopPropagation(); openHierarchyEntity('display', ${Number(item.displayId) || 0})" class="calibration-display-title text-left transition hover:text-sky-600 hover:underline">${Perfectlum.escapeHtml(item.displayName || '-')}</button>
                <div class="calibration-display-meta">
                    <button type="button" onclick="event.stopPropagation(); openHierarchyEntity('workstation', ${Number(item.workstationId) || 0})" class="meta-link transition hover:text-emerald-600 hover:underline">${Perfectlum.escapeHtml(item.wsName || '-')}</button>
                    <span class="shrink-0">•</span>
                    <button type="button" onclick="event.stopPropagation(); openHierarchyEntity('workgroup', ${Number(item.workgroupId) || 0})" class="meta-link transition hover:text-violet-600 hover:underline">${Perfectlum.escapeHtml(item.wgName || '-')}</button>
                    <span class="shrink-0">•</span>
                    <button type="button" onclick="event.stopPropagation(); openHierarchyEntity('facility', ${Number(item.facilityId) || 0})" class="meta-link transition hover:text-amber-600 hover:underline">${Perfectlum.escapeHtml(item.facName || '-')}</button>
                </div>
            </div>
        `;
    }

    function renderCalibrationPager() {
        const totalPages = Math.max(1, Math.ceil(calibrationTableState.total / calibrationTableState.limit));
        const currentPage = Math.min(calibrationTableState.page, totalPages);
        const from = calibrationTableState.total === 0 ? 0 : ((currentPage - 1) * calibrationTableState.limit) + 1;
        const to = Math.min(calibrationTableState.total, currentPage * calibrationTableState.limit);

        const summary = document.getElementById('calibration-table-summary');
        const meta = document.getElementById('calibration-table-meta');
        const pageLabel = document.getElementById('calibration-page-label');
        const prevBtn = document.getElementById('calibration-page-prev');
        const nextBtn = document.getElementById('calibration-page-next');

        if (summary) {
            summary.textContent = `${calibrationText.showing} ${from}-${to} of ${calibrationTableState.total} ${calibrationText.results}`;
        }
        if (meta) {
            meta.textContent = `All • ${calibrationTableState.total} ${calibrationText.results}`;
        }
        if (pageLabel) {
            pageLabel.textContent = `Page ${currentPage} / ${totalPages}`;
        }
        if (prevBtn) prevBtn.disabled = calibrationTableState.loading || currentPage <= 1;
        if (nextBtn) nextBtn.disabled = calibrationTableState.loading || currentPage >= totalPages;
    }

    function renderCalibrationRows() {
        const body = document.getElementById('calibration-tasks-body');
        if (!body) return;

        if (calibrationTableState.loading) {
            body.innerHTML = `<tr><td colspan="6" class="calibration-empty">${Perfectlum.escapeHtml(calibrationText.loading)}</td></tr>`;
            return;
        }

        const items = Array.from(calibrationTaskRows.values());
        if (!items.length) {
            body.innerHTML = `<tr><td colspan="6" class="calibration-empty">${Perfectlum.escapeHtml(calibrationText.noMatchingRecordsFound)}</td></tr>`;
            return;
        }

        body.innerHTML = items.map((item) => {
            const createdValue = String(item.createdAt || calibrationText.notRecorded);
            const createdClass = createdValue === calibrationText.notRecorded
                ? 'text-sm text-slate-400 whitespace-nowrap'
                : 'text-sm font-semibold text-slate-900 whitespace-nowrap';

            return `
                <tr>
                    <td>${renderCalibrationDisplayCell(item)}</td>
                    <td><div data-calibration-row-trigger="${Perfectlum.escapeHtml(item.id)}" class="cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50"><span class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">${Perfectlum.escapeHtml(item.taskName || '-')}</span></div></td>
                    <td><div data-calibration-row-trigger="${Perfectlum.escapeHtml(item.id)}" class="cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50"><span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">${Perfectlum.escapeHtml(item.scheduleName || '-')}</span></div></td>
                    <td><div data-calibration-row-trigger="${Perfectlum.escapeHtml(item.id)}" class="cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50">${renderCalibrationDateTimeCell(item.dueAt || '-', 'text-sm font-semibold text-slate-900 whitespace-nowrap')}</div></td>
                    <td><div data-calibration-row-trigger="${Perfectlum.escapeHtml(item.id)}" class="cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50">${renderCalibrationDateTimeCell(createdValue, createdClass)}</div></td>
                    <td>${renderCalibrationTaskActions(item.id)}</td>
                </tr>
            `;
        }).join('');

        window.lucide?.createIcons();
    }

    function renderCalibrationDateTimeCell(value, primaryClass, secondaryClass = 'text-[11px] font-medium text-slate-400 whitespace-nowrap') {
        const raw = String(value || '').trim();
        if (!raw || raw === '-') {
            return `<span class="${primaryClass}">-</span>`;
        }

        const match = raw.match(/^(.*?)(?:\s+(\d{1,2}:\d{2}(?::\d{2})?))$/);
        if (!match) {
            return `<span class="${primaryClass}">${Perfectlum.escapeHtml(raw)}</span>`;
        }

        const datePart = (match[1] || '').trim();
        const timePart = (match[2] || '').trim();

        return `
            <span class="flex flex-col items-center text-center leading-tight">
                <span class="${primaryClass}">${Perfectlum.escapeHtml(datePart || raw)}</span>
                <span class="${secondaryClass}">${Perfectlum.escapeHtml(timePart)}</span>
            </span>
        `;
    }

    function renderCalibrationMetaButton(type, id, label, badge) {
        const safeLabel = String(label || '').trim();
        const numericId = Number(id) || 0;
        if (!numericId || !safeLabel || safeLabel === '-') {
            return '';
        }

        return `
            <button type="button" onclick="event.stopPropagation(); openHierarchyEntity('${Perfectlum.escapeHtml(type)}', ${numericId})" class="calibration-display-meta-button">
                <span class="calibration-display-meta-badge">${Perfectlum.escapeHtml(badge)}</span>
                <span class="calibration-display-meta-label">${Perfectlum.escapeHtml(safeLabel)}</span>
            </button>
        `;
    }

    function renderCalibrationDisplayCell(item) {
        const workstation = renderCalibrationMetaButton('workstation', item.workstationId, item.wsName, 'WS');
        const workgroup = renderCalibrationMetaButton('workgroup', item.workgroupId, item.wgName, 'WG');
        const facility = renderCalibrationMetaButton('facility', item.facilityId, item.facName, 'F');
        const meta = [workstation, workgroup, facility]
            .filter(Boolean)
            .join('<span class="calibration-display-separator">•</span>');

        return `
            <div data-calibration-row-trigger="${Perfectlum.escapeHtml(item.id)}" class="calibration-display-cell cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50">
                <button type="button" onclick="event.stopPropagation(); openHierarchyEntity('display', ${Number(item.displayId) || 0})" class="calibration-display-title text-left transition hover:text-sky-600 hover:underline">${Perfectlum.escapeHtml(item.displayName || '-')}</button>
                <div class="calibration-display-meta">${meta}</div>
            </div>
        `;
    }

    function renderCalibrationRows() {
        const body = document.getElementById('calibration-tasks-body');
        if (!body) return;

        if (calibrationTableState.loading) {
            body.innerHTML = `<tr><td colspan="6" class="calibration-empty">${Perfectlum.escapeHtml(calibrationText.loading)}</td></tr>`;
            return;
        }

        const items = Array.from(calibrationTaskRows.values());
        if (!items.length) {
            body.innerHTML = `<tr><td colspan="6" class="calibration-empty">${Perfectlum.escapeHtml(calibrationText.noMatchingRecordsFound)}</td></tr>`;
            return;
        }

        body.innerHTML = items.map((item) => {
            const createdValue = String(item.createdAt || calibrationText.notRecorded);
            const dueOverdue = item.dueColor === 'danger';
            const createdClass = (createdValue === calibrationText.notRecorded || createdValue === 'Not recorded')
                ? 'text-sm text-slate-400 whitespace-nowrap'
                : 'text-sm font-semibold text-slate-900 whitespace-nowrap';
            const dueClass = dueOverdue
                ? 'text-sm font-semibold text-rose-600 whitespace-nowrap'
                : 'text-sm font-semibold text-slate-900 whitespace-nowrap';

            return `
                <tr>
                    <td>${renderCalibrationDisplayCell(item)}</td>
                    <td><div data-calibration-row-trigger="${Perfectlum.escapeHtml(item.id)}" class="calibration-cell-center cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50"><span class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">${Perfectlum.escapeHtml(item.taskName || '-')}</span></div></td>
                    <td><div data-calibration-row-trigger="${Perfectlum.escapeHtml(item.id)}" class="calibration-cell-center cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50"><span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">${Perfectlum.escapeHtml(item.scheduleName || '-')}</span></div></td>
                    <td><div data-calibration-row-trigger="${Perfectlum.escapeHtml(item.id)}" class="calibration-cell-center cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50">${renderCalibrationDateTimeCell(item.dueAt || '-', dueClass, dueOverdue ? 'text-[11px] font-semibold text-rose-300 whitespace-nowrap' : undefined)}</div></td>
                    <td><div data-calibration-row-trigger="${Perfectlum.escapeHtml(item.id)}" class="calibration-cell-center cursor-pointer rounded-2xl px-1 py-1 transition hover:bg-slate-50">${renderCalibrationDateTimeCell(createdValue, createdClass)}</div></td>
                    <td>${renderCalibrationTaskActions(item.id)}</td>
                </tr>
            `;
        }).join('');

        window.lucide?.createIcons();
    }

    async function loadCalibrationTasks() {
        calibrationTableState.loading = true;
        renderCalibrationPager();
        renderCalibrationRows();
        let hasError = false;

        try {
            const payload = await Perfectlum.request(
                calibrationTasksUrl(calibrationTableState.search, calibrationTableState.page, calibrationTableState.limit)
            );
            const rows = Array.isArray(payload?.data) ? payload.data : [];
            calibrationTaskRows.clear();
            rows.forEach((item) => calibrationTaskRows.set(String(item.id), item));
            calibrationTableState.total = Number(payload?.total || rows.length || 0);

            const totalPages = Math.max(1, Math.ceil(calibrationTableState.total / calibrationTableState.limit));
            if (calibrationTableState.page > totalPages) {
                calibrationTableState.page = totalPages;
                return loadCalibrationTasks();
            }
        } catch (error) {
            hasError = true;
            calibrationTaskRows.clear();
            calibrationTableState.total = 0;
            const body = document.getElementById('calibration-tasks-body');
            if (body) {
                body.innerHTML = `<tr><td colspan="6" class="calibration-empty text-rose-600">${Perfectlum.escapeHtml(error.message || calibrationText.unableToLoadData)}</td></tr>`;
            }
        } finally {
            calibrationTableState.loading = false;
            if (!hasError) {
                renderCalibrationRows();
            }
            renderCalibrationPager();
        }
    }

    function updateCalibrationSearchClearButton() {
        const searchInput = document.getElementById('gridjs-custom-search');
        const clearButton = document.getElementById('calibration-table-search-clear');
        if (!searchInput || !clearButton) return;
        clearButton.hidden = !String(searchInput.value || '').trim();
    }

    function initCalibrationPage() {
        const body = document.getElementById('calibration-tasks-body');
        if (!body || body.dataset.calibrationInitialized === '1') {
            return;
        }
        body.dataset.calibrationInitialized = '1';

        const calibrationJobModal = document.getElementById('calibration-job-modal');
        if (calibrationJobModal && calibrationJobModal.dataset.dismissBound !== '1') {
            calibrationJobModal.dataset.dismissBound = '1';
            calibrationJobModal.querySelectorAll('[data-calibration-job-dismiss="1"]').forEach((node) => {
                node.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    closeCalibrationJobModal();
                });
            });
        }

        initCalibrateSearchableDropdowns();
        const quickCalibrationForm = document.getElementById('display-calibration-quick-form');
        const searchInput = document.getElementById('gridjs-custom-search');
        const customSearchParams = new URLSearchParams(window.location.search);
        const initialKeyword = customSearchParams.get('keywords') || '';
        if (searchInput) {
            searchInput.value = initialKeyword;
        }
        calibrationTableState.search = initialKeyword;
        updateCalibrationSearchClearButton();

        quickCalibrationForm?.addEventListener('submit', function (event) {
            event.preventDefault();

            if (typeof window.openTaskEditorWithPayload !== 'function') {
                return;
            }

            const facilityId = document.getElementById('calibrate-facility-native')?.value || '';
            const workgroupId = document.getElementById('workgroups_field')?.value || '';
            const workstationId = document.getElementById('workstations_field')?.value || '';
            const checkedDisplays = Array.from(document.querySelectorAll('#calibrate-displays-options input[name="displays[]"]:checked:not([data-select-all="1"])'))
                .map((input) => String(input.value || '').trim())
                .filter(Boolean);

            if (!facilityId) {
                window.notify?.('failed', 'Please select a facility first.');
                return;
            }

            window.openTaskEditorWithPayload({
                id: 0,
                tasktype: 'cal',
                quick_calibration: '1',
                lock_tasktype: '1',
                facility2: facilityId,
                workgroup2: workgroupId,
                workstation2: workstationId,
                displays: checkedDisplays
            }, {
                title: 'Calibrate Display',
                subtitle: checkedDisplays.length
                    ? `Set the schedule window for ${checkedDisplays.length} selected display${checkedDisplays.length > 1 ? 's' : ''}.`
                    : 'Set the schedule window for the current hierarchy scope before creating the calibration task.',
            });
        });

        document.querySelectorAll('[data-calibration-sort]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const key = button.getAttribute('data-calibration-sort');
                if (!key) return;
                if (calibrationGridState.sortKey === key) {
                    calibrationGridState.sortOrder = calibrationGridState.sortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    calibrationGridState.sortKey = key;
                    calibrationGridState.sortOrder = (key === 'due_at' || key === 'created_at') ? 'desc' : 'asc';
                }
                calibrationTableState.page = 1;
                updateCalibrationSortIndicators();
                loadCalibrationTasks();
            });
        });

        let searchTimeout;
        searchInput?.addEventListener('input', function (e) {
            updateCalibrationSearchClearButton();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const keyword = String(e.target.value || '').trim();
                const url = new URL(window.location);
                if (keyword) {
                    url.searchParams.set('keywords', keyword);
                } else {
                    url.searchParams.delete('keywords');
                }
                window.history.replaceState({}, '', url);
                calibrationTableState.search = keyword;
                calibrationTableState.page = 1;
                loadCalibrationTasks();
            }, 400);
        });

        document.getElementById('calibration-table-search-clear')?.addEventListener('click', () => {
            if (!searchInput) return;
            clearTimeout(searchTimeout);
            searchInput.value = '';
            updateCalibrationSearchClearButton();
            const url = new URL(window.location);
            url.searchParams.delete('keywords');
            window.history.replaceState({}, '', url);
            calibrationTableState.search = '';
            calibrationTableState.page = 1;
            loadCalibrationTasks();
            searchInput.focus();
        });

        document.getElementById('calibration-page-prev')?.addEventListener('click', () => {
            if (calibrationTableState.page <= 1 || calibrationTableState.loading) return;
            calibrationTableState.page -= 1;
            loadCalibrationTasks();
        });

        document.getElementById('calibration-page-next')?.addEventListener('click', () => {
            const totalPages = Math.max(1, Math.ceil(calibrationTableState.total / calibrationTableState.limit));
            if (calibrationTableState.page >= totalPages || calibrationTableState.loading) return;
            calibrationTableState.page += 1;
            loadCalibrationTasks();
        });

        updateCalibrationSortIndicators();
        loadCalibrationTasks();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCalibrationPage, { once: true });
    } else {
        initCalibrationPage();
    }

    window.calibrationPageCleanup = function () {
        closeCalibrationTaskMenu();
        closeCalibrationJobModal();
    };
    window.calibrationPageMount = initCalibrationPage;

    // Delete task function logic
    function confirmDelete(id) {
        var formData = new FormData();
        formData.append('_token', '{{csrf_token()}}');
        formData.append('id', id);

        fetch("{{ url('delete-task') }}", {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData
        })
            .then((res) => res.json())
            .then((data) => {
                if (!data.success) {
                    notify('failed', data.msg || calibrationText.deletionFailed);
                } else {
                    loadCalibrationTasks();
                    notify('success', data.msg || calibrationText.taskDeletedSuccessfully);
                }
            });
    }

    document.addEventListener('click', (event) => {
        const rowTrigger = event.target.closest('[data-calibration-row-trigger]');
        if (rowTrigger && !event.target.closest('[data-calibration-task-toggle]') && !event.target.closest('[data-calibration-task-menu]')) {
            event.preventDefault();
            openCalibrationJobModal(rowTrigger.dataset.calibrationRowTrigger);
            return;
        }

        const toggle = event.target.closest('[data-calibration-task-toggle]');
        if (toggle) {
            event.preventDefault();
            event.stopPropagation();
            const id = toggle.dataset.calibrationTaskToggle;
            const menu = document.querySelector(`[data-calibration-task-menu="${id}"]`);
            const willOpen = menu?.classList.contains('hidden');
            closeCalibrationTaskMenus();
            if (menu && willOpen) {
                positionCalibrationTaskMenu(toggle, menu);
                menu.classList.remove('hidden');
            }
            return;
        }

        const editButton = event.target.closest('[data-calibration-task-edit]');
        if (editButton) {
            event.preventDefault();
            event.stopPropagation();
            closeCalibrationTaskMenus();
            window.edit_task(null, editButton.dataset.calibrationTaskEdit);
            return;
        }

        const deleteButton = event.target.closest('[data-calibration-task-delete]');
        if (deleteButton) {
            event.preventDefault();
            event.stopPropagation();
            closeCalibrationTaskMenus();
            window.dispatchEvent(new CustomEvent('delete-task', { detail: { id: deleteButton.dataset.calibrationTaskDelete } }));
            return;
        }

        if (!event.target.closest('[data-calibration-task-menu]')) {
            closeCalibrationTaskMenus();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeCalibrationTaskMenus();
            closeCalibrationJobModal();
        }
    });

    window.addEventListener('resize', closeCalibrationTaskMenus);
    window.addEventListener('scroll', closeCalibrationTaskMenus, true);

    window.addEventListener('task-saved', () => {
        resetCalibrationQuickForm();
        loadCalibrationTasks();
    });

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-calibration-job-dismiss="1"]')) {
            event.preventDefault();
            closeCalibrationJobModal();
        }
    });

    window.fetch_workgroups = fetch_workgroups;
    window.fetch_workstations = fetch_workstations;
    window.fetch_displays_checklist = fetch_displays_checklist;
    window.openHierarchyEntity = openHierarchyEntity;
    window.confirmDelete = confirmDelete;
    })();
</script>

@include('tasks.schedule_task_modal')
@include('common.navigations.footer')
