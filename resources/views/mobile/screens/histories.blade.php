@extends('mobile.layouts.app')

@section('content')
    <div class="mobile-search-shell">
        <div class="mobile-searchbar">
            <i data-lucide="search" class="mobile-searchbar-icon"></i>
            <input id="mobile-history-search" type="search" class="mobile-input mobile-search-input" placeholder="Search recent runs">
        </div>
    </div>

    <section class="mobile-section-gap">
        <div id="mobile-history-list" class="mobile-stack">
            <div class="mobile-list-item text-[13px] text-slate-500">Loading recent activity...</div>
        </div>
        <div id="mobile-history-pagination" class="mt-3"></div>
    </section>

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileHistories', () => {
                const list = document.getElementById('mobile-history-list');
                const pagination = document.getElementById('mobile-history-pagination');
                const searchInput = document.getElementById('mobile-history-search');
                const perPage = 10;
                let timer = null;
                let currentPage = 1;
                let currentKeyword = '';

                const loadingState = () => '<div class="mobile-list-item text-[13px] text-slate-500">Loading recent activity...</div>';
                const emptyState = (message) => `<div class="mobile-empty">${message}</div>`;
                const escapeHtml = window.Perfectlum.escapeHtml;

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

                async function loadHistories(keyword = '', page = 1) {
                    currentKeyword = keyword;
                    currentPage = page;
                    list.innerHTML = loadingState();
                    pagination.innerHTML = '';

                    try {
                        const response = await window.Perfectlum.request(`/api/histories?limit=${perPage}&page=${page}${keyword ? `&search=${encodeURIComponent(keyword)}` : ''}`);
                        const rows = response.data || [];
                        const total = Number(response.total || 0);

                        if (!rows.length) {
                            list.innerHTML = emptyState('No recent runs matched this filter.');
                            return;
                        }

                        list.innerHTML = rows.map((item) => `
                            <a href="${@json(url('/m/displays'))}/${item.displayId}" class="mobile-list-item compact block">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="mobile-type-pill ${item.result === 'passed' ? 'run' : 'alert'}">${item.result === 'passed' ? 'Pass' : 'Fail'}</span>
                                            <p class="mobile-meta mobile-clamp-1">${escapeHtml(item.displayName)}</p>
                                        </div>
                                        <p class="mt-1 text-[13px] font-semibold leading-[1.3] text-slate-950">${escapeHtml(item.name)}</p>
                                        <p class="mt-0.5 mobile-meta mobile-clamp-1">${escapeHtml([item.wsName, item.wgName, item.facName].filter(Boolean).join(' • '))}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-medium text-slate-500">${escapeHtml(item.time)}</span>
                                </div>
                            </a>
                        `).join('');

                        pagination.innerHTML = renderPager(total, page, perPage);
                    } catch (error) {
                        list.innerHTML = emptyState('Unable to load recent activity right now.');
                    }
                }

                pagination.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-page]');
                    if (!button || button.hasAttribute('disabled')) {
                        return;
                    }

                    loadHistories(currentKeyword, Number(button.dataset.page));
                });

                searchInput.addEventListener('input', () => {
                    window.clearTimeout(timer);
                    timer = window.setTimeout(() => loadHistories(searchInput.value.trim(), 1), 220);
                });

                loadHistories();
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
