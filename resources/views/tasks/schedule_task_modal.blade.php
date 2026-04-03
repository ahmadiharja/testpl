@once
    <div id="task-editor-overlay" class="fixed inset-0 z-[9290] hidden bg-slate-950/40 backdrop-blur-sm"></div>
    <div id="task-editor-panel" class="fixed inset-x-0 bottom-0 z-[9300] hidden max-h-[92vh] overflow-y-auto rounded-t-[1.75rem] border border-slate-200 bg-white shadow-2xl md:inset-y-0 md:right-0 md:left-auto md:max-h-none md:w-full md:max-w-2xl md:rounded-none md:border-y-0 md:border-r-0 md:border-l md:rounded-l-[1.75rem]">
        <div class="sticky top-0 z-10 border-b border-slate-200 bg-white/96 backdrop-blur">
            <div class="flex justify-center pt-3 md:hidden">
                <span class="h-1.5 w-14 rounded-full bg-slate-200"></span>
            </div>
            <div class="flex items-start justify-between gap-4 px-5 py-4 md:px-6">
                <div class="min-w-0">
                    <h3 id="task-editor-title" class="text-lg font-semibold tracking-tight text-slate-900">{{ __('Schedule Task') }}</h3>
                    <p id="task-editor-subtitle" class="mt-1 text-sm text-slate-500">{{ __('Edit or create task without Bootstrap modal dependency.') }}</p>
                </div>
                <button type="button" onclick="window.closeTaskEditor()" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200 hover:text-slate-700">
                    <span class="sr-only">{{ __('Close') }}</span>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="px-4 py-5 pb-[calc(env(safe-area-inset-bottom)+1.25rem)] md:p-6">
            <div id="tasks_edit_box" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                {{ __('Loading form...') }}
            </div>
        </div>
    </div>

    <script>
        (function () {
            const overlay = document.getElementById('task-editor-overlay');
            const panel = document.getElementById('task-editor-panel');
            const box = document.getElementById('tasks_edit_box');
            const titleEl = document.getElementById('task-editor-title');
            const subtitleEl = document.getElementById('task-editor-subtitle');
            const csrfToken = @json(csrf_token());
            const loadingMarkup = `<div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">${@json(__('Loading form...'))}</div>`;
            const defaultTitle = @json(__('Schedule Task'));
            const defaultSubtitle = @json(__('Edit or create task without Bootstrap modal dependency.'));

            function flash(type, message) {
                if (typeof window.notify === 'function') {
                    window.notify(type, message);
                    return;
                }

                console[type === 'success' ? 'log' : 'error'](message);
            }

            function openPanel() {
                overlay.classList.remove('hidden');
                panel.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closePanel() {
                overlay.classList.add('hidden');
                panel.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                box.innerHTML = loadingMarkup;
                if (titleEl) titleEl.textContent = defaultTitle;
                if (subtitleEl) subtitleEl.textContent = defaultSubtitle;
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
                        flash('failed', @js(__('Failed to save task.')));
                        return;
                    }

                    const payload = await response.json();
                    if (!payload.success) {
                        flash('failed', payload.msg || payload.message || @js(__('Failed to save task.')));
                        return;
                    }

                    closePanel();
                    window.dispatchEvent(new CustomEvent('task-saved'));
                    flash('success', @js(__('Task saved successfully.')));
                }, { once: true });
            }

            async function loadTaskForm(url, formData, options = {}) {
                if (titleEl) titleEl.textContent = options.title || defaultTitle;
                if (subtitleEl) subtitleEl.textContent = options.subtitle || defaultSubtitle;
                openPanel();
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    const payload = await response.json();
                    if (!payload.success) {
                        throw new Error(payload.message || @js(__('Failed to load task form.')));
                    }
                    box.innerHTML = payload.content;
                    await bindForm();
                } catch (error) {
                    box.innerHTML = `<div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-600">${@json(__('Failed to load task form.'))}</div>`;
                    flash('failed', error.message || @js(__('Failed to load task form.')));
                }
            }

            function appendPayload(formData, key, value) {
                if (Array.isArray(value)) {
                    value.forEach((item) => {
                        if (item !== undefined && item !== null && item !== '') {
                            formData.append(`${key}[]`, String(item));
                        }
                    });
                    return;
                }

                if (value !== undefined && value !== null && value !== '') {
                    formData.append(key, String(value));
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
                formData.append('_token', csrfToken);
                formData.append('id', id);
                loadTaskForm(@json(url('edit-task')), formData);
            };

            window.create_task = function (form) {
                const formData = new FormData(form);
                loadTaskForm(@json(url('create-task')), formData);
            };

            window.openTaskEditorWithPayload = function (payload = {}, options = {}) {
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('id', payload.id ?? '0');

                Object.entries(payload).forEach(([key, value]) => {
                    if (key === 'id') {
                        return;
                    }

                    appendPayload(formData, key, value);
                });

                loadTaskForm(@json(url('create-task')), formData, options);
            };

            overlay.addEventListener('click', closePanel);
        })();
    </script>
@endonce
