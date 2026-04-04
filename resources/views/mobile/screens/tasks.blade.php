@extends('mobile.layouts.app')

@push('head')
    <style>
        @keyframes mobile-task-shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .mobile-task-hero {
            position: relative;
            overflow: hidden;
            padding: 0.95rem;
            border-color: rgba(191, 219, 254, 0.52);
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.12), transparent 34%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
        }

        .mobile-task-switcher {
            display: inline-flex;
            width: 100%;
            gap: 0.35rem;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.8);
            padding: 0.24rem;
        }

        .mobile-task-tab {
            flex: 1 1 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.38rem;
            min-height: 2.35rem;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: #64748b;
            transition: background 160ms ease, color 160ms ease, box-shadow 160ms ease;
        }

        .mobile-task-tab.active {
            background: linear-gradient(180deg, rgba(224, 242, 254, 0.98), rgba(241, 245, 249, 0.94));
            color: #0369a1;
            box-shadow: inset 0 0 0 1px rgba(125, 211, 252, 0.72);
        }

        .mobile-task-kicker {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-task-title {
            margin-top: 0.4rem;
            font-size: 1.2rem;
            font-weight: 760;
            letter-spacing: -0.035em;
            line-height: 1.04;
            color: #0f172a;
        }

        .mobile-task-copy {
            margin-top: 0.45rem;
            font-size: 12.5px;
            line-height: 1.52;
            color: #475569;
        }

        .mobile-task-subcopy {
            margin-top: 0.68rem;
            font-size: 11.5px;
            line-height: 1.48;
            color: #64748b;
        }

        .mobile-task-scan-card {
            display: block;
            padding: 0.82rem 0.84rem;
            transition: background 160ms ease, transform 160ms ease;
        }

        .mobile-task-scan-card:active {
            background: rgba(14, 165, 233, 0.05);
            transform: scale(0.995);
        }

        .mobile-task-scan-card + .mobile-task-scan-card {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mobile-task-scan-title {
            margin-top: 0.32rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 13px;
            font-weight: 650;
            line-height: 1.32;
            color: #0f172a;
        }

        .mobile-task-scan-scope,
        .mobile-task-scan-meta {
            margin-top: 0.24rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 12px;
            line-height: 1.45;
            color: #64748b;
        }

        .mobile-task-scan-meta {
            color: #475569;
        }

        .mobile-task-date-pill {
            flex: 0 0 auto;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(248, 250, 252, 0.96);
            padding: 0.34rem 0.58rem;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
        }

        .mobile-task-skeleton {
            position: relative;
            display: block;
            padding: 0.82rem 0.84rem;
            overflow: hidden;
        }

        .mobile-task-skeleton + .mobile-task-skeleton {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mobile-task-skeleton::after {
            content: '';
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.85), transparent);
            animation: mobile-task-shimmer 1.2s infinite;
        }

        .mobile-task-skeleton-pill,
        .mobile-task-skeleton-line,
        .mobile-task-skeleton-date {
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(226, 232, 240, 0.82), rgba(241, 245, 249, 0.92));
        }

        .mobile-task-skeleton-pill {
            height: 1.18rem;
            width: 4.25rem;
        }

        .mobile-task-skeleton-line {
            height: 0.82rem;
            margin-top: 0.38rem;
        }

        .mobile-task-skeleton-line.title {
            width: 72%;
            height: 0.96rem;
            margin-top: 0.52rem;
        }

        .mobile-task-skeleton-line.scope {
            width: 64%;
        }

        .mobile-task-skeleton-line.meta {
            width: 56%;
        }

        .mobile-task-skeleton-date {
            flex: 0 0 auto;
            width: 4.35rem;
            height: 1.48rem;
        }

        .mobile-task-hero-actions {
            margin-top: 0.8rem;
            display: flex;
            justify-content: flex-start;
        }

        .mobile-task-hero-actions[hidden] {
            display: none;
        }

        .mobile-task-hero-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-height: 2.55rem;
            border-radius: 999px;
            border: 1px solid rgba(56, 189, 248, 0.26);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
            padding: 0.58rem 0.86rem;
            color: #0369a1;
        }

        .mobile-task-hero-button:active {
            transform: translateY(1px);
        }

        .mobile-task-hero-button-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.6rem;
            height: 1.6rem;
            border-radius: 999px;
            background: rgba(14, 165, 233, 0.12);
        }

        .mobile-task-hero-button-copy {
            display: inline-flex;
            align-items: center;
        }

        .mobile-task-hero-button-kicker {
            display: none;
        }

        .mobile-task-hero-button-label {
            font-size: 12px;
            font-weight: 700;
            line-height: 1.1;
            color: #0369a1;
            white-space: nowrap;
        }

        .mobile-task-picker-overlay {
            position: fixed;
            inset: 0;
            z-index: 70;
            background: rgba(15, 23, 42, 0.44);
            backdrop-filter: blur(8px);
        }

        .mobile-task-picker-panel {
            position: fixed;
            inset-inline: 0.7rem;
            bottom: calc(0.8rem + env(safe-area-inset-bottom));
            z-index: 80;
            border-radius: 1.5rem;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 28px 60px rgba(15, 23, 42, 0.22);
            overflow: hidden;
        }

        .mobile-task-picker-shell {
            max-height: min(80vh, 680px);
            overflow-y: auto;
            padding: 1rem;
        }

        .mobile-task-picker-eyebrow {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-task-picker-title {
            margin-top: 0.35rem;
            font-size: 1.05rem;
            font-weight: 760;
            letter-spacing: -0.03em;
            color: #0f172a;
        }

        .mobile-task-picker-copy {
            margin-top: 0.35rem;
            font-size: 12px;
            line-height: 1.55;
            color: #64748b;
        }

        .mobile-task-picker-grid {
            display: grid;
            gap: 0.72rem;
            margin-top: 1rem;
        }

        .mobile-task-picker-field {
            display: grid;
            gap: 0.38rem;
        }

        .mobile-task-picker-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-task-picker-hint {
            margin-top: 0.2rem;
            font-size: 11px;
            line-height: 1.45;
            color: #64748b;
        }

        .mobile-task-picker-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.55rem;
            margin-top: 1rem;
            padding-top: 0.9rem;
            border-top: 1px solid rgba(148, 163, 184, 0.14);
        }

        .mobile-task-picker-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.85rem;
            border-radius: 999px;
            padding: 0.7rem 1rem;
            font-size: 12px;
            font-weight: 700;
        }

        .mobile-task-picker-button.secondary {
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(248, 250, 252, 0.95);
            color: #475569;
        }

        .mobile-task-picker-button.primary {
            background: linear-gradient(180deg, #0ea5e9, #0284c7);
            color: white;
            box-shadow: 0 14px 30px rgba(2, 132, 199, 0.18);
        }

        .mobile-task-picker-button:disabled {
            opacity: 0.45;
        }
    </style>
@endpush

@section('content')
    <section class="mobile-panel mobile-task-hero">
        <div class="mobile-task-switcher">
            <button type="button" data-task-view="calibrate" class="mobile-task-tab">
                <i data-lucide="monitor-play" class="h-3.5 w-3.5"></i>
                <span>Calibrate</span>
            </button>
            <button type="button" data-task-view="scheduled" class="mobile-task-tab">
                <i data-lucide="calendar-range" class="h-3.5 w-3.5"></i>
                <span>Scheduled</span>
            </button>
            <button type="button" data-task-view="due" class="mobile-task-tab active">
                <i data-lucide="list-checks" class="h-3.5 w-3.5"></i>
                <span>Due now</span>
            </button>
        </div>

        <p class="mobile-task-kicker mt-4">Task Scheduler</p>
        <h2 class="mobile-task-title">Due tasks and scheduled work</h2>
        <p class="mobile-task-copy">Manage overdue work and upcoming calibration or QA schedules without leaving the same mobile queue.</p>
        <p id="mobile-task-subcopy" class="mobile-task-subcopy">Due tasks that already need operational follow-up.</p>
        <div id="mobile-task-hero-actions" class="mobile-task-hero-actions" hidden>
            <button id="mobile-task-hero-button" type="button" class="mobile-task-hero-button" aria-label="Create task">
                <span class="mobile-task-hero-button-icon">
                    <i id="mobile-task-hero-icon" data-lucide="calendar-plus" class="h-4 w-4"></i>
                </span>
                <span class="mobile-task-hero-button-copy">
                    <span id="mobile-task-hero-kicker" class="mobile-task-hero-button-kicker">Scheduled</span>
                    <span id="mobile-task-hero-label" class="mobile-task-hero-button-label">New schedule</span>
                </span>
            </button>
        </div>
    </section>

    <div class="mobile-search-shell">
        <div class="mobile-searchbar">
            <i data-lucide="search" class="mobile-searchbar-icon"></i>
            <input id="mobile-task-search" type="search" class="mobile-input mobile-search-input" placeholder="Search tasks or schedules">
        </div>
    </div>

    <section class="mobile-section-gap">
        <div id="mobile-task-list" class="mobile-stack">
            <div class="mobile-task-skeleton" aria-hidden="true">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="mobile-task-skeleton-pill"></div>
                        <div class="mobile-task-skeleton-line title"></div>
                        <div class="mobile-task-skeleton-line scope"></div>
                        <div class="mobile-task-skeleton-line meta"></div>
                    </div>
                    <div class="mobile-task-skeleton-date"></div>
                </div>
            </div>
            <div class="mobile-task-skeleton" aria-hidden="true">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="mobile-task-skeleton-pill"></div>
                        <div class="mobile-task-skeleton-line title"></div>
                        <div class="mobile-task-skeleton-line scope"></div>
                        <div class="mobile-task-skeleton-line meta"></div>
                    </div>
                    <div class="mobile-task-skeleton-date"></div>
                </div>
            </div>
        </div>
        <div id="mobile-task-pagination" class="mt-3"></div>
    </section>

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileTasks', () => {
                    const list = document.getElementById('mobile-task-list');
                    const pagination = document.getElementById('mobile-task-pagination');
                    const searchInput = document.getElementById('mobile-task-search');
                    const subcopy = document.getElementById('mobile-task-subcopy');
                    const tabs = Array.from(document.querySelectorAll('[data-task-view]'));
                    const heroActions = document.getElementById('mobile-task-hero-actions');
                    const heroButton = document.getElementById('mobile-task-hero-button');
                    const heroIcon = document.getElementById('mobile-task-hero-icon');
                    const heroKicker = document.getElementById('mobile-task-hero-kicker');
                    const heroLabel = document.getElementById('mobile-task-hero-label');
                    const pickerRoot = document.getElementById('mobile-task-picker');
                    const pickerOverlay = document.getElementById('mobile-task-picker-overlay');
                    const pickerPanel = document.getElementById('mobile-task-picker-panel');
                    const pickerClose = document.getElementById('mobile-task-picker-close');
                    const pickerCancel = document.getElementById('mobile-task-picker-cancel');
                    const pickerContinue = document.getElementById('mobile-task-picker-continue');
                    const pickerEyebrow = document.getElementById('mobile-task-picker-eyebrow');
                    const pickerTitle = document.getElementById('mobile-task-picker-title');
                    const pickerCopy = document.getElementById('mobile-task-picker-copy');
                    const facilitySelect = document.getElementById('mobile-task-picker-facility');
                    const workgroupSelect = document.getElementById('mobile-task-picker-workgroup');
                    const workstationSelect = document.getElementById('mobile-task-picker-workstation');
                    const displaySelect = document.getElementById('mobile-task-picker-display');
                    const perPage = 10;
                    const initialView = @json($initialTaskView ?? 'due');
                    const initialDisplayId = @json((int) ($initialTaskDisplayId ?? 0));
                    const initialDisplayName = @json((string) ($initialTaskDisplayName ?? ''));
                    const taskFilters = @json($taskFilters ?? []);
                    const scopeState = {
                        facilities: Array.isArray(taskFilters.facilities) ? taskFilters.facilities : [],
                        workgroupsByFacility: taskFilters.workgroupsByFacility || {},
                        workstationsByWorkgroup: taskFilters.workstationsByWorkgroup || {},
                        selectedFacilityId: String(taskFilters.selectedFacilityId || ''),
                        selectedWorkgroupId: String(taskFilters.selectedWorkgroupId || ''),
                        selectedWorkstationId: String(taskFilters.selectedWorkstationId || ''),
                        displayCache: new Map(),
                        mode: 'scheduled',
                    };
                    const cache = new Map();
                    let requestToken = 0;
                    let timer = null;
                    let currentPage = 1;
                    let currentKeyword = '';
                    let currentView = initialView;
                    let pickerDisplayLoadToken = 0;

                    const filteredSubcopy = (base) => {
                        if (!initialDisplayId) {
                            return base;
                        }

                        return initialDisplayName
                            ? `${base} Filtered to ${initialDisplayName}.`
                            : `${base} This view is filtered to the current display.`;
                    };

                    const viewDefs = {
                        calibrate: {
                            loading: 'Loading calibration work...',
                            empty: 'No calibration tasks matched this filter.',
                            subcopy: filteredSubcopy('Active calibration jobs and recent calibration schedules for the current scope.'),
                            buildUrl: (keyword, page) => `/api/calibration-tasks?limit=${perPage}&page=${page}${initialDisplayId ? `&display_id=${encodeURIComponent(initialDisplayId)}` : ''}${keyword ? `&search=${encodeURIComponent(keyword)}` : ''}`,
                            pill: 'Calibration',
                            fab: {
                                icon: 'monitor-play',
                                kicker: 'Calibrate',
                                label: 'Add calibration',
                            },
                        },
                        due: {
                            loading: 'Loading due tasks...',
                            empty: 'No due tasks matched this filter.',
                            subcopy: filteredSubcopy('Due tasks that already need operational follow-up.'),
                            buildUrl: (keyword, page) => `/api/due-tasks?limit=${perPage}&page=${page}${initialDisplayId ? `&display_id=${encodeURIComponent(initialDisplayId)}` : ''}${keyword ? `&search=${encodeURIComponent(keyword)}` : ''}`,
                            pill: 'Task',
                        },
                        scheduled: {
                            loading: 'Loading scheduled work...',
                            empty: 'No schedules matched this filter.',
                            subcopy: filteredSubcopy('Nearest due schedules are shown first so upcoming work is easier to prioritize.'),
                            buildUrl: (keyword, page) => `/api/tasks?sort_mode=due&limit=${perPage}&page=${page}${initialDisplayId ? `&display_id=${encodeURIComponent(initialDisplayId)}` : ''}${keyword ? `&search=${encodeURIComponent(keyword)}` : ''}`,
                            pill: 'Schedule',
                            fab: {
                                icon: 'calendar-plus',
                                kicker: 'Scheduled',
                                label: 'New schedule',
                            },
                        },
                    };

                    const setOptions = (select, options, value = '', placeholder = 'Please select') => {
                        select.innerHTML = [`<option value="">${placeholder}</option>`]
                            .concat(options.map((option) => `<option value="${String(option.id)}">${String(option.name)}</option>`))
                            .join('');
                        select.value = value && options.some((option) => String(option.id) === String(value))
                            ? String(value)
                            : '';
                    };

                    const setDisplayOptions = (options, value = '') => {
                        displaySelect.innerHTML = ['<option value="">All displays in this workstation</option>']
                            .concat(options.map((option) => `<option value="${String(option.id)}">${String(option.name)}</option>`))
                            .join('');
                        displaySelect.value = value && options.some((option) => String(option.id) === String(value))
                            ? String(value)
                            : '';
                    };

                    const ensureIcons = () => {
                        if (window.lucide?.createIcons) {
                            window.lucide.createIcons();
                        }
                    };

                    const loadingState = () => Array.from({ length: 4 }).map(() => `
                        <div class="mobile-task-skeleton" aria-hidden="true">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="mobile-task-skeleton-pill"></div>
                                    <div class="mobile-task-skeleton-line title"></div>
                                    <div class="mobile-task-skeleton-line scope"></div>
                                    <div class="mobile-task-skeleton-line meta"></div>
                                </div>
                                <div class="mobile-task-skeleton-date"></div>
                            </div>
                        </div>
                    `).join('');
                    const emptyState = (message) => `<div class="mobile-empty">${message}</div>`;
                    const renderPager = (total, page, limit) => {
                        const lastPage = Math.max(1, Math.ceil(total / limit));
                        if (total <= limit) {
                            return '';
                        }

                        const from = total ? (((page - 1) * limit) + 1) : 0;
                        const to = Math.min(page * limit, total);

                        return `
                            <div class="mobile-pager">
                                <p class="mobile-pager-meta">${from}-${to} of ${total}</p>
                                <div class="mobile-pager-actions">
                                    <button type="button" class="mobile-pager-button" data-page="${page - 1}" ${page <= 1 ? 'disabled' : ''}>Prev</button>
                                    <span class="mobile-pager-status">Page ${page} / ${lastPage}</span>
                                    <button type="button" class="mobile-pager-button" data-page="${page + 1}" ${page >= lastPage ? 'disabled' : ''}>Next</button>
                                </div>
                            </div>
                        `;
                    };

                    const updateHeroAction = () => {
                        const definition = viewDefs[currentView];
                        if (!definition?.fab) {
                            heroActions.hidden = true;
                            return;
                        }

                        heroActions.hidden = false;
                        heroKicker.textContent = definition.fab.kicker;
                        heroLabel.textContent = definition.fab.label;
                        heroIcon.setAttribute('data-lucide', definition.fab.icon);
                        heroButton.setAttribute('aria-label', definition.fab.label);
                        heroButton.setAttribute('title', definition.fab.label);
                        ensureIcons();
                    };

                    const syncTabs = () => {
                        tabs.forEach((button) => {
                            button.classList.toggle('active', button.dataset.taskView === currentView);
                        });
                        subcopy.textContent = viewDefs[currentView].subcopy;
                        updateHeroAction();

                        const url = new URL(window.location.href);
                        url.searchParams.set('view', currentView);
                        if (!currentKeyword) {
                            url.searchParams.delete('search');
                        } else {
                            url.searchParams.set('search', currentKeyword);
                        }
                        window.history.replaceState({}, '', `${url.pathname}${url.search}`);
                    };

                    const closeScopePicker = () => {
                        pickerRoot.classList.add('hidden');
                        pickerOverlay.classList.add('hidden');
                        pickerPanel.classList.add('hidden');
                    };

                    const openScopePicker = (mode) => {
                        scopeState.mode = mode;
                        pickerEyebrow.textContent = mode === 'calibrate' ? 'Calibrate' : 'Scheduled work';
                        pickerTitle.textContent = mode === 'calibrate' ? 'Choose a calibration scope' : 'Choose a schedule scope';
                        pickerCopy.textContent = mode === 'calibrate'
                            ? 'Select a workstation or a specific display before creating the calibration task.'
                            : 'Select a workstation or a specific display before opening the scheduler form.';

                        setOptions(
                            facilitySelect,
                            scopeState.facilities,
                            scopeState.selectedFacilityId,
                            scopeState.facilities.length > 1 ? 'Select a facility' : 'Assigned facility'
                        );
                        facilitySelect.disabled = scopeState.facilities.length <= 1;

                        refreshWorkgroups();
                        refreshWorkstations();
                        refreshDisplays().then(() => {
                            pickerRoot.classList.remove('hidden');
                            pickerOverlay.classList.remove('hidden');
                            pickerPanel.classList.remove('hidden');
                            ensureIcons();
                        });
                    };

                    const refreshWorkgroups = () => {
                        const options = Array.isArray(scopeState.workgroupsByFacility?.[scopeState.selectedFacilityId])
                            ? scopeState.workgroupsByFacility[scopeState.selectedFacilityId]
                            : [];

                        setOptions(workgroupSelect, options, scopeState.selectedWorkgroupId, 'Select a workgroup');
                        if (!options.some((option) => String(option.id) === String(scopeState.selectedWorkgroupId))) {
                            scopeState.selectedWorkgroupId = '';
                        }
                        workgroupSelect.value = scopeState.selectedWorkgroupId;
                    };

                    const refreshWorkstations = () => {
                        const options = Array.isArray(scopeState.workstationsByWorkgroup?.[scopeState.selectedWorkgroupId])
                            ? scopeState.workstationsByWorkgroup[scopeState.selectedWorkgroupId]
                            : [];

                        setOptions(workstationSelect, options, scopeState.selectedWorkstationId, 'Select a workstation');
                        if (!options.some((option) => String(option.id) === String(scopeState.selectedWorkstationId))) {
                            scopeState.selectedWorkstationId = '';
                        }
                        workstationSelect.value = scopeState.selectedWorkstationId;
                        pickerContinue.disabled = !scopeState.selectedWorkstationId;
                    };

                    const refreshDisplays = async () => {
                        if (!scopeState.selectedWorkstationId) {
                            setDisplayOptions([]);
                            pickerContinue.disabled = true;
                            return;
                        }

                        pickerContinue.disabled = false;
                        const cacheKey = `${scopeState.selectedFacilityId}::${scopeState.selectedWorkgroupId}::${scopeState.selectedWorkstationId}`;
                        if (scopeState.displayCache.has(cacheKey)) {
                            setDisplayOptions(scopeState.displayCache.get(cacheKey));
                            return;
                        }

                        const currentToken = ++pickerDisplayLoadToken;
                        displaySelect.innerHTML = '<option value="">Loading displays...</option>';

                        try {
                            const response = await window.Perfectlum.request(`/api/displays?sort=display_name&order=asc&limit=150&page=1${scopeState.selectedFacilityId ? `&facility_id=${encodeURIComponent(scopeState.selectedFacilityId)}` : ''}${scopeState.selectedWorkgroupId ? `&workgroup_id=${encodeURIComponent(scopeState.selectedWorkgroupId)}` : ''}&workstation_id=${encodeURIComponent(scopeState.selectedWorkstationId)}`);
                            if (currentToken !== pickerDisplayLoadToken) {
                                return;
                            }

                            const options = (response.data || []).map((item) => ({
                                id: item.id,
                                name: item.name || item.displayName || `Display #${item.id}`,
                            }));
                            scopeState.displayCache.set(cacheKey, options);
                            setDisplayOptions(options);
                        } catch (error) {
                            if (currentToken !== pickerDisplayLoadToken) {
                                return;
                            }

                            setDisplayOptions([]);
                        }
                    };

                    const openTaskComposer = (mode) => {
                        if (typeof window.openTaskEditorWithPayload !== 'function') {
                            return;
                        }

                        const isCalibration = mode === 'calibrate';
                        const payload = {
                            id: 0,
                        };
                        const options = isCalibration
                            ? {
                                title: 'Calibrate Display',
                                subtitle: initialDisplayName
                                    ? `Set the schedule window for ${initialDisplayName} before creating the calibration task.`
                                    : 'Set the schedule window for the selected scope before creating the calibration task.',
                            }
                            : {
                                title: 'Schedule Task',
                                subtitle: initialDisplayName
                                    ? `Create a scheduled task for ${initialDisplayName} without leaving this mobile queue.`
                                    : 'Create a scheduled task for the selected scope without leaving this mobile queue.',
                            };

                        if (isCalibration) {
                            payload.tasktype = 'cal';
                            payload.quick_calibration = '1';
                            payload.lock_tasktype = '1';
                        }

                        if (initialDisplayId) {
                            payload.displays = [initialDisplayId];
                            window.openTaskEditorWithPayload(payload, options);
                            return;
                        }

                        openScopePicker(mode);
                    };

                    const renderRow = (item) => {
                        const href = item.displayId ? `${@json(url('/m/displays'))}/${item.displayId}?return_to=${encodeURIComponent(`${window.location.pathname}${window.location.search}`)}` : '#';
                        const meta = currentView === 'due'
                            ? [item.scheduleName, item.status, item.overdue].filter(Boolean).join(' • ')
                            : currentView === 'calibrate'
                                ? [item.scheduleName, item.createdBy, item.statusLabel].filter(Boolean).join(' • ')
                                : [item.scheduleName, item.status].filter(Boolean).join(' • ');

                        return `
                            <a href="${href}" class="mobile-task-scan-card">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="mobile-type-pill ${currentView === 'due' ? 'alert' : 'run'}">${viewDefs[currentView].pill}</span>
                                            <p class="mobile-meta mobile-clamp-1">${item.displayName}</p>
                                        </div>
                                        <p class="mobile-task-scan-title">${item.taskName}</p>
                                        <p class="mobile-task-scan-scope">${[item.wsName, item.wgName, item.facName].filter(Boolean).join(' • ')}</p>
                                        <p class="mobile-task-scan-meta">${meta || 'Open the linked display for more detail.'}</p>
                                    </div>
                                    <span class="mobile-task-date-pill">${item.dueAt}</span>
                                </div>
                            </a>
                        `;
                    };

                    async function loadTasks(keyword = '', page = 1) {
                        currentKeyword = keyword;
                        currentPage = page;
                        const viewKey = currentView;
                        const cacheKey = `${viewKey}::${keyword}::${page}`;
                        const cached = cache.get(cacheKey);
                        const currentRequest = ++requestToken;

                        if (cached) {
                            list.innerHTML = cached.html;
                            pagination.innerHTML = cached.pager;
                            syncTabs();
                            return;
                        }

                        list.innerHTML = loadingState();
                        pagination.innerHTML = '';

                        try {
                            const response = await window.Perfectlum.request(viewDefs[viewKey].buildUrl(keyword, page));
                            if (currentRequest !== requestToken) {
                                return;
                            }

                            const rows = response.data || [];
                            const total = Number(response.total || 0);

                            if (!rows.length) {
                                list.innerHTML = emptyState(viewDefs[viewKey].empty);
                                syncTabs();
                                return;
                            }

                            const html = rows.map(renderRow).join('');
                            const pager = renderPager(total, page, perPage);
                            cache.set(cacheKey, { html, pager });
                            list.innerHTML = html;
                            pagination.innerHTML = pager;
                            syncTabs();
                            ensureIcons();
                        } catch (error) {
                            if (currentRequest !== requestToken) {
                                return;
                            }

                            list.innerHTML = emptyState('Unable to load task data right now.');
                            syncTabs();
                        }
                    }

                    tabs.forEach((button) => button.addEventListener('click', () => {
                        const nextView = ['calibrate', 'scheduled', 'due'].includes(button.dataset.taskView)
                            ? button.dataset.taskView
                            : 'due';
                        if (nextView === currentView) {
                            return;
                        }

                        currentView = nextView;
                        loadTasks(searchInput.value.trim(), 1);
                    }));

                    pagination.addEventListener('click', (event) => {
                        const button = event.target.closest('[data-page]');
                        if (!button || button.hasAttribute('disabled')) {
                            return;
                        }

                        loadTasks(currentKeyword, Number(button.dataset.page));
                    });

                    searchInput.addEventListener('input', () => {
                        window.clearTimeout(timer);
                        timer = window.setTimeout(() => loadTasks(searchInput.value.trim(), 1), 220);
                    });

                    heroButton.addEventListener('click', () => openTaskComposer(currentView));
                    [pickerOverlay, pickerClose, pickerCancel].forEach((element) => {
                        element?.addEventListener('click', closeScopePicker);
                    });

                    facilitySelect.addEventListener('change', () => {
                        scopeState.selectedFacilityId = facilitySelect.value;
                        scopeState.selectedWorkgroupId = '';
                        scopeState.selectedWorkstationId = '';
                        refreshWorkgroups();
                        refreshWorkstations();
                        refreshDisplays();
                    });

                    workgroupSelect.addEventListener('change', () => {
                        scopeState.selectedWorkgroupId = workgroupSelect.value;
                        scopeState.selectedWorkstationId = '';
                        refreshWorkstations();
                        refreshDisplays();
                    });

                    workstationSelect.addEventListener('change', () => {
                        scopeState.selectedWorkstationId = workstationSelect.value;
                        refreshWorkstations();
                        refreshDisplays();
                    });

                    pickerContinue.addEventListener('click', () => {
                        if (!scopeState.selectedWorkstationId || typeof window.openTaskEditorWithPayload !== 'function') {
                            return;
                        }

                        const payload = {
                            id: 0,
                            facility2: scopeState.selectedFacilityId,
                            workgroup2: scopeState.selectedWorkgroupId,
                            workstation2: scopeState.selectedWorkstationId,
                        };

                        if (displaySelect.value) {
                            payload.displays = [displaySelect.value];
                        }

                        const isCalibration = scopeState.mode === 'calibrate';
                        if (isCalibration) {
                            payload.tasktype = 'cal';
                            payload.quick_calibration = '1';
                            payload.lock_tasktype = '1';
                        }

                        closeScopePicker();
                        window.openTaskEditorWithPayload(payload, isCalibration
                            ? {
                                title: 'Calibrate Display',
                                subtitle: 'Set the schedule window for the selected mobile scope before creating the calibration task.',
                            }
                            : {
                                title: 'Schedule Task',
                                subtitle: 'Create a scheduled task for the selected mobile scope without leaving this queue.',
                            });
                    });

                    window.addEventListener('task-saved', () => {
                        cache.clear();
                        loadTasks(currentKeyword, 1);
                    });

                    syncTabs();
                    loadTasks(searchInput.value.trim(), 1);
                    ensureIcons();
                });

                if (window.Perfectlum?.mountMobilePage) {
                    boot();
                    return;
                }

                (window.__perfectlumPageBoot = window.__perfectlumPageBoot || []).push(boot);
            })();
        </script>
    @endpush

    @push('modals')
        <div id="mobile-task-picker" class="hidden">
            <button id="mobile-task-picker-overlay" type="button" class="mobile-task-picker-overlay hidden" aria-label="Close scope picker"></button>
            <section id="mobile-task-picker-panel" class="mobile-task-picker-panel hidden" aria-labelledby="mobile-task-picker-title">
                <div class="mobile-task-picker-shell">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p id="mobile-task-picker-eyebrow" class="mobile-task-picker-eyebrow">Scheduled work</p>
                            <h3 id="mobile-task-picker-title" class="mobile-task-picker-title">Choose a scope first</h3>
                            <p id="mobile-task-picker-copy" class="mobile-task-picker-copy">Pick the display scope for the task you are about to create.</p>
                        </div>
                        <button id="mobile-task-picker-close" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-500">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </div>

                    <div class="mobile-task-picker-grid">
                        <label class="mobile-task-picker-field">
                            <span class="mobile-task-picker-label">Facility</span>
                            <select id="mobile-task-picker-facility" class="mobile-input h-11 rounded-2xl px-3 text-sm"></select>
                        </label>

                        <label class="mobile-task-picker-field">
                            <span class="mobile-task-picker-label">Workgroup</span>
                            <select id="mobile-task-picker-workgroup" class="mobile-input h-11 rounded-2xl px-3 text-sm"></select>
                        </label>

                        <label class="mobile-task-picker-field">
                            <span class="mobile-task-picker-label">Workstation</span>
                            <select id="mobile-task-picker-workstation" class="mobile-input h-11 rounded-2xl px-3 text-sm"></select>
                        </label>

                        <label class="mobile-task-picker-field">
                            <span class="mobile-task-picker-label">Display</span>
                            <select id="mobile-task-picker-display" class="mobile-input h-11 rounded-2xl px-3 text-sm"></select>
                            <p class="mobile-task-picker-hint">Keep “All displays in this workstation” selected if you want the task to apply to the whole workstation scope.</p>
                        </label>
                    </div>

                    <div class="mobile-task-picker-footer">
                        <button id="mobile-task-picker-cancel" type="button" class="mobile-task-picker-button secondary">Cancel</button>
                        <button id="mobile-task-picker-continue" type="button" class="mobile-task-picker-button primary" disabled>Continue</button>
                    </div>
                </div>
            </section>
        </div>
    @endpush
@endsection
