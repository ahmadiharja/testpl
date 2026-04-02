@extends('mobile.layouts.app')

@push('head')
    <style>
        .mobile-display-list {
            display: grid;
            gap: 0.6rem;
        }

        .mobile-display-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 0.85rem;
            align-items: start;
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.15);
            background: rgba(255, 255, 255, 0.96);
            padding: 0.82rem 0.86rem;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        }

        .mobile-display-card.is-link {
            transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
        }

        .mobile-display-card.is-link:active {
            transform: scale(0.992);
        }

        .mobile-display-card-main {
            min-width: 0;
        }

        .mobile-display-card-side {
            display: flex;
            min-width: 5.25rem;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.45rem;
        }

        .mobile-display-card-top {
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .mobile-display-card-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.38rem;
            min-width: 0;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #64748b;
        }

        .mobile-display-card-dot {
            height: 0.44rem;
            width: 0.44rem;
            border-radius: 999px;
            flex: 0 0 auto;
            background: #94a3b8;
        }

        .mobile-display-card-dot.run {
            background: #10b981;
        }

        .mobile-display-card-dot.alert {
            background: #f43f5e;
        }

        .mobile-display-title {
            margin-top: 0.34rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.32;
            letter-spacing: -0.01em;
            color: #0f172a;
        }

        .mobile-display-context,
        .mobile-display-detail {
            margin-top: 0.35rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 12px;
            line-height: 1.45;
            color: #475569;
        }

        .mobile-display-context {
            margin-top: 0.34rem;
            color: #64748b;
        }

        .mobile-display-detail.problem {
            color: #be123c;
            font-weight: 600;
        }

        .mobile-display-note {
            margin-top: 0.26rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
            overflow: hidden;
            font-size: 11px;
            line-height: 1.4;
            color: #64748b;
        }

        .mobile-display-time {
            margin: 0;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            color: #475569;
        }

        .mobile-display-state {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.36rem 0.7rem;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            line-height: 1;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(248, 250, 252, 0.96);
            color: #475569;
        }

        .mobile-display-state.run {
            border-color: rgba(167, 243, 208, 0.95);
            background: rgba(236, 253, 245, 0.98);
            color: #047857;
        }

        .mobile-display-state.alert {
            border-color: rgba(251, 191, 202, 0.95);
            background: rgba(255, 241, 242, 0.98);
            color: #be123c;
        }

        .mobile-display-banner {
            border-radius: 1.05rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.88);
            padding: 0.72rem 0.72rem 0.68rem;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.035);
            backdrop-filter: blur(12px);
        }

        .mobile-display-banner-top {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            justify-content: space-between;
            gap: 0.65rem;
        }

        .mobile-display-banner-kicker {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-display-banner-summary {
            margin-top: 0.22rem;
            font-size: 12.5px;
            font-weight: 600;
            line-height: 1.35;
            letter-spacing: -0.01em;
            color: #0f172a;
        }

        .mobile-display-banner-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 1.95rem;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(248, 250, 252, 0.96);
            padding: 0.32rem 0.6rem;
            font-size: 10.5px;
            font-weight: 700;
            color: #334155;
            text-align: center;
        }

        .mobile-display-scope-row {
            display: flex;
            margin-top: 0.62rem;
            min-width: 0;
        }

        .mobile-display-scope-button {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 0.4rem 0.65rem;
            align-items: center;
            width: 100%;
            padding: 0.62rem 0.72rem;
            border-radius: 1rem;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.025);
            text-align: left;
            transition: transform 160ms ease, border-color 160ms ease, box-shadow 160ms ease;
        }

        .mobile-display-scope-button:active {
            transform: scale(0.992);
        }

        .mobile-display-scope-button:disabled {
            background: rgba(248, 250, 252, 0.96);
            color: #94a3b8;
            box-shadow: none;
        }

        .mobile-display-scope-button-meta {
            min-width: 0;
        }

        .mobile-display-scope-button-label {
            display: block;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-display-scope-button-value {
            display: block;
            margin-top: 0.22rem;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.3;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mobile-display-scope-button-caption {
            display: block;
            margin-top: 0.18rem;
            font-size: 11px;
            line-height: 1.3;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mobile-display-scope-button-icon {
            height: 1rem;
            width: 1rem;
            color: #94a3b8;
        }

        .mobile-display-status-row {
            display: flex;
            gap: 0.45rem;
            overflow-x: auto;
            padding: 0.6rem 0 0.05rem;
            scrollbar-width: none;
        }

        .mobile-display-status-row::-webkit-scrollbar {
            display: none;
        }

        .mobile-display-status-row .mobile-filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.38rem;
            white-space: nowrap;
        }

        .mobile-display-banner-foot {
            display: flex;
            justify-content: flex-end;
            margin-top: 0.52rem;
        }

        .mobile-display-reset {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            min-height: 1.8rem;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(255, 255, 255, 0.9);
            padding: 0.26rem 0.64rem;
            font-size: 10.5px;
            font-weight: 600;
            color: #475569;
        }

        .mobile-display-picker-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.7rem;
            width: 100%;
            min-height: 3.15rem;
            border: 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.75);
            background: transparent;
            padding: 0.72rem 0.1rem;
            text-align: left;
            font: inherit;
            transition: background-color 140ms ease, transform 140ms ease;
        }

        .mobile-display-picker-option:active {
            transform: scale(0.996);
        }

        .mobile-display-picker-option.active {
            background: rgba(240, 249, 255, 0.7);
        }

        .mobile-display-picker-option-main {
            display: block;
            min-width: 0;
            flex: 1 1 auto;
        }

        .mobile-display-picker-option-top {
            display: block;
            min-width: 0;
        }

        .mobile-display-picker-option-title {
            display: block;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.28;
            color: #0f172a;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .mobile-display-picker-option-subtitle {
            display: block;
            margin-top: 0.22rem;
            font-size: 11px;
            line-height: 1.35;
            color: #64748b;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .mobile-display-picker-option-meta {
            display: inline-flex;
            align-items: center;
            gap: 0.38rem;
            min-width: 0;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-display-picker-option-state {
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

        .mobile-display-picker-option.active .mobile-display-picker-option-state {
            border-color: rgba(125, 211, 252, 0.92);
            background: rgba(224, 242, 254, 0.98);
            color: #0284c7;
        }

        .mobile-display-picker-section {
            display: grid;
            gap: 0;
        }

        .mobile-display-picker-section + .mobile-display-picker-section {
            margin-top: 1rem;
        }

        .mobile-display-picker-path {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.42rem;
            margin-top: 0.1rem;
        }

        .mobile-display-picker-path-chip {
            display: inline-flex;
            align-items: center;
            min-height: 1.9rem;
            border-radius: 999px;
            border: 1px solid rgba(191, 219, 254, 0.95);
            background: rgba(239, 246, 255, 0.96);
            padding: 0.28rem 0.72rem;
            font-size: 10px;
            font-weight: 600;
            color: #0369a1;
            white-space: nowrap;
        }

        .mobile-display-picker-section-title {
            margin-bottom: 0.38rem;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-display-picker-footer {
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

        .mobile-display-picker-shell {
            height: clamp(22rem, 68vh, 34rem);
            max-height: calc(100vh - 0.8rem);
            min-height: 22rem;
        }

        @supports (height: 1dvh) {
            .mobile-display-picker-shell {
                height: clamp(22rem, 68dvh, 34rem);
                max-height: calc(100dvh - 0.8rem);
            }
        }

        .mobile-display-picker-sheet {
            display: flex;
            flex-direction: column;
            height: 100%;
            max-height: 100%;
            overflow: hidden;
            padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 0.8rem);
        }

        .mobile-display-picker-body {
            display: flex;
            min-height: 0;
            flex: 1 1 auto;
            flex-direction: column;
            overflow: hidden;
        }

        .mobile-display-picker-scroll {
            min-height: 0;
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            gap: 0.42rem;
            overflow-y: auto;
            overscroll-behavior: contain;
            padding-right: 0.12rem;
            padding-bottom: 0.18rem;
        }

        .mobile-display-picker-footer-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.8rem;
            border-radius: 999px;
            padding: 0.62rem 1rem;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: -0.01em;
            transition: transform 140ms ease, box-shadow 140ms ease, background-color 140ms ease, border-color 140ms ease;
        }

        .mobile-display-picker-footer-button:active {
            transform: translateY(1px) scale(0.995);
        }

        .mobile-display-picker-footer-button.secondary {
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(255, 255, 255, 0.96);
            color: #475569;
        }

        .mobile-display-picker-footer-button.reset {
            flex: 0 0 auto;
            min-width: 5.85rem;
            border: 1px solid rgba(203, 213, 225, 0.95);
            background: rgba(255, 255, 255, 0.98);
            color: #475569;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .mobile-display-picker-footer-button.primary {
            flex: 1 1 auto;
            border: 1px solid rgba(2, 132, 199, 0.38);
            background: linear-gradient(180deg, #38bdf8 0%, #0ea5e9 55%, #0284c7 100%);
            color: #f8fbff;
            text-shadow: 0 1px 0 rgba(3, 105, 161, 0.18);
            box-shadow: 0 10px 18px rgba(14, 165, 233, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.22);
        }

        .mobile-display-picker-footer-button.primary:hover {
            box-shadow: 0 12px 22px rgba(14, 165, 233, 0.24), inset 0 1px 0 rgba(255, 255, 255, 0.24);
        }

        .mobile-display-skeleton-card {
            position: relative;
            overflow: hidden;
            pointer-events: none;
        }

        .mobile-display-skeleton-card::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.74), transparent);
            animation: mobileDisplaySkeletonSweep 1.12s ease-in-out infinite;
        }

        .mobile-display-skeleton-pill,
        .mobile-display-skeleton-line,
        .mobile-display-skeleton-time,
        .mobile-display-skeleton-state {
            background: linear-gradient(90deg, rgba(226, 232, 240, 0.84), rgba(241, 245, 249, 0.96), rgba(226, 232, 240, 0.84));
            background-size: 200% 100%;
            animation: mobileDisplaySkeletonPulse 1.38s ease-in-out infinite;
        }

        .mobile-display-skeleton-pill,
        .mobile-display-skeleton-line,
        .mobile-display-skeleton-time,
        .mobile-display-skeleton-state {
            border-radius: 999px;
        }

        .mobile-display-skeleton-pill {
            height: 0.82rem;
            width: 4.8rem;
        }

        .mobile-display-skeleton-line {
            height: 0.84rem;
        }

        .mobile-display-skeleton-line.title {
            margin-top: 0.44rem;
            width: 9.8rem;
            height: 0.98rem;
        }

        .mobile-display-skeleton-line.context {
            margin-top: 0.34rem;
            width: 11.6rem;
        }

        .mobile-display-skeleton-line.detail {
            margin-top: 0.34rem;
            width: 8.9rem;
        }

        .mobile-display-skeleton-time {
            height: 0.82rem;
            width: 4.8rem;
        }

        .mobile-display-skeleton-state {
            height: 1.82rem;
            width: 4.2rem;
        }

        @keyframes mobileDisplaySkeletonSweep {
            100% {
                transform: translateX(100%);
            }
        }

        @keyframes mobileDisplaySkeletonPulse {
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
        $workgroupId = request('workgroup_id');
        $workgroupName = request('workgroup_name');
        $workstationId = request('workstation_id');
        $workstationName = request('workstation_name');
        $initialKeyword = (string) request('search', '');
        $initialPage = max(1, (int) request('page', 1));
        $initialStatus = request('status', '');
        $requestedSort = (string) request('sort', 'updated_at');
        $requestedOrder = strtolower((string) request('order', 'desc'));
        $allowedSorts = ['updated_at', 'latest_activity', 'display_hours', 'display_name', 'status'];
        $initialSort = in_array($requestedSort, $allowedSorts, true) ? $requestedSort : 'updated_at';
        $initialOrder = $requestedOrder === 'asc' ? 'asc' : 'desc';
        $displayFilters = $displayFilters ?? [
            'canChooseFacility' => false,
            'facilities' => [],
            'workgroupsByFacility' => [],
            'workstationsByWorkgroup' => [],
            'selectedFacilityId' => '',
            'selectedWorkgroupId' => '',
            'selectedWorkstationId' => '',
        ];
    @endphp

    <div class="mobile-display-banner">
        <div class="mobile-display-banner-top">
            <div class="min-w-0">
                <p class="mobile-display-banner-kicker">Filters</p>
                <p class="mobile-display-banner-summary">Search and narrow the display fleet.</p>
            </div>
        </div>

        <div class="mobile-searchbar mt-3">
            <i data-lucide="search" class="mobile-searchbar-icon"></i>
            <input id="mobile-display-search" type="search" class="mobile-input mobile-search-input" placeholder="Search displays">
        </div>

        <div class="mobile-display-scope-row">
            <button id="mobile-display-scope-trigger" type="button" class="mobile-display-scope-button">
                <span class="mobile-display-scope-button-meta">
                    <span class="mobile-display-scope-button-label">Scope filter</span>
                    <span id="mobile-display-scope-value" class="mobile-display-scope-button-value">All facilities</span>
                    <span id="mobile-display-scope-caption" class="mobile-display-scope-button-caption">Browse by facility, workgroup, or workstation.</span>
                </span>
                <i data-lucide="sliders-horizontal" class="mobile-display-scope-button-icon"></i>
            </button>
        </div>

        <div class="mobile-display-status-row">
            <button type="button" data-display-filter="" class="mobile-display-filter mobile-filter-chip active">
                <i data-lucide="layers-3" class="h-3.5 w-3.5"></i>
                <span>All</span>
            </button>
            <button type="button" data-display-filter="2" class="mobile-display-filter mobile-filter-chip">
                <i data-lucide="triangle-alert" class="h-3.5 w-3.5"></i>
                <span>Not OK</span>
            </button>
            <button type="button" data-display-filter="1" class="mobile-display-filter mobile-filter-chip">
                <i data-lucide="badge-check" class="h-3.5 w-3.5"></i>
                <span>OK</span>
            </button>
        </div>

        <div class="mobile-display-banner-foot">
            <button id="mobile-display-reset" type="button" class="mobile-display-reset">
                <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i>
                <span>Reset filters</span>
            </button>
        </div>
    </div>

    <section class="mobile-section-gap">
        <div id="mobile-display-list" class="mobile-display-list">
            <div class="mobile-display-card mobile-display-skeleton-card" aria-hidden="true">
                <div class="mobile-display-card-main">
                    <div class="mobile-display-card-top">
                        <span class="mobile-display-skeleton-pill"></span>
                    </div>
                    <div class="mobile-display-skeleton-line title"></div>
                    <div class="mobile-display-skeleton-line context"></div>
                    <div class="mobile-display-skeleton-line detail"></div>
                </div>
                <div class="mobile-display-card-side">
                    <div class="mobile-display-skeleton-time"></div>
                    <div class="mobile-display-skeleton-state"></div>
                </div>
            </div>
            <div class="mobile-display-card mobile-display-skeleton-card" aria-hidden="true">
                <div class="mobile-display-card-main">
                    <div class="mobile-display-card-top">
                        <span class="mobile-display-skeleton-pill"></span>
                    </div>
                    <div class="mobile-display-skeleton-line title"></div>
                    <div class="mobile-display-skeleton-line context"></div>
                    <div class="mobile-display-skeleton-line detail"></div>
                </div>
                <div class="mobile-display-card-side">
                    <div class="mobile-display-skeleton-time"></div>
                    <div class="mobile-display-skeleton-state"></div>
                </div>
            </div>
        </div>
        <div id="mobile-display-pagination" class="mt-3"></div>
    </section>

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileDisplays', () => {
                const list = document.getElementById('mobile-display-list');
                const pagination = document.getElementById('mobile-display-pagination');
                const searchInput = document.getElementById('mobile-display-search');
                const filterButtons = Array.from(document.querySelectorAll('.mobile-display-filter'));
                const scopeTrigger = document.getElementById('mobile-display-scope-trigger');
                const scopeValue = document.getElementById('mobile-display-scope-value');
                const scopeCaption = document.getElementById('mobile-display-scope-caption');
                const resetButton = document.getElementById('mobile-display-reset');
                const pickerRoot = document.getElementById('mobile-display-picker');
                const pickerBackdrop = pickerRoot.querySelector('.mobile-sheet-backdrop');
                const pickerClose = document.getElementById('mobile-display-picker-close');
                const pickerTitle = document.getElementById('mobile-display-picker-title');
                const pickerSearchInput = document.getElementById('mobile-display-picker-search');
                const pickerOptions = document.getElementById('mobile-display-picker-options');
                const pickerReset = document.getElementById('mobile-display-picker-reset');
                const pickerApply = document.getElementById('mobile-display-picker-apply');
                const bottomNav = document.getElementById('mobile-bottom-nav');
                const htmlTarget = document.documentElement;
                const bodyTarget = document.body;
                let previousOverflow = '';
                let previousBodyOverflow = '';
                let lockedScrollY = 0;
                const filterConfig = @json($displayFilters);
                const facilityId = @json($facilityId);
                const workgroupId = @json($workgroupId);
                const workstationId = @json($workstationId);
                const initialKeyword = @json($initialKeyword);
                const initialPage = @json($initialPage);
                const sortKey = @json($initialSort);
                const sortOrder = @json($initialOrder);
                const perPage = 10;
                const cache = new Map();
                let requestToken = 0;
                let timer = null;
                let currentPage = Number(initialPage || 1);
                let currentKeyword = String(initialKeyword || '');
                const defaultStatus = @json((string) $initialStatus);
                let status = @json((string) $initialStatus);
                let selectedFacilityId = String(filterConfig.selectedFacilityId || facilityId || '');
                let selectedWorkgroupId = String(filterConfig.selectedWorkgroupId || workgroupId || '');
                let selectedWorkstationId = String(filterConfig.selectedWorkstationId || workstationId || '');
                let draftFacilityId = selectedFacilityId;
                let draftWorkgroupId = selectedWorkgroupId;
                let draftWorkstationId = selectedWorkstationId;
                const escapeHtml = window.Perfectlum.escapeHtml;
                const workgroupsAll = Object.entries(filterConfig.workgroupsByFacility || {}).flatMap(([facilityKey, items]) =>
                    (Array.isArray(items) ? items : []).map((item) => ({
                        id: String(item.id),
                        name: item.name,
                        facilityId: String(facilityKey),
                    }))
                );
                const workgroupFacilityMap = new Map(workgroupsAll.map((item) => [String(item.id), String(item.facilityId)]));
                const workstationsAll = Object.entries(filterConfig.workstationsByWorkgroup || {}).flatMap(([workgroupKey, items]) =>
                    (Array.isArray(items) ? items : []).map((item) => ({
                        id: String(item.id),
                        name: item.name,
                        workgroupId: String(workgroupKey),
                        facilityId: workgroupFacilityMap.get(String(workgroupKey)) || '',
                    }))
                );
                const buildDetailHref = (displayId) => {
                    const detailContextQuery = new URLSearchParams();
                    const facilityName = selectedFacilityId ? findOptionLabel(getFacilityOptions(), selectedFacilityId, '') : '';
                    const workgroupName = selectedWorkgroupId ? findOptionLabel(getWorkgroupOptions(), selectedWorkgroupId, '') : '';
                    const workstationName = selectedWorkstationId ? findOptionLabel(getWorkstationOptions(), selectedWorkstationId, '') : '';

                    if (selectedFacilityId) detailContextQuery.set('facility_id', selectedFacilityId);
                    if (selectedWorkgroupId) detailContextQuery.set('workgroup_id', selectedWorkgroupId);
                    if (selectedWorkstationId) detailContextQuery.set('workstation_id', selectedWorkstationId);
                    if (facilityName) detailContextQuery.set('facility_name', facilityName);
                    if (workgroupName) detailContextQuery.set('workgroup_name', workgroupName);
                    if (workstationName) detailContextQuery.set('workstation_name', workstationName);
                    if (status) detailContextQuery.set('status', status);
                    if (sortKey) detailContextQuery.set('sort', sortKey);
                    if (sortOrder) detailContextQuery.set('order', sortOrder);
                    if (currentKeyword) detailContextQuery.set('search', currentKeyword);
                    if (currentPage > 1) detailContextQuery.set('page', String(currentPage));
                    detailContextQuery.set('return_to', `${window.location.pathname}${window.location.search}`);
                    const query = detailContextQuery.toString();
                    return `${@json(url('/m/displays'))}/${displayId}${query ? `?${query}` : ''}`;
                };

                const emptyState = (message) => `<div class="mobile-empty">${escapeHtml(message)}</div>`;
                const uiText = {
                    display: 'Display',
                    browseScope: 'Browse by facility, workgroup, or workstation.',
                    currentHierarchy: 'Current hierarchy filter.',
                    noActiveAlert: 'No active alert',
                    noAlertDetail: 'No alert detail',
                    notOk: 'Not OK',
                    noRecordedActivity: 'No recorded activity',
                    online: 'Online',
                    offline: 'Offline',
                    ok: 'OK',
                    failed: 'Failed',
                    noDisplaysMatched: 'No displays matched this filter.',
                    unableToLoadDisplays: 'Unable to load displays right now.',
                };
                const extractErrorText = (errors) => {
                    if (!Array.isArray(errors) || !errors.length) {
                        return '';
                    }

                    const latest = errors[errors.length - 1];
                    if (typeof latest === 'string') {
                        return latest;
                    }

                    if (latest && typeof latest === 'object') {
                        return latest.message || latest.error || JSON.stringify(latest);
                    }

                    return '';
                };

                const getFacilityOptions = () => Array.isArray(filterConfig.facilities) ? filterConfig.facilities : [];
                const getWorkgroupOptions = () => {
                    if (!selectedFacilityId) {
                        return [];
                    }

                    return Array.isArray(filterConfig.workgroupsByFacility?.[String(selectedFacilityId)])
                        ? filterConfig.workgroupsByFacility[String(selectedFacilityId)]
                        : [];
                };
                const getWorkstationOptions = () => {
                    if (!selectedWorkgroupId) {
                        return [];
                    }

                    return Array.isArray(filterConfig.workstationsByWorkgroup?.[String(selectedWorkgroupId)])
                        ? filterConfig.workstationsByWorkgroup[String(selectedWorkgroupId)]
                        : [];
                };
                const findOptionLabel = (options, value, fallback = '') => {
                    const match = Array.isArray(options)
                        ? options.find((item) => String(item.id) === String(value))
                        : null;

                    return match?.name || fallback;
                };
                const getDraftWorkgroupOptions = () => {
                    if (!draftFacilityId) {
                        return [];
                    }

                    return Array.isArray(filterConfig.workgroupsByFacility?.[String(draftFacilityId)])
                        ? filterConfig.workgroupsByFacility[String(draftFacilityId)]
                        : [];
                };
                const getDraftWorkstationOptions = () => {
                    if (!draftWorkgroupId) {
                        return [];
                    }

                    return Array.isArray(filterConfig.workstationsByWorkgroup?.[String(draftWorkgroupId)])
                        ? filterConfig.workstationsByWorkgroup[String(draftWorkgroupId)]
                        : [];
                };
                const scopePathText = (facilityValueId, workgroupValueId, workstationValueId) => {
                    const facilityName = facilityValueId ? findOptionLabel(getFacilityOptions(), facilityValueId, '') : '';
                    const workgroupName = workgroupValueId ? findOptionLabel(
                        (facilityValueId
                            ? (Array.isArray(filterConfig.workgroupsByFacility?.[String(facilityValueId)]) ? filterConfig.workgroupsByFacility[String(facilityValueId)] : [])
                            : workgroupsAll),
                        workgroupValueId,
                        ''
                    ) : '';
                    const workstationName = workstationValueId ? findOptionLabel(
                        (workgroupValueId
                            ? (Array.isArray(filterConfig.workstationsByWorkgroup?.[String(workgroupValueId)]) ? filterConfig.workstationsByWorkgroup[String(workgroupValueId)] : [])
                            : workstationsAll),
                        workstationValueId,
                        ''
                    ) : '';

                    return [facilityName, workgroupName, workstationName].filter(Boolean);
                };

                const renderScopeFilters = () => {
                    const facilities = getFacilityOptions();
                    if (!filterConfig.canChooseFacility && facilities.length && !selectedFacilityId) {
                        selectedFacilityId = String(facilities[0].id);
                    }

                    const workgroups = getWorkgroupOptions();
                    if (selectedWorkgroupId && !workgroups.some((item) => String(item.id) === String(selectedWorkgroupId))) {
                        selectedWorkgroupId = '';
                    }

                    const workstations = getWorkstationOptions();
                    if (selectedWorkstationId && !workstations.some((item) => String(item.id) === String(selectedWorkstationId))) {
                        selectedWorkstationId = '';
                    }

                    const path = scopePathText(selectedFacilityId, selectedWorkgroupId, selectedWorkstationId);
                    scopeValue.textContent = path.length ? path.join(' / ') : 'All displays';
                    scopeCaption.textContent = path.length
                        ? uiText.currentHierarchy
                        : uiText.browseScope;
                    scopeTrigger.disabled = !filterConfig.canChooseFacility && facilities.length <= 1 && !workgroupsAll.length && !workstationsAll.length;
                };

                const closePicker = () => {
                    if (document.activeElement instanceof HTMLElement && pickerRoot.contains(document.activeElement)) {
                        document.activeElement.blur();
                    }
                    pickerRoot.classList.add('hidden');
                    pickerSearchInput.value = '';
                    pickerOptions.innerHTML = '';
                    if (bottomNav) {
                        bottomNav.classList.remove('invisible', 'pointer-events-none');
                    }
                    htmlTarget.style.overflow = previousOverflow;
                    bodyTarget.style.overflow = previousBodyOverflow;
                    previousOverflow = '';
                    previousBodyOverflow = '';
                    const restoreY = lockedScrollY;
                    lockedScrollY = 0;
                    window.requestAnimationFrame(() => {
                        window.scrollTo(0, restoreY);
                    });
                };

                const renderPickerOptions = () => {
                    const query = pickerSearchInput.value.trim().toLowerCase();
                    const renderOption = ({ id = '', name = '', kind = '', subtitle = '', active = false, data = '', navigates = false }) => `
                        <button type="button" class="mobile-display-picker-option ${active ? 'active' : ''}" ${data}>
                            <span class="mobile-display-picker-option-main">
                                <span class="mobile-display-picker-option-top">
                                    <span class="mobile-display-picker-option-title">${escapeHtml(name)}</span>
                                </span>
                                ${kind || subtitle ? `
                                    <span class="mobile-display-picker-option-subtitle">
                                        ${kind ? `<span class="mobile-display-picker-option-meta">${escapeHtml(kind)}</span>` : ''}
                                        ${kind && subtitle ? '<span aria-hidden="true"> • </span>' : ''}
                                        ${subtitle ? escapeHtml(subtitle) : ''}
                                    </span>
                                ` : ''}
                            </span>
                            <span class="mobile-display-picker-option-state">
                                ${active ? '<i data-lucide="check" class="h-3.5 w-3.5"></i>' : (navigates ? '<i data-lucide="chevron-right" class="h-4 w-4"></i>' : '')}
                            </span>
                        </button>
                    `;

                    const searchMode = query.length > 0;
                    pickerOptions.innerHTML = '';

                    if (searchMode) {
                        pickerTitle.textContent = 'Search scope';
                        const facilityMatches = getFacilityOptions()
                            .filter((item) => (item.name || '').toLowerCase().includes(query))
                            .map((item) => renderOption({
                                id: item.id,
                                name: item.name,
                                kind: 'Facility',
                                active: String(item.id) === String(draftFacilityId) && !draftWorkgroupId && !draftWorkstationId,
                                data: `data-scope-kind="facility" data-scope-id="${escapeHtml(String(item.id))}"`,
                                navigates: true,
                            }));
                        const workgroupMatches = workgroupsAll
                            .filter((item) => (item.name || '').toLowerCase().includes(query))
                            .map((item) => renderOption({
                                id: item.id,
                                name: item.name,
                                kind: 'Workgroup',
                                subtitle: findOptionLabel(getFacilityOptions(), item.facilityId, ''),
                                active: String(item.id) === String(draftWorkgroupId) && !draftWorkstationId,
                                data: `data-scope-kind="workgroup" data-scope-id="${escapeHtml(String(item.id))}" data-scope-parent="${escapeHtml(String(item.facilityId))}"`,
                                navigates: true,
                            }));
                        const workstationMatches = workstationsAll
                            .filter((item) => (item.name || '').toLowerCase().includes(query))
                            .map((item) => renderOption({
                                id: item.id,
                                name: item.name,
                                kind: 'Workstation',
                                subtitle: [findOptionLabel(workgroupsAll, item.workgroupId, ''), findOptionLabel(getFacilityOptions(), item.facilityId, '')].filter(Boolean).join(' • '),
                                active: String(item.id) === String(draftWorkstationId),
                                data: `data-scope-kind="workstation" data-scope-id="${escapeHtml(String(item.id))}" data-scope-parent="${escapeHtml(String(item.workgroupId))}" data-scope-grandparent="${escapeHtml(String(item.facilityId))}"`,
                            }));

                        const sections = [];
                        if (facilityMatches.length) {
                            sections.push(`<div class="mobile-display-picker-section"><p class="mobile-display-picker-section-title">Facilities</p>${facilityMatches.join('')}</div>`);
                        }
                        if (workgroupMatches.length) {
                            sections.push(`<div class="mobile-display-picker-section"><p class="mobile-display-picker-section-title">Workgroups</p>${workgroupMatches.join('')}</div>`);
                        }
                        if (workstationMatches.length) {
                            sections.push(`<div class="mobile-display-picker-section"><p class="mobile-display-picker-section-title">Workstations</p>${workstationMatches.join('')}</div>`);
                        }

                        pickerOptions.innerHTML = sections.length ? sections.join('') : '<div class="mobile-empty">No matching scope found.</div>';
                    } else {
                        const facilityName = draftFacilityId ? findOptionLabel(getFacilityOptions(), draftFacilityId, '') : '';
                        const workgroupName = draftWorkgroupId ? findOptionLabel(getDraftWorkgroupOptions(), draftWorkgroupId, '') : '';
                        const workstationName = draftWorkstationId ? findOptionLabel(getDraftWorkstationOptions(), draftWorkstationId, '') : '';
                        const path = [];
                        if (facilityName) {
                            path.push(`<button type="button" class="mobile-display-picker-path-chip" data-scope-nav="facility">${escapeHtml(facilityName)}</button>`);
                        }
                        if (workgroupName) {
                            path.push(`<button type="button" class="mobile-display-picker-path-chip" data-scope-nav="workgroup">${escapeHtml(workgroupName)}</button>`);
                        }
                        if (workstationName) {
                            path.push(`<span class="mobile-display-picker-path-chip">${escapeHtml(workstationName)}</span>`);
                        }

                        const pathSection = path.length ? `<div class="mobile-display-picker-path">${path.join('')}</div>` : '';

                        if (!draftFacilityId) {
                            pickerTitle.textContent = 'Choose facility';
                            pickerOptions.innerHTML = `
                                <div class="mobile-display-picker-section">
                                    <p class="mobile-display-picker-section-title">Facilities</p>
                                    ${getFacilityOptions().map((item) => renderOption({
                                        id: item.id,
                                        name: item.name,
                                        kind: 'Facility',
                                        active: String(item.id) === String(draftFacilityId),
                                        data: `data-scope-kind="facility" data-scope-id="${escapeHtml(String(item.id))}"`,
                                        navigates: true,
                                    })).join('')}
                                </div>
                            `;
                        } else if (!draftWorkgroupId) {
                            pickerTitle.textContent = 'Choose workgroup';
                            pickerOptions.innerHTML = `
                                ${pathSection}
                                <div class="mobile-display-picker-section">
                                    <p class="mobile-display-picker-section-title">Workgroups</p>
                                    ${getDraftWorkgroupOptions().map((item) => renderOption({
                                        id: item.id,
                                        name: item.name,
                                        kind: 'Workgroup',
                                        subtitle: facilityName,
                                        active: String(item.id) === String(draftWorkgroupId),
                                        data: `data-scope-kind="workgroup" data-scope-id="${escapeHtml(String(item.id))}" data-scope-parent="${escapeHtml(String(draftFacilityId))}"`,
                                        navigates: true,
                                    })).join('') || '<div class="mobile-empty">No workgroups in this facility.</div>'}
                                </div>
                            `;
                        } else {
                            pickerTitle.textContent = 'Choose workstation';
                            pickerOptions.innerHTML = `
                                ${pathSection}
                                <div class="mobile-display-picker-section">
                                    <p class="mobile-display-picker-section-title">Workstations</p>
                                    ${getDraftWorkstationOptions().map((item) => renderOption({
                                        id: item.id,
                                        name: item.name,
                                        kind: 'Workstation',
                                        subtitle: [workgroupName, facilityName].filter(Boolean).join(' • '),
                                        active: String(item.id) === String(draftWorkstationId),
                                        data: `data-scope-kind="workstation" data-scope-id="${escapeHtml(String(item.id))}" data-scope-parent="${escapeHtml(String(draftWorkgroupId))}" data-scope-grandparent="${escapeHtml(String(draftFacilityId))}"`,
                                    })).join('') || '<div class="mobile-empty">No workstations in this workgroup.</div>'}
                                </div>
                            `;
                        }
                    }

                    lucide.createIcons();

                    pickerOptions.querySelectorAll('[data-scope-nav]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const target = button.getAttribute('data-scope-nav');
                            if (target === 'facility') {
                                draftWorkgroupId = '';
                                draftWorkstationId = '';
                            } else if (target === 'workgroup') {
                                draftWorkstationId = '';
                            }
                            renderPickerOptions();
                        });
                    });

                    pickerOptions.querySelectorAll('[data-scope-kind]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const kind = button.getAttribute('data-scope-kind') || '';
                            const nextValue = button.getAttribute('data-scope-id') || '';
                            const parentValue = button.getAttribute('data-scope-parent') || '';
                            const grandparentValue = button.getAttribute('data-scope-grandparent') || '';

                            if (kind === 'all') {
                                draftFacilityId = '';
                                draftWorkgroupId = '';
                                draftWorkstationId = '';
                            } else if (kind === 'facility') {
                                draftFacilityId = nextValue;
                                draftWorkgroupId = '';
                                draftWorkstationId = '';
                            } else if (kind === 'workgroup-clear') {
                                draftWorkgroupId = '';
                                draftWorkstationId = '';
                            } else if (kind === 'workgroup') {
                                draftFacilityId = parentValue || draftFacilityId;
                                draftWorkgroupId = nextValue;
                                draftWorkstationId = '';
                            } else if (kind === 'workstation-clear') {
                                draftWorkstationId = '';
                            } else if (kind === 'workstation') {
                                draftFacilityId = grandparentValue || draftFacilityId;
                                draftWorkgroupId = parentValue || draftWorkgroupId;
                                draftWorkstationId = nextValue;
                            }

                            renderPickerOptions();
                        });
                    });
                };

                const openPicker = () => {
                    if (scopeTrigger.disabled) {
                        return;
                    }

                    draftFacilityId = selectedFacilityId;
                    draftWorkgroupId = selectedWorkgroupId;
                    draftWorkstationId = selectedWorkstationId;
                    pickerRoot.classList.remove('hidden');
                    if (!previousOverflow) {
                        previousOverflow = htmlTarget.style.overflow || '';
                    }
                    if (!previousBodyOverflow) {
                        previousBodyOverflow = bodyTarget.style.overflow || '';
                    }
                    lockedScrollY = window.scrollY || window.pageYOffset || 0;
                    htmlTarget.style.overflow = 'hidden';
                    bodyTarget.style.overflow = 'hidden';
                    if (bottomNav) {
                        bottomNav.classList.add('invisible', 'pointer-events-none');
                    }
                    pickerSearchInput.value = '';
                    renderPickerOptions();
                    pickerSearchInput.focus({ preventScroll: true });
                };
                const loadingState = () => Array.from({ length: 4 }).map(() => `
                    <div class="mobile-display-card mobile-display-skeleton-card" aria-hidden="true">
                        <div class="mobile-display-card-main">
                            <div class="mobile-display-card-top">
                                <span class="mobile-display-skeleton-pill"></span>
                            </div>
                            <div class="mobile-display-skeleton-line title"></div>
                            <div class="mobile-display-skeleton-line context"></div>
                            <div class="mobile-display-skeleton-line detail"></div>
                        </div>
                        <div class="mobile-display-card-side">
                            <div class="mobile-display-skeleton-time"></div>
                            <div class="mobile-display-skeleton-state"></div>
                        </div>
                    </div>
                `).join('');

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

                const displayCard = ({
                    tone = 'run',
                    label = 'Display',
                    title = '',
                    context = '',
                    detail = '',
                    note = '',
                    time = '',
                    statusLabel = '',
                    href = null,
                }) => {
                    const Tag = href ? 'a' : 'div';
                    const hrefAttr = href ? ` href="${href}"` : '';
                    const roleAttr = href ? '' : ' role="group"';

                    return `
                        <${Tag}${hrefAttr}${roleAttr} class="mobile-display-card ${href ? 'is-link' : ''}">
                            <div class="mobile-display-card-main">
                                <div class="mobile-display-card-top">
                                    <span class="mobile-display-card-kicker"><span class="mobile-display-card-dot ${tone}"></span>${escapeHtml(label)}</span>
                                </div>
                                <p class="mobile-display-title">${escapeHtml(title)}</p>
                                ${context ? `<p class="mobile-display-context">${escapeHtml(context)}</p>` : ''}
                                ${detail ? `<p class="mobile-display-detail ${tone === 'alert' ? 'problem' : ''}">${escapeHtml(detail)}</p>` : ''}
                                ${note ? `<p class="mobile-display-note">${escapeHtml(note)}</p>` : ''}
                            </div>
                            <div class="mobile-display-card-side">
                                ${time ? `<p class="mobile-display-time">${escapeHtml(time)}</p>` : ''}
                                ${statusLabel ? `<span class="mobile-display-state ${tone}">${escapeHtml(statusLabel)}</span>` : ''}
                            </div>
                        </${Tag}>
                    `;
                };

                function syncRouteState(keyword = currentKeyword, page = currentPage) {
                    const facilityName = selectedFacilityId ? findOptionLabel(getFacilityOptions(), selectedFacilityId, '') : '';
                    const workgroupName = selectedWorkgroupId ? findOptionLabel(getWorkgroupOptions(), selectedWorkgroupId, '') : '';
                    const workstationName = selectedWorkstationId ? findOptionLabel(getWorkstationOptions(), selectedWorkstationId, '') : '';
                    const url = new URL(window.location.href);

                    if (selectedFacilityId) {
                        url.searchParams.set('facility_id', selectedFacilityId);
                        if (facilityName) {
                            url.searchParams.set('facility_name', facilityName);
                        } else {
                            url.searchParams.delete('facility_name');
                        }
                    } else {
                        url.searchParams.delete('facility_id');
                        url.searchParams.delete('facility_name');
                    }

                    if (selectedWorkgroupId) {
                        url.searchParams.set('workgroup_id', selectedWorkgroupId);
                        if (workgroupName) {
                            url.searchParams.set('workgroup_name', workgroupName);
                        } else {
                            url.searchParams.delete('workgroup_name');
                        }
                    } else {
                        url.searchParams.delete('workgroup_id');
                        url.searchParams.delete('workgroup_name');
                    }

                    if (selectedWorkstationId) {
                        url.searchParams.set('workstation_id', selectedWorkstationId);
                        if (workstationName) {
                            url.searchParams.set('workstation_name', workstationName);
                        } else {
                            url.searchParams.delete('workstation_name');
                        }
                    } else {
                        url.searchParams.delete('workstation_id');
                        url.searchParams.delete('workstation_name');
                    }

                    if (status) {
                        url.searchParams.set('status', status);
                    } else {
                        url.searchParams.delete('status');
                    }

                    if (sortKey) {
                        url.searchParams.set('sort', sortKey);
                    }

                    if (sortOrder) {
                        url.searchParams.set('order', sortOrder);
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
                }

                function syncFilters() {
                    filterButtons.forEach((button) => {
                        const active = button.dataset.displayFilter === status;
                        button.className = `mobile-display-filter mobile-filter-chip ${active ? 'active' : ''}`;
                    });
                }

                async function loadDisplays(keyword = '', page = 1) {
                    currentKeyword = keyword;
                    currentPage = page;
                    const cacheKey = `${selectedFacilityId}::${selectedWorkgroupId}::${selectedWorkstationId}::${status}::${sortKey}::${sortOrder}::${keyword}::${page}`;
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
                        sort: sortKey,
                        order: sortOrder,
                    });
                    if (keyword) params.set('search', keyword);
                    if (status) params.set('status', status);
                    if (selectedFacilityId) params.set('facility_id', selectedFacilityId);
                    if (selectedWorkgroupId) params.set('workgroup_id', selectedWorkgroupId);
                    if (selectedWorkstationId) params.set('workstation_id', selectedWorkstationId);

                    try {
                        const response = await window.Perfectlum.request(`/api/displays?${params.toString()}`);
                        if (currentRequest !== requestToken) {
                            return;
                        }
                        const rows = response.data || [];
                        const total = Number(response.total || 0);

                        if (!rows.length) {
                            list.innerHTML = emptyState(uiText.noDisplaysMatched);
                            return;
                        }

                        const html = rows.map((item) => {
                            const tone = item.status === 1 ? 'run' : 'alert';
                            const context = [item.wsName, item.wgName, item.facName].filter(Boolean).join(' • ');
                            const attentionDetail = extractErrorText(item.errors)
                                || String(item.latestFailedCheckText || '').trim()
                                || String(item.latestFailedHistoryName || '').trim()
                                || uiText.noAlertDetail;
                            const detail = item.status === 1 ? uiText.noActiveAlert : attentionDetail;
                            const note = item.connected ? uiText.online : uiText.offline;

                            return displayCard({
                                tone,
                                label: uiText.display,
                                title: item.displayName,
                                context,
                                detail,
                                note,
                                time: item.latestActivityMode === 'none' ? uiText.noRecordedActivity : item.updatedAt,
                                statusLabel: item.status === 1 ? uiText.ok : uiText.failed,
                                href: buildDetailHref(item.id),
                            });
                        }).join('');
                        const pager = renderPager(total, page, perPage);
                        cache.set(cacheKey, { html, pager });
                        list.innerHTML = html;
                        pagination.innerHTML = pager;
                        lucide.createIcons();
                    } catch (error) {
                        if (currentRequest !== requestToken) {
                            return;
                        }
                        list.innerHTML = emptyState(uiText.unableToLoadDisplays);
                    }
                }

                pagination.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-page]');
                    if (!button || button.hasAttribute('disabled')) {
                        return;
                    }

                    loadDisplays(currentKeyword, Number(button.dataset.page));
                });

                filterButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        status = button.dataset.displayFilter;
                        syncFilters();
                        loadDisplays(searchInput.value.trim(), 1);
                    });
                });

                scopeTrigger.addEventListener('click', openPicker);

                pickerBackdrop.addEventListener('click', closePicker);
                pickerClose.addEventListener('click', closePicker);
                pickerSearchInput.addEventListener('input', renderPickerOptions);
                const resetAllFilters = () => {
                    const facilities = getFacilityOptions();
                    selectedFacilityId = !filterConfig.canChooseFacility && facilities.length ? String(facilities[0].id) : '';
                    selectedWorkgroupId = '';
                    selectedWorkstationId = '';
                    draftFacilityId = selectedFacilityId;
                    draftWorkgroupId = '';
                    draftWorkstationId = '';
                    status = defaultStatus;
                    searchInput.value = '';
                    currentKeyword = '';
                    pickerSearchInput.value = '';
                    closePicker();
                    renderScopeFilters();
                    syncFilters();
                    loadDisplays('', 1);
                };

                pickerReset.addEventListener('click', resetAllFilters);
                pickerApply.addEventListener('click', () => {
                    selectedFacilityId = draftFacilityId;
                    selectedWorkgroupId = draftWorkgroupId;
                    selectedWorkstationId = draftWorkstationId;
                    closePicker();
                    renderScopeFilters();
                    loadDisplays(searchInput.value.trim(), 1);
                });

                resetButton.addEventListener('click', resetAllFilters);

                searchInput.addEventListener('input', () => {
                    window.clearTimeout(timer);
                    timer = window.setTimeout(() => loadDisplays(searchInput.value.trim(), 1), 220);
                });

                searchInput.value = currentKeyword;
                renderScopeFilters();
                syncFilters();
                loadDisplays(currentKeyword, currentPage);
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
        <div id="mobile-display-picker" class="hidden">
            <div class="mobile-sheet-backdrop fixed inset-0 z-[120]"></div>
            <div class="mobile-display-picker-shell fixed bottom-0 left-1/2 z-[130] w-full max-w-[440px] -translate-x-1/2">
                <div class="mobile-sheet mobile-display-picker-sheet compact px-4 pt-3">
                    <div class="mobile-sheet-handle"></div>
                    <div class="mt-3 flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Select scope</p>
                            <p id="mobile-display-picker-title" class="mt-1 text-[15px] font-semibold text-slate-950">Choose filter</p>
                        </div>
                        <button id="mobile-display-picker-close" type="button" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </div>

                    <div class="mobile-display-picker-body">
                        <div class="mobile-searchbar mt-4 shrink-0">
                            <i data-lucide="search" class="mobile-searchbar-icon"></i>
                            <input id="mobile-display-picker-search" type="search" class="mobile-input mobile-search-input" placeholder="Search facility, workgroup, workstation">
                        </div>

                        <div id="mobile-display-picker-options" class="mobile-display-picker-scroll mt-4 pb-2"></div>
                    </div>

                    <div class="mobile-display-picker-footer">
                        <button id="mobile-display-picker-reset" type="button" class="mobile-display-picker-footer-button reset">Reset all</button>
                        <button id="mobile-display-picker-apply" type="button" class="mobile-display-picker-footer-button primary">Apply scope</button>
                    </div>
                </div>
            </div>
        </div>
    @endpush
@endsection
