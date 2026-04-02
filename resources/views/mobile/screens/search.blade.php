@extends('mobile.layouts.app')

@push('head')
    <style>
        .mobile-search-card {
            display: block;
            padding: 0.8rem 0.84rem;
            transition: background 160ms ease, transform 160ms ease;
        }

        .mobile-search-card:active {
            background: rgba(14, 165, 233, 0.05);
            transform: scale(0.995);
        }

        .mobile-search-card + .mobile-search-card {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mobile-search-title {
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

        .mobile-search-subtitle {
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
            <input id="mobile-global-search" type="search" class="mobile-input mobile-search-input" placeholder="Search workspace">
        </div>
    </div>

    <section class="mobile-section-gap">
        <div id="mobile-search-results" class="mobile-stack">
            <div class="mobile-list-item text-[13px] text-slate-500">Search across facilities, workgroups, workstations, and displays.</div>
        </div>
    </section>

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileSearch', () => {
                const input = document.getElementById('mobile-global-search');
                const results = document.getElementById('mobile-search-results');
                let timer = null;

                const loadingState = () => '<div class="mobile-list-item text-[13px] text-slate-500">Searching...</div>';
                const emptyState = (message) => `<div class="mobile-empty">${message}</div>`;
                const escapeHtml = window.Perfectlum.escapeHtml;
                const resolveMobileHref = (item) => {
                    const returnTo = encodeURIComponent(`${window.location.pathname}${window.location.search}`);

                    if (item.type === 'display') {
                        return `${@json(url('/m/displays'))}/${item.recordId}?return_to=${returnTo}`;
                    }

                    if (item.type === 'facility') {
                        return `${@json(url('/m/facilities'))}/${item.recordId}?return_to=${returnTo}`;
                    }

                    if (item.type === 'workgroup') {
                        const params = new URLSearchParams();
                        if (item.facilityId) params.set('facility_id', item.facilityId);
                        if (item.facilityName) params.set('facility_name', item.facilityName);
                        params.set('return_to', `${window.location.pathname}${window.location.search}`);
                        return `${@json(url('/m/workgroups'))}/${item.recordId}?${params.toString()}`;
                    }

                    if (item.type === 'workstation') {
                        const params = new URLSearchParams();
                        if (item.facilityId) params.set('facility_id', item.facilityId);
                        if (item.workgroupId) params.set('workgroup_id', item.workgroupId);
                        if (item.facilityName) params.set('facility_name', item.facilityName);
                        if (item.workgroupName) params.set('workgroup_name', item.workgroupName);
                        params.set('return_to', `${window.location.pathname}${window.location.search}`);
                        return `${@json(url('/m/workstations'))}/${item.recordId}?${params.toString()}`;
                    }

                    return item.url || '#';
                };

                async function searchWorkspace(keyword) {
                    if (keyword.length < 2) {
                        results.innerHTML = emptyState('Type at least 2 characters to search.');
                        return;
                    }

                    results.innerHTML = loadingState();

                    try {
                        const response = await window.Perfectlum.request(`/api/global-search?q=${encodeURIComponent(keyword)}&limit=12`);
                        const rows = response.data || [];

                        if (!rows.length) {
                            results.innerHTML = emptyState('No workspace results matched your search.');
                            return;
                        }

                        results.innerHTML = rows.map((item) => {
                            const href = resolveMobileHref(item);

                            return `
                                <a href="${href}" class="mobile-search-card">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="mobile-type-pill run">${escapeHtml(item.type)}</span>
                                                <p class="mobile-meta mobile-clamp-1">${escapeHtml(item.subtitle || 'Open result')}</p>
                                            </div>
                                            <p class="mobile-search-title">${escapeHtml(item.title)}</p>
                                            <p class="mobile-search-subtitle">${escapeHtml(item.subtitle || 'Open result')}</p>
                                        </div>
                                        <span class="shrink-0 rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-medium text-slate-500">Open</span>
                                    </div>
                                </a>
                            `;
                        }).join('');
                    } catch (error) {
                        results.innerHTML = emptyState('Unable to search right now.');
                    }
                }

                input.addEventListener('input', () => {
                    window.clearTimeout(timer);
                    timer = window.setTimeout(() => searchWorkspace(input.value.trim()), 220);
                });
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
