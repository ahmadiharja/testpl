@include('common.navigations.header')

@php
    $role = session('role');
    $canManageFacilities = in_array($role, ['super', 'admin'], true);
    $canDeleteFacilities = $role === 'super';
    $initialFacilityStatus = in_array(request('type'), ['ok', 'failed'], true) ? request('type') : '';
    $facilityText = [
        'display' => __('display'),
        'displays' => __('displays'),
        'needAttention' => __('need attention'),
        'name' => __('Name'),
        'location' => __('Location'),
        'timezone' => __('Timezone'),
        'workgroups' => __('Workgroups'),
        'users' => __('Users'),
        'displaysLabel' => __('Displays'),
        'actions' => __('Actions'),
        'searchFacilities' => __('Search facilities...'),
        'previous' => __('Previous'),
        'next' => __('Next'),
        'showing' => __('Showing'),
        'of' => __('of'),
        'page' => __('Page'),
        'results' => __('results'),
        'loading' => __('Loading...'),
        'noMatchingRecordsFound' => __('No matching records found'),
        'unableToLoadData' => __('Unable to load data'),
        'addFacility' => __('Add Facility'),
        'editFacility' => __('Edit Facility'),
        'createFacility' => __('Create a new facility'),
        'updateFacilityDetails' => __('Update facility details'),
        'unableToLoadFacilityForm' => __('Unable to load facility form.'),
        'unableToSaveFacility' => __('Unable to save facility.'),
        'deleteFacility' => __('Delete Facility'),
        'deleting' => __('Deleting...'),
        'unableToDeleteFacility' => __('Unable to delete facility.'),
        'displaysNeedAttentionTitle' => __('Displays needing attention'),
        'loadingAttentionDisplays' => __('Loading displays needing attention...'),
        'noDisplaysNeedAttention' => __('No displays currently need attention in this facility.'),
        'unableToLoadAttentionDisplays' => __('Unable to load displays needing attention.'),
        'close' => __('Close'),
        'openDisplay' => __('Open Display'),
        'displayLabel' => __('Display'),
        'workstationLabel' => __('Workstation'),
        'workgroupLabel' => __('Workgroup'),
        'lastSync' => __('Last Sync'),
        'detail' => __('Detail'),
        'loadingMore' => __('Loading more...'),
    ];
@endphp

<style>
    .facility-table-shell {
        border-radius: 2rem;
        border: 1px solid #d5e0ec;
        background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        box-shadow: 0 26px 64px -46px rgba(15, 23, 42, 0.34);
        overflow: hidden;
    }
    .facility-table-controlbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 18px;
        border-bottom: 1px solid #e3ecf5;
        background: linear-gradient(180deg, #f4f9ff 0%, #ffffff 100%);
    }
    .facility-status-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px;
        border-radius: 999px;
        border: 1px solid #d3dfec;
        background: #f3f8fe;
    }
    .facility-status-pill {
        height: 36px;
        border-radius: 999px;
        padding: 0 14px;
        font-size: 12px;
        font-weight: 700;
        color: #4c6077;
        transition: all .18s ease;
    }
    .facility-status-pill:hover {
        color: #243b53;
        background: #eff6fd;
    }
    .facility-reset-btn {
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
    .facility-reset-btn:hover {
        border-color: #9ec6ea;
        color: #1f4f80;
        background: #f3f9ff;
    }
    .facility-table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 18px;
        border-bottom: 1px solid #e3ecf5;
        background: #f8fbff;
    }
    .facility-table-search {
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
    .facility-table-search:focus {
        outline: none;
        border-color: #1d9bf0;
        box-shadow: 0 0 0 3px rgba(29, 155, 240, 0.16);
    }
    .facility-table-wrap {
        overflow-x: auto;
        background: #fff;
    }
    .facility-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 980px;
        table-layout: fixed;
    }
    .facility-table th {
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
    .facility-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #edf2f8;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
        background: #fff;
    }
    .facility-table td:not(:first-child) {
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .facility-table th:nth-child(4),
    .facility-table th:nth-child(5),
    .facility-table th:nth-child(6),
    .facility-table th:nth-child(7),
    .facility-table td:nth-child(4),
    .facility-table td:nth-child(5),
    .facility-table td:nth-child(6),
    .facility-table td:nth-child(7) {
        text-align: center;
    }
    .facility-table tbody tr:hover td {
        background: #f7fbff;
    }
    .facility-row-clickable {
        cursor: pointer;
    }
    .facility-sort-btn {
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
        padding: 0;
        border-radius: 999px;
        padding: 2px 8px;
        margin-left: -8px;
        transition: all .18s ease;
    }
    .facility-sort-btn:hover {
        background: #f2f7fd;
        color: #2f4d6a;
    }
    .facility-sort-btn.is-active {
        background: #e2edf9;
        color: #24486b;
    }
    .facility-sort-indicator {
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
    .facility-sort-btn.is-active .facility-sort-indicator {
        background: #2f6fae;
        color: #ffffff;
    }
    .facility-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 16px 14px;
        border-top: 1px solid #dbe7f3;
        background: #f7fbff;
    }
    .facility-pager {
        display: inline-flex;
        gap: 8px;
        align-items: center;
    }
    .facility-page-btn {
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
    .facility-page-btn:hover:not(:disabled) {
        border-color: #1d9bf0;
        color: #0f5f9f;
        background: #f0f8ff;
    }
    .facility-page-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }
    .facility-row-action {
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
    .facility-empty {
        padding: 24px 16px;
        text-align: center;
        color: #5f7388;
        font-size: 14px;
        border-bottom: 1px solid #edf2f8;
    }
    @media (max-width: 960px) {
        .facility-table-controlbar {
            flex-wrap: wrap;
            align-items: stretch;
        }
        .facility-status-group {
            width: 100%;
            justify-content: space-between;
        }
        .facility-status-pill {
            flex: 1 1 0;
            padding: 0 10px;
        }
        .facility-reset-btn {
            width: 100%;
            justify-content: center;
        }
        .facility-table-toolbar {
            flex-wrap: wrap;
            align-items: center;
        }
        .facility-table-search {
            width: 100%;
        }
        .facility-table-footer {
            flex-wrap: wrap;
        }
    }
</style>

<div class="flex flex-col gap-6 pb-8">
    <x-page-header title="{{ __('All Facilities') }}" description="{{ __('Manage all geographical or organizational facility nodes.') }}" icon="building-2">
        @if($role === 'super')
            <x-slot name="actions">
                <button
                    id="create-facility-button"
                    type="button"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    {{ __('Add Facility') }}
                </button>
            </x-slot>
        @endif
    </x-page-header>

    <section class="facility-table-shell mb-10">
        <div class="facility-table-controlbar">
            <div class="facility-status-group">
                <button
                    id="facility-status-all"
                    type="button"
                    data-status=""
                    class="facility-status-pill">
                    <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <i data-lucide="layers-3" class="h-4 w-4"></i>
                        <span>{{ __('All') }}</span>
                    </span>
                </button>
                <button
                    id="facility-status-ok"
                    type="button"
                    data-status="ok"
                    class="facility-status-pill">
                    <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <i data-lucide="badge-check" class="h-4 w-4"></i>
                        <span>{{ __('OK') }}</span>
                    </span>
                </button>
                <button
                    id="facility-status-failed"
                    type="button"
                    data-status="failed"
                    class="facility-status-pill">
                    <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <i data-lucide="triangle-alert" class="h-4 w-4"></i>
                        <span>{{ __('Not OK') }}</span>
                    </span>
                </button>
            </div>
            <button
                id="reset-facility-filters"
                type="button"
                class="facility-reset-btn inline-flex items-center gap-2">
                <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                {{ __('Reset Filters') }}
            </button>
        </div>
        <div class="facility-table-toolbar">
            <input id="facility-table-search" type="text" class="facility-table-search" placeholder="{{ __('Search facilities...') }}">
            <div class="text-[12px] font-semibold text-slate-500" id="facility-table-meta"></div>
        </div>
        <div class="facility-table-wrap">
            <table class="facility-table">
                <colgroup>
                    <col style="width: 27%">
                    <col style="width: 17%">
                    <col style="width: 14%">
                    <col style="width: 11%">
                    <col style="width: 10%">
                    <col style="width: 11%">
                    <col style="width: 10%">
                </colgroup>
                <thead>
                    <tr>
                        <th><button type="button" class="facility-sort-btn" data-facility-sort="name"><span>{{ __('Name') }}</span><span class="facility-sort-indicator" data-facility-sort-indicator="name">↕</span></button></th>
                        <th><button type="button" class="facility-sort-btn" data-facility-sort="location"><span>{{ __('Location') }}</span><span class="facility-sort-indicator" data-facility-sort-indicator="location">↕</span></button></th>
                        <th><button type="button" class="facility-sort-btn" data-facility-sort="timezone"><span>{{ __('Timezone') }}</span><span class="facility-sort-indicator" data-facility-sort-indicator="timezone">↕</span></button></th>
                        <th><button type="button" class="facility-sort-btn" data-facility-sort="workgroupsCount"><span>{{ __('Workgroups') }}</span><span class="facility-sort-indicator" data-facility-sort-indicator="workgroupsCount">↕</span></button></th>
                        <th><button type="button" class="facility-sort-btn" data-facility-sort="usersCount"><span>{{ __('Users') }}</span><span class="facility-sort-indicator" data-facility-sort-indicator="usersCount">↕</span></button></th>
                        <th><button type="button" class="facility-sort-btn" data-facility-sort="displaysCount"><span>{{ __('Displays') }}</span><span class="facility-sort-indicator" data-facility-sort-indicator="displaysCount">↕</span></button></th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="facilities-table-body"></tbody>
            </table>
        </div>
        <div class="facility-table-footer">
            <div class="text-[12px] font-semibold text-slate-500" id="facility-table-summary"></div>
            <div class="facility-pager">
                <button type="button" class="facility-page-btn" id="facility-page-prev">{{ __('Previous') }}</button>
                <span class="text-[12px] font-semibold text-slate-600" id="facility-page-label">1</span>
                <button type="button" class="facility-page-btn" id="facility-page-next">{{ __('Next') }}</button>
            </div>
        </div>
    </section>
</div>

<div id="facility-action-overlay" class="pointer-events-none fixed inset-0 z-[1200] hidden">
    <div
        id="facility-action-menu"
        class="pointer-events-auto fixed hidden w-52 rounded-2xl border border-slate-200 bg-white p-2 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.35)]">
        @if($canManageFacilities)
            <button
                id="facility-action-edit"
                type="button"
                class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                <i data-lucide="pencil-line" class="h-4 w-4"></i>
                {{ __('Edit Facility') }}
            </button>
        @endif
        @if($canDeleteFacilities)
            <button
                id="facility-action-delete"
                type="button"
                class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
                {{ __('Delete Facility') }}
            </button>
        @endif
    </div>
</div>

<div id="facility-edit-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="w-full max-w-2xl rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p id="facility-edit-kicker" class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Edit Facility') }}</p>
                <h3 id="facility-edit-title" class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Update facility details') }}</h3>
            </div>
            <button id="facility-edit-close" type="button" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <div class="px-6 py-6">
            <div id="facility-edit-loading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                {{ __('Loading facility form...') }}
            </div>
            <div id="facility-edit-error" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700"></div>
            <div id="facility-edit-form"></div>
        </div>
    </div>
</div>

<div id="facility-delete-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="w-full max-w-lg rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="border-b border-slate-200 px-6 py-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-400">{{ __('Delete Facility') }}</p>
            <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Delete this facility?') }}</h3>
            <p class="mt-3 text-sm text-slate-500">
                {{ __('This action will permanently remove') }} <span id="facility-delete-name" class="font-semibold text-slate-700"></span>.
                {{ __('Facilities that still have workstations attached cannot be deleted.') }}
            </p>
        </div>

        <div class="flex justify-end gap-3 px-6 py-5">
            <button
                id="facility-delete-cancel"
                type="button"
                class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                {{ __('Cancel') }}
            </button>
            <button
                id="facility-delete-confirm"
                type="button"
                class="inline-flex h-11 items-center rounded-2xl bg-rose-500 px-4 text-sm font-semibold text-white transition hover:bg-rose-400 disabled:cursor-not-allowed disabled:opacity-60">
                {{ __('Delete Facility') }}
            </button>
        </div>
    </div>
</div>

<div id="facility-attention-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="flex max-h-[88vh] w-full max-w-5xl flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-400">{{ __('Needs Attention') }}</p>
                <h3 id="facility-attention-title" class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Displays needing attention') }}</h3>
                <p id="facility-attention-subtitle" class="mt-2 text-sm text-slate-500"></p>
            </div>
            <button id="facility-attention-close" type="button" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden px-6 py-5">
            <div id="facility-attention-loading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                {{ __('Loading displays needing attention...') }}
            </div>
            <div id="facility-attention-error" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700"></div>
            <div id="facility-attention-empty" class="hidden rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-700">{{ __('No displays currently need attention in this facility.') }}</div>

            <div id="facility-attention-list-wrap" class="hidden h-[56vh] overflow-y-auto rounded-2xl border border-slate-200 overscroll-contain">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="sticky top-0 z-10 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <button type="button" data-attention-sort="displayName" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Display') }}</span><span data-sort-indicator="displayName">-</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <button type="button" data-attention-sort="wgName" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Workgroup') }}</span><span data-sort-indicator="wgName">-</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <button type="button" data-attention-sort="wsName" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Workstation') }}</span><span data-sort-indicator="wsName">-</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <button type="button" data-attention-sort="updatedAt" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Last Sync') }}</span><span data-sort-indicator="updatedAt">v</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <button type="button" data-attention-sort="attentionText" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Detail') }}</span><span data-sort-indicator="attentionText">-</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="facility-attention-list" class="divide-y divide-slate-100 bg-white"></tbody>
                </table>
                <div id="facility-attention-more" class="hidden border-t border-slate-200 bg-slate-50 px-4 py-2 text-center text-[12px] font-medium text-slate-500">{{ __('Loading more...') }}</div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const text = @json($facilityText);
    const canManageFacilities = @json($canManageFacilities);
    const canDeleteFacilities = @json($canDeleteFacilities);
    let initialized = false;
    const state = {
        actionTarget: null,
        deleteTarget: null,
        defaultStatus: @json($initialFacilityStatus),
        selectedStatus: @json($initialFacilityStatus),
        facilityRows: [],
        facilityPage: 1,
        facilityLimit: 10,
        facilityTotal: 0,
        facilityLoading: false,
        facilitySearch: '',
        facilitySearchTimer: null,
        facilitySortKey: 'name',
        facilitySortDir: 'asc',
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
        bindElements();
        bindEvents();
        updateFacilitySortIndicators();
        updateAttentionSortIndicators();
        renderStatusFilter();
        loadFacilities();
        window.facilitiesPage = { toggleActionMenu, openEditModal, openDeleteModal };
        window.lucide?.createIcons();
    }

    function bindElements() {
        els.createButton = document.getElementById('create-facility-button');
        els.statusButtons = Array.from(document.querySelectorAll('[data-status]'));
        els.resetFilters = document.getElementById('reset-facility-filters');
        els.tableSearch = document.getElementById('facility-table-search');
        els.tableMeta = document.getElementById('facility-table-meta');
        els.tableBody = document.getElementById('facilities-table-body');
        els.tableSummary = document.getElementById('facility-table-summary');
        els.pagePrev = document.getElementById('facility-page-prev');
        els.pageNext = document.getElementById('facility-page-next');
        els.pageLabel = document.getElementById('facility-page-label');
        els.sortButtons = Array.from(document.querySelectorAll('[data-facility-sort]'));
        els.actionOverlay = document.getElementById('facility-action-overlay');
        els.actionMenu = document.getElementById('facility-action-menu');
        els.actionEdit = document.getElementById('facility-action-edit');
        els.actionDelete = document.getElementById('facility-action-delete');

        els.editModal = document.getElementById('facility-edit-modal');
        els.editClose = document.getElementById('facility-edit-close');
        els.editLoading = document.getElementById('facility-edit-loading');
        els.editError = document.getElementById('facility-edit-error');
        els.editForm = document.getElementById('facility-edit-form');
        els.editKicker = document.getElementById('facility-edit-kicker');
        els.editTitle = document.getElementById('facility-edit-title');

        els.deleteModal = document.getElementById('facility-delete-modal');
        els.deleteName = document.getElementById('facility-delete-name');
        els.deleteCancel = document.getElementById('facility-delete-cancel');
        els.deleteConfirm = document.getElementById('facility-delete-confirm');

        els.attentionModal = document.getElementById('facility-attention-modal');
        els.attentionClose = document.getElementById('facility-attention-close');
        els.attentionTitle = document.getElementById('facility-attention-title');
        els.attentionSubtitle = document.getElementById('facility-attention-subtitle');
        els.attentionLoading = document.getElementById('facility-attention-loading');
        els.attentionError = document.getElementById('facility-attention-error');
        els.attentionEmpty = document.getElementById('facility-attention-empty');
        els.attentionListWrap = document.getElementById('facility-attention-list-wrap');
        els.attentionList = document.getElementById('facility-attention-list');
        els.attentionMore = document.getElementById('facility-attention-more');
        els.attentionSortButtons = Array.from(document.querySelectorAll('[data-attention-sort]'));
    }

    function bindEvents() {
        els.createButton?.addEventListener('click', () => openEditModal(0));
        els.statusButtons.forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedStatus = button.dataset.status || '';
                state.facilityPage = 1;
                renderStatusFilter();
                loadFacilities();
            });
        });
        els.resetFilters?.addEventListener('click', resetFilters);

        els.tableSearch?.addEventListener('input', (event) => {
            const value = String(event.target.value || '').trim();
            if (state.facilitySearchTimer) {
                window.clearTimeout(state.facilitySearchTimer);
            }
            state.facilitySearchTimer = window.setTimeout(() => {
                state.facilitySearch = value;
                state.facilityPage = 1;
                loadFacilities();
            }, 260);
        });

        els.sortButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const key = button.dataset.facilitySort;
                if (!key) return;
                if (state.facilitySortKey === key) {
                    state.facilitySortDir = state.facilitySortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    state.facilitySortKey = key;
                    state.facilitySortDir = 'asc';
                }
                updateFacilitySortIndicators();
                state.facilityPage = 1;
                loadFacilities();
            });
        });

        els.pagePrev?.addEventListener('click', () => {
            if (state.facilityPage <= 1 || state.facilityLoading) return;
            state.facilityPage -= 1;
            loadFacilities();
        });

        els.pageNext?.addEventListener('click', () => {
            const totalPages = Math.max(1, Math.ceil(state.facilityTotal / state.facilityLimit));
            if (state.facilityPage >= totalPages || state.facilityLoading) return;
            state.facilityPage += 1;
            loadFacilities();
        });

        els.tableBody?.addEventListener('click', (event) => {
            const attentionButton = event.target.closest('[data-action="attention"]');
            if (attentionButton) {
                const id = Number(attentionButton.dataset.facilityId || 0);
                const name = decodeURIComponent(attentionButton.dataset.facilityName || '');
                const failedCount = Number(attentionButton.dataset.failedCount || 0);
                openAttentionModal(id, name, failedCount);
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
                toggleActionMenu(event, Number(actionButton.dataset.facilityId || 0), decodeURIComponent(actionButton.dataset.facilityName || ''), actionButton);
                return;
            }

            const row = event.target.closest('tr[data-action="open-facility-row"]');
            if (!row) {
                return;
            }

            if (event.target.closest('button, a, input, select, textarea, [role="button"]')) {
                return;
            }

            const id = Number(row.dataset.facilityId || 0);
            if (id > 0) {
                window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'facility', id } }));
            }
        });

        document.addEventListener('click', (event) => {
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
                renderAttentionRowsV2(state.attentionRows, false);
            });
        });
    }

    function renderStatusFilter() {
        els.statusButtons.forEach((button) => {
            const status = button.dataset.status || '';
            const active = status === (state.selectedStatus || '');
            button.className = 'facility-status-pill';

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

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function resetFilters() {
        state.selectedStatus = state.defaultStatus || '';
        state.facilitySearch = '';
        state.facilityPage = 1;
        if (els.tableSearch) {
            els.tableSearch.value = '';
        }
        renderStatusFilter();
        loadFacilities();
    }

    function buildFacilitiesUrl(extra = {}) {
        return Perfectlum.buildServerUrl('/api/facilities', {
            type: state.selectedStatus || '',
            search: state.facilitySearch || '',
            page: state.facilityPage,
            limit: state.facilityLimit,
            sort: state.facilitySortKey || 'name',
            order: state.facilitySortDir || 'asc',
            ...extra,
        });
    }

    function facilityMatchesSelectedStatus(item) {
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

    function updateFacilitySortIndicators() {
        document.querySelectorAll('[data-facility-sort]').forEach((button) => {
            const key = button.getAttribute('data-facility-sort');
            button.classList.toggle('is-active', key === state.facilitySortKey);
        });
        document.querySelectorAll('[data-facility-sort-indicator]').forEach((node) => {
            const key = node.getAttribute('data-facility-sort-indicator');
            if (key === state.facilitySortKey) {
                node.textContent = state.facilitySortDir === 'asc' ? '↑' : '↓';
            } else {
                node.textContent = '↕';
            }
        });
    }

    function renderFacilityPager() {
        const totalPages = Math.max(1, Math.ceil(state.facilityTotal / state.facilityLimit));
        const current = Math.min(state.facilityPage, totalPages);
        if (els.pageLabel) {
            els.pageLabel.textContent = `${text.page || 'Page'} ${current} / ${totalPages}`;
        }
        if (els.pagePrev) {
            els.pagePrev.disabled = state.facilityLoading || current <= 1;
        }
        if (els.pageNext) {
            els.pageNext.disabled = state.facilityLoading || current >= totalPages;
        }

        const from = state.facilityTotal === 0 ? 0 : ((current - 1) * state.facilityLimit) + 1;
        const to = Math.min(state.facilityTotal, current * state.facilityLimit);
        if (els.tableSummary) {
            els.tableSummary.textContent = `${text.showing || 'Showing'} ${from}-${to} ${text.of || 'of'} ${state.facilityTotal} ${text.results || 'results'}`;
        }
        if (els.tableMeta) {
            const statusLabel = state.selectedStatus === 'ok' ? 'OK' : (state.selectedStatus === 'failed' ? 'Not OK' : 'All');
            els.tableMeta.textContent = `${statusLabel} • ${state.facilityTotal} ${text.results || 'results'}`;
        }
    }

    function rowStatusDotClass(item) {
        if (Number(item.failedDisplaysCount || 0) > 0) {
            return 'bg-rose-500 shadow-[0_0_0_4px_rgba(244,63,94,0.14)]';
        }
        if (Number(item.okDisplaysCount || 0) > 0) {
            return 'bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.14)]';
        }
        return 'bg-slate-300 shadow-[0_0_0_4px_rgba(148,163,184,0.14)]';
    }

    function renderFacilityRows() {
        if (!els.tableBody) return;
        if (state.facilityLoading) {
            els.tableBody.innerHTML = `<tr><td colspan="7" class="facility-empty">${Perfectlum.escapeHtml(text.loading || 'Loading...')}</td></tr>`;
            return;
        }

        const rows = state.facilityRows;
        if (!rows.length) {
            els.tableBody.innerHTML = `<tr><td colspan="7" class="facility-empty">${Perfectlum.escapeHtml(text.noMatchingRecordsFound || 'No matching records found')}</td></tr>`;
            return;
        }

        els.tableBody.innerHTML = rows.map((item) => {
            const failedCount = Number(item.failedDisplaysCount || 0);
            const name = String(item.name || '-');
            const encodedName = encodeURIComponent(name);
            return `
                <tr class="facility-row-clickable" data-action="open-facility-row" data-facility-id="${Number(item.id || 0)}">
                    <td>
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex h-2.5 w-2.5 shrink-0 rounded-full ${rowStatusDotClass(item)}"></span>
                            <div class="min-w-0">
                                <button
                                    type="button"
                                    data-action="open-facility"
                                    data-facility-id="${Number(item.id || 0)}"
                                    class="cursor-pointer font-semibold text-sky-600 transition hover:text-sky-700 hover:underline">
                                    ${Perfectlum.escapeHtml(name)}
                                </button>
                                ${failedCount > 0
                                    ? `<button
                                        type="button"
                                        data-action="attention"
                                        data-facility-id="${Number(item.id || 0)}"
                                        data-facility-name="${encodedName}"
                                        data-failed-count="${failedCount}"
                                        class="mt-1 block text-[11px] font-semibold text-rose-600 underline decoration-rose-300 decoration-dashed underline-offset-2 transition hover:text-rose-700">
                                        ${Perfectlum.escapeHtml(String(failedCount))} ${Perfectlum.escapeHtml(failedCount === 1 ? text.display : text.displays)} ${Perfectlum.escapeHtml(text.needAttention)}
                                    </button>`
                                    : ''}
                            </div>
                        </div>
                    </td>
                    <td>${Perfectlum.escapeHtml(item.location || '-')}</td>
                    <td>${Perfectlum.escapeHtml(item.timezone || '-')}</td>
                    <td><span class="font-semibold text-slate-700">${Perfectlum.escapeHtml(String(item.workgroupsCount ?? 0))}</span></td>
                    <td><span class="font-semibold text-slate-700">${Perfectlum.escapeHtml(String(item.usersCount ?? 0))}</span></td>
                    <td><span class="font-semibold text-slate-700">${Perfectlum.escapeHtml(String(item.displaysCount ?? 0))}</span></td>
                    <td class="text-center">
                        <button
                            type="button"
                            data-action="menu"
                            data-facility-id="${Number(item.id || 0)}"
                            data-facility-name="${encodedName}"
                            class="facility-row-action mx-auto transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2"></circle><circle cx="12" cy="5" r="2"></circle><circle cx="12" cy="19" r="2"></circle></svg>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function loadFacilities() {
        closeActionMenu();
        state.facilityLoading = true;
        renderFacilityPager();
        renderFacilityRows();
        let hasError = false;
        try {
            const payload = await Perfectlum.request(buildFacilitiesUrl());
            const incoming = Array.isArray(payload?.data) ? payload.data : [];
            state.facilityRows = incoming.filter(facilityMatchesSelectedStatus);
            state.facilityTotal = Number(payload?.total || 0);
            if (!Number.isFinite(state.facilityTotal) || state.facilityTotal < state.facilityRows.length) {
                state.facilityTotal = state.facilityRows.length;
            }

            const totalPages = Math.max(1, Math.ceil(state.facilityTotal / state.facilityLimit));
            if (state.facilityPage > totalPages) {
                state.facilityPage = totalPages;
                return loadFacilities();
            }
        } catch (error) {
            hasError = true;
            state.facilityRows = [];
            state.facilityTotal = 0;
            renderFacilityPager();
            if (els.tableBody) {
                els.tableBody.innerHTML = `<tr><td colspan="7" class="facility-empty">${Perfectlum.escapeHtml(error.message || text.unableToLoadData || 'Unable to load data')}</td></tr>`;
            }
        } finally {
            state.facilityLoading = false;
            renderFacilityPager();
            if (!hasError) {
                renderFacilityRows();
            }
        }
    }

    function toggleActionMenu(event, id, name, anchorEl = null) {
        if (!canManageFacilities) return;
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
        els.actionMenu.style.left = `${Math.max(16, rect.right - 208)}px`;
        els.actionMenu.style.top = `${rect.bottom + 10}px`;
        window.lucide?.createIcons();
    }

    function closeActionMenu() {
        state.actionTarget = null;
        els.actionOverlay.classList.add('hidden');
        els.actionMenu.classList.add('hidden');
    }

    async function openEditModal(id) {
        closeActionMenu();
        els.editModal.classList.remove('hidden');
        els.editModal.classList.add('flex');
        els.editLoading.classList.remove('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editForm.innerHTML = '';
        if (els.editKicker) els.editKicker.textContent = id === 0 ? text.addFacility : text.editFacility;
        if (els.editTitle) els.editTitle.textContent = id === 0 ? text.createFacility : text.updateFacilityDetails;

        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', id);
            const payload = await Perfectlum.postForm('/facility-form', formData);
            els.editForm.innerHTML = payload.content || '';
            bindEditForm();
            window.lucide?.createIcons();
        } catch (error) {
            els.editError.textContent = error.message || text.unableToLoadFacilityForm;
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
                loadFacilities();
            } catch (error) {
                els.editError.textContent = error.message || text.unableToSaveFacility;
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
        if (!canDeleteFacilities) return;
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
        els.deleteConfirm.textContent = text.deleteFacility;
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
        let av;
        let bv;

        if (key === 'updatedAt') {
            av = attentionLastSyncToEpoch(a.updatedAt);
            bv = attentionLastSyncToEpoch(b.updatedAt);
            return (av - bv) * dir;
        }

        av = String(a[key] ?? '').toLowerCase();
        bv = String(b[key] ?? '').toLowerCase();
        if (av === bv) return 0;
        return av > bv ? dir : -dir;
    }

    function updateAttentionSortIndicators() {
        document.querySelectorAll('[data-sort-indicator]').forEach((node) => {
            const key = node.getAttribute('data-sort-indicator');
            if (key === state.attentionSortKey) {
                node.textContent = state.attentionSortDir === 'asc' ? '^' : 'v';
            } else {
                node.textContent = '-';
            }
        });
    }

    function renderAttentionRowsV2(rows, append = false) {
        const sortedRows = [...(rows || [])].sort(compareAttentionRows);
        const html = sortedRows.map((item) => `
            <tr class="align-top">
                <td class="px-4 py-3 text-[13px] font-medium text-slate-800">${Perfectlum.escapeHtml(item.displayName || '-')}</td>
                <td class="px-4 py-3 text-[13px] text-slate-600">${Perfectlum.escapeHtml(item.wgName || '-')}</td>
                <td class="px-4 py-3 text-[13px] text-slate-600">${Perfectlum.escapeHtml(item.wsName || '-')}</td>
                <td class="px-4 py-3 text-[13px] text-slate-600">${Perfectlum.escapeHtml(item.updatedAt || '-')}</td>
                <td class="px-4 py-3 text-[12px] text-rose-600">${Perfectlum.escapeHtml(item.attentionText || 'No alert detail')}</td>
                <td class="px-4 py-3 text-right">
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'display',id:${Number(item.displayId || 0)}}}))" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-[12px] font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                        ${Perfectlum.escapeHtml(text.openDisplay)}
                    </button>
                </td>
            </tr>
        `).join('');

        els.attentionList.innerHTML = html;
    }

    function fetchAttentionPageV2(page) {
        return Perfectlum.request(Perfectlum.buildServerUrl('/api/displays', {
            facility_id: Number(state.attentionTarget.id),
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
            const payload = await fetchAttentionPageV2(nextPage);
            const rows = Array.isArray(payload.data) ? payload.data : [];
            if (rows.length) {
                state.attentionRows = state.attentionRows.concat(rows);
                renderAttentionRowsV2(state.attentionRows, false);
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

    async function openAttentionModal(facilityId, facilityName, failedCount = null) {
        closeActionMenu();
        state.attentionTarget = { id: Number(facilityId), name: facilityName || '' };
        state.attentionPage = 1;
        state.attentionHasMore = false;
        state.attentionLoadingMore = false;
        state.attentionRows = [];
        els.attentionTitle.textContent = text.displaysNeedAttentionTitle;
        const suffix = failedCount === null ? '' : ` • ${failedCount} ${failedCount === 1 ? text.display : text.displays}`;
        els.attentionSubtitle.textContent = `${facilityName || '-'}${suffix}`;

        els.attentionLoading.classList.remove('hidden');
        els.attentionError.classList.add('hidden');
        els.attentionEmpty.classList.add('hidden');
        els.attentionListWrap.classList.add('hidden');
        els.attentionMore.classList.add('hidden');
        els.attentionList.innerHTML = '';
        els.attentionModal.classList.remove('hidden');
        els.attentionModal.classList.add('flex');
        applyAttentionViewportHeight();
        if (els.attentionListWrap) {
            els.attentionListWrap.scrollTop = 0;
        }

        try {
            const payload = await fetchAttentionPageV2(1);
            const rows = Array.isArray(payload.data) ? payload.data : [];
            if (!rows.length) {
                els.attentionEmpty.classList.remove('hidden');
                return;
            }

            state.attentionRows = rows;
            updateAttentionSortIndicators();
            renderAttentionRowsV2(state.attentionRows, false);
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
        if (els.attentionListWrap) {
            els.attentionListWrap.scrollTop = 0;
        }
    }

    async function confirmDelete() {
        if (!state.deleteTarget?.id || els.deleteConfirm.disabled) return;
        els.deleteConfirm.disabled = true;
        els.deleteConfirm.textContent = text.deleting;

        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', state.deleteTarget.id);
            const payload = await Perfectlum.postForm('/delete-facility', formData);
            if (!payload.success) {
                throw new Error(payload.msg || text.unableToDeleteFacility);
            }
            closeDeleteModal();
            loadFacilities();
        } catch (error) {
            window.alert(error.message || text.unableToDeleteFacility);
            els.deleteConfirm.disabled = false;
            els.deleteConfirm.textContent = text.deleteFacility;
        }
    }

    window.closeFacilityPanel = closeEditModal;
    window.openFacilityAttentionModal = openAttentionModal;
    window.addEventListener('resize', applyAttentionViewportHeight);
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
</script>

@include('common.navigations.footer')
