@extends('mobile.layouts.app')

@push('head')
    <style>
        .mobile-notification-card {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.8rem 0.84rem;
        }

        .mobile-notification-card + .mobile-notification-card {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mobile-notification-card:active {
            background: rgba(14, 165, 233, 0.05);
        }

        .mobile-notification-title {
            margin-top: 0.28rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.32;
            color: #0f172a;
        }

        .mobile-notification-body {
            margin-top: 0.28rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 12px;
            line-height: 1.42;
            color: #475569;
        }
    </style>
@endpush

@section('content')
    <div class="mobile-search-shell">
        <div class="flex gap-2 overflow-x-auto no-scrollbar">
            <button type="button" data-notification-filter="unread" class="mobile-notification-filter mobile-filter-chip active">Unread</button>
            <button type="button" data-notification-filter="all" class="mobile-notification-filter mobile-filter-chip">All</button>
        </div>
        <div class="mt-3 flex items-center justify-between text-[12px] text-slate-500">
            <span id="mobile-notification-count">Loading...</span>
            <button id="mobile-notification-read-all" type="button" class="font-medium text-sky-700">Mark all read</button>
        </div>
    </div>

    <section class="mobile-section-gap">
        <div id="mobile-notification-list" class="mobile-stack">
            <div class="mobile-list-item text-[13px] text-slate-500">Loading notifications...</div>
        </div>
        <div id="mobile-notification-pagination" class="mt-3"></div>
    </section>

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileNotifications', () => {
                const list = document.getElementById('mobile-notification-list');
                const pagination = document.getElementById('mobile-notification-pagination');
                const count = document.getElementById('mobile-notification-count');
                const readAllButton = document.getElementById('mobile-notification-read-all');
                const filters = Array.from(document.querySelectorAll('.mobile-notification-filter'));
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const perPage = 10;
                let filter = 'unread';
                let currentPage = 1;

                const loadingState = () => '<div class="mobile-list-item text-[13px] text-slate-500">Loading notifications...</div>';
                const emptyState = (message) => `<div class="mobile-empty">${message}</div>`;
                const renderPager = (meta) => {
                    const total = Number(meta?.total || 0);
                    const page = Number(meta?.currentPage || 1);
                    const lastPage = Number(meta?.lastPage || 1);
                    if (total <= Number(meta?.perPage || perPage)) {
                        return '';
                    }

                    return `
                        <div class="mobile-pager">
                            <p class="mobile-pager-meta">${meta.from}-${meta.to} of ${total}</p>
                            <div class="mobile-pager-actions">
                                <button type="button" class="mobile-pager-button" data-page="${page - 1}" ${page <= 1 ? 'disabled' : ''}>Prev</button>
                                <span class="mobile-pager-status">Page ${page} / ${lastPage}</span>
                                <button type="button" class="mobile-pager-button" data-page="${page + 1}" ${page >= lastPage ? 'disabled' : ''}>Next</button>
                            </div>
                        </div>
                    `;
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

                function syncFilters() {
                    filters.forEach((button) => {
                        const active = button.dataset.notificationFilter === filter;
                        button.className = `mobile-notification-filter mobile-filter-chip ${active ? 'active' : ''}`;
                    });
                }

                async function loadNotifications(page = 1) {
                    currentPage = page;
                    list.innerHTML = loadingState();
                    pagination.innerHTML = '';

                    try {
                        const response = await window.Perfectlum.request(`/api/notifications?context=mobile&filter=${filter}&limit=${perPage}&page=${page}`);
                        const rows = response.data || [];
                        count.textContent = `${response.unreadCount ?? 0} unread`;

                        if (!rows.length) {
                            list.innerHTML = emptyState('No notifications in this view.');
                            return;
                        }

                        list.innerHTML = rows.map((item) => `
                            <button type="button" data-notification-id="${item.id}" data-notification-url="${item.url || ''}" class="mobile-notification-card">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="min-w-0 flex items-center gap-2">
                                        <span class="mobile-type-pill ${item.read ? 'run' : 'alert'}">${item.category || (item.read ? 'Read' : 'New')}</span>
                                        <p class="mobile-meta mobile-clamp-1">${item.relativeTime}</p>
                                    </div>
                                    ${item.read ? '' : '<span class="mt-0.5 h-2 w-2 rounded-full bg-sky-500"></span>'}
                                </div>
                                <p class="mobile-notification-title">${item.title}</p>
                                <p class="mobile-notification-body">${item.body}</p>
                                <p class="mt-1 mobile-meta mobile-clamp-1">${item.createdAt}</p>
                            </button>
                        `).join('');
                        pagination.innerHTML = renderPager(response.meta || {});
                    } catch (error) {
                        list.innerHTML = emptyState('Unable to load notifications right now.');
                    }
                }

                filters.forEach((button) => button.addEventListener('click', () => {
                    filter = button.dataset.notificationFilter;
                    syncFilters();
                    loadNotifications(1);
                }));

                readAllButton.addEventListener('click', async () => {
                    try {
                        await post('/api/notifications/read-all');
                        loadNotifications(1);
                    } catch (error) {}
                });

                pagination.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-page]');
                    if (!button || button.hasAttribute('disabled')) {
                        return;
                    }

                    loadNotifications(Number(button.dataset.page));
                });

                list.addEventListener('click', async (event) => {
                    const button = event.target.closest('[data-notification-id]');
                    if (!button) {
                        return;
                    }

                    const id = button.dataset.notificationId;
                    const url = button.dataset.notificationUrl;

                    try {
                        await post(`/api/notifications/${id}/read`);
                    } catch (error) {}

                    if (url) {
                        window.location.href = url;
                        return;
                    }

                    loadNotifications(currentPage);
                });

                syncFilters();
                loadNotifications();
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
