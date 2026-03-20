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
    ];
@endphp

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

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,280px)_1fr]">
            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Status') }}</label>
                <div class="grid h-12 grid-cols-3 rounded-2xl border border-slate-200 bg-white p-1">
                    <button
                        id="facility-status-all"
                        type="button"
                        data-status=""
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="layers-3" class="h-4 w-4"></i>
                            <span>{{ __('All') }}</span>
                        </span>
                    </button>
                    <button
                        id="facility-status-ok"
                        type="button"
                        data-status="ok"
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="badge-check" class="h-4 w-4"></i>
                            <span>{{ __('OK') }}</span>
                        </span>
                    </button>
                    <button
                        id="facility-status-failed"
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
                    id="reset-facility-filters"
                    type="button"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    {{ __('Reset Filters') }}
                </button>
            </div>
        </div>
    </section>

    <x-data-table id="facilities-grid" class="mb-10 workstation-table-shell" />
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

<script>
(function () {
    const text = @json($facilityText);
    const canManageFacilities = @json($canManageFacilities);
    const canDeleteFacilities = @json($canDeleteFacilities);
    let initialized = false;
    const state = {
        actionTarget: null,
        deleteTarget: null,
        grid: null,
        defaultStatus: @json($initialFacilityStatus),
        selectedStatus: @json($initialFacilityStatus),
    };

    const els = {};

    function init() {
        if (initialized) return;
        if (!window.Perfectlum || !window.gridjs) {
            window.setTimeout(init, 50);
            return;
        }

        initialized = true;
        bindElements();
        bindEvents();
        renderStatusFilter();
        initGrid();
        window.facilitiesPage = { toggleActionMenu };
        window.lucide?.createIcons();
    }

    function bindElements() {
        els.createButton = document.getElementById('create-facility-button');
        els.statusButtons = Array.from(document.querySelectorAll('[data-status]'));
        els.resetFilters = document.getElementById('reset-facility-filters');
        els.grid = document.getElementById('facilities-grid');
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
    }

    function bindEvents() {
        els.createButton?.addEventListener('click', () => openEditModal(0));
        els.statusButtons.forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedStatus = button.dataset.status || '';
                renderStatusFilter();
                reloadGrid();
            });
        });
        els.resetFilters?.addEventListener('click', resetFilters);
        document.addEventListener('click', (event) => {
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

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function resetFilters() {
        state.selectedStatus = state.defaultStatus || '';
        renderStatusFilter();
        reloadGrid();
    }

    function buildGridUrl(extra = {}) {
        return Perfectlum.buildServerUrl('/api/facilities', {
            type: state.selectedStatus || '',
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

    function mapRows(d) {
        return (d.data || []).filter(facilityMatchesSelectedStatus).map(r => [
            { id: r.id, name: r.name, okDisplaysCount: r.okDisplaysCount, failedDisplaysCount: r.failedDisplaysCount },
            r.location,
            r.timezone,
            r.workgroupsCount,
            r.usersCount,
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
                                    onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'facility',id:${c.id}}}))"
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
                { name: text.location, formatter: (c) => gridjs.html(`<span class="text-gray-600 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(c)}</span>`) },
                { name: text.timezone, formatter: (c) => gridjs.html(`<span class="text-gray-600 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(c)}</span>`) },
                { name: text.workgroups, sort: false, formatter: (c) => gridjs.html(`<span class="font-semibold text-gray-700 group-[.theme-chroma]:text-gray-200">${Perfectlum.escapeHtml(c)}</span>`) },
                { name: text.users, sort: false, formatter: (c) => gridjs.html(`<span class="font-semibold text-gray-700 group-[.theme-chroma]:text-gray-200">${Perfectlum.escapeHtml(c)}</span>`) },
                { name: text.displaysLabel, sort: false, formatter: (c) => gridjs.html(`<span class="font-semibold text-gray-700 group-[.theme-chroma]:text-gray-200">${Perfectlum.escapeHtml(c)}</span>`) },
                {
                    name: text.actions,
                    sort: false,
                    width: '112px',
                    formatter: (c) => gridjs.html(`
                        <div class="flex justify-center">
                            <button
                                type="button"
                                onclick='window.facilitiesPage && window.facilitiesPage.toggleActionMenu(event, ${c.id}, ${JSON.stringify(c.name)})'
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
                search: { placeholder: text.searchFacilities },
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

        Perfectlum.remountGrid('facilities-grid', (freshGrid) => {
            els.grid = freshGrid || document.getElementById('facilities-grid');
            state.grid = null;
            initGrid();
        });
    }

    function toggleActionMenu(event, id, name) {
        if (!canManageFacilities) return;
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
        els.actionMenu.style.left = `${Math.max(16, rect.right - 208)}px`;
        els.actionMenu.style.top = `${rect.bottom + 10}px`;
        window.lucide?.createIcons();
    }

    function closeActionMenu() {
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
                reloadGrid();
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
            reloadGrid();
        } catch (error) {
            window.alert(error.message || text.unableToDeleteFacility);
            els.deleteConfirm.disabled = false;
            els.deleteConfirm.textContent = text.deleteFacility;
        }
    }

    window.closeFacilityPanel = closeEditModal;
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
</script>

@include('common.navigations.footer')
