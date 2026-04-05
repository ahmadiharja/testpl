@extends('mobile.layouts.app')

@push('head')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .mobile-detail-screen {
            display: grid;
            gap: 1rem;
            padding-bottom: 0.15rem;
        }

        .mobile-detail-screen .mobile-panel {
            border-radius: 1.28rem;
            border-color: rgba(148, 163, 184, 0.14);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.985), rgba(248, 250, 252, 0.96));
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.72),
                0 16px 34px rgba(15, 23, 42, 0.055);
        }

        .mobile-detail-screen .mobile-panel.compact {
            padding: 0.92rem 0.96rem;
            border-radius: 1.08rem;
        }

        .mobile-detail-hero-shell {
            display: grid;
            gap: 0.72rem;
        }

        .mobile-detail-screen .mobile-panel.mobile-detail-hero {
            position: relative;
            overflow: hidden;
            border-color: rgba(15, 23, 42, 0.18) !important;
            background:
                radial-gradient(circle at top left, rgba(125, 211, 252, 0.24), transparent 28%),
                radial-gradient(circle at 86% 18%, rgba(59, 130, 246, 0.18), transparent 18%),
                radial-gradient(circle at bottom right, rgba(45, 212, 191, 0.22), transparent 34%),
                linear-gradient(145deg, #081a33 0%, #0f172a 42%, #0f766e 100%);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.08),
                0 22px 40px rgba(8, 26, 51, 0.2) !important;
        }

        .mobile-detail-screen .mobile-panel.mobile-detail-hero::before {
            content: '';
            position: absolute;
            top: -1.4rem;
            right: -1.2rem;
            height: 5.8rem;
            width: 5.8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            filter: blur(10px);
            pointer-events: none;
        }

        .mobile-detail-screen .mobile-panel.mobile-detail-hero::after {
            content: '';
            position: absolute;
            inset: auto 1rem 1rem 1rem;
            height: 1px;
            background: rgba(255, 255, 255, 0.14);
            pointer-events: none;
        }

        .mobile-detail-kicker {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(226, 232, 240, 0.68);
        }

        .mobile-detail-hero-title {
            margin-top: 0.5rem;
            max-width: 12.8rem;
            font-size: 1.24rem;
            font-weight: 700;
            line-height: 1.08;
            letter-spacing: -0.035em;
            color: #f8fafc;
        }

        .mobile-detail-hero-meta {
            margin-top: 0.38rem;
            font-size: 11.5px;
            line-height: 1.55;
            color: rgba(226, 232, 240, 0.78);
        }

        .mobile-detail-hero-summary {
            margin-top: 0.92rem;
            max-width: 14.8rem;
            font-size: 12px;
            line-height: 1.55;
            color: rgba(226, 232, 240, 0.82);
        }

        .mobile-detail-hero-stats {
            position: relative;
            z-index: 1;
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.72rem;
        }

        .mobile-detail-hero-stat {
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            padding-top: 0.56rem;
        }

        .mobile-detail-hero-stat-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(226, 232, 240, 0.68);
        }

        .mobile-detail-hero-stat-value {
            margin-top: 0.2rem;
            font-size: 13.5px;
            font-weight: 700;
            line-height: 1.16;
            letter-spacing: -0.02em;
            color: #fff;
        }

        .mobile-detail-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.48rem;
        }

        .mobile-detail-chip {
            display: inline-flex;
            align-items: center;
            min-height: 1.78rem;
            border-radius: 999px;
            border: 1px solid rgba(191, 219, 254, 0.9);
            background: rgba(255, 255, 255, 0.95);
            padding: 0.24rem 0.68rem;
            font-size: 10.5px;
            font-weight: 700;
            color: #0c4a6e;
        }

        .mobile-detail-chip.subtle {
            border-color: rgba(226, 232, 240, 0.92);
            color: #475569;
        }

        .mobile-detail-tool-row {
            display: flex;
            flex-wrap: nowrap;
            gap: 0.54rem;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 0.12rem;
            scroll-snap-type: x proximity;
            scroll-behavior: auto;
            overscroll-behavior-x: contain;
            -webkit-overflow-scrolling: touch;
            touch-action: pan-x pinch-zoom;
            position: relative;
            z-index: 2;
            scrollbar-width: none;
        }

        .mobile-detail-tool-row::-webkit-scrollbar {
            display: none;
        }

        .mobile-detail-tool {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            gap: 0.42rem;
            min-height: 2.7rem;
            min-width: 6.4rem;
            border-radius: 1.05rem;
            border: 1px solid rgba(186, 230, 253, 0.9);
            background: linear-gradient(180deg, rgba(241, 249, 255, 0.98), rgba(232, 245, 255, 0.96));
            padding: 0.64rem 0.92rem;
            font-size: 11px;
            font-weight: 700;
            color: #0c4a6e;
            box-shadow: 0 12px 24px rgba(14, 165, 233, 0.08);
            scroll-snap-align: start;
        }

        .mobile-detail-tool.active {
            border-color: rgba(14, 165, 233, 0.34);
            background: linear-gradient(180deg, rgba(224, 242, 254, 1), rgba(186, 230, 253, 0.88));
            color: #075985;
        }

        .mobile-detail-tool-row.is-dragging,
        .mobile-detail-tool-row.is-pointer-down {
            scroll-snap-type: none;
            cursor: grabbing;
        }

        .mobile-detail-tool-row.is-dragging .mobile-detail-tool,
        .mobile-detail-tool-row.is-pointer-down .mobile-detail-tool {
            scroll-snap-align: none;
        }

        .mobile-detail-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-detail-section-title {
            margin-top: 0.3rem;
            font-size: 15px;
            font-weight: 650;
            line-height: 1.32;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .mobile-detail-section-copy {
            margin-top: 0.22rem;
            font-size: 12px;
            line-height: 1.5;
            color: #64748b;
        }

        .mobile-detail-tabs {
            display: flex;
            gap: 0.45rem;
            overflow-x: auto;
            padding: 0.26rem;
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.98), rgba(241, 245, 249, 0.9));
            border: 1px solid rgba(148, 163, 184, 0.12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.64);
        }

        .mobile-detail-tab {
            white-space: nowrap;
            border-radius: 999px;
            padding: 0.52rem 0.88rem;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #64748b;
            transition: all 160ms ease;
        }

        .mobile-detail-tab.active {
            background: linear-gradient(180deg, #0f172a, #1e293b);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.18);
        }

        .mobile-detail-grid {
            display: grid;
            gap: 0.75rem;
        }

        .mobile-detail-grid.two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mobile-detail-field {
            border-radius: 1rem;
            border: 1px solid rgba(226, 232, 240, 0.94);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.92));
            padding: 0.82rem 0.88rem;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.66);
        }

        .mobile-detail-field dt {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-detail-field dd {
            margin-top: 0.36rem;
            font-size: 12px;
            line-height: 1.45;
            color: #0f172a;
            word-break: break-word;
        }

        .mobile-detail-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(255, 255, 255, 0.96);
            padding: 0.56rem 0.92rem;
            font-size: 10px;
            font-weight: 700;
            color: #334155;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
        }

        .mobile-detail-link.primary {
            border-color: rgba(59, 130, 246, 0.24);
            background: linear-gradient(180deg, rgba(239, 246, 255, 1), rgba(219, 234, 254, 0.92));
            color: #0f4c81;
        }

        .mobile-history-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.28rem 0.62rem;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            border: 1px solid transparent;
        }

        .mobile-history-badge.success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.18);
            color: #047857;
        }

        .mobile-history-badge.danger {
            background: rgba(244, 63, 94, 0.1);
            border-color: rgba(244, 63, 94, 0.16);
            color: #be123c;
        }

        .mobile-history-badge.warning {
            background: rgba(245, 158, 11, 0.12);
            border-color: rgba(245, 158, 11, 0.18);
            color: #b45309;
        }

        .mobile-history-badge.neutral {
            background: rgba(148, 163, 184, 0.1);
            border-color: rgba(148, 163, 184, 0.18);
            color: #475569;
        }

        .mobile-period-button {
            border-radius: 999px;
            padding: 0.42rem 0.76rem;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            background: rgba(248, 250, 252, 0.96);
            border: 1px solid rgba(148, 163, 184, 0.12);
            transition: all 160ms ease;
        }

        .mobile-period-button.active {
            color: #ffffff;
            background: #0ea5e9;
            border-color: #0ea5e9;
        }

        .mobile-trend-bucket {
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.13);
            background: rgba(248, 250, 252, 0.88);
            padding: 0.78rem 0.84rem;
            transition: all 160ms ease;
        }

        .mobile-trend-bucket.active {
            border-color: rgba(14, 165, 233, 0.28);
            background: rgba(14, 165, 233, 0.08);
        }

        .mobile-progress-bar {
            display: flex;
            height: 0.58rem;
            width: 100%;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(226, 232, 240, 0.85);
        }

        .mobile-progress-bar > span {
            height: 100%;
        }

        .mobile-select,
        .mobile-input-field {
            width: 100%;
            height: 2.72rem;
            border-radius: 0.95rem;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(255, 255, 255, 0.98);
            padding: 0 0.82rem;
            font-size: 12px;
            color: #0f172a;
            outline: none;
            transition: border 160ms ease, box-shadow 160ms ease;
        }

        .mobile-select:focus,
        .mobile-input-field:focus {
            border-color: rgba(56, 189, 248, 0.7);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.14);
        }

        .mobile-toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border-radius: 0.95rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(248, 250, 252, 0.94);
            padding: 0.76rem 0.84rem;
        }

        .mobile-toggle-row input[type="checkbox"] {
            height: 1rem;
            width: 1rem;
            border-radius: 0.35rem;
        }

        .mobile-trend-chart {
            width: 100%;
            overflow: hidden;
            border-radius: 1.08rem;
            border: 1px solid rgba(226, 232, 240, 0.92);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.9));
            padding: 0.92rem;
        }

        .mobile-map-backdrop {
            background: rgba(15, 23, 42, 0.34);
            backdrop-filter: blur(10px);
        }

        .mobile-map-panel {
            background:
                radial-gradient(circle at top, rgba(14, 165, 233, 0.09), transparent 32%),
                linear-gradient(180deg, rgba(248, 250, 252, 0.99), rgba(255, 255, 255, 0.98));
        }

        .mobile-map-stage-shell {
            position: relative;
            overflow: hidden;
            border-radius: 1.5rem;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background:
                linear-gradient(180deg, rgba(248, 250, 252, 0.94), rgba(255, 255, 255, 0.98));
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.6),
                0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .mobile-map-stage-shell::after {
            content: '';
            position: absolute;
            inset: auto 0 0 0;
            height: 5rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.86));
            pointer-events: none;
        }

        .mobile-map-graph {
            height: 100%;
            width: 100%;
            min-height: 0;
        }

        .mobile-map-dock {
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        }

        .mobile-map-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(255, 255, 255, 0.92);
            padding: 0.48rem 0.76rem;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
        }

        .mobile-map-control {
            display: inline-flex;
            height: 2.5rem;
            min-width: 2.5rem;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(248, 250, 252, 0.98);
            color: #0f172a;
            font-size: 11px;
            font-weight: 700;
        }

        .mobile-detail-status-row {
            display: flex;
            align-items: center;
            gap: 0.52rem;
        }

        .mobile-detail-status-copy {
            margin-top: 0.72rem;
            font-size: 12px;
            line-height: 1.5;
            color: #64748b;
        }

        .mobile-detail-status-meta {
            display: grid;
            gap: 0.42rem;
            margin-top: 0.85rem;
            font-size: 11px;
            color: #475569;
        }

        .mobile-detail-status-error {
            margin-top: 0.82rem;
            border-radius: 0.95rem;
            padding: 0.72rem 0.82rem;
            font-size: 11px;
            line-height: 1.5;
        }

        .mobile-detail-form-stack {
            display: grid;
            gap: 0.72rem;
        }

        .mobile-detail-form-grid-two {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.72rem;
        }

        .mobile-detail-form-label {
            display: grid;
            gap: 0.34rem;
        }

        .mobile-detail-form-label-text {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-detail-settings-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .mobile-detail-settings-copy {
            margin-top: 0.2rem;
            font-size: 12px;
            line-height: 1.45;
            color: #64748b;
        }

        .mobile-detail-surface-head {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.7rem;
        }

        .mobile-detail-top-grid {
            display: grid;
            gap: 0.9rem;
        }

        .mobile-detail-top-grid .mobile-panel {
            padding: 1rem;
        }

        .mobile-detail-scope-grid {
            display: grid;
            gap: 0.72rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mobile-detail-scope-card {
            border-radius: 1.06rem;
            border: 1px solid rgba(226, 232, 240, 0.9);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.92));
            padding: 0.92rem 0.94rem;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.03);
        }

        .mobile-detail-scope-card.tone-facility {
            border-color: rgba(191, 219, 254, 0.92);
            background: linear-gradient(180deg, rgba(239, 246, 255, 0.98), rgba(255, 255, 255, 0.94));
        }

        .mobile-detail-scope-card.tone-workgroup {
            border-color: rgba(216, 180, 254, 0.78);
            background: linear-gradient(180deg, rgba(250, 245, 255, 0.98), rgba(255, 255, 255, 0.94));
        }

        .mobile-detail-scope-card.tone-workstation {
            border-color: rgba(167, 243, 208, 0.88);
            background: linear-gradient(180deg, rgba(240, 253, 250, 0.98), rgba(255, 255, 255, 0.94));
        }

        .mobile-detail-scope-card.tone-display {
            border-color: rgba(254, 215, 170, 0.9);
            background: linear-gradient(180deg, rgba(255, 247, 237, 0.98), rgba(255, 255, 255, 0.94));
        }

        .mobile-detail-scope-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-detail-scope-value {
            margin-top: 0.34rem;
            font-size: 12.6px;
            font-weight: 650;
            line-height: 1.4;
            color: #0f172a;
            word-break: break-word;
        }

        .mobile-detail-scope-note {
            margin-top: 0.26rem;
            font-size: 10.8px;
            line-height: 1.45;
            color: #64748b;
        }

        .mobile-metric {
            border-radius: 1.06rem;
            border: 1px solid rgba(226, 232, 240, 0.9);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.92));
            padding: 0.84rem 0.9rem;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.035);
        }

        .mobile-stat-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-stat-value {
            margin-top: 0.26rem;
            font-size: 1.18rem;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -0.03em;
        }

        .mobile-stat-note {
            margin-top: 0.26rem;
            font-size: 11.3px;
            line-height: 1.42;
            color: #64748b;
        }

        .mobile-detail-loading-card {
            position: relative;
            overflow: hidden;
        }

        .mobile-detail-loading-card::after {
            content: '';
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.72), transparent);
            animation: mobileDisplayDetailLoadingSweep 1.12s ease-in-out infinite;
        }

        .mobile-detail-loading-line,
        .mobile-detail-loading-chip,
        .mobile-detail-loading-grid-cell {
            background: linear-gradient(90deg, rgba(226, 232, 240, 0.84), rgba(241, 245, 249, 0.96), rgba(226, 232, 240, 0.84));
            background-size: 200% 100%;
            animation: mobileDisplayDetailLoadingPulse 1.35s ease-in-out infinite;
        }

        .mobile-detail-loading-line,
        .mobile-detail-loading-chip,
        .mobile-detail-loading-grid-cell {
            border-radius: 999px;
        }

        .mobile-detail-loading-chip {
            height: 1.5rem;
            width: 4.9rem;
        }

        .mobile-detail-loading-line {
            height: 0.84rem;
        }

        .mobile-detail-loading-line.title {
            margin-top: 0.62rem;
            width: 10rem;
            height: 1.12rem;
        }

        .mobile-detail-loading-line.body {
            margin-top: 0.44rem;
            width: 13rem;
        }

        .mobile-detail-loading-line.body.short {
            width: 9.8rem;
        }

        .mobile-detail-loading-grid {
            margin-top: 0.96rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.7rem;
        }

        .mobile-detail-loading-grid-cell {
            height: 2.5rem;
            border-radius: 0.88rem;
        }

        @keyframes mobileDisplayDetailLoadingSweep {
            100% {
                transform: translateX(100%);
            }
        }

        @keyframes mobileDisplayDetailLoadingPulse {
            0%,
            100% {
                background-position: 100% 50%;
            }

            50% {
                background-position: 0% 50%;
            }
        }
    </style>
    <script>
        (() => {
            const register = () => window.Perfectlum.registerAlpineData('mobileDisplayDetail', (displayId) => ({
                displayId,
                detail: null,
                loading: true,
                historyLoading: false,
                error: '',
                activeTab: 'overview',
                displayPeriod: 'all',
                isEditingDisplaySettings: false,
                savingDisplaySettings: false,
                settingsError: '',
                selectedTimelineBucketKey: null,
                movePanelOpen: false,
                moveLoading: false,
                moveSaving: false,
                moveError: '',
                quickCalibrating: false,
                structureMapOpen: false,
                displayDetailBase: @json(url('/m/displays')),
                displayDetailQueryString: @json(request()->getQueryString() ?? ''),
                structureMapZoom: 1,
                structureGraphInstance: null,
                moveOptions: {
                    facilities: [],
                    workgroups: [],
                    workstations: [],
                },
                moveForm: {
                    facilityId: '',
                    workgroupId: '',
                    workstationId: '',
                },
                displayForm: {
                    exclude: false,
                    graphicboardOnly: false,
                    internalSensor: false,
                    currentLut: '',
                    installationDate: '',
                    manufacturer: '',
                    model: '',
                    serial: '',
                    inventoryNumber: '',
                    typeOfDisplay: '',
                    displayTechnology: '',
                    screenSize: '',
                    resolutionHorizontal: '',
                    resolutionVertical: '',
                },
                financialForm: {
                    purchaseDate: '',
                    initialValue: '',
                    expectedValue: '',
                    annualStraightLine: '',
                    monthlyStraightLine: '',
                    currentValue: '',
                    expectedReplacementDate: '',
                },

                init() {
                    this.loadDetail(this.displayId, { syncForms: true, preserveDetail: false });
                },

                switchTab(tab) {
                    this.activeTab = tab;

                    this.$nextTick(() => {
                        const target = {
                            overview: this.$refs.overviewSection,
                            history: this.$refs.historySection,
                            settings: this.$refs.settingsSection,
                        }[tab];

                        if (!target) {
                            return;
                        }

                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start',
                        });
                    });
                },

                textOrDash(value) {
                    if (value === null || value === undefined || value === '') {
                        return '-';
                    }
                    return value;
                },

                yesNo(value) {
                    return value ? 'Yes' : 'No';
                },

                enabledDisabled(value) {
                    return value ? 'Enabled' : 'Disabled';
                },

                historyTimeline() {
                    return this.detail?.history?.timeline || [];
                },

                historyMetrics() {
                    return this.detail?.history?.metrics || [];
                },

                recentHistory() {
                    return this.detail?.history?.recent || [];
                },

                selectedTimelineBucket() {
                    return this.historyTimeline().find((bucket) => bucket.key === this.selectedTimelineBucketKey) || null;
                },

                filteredRecentHistory() {
                    const selected = this.selectedTimelineBucket();
                    if (!selected) {
                        return this.recentHistory();
                    }

                    return this.recentHistory().filter((item) => item.bucketKey === selected.key);
                },

                toggleTimelineBucket(bucket) {
                    this.selectedTimelineBucketKey = this.selectedTimelineBucketKey === bucket.key ? null : bucket.key;
                },

                historyBadgeClass(tone) {
                    return {
                        success: 'mobile-history-badge success',
                        danger: 'mobile-history-badge danger',
                        warning: 'mobile-history-badge warning',
                        neutral: 'mobile-history-badge neutral',
                    }[tone] || 'mobile-history-badge neutral';
                },

                statusSurfaceClass() {
                    return this.detail?.statusTone === 'success'
                        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                        : 'border-rose-200 bg-rose-50 text-rose-700';
                },

                latestErrorClass() {
                    return this.detail?.statusTone === 'success'
                        ? 'border-slate-200 bg-white text-slate-600'
                        : 'border-rose-100 bg-white text-slate-700';
                },

                liveErrors() {
                    return Array.isArray(this.detail?.liveErrors) ? this.detail.liveErrors : [];
                },

                overviewStats() {
                    const history = this.detail?.history || {};
                    return [
                        {
                            label: 'Pass Rate',
                            note: 'Current success ratio in selected period',
                            value: `${history.passRate ?? 0}%`,
                            valueClass: 'text-slate-950',
                        },
                        {
                            label: 'Total Histories',
                            note: 'Recorded runs included in this view',
                            value: this.textOrDash(history.total),
                            valueClass: 'text-slate-950',
                        },
                        {
                            label: 'Passed',
                            note: 'Runs completed successfully',
                            value: this.textOrDash(history.passed),
                            valueClass: 'text-emerald-700',
                        },
                        {
                            label: 'Failed Runs',
                            note: 'Recorded failed histories in this view',
                            value: this.textOrDash(history.failed),
                            valueClass: 'text-rose-700',
                        },
                    ];
                },

                hierarchyRows() {
                    return [
                        { label: 'Facility', value: this.detail?.hierarchy?.facility?.name, note: 'Top operational scope', tone: 'facility' },
                        { label: 'Workgroup', value: this.detail?.hierarchy?.workgroup?.name, note: 'Grouping inside the facility', tone: 'workgroup' },
                        { label: 'Workstation', value: this.detail?.hierarchy?.workstation?.name, note: 'Parent client endpoint', tone: 'workstation' },
                        { label: 'Display', value: this.detail?.name, note: 'Current selected display', tone: 'display' },
                    ];
                },

                deviceDetailRows() {
                    return [
                        { label: 'Model', value: this.detail?.model },
                        { label: 'Serial Number', value: this.detail?.serial },
                        { label: 'Resolution', value: this.detail?.resolution },
                    ];
                },

                technicalSummaryRows() {
                    return [
                        { label: 'Manufacturer', value: this.detail?.manufacturer },
                        { label: 'Model', value: this.detail?.model },
                        { label: 'Serial', value: this.detail?.serial },
                        { label: 'Inventory', value: this.detail?.inventoryNumber },
                        { label: 'Type', value: this.detail?.typeOfDisplay },
                        { label: 'Technology', value: this.detail?.displayTechnology },
                        { label: 'Screen Size', value: this.detail?.screenSize },
                        { label: 'Installed', value: this.detail?.installationDate },
                    ];
                },

                readOnlyCalibrationFields() {
                    return [
                        { label: 'Exclude', value: this.yesNo(this.detail?.exclude) },
                        { label: 'Graphicboard LUTs', value: this.enabledDisabled(this.detail?.graphicboardOnly) },
                        { label: 'Internal Sensor', value: this.enabledDisabled(this.detail?.internalSensor) },
                        { label: 'Current LUT', value: this.detail?.currentLut },
                        { label: 'Installation Date', value: this.detail?.installationDate },
                    ];
                },

                readOnlyTechnicalFields() {
                    return [
                        { label: 'Manufacturer', value: this.detail?.manufacturer },
                        { label: 'Model', value: this.detail?.model },
                        { label: 'Serial', value: this.detail?.serial },
                        { label: 'Inventory', value: this.detail?.inventoryNumber },
                        { label: 'Display Type', value: this.detail?.typeOfDisplay },
                        { label: 'Technology', value: this.detail?.displayTechnology },
                        { label: 'Screen Size', value: this.detail?.screenSize },
                        { label: 'Resolution', value: this.detail?.resolution },
                    ];
                },

                readOnlyFinancialFields() {
                    return [
                        { label: 'Purchase Date', value: this.detail?.purchaseDate },
                        { label: 'Expected Replacement', value: this.detail?.expectedReplacementDate },
                        { label: 'Initial Value', value: this.detail?.initialValue },
                        { label: 'Expected Value', value: this.detail?.expectedValue },
                        { label: 'Annual Straight Line', value: this.detail?.annualStraightLine },
                        { label: 'Monthly Straight Line', value: this.detail?.monthlyStraightLine },
                        { label: 'Current Value', value: this.detail?.currentValue },
                    ];
                },

                runningHoursRows() {
                    return [
                        { label: 'Latest reported hours', value: this.detail?.runningHours?.latestReported },
                        { label: 'Highest reported hours', value: this.detail?.runningHours?.peakReported },
                        { label: 'Log entries', value: this.detail?.runningHours?.recordCount },
                        { label: 'Last reported at', value: this.detail?.runningHours?.lastReportedAt },
                        { label: 'Last sync update', value: this.detail?.runningHours?.lastSyncUpdate },
                        { label: 'Tracking window', value: this.detail?.runningHours?.trackingWindow },
                    ];
                },

                syncDisplayForms() {
                    if (!this.detail) {
                        return;
                    }

                    const resolution = String(this.detail.resolution || '').split(' x ');

                    this.displayForm = {
                        exclude: !!this.detail.exclude,
                        graphicboardOnly: !!this.detail.graphicboardOnly,
                        internalSensor: !!this.detail.internalSensor,
                        currentLut: this.detail.currentLut === '-' ? '' : (this.detail.currentLut || ''),
                        installationDate: this.detail.installationDate === '-' ? '' : (this.detail.installationDate || ''),
                        manufacturer: this.detail.manufacturer === '-' ? '' : (this.detail.manufacturer || ''),
                        model: this.detail.model === '-' ? '' : (this.detail.model || ''),
                        serial: this.detail.serial === '-' ? '' : (this.detail.serial || ''),
                        inventoryNumber: this.detail.inventoryNumber === '-' ? '' : (this.detail.inventoryNumber || ''),
                        typeOfDisplay: this.detail.typeOfDisplay === '-' ? '' : (this.detail.typeOfDisplay || ''),
                        displayTechnology: this.detail.displayTechnology === '-' ? '' : (this.detail.displayTechnology || ''),
                        screenSize: this.detail.screenSize === '-' ? '' : (this.detail.screenSize || ''),
                        resolutionHorizontal: resolution[0] || '',
                        resolutionVertical: resolution[1] || '',
                    };

                    this.financialForm = {
                        purchaseDate: this.detail.purchaseDate === '-' ? '' : (this.detail.purchaseDate || ''),
                        initialValue: this.detail.initialValue === '-' ? '' : (this.detail.initialValue || ''),
                        expectedValue: this.detail.expectedValue === '-' ? '' : (this.detail.expectedValue || ''),
                        annualStraightLine: this.detail.annualStraightLine === '-' ? '' : (this.detail.annualStraightLine || ''),
                        monthlyStraightLine: this.detail.monthlyStraightLine === '-' ? '' : (this.detail.monthlyStraightLine || ''),
                        currentValue: this.detail.currentValue === '-' ? '' : (this.detail.currentValue || ''),
                        expectedReplacementDate: this.detail.expectedReplacementDate === '-' ? '' : (this.detail.expectedReplacementDate || ''),
                    };
                },

                updateHeaderTitle() {
                    const mainContent = document.getElementById('mobile-main-content');
                    const heading = document.getElementById('mobile-appbar-title');
                    const path = window.location.pathname || '';

                    if (!this.$root?.isConnected || !mainContent?.contains(this.$root) || !heading || !/^\/m\/displays\/[^/]+$/.test(path)) {
                        return;
                    }

                    const title = this.detail?.name || 'Display';
                    heading.textContent = title;
                },

                mobileSchedulerUrl() {
                    const displayId = this.detail?.id || this.displayId;
                    const displayName = this.detail?.name || '';
                    const params = new URLSearchParams({
                        view: 'scheduled',
                        display_id: String(displayId),
                        return_to: window.location.href,
                    });

                    if (displayName) {
                        params.set('display_name', displayName);
                    }

                    return `${@json(route('mobile.tasks'))}?${params.toString()}`;
                },

                openSchedulerEditor() {
                    if (!this.detail?.permissions?.edit) {
                        return;
                    }

                    const displayId = this.detail?.id || this.displayId;
                    const hierarchy = this.detail?.hierarchy || {};

                    if (typeof window.openTaskEditorWithPayload !== 'function') {
                        window.location.href = this.mobileSchedulerUrl();
                        return;
                    }

                    window.openTaskEditorWithPayload({
                        id: 0,
                        displays: [displayId],
                        facility2: hierarchy.facility?.id || '',
                        workgroup2: hierarchy.workgroup?.id || '',
                        workstation2: hierarchy.workstation?.id || '',
                    });
                },

                async quickCalibrateDisplay() {
                    if (!this.detail?.permissions?.edit || this.quickCalibrating) {
                        return;
                    }

                    if (typeof window.openTaskEditorWithPayload !== 'function') {
                        window.location.href = this.mobileSchedulerUrl();
                        return;
                    }

                    const displayId = this.detail?.id || this.displayId;
                    const hierarchy = this.detail?.hierarchy || {};

                    window.openTaskEditorWithPayload({
                        id: 0,
                        tasktype: 'cal',
                        quick_calibration: '1',
                        lock_tasktype: '1',
                        displays: [displayId],
                        facility2: hierarchy.facility?.id || '',
                        workgroup2: hierarchy.workgroup?.id || '',
                        workstation2: hierarchy.workstation?.id || '',
                    }, {
                        title: 'Calibrate Display',
                        subtitle: `Set the schedule window for ${this.detail?.name || 'this display'} before creating the calibration task.`,
                    });
                },

                async loadDetail(id, { syncForms = true, preserveDetail = true } = {}) {
                    if (!preserveDetail) {
                        this.loading = true;
                    } else {
                        this.historyLoading = true;
                    }

                    this.error = '';
                    this.settingsError = '';

                    try {
                        const response = await window.Perfectlum.request(`/api/display-modal/${id}?period=${encodeURIComponent(this.displayPeriod)}`);
                        this.detail = response;
                        this.updateHeaderTitle();

                        const bucketStillExists = this.historyTimeline().find((bucket) => bucket.key === this.selectedTimelineBucketKey);
                        if (!bucketStillExists) {
                            this.selectedTimelineBucketKey = null;
                        }

                        if (syncForms) {
                            this.syncDisplayForms();
                        }
                    } catch (error) {
                        if (!preserveDetail) {
                            this.detail = null;
                        }
                        this.error = error.message || 'Display detail could not be loaded.';
                    } finally {
                        this.loading = false;
                        this.historyLoading = false;
                        this.$nextTick(() => {
                            lucide.createIcons();
                            window.Perfectlum?.bindMobileDragScroll?.(this.$root);
                        });
                    }
                },

                async changeDisplayPeriod(period) {
                    if (this.displayPeriod === period) {
                        return;
                    }

                    this.displayPeriod = period;
                    await this.loadDetail(this.displayId, { syncForms: false, preserveDetail: true });
                },

                cancelDisplayEditing() {
                    this.isEditingDisplaySettings = false;
                    this.settingsError = '';
                    this.syncDisplayForms();
                },

                async saveDisplaySettings() {
                    this.savingDisplaySettings = true;
                    this.settingsError = '';

                    try {
                        const formData = new FormData();
                        formData.append('exclude', this.displayForm.exclude ? '1' : '0');
                        formData.append('CommunicationType', this.displayForm.graphicboardOnly ? '1' : '3');
                        formData.append('InternalSensor', this.displayForm.internalSensor ? '1' : '0');
                        formData.append('CurrentLUTIndex', this.displayForm.currentLut || '');
                        formData.append('InstalationDate', this.displayForm.installationDate || '');
                        formData.append('Manufacturer', this.displayForm.manufacturer || '');
                        formData.append('Model', this.displayForm.model || '');
                        formData.append('SerialNumber', this.displayForm.serial || '');
                        formData.append('InventoryNumber', this.displayForm.inventoryNumber || '');
                        formData.append('TypeOfDisplay', this.displayForm.typeOfDisplay || '');
                        formData.append('DisplayTechnology', this.displayForm.displayTechnology || '');
                        formData.append('ScreenSize', this.displayForm.screenSize || '');
                        formData.append('ResolutionHorizontal', this.displayForm.resolutionHorizontal || '');
                        formData.append('ResolutionVertical', this.displayForm.resolutionVertical || '');
                        formData.append('purchase_date', this.financialForm.purchaseDate || '');
                        formData.append('initial_value', this.financialForm.initialValue || '');
                        formData.append('expected_value', this.financialForm.expectedValue || '');
                        formData.append('annual_straight_line', this.financialForm.annualStraightLine || '');
                        formData.append('monthly_straight_line', this.financialForm.monthlyStraightLine || '');
                        formData.append('current_value', this.financialForm.currentValue || '');
                        formData.append('expected_replacement_date', this.financialForm.expectedReplacementDate || '');

                        await window.Perfectlum.postForm(`/api/display-modal/${this.displayId}/save`, formData);
                        this.isEditingDisplaySettings = false;
                        await this.loadDetail(this.displayId, { syncForms: true, preserveDetail: true });
                    } catch (error) {
                        this.settingsError = error.message || 'Failed to save display settings.';
                    } finally {
                        this.savingDisplaySettings = false;
                    }
                },

                async toggleMovePanel() {
                    this.movePanelOpen = !this.movePanelOpen;
                    this.moveError = '';

                    if (this.movePanelOpen && !this.moveOptions.facilities.length) {
                        await this.loadMoveOptions();
                    }
                },

                async loadMoveOptions() {
                    this.moveLoading = true;
                    this.moveError = '';

                    try {
                        const response = await window.Perfectlum.request(`/api/display-modal/${this.displayId}/move-options`);
                        this.moveOptions.facilities = response.facilities || [];
                        this.moveOptions.workgroups = response.workgroups || [];
                        this.moveOptions.workstations = response.workstations || [];
                        this.moveForm.facilityId = response.current?.facilityId ? String(response.current.facilityId) : '';
                        this.moveForm.workgroupId = response.current?.workgroupId ? String(response.current.workgroupId) : '';
                        this.moveForm.workstationId = response.current?.workstationId ? String(response.current.workstationId) : '';
                    } catch (error) {
                        this.moveError = error.message || 'Move options could not be loaded.';
                    } finally {
                        this.moveLoading = false;
                    }
                },

                async onMoveFacilityChange() {
                    this.moveForm.workgroupId = '';
                    this.moveForm.workstationId = '';
                    this.moveOptions.workstations = [];

                    if (!this.moveForm.facilityId) {
                        this.moveOptions.workgroups = [];
                        return;
                    }

                    this.moveLoading = true;
                    this.moveError = '';

                    try {
                        this.moveOptions.workgroups = await window.Perfectlum.request(`/api/display-modal/workgroups/${this.moveForm.facilityId}`);
                    } catch (error) {
                        this.moveError = error.message || 'Workgroups could not be loaded.';
                    } finally {
                        this.moveLoading = false;
                    }
                },

                async onMoveWorkgroupChange() {
                    this.moveForm.workstationId = '';

                    if (!this.moveForm.workgroupId) {
                        this.moveOptions.workstations = [];
                        return;
                    }

                    this.moveLoading = true;
                    this.moveError = '';

                    try {
                        this.moveOptions.workstations = await window.Perfectlum.request(`/api/display-modal/workstations/${this.moveForm.workgroupId}`);
                    } catch (error) {
                        this.moveError = error.message || 'Workstations could not be loaded.';
                    } finally {
                        this.moveLoading = false;
                    }
                },

                async moveDisplay() {
                    if (!this.moveForm.workstationId) {
                        return;
                    }

                    this.moveSaving = true;
                    this.moveError = '';

                    try {
                        const formData = new FormData();
                        formData.append('workstation_id', this.moveForm.workstationId);
                        await window.Perfectlum.postForm(`/api/display-modal/${this.displayId}/move`, formData);
                        this.movePanelOpen = false;
                        await this.loadDetail(this.displayId, { syncForms: true, preserveDetail: true });
                    } catch (error) {
                        this.moveError = error.message || 'Display could not be moved.';
                    } finally {
                        this.moveSaving = false;
                    }
                },

                destroyStructureMapGraph() {
                    if (this.structureGraphInstance?.destroy) {
                        this.structureGraphInstance.destroy();
                    }

                    this.structureGraphInstance = null;
                },

                async renderStructureMapGraph() {
                    if (!this.structureMapOpen || !this.$refs.structureGraphContainer || !this.detail?.structure) {
                        return;
                    }

                    this.destroyStructureMapGraph();
                    this.structureGraphInstance = await window.Perfectlum.createStructureMapGraph({
                        container: this.$refs.structureGraphContainer,
                        structure: this.detail.structure,
                        mobile: true,
                        onZoomChange: (zoom) => {
                            this.structureMapZoom = Number(zoom || 1);
                        },
                        onOpenDisplay: (displayId) => {
                            if (!displayId) {
                                return;
                            }

                            this.closeStructureMap();
                            const query = this.displayDetailQueryString ? `?${this.displayDetailQueryString}` : '';
                            window.location.href = `${this.displayDetailBase}/${displayId}${query}`;
                        },
                    });

                    this.structureMapZoom = Number(this.structureGraphInstance?.graph?.zoom?.() || 1);
                },

                async openStructureMap() {
                    this.structureMapOpen = true;
                    await this.$nextTick();
                    lucide.createIcons();
                    await new Promise((resolve) => requestAnimationFrame(() => resolve()));
                    await this.renderStructureMapGraph();
                },

                closeStructureMap() {
                    this.structureMapOpen = false;
                    this.destroyStructureMapGraph();
                },

                resetStructureMapView() {
                    this.structureGraphInstance?.fit?.();
                },

                zoomStructureMapIn() {
                    this.structureGraphInstance?.zoomIn?.();
                },

                zoomStructureMapOut() {
                    this.structureGraphInstance?.zoomOut?.();
                },

                performanceTrendSvg(timeline) {
                    if (!timeline || !timeline.length) {
                        return '';
                    }

                    const width = 320;
                    const height = 170;
                    const padX = 18;
                    const padTop = 12;
                    const padBottom = 18;
                    const chartHeight = height - padTop - padBottom;
                    const chartWidth = width - (padX * 2);
                    const maxTotal = Math.max(...timeline.map((bucket) => Number(bucket.total || 0)), 1);
                    const barWidth = Math.max(18, Math.min(28, chartWidth / Math.max(timeline.length, 1) * 0.55));
                    const gap = timeline.length > 1
                        ? (chartWidth - (barWidth * timeline.length)) / (timeline.length - 1)
                        : 0;
                    const baseline = height - padBottom;
                    const grid = [0.25, 0.5, 0.75].map((ratio) => {
                        const y = padTop + (chartHeight * ratio);
                        return `<line x1="${padX}" y1="${y}" x2="${width - padX}" y2="${y}" stroke="rgba(148,163,184,0.18)" stroke-width="1" stroke-dasharray="3 4" />`;
                    }).join('');

                    const linePoints = [];
                    const bars = timeline.map((bucket, index) => {
                        const x = padX + ((barWidth + gap) * index);
                        const total = Number(bucket.total || 0);
                        const passed = Number(bucket.passed || 0);
                        const failed = Number(bucket.failed || 0);
                        const other = Number(bucket.other || 0);
                        const totalHeight = (total / maxTotal) * chartHeight;
                        const otherHeight = total > 0 ? (other / total) * totalHeight : 0;
                        const failedHeight = total > 0 ? (failed / total) * totalHeight : 0;
                        const passedHeight = total > 0 ? (passed / total) * totalHeight : 0;
                        const y = baseline - totalHeight;

                        const rateY = baseline - ((Number(bucket.passRate || 0) / 100) * chartHeight);
                        linePoints.push(`${x + (barWidth / 2)},${rateY}`);

                        return `
                            <rect x="${x}" y="${y}" width="${barWidth}" height="${passedHeight}" rx="7" ry="7" fill="#10b981"></rect>
                            <rect x="${x}" y="${y + passedHeight}" width="${barWidth}" height="${failedHeight}" fill="#f43f5e"></rect>
                            <rect x="${x}" y="${y + passedHeight + failedHeight}" width="${barWidth}" height="${otherHeight}" rx="7" ry="7" fill="#f59e0b"></rect>
                        `;
                    }).join('');

                    const line = `<polyline fill="none" stroke="#0ea5e9" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" points="${linePoints.join(' ')}"></polyline>`;
                    const points = linePoints.map((point) => {
                        const [cx, cy] = point.split(',');
                        return `<circle cx="${cx}" cy="${cy}" r="3.4" fill="#ffffff" stroke="#0ea5e9" stroke-width="2"></circle>`;
                    }).join('');

                    return `
                        <svg viewBox="0 0 ${width} ${height}" class="h-40 w-full" preserveAspectRatio="none" aria-hidden="true">
                            ${grid}
                            <line x1="${padX}" y1="${baseline}" x2="${width - padX}" y2="${baseline}" stroke="rgba(148,163,184,0.18)" stroke-width="1.2"></line>
                            ${bars}
                            ${line}
                            ${points}
                        </svg>
                    `;
                },

                runningHoursTrendSvg(trend) {
                    if (!trend || trend.length < 2) {
                        return '';
                    }

                    const width = 320;
                    const height = 124;
                    const padX = 10;
                    const padY = 12;
                    const values = trend.map((point) => Number(point.value || 0));
                    const min = Math.min(...values);
                    const max = Math.max(...values);
                    const range = max - min || 1;
                    const stepX = trend.length > 1 ? (width - (padX * 2)) / (trend.length - 1) : 0;

                    const points = trend.map((point, index) => {
                        const x = padX + (stepX * index);
                        const y = height - padY - (((Number(point.value || 0) - min) / range) * (height - (padY * 2)));
                        return `${x},${y}`;
                    });

                    const path = points.join(' ');
                    const circles = points.map((point) => {
                        const [cx, cy] = point.split(',');
                        return `<circle cx="${cx}" cy="${cy}" r="3.3" fill="#ffffff" stroke="#0ea5e9" stroke-width="2"></circle>`;
                    }).join('');

                    return `
                        <svg viewBox="0 0 ${width} ${height}" class="h-28 w-full" preserveAspectRatio="none" aria-hidden="true">
                            <line x1="${padX}" y1="${height - padY}" x2="${width - padX}" y2="${height - padY}" stroke="rgba(148,163,184,0.18)" stroke-width="1.2"></line>
                            <polyline fill="none" stroke="#0ea5e9" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" points="${path}"></polyline>
                            ${circles}
                        </svg>
                    `;
                },

                runningHoursAxisLabels(trend) {
                    if (!trend || !trend.length) {
                        return [];
                    }

                    if (trend.length <= 4) {
                        return trend.map((point, index) => ({
                            label: point.label,
                            position: trend.length === 1 ? 50 : (index / (trend.length - 1)) * 100,
                            fullLabel: point.fullLabel,
                        }));
                    }

                    return [
                        {
                            label: trend[0].label,
                            position: 0,
                            fullLabel: trend[0].fullLabel,
                        },
                        {
                            label: trend[Math.floor((trend.length - 1) / 2)].label,
                            position: 50,
                            fullLabel: trend[Math.floor((trend.length - 1) / 2)].fullLabel,
                        },
                        {
                            label: trend[trend.length - 1].label,
                            position: 100,
                            fullLabel: trend[trend.length - 1].fullLabel,
                        },
                    ];
                },

                sparklinePoints(points) {
                    if (!points || !points.length) {
                        return '';
                    }

                    const width = 100;
                    const height = 40;
                    const values = points.map((point) => Number(point.value || 0));
                    const min = Math.min(...values);
                    const max = Math.max(...values);
                    const range = max - min || 1;

                    return points.map((point, index) => {
                        const x = points.length === 1 ? 0 : (index / (points.length - 1)) * width;
                        const y = height - (((Number(point.value || 0) - min) / range) * height);
                        return `${x},${y}`;
                    }).join(' ');
                },
            }));

            if (window.Perfectlum?.registerAlpineData) {
                register();
                return;
            }

            (window.__perfectlumPageBoot = window.__perfectlumPageBoot || []).push(register);
        })();
    </script>
@endpush

@section('content')
    <div x-data="mobileDisplayDetail({{ (int) $displayId }})" x-init="init()" class="mobile-detail-screen">
        <div x-show="loading" x-cloak class="mobile-panel compact mobile-detail-loading-card" aria-hidden="true">
            <div class="mobile-detail-loading-chip"></div>
            <div class="mobile-detail-loading-line title"></div>
            <div class="mobile-detail-loading-line body"></div>
            <div class="mobile-detail-loading-line body short"></div>
            <div class="mobile-detail-loading-grid">
                <div class="mobile-detail-loading-grid-cell"></div>
                <div class="mobile-detail-loading-grid-cell"></div>
                <div class="mobile-detail-loading-grid-cell"></div>
            </div>
        </div>

        <div x-show="!loading && error" x-cloak class="mobile-panel compact">
            <p class="text-sm text-rose-600" x-text="error"></p>
        </div>

        <template x-if="detail && !loading">
            <div class="space-y-4">
                <section class="mobile-detail-hero-shell">
                    <section class="mobile-panel mobile-detail-hero p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="mobile-detail-kicker">Display cockpit</p>
                                <h2 class="mobile-detail-hero-title" x-text="detail.name"></h2>
                                <p class="mobile-detail-hero-meta">
                                    <span x-text="textOrDash(detail.manufacturer)"></span>
                                    <span class="mx-1">·</span>
                                    <span x-text="textOrDash(detail.model)"></span>
                                    <span class="mx-1">·</span>
                                    <span x-text="textOrDash(detail.serial)"></span>
                                </p>
                            </div>
                            <div class="shrink-0 flex flex-col items-end gap-2">
                                <span class="rounded-full border px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em]" :class="statusSurfaceClass()" x-text="detail.statusLabel"></span>
                            </div>
                        </div>

                        <p class="mobile-detail-hero-summary" x-text="detail.statusSummary"></p>

                        <div class="mobile-detail-hero-stats">
                            <div class="mobile-detail-hero-stat">
                                <p class="mobile-detail-hero-stat-label">Connection</p>
                                <p class="mobile-detail-hero-stat-value" x-text="detail.connectedLabel"></p>
                            </div>
                            <div class="mobile-detail-hero-stat">
                                <p class="mobile-detail-hero-stat-label">Pass Rate</p>
                                <p class="mobile-detail-hero-stat-value" x-text="`${detail.history?.passRate ?? 0}%`"></p>
                            </div>
                            <div class="mobile-detail-hero-stat">
                                <p class="mobile-detail-hero-stat-label">Histories</p>
                                <p class="mobile-detail-hero-stat-value" x-text="textOrDash(detail.history?.total)"></p>
                            </div>
                        </div>
                    </section>

                    <div class="mobile-detail-chip-row">
                        <span x-show="detail.resolution && detail.resolution !== '-'" x-cloak class="mobile-detail-chip subtle">
                            <span x-text="detail.resolution"></span>
                        </span>
                        <span x-show="detail.screenSize && detail.screenSize !== '-'" x-cloak class="mobile-detail-chip subtle">
                            <span x-text="detail.screenSize"></span>
                        </span>
                        <span class="mobile-detail-chip subtle">
                            <span x-text="detail.lastSync"></span>
                        </span>
                    </div>

                    <div class="mobile-detail-tool-row no-scrollbar">
                        <button type="button" class="mobile-detail-tool" @click="openStructureMap()">
                            <i data-lucide="map" class="h-3.5 w-3.5"></i>
                            <span>Map</span>
                        </button>
                        <button type="button" class="mobile-detail-tool" :class="{ 'active': activeTab === 'overview' }" @click="switchTab('overview')">
                            <i data-lucide="layout-grid" class="h-3.5 w-3.5"></i>
                            <span>Overview</span>
                        </button>
                        <button type="button" class="mobile-detail-tool" :class="{ 'active': activeTab === 'history' }" @click="switchTab('history')">
                            <i data-lucide="history" class="h-3.5 w-3.5"></i>
                            <span>History</span>
                        </button>
                        <button type="button" class="mobile-detail-tool" :class="{ 'active': activeTab === 'settings' }" @click="switchTab('settings')">
                            <i data-lucide="sliders-horizontal" class="h-3.5 w-3.5"></i>
                            <span>Settings</span>
                        </button>
                        <template x-if="detail.permissions?.edit">
                            <button type="button" class="mobile-detail-tool" :disabled="quickCalibrating" @click="quickCalibrateDisplay()">
                                <i data-lucide="monitor-play" class="h-3.5 w-3.5"></i>
                                <span x-text="quickCalibrating ? 'Calibrating...' : 'Calibrate'"></span>
                            </button>
                        </template>
                        <template x-if="detail.permissions?.edit">
                            <button type="button" class="mobile-detail-tool" @click="openSchedulerEditor()">
                                <i data-lucide="calendar-clock" class="h-3.5 w-3.5"></i>
                                <span>Scheduler</span>
                            </button>
                        </template>
                    </div>
                </section>

                <div class="mobile-detail-top-grid">
                    <section class="mobile-panel">
                        <div class="mobile-detail-surface-head">
                            <div>
                                <p class="mobile-detail-section-label">Display Scope</p>
                                <h3 class="mobile-detail-section-title">Current hierarchy</h3>
                                <p class="mobile-detail-section-copy">This display lives inside the scope below.</p>
                            </div>
                        </div>

                    <div class="mobile-detail-scope-grid mt-4">
                        <template x-for="item in hierarchyRows()" :key="item.label">
                            <article class="mobile-detail-scope-card" :class="`tone-${item.tone}`">
                                <p class="mobile-detail-scope-label" x-text="item.label"></p>
                                <p class="mobile-detail-scope-value" x-text="textOrDash(item.value)"></p>
                                <p class="mobile-detail-scope-note" x-text="item.note"></p>
                            </article>
                        </template>
                    </div>

                    </section>

                    <section class="mobile-panel">
                        <div class="mobile-detail-surface-head">
                            <div>
                                <p class="mobile-detail-section-label" x-text="detail.statusSectionLabel || 'Live Health'"></p>
                                <h3 class="mobile-detail-section-title">Current device health</h3>
                                <p class="mobile-detail-section-copy">Live sync state and latest issue summary for this display.</p>
                            </div>
                        </div>
                        <div class="mobile-detail-status-row mt-4">
                            <span class="h-2.5 w-2.5 rounded-full" :class="detail.statusTone === 'success' ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                            <span class="text-[15px] font-semibold text-slate-950" x-text="detail.statusLabel"></span>
                        </div>
                        <p class="mobile-detail-status-copy" x-text="detail.statusSummary"></p>
                        <div class="mobile-detail-status-meta">
                            <p><span class="font-semibold text-slate-700">Connection:</span> <span x-text="detail.connectedLabel"></span></p>
                            <p><span class="font-semibold text-slate-700">Last sync:</span> <span x-text="detail.lastSync"></span></p>
                        </div>
                        <div class="mt-4 space-y-2">
                            <template x-if="liveErrors().length">
                                <div class="space-y-2">
                                    <template x-for="(message, index) in liveErrors()" :key="`live-error-${index}`">
                                        <p class="mobile-detail-status-error border" :class="latestErrorClass()" x-text="message"></p>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!liveErrors().length">
                                <p class="mobile-detail-status-error border" :class="latestErrorClass()" x-text="detail.latestError"></p>
                            </template>
                        </div>
                    </section>
                </div>

                <section class="mobile-panel p-4">
                    <div class="mobile-detail-surface-head">
                        <div>
                            <p class="mobile-detail-section-label">Device Profile</p>
                            <h3 class="mobile-detail-section-title">Hardware and inventory details</h3>
                            <p class="mobile-detail-section-copy">Key device identifiers used across service, calibration, and reporting.</p>
                        </div>
                    </div>
                    <div class="mobile-detail-grid mt-4">
                        <template x-for="item in deviceDetailRows()" :key="item.label">
                            <dl class="mobile-detail-field">
                                <dt x-text="item.label"></dt>
                                <dd x-text="textOrDash(item.value)"></dd>
                            </dl>
                        </template>
                    </div>
                </section>

                <section class="mobile-panel p-2">
                    <div class="mobile-detail-tabs no-scrollbar">
                        <button type="button" class="mobile-detail-tab" :class="{ 'active': activeTab === 'overview' }" @click="switchTab('overview')">Overview</button>
                        <button type="button" class="mobile-detail-tab" :class="{ 'active': activeTab === 'history' }" @click="switchTab('history')">History</button>
                        <button type="button" class="mobile-detail-tab" :class="{ 'active': activeTab === 'settings' }" @click="switchTab('settings')">Settings</button>
                    </div>
                </section>

                <section x-ref="overviewSection" x-show="activeTab === 'overview'" x-cloak class="space-y-4">
                    <section class="mobile-panel p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="mobile-detail-section-label">Performance Trend</p>
                                <h3 class="mobile-detail-section-title">Recent calibration history</h3>
                                <p x-show="historyLoading" x-cloak class="mt-2 text-[12px] font-medium text-sky-600">Updating history...</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" class="mobile-period-button" :class="{ 'active': displayPeriod === '30d' }" :disabled="historyLoading" @click="changeDisplayPeriod('30d')">30D</button>
                                <button type="button" class="mobile-period-button" :class="{ 'active': displayPeriod === '90d' }" :disabled="historyLoading" @click="changeDisplayPeriod('90d')">90D</button>
                                <button type="button" class="mobile-period-button" :class="{ 'active': displayPeriod === '180d' }" :disabled="historyLoading" @click="changeDisplayPeriod('180d')">180D</button>
                                <button type="button" class="mobile-period-button" :class="{ 'active': displayPeriod === 'all' }" :disabled="historyLoading" @click="changeDisplayPeriod('all')">ALL</button>
                            </div>
                        </div>

                        <template x-if="historyTimeline().length">
                            <div class="mt-4 space-y-3">
                                <div class="mobile-trend-chart">
                                    <div class="mb-3 flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-[12px] font-semibold text-slate-700" x-text="detail.history.timelineTitle"></p>
                                            <p class="mt-1 text-[11px] text-slate-500">Stacked bars show run volume by outcome. Blue line shows pass rate.</p>
                                        </div>
                                        <p class="text-[11px] font-semibold text-slate-500">Bucket: <span class="text-slate-700" x-text="detail.history.bucket"></span></p>
                                    </div>
                                    <div x-html="performanceTrendSvg(historyTimeline())"></div>
                                </div>

                                <div class="flex flex-wrap gap-3 text-[11px] font-semibold text-slate-500">
                                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-emerald-500"></span> Passed</span>
                                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-rose-500"></span> Failed</span>
                                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-amber-500"></span> Skipped / Cancelled</span>
                                    <span class="inline-flex items-center gap-2"><span class="h-0.5 w-4 rounded-full bg-sky-500"></span> Pass Rate</span>
                                </div>

                                <div class="space-y-2">
                                    <template x-for="bucket in historyTimeline()" :key="bucket.key">
                                        <button type="button" class="mobile-trend-bucket w-full text-left" :class="{ 'active': selectedTimelineBucketKey === bucket.key }" @click="toggleTimelineBucket(bucket)">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-[12px] font-semibold text-slate-900" x-text="bucket.rangeLabel"></p>
                                                    <p class="mt-1 text-[11px] text-slate-500"><span x-text="bucket.label"></span> · <span x-text="`${bucket.total} runs`"></span></p>
                                                </div>
                                                <span class="rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-[10px] font-semibold text-sky-700"><span x-text="bucket.passRate"></span>%</span>
                                            </div>
                                            <div class="mobile-progress-bar mt-3">
                                                <span class="bg-emerald-500" :style="`width:${bucket.passedPct}%`"></span>
                                                <span class="bg-rose-500" :style="`width:${bucket.failedPct}%`"></span>
                                                <span class="bg-amber-400" :style="`width:${bucket.otherPct}%`"></span>
                                            </div>
                                            <div class="mt-2 flex flex-wrap gap-3 text-[11px] text-slate-500">
                                                <span>Passed: <span class="font-semibold text-emerald-700" x-text="bucket.passed"></span></span>
                                                <span>Failed: <span class="font-semibold text-rose-700" x-text="bucket.failed"></span></span>
                                                <span>Skipped / Cancelled: <span class="font-semibold text-amber-700" x-text="bucket.other"></span></span>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="!historyTimeline().length">
                            <div class="mt-4 space-y-3">
                                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">No history data available yet.</div>
                                <div class="rounded-2xl border border-sky-100 bg-sky-50/80 px-4 py-3 text-sm leading-6 text-sky-700">Current device health can still show a live device alert even when calibration history is empty, because live sync status and recorded histories are tracked separately.</div>
                            </div>
                        </template>
                    </section>

                    <section class="mobile-detail-grid two">
                        <template x-for="item in overviewStats()" :key="item.label">
                            <article class="mobile-metric">
                                <p class="mobile-stat-label" x-text="item.label"></p>
                                <p class="mobile-stat-value" :class="item.valueClass" x-text="item.value"></p>
                                <p class="mobile-stat-note" x-text="item.note"></p>
                            </article>
                        </template>
                    </section>

                    <section class="mobile-panel p-4">
                        <p class="mobile-detail-section-label">Technical Summary</p>
                        <div class="mobile-detail-grid two mt-4">
                            <template x-for="item in technicalSummaryRows()" :key="item.label">
                                <dl class="mobile-detail-field">
                                    <dt x-text="item.label"></dt>
                                    <dd x-text="textOrDash(item.value)"></dd>
                                </dl>
                            </template>
                        </div>
                    </section>

                    <section class="mobile-panel p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="mobile-detail-section-label">Backlight / Running Hours</p>
                                <h3 class="mobile-detail-section-title">Synced runtime snapshots</h3>
                                <p class="mobile-detail-section-copy">Based on synchronized display usage data from the remote client.</p>
                            </div>
                            <span class="rounded-full bg-sky-50 px-3 py-1 text-[11px] font-semibold text-sky-700" x-text="detail.runningHours.available ? 'Runtime data available' : 'No runtime data'"></span>
                        </div>

                        <template x-if="detail.runningHours.available">
                            <div class="mt-4 space-y-4">
                                <div class="mobile-detail-grid two">
                                    <template x-for="item in runningHoursRows()" :key="item.label">
                                        <dl class="mobile-detail-field">
                                            <dt x-text="item.label"></dt>
                                            <dd x-text="textOrDash(item.value)"></dd>
                                        </dl>
                                    </template>
                                </div>

                                <template x-if="(detail.runningHours.trend || []).length >= 2">
                                    <div class="mobile-trend-chart">
                                        <div class="mb-3 flex items-start justify-between gap-3">
                                            <div>
                                                <p class="text-[12px] font-semibold text-slate-700">Reported runtime trend</p>
                                                <p class="mt-1 text-[11px] text-slate-500">Latest synced runtime points</p>
                                            </div>
                                            <p class="text-[11px] font-semibold text-slate-500">Latest reported hours: <span class="text-slate-700" x-text="detail.runningHours.latestReported"></span></p>
                                        </div>
                                        <div x-html="runningHoursTrendSvg(detail.runningHours.trend)"></div>
                                        <div class="relative mt-2 h-5">
                                            <template x-for="label in runningHoursAxisLabels(detail.runningHours.trend)" :key="label.fullLabel">
                                                <p class="absolute top-0 text-[10px] font-semibold text-slate-400" :style="`left:${label.position}%; transform: translateX(-50%);`" x-text="label.label"></p>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="!detail.runningHours.available">
                            <div class="mt-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-sm text-slate-500">No runtime records have been synced for this display yet.</div>
                        </template>
                    </section>
                </section>

                <section x-ref="historySection" x-show="activeTab === 'history'" x-cloak class="space-y-4">
                    <section class="mobile-panel p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="mobile-detail-section-label">History Range</p>
                                <p class="mobile-detail-section-copy">Select the time period used across trends and recent runs.</p>
                            </div>
                            <a :href="detail.links.histories" class="mobile-detail-link">All History</a>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button type="button" class="mobile-period-button" :class="{ 'active': displayPeriod === '30d' }" :disabled="historyLoading" @click="changeDisplayPeriod('30d')">30D</button>
                            <button type="button" class="mobile-period-button" :class="{ 'active': displayPeriod === '90d' }" :disabled="historyLoading" @click="changeDisplayPeriod('90d')">90D</button>
                            <button type="button" class="mobile-period-button" :class="{ 'active': displayPeriod === '180d' }" :disabled="historyLoading" @click="changeDisplayPeriod('180d')">180D</button>
                            <button type="button" class="mobile-period-button" :class="{ 'active': displayPeriod === 'all' }" :disabled="historyLoading" @click="changeDisplayPeriod('all')">ALL</button>
                        </div>
                    </section>

                    <section class="mobile-panel p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="mobile-detail-section-label">Latest Scored Report</p>
                                <h3 class="mobile-detail-section-title">Most recent scored calibration report in the active range</h3>
                                <p class="mobile-detail-section-copy">This panel summarizes the latest report that contains scored checks. It is separate from the live device health shown in the left sidebar.</p>
                            </div>
                            <template x-if="detail.history.latestEvaluation">
                                <a :href="detail.history.latestEvaluation.url" class="mobile-detail-link">View Full Report</a>
                            </template>
                        </div>

                        <template x-if="detail.history.latestEvaluation">
                            <div class="mt-4 space-y-4">
                                <div class="rounded-[1rem] border border-slate-200 bg-slate-50 px-4 py-3.5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <p class="text-[13px] font-semibold text-slate-900" x-text="detail.history.latestEvaluation.name"></p>
                                            <p class="mt-1 text-[11px] text-slate-500" x-text="detail.history.latestEvaluation.performedAt"></p>
                                        </div>
                                        <span :class="historyBadgeClass(detail.history.latestEvaluation.resultTone)" x-text="detail.history.latestEvaluation.resultLabel"></span>
                                    </div>
                                </div>

                                <div class="mobile-detail-grid two">
                                    <dl class="mobile-detail-field">
                                        <dt>Scored Checks</dt>
                                        <dd x-text="detail.history.latestEvaluation.totalScores"></dd>
                                    </dl>
                                    <dl class="mobile-detail-field">
                                        <dt>Passed Checks</dt>
                                        <dd class="text-emerald-700" x-text="detail.history.latestEvaluation.okScores"></dd>
                                    </dl>
                                    <dl class="mobile-detail-field">
                                        <dt>Failed Checks</dt>
                                        <dd class="text-rose-700" x-text="detail.history.latestEvaluation.failedScores"></dd>
                                    </dl>
                                </div>

                                <div class="rounded-[1rem] border border-slate-200 overflow-hidden">
                                    <div class="border-b border-slate-200 px-4 py-3">
                                        <p class="text-[13px] font-semibold text-slate-900">Failed Check Highlights</p>
                                        <p class="mt-1 text-[11px] text-slate-500">Checks from the latest scored report that missed their target.</p>
                                    </div>
                                    <template x-if="detail.history.latestEvaluation.highlights.length">
                                        <div class="divide-y divide-slate-100">
                                            <template x-for="item in detail.history.latestEvaluation.highlights" :key="`${item.section}-${item.name}`">
                                                <div class="px-4 py-3">
                                                    <p class="text-[13px] font-semibold text-slate-900" x-text="item.name"></p>
                                                    <p class="mt-1 text-[11px] text-slate-400" x-text="item.section"></p>
                                                    <div class="mt-3 grid grid-cols-[68px_minmax(0,1fr)] gap-x-3 gap-y-2 text-[12px]">
                                                        <p class="font-semibold text-slate-500">Target</p>
                                                        <p class="text-slate-700" x-text="item.limit"></p>
                                                        <p class="font-semibold text-slate-500">Result</p>
                                                        <p class="text-slate-700" x-text="item.measured"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!detail.history.latestEvaluation.highlights.length">
                                        <div class="px-4 py-6 text-sm text-emerald-700">No failed checks were found in the latest scored report.</div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="!detail.history.latestEvaluation">
                            <div class="mt-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">No scored evaluation was found in the selected period.</div>
                        </template>
                    </section>

                    <section class="mobile-panel p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="mobile-detail-section-label">Measurement Trends</p>
                                <h3 class="mobile-detail-section-title">Key calibration metrics over time</h3>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-400">Extracted from Target &amp; Results</span>
                        </div>

                        <template x-if="historyMetrics().length">
                            <div class="mt-4 space-y-3">
                                <template x-for="metric in historyMetrics()" :key="metric.key">
                                    <div class="rounded-[1rem] border border-slate-200 bg-slate-50 px-4 py-3.5">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="text-[13px] font-semibold text-slate-900" x-text="metric.label"></p>
                                                <p class="mt-1 text-[11px] text-slate-500">Latest: <span class="font-semibold text-slate-700" x-text="`${metric.latest}${metric.unit || ''}`"></span></p>
                                            </div>
                                            <div class="text-right text-[11px] text-slate-500">
                                                <p>Min <span class="font-semibold text-slate-700" x-text="`${metric.min}${metric.unit || ''}`"></span></p>
                                                <p class="mt-1">Max <span class="font-semibold text-slate-700" x-text="`${metric.max}${metric.unit || ''}`"></span></p>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <svg viewBox="0 0 100 40" preserveAspectRatio="none" class="h-20 w-full overflow-visible">
                                                <polyline
                                                    fill="none"
                                                    stroke="#0ea5e9"
                                                    stroke-width="1.35"
                                                    stroke-opacity="0.95"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    :points="sparklinePoints(metric.points)">
                                                </polyline>
                                            </svg>
                                        </div>
                                        <div class="mt-2 flex flex-wrap justify-between gap-2 text-[11px] text-slate-400">
                                            <template x-for="point in metric.points" :key="`${metric.key}-${point.label}-${point.value}`">
                                                <span x-text="point.label"></span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="!historyMetrics().length">
                            <div class="mt-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">No measurable numeric trends were found for this display in the selected period.</div>
                        </template>
                    </section>

                    <section class="mobile-panel p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="mobile-detail-section-label">Timeline Mix</p>
                                <h3 class="mobile-detail-section-title">Pass / fail distribution by period</h3>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-400">Based on active filter</span>
                        </div>

                        <template x-if="historyTimeline().length">
                            <div class="mt-4 space-y-3">
                                <template x-for="bucket in historyTimeline()" :key="`timeline-${bucket.key}`">
                                    <div class="rounded-[1rem] border border-slate-200 bg-slate-50 px-4 py-3.5">
                                        <div class="flex items-center justify-between gap-3 text-[12px]">
                                            <span class="font-semibold text-slate-800" x-text="bucket.label"></span>
                                            <span class="text-slate-400" x-text="`${bucket.total} runs`"></span>
                                        </div>
                                        <div class="mobile-progress-bar mt-3">
                                            <span class="bg-emerald-500" :style="`width:${bucket.passedPct}%`"></span>
                                            <span class="bg-rose-500" :style="`width:${bucket.failedPct}%`"></span>
                                            <span class="bg-amber-400" :style="`width:${bucket.otherPct}%`"></span>
                                        </div>
                                        <div class="mt-2 flex flex-wrap gap-3 text-[11px] text-slate-500">
                                            <span>Passed: <span class="font-semibold text-emerald-700" x-text="bucket.passed"></span></span>
                                            <span>Failed: <span class="font-semibold text-rose-700" x-text="bucket.failed"></span></span>
                                            <span>Other: <span class="font-semibold text-amber-700" x-text="bucket.other"></span></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="!historyTimeline().length">
                            <div class="mt-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">No timeline data available for the selected period.</div>
                        </template>
                    </section>

                    <section class="mobile-panel p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="mobile-detail-section-label">Recent Runs</p>
                                <h3 class="mobile-detail-section-title">Calibration and QA history</h3>
                            </div>
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <template x-if="selectedTimelineBucket()">
                                    <button type="button" class="mobile-detail-link primary" @click="selectedTimelineBucketKey = null" x-text="selectedTimelineBucket().rangeLabel"></button>
                                </template>
                                <span class="text-[11px] font-semibold text-slate-400" x-text="`${filteredRecentHistory().length} records`"></span>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3">
                            <template x-for="item in filteredRecentHistory()" :key="`history-${item.id}`">
                                <a :href="item.url" class="block rounded-[1rem] border border-slate-200 bg-slate-50 px-4 py-3.5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-[13px] font-semibold leading-[1.4] text-slate-900" x-text="item.name"></p>
                                            <p class="mt-1 text-[11px] text-slate-500"><span x-text="item.performedAt"></span><span class="mx-1">·</span><span x-text="item.bucketRangeLabel"></span></p>
                                        </div>
                                        <span :class="historyBadgeClass(item.resultTone)" x-text="item.resultLabel"></span>
                                    </div>
                                </a>
                            </template>

                            <template x-if="!filteredRecentHistory().length">
                                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">No history records match the selected bucket.</div>
                            </template>
                        </div>
                    </section>
                </section>

                <section x-ref="settingsSection" x-show="activeTab === 'settings'" x-cloak class="space-y-4">
                    <section class="mobile-panel p-4">
                        <div class="mobile-detail-settings-toolbar">
                            <div>
                                <p class="mobile-detail-section-label">Settings</p>
                                <p class="mobile-detail-settings-copy">Review or edit display preferences.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <template x-if="!isEditingDisplaySettings && detail.permissions?.edit">
                                    <button type="button" class="mobile-detail-link primary" @click="isEditingDisplaySettings = true">Edit</button>
                                </template>
                                <template x-if="isEditingDisplaySettings">
                                    <button type="button" class="mobile-detail-link" @click="cancelDisplayEditing()">Cancel</button>
                                </template>
                            </div>
                        </div>
                    </section>

                    <template x-if="!isEditingDisplaySettings">
                        <div class="space-y-4">
                            <section class="mobile-panel p-4">
                                <p class="mobile-detail-section-label">Calibration Options</p>
                                <div class="mobile-detail-grid mt-4">
                                    <template x-for="item in readOnlyCalibrationFields()" :key="item.label">
                                        <dl class="mobile-detail-field">
                                            <dt x-text="item.label"></dt>
                                            <dd x-text="textOrDash(item.value)"></dd>
                                        </dl>
                                    </template>
                                </div>
                            </section>

                            <section class="mobile-panel p-4">
                                <p class="mobile-detail-section-label">Technical Settings</p>
                                <div class="mobile-detail-grid two mt-4">
                                    <template x-for="item in readOnlyTechnicalFields()" :key="item.label">
                                        <dl class="mobile-detail-field">
                                            <dt x-text="item.label"></dt>
                                            <dd x-text="textOrDash(item.value)"></dd>
                                        </dl>
                                    </template>
                                </div>
                            </section>

                            <section class="mobile-panel p-4">
                                <p class="mobile-detail-section-label">Financial Settings</p>
                                <div class="mobile-detail-grid two mt-4">
                                    <template x-for="item in readOnlyFinancialFields()" :key="item.label">
                                        <dl class="mobile-detail-field">
                                            <dt x-text="item.label"></dt>
                                            <dd x-text="textOrDash(item.value)"></dd>
                                        </dl>
                                    </template>
                                </div>
                            </section>
                        </div>
                    </template>

                    <template x-if="isEditingDisplaySettings">
                        <div class="space-y-4">
                            <section class="mobile-panel p-4">
                                <p class="mobile-detail-section-label">Calibration Options</p>

                                <div class="mobile-detail-form-stack mt-4">
                                    <label class="mobile-toggle-row">
                                        <span class="text-[13px] text-slate-700">Exclude From Calibration</span>
                                        <input type="checkbox" x-model="displayForm.exclude">
                                    </label>

                                    <label class="mobile-toggle-row">
                                        <span class="text-[13px] text-slate-700">Graphicboard LUTs Only</span>
                                        <input type="checkbox" x-model="displayForm.graphicboardOnly">
                                    </label>

                                    <label class="mobile-toggle-row">
                                        <span class="text-[13px] text-slate-700">Internal Sensor</span>
                                        <input type="checkbox" x-model="displayForm.internalSensor">
                                    </label>

                                    <div class="mobile-detail-form-grid-two">
                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Current LUT Index</span>
                                            <input type="text" class="mobile-input-field" x-model="displayForm.currentLut">
                                        </label>

                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Installation Date</span>
                                            <input type="date" class="mobile-input-field" x-model="displayForm.installationDate">
                                        </label>
                                    </div>
                                </div>
                            </section>

                            <section class="mobile-panel p-4">
                                <p class="mobile-detail-section-label">Technical Settings</p>

                                <div class="mobile-detail-form-stack mt-4">
                                    <div class="mobile-detail-form-grid-two">
                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Manufacturer</span>
                                            <input type="text" class="mobile-input-field" x-model="displayForm.manufacturer">
                                        </label>

                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Model</span>
                                            <input type="text" class="mobile-input-field" x-model="displayForm.model">
                                        </label>
                                    </div>

                                    <div class="mobile-detail-form-grid-two">
                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Serial</span>
                                            <input type="text" class="mobile-input-field" x-model="displayForm.serial">
                                        </label>

                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Inventory Number</span>
                                            <input type="text" class="mobile-input-field" x-model="displayForm.inventoryNumber">
                                        </label>
                                    </div>

                                    <div class="mobile-detail-form-grid-two">
                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Type Of Display</span>
                                            <input type="text" class="mobile-input-field" x-model="displayForm.typeOfDisplay">
                                        </label>

                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Display Technology</span>
                                            <input type="text" class="mobile-input-field" x-model="displayForm.displayTechnology">
                                        </label>
                                    </div>

                                    <label class="mobile-detail-form-label">
                                        <span class="mobile-detail-form-label-text">Screen Size</span>
                                        <input type="text" class="mobile-input-field" x-model="displayForm.screenSize">
                                    </label>

                                    <div class="mobile-detail-form-grid-two">
                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Resolution H</span>
                                        <input type="text" class="mobile-input-field" x-model="displayForm.resolutionHorizontal">
                                        </label>

                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Resolution V</span>
                                        <input type="text" class="mobile-input-field" x-model="displayForm.resolutionVertical">
                                        </label>
                                    </div>
                                </div>
                            </section>

                            <section class="mobile-panel p-4">
                                <p class="mobile-detail-section-label">Financial Settings</p>

                                <div class="mobile-detail-form-stack mt-4">
                                    <div class="mobile-detail-form-grid-two">
                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Purchase Date</span>
                                            <input type="date" class="mobile-input-field" x-model="financialForm.purchaseDate">
                                        </label>

                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Expected Replacement Date</span>
                                            <input type="date" class="mobile-input-field" x-model="financialForm.expectedReplacementDate">
                                        </label>
                                    </div>

                                    <div class="mobile-detail-form-grid-two">
                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Initial Value</span>
                                            <input type="text" class="mobile-input-field" x-model="financialForm.initialValue">
                                        </label>

                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Expected Value</span>
                                            <input type="text" class="mobile-input-field" x-model="financialForm.expectedValue">
                                        </label>
                                    </div>

                                    <div class="mobile-detail-form-grid-two">
                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Annual Straight Line</span>
                                            <input type="text" class="mobile-input-field" x-model="financialForm.annualStraightLine">
                                        </label>

                                        <label class="mobile-detail-form-label">
                                            <span class="mobile-detail-form-label-text">Monthly Straight Line</span>
                                            <input type="text" class="mobile-input-field" x-model="financialForm.monthlyStraightLine">
                                        </label>
                                    </div>

                                    <label class="mobile-detail-form-label">
                                        <span class="mobile-detail-form-label-text">Current Value</span>
                                        <input type="text" class="mobile-input-field" x-model="financialForm.currentValue">
                                    </label>
                                </div>
                            </section>

                            <section class="mobile-panel p-4">
                                <div x-show="settingsError" x-cloak class="mb-3 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-[12px] text-rose-700" x-text="settingsError"></div>
                                <div class="flex justify-end">
                                    <button type="button" class="mobile-detail-link primary" :disabled="savingDisplaySettings" @click="saveDisplaySettings()" x-text="savingDisplaySettings ? 'Saving...' : 'Save Changes'"></button>
                                </div>
                            </section>
                        </div>
                    </template>
                </section>
            </div>
        </template>

        <template x-teleport="body">
            <div x-show="structureMapOpen" x-cloak class="fixed inset-0 z-[120] mobile-map-backdrop" @keydown.escape.window="closeStructureMap()">
                <div class="flex h-full w-full flex-col mobile-map-panel">
                    <div class="px-4 pb-3 pt-[max(0.9rem,env(safe-area-inset-top))]">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="mobile-detail-section-label">Structure Map</p>
                                <h3 class="mobile-detail-section-title">Display Hierarchy Map</h3>
                                <p class="mobile-detail-section-copy">Vertical mind map. Drag the canvas to explore the hierarchy, then use the dock to fit or zoom.</p>
                            </div>
                            <button type="button" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white/90 text-slate-700 shadow-sm" @click="closeStructureMap()">
                                <i data-lucide="x" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="px-4 pb-3">
                        <div class="flex items-center justify-between gap-3">
                            <span class="mobile-map-chip">Mind Map</span>
                            <span class="mobile-map-chip" x-text="`${Math.round((structureMapZoom || 1) * 100)}%`"></span>
                        </div>
                    </div>

                    <div class="min-h-0 flex-1 px-3 pb-[calc(5.5rem+env(safe-area-inset-bottom))]">
                        <div class="mobile-map-stage-shell h-full">
                            <div x-ref="structureGraphContainer" class="mobile-map-graph"></div>
                        </div>
                    </div>

                    <div class="pointer-events-none fixed inset-x-0 bottom-0 z-[130] px-4 pb-[calc(0.9rem+env(safe-area-inset-bottom))]">
                        <div class="pointer-events-auto mx-auto flex w-full max-w-[18rem] items-center justify-center gap-2 mobile-map-dock p-2">
                            <button type="button" class="mobile-map-control" @click="zoomStructureMapOut()">
                                <i data-lucide="minus" class="h-4 w-4"></i>
                            </button>

                            <button type="button" class="mobile-map-control px-4" @click="resetStructureMapView()">Fit</button>

                            <button type="button" class="mobile-map-control" @click="zoomStructureMapIn()">
                                <i data-lucide="plus" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
