@include('common.navigations.header')

@php
    $role = session('role');
    $canManageWorkgroups = in_array($role, ['super', 'admin'], true);
    $initialWorkgroupStatus = in_array(request('type'), ['ok', 'failed'], true) ? request('type') : '';
    $workgroupText = [
        'display' => __('display'),
        'displays' => __('displays'),
        'needAttention' => __('need attention'),
        'name' => __('Name'),
        'address' => __('Address'),
        'phone' => __('Phone'),
        'facility' => __('Facility'),
        'workstations' => __('Workstations'),
        'displaysLabel' => __('Displays'),
        'actions' => __('Actions'),
        'allFacilities' => __('All facilities'),
        'selectFacility' => __('Select facility'),
        'searchFacilities' => __('Search facilities...'),
        'noOptionsFound' => __('No options found'),
        'option' => __('option'),
        'options' => __('options'),
        'searchWorkgroups' => __('Search workgroups...'),
        'previous' => __('Previous'),
        'next' => __('Next'),
        'showing' => __('Showing'),
        'of' => __('of'),
        'page' => __('Page'),
        'results' => __('results'),
        'loading' => __('Loading...'),
        'noMatchingRecordsFound' => __('No matching records found'),
        'unableToLoadData' => __('Unable to load data'),
        'addWorkgroup' => __('Add Workgroup'),
        'editWorkgroup' => __('Edit Workgroup'),
        'createWorkgroup' => __('Create a new workgroup'),
        'updateWorkgroupDetails' => __('Update workgroup details'),
        'unableToLoadWorkgroupForm' => __('Unable to load workgroup form.'),
        'unableToSaveWorkgroup' => __('Unable to save workgroup.'),
        'deleteWorkgroup' => __('Delete Workgroup'),
        'deleting' => __('Deleting...'),
        'unableToDeleteWorkgroup' => __('Unable to delete workgroup.'),
        'displaysNeedAttentionTitle' => __('Displays needing attention'),
        'loadingAttentionDisplays' => __('Loading displays needing attention...'),
        'noDisplaysNeedAttention' => __('No displays currently need attention in this workgroup.'),
        'unableToLoadAttentionDisplays' => __('Unable to load displays needing attention.'),
        'openDisplay' => __('Open Display'),
        'loadingMore' => __('Loading more...'),
    ];
@endphp

<style>
    .workgroup-table-shell {
        border-radius: 2rem;
        border: 1px solid #d5e0ec;
        background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        box-shadow: 0 26px 64px -46px rgba(15, 23, 42, 0.34);
        overflow: hidden;
    }
    .workgroup-table-controlbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 1px solid #e3ecf5;
        background: linear-gradient(180deg, #f4f9ff 0%, #ffffff 100%);
    }
    .workgroup-controls-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .workgroup-facility-picker {
        position: relative;
        min-width: 240px;
    }
    .workgroup-facility-trigger {
        width: 100%;
        height: 36px;
        border-radius: 999px;
        border: 1px solid #d3dfec;
        background: #ffffff;
        color: #4b6078;
        font-size: 12px;
        font-weight: 700;
        padding: 0 12px;
    }
    .workgroup-facility-panel {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        z-index: 40;
        border-radius: 16px;
        border: 1px solid #d3dfec;
        background: #ffffff;
        box-shadow: 0 24px 50px -26px rgba(15, 23, 42, 0.32);
        padding: 10px;
    }
    .workgroup-facility-search {
        width: 100%;
        height: 34px;
        border-radius: 10px;
        border: 1px solid #d7e3ef;
        padding: 0 10px;
        font-size: 12px;
        color: #334155;
    }
    .workgroup-facility-search:focus {
        outline: none;
        border-color: #1d9bf0;
        box-shadow: 0 0 0 3px rgba(29, 155, 240, 0.14);
    }
    .workgroup-status-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px;
        border-radius: 999px;
        border: 1px solid #d3dfec;
        background: #f3f8fe;
    }
    .workgroup-status-pill {
        height: 36px;
        border-radius: 999px;
        padding: 0 14px;
        font-size: 12px;
        font-weight: 700;
        color: #4c6077;
        transition: all .18s ease;
    }
    .workgroup-status-pill:hover {
        color: #243b53;
        background: #eff6fd;
    }
    .workgroup-reset-btn {
        height: 36px;
        border-radius: 999px;
        border: 1px solid #d3dfec;
        background: #fff;
        color: #4b6078;
        font-size: 12px;
        font-weight: 700;
        padding: 0 14px;
        transition: all .18s ease;
    }
    .workgroup-reset-btn:hover {
        border-color: #9ec6ea;
        color: #1f4f80;
        background: #f3f9ff;
    }
    .workgroup-table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 18px;
        border-bottom: 1px solid #e3ecf5;
        background: #f8fbff;
    }
    .workgroup-table-search {
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
    .workgroup-table-search:focus {
        outline: none;
        border-color: #1d9bf0;
        box-shadow: 0 0 0 3px rgba(29, 155, 240, 0.16);
    }
    .workgroup-table-wrap {
        overflow-x: auto;
        background: #fff;
    }
    .workgroup-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1040px;
        table-layout: fixed;
    }
    .workgroup-table th {
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
    .workgroup-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #edf2f8;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
        background: #fff;
    }
    .workgroup-table td:nth-child(2),
    .workgroup-table td:nth-child(3),
    .workgroup-table td:nth-child(4) {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .workgroup-facility-cell {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        max-width: 100%;
    }
    .workgroup-facility-badge {
        width: 18px;
        height: 18px;
        border-radius: 999px;
        background: #1d9bf0;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0;
        line-height: 1;
        flex-shrink: 0;
    }
    .workgroup-facility-link {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .workgroup-table tbody tr:hover td {
        background: #f7fbff;
    }
    .workgroup-row-clickable {
        cursor: pointer;
    }
    .workgroup-table th:nth-child(5),
    .workgroup-table th:nth-child(6),
    .workgroup-table th:nth-child(7),
    .workgroup-table td:nth-child(5),
    .workgroup-table td:nth-child(6),
    .workgroup-table td:nth-child(7) {
        text-align: center;
    }
    .workgroup-table th:nth-child(4),
    .workgroup-table td:nth-child(4) {
        padding-right: 20px;
    }
    .workgroup-table th:nth-child(5),
    .workgroup-table td:nth-child(5) {
        padding-left: 20px;
        padding-right: 20px;
    }
    .workgroup-table th:nth-child(6),
    .workgroup-table td:nth-child(6) {
        padding-left: 20px;
    }
    .workgroup-sort-btn {
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
    .workgroup-sort-btn:hover {
        background: #f2f7fd;
        color: #2f4d6a;
    }
    .workgroup-sort-btn.is-active {
        background: #e2edf9;
        color: #24486b;
    }
    .workgroup-sort-indicator {
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
    .workgroup-sort-btn.is-active .workgroup-sort-indicator {
        background: #2f6fae;
        color: #ffffff;
    }
    .workgroup-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 16px 14px;
        border-top: 1px solid #dbe7f3;
        background: #f7fbff;
    }
    .workgroup-pager {
        display: inline-flex;
        gap: 8px;
        align-items: center;
    }
    .workgroup-page-btn {
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
    .workgroup-page-btn:hover:not(:disabled) {
        border-color: #1d9bf0;
        color: #0f5f9f;
        background: #f0f8ff;
    }
    .workgroup-page-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }
    .workgroup-row-action {
        width: 36px;
        height: 36px;
        border-radius: 9999px;
        border: 1px solid #d6dee8;
        background: #fff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .workgroup-empty {
        padding: 24px 16px;
        text-align: center;
        color: #5f7388;
        font-size: 14px;
        border-bottom: 1px solid #edf2f8;
    }
    @media (max-width: 1120px) {
        .workgroup-table-controlbar {
            flex-wrap: wrap;
            align-items: stretch;
        }
        .workgroup-controls-left {
            flex-wrap: wrap;
            width: 100%;
        }
        .workgroup-facility-picker {
            min-width: 100%;
        }
        .workgroup-status-group {
            width: 100%;
            justify-content: space-between;
        }
        .workgroup-status-pill {
            flex: 1 1 0;
            padding: 0 10px;
        }
        .workgroup-reset-btn {
            width: 100%;
            justify-content: center;
        }
        .workgroup-table-toolbar {
            flex-wrap: wrap;
        }
        .workgroup-table-search {
            width: 100%;
        }
        .workgroup-table-footer {
            flex-wrap: wrap;
        }
    }
</style>

<div class="flex flex-col gap-6 pb-8">
    <x-page-header title="{{ __('All Workgroups') }}" description="{{ __('Manage departmental workgroups spanning across all facilities.') }}" icon="network">
        <x-slot name="actions">
            @if($canManageWorkgroups)
                <button
                    id="create-workgroup-button"
                    type="button"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    {{ __('Add Workgroup') }}
                </button>
            @endif
            <x-export-dropdown
                excel-url="{{ url('reports/workgroups?export_type=excel') }}"
                pdf-url="{{ url('reports/workgroups?export_type=pdf') }}" />
        </x-slot>
    </x-page-header>

    <section class="workgroup-table-shell mb-10">
        <div class="workgroup-table-controlbar">
            <div class="workgroup-controls-left">
                <div class="workgroup-facility-picker">
                    <button
                        id="facility-filter-trigger"
                        type="button"
                        class="workgroup-facility-trigger flex items-center justify-between gap-2 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                        <span id="facility-filter-label" class="truncate">{{ __('All facilities') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div id="facility-filter-panel" class="workgroup-facility-panel hidden">
                        <input
                            id="facility-filter-search"
                            type="text"
                            placeholder="{{ __('Search facilities...') }}"
                            class="workgroup-facility-search mb-2">
                        <p id="facility-filter-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="facility-filter-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>

                <div class="workgroup-status-group">
                    <button id="workgroup-status-all" type="button" data-status="" class="workgroup-status-pill">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="layers-3" class="h-4 w-4"></i>
                            <span>{{ __('All') }}</span>
                        </span>
                    </button>
                    <button id="workgroup-status-ok" type="button" data-status="ok" class="workgroup-status-pill">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="badge-check" class="h-4 w-4"></i>
                            <span>{{ __('OK') }}</span>
                        </span>
                    </button>
                    <button id="workgroup-status-failed" type="button" data-status="failed" class="workgroup-status-pill">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="triangle-alert" class="h-4 w-4"></i>
                            <span>{{ __('Not OK') }}</span>
                        </span>
                    </button>
                </div>
            </div>

            <button id="reset-workgroup-filters" type="button" class="workgroup-reset-btn inline-flex items-center gap-2">
                <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                {{ __('Reset Filters') }}
            </button>
        </div>

        <div class="workgroup-table-toolbar">
            <input id="workgroup-table-search" type="text" class="workgroup-table-search" placeholder="{{ __('Search workgroups...') }}">
            <div class="text-[12px] font-semibold text-slate-500" id="workgroup-table-meta"></div>
        </div>

        <div class="workgroup-table-wrap">
            <table class="workgroup-table">
                <colgroup>
                    <col style="width: 25%">
                    <col style="width: 14%">
                    <col style="width: 12%">
                    <col style="width: 20%">
                    <col style="width: 12%">
                    <col style="width: 10%">
                    <col style="width: 7%">
                </colgroup>
                <thead>
                    <tr>
                        <th><button type="button" class="workgroup-sort-btn" data-workgroup-sort="name"><span>{{ __('Name') }}</span><span class="workgroup-sort-indicator" data-workgroup-sort-indicator="name">↕</span></button></th>
                        <th><button type="button" class="workgroup-sort-btn" data-workgroup-sort="address"><span>{{ __('Address') }}</span><span class="workgroup-sort-indicator" data-workgroup-sort-indicator="address">↕</span></button></th>
                        <th><button type="button" class="workgroup-sort-btn" data-workgroup-sort="phone"><span>{{ __('Phone') }}</span><span class="workgroup-sort-indicator" data-workgroup-sort-indicator="phone">↕</span></button></th>
                        <th><button type="button" class="workgroup-sort-btn" data-workgroup-sort="facName"><span>{{ __('Facility') }}</span><span class="workgroup-sort-indicator" data-workgroup-sort-indicator="facName">↕</span></button></th>
                        <th><button type="button" class="workgroup-sort-btn" data-workgroup-sort="workstationsCount"><span>{{ __('Workstations') }}</span><span class="workgroup-sort-indicator" data-workgroup-sort-indicator="workstationsCount">↕</span></button></th>
                        <th><button type="button" class="workgroup-sort-btn" data-workgroup-sort="displaysCount"><span>{{ __('Displays') }}</span><span class="workgroup-sort-indicator" data-workgroup-sort-indicator="displaysCount">↕</span></button></th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="workgroups-table-body"></tbody>
            </table>
        </div>

        <div class="workgroup-table-footer">
            <div class="text-[12px] font-semibold text-slate-500" id="workgroup-table-summary"></div>
            <div class="workgroup-pager">
                <button type="button" class="workgroup-page-btn" id="workgroup-page-prev">{{ __('Previous') }}</button>
                <span class="text-[12px] font-semibold text-slate-600" id="workgroup-page-label">1</span>
                <button type="button" class="workgroup-page-btn" id="workgroup-page-next">{{ __('Next') }}</button>
            </div>
        </div>
    </section>
</div>

<div id="workgroup-action-overlay" class="pointer-events-none fixed inset-0 z-[1200] hidden">
    <div
        id="workgroup-action-menu"
        class="pointer-events-auto fixed hidden w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.35)]">
        @if($canManageWorkgroups)
            <button
                id="workgroup-action-edit"
                type="button"
                class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                <i data-lucide="pencil-line" class="h-4 w-4"></i>
                {{ __('Edit Workgroup') }}
            </button>
            <button
                id="workgroup-action-delete"
                type="button"
                class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
                {{ __('Delete Workgroup') }}
            </button>
        @endif
    </div>
</div>

<div id="workgroup-edit-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="w-full max-w-2xl rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p id="workgroup-edit-kicker" class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Edit Workgroup') }}</p>
                <h3 id="workgroup-edit-title" class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Update workgroup details') }}</h3>
            </div>
            <button id="workgroup-edit-close" type="button" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <div class="px-6 py-6">
            <div id="workgroup-edit-loading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                {{ __('Loading workgroup form...') }}
            </div>
            <div id="workgroup-edit-error" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700"></div>
            <div id="workgroup-edit-form"></div>
        </div>
    </div>
</div>

<div id="workgroup-delete-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="w-full max-w-lg rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="border-b border-slate-200 px-6 py-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-400">{{ __('Delete Workgroup') }}</p>
            <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Delete this workgroup?') }}</h3>
            <p class="mt-3 text-sm text-slate-500">
                {{ __('This action will permanently remove') }} <span id="workgroup-delete-name" class="font-semibold text-slate-700"></span>.
                {{ __('Workgroups that still have workstations attached cannot be deleted.') }}
            </p>
        </div>

        <div class="flex justify-end gap-3 px-6 py-5">
            <button
                id="workgroup-delete-cancel"
                type="button"
                class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                {{ __('Cancel') }}
            </button>
            <button
                id="workgroup-delete-confirm"
                type="button"
                class="inline-flex h-11 items-center rounded-2xl bg-rose-500 px-4 text-sm font-semibold text-white transition hover:bg-rose-400 disabled:cursor-not-allowed disabled:opacity-60">
                {{ __('Delete Workgroup') }}
            </button>
        </div>
    </div>
</div>

<div id="workgroup-attention-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="flex max-h-[88vh] w-full max-w-5xl flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-400">{{ __('Needs Attention') }}</p>
                <h3 id="workgroup-attention-title" class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Displays needing attention') }}</h3>
                <p id="workgroup-attention-subtitle" class="mt-2 text-sm text-slate-500"></p>
            </div>
            <button id="workgroup-attention-close" type="button" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden px-6 py-5">
            <div id="workgroup-attention-loading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                {{ __('Loading displays needing attention...') }}
            </div>
            <div id="workgroup-attention-error" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700"></div>
            <div id="workgroup-attention-empty" class="hidden rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-700">{{ __('No displays currently need attention in this workgroup.') }}</div>

            <div id="workgroup-attention-list-wrap" class="hidden h-[56vh] overflow-y-auto rounded-2xl border border-slate-200 overscroll-contain">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="sticky top-0 z-10 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <button type="button" data-attention-sort="displayName" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Display') }}</span><span data-attention-sort-indicator="displayName">↕</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <button type="button" data-attention-sort="wsName" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Workstation') }}</span><span data-attention-sort-indicator="wsName">↕</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <button type="button" data-attention-sort="updatedAt" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Last Sync') }}</span><span data-attention-sort-indicator="updatedAt">↓</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <button type="button" data-attention-sort="attentionText" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Detail') }}</span><span data-attention-sort-indicator="attentionText">↕</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="workgroup-attention-list" class="divide-y divide-slate-100 bg-white"></tbody>
                </table>
                <div id="workgroup-attention-more" class="hidden border-t border-slate-200 bg-slate-50 px-4 py-2 text-center text-[12px] font-medium text-slate-500">{{ __('Loading more...') }}</div>
            </div>
        </div>
    </div>
</div>

<script id="workgroup-filters-data" type="application/json">@json($filters)</script>
<script>
(function () {
    const text = @json($workgroupText);
    const canManageWorkgroups = @json($canManageWorkgroups);
    let initialized = false;
    const state = {
        config: { canChooseFacility: false, facilities: [], selectedFacilityId: '' },
        selectedFacilityId: '',
        defaultStatus: @json($initialWorkgroupStatus),
        selectedStatus: @json($initialWorkgroupStatus),
        facilitySearch: '',
        activeDropdown: null,
        actionTarget: null,
        deleteTarget: null,
        workgroupRows: [],
        workgroupPage: 1,
        workgroupLimit: 10,
        workgroupTotal: 0,
        workgroupLoading: false,
        workgroupSearch: '',
        workgroupSearchTimer: null,
        workgroupSortKey: 'name',
        workgroupSortDir: 'asc',
        attentionTarget: null,
        attentionPage: 1,
        attentionHasMore: false,
        attentionLoadingMore: false,
        attentionLimit: 9,
        attentionRows: [],
        attentionSortKey: 'updatedAt',
        attentionSortDir: 'desc',
    };

    const els = {};

    function init() {
        if (initialized) return;
        if (!window.Perfectlum) {
            window.setTimeout(init, 50);
            return;
        }

        initialized = true;

        try {
            state.config = JSON.parse(document.getElementById('workgroup-filters-data')?.textContent || '{}');
        } catch (error) {
            state.config = { canChooseFacility: false, facilities: [], selectedFacilityId: '' };
        }

        state.selectedFacilityId = state.config.selectedFacilityId || '';

        bindElements();
        bindEvents();
        renderFilters();
        updateWorkgroupSortIndicators();
        updateAttentionSortIndicators();
        loadWorkgroups();
        window.workgroupsPage = { toggleActionMenu, openEditModal, openDeleteModal };
        window.lucide?.createIcons();
    }

    function bindElements() {
        els.createButton = document.getElementById('create-workgroup-button');
        els.facilityTrigger = document.getElementById('facility-filter-trigger');
        els.facilityLabel = document.getElementById('facility-filter-label');
        els.facilityPanel = document.getElementById('facility-filter-panel');
        els.facilitySearch = document.getElementById('facility-filter-search');
        els.facilityHint = document.getElementById('facility-filter-hint');
        els.facilityOptions = document.getElementById('facility-filter-options');
        els.statusButtons = Array.from(document.querySelectorAll('[data-status]'));
        els.resetFilters = document.getElementById('reset-workgroup-filters');
        els.tableSearch = document.getElementById('workgroup-table-search');
        els.tableMeta = document.getElementById('workgroup-table-meta');
        els.tableBody = document.getElementById('workgroups-table-body');
        els.tableSummary = document.getElementById('workgroup-table-summary');
        els.pagePrev = document.getElementById('workgroup-page-prev');
        els.pageNext = document.getElementById('workgroup-page-next');
        els.pageLabel = document.getElementById('workgroup-page-label');
        els.sortButtons = Array.from(document.querySelectorAll('[data-workgroup-sort]'));

        els.actionOverlay = document.getElementById('workgroup-action-overlay');
        els.actionMenu = document.getElementById('workgroup-action-menu');
        els.actionEdit = document.getElementById('workgroup-action-edit');
        els.actionDelete = document.getElementById('workgroup-action-delete');

        els.editModal = document.getElementById('workgroup-edit-modal');
        els.editClose = document.getElementById('workgroup-edit-close');
        els.editLoading = document.getElementById('workgroup-edit-loading');
        els.editError = document.getElementById('workgroup-edit-error');
        els.editForm = document.getElementById('workgroup-edit-form');
        els.editKicker = document.getElementById('workgroup-edit-kicker');
        els.editTitle = document.getElementById('workgroup-edit-title');

        els.deleteModal = document.getElementById('workgroup-delete-modal');
        els.deleteName = document.getElementById('workgroup-delete-name');
        els.deleteCancel = document.getElementById('workgroup-delete-cancel');
        els.deleteConfirm = document.getElementById('workgroup-delete-confirm');

        els.attentionModal = document.getElementById('workgroup-attention-modal');
        els.attentionClose = document.getElementById('workgroup-attention-close');
        els.attentionTitle = document.getElementById('workgroup-attention-title');
        els.attentionSubtitle = document.getElementById('workgroup-attention-subtitle');
        els.attentionLoading = document.getElementById('workgroup-attention-loading');
        els.attentionError = document.getElementById('workgroup-attention-error');
        els.attentionEmpty = document.getElementById('workgroup-attention-empty');
        els.attentionListWrap = document.getElementById('workgroup-attention-list-wrap');
        els.attentionList = document.getElementById('workgroup-attention-list');
        els.attentionMore = document.getElementById('workgroup-attention-more');
        els.attentionSortButtons = Array.from(document.querySelectorAll('[data-attention-sort]'));
    }

    function bindEvents() {
        els.createButton?.addEventListener('click', () => openEditModal(0));
        els.facilityTrigger?.addEventListener('click', () => toggleDropdown('facility'));
        els.facilitySearch?.addEventListener('input', (event) => {
            state.facilitySearch = event.target.value || '';
            renderFacilityOptions();
        });
        els.statusButtons.forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedStatus = button.dataset.status || '';
                state.workgroupPage = 1;
                renderStatusFilter();
                loadWorkgroups();
            });
        });
        els.resetFilters?.addEventListener('click', resetFilters);

        els.tableSearch?.addEventListener('input', (event) => {
            const value = String(event.target.value || '').trim();
            if (state.workgroupSearchTimer) {
                window.clearTimeout(state.workgroupSearchTimer);
            }
            state.workgroupSearchTimer = window.setTimeout(() => {
                state.workgroupSearch = value;
                state.workgroupPage = 1;
                loadWorkgroups();
            }, 260);
        });

        els.sortButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const key = button.dataset.workgroupSort;
                if (!key) return;
                if (state.workgroupSortKey === key) {
                    state.workgroupSortDir = state.workgroupSortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    state.workgroupSortKey = key;
                    state.workgroupSortDir = 'asc';
                }
                updateWorkgroupSortIndicators();
                state.workgroupPage = 1;
                loadWorkgroups();
            });
        });

        els.pagePrev?.addEventListener('click', () => {
            if (state.workgroupPage <= 1 || state.workgroupLoading) return;
            state.workgroupPage -= 1;
            loadWorkgroups();
        });

        els.pageNext?.addEventListener('click', () => {
            const totalPages = Math.max(1, Math.ceil(state.workgroupTotal / state.workgroupLimit));
            if (state.workgroupPage >= totalPages || state.workgroupLoading) return;
            state.workgroupPage += 1;
            loadWorkgroups();
        });

        els.tableBody?.addEventListener('click', (event) => {
            const attentionButton = event.target.closest('[data-action="attention"]');
            if (attentionButton) {
                const id = Number(attentionButton.dataset.workgroupId || 0);
                const name = decodeURIComponent(attentionButton.dataset.workgroupName || '');
                const failedCount = Number(attentionButton.dataset.failedCount || 0);
                openAttentionModal(id, name, failedCount);
                return;
            }

            const workgroupButton = event.target.closest('[data-action="open-workgroup"]');
            if (workgroupButton) {
                const id = Number(workgroupButton.dataset.workgroupId || 0);
                window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'workgroup', id } }));
                return;
            }

            const facilityButton = event.target.closest('[data-action="open-facility"]');
            if (facilityButton) {
                const id = Number(facilityButton.dataset.facilityId || 0);
                window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'facility', id } }));
                return;
            }

            const actionButton = event.target.closest('[data-action="menu"]');
            if (actionButton) {
                toggleActionMenu(event, Number(actionButton.dataset.workgroupId || 0), decodeURIComponent(actionButton.dataset.workgroupName || ''), actionButton);
                return;
            }

            const row = event.target.closest('tr[data-action="open-workgroup-row"]');
            if (!row) {
                return;
            }

            if (event.target.closest('button, a, input, select, textarea, [role="button"]')) {
                return;
            }

            const id = Number(row.dataset.workgroupId || 0);
            if (id > 0) {
                window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'workgroup', id } }));
            }
        });

        document.addEventListener('click', (event) => {
            if (state.activeDropdown === 'facility' && !els.facilityPanel.contains(event.target) && !els.facilityTrigger.contains(event.target)) {
                closeDropdown();
            }
            const clickedToggle = event.target.closest('[data-action="menu"]');
            if (!clickedToggle && els.actionMenu && !els.actionMenu.contains(event.target)) {
                closeActionMenu();
            }
        });

        els.actionOverlay?.addEventListener('click', closeActionMenu);
        els.actionEdit?.addEventListener('click', () => state.actionTarget && openEditModal(state.actionTarget.id));
        els.actionDelete?.addEventListener('click', () => state.actionTarget && openDeleteModal(state.actionTarget.id, state.actionTarget.name));

        els.editClose?.addEventListener('click', closeEditModal);
        els.editModal?.addEventListener('click', (event) => {
            if (event.target === els.editModal) closeEditModal();
        });

        els.deleteCancel?.addEventListener('click', closeDeleteModal);
        els.deleteConfirm?.addEventListener('click', confirmDelete);
        els.deleteModal?.addEventListener('click', (event) => {
            if (event.target === els.deleteModal) closeDeleteModal();
        });

        els.attentionClose?.addEventListener('click', closeAttentionModal);
        els.attentionModal?.addEventListener('click', (event) => {
            if (event.target === els.attentionModal) closeAttentionModal();
        });
        els.attentionListWrap?.addEventListener('scroll', maybeLoadMoreAttentionRows);
        els.attentionSortButtons?.forEach((button) => {
            button.addEventListener('click', () => {
                const key = button.dataset.attentionSort;
                if (!key) return;
                if (state.attentionSortKey === key) {
                    state.attentionSortDir = state.attentionSortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    state.attentionSortKey = key;
                    state.attentionSortDir = key === 'updatedAt' ? 'desc' : 'asc';
                }
                updateAttentionSortIndicators();
                renderAttentionRows(state.attentionRows);
            });
        });
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function getFacilityOptions() {
        return Array.isArray(state.config.facilities) ? state.config.facilities : [];
    }

    function findOptionLabel(options, value, fallback) {
        const match = options.find((item) => String(item.id) === String(value));
        return match?.name || fallback;
    }

    function renderFilters() {
        const facilities = getFacilityOptions();
        els.facilityTrigger.disabled = !state.config.canChooseFacility && facilities.length <= 1;
        els.facilityLabel.textContent = state.selectedFacilityId
            ? findOptionLabel(facilities, state.selectedFacilityId, text.selectFacility)
            : text.allFacilities;
        renderFacilityOptions();
        renderStatusFilter();
    }

    function renderStatusFilter() {
        els.statusButtons.forEach((button) => {
            const status = button.dataset.status || '';
            const active = status === (state.selectedStatus || '');
            button.className = 'workgroup-status-pill';

            if (active) {
                if (status === 'ok') {
                    button.classList.add('bg-emerald-50', 'text-emerald-700', 'shadow-[inset_0_0_0_1px_rgba(16,185,129,0.22)]');
                } else if (status === 'failed') {
                    button.classList.add('bg-rose-50', 'text-rose-700', 'shadow-[inset_0_0_0_1px_rgba(244,63,94,0.22)]');
                } else {
                    button.classList.add('bg-sky-50', 'text-sky-700', 'shadow-[inset_0_0_0_1px_rgba(14,165,233,0.18)]');
                }
                return;
            }

            button.classList.add('text-slate-600', 'hover:bg-slate-50', 'hover:text-slate-900');
        });
    }

    function renderFacilityOptions() {
        const facilities = getFacilityOptions();
        const query = state.facilitySearch.trim().toLowerCase();
        let options = facilities.filter((item) => item.name.toLowerCase().includes(query));
        if (state.config.canChooseFacility) {
            options = [{ id: '', name: text.allFacilities }, ...options];
        }

        els.facilityHint.textContent = options.length
            ? `${options.length} ${options.length === 1 ? text.option : text.options}`
            : text.noOptionsFound;

        els.facilityOptions.innerHTML = options.length
            ? options.map((item) => `
                <button
                    type="button"
                    data-id="${String(item.id)}"
                    class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm ${String(item.id) === String(state.selectedFacilityId) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-sky-50 hover:text-sky-700'}">
                    ${Perfectlum.escapeHtml(item.name)}
                </button>
            `).join('')
            : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${Perfectlum.escapeHtml(text.noOptionsFound)}</div>`;

        els.facilityOptions.querySelectorAll('button[data-id]').forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedFacilityId = button.dataset.id || '';
                state.facilitySearch = '';
                if (els.facilitySearch) els.facilitySearch.value = '';
                closeDropdown();
                renderFilters();
                state.workgroupPage = 1;
                loadWorkgroups();
            });
        });
    }

    function toggleDropdown(type) {
        if (type === 'facility' && els.facilityTrigger.disabled) return;
        state.activeDropdown = state.activeDropdown === type ? null : type;
        els.facilityPanel.classList.toggle('hidden', state.activeDropdown !== 'facility');
        if (state.activeDropdown === 'facility') {
            els.facilitySearch?.focus();
        }
    }

    function closeDropdown() {
        state.activeDropdown = null;
        els.facilityPanel.classList.add('hidden');
    }

    function resetFilters() {
        state.selectedFacilityId = state.config.canChooseFacility ? '' : (getFacilityOptions()[0] ? String(getFacilityOptions()[0].id) : '');
        state.selectedStatus = state.defaultStatus || '';
        state.facilitySearch = '';
        state.workgroupSearch = '';
        state.workgroupPage = 1;
        if (els.tableSearch) els.tableSearch.value = '';
        if (els.facilitySearch) els.facilitySearch.value = '';
        closeDropdown();
        renderFilters();
        loadWorkgroups();
    }

    function buildWorkgroupsUrl(extra = {}) {
        return Perfectlum.buildServerUrl('/api/workgroups', {
            facility_id: state.selectedFacilityId || '',
            type: state.selectedStatus || '',
            search: state.workgroupSearch || '',
            page: state.workgroupPage,
            limit: state.workgroupLimit,
            sort: state.workgroupSortKey || 'name',
            order: state.workgroupSortDir || 'asc',
            ...extra,
        });
    }

    function workgroupMatchesSelectedStatus(item) {
        if (!state.selectedStatus) {
            return true;
        }

        const displaysCount = Number(item.displaysCount || 0);
        const okDisplaysCount = Number(item.okDisplaysCount || 0);
        const failedDisplaysCount = Number(item.failedDisplaysCount || 0);

        if (state.selectedStatus === 'failed') {
            return failedDisplaysCount > 0;
        }

        if (state.selectedStatus === 'ok') {
            return displaysCount > 0 && failedDisplaysCount === 0 && okDisplaysCount === displaysCount;
        }

        return true;
    }

    function updateWorkgroupSortIndicators() {
        document.querySelectorAll('[data-workgroup-sort]').forEach((button) => {
            const key = button.getAttribute('data-workgroup-sort');
            button.classList.toggle('is-active', key === state.workgroupSortKey);
        });
        document.querySelectorAll('[data-workgroup-sort-indicator]').forEach((node) => {
            const key = node.getAttribute('data-workgroup-sort-indicator');
            if (key === state.workgroupSortKey) {
                node.textContent = state.workgroupSortDir === 'asc' ? '↑' : '↓';
            } else {
                node.textContent = '↕';
            }
        });
    }

    function renderWorkgroupPager() {
        const totalPages = Math.max(1, Math.ceil(state.workgroupTotal / state.workgroupLimit));
        const current = Math.min(state.workgroupPage, totalPages);
        if (els.pageLabel) {
            els.pageLabel.textContent = `${text.page || 'Page'} ${current} / ${totalPages}`;
        }
        if (els.pagePrev) els.pagePrev.disabled = state.workgroupLoading || current <= 1;
        if (els.pageNext) els.pageNext.disabled = state.workgroupLoading || current >= totalPages;

        const from = state.workgroupTotal === 0 ? 0 : ((current - 1) * state.workgroupLimit) + 1;
        const to = Math.min(state.workgroupTotal, current * state.workgroupLimit);
        if (els.tableSummary) {
            els.tableSummary.textContent = `${text.showing || 'Showing'} ${from}-${to} ${text.of || 'of'} ${state.workgroupTotal} ${text.results || 'results'}`;
        }
        if (els.tableMeta) {
            const statusLabel = state.selectedStatus === 'ok' ? 'OK' : (state.selectedStatus === 'failed' ? 'Not OK' : 'All');
            els.tableMeta.textContent = `${statusLabel} • ${state.workgroupTotal} ${text.results || 'results'}`;
        }
    }

    function rowStatusDotClass(item) {
        if (Number(item.failedDisplaysCount || 0) > 0) return 'bg-rose-500 shadow-[0_0_0_4px_rgba(244,63,94,0.14)]';
        if (Number(item.okDisplaysCount || 0) > 0) return 'bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.14)]';
        return 'bg-slate-300 shadow-[0_0_0_4px_rgba(148,163,184,0.14)]';
    }

    function renderWorkgroupRows() {
        if (!els.tableBody) return;
        if (state.workgroupLoading) {
            els.tableBody.innerHTML = `<tr><td colspan="7" class="workgroup-empty">${Perfectlum.escapeHtml(text.loading || 'Loading...')}</td></tr>`;
            return;
        }
        const rows = state.workgroupRows;
        if (!rows.length) {
            els.tableBody.innerHTML = `<tr><td colspan="7" class="workgroup-empty">${Perfectlum.escapeHtml(text.noMatchingRecordsFound || 'No matching records found')}</td></tr>`;
            return;
        }

        els.tableBody.innerHTML = rows.map((item) => {
            const failedCount = Number(item.failedDisplaysCount || 0);
            const name = String(item.name || '-');
            const encodedName = encodeURIComponent(name);
            const facName = String(item.facName || '-');
            return `
                <tr class="workgroup-row-clickable" data-action="open-workgroup-row" data-workgroup-id="${Number(item.id || 0)}">
                    <td>
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex h-2.5 w-2.5 shrink-0 rounded-full ${rowStatusDotClass(item)}"></span>
                            <div class="min-w-0">
                                <button type="button" data-action="open-workgroup" data-workgroup-id="${Number(item.id || 0)}" class="cursor-pointer font-semibold text-sky-600 transition hover:text-sky-700 hover:underline">${Perfectlum.escapeHtml(name)}</button>
                                ${failedCount > 0 ? `<button type="button" data-action="attention" data-workgroup-id="${Number(item.id || 0)}" data-workgroup-name="${encodedName}" data-failed-count="${failedCount}" class="mt-1 block text-[11px] font-semibold text-rose-600 underline decoration-rose-300 decoration-dashed underline-offset-2 transition hover:text-rose-700">${Perfectlum.escapeHtml(String(failedCount))} ${Perfectlum.escapeHtml(failedCount === 1 ? text.display : text.displays)} ${Perfectlum.escapeHtml(text.needAttention)}</button>` : ''}
                            </div>
                        </div>
                    </td>
                    <td>${Perfectlum.escapeHtml(String(item.address || '-'))}</td>
                    <td>${Perfectlum.escapeHtml(String(item.phone || '-'))}</td>
                    <td>${!item.facId || facName === '-' ? '-' : `<span class="workgroup-facility-cell"><span class="workgroup-facility-badge">F</span><button type="button" data-action="open-facility" data-facility-id="${Number(item.facId || 0)}" class="workgroup-facility-link cursor-pointer text-slate-600 transition hover:text-sky-600 hover:underline">${Perfectlum.escapeHtml(facName)}</button></span>`}</td>
                    <td><span class="font-semibold text-slate-700">${Perfectlum.escapeHtml(String(item.workstationsCount ?? 0))}</span></td>
                    <td><span class="font-semibold text-slate-700">${Perfectlum.escapeHtml(String(item.displaysCount ?? 0))}</span></td>
                    <td class="text-center">${!canManageWorkgroups ? '' : `<button type="button" data-action="menu" data-workgroup-id="${Number(item.id || 0)}" data-workgroup-name="${encodedName}" class="workgroup-row-action mx-auto transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2"></circle><circle cx="12" cy="5" r="2"></circle><circle cx="12" cy="19" r="2"></circle></svg></button>`}</td>
                </tr>
            `;
        }).join('');
    }

    async function loadWorkgroups() {
        closeActionMenu();
        state.workgroupLoading = true;
        renderWorkgroupPager();
        renderWorkgroupRows();
        let hasError = false;
        try {
            const payload = await Perfectlum.request(buildWorkgroupsUrl());
            const incoming = Array.isArray(payload?.data) ? payload.data : [];
            state.workgroupRows = incoming.filter(workgroupMatchesSelectedStatus);
            state.workgroupTotal = Number(payload?.total || 0);
            if (!Number.isFinite(state.workgroupTotal) || state.workgroupTotal < state.workgroupRows.length) {
                state.workgroupTotal = state.workgroupRows.length;
            }
            const totalPages = Math.max(1, Math.ceil(state.workgroupTotal / state.workgroupLimit));
            if (state.workgroupPage > totalPages) {
                state.workgroupPage = totalPages;
                return loadWorkgroups();
            }
        } catch (error) {
            hasError = true;
            state.workgroupRows = [];
            state.workgroupTotal = 0;
            renderWorkgroupPager();
            if (els.tableBody) {
                els.tableBody.innerHTML = `<tr><td colspan="7" class="workgroup-empty">${Perfectlum.escapeHtml(error.message || text.unableToLoadData || 'Unable to load data')}</td></tr>`;
            }
        } finally {
            state.workgroupLoading = false;
            renderWorkgroupPager();
            if (!hasError) renderWorkgroupRows();
        }
    }

    function toggleActionMenu(event, id, name, anchorEl = null) {
        if (!canManageWorkgroups) return;
        event.preventDefault();
        event.stopPropagation();

        const target = anchorEl || event.currentTarget;
        const rect = target.getBoundingClientRect();
        const nextOpen = !(state.actionTarget && state.actionTarget.id === id && !els.actionMenu.classList.contains('hidden'));
        state.actionTarget = nextOpen ? { id, name } : null;

        if (!nextOpen) {
            closeActionMenu();
            return;
        }

        els.actionOverlay.classList.remove('hidden');
        els.actionMenu.classList.remove('hidden');
        els.actionMenu.style.left = `${Math.max(16, rect.right - 224)}px`;
        els.actionMenu.style.top = `${rect.bottom + 10}px`;
        window.lucide?.createIcons();
    }

    function closeActionMenu() {
        state.actionTarget = null;
        els.actionOverlay.classList.add('hidden');
        els.actionMenu.classList.add('hidden');
    }

    async function openEditModal(id) {
        if (!canManageWorkgroups) return;
        closeActionMenu();
        els.editModal.classList.remove('hidden');
        els.editModal.classList.add('flex');
        els.editLoading.classList.remove('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editForm.innerHTML = '';
        if (els.editKicker) els.editKicker.textContent = id === 0 ? text.addWorkgroup : text.editWorkgroup;
        if (els.editTitle) els.editTitle.textContent = id === 0 ? text.createWorkgroup : text.updateWorkgroupDetails;

        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', id);
            const payload = await Perfectlum.postForm('/workgroup-form', formData);
            els.editForm.innerHTML = payload.content || '';
            bindEditForm();
            window.lucide?.createIcons();
        } catch (error) {
            els.editError.textContent = error.message || text.unableToLoadWorkgroupForm;
            els.editError.classList.remove('hidden');
        } finally {
            els.editLoading.classList.add('hidden');
        }
    }

    function bindEditForm() {
        const form = els.editForm.querySelector('form');
        if (!form) return;
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (form.dataset.submitting === '1') return;
            form.dataset.submitting = '1';

            try {
                const formData = new FormData(form);
                if (!formData.get('_token')) {
                    formData.append('_token', csrfToken());
                }
                await Perfectlum.postForm(form.getAttribute('action') || window.location.pathname, formData);
                closeEditModal();
                loadWorkgroups();
            } catch (error) {
                els.editError.textContent = error.message || text.unableToSaveWorkgroup;
                els.editError.classList.remove('hidden');
            } finally {
                form.dataset.submitting = '0';
            }
        });
    }

    function closeEditModal() {
        els.editModal.classList.add('hidden');
        els.editModal.classList.remove('flex');
        els.editLoading.classList.add('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editForm.innerHTML = '';
    }

    function openDeleteModal(id, name) {
        if (!canManageWorkgroups) return;
        closeActionMenu();
        state.deleteTarget = { id, name };
        els.deleteName.textContent = name || '';
        els.deleteModal.classList.remove('hidden');
        els.deleteModal.classList.add('flex');
    }

    function closeDeleteModal() {
        state.deleteTarget = null;
        els.deleteModal.classList.add('hidden');
        els.deleteModal.classList.remove('flex');
        els.deleteConfirm.disabled = false;
        els.deleteConfirm.textContent = text.deleteWorkgroup;
    }

    function attentionLastSyncToEpoch(value) {
        if (!value || value === '-') return 0;
        const parts = String(value).trim().split(' ');
        if (parts.length < 4) return 0;
        const day = Number(parts[0]);
        const monthMap = { Jan: 0, Feb: 1, Mar: 2, Apr: 3, May: 4, Jun: 5, Jul: 6, Aug: 7, Sep: 8, Oct: 9, Nov: 10, Dec: 11 };
        const month = monthMap[parts[1]] ?? -1;
        const year = Number(parts[2]);
        const hm = String(parts[3] || '').split(':');
        const hour = Number(hm[0] || 0);
        const minute = Number(hm[1] || 0);
        if (month < 0 || !Number.isFinite(day) || !Number.isFinite(year)) return 0;
        const d = new Date(year, month, day, hour, minute, 0);
        return Number.isNaN(d.getTime()) ? 0 : d.getTime();
    }

    function compareAttentionRows(a, b) {
        const key = state.attentionSortKey;
        const dir = state.attentionSortDir === 'desc' ? -1 : 1;
        if (key === 'updatedAt') {
            return (attentionLastSyncToEpoch(a.updatedAt) - attentionLastSyncToEpoch(b.updatedAt)) * dir;
        }
        const av = String(a[key] ?? '').toLowerCase();
        const bv = String(b[key] ?? '').toLowerCase();
        if (av === bv) return 0;
        return av > bv ? dir : -dir;
    }

    function updateAttentionSortIndicators() {
        document.querySelectorAll('[data-attention-sort-indicator]').forEach((node) => {
            const key = node.getAttribute('data-attention-sort-indicator');
            if (key === state.attentionSortKey) {
                node.textContent = state.attentionSortDir === 'asc' ? '↑' : '↓';
            } else {
                node.textContent = '↕';
            }
        });
    }

    function renderAttentionRows(rows) {
        const sortedRows = [...(rows || [])].sort(compareAttentionRows);
        els.attentionList.innerHTML = sortedRows.map((item) => `
            <tr class="align-top">
                <td class="px-4 py-3 text-[13px] font-medium text-slate-800">${Perfectlum.escapeHtml(item.displayName || '-')}</td>
                <td class="px-4 py-3 text-[13px] text-slate-600">${Perfectlum.escapeHtml(item.wsName || '-')}</td>
                <td class="px-4 py-3 text-[13px] text-slate-600">${Perfectlum.escapeHtml(item.updatedAt || '-')}</td>
                <td class="px-4 py-3 text-[12px] text-rose-600">${Perfectlum.escapeHtml(item.attentionText || 'No alert detail')}</td>
                <td class="px-4 py-3 text-center">
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'display',id:${Number(item.displayId || 0)}}}))" class="mx-auto rounded-lg border border-slate-200 px-2.5 py-1.5 text-[12px] font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                        ${Perfectlum.escapeHtml(text.openDisplay)}
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function fetchAttentionPage(page) {
        return Perfectlum.request(Perfectlum.buildServerUrl('/api/displays', {
            workgroup_id: Number(state.attentionTarget.id),
            type: 'failed',
            sort: 'updated_at',
            order: 'desc',
            limit: state.attentionLimit,
            page,
        }));
    }

    function applyAttentionViewportHeight() {
        if (!els.attentionListWrap) return;
        const maxFromViewport = Math.max(280, Math.floor(window.innerHeight * 0.56));
        els.attentionListWrap.style.height = `${maxFromViewport}px`;
    }

    async function maybeLoadMoreAttentionRows() {
        if (!state.attentionHasMore || state.attentionLoadingMore || !state.attentionTarget) return;
        const node = els.attentionListWrap;
        if (!node) return;
        const nearBottom = node.scrollTop + node.clientHeight >= node.scrollHeight - 80;
        if (!nearBottom) return;

        state.attentionLoadingMore = true;
        els.attentionMore.textContent = text.loadingMore;
        els.attentionMore.classList.remove('hidden');
        try {
            const nextPage = state.attentionPage + 1;
            const payload = await fetchAttentionPage(nextPage);
            const rows = Array.isArray(payload.data) ? payload.data : [];
            if (rows.length) {
                state.attentionRows = state.attentionRows.concat(rows);
                renderAttentionRows(state.attentionRows);
                state.attentionPage = nextPage;
            }
            state.attentionHasMore = rows.length === state.attentionLimit;
        } catch (error) {
            state.attentionHasMore = false;
        } finally {
            state.attentionLoadingMore = false;
            if (!state.attentionHasMore) {
                els.attentionMore.classList.add('hidden');
            }
        }
    }

    async function openAttentionModal(workgroupId, workgroupName, failedCount = null) {
        closeActionMenu();
        state.attentionTarget = { id: Number(workgroupId), name: workgroupName || '' };
        state.attentionPage = 1;
        state.attentionHasMore = false;
        state.attentionLoadingMore = false;
        state.attentionRows = [];
        els.attentionTitle.textContent = text.displaysNeedAttentionTitle;
        const suffix = failedCount === null ? '' : ` • ${failedCount} ${failedCount === 1 ? text.display : text.displays}`;
        els.attentionSubtitle.textContent = `${workgroupName || '-'}${suffix}`;
        updateAttentionSortIndicators();

        els.attentionLoading.classList.remove('hidden');
        els.attentionError.classList.add('hidden');
        els.attentionEmpty.classList.add('hidden');
        els.attentionListWrap.classList.add('hidden');
        els.attentionMore.classList.add('hidden');
        els.attentionList.innerHTML = '';
        els.attentionModal.classList.remove('hidden');
        els.attentionModal.classList.add('flex');
        applyAttentionViewportHeight();
        if (els.attentionListWrap) els.attentionListWrap.scrollTop = 0;

        try {
            const payload = await fetchAttentionPage(1);
            const rows = Array.isArray(payload.data) ? payload.data : [];
            if (!rows.length) {
                els.attentionEmpty.classList.remove('hidden');
                return;
            }

            state.attentionRows = rows;
            renderAttentionRows(state.attentionRows);
            state.attentionHasMore = rows.length === state.attentionLimit;
            if (state.attentionHasMore) {
                els.attentionMore.textContent = text.loadingMore;
                els.attentionMore.classList.remove('hidden');
            }
            els.attentionListWrap.classList.remove('hidden');
        } catch (error) {
            els.attentionError.textContent = error.message || text.unableToLoadAttentionDisplays;
            els.attentionError.classList.remove('hidden');
        } finally {
            els.attentionLoading.classList.add('hidden');
        }
    }

    function closeAttentionModal() {
        state.attentionTarget = null;
        state.attentionPage = 1;
        state.attentionHasMore = false;
        state.attentionLoadingMore = false;
        state.attentionRows = [];
        els.attentionModal.classList.add('hidden');
        els.attentionModal.classList.remove('flex');
        els.attentionLoading.classList.add('hidden');
        els.attentionError.classList.add('hidden');
        els.attentionEmpty.classList.add('hidden');
        els.attentionListWrap.classList.add('hidden');
        els.attentionMore.classList.add('hidden');
        els.attentionList.innerHTML = '';
        if (els.attentionListWrap) els.attentionListWrap.scrollTop = 0;
    }

    async function confirmDelete() {
        if (!state.deleteTarget?.id || els.deleteConfirm.disabled) return;
        els.deleteConfirm.disabled = true;
        els.deleteConfirm.textContent = text.deleting;

        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', state.deleteTarget.id);
            const payload = await Perfectlum.postForm('/delete-workgroup', formData);
            if (!payload.success) {
                throw new Error(payload.msg || text.unableToDeleteWorkgroup);
            }
            closeDeleteModal();
            loadWorkgroups();
        } catch (error) {
            window.alert(error.message || text.unableToDeleteWorkgroup);
            els.deleteConfirm.disabled = false;
            els.deleteConfirm.textContent = text.deleteWorkgroup;
        }
    }

    window.closeWorkgroupPanel = closeEditModal;
    window.openWorkgroupAttentionModal = openAttentionModal;
    window.addEventListener('resize', applyAttentionViewportHeight);
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
</script>

@include('common.navigations.footer')
