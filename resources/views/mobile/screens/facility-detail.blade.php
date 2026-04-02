@extends('mobile.layouts.app')

@push('head')
    @include('mobile.partials.scope-detail-styles')
@endpush

@section('content')
    <div id="mobile-facility-detail-screen" class="mobile-scope-screen scope-facility">
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
            const boot = () => window.Perfectlum.mountMobilePage('mobileFacilityDetail', () => {
                const root = document.getElementById('mobile-facility-detail-screen');
                const loadingMarkup = root?.innerHTML || '';
                const facilityId = @json($facilityId);
                const workgroupsRoute = @json(route('mobile.workgroups'));
                const workstationsRoute = @json(route('mobile.workstations'));
                const displaysRoute = @json(route('mobile.displays'));
                const historiesRoute = @json(route('mobile.reports'));
                const workgroupDetailBase = @json(url('/m/workgroups'));
                const workstationDetailBase = @json(url('/m/workstations'));
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

                const displayErrorText = (errors) => {
                    if (!Array.isArray(errors) || !errors.length) {
                        return 'Needs attention';
                    }

                    const latest = errors[errors.length - 1];
                    if (typeof latest === 'string') {
                        return latest;
                    }

                    if (latest && typeof latest === 'object') {
                        return latest.message || latest.error || 'Needs attention';
                    }

                    return 'Needs attention';
                };

                const renderEditorialList = (rows, mapper) => rows.map((row, index) => mapper(row, index === 0)).join('');

                const render = ({ detail, workgroups, workstations, displaysTotal, alertDisplaysTotal, alertDisplays, latestPerformed }) => {
                    const facilityName = detail?.name || 'Facility';
                    const description = detail?.description || 'A top-level operational scope for workgroups, workstations, and displays.';
                    const location = detail?.location || 'Location not set';
                    const timezone = detail?.timezone || 'Timezone not set';
                    const healthyDisplaysTotal = Math.max(Number(displaysTotal || 0) - Number(alertDisplaysTotal || 0), 0);
                    const attentionRate = displaysTotal > 0 ? Math.round((alertDisplaysTotal / displaysTotal) * 100) : 0;
                    const leadAlert = alertDisplays[0] || null;
                    const leadActivity = latestPerformed[0] || null;

                    root.innerHTML = `
                        <section class="mobile-scope-hero">
                            <div>
                                <p class="mobile-scope-kicker">Facility scope</p>
                                <h2 class="mobile-scope-title">${escapeHtml(facilityName)}</h2>
                                <p class="mobile-scope-copy">${escapeHtml(description)}</p>
                            </div>
                            <article class="mobile-scope-card">
                                <div class="mobile-scope-card-top">
                                    <span class="mobile-scope-card-badge">Facility</span>
                                    <span class="mobile-scope-card-context">${escapeHtml(timezone)}</span>
                                </div>
                                <p class="mobile-scope-card-title">${escapeHtml(facilityName)}</p>
                                <p class="mobile-scope-card-value">${formatNumber(displaysTotal)}</p>
                                <p class="mobile-scope-card-caption">Managed displays across the facility network.</p>
                                <div class="mobile-scope-card-grid">
                                    <div class="mobile-scope-card-stat">
                                        <p class="mobile-scope-card-stat-label">Workgroups</p>
                                        <p class="mobile-scope-card-stat-value">${formatNumber(workgroups.total)}</p>
                                    </div>
                                    <div class="mobile-scope-card-stat">
                                        <p class="mobile-scope-card-stat-label">Clients</p>
                                        <p class="mobile-scope-card-stat-value">${formatNumber(workstations.total)}</p>
                                    </div>
                                    <div class="mobile-scope-card-stat">
                                        <p class="mobile-scope-card-stat-label">Alerts</p>
                                        <p class="mobile-scope-card-stat-value">${formatNumber(alertDisplaysTotal)}</p>
                                    </div>
                                </div>
                            </article>
                            <div class="mobile-scope-chip-row">
                                <span class="mobile-scope-chip">${escapeHtml(location)}</span>
                                <span class="mobile-scope-chip">${formatNumber(healthyDisplaysTotal)} healthy</span>
                                <span class="mobile-scope-chip">${attentionRate}% attention rate</span>
                            </div>
                        </section>

                        <section class="mobile-scope-metrics">
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Healthy</p>
                                <p class="mobile-scope-metric-value">${formatNumber(healthyDisplaysTotal)}</p>
                                <p class="mobile-scope-metric-note">Displays currently running clean.</p>
                            </div>
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Attention now</p>
                                <p class="mobile-scope-metric-value">${formatNumber(alertDisplaysTotal)}</p>
                                <p class="mobile-scope-metric-note">Displays currently requiring action.</p>
                            </div>
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Workgroups</p>
                                <p class="mobile-scope-metric-value">${formatNumber(workgroups.total)}</p>
                                <p class="mobile-scope-metric-note">Operational groups inside this facility.</p>
                            </div>
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Workstations</p>
                                <p class="mobile-scope-metric-value">${formatNumber(workstations.total)}</p>
                                <p class="mobile-scope-metric-note">Endpoint clients under this scope.</p>
                            </div>
                        </section>

                        <section class="mobile-scope-pulse-grid">
                            <article class="mobile-scope-pulse">
                                <p class="mobile-scope-pulse-label">Attention now</p>
                                <p class="mobile-scope-pulse-title">${escapeHtml(leadAlert ? leadAlert.displayName : 'No current display alerts')}</p>
                                <p class="mobile-scope-pulse-copy">${escapeHtml(leadAlert ? displayErrorText(leadAlert.errors) : 'This facility is currently clear of alerting displays.')}</p>
                                <span class="mobile-scope-pulse-pill alert">${formatNumber(alertDisplaysTotal)} alerts</span>
                            </article>
                            <article class="mobile-scope-pulse">
                                <p class="mobile-scope-pulse-label">Recent activity</p>
                                <p class="mobile-scope-pulse-title">${escapeHtml(leadActivity ? leadActivity.name : 'No recent completed activity')}</p>
                                <p class="mobile-scope-pulse-copy">${escapeHtml(leadActivity ? `${leadActivity.displayName} • ${leadActivity.timeFormatted}` : 'Completed calibration runs will appear here as operators report back.')}</p>
                                <span class="mobile-scope-pulse-pill activity">${formatNumber(latestPerformed.length)} recent runs</span>
                            </article>
                        </section>

                        <section class="mobile-scope-actions" data-mobile-drag-scroll="1">
                            ${actionCard({
                                icon: 'folders',
                                title: 'Workgroups',
                                copy: 'Open the full list in this facility.',
                                href: `${workgroupsRoute}?facility_id=${facilityId}&facility_name=${encodeURIComponent(facilityName)}&return_to=${encodeURIComponent(returnTo())}`,
                            })}
                            ${actionCard({
                                icon: 'monitor-smartphone',
                                title: 'Workstations',
                                copy: 'Browse clients inside this facility.',
                                href: `${workstationsRoute}?facility_id=${facilityId}&facility_name=${encodeURIComponent(facilityName)}&return_to=${encodeURIComponent(returnTo())}`,
                            })}
                            ${actionCard({
                                icon: 'monitor',
                                title: 'Displays',
                                copy: 'Open the full display inventory here.',
                                href: `${displaysRoute}?facility_id=${facilityId}&facility_name=${encodeURIComponent(facilityName)}&return_to=${encodeURIComponent(returnTo())}`,
                            })}
                        </section>

                        <section class="mobile-scope-section">
                            <div class="mobile-scope-section-top">
                                <div>
                                    <p class="mobile-scope-section-label">Child scopes</p>
                                    <h3 class="mobile-scope-section-title">Workgroups in this facility</h3>
                                    <p class="mobile-scope-section-copy">Open a workgroup dashboard or continue to the full list.</p>
                                </div>
                                <a href="${workgroupsRoute}?facility_id=${facilityId}&facility_name=${encodeURIComponent(facilityName)}&return_to=${encodeURIComponent(returnTo())}" class="mobile-scope-section-link">View all</a>
                            </div>
                            ${workgroups.rows.length ? `<div class="mobile-scope-preview-list">
                                ${renderEditorialList(workgroups.rows, (item, featured) => previewItem({
                                    href: `${workgroupDetailBase}/${item.id}?facility_id=${facilityId}&facility_name=${encodeURIComponent(facilityName)}&workgroup_name=${encodeURIComponent(item.name)}&return_to=${encodeURIComponent(returnTo())}`,
                                    tone: item.displayHealth === 'failed' ? 'alert' : (item.displayHealth === 'ok' ? 'run' : 'scope'),
                                    pill: item.displayHealth === 'failed' ? 'Alert' : (item.displayHealth === 'ok' ? 'Healthy' : 'Workgroup'),
                                    meta: facilityName,
                                    title: item.name,
                                    subtitle: [item.address, item.phone].filter((value) => value && value !== '-').join(' • ') || 'No address or phone saved',
                                    stats: [`${item.workstationsCount} workstations`, `${item.displaysCount} displays`],
                                    featured,
                                }))}
                            </div>` : '<div class="mobile-scope-empty">No workgroups are available in this facility.</div>'}
                        </section>

                        <section class="mobile-scope-section">
                            <div class="mobile-scope-section-top">
                                <div>
                                    <p class="mobile-scope-section-label">Client view</p>
                                    <h3 class="mobile-scope-section-title">Workstations at a glance</h3>
                                    <p class="mobile-scope-section-copy">Open a workstation scope or continue into the workstation list.</p>
                                </div>
                                <a href="${workstationsRoute}?facility_id=${facilityId}&facility_name=${encodeURIComponent(facilityName)}&return_to=${encodeURIComponent(returnTo())}" class="mobile-scope-section-link">View all</a>
                            </div>
                            ${workstations.rows.length ? `<div class="mobile-scope-preview-list">
                                ${renderEditorialList(workstations.rows, (item, featured) => previewItem({
                                    href: `${workstationDetailBase}/${item.id}?facility_id=${facilityId}&facility_name=${encodeURIComponent(facilityName)}&workgroup_id=${item.wgId}&workgroup_name=${encodeURIComponent(item.wgName)}&return_to=${encodeURIComponent(returnTo())}`,
                                    tone: item.displayHealth === 'failed' ? 'alert' : (item.displayHealth === 'ok' ? 'run' : 'scope'),
                                    pill: item.displayHealth === 'failed' ? 'Alert' : (item.displayHealth === 'ok' ? 'Healthy' : 'Sync'),
                                    meta: `${item.wgName} • ${facilityName}`,
                                    title: item.name,
                                    subtitle: item.lastConnected !== '-' ? `Last sync ${item.lastSeenRelative}` : 'No sync data received',
                                    stats: [`${item.displaysCount} displays`, `${item.failedDisplaysCount} alerts`],
                                    featured,
                                }))}
                            </div>` : '<div class="mobile-scope-empty">No workstations are available in this facility.</div>'}
                        </section>

                        <section class="mobile-scope-section">
                            <div class="mobile-scope-section-top">
                                <div>
                                    <p class="mobile-scope-section-label">Attention</p>
                                    <h3 class="mobile-scope-section-title">Displays needing attention</h3>
                                    <p class="mobile-scope-section-copy">The most urgent displays in this facility, ordered by latest update.</p>
                                </div>
                                <a href="${displaysRoute}?facility_id=${facilityId}&facility_name=${encodeURIComponent(facilityName)}&status=2&sort=updated_at&order=desc&return_to=${encodeURIComponent(returnTo())}" class="mobile-scope-section-link">View all</a>
                            </div>
                            ${alertDisplays.length ? `<div class="mobile-scope-preview-list">
                                ${renderEditorialList(alertDisplays, (item, featured) => previewItem({
                                    href: `${displayDetailBase}/${item.id}?facility_id=${item.facId}&workgroup_id=${item.wgId}&workstation_id=${item.wsId}&facility_name=${encodeURIComponent(item.facName)}&workgroup_name=${encodeURIComponent(item.wgName)}&workstation_name=${encodeURIComponent(item.wsName)}&status=2&sort=updated_at&order=desc&return_to=${encodeURIComponent(returnTo())}`,
                                    tone: 'alert',
                                    pill: 'Alert',
                                    meta: item.updatedAt,
                                    title: item.displayName,
                                    subtitle: displayErrorText(item.errors),
                                    stats: [item.wsName, item.wgName],
                                    featured,
                                }))}
                            </div>` : '<div class="mobile-scope-empty">No alerting displays in this facility right now.</div>'}
                        </section>

                        <section class="mobile-scope-section">
                            <div class="mobile-scope-section-top">
                                <div>
                                    <p class="mobile-scope-section-label">Recent activity</p>
                                    <h3 class="mobile-scope-section-title">Latest completed work</h3>
                                    <p class="mobile-scope-section-copy">Recently completed calibration activity reported from this facility.</p>
                                </div>
                                <a href="${historiesRoute}?return_to=${encodeURIComponent(returnTo())}" class="mobile-scope-section-link">View all</a>
                            </div>
                            ${latestPerformed.length ? `<div class="mobile-scope-preview-list">
                                ${renderEditorialList(latestPerformed, (item, featured) => previewItem({
                                    href: `${displayDetailBase}/${item.displayId}?facility_name=${encodeURIComponent(item.facName)}&workgroup_name=${encodeURIComponent(item.wgName)}&workstation_name=${encodeURIComponent(item.wsName)}&return_to=${encodeURIComponent(returnTo())}`,
                                    tone: item.result === 'fail' ? 'alert' : 'run',
                                    pill: item.result === 'fail' ? 'Fail' : 'Pass',
                                    meta: item.timeFormatted,
                                    title: item.name,
                                    subtitle: `${item.displayName} • ${item.wsName}`,
                                    stats: [item.wgName, item.facName],
                                    featured,
                                }))}
                            </div>` : '<div class="mobile-scope-empty">No recent completed activity has been reported in this facility.</div>'}
                        </section>
                    `;

                    window.lucide?.createIcons();
                    window.Perfectlum?.bindMobileDragScroll?.(root);
                };

                const load = async () => {
                    root.innerHTML = loadingMarkup;

                    try {
                        const [detail, workgroupsResponse, workstationsResponse, displaysResponse, alertDisplaysResponse, latestPerformedResponse] = await Promise.all([
                            window.Perfectlum.request(`/api/facility-modal/${facilityId}`),
                            window.Perfectlum.request(`/api/workgroups?facility_id=${facilityId}&limit=3&page=1`),
                            window.Perfectlum.request(`/api/workstations?facility_id=${facilityId}&limit=3&page=1`),
                            window.Perfectlum.request(`/api/displays?facility_id=${facilityId}&limit=1&page=1`),
                            window.Perfectlum.request(`/api/displays?facility_id=${facilityId}&status=2&sort=updated_at&order=desc&limit=3&page=1`),
                            window.Perfectlum.request(`/api/latest-performed?facility_id=${facilityId}&limit=3`),
                        ]);

                        render({
                            detail,
                            workgroups: {
                                rows: Array.isArray(workgroupsResponse.data) ? workgroupsResponse.data : [],
                                total: Number(workgroupsResponse.total || detail?.summary?.workgroupCount || 0),
                            },
                            workstations: {
                                rows: Array.isArray(workstationsResponse.data) ? workstationsResponse.data : [],
                                total: Number(workstationsResponse.total || 0),
                            },
                            displaysTotal: Number(displaysResponse.total || 0),
                            alertDisplaysTotal: Number(alertDisplaysResponse.total || 0),
                            alertDisplays: Array.isArray(alertDisplaysResponse.data) ? alertDisplaysResponse.data : [],
                            latestPerformed: Array.isArray(latestPerformedResponse) ? latestPerformedResponse : [],
                        });
                    } catch (error) {
                        root.innerHTML = `<div class="mobile-empty">${escapeHtml(error.message || 'Facility overview could not be loaded.')}</div>`;
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
