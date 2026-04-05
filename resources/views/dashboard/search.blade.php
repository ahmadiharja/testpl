@include('common.navigations.header')

<div x-data="{ activeTab: 'displays' }" class="flex flex-col gap-6 pb-8">
    <x-page-header
        title="Search"
        description="Cari displays, calibration tasks, dan scheduled tasks dari satu halaman yang konsisten."
        icon="search"
    />

    <x-filter-panel
        :facilities="$facilities"
        :role="$role"
        accent-color="sky"
        title="Search Filters"
    />

    <x-bento-card>
        <div class="border-b border-slate-200 px-6 py-4">
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    @click="activeTab = 'displays'; window.refreshSearchGrid && window.refreshSearchGrid('displays')"
                    :class="activeTab === 'displays' ? 'bg-sky-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="rounded-full px-4 py-2 text-sm font-semibold transition"
                >
                    Displays
                </button>
                <button
                    type="button"
                    @click="activeTab = 'tasks'; window.refreshSearchGrid && window.refreshSearchGrid('tasks')"
                    :class="activeTab === 'tasks' ? 'bg-sky-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="rounded-full px-4 py-2 text-sm font-semibold transition"
                >
                    Tasks
                </button>
                <button
                    type="button"
                    @click="activeTab = 'calibration'; window.refreshSearchGrid && window.refreshSearchGrid('calibration')"
                    :class="activeTab === 'calibration' ? 'bg-sky-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="rounded-full px-4 py-2 text-sm font-semibold transition"
                >
                    Display Calibration
                </button>
            </div>
        </div>

        <div class="space-y-6 p-6">
            <div x-show="activeTab === 'displays'" x-cloak class="space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Displays</h2>
                        <p class="text-sm text-slate-500">Inventory display dengan status, lokasi, dan aksi cepat.</p>
                    </div>
                    <x-export-dropdown
                        excel-url="{{ url('reports/displays?export_type=excel') }}"
                        pdf-url="{{ url('reports/displays?export_type=pdf') }}"
                    />
                </div>
                <x-data-table id="search-displays-grid" />
            </div>

            <div x-show="activeTab === 'tasks'" x-cloak class="space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">All Tasks</h2>
                        <p class="text-sm text-slate-500">Gabungan task calibration dan QA task dengan filter lokasi yang sama.</p>
                    </div>
                    <x-export-dropdown
                        excel-url="{{ url('reports/all-tasks?export_type=excel') }}"
                        pdf-url="{{ url('reports/all-tasks?export_type=pdf') }}"
                    />
                </div>
                <x-data-table id="search-tasks-grid" />
            </div>

            <div x-show="activeTab === 'calibration'" x-cloak class="space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Calibration Tasks</h2>
                        <p class="text-sm text-slate-500">Task calibration aktif tanpa DataTables dan tanpa modal Bootstrap.</p>
                    </div>
                    <x-export-dropdown
                        excel-url="{{ url('reports/display-calibration?export_type=excel') }}"
                        pdf-url="{{ url('reports/display-calibration?export_type=pdf') }}"
                    />
                </div>
                <x-data-table id="search-calibration-grid" />
            </div>
        </div>
    </x-bento-card>
</div>

@include('tasks.schedule_task_modal')
@include('common.navigations.footer')

<script>
    (function () {
        const canManage = @json((($role ?? session('role')) === 'super') || (($role ?? session('role')) === 'admin'));

        function selectedDisplayIds() {
            return Array.from(document.querySelectorAll('#displays_field input[name="displays[]"]:checked'))
                .map((input) => input.value)
                .filter(Boolean);
        }

        function currentFilterQuery() {
            const params = new URLSearchParams();
            const facilityId = document.getElementById('facility_field')?.value || '';
            const workgroupId = document.getElementById('workgroups_field')?.value || '';
            const workstationId = document.getElementById('workstations_field')?.value || '';
            const displayIds = selectedDisplayIds();

            if (facilityId) params.set('facility_id', facilityId);
            if (workgroupId) params.set('workgroup_id', workgroupId);
            if (workstationId) params.set('workstation_id', workstationId);
            if (displayIds.length) params.set('display_ids', displayIds.join(','));

            const query = params.toString();
            return query ? `?${query}` : '';
        }

        function syncDisplayLabel() {
            const host = document.getElementById('displays_field');
            const button = document.querySelector('#displays-dropdown span');
            if (!host || !button) {
                return;
            }

            const checked = Array.from(host.querySelectorAll('input[type="checkbox"]:checked'));
            if (!checked.length) {
                button.textContent = 'Please select';
                return;
            }

            if (checked.length === 1) {
                const text = checked[0].closest('label')?.textContent?.trim() || '1 display selected';
                button.textContent = text;
                return;
            }

            button.textContent = `${checked.length} displays selected`;
        }

        function renderDisplayStatus(row) {
            if (!row.connected) {
                return Perfectlum.badge('Disconnected', 'warning');
            }
            if (Number(row.status) === 2) {
                return Perfectlum.badge('Failed', 'danger');
            }
            return Perfectlum.badge('OK', 'success');
        }

        function renderDisplayActions(row) {
            if (!canManage) {
                return '<span class="text-xs text-slate-400">No actions</span>';
            }

            const settingsUrl = @json(url('display-settings')) + `/${row.id}`;
            const settingsButton = `
                <a href="${settingsUrl}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 transition hover:bg-sky-100">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6 1.65 1.65 0 0 0-.33 1V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-.33-1 1.65 1.65 0 0 0-1-.6 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-.6-1 1.65 1.65 0 0 0-1-.33H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1-.33 1.65 1.65 0 0 0 .6-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-.6 1.65 1.65 0 0 0 .33-1V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 .33 1 1.65 1.65 0 0 0 1 .6 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.24.31.44.65.6 1a1.65 1.65 0 0 0 1 .33H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1 .33c-.35.16-.69.36-1 .6Z"/>
                    </svg>
                </a>
            `;

            return `
                <div class="flex items-center gap-2">
                    ${settingsButton}
                    <button onclick="window.deleteSearchDisplay(${row.id})" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18"/>
                            <path d="M8 6V4h8v2"/>
                            <path d="m19 6-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/>
                            <path d="M14 11v6"/>
                        </svg>
                    </button>
                </div>
            `;
        }

        function renderTaskActions(row, allowAll = false) {
            if (!canManage) {
                return '<span class="text-xs text-slate-400">No actions</span>';
            }

            if (!allowAll && row.type !== 'task') {
                return '<span class="text-xs text-slate-400">QA task</span>';
            }

            return `
                <div class="flex items-center gap-2">
                    <button onclick="edit_task(this, ${row.id})" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 transition hover:bg-sky-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M8 2V5"/>
                            <path d="M16 2V5"/>
                            <path d="M3.5 9.09H20.5"/>
                            <path d="M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z"/>
                        </svg>
                    </button>
                    <button onclick="window.deleteSearchTask(${row.id})" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18"/>
                            <path d="M8 6V4h8v2"/>
                            <path d="m19 6-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/>
                            <path d="M14 11v6"/>
                        </svg>
                    </button>
                </div>
            `;
        }

        async function deleteSearchTask(id) {
            if (!confirm('Delete this task?')) {
                return;
            }

            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', id);

            try {
                const payload = await Perfectlum.postForm(@json(url('delete-task')), formData);
                if (!payload.success) {
                    notify('failed', payload.msg || 'Failed to delete task.');
                    return;
                }
                notify('success', payload.msg || 'Task deleted successfully.');
                refreshAllGrids();
            } catch (error) {
                notify('failed', 'Failed to delete task.');
            }
        }

        async function deleteSearchDisplay(id) {
            if (!confirm('Delete this display?')) {
                return;
            }

            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', id);

            try {
                const payload = await Perfectlum.postForm(@json(url('delete-display')), formData);
                if (!payload.success) {
                    notify('failed', payload.msg || 'Failed to delete display.');
                    return;
                }
                notify('success', payload.msg || 'Display deleted successfully.');
                refreshAllGrids();
            } catch (error) {
                notify('failed', 'Failed to delete display.');
            }
        }

        function reRenderGrid(holderId, initFn, key) {
            const el = document.getElementById(holderId);
            if (!el) {
                return;
            }
            el.innerHTML = '';
            el._gi = false;
            window.searchGrids[key] = null;
            initFn();
        }

        function refreshAllGrids() {
            reRenderGrid('search-displays-grid', initDisplaysGrid, 'displays');
            reRenderGrid('search-tasks-grid', initTasksGrid, 'tasks');
            reRenderGrid('search-calibration-grid', initCalibrationGrid, 'calibration');
        }

        function refreshSearchGrid(which) {
            const map = {
                displays: ['search-displays-grid', initDisplaysGrid],
                tasks: ['search-tasks-grid', initTasksGrid],
                calibration: ['search-calibration-grid', initCalibrationGrid],
            };
            const item = map[which];
            if (!item) {
                return;
            }
            reRenderGrid(item[0], item[1], which);
        }

        function initDisplaysGrid() {
            const el = document.getElementById('search-displays-grid');
            if (!el || el._gi) return;
            el._gi = true;

            window.searchGrids.displays = Perfectlum.createGrid(el, {
                columns: [
                    {
                        name: 'Display',
                        formatter: (_, row) => gridjs.html(`
                            <div>
                                <div class="text-sm font-semibold text-slate-900">${Perfectlum.escapeHtml(row.cells[0].data.displayName)}</div>
                                <div class="text-xs text-slate-500">${Perfectlum.escapeHtml(row.cells[0].data.facName)}</div>
                            </div>
                        `)
                    },
                    { name: 'Workstation' },
                    { name: 'Workgroup' },
                    {
                        name: 'Status',
                        formatter: (_, row) => gridjs.html(renderDisplayStatus(row.cells[0].data))
                    },
                    {
                        name: 'Actions',
                        sort: false,
                        formatter: (_, row) => gridjs.html(renderDisplayActions(row.cells[0].data))
                    }
                ],
                server: {
                    url: @json(url('api/displays')) + currentFilterQuery(),
                    then: (payload) => payload.data.map((item) => [item, item.wsName, item.wgName, item.status, null]),
                    total: (payload) => payload.total
                },
                pagination: {
                    enabled: true,
                    limit: 10,
                    server: { url: (prev, page, limit) => `${prev}${prev.includes('?') ? '&' : '?'}page=${page + 1}&limit=${limit}` }
                },
                search: {
                    enabled: true,
                    server: { url: (prev, keyword) => `${prev}${prev.includes('?') ? '&' : '?'}search=${encodeURIComponent(keyword)}` }
                },
                sort: true
            });
        }

        function initTasksGrid() {
            const el = document.getElementById('search-tasks-grid');
            if (!el || el._gi) return;
            el._gi = true;

            window.searchGrids.tasks = Perfectlum.createGrid(el, {
                columns: [
                    {
                        name: 'Display',
                        formatter: (_, row) => gridjs.html(`
                            <div>
                                <div class="text-sm font-semibold text-slate-900">${Perfectlum.escapeHtml(row.cells[0].data.displayName)}</div>
                                <div class="text-xs text-slate-500">${Perfectlum.escapeHtml(row.cells[0].data.type === 'qa_task' ? 'QA Task' : 'Calibration Task')}</div>
                            </div>
                        `)
                    },
                    { name: 'Workstation' },
                    { name: 'Workgroup' },
                    { name: 'Facility' },
                    { name: 'Task' },
                    { name: 'Schedule' },
                    {
                        name: 'Due',
                        formatter: (_, row) => gridjs.html(Perfectlum.badge(row.cells[0].data.dueAt, row.cells[0].data.dueColor || 'neutral'))
                    },
                    {
                        name: 'Actions',
                        sort: false,
                        formatter: (_, row) => gridjs.html(renderTaskActions(row.cells[0].data))
                    }
                ],
                server: {
                    url: @json(url('api/tasks')) + currentFilterQuery(),
                    then: (payload) => payload.data.map((item) => [item, item.wsName, item.wgName, item.facName, item.taskName, item.scheduleName, item.dueAt, null]),
                    total: (payload) => payload.total
                },
                pagination: {
                    enabled: true,
                    limit: 10,
                    server: { url: (prev, page, limit) => `${prev}${prev.includes('?') ? '&' : '?'}page=${page + 1}&limit=${limit}` }
                },
                search: {
                    enabled: true,
                    server: { url: (prev, keyword) => `${prev}${prev.includes('?') ? '&' : '?'}search=${encodeURIComponent(keyword)}` }
                },
                sort: true
            });
        }

        function initCalibrationGrid() {
            const el = document.getElementById('search-calibration-grid');
            if (!el || el._gi) return;
            el._gi = true;

            window.searchGrids.calibration = Perfectlum.createGrid(el, {
                columns: [
                    {
                        name: 'Display',
                        formatter: (_, row) => gridjs.html(`
                            <div>
                                <div class="text-sm font-semibold text-slate-900">${Perfectlum.escapeHtml(row.cells[0].data.displayName)}</div>
                                <div class="text-xs text-slate-500">${Perfectlum.escapeHtml(row.cells[0].data.facName)}</div>
                            </div>
                        `)
                    },
                    { name: 'Workstation' },
                    { name: 'Workgroup' },
                    { name: 'Task' },
                    { name: 'Schedule' },
                    {
                        name: 'Due',
                        formatter: (_, row) => gridjs.html(Perfectlum.badge(row.cells[0].data.dueAt, 'neutral'))
                    },
                    {
                        name: 'Actions',
                        sort: false,
                        formatter: (_, row) => gridjs.html(renderTaskActions(row.cells[0].data, true))
                    }
                ],
                server: {
                    url: @json(url('api/calibration-tasks')) + currentFilterQuery(),
                    then: (payload) => payload.data.map((item) => [item, item.wsName, item.wgName, item.taskName, item.scheduleName, item.dueAt, null]),
                    total: (payload) => payload.total
                },
                pagination: {
                    enabled: true,
                    limit: 10,
                    server: { url: (prev, page, limit) => `${prev}${prev.includes('?') ? '&' : '?'}page=${page + 1}&limit=${limit}` }
                },
                search: {
                    enabled: true,
                    server: { url: (prev, keyword) => `${prev}${prev.includes('?') ? '&' : '?'}search=${encodeURIComponent(keyword)}` }
                },
                sort: true
            });
        }

        window.fetch_workgroups = async function (el) {
            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', el.value || '');
            const data = await Perfectlum.postForm(@json(url('fetch-groups')), formData);
            if (!data.success) return;

            const wgField = document.getElementById('workgroups_field');
            const wsField = document.getElementById('workstations_field');
            const displayField = document.getElementById('displays_field');
            if (wgField) wgField.innerHTML = data.content;
            if (wsField) wsField.innerHTML = '<option value="">Select Workgroup first</option>';
            if (displayField) displayField.innerHTML = '<div class="px-3 py-2 text-[12px] text-gray-500 italic">Select Workstation first</div>';
            syncDisplayLabel();
            refreshAllGrids();
        };

        window.fetch_workstations = async function (el) {
            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', el.value || '');
            const data = await Perfectlum.postForm(@json(url('fetch-workstations')), formData);
            if (!data.success) return;

            const wsField = document.getElementById('workstations_field');
            const displayField = document.getElementById('displays_field');
            if (wsField) wsField.innerHTML = data.content;
            if (displayField) displayField.innerHTML = '<div class="px-3 py-2 text-[12px] text-gray-500 italic">Select Workstation first</div>';
            syncDisplayLabel();
            refreshAllGrids();
        };

        window.fetch_displays_checklist = async function (el) {
            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', el.value || '');
            const data = await Perfectlum.postForm(@json(url('fetch-displays-checklist')), formData);
            if (!data.success) return;

            const displayField = document.getElementById('displays_field');
            if (displayField) {
                displayField.innerHTML = data.content;
            }
            syncDisplayLabel();
            refreshAllGrids();
        };

        document.addEventListener('change', function (event) {
            if (event.target.closest('#displays_field')) {
                syncDisplayLabel();
                refreshAllGrids();
            }
        });

        window.searchGrids = { displays: null, tasks: null, calibration: null };
        window.deleteSearchTask = deleteSearchTask;
        window.deleteSearchDisplay = deleteSearchDisplay;
        window.refreshSearchGrid = refreshSearchGrid;
        window.addEventListener('task-saved', refreshAllGrids);

        document.readyState === 'loading'
            ? document.addEventListener('DOMContentLoaded', function () {
                syncDisplayLabel();
                initDisplaysGrid();
                initTasksGrid();
                initCalibrationGrid();
            })
            : (syncDisplayLabel(), initDisplaysGrid(), initTasksGrid(), initCalibrationGrid());
    })();
</script>
