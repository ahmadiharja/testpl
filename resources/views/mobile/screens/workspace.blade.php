@extends('mobile.layouts.app')

@php
    $summary = $workspaceSummary ?? [
        'facilities' => 0,
        'workgroups' => 0,
        'workstations' => 0,
        'displays' => 0,
        'displayAlerts' => 0,
        'staleWorkstations' => 0,
        'dueTasks' => 0,
        'scopeLabel' => 'Workspace',
    ];

    $gatewayCards = [
        [
            'label' => 'Facilities',
            'count' => $summary['facilities'] ?? 0,
            'copy' => 'Top-level locations and scope boundaries.',
            'route' => route('mobile.facilities', ['from' => 'workspace']),
            'icon' => 'building-2',
            'tone' => 'sky',
        ],
        [
            'label' => 'Workgroups',
            'count' => $summary['workgroups'] ?? 0,
            'copy' => 'Operational groups inside each facility.',
            'route' => route('mobile.workgroups', ['from' => 'workspace']),
            'icon' => 'folders',
            'tone' => 'indigo',
        ],
        [
            'label' => 'Workstations',
            'count' => $summary['workstations'] ?? 0,
            'copy' => 'Endpoint clients, sync state, and attached displays.',
            'route' => route('mobile.workstations', ['from' => 'workspace']),
            'icon' => 'computer',
            'tone' => 'amber',
        ],
        [
            'label' => 'Displays',
            'count' => $summary['displays'] ?? 0,
            'copy' => 'Managed displays and current device health.',
            'route' => route('mobile.displays', ['from' => 'workspace']),
            'icon' => 'monitor',
            'tone' => 'emerald',
        ],
    ];

    $monitorCards = [
        [
            'label' => 'Displays Not OK',
            'value' => $summary['displayAlerts'] ?? 0,
            'copy' => 'Displays that currently require follow-up.',
            'route' => route('mobile.displays', ['status' => 2, 'sort' => 'updated_at', 'order' => 'desc', 'from' => 'workspace']),
            'tone' => 'alert',
        ],
        [
            'label' => 'Connection Watchlist',
            'value' => $summary['staleWorkstations'] ?? 0,
            'copy' => 'Offline or stale workstations that need review.',
            'route' => route('mobile.workstations', ['stale' => 1, 'from' => 'workspace']),
            'tone' => 'warn',
        ],
        [
            'label' => 'Due Tasks',
            'value' => $summary['dueTasks'] ?? 0,
            'copy' => 'Scheduled work that already needs action.',
            'route' => route('mobile.tasks', ['from' => 'workspace']),
            'tone' => 'info',
        ],
    ];
@endphp

@push('head')
    <style>
        .mobile-workspace-shell {
            display: grid;
            gap: 0.95rem;
        }

        .mobile-workspace-search-shell {
            position: sticky;
            top: 0.3rem;
            z-index: 8;
            border-radius: 1.18rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background:
                radial-gradient(circle at top left, rgba(14, 165, 233, 0.08), transparent 44%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
            padding: 0.92rem;
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.05);
            backdrop-filter: blur(18px);
        }

        .mobile-workspace-kicker {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-workspace-title {
            margin-top: 0.34rem;
            font-size: 1.08rem;
            font-weight: 650;
            letter-spacing: -0.03em;
            color: #0f172a;
        }

        .mobile-workspace-copy {
            margin-top: 0.24rem;
            font-size: 12px;
            line-height: 1.45;
            color: #64748b;
        }

        .mobile-workspace-scope {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            border: 1px solid rgba(14, 165, 233, 0.14);
            background: rgba(14, 165, 233, 0.08);
            padding: 0.32rem 0.7rem;
            font-size: 10px;
            font-weight: 700;
            color: #0369a1;
        }

        .mobile-workspace-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.7rem;
        }

        .mobile-workspace-card {
            display: block;
            border-radius: 1.08rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.96);
            padding: 0.86rem;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.035);
            transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
        }

        .mobile-workspace-card:active {
            transform: scale(0.992);
        }

        .mobile-workspace-card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 2.35rem;
            width: 2.35rem;
            border-radius: 0.95rem;
        }

        .mobile-workspace-card-icon.sky {
            background: rgba(14, 165, 233, 0.12);
            color: #0369a1;
        }

        .mobile-workspace-card-icon.indigo {
            background: rgba(99, 102, 241, 0.12);
            color: #4338ca;
        }

        .mobile-workspace-card-icon.amber {
            background: rgba(245, 158, 11, 0.14);
            color: #b45309;
        }

        .mobile-workspace-card-icon.emerald {
            background: rgba(16, 185, 129, 0.12);
            color: #047857;
        }

        .mobile-workspace-card-label {
            margin-top: 0.82rem;
            font-size: 12px;
            font-weight: 600;
            color: #0f172a;
        }

        .mobile-workspace-card-count {
            margin-top: 0.28rem;
            font-size: 1.12rem;
            font-weight: 700;
            line-height: 1;
            color: #0f172a;
        }

        .mobile-workspace-card-copy {
            margin-top: 0.38rem;
            font-size: 11px;
            line-height: 1.45;
            color: #64748b;
        }

        .mobile-workspace-monitor-grid {
            display: grid;
            gap: 0.65rem;
        }

        .mobile-workspace-monitor {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.8rem;
            border-radius: 1.02rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.96);
            padding: 0.82rem 0.86rem;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.03);
        }

        .mobile-workspace-monitor:active {
            transform: scale(0.995);
        }

        .mobile-workspace-monitor-value {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1;
            color: #0f172a;
        }

        .mobile-workspace-monitor-label {
            margin-top: 0.22rem;
            font-size: 12px;
            font-weight: 600;
            color: #0f172a;
        }

        .mobile-workspace-monitor-copy {
            margin-top: 0.18rem;
            font-size: 11px;
            line-height: 1.42;
            color: #64748b;
        }

        .mobile-workspace-monitor-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 3.2rem;
            border-radius: 999px;
            padding: 0.34rem 0.66rem;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .mobile-workspace-monitor-pill.alert {
            background: rgba(244, 63, 94, 0.1);
            color: #be123c;
        }

        .mobile-workspace-monitor-pill.warn {
            background: rgba(245, 158, 11, 0.12);
            color: #b45309;
        }

        .mobile-workspace-monitor-pill.info {
            background: rgba(14, 165, 233, 0.1);
            color: #0369a1;
        }

        .mobile-workspace-section-title {
            font-size: 14px;
            font-weight: 650;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .mobile-workspace-section-copy {
            margin-top: 0.2rem;
            font-size: 12px;
            line-height: 1.42;
            color: #64748b;
        }

        .mobile-workspace-result-group {
            display: grid;
            gap: 0.7rem;
        }

        .mobile-workspace-result-card {
            display: block;
            padding: 0.8rem 0.84rem;
            transition: background 160ms ease, transform 160ms ease;
        }

        .mobile-workspace-result-card:active {
            background: rgba(14, 165, 233, 0.05);
            transform: scale(0.995);
        }

        .mobile-workspace-result-card + .mobile-workspace-result-card {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mobile-workspace-result-title {
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

        .mobile-workspace-result-copy {
            margin-top: 0.28rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 12px;
            line-height: 1.42;
            color: #475569;
        }

        .mobile-workspace-result-skeleton {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            border: 1px solid rgba(226, 232, 240, 0.9);
            background: rgba(255, 255, 255, 0.97);
            padding: 0.84rem;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.028);
        }

        .mobile-workspace-result-skeleton::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.72), transparent);
            animation: mobileWorkspaceSkeletonSweep 1.12s ease-in-out infinite;
        }

        .mobile-workspace-skeleton-pill,
        .mobile-workspace-skeleton-line,
        .mobile-workspace-skeleton-action {
            background: linear-gradient(90deg, rgba(226, 232, 240, 0.84), rgba(241, 245, 249, 0.96), rgba(226, 232, 240, 0.84));
            background-size: 200% 100%;
            animation: mobileWorkspaceSkeletonPulse 1.35s ease-in-out infinite;
        }

        .mobile-workspace-skeleton-pill,
        .mobile-workspace-skeleton-line,
        .mobile-workspace-skeleton-action {
            border-radius: 999px;
        }

        .mobile-workspace-skeleton-pill {
            height: 1.24rem;
            width: 4.2rem;
        }

        .mobile-workspace-skeleton-line {
            height: 0.82rem;
        }

        .mobile-workspace-skeleton-line.title {
            margin-top: 0.46rem;
            width: 9.8rem;
            height: 0.96rem;
        }

        .mobile-workspace-skeleton-line.copy {
            margin-top: 0.34rem;
            width: 11.8rem;
        }

        .mobile-workspace-skeleton-action {
            height: 1.5rem;
            width: 3rem;
        }

        @keyframes mobileWorkspaceSkeletonSweep {
            100% {
                transform: translateX(100%);
            }
        }

        @keyframes mobileWorkspaceSkeletonPulse {
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
    <div class="mobile-workspace-shell">
        <section class="mobile-workspace-search-shell">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="mobile-workspace-kicker">Workspace</p>
                    <h2 class="mobile-workspace-title">Browse and monitor the structure</h2>
                    <p class="mobile-workspace-copy">Search facility, workgroup, workstation, or display from one place.</p>
                </div>
                <span class="mobile-workspace-scope">{{ $summary['scopeLabel'] ?? 'Workspace' }}</span>
            </div>

            <div class="mobile-searchbar mt-4">
                <i data-lucide="search" class="mobile-searchbar-icon"></i>
                <input id="mobile-workspace-search" type="search" class="mobile-input mobile-search-input" placeholder="Search facility, workgroup, workstation, or display">
            </div>
        </section>

        <div id="mobile-workspace-home" class="grid gap-4">
            <section>
                <div class="mobile-section-head">
                    <div>
                        <p class="mobile-workspace-section-title">Browse by level</p>
                        <p class="mobile-workspace-section-copy">Jump into the right part of the operational structure.</p>
                    </div>
                </div>

                <div class="mobile-workspace-grid">
                    @foreach ($gatewayCards as $card)
                        <a href="{{ $card['route'] }}" data-mobile-nav="forward" data-workspace-gateway="1" class="mobile-workspace-card">
                            <span class="mobile-workspace-card-icon {{ $card['tone'] }}">
                                <i data-lucide="{{ $card['icon'] }}" class="h-5 w-5"></i>
                            </span>
                            <p class="mobile-workspace-card-label">{{ $card['label'] }}</p>
                            <p class="mobile-workspace-card-count">{{ number_format((int) $card['count']) }}</p>
                            <p class="mobile-workspace-card-copy">{{ $card['copy'] }}</p>
                        </a>
                    @endforeach
                </div>
            </section>

            <section>
                <div class="mobile-section-head">
                    <div>
                        <p class="mobile-workspace-section-title">Operational shortcuts</p>
                        <p class="mobile-workspace-section-copy">Jump straight into displays not OK, the connection watchlist, or due tasks.</p>
                    </div>
                </div>

                <div class="mobile-workspace-monitor-grid">
                    @foreach ($monitorCards as $card)
                        <a href="{{ $card['route'] }}" data-mobile-nav="forward" class="mobile-workspace-monitor">
                            <div class="min-w-0">
                                <p class="mobile-workspace-monitor-value">{{ number_format((int) $card['value']) }}</p>
                                <p class="mobile-workspace-monitor-label">{{ $card['label'] }}</p>
                                <p class="mobile-workspace-monitor-copy">{{ $card['copy'] }}</p>
                            </div>
                            <span class="mobile-workspace-monitor-pill {{ $card['tone'] }}">Open</span>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>

        <section id="mobile-workspace-results-shell" class="hidden">
            <div class="mobile-section-head">
                <div>
                    <p class="mobile-workspace-section-title">Workspace results</p>
                    <p id="mobile-workspace-results-copy" class="mobile-workspace-section-copy">Grouped by hierarchy level.</p>
                </div>
            </div>

            <div id="mobile-workspace-results" class="mobile-workspace-result-group"></div>
        </section>
    </div>

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileWorkspace', () => {
                    const input = document.getElementById('mobile-workspace-search');
                    const home = document.getElementById('mobile-workspace-home');
                    const resultsShell = document.getElementById('mobile-workspace-results-shell');
                    const results = document.getElementById('mobile-workspace-results');
                    const resultsCopy = document.getElementById('mobile-workspace-results-copy');
                    const escapeHtml = window.Perfectlum.escapeHtml;
                    const gatewayLinks = Array.from(document.querySelectorAll('[data-workspace-gateway="1"]'));
                    const cache = new Map();
                    let requestToken = 0;
                    let timer = null;

                    const levelRoutes = {
                        facility: @json(route('mobile.facilities', ['from' => 'workspace'])),
                        workgroup: @json(route('mobile.workgroups', ['from' => 'workspace'])),
                        workstation: @json(route('mobile.workstations', ['from' => 'workspace'])),
                        display: @json(route('mobile.displays', ['from' => 'workspace'])),
                    };

                    const resolveMobileHref = (item) => {
                        const returnTo = encodeURIComponent(`${window.location.pathname}${window.location.search}`);

                        if (item.type === 'display') {
                            return `${@json(url('/m/displays'))}/${item.recordId}?from=workspace&return_to=${returnTo}`;
                        }

                        if (item.type === 'facility') {
                            return `${@json(url('/m/facilities'))}/${item.recordId}?from=workspace&return_to=${returnTo}`;
                        }

                        if (item.type === 'workgroup') {
                            const params = new URLSearchParams();
                            if (item.facilityId) params.set('facility_id', item.facilityId);
                            if (item.facilityName) params.set('facility_name', item.facilityName);
                            params.set('from', 'workspace');
                            params.set('return_to', `${window.location.pathname}${window.location.search}`);
                            return `${@json(url('/m/workgroups'))}/${item.recordId}?${params.toString()}`;
                        }

                        if (item.type === 'workstation') {
                            const params = new URLSearchParams();
                            if (item.facilityId) params.set('facility_id', item.facilityId);
                            if (item.workgroupId) params.set('workgroup_id', item.workgroupId);
                            if (item.facilityName) params.set('facility_name', item.facilityName);
                            if (item.workgroupName) params.set('workgroup_name', item.workgroupName);
                            params.set('from', 'workspace');
                            params.set('return_to', `${window.location.pathname}${window.location.search}`);
                            return `${@json(url('/m/workstations'))}/${item.recordId}?${params.toString()}`;
                        }

                        return item.url || '#';
                    };

                    const emptyState = (message) => `<div class="mobile-empty">${escapeHtml(message)}</div>`;
                    const loadingState = () => Array.from({ length: 4 }).map(() => `
                        <div class="mobile-workspace-result-skeleton" aria-hidden="true">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="mobile-workspace-skeleton-pill"></div>
                                    <div class="mobile-workspace-skeleton-line title"></div>
                                    <div class="mobile-workspace-skeleton-line copy"></div>
                                </div>
                                <div class="mobile-workspace-skeleton-action"></div>
                            </div>
                        </div>
                    `).join('');
                    const openSearchMode = () => {
                        home.classList.add('hidden');
                        resultsShell.classList.remove('hidden');
                    };
                    const closeSearchMode = () => {
                        home.classList.remove('hidden');
                        resultsShell.classList.add('hidden');
                        results.innerHTML = '';
                    };

                    const renderGroup = (label, type, rows) => {
                        if (!rows.length) {
                            return '';
                        }

                        return `
                            <section class="mobile-panel compact">
                                <div class="mobile-section-head mb-3">
                                    <div>
                                        <p class="mobile-workspace-section-title">${escapeHtml(label)}</p>
                                        <p class="mobile-workspace-section-copy">${rows.length} result${rows.length > 1 ? 's' : ''}</p>
                                    </div>
                                    <a href="${levelRoutes[type]}" data-mobile-nav="forward" class="mobile-action-link">Browse</a>
                                </div>
                                <div class="mobile-stack">
                                    ${rows.map((item) => `
                                        <a href="${resolveMobileHref(item)}" data-mobile-nav="forward" class="mobile-workspace-result-card">
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="mobile-type-pill run">${escapeHtml(type)}</span>
                                                        <p class="mobile-meta mobile-clamp-1">${escapeHtml(item.subtitle || 'Open result')}</p>
                                                    </div>
                                                    <p class="mobile-workspace-result-title">${escapeHtml(item.title)}</p>
                                                    <p class="mobile-workspace-result-copy">${escapeHtml(item.subtitle || 'Open result')}</p>
                                                </div>
                                                <span class="shrink-0 rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-medium text-slate-500">Open</span>
                                            </div>
                                        </a>
                                    `).join('')}
                                </div>
                            </section>
                        `;
                    };

                    async function searchWorkspace(keyword) {
                        const trimmed = keyword.trim();
                        if (trimmed.length < 2) {
                            closeSearchMode();
                            return;
                        }

                        const cacheKey = trimmed.toLowerCase();
                        const cached = cache.get(cacheKey);
                        const currentRequest = ++requestToken;

                        openSearchMode();
                        if (cached) {
                            results.innerHTML = cached;
                            resultsCopy.textContent = `Looking for “${trimmed}” across the operational hierarchy.`;
                            return;
                        }

                        results.innerHTML = loadingState();
                        resultsCopy.textContent = `Looking for “${trimmed}” across the operational hierarchy.`;

                        try {
                            const response = await window.Perfectlum.request(`/api/global-search?q=${encodeURIComponent(trimmed)}&limit=16`);
                            if (currentRequest !== requestToken) {
                                return;
                            }
                            const rows = response.data || [];

                            if (!rows.length) {
                                results.innerHTML = emptyState('No workspace records matched your search.');
                                return;
                            }

                            const grouped = {
                                facility: rows.filter((item) => item.type === 'facility'),
                                workgroup: rows.filter((item) => item.type === 'workgroup'),
                                workstation: rows.filter((item) => item.type === 'workstation'),
                                display: rows.filter((item) => item.type === 'display'),
                            };

                            const html = [
                                renderGroup('Facilities', 'facility', grouped.facility),
                                renderGroup('Workgroups', 'workgroup', grouped.workgroup),
                                renderGroup('Workstations', 'workstation', grouped.workstation),
                                renderGroup('Displays', 'display', grouped.display),
                            ].filter(Boolean).join('');
                            cache.set(cacheKey, html);
                            results.innerHTML = html;
                        } catch (error) {
                            if (currentRequest !== requestToken) {
                                return;
                            }
                            results.innerHTML = emptyState('Unable to search the workspace right now.');
                        }
                    }

                    input.addEventListener('input', () => {
                        window.clearTimeout(timer);
                        timer = window.setTimeout(() => searchWorkspace(input.value), 220);
                    });

                    const prefetchGateway = (link) => {
                        const href = link?.getAttribute('href');
                        if (!href || !window.Perfectlum?.prefetchMobilePage) {
                            return;
                        }

                        window.Perfectlum.prefetchMobilePage(href).catch(() => null);
                    };

                    gatewayLinks.forEach((link) => {
                        link.addEventListener('pointerdown', () => prefetchGateway(link), { passive: true });
                        link.addEventListener('mouseenter', () => prefetchGateway(link), { passive: true });
                        link.addEventListener('touchstart', () => prefetchGateway(link), { passive: true });
                    });

                    const warmup = () => gatewayLinks.forEach((link) => prefetchGateway(link));
                    warmup();
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
