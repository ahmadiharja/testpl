@extends('mobile.layouts.app')

@push('head')
    <style>
        @keyframes mobile-reports-shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .mobile-reports-hero {
            position: relative;
            overflow: hidden;
            padding: 0.98rem;
            border-color: rgba(203, 213, 225, 0.78);
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.12), transparent 34%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.95));
        }

        .mobile-reports-kicker {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-reports-title {
            margin-top: 0.42rem;
            font-size: 1.22rem;
            font-weight: 760;
            letter-spacing: -0.036em;
            line-height: 1.04;
            color: #0f172a;
        }

        .mobile-reports-copy {
            margin-top: 0.45rem;
            max-width: 18rem;
            font-size: 12.5px;
            line-height: 1.52;
            color: #475569;
        }

        .mobile-reports-chip-row {
            margin-top: 0.75rem;
            display: flex;
            gap: 0.45rem;
            overflow-x: auto;
            padding-bottom: 0.05rem;
        }

        .mobile-reports-chip {
            flex: 0 0 auto;
            border-radius: 999px;
            border: 1px solid rgba(186, 230, 253, 0.82);
            background: rgba(239, 246, 255, 0.88);
            padding: 0.4rem 0.68rem;
            font-size: 10.5px;
            font-weight: 700;
            color: #0369a1;
        }

        .mobile-report-run-card {
            display: block;
            width: 100%;
            border: 0;
            background: transparent;
            padding: 0.84rem 0.86rem;
            text-align: left;
            transition: background 160ms ease, transform 160ms ease;
        }

        .mobile-report-run-card:active {
            background: rgba(14, 165, 233, 0.05);
            transform: scale(0.995);
        }

        .mobile-report-run-card + .mobile-report-run-card {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mobile-report-run-title {
            margin-top: 0.28rem;
            font-size: 13px;
            font-weight: 680;
            line-height: 1.32;
            color: #0f172a;
        }

        .mobile-report-run-display {
            margin-top: 0.26rem;
            font-size: 12px;
            line-height: 1.42;
            color: #64748b;
        }

        .mobile-report-run-scope {
            margin-top: 0.22rem;
            font-size: 12px;
            line-height: 1.46;
            color: #475569;
        }

        .mobile-report-run-time {
            flex: 0 0 auto;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(248, 250, 252, 0.96);
            padding: 0.34rem 0.58rem;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
        }

        .mobile-report-run-skeleton {
            position: relative;
            display: block;
            padding: 0.84rem 0.86rem;
            overflow: hidden;
        }

        .mobile-report-run-skeleton + .mobile-report-run-skeleton {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mobile-report-run-skeleton::after {
            content: '';
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.85), transparent);
            animation: mobile-reports-shimmer 1.2s infinite;
        }

        .mobile-report-skeleton-pill,
        .mobile-report-skeleton-line,
        .mobile-report-skeleton-time {
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(226, 232, 240, 0.82), rgba(241, 245, 249, 0.92));
        }

        .mobile-report-skeleton-pill {
            height: 1.18rem;
            width: 4.1rem;
        }

        .mobile-report-skeleton-line {
            height: 0.82rem;
            margin-top: 0.36rem;
        }

        .mobile-report-skeleton-line.title {
            width: 74%;
            height: 0.96rem;
            margin-top: 0.5rem;
        }

        .mobile-report-skeleton-line.display {
            width: 62%;
        }

        .mobile-report-skeleton-line.scope {
            width: 68%;
        }

        .mobile-report-skeleton-time {
            flex: 0 0 auto;
            width: 4.55rem;
            height: 1.5rem;
        }

        .mobile-report-detail-backdrop {
            background: rgba(15, 23, 42, 0.28);
            backdrop-filter: blur(8px);
        }

        .mobile-report-detail-shell {
            position: absolute;
            inset: 1.25rem 0 0;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.985), rgba(248, 250, 252, 0.985));
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 0 -22px 48px rgba(15, 23, 42, 0.16);
            overflow: hidden;
        }

        .mobile-report-detail-topbar {
            padding: 0.7rem 1rem 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 255, 255, 0.9));
        }

        .mobile-report-detail-handle {
            height: 0.28rem;
            width: 2.9rem;
            margin: 0 auto 0.7rem;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.45);
        }

        .mobile-report-detail-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            padding-bottom: 0.9rem;
        }

        .mobile-report-detail-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 2.35rem;
            width: 2.35rem;
            flex: 0 0 auto;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(255, 255, 255, 0.96);
            color: #475569;
        }

        .mobile-report-detail-kicker {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-report-detail-title {
            margin-top: 0.38rem;
            font-size: 1.1rem;
            font-weight: 760;
            line-height: 1.08;
            letter-spacing: -0.03em;
            color: #0f172a;
        }

        .mobile-report-detail-subtitle {
            margin-top: 0.28rem;
            font-size: 12px;
            line-height: 1.45;
            color: #64748b;
        }

        .mobile-report-detail-body {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 0 1rem calc(1rem + env(safe-area-inset-bottom));
        }

        .mobile-report-detail-status {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(248, 250, 252, 0.9);
            padding: 0.85rem 0.9rem;
        }

        .mobile-report-detail-status-copy {
            font-size: 12px;
            line-height: 1.45;
            color: #64748b;
        }

        .mobile-report-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.55rem;
        }

        .mobile-report-detail-info {
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.92);
            padding: 0.8rem 0.82rem;
        }

        .mobile-report-detail-label {
            font-size: 9.5px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-report-detail-value {
            margin-top: 0.3rem;
            font-size: 12.5px;
            line-height: 1.42;
            color: #0f172a;
            word-break: break-word;
        }

        .mobile-report-detail-section {
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.92);
            padding: 0.9rem;
        }

        .mobile-report-detail-section-title {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.35;
            color: #0f172a;
        }

        .mobile-report-detail-score,
        .mobile-report-detail-question {
            border-radius: 0.95rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(248, 250, 252, 0.92);
            padding: 0.75rem 0.82rem;
        }

        .mobile-report-detail-score-value {
            font-size: 11px;
            line-height: 1.45;
            color: #64748b;
        }

        .mobile-report-detail-comment {
            border-radius: 0.95rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(248, 250, 252, 0.92);
            padding: 0.82rem;
            font-size: 12px;
            line-height: 1.55;
            color: #475569;
        }

        .mobile-report-detail-actions {
            position: sticky;
            bottom: 0;
            display: flex;
            gap: 0.6rem;
            padding: 0.8rem 1rem calc(0.8rem + env(safe-area-inset-bottom));
            margin: 0 -1rem calc(-1rem - env(safe-area-inset-bottom));
            background: linear-gradient(180deg, rgba(248, 250, 252, 0), rgba(248, 250, 252, 0.98) 24%, rgba(248, 250, 252, 1));
        }

        .mobile-report-detail-action {
            flex: 1 1 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            min-height: 2.7rem;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .mobile-report-detail-action.secondary {
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(255, 255, 255, 0.96);
            color: #334155;
        }

        .mobile-report-detail-action.primary {
            border: 1px solid rgba(125, 211, 252, 0.4);
            background: linear-gradient(180deg, rgba(14, 165, 233, 0.95), rgba(2, 132, 199, 0.96));
            color: white;
            box-shadow: 0 16px 30px rgba(14, 165, 233, 0.24);
        }
    </style>
@endpush

@section('content')
    <section class="mobile-panel mobile-reports-hero">
        <p class="mobile-reports-kicker">History &amp; Reports</p>
        <h2 class="mobile-reports-title">Calibration and QA history</h2>
        <p class="mobile-reports-copy">Browse calibration history records, review failed runs quickly, and open scored report summaries without leaving mobile.</p>
        <div class="mobile-reports-chip-row no-scrollbar">
            <span class="mobile-reports-chip">Same report source as desktop</span>
            <span class="mobile-reports-chip">History summaries on tap</span>
            <span class="mobile-reports-chip">Calibration history records</span>
        </div>
    </section>

    <div class="mobile-search-shell">
        <div class="mobile-searchbar">
            <i data-lucide="search" class="mobile-searchbar-icon"></i>
            <input id="mobile-reports-search" type="search" class="mobile-input mobile-search-input" placeholder="Search reports and runs">
        </div>
    </div>

    <section class="mobile-section-gap">
        <div id="mobile-reports-list" class="mobile-stack">
            <div class="mobile-report-run-skeleton" aria-hidden="true">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="mobile-report-skeleton-pill"></div>
                        <div class="mobile-report-skeleton-line title"></div>
                        <div class="mobile-report-skeleton-line display"></div>
                        <div class="mobile-report-skeleton-line scope"></div>
                    </div>
                    <div class="mobile-report-skeleton-time"></div>
                </div>
            </div>
            <div class="mobile-report-run-skeleton" aria-hidden="true">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="mobile-report-skeleton-pill"></div>
                        <div class="mobile-report-skeleton-line title"></div>
                        <div class="mobile-report-skeleton-line display"></div>
                        <div class="mobile-report-skeleton-line scope"></div>
                    </div>
                    <div class="mobile-report-skeleton-time"></div>
                </div>
            </div>
        </div>
        <div id="mobile-reports-pagination" class="mt-3"></div>
    </section>

    @push('modals')
        <div id="mobile-report-detail-modal" class="fixed inset-0 z-[140] hidden">
            <div data-report-detail-backdrop class="absolute inset-0 mobile-report-detail-backdrop opacity-0 transition-opacity duration-200"></div>
            <div data-report-detail-panel class="mobile-report-detail-shell translate-y-4 opacity-0 transition-all duration-200">
                <div class="mobile-report-detail-topbar">
                    <div class="mobile-report-detail-handle"></div>
                    <div class="mobile-report-detail-header">
                        <div class="min-w-0 flex-1">
                            <p class="mobile-report-detail-kicker">History summary</p>
                            <h3 id="mobile-report-detail-title" class="mobile-report-detail-title">Loading report summary</h3>
                            <p id="mobile-report-detail-subtitle" class="mobile-report-detail-subtitle">Preparing structured report details.</p>
                        </div>
                        <button type="button" data-report-detail-close class="mobile-report-detail-close">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
                <div id="mobile-report-detail-body" class="mobile-report-detail-body">
                    <div class="mobile-empty">Loading report summary...</div>
                </div>
            </div>
        </div>
    @endpush

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileReports', () => {
                    const list = document.getElementById('mobile-reports-list');
                    const pagination = document.getElementById('mobile-reports-pagination');
                    const searchInput = document.getElementById('mobile-reports-search');
                    const perPage = 10;
                    const cache = new Map();
                    const rowIndex = new Map();
                    let requestToken = 0;
                    let timer = null;
                    let currentPage = 1;
                    let currentKeyword = '';
                    let activeDetailId = null;
                    let lockedScrollY = 0;

                    const modalRoot = document.getElementById('mobile-report-detail-modal');
                    const modalBackdrop = modalRoot?.querySelector('[data-report-detail-backdrop]');
                    const modalPanel = modalRoot?.querySelector('[data-report-detail-panel]');
                    const modalTitle = document.getElementById('mobile-report-detail-title');
                    const modalSubtitle = document.getElementById('mobile-report-detail-subtitle');
                    const modalBody = document.getElementById('mobile-report-detail-body');
                    const modalCloseButtons = Array.from(modalRoot?.querySelectorAll('[data-report-detail-close]') || []);

                    const loadingState = () => Array.from({ length: 4 }).map(() => `
                        <div class="mobile-report-run-skeleton" aria-hidden="true">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="mobile-report-skeleton-pill"></div>
                                    <div class="mobile-report-skeleton-line title"></div>
                                    <div class="mobile-report-skeleton-line display"></div>
                                    <div class="mobile-report-skeleton-line scope"></div>
                                </div>
                                <div class="mobile-report-skeleton-time"></div>
                            </div>
                        </div>
                    `).join('');
                    const emptyState = (message) => `<div class="mobile-empty">${message}</div>`;
                    const escapeHtml = window.Perfectlum.escapeHtml;
                    const returnTo = () => `${window.location.pathname}${window.location.search}`;
                    const displayUrl = (displayId) => `${@json(url('/m/displays'))}/${displayId}?return_to=${encodeURIComponent(returnTo())}`;

                    const lockScroll = () => {
                        lockedScrollY = window.scrollY || window.pageYOffset || 0;
                        document.documentElement.style.overflow = 'hidden';
                        document.body.style.overflow = 'hidden';
                    };

                    const unlockScroll = () => {
                        document.documentElement.style.overflow = '';
                        document.body.style.overflow = '';
                        window.scrollTo(0, lockedScrollY);
                    };

                    const renderBadge = (label, tone = 'neutral') => {
                        const className = tone === 'success'
                            ? 'run'
                            : (tone === 'danger' ? 'alert' : 'due');

                        return `<span class="mobile-type-pill ${className}">${escapeHtml(label || '-')}</span>`;
                    };

                    const renderInfoGrid = (items) => `
                        <div class="mobile-report-detail-grid">
                            ${items.map((item) => `
                                <div class="mobile-report-detail-info">
                                    <p class="mobile-report-detail-label">${escapeHtml(item.label)}</p>
                                    <p class="mobile-report-detail-value">${escapeHtml(item.value || '-')}</p>
                                </div>
                            `).join('')}
                        </div>
                    `;

                    const renderSection = (section) => {
                        const scores = Array.isArray(section?.scores) ? section.scores : [];
                        const questions = Array.isArray(section?.questions) ? section.questions : [];
                        const comment = section?.comment || '';

                        return `
                            <section class="mobile-report-detail-section">
                                <p class="mobile-report-detail-section-title">${escapeHtml(section?.name || 'Section')}</p>
                                ${scores.length ? `
                                    <div class="mt-3 space-y-3">
                                        ${scores.map((score) => `
                                            <div class="mobile-report-detail-score">
                                                <div class="flex items-start justify-between gap-3">
                                                    <p class="text-[12.5px] font-semibold leading-5 text-slate-900">${escapeHtml(score.name || '-')}</p>
                                                    ${renderBadge(score.statusLabel || '-', score.statusTone || 'neutral')}
                                                </div>
                                                <div class="mt-2 grid grid-cols-2 gap-2">
                                                    <div>
                                                        <p class="mobile-report-detail-label">Limit</p>
                                                        <p class="mobile-report-detail-score-value">${escapeHtml(score.limit || '-')}</p>
                                                    </div>
                                                    <div>
                                                        <p class="mobile-report-detail-label">Measured</p>
                                                        <p class="mobile-report-detail-score-value">${escapeHtml(score.measured || '-')}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                ` : ''}
                                ${questions.length ? `
                                    <div class="mt-3 space-y-3">
                                        ${questions.map((question) => `
                                            <div class="mobile-report-detail-question">
                                                <div class="flex items-start justify-between gap-3">
                                                    <p class="text-[12.5px] font-semibold leading-5 text-slate-900">${escapeHtml(question.text || '-')}</p>
                                                    ${renderBadge(question.answer || '-', question.tone || 'neutral')}
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                ` : ''}
                                ${comment ? `
                                    <div class="mt-3 mobile-report-detail-comment">
                                        ${escapeHtml(comment)}
                                    </div>
                                ` : ''}
                            </section>
                        `;
                    };

                    const openDetailSkeleton = (item) => {
                        if (!modalRoot || !modalBackdrop || !modalPanel || !modalTitle || !modalSubtitle || !modalBody) {
                            return;
                        }

                        activeDetailId = item.id;
                        modalTitle.textContent = item.name || 'History summary';
                        modalSubtitle.textContent = `${item.time || '-'} • ${item.displayName || '-'}`;
                        modalBody.innerHTML = `
                            <div class="space-y-4 pb-6">
                                <div class="mobile-report-detail-status">
                                    ${renderBadge(item.result === 'passed' ? 'Passed' : 'Failed', item.result === 'passed' ? 'success' : 'danger')}
                                    <span class="mobile-report-detail-status-copy">Detailed summary for the selected task execution.</span>
                                </div>
                                <div class="mobile-empty">Loading report summary...</div>
                            </div>
                        `;

                        modalRoot.classList.remove('hidden');
                        lockScroll();
                        requestAnimationFrame(() => {
                            modalBackdrop.classList.remove('opacity-0');
                            modalPanel.classList.remove('translate-y-4', 'opacity-0');
                        });
                    };

                    const closeDetail = () => {
                        if (!modalRoot || !modalBackdrop || !modalPanel || modalRoot.classList.contains('hidden')) {
                            return;
                        }

                        activeDetailId = null;
                        modalBackdrop.classList.add('opacity-0');
                        modalPanel.classList.add('translate-y-4', 'opacity-0');
                        window.setTimeout(() => {
                            modalRoot.classList.add('hidden');
                            unlockScroll();
                        }, 180);
                    };

                    const renderDetail = (item, payload) => {
                        if (!modalTitle || !modalSubtitle || !modalBody) {
                            return;
                        }

                        modalTitle.textContent = payload.name || item.name || 'History summary';
                        modalSubtitle.textContent = `${payload.performedAt || item.time || '-'} • ${payload.display?.display || item.displayName || '-'}`;

                        const displayInfo = [
                            { label: 'Facility', value: payload.display?.facility || '-' },
                            { label: 'Workgroup', value: payload.display?.workgroup || '-' },
                            { label: 'Workstation', value: payload.display?.workstation || '-' },
                            { label: 'Display', value: payload.display?.display || '-' },
                            { label: 'Performed', value: payload.performedAt || '-' },
                            { label: 'Result', value: payload.resultLabel || '-' },
                        ];

                        const printHref = payload.printUrl || '#';
                        const openDisplayHref = item.displayId ? displayUrl(item.displayId) : '#';

                        modalBody.innerHTML = `
                            <div class="space-y-4 pb-6">
                                <section class="mobile-report-detail-status">
                                    ${renderBadge(payload.resultLabel || 'Unknown', payload.resultTone || 'neutral')}
                                    <span class="mobile-report-detail-status-copy">Detailed summary for the selected task execution.</span>
                                </section>
                                ${renderInfoGrid(displayInfo)}
                                ${Array.isArray(payload.header) && payload.header.length ? renderInfoGrid(payload.header) : ''}
                                ${Array.isArray(payload.sections) && payload.sections.length
                                    ? payload.sections.map(renderSection).join('')
                                    : '<div class="mobile-empty">No structured summary is available for this history record.</div>'}
                                <div class="mobile-report-detail-actions">
                                    <a href="${escapeHtml(printHref)}" target="_blank" rel="noopener" class="mobile-report-detail-action secondary">
                                        <i data-lucide="printer" class="h-4 w-4"></i>
                                        <span>Print preview</span>
                                    </a>
                                    <a href="${escapeHtml(openDisplayHref)}" class="mobile-report-detail-action primary">
                                        <i data-lucide="monitor" class="h-4 w-4"></i>
                                        <span>Open display</span>
                                    </a>
                                </div>
                            </div>
                        `;

                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    };

                    async function loadReportDetail(id) {
                        const item = rowIndex.get(String(id));
                        if (!item) {
                            return;
                        }

                        openDetailSkeleton(item);

                        try {
                            const payload = await window.Perfectlum.request(`/api/history-modal/${id}`);
                            if (String(activeDetailId) !== String(id)) {
                                return;
                            }

                            renderDetail(item, payload);
                        } catch (error) {
                            if (!modalBody || String(activeDetailId) !== String(id)) {
                                return;
                            }

                            modalBody.innerHTML = '<div class="mobile-empty">Unable to load this history summary right now.</div>';
                        }
                    }

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

                    async function loadReports(keyword = '', page = 1) {
                        currentKeyword = keyword;
                        currentPage = page;
                        const cacheKey = `${keyword}::${page}`;
                        const cached = cache.get(cacheKey);
                        const currentRequest = ++requestToken;

                        if (cached) {
                            list.innerHTML = cached.html;
                            pagination.innerHTML = cached.pager;
                            return;
                        }

                        list.innerHTML = loadingState();
                        pagination.innerHTML = '';

                        try {
                            const response = await window.Perfectlum.request(`/api/histories?limit=${perPage}&page=${page}${keyword ? `&search=${encodeURIComponent(keyword)}` : ''}`);
                            if (currentRequest !== requestToken) {
                                return;
                            }

                            const rows = response.data || [];
                            const total = Number(response.total || 0);
                            rowIndex.clear();

                            if (!rows.length) {
                                list.innerHTML = emptyState('No report runs matched this filter.');
                                return;
                            }

                            const html = rows.map((item) => {
                                rowIndex.set(String(item.id), item);
                                return `
                                    <button type="button" data-report-open="${item.id}" class="mobile-report-run-card w-full text-left">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="mobile-type-pill ${item.result === 'passed' ? 'run' : 'alert'}">${item.result === 'passed' ? 'Pass' : 'Fail'}</span>
                                                    <p class="mobile-meta mobile-clamp-1">${escapeHtml(item.displayName)}</p>
                                                </div>
                                                <p class="mobile-report-run-title">${escapeHtml(item.name)}</p>
                                                <p class="mobile-report-run-display">${escapeHtml(item.pattern && item.pattern !== '-' ? item.pattern : item.displayName)}</p>
                                                <p class="mobile-report-run-scope">${escapeHtml([item.wsName, item.wgName, item.facName].filter(Boolean).join(' • '))}</p>
                                            </div>
                                            <span class="mobile-report-run-time">${escapeHtml(item.time)}</span>
                                        </div>
                                    </button>
                                `;
                            }).join('');
                            const pager = renderPager(total, page, perPage);
                            cache.set(cacheKey, { html, pager });
                            list.innerHTML = html;
                            pagination.innerHTML = pager;
                        } catch (error) {
                            if (currentRequest !== requestToken) {
                                return;
                            }

                            list.innerHTML = emptyState('Unable to load reports right now.');
                        }
                    }

                    list.addEventListener('click', (event) => {
                        const trigger = event.target.closest('[data-report-open]');
                        if (!trigger) {
                            return;
                        }

                        loadReportDetail(trigger.dataset.reportOpen);
                    });

                    pagination.addEventListener('click', (event) => {
                        const button = event.target.closest('[data-page]');
                        if (!button || button.hasAttribute('disabled')) {
                            return;
                        }

                        loadReports(currentKeyword, Number(button.dataset.page));
                    });

                    searchInput.addEventListener('input', () => {
                        window.clearTimeout(timer);
                        timer = window.setTimeout(() => loadReports(searchInput.value.trim(), 1), 220);
                    });

                    modalCloseButtons.forEach((button) => button.addEventListener('click', closeDetail));
                    modalBackdrop?.addEventListener('click', closeDetail);

                    loadReports();
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
