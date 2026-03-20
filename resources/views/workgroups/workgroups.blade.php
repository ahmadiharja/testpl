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
    ];
@endphp

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

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,240px)_minmax(0,280px)_1fr]">
            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Facility') }}</label>
                <div class="relative">
                    <button
                        id="facility-filter-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                        <span id="facility-filter-label" class="truncate">{{ __('All facilities') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div
                        id="facility-filter-panel"
                        class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input
                            id="facility-filter-search"
                            type="text"
                            placeholder="{{ __('Search facilities...') }}"
                            class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="facility-filter-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="facility-filter-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Status') }}</label>
                <div class="grid h-12 grid-cols-3 rounded-2xl border border-slate-200 bg-white p-1">
                    <button
                        id="workgroup-status-all"
                        type="button"
                        data-status=""
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="layers-3" class="h-4 w-4"></i>
                            <span>{{ __('All') }}</span>
                        </span>
                    </button>
                    <button
                        id="workgroup-status-ok"
                        type="button"
                        data-status="ok"
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="badge-check" class="h-4 w-4"></i>
                            <span>{{ __('OK') }}</span>
                        </span>
                    </button>
                    <button
                        id="workgroup-status-failed"
                        type="button"
                        data-status="failed"
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="triangle-alert" class="h-4 w-4"></i>
                            <span>{{ __('Not OK') }}</span>
                        </span>
                    </button>
                </div>
            </div>

            <div class="flex items-end justify-end">
                <button
                    id="reset-workgroup-filters"
                    type="button"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    {{ __('Reset Filters') }}
                </button>
            </div>
        </div>
    </section>

    <x-data-table id="workgroups-grid" class="mb-10 workstation-table-shell" />
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
        grid: null,
    };

    const els = {};

    function init() {
        if (initialized) return;
        if (!window.Perfectlum || !window.gridjs) {
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
        initGrid();
        window.workgroupsPage = { toggleActionMenu };
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
        els.grid = document.getElementById('workgroups-grid');

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
                renderStatusFilter();
                reloadGrid();
            });
        });
        els.resetFilters?.addEventListener('click', resetFilters);

        document.addEventListener('click', (event) => {
            if (state.activeDropdown === 'facility' && !els.facilityPanel.contains(event.target) && !els.facilityTrigger.contains(event.target)) {
                closeDropdown();
            }
            if (els.actionOverlay && !els.actionMenu.contains(event.target)) {
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
            button.className = 'rounded-[0.9rem] px-3 text-sm font-semibold transition';

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
                reloadGrid();
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
        if (els.facilitySearch) els.facilitySearch.value = '';
        closeDropdown();
        renderFilters();
        reloadGrid();
    }

    function buildGridUrl(extra = {}) {
        return Perfectlum.buildServerUrl('/api/workgroups', {
            facility_id: state.selectedFacilityId || '',
            type: state.selectedStatus || '',
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

    function mapRows(d) {
        return (d.data || []).filter(workgroupMatchesSelectedStatus).map(r => [
            { id: r.id, name: r.name, okDisplaysCount: r.okDisplaysCount, failedDisplaysCount: r.failedDisplaysCount },
            r.address,
            r.phone,
            { facId: r.facId, facName: r.facName },
            r.workstationsCount,
            r.displaysCount,
            { id: r.id, name: r.name },
        ]);
    }

    function initGrid() {
        if (!els.grid || state.grid) return;

        state.grid = Perfectlum.createGrid(els.grid, {
            columns: [
                {
                    name: text.name,
                    formatter: (c) => gridjs.html(`
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex h-2.5 w-2.5 shrink-0 rounded-full ${Number(c.failedDisplaysCount || 0) > 0 ? 'bg-rose-500 shadow-[0_0_0_4px_rgba(244,63,94,0.12)]' : (Number(c.okDisplaysCount || 0) > 0 ? 'bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]' : 'bg-slate-300 shadow-[0_0_0_4px_rgba(148,163,184,0.12)]')}"></span>
                            <div class="min-w-0">
                                <button
                                    type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'workgroup',id:${c.id}}}))"
                                    class="cursor-pointer font-medium text-sky-600 transition hover:text-sky-700 hover:underline">
                                    ${Perfectlum.escapeHtml(c.name)}
                                </button>
                                ${Number(c.failedDisplaysCount || 0) > 0
                                    ? `<p class="mt-1 text-[11px] font-medium text-rose-600">${Perfectlum.escapeHtml(String(c.failedDisplaysCount))} ${Perfectlum.escapeHtml(Number(c.failedDisplaysCount) === 1 ? text.display : text.displays)} ${Perfectlum.escapeHtml(text.needAttention)}</p>`
                                    : ''}
                            </div>
                        </div>
                    `),
                },
                { name: text.address, formatter: (c) => gridjs.html(`<span class="text-gray-600 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(c)}</span>`) },
                { name: text.phone, formatter: (c) => gridjs.html(`<span class="text-gray-600 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(c)}</span>`) },
                {
                    name: text.facility,
                    formatter: (c) => !c.facName || c.facName === '-'
                        ? '-'
                        : gridjs.html(`<button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'facility',id:${c.facId}}}))" class="cursor-pointer text-gray-600 transition hover:text-sky-500 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(c.facName)}</button>`),
                },
                { name: text.workstations, sort: false, formatter: (c) => gridjs.html(`<span class="font-semibold text-gray-700 group-[.theme-chroma]:text-gray-200">${Perfectlum.escapeHtml(c)}</span>`) },
                { name: text.displaysLabel, sort: false, formatter: (c) => gridjs.html(`<span class="font-semibold text-gray-700 group-[.theme-chroma]:text-gray-200">${Perfectlum.escapeHtml(c)}</span>`) },
                {
                    name: text.actions,
                    sort: false,
                    width: '112px',
                    formatter: (c) => !canManageWorkgroups ? '' : gridjs.html(`
                        <div class="flex justify-center">
                            <button
                                type="button"
                                onclick='window.workgroupsPage && window.workgroupsPage.toggleActionMenu(event, ${c.id}, ${JSON.stringify(c.name)})'
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2"/><circle cx="12" cy="5" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                            </button>
                        </div>`),
                },
            ],
            server: {
                url: buildGridUrl(),
                then: mapRows,
                total: d => d.total,
            },
            pagination: {
                enabled: true,
                limit: 10,
                server: {
                    url: (_, pg, lim) => buildGridUrl({ page: pg + 1, limit: lim }),
                },
            },
            search: {
                enabled: true,
                server: {
                    url: (_, kw) => buildGridUrl({ search: kw }),
                },
            },
            sort: { multiColumn: false },
            language: {
                search: { placeholder: text.searchWorkgroups },
                pagination: {
                    previous: text.previous,
                    next: text.next,
                    showing: text.showing,
                    results: () => text.results,
                },
                loading: text.loading,
                noRecordsFound: text.noMatchingRecordsFound,
                error: text.unableToLoadData,
            },
        });
    }

    function reloadGrid() {
        closeActionMenu();
        state.grid = null;
        if (!els.grid) {
            initGrid();
            return;
        }

        Perfectlum.remountGrid('workgroups-grid', (freshGrid) => {
            els.grid = freshGrid || document.getElementById('workgroups-grid');
            state.grid = null;
            initGrid();
        });
    }

    function toggleActionMenu(event, id, name) {
        if (!canManageWorkgroups) return;
        event.preventDefault();
        event.stopPropagation();

        const rect = event.currentTarget.getBoundingClientRect();
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
                reloadGrid();
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
            reloadGrid();
        } catch (error) {
            window.alert(error.message || text.unableToDeleteWorkgroup);
            els.deleteConfirm.disabled = false;
            els.deleteConfirm.textContent = text.deleteWorkgroup;
        }
    }

    window.closeWorkgroupPanel = closeEditModal;
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
</script>

@include('common.navigations.footer')
