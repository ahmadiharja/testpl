@extends('mobile.layouts.app')

@push('head')
    <style>
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

        .mobile-workgroup-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.36rem 0.5rem;
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

        .mobile-workgroup-filter-row {
            display: flex;
            gap: 0.55rem;
            margin-top: 0.72rem;
            align-items: center;
        }

        .mobile-workgroup-filter-trigger {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            min-height: 2.65rem;
            border-radius: 0.95rem;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(255, 255, 255, 0.94);
            padding: 0.58rem 0.72rem;
            text-align: left;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.025);
        }

        .mobile-workgroup-filter-trigger:disabled {
            opacity: 0.8;
        }

        .mobile-workgroup-filter-label {
            display: block;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-workgroup-filter-value {
            display: block;
            margin-top: 0.18rem;
            font-size: 12.5px;
            font-weight: 600;
            line-height: 1.3;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mobile-workgroup-filter-reset {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.65rem;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(255, 255, 255, 0.96);
            padding: 0.58rem 0.8rem;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            white-space: nowrap;
        }

        .mobile-workgroup-picker-shell {
            height: clamp(20rem, 62dvh, 31rem);
            max-height: calc(100dvh - 0.8rem);
            min-height: 20rem;
        }

        .mobile-workgroup-picker-sheet {
            display: flex;
            flex-direction: column;
            height: 100%;
            max-height: 100%;
            overflow: hidden;
            padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 0.8rem);
        }

        .mobile-workgroup-picker-body {
            display: flex;
            min-height: 0;
            flex: 1 1 auto;
            flex-direction: column;
            overflow: hidden;
        }

        .mobile-workgroup-picker-scroll {
            min-height: 0;
            flex: 1 1 auto;
            overflow-y: auto;
            overscroll-behavior: contain;
            padding-bottom: 0.2rem;
        }

        .mobile-workgroup-picker-section-title {
            margin-bottom: 0.38rem;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-workgroup-picker-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.7rem;
            width: 100%;
            min-height: 3rem;
            border: 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.75);
            background: transparent;
            padding: 0.72rem 0.08rem;
            text-align: left;
            font: inherit;
        }

        .mobile-workgroup-picker-option.active {
            background: rgba(240, 249, 255, 0.72);
        }

        .mobile-workgroup-picker-option-main {
            min-width: 0;
            flex: 1 1 auto;
        }

        .mobile-workgroup-picker-option-title {
            display: block;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.28;
            color: #0f172a;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .mobile-workgroup-picker-option-subtitle {
            display: block;
            margin-top: 0.2rem;
            font-size: 11px;
            line-height: 1.35;
            color: #64748b;
        }

        .mobile-workgroup-picker-option-state {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 1.85rem;
            width: 1.85rem;
            flex: 0 0 auto;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.92);
            background: rgba(255, 255, 255, 0.96);
            color: #94a3b8;
        }

        .mobile-workgroup-picker-option.active .mobile-workgroup-picker-option-state {
            border-color: rgba(125, 211, 252, 0.92);
            background: rgba(224, 242, 254, 0.98);
            color: #0284c7;
        }

        .mobile-workgroup-picker-footer {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            flex: 0 0 auto;
            margin-top: 0.72rem;
            border-top: 1px solid rgba(226, 232, 240, 0.9);
            padding-top: 0.78rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.72) 0%, rgba(255, 255, 255, 0.97) 28%, rgba(255, 255, 255, 0.99) 100%);
            backdrop-filter: blur(10px);
            position: sticky;
            bottom: 0;
            z-index: 2;
        }

        .mobile-workgroup-picker-footer-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.75rem;
            border-radius: 999px;
            padding: 0.62rem 1rem;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .mobile-workgroup-picker-footer-button.reset {
            min-width: 5.7rem;
            border: 1px solid rgba(203, 213, 225, 0.95);
            background: rgba(255, 255, 255, 0.98);
            color: #475569;
        }

        .mobile-workgroup-picker-footer-button.primary {
            flex: 1 1 auto;
            border: 1px solid rgba(2, 132, 199, 0.38);
            background: linear-gradient(180deg, #38bdf8 0%, #0ea5e9 55%, #0284c7 100%);
            color: #f8fbff;
            text-shadow: 0 1px 0 rgba(3, 105, 161, 0.18);
            box-shadow: 0 10px 18px rgba(14, 165, 233, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.22);
        }

        .mobile-workgroup-skeleton {
            position: relative;
            overflow: hidden;
            pointer-events: none;
        }

        .mobile-workgroup-skeleton::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.72), transparent);
            animation: mobileWorkgroupSkeletonSweep 1.12s ease-in-out infinite;
        }

        .mobile-workgroup-skeleton-pill,
        .mobile-workgroup-skeleton-line,
        .mobile-workgroup-skeleton-circle {
            background: linear-gradient(90deg, rgba(226, 232, 240, 0.84), rgba(241, 245, 249, 0.96), rgba(226, 232, 240, 0.84));
            background-size: 200% 100%;
            animation: mobileWorkgroupSkeletonPulse 1.38s ease-in-out infinite;
        }

        .mobile-workgroup-skeleton-pill,
        .mobile-workgroup-skeleton-circle,
        .mobile-workgroup-skeleton-line {
            border-radius: 999px;
        }

        .mobile-workgroup-skeleton-pill {
            height: 1.42rem;
            width: 4.9rem;
        }

        .mobile-workgroup-skeleton-circle {
            height: 1.85rem;
            width: 1.85rem;
            flex: 0 0 auto;
        }

        .mobile-workgroup-skeleton-line {
            height: 0.82rem;
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
        $facilityId = request('facility_id');
        $facilityName = request('facility_name');
        $initialKeyword = (string) request('search', '');
        $initialPage = max(1, (int) request('page', 1));
        $facilityFilters = $facilityFilters ?? [
            'canChooseFacility' => false,
            'facilities' => [],
            'selectedFacilityId' => '',
            'selectedFacilityName' => '',
        ];
    @endphp

    <div class="mobile-search-shell">
        <div class="mobile-searchbar">
            <i data-lucide="search" class="mobile-searchbar-icon"></i>
            <input id="mobile-workgroup-search" type="search" class="mobile-input mobile-search-input" placeholder="Search workgroups">
        </div>
        <div class="mobile-workgroup-filter-row">
            <button id="mobile-workgroup-facility-trigger" type="button" class="mobile-workgroup-filter-trigger" @if (!$facilityFilters['canChooseFacility'] && count($facilityFilters['facilities']) <= 1) disabled @endif>
                <span class="min-w-0">
                    <span class="mobile-workgroup-filter-label">Facility filter</span>
                    <span id="mobile-workgroup-facility-value" class="mobile-workgroup-filter-value">{{ $facilityName ?: ($facilityFilters['selectedFacilityName'] ?: 'All facilities') }}</span>
                </span>
                <i data-lucide="sliders-horizontal" class="h-4 w-4 text-slate-400"></i>
            </button>
            <button id="mobile-workgroup-filter-reset" type="button" class="mobile-workgroup-filter-reset">Reset</button>
        </div>
    </div>

    <section class="mobile-section-gap">
        <div id="mobile-workgroup-list" class="mobile-workgroup-list">
            <div class="mobile-workgroup-card mobile-workgroup-skeleton" aria-hidden="true">
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="mobile-workgroup-meta">
                                    <span class="mobile-workgroup-skeleton-pill"></span>
                                    <span class="mobile-workgroup-skeleton-line h-[0.72rem] w-24"></span>
                                </div>
                                <div class="mobile-workgroup-skeleton-line mt-2 h-[1rem] w-40"></div>
                                <div class="mobile-workgroup-skeleton-line mt-2 h-[0.78rem] w-32"></div>
                            </div>
                            <span class="mobile-workgroup-skeleton-circle"></span>
                        </div>
                        <div class="mobile-workgroup-stat-row">
                            <span class="mobile-workgroup-skeleton-pill" style="width: 5.9rem"></span>
                            <span class="mobile-workgroup-skeleton-pill" style="width: 4.9rem"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mobile-workgroup-card mobile-workgroup-skeleton" aria-hidden="true">
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="mobile-workgroup-meta">
                                    <span class="mobile-workgroup-skeleton-pill"></span>
                                    <span class="mobile-workgroup-skeleton-line h-[0.72rem] w-20"></span>
                                </div>
                                <div class="mobile-workgroup-skeleton-line mt-2 h-[1rem] w-36"></div>
                                <div class="mobile-workgroup-skeleton-line mt-2 h-[0.78rem] w-28"></div>
                            </div>
                            <span class="mobile-workgroup-skeleton-circle"></span>
                        </div>
                        <div class="mobile-workgroup-stat-row">
                            <span class="mobile-workgroup-skeleton-pill" style="width: 5.4rem"></span>
                            <span class="mobile-workgroup-skeleton-pill" style="width: 4.6rem"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="mobile-workgroup-pagination" class="mt-3"></div>
    </section>

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileWorkgroups', () => {
                    const list = document.getElementById('mobile-workgroup-list');
                    const pagination = document.getElementById('mobile-workgroup-pagination');
                    const searchInput = document.getElementById('mobile-workgroup-search');
                    const facilityTrigger = document.getElementById('mobile-workgroup-facility-trigger');
                    const facilityValue = document.getElementById('mobile-workgroup-facility-value');
                    const resetFilterButton = document.getElementById('mobile-workgroup-filter-reset');
                    const pickerRoot = document.getElementById('mobile-workgroup-picker');
                    const pickerBackdrop = pickerRoot?.querySelector('.mobile-sheet-backdrop') || null;
                    const pickerClose = document.getElementById('mobile-workgroup-picker-close');
                    const pickerSearch = document.getElementById('mobile-workgroup-picker-search');
                    const pickerOptions = document.getElementById('mobile-workgroup-picker-options');
                    const pickerReset = document.getElementById('mobile-workgroup-picker-reset');
                    const pickerApply = document.getElementById('mobile-workgroup-picker-apply');
                    const facilityConfig = @json($facilityFilters);
                    const initialFacilityId = @json($facilityId);
                    const initialFacilityName = @json($facilityName);
                    const initialKeyword = @json($initialKeyword);
                    const initialPage = @json($initialPage);
                    const perPage = 10;
                    const cache = new Map();
                    let requestToken = 0;
                    let timer = null;
                    let currentPage = Number(initialPage || 1);
                    let currentKeyword = String(initialKeyword || '');
                    let selectedFacilityId = String(facilityConfig.selectedFacilityId || initialFacilityId || '');
                    let selectedFacilityName = initialFacilityName || String(facilityConfig.selectedFacilityName || '');
                    let draftFacilityId = selectedFacilityId;
                    const getFacilityOptions = () => Array.isArray(facilityConfig.facilities) ? facilityConfig.facilities : [];

                    const findFacilityName = (value) => {
                        const match = getFacilityOptions().find((item) => String(item.id) === String(value));
                        return match?.name || '';
                    };

                    const syncRouteState = (keyword = currentKeyword, page = currentPage) => {
                        facilityValue.textContent = selectedFacilityName || 'All facilities';
                        resetFilterButton.hidden = !facilityConfig.canChooseFacility || !selectedFacilityId;
                        const url = new URL(window.location.href);
                        if (selectedFacilityId) {
                            url.searchParams.set('facility_id', selectedFacilityId);
                            if (selectedFacilityName) {
                                url.searchParams.set('facility_name', selectedFacilityName);
                            }
                        } else {
                            url.searchParams.delete('facility_id');
                            url.searchParams.delete('facility_name');
                        }

                        if (keyword) {
                            url.searchParams.set('search', keyword);
                        } else {
                            url.searchParams.delete('search');
                        }

                        if (page > 1) {
                            url.searchParams.set('page', String(page));
                        } else {
                            url.searchParams.delete('page');
                        }

                        window.history.replaceState(window.history.state, '', `${url.pathname}${url.search}`);
                    };

                    const closePicker = () => {
                        if (!pickerRoot || !pickerSearch || !pickerOptions) {
                            return;
                        }

                        pickerRoot.classList.add('hidden');
                        pickerSearch.value = '';
                        pickerOptions.innerHTML = '';
                    };

                    const renderPickerOptions = () => {
                        if (!pickerOptions || !pickerSearch) {
                            return;
                        }

                        const query = pickerSearch.value.trim().toLowerCase();
                        const options = getFacilityOptions().filter((item) => !query || (item.name || '').toLowerCase().includes(query));
                        pickerOptions.innerHTML = `
                            <div>
                                <p class="mobile-workgroup-picker-section-title">Facilities</p>
                                ${facilityConfig.canChooseFacility && !query ? `
                                    <button type="button" class="mobile-workgroup-picker-option ${!draftFacilityId ? 'active' : ''}" data-facility-id="">
                                        <span class="mobile-workgroup-picker-option-main">
                                            <span class="mobile-workgroup-picker-option-title">All facilities</span>
                                            <span class="mobile-workgroup-picker-option-subtitle">Show every workgroup in scope</span>
                                        </span>
                                        <span class="mobile-workgroup-picker-option-state">
                                            ${!draftFacilityId ? '<i data-lucide="check" class="h-3.5 w-3.5"></i>' : ''}
                                        </span>
                                    </button>
                                ` : ''}
                                ${options.length ? options.map((item) => `
                                    <button type="button" class="mobile-workgroup-picker-option ${String(item.id) === String(draftFacilityId) ? 'active' : ''}" data-facility-id="${item.id}">
                                        <span class="mobile-workgroup-picker-option-main">
                                            <span class="mobile-workgroup-picker-option-title">${item.name}</span>
                                            <span class="mobile-workgroup-picker-option-subtitle">Facility scope</span>
                                        </span>
                                        <span class="mobile-workgroup-picker-option-state">
                                            ${String(item.id) === String(draftFacilityId) ? '<i data-lucide="check" class="h-3.5 w-3.5"></i>' : ''}
                                        </span>
                                    </button>
                                `).join('') : '<div class="mobile-empty">No facilities matched this filter.</div>'}
                            </div>
                        `;

                        lucide.createIcons();

                        pickerOptions.querySelectorAll('[data-facility-id]').forEach((button) => {
                            button.addEventListener('click', () => {
                                draftFacilityId = String(button.getAttribute('data-facility-id') || '');
                                renderPickerOptions();
                            });
                        });
                    };

                    const openPicker = () => {
                        if (!pickerRoot || !pickerSearch || !pickerOptions || facilityTrigger.disabled) {
                            return;
                        }

                        draftFacilityId = selectedFacilityId;
                        pickerRoot.classList.remove('hidden');
                        pickerSearch.value = '';
                        renderPickerOptions();
                        pickerSearch.focus({ preventScroll: true });
                    };

                    const loadingState = () => Array.from({ length: 4 }).map(() => `
                        <div class="mobile-workgroup-card mobile-workgroup-skeleton" aria-hidden="true">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="mobile-workgroup-meta">
                                                <span class="mobile-workgroup-skeleton-pill"></span>
                                                <span class="mobile-workgroup-skeleton-line h-[0.72rem] w-24"></span>
                                            </div>
                                            <div class="mobile-workgroup-skeleton-line mt-2 h-[1rem] w-40"></div>
                                            <div class="mobile-workgroup-skeleton-line mt-2 h-[0.78rem] w-32"></div>
                                        </div>
                                        <span class="mobile-workgroup-skeleton-circle"></span>
                                    </div>
                                    <div class="mobile-workgroup-stat-row">
                                        <span class="mobile-workgroup-skeleton-pill" style="width: 5.9rem"></span>
                                        <span class="mobile-workgroup-skeleton-pill" style="width: 4.9rem"></span>
                                    </div>
                                </div>
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

                    const buildWorkstationHref = (item) => {
                        const params = new URLSearchParams();
                        if (selectedFacilityId || item.facId) params.set('facility_id', selectedFacilityId || item.facId);
                        if (selectedFacilityName || item.facName) params.set('facility_name', selectedFacilityName || item.facName);
                        if (item.name) params.set('workgroup_name', item.name);
                        params.set('return_to', `${window.location.pathname}${window.location.search}`);
                        return `${@json(url('/m/workgroups'))}/${item.id}?${params.toString()}`;
                    };

                    async function loadWorkgroups(keyword = '', page = 1) {
                        currentKeyword = keyword;
                        currentPage = page;
                        const cacheKey = `${selectedFacilityId}::${keyword}::${page}`;
                        const cached = cache.get(cacheKey);
                        const currentRequest = ++requestToken;

                        if (cached) {
                            list.innerHTML = cached.html;
                            pagination.innerHTML = cached.pager;
                            syncRouteState(keyword, page);
                            return;
                        }

                        syncRouteState(keyword, page);
                        list.innerHTML = loadingState();
                        pagination.innerHTML = '';

                        const params = new URLSearchParams({
                            limit: String(perPage),
                            page: String(page),
                        });
                        if (selectedFacilityId) params.set('facility_id', selectedFacilityId);
                        if (keyword) params.set('search', keyword);

                        try {
                            const response = await window.Perfectlum.request(`/api/workgroups?${params.toString()}`);
                            if (currentRequest !== requestToken) {
                                return;
                            }
                            const rows = response.data || [];
                            const total = Number(response.total || 0);

                            if (!rows.length) {
                                list.innerHTML = emptyState('No workgroups matched this filter.');
                                return;
                            }

                            const html = rows.map((item) => `
                                <a href="${buildWorkstationHref(item)}" class="mobile-workgroup-card ${item.displayHealth === 'failed' ? 'attention' : ''}">
                                    <div class="flex items-start gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <div class="mobile-workgroup-meta">
                                                        <span class="mobile-type-pill ${item.displayHealth === 'ok' ? 'run' : (item.displayHealth === 'failed' ? 'alert' : 'sync')}">${item.displayHealth === 'ok' ? 'Healthy' : (item.displayHealth === 'failed' ? 'Alert' : 'Group')}</span>
                                                        <p class="mobile-meta mobile-clamp-1">${selectedFacilityName || item.facName || '-'}</p>
                                                    </div>
                                                    <p class="mobile-workgroup-title">${item.name}</p>
                                                    <p class="mobile-workgroup-detail">${[item.address, item.phone].filter((value) => value && value !== '-').join(' • ') || 'No address or phone saved'}</p>
                                                </div>
                                                <span class="mobile-workgroup-arrow">›</span>
                                            </div>
                                            <div class="mobile-workgroup-stat-row">
                                                <span class="mobile-workgroup-stat">${item.workstationsCount} workstations</span>
                                                <span class="mobile-workgroup-stat">${item.displaysCount} displays</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            `).join('');
                            const pager = renderPager(total, page, perPage);
                            cache.set(cacheKey, { html, pager });
                            list.innerHTML = html;
                            pagination.innerHTML = pager;
                        } catch (error) {
                            if (currentRequest !== requestToken) {
                                return;
                            }
                            list.innerHTML = emptyState('Unable to load workgroups right now.');
                        }
                    }

                    pagination.addEventListener('click', (event) => {
                        const button = event.target.closest('[data-page]');
                        if (!button || button.hasAttribute('disabled')) {
                            return;
                        }

                        loadWorkgroups(currentKeyword, Number(button.dataset.page));
                    });

                    searchInput.addEventListener('input', () => {
                        window.clearTimeout(timer);
                        timer = window.setTimeout(() => loadWorkgroups(searchInput.value.trim(), 1), 220);
                    });

                    facilityTrigger.addEventListener('click', openPicker);
                    pickerBackdrop?.addEventListener('click', closePicker);
                    pickerClose?.addEventListener('click', closePicker);
                    pickerSearch?.addEventListener('input', renderPickerOptions);
                    pickerReset?.addEventListener('click', () => {
                        selectedFacilityId = '';
                        selectedFacilityName = '';
                        draftFacilityId = '';
                        if (pickerSearch) {
                            pickerSearch.value = '';
                        }
                        closePicker();
                        syncRouteState();
                        loadWorkgroups(searchInput.value.trim(), 1);
                    });
                    pickerApply?.addEventListener('click', () => {
                        selectedFacilityId = draftFacilityId;
                        selectedFacilityName = draftFacilityId ? findFacilityName(draftFacilityId) : '';
                        closePicker();
                        syncRouteState();
                        loadWorkgroups(searchInput.value.trim(), 1);
                    });
                    resetFilterButton.addEventListener('click', () => {
                        selectedFacilityId = '';
                        selectedFacilityName = '';
                        draftFacilityId = '';
                        syncRouteState();
                        loadWorkgroups(searchInput.value.trim(), 1);
                    });

                    searchInput.value = currentKeyword;
                    syncRouteState(currentKeyword, currentPage);
                    loadWorkgroups(currentKeyword, currentPage);

                    return () => {
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

    @push('modals')
        <div id="mobile-workgroup-picker" class="hidden">
            <div class="mobile-sheet-backdrop fixed inset-0 z-[120]"></div>
            <div class="mobile-workgroup-picker-shell fixed bottom-0 left-1/2 z-[130] w-full max-w-[440px] -translate-x-1/2">
                <div class="mobile-sheet mobile-workgroup-picker-sheet compact px-4 pt-3">
                    <div class="mobile-sheet-handle"></div>
                    <div class="mt-3 flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Filter workgroups</p>
                            <p class="mt-1 text-[15px] font-semibold text-slate-950">Choose facility</p>
                        </div>
                        <button id="mobile-workgroup-picker-close" type="button" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </div>

                    <div class="mobile-workgroup-picker-body">
                        <div class="mobile-searchbar mt-4 shrink-0">
                            <i data-lucide="search" class="mobile-searchbar-icon"></i>
                            <input id="mobile-workgroup-picker-search" type="search" class="mobile-input mobile-search-input" placeholder="Search facilities">
                        </div>

                        <div id="mobile-workgroup-picker-options" class="mobile-workgroup-picker-scroll mt-4"></div>
                    </div>

                    <div class="mobile-workgroup-picker-footer">
                        <button id="mobile-workgroup-picker-reset" type="button" class="mobile-workgroup-picker-footer-button reset">Reset</button>
                        <button id="mobile-workgroup-picker-apply" type="button" class="mobile-workgroup-picker-footer-button primary">Apply facility</button>
                    </div>
                </div>
            </div>
        </div>
    @endpush
@endsection
