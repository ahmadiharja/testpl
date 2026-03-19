@include('common.navigations.header')

<div class="flex flex-col gap-6 pb-8">
    <x-page-header
        title="Schedule Tasks"
        description="Pantau task terjadwal dan edit task calibration tanpa DataTables atau modal Bootstrap."
        icon="calendar-clock"
    />

    <x-bento-card>
        <div class="space-y-4 p-6">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Upcoming Tasks</h2>
                <p class="text-sm text-slate-500">Data ditarik dari API Grid.js dan aksi edit memakai panel task bersama.</p>
            </div>

            <x-data-table id="due-tasks-grid" />
        </div>
    </x-bento-card>
</div>

@include('tasks.schedule_task_modal')
@include('common.navigations.footer')

<script>
    (function () {
        const canManageTasks = @json(($role ?? session('role')) !== 'user');

        function renderBadge(label, tone) {
            return Perfectlum.badge(label, tone);
        }

        function renderTaskActions(row) {
            if (!canManageTasks || row.type !== 'task') {
                return '<span class="text-xs text-slate-400">No actions</span>';
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
                    <button onclick="deleteDueTask(${row.id})" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100">
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

        async function deleteDueTask(id) {
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
                reloadDueTasksGrid();
            } catch (error) {
                notify('failed', 'Failed to delete task.');
            }
        }

        function initDueTasksGrid() {
            const el = document.getElementById('due-tasks-grid');
            if (!el || el._gi) {
                return;
            }

            el._gi = true;
            window.dueTasksGrid = Perfectlum.createGrid(el, {
                columns: [
                    {
                        name: 'Display',
                        formatter: (_, row) => gridjs.html(`
                            <div>
                                <div class="text-sm font-semibold text-slate-900">${escapeHtml(row.cells[0].data.displayName)}</div>
                                <div class="text-xs text-slate-500">${escapeHtml(row.cells[0].data.type === 'qa_task' ? 'QA Task' : 'Calibration Task')}</div>
                            </div>
                        `)
                    },
                    { name: 'Workstation' },
                    { name: 'Workgroup' },
                    { name: 'Task' },
                    { name: 'Schedule' },
                    {
                        name: 'Due',
                        formatter: (_, row) => {
                            const item = row.cells[0].data;
                            return gridjs.html(renderBadge(item.dueAt, item.dueColor || 'neutral'));
                        }
                    },
                    {
                        name: 'Status',
                        formatter: (_, row) => {
                            const item = row.cells[0].data;
                            return gridjs.html(renderBadge(item.status, item.statusColor || 'neutral'));
                        }
                    },
                    {
                        name: 'Actions',
                        sort: false,
                        width: '110px',
                        formatter: (_, row) => gridjs.html(renderTaskActions(row.cells[0].data))
                    }
                ],
                server: {
                    url: @json(url('api/tasks')),
                    then: (payload) => payload.data.map((item) => [item, item.wsName, item.wgName, item.taskName, item.scheduleName, item.dueAt, item.status, null]),
                    total: (payload) => payload.total
                },
                pagination: {
                    enabled: true,
                    limit: 10,
                    server: {
                        url: (prev, page, limit) => `${prev}${prev.includes('?') ? '&' : '?'}page=${page + 1}&limit=${limit}`
                    }
                },
                search: {
                    enabled: true,
                    server: {
                        url: (prev, keyword) => `${prev}${prev.includes('?') ? '&' : '?'}search=${encodeURIComponent(keyword)}`
                    }
                },
                sort: true,
                language: { search: { placeholder: 'Search upcoming tasks...' } }
            });
        }

        function reloadDueTasksGrid() {
            const el = document.getElementById('due-tasks-grid');
            if (!el) {
                return;
            }
            window.dueTasksGrid = null;
            Perfectlum.remountGrid('due-tasks-grid', initDueTasksGrid);
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        window.deleteDueTask = deleteDueTask;
        window.addEventListener('task-saved', reloadDueTasksGrid);
        document.readyState === 'loading'
            ? document.addEventListener('DOMContentLoaded', initDueTasksGrid)
            : initDueTasksGrid();
    })();
</script>
