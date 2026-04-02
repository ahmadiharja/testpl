@extends('mobile.layouts.app')

@push('head')
    @include('mobile.partials.scope-detail-styles')
@endpush

@section('content')
    <div id="mobile-workstation-detail-screen" class="mobile-scope-screen scope-workstation">
        <div class="mobile-scope-loading" aria-hidden="true">
            <div class="mobile-scope-loading-card hero">
                <div class="mobile-scope-loading-chip"></div>
                <div class="mobile-scope-loading-line title"></div>
                <div class="mobile-scope-loading-line body"></div>
                <div class="mobile-scope-loading-line body short"></div>
                <div class="mobile-scope-loading-grid">
                    <div class="mobile-scope-loading-grid-cell"></div>
                    <div class="mobile-scope-loading-grid-cell"></div>
                    <div class="mobile-scope-loading-grid-cell"></div>
                </div>
            </div>
            <div class="mobile-scope-loading-metrics">
                <div class="mobile-scope-loading-card"><div class="mobile-scope-loading-line title"></div><div class="mobile-scope-loading-line body"></div></div>
                <div class="mobile-scope-loading-card"><div class="mobile-scope-loading-line title"></div><div class="mobile-scope-loading-line body"></div></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const boot = () => window.Perfectlum.mountMobilePage('mobileWorkstationDetail', () => {
                const root = document.getElementById('mobile-workstation-detail-screen');
                const loadingMarkup = root?.innerHTML || '';
                const workstationId = @json($workstationId);
                const displaysRoute = @json(route('mobile.displays'));
                const facilityDetailBase = @json(url('/m/facilities'));
                const workgroupDetailBase = @json(url('/m/workgroups'));
                const displayDetailBase = @json(url('/m/displays'));
                const escapeHtml = window.Perfectlum.escapeHtml;
                const returnTo = () => `${window.location.pathname}${window.location.search}`;
                const formatNumber = (value) => Number(value || 0).toLocaleString();

                const actionCard = ({ icon, title, copy, href }) => `
                    <a href="${href}" class="mobile-scope-action">
                        <i data-lucide="${icon}" class="h-4.5 w-4.5 text-sky-600"></i>
                        <p class="mobile-scope-action-title">${escapeHtml(title)}</p>
                        <p class="mobile-scope-action-copy">${escapeHtml(copy)}</p>
                    </a>
                `;

                const previewItem = ({ href, tone = 'scope', pill = '', meta = '', title = '', subtitle = '', stats = [], featured = false }) => `
                    <a href="${href}" class="mobile-scope-preview-item ${tone === 'alert' ? 'attention' : ''} ${featured ? 'featured' : ''}">
                        <div class="mobile-scope-preview-row">
                            <div class="min-w-0">
                                <div class="mobile-scope-preview-meta">
                                    ${pill ? `<span class="mobile-type-pill ${tone === 'alert' ? 'alert' : (tone === 'run' ? 'run' : 'sync')}">${escapeHtml(pill)}</span>` : ''}
                                    ${meta ? `<p class="mobile-meta mobile-clamp-1">${escapeHtml(meta)}</p>` : ''}
                                </div>
                                <p class="mobile-scope-preview-title">${escapeHtml(title)}</p>
                                ${subtitle ? `<p class="mobile-scope-preview-subtitle">${escapeHtml(subtitle)}</p>` : ''}
                            </div>
                            <span class="mobile-scope-preview-arrow">›</span>
                        </div>
                        ${stats.length ? `<div class="mobile-scope-preview-stats">${stats.map((stat) => `<span class="mobile-scope-stat-pill">${escapeHtml(stat)}</span>`).join('')}</div>` : ''}
                    </a>
                `;

                const renderEditorialList = (rows, mapper) => rows.map((row, index) => mapper(row, index === 0)).join('');

                const render = (detail) => {
                    const facility = detail?.facility?.name || 'Facility';
                    const workgroup = detail?.workgroup?.name || 'Workgroup';
                    const workstation = detail?.name || 'Workstation';
                    const displays = Array.isArray(detail?.displays) ? detail.displays : [];
                    const healthyCount = displays.filter((item) => item.statusTone === 'success').length;
                    const attentionCount = displays.filter((item) => item.statusTone === 'danger').length;
                    const onlineCount = displays.filter((item) => /online/i.test(item.connectedLabel || '')).length;
                    const leadAlert = displays.find((item) => item.statusTone === 'danger') || null;
                    const leadActivity = displays.find((item) => /online/i.test(item.connectedLabel || '')) || displays[0] || null;
                    const lastConnected = detail?.lastConnected && detail.lastConnected !== '-' ? detail.lastConnected : 'No sync data received';
                    const clientVersion = detail?.clientVersion && detail.clientVersion !== '-' ? detail.clientVersion : 'Client version not reported';
                    const syncState = detail?.lastConnected && detail.lastConnected !== '-' ? 'Live' : 'Stale';

                    root.innerHTML = `
                        <section class="mobile-scope-hero">
                            <div>
                                <p class="mobile-scope-kicker">Workstation scope</p>
                                <h2 class="mobile-scope-title">${escapeHtml(workstation)}</h2>
                                <p class="mobile-scope-copy">${escapeHtml(lastConnected)} • ${escapeHtml(clientVersion)}</p>
                            </div>
                            <article class="mobile-scope-card">
                                <div class="mobile-scope-card-top">
                                    <span class="mobile-scope-card-badge">Workstation</span>
                                    <span class="mobile-scope-card-context">${escapeHtml(syncState)}</span>
                                </div>
                                <p class="mobile-scope-card-title">${escapeHtml(workstation)}</p>
                                <p class="mobile-scope-card-value">${formatNumber(displays.length)}</p>
                                <p class="mobile-scope-card-caption">Attached displays currently tracked on this client.</p>
                                <div class="mobile-scope-card-grid">
                                    <div class="mobile-scope-card-stat">
                                        <p class="mobile-scope-card-stat-label">Healthy</p>
                                        <p class="mobile-scope-card-stat-value">${formatNumber(healthyCount)}</p>
                                    </div>
                                    <div class="mobile-scope-card-stat">
                                        <p class="mobile-scope-card-stat-label">Alerts</p>
                                        <p class="mobile-scope-card-stat-value">${formatNumber(attentionCount)}</p>
                                    </div>
                                    <div class="mobile-scope-card-stat">
                                        <p class="mobile-scope-card-stat-label">Online</p>
                                        <p class="mobile-scope-card-stat-value">${formatNumber(onlineCount)}</p>
                                    </div>
                                </div>
                            </article>
                            <div class="mobile-scope-chip-row">
                                <span class="mobile-scope-chip">${escapeHtml(workgroup)}</span>
                                <span class="mobile-scope-chip">${escapeHtml(facility)}</span>
                                <span class="mobile-scope-chip">${escapeHtml(syncState)}</span>
                            </div>
                        </section>

                        <section class="mobile-scope-metrics">
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Healthy</p>
                                <p class="mobile-scope-metric-value">${formatNumber(healthyCount)}</p>
                                <p class="mobile-scope-metric-note">Displays currently reporting healthy.</p>
                            </div>
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Attention</p>
                                <p class="mobile-scope-metric-value">${formatNumber(attentionCount)}</p>
                                <p class="mobile-scope-metric-note">Displays requiring intervention.</p>
                            </div>
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Online now</p>
                                <p class="mobile-scope-metric-value">${formatNumber(onlineCount)}</p>
                                <p class="mobile-scope-metric-note">Displays currently reachable in this scope.</p>
                            </div>
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Sync</p>
                                <p class="mobile-scope-metric-value">${escapeHtml(syncState)}</p>
                                <p class="mobile-scope-metric-note">${escapeHtml(lastConnected)}</p>
                            </div>
                        </section>

                        <section class="mobile-scope-pulse-grid">
                            <article class="mobile-scope-pulse">
                                <p class="mobile-scope-pulse-label">Attention now</p>
                                <p class="mobile-scope-pulse-title">${escapeHtml(leadAlert ? leadAlert.name : 'No current display alerts')}</p>
                                <p class="mobile-scope-pulse-copy">${escapeHtml(leadAlert ? (leadAlert.model && leadAlert.model !== '-' ? leadAlert.model : leadAlert.connectedLabel) : 'This workstation is currently clear of alerting displays.')}</p>
                                <span class="mobile-scope-pulse-pill alert">${formatNumber(attentionCount)} alerts</span>
                            </article>
                            <article class="mobile-scope-pulse">
                                <p class="mobile-scope-pulse-label">Recent activity</p>
                                <p class="mobile-scope-pulse-title">${escapeHtml(leadActivity ? leadActivity.name : 'No recent device signal')}</p>
                                <p class="mobile-scope-pulse-copy">${escapeHtml(leadActivity ? leadActivity.connectedLabel : 'Display status signals will appear here as this workstation syncs.')}</p>
                                <span class="mobile-scope-pulse-pill activity">${formatNumber(onlineCount)} online</span>
                            </article>
                        </section>

                        <section class="mobile-scope-actions" data-mobile-drag-scroll="1">
                            ${actionCard({
                                icon: 'building-2',
                                title: 'Facility',
                                copy: 'Open the parent facility dashboard.',
                                href: `${facilityDetailBase}/${detail.facility.id}?return_to=${encodeURIComponent(returnTo())}`,
                            })}
                            ${actionCard({
                                icon: 'folder-kanban',
                                title: 'Workgroup',
                                copy: 'Open the parent workgroup dashboard.',
                                href: `${workgroupDetailBase}/${detail.workgroup.id}?facility_id=${detail.facility.id}&facility_name=${encodeURIComponent(facility)}&workgroup_name=${encodeURIComponent(workgroup)}&return_to=${encodeURIComponent(returnTo())}`,
                            })}
                            ${actionCard({
                                icon: 'monitor',
                                title: 'Displays',
                                copy: 'Open every display on this workstation.',
                                href: `${displaysRoute}?facility_id=${detail.facility.id}&workgroup_id=${detail.workgroup.id}&workstation_id=${workstationId}&facility_name=${encodeURIComponent(facility)}&workgroup_name=${encodeURIComponent(workgroup)}&workstation_name=${encodeURIComponent(workstation)}&return_to=${encodeURIComponent(returnTo())}`,
                            })}
                        </section>

                        <section class="mobile-scope-section">
                            <div class="mobile-scope-section-top">
                                <div>
                                    <p class="mobile-scope-section-label">Attached displays</p>
                                    <h3 class="mobile-scope-section-title">Displays on this workstation</h3>
                                    <p class="mobile-scope-section-copy">Open a display detail or continue into the full display list.</p>
                                </div>
                                <a href="${displaysRoute}?facility_id=${detail.facility.id}&workgroup_id=${detail.workgroup.id}&workstation_id=${workstationId}&facility_name=${encodeURIComponent(facility)}&workgroup_name=${encodeURIComponent(workgroup)}&workstation_name=${encodeURIComponent(workstation)}&return_to=${encodeURIComponent(returnTo())}" class="mobile-scope-section-link">View all</a>
                            </div>
                            ${displays.length ? `<div class="mobile-scope-preview-list">
                                ${renderEditorialList(displays.slice(0, 5), (item, featured) => previewItem({
                                    href: `${displayDetailBase}/${item.id}?facility_id=${detail.facility.id}&workgroup_id=${detail.workgroup.id}&workstation_id=${workstationId}&facility_name=${encodeURIComponent(facility)}&workgroup_name=${encodeURIComponent(workgroup)}&workstation_name=${encodeURIComponent(workstation)}&return_to=${encodeURIComponent(returnTo())}`,
                                    tone: item.statusTone === 'danger' ? 'alert' : 'run',
                                    pill: item.statusLabel,
                                    meta: workstation,
                                    title: item.name,
                                    subtitle: item.model && item.model !== '-' ? item.model : item.connectedLabel,
                                    stats: [item.connectedLabel],
                                    featured,
                                }))}
                            </div>` : '<div class="mobile-scope-empty">No displays are attached to this workstation.</div>'}
                        </section>
                    `;

                    window.lucide?.createIcons();
                    window.Perfectlum?.bindMobileDragScroll?.(root);
                };

                const load = async () => {
                    root.innerHTML = loadingMarkup;

                    try {
                        const detail = await window.Perfectlum.request(`/api/workstation-modal/${workstationId}`);
                        render(detail);
                    } catch (error) {
                        root.innerHTML = `<div class="mobile-empty">${escapeHtml(error.message || 'Workstation overview could not be loaded.')}</div>`;
                    }
                };

                load();

                return () => {};
            });

            if (window.Perfectlum?.mountMobilePage) {
                boot();
                return;
            }

            (window.__perfectlumPageBoot = window.__perfectlumPageBoot || []).push(boot);
        })();
    </script>
@endpush
