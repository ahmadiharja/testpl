@extends('mobile.layouts.app')

@push('head')
    <style>
        .mobile-scheduler-scan-card {
            display: block;
            padding: 0.78rem 0.82rem;
            transition: background 160ms ease, transform 160ms ease;
        }

        .mobile-scheduler-scan-card:active {
            background: rgba(14, 165, 233, 0.05);
            transform: scale(0.995);
        }

        .mobile-scheduler-scan-card + .mobile-scheduler-scan-card {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mobile-scheduler-scan-title {
            margin-top: 0.28rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.3;
            letter-spacing: -0.01em;
            color: #0f172a;
        }

        .mobile-scheduler-scan-display {
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
        <div class="mobile-searchbar">
            <i data-lucide="search" class="mobile-searchbar-icon"></i>
            <input id="mobile-scheduler-search" type="search" class="mobile-input mobile-search-input" placeholder="Search schedules">
        </div>
    </div>

    <section class="mobile-section-gap">
        <div id="mobile-scheduler-list" class="mobile-stack">
            <div class="mobile-list-item text-[13px] text-slate-500">Loading scheduled work...</div>
        </div>
        <div id="mobile-scheduler-pagination" class="mt-3"></div>
    </section>

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileScheduler', () => {
                const list = document.getElementById('mobile-scheduler-list');
                const pagination = document.getElementById('mobile-scheduler-pagination');
                const searchInput = document.getElementById('mobile-scheduler-search');
                const perPage = 10;
                let timer = null;
                let currentPage = 1;
                let currentKeyword = '';

                const loadingState = () => '<div class="mobile-list-item text-[13px] text-slate-500">Loading scheduled work...</div>';
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

                async function loadSchedules(keyword = '', page = 1) {
                    currentKeyword = keyword;
                    currentPage = page;
                    list.innerHTML = loadingState();
                    pagination.innerHTML = '';

                    try {
                        const response = await window.Perfectlum.request(`/api/tasks?sort_mode=due&limit=${perPage}&page=${page}${keyword ? `&search=${encodeURIComponent(keyword)}` : ''}`);
                        const rows = response.data || [];
                        const total = Number(response.total || 0);

                        if (!rows.length) {
                            list.innerHTML = emptyState('No schedules matched this filter.');
                            return;
                        }

                        list.innerHTML = rows.map((item) => `
                            <a href="${item.displayId ? `${@json(url('/m/displays'))}/${item.displayId}` : '#'}" class="mobile-scheduler-scan-card">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="mobile-type-pill due">Plan</span>
                                            <p class="mobile-meta mobile-clamp-1">${item.displayName}</p>
                                        </div>
                                        <p class="mobile-scheduler-scan-title">${item.taskName}</p>
                                        <p class="mobile-scheduler-scan-display">${[item.wsName, item.wgName, item.facName].filter(Boolean).join(' • ')}</p>
                                        <p class="mt-1 mobile-meta mobile-clamp-1">${[item.scheduleName, item.status].filter(Boolean).join(' • ')}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-medium text-slate-500">${item.dueAt}</span>
                                </div>
                            </a>
                        `).join('');

                        pagination.innerHTML = renderPager(total, page, perPage);
                    } catch (error) {
                        list.innerHTML = emptyState('Unable to load scheduler data right now.');
                    }
                }

                pagination.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-page]');
                    if (!button || button.hasAttribute('disabled')) {
                        return;
                    }

                    loadSchedules(currentKeyword, Number(button.dataset.page));
                });

                searchInput.addEventListener('input', () => {
                    window.clearTimeout(timer);
                    timer = window.setTimeout(() => loadSchedules(searchInput.value.trim(), 1), 220);
                });

                loadSchedules();
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
