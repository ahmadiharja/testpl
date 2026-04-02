@extends('mobile.layouts.app')

@push('head')
    @include('mobile.partials.scope-detail-styles')
@endpush

@section('content')
    <div id="mobile-workgroup-detail-screen" class="mobile-scope-screen scope-workgroup">
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
            const boot = () => window.Perfectlum.mountMobilePage('mobileWorkgroupDetail', () => {
                const root = document.getElementById('mobile-workgroup-detail-screen');
                const loadingMarkup = root?.innerHTML || '';
                const workgroupId = @json($workgroupId);
                const workstationsRoute = @json(route('mobile.workstations'));
                const displaysRoute = @json(route('mobile.displays'));
                const facilityDetailBase = @json(url('/m/facilities'));
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
                    if (!Array.isArray(errors) || !errors.length) return 'Needs attention';
                    const latest = errors[errors.length - 1];
                    if (typeof latest === 'string') return latest;
                    if (latest && typeof latest === 'object') return latest.message || latest.error || 'Needs attention';
                    return 'Needs attention';
                };

                const renderEditorialList = (rows, mapper) => rows.map((row, index) => mapper(row, index === 0)).join('');

                const render = ({ detail, alertDisplays }) => {
                    const facility = detail?.facility?.name || 'Facility';
                    const workgroup = detail?.name || 'Workgroup';
                    const address = detail?.address && detail.address !== '-' ? detail.address : 'Address not set';
                    const phone = detail?.phone && detail.phone !== '-' ? detail.phone : 'Phone not set';
                    const summary = detail?.summary || {};
                    const workstations = Array.isArray(detail?.workstations) ? detail.workstations : [];
                    const workstationCount = Number(summary.workstationCount || 0);
                    const displayCount = Number(summary.displayCount || 0);
                    const healthyCount = Number(summary.healthyCount || 0);
                    const attentionCount = Number(summary.attentionCount || 0);
                    const healthRate = displayCount > 0 ? Math.round((healthyCount / displayCount) * 100) : 0;
                    const leadAlert = alertDisplays[0] || null;
                    const leadActivity = workstations.find((item) => item.lastConnected && item.lastConnected !== '-') || workstations[0] || null;

                    root.innerHTML = `
                        <section class="mobile-scope-hero">
                            <div>
                                <p class="mobile-scope-kicker">Workgroup scope</p>
                                <h2 class="mobile-scope-title">${escapeHtml(workgroup)}</h2>
                                <p class="mobile-scope-copy">${escapeHtml(address)}${phone !== 'Phone not set' ? ` • ${escapeHtml(phone)}` : ''}</p>
                            </div>
                            <article class="mobile-scope-card">
                                <div class="mobile-scope-card-top">
                                    <span class="mobile-scope-card-badge">Workgroup</span>
                                    <span class="mobile-scope-card-context">${escapeHtml(facility)}</span>
                                </div>
                                <p class="mobile-scope-card-title">${escapeHtml(workgroup)}</p>
                                <p class="mobile-scope-card-value">${formatNumber(displayCount)}</p>
                                <p class="mobile-scope-card-caption">Managed displays inside this workgroup scope.</p>
                                <div class="mobile-scope-card-grid">
                                    <div class="mobile-scope-card-stat">
                                        <p class="mobile-scope-card-stat-label">Clients</p>
                                        <p class="mobile-scope-card-stat-value">${formatNumber(workstationCount)}</p>
                                    </div>
                                    <div class="mobile-scope-card-stat">
                                        <p class="mobile-scope-card-stat-label">Healthy</p>
                                        <p class="mobile-scope-card-stat-value">${formatNumber(healthyCount)}</p>
                                    </div>
                                    <div class="mobile-scope-card-stat">
                                        <p class="mobile-scope-card-stat-label">Alerts</p>
                                        <p class="mobile-scope-card-stat-value">${formatNumber(attentionCount)}</p>
                                    </div>
                                </div>
                            </article>
                            <div class="mobile-scope-chip-row">
                                <span class="mobile-scope-chip">${escapeHtml(facility)}</span>
                                <span class="mobile-scope-chip">${healthRate}% healthy</span>
                                <span class="mobile-scope-chip">${attentionCount > 0 ? `${formatNumber(attentionCount)} attention` : 'Healthy scope'}</span>
                            </div>
                        </section>

                        <section class="mobile-scope-metrics">
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Workstations</p>
                                <p class="mobile-scope-metric-value">${formatNumber(workstationCount)}</p>
                                <p class="mobile-scope-metric-note">Clients registered in this workgroup.</p>
                            </div>
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Displays</p>
                                <p class="mobile-scope-metric-value">${formatNumber(displayCount)}</p>
                                <p class="mobile-scope-metric-note">Managed displays under this workgroup.</p>
                            </div>
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Healthy</p>
                                <p class="mobile-scope-metric-value">${formatNumber(healthyCount)}</p>
                                <p class="mobile-scope-metric-note">Displays currently passing.</p>
                            </div>
                            <div class="mobile-scope-metric">
                                <p class="mobile-scope-metric-label">Health rate</p>
                                <p class="mobile-scope-metric-value">${healthRate}%</p>
                                <p class="mobile-scope-metric-note">${formatNumber(attentionCount)} displays still need action.</p>
                            </div>
                        </section>

                        <section class="mobile-scope-pulse-grid">
                            <article class="mobile-scope-pulse">
                                <p class="mobile-scope-pulse-label">Attention now</p>
                                <p class="mobile-scope-pulse-title">${escapeHtml(leadAlert ? leadAlert.displayName : 'No current display alerts')}</p>
                                <p class="mobile-scope-pulse-copy">${escapeHtml(leadAlert ? displayErrorText(leadAlert.errors) : 'This workgroup is currently clear of alerting displays.')}</p>
                                <span class="mobile-scope-pulse-pill alert">${formatNumber(attentionCount)} alerts</span>
                            </article>
                            <article class="mobile-scope-pulse">
                                <p class="mobile-scope-pulse-label">Recent activity</p>
                                <p class="mobile-scope-pulse-title">${escapeHtml(leadActivity ? leadActivity.name : 'No recent sync activity')}</p>
                                <p class="mobile-scope-pulse-copy">${escapeHtml(leadActivity ? (leadActivity.lastConnected !== '-' ? `Last connected ${leadActivity.lastConnected}` : 'No sync data received yet') : 'Client sync signals will appear here as this workgroup becomes active.')}</p>
                                <span class="mobile-scope-pulse-pill activity">${formatNumber(workstationCount)} clients</span>
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
                                icon: 'monitor-smartphone',
                                title: 'Workstations',
                                copy: 'Open all workstations in this scope.',
                                href: `${workstationsRoute}?facility_id=${detail.facility.id}&workgroup_id=${workgroupId}&facility_name=${encodeURIComponent(facility)}&workgroup_name=${encodeURIComponent(workgroup)}&return_to=${encodeURIComponent(returnTo())}`,
                            })}
                            ${actionCard({
                                icon: 'monitor',
                                title: 'Displays',
                                copy: 'Open all displays in this workgroup.',
                                href: `${displaysRoute}?facility_id=${detail.facility.id}&workgroup_id=${workgroupId}&facility_name=${encodeURIComponent(facility)}&workgroup_name=${encodeURIComponent(workgroup)}&return_to=${encodeURIComponent(returnTo())}`,
                            })}
                        </section>

                        <section class="mobile-scope-section">
                            <div class="mobile-scope-section-top">
                                <div>
                                    <p class="mobile-scope-section-label">Child scopes</p>
                                    <h3 class="mobile-scope-section-title">Workstations in this workgroup</h3>
                                    <p class="mobile-scope-section-copy">Continue to a workstation dashboard or open the full workstation list.</p>
                                </div>
                                <a href="${workstationsRoute}?facility_id=${detail.facility.id}&workgroup_id=${workgroupId}&facility_name=${encodeURIComponent(facility)}&workgroup_name=${encodeURIComponent(workgroup)}&return_to=${encodeURIComponent(returnTo())}" class="mobile-scope-section-link">View all</a>
                            </div>
                            ${workstations.length ? `<div class="mobile-scope-preview-list">
                                ${renderEditorialList(workstations, (item, featured) => previewItem({
                                    href: `${workstationDetailBase}/${item.id}?facility_id=${detail.facility.id}&workgroup_id=${workgroupId}&facility_name=${encodeURIComponent(facility)}&workgroup_name=${encodeURIComponent(workgroup)}&workstation_name=${encodeURIComponent(item.name)}&return_to=${encodeURIComponent(returnTo())}`,
                                    tone: item.attentionCount > 0 ? 'alert' : (item.displayCount > 0 ? 'run' : 'scope'),
                                    pill: item.attentionCount > 0 ? 'Alert' : 'Workstation',
                                    meta: facility,
                                    title: item.name,
                                    subtitle: item.lastConnected !== '-' ? `Last connected ${item.lastConnected}` : 'No sync data received',
                                    stats: [`${item.displayCount} displays`, `${item.attentionCount} alerts`],
                                    featured,
                                }))}
                            </div>` : '<div class="mobile-scope-empty">No workstations are registered in this workgroup.</div>'}
                        </section>

                        <section class="mobile-scope-section">
                            <div class="mobile-scope-section-top">
                                <div>
                                    <p class="mobile-scope-section-label">Attention</p>
                                    <h3 class="mobile-scope-section-title">Displays needing attention</h3>
                                    <p class="mobile-scope-section-copy">The latest alerting displays in this workgroup.</p>
                                </div>
                                <a href="${displaysRoute}?facility_id=${detail.facility.id}&workgroup_id=${workgroupId}&facility_name=${encodeURIComponent(facility)}&workgroup_name=${encodeURIComponent(workgroup)}&status=2&sort=updated_at&order=desc&return_to=${encodeURIComponent(returnTo())}" class="mobile-scope-section-link">View all</a>
                            </div>
                            ${alertDisplays.length ? `<div class="mobile-scope-preview-list">
                                ${renderEditorialList(alertDisplays, (item, featured) => previewItem({
                                    href: `${displayDetailBase}/${item.id}?facility_id=${item.facId}&workgroup_id=${item.wgId}&workstation_id=${item.wsId}&facility_name=${encodeURIComponent(item.facName)}&workgroup_name=${encodeURIComponent(item.wgName)}&workstation_name=${encodeURIComponent(item.wsName)}&status=2&sort=updated_at&order=desc&return_to=${encodeURIComponent(returnTo())}`,
                                    tone: 'alert',
                                    pill: 'Alert',
                                    meta: item.updatedAt,
                                    title: item.displayName,
                                    subtitle: displayErrorText(item.errors),
                                    stats: [item.wsName],
                                    featured,
                                }))}
                            </div>` : '<div class="mobile-scope-empty">No alerting displays in this workgroup right now.</div>'}
                        </section>
                    `;

                    window.lucide?.createIcons();
                    window.Perfectlum?.bindMobileDragScroll?.(root);
                };

                const load = async () => {
                    root.innerHTML = loadingMarkup;

                    try {
                        const [detail, alertDisplaysResponse] = await Promise.all([
                            window.Perfectlum.request(`/api/workgroup-modal/${workgroupId}`),
                            window.Perfectlum.request(`/api/displays?workgroup_id=${workgroupId}&status=2&sort=updated_at&order=desc&limit=3&page=1`),
                        ]);

                        render({
                            detail,
                            alertDisplays: Array.isArray(alertDisplaysResponse.data) ? alertDisplaysResponse.data : [],
                        });
                    } catch (error) {
                        root.innerHTML = `<div class="mobile-empty">${escapeHtml(error.message || 'Workgroup overview could not be loaded.')}</div>`;
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
