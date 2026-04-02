@extends('mobile.layouts.app')

@push('head')
    <style>
        .mobile-workstation-list { display: grid; gap: 0.58rem; }
        .mobile-workstation-card { display: block; border-radius: 1rem; border: 1px solid rgba(148,163,184,.14); background: rgba(255,255,255,.97); padding: .78rem .8rem; box-shadow: 0 8px 18px rgba(15,23,42,.032); transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease; }
        .mobile-workstation-card:active { transform: scale(.994); box-shadow: 0 10px 22px rgba(15,23,42,.042); }
        .mobile-workstation-card.attention { border-color: rgba(251,113,133,.18); background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,241,242,.45)); }
        .mobile-workstation-meta { display: flex; flex-wrap: wrap; align-items: center; gap: .36rem .5rem; }
        .mobile-workstation-title { margin-top: .24rem; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden; font-size: 14px; font-weight: 650; line-height: 1.28; letter-spacing: -.02em; color: #0f172a; }
        .mobile-workstation-detail { margin-top: .24rem; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden; font-size: 12px; line-height: 1.42; color: #475569; }
        .mobile-workstation-stat-row { display: flex; flex-wrap: wrap; gap: .38rem; margin-top: .62rem; }
        .mobile-workstation-stat { display: inline-flex; align-items: center; min-height: 1.6rem; border-radius: 999px; border: 1px solid rgba(226,232,240,.95); background: rgba(248,250,252,.96); padding: .25rem .54rem; font-size: 10.25px; font-weight: 600; line-height: 1; color: #475569; }
        .mobile-workstation-arrow { display: inline-flex; align-items: center; justify-content: center; height: 1.78rem; width: 1.78rem; flex: 0 0 auto; border-radius: 999px; border: 1px solid rgba(148,163,184,.16); background: rgba(248,250,252,.95); font-size: 15px; line-height: 1; color: #64748b; }
        .mobile-workstation-filter-row { display: flex; align-items: center; gap: .55rem; margin-top: .72rem; }
        .mobile-workstation-filter-trigger { display: grid; grid-template-columns: minmax(0,1fr) auto; align-items: center; gap: .5rem; width: 100%; min-height: 2.65rem; border-radius: .95rem; border: 1px solid rgba(226,232,240,.95); background: rgba(255,255,255,.94); padding: .58rem .72rem; text-align: left; box-shadow: 0 8px 18px rgba(15,23,42,.025); }
        .mobile-workstation-filter-trigger:disabled { opacity: .78; }
        .mobile-workstation-filter-label { display: block; font-size: 9px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: #94a3b8; }
        .mobile-workstation-filter-value { display: block; margin-top: .18rem; font-size: 12.5px; font-weight: 600; line-height: 1.3; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .mobile-workstation-filter-reset { display: inline-flex; align-items: center; justify-content: center; min-height: 2.65rem; border-radius: 999px; border: 1px solid rgba(226,232,240,.95); background: rgba(255,255,255,.96); padding: .58rem .8rem; font-size: 11px; font-weight: 700; color: #475569; white-space: nowrap; }
        .mobile-workstation-picker-shell { height: clamp(21rem, 66dvh, 32rem); max-height: calc(100dvh - .8rem); min-height: 21rem; }
        .mobile-workstation-picker-sheet { display: flex; flex-direction: column; height: 100%; max-height: 100%; overflow: hidden; padding-bottom: calc(env(safe-area-inset-bottom, 0px) + .8rem); }
        .mobile-workstation-picker-body { display: flex; min-height: 0; flex: 1 1 auto; flex-direction: column; overflow: hidden; }
        .mobile-workstation-picker-scroll { min-height: 0; flex: 1 1 auto; overflow-y: auto; overscroll-behavior: contain; padding-bottom: .2rem; }
        .mobile-workstation-picker-context { margin-top: .75rem; }
        .mobile-workstation-picker-crumb { display: inline-flex; align-items: center; gap: .36rem; min-height: 2rem; border-radius: 999px; border: 1px solid rgba(186,230,253,.98); background: rgba(240,249,255,.96); padding: .3rem .72rem; font-size: 11.5px; font-weight: 700; color: #0369a1; }
        .mobile-workstation-picker-section-title { margin-bottom: .4rem; font-size: 10px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: #94a3b8; }
        .mobile-workstation-picker-option { display: flex; align-items: center; justify-content: space-between; gap: .7rem; width: 100%; min-height: 3rem; border: 0; border-bottom: 1px solid rgba(226,232,240,.75); background: transparent; padding: .72rem .08rem; text-align: left; font: inherit; }
        .mobile-workstation-picker-option.active { background: rgba(240,249,255,.62); }
        .mobile-workstation-picker-option-main { min-width: 0; flex: 1 1 auto; }
        .mobile-workstation-picker-option-title { display: block; font-size: 14px; font-weight: 600; line-height: 1.28; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .mobile-workstation-picker-option-subtitle { display: block; margin-top: .2rem; font-size: 11px; line-height: 1.35; color: #64748b; }
        .mobile-workstation-picker-option-state { display: inline-flex; align-items: center; justify-content: center; height: 1.85rem; width: 1.85rem; flex: 0 0 auto; border-radius: 999px; border: 1px solid rgba(226,232,240,.92); background: rgba(255,255,255,.96); color: #94a3b8; }
        .mobile-workstation-picker-option.active .mobile-workstation-picker-option-state { border-color: rgba(125,211,252,.92); background: rgba(224,242,254,.98); color: #0284c7; }
        .mobile-workstation-picker-footer { display: flex; align-items: center; gap: .55rem; flex: 0 0 auto; margin-top: .72rem; border-top: 1px solid rgba(226,232,240,.9); padding-top: .78rem; background: linear-gradient(180deg, rgba(255,255,255,.72) 0%, rgba(255,255,255,.97) 28%, rgba(255,255,255,.99) 100%); backdrop-filter: blur(10px); position: sticky; bottom: 0; z-index: 2; }
        .mobile-workstation-picker-footer-button { display: inline-flex; align-items: center; justify-content: center; min-height: 2.75rem; border-radius: 999px; padding: .62rem 1rem; font-size: 12.5px; font-weight: 700; letter-spacing: -.01em; }
        .mobile-workstation-picker-footer-button.reset { min-width: 5.7rem; border: 1px solid rgba(203,213,225,.95); background: rgba(255,255,255,.98); color: #475569; }
        .mobile-workstation-picker-footer-button.primary { flex: 1 1 auto; border: 1px solid rgba(2,132,199,.38); background: linear-gradient(180deg, #38bdf8 0%, #0ea5e9 55%, #0284c7 100%); color: #f8fbff; text-shadow: 0 1px 0 rgba(3,105,161,.18); box-shadow: 0 10px 18px rgba(14,165,233,.2), inset 0 1px 0 rgba(255,255,255,.22); }
        .mobile-workstation-skeleton { position: relative; overflow: hidden; pointer-events: none; }
        .mobile-workstation-skeleton::after { content: ""; position: absolute; inset: 0; transform: translateX(-100%); background: linear-gradient(90deg, transparent, rgba(255,255,255,.72), transparent); animation: mobileWorkstationSkeletonSweep 1.12s ease-in-out infinite; }
        .mobile-workstation-skeleton-pill, .mobile-workstation-skeleton-line, .mobile-workstation-skeleton-circle { background: linear-gradient(90deg, rgba(226,232,240,.84), rgba(241,245,249,.96), rgba(226,232,240,.84)); background-size: 200% 100%; animation: mobileWorkstationSkeletonPulse 1.38s ease-in-out infinite; }
        .mobile-workstation-skeleton-pill, .mobile-workstation-skeleton-line, .mobile-workstation-skeleton-circle { border-radius: 999px; }
        .mobile-workstation-skeleton-pill { height: 1.42rem; width: 4.9rem; }
        .mobile-workstation-skeleton-circle { height: 1.85rem; width: 1.85rem; flex: 0 0 auto; }
        .mobile-workstation-skeleton-line { height: .82rem; }
        @keyframes mobileWorkstationSkeletonSweep { 100% { transform: translateX(100%); } }
        @keyframes mobileWorkstationSkeletonPulse { 0%, 100% { background-position: 100% 50%; } 50% { background-position: 0% 50%; } }
    </style>
@endpush

@section('content')
    @php
        $facilityId = request('facility_id');
        $facilityName = request('facility_name');
        $workgroupId = request('workgroup_id');
        $workgroupName = request('workgroup_name');
        $initialKeyword = (string) request('search', '');
        $initialPage = max(1, (int) request('page', 1));
        $staleOnly = request()->boolean('stale');
        $workstationFilters = $workstationFilters ?? [
            'canChooseFacility' => false,
            'facilities' => [],
            'workgroupsByFacility' => [],
            'selectedFacilityId' => '',
            'selectedFacilityName' => '',
            'selectedWorkgroupId' => '',
            'selectedWorkgroupName' => '',
        ];
        $initialScopeLabel = $workgroupName ?: ($facilityName ?: ($workstationFilters['selectedWorkgroupName'] ?: ($workstationFilters['selectedFacilityName'] ?: 'All workstations')));
        $hasScopeReset = filled($workgroupName) || ($workstationFilters['canChooseFacility'] && filled($facilityName));
    @endphp

    <div class="mobile-search-shell">
        <div class="mobile-searchbar">
            <i data-lucide="search" class="mobile-searchbar-icon"></i>
            <input id="mobile-workstation-search" type="search" class="mobile-input mobile-search-input" placeholder="Search workstations">
        </div>
        <div class="mobile-workstation-filter-row">
            <button id="mobile-workstation-scope-trigger" type="button" class="mobile-workstation-filter-trigger">
                <span class="min-w-0">
                    <span class="mobile-workstation-filter-label">Scope filter</span>
                    <span id="mobile-workstation-scope-value" class="mobile-workstation-filter-value">{{ $initialScopeLabel }}</span>
                </span>
                <i data-lucide="sliders-horizontal" class="h-4 w-4 text-slate-400"></i>
            </button>
            <button id="mobile-workstation-filter-reset" type="button" class="mobile-workstation-filter-reset" @if (!$hasScopeReset) hidden @endif>Reset</button>
        </div>
        @if ($staleOnly)
            <div class="mt-3 flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                <span class="mobile-filter-chip active">Stale only</span>
            </div>
        @endif
    </div>

    <section class="mobile-section-gap">
        <div id="mobile-workstation-list" class="mobile-workstation-list">
            <div class="mobile-workstation-card mobile-workstation-skeleton" aria-hidden="true">
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="mobile-workstation-meta">
                                    <span class="mobile-workstation-skeleton-pill"></span>
                                    <span class="mobile-workstation-skeleton-line h-[0.72rem] w-24"></span>
                                </div>
                                <div class="mobile-workstation-skeleton-line mt-2 h-[1rem] w-40"></div>
                                <div class="mobile-workstation-skeleton-line mt-2 h-[0.78rem] w-32"></div>
                            </div>
                            <span class="mobile-workstation-skeleton-circle"></span>
                        </div>
                        <div class="mobile-workstation-stat-row">
                            <span class="mobile-workstation-skeleton-pill" style="width: 5.9rem"></span>
                            <span class="mobile-workstation-skeleton-pill" style="width: 4.9rem"></span>
                            <span class="mobile-workstation-skeleton-pill" style="width: 4.4rem"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mobile-workstation-card mobile-workstation-skeleton" aria-hidden="true">
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="mobile-workstation-meta">
                                    <span class="mobile-workstation-skeleton-pill"></span>
                                    <span class="mobile-workstation-skeleton-line h-[0.72rem] w-20"></span>
                                </div>
                                <div class="mobile-workstation-skeleton-line mt-2 h-[1rem] w-36"></div>
                                <div class="mobile-workstation-skeleton-line mt-2 h-[0.78rem] w-28"></div>
                            </div>
                            <span class="mobile-workstation-skeleton-circle"></span>
                        </div>
                        <div class="mobile-workstation-stat-row">
                            <span class="mobile-workstation-skeleton-pill" style="width: 5.4rem"></span>
                            <span class="mobile-workstation-skeleton-pill" style="width: 4.6rem"></span>
                            <span class="mobile-workstation-skeleton-pill" style="width: 4.2rem"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="mobile-workstation-pagination" class="mt-3"></div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const boot = () => window.Perfectlum.mountMobilePage('mobileWorkstations', () => {
                const list = document.getElementById('mobile-workstation-list');
                const pagination = document.getElementById('mobile-workstation-pagination');
                const searchInput = document.getElementById('mobile-workstation-search');
                const scopeTrigger = document.getElementById('mobile-workstation-scope-trigger');
                const scopeValue = document.getElementById('mobile-workstation-scope-value');
                const resetFilterButton = document.getElementById('mobile-workstation-filter-reset');
                const pickerRoot = document.getElementById('mobile-workstation-picker');
                const pickerBackdrop = pickerRoot?.querySelector('.mobile-sheet-backdrop') || null;
                const pickerClose = document.getElementById('mobile-workstation-picker-close');
                const pickerTitle = document.getElementById('mobile-workstation-picker-title');
                const pickerContext = document.getElementById('mobile-workstation-picker-context');
                const pickerSearch = document.getElementById('mobile-workstation-picker-search');
                const pickerOptions = document.getElementById('mobile-workstation-picker-options');
                const pickerReset = document.getElementById('mobile-workstation-picker-reset');
                const pickerApply = document.getElementById('mobile-workstation-picker-apply');
                const filterConfig = @json($workstationFilters);
                const staleOnly = @json($staleOnly);
                const initialKeyword = @json($initialKeyword);
                const initialPage = @json($initialPage);
                const perPage = 10;
                const cache = new Map();
                let requestToken = 0;
                const canChooseFacility = !!filterConfig.canChooseFacility;
                const facilities = Array.isArray(filterConfig.facilities) ? filterConfig.facilities : [];
                const workgroupsByFacility = filterConfig.workgroupsByFacility || {};
                const defaultFacilityId = canChooseFacility ? '' : String(filterConfig.selectedFacilityId || '');
                const defaultFacilityName = canChooseFacility ? '' : String(filterConfig.selectedFacilityName || '');
                let timer = null;
                let currentPage = Number(initialPage || 1);
                let currentKeyword = String(initialKeyword || '');
                let selectedFacilityId = String(filterConfig.selectedFacilityId || @json($facilityId) || '');
                let selectedFacilityName = String(@json($facilityName) || filterConfig.selectedFacilityName || '');
                let selectedWorkgroupId = String(filterConfig.selectedWorkgroupId || @json($workgroupId) || '');
                let selectedWorkgroupName = String(@json($workgroupName) || filterConfig.selectedWorkgroupName || '');
                let draftFacilityId = selectedFacilityId || defaultFacilityId;
                let draftFacilityName = selectedFacilityName || defaultFacilityName;
                let draftWorkgroupId = selectedWorkgroupId;
                let draftWorkgroupName = selectedWorkgroupName;

                const getWorkgroupOptions = (facilityId) => Array.isArray(workgroupsByFacility[String(facilityId)] || workgroupsByFacility[facilityId]) ? (workgroupsByFacility[String(facilityId)] || workgroupsByFacility[facilityId]) : [];
                const findFacilityName = (value) => facilities.find((item) => String(item.id) === String(value))?.name || '';
                const findWorkgroupName = (facilityId, value) => getWorkgroupOptions(facilityId).find((item) => String(item.id) === String(value))?.name || '';
                const currentScopeLabel = () => selectedWorkgroupName || selectedFacilityName || 'All workstations';
                const hasResettableScope = () => !!selectedWorkgroupId || (canChooseFacility && !!selectedFacilityId);

                const syncScopeUi = () => {
                    scopeValue.textContent = currentScopeLabel();
                    resetFilterButton.hidden = !hasResettableScope();

                    const url = new URL(window.location.href);

                    if (selectedFacilityId) {
                        url.searchParams.set('facility_id', selectedFacilityId);
                        if (selectedFacilityName) {
                            url.searchParams.set('facility_name', selectedFacilityName);
                        } else {
                            url.searchParams.delete('facility_name');
                        }
                    } else {
                        url.searchParams.delete('facility_id');
                        url.searchParams.delete('facility_name');
                    }

                    if (selectedWorkgroupId) {
                        url.searchParams.set('workgroup_id', selectedWorkgroupId);
                        if (selectedWorkgroupName) {
                            url.searchParams.set('workgroup_name', selectedWorkgroupName);
                        } else {
                            url.searchParams.delete('workgroup_name');
                        }
                    } else {
                        url.searchParams.delete('workgroup_id');
                        url.searchParams.delete('workgroup_name');
                    }

                    if (staleOnly) {
                        url.searchParams.set('stale', '1');
                    }

                    if (currentKeyword) {
                        url.searchParams.set('search', currentKeyword);
                    } else {
                        url.searchParams.delete('search');
                    }

                    if (currentPage > 1) {
                        url.searchParams.set('page', String(currentPage));
                    } else {
                        url.searchParams.delete('page');
                    }

                    window.history.replaceState(window.history.state, '', `${url.pathname}${url.search}`);
                };

                const closePicker = () => {
                    if (!pickerRoot || !pickerSearch || !pickerOptions || !pickerContext) {
                        return;
                    }

                    pickerRoot.classList.add('hidden');
                    pickerSearch.value = '';
                    pickerContext.innerHTML = '';
                    pickerOptions.innerHTML = '';
                };

                const renderPickerOptions = () => {
                    if (!pickerOptions || !pickerSearch || !pickerTitle || !pickerContext) {
                        return;
                    }

                    const query = pickerSearch.value.trim().toLowerCase();
                    const activeFacilityId = draftFacilityId || defaultFacilityId;
                    const activeFacilityName = draftFacilityName || (activeFacilityId ? findFacilityName(activeFacilityId) : defaultFacilityName);
                    const atWorkgroupLevel = !!activeFacilityId;

                    pickerTitle.textContent = atWorkgroupLevel ? 'Choose workgroup' : 'Choose facility';
                    pickerSearch.placeholder = atWorkgroupLevel ? 'Search workgroups' : 'Search facilities';

                    pickerContext.innerHTML = atWorkgroupLevel && canChooseFacility
                        ? `<button type="button" class="mobile-workstation-picker-crumb" data-picker-back="facility"><i data-lucide="arrow-left" class="h-3.5 w-3.5"></i><span>${window.Perfectlum.escapeHtml(activeFacilityName || 'Selected facility')}</span></button>`
                        : '';

                    const facilityRows = facilities.filter((item) => !query || (item.name || '').toLowerCase().includes(query));
                    const workgroupRows = getWorkgroupOptions(activeFacilityId).filter((item) => !query || (item.name || '').toLowerCase().includes(query));

                    if (!atWorkgroupLevel) {
                        pickerOptions.innerHTML = `
                            <div>
                                <p class="mobile-workstation-picker-section-title">Facilities</p>
                                ${canChooseFacility && !query ? `
                                    <button type="button" class="mobile-workstation-picker-option ${!draftFacilityId ? 'active' : ''}" data-facility-id="">
                                        <span class="mobile-workstation-picker-option-main">
                                            <span class="mobile-workstation-picker-option-title">All facilities</span>
                                            <span class="mobile-workstation-picker-option-subtitle">Show every workstation in scope</span>
                                        </span>
                                        <span class="mobile-workstation-picker-option-state">${!draftFacilityId ? '<i data-lucide="check" class="h-3.5 w-3.5"></i>' : ''}</span>
                                    </button>
                                ` : ''}
                                ${facilityRows.length ? facilityRows.map((item) => `
                                    <button type="button" class="mobile-workstation-picker-option" data-facility-id="${item.id}" data-facility-name="${window.Perfectlum.escapeHtml(item.name)}">
                                        <span class="mobile-workstation-picker-option-main">
                                            <span class="mobile-workstation-picker-option-title">${window.Perfectlum.escapeHtml(item.name)}</span>
                                            <span class="mobile-workstation-picker-option-subtitle">Filter workstations inside this facility</span>
                                        </span>
                                        <span class="mobile-workstation-picker-option-state"><i data-lucide="chevron-right" class="h-3.5 w-3.5"></i></span>
                                    </button>
                                `).join('') : '<div class="mobile-empty">No facilities matched this filter.</div>'}
                            </div>
                        `;
                    } else {
                        pickerOptions.innerHTML = `
                            <div>
                                <p class="mobile-workstation-picker-section-title">Scope</p>
                                <button type="button" class="mobile-workstation-picker-option ${!draftWorkgroupId ? 'active' : ''}" data-workgroup-id="" data-workgroup-name="">
                                    <span class="mobile-workstation-picker-option-main">
                                        <span class="mobile-workstation-picker-option-title">All workstations in ${window.Perfectlum.escapeHtml(activeFacilityName || 'this facility')}</span>
                                        <span class="mobile-workstation-picker-option-subtitle">Keep facility filter only</span>
                                    </span>
                                    <span class="mobile-workstation-picker-option-state">${!draftWorkgroupId ? '<i data-lucide="check" class="h-3.5 w-3.5"></i>' : ''}</span>
                                </button>
                            </div>
                            <div class="mt-4">
                                <p class="mobile-workstation-picker-section-title">Workgroups</p>
                                ${workgroupRows.length ? workgroupRows.map((item) => `
                                    <button type="button" class="mobile-workstation-picker-option ${String(item.id) === String(draftWorkgroupId) ? 'active' : ''}" data-workgroup-id="${item.id}" data-workgroup-name="${window.Perfectlum.escapeHtml(item.name)}">
                                        <span class="mobile-workstation-picker-option-main">
                                            <span class="mobile-workstation-picker-option-title">${window.Perfectlum.escapeHtml(item.name)}</span>
                                            <span class="mobile-workstation-picker-option-subtitle">Workgroup • ${window.Perfectlum.escapeHtml(activeFacilityName || '')}</span>
                                        </span>
                                        <span class="mobile-workstation-picker-option-state">${String(item.id) === String(draftWorkgroupId) ? '<i data-lucide="check" class="h-3.5 w-3.5"></i>' : '<i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>'}</span>
                                    </button>
                                `).join('') : '<div class="mobile-empty">No workgroups matched this filter.</div>'}
                            </div>
                        `;
                    }

                    window.lucide?.createIcons();

                    pickerContext.querySelector('[data-picker-back="facility"]')?.addEventListener('click', () => {
                        draftFacilityId = '';
                        draftFacilityName = '';
                        draftWorkgroupId = '';
                        draftWorkgroupName = '';
                        pickerSearch.value = '';
                        renderPickerOptions();
                    });

                    pickerOptions.querySelectorAll('[data-facility-id]').forEach((button) => {
                        button.addEventListener('click', () => {
                            draftFacilityId = String(button.getAttribute('data-facility-id') || '');
                            draftFacilityName = String(button.getAttribute('data-facility-name') || '') || findFacilityName(draftFacilityId);
                            draftWorkgroupId = '';
                            draftWorkgroupName = '';
                            renderPickerOptions();
                        });
                    });

                    pickerOptions.querySelectorAll('[data-workgroup-id]').forEach((button) => {
                        button.addEventListener('click', () => {
                            draftWorkgroupId = String(button.getAttribute('data-workgroup-id') || '');
                            draftWorkgroupName = String(button.getAttribute('data-workgroup-name') || '') || findWorkgroupName(activeFacilityId, draftWorkgroupId);
                            renderPickerOptions();
                        });
                    });
                };

                const openPicker = () => {
                    if (!pickerRoot || !pickerSearch || !pickerOptions) {
                        return;
                    }

                    draftFacilityId = selectedFacilityId || defaultFacilityId;
                    draftFacilityName = selectedFacilityName || defaultFacilityName || findFacilityName(draftFacilityId);
                    draftWorkgroupId = selectedWorkgroupId;
                    draftWorkgroupName = selectedWorkgroupName || findWorkgroupName(draftFacilityId, draftWorkgroupId);
                    pickerRoot.classList.remove('hidden');
                    pickerSearch.value = '';
                    renderPickerOptions();
                    pickerSearch.focus({ preventScroll: true });
                };

                const resetScope = () => {
                    selectedFacilityId = defaultFacilityId;
                    selectedFacilityName = defaultFacilityName;
                    selectedWorkgroupId = '';
                    selectedWorkgroupName = '';
                    draftFacilityId = selectedFacilityId;
                    draftFacilityName = selectedFacilityName;
                    draftWorkgroupId = '';
                    draftWorkgroupName = '';
                };

                const loadingState = () => Array.from({ length: 4 }).map(() => `
                    <div class="mobile-workstation-card mobile-workstation-skeleton" aria-hidden="true">
                        <div class="flex items-start gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="mobile-workstation-meta">
                                            <span class="mobile-workstation-skeleton-pill"></span>
                                            <span class="mobile-workstation-skeleton-line h-[0.72rem] w-24"></span>
                                        </div>
                                        <div class="mobile-workstation-skeleton-line mt-2 h-[1rem] w-40"></div>
                                        <div class="mobile-workstation-skeleton-line mt-2 h-[0.78rem] w-32"></div>
                                    </div>
                                    <span class="mobile-workstation-skeleton-circle"></span>
                                </div>
                                <div class="mobile-workstation-stat-row">
                                    <span class="mobile-workstation-skeleton-pill" style="width: 5.9rem"></span>
                                    <span class="mobile-workstation-skeleton-pill" style="width: 4.9rem"></span>
                                    <span class="mobile-workstation-skeleton-pill" style="width: 4.4rem"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
                const emptyState = (message) => `<div class="mobile-empty">${message}</div>`;
                const renderPager = (total, page, limit) => {
                    const lastPage = Math.max(1, Math.ceil(total / limit));
                    if (total <= limit) return '';
                    const from = total ? (((page - 1) * limit) + 1) : 0;
                    const to = Math.min(page * limit, total);
                    return `<div class="mobile-pager"><p class="mobile-pager-meta">${from}-${to} of ${total}</p><div class="mobile-pager-actions"><button type="button" class="mobile-pager-button" data-page="${page - 1}" ${page <= 1 ? 'disabled' : ''}>Prev</button><span class="mobile-pager-status">Page ${page} / ${lastPage}</span><button type="button" class="mobile-pager-button" data-page="${page + 1}" ${page >= lastPage ? 'disabled' : ''}>Next</button></div></div>`;
                };

                const buildDisplayHref = (item) => {
                    const params = new URLSearchParams();
                    if (selectedFacilityId || item.facId) params.set('facility_id', selectedFacilityId || item.facId);
                    if (selectedWorkgroupId || item.wgId) params.set('workgroup_id', selectedWorkgroupId || item.wgId);
                    if (selectedFacilityName || item.facName) params.set('facility_name', selectedFacilityName || item.facName);
                    if (selectedWorkgroupName || item.wgName) params.set('workgroup_name', selectedWorkgroupName || item.wgName);
                    if (item.name) params.set('workstation_name', item.name);
                    params.set('return_to', `${window.location.pathname}${window.location.search}`);
                    return `${@json(url('/m/workstations'))}/${item.id}?${params.toString()}`;
                };

                async function loadWorkstations(keyword = '', page = 1) {
                    currentKeyword = keyword;
                    currentPage = page;
                    const cacheKey = `${selectedFacilityId}::${selectedWorkgroupId}::${staleOnly ? '1' : '0'}::${keyword}::${page}`;
                    const cached = cache.get(cacheKey);
                    const currentRequest = ++requestToken;

                    if (cached) {
                        list.innerHTML = cached.html;
                        pagination.innerHTML = cached.pager;
                        syncScopeUi();
                        return;
                    }

                    syncScopeUi();
                    list.innerHTML = loadingState();
                    pagination.innerHTML = '';
                    const params = new URLSearchParams({ limit: String(perPage), page: String(page) });
                    if (selectedFacilityId) params.set('facility_id', selectedFacilityId);
                    if (selectedWorkgroupId) params.set('workgroup_id', selectedWorkgroupId);
                    if (keyword) params.set('search', keyword);
                    if (staleOnly) params.set('stale', '1');

                    try {
                        const response = await window.Perfectlum.request(`/api/workstations?${params.toString()}`);
                        if (currentRequest !== requestToken) {
                            return;
                        }
                        const rows = response.data || [];
                        const total = Number(response.total || 0);
                        if (!rows.length) {
                            list.innerHTML = emptyState('No workstations matched this filter.');
                            return;
                        }

                        const html = rows.map((item) => `
                            <a href="${buildDisplayHref(item)}" class="mobile-workstation-card ${item.displayHealth === 'failed' ? 'attention' : ''}">
                                <div class="flex items-start gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="mobile-workstation-meta">
                                                    <span class="mobile-type-pill ${item.displayHealth === 'ok' ? 'run' : (item.displayHealth === 'failed' ? 'alert' : 'sync')}">${item.displayHealth === 'ok' ? 'Healthy' : (item.displayHealth === 'failed' ? 'Alert' : 'Sync')}</span>
                                                    <p class="mobile-meta mobile-clamp-1">${window.Perfectlum.escapeHtml(item.wgName)} • ${window.Perfectlum.escapeHtml(item.facName)}</p>
                                                </div>
                                                <p class="mobile-workstation-title">${window.Perfectlum.escapeHtml(item.name)}</p>
                                                <p class="mobile-workstation-detail">${item.lastConnected !== '-' ? `Last sync ${window.Perfectlum.escapeHtml(item.lastSeenRelative)}` : 'No sync data received'}</p>
                                            </div>
                                            <span class="mobile-workstation-arrow">›</span>
                                        </div>
                                        <div class="mobile-workstation-stat-row">
                                            <span class="mobile-workstation-stat">${item.displaysCount} displays</span>
                                            <span class="mobile-workstation-stat">${item.okDisplaysCount} ok</span>
                                            <span class="mobile-workstation-stat">${item.failedDisplaysCount} alert</span>
                                            <span class="mobile-workstation-stat">Sleep ${window.Perfectlum.escapeHtml(item.sleepTime)}</span>
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
                        list.innerHTML = emptyState('Unable to load workstations right now.');
                    }
                }

                pagination.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-page]');
                    if (!button || button.hasAttribute('disabled')) return;
                    loadWorkstations(currentKeyword, Number(button.dataset.page));
                });

                searchInput.addEventListener('input', () => {
                    window.clearTimeout(timer);
                    timer = window.setTimeout(() => loadWorkstations(searchInput.value.trim(), 1), 220);
                });

                scopeTrigger?.addEventListener('click', openPicker);
                pickerBackdrop?.addEventListener('click', closePicker);
                pickerClose?.addEventListener('click', closePicker);
                pickerSearch?.addEventListener('input', renderPickerOptions);
                pickerReset?.addEventListener('click', () => {
                    resetScope();
                    if (pickerSearch) pickerSearch.value = '';
                    renderPickerOptions();
                });
                pickerApply?.addEventListener('click', () => {
                    selectedFacilityId = draftFacilityId || defaultFacilityId;
                    selectedFacilityName = selectedFacilityId ? (draftFacilityName || findFacilityName(selectedFacilityId) || defaultFacilityName) : '';
                    selectedWorkgroupId = draftWorkgroupId;
                    selectedWorkgroupName = selectedWorkgroupId ? (draftWorkgroupName || findWorkgroupName(selectedFacilityId, selectedWorkgroupId)) : '';
                    closePicker();
                    syncScopeUi();
                    loadWorkstations(searchInput.value.trim(), 1);
                });
                resetFilterButton?.addEventListener('click', () => {
                    resetScope();
                    syncScopeUi();
                    loadWorkstations(searchInput.value.trim(), 1);
                });

                searchInput.value = currentKeyword;
                syncScopeUi();
                loadWorkstations(currentKeyword, currentPage);

                return () => window.clearTimeout(timer);
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
    <div id="mobile-workstation-picker" class="hidden">
        <div class="mobile-sheet-backdrop fixed inset-0 z-[120]"></div>
        <div class="mobile-workstation-picker-shell fixed bottom-0 left-1/2 z-[130] w-full max-w-[440px] -translate-x-1/2">
            <div class="mobile-sheet mobile-workstation-picker-sheet compact px-4 pt-3">
                <div class="mobile-sheet-handle"></div>
                <div class="mt-3 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Filter workstations</p>
                        <p id="mobile-workstation-picker-title" class="mt-1 text-[15px] font-semibold text-slate-950">Choose facility</p>
                    </div>
                    <button id="mobile-workstation-picker-close" type="button" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600">
                        <i data-lucide="x" class="h-4 w-4"></i>
                    </button>
                </div>

                <div id="mobile-workstation-picker-context" class="mobile-workstation-picker-context"></div>

                <div class="mobile-workstation-picker-body">
                    <div class="mobile-searchbar mt-4 shrink-0">
                        <i data-lucide="search" class="mobile-searchbar-icon"></i>
                        <input id="mobile-workstation-picker-search" type="search" class="mobile-input mobile-search-input" placeholder="Search facilities">
                    </div>

                    <div id="mobile-workstation-picker-options" class="mobile-workstation-picker-scroll mt-4"></div>
                </div>

                <div class="mobile-workstation-picker-footer">
                    <button id="mobile-workstation-picker-reset" type="button" class="mobile-workstation-picker-footer-button reset">Reset</button>
                    <button id="mobile-workstation-picker-apply" type="button" class="mobile-workstation-picker-footer-button primary">Apply scope</button>
                </div>
            </div>
        </div>
    </div>
@endpush
