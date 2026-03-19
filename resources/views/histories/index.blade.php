@include('common.navigations.header')

<div class="flex flex-col gap-6 pb-8">

    <x-page-header title="History and Reports" description="Browse and export calibration history records." icon="files">
        <x-slot name="actions">
            <x-export-dropdown
                excel-url="{{ url('reports/histories-reports?export_type=excel') }}"
                pdf-url="{{ url('reports/histories-reports?export_type=pdf') }}" />
        </x-slot>
    </x-page-header>

    @php
        $role = session('role') ?? 'user';
        $initialDisplayId = request('display_id');
    @endphp

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,220px)_minmax(0,220px)_minmax(0,220px)_minmax(0,260px)_1fr]">
            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Facility</label>
                <div class="relative">
                    <select id="facility_field" class="hidden" onchange="fetch_workgroups(this)">
                        @if($role === 'super')
                            <option value="">All facilities</option>
                            @foreach($facilities as $facility)
                                <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                            @endforeach
                        @else
                            @foreach($facilities as $facility)
                                <option value="{{ $facility['id'] }}">{{ $facility['name'] }}</option>
                            @endforeach
                        @endif
                    </select>

                    <button
                        id="history-facility-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        <span id="history-facility-label" class="truncate">{{ $role === 'super' ? 'All facilities' : ($facilities[0]['name'] ?? 'Please select') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div id="history-facility-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="history-facility-search" type="text" placeholder="Search facilities..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="history-facility-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="history-facility-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Workgroup</label>
                <div class="relative">
                    <select id="workgroups_field" class="hidden" onchange="fetch_workstations(this)">
                        <option value="">All workgroups</option>
                    </select>

                    <button
                        id="history-workgroup-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        <span id="history-workgroup-label" class="truncate">All workgroups</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div id="history-workgroup-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="history-workgroup-search" type="text" placeholder="Search workgroups..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="history-workgroup-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="history-workgroup-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Workstation</label>
                <div class="relative">
                    <select id="workstations_field" class="hidden" onchange="fetch_displays_checklist(this)">
                        <option value="">All workstations</option>
                    </select>

                    <button
                        id="history-workstation-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        <span id="history-workstation-label" class="truncate">All workstations</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div id="history-workstation-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="history-workstation-search" type="text" placeholder="Search workstations..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="history-workstation-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="history-workstation-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Displays</label>
                <div class="relative">
                    <button
                        id="history-displays-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        <span id="history-displays-label" class="truncate">All displays in scope</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div id="history-displays-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="history-displays-search" type="text" placeholder="Search displays..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="history-displays-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="displays_field" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="flex items-end justify-end">
                <button
                    id="reset-history-filters"
                    type="button"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    Reset Filters
                </button>
            </div>
        </div>
    </section>

    <x-data-table id="histories-grid" class="workstation-table-shell mb-10" />

</div>

<div id="history-summary-modal" class="fixed inset-0 z-[120] hidden">
    <div data-history-summary-overlay class="absolute inset-0 bg-slate-950/45 backdrop-blur-[2px] opacity-0 transition-opacity duration-200"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 md:p-6">
        <div data-history-summary-panel class="relative flex max-h-[88vh] w-full max-w-5xl translate-y-4 scale-[0.985] flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_32px_90px_rgba(15,23,42,0.24)] opacity-0 transition-all duration-200">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-500">History Summary</p>
                    <h2 id="history-summary-title" class="mt-1 truncate text-2xl font-semibold text-slate-900">Loading report...</h2>
                    <p id="history-summary-subtitle" class="mt-2 text-sm text-slate-500">Preparing history summary.</p>
                </div>
                <button type="button" data-history-summary-close class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 text-slate-400 transition hover:border-slate-300 hover:text-slate-700">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <div id="history-summary-body" class="flex-1 overflow-y-auto px-6 py-5">
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                    Loading history summary...
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <a id="history-summary-print" href="#" target="_blank" rel="noopener" class="inline-flex h-11 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="printer" class="h-4 w-4"></i>
                    Print Preview
                </a>
                <button type="button" data-history-summary-close class="inline-flex h-11 items-center rounded-2xl bg-sky-500 px-5 text-sm font-semibold text-white transition hover:bg-sky-600">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@include('common.navigations.footer')

<script>
(function(){
    const isSuperHistoryRole = @json($role === 'super');
    const initialFacilityValue = isSuperHistoryRole ? '' : '{{ $facilities[0]['id'] ?? '' }}';
    const initialFacilityLabel = isSuperHistoryRole ? 'All facilities' : '{{ $facilities[0]['name'] ?? 'Please select' }}';
    const historyFilterState = {
        facilitySearch: '',
        workgroupSearch: '',
        workstationSearch: '',
        displaysSearch: '',
        activeDropdown: null,
    };

    function parseNativeSelectOptions(selectId, includeBlank = true) {
        const select = document.getElementById(selectId);
        if (!select) return [];
        return Array.from(select.options || [])
            .filter((option, index) => includeBlank || index > 0)
            .map((option) => ({
                value: option.value,
                label: option.textContent.trim(),
            }));
    }

    function closeHistoryDropdowns() {
        historyFilterState.activeDropdown = null;
        ['history-facility-panel', 'history-workgroup-panel', 'history-workstation-panel', 'history-displays-panel']
            .forEach((id) => document.getElementById(id)?.classList.add('hidden'));
    }

    function toggleHistoryDropdown(type) {
        historyFilterState.activeDropdown = historyFilterState.activeDropdown === type ? null : type;
        const map = {
            facility: 'history-facility-panel',
            workgroup: 'history-workgroup-panel',
            workstation: 'history-workstation-panel',
            displays: 'history-displays-panel',
        };

        Object.entries(map).forEach(([key, id]) => {
            document.getElementById(id)?.classList.toggle('hidden', historyFilterState.activeDropdown !== key);
        });
    }

    function renderHistoryNativeOptions(selectId, optionsId, hintId, query, onPick, emptyText = 'No options found') {
        const options = parseNativeSelectOptions(selectId).filter((item) => item.label.toLowerCase().includes((query || '').trim().toLowerCase()));
        const hint = document.getElementById(hintId);
        const box = document.getElementById(optionsId);
        if (hint) hint.textContent = options.length ? `${options.length} option${options.length === 1 ? '' : 's'}` : emptyText;
        if (!box) return;

        box.innerHTML = options.length
            ? options.map((item) => `<button type="button" data-value="${Perfectlum.escapeHtml(item.value)}" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 hover:bg-sky-50 hover:text-sky-700">${Perfectlum.escapeHtml(item.label)}</button>`).join('')
            : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${emptyText}</div>`;

        box.querySelectorAll('button[data-value]').forEach((button) => {
            button.addEventListener('click', () => onPick(button.dataset.value));
        });
    }

    function setHistorySelectValue(selectId, labelId, value, fallbackLabel = 'Please select') {
        const select = document.getElementById(selectId);
        const label = document.getElementById(labelId);
        if (!select) return;
        select.value = value;
        const text = select.options[select.selectedIndex]?.textContent?.trim() || fallbackLabel;
        if (label) label.textContent = text;
    }

    function updateDisplaysHint() {
        const host = document.getElementById('displays_field');
        const hint = document.getElementById('history-displays-hint');
        if (!host || !hint) return;
        const options = Array.from(host.querySelectorAll('.form-check'));
        const visible = options.filter((item) => !item.classList.contains('hidden'));
        hint.textContent = visible.length ? `${visible.length} option${visible.length === 1 ? '' : 's'}` : 'No displays found';
    }

    function filterDisplaysList() {
        const host = document.getElementById('displays_field');
        if (!host) return;
        const query = (historyFilterState.displaysSearch || '').trim().toLowerCase();
        host.querySelectorAll('.form-check').forEach((item) => {
            const label = item.querySelector('label')?.textContent?.trim().toLowerCase() || '';
            item.classList.toggle('hidden', query && !label.includes(query));
        });
        updateDisplaysHint();
    }

    function syncDisplayLabel() {
        const host = document.getElementById('displays_field');
        const label = document.getElementById('history-displays-label');
        if (!host || !label) return;

        const checked = Array.from(host.querySelectorAll('input[type="checkbox"]:checked'));
        if (!checked.length) {
            label.textContent = 'All displays in scope';
            return;
        }

        if (checked.length === 1) {
            const text = checked[0].closest('.form-check')?.querySelector('label')?.textContent?.trim() || '1 display selected';
            label.textContent = text;
            return;
        }

        label.textContent = `${checked.length} displays selected`;
    }

    function refreshHistoriesGrid() {
        if (!window.historiesGrid) return;
        window.historiesGrid.updateConfig({
            server: {
                url: '{{ url("api/histories") }}' + getFilterParams(),
                then: d => d.data.map(r => [
                    { id: r.id, name: r.name },
                    r.pattern,
                    r.displayName,
                    r.wsName,
                    r.wgName,
                    r.time,
                    r.result
                ]),
                total: d => d.total
            }
        }).forceRender();
    }

    const historySummaryModal = {
        root: null,
        overlay: null,
        panel: null,
        body: null,
        title: null,
        subtitle: null,
        printLink: null,
        activeId: null,
        init() {
            this.root = document.getElementById('history-summary-modal');
            if (!this.root) return;
            this.overlay = this.root.querySelector('[data-history-summary-overlay]');
            this.panel = this.root.querySelector('[data-history-summary-panel]');
            this.body = document.getElementById('history-summary-body');
            this.title = document.getElementById('history-summary-title');
            this.subtitle = document.getElementById('history-summary-subtitle');
            this.printLink = document.getElementById('history-summary-print');

            this.root.querySelectorAll('[data-history-summary-close]').forEach((button) => {
                button.addEventListener('click', () => this.close());
            });

            this.overlay?.addEventListener('click', () => this.close());
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && this.root && !this.root.classList.contains('hidden')) {
                    this.close();
                }
            });
        },
        openSkeleton(id, name) {
            this.activeId = id;
            this.title.textContent = name || 'History Summary';
            this.subtitle.textContent = 'Loading report summary...';
            this.body.innerHTML = '<div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">Loading history summary...</div>';
            this.printLink.setAttribute('href', '#');
            this.root.classList.remove('hidden');
            requestAnimationFrame(() => {
                this.overlay?.classList.remove('opacity-0');
                this.panel?.classList.remove('translate-y-4', 'scale-[0.985]', 'opacity-0');
            });
            document.body.classList.add('overflow-hidden');
            if (window.lucide) window.lucide.createIcons();
        },
        close() {
            if (!this.root || this.root.classList.contains('hidden')) return;
            this.overlay?.classList.add('opacity-0');
            this.panel?.classList.add('translate-y-4', 'scale-[0.985]', 'opacity-0');
            window.setTimeout(() => {
                this.root.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 200);
        },
        renderBadge(label, tone) {
            const map = {
                success: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                danger: 'bg-rose-50 text-rose-700 ring-rose-200',
                warning: 'bg-amber-50 text-amber-700 ring-amber-200',
                neutral: 'bg-slate-100 text-slate-600 ring-slate-200',
            };
            const cls = map[tone] || map.neutral;
            return `<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ${cls}">${Perfectlum.escapeHtml(label || '-')}</span>`;
        },
        renderInfoGrid(items) {
            if (!items?.length) return '';
            return `<section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">${items.map((item) => `
                <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">${Perfectlum.escapeHtml(item.label || '-')}</p>
                    <p class="mt-2 break-words text-sm font-medium text-slate-800">${Perfectlum.escapeHtml(item.value || '-')}</p>
                </div>`).join('')}</section>`;
        },
        renderSection(section) {
            const scores = Array.isArray(section.scores) ? section.scores : [];
            const questions = Array.isArray(section.questions) ? section.questions : [];
            const comment = section.comment || '';
            return `
                <section class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_12px_40px_-32px_rgba(15,23,42,0.24)]">
                    <h3 class="text-lg font-semibold text-slate-900">${Perfectlum.escapeHtml(section.name || 'Section')}</h3>
                    <p class="mt-1 text-sm text-slate-500">Review scored checks, question answers, and comments captured for this task.</p>
                    ${scores.length ? `
                        <div class="mt-5 overflow-hidden rounded-[1.25rem] border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr class="text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                                        <th class="px-4 py-3">Score</th>
                                        <th class="px-4 py-3">Limit</th>
                                        <th class="px-4 py-3">Measured</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    ${scores.map((score) => `
                                        <tr>
                                            <td class="px-4 py-3 font-medium text-slate-800">${Perfectlum.escapeHtml(score.name || '-')}</td>
                                            <td class="px-4 py-3 text-slate-600">${Perfectlum.escapeHtml(score.limit || '-')}</td>
                                            <td class="px-4 py-3 text-slate-600">${Perfectlum.escapeHtml(score.measured || '-')}</td>
                                            <td class="px-4 py-3">${this.renderBadge(score.statusLabel || '-', score.statusTone || 'neutral')}</td>
                                        </tr>`).join('')}
                                </tbody>
                            </table>
                        </div>` : ''}
                    ${questions.length ? `<div class="mt-5 grid gap-3">${questions.map((question) => `
                        <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm font-medium text-slate-800">${Perfectlum.escapeHtml(question.text || '-')}</p>
                                ${this.renderBadge(question.answer || '-', question.tone || 'neutral')}
                            </div>
                        </div>`).join('')}</div>` : ''}
                    ${comment ? `
                        <div class="mt-5 rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Comment</p>
                            <p class="mt-2 text-sm leading-6 text-slate-700">${Perfectlum.escapeHtml(comment)}</p>
                        </div>` : ''}
                </section>`;
        },
        render(payload) {
            this.title.textContent = payload.name || 'History Summary';
            this.subtitle.textContent = `${payload.performedAt || '-'} • ${payload.display?.display || '-'}`;
            this.printLink.setAttribute('href', payload.printUrl || '#');
            const displayInfo = [
                { label: 'Facility', value: payload.display?.facility || '-' },
                { label: 'Workgroup', value: payload.display?.workgroup || '-' },
                { label: 'Workstation', value: payload.display?.workstation || '-' },
                { label: 'Display', value: payload.display?.display || '-' },
                { label: 'Performed At', value: payload.performedAt || '-' },
                { label: 'Result', value: payload.resultLabel || '-' },
            ];
            this.body.innerHTML = `
                <div class="space-y-5">
                    <section class="flex flex-wrap items-center gap-3 rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4">
                        ${this.renderBadge(payload.resultLabel || 'Unknown', payload.resultTone || 'neutral')}
                        <span class="text-sm text-slate-500">Detailed summary for the selected task execution.</span>
                    </section>
                    ${this.renderInfoGrid(displayInfo)}
                    ${payload.header?.length ? this.renderInfoGrid(payload.header) : ''}
                    ${payload.sections?.length ? payload.sections.map((section) => this.renderSection(section)).join('') : '<div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">No structured summary is available for this history record.</div>'}
                </div>`;
            if (window.lucide) window.lucide.createIcons();
        },
        async load(id, name) {
            this.openSkeleton(id, name);
            try {
                const payload = await Perfectlum.request(`/api/history-modal/${id}`);
                if (this.activeId !== id) return;
                this.render(payload);
            } catch (error) {
                this.body.innerHTML = '<div class="rounded-[1.5rem] border border-rose-200 bg-rose-50 px-4 py-8 text-center text-sm text-rose-600">Failed to load history summary.</div>';
            }
        }
    };

    // Helper to get selected values from filter-panel
    function getFilterParams() {
        const facId = document.getElementById('facility_field')?.value || '';
        const wgId  = document.getElementById('workgroups_field')?.value || '';
        const wsId  = document.getElementById('workstations_field')?.value || '';
        const initialDisplayId = @json($initialDisplayId);
        const checkedDisplayIds = Array.from(document.querySelectorAll('#displays_field input[name="displays[]"]:checked'))
            .map((input) => input.value)
            .filter(Boolean);
        
        // Displays are checkboxes if checklist is used, but for API filtering we might just use the select values
        let params = [];
        if(facId) params.push('facility_id=' + facId);
        if(wgId)  params.push('workgroup_id=' + wgId);
        if(wsId)  params.push('workstation_id=' + wsId);
        if(checkedDisplayIds.length) params.push('display_ids=' + encodeURIComponent(checkedDisplayIds.join(',')));
        if(initialDisplayId) params.push('display_id=' + encodeURIComponent(initialDisplayId));
        
        return params.length ? '?' + params.join('&') : '';
    }

    // JS functions for filter-panel (as expected by x-filter-panel)
    window.fetch_workgroups = function(el) {
        let id = el.value;
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('id', id);
        Perfectlum.postForm('{{ url("fetch-groups") }}', formData)
            .then(data => {
                if (data.success) {
                    let wgField = document.getElementById('workgroups_field');
                    if(wgField) {
                        wgField.innerHTML = '<option value="">All workgroups</option>' + data.content;
                        setHistorySelectValue('workgroups_field', 'history-workgroup-label', '', 'All workgroups');
                        let wsField = document.getElementById('workstations_field');
                        if (wsField) {
                            wsField.innerHTML = '<option value="">All workstations</option>';
                            setHistorySelectValue('workstations_field', 'history-workstation-label', '', 'All workstations');
                        }
                        const displaysField = document.getElementById('displays_field');
                        if (displaysField) {
                            displaysField.innerHTML = '<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">All displays in scope</div>';
                            syncDisplayLabel();
                            updateDisplaysHint();
                        }
                        renderHistoryNativeOptions('workgroups_field', 'history-workgroup-options', 'history-workgroup-hint', historyFilterState.workgroupSearch, (value) => {
                            setHistorySelectValue('workgroups_field', 'history-workgroup-label', value, 'All workgroups');
                            closeHistoryDropdowns();
                            fetch_workstations(document.getElementById('workgroups_field'));
                        }, 'No workgroups found');
                        refreshHistoriesGrid();
                    }
                }
            });
    };

    window.fetch_workstations = function(el) {
        let id = el.value;
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('id', id);
        Perfectlum.postForm('{{ url("fetch-workstations") }}', formData)
            .then(data => {
                if (data.success) {
                    let wsField = document.getElementById('workstations_field');
                    if(wsField) {
                        wsField.innerHTML = '<option value="">All workstations</option>' + data.content;
                        setHistorySelectValue('workstations_field', 'history-workstation-label', '', 'All workstations');
                        const displaysField = document.getElementById('displays_field');
                        if (displaysField) {
                            displaysField.innerHTML = '<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">All displays in scope</div>';
                            syncDisplayLabel();
                            updateDisplaysHint();
                        }
                        renderHistoryNativeOptions('workstations_field', 'history-workstation-options', 'history-workstation-hint', historyFilterState.workstationSearch, (value) => {
                            setHistorySelectValue('workstations_field', 'history-workstation-label', value, 'All workstations');
                            closeHistoryDropdowns();
                            fetch_displays_checklist(document.getElementById('workstations_field'));
                        }, 'No workstations found');
                        refreshHistoriesGrid();
                    }
                }
            });
    };

    window.fetch_displays_checklist = function(el) {
        let id = el.value;
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('id', id);
        Perfectlum.postForm('{{ url("fetch-displays-checklist") }}', formData)
            .then(data => {
                if (data.success) {
                    let dField = document.getElementById('displays_field');
                    if(dField) {
                        dField.innerHTML = data.content;
                        dField.querySelectorAll('input[name="displays[]"]').forEach((input) => {
                            input.addEventListener('change', () => {
                                syncDisplayLabel();
                                refreshHistoriesGrid();
                            });
                        });
                        syncDisplayLabel();
                        filterDisplaysList();
                        refreshHistoriesGrid();
                    }
                }
            });
    };

    function initHistoriesGrid() {
        var el = document.getElementById('histories-grid');
        if (!el || el._gi) return;
        el._gi = true;
        historySummaryModal.init();
        document.getElementById('history-facility-trigger')?.addEventListener('click', () => {
            toggleHistoryDropdown('facility');
            renderHistoryNativeOptions('facility_field', 'history-facility-options', 'history-facility-hint', historyFilterState.facilitySearch, (value) => {
                setHistorySelectValue('facility_field', 'history-facility-label', value, '{{ $role === "super" ? "All facilities" : ($facilities[0]["name"] ?? "Please select") }}');
                closeHistoryDropdowns();
                fetch_workgroups(document.getElementById('facility_field'));
            }, 'No facilities found');
        });
        document.getElementById('history-workgroup-trigger')?.addEventListener('click', () => {
            toggleHistoryDropdown('workgroup');
            renderHistoryNativeOptions('workgroups_field', 'history-workgroup-options', 'history-workgroup-hint', historyFilterState.workgroupSearch, (value) => {
                setHistorySelectValue('workgroups_field', 'history-workgroup-label', value, 'All workgroups');
                closeHistoryDropdowns();
                fetch_workstations(document.getElementById('workgroups_field'));
            }, 'No workgroups found');
        });
        document.getElementById('history-workstation-trigger')?.addEventListener('click', () => {
            toggleHistoryDropdown('workstation');
            renderHistoryNativeOptions('workstations_field', 'history-workstation-options', 'history-workstation-hint', historyFilterState.workstationSearch, (value) => {
                setHistorySelectValue('workstations_field', 'history-workstation-label', value, 'All workstations');
                closeHistoryDropdowns();
                fetch_displays_checklist(document.getElementById('workstations_field'));
            }, 'No workstations found');
        });
        document.getElementById('history-displays-trigger')?.addEventListener('click', () => {
            toggleHistoryDropdown('displays');
            filterDisplaysList();
        });

        document.getElementById('history-facility-search')?.addEventListener('input', (event) => {
            historyFilterState.facilitySearch = event.target.value || '';
            renderHistoryNativeOptions('facility_field', 'history-facility-options', 'history-facility-hint', historyFilterState.facilitySearch, (value) => {
                setHistorySelectValue('facility_field', 'history-facility-label', value, '{{ $role === "super" ? "All facilities" : ($facilities[0]["name"] ?? "Please select") }}');
                closeHistoryDropdowns();
                fetch_workgroups(document.getElementById('facility_field'));
            }, 'No facilities found');
        });
        document.getElementById('history-workgroup-search')?.addEventListener('input', (event) => {
            historyFilterState.workgroupSearch = event.target.value || '';
            renderHistoryNativeOptions('workgroups_field', 'history-workgroup-options', 'history-workgroup-hint', historyFilterState.workgroupSearch, (value) => {
                setHistorySelectValue('workgroups_field', 'history-workgroup-label', value, 'All workgroups');
                closeHistoryDropdowns();
                fetch_workstations(document.getElementById('workgroups_field'));
            }, 'No workgroups found');
        });
        document.getElementById('history-workstation-search')?.addEventListener('input', (event) => {
            historyFilterState.workstationSearch = event.target.value || '';
            renderHistoryNativeOptions('workstations_field', 'history-workstation-options', 'history-workstation-hint', historyFilterState.workstationSearch, (value) => {
                setHistorySelectValue('workstations_field', 'history-workstation-label', value, 'All workstations');
                closeHistoryDropdowns();
                fetch_displays_checklist(document.getElementById('workstations_field'));
            }, 'No workstations found');
        });
        document.getElementById('history-displays-search')?.addEventListener('input', (event) => {
            historyFilterState.displaysSearch = event.target.value || '';
            filterDisplaysList();
        });

        document.getElementById('reset-history-filters')?.addEventListener('click', () => {
            historyFilterState.facilitySearch = '';
            historyFilterState.workgroupSearch = '';
            historyFilterState.workstationSearch = '';
            historyFilterState.displaysSearch = '';
            setHistorySelectValue('facility_field', 'history-facility-label', initialFacilityValue, initialFacilityLabel);
            if (document.getElementById('history-facility-search')) document.getElementById('history-facility-search').value = '';
            if (document.getElementById('history-workgroup-search')) document.getElementById('history-workgroup-search').value = '';
            if (document.getElementById('history-workstation-search')) document.getElementById('history-workstation-search').value = '';
            if (document.getElementById('history-displays-search')) document.getElementById('history-displays-search').value = '';
            fetch_workgroups(document.getElementById('facility_field'));
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('#history-facility-trigger') &&
                !event.target.closest('#history-facility-panel') &&
                !event.target.closest('#history-workgroup-trigger') &&
                !event.target.closest('#history-workgroup-panel') &&
                !event.target.closest('#history-workstation-trigger') &&
                !event.target.closest('#history-workstation-panel') &&
                !event.target.closest('#history-displays-trigger') &&
                !event.target.closest('#history-displays-panel')) {
                closeHistoryDropdowns();
            }
        });

        syncDisplayLabel();
        updateDisplaysHint();
        renderHistoryNativeOptions('facility_field', 'history-facility-options', 'history-facility-hint', historyFilterState.facilitySearch, (value) => {
            setHistorySelectValue('facility_field', 'history-facility-label', value, '{{ $role === "super" ? "All facilities" : ($facilities[0]["name"] ?? "Please select") }}');
            closeHistoryDropdowns();
            fetch_workgroups(document.getElementById('facility_field'));
        }, 'No facilities found');
        renderHistoryNativeOptions('workgroups_field', 'history-workgroup-options', 'history-workgroup-hint', historyFilterState.workgroupSearch, (value) => {
            setHistorySelectValue('workgroups_field', 'history-workgroup-label', value, 'All workgroups');
            closeHistoryDropdowns();
            fetch_workstations(document.getElementById('workgroups_field'));
        }, 'No workgroups found');
        renderHistoryNativeOptions('workstations_field', 'history-workstation-options', 'history-workstation-hint', historyFilterState.workstationSearch, (value) => {
            setHistorySelectValue('workstations_field', 'history-workstation-label', value, 'All workstations');
            closeHistoryDropdowns();
            fetch_displays_checklist(document.getElementById('workstations_field'));
        }, 'No workstations found');

        if (document.getElementById('facility_field')?.value) {
            fetch_workgroups(document.getElementById('facility_field'));
        }

        window.historiesGrid = Perfectlum.createGrid(el, {
            columns: [
                {
                    name: 'Task Name',
                    formatter: (r) => gridjs.html(`<button type="button" data-history-summary-open="${r.id}" data-history-summary-name="${Perfectlum.escapeHtml(r.name)}" class="font-medium text-sky-600 transition hover:text-sky-700 hover:underline">${Perfectlum.escapeHtml(r.name)}</button>`)
                },
                { name: 'Pattern' },
                { name: 'Display' },
                { name: 'Workstation' },
                { name: 'Workgroup' }, // Typo fix: Workgroup (was Wrokgroup in older pages)
                { 
                    name: 'Performed Date/Time',
                    formatter: (c) => gridjs.html(`<span class="text-gray-500 text-[11px] font-mono">${c}</span>`)
                },
                {
                    name: 'Result',
                    formatter: (c) => gridjs.html(typeof renderResultBadge === 'function' ? renderResultBadge(c === 'passed' ? 'pass' : 'fail') : `<span class="px-2 py-1 rounded-full text-xs font-bold ${c==='passed'?'bg-emerald-100 text-emerald-700':'bg-rose-100 text-rose-700'}">${c}</span>`)
                }
            ],
            server: {
                url: '{{ url("api/histories") }}' + getFilterParams(),
                then: d => d.data.map(r => [
                    { id: r.id, name: r.name },
                    r.pattern,
                    r.displayName,
                    r.wsName,
                    r.wgName,
                    r.time,
                    r.result
                ]),
                total: d => d.total
            },
            pagination: {
                enabled: true,
                limit: 10,
                server: {
                    url: (prev, pg, lim) => prev + (prev.includes('?') ? '&' : '?') + 'page=' + (pg + 1) + '&limit=' + lim
                }
            },
            search: {
                enabled: true,
                server: {
                    url: (prev, kw) => prev + (prev.includes('?') ? '&' : '?') + 'search=' + encodeURIComponent(kw)
                }
            },
            sort: { multiColumn: false },
            language: {
                search: { placeholder: 'Search histories...' }
            }
        });

        el.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-history-summary-open]');
            if (!trigger) return;
            event.preventDefault();
            historySummaryModal.load(Number(trigger.dataset.historySummaryOpen), trigger.dataset.historySummaryName || 'History Summary');
        });
    }

    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', initHistoriesGrid) : initHistoriesGrid();
})();
</script>
