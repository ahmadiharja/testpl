@php
    $userText = [
        'allFacilities' => __('All facilities'),
        'searchFacilities' => __('Search facilities...'),
        'resetFilters' => __('Reset Filters'),
        'editUser' => __('Edit User'),
        'deleteUser' => __('Delete User'),
        'updateUserDetails' => __('Update user details'),
        'adjustUserDetails' => __('Adjust account, role, and facility assignment without leaving the table.'),
        'loadingUserForm' => __('Loading user form...'),
        'username' => __('Username'),
        'fullName' => __('Full Name'),
        'email' => __('Email'),
        'userLevel' => __('User Level'),
        'password' => __('Password'),
        'confirmPassword' => __('Confirm Password'),
        'facility' => __('Facility'),
        'enableUserAccount' => __('Enable user account'),
        'cancel' => __('Cancel'),
        'saveChanges' => __('Save Changes'),
        'deleteThisUser' => __('Delete this user?'),
        'unableToLoadUserForm' => __('Unable to load user form.'),
        'createNewUser' => __('Create a new user'),
        'createUserSubtitle' => __('Create a new account and assign its facility scope and role.'),
        'selectUserLevel' => __('Select user level'),
        'selectFacility' => __('Select facility'),
        'searchUsers' => __('Search users...'),
        'active' => __('Active'),
        'disabled' => __('Disabled'),
        'saving' => __('Saving...'),
        'deleting' => __('Deleting...'),
        'unableToSaveUser' => __('Unable to save user.'),
        'unableToDeleteUser' => __('Unable to delete user.'),
        'optionCount' => __('options'),
        'noOptionsFound' => __('No options found'),
    ];
@endphp
@include('common.navigations.header')

<div class="flex flex-col gap-6 pb-8">
    <x-page-header title="{{ __('User Management') }}" description="{{ __('Manage user accounts, facility scope, and role assignments.') }}" icon="users">
        <x-slot name="actions">
            <button
                id="create-user-button"
                type="button"
                class="inline-flex h-12 items-center gap-2 rounded-2xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400">
                <i data-lucide="user-plus" class="h-4 w-4"></i>
                {{ __('Add User') }}
            </button>
        </x-slot>
    </x-page-header>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,240px)_1fr]">
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

            <div class="flex items-end justify-end">
                <button
                    id="reset-user-filters"
                    type="button"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    {{ __('Reset Filters') }}
                </button>
            </div>
        </div>
    </section>

    <x-data-table id="users-grid" class="mb-10 workstation-table-shell" />
</div>

<div id="user-action-overlay" class="pointer-events-none fixed inset-0 z-[1200] hidden">
    <div
        id="user-action-menu"
        class="pointer-events-auto fixed hidden w-52 rounded-2xl border border-slate-200 bg-white p-2 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.35)]">
        <button id="user-action-edit" type="button" class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
            <i data-lucide="pencil-line" class="h-4 w-4"></i>
            {{ __('Edit User') }}
        </button>
        <button id="user-action-delete" type="button" class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
            <i data-lucide="trash-2" class="h-4 w-4"></i>
            {{ __('Delete User') }}
        </button>
    </div>
</div>

<div id="user-edit-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="flex max-h-[88vh] w-full max-w-3xl flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p id="user-edit-kicker" class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Edit User') }}</p>
                <h3 id="user-edit-title" class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Update user details') }}</h3>
                <p id="user-edit-subtitle" class="mt-2 text-sm text-slate-500">{{ __('Adjust account, role, and facility assignment without leaving the table.') }}</p>
            </div>
            <button id="user-edit-close" type="button" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6">
            <div id="user-edit-loading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">{{ __('Loading user form...') }}</div>
            <div id="user-edit-error" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700"></div>
            <form id="user-edit-form" class="hidden space-y-5">
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('Username') }}</span><input id="user-name" name="name" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></label>
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('Full Name') }}</span><input id="user-fullname" name="fullname" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></label>
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('Email') }}</span><input id="user-email" name="email" type="email" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></label>
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('User Level') }}</span><select id="user-role" name="user_level" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></select></label>
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('Password') }}</span><input id="user-password" name="password" type="password" autocomplete="new-password" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></label>
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('Confirm Password') }}</span><input id="user-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></label>
                    <label class="space-y-2 md:col-span-2"><span class="text-sm font-semibold text-slate-700">{{ __('Facility') }}</span><select id="user-facility" name="facility_id" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></select></label>
                </div>

                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700">
                    <input id="user-enabled" name="enabled" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                    {{ __('Enable user account') }}
                </label>

                <input id="user-id" name="id" type="hidden" value="0">
            </form>
        </div>

        <div class="flex shrink-0 justify-end gap-3 border-t border-slate-200 px-6 py-5">
            <button id="user-edit-cancel" type="button" class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">{{ __('Cancel') }}</button>
            <button id="user-edit-save" type="button" class="inline-flex h-11 items-center gap-2 rounded-2xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60">
                <i data-lucide="save" class="h-4 w-4"></i>
                <span id="user-edit-save-label">{{ __('Save Changes') }}</span>
            </button>
        </div>
    </div>
</div>

<div id="user-delete-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="w-full max-w-lg rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="border-b border-slate-200 px-6 py-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-400">{{ __('Delete User') }}</p>
            <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Delete this user?') }}</h3>
            <p class="mt-3 text-sm text-slate-500">{{ __('This action will permanently remove') }} <span id="user-delete-name" class="font-semibold text-slate-700"></span>.</p>
        </div>

        <div class="flex justify-end gap-3 px-6 py-5">
            <button id="user-delete-cancel" type="button" class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">{{ __('Cancel') }}</button>
            <button id="user-delete-confirm" type="button" class="inline-flex h-11 items-center rounded-2xl bg-rose-500 px-4 text-sm font-semibold text-white transition hover:bg-rose-400 disabled:cursor-not-allowed disabled:opacity-60">{{ __('Delete User') }}</button>
        </div>
    </div>
</div>

<script id="users-filters-data" type="application/json">@json($filters)</script>
<script id="users-text" type="application/json">@json($userText)</script>
<script>
(function () {
    const text = JSON.parse(document.getElementById('users-text')?.textContent || '{}');
    let initialized = false;
    const state = {
        config: { canChooseFacility: false, facilities: [], selectedFacilityId: '' },
        selectedFacilityId: '',
        facilitySearch: '',
        activeDropdown: null,
        actionTarget: null,
        deleteTarget: null,
        grid: null,
        edit: { id: 0, loading: false, saving: false, payload: null },
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
            state.config = JSON.parse(document.getElementById('users-filters-data')?.textContent || '{}');
        } catch (error) {
            state.config = { canChooseFacility: false, facilities: [], selectedFacilityId: '' };
        }

        state.selectedFacilityId = state.config.selectedFacilityId || '';

        bindElements();
        bindEvents();
        renderFilters();
        initGrid();
        window.usersPage = { toggleActionMenu };
        window.lucide?.createIcons();
    }

    function bindElements() {
        els.createButton = document.getElementById('create-user-button');
        els.facilityTrigger = document.getElementById('facility-filter-trigger');
        els.facilityLabel = document.getElementById('facility-filter-label');
        els.facilityPanel = document.getElementById('facility-filter-panel');
        els.facilitySearch = document.getElementById('facility-filter-search');
        els.facilityHint = document.getElementById('facility-filter-hint');
        els.facilityOptions = document.getElementById('facility-filter-options');
        els.resetFilters = document.getElementById('reset-user-filters');
        els.grid = document.getElementById('users-grid');

        els.actionOverlay = document.getElementById('user-action-overlay');
        els.actionMenu = document.getElementById('user-action-menu');
        els.actionEdit = document.getElementById('user-action-edit');
        els.actionDelete = document.getElementById('user-action-delete');

        els.editModal = document.getElementById('user-edit-modal');
        els.editClose = document.getElementById('user-edit-close');
        els.editCancel = document.getElementById('user-edit-cancel');
        els.editSave = document.getElementById('user-edit-save');
        els.editSaveLabel = document.getElementById('user-edit-save-label');
        els.editLoading = document.getElementById('user-edit-loading');
        els.editError = document.getElementById('user-edit-error');
        els.editForm = document.getElementById('user-edit-form');
        els.editKicker = document.getElementById('user-edit-kicker');
        els.editTitle = document.getElementById('user-edit-title');
        els.editSubtitle = document.getElementById('user-edit-subtitle');
        els.userId = document.getElementById('user-id');
        els.username = document.getElementById('user-name');
        els.fullname = document.getElementById('user-fullname');
        els.email = document.getElementById('user-email');
        els.password = document.getElementById('user-password');
        els.passwordConfirmation = document.getElementById('user-password-confirmation');
        els.role = document.getElementById('user-role');
        els.facility = document.getElementById('user-facility');
        els.enabled = document.getElementById('user-enabled');

        els.deleteModal = document.getElementById('user-delete-modal');
        els.deleteName = document.getElementById('user-delete-name');
        els.deleteCancel = document.getElementById('user-delete-cancel');
        els.deleteConfirm = document.getElementById('user-delete-confirm');
    }

    function bindEvents() {
        els.createButton?.addEventListener('click', () => openEditModal(0));
        els.facilityTrigger?.addEventListener('click', () => toggleDropdown('facility'));
        els.facilitySearch?.addEventListener('input', (event) => {
            state.facilitySearch = event.target.value || '';
            renderFacilityOptions();
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
        els.editCancel?.addEventListener('click', closeEditModal);
        els.editSave?.addEventListener('click', saveEditModal);
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
    }

    function renderFacilityOptions() {
        const facilities = getFacilityOptions();
        const query = state.facilitySearch.trim().toLowerCase();
        let options = facilities.filter((item) => item.name.toLowerCase().includes(query));
        if (state.config.canChooseFacility) {
            options = [{ id: '', name: 'All facilities' }, ...options];
        }

        els.facilityHint.textContent = options.length
            ? `${options.length} ${text.optionCount}`
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
        state.facilitySearch = '';
        if (els.facilitySearch) els.facilitySearch.value = '';
        closeDropdown();
        renderFilters();
        reloadGrid();
    }

    function buildGridUrl(extra = {}) {
        return Perfectlum.buildServerUrl('/users-list', {
            facility_id: state.selectedFacilityId || '',
            ...extra,
        });
    }

    function mapRows(d) {
        return d.data.map((r) => [
            r.username,
            r.fullname,
            r.email,
            { id: r.facilityId, name: r.facility },
            r.role,
            r.enabled,
            { id: r.id, name: r.fullname || r.username },
        ]);
    }

    function initGrid() {
        if (!els.grid || state.grid) return;

        state.grid = Perfectlum.createGrid(els.grid, {
            columns: [
                { name: text.username, formatter: (c) => gridjs.html(`<span class="font-semibold text-gray-800 group-[.theme-chroma]:text-gray-100">${Perfectlum.escapeHtml(c)}</span>`) },
                { name: text.fullName, formatter: (c) => gridjs.html(`<span class="text-gray-600 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(c || '-')}</span>`) },
                { name: text.email, formatter: (c) => gridjs.html(`<a href="mailto:${Perfectlum.escapeHtml(c)}" class="text-sky-600 hover:underline">${Perfectlum.escapeHtml(c)}</a>`) },
                { name: text.facility, formatter: (c) => !c.name || c.name === '-' ? '-' : gridjs.html(`<span class="text-gray-600 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(c.name)}</span>`) },
                { name: @js(__('Role')), formatter: (c) => gridjs.html(Perfectlum.badge(c, 'info')) },
                { name: @js(__('Status')), formatter: (c) => c ? gridjs.html(Perfectlum.badge(text.active, 'success')) : gridjs.html(Perfectlum.badge(text.disabled, 'danger')) },
                {
                    name: @js(__('Actions')),
                    sort: false,
                    width: '112px',
                    formatter: (c) => gridjs.html(`
                        <div class="flex justify-center">
                            <button
                                type="button"
                                onclick='window.usersPage && window.usersPage.toggleActionMenu(event, ${c.id}, ${JSON.stringify(c.name)})'
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
                server: { url: (_, pg, lim) => buildGridUrl({ page: pg + 1, limit: lim }) },
            },
            search: {
                enabled: true,
                server: { url: (_, kw) => buildGridUrl({ search: kw }) },
            },
            sort: { multiColumn: false },
            language: { search: { placeholder: text.searchUsers } },
        });
    }

    function reloadGrid() {
        closeActionMenu();
        if (!state.grid) {
            initGrid();
            return;
        }

        state.grid.updateConfig({
            server: { url: buildGridUrl(), then: mapRows, total: d => d.total },
            pagination: { enabled: true, limit: 10, server: { url: (_, pg, lim) => buildGridUrl({ page: pg + 1, limit: lim }) } },
            search: { enabled: true, server: { url: (_, kw) => buildGridUrl({ search: kw }) } },
        }).forceRender();
    }

    function toggleActionMenu(event, id, name) {
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

    function fillSelect(select, options, currentValue, placeholder) {
        const html = [
            placeholder ? `<option value="">${Perfectlum.escapeHtml(placeholder)}</option>` : '',
            ...(options || []).map((item) => `<option value="${Perfectlum.escapeHtml(String(item.id))}" ${String(item.id) === String(currentValue ?? '') ? 'selected' : ''}>${Perfectlum.escapeHtml(item.name)}</option>`),
        ].join('');
        select.innerHTML = html;
    }

    async function openEditModal(id) {
        closeActionMenu();
        state.edit.id = id || 0;
        state.edit.payload = null;
        state.edit.loading = true;
        state.edit.saving = false;

        els.editModal.classList.remove('hidden');
        els.editModal.classList.add('flex');
        els.editLoading.classList.remove('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editForm.classList.add('hidden');
        els.editSave.disabled = false;
        els.editSaveLabel.textContent = text.saveChanges;

        try {
            const payload = await Perfectlum.request(`/api/user-modal/${id || ''}`);
            state.edit.payload = payload;

            els.editKicker.textContent = payload.is_existing ? text.editUser : @js(__('Add User'));
            els.editTitle.textContent = payload.is_existing ? (payload.fullname || payload.username || text.updateUserDetails) : text.createNewUser;
            els.editSubtitle.textContent = payload.is_existing
                ? text.adjustUserDetails
                : text.createUserSubtitle;

            els.userId.value = payload.id || 0;
            els.username.value = payload.username || '';
            els.username.readOnly = !!payload.is_existing;
            els.fullname.value = payload.fullname || '';
            els.email.value = payload.email || '';
            els.password.value = '';
            els.passwordConfirmation.value = '';
            fillSelect(els.role, payload.options?.roles || [], payload.user_level, text.selectUserLevel);
            fillSelect(els.facility, payload.options?.facilities || [], payload.facility_id, payload.can_choose_facility ? text.selectFacility : '');
            els.facility.disabled = !payload.can_choose_facility;
            els.enabled.checked = !!payload.enabled;

            els.editForm.classList.remove('hidden');
        } catch (error) {
            els.editError.textContent = error.message || text.unableToLoadUserForm;
            els.editError.classList.remove('hidden');
        } finally {
            state.edit.loading = false;
            els.editLoading.classList.add('hidden');
        }
    }

    async function saveEditModal() {
        if (state.edit.saving || state.edit.loading) return;
        state.edit.saving = true;
        els.editSave.disabled = true;
        els.editSaveLabel.textContent = text.saving;
        els.editError.classList.add('hidden');

        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', els.userId.value || '0');
            formData.append('name', els.username.value || '');
            formData.append('fullname', els.fullname.value || '');
            formData.append('email', els.email.value || '');
            formData.append('password', els.password.value || '');
            formData.append('password_confirmation', els.passwordConfirmation.value || '');
            formData.append('user_level', els.role.value || '');
            formData.append('facility_id', els.facility.value || '');
            if (els.enabled.checked) {
                formData.append('enabled', '1');
            }

            const payload = await Perfectlum.postForm('/api/user-modal/save', formData);
            if (!payload.success) {
                throw new Error(payload.message || text.unableToSaveUser);
            }
            closeEditModal();
            reloadGrid();
        } catch (error) {
            els.editError.textContent = error.message || text.unableToSaveUser;
            els.editError.classList.remove('hidden');
        } finally {
            state.edit.saving = false;
            els.editSave.disabled = false;
            els.editSaveLabel.textContent = text.saveChanges;
        }
    }

    function closeEditModal() {
        els.editModal.classList.add('hidden');
        els.editModal.classList.remove('flex');
        els.editLoading.classList.add('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editForm.classList.add('hidden');
        els.editSave.disabled = false;
        els.editSaveLabel.textContent = text.saveChanges;
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
        els.deleteConfirm.textContent = text.deleteUser;
    }

    async function confirmDelete() {
        if (!state.deleteTarget?.id || els.deleteConfirm.disabled) return;
        els.deleteConfirm.disabled = true;
        els.deleteConfirm.textContent = text.deleting;

        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', state.deleteTarget.id);
            const payload = await Perfectlum.postForm('/delete-user', formData);
            if (!payload.success) {
                throw new Error(payload.msg || text.unableToDeleteUser);
            }
            closeDeleteModal();
            reloadGrid();
        } catch (error) {
            window.alert(error.message || text.unableToDeleteUser);
            els.deleteConfirm.disabled = false;
            els.deleteConfirm.textContent = text.deleteUser;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
</script>

@include('common.navigations.footer')
