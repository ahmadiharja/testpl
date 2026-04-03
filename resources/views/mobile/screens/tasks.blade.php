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
    </style>
@endpush

@section('content')
    <section class="mobile-panel mobile-task-hero">
        <div class="mobile-task-switcher">
            <button type="button" data-task-view="due" class="mobile-task-tab active">
                <i data-lucide="list-checks" class="h-3.5 w-3.5"></i>
                <span>Due now</span>
            </button>
            <button type="button" data-task-view="scheduled" class="mobile-task-tab">
                <i data-lucide="calendar-range" class="h-3.5 w-3.5"></i>
                <span>Scheduled</span>
            </button>
        </div>

        <p class="mobile-task-kicker mt-4">Task Scheduler</p>
        <h2 class="mobile-task-title">Due tasks and scheduled work</h2>
        <p class="mobile-task-copy">Manage overdue work and upcoming calibration or QA schedules without leaving the same mobile queue.</p>
        <p id="mobile-task-subcopy" class="mobile-task-subcopy">Due tasks that already need operational follow-up.</p>
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
                    const perPage = 10;
                    const initialView = @json(($initialTaskView ?? 'due') === 'scheduled' ? 'scheduled' : 'due');
                    const initialDisplayId = @json((int) ($initialTaskDisplayId ?? 0));
                    const initialDisplayName = @json((string) ($initialTaskDisplayName ?? ''));
                    const cache = new Map();
                    let requestToken = 0;
                    let timer = null;
                    let currentPage = 1;
                    let currentKeyword = '';
                    let currentView = initialView;

                    const filteredSubcopy = (base) => {
                        if (!initialDisplayId) {
                            return base;
                        }

                        return initialDisplayName
                            ? `${base} Filtered to ${initialDisplayName}.`
                            : `${base} This view is filtered to the current display.`;
                    };

                    const viewDefs = {
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
                        },
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

                    const syncTabs = () => {
                        tabs.forEach((button) => {
                            button.classList.toggle('active', button.dataset.taskView === currentView);
                        });
                        subcopy.textContent = viewDefs[currentView].subcopy;

                        const url = new URL(window.location.href);
                        url.searchParams.set('view', currentView);
                        if (!currentKeyword) {
                            url.searchParams.delete('search');
                        } else {
                            url.searchParams.set('search', currentKeyword);
                        }
                        window.history.replaceState({}, '', `${url.pathname}${url.search}`);
                    };

                    const renderRow = (item) => {
                        const href = item.displayId ? `${@json(url('/m/displays'))}/${item.displayId}?return_to=${encodeURIComponent(`${window.location.pathname}${window.location.search}`)}` : '#';
                        const meta = currentView === 'due'
                            ? [item.scheduleName, item.status, item.overdue].filter(Boolean).join(' • ')
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
                        } catch (error) {
                            if (currentRequest !== requestToken) {
                                return;
                            }

                            list.innerHTML = emptyState('Unable to load task data right now.');
                            syncTabs();
                        }
                    }

                    tabs.forEach((button) => button.addEventListener('click', () => {
                        const nextView = button.dataset.taskView === 'scheduled' ? 'scheduled' : 'due';
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

                    syncTabs();
                    loadTasks(searchInput.value.trim(), 1);
                });

                if (window.Perfectlum?.mountMobilePage) {
                    boot();
                    return;
                }

                (window.__perfectlumPageBoot = window.__perfectlumPageBoot || []).push(boot);
            })();
        </script>
    @endpush
@endsection
