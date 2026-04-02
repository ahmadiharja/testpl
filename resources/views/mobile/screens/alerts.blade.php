@extends('mobile.layouts.app')

@php
    $summary = $dashboardSummary ?? [
        'displaysOk' => 0,
        'displaysFailed' => 0,
        'workstations' => 0,
        'dueTasks' => 0,
        'staleWorkstations' => 0,
    ];
@endphp

@push('head')
    <style>
        .mobile-alerts-hero {
            position: relative;
            overflow: hidden;
            padding: 0.98rem;
            border-color: rgba(254, 205, 211, 0.7);
            background:
                radial-gradient(circle at top right, rgba(251, 113, 133, 0.12), transparent 34%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(250, 252, 255, 0.95));
        }

        .mobile-alerts-kicker {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-alerts-title {
            margin-top: 0.42rem;
            font-size: 1.22rem;
            font-weight: 760;
            letter-spacing: -0.036em;
            line-height: 1.04;
            color: #0f172a;
        }

        .mobile-alerts-copy {
            margin-top: 0.45rem;
            max-width: 18rem;
            font-size: 12.5px;
            line-height: 1.52;
            color: #475569;
        }

        .mobile-alerts-summary {
            margin-top: 0.8rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.55rem;
        }

        .mobile-alerts-summary-tile {
            border-radius: 1rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            background: rgba(255, 255, 255, 0.86);
            padding: 0.64rem 0.7rem;
        }

        .mobile-alerts-summary-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-alerts-summary-value {
            margin-top: 0.32rem;
            font-size: 1.24rem;
            font-weight: 760;
            letter-spacing: -0.04em;
            line-height: 1;
            color: #0f172a;
        }

        .mobile-alerts-tabs {
            display: flex;
            gap: 0.45rem;
            overflow-x: auto;
            padding-bottom: 0.05rem;
        }

        .mobile-alerts-tab {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.38rem;
            min-height: 2.35rem;
            border-radius: 999px;
            border: 1px solid rgba(203, 213, 225, 0.92);
            background: rgba(255, 255, 255, 0.92);
            padding: 0 0.9rem;
            font-size: 11.5px;
            font-weight: 700;
            color: #64748b;
            transition: border-color 160ms ease, background 160ms ease, color 160ms ease;
        }

        .mobile-alerts-tab.active {
            border-color: rgba(251, 113, 133, 0.38);
            background: linear-gradient(180deg, rgba(255, 241, 242, 0.98), rgba(255, 255, 255, 0.94));
            color: #be123c;
        }

        .mobile-alerts-subfilters {
            display: none;
            gap: 0.45rem;
            overflow-x: auto;
            padding-top: 0.72rem;
        }

        .mobile-alerts-subfilters.active {
            display: flex;
        }

        .mobile-alert-card {
            display: block;
            padding: 0.84rem 0.86rem;
            transition: background 160ms ease, transform 160ms ease;
        }

        .mobile-alert-card:active {
            background: rgba(14, 165, 233, 0.04);
            transform: scale(0.995);
        }

        .mobile-alert-card + .mobile-alert-card {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mobile-alert-card-title {
            margin-top: 0.28rem;
            font-size: 13px;
            font-weight: 680;
            line-height: 1.32;
            color: #0f172a;
        }

        .mobile-alert-card-scope,
        .mobile-alert-card-meta,
        .mobile-alert-card-body {
            margin-top: 0.22rem;
            font-size: 12px;
            line-height: 1.46;
        }

        .mobile-alert-card-scope {
            color: #64748b;
        }

        .mobile-alert-card-meta {
            color: #475569;
        }

        .mobile-alert-card-body {
            color: #475569;
        }

        .mobile-alert-card-body.critical {
            color: #ef4444;
            font-weight: 600;
        }

        .mobile-alert-skeleton-card {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            border: 1px solid rgba(226, 232, 240, 0.88);
            background: rgba(255, 255, 255, 0.97);
            padding: 0.88rem;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.028);
        }

        .mobile-alert-skeleton-card::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.72), transparent);
            animation: mobileAlertSkeletonSweep 1.12s ease-in-out infinite;
        }

        .mobile-alert-skeleton-pill,
        .mobile-alert-skeleton-line,
        .mobile-alert-skeleton-badge {
            background: linear-gradient(90deg, rgba(226, 232, 240, 0.84), rgba(241, 245, 249, 0.96), rgba(226, 232, 240, 0.84));
            background-size: 200% 100%;
            animation: mobileAlertSkeletonPulse 1.35s ease-in-out infinite;
        }

        .mobile-alert-skeleton-pill,
        .mobile-alert-skeleton-badge {
            border-radius: 999px;
        }

        .mobile-alert-skeleton-line {
            border-radius: 999px;
            height: 0.78rem;
        }

        .mobile-alert-skeleton-pill {
            height: 1.25rem;
            width: 4.8rem;
        }

        .mobile-alert-skeleton-badge {
            height: 1.7rem;
            width: 3.9rem;
        }

        .mobile-alert-skeleton-line.title {
            margin-top: 0.5rem;
            width: 10.4rem;
            height: 0.98rem;
        }

        .mobile-alert-skeleton-line.scope {
            margin-top: 0.42rem;
            width: 12.4rem;
        }

        .mobile-alert-skeleton-line.body {
            margin-top: 0.42rem;
            width: 9.8rem;
        }

        @keyframes mobileAlertSkeletonSweep {
            100% {
                transform: translateX(100%);
            }
        }

        @keyframes mobileAlertSkeletonPulse {
            0%,
            100% {
                background-position: 100% 50%;
            }

            50% {
                background-position: 0% 50%;
            }
        }
    </style>
@endpush

@section('content')
    <section class="mobile-panel mobile-alerts-hero">
        <p class="mobile-alerts-kicker">Alerts</p>
        <h2 class="mobile-alerts-title">Displays that need attention</h2>
        <p class="mobile-alerts-copy">Review failed displays, the connection watchlist, and unread notifications from one operational surface.</p>

        <div class="mobile-alerts-summary">
            <div class="mobile-alerts-summary-tile">
                <p class="mobile-alerts-summary-label">Not OK</p>
                <p class="mobile-alerts-summary-value">{{ number_format($summary['displaysFailed'] ?? 0) }}</p>
            </div>
            <div class="mobile-alerts-summary-tile">
                <p class="mobile-alerts-summary-label">Watchlist</p>
                <p class="mobile-alerts-summary-value">{{ number_format($summary['staleWorkstations'] ?? 0) }}</p>
            </div>
            <div class="mobile-alerts-summary-tile">
                <p class="mobile-alerts-summary-label">Unread</p>
                <p id="mobile-alert-unread-count" class="mobile-alerts-summary-value">—</p>
            </div>
        </div>
    </section>

    <div class="mobile-search-shell">
        <div class="mobile-alerts-tabs no-scrollbar">
            <button type="button" data-alert-view="displays" class="mobile-alerts-tab active">
                <i data-lucide="monitor" class="h-3.5 w-3.5"></i>
                <span>Need attention</span>
            </button>
            <button type="button" data-alert-view="connections" class="mobile-alerts-tab">
                <i data-lucide="wifi-off" class="h-3.5 w-3.5"></i>
                <span>Watchlist</span>
            </button>
            <button type="button" data-alert-view="inbox" class="mobile-alerts-tab">
                <i data-lucide="inbox" class="h-3.5 w-3.5"></i>
                <span>Notifications</span>
            </button>
        </div>

        <div id="mobile-alerts-inbox-filters" class="mobile-alerts-subfilters no-scrollbar">
            <button type="button" data-alert-inbox-filter="unread" class="mobile-filter-chip active">Unread</button>
            <button type="button" data-alert-inbox-filter="all" class="mobile-filter-chip">All</button>
        </div>

        <div class="mt-3 flex items-center justify-between gap-3 text-[12px] text-slate-500">
            <span id="mobile-alerts-count">Loading…</span>
            <button id="mobile-alerts-read-all" type="button" class="hidden font-medium text-sky-700">Mark all read</button>
        </div>
    </div>

    <section class="mobile-section-gap">
        <div id="mobile-alerts-list" class="mobile-stack">
            <div class="mobile-alert-skeleton-card" aria-hidden="true">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="mobile-alert-skeleton-pill"></div>
                        <div class="mobile-alert-skeleton-line title"></div>
                        <div class="mobile-alert-skeleton-line scope"></div>
                        <div class="mobile-alert-skeleton-line body"></div>
                    </div>
                    <div class="mobile-alert-skeleton-badge"></div>
                </div>
            </div>
            <div class="mobile-alert-skeleton-card" aria-hidden="true">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="mobile-alert-skeleton-pill"></div>
                        <div class="mobile-alert-skeleton-line title"></div>
                        <div class="mobile-alert-skeleton-line scope"></div>
                        <div class="mobile-alert-skeleton-line body"></div>
                    </div>
                    <div class="mobile-alert-skeleton-badge"></div>
                </div>
            </div>
        </div>
        <div id="mobile-alerts-pagination" class="mt-3"></div>
    </section>

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileAlerts', () => {
                    const list = document.getElementById('mobile-alerts-list');
                    const pagination = document.getElementById('mobile-alerts-pagination');
                    const count = document.getElementById('mobile-alerts-count');
                    const unreadCount = document.getElementById('mobile-alert-unread-count');
                    const readAllButton = document.getElementById('mobile-alerts-read-all');
                    const inboxFiltersWrap = document.getElementById('mobile-alerts-inbox-filters');
                    const tabs = Array.from(document.querySelectorAll('[data-alert-view]'));
                    const inboxFilters = Array.from(document.querySelectorAll('[data-alert-inbox-filter]'));
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const initialView = @json($initialAlertsView ?? 'displays');
                    const perPage = 10;
                    const cache = new Map();
                    let requestToken = 0;
                    let currentView = ['displays', 'connections', 'inbox'].includes(initialView) ? initialView : 'displays';
                    let inboxFilter = 'unread';
                    let currentPage = 1;

                    const escapeHtml = window.Perfectlum.escapeHtml;
                    const loadingState = () => Array.from({ length: 4 }).map(() => `
                        <div class="mobile-alert-skeleton-card" aria-hidden="true">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="mobile-alert-skeleton-pill"></div>
                                    <div class="mobile-alert-skeleton-line title"></div>
                                    <div class="mobile-alert-skeleton-line scope"></div>
                                    <div class="mobile-alert-skeleton-line body"></div>
                                </div>
                                <div class="mobile-alert-skeleton-badge"></div>
                            </div>
                        </div>
                    `).join('');
                    const emptyState = (message) => `<div class="mobile-empty">${message}</div>`;
                    const currentReturnTo = () => `${window.location.pathname}${window.location.search}`;
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

                    const updateUrl = () => {
                        const url = new URL(window.location.href);
                        url.searchParams.set('panel', currentView);
                        if (currentView === 'inbox') {
                            url.searchParams.set('filter', inboxFilter);
                        } else {
                            url.searchParams.delete('filter');
                        }
                        window.history.replaceState({}, '', `${url.pathname}${url.search}`);
                    };

                    const syncControls = () => {
                        tabs.forEach((button) => {
                            button.classList.toggle('active', button.dataset.alertView === currentView);
                        });

                        const inboxVisible = currentView === 'inbox';
                        inboxFiltersWrap.classList.toggle('active', inboxVisible);
                        readAllButton.classList.toggle('hidden', !inboxVisible);

                        inboxFilters.forEach((button) => {
                            button.className = `mobile-filter-chip ${button.dataset.alertInboxFilter === inboxFilter ? 'active' : ''}`;
                        });

                        updateUrl();
                    };

                    async function post(url) {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Request failed');
                        }

                        return response.json();
                    }

                    const renderDisplayRows = (rows) => rows.map((item) => `
                        <a href="${@json(url('/m/displays'))}/${item.id}?return_to=${encodeURIComponent(currentReturnTo())}" class="mobile-alert-card">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="mobile-type-pill alert">Failed</span>
                                        <p class="mobile-meta mobile-clamp-1">${escapeHtml(item.updatedAt)}</p>
                                    </div>
                                    <p class="mobile-alert-card-title">${escapeHtml(item.displayName)}</p>
                                    <p class="mobile-alert-card-scope">${escapeHtml([item.wsName, item.wgName, item.facName].filter(Boolean).join(' • '))}</p>
                                    <p class="mobile-alert-card-body critical">${escapeHtml(item.attentionText || 'No alert detail')}</p>
                                </div>
                                <span class="rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-rose-600">Failed</span>
                            </div>
                        </a>
                    `).join('');

                    const renderConnectionRows = (rows) => rows.map((item) => `
                        <a href="${@json(url('/m/workstations'))}/${item.id}?return_to=${encodeURIComponent(currentReturnTo())}" class="mobile-alert-card">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="mobile-type-pill alert">${item.lcColor === 'danger' ? 'Stale' : 'Watch'}</span>
                                        <p class="mobile-meta mobile-clamp-1">${escapeHtml(item.lastSeenRelative)}</p>
                                    </div>
                                    <p class="mobile-alert-card-title">${escapeHtml(item.name)}</p>
                                    <p class="mobile-alert-card-scope">${escapeHtml([item.wgName, item.facName].filter(Boolean).join(' • '))}</p>
                                    <p class="mobile-alert-card-body">${escapeHtml(`${item.displaysCount} displays • Last sync ${item.lastConnected}`)}</p>
                                </div>
                                <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-amber-700">${item.lcColor === 'danger' ? 'Stale' : 'Watch'}</span>
                            </div>
                        </a>
                    `).join('');

                    const renderInboxRows = (rows) => rows.map((item) => `
                        <button type="button" data-notification-id="${item.id}" data-notification-url="${item.url || ''}" class="mobile-alert-card w-full text-left">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="mobile-type-pill ${item.read ? 'run' : 'alert'}">${escapeHtml(item.category || (item.read ? 'Read' : 'New'))}</span>
                                        <p class="mobile-meta mobile-clamp-1">${escapeHtml(item.relativeTime)}</p>
                                    </div>
                                    <p class="mobile-alert-card-title">${escapeHtml(item.title)}</p>
                                    <p class="mobile-alert-card-body">${escapeHtml(item.body)}</p>
                                    <p class="mobile-alert-card-meta">${escapeHtml(item.createdAt)}</p>
                                </div>
                                ${item.read ? '' : '<span class="mt-0.5 h-2.5 w-2.5 rounded-full bg-sky-500"></span>'}
                            </div>
                        </button>
                    `).join('');

                    async function loadCurrent(page = 1) {
                        currentPage = page;
                        const cacheKey = `${currentView}::${inboxFilter}::${page}`;
                        const cached = cache.get(cacheKey);
                        const currentRequest = ++requestToken;

                        if (cached) {
                            list.innerHTML = cached.html;
                            pagination.innerHTML = cached.pager;
                            count.textContent = cached.countText;
                            if (typeof cached.unreadText === 'string') {
                                unreadCount.textContent = cached.unreadText;
                            }
                            syncControls();
                            return;
                        }

                        list.innerHTML = loadingState();
                        pagination.innerHTML = '';
                        count.textContent = 'Loading…';
                        syncControls();

                        try {
                            if (currentView === 'displays') {
                                const response = await window.Perfectlum.request(`/api/displays?type=failed&sort=updated_at&order=desc&limit=${perPage}&page=${page}`);
                                if (currentRequest !== requestToken) {
                                    return;
                                }
                                const rows = response.data || [];
                                const countText = `${Number(response.total || 0)} displays needing attention`;
                                count.textContent = countText;
                                if (!rows.length) {
                                    list.innerHTML = emptyState('No displays need attention in the current scope.');
                                    return;
                                }

                                const html = renderDisplayRows(rows);
                                const pager = renderPager(Number(response.total || 0), page, perPage);
                                cache.set(cacheKey, { html, pager, countText });
                                list.innerHTML = html;
                                pagination.innerHTML = pager;
                                return;
                            }

                            if (currentView === 'connections') {
                                const response = await window.Perfectlum.request('/api/connection-watchlist?limit=20');
                                if (currentRequest !== requestToken) {
                                    return;
                                }
                                const rows = response.data || [];
                                const countText = `${Number(response.total || 0)} workstations in the connection watchlist`;
                                count.textContent = countText;
                                if (!rows.length) {
                                    list.innerHTML = emptyState('No stale workstations need review right now.');
                                    return;
                                }

                                const html = renderConnectionRows(rows);
                                cache.set(cacheKey, { html, pager: '', countText });
                                list.innerHTML = html;
                                return;
                            }

                            const response = await window.Perfectlum.request(`/api/notifications?context=mobile&filter=${inboxFilter}&limit=${perPage}&page=${page}`);
                            if (currentRequest !== requestToken) {
                                return;
                            }
                            const rows = response.data || [];
                            const unreadText = String(response.unreadCount ?? 0);
                            const countText = inboxFilter === 'unread'
                                ? `${response.unreadCount ?? 0} unread notifications`
                                : `${Number(response.meta?.total || 0)} notifications in this inbox`;
                            unreadCount.textContent = unreadText;
                            count.textContent = countText;

                            if (!rows.length) {
                                list.innerHTML = emptyState('No notifications in this view.');
                                return;
                            }

                            const html = renderInboxRows(rows);
                            const pager = renderPager(Number(response.meta?.total || 0), Number(response.meta?.currentPage || page), Number(response.meta?.perPage || perPage));
                            cache.set(cacheKey, { html, pager, countText, unreadText });
                            list.innerHTML = html;
                            pagination.innerHTML = pager;
                        } catch (error) {
                            if (currentRequest !== requestToken) {
                                return;
                            }
                            list.innerHTML = emptyState('Unable to load this alert feed right now.');
                        }
                    }

                    tabs.forEach((button) => button.addEventListener('click', () => {
                        const nextView = button.dataset.alertView;
                        if (!nextView || nextView === currentView) {
                            return;
                        }

                        currentView = nextView;
                        loadCurrent(1);
                    }));

                    inboxFilters.forEach((button) => button.addEventListener('click', () => {
                        const nextFilter = button.dataset.alertInboxFilter === 'all' ? 'all' : 'unread';
                        if (nextFilter === inboxFilter) {
                            return;
                        }

                        inboxFilter = nextFilter;
                        loadCurrent(1);
                    }));

                    readAllButton.addEventListener('click', async () => {
                        try {
                            await post('/api/notifications/read-all');
                            cache.clear();
                            loadCurrent(1);
                        } catch (error) {}
                    });

                    pagination.addEventListener('click', (event) => {
                        const button = event.target.closest('[data-page]');
                        if (!button || button.hasAttribute('disabled')) {
                            return;
                        }

                        loadCurrent(Number(button.dataset.page));
                    });

                    list.addEventListener('click', async (event) => {
                        const notificationButton = event.target.closest('[data-notification-id]');
                        if (!notificationButton) {
                            return;
                        }

                        const id = notificationButton.dataset.notificationId;
                        const url = notificationButton.dataset.notificationUrl;

                        try {
                            await post(`/api/notifications/${id}/read`);
                            cache.clear();
                        } catch (error) {}

                        if (url) {
                            window.location.href = url;
                            return;
                        }

                        loadCurrent(currentPage);
                    });

                    loadCurrent(1);
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
