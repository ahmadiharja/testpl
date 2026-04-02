@extends('mobile.layouts.app')

@push('head')
    <style>
        .mobile-facility-list {
            display: grid;
            gap: 0.58rem;
        }

        .mobile-facility-card {
            display: block;
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.97);
            padding: 0.78rem 0.8rem;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.032);
            transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
        }

        .mobile-facility-card:active {
            transform: scale(0.994);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.042);
        }

        .mobile-facility-card.attention {
            border-color: rgba(251, 113, 133, 0.18);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 241, 242, 0.45));
        }

        .mobile-facility-title {
            margin-top: 0.24rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 14px;
            font-weight: 650;
            line-height: 1.28;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .mobile-facility-detail {
            margin-top: 0.24rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 12px;
            line-height: 1.42;
            color: #475569;
        }

        .mobile-facility-arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 1.78rem;
            width: 1.78rem;
            flex: 0 0 auto;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(248, 250, 252, 0.95);
            font-size: 15px;
            line-height: 1;
            color: #64748b;
        }

        .mobile-facility-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.36rem 0.5rem;
        }

        .mobile-facility-stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.38rem;
            margin-top: 0.62rem;
        }

        .mobile-facility-stat {
            display: inline-flex;
            align-items: center;
            min-height: 1.6rem;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(248, 250, 252, 0.96);
            padding: 0.25rem 0.54rem;
            font-size: 10.25px;
            font-weight: 600;
            line-height: 1;
            color: #475569;
        }

        .mobile-workgroup-list {
            display: grid;
            gap: 0.58rem;
        }

        .mobile-workgroup-card {
            display: block;
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.97);
            padding: 0.78rem 0.8rem;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.032);
            transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
        }

        .mobile-workgroup-card:active {
            transform: scale(0.994);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.042);
        }

        .mobile-workgroup-card.attention {
            border-color: rgba(251, 113, 133, 0.18);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 241, 242, 0.45));
        }

        .mobile-workgroup-title {
            margin-top: 0.24rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 14px;
            font-weight: 650;
            line-height: 1.28;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .mobile-workgroup-detail {
            margin-top: 0.24rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 12px;
            line-height: 1.42;
            color: #475569;
        }

        .mobile-workgroup-arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 1.78rem;
            width: 1.78rem;
            flex: 0 0 auto;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(248, 250, 252, 0.95);
            font-size: 15px;
            line-height: 1;
            color: #64748b;
        }

        .mobile-workgroup-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.36rem 0.5rem;
        }

        .mobile-workgroup-stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.38rem;
            margin-top: 0.62rem;
        }

        .mobile-workgroup-stat {
            display: inline-flex;
            align-items: center;
            min-height: 1.6rem;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(248, 250, 252, 0.96);
            padding: 0.25rem 0.54rem;
            font-size: 10.25px;
            font-weight: 600;
            line-height: 1;
            color: #475569;
        }

        .mobile-facility-context {
            border-radius: 1.08rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background:
                radial-gradient(circle at top left, rgba(14, 165, 233, 0.08), transparent 42%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
            padding: 0.9rem 0.92rem;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.035);
        }

        .mobile-facility-context.attention {
            border-color: rgba(251, 113, 133, 0.18);
            background:
                radial-gradient(circle at top left, rgba(251, 113, 133, 0.08), transparent 42%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 241, 242, 0.6));
        }

        .mobile-facility-context-title {
            margin-top: 0.3rem;
            font-size: 17px;
            font-weight: 650;
            line-height: 1.24;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .mobile-workgroup-skeleton,
        .mobile-facility-skeleton {
            position: relative;
            overflow: hidden;
            pointer-events: none;
        }

        .mobile-workgroup-skeleton::after,
        .mobile-facility-skeleton::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.72), transparent);
            animation: mobileWorkgroupSkeletonSweep 1.15s ease-in-out infinite;
        }

        .mobile-skeleton-line,
        .mobile-skeleton-chip,
        .mobile-skeleton-circle {
            background: linear-gradient(90deg, rgba(226, 232, 240, 0.84), rgba(241, 245, 249, 0.96), rgba(226, 232, 240, 0.84));
            background-size: 200% 100%;
            animation: mobileWorkgroupSkeletonPulse 1.4s ease-in-out infinite;
        }

        .mobile-skeleton-line {
            border-radius: 999px;
        }

        .mobile-skeleton-chip {
            border-radius: 999px;
            height: 1.4rem;
            width: 4.7rem;
        }

        .mobile-skeleton-circle {
            border-radius: 999px;
            height: 1.9rem;
            width: 1.9rem;
            flex: 0 0 auto;
        }

        @keyframes mobileWorkgroupSkeletonSweep {
            100% {
                transform: translateX(100%);
            }
        }

        @keyframes mobileWorkgroupSkeletonPulse {
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
    @php
        $initialKeyword = (string) request('search', '');
        $initialPage = max(1, (int) request('page', 1));
        $initialType = (string) request('type', '');
    @endphp
    <div class="mobile-search-shell">
        <div class="mobile-searchbar">
            <i data-lucide="search" class="mobile-searchbar-icon"></i>
            <input id="mobile-facility-search" type="search" class="mobile-input mobile-search-input" placeholder="Search facilities">
        </div>
        <div class="mt-3 flex gap-2 overflow-x-auto pb-1 no-scrollbar">
            <button type="button" data-facility-filter="" class="mobile-facility-filter mobile-filter-chip active">All</button>
            <button type="button" data-facility-filter="failed" class="mobile-facility-filter mobile-filter-chip">Needs Attention</button>
            <button type="button" data-facility-filter="ok" class="mobile-facility-filter mobile-filter-chip">Healthy</button>
        </div>
    </div>

    <section class="mobile-section-gap">
        <div id="mobile-facility-list" class="mobile-facility-list">
            <div class="mobile-facility-card mobile-facility-skeleton" aria-hidden="true">
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="mobile-facility-meta">
                                    <span class="mobile-skeleton-chip"></span>
                                    <span class="mobile-skeleton-line h-[0.72rem] w-24"></span>
                                </div>
                                <div class="mobile-skeleton-line mt-2 h-[1rem] w-44"></div>
                                <div class="mobile-skeleton-line mt-2 h-[0.78rem] w-36"></div>
                            </div>
                            <span class="mobile-skeleton-circle"></span>
                        </div>
                        <div class="mobile-facility-stat-row">
                            <span class="mobile-skeleton-chip" style="width: 6rem"></span>
                            <span class="mobile-skeleton-chip" style="width: 5rem"></span>
                            <span class="mobile-skeleton-chip" style="width: 4.6rem"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mobile-facility-card mobile-facility-skeleton" aria-hidden="true">
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="mobile-facility-meta">
                                    <span class="mobile-skeleton-chip"></span>
                                    <span class="mobile-skeleton-line h-[0.72rem] w-20"></span>
                                </div>
                                <div class="mobile-skeleton-line mt-2 h-[1rem] w-40"></div>
                                <div class="mobile-skeleton-line mt-2 h-[0.78rem] w-28"></div>
                            </div>
                            <span class="mobile-skeleton-circle"></span>
                        </div>
                        <div class="mobile-facility-stat-row">
                            <span class="mobile-skeleton-chip" style="width: 5.5rem"></span>
                            <span class="mobile-skeleton-chip" style="width: 4.8rem"></span>
                            <span class="mobile-skeleton-chip" style="width: 4.3rem"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="mobile-facility-pagination" class="mt-3"></div>
    </section>

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileFacilities', () => {
                const stage = document.getElementById('mobile-page-stage');
                const mainContent = document.getElementById('mobile-main-content');
                const appbarBack = document.getElementById('mobile-appbar-back');
                const appbarTitle = document.getElementById('mobile-appbar-title');
                const siteName = @json($siteName ?? 'PerfectLum');
                const workgroupsRoute = @json(route('mobile.workgroups'));
                const workstationsRoute = @json(route('mobile.workstations'));
                const displaysRoute = @json(route('mobile.displays'));
                const facilitiesRoute = @json(route('mobile.facilities'));
                const rootBackUrl = @json($backUrl);
                const initialKeyword = @json($initialKeyword);
                const initialPage = @json($initialPage);
                const initialType = @json($initialType);
                const initialFacilitiesUrl = window.location.href;
                const perPage = 10;
                const prefetchKeyPrefix = 'perfectlum-mobile-prefetch:';
                const prefetchInflight = new Map();
                const facilitiesCache = new Map();
                let facilitiesRequestToken = 0;
                const hierarchyCache = {
                    workgroups: new Map(),
                    workstations: new Map(),
                    displays: new Map(),
                };
                const hierarchyRequestToken = {
                    workgroups: 0,
                    workstations: 0,
                    displays: 0,
                };
                const bottomNav = document.getElementById('mobile-bottom-nav');
                const bottomNavSurface = document.getElementById('mobile-bottom-nav-surface');
                let list = null;
                let pagination = null;
                let searchInput = null;
                let filterButtons = [];
                let timer = null;
                let currentPage = Number(initialPage || 1);
                let currentKeyword = String(initialKeyword || '');
                let type = String(initialType || '');
                let clientHierarchyLevel = 'facilities';
                const hierarchyStack = [];
                if ('scrollRestoration' in history) {
                    history.scrollRestoration = 'manual';
                }

                const loadingState = () => renderFacilitiesSkeleton();
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

                function resolveFacilityElements() {
                    list = document.getElementById('mobile-facility-list');
                    pagination = document.getElementById('mobile-facility-pagination');
                    searchInput = document.getElementById('mobile-facility-search');
                    filterButtons = Array.from(document.querySelectorAll('.mobile-facility-filter'));
                }

                function setHierarchyHeader({ title, backUrl = facilitiesRoute, backVisible = false, hierarchyBack = false }) {
                    if (appbarTitle) {
                        appbarTitle.textContent = title || 'Workspace';
                    }

                    if (!appbarBack) {
                        return;
                    }

                    if (backVisible) {
                        appbarBack.classList.remove('hidden');
                        appbarBack.href = backUrl;
                        if (hierarchyBack) {
                            appbarBack.dataset.hierarchyNav = '1';
                        } else {
                            delete appbarBack.dataset.hierarchyNav;
                        }
                    } else {
                        appbarBack.classList.add('hidden');
                        appbarBack.href = '#';
                        delete appbarBack.dataset.hierarchyNav;
                    }
                }

                function captureHierarchySnapshot(restore, url = window.location.href) {
                    if (!mainContent) {
                        return null;
                    }

                    return {
                        html: mainContent.innerHTML,
                        title: appbarTitle?.textContent || document.title,
                        backHref: appbarBack?.getAttribute('href') || '#',
                        backVisible: !!appbarBack && !appbarBack.classList.contains('hidden'),
                        hierarchyBack: !!appbarBack?.dataset.hierarchyNav,
                        scrollY: window.scrollY || 0,
                        restore,
                        url,
                    };
                }

                function restoreHierarchySnapshot(snapshot) {
                    if (!snapshot || !mainContent) {
                        return;
                    }

                    animateMainTransition('back', () => {
                        document.title = snapshot.title ? `${snapshot.title} | ${siteName}` : document.title;
                        mainContent.innerHTML = snapshot.html;
                        setHierarchyHeader({
                            title: snapshot.title,
                            backUrl: snapshot.backHref,
                            backVisible: snapshot.backVisible,
                            hierarchyBack: snapshot.hierarchyBack,
                        });
                        ensureBottomNavVisible('workspace');
                        lucide.createIcons();

                        if (typeof snapshot.restore === 'function') {
                            snapshot.restore();
                        }

                        window.requestAnimationFrame(() => {
                            window.scrollTo(0, snapshot.scrollY || 0);
                        });
                    });
                }

                function animateMainTransition(direction, render) {
                    const animatedRoot = stage || mainContent;
                    if (animatedRoot && window.Perfectlum?.animateMobileSwap) {
                        window.Perfectlum.animateMobileSwap(direction, animatedRoot, render);
                        return;
                    }

                    if (!animatedRoot) {
                        render();
                        return;
                    }

                    render();
                }

                function ensureBottomNavVisible(activeKey = 'workspace') {
                    if (!bottomNav) {
                        return;
                    }

                    bottomNav.hidden = false;
                    bottomNav.style.removeProperty('display');
                    bottomNav.style.removeProperty('visibility');
                    bottomNav.style.removeProperty('opacity');

                    if (bottomNavSurface) {
                        bottomNavSurface.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
                        bottomNavSurface.classList.add('opacity-100');
                    }

                    const alpineData = bottomNav.__x?.$data;
                    if (alpineData && Object.prototype.hasOwnProperty.call(alpineData, 'hierarchyOpen')) {
                        alpineData.hierarchyOpen = false;
                    }

                    bottomNav.querySelectorAll('[data-mobile-bottom-key]').forEach((item) => {
                        item.classList.toggle('active', item.dataset.mobileBottomKey === activeKey);
                    });
                }

                function syncFilters() {
                    filterButtons.forEach((button) => {
                        button.className = `mobile-facility-filter mobile-filter-chip ${button.dataset.facilityFilter === type ? 'active' : ''}`;
                    });
                }

                function syncRouteState(keyword = currentKeyword, page = currentPage) {
                    const url = new URL(window.location.href);

                    if (keyword) {
                        url.searchParams.set('search', keyword);
                    } else {
                        url.searchParams.delete('search');
                    }

                    if (type) {
                        url.searchParams.set('type', type);
                    } else {
                        url.searchParams.delete('type');
                    }

                    if (page > 1) {
                        url.searchParams.set('page', String(page));
                    } else {
                        url.searchParams.delete('page');
                    }

                    history.replaceState(history.state, '', `${url.pathname}${url.search}`);
                }

                function buildWorkgroupsApiUrl(facilityId) {
                    const params = new URLSearchParams({
                        facility_id: String(facilityId),
                        limit: '10',
                        page: '1',
                    });

                    return `/api/workgroups?${params.toString()}`;
                }

                function storeFacilityContext(link) {
                    const facilityId = link?.dataset.facilityId;
                    if (!facilityId) {
                        return;
                    }

                    const payload = {
                        id: Number(facilityId),
                        name: link.dataset.facilityName || '',
                        location: link.dataset.facilityLocation || '',
                        timezone: link.dataset.facilityTimezone || '',
                        displayHealth: link.dataset.facilityDisplayHealth || 'scope',
                        workgroupsCount: Number(link.dataset.facilityWorkgroupsCount || 0),
                        displaysCount: Number(link.dataset.facilityDisplaysCount || 0),
                        usersCount: Number(link.dataset.facilityUsersCount || 0),
                        failedDisplaysCount: Number(link.dataset.facilityFailedDisplaysCount || 0),
                    };

                    sessionStorage.setItem(`perfectlum-mobile-facility-context:${facilityId}`, JSON.stringify(payload));
                }

                async function prefetchWorkgroups(link) {
                    const href = link?.getAttribute('href');
                    if (!href) {
                        return;
                    }

                    const url = new URL(href, window.location.href);
                    const facilityId = url.searchParams.get('facility_id');
                    if (!facilityId) {
                        return;
                    }

                    storeFacilityContext(link);

                    const apiUrl = buildWorkgroupsApiUrl(facilityId);
                    const cacheKey = `${prefetchKeyPrefix}${apiUrl}`;

                    if (sessionStorage.getItem(cacheKey) || prefetchInflight.has(cacheKey)) {
                        return;
                    }

                    const request = window.Perfectlum.request(apiUrl)
                        .then((payload) => {
                            sessionStorage.setItem(cacheKey, JSON.stringify({
                                payload,
                                timestamp: Date.now(),
                            }));
                        })
                        .catch(() => null)
                        .finally(() => {
                            prefetchInflight.delete(cacheKey);
                        });

                    prefetchInflight.set(cacheKey, request);
                }

                function readPrefetchedWorkgroups(facilityId) {
                    const cacheKey = `${prefetchKeyPrefix}${buildWorkgroupsApiUrl(facilityId)}`;

                    try {
                        const raw = sessionStorage.getItem(cacheKey);
                        if (!raw) {
                            return null;
                        }

                        sessionStorage.removeItem(cacheKey);
                        const parsed = JSON.parse(raw);
                        return parsed && typeof parsed === 'object' ? (parsed.payload || null) : null;
                    } catch (error) {
                        sessionStorage.removeItem(cacheKey);
                        return null;
                    }
                }

                function facilityContextFromLink(link) {
                    return {
                        id: Number(link.dataset.facilityId || 0),
                        name: link.dataset.facilityName || '',
                        location: link.dataset.facilityLocation || '',
                        timezone: link.dataset.facilityTimezone || '',
                        displayHealth: link.dataset.facilityDisplayHealth || 'scope',
                        workgroupsCount: Number(link.dataset.facilityWorkgroupsCount || 0),
                        displaysCount: Number(link.dataset.facilityDisplaysCount || 0),
                        usersCount: Number(link.dataset.facilityUsersCount || 0),
                        failedDisplaysCount: Number(link.dataset.facilityFailedDisplaysCount || 0),
                    };
                }

                function workgroupContextFromLink(link, facilityContext) {
                    return {
                        facilityId: Number(facilityContext.id || 0),
                        facilityName: facilityContext.name || '',
                        facilityLocation: facilityContext.location || '',
                        facilityTimezone: facilityContext.timezone || '',
                        id: Number(link.dataset.workgroupId || 0),
                        name: link.dataset.workgroupName || '',
                        displayHealth: link.dataset.workgroupDisplayHealth || 'scope',
                        workstationsCount: Number(link.dataset.workgroupWorkstationsCount || 0),
                        displaysCount: Number(link.dataset.workgroupDisplaysCount || 0),
                        address: link.dataset.workgroupAddress || '',
                        phone: link.dataset.workgroupPhone || '',
                    };
                }

                function workstationContextFromLink(link, workgroupContext) {
                    return {
                        facilityId: Number(workgroupContext.facilityId || 0),
                        facilityName: workgroupContext.facilityName || '',
                        workgroupId: Number(workgroupContext.id || 0),
                        workgroupName: workgroupContext.name || '',
                        id: Number(link.dataset.workstationId || 0),
                        name: link.dataset.workstationName || '',
                        displayHealth: link.dataset.workstationDisplayHealth || 'sync',
                        displaysCount: Number(link.dataset.workstationDisplaysCount || 0),
                        okDisplaysCount: Number(link.dataset.workstationOkDisplaysCount || 0),
                        failedDisplaysCount: Number(link.dataset.workstationFailedDisplaysCount || 0),
                        lastSeenRelative: link.dataset.workstationLastSeenRelative || '',
                        sleepTime: link.dataset.workstationSleepTime || '-',
                    };
                }

                function renderWorkgroupRows(rows, context) {
                    const escapeHtml = window.Perfectlum.escapeHtml;

                    return rows.map((item) => `
                        <a href="${workstationsRoute}?facility_id=${context.id}&workgroup_id=${item.id}&facility_name=${encodeURIComponent(context.name)}&workgroup_name=${encodeURIComponent(item.name)}"
                           data-mobile-nav="forward"
                           data-workgroup-id="${item.id}"
                           data-workgroup-name="${escapeHtml(item.name).replace(/"/g, '&quot;')}"
                           data-workgroup-display-health="${item.displayHealth || 'scope'}"
                           data-workgroup-workstations-count="${Number(item.workstationsCount || 0)}"
                           data-workgroup-displays-count="${Number(item.displaysCount || 0)}"
                           data-workgroup-address="${escapeHtml(item.address || '').replace(/"/g, '&quot;')}"
                           data-workgroup-phone="${escapeHtml(item.phone || '').replace(/"/g, '&quot;')}"
                           class="mobile-workgroup-card ${item.displayHealth === 'failed' ? 'attention' : ''}">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="mobile-workgroup-meta">
                                                <span class="mobile-type-pill ${item.displayHealth === 'ok' ? 'run' : (item.displayHealth === 'failed' ? 'alert' : 'sync')}">${item.displayHealth === 'ok' ? 'Healthy' : (item.displayHealth === 'failed' ? 'Alert' : 'Group')}</span>
                                                <p class="mobile-meta mobile-clamp-1">${escapeHtml(item.facName)}</p>
                                            </div>
                                            <p class="mobile-workgroup-title">${escapeHtml(item.name)}</p>
                                            <p class="mobile-workgroup-detail">${escapeHtml([item.address, item.phone].filter((value) => value && value !== '-').join(' • ') || 'No address or phone saved')}</p>
                                        </div>
                                        <span class="mobile-workgroup-arrow">›</span>
                                    </div>
                                    <div class="mobile-workgroup-stat-row">
                                        <span class="mobile-workgroup-stat">${Number(item.workstationsCount || 0)} workstations</span>
                                        <span class="mobile-workgroup-stat">${Number(item.displaysCount || 0)} displays</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    `).join('');
                }

                function renderWorkgroupsPager(total, page, limit) {
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
                }

                function renderFacilitiesSkeleton() {
                    return `
                        <div class="mobile-facility-card mobile-facility-skeleton">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="mobile-facility-meta">
                                                <span class="mobile-skeleton-chip"></span>
                                                <span class="mobile-skeleton-line h-[0.72rem] w-24"></span>
                                            </div>
                                            <div class="mobile-skeleton-line mt-2 h-[1rem] w-44"></div>
                                            <div class="mobile-skeleton-line mt-2 h-[0.78rem] w-36"></div>
                                        </div>
                                        <span class="mobile-skeleton-circle"></span>
                                    </div>
                                    <div class="mobile-facility-stat-row">
                                        <span class="mobile-skeleton-chip" style="width: 6rem"></span>
                                        <span class="mobile-skeleton-chip" style="width: 5rem"></span>
                                        <span class="mobile-skeleton-chip" style="width: 4.6rem"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mobile-facility-card mobile-facility-skeleton">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="mobile-facility-meta">
                                                <span class="mobile-skeleton-chip"></span>
                                                <span class="mobile-skeleton-line h-[0.72rem] w-20"></span>
                                            </div>
                                            <div class="mobile-skeleton-line mt-2 h-[1rem] w-40"></div>
                                            <div class="mobile-skeleton-line mt-2 h-[0.78rem] w-28"></div>
                                        </div>
                                        <span class="mobile-skeleton-circle"></span>
                                    </div>
                                    <div class="mobile-facility-stat-row">
                                        <span class="mobile-skeleton-chip" style="width: 5.5rem"></span>
                                        <span class="mobile-skeleton-chip" style="width: 4.8rem"></span>
                                        <span class="mobile-skeleton-chip" style="width: 4.3rem"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                function renderWorkgroupsSkeleton() {
                    return `
                        <div class="mobile-workgroup-card mobile-workgroup-skeleton">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="mobile-workgroup-meta">
                                                <span class="mobile-skeleton-chip"></span>
                                                <span class="mobile-skeleton-line h-[0.72rem] w-24"></span>
                                            </div>
                                            <div class="mobile-skeleton-line mt-2 h-[1rem] w-40"></div>
                                            <div class="mobile-skeleton-line mt-2 h-[0.78rem] w-32"></div>
                                        </div>
                                        <span class="mobile-skeleton-circle"></span>
                                    </div>
                                    <div class="mobile-workgroup-stat-row">
                                        <span class="mobile-skeleton-chip" style="width: 5.9rem"></span>
                                        <span class="mobile-skeleton-chip" style="width: 4.9rem"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mobile-workgroup-card mobile-workgroup-skeleton">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="mobile-workgroup-meta">
                                                <span class="mobile-skeleton-chip"></span>
                                                <span class="mobile-skeleton-line h-[0.72rem] w-20"></span>
                                            </div>
                                            <div class="mobile-skeleton-line mt-2 h-[1rem] w-36"></div>
                                            <div class="mobile-skeleton-line mt-2 h-[0.78rem] w-28"></div>
                                        </div>
                                        <span class="mobile-skeleton-circle"></span>
                                    </div>
                                    <div class="mobile-workgroup-stat-row">
                                        <span class="mobile-skeleton-chip" style="width: 5.4rem"></span>
                                        <span class="mobile-skeleton-chip" style="width: 4.6rem"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                function renderWorkstationRows(rows, context) {
                    const escapeHtml = window.Perfectlum.escapeHtml;

                    return rows.map((item) => `
                        <a href="${displaysRoute}?facility_id=${context.facilityId}&workgroup_id=${context.workgroupId}&workstation_id=${item.id}&facility_name=${encodeURIComponent(context.facilityName)}&workgroup_name=${encodeURIComponent(context.workgroupName)}&workstation_name=${encodeURIComponent(item.name)}"
                           data-mobile-nav="forward"
                           data-workstation-id="${item.id}"
                           data-workstation-name="${escapeHtml(item.name).replace(/"/g, '&quot;')}"
                           data-workstation-display-health="${item.displayHealth || 'sync'}"
                           data-workstation-displays-count="${Number(item.displaysCount || 0)}"
                           data-workstation-ok-displays-count="${Number(item.okDisplaysCount || 0)}"
                           data-workstation-failed-displays-count="${Number(item.failedDisplaysCount || 0)}"
                           data-workstation-last-seen-relative="${escapeHtml(item.lastSeenRelative || '').replace(/"/g, '&quot;')}"
                           data-workstation-sleep-time="${escapeHtml(item.sleepTime || '-').replace(/"/g, '&quot;')}"
                           class="mobile-workgroup-card ${item.displayHealth === 'failed' ? 'attention' : ''}">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="mobile-workgroup-meta">
                                                <span class="mobile-type-pill ${item.displayHealth === 'ok' ? 'run' : (item.displayHealth === 'failed' ? 'alert' : 'sync')}">${item.displayHealth === 'ok' ? 'Healthy' : (item.displayHealth === 'failed' ? 'Alert' : 'Sync')}</span>
                                                <p class="mobile-meta mobile-clamp-1">${escapeHtml(context.workgroupName)} • ${escapeHtml(context.facilityName)}</p>
                                            </div>
                                            <p class="mobile-workgroup-title">${escapeHtml(item.name)}</p>
                                            <p class="mobile-workgroup-detail">${item.lastConnected !== '-' ? `Last sync ${escapeHtml(item.lastSeenRelative || '-')}` : 'No sync data received'}</p>
                                        </div>
                                        <span class="mobile-workgroup-arrow">›</span>
                                    </div>
                                    <div class="mobile-workgroup-stat-row">
                                        <span class="mobile-workgroup-stat">${Number(item.displaysCount || 0)} displays</span>
                                        <span class="mobile-workgroup-stat">${Number(item.okDisplaysCount || 0)} ok</span>
                                        <span class="mobile-workgroup-stat">${Number(item.failedDisplaysCount || 0)} alert</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    `).join('');
                }

                function renderDisplayRows(rows, context) {
                    const escapeHtml = window.Perfectlum.escapeHtml;
                    const buildDetailHref = (displayId) => {
                        const params = new URLSearchParams({
                            facility_id: String(context.facilityId || ''),
                            workgroup_id: String(context.workgroupId || ''),
                            workstation_id: String(context.id || ''),
                            facility_name: context.facilityName || '',
                            workgroup_name: context.workgroupName || '',
                            workstation_name: context.name || '',
                        });
                        return `${@json(url('/m/displays'))}/${displayId}?${params.toString()}`;
                    };
                    const latestError = (errors) => {
                        if (!Array.isArray(errors) || !errors.length) {
                            return 'No current device errors';
                        }

                        const lastItem = errors[errors.length - 1];
                        if (typeof lastItem === 'string') {
                            return lastItem;
                        }

                        return lastItem?.message || lastItem?.error || 'Device alert detected';
                    };

                    return rows.map((item) => `
                        <a href="${buildDetailHref(item.id)}" class="mobile-workgroup-card ${item.status === 2 ? 'attention' : ''}">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="mobile-workgroup-meta">
                                                <span class="mobile-type-pill ${item.status === 1 ? 'run' : 'alert'}">${item.status === 1 ? 'Healthy' : 'Alert'}</span>
                                                <p class="mobile-meta mobile-clamp-1">${escapeHtml(context.workstationName)} • ${escapeHtml(context.workgroupName)}</p>
                                            </div>
                                            <p class="mobile-workgroup-title">${escapeHtml(item.displayName || item.name || '-')}</p>
                                            <p class="mobile-workgroup-detail">${escapeHtml(item.status === 2 ? latestError(item.errors) : 'Open display detail')}</p>
                                        </div>
                                        <span class="mobile-workgroup-arrow">›</span>
                                    </div>
                                    <div class="mobile-workgroup-stat-row">
                                        <span class="mobile-workgroup-stat">${escapeHtml(item.updatedAt || '-')}</span>
                                        <span class="mobile-workgroup-stat">${escapeHtml(item.manufacturer || '-')}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    `).join('');
                }

                function renderWorkgroupsView(context, payload = null, keyword = '') {
                    const escapeHtml = window.Perfectlum.escapeHtml;
                    const displayHealth = context.displayHealth || 'scope';
                    const tone = displayHealth === 'ok' ? 'run' : (displayHealth === 'failed' ? 'alert' : 'sync');
                    const label = displayHealth === 'ok' ? 'Healthy' : (displayHealth === 'failed' ? 'Alert' : 'Facility');
                    const stats = [
                        `${Number(context.workgroupsCount || 0)} workgroups`,
                        Number(context.displaysCount || 0) > 0 ? `${Number(context.displaysCount || 0)} displays` : '',
                        Number(context.usersCount || 0) > 0 ? `${Number(context.usersCount || 0)} users` : '',
                    ].filter(Boolean);

                    const rows = payload?.data || [];
                    const total = Number(payload?.total || 0);
                    const page = Number(payload?.page || 1);
                    const listHtml = rows.length ? renderWorkgroupRows(rows, context) : renderWorkgroupsSkeleton();
                    const pagerHtml = rows.length ? renderWorkgroupsPager(total, page, perPage) : '';

                    return `
                        <section class="mobile-facility-context ${displayHealth === 'failed' ? 'attention' : ''}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="mobile-workgroup-meta">
                                        <span class="mobile-type-pill ${tone}">${label}</span>
                                        <p class="mobile-meta mobile-clamp-1">${escapeHtml(context.timezone || 'Timezone not set')}</p>
                                    </div>
                                    <p class="mobile-facility-context-title">${escapeHtml(context.name || 'Selected facility')}</p>
                                    <p class="mobile-section-copy">${escapeHtml(context.location || 'No location details available')}</p>
                                </div>
                                ${displayHealth === 'failed' && Number(context.failedDisplaysCount || 0) > 0 ? `<span class="mobile-workgroup-stat">${Number(context.failedDisplaysCount || 0)} alerts</span>` : ''}
                            </div>
                            <div class="mobile-workgroup-stat-row">
                                ${stats.map((stat) => `<span class="mobile-workgroup-stat">${escapeHtml(stat)}</span>`).join('')}
                            </div>
                        </section>

                        <div class="mobile-section-gap mobile-search-shell">
                            <div class="mobile-searchbar">
                                <i data-lucide="search" class="mobile-searchbar-icon"></i>
                                <input id="mobile-workgroup-search" type="search" class="mobile-input mobile-search-input" placeholder="Search workgroups" value="${escapeHtml(keyword)}">
                            </div>
                            <div class="mt-3 flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                                <span class="mobile-filter-chip active">Facility: ${escapeHtml(context.name || 'Selected facility')}</span>
                                <button type="button" class="mobile-filter-chip" data-client-clear-level="workgroups">Clear</button>
                            </div>
                        </div>

                        <section class="mobile-section-gap">
                            <div id="mobile-workgroup-list" class="mobile-workgroup-list">${listHtml}</div>
                            <div id="mobile-workgroup-pagination" class="mt-3">${pagerHtml}</div>
                        </section>
                    `;
                }

                function renderWorkstationsView(context, payload = null, keyword = '') {
                    const escapeHtml = window.Perfectlum.escapeHtml;
                    const tone = context.displayHealth === 'ok' ? 'run' : (context.displayHealth === 'failed' ? 'alert' : 'sync');
                    const label = context.displayHealth === 'ok' ? 'Healthy' : (context.displayHealth === 'failed' ? 'Alert' : 'Group');
                    const rows = payload?.data || [];
                    const total = Number(payload?.total || 0);
                    const page = Number(payload?.page || 1);
                    const listHtml = rows.length ? renderWorkstationRows(rows, context) : renderWorkgroupsSkeleton();
                    const pagerHtml = rows.length ? renderWorkgroupsPager(total, page, perPage) : '';

                    return `
                        <section class="mobile-facility-context ${context.displayHealth === 'failed' ? 'attention' : ''}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="mobile-workgroup-meta">
                                        <span class="mobile-type-pill ${tone}">${label}</span>
                                        <p class="mobile-meta mobile-clamp-1">${escapeHtml(context.facilityName || 'Selected facility')}</p>
                                    </div>
                                    <p class="mobile-facility-context-title">${escapeHtml(context.name || 'Selected workgroup')}</p>
                                    <p class="mobile-section-copy">${escapeHtml([context.address, context.phone].filter(Boolean).join(' • ') || 'Workgroup scope and connected endpoints')}</p>
                                </div>
                                <span class="mobile-workgroup-stat">${Number(context.displaysCount || 0)} displays</span>
                            </div>
                            <div class="mobile-workgroup-stat-row">
                                <span class="mobile-workgroup-stat">${Number(context.workstationsCount || 0)} workstations</span>
                                <span class="mobile-workgroup-stat">${Number(context.displaysCount || 0)} displays</span>
                            </div>
                        </section>

                        <div class="mobile-section-gap mobile-search-shell">
                            <div class="mobile-searchbar">
                                <i data-lucide="search" class="mobile-searchbar-icon"></i>
                                <input id="mobile-client-workstation-search" type="search" class="mobile-input mobile-search-input" placeholder="Search workstations" value="${escapeHtml(keyword)}">
                            </div>
                            <div class="mt-3 flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                                <span class="mobile-filter-chip active">Workgroup: ${escapeHtml(context.name || 'Selected workgroup')}</span>
                                <button type="button" class="mobile-filter-chip" data-client-clear-level="workstations">Clear</button>
                            </div>
                        </div>

                        <section class="mobile-section-gap">
                            <div id="mobile-client-workstation-list" class="mobile-workgroup-list">${listHtml}</div>
                            <div id="mobile-client-workstation-pagination" class="mt-3">${pagerHtml}</div>
                        </section>
                    `;
                }

                function renderDisplaysView(context, payload = null, keyword = '') {
                    const escapeHtml = window.Perfectlum.escapeHtml;
                    const tone = context.displayHealth === 'ok' ? 'run' : (context.displayHealth === 'failed' ? 'alert' : 'sync');
                    const label = context.displayHealth === 'ok' ? 'Healthy' : (context.displayHealth === 'failed' ? 'Alert' : 'Sync');
                    const rows = payload?.data || [];
                    const total = Number(payload?.total || 0);
                    const page = Number(payload?.page || 1);
                    const listHtml = rows.length ? renderDisplayRows(rows, context) : renderWorkgroupsSkeleton();
                    const pagerHtml = rows.length ? renderWorkgroupsPager(total, page, perPage) : '';

                    return `
                        <section class="mobile-facility-context ${context.displayHealth === 'failed' ? 'attention' : ''}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="mobile-workgroup-meta">
                                        <span class="mobile-type-pill ${tone}">${label}</span>
                                        <p class="mobile-meta mobile-clamp-1">${escapeHtml(context.workgroupName || 'Selected workgroup')}</p>
                                    </div>
                                    <p class="mobile-facility-context-title">${escapeHtml(context.name || 'Selected workstation')}</p>
                                    <p class="mobile-section-copy">${escapeHtml(context.lastSeenRelative ? `Last sync ${context.lastSeenRelative}` : 'Display fleet on this workstation')}</p>
                                </div>
                                <span class="mobile-workgroup-stat">${Number(context.displaysCount || 0)} displays</span>
                            </div>
                            <div class="mobile-workgroup-stat-row">
                                <span class="mobile-workgroup-stat">${Number(context.okDisplaysCount || 0)} ok</span>
                                <span class="mobile-workgroup-stat">${Number(context.failedDisplaysCount || 0)} alert</span>
                                <span class="mobile-workgroup-stat">${escapeHtml(context.sleepTime || '-')}</span>
                            </div>
                        </section>

                        <div class="mobile-section-gap mobile-search-shell">
                            <div class="mobile-searchbar">
                                <i data-lucide="search" class="mobile-searchbar-icon"></i>
                                <input id="mobile-client-display-search" type="search" class="mobile-input mobile-search-input" placeholder="Search displays" value="${escapeHtml(keyword)}">
                            </div>
                            <div class="mt-3 flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                                <span class="mobile-filter-chip active">Workstation: ${escapeHtml(context.name || 'Selected workstation')}</span>
                                <button type="button" class="mobile-filter-chip" data-client-clear-level="displays">Clear</button>
                            </div>
                        </div>

                        <section class="mobile-section-gap">
                            <div id="mobile-client-display-list" class="mobile-workgroup-list">${listHtml}</div>
                            <div id="mobile-client-display-pagination" class="mt-3">${pagerHtml}</div>
                        </section>
                    `;
                }

                function renderWorkgroupsPayload(payload, context) {
                    const rows = payload?.data || [];
                    const total = Number(payload?.total || 0);
                    const page = Number(payload?.page || 1);
                    const workgroupList = document.getElementById('mobile-workgroup-list');
                    const workgroupPagination = document.getElementById('mobile-workgroup-pagination');

                    if (!workgroupList || !workgroupPagination) {
                        return;
                    }

                    if (!rows.length) {
                        workgroupList.innerHTML = '<div class="mobile-empty">No workgroups matched this filter.</div>';
                        workgroupPagination.innerHTML = '';
                        return;
                    }

                    workgroupList.innerHTML = renderWorkgroupRows(rows, context);
                    workgroupPagination.innerHTML = renderWorkgroupsPager(total, page, perPage);
                }

                function renderWorkstationsPayload(payload, context) {
                    const rows = payload?.data || [];
                    const total = Number(payload?.total || 0);
                    const page = Number(payload?.page || 1);
                    const workstationList = document.getElementById('mobile-client-workstation-list');
                    const workstationPagination = document.getElementById('mobile-client-workstation-pagination');

                    if (!workstationList || !workstationPagination) {
                        return;
                    }

                    if (!rows.length) {
                        workstationList.innerHTML = '<div class="mobile-empty">No workstations matched this filter.</div>';
                        workstationPagination.innerHTML = '';
                        return;
                    }

                    workstationList.innerHTML = renderWorkstationRows(rows, context);
                    workstationPagination.innerHTML = renderWorkgroupsPager(total, page, perPage);
                }

                function renderDisplaysPayload(payload, context) {
                    const rows = payload?.data || [];
                    const total = Number(payload?.total || 0);
                    const page = Number(payload?.page || 1);
                    const displayList = document.getElementById('mobile-client-display-list');
                    const displayPagination = document.getElementById('mobile-client-display-pagination');

                    if (!displayList || !displayPagination) {
                        return;
                    }

                    if (!rows.length) {
                        displayList.innerHTML = '<div class="mobile-empty">No displays matched this filter.</div>';
                        displayPagination.innerHTML = '';
                        return;
                    }

                    displayList.innerHTML = renderDisplayRows(rows, context);
                    displayPagination.innerHTML = renderWorkgroupsPager(total, page, perPage);
                }

                function bindClientWorkgroupsView(context, initialPayload = null, options = {}) {
                    const workgroupList = document.getElementById('mobile-workgroup-list');
                    const workgroupPagination = document.getElementById('mobile-workgroup-pagination');
                    const workgroupSearch = document.getElementById('mobile-workgroup-search');
                    const clearButton = document.querySelector('[data-client-clear-level="workgroups"]');
                    let workgroupTimer = null;
                    let keyword = '';
                    let initialConsumed = false;

                    async function loadClientWorkgroups(nextKeyword = '', page = 1) {
                        keyword = nextKeyword;
                        const cacheKey = `${context.id}::${nextKeyword}::${page}`;
                        const cached = hierarchyCache.workgroups.get(cacheKey);
                        const currentRequest = ++hierarchyRequestToken.workgroups;

                        if (!initialConsumed && initialPayload && page === 1 && !nextKeyword) {
                            initialConsumed = true;
                            hierarchyCache.workgroups.set(cacheKey, {
                                ...initialPayload,
                                page: 1,
                            });
                            renderWorkgroupsPayload({
                                ...initialPayload,
                                page: 1,
                            }, context);
                            return;
                        }

                        if (cached) {
                            renderWorkgroupsPayload(cached, context);
                            return;
                        }

                        if (workgroupList) {
                            workgroupList.innerHTML = renderWorkgroupsSkeleton();
                        }
                        if (workgroupPagination) {
                            workgroupPagination.innerHTML = '';
                        }

                        const params = new URLSearchParams({
                            facility_id: String(context.id),
                            limit: String(perPage),
                            page: String(page),
                        });
                        if (nextKeyword) {
                            params.set('search', nextKeyword);
                        }

                        try {
                            const response = await window.Perfectlum.request(`/api/workgroups?${params.toString()}`);
                            if (currentRequest !== hierarchyRequestToken.workgroups) {
                                return;
                            }
                            const payload = {
                                ...response,
                                page,
                            };
                            hierarchyCache.workgroups.set(cacheKey, payload);
                            renderWorkgroupsPayload(payload, context);
                        } catch (error) {
                            if (currentRequest !== hierarchyRequestToken.workgroups) {
                                return;
                            }
                            if (workgroupList) {
                                workgroupList.innerHTML = '<div class="mobile-empty">Unable to load workgroups right now.</div>';
                            }
                        }
                    }

                    workgroupPagination?.addEventListener('click', (event) => {
                        const button = event.target.closest('[data-page]');
                        if (!button || button.hasAttribute('disabled')) {
                            return;
                        }

                        loadClientWorkgroups(keyword, Number(button.dataset.page));
                    });

                    workgroupSearch?.addEventListener('input', () => {
                        window.clearTimeout(workgroupTimer);
                        workgroupTimer = window.setTimeout(() => {
                            loadClientWorkgroups(workgroupSearch.value.trim(), 1);
                        }, 220);
                    });

                    clearButton?.addEventListener('click', () => {
                        workgroupSearch.value = '';
                        loadClientWorkgroups('', 1);
                    });

                    workgroupList?.addEventListener('click', (event) => {
                        const link = event.target.closest('.mobile-workgroup-card');
                        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                            return;
                        }

                        event.preventDefault();
                        event.stopPropagation();
                        openClientWorkstations(link, context);
                    }, true);

                    if (!initialPayload && !options.skipLoad) {
                        loadClientWorkgroups('', 1);
                    }
                }

                function bindClientWorkstationsView(context, initialPayload = null, options = {}) {
                    const workstationList = document.getElementById('mobile-client-workstation-list');
                    const workstationPagination = document.getElementById('mobile-client-workstation-pagination');
                    const workstationSearch = document.getElementById('mobile-client-workstation-search');
                    const clearButton = document.querySelector('[data-client-clear-level="workstations"]');
                    let workstationTimer = null;
                    let keyword = '';
                    let initialConsumed = false;

                    async function loadClientWorkstations(nextKeyword = '', page = 1) {
                        keyword = nextKeyword;
                        const cacheKey = `${context.facilityId}::${context.id}::${nextKeyword}::${page}`;
                        const cached = hierarchyCache.workstations.get(cacheKey);
                        const currentRequest = ++hierarchyRequestToken.workstations;

                        if (!initialConsumed && initialPayload && page === 1 && !nextKeyword) {
                            initialConsumed = true;
                            hierarchyCache.workstations.set(cacheKey, {
                                ...initialPayload,
                                page: 1,
                            });
                            renderWorkstationsPayload({
                                ...initialPayload,
                                page: 1,
                            }, context);
                            return;
                        }

                        if (cached) {
                            renderWorkstationsPayload(cached, context);
                            return;
                        }

                        if (workstationList) {
                            workstationList.innerHTML = renderWorkgroupsSkeleton();
                        }
                        if (workstationPagination) {
                            workstationPagination.innerHTML = '';
                        }

                        const params = new URLSearchParams({
                            facility_id: String(context.facilityId),
                            workgroup_id: String(context.id),
                            limit: String(perPage),
                            page: String(page),
                        });
                        if (nextKeyword) {
                            params.set('search', nextKeyword);
                        }

                        try {
                            const response = await window.Perfectlum.request(`/api/workstations?${params.toString()}`);
                            if (currentRequest !== hierarchyRequestToken.workstations) {
                                return;
                            }
                            const payload = {
                                ...response,
                                page,
                            };
                            hierarchyCache.workstations.set(cacheKey, payload);
                            renderWorkstationsPayload(payload, context);
                        } catch (error) {
                            if (currentRequest !== hierarchyRequestToken.workstations) {
                                return;
                            }
                            if (workstationList) {
                                workstationList.innerHTML = '<div class="mobile-empty">Unable to load workstations right now.</div>';
                            }
                        }
                    }

                    workstationPagination?.addEventListener('click', (event) => {
                        const button = event.target.closest('[data-page]');
                        if (!button || button.hasAttribute('disabled')) {
                            return;
                        }

                        loadClientWorkstations(keyword, Number(button.dataset.page));
                    });

                    workstationSearch?.addEventListener('input', () => {
                        window.clearTimeout(workstationTimer);
                        workstationTimer = window.setTimeout(() => {
                            loadClientWorkstations(workstationSearch.value.trim(), 1);
                        }, 220);
                    });

                    clearButton?.addEventListener('click', () => {
                        workstationSearch.value = '';
                        loadClientWorkstations('', 1);
                    });

                    workstationList?.addEventListener('click', (event) => {
                        const link = event.target.closest('.mobile-workgroup-card');
                        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                            return;
                        }

                        event.preventDefault();
                        event.stopPropagation();
                        openClientDisplays(link, context);
                    }, true);

                    if (!initialPayload && !options.skipLoad) {
                        loadClientWorkstations('', 1);
                    }
                }

                function bindClientDisplaysView(context, initialPayload = null, options = {}) {
                    const displayList = document.getElementById('mobile-client-display-list');
                    const displayPagination = document.getElementById('mobile-client-display-pagination');
                    const displaySearch = document.getElementById('mobile-client-display-search');
                    const clearButton = document.querySelector('[data-client-clear-level="displays"]');
                    let displayTimer = null;
                    let keyword = '';
                    let initialConsumed = false;

                    async function loadClientDisplays(nextKeyword = '', page = 1) {
                        keyword = nextKeyword;
                        const cacheKey = `${context.facilityId}::${context.workgroupId}::${context.id}::${nextKeyword}::${page}`;
                        const cached = hierarchyCache.displays.get(cacheKey);
                        const currentRequest = ++hierarchyRequestToken.displays;

                        if (!initialConsumed && initialPayload && page === 1 && !nextKeyword) {
                            initialConsumed = true;
                            hierarchyCache.displays.set(cacheKey, {
                                ...initialPayload,
                                page: 1,
                            });
                            renderDisplaysPayload({
                                ...initialPayload,
                                page: 1,
                            }, context);
                            return;
                        }

                        if (cached) {
                            renderDisplaysPayload(cached, context);
                            return;
                        }

                        if (displayList) {
                            displayList.innerHTML = renderWorkgroupsSkeleton();
                        }
                        if (displayPagination) {
                            displayPagination.innerHTML = '';
                        }

                        const params = new URLSearchParams({
                            facility_id: String(context.facilityId),
                            workgroup_id: String(context.workgroupId),
                            workstation_id: String(context.id),
                            limit: String(perPage),
                            page: String(page),
                        });
                        if (nextKeyword) {
                            params.set('search', nextKeyword);
                        }

                        try {
                            const response = await window.Perfectlum.request(`/api/displays?${params.toString()}`);
                            if (currentRequest !== hierarchyRequestToken.displays) {
                                return;
                            }
                            const payload = {
                                ...response,
                                page,
                            };
                            hierarchyCache.displays.set(cacheKey, payload);
                            renderDisplaysPayload(payload, context);
                        } catch (error) {
                            if (currentRequest !== hierarchyRequestToken.displays) {
                                return;
                            }
                            if (displayList) {
                                displayList.innerHTML = '<div class="mobile-empty">Unable to load displays right now.</div>';
                            }
                        }
                    }

                    displayPagination?.addEventListener('click', (event) => {
                        const button = event.target.closest('[data-page]');
                        if (!button || button.hasAttribute('disabled')) {
                            return;
                        }

                        loadClientDisplays(keyword, Number(button.dataset.page));
                    });

                    displaySearch?.addEventListener('input', () => {
                        window.clearTimeout(displayTimer);
                        displayTimer = window.setTimeout(() => {
                            loadClientDisplays(displaySearch.value.trim(), 1);
                        }, 220);
                    });

                    clearButton?.addEventListener('click', () => {
                        displaySearch.value = '';
                        loadClientDisplays('', 1);
                    });

                    if (!initialPayload && !options.skipLoad) {
                        loadClientDisplays('', 1);
                    }
                }

                function openClientWorkgroups(link) {
                    if (!mainContent) {
                        window.location.href = link.href;
                        return;
                    }

                    const context = facilityContextFromLink(link);
                    const payload = readPrefetchedWorkgroups(context.id);
                    hierarchyStack.push(captureHierarchySnapshot(() => bindFacilitiesView({ skipLoad: true })));
                    clientHierarchyLevel = 'workgroups';

                    animateMainTransition('forward', () => {
                        history.pushState({ perfectlumClientHierarchy: true, level: 'workgroups' }, '', link.href);
                        document.title = `Workgroups | ${siteName}`;
                        setHierarchyHeader({
                            title: 'Workgroups',
                            backUrl: facilitiesRoute,
                            backVisible: true,
                            hierarchyBack: true,
                        });
                        mainContent.innerHTML = renderWorkgroupsView(context, payload, '');
                        ensureBottomNavVisible('workspace');
                        lucide.createIcons();
                        bindClientWorkgroupsView(context, payload);
                        window.requestAnimationFrame(() => {
                            window.scrollTo(0, 0);
                        });
                    });
                }

                function openClientWorkstations(link, facilityContext) {
                    if (!mainContent) {
                        window.location.href = link.href;
                        return;
                    }

                    const context = workgroupContextFromLink(link, facilityContext);
                    hierarchyStack.push(captureHierarchySnapshot(() => bindClientWorkgroupsView(facilityContext, null, { skipLoad: true }), window.location.href));
                    clientHierarchyLevel = 'workstations';

                    animateMainTransition('forward', () => {
                        history.pushState({ perfectlumClientHierarchy: true, level: 'workstations' }, '', link.href);
                        document.title = `Workstations | ${siteName}`;
                        setHierarchyHeader({
                            title: 'Workstations',
                            backUrl: `${workgroupsRoute}?facility_id=${facilityContext.id}&facility_name=${encodeURIComponent(facilityContext.name)}`,
                            backVisible: true,
                            hierarchyBack: true,
                        });
                        mainContent.innerHTML = renderWorkstationsView(context, null, '');
                        ensureBottomNavVisible('workspace');
                        lucide.createIcons();
                        bindClientWorkstationsView(context);
                        window.requestAnimationFrame(() => {
                            window.scrollTo(0, 0);
                        });
                    });
                }

                function openClientDisplays(link, workgroupContext) {
                    if (!mainContent) {
                        window.location.href = link.href;
                        return;
                    }

                    const context = workstationContextFromLink(link, workgroupContext);
                    hierarchyStack.push(captureHierarchySnapshot(() => bindClientWorkstationsView(workgroupContext, null, { skipLoad: true }), window.location.href));
                    clientHierarchyLevel = 'displays';

                    animateMainTransition('forward', () => {
                        history.pushState({ perfectlumClientHierarchy: true, level: 'displays' }, '', link.href);
                        document.title = `Displays | ${siteName}`;
                        setHierarchyHeader({
                            title: 'Displays',
                            backUrl: `${workstationsRoute}?facility_id=${workgroupContext.facilityId}&workgroup_id=${workgroupContext.id}&facility_name=${encodeURIComponent(workgroupContext.facilityName)}&workgroup_name=${encodeURIComponent(workgroupContext.name)}`,
                            backVisible: true,
                            hierarchyBack: true,
                        });
                        mainContent.innerHTML = renderDisplaysView(context, null, '');
                        ensureBottomNavVisible('workspace');
                        lucide.createIcons();
                        bindClientDisplaysView(context);
                        window.requestAnimationFrame(() => {
                            window.scrollTo(0, 0);
                        });
                    });
                }

                async function loadFacilities(keyword = '', page = 1) {
                    currentKeyword = keyword;
                    currentPage = page;
                    const cacheKey = `${type}::${keyword}::${page}`;
                    const cached = facilitiesCache.get(cacheKey);
                    const currentRequest = ++facilitiesRequestToken;
                    syncRouteState(keyword, page);
                    if (!list || !pagination) {
                        return;
                    }

                    if (cached) {
                        list.innerHTML = cached.html;
                        pagination.innerHTML = cached.pager;
                        return;
                    }

                    list.innerHTML = loadingState();
                    pagination.innerHTML = '';

                    const params = new URLSearchParams({ limit: String(perPage), page: String(page) });
                    if (keyword) params.set('search', keyword);
                    if (type) params.set('type', type);

                    try {
                        const response = await window.Perfectlum.request(`/api/facilities?${params.toString()}`);
                        if (currentRequest !== facilitiesRequestToken) {
                            return;
                        }
                        const rows = response.data || [];
                        const total = Number(response.total || 0);

                        if (!rows.length) {
                            list.innerHTML = emptyState('No facilities matched this filter.');
                            return;
                        }

                        const html = rows.map((item) => `
                            <a href="${@json(url('/m/facilities'))}/${item.id}?return_to=${encodeURIComponent(`${window.location.pathname}${window.location.search}`)}" data-mobile-nav="forward" data-facility-id="${item.id}" data-facility-name="${item.name.replace(/"/g, '&quot;')}" data-facility-location="${(item.location || '').replace(/"/g, '&quot;')}" data-facility-timezone="${(item.timezone || '').replace(/"/g, '&quot;')}" data-facility-display-health="${item.displayHealth || 'scope'}" data-facility-workgroups-count="${item.workgroupsCount || 0}" data-facility-displays-count="${item.displaysCount || 0}" data-facility-users-count="${item.usersCount || 0}" data-facility-failed-displays-count="${item.failedDisplaysCount || 0}" class="mobile-facility-card ${item.displayHealth === 'failed' ? 'attention' : ''}">
                                <div class="flex items-start gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="mobile-facility-meta">
                                                    <span class="mobile-type-pill ${item.displayHealth === 'ok' ? 'run' : (item.displayHealth === 'failed' ? 'alert' : 'sync')}">${item.displayHealth === 'ok' ? 'Healthy' : (item.displayHealth === 'failed' ? 'Alert' : 'Scope')}</span>
                                                    <p class="mobile-meta mobile-clamp-1">${item.timezone || 'Timezone not set'}</p>
                                                </div>
                                                <p class="mobile-facility-title">${item.name}</p>
                                                <p class="mobile-facility-detail">${item.location || 'No location details available'}</p>
                                            </div>
                                            <span class="mobile-facility-arrow">›</span>
                                        </div>
                                        <div class="mobile-facility-stat-row">
                                            <span class="mobile-facility-stat">${item.workgroupsCount} workgroups</span>
                                            <span class="mobile-facility-stat">${item.displaysCount} displays</span>
                                            <span class="mobile-facility-stat">${item.usersCount} users</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        `).join('');
                        const pager = renderPager(total, page, perPage);
                        facilitiesCache.set(cacheKey, { html, pager });
                        list.innerHTML = html;
                        pagination.innerHTML = pager;
                    } catch (error) {
                        if (currentRequest !== facilitiesRequestToken) {
                            return;
                        }
                        list.innerHTML = emptyState('Unable to load facilities right now.');
                    }
                }

                const prefetchFromEvent = (event) => {
                    const link = event.target.closest('.mobile-facility-card');
                    if (link?.href) {
                        window.Perfectlum.prefetchMobilePage?.(link.href);
                    }
                };

                function bindFacilitiesView(options = {}) {
                    ensureBottomNavVisible('workspace');
                    setHierarchyHeader({
                        title: 'Facilities',
                        backUrl: rootBackUrl || facilitiesRoute,
                        backVisible: Boolean(rootBackUrl),
                        hierarchyBack: false,
                    });
                    resolveFacilityElements();

                    if (!list || !pagination || !searchInput) {
                        return;
                    }

                    pagination.addEventListener('click', (event) => {
                        const button = event.target.closest('[data-page]');
                        if (!button || button.hasAttribute('disabled')) {
                            return;
                        }

                        loadFacilities(currentKeyword, Number(button.dataset.page));
                    });

                    filterButtons.forEach((button) => {
                        button.addEventListener('click', () => {
                            type = button.dataset.facilityFilter;
                            syncFilters();
                            loadFacilities(searchInput.value.trim(), 1);
                        });
                    });

                    searchInput.addEventListener('input', () => {
                        window.clearTimeout(timer);
                        timer = window.setTimeout(() => loadFacilities(searchInput.value.trim(), 1), 220);
                    });

                    list.addEventListener('pointerdown', prefetchFromEvent);
                    list.addEventListener('mouseover', prefetchFromEvent);
                    list.addEventListener('focusin', prefetchFromEvent);
                    list.addEventListener('touchstart', prefetchFromEvent, { passive: true });
                    list.addEventListener('click', (event) => {
                        const link = event.target.closest('.mobile-facility-card');
                        if (!link) {
                            return;
                        }

                        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                            return;
                        }

                        storeFacilityContext(link);
                    }, true);

                    searchInput.value = currentKeyword;
                    syncFilters();

                    if (!options.skipLoad) {
                        loadFacilities(currentKeyword, currentPage);
                    }
                }

                function navigateBackInHierarchy() {
                    if (!hierarchyStack.length || !mainContent) {
                        window.location.href = initialFacilitiesUrl;
                        return;
                    }

                    const snapshot = hierarchyStack.pop();
                    if (!snapshot) {
                        window.location.href = initialFacilitiesUrl;
                        return;
                    }

                    clientHierarchyLevel = hierarchyStack.length ? 'hierarchy' : 'facilities';
                    restoreHierarchySnapshot(snapshot);
                    history.replaceState({ mobileShell: true }, '', snapshot.url || facilitiesRoute);
                }

                const handleHierarchyBackClick = (event) => {
                    const backLink = event.target.closest('#mobile-appbar-back[data-hierarchy-nav="1"]');
                    if (!backLink || !hierarchyStack.length) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();
                    navigateBackInHierarchy();
                };

                const handleHierarchyPopstate = () => {
                    if (!hierarchyStack.length) {
                        return false;
                    }
                    const snapshot = hierarchyStack.pop();
                    clientHierarchyLevel = hierarchyStack.length ? 'hierarchy' : 'facilities';
                    restoreHierarchySnapshot(snapshot);
                    history.replaceState({ mobileShell: true }, '', snapshot.url || facilitiesRoute);
                    return true;
                };

                document.addEventListener('click', handleHierarchyBackClick, true);
                window.__perfectlumMobileShellPopstateHandler = handleHierarchyPopstate;

                bindFacilitiesView();

                    return () => {
                        document.removeEventListener('click', handleHierarchyBackClick, true);
                        if (window.__perfectlumMobileShellPopstateHandler === handleHierarchyPopstate) {
                            window.__perfectlumMobileShellPopstateHandler = null;
                        }
                        window.clearTimeout(timer);
                    };
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
