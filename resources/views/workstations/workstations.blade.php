@include('common.navigations.header')

@php
    $role = session('role');
    $canManageWorkstations = in_array($role, ['super', 'admin'], true);
    $initialWorkstationStatus = in_array(request('type'), ['ok', 'failed'], true) ? request('type') : '';
@endphp

<div class="flex flex-col gap-6 pb-8">
    <x-page-header title="All Workstations" description="Manage physical hardware clusters connecting your remote displays." icon="monitor-speaker">
        <x-slot name="actions">
            <x-export-dropdown
                excel-url="{{ url('reports/workstations?export_type=excel') }}"
                pdf-url="{{ url('reports/workstations?export_type=pdf') }}" />
        </x-slot>
    </x-page-header>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,240px)_minmax(0,240px)_minmax(0,280px)_1fr]">
            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Facility</label>
                <div class="relative">
                    <button
                        id="facility-filter-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                        <span id="facility-filter-label" class="truncate">All facilities</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div
                        id="facility-filter-panel"
                        class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input
                            id="facility-filter-search"
                            type="text"
                            placeholder="Search facilities..."
                            class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="facility-filter-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="facility-filter-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Workgroup</label>
                <div class="relative">
                    <button
                        id="workgroup-filter-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                        <span id="workgroup-filter-label" class="truncate">All workgroups</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div
                        id="workgroup-filter-panel"
                        class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input
                            id="workgroup-filter-search"
                            type="text"
                            placeholder="Search workgroups..."
                            class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="workgroup-filter-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="workgroup-filter-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Status</label>
                <div class="grid h-12 grid-cols-3 rounded-2xl border border-slate-200 bg-white p-1">
                    <button
                        id="workstation-status-all"
                        type="button"
                        data-status=""
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="layers-3" class="h-4 w-4"></i>
                            <span>All</span>
                        </span>
                    </button>
                    <button
                        id="workstation-status-ok"
                        type="button"
                        data-status="ok"
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="badge-check" class="h-4 w-4"></i>
                            <span>OK</span>
                        </span>
                    </button>
                    <button
                        id="workstation-status-failed"
                        type="button"
                        data-status="failed"
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="triangle-alert" class="h-4 w-4"></i>
                            <span>Not OK</span>
                        </span>
                    </button>
                </div>
            </div>

            <div class="flex items-end justify-end">
                <button
                    id="reset-workstation-filters"
                    type="button"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    Reset Filters
                </button>
            </div>
        </div>
    </section>

    <x-data-table id="workstations-grid" class="mb-10 workstation-table-shell" />
</div>

<div id="workstation-action-overlay" class="pointer-events-none fixed inset-0 z-[1200] hidden">
    <div
        id="workstation-action-menu"
        class="pointer-events-auto fixed hidden w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.35)]">
        @if($canManageWorkstations)
            <button
                id="workstation-action-edit"
                type="button"
                class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                <i data-lucide="pencil-line" class="h-4 w-4"></i>
                Edit Workstation
            </button>
            <button
                id="workstation-action-delete"
                type="button"
                class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
                Delete Workstation
            </button>
        @endif
    </div>
</div>

<div id="workstation-edit-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="flex max-h-[88vh] w-full max-w-5xl flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Edit Workstation</p>
                <h3 id="workstation-edit-title" class="mt-2 text-2xl font-semibold text-slate-900">Update workstation details</h3>
                <p id="workstation-edit-subtitle" class="mt-2 text-sm text-slate-500">Adjust workstation information and settings without leaving the table.</p>
            </div>
            <button id="workstation-edit-close" type="button" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <div class="shrink-0 border-b border-slate-200 px-6 py-4">
            <div id="workstation-edit-tabs" class="flex flex-wrap gap-2"></div>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6">
            <div id="workstation-edit-loading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                Loading workstation settings...
            </div>
            <div id="workstation-edit-error" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700"></div>
            <div id="workstation-edit-form" class="hidden"></div>
        </div>

        <div class="flex shrink-0 justify-end gap-3 border-t border-slate-200 px-6 py-5">
            <button
                id="workstation-edit-cancel"
                type="button"
                class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                Cancel
            </button>
            <button
                id="workstation-edit-save"
                type="button"
                class="inline-flex h-11 items-center gap-2 rounded-2xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60">
                <i data-lucide="save" class="h-4 w-4"></i>
                <span id="workstation-edit-save-label">Save Changes</span>
            </button>
        </div>
    </div>
</div>

<div id="workstation-delete-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="w-full max-w-lg rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="border-b border-slate-200 px-6 py-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-400">Delete Workstation</p>
            <h3 class="mt-2 text-2xl font-semibold text-slate-900">Delete this workstation?</h3>
            <p class="mt-3 text-sm text-slate-500">
                This action will permanently remove <span id="workstation-delete-name" class="font-semibold text-slate-700"></span>.
                Workstations that still have displays attached cannot be deleted.
            </p>
        </div>

        <div class="flex justify-end gap-3 px-6 py-5">
            <button
                id="workstation-delete-cancel"
                type="button"
                class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                Cancel
            </button>
            <button
                id="workstation-delete-confirm"
                type="button"
                class="inline-flex h-11 items-center rounded-2xl bg-rose-500 px-4 text-sm font-semibold text-white transition hover:bg-rose-400 disabled:cursor-not-allowed disabled:opacity-60">
                Delete Workstation
            </button>
        </div>
    </div>
</div>

<script id="workstation-filters-data" type="application/json">@json($filters)</script>
<script>
(function () {
    const canManageWorkstations = @json($canManageWorkstations);
    let initialized = false;
    const state = {
        config: { canChooseFacility: false, facilities: [], workgroupsByFacility: {}, selectedFacilityId: '', selectedWorkgroupId: '' },
        selectedFacilityId: '',
        selectedWorkgroupId: '',
        defaultStatus: @json($initialWorkstationStatus),
        selectedStatus: @json($initialWorkstationStatus),
        facilitySearch: '',
        workgroupSearch: '',
        activeDropdown: null,
        actionTarget: null,
        deleteTarget: null,
        grid: null,
        edit: {
            id: null,
            meta: null,
            settings: null,
            options: {},
            tab: 'application',
            saving: false,
        },
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
            state.config = JSON.parse(document.getElementById('workstation-filters-data')?.textContent || '{}');
        } catch (error) {
            state.config = { canChooseFacility: false, facilities: [], workgroupsByFacility: {}, selectedFacilityId: '', selectedWorkgroupId: '' };
        }

        state.selectedFacilityId = state.config.selectedFacilityId || '';
        state.selectedWorkgroupId = state.config.selectedWorkgroupId || '';

        bindElements();
        bindEvents();
        renderFilters();
        initGrid();
        window.workstationsPage = {
            toggleActionMenu,
            openQuickEdit: openQuickEditAction,
            openQuickDelete: openQuickDeleteAction,
        };
        window.lucide?.createIcons();
    }

    function bindElements() {
        els.facilityTrigger = document.getElementById('facility-filter-trigger');
        els.facilityLabel = document.getElementById('facility-filter-label');
        els.facilityPanel = document.getElementById('facility-filter-panel');
        els.facilitySearch = document.getElementById('facility-filter-search');
        els.facilityHint = document.getElementById('facility-filter-hint');
        els.facilityOptions = document.getElementById('facility-filter-options');

        els.workgroupTrigger = document.getElementById('workgroup-filter-trigger');
        els.workgroupLabel = document.getElementById('workgroup-filter-label');
        els.workgroupPanel = document.getElementById('workgroup-filter-panel');
        els.workgroupSearch = document.getElementById('workgroup-filter-search');
        els.workgroupHint = document.getElementById('workgroup-filter-hint');
        els.workgroupOptions = document.getElementById('workgroup-filter-options');
        els.statusButtons = [
            document.getElementById('workstation-status-all'),
            document.getElementById('workstation-status-ok'),
            document.getElementById('workstation-status-failed'),
        ].filter(Boolean);

        els.resetFilters = document.getElementById('reset-workstation-filters');
        els.grid = document.getElementById('workstations-grid');

        els.actionOverlay = document.getElementById('workstation-action-overlay');
        els.actionMenu = document.getElementById('workstation-action-menu');
        els.actionEdit = document.getElementById('workstation-action-edit');
        els.actionDelete = document.getElementById('workstation-action-delete');

        els.editModal = document.getElementById('workstation-edit-modal');
        els.editClose = document.getElementById('workstation-edit-close');
        els.editCancel = document.getElementById('workstation-edit-cancel');
        els.editSave = document.getElementById('workstation-edit-save');
        els.editSaveLabel = document.getElementById('workstation-edit-save-label');
        els.editTitle = document.getElementById('workstation-edit-title');
        els.editSubtitle = document.getElementById('workstation-edit-subtitle');
        els.editTabs = document.getElementById('workstation-edit-tabs');
        els.editLoading = document.getElementById('workstation-edit-loading');
        els.editError = document.getElementById('workstation-edit-error');
        els.editForm = document.getElementById('workstation-edit-form');

        els.deleteModal = document.getElementById('workstation-delete-modal');
        els.deleteName = document.getElementById('workstation-delete-name');
        els.deleteCancel = document.getElementById('workstation-delete-cancel');
        els.deleteConfirm = document.getElementById('workstation-delete-confirm');
    }

    function bindEvents() {
        els.facilityTrigger?.addEventListener('click', () => toggleDropdown('facility'));
        els.workgroupTrigger?.addEventListener('click', () => toggleDropdown('workgroup'));
        els.facilitySearch?.addEventListener('input', (event) => {
            state.facilitySearch = event.target.value || '';
            renderFacilityOptions();
        });
        els.workgroupSearch?.addEventListener('input', (event) => {
            state.workgroupSearch = event.target.value || '';
            renderWorkgroupOptions();
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
            if (state.activeDropdown === 'workgroup' && !els.workgroupPanel.contains(event.target) && !els.workgroupTrigger.contains(event.target)) {
                closeDropdown();
            }
            if (els.actionOverlay && !els.actionMenu.contains(event.target)) {
                closeActionMenu();
            }
        });

        els.actionOverlay?.addEventListener('click', closeActionMenu);
        els.actionEdit?.addEventListener('click', openQuickEditAction);
        els.actionDelete?.addEventListener('click', openQuickDeleteAction);

        els.editClose?.addEventListener('click', closeEditModal);
        els.editCancel?.addEventListener('click', closeEditModal);
        els.editSave?.addEventListener('click', saveEditModal);
        els.editModal?.addEventListener('click', (event) => {
            if (event.target === els.editModal) {
                closeEditModal();
            }
        });

        els.deleteCancel?.addEventListener('click', closeDeleteModal);
        els.deleteConfirm?.addEventListener('click', confirmDelete);
        els.deleteModal?.addEventListener('click', (event) => {
            if (event.target === els.deleteModal) {
                closeDeleteModal();
            }
        });
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function getFacilityOptions() {
        return Array.isArray(state.config.facilities) ? state.config.facilities : [];
    }

    function getWorkgroupOptions() {
        if (!state.selectedFacilityId) {
            return [];
        }

        const options = state.config.workgroupsByFacility?.[String(state.selectedFacilityId)] || [];
        return Array.isArray(options) ? options : [];
    }

    function findOptionLabel(options, value, fallback) {
        const match = options.find((item) => String(item.id) === String(value));
        return match?.name || fallback;
    }

    function renderFilters() {
        const facilities = getFacilityOptions();
        const workgroups = getWorkgroupOptions();

        els.facilityTrigger.disabled = !state.config.canChooseFacility && facilities.length <= 1;
        els.workgroupTrigger.disabled = !workgroups.length;

        els.facilityLabel.textContent = state.selectedFacilityId
            ? findOptionLabel(facilities, state.selectedFacilityId, 'Select facility')
            : 'All facilities';
        els.workgroupLabel.textContent = state.selectedWorkgroupId
            ? findOptionLabel(workgroups, state.selectedWorkgroupId, 'Select workgroup')
            : 'All workgroups';

        renderFacilityOptions();
        renderWorkgroupOptions();
        renderStatusFilter();
    }

    function renderStatusFilter() {
        els.statusButtons.forEach((button) => {
            const status = button.dataset.status || '';
            const active = status === (state.selectedStatus || '');

            const activeClass = status === 'ok'
                ? 'rounded-[0.9rem] bg-white px-3 text-sm font-semibold text-emerald-700 shadow-[0_10px_24px_-16px_rgba(16,185,129,0.5)] ring-1 ring-emerald-200 transition'
                : (status === 'failed'
                    ? 'rounded-[0.9rem] bg-white px-3 text-sm font-semibold text-rose-700 shadow-[0_10px_24px_-16px_rgba(244,63,94,0.45)] ring-1 ring-rose-200 transition'
                    : 'rounded-[0.9rem] bg-white px-3 text-sm font-semibold text-sky-700 shadow-[0_10px_24px_-16px_rgba(14,165,233,0.45)] ring-1 ring-sky-200 transition');

            const inactiveClass = status === 'ok'
                ? 'rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition hover:bg-white/95 hover:text-emerald-700'
                : (status === 'failed'
                    ? 'rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition hover:bg-white/95 hover:text-rose-700'
                    : 'rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition hover:bg-white/95 hover:text-sky-700');

            button.className = active ? activeClass : inactiveClass;
        });

        window.lucide?.createIcons();
    }

    function renderFacilityOptions() {
        const facilities = getFacilityOptions();
        const query = state.facilitySearch.trim().toLowerCase();
        let options = facilities.filter((item) => item.name.toLowerCase().includes(query));
        if (state.config.canChooseFacility) {
            options = [{ id: '', name: 'All facilities' }, ...options];
        }

        els.facilityHint.textContent = options.length
            ? `${options.length} option${options.length === 1 ? '' : 's'}`
            : 'No options found';

        els.facilityOptions.innerHTML = options.length
            ? options.map((item) => `
                <button
                    type="button"
                    data-id="${String(item.id)}"
                    class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm ${String(item.id) === String(state.selectedFacilityId) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-sky-50 hover:text-sky-700'}">
                    ${Perfectlum.escapeHtml(item.name)}
                </button>
            `).join('')
            : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div>`;

        els.facilityOptions.querySelectorAll('button[data-id]').forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedFacilityId = button.dataset.id || '';
                state.selectedWorkgroupId = '';
                state.facilitySearch = '';
                if (els.facilitySearch) {
                    els.facilitySearch.value = '';
                }
                closeDropdown();
                renderFilters();
                reloadGrid();
            });
        });
    }

    function renderWorkgroupOptions() {
        const workgroups = getWorkgroupOptions();
        const query = state.workgroupSearch.trim().toLowerCase();
        const options = [{ id: '', name: 'All workgroups' }, ...workgroups.filter((item) => item.name.toLowerCase().includes(query))];

        els.workgroupHint.textContent = options.length
            ? `${options.length} option${options.length === 1 ? '' : 's'}`
            : 'No options found';

        els.workgroupOptions.innerHTML = options.length
            ? options.map((item) => `
                <button
                    type="button"
                    data-id="${String(item.id)}"
                    class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm ${String(item.id) === String(state.selectedWorkgroupId) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-sky-50 hover:text-sky-700'}">
                    ${Perfectlum.escapeHtml(item.name)}
                </button>
            `).join('')
            : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div>`;

        els.workgroupOptions.querySelectorAll('button[data-id]').forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedWorkgroupId = button.dataset.id || '';
                state.workgroupSearch = '';
                if (els.workgroupSearch) {
                    els.workgroupSearch.value = '';
                }
                closeDropdown();
                renderFilters();
                reloadGrid();
            });
        });
    }

    function toggleDropdown(type) {
        if (type === 'facility' && els.facilityTrigger.disabled) return;
        if (type === 'workgroup' && els.workgroupTrigger.disabled) return;

        state.activeDropdown = state.activeDropdown === type ? null : type;
        els.facilityPanel.classList.toggle('hidden', state.activeDropdown !== 'facility');
        els.workgroupPanel.classList.toggle('hidden', state.activeDropdown !== 'workgroup');

        if (state.activeDropdown === 'facility') {
            els.facilitySearch?.focus();
        }

        if (state.activeDropdown === 'workgroup') {
            els.workgroupSearch?.focus();
        }
    }

    function closeDropdown() {
        state.activeDropdown = null;
        els.facilityPanel.classList.add('hidden');
        els.workgroupPanel.classList.add('hidden');
    }

    function resetFilters() {
        state.selectedFacilityId = state.config.canChooseFacility ? '' : (getFacilityOptions()[0] ? String(getFacilityOptions()[0].id) : '');
        state.selectedWorkgroupId = '';
        state.selectedStatus = state.defaultStatus || '';
        state.facilitySearch = '';
        state.workgroupSearch = '';
        if (els.facilitySearch) els.facilitySearch.value = '';
        if (els.workgroupSearch) els.workgroupSearch.value = '';
        closeDropdown();
        renderFilters();
        reloadGrid();
    }

    function buildGridUrl(extra = {}) {
        return Perfectlum.buildServerUrl('/api/workstations', {
            facility_id: state.selectedFacilityId || '',
            workgroup_id: state.selectedWorkgroupId || '',
            type: state.selectedStatus || '',
            ...extra,
        });
    }

    function workstationMatchesSelectedStatus(item) {
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

    function initGrid() {
        if (!els.grid) return;
        if (state.grid) return;

        state.grid = Perfectlum.createGrid(els.grid, {
            columns: [
                {
                    name: 'Name',
                    formatter: (c) => gridjs.html(`
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex h-2.5 w-2.5 shrink-0 rounded-full ${Number(c.failedDisplaysCount || 0) > 0 ? 'bg-rose-500 shadow-[0_0_0_4px_rgba(244,63,94,0.12)]' : (Number(c.okDisplaysCount || 0) > 0 ? 'bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.12)]' : 'bg-slate-300 shadow-[0_0_0_4px_rgba(148,163,184,0.12)]')}"></span>
                            <div class="min-w-0">
                                <button
                                    type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'workstation',id:${c.id}}}))"
                                    class="cursor-pointer font-medium text-sky-600 transition hover:text-sky-700 hover:underline">
                                    ${Perfectlum.escapeHtml(c.name)}
                                </button>
                                ${Number(c.failedDisplaysCount || 0) > 0
                                    ? `<p class="mt-1 text-[11px] font-medium text-rose-600">${Perfectlum.escapeHtml(String(c.failedDisplaysCount))} display${Number(c.failedDisplaysCount) === 1 ? '' : 's'} need attention</p>`
                                    : ''}
                            </div>
                        </div>
                    `),
                },
                {
                    name: 'Workgroup',
                    formatter: (c) => !c.wgName || c.wgName === '-'
                        ? '-'
                        : gridjs.html(`<button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'workgroup',id:${c.wgId}}}))" class="cursor-pointer text-gray-600 transition hover:text-sky-500 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(c.wgName)}</button>`),
                },
                {
                    name: 'Facility',
                    formatter: (c) => !c.facName || c.facName === '-'
                        ? '-'
                        : gridjs.html(`<button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'facility',id:${c.facId}}}))" class="cursor-pointer text-gray-600 transition hover:text-sky-500 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(c.facName)}</button>`),
                },
                { name: 'Sleep Time', formatter: (c) => gridjs.html(`<span class="text-gray-600 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(c)}</span>`) },
                { name: 'Displays', sort: false, formatter: (c) => gridjs.html(`<span class="font-semibold text-gray-700 group-[.theme-chroma]:text-gray-200">${Perfectlum.escapeHtml(c)}</span>`) },
                { name: 'Last Connected', sort: false, formatter: (c) => gridjs.html(`<span class="text-gray-600 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(c)}</span>`) },
                {
                    name: 'Actions',
                    sort: false,
                    width: '112px',
                    formatter: (c) => !canManageWorkstations ? '' : gridjs.html(`
                        <div class="flex justify-center">
                            <button
                                type="button"
                                onclick='window.workstationsPage && window.workstationsPage.toggleActionMenu(event, ${c.id}, ${JSON.stringify(c.name)}, ${c.facId ?? "null"})'
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2"/><circle cx="12" cy="5" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                            </button>
                        </div>`),
                },
            ],
            server: {
                url: buildGridUrl(),
                then: d => (d.data || []).filter(workstationMatchesSelectedStatus).map(r => [
                    { id: r.id, name: r.name, displayHealth: r.displayHealth, okDisplaysCount: r.okDisplaysCount, failedDisplaysCount: r.failedDisplaysCount, displaysCount: r.displaysCount },
                    { wgId: r.wgId, wgName: r.wgName },
                    { facId: r.facId, facName: r.facName },
                    r.sleepTime,
                    r.displaysCount,
                    r.lastConnected,
                    { id: r.id, name: r.name, facId: r.facId },
                ]),
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
            language: { search: { placeholder: 'Search workstations...' } },
        });
    }

    function reloadGrid() {
        closeActionMenu();
        state.grid = null;
        if (!els.grid) {
            initGrid();
            return;
        }

        Perfectlum.remountGrid('workstations-grid', (freshGrid) => {
            els.grid = freshGrid || document.getElementById('workstations-grid');
            state.grid = null;
            initGrid();
        });
    }

    function toggleActionMenu(event, id, name, facilityId) {
        if (!canManageWorkstations) return;
        event.preventDefault();
        event.stopPropagation();

        const rect = event.currentTarget.getBoundingClientRect();
        const nextOpen = !(state.actionTarget && state.actionTarget.id === id && !els.actionMenu.classList.contains('hidden'));
        state.actionTarget = nextOpen ? { id, name, facilityId } : null;

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

    function openQuickEditAction(event) {
        if (!canManageWorkstations) return;
        event?.preventDefault?.();
        event?.stopPropagation?.();
        if (!state.actionTarget?.id) return;
        openEditModal(state.actionTarget.id);
    }

    function openQuickDeleteAction(event) {
        if (!canManageWorkstations) return;
        event?.preventDefault?.();
        event?.stopPropagation?.();
        if (!state.actionTarget?.id) return;
        openDeleteModal(state.actionTarget.id, state.actionTarget.name);
    }

    function boolValue(value) {
        return value === true || value === 'true' || value === 1 || value === '1';
    }

    function parseOptionList(raw, preferredOrder = []) {
        let parsed = raw;
        if (typeof parsed === 'string') {
            try {
                parsed = JSON.parse(parsed);
            } catch (error) {
                parsed = [];
            }
        }

        let options = [];
        if (Array.isArray(parsed)) {
            options = parsed.map((item) => ({
                value: String(item.value ?? item.id ?? item.key ?? item),
                label: String(item.label ?? item.name ?? item.value ?? item),
            }));
        } else if (parsed && typeof parsed === 'object') {
            options = Object.entries(parsed).map(([value, label]) => ({
                value: String(value),
                label: String(label),
            }));
        }

        options.sort((a, b) => a.label.localeCompare(b.label, undefined, { sensitivity: 'base' }));

        if (preferredOrder.length) {
            options.sort((a, b) => {
                const aIndex = preferredOrder.indexOf(a.value);
                const bIndex = preferredOrder.indexOf(b.value);
                if (aIndex !== -1 || bIndex !== -1) {
                    return (aIndex === -1 ? 999 : aIndex) - (bIndex === -1 ? 999 : bIndex);
                }
                return a.label.localeCompare(b.label, undefined, { sensitivity: 'base' });
            });
        }

        return options;
    }

    function inputValue(name, fallback = '') {
        return Perfectlum.escapeHtml(String(state.edit.settings?.[name] ?? fallback ?? ''));
    }

    function checkedAttr(name) {
        return boolValue(state.edit.settings?.[name]) ? 'checked' : '';
    }

    function selectOptionsHtml(name, placeholder = 'Select an option', preferredOrder = []) {
        const current = String(state.edit.settings?.[name] ?? '');
        const options = parseOptionList(state.edit.options?.[name], preferredOrder);
        const placeholderOption = `<option value="">${Perfectlum.escapeHtml(placeholder)}</option>`;
        return placeholderOption + options.map((option) => `
            <option value="${Perfectlum.escapeHtml(option.value)}" ${option.value === current ? 'selected' : ''}>
                ${Perfectlum.escapeHtml(option.label)}
            </option>
        `).join('');
    }

    function renderEditTabs() {
        const tabs = [
            { key: 'application', label: 'Application' },
            { key: 'display-calibration', label: 'Display Calibration' },
            { key: 'quality-assurance', label: 'Quality Assurance' },
            { key: 'location', label: 'Location' },
        ];

        els.editTabs.innerHTML = tabs.map((tab) => `
            <button
                type="button"
                data-tab="${tab.key}"
                class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition ${
                    state.edit.tab === tab.key
                        ? 'border-sky-500 bg-sky-500 text-white'
                        : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900'
                }">
                ${tab.label}
            </button>
        `).join('');

        els.editTabs.querySelectorAll('button[data-tab]').forEach((button) => {
            button.addEventListener('click', () => {
                captureEditFormState();
                state.edit.tab = button.dataset.tab;
                renderEditModal();
            });
        });
    }

    function renderApplicationTab() {
        const canChangeWorkgroup = !!state.edit.meta?.permissions?.changeWorkgroup;
        return `
            <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="mb-5 grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Workstation Name</span>
                            <input name="name" value="${inputValue('name', state.edit.meta?.name || '')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Workgroup</span>
                            <select name="workgroup_id" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" ${canChangeWorkgroup ? '' : 'disabled'}>
                                ${selectOptionsHtml('workgroup_id', 'Select workgroup')}
                            </select>
                            ${canChangeWorkgroup ? '' : '<p class="text-xs text-slate-400">Only super users can change workgroup.</p>'}
                        </label>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Units of Length</span>
                            <select name="units" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('units', 'Select length unit')}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Units of Luminance</span>
                            <select name="LumUnits" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('LumUnits', 'Select luminance unit')}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Veiling Luminance</span>
                            <input name="AmbientLight" value="${inputValue('AmbientLight')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Ambient Conditions Stable</span>
                            <select name="AmbientStable" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('AmbientStable', 'Select value', ['no', 'yes', '0', '1'])}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Energy Save Start</span>
                            <input name="StartEnergySaveMode" value="${inputValue('StartEnergySaveMode')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Energy Save End</span>
                            <input name="EndEnergySaveMode" value="${inputValue('EndEnergySaveMode')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                    </div>
                    <label class="mt-5 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700">
                        <input name="PutDisplaysToEnergySaveMode" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500" ${checkedAttr('PutDisplaysToEnergySaveMode')}>
                        Enable Display Energy Save Mode
                    </label>
                </div>
            </div>
        `;
    }

    function renderDisplayCalibrationTab() {
        return `
            <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Preset</span>
                            <select name="CalibrationPresents" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('CalibrationPresents', 'Select preset')}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Luminance Response</span>
                            <select name="CalibrationType" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('CalibrationType', 'Select response')}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Color Temperature</span>
                            <select name="ColorTemperatureAdjustment" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('ColorTemperatureAdjustment_extcombo', 'Select temperature')}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Max Luminance</span>
                            <select name="WhiteLevel" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('WhiteLevel_u_extcombo', 'Select max luminance')}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Black Level</span>
                            <input name="BlackLevel" value="${inputValue('BlackLevel')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Gamut</span>
                            <select name="gamut_name" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('gamut_name', 'Select gamut')}
                            </select>
                        </label>
                    </div>
                    <label class="mt-5 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700">
                        <input name="CreateICCICMProfile" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500" ${checkedAttr('CreateICCICMProfile')}>
                        Create Display ICC Profile
                    </label>
                </div>
            </div>
        `;
    }

    function renderQualityAssuranceTab() {
        return `
            <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Regulation</span>
                            <select id="workstation-edit-regulation" name="UsedRegulation" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('UsedRegulation', 'Select regulation')}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Display Category</span>
                            <select id="workstation-edit-classification" name="UsedClassification" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('UsedClassification', 'Select category')}
                            </select>
                        </label>
                        <label class="space-y-2 md:col-span-2">
                            <span class="text-sm font-semibold text-slate-700">Body Region</span>
                            <input name="bodyRegion" value="${inputValue('bodyRegion')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                    </div>
                    <label class="mt-5 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700">
                        <input name="AutoDailyTests" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500" ${checkedAttr('AutoDailyTests')}>
                        Start daily tests automatically
                    </label>
                </div>
            </div>
        `;
    }

    function renderLocationTab() {
        return `
            <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Facility Label</span>
                            <input name="Facility" value="${inputValue('Facility')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Department</span>
                            <input name="Department" value="${inputValue('Department')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Room</span>
                            <input name="Room" value="${inputValue('Room')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Responsible Person</span>
                            <input name="ResponsiblePersonName" value="${inputValue('ResponsiblePersonName')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Address</span>
                            <input name="ResponsiblePersonAddress" value="${inputValue('ResponsiblePersonAddress')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">City</span>
                            <input name="ResponsiblePersonCity" value="${inputValue('ResponsiblePersonCity')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Email</span>
                            <input name="ResponsiblePersonEmail" value="${inputValue('ResponsiblePersonEmail')}" type="email" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Phone Number</span>
                            <input name="ResponsiblePersonPhoneNumber" value="${inputValue('ResponsiblePersonPhoneNumber')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                    </div>
                </div>
            </div>
        `;
    }

    function renderEditFormBody() {
        switch (state.edit.tab) {
            case 'display-calibration':
                return renderDisplayCalibrationTab();
            case 'quality-assurance':
                return renderQualityAssuranceTab();
            case 'location':
                return renderLocationTab();
            default:
                return renderApplicationTab();
        }
    }

    function bindEditFormInputs() {
        const form = els.editForm.querySelector('form');
        if (!form) return;

        form.querySelectorAll('input, select, textarea').forEach((input) => {
            const eventName = input.type === 'checkbox' || input.tagName === 'SELECT' ? 'change' : 'input';
            input.addEventListener(eventName, async (event) => {
                const target = event.currentTarget;
                state.edit.settings[target.name] = target.type === 'checkbox' ? target.checked : target.value;
                if (target.id === 'workstation-edit-regulation') {
                    await refreshClassificationOptions(target.value);
                }
            });
        });
    }

    function renderEditModal() {
        renderEditTabs();
        els.editForm.classList.remove('hidden');
        els.editForm.innerHTML = `
            <form id="workstation-quick-edit-form" class="space-y-5">
                ${renderEditFormBody()}
            </form>
        `;
        els.editSaveLabel.textContent = state.edit.saving ? 'Saving…' : 'Save Changes';
        bindEditFormInputs();
        window.lucide?.createIcons();
    }

    async function refreshClassificationOptions(regulation) {
        if (!state.edit.id) return;
        try {
            const items = await Perfectlum.request(`/app-settings/get/categories?id=ws-${state.edit.id}&regulation=${encodeURIComponent(regulation || '')}`);
            state.edit.options.UsedClassification = items.reduce((acc, item) => {
                acc[item.key] = item.value;
                return acc;
            }, {});
            renderEditModal();
        } catch (error) {
            // keep current options
        }
    }

    function captureEditFormState() {
        const form = els.editForm.querySelector('form');
        if (!form) return;
        form.querySelectorAll('input[name], select[name], textarea[name]').forEach((input) => {
            state.edit.settings[input.name] = input.type === 'checkbox' ? input.checked : input.value;
        });
    }

    async function openEditModal(id) {
        closeActionMenu();
        state.edit = {
            id,
            meta: null,
            settings: null,
            options: {},
            tab: 'application',
            saving: false,
        };
        els.editModal.classList.remove('hidden');
        els.editModal.classList.add('flex');
        els.editLoading.classList.remove('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editForm.classList.add('hidden');
        els.editForm.innerHTML = '';
        els.editTabs.innerHTML = '';
        els.editTitle.textContent = 'Update workstation details';
        els.editSubtitle.textContent = 'Adjust workstation information and settings without leaving the table.';

        try {
            const [meta, payload] = await Promise.all([
                Perfectlum.request(`/api/workstation-modal/${id}`),
                Perfectlum.request(`/app-settings/ws-${id}`),
            ]);
            state.edit.meta = meta;
            state.edit.settings = {
                ...payload.data,
                name: payload.data?.name ?? meta.name ?? '',
                workgroup_id: String(payload.data?.workgroup_id ?? meta.workgroup?.id ?? ''),
                PutDisplaysToEnergySaveMode: boolValue(payload.data?.PutDisplaysToEnergySaveMode),
                CreateICCICMProfile: boolValue(payload.data?.CreateICCICMProfile),
                AutoDailyTests: boolValue(payload.data?.AutoDailyTests),
            };
            state.edit.options = payload.options || {};
            els.editTitle.textContent = meta.name || 'Update workstation details';
            els.editSubtitle.textContent = `${meta.facility?.name || '-'} / ${meta.workgroup?.name || '-'} / ${meta.name || '-'}`;
            els.editLoading.classList.add('hidden');
            renderEditModal();
        } catch (error) {
            els.editLoading.classList.add('hidden');
            els.editError.textContent = error.message || 'Unable to load workstation settings.';
            els.editError.classList.remove('hidden');
        }
    }

    async function saveEditModal() {
        if (!state.edit.id || state.edit.saving) return;
        captureEditFormState();
        state.edit.saving = true;
        els.editSave.disabled = true;
        els.editSaveLabel.textContent = 'Saving…';
        els.editError.classList.add('hidden');

        const formData = new FormData();
        formData.append('_token', csrfToken());
        let endpoint = `/app-settings/save/app/ws-${state.edit.id}`;

        if (state.edit.tab === 'application') {
            ['name', 'workgroup_id', 'units', 'LumUnits', 'AmbientLight', 'AmbientStable', 'StartEnergySaveMode', 'EndEnergySaveMode']
                .forEach((key) => formData.append(key, state.edit.settings[key] ?? ''));
            formData.append('PutDisplaysToEnergySaveMode', state.edit.settings.PutDisplaysToEnergySaveMode ? '1' : '0');
        } else if (state.edit.tab === 'display-calibration') {
            endpoint = `/app-settings/save/dc/ws-${state.edit.id}`;
            ['CalibrationPresents', 'CalibrationType', 'ColorTemperatureAdjustment', 'WhiteLevel', 'BlackLevel', 'gamut_name']
                .forEach((key) => formData.append(key, state.edit.settings[key] ?? ''));
            formData.append('CreateICCICMProfile', state.edit.settings.CreateICCICMProfile ? '1' : '0');
        } else if (state.edit.tab === 'quality-assurance') {
            endpoint = `/app-settings/save/qa/ws-${state.edit.id}`;
            ['UsedRegulation', 'UsedClassification', 'bodyRegion'].forEach((key) => formData.append(key, state.edit.settings[key] ?? ''));
            formData.append('AutoDailyTests', state.edit.settings.AutoDailyTests ? '1' : '0');
        } else {
            endpoint = `/app-settings/save/location/ws-${state.edit.id}`;
            ['Facility', 'Department', 'Room', 'ResponsiblePersonName', 'ResponsiblePersonCity', 'ResponsiblePersonAddress', 'ResponsiblePersonEmail', 'ResponsiblePersonPhoneNumber']
                .forEach((key) => formData.append(key, state.edit.settings[key] ?? ''));
        }

        try {
            await Perfectlum.postForm(endpoint, formData);
            closeEditModal();
            reloadGrid();
        } catch (error) {
            els.editError.textContent = error.message || 'Unable to save workstation.';
            els.editError.classList.remove('hidden');
        } finally {
            state.edit.saving = false;
            els.editSave.disabled = false;
            els.editSaveLabel.textContent = 'Save Changes';
        }
    }

    function closeEditModal() {
        els.editModal.classList.add('hidden');
        els.editModal.classList.remove('flex');
        els.editLoading.classList.add('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editForm.innerHTML = '';
        els.editForm.classList.add('hidden');
        els.editTabs.innerHTML = '';
        els.editSave.disabled = false;
        els.editSaveLabel.textContent = 'Save Changes';
        state.edit = {
            id: null,
            meta: null,
            settings: null,
            options: {},
            tab: 'application',
            saving: false,
        };
    }

    function openDeleteModal(id, name) {
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
        els.deleteConfirm.textContent = 'Delete Workstation';
    }

    async function confirmDelete() {
        if (!state.deleteTarget?.id || els.deleteConfirm.disabled) return;
        els.deleteConfirm.disabled = true;
        els.deleteConfirm.textContent = 'Deleting...';

        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', state.deleteTarget.id);
            const payload = await Perfectlum.postForm('/delete-workstation', formData);
            if (!payload.success) {
                throw new Error(payload.msg || 'Unable to delete workstation.');
            }
            closeDeleteModal();
            reloadGrid();
        } catch (error) {
            window.alert(error.message || 'Unable to delete workstation.');
            els.deleteConfirm.disabled = false;
            els.deleteConfirm.textContent = 'Delete Workstation';
        }
    }

    window.closeWorkstationPanel = closeEditModal;
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
</script>

@include('common.navigations.footer')
