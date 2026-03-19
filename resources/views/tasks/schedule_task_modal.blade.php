<div id="task-editor-overlay" class="fixed inset-0 z-[80] hidden bg-slate-950/40"></div>
<div id="task-editor-panel" class="fixed inset-y-0 right-0 z-[81] hidden w-full max-w-2xl overflow-y-auto bg-white shadow-2xl">
    <div class="sticky top-0 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
        <div>
            <h3 class="text-lg font-semibold text-slate-900">Schedule Task</h3>
            <p class="text-sm text-slate-500">Edit or create task without Bootstrap modal dependency.</p>
        </div>
        <button type="button" onclick="window.closeTaskEditor()" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200 hover:text-slate-700">
            <span class="sr-only">Close</span>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6 6 18"/>
                <path d="m6 6 12 12"/>
            </svg>
        </button>
    </div>
    <div class="p-6">
        <div id="tasks_edit_box" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
            Loading form...
        </div>
    </div>
</div>

<script>
    (function () {
        const overlay = document.getElementById('task-editor-overlay');
        const panel = document.getElementById('task-editor-panel');
        const box = document.getElementById('tasks_edit_box');

        function openPanel() {
            overlay.classList.remove('hidden');
            panel.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closePanel() {
            overlay.classList.add('hidden');
            panel.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            box.innerHTML = '<div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">Loading form...</div>';
        }

        function toggleTaskSections(selectValue) {
            const root = panel;
            root.querySelectorAll('.date-time-fields').forEach((el) => {
                el.style.display = 'none';
            });
            root.querySelectorAll('.week_field_box').forEach((el) => {
                el.style.display = 'none';
            });

            const performBox = root.querySelector('#perform_task_box');
            const dailyBox = root.querySelector('#daily_field_box');
            const monthlyBox = root.querySelector('.monthly_field_box');
            const dateFields = root.querySelectorAll('.date-time-fields');
            const dateOnlyFields = root.querySelectorAll('.date-field');

            if (performBox) performBox.style.display = 'none';
            if (dailyBox) dailyBox.style.display = 'none';
            if (monthlyBox) monthlyBox.style.display = 'none';
            dateOnlyFields.forEach((el) => { el.style.display = ''; });

            if (selectValue === '0') {
                return;
            }

            dateFields.forEach((el) => { el.style.display = ''; });

            if (selectValue === '2') {
                if (performBox) performBox.style.display = '';
                if (dailyBox) dailyBox.style.display = '';
            }

            if (selectValue === '3') {
                if (performBox) performBox.style.display = '';
                root.querySelectorAll('.week_field_box').forEach((el) => {
                    el.style.display = '';
                });
                dateOnlyFields.forEach((el) => { el.style.display = 'none'; });
            }

            if (selectValue === '4') {
                if (performBox) performBox.style.display = '';
                if (monthlyBox) monthlyBox.style.display = '';
                dateOnlyFields.forEach((el) => { el.style.display = 'none'; });
            }
        }

        function toggleTaskPattern(taskValue) {
            const target = panel.querySelector('#testpaternId');
            if (!target) {
                return;
            }
            target.style.display = taskValue === 'dtp' ? '' : 'none';
        }

        async function bindForm() {
            const form = box.querySelector('#schedule_task_form');
            if (!form) {
                return;
            }

            const scheduleSelect = form.querySelector('#scheduletype');
            const taskSelect = form.querySelector('#tasktype');
            if (scheduleSelect) {
                toggleTaskSections(scheduleSelect.value);
            }
            if (taskSelect) {
                toggleTaskPattern(taskSelect.value);
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                const response = await fetch(@json(url('update-task')), {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form)
                });

                if (!response.ok) {
                    notify('failed', 'Failed to save task.');
                    return;
                }

                const payload = await response.json();
                if (!payload.success) {
                    notify('failed', payload.msg || 'Failed to save task.');
                    return;
                }

                closePanel();
                window.dispatchEvent(new CustomEvent('task-saved'));
                notify('success', 'Task saved successfully.');
            }, { once: true });
        }

        async function loadTaskForm(url, formData) {
            openPanel();
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const payload = await response.json();
                if (!payload.success) {
                    throw new Error('Failed to load task form.');
                }
                box.innerHTML = payload.content;
                await bindForm();
            } catch (error) {
                box.innerHTML = '<div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-600">Failed to load task form.</div>';
            }
        }

        window.closeTaskEditor = closePanel;

        window.schedule_type = function (el) {
            const value = typeof el === 'string'
                ? (panel.querySelector(el)?.value || '0')
                : (el?.value || '0');
            toggleTaskSections(value);
        };

        window.task_type = function (el) {
            const value = typeof el === 'string'
                ? (panel.querySelector(el)?.value || '')
                : (el?.value || '');
            toggleTaskPattern(value);
        };

        window.edit_task = function (_th, id) {
            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', id);
            loadTaskForm(@json(url('edit-task')), formData);
        };

        window.create_task = function (form) {
            const formData = new FormData(form);
            loadTaskForm(@json(url('create-task')), formData);
        };

        overlay.addEventListener('click', closePanel);
    })();
</script>
